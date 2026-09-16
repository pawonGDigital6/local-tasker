<?php
/**
 * AJAX Handler: LVL & MGP10 Quote Calculator.
 *
 * The calculator ships as a standalone document (calculator/index.html) that is
 * embedded in the global "Request a Quote" popup via an iframe. Because that
 * document is served by Apache rather than rendered by WordPress it cannot be
 * localised with wp_localize_script(), so it receives the admin-ajax URL through
 * its iframe query string (see footer.php) and pulls a fresh nonce from
 * `lt_quote_nonce` immediately before submitting. Fetching the nonce on demand
 * keeps the form working on cached pages, where a nonce printed into the markup
 * would eventually expire and silently break every submission.
 *
 * Actions
 * -------
 * lt_quote_nonce  Returns a fresh nonce for the quote form.
 * lt_send_quote   Validates a submitted quote and emails it to the business.
 *
 * Response shape (lt_send_quote)
 * ------------------------------
 * Success: { success: true,  data: { message: string } }
 * Failure: { success: false, data: { message: string, errors: object } }
 *
 * @package local-tasker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class LT_Quote_Calculator_Ajax
 */
class LT_Quote_Calculator_Ajax {

	/**
	 * Nonce action shared by both endpoints.
	 */
	const NONCE_ACTION = 'lt_quote_calculator';

	/**
	 * GST rate applied to the quote. Mirrors GST_RATE in calculator/script.js.
	 */
	const GST_RATE = 0.10;

	/**
	 * Maximum line items accepted in a single submission.
	 */
	const MAX_LINES = 60;

	/**
	 * Seconds a single IP must wait between submissions.
	 */
	const THROTTLE_SECONDS = 20;

	/**
	 * Minimum reCAPTCHA v3 score treated as human.
	 */
	const RECAPTCHA_THRESHOLD = 0.5;

	/**
	 * reCAPTCHA v3 action name submitted with the token.
	 */
	const RECAPTCHA_ACTION = 'lt_quote';

	/**
	 * Register the AJAX endpoints.
	 */
	public function __construct() {
		add_action( 'wp_ajax_lt_quote_nonce', array( $this, 'handle_nonce' ) );
		add_action( 'wp_ajax_nopriv_lt_quote_nonce', array( $this, 'handle_nonce' ) );
		add_action( 'wp_ajax_lt_send_quote', array( $this, 'handle_send' ) );
		add_action( 'wp_ajax_nopriv_lt_send_quote', array( $this, 'handle_send' ) );
	}

	/**
	 * Issue a fresh nonce for the calculator.
	 *
	 * Safe to expose publicly: the same-origin policy stops a third-party page
	 * reading the response, so the nonce still blocks cross-site submissions
	 * while remaining immune to full-page caching.
	 *
	 * @return void
	 */
	public function handle_nonce() {
		wp_send_json_success( array( 'nonce' => wp_create_nonce( self::NONCE_ACTION ) ) );
	}

	/**
	 * Validate a submitted quote and email it to the business.
	 *
	 * @return void
	 */
	public function handle_send() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Your session expired. Please reload the page and try again.', 'local-tasker' ) ),
				403
			);
		}

		// Honeypot: a real visitor never sees this field, so any value is a bot.
		// Report success so the bot has no signal to retry against.
		$honeypot = isset( $_POST['company_website'] ) ? trim( (string) wp_unslash( $_POST['company_website'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- compared against empty string only.

		if ( '' !== $honeypot ) {
			wp_send_json_success( array( 'message' => __( 'Thanks - your quote request has been sent.', 'local-tasker' ) ) );
		}

		if ( $this->is_throttled() ) {
			wp_send_json_error(
				array( 'message' => __( 'You have just sent a quote request. Please wait a moment before sending another.', 'local-tasker' ) ),
				429
			);
		}

		if ( ! $this->verify_recaptcha() ) {
			wp_send_json_error(
				array( 'message' => __( 'We could not verify that you are human. Please reload the page and try again.', 'local-tasker' ) ),
				403
			);
		}

		$raw     = isset( $_POST['quote'] ) ? (string) wp_unslash( $_POST['quote'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- decoded then sanitised field by field below.
		$payload = json_decode( $raw, true );

		if ( ! is_array( $payload ) ) {
			wp_send_json_error(
				array( 'message' => __( 'We could not read your quote. Please reload the page and try again.', 'local-tasker' ) ),
				400
			);
		}

		$customer = $this->sanitise_customer( isset( $payload['customer'] ) ? $payload['customer'] : array() );
		$lines    = $this->sanitise_lines( isset( $payload['lines'] ) ? $payload['lines'] : array() );
		$errors   = $this->validate( $customer, $lines );

		if ( ! empty( $errors ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Please check the highlighted fields and try again.', 'local-tasker' ),
					'errors'  => $errors,
				),
				422
			);
		}

		// Totals are always recalculated here; the browser's figures are only
		// ever used for on-screen display.
		$totals = $this->calculate_totals( $lines );

		if ( ! $this->send_to_business( $customer, $lines, $totals ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'We could not send your quote just now. Please call us on 03 7302 0482 or email contact@localtasker.com.au.', 'local-tasker' ),
				),
				500
			);
		}

		$this->mark_throttled();

		/**
		 * Filter whether the customer receives a copy of their own quote.
		 *
		 * @param bool $send_copy Default true.
		 */
		if ( apply_filters( 'lt_quote_send_customer_copy', true ) ) {
			$this->send_to_customer( $customer, $lines, $totals );
		}

		wp_send_json_success(
			array( 'message' => __( 'Thanks - your quote request has been sent. Our team will be in touch shortly.', 'local-tasker' ) )
		);
	}

	/* --------------------------------------------------------------------- */
	/* reCAPTCHA v3                                                          */
	/* --------------------------------------------------------------------- */

	/**
	 * Resolve the reCAPTCHA v3 key pair.
	 *
	 * Reuses whatever Contact Form 7 is already configured with so there is one
	 * place to manage keys, and falls back to theme constants for sites where
	 * CF7 is not installed. Returning empty strings disables reCAPTCHA entirely,
	 * which is what keeps the form working on local and staging where the domain
	 * is usually not registered with Google.
	 *
	 * @return array{sitekey:string,secret:string}
	 */
	public static function get_recaptcha_keys() {
		$sitekey = '';
		$secret  = '';

		if ( defined( 'LT_QUOTE_RECAPTCHA_SITEKEY' ) && defined( 'LT_QUOTE_RECAPTCHA_SECRET' ) ) {
			$sitekey = (string) LT_QUOTE_RECAPTCHA_SITEKEY;
			$secret  = (string) LT_QUOTE_RECAPTCHA_SECRET;
		} elseif ( class_exists( 'WPCF7_RECAPTCHA' ) ) {
			$service = WPCF7_RECAPTCHA::get_instance();

			if ( $service->is_active() ) {
				$sitekey = (string) $service->get_sitekey();
				$secret  = (string) $service->get_secret( $sitekey );
			}
		}

		/**
		 * Filter the reCAPTCHA key pair used by the quote calculator.
		 *
		 * Return empty strings to switch reCAPTCHA off for this form.
		 *
		 * @param array $keys { sitekey, secret }.
		 */
		$keys = (array) apply_filters(
			'lt_quote_recaptcha_keys',
			array(
				'sitekey' => $sitekey,
				'secret'  => $secret,
			)
		);

		return array(
			'sitekey' => isset( $keys['sitekey'] ) ? (string) $keys['sitekey'] : '',
			'secret'  => isset( $keys['secret'] ) ? (string) $keys['secret'] : '',
		);
	}

	/**
	 * Whether reCAPTCHA is configured for this site.
	 *
	 * @return bool
	 */
	public static function recaptcha_enabled() {
		$keys = self::get_recaptcha_keys();

		return ( '' !== $keys['sitekey'] && '' !== $keys['secret'] );
	}

	/**
	 * Verify the submitted reCAPTCHA token.
	 *
	 * Note on failure policy: an explicit low score or a mismatched action is
	 * rejected, but a network error talking to Google is allowed through. For a
	 * quote form, losing a real customer enquiry to a Google outage is worse
	 * than letting the occasional bot past, and the honeypot plus throttle still
	 * apply. Filter `lt_quote_recaptcha_fail_open` to reverse that.
	 *
	 * @return bool
	 */
	private function verify_recaptcha() {
		if ( ! self::recaptcha_enabled() ) {
			return true;
		}

		$keys  = self::get_recaptcha_keys();
		$token = isset( $_POST['recaptcha_token'] ) ? sanitize_text_field( wp_unslash( $_POST['recaptcha_token'] ) ) : '';

		if ( '' === $token ) {
			return false;
		}

		$response = wp_remote_post(
			'https://www.google.com/recaptcha/api/siteverify',
			array(
				'timeout' => 10,
				'body'    => array(
					'secret'   => $keys['secret'],
					'response' => $token,
					'remoteip' => isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '',
				),
			)
		);

		/** This filter is documented above. */
		$fail_open = (bool) apply_filters( 'lt_quote_recaptcha_fail_open', true );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			error_log( '[local-tasker] Quote calculator: reCAPTCHA endpoint unreachable, falling back.' ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log

			return $fail_open;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( ! is_array( $body ) ) {
			return $fail_open;
		}

		if ( empty( $body['success'] ) ) {
			$codes = isset( $body['error-codes'] ) ? implode( ', ', (array) $body['error-codes'] ) : 'unknown';

			// invalid-input-secret and invalid-keys mean the site is misconfigured
			// rather than the visitor being a bot, so they are logged loudly.
			error_log( sprintf( '[local-tasker] Quote calculator: reCAPTCHA rejected the token (%s)', $codes ) ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log

			return false;
		}

		// v3 returns the action the token was minted for. A mismatch means the
		// token was lifted from another form on the site.
		if ( isset( $body['action'] ) && self::RECAPTCHA_ACTION !== $body['action'] ) {
			return false;
		}

		$score = isset( $body['score'] ) ? (float) $body['score'] : 0.0;

		/**
		 * Filter the minimum reCAPTCHA v3 score treated as human.
		 *
		 * @param float $threshold Default 0.5.
		 */
		$threshold = (float) apply_filters( 'lt_quote_recaptcha_threshold', self::RECAPTCHA_THRESHOLD );

		return $score >= $threshold;
	}

	/* --------------------------------------------------------------------- */
	/* Validation                                                            */
	/* --------------------------------------------------------------------- */

	/**
	 * Sanitise the customer block of the payload.
	 *
	 * @param mixed $customer Raw customer data.
	 * @return array<string,string>
	 */
	private function sanitise_customer( $customer ) {
		$customer = is_array( $customer ) ? $customer : array();

		// sanitize_email() reduces anything malformed to an empty string, which
		// would make a typo look like a blank field. Keep the typed value when
		// it is non-empty so validate() can tell the two cases apart; it never
		// reaches a mail header unless is_email() passes.
		$email_raw   = $this->str( $customer, 'email' );
		$email_clean = sanitize_email( $email_raw );
		$email       = ( '' === $email_clean && '' !== $email_raw ) ? sanitize_text_field( $email_raw ) : $email_clean;

		return array(
			'name'          => sanitize_text_field( $this->str( $customer, 'name' ) ),
			'email'         => $email,
			'phone'         => sanitize_text_field( $this->str( $customer, 'phone' ) ),
			'projectSuburb' => sanitize_text_field( $this->str( $customer, 'projectSuburb' ) ),
			'notes'         => sanitize_textarea_field( $this->str( $customer, 'notes' ) ),
		);
	}

	/**
	 * Sanitise line items and re-derive unit prices from the product table.
	 *
	 * @param mixed $lines Raw line items.
	 * @return array<int,array<string,mixed>>
	 */
	private function sanitise_lines( $lines ) {
		if ( ! is_array( $lines ) ) {
			return array();
		}

		$catalogue = $this->get_product_data();
		$clean     = array();

		foreach ( array_slice( $lines, 0, self::MAX_LINES ) as $line ) {
			if ( ! is_array( $line ) ) {
				continue;
			}

			$entry = array(
				'category' => sanitize_text_field( $this->str( $line, 'category' ) ),
				'product'  => sanitize_text_field( $this->str( $line, 'product' ) ),
				'size'     => sanitize_text_field( $this->str( $line, 'size' ) ),
				'length'   => sanitize_text_field( $this->str( $line, 'length' ) ),
				'unit'     => sanitize_text_field( $this->str( $line, 'unit' ) ),
				'sku'      => sanitize_text_field( $this->str( $line, 'sku' ) ),
				'quantity' => absint( isset( $line['quantity'] ) ? $line['quantity'] : 0 ),
			);

			// Skip rows the visitor never filled in - the calculator always
			// renders at least one empty row.
			if ( '' === $entry['category'] && '' === $entry['product'] && '' === $entry['size'] && 0 === $entry['quantity'] ) {
				continue;
			}

			$item = $this->find_item( $catalogue, $entry );

			$entry['unitPrice']   = $this->resolve_unit_price( $item, $entry, isset( $line['unitPrice'] ) ? $line['unitPrice'] : 0 );
			$entry['subtotal']    = round( $entry['quantity'] * $entry['unitPrice'], 2 );
			$entry['knownItem']   = ( null !== $item ) || empty( $catalogue );
			$entry['needsLength'] = ( null !== $item && ! empty( $item['lengthPrices'] ) );

			$clean[] = $entry;
		}

		return $clean;
	}

	/**
	 * Validate the sanitised submission.
	 *
	 * @param array $customer Sanitised customer data.
	 * @param array $lines    Sanitised line items.
	 * @return array<string,string> Field key => message.
	 */
	private function validate( $customer, $lines ) {
		$errors = array();

		if ( '' === $customer['name'] ) {
			$errors['customerName'] = __( 'Please enter your name.', 'local-tasker' );
		}

		if ( '' === $customer['email'] ) {
			$errors['customerEmail'] = __( 'Please enter your email address.', 'local-tasker' );
		} elseif ( ! is_email( $customer['email'] ) ) {
			$errors['customerEmail'] = __( 'That email address does not look right.', 'local-tasker' );
		}

		// Digits only, so "04 1234 5678", "+61 412 345 678" and "(03) 9123 4567"
		// all pass while "call me" does not.
		$phone_digits = preg_replace( '/\D/', '', $customer['phone'] );

		if ( '' === $customer['phone'] ) {
			$errors['customerPhone'] = __( 'Please enter your phone number.', 'local-tasker' );
		} elseif ( strlen( $phone_digits ) < 8 ) {
			$errors['customerPhone'] = __( 'That phone number does not look right.', 'local-tasker' );
		}

		if ( empty( $lines ) ) {
			$errors['rows'] = __( 'Please add at least one product to your quote.', 'local-tasker' );

			return $errors;
		}

		foreach ( $lines as $index => $line ) {
			$position = $index + 1;

			if ( '' === $line['category'] || '' === $line['product'] || '' === $line['size'] ) {
				/* translators: %d: product row number. */
				$errors['rows'] = sprintf( __( 'Row %d is incomplete. Choose a category, product and size, or remove the row.', 'local-tasker' ), $position );
				break;
			}

			if ( ! $line['knownItem'] ) {
				/* translators: %d: product row number. */
				$errors['rows'] = sprintf( __( 'Row %d is not a product we stock. Please choose from the list.', 'local-tasker' ), $position );
				break;
			}

			if ( $line['needsLength'] && ( '' === $line['length'] || 'N/A' === $line['length'] ) ) {
				/* translators: %d: product row number. */
				$errors['rows'] = sprintf( __( 'Row %d needs a length before we can price it.', 'local-tasker' ), $position );
				break;
			}

			if ( $line['quantity'] < 1 ) {
				/* translators: %d: product row number. */
				$errors['rows'] = sprintf( __( 'Row %d needs a quantity of at least 1.', 'local-tasker' ), $position );
				break;
			}

			if ( $line['unitPrice'] <= 0 ) {
				/* translators: %d: product row number. */
				$errors['rows'] = sprintf( __( 'Row %d could not be priced. Please reselect the size and length.', 'local-tasker' ), $position );
				break;
			}
		}

		return $errors;
	}

	/* --------------------------------------------------------------------- */
	/* Pricing                                                               */
	/* --------------------------------------------------------------------- */

	/**
	 * Load and cache the calculator's product table.
	 *
	 * calculator/data.js is a JS assignment wrapping a JSON array. Parsing it
	 * here keeps one source of truth for prices instead of duplicating the table
	 * in PHP. If the file ever stops parsing, pricing falls back to the values
	 * submitted by the browser rather than failing every request.
	 *
	 * @return array<int,array<string,mixed>>
	 */
	private function get_product_data() {
		static $cache = null;

		if ( null !== $cache ) {
			return $cache;
		}

		$cache = array();
		$path  = get_template_directory() . '/calculator/data.js';

		if ( ! file_exists( $path ) ) {
			return $cache;
		}

		$contents = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local theme asset, not a remote request.

		if ( false === $contents ) {
			return $cache;
		}

		$start = strpos( $contents, '[' );
		$end   = strrpos( $contents, ']' );

		if ( false === $start || false === $end || $end <= $start ) {
			return $cache;
		}

		$decoded = json_decode( substr( $contents, $start, ( $end - $start ) + 1 ), true );

		if ( is_array( $decoded ) ) {
			$cache = $decoded;
		}

		return $cache;
	}

	/**
	 * Find the catalogue entry matching a submitted line.
	 *
	 * @param array $catalogue Product table.
	 * @param array $line      Sanitised line.
	 * @return array<string,mixed>|null
	 */
	private function find_item( $catalogue, $line ) {
		foreach ( $catalogue as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			if ( $this->str( $item, 'category' ) === $line['category']
				&& $this->str( $item, 'product' ) === $line['product']
				&& $this->str( $item, 'size' ) === $line['size'] ) {
				return $item;
			}
		}

		return null;
	}

	/**
	 * Resolve the authoritative unit price for a line.
	 *
	 * @param array|null $item     Catalogue entry, or null when unknown.
	 * @param array      $line     Sanitised line.
	 * @param mixed      $fallback Price submitted by the browser.
	 * @return float
	 */
	private function resolve_unit_price( $item, $line, $fallback ) {
		if ( null === $item ) {
			// Catalogue unreadable - keep the submitted figure so the business
			// still sees what the visitor was shown on screen.
			return empty( $this->get_product_data() ) ? round( (float) $fallback, 2 ) : 0.0;
		}

		$length_prices = ( isset( $item['lengthPrices'] ) && is_array( $item['lengthPrices'] ) ) ? $item['lengthPrices'] : array();

		if ( ! empty( $length_prices ) ) {
			return isset( $length_prices[ $line['length'] ] ) ? round( (float) $length_prices[ $line['length'] ], 2 ) : 0.0;
		}

		return isset( $item['salePricePerUnit'] ) ? round( (float) $item['salePricePerUnit'], 2 ) : 0.0;
	}

	/**
	 * Recalculate quote totals from validated line items.
	 *
	 * @param array $lines Sanitised line items.
	 * @return array{exGST:float,gst:float,incGST:float}
	 */
	private function calculate_totals( $lines ) {
		$ex_gst = 0.0;

		foreach ( $lines as $line ) {
			$ex_gst += (float) $line['subtotal'];
		}

		$ex_gst = round( $ex_gst, 2 );
		$gst    = round( $ex_gst * self::GST_RATE, 2 );

		return array(
			'exGST'  => $ex_gst,
			'gst'    => $gst,
			'incGST' => round( $ex_gst + $gst, 2 ),
		);
	}

	/* --------------------------------------------------------------------- */
	/* Delivery                                                              */
	/* --------------------------------------------------------------------- */

	/**
	 * Email the quote to the business.
	 *
	 * @param array $customer Sanitised customer data.
	 * @param array $lines    Validated line items.
	 * @param array $totals   Recalculated totals.
	 * @return bool
	 */
	private function send_to_business( $customer, $lines, $totals ) {
		$subject = sprintf(
			/* translators: 1: customer name, 2: project suburb. */
			__( 'Quote request from %1$s - %2$s', 'local-tasker' ),
			$customer['name'],
			'' !== $customer['projectSuburb'] ? $customer['projectSuburb'] : __( 'suburb not supplied', 'local-tasker' )
		);

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );

		if ( is_email( $customer['email'] ) ) {
			$headers[] = sprintf( 'Reply-To: %s <%s>', $customer['name'], $customer['email'] );
		}

		return $this->mail(
			$this->get_recipient(),
			$subject,
			$this->render_email( $customer, $lines, $totals, false ),
			$headers
		);
	}

	/**
	 * Email a copy of the quote to the customer.
	 *
	 * @param array $customer Sanitised customer data.
	 * @param array $lines    Validated line items.
	 * @param array $totals   Recalculated totals.
	 * @return bool
	 */
	private function send_to_customer( $customer, $lines, $totals ) {
		$recipients = $this->get_recipient();

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			sprintf( 'Reply-To: %s', reset( $recipients ) ),
		);

		return $this->mail(
			$customer['email'],
			__( 'Your LocalTasker quote request', 'local-tasker' ),
			$this->render_email( $customer, $lines, $totals, true ),
			$headers
		);
	}

	/**
	 * Send mail with a From address on the site's own domain.
	 *
	 * Using the visitor's address as the sender fails SPF/DKIM at most hosts and
	 * is the usual cause of quote emails disappearing, so the sender is always
	 * the site domain and the visitor is set as Reply-To instead.
	 *
	 * @param string|string[] $to      Recipient, or list of recipients.
	 * @param string          $subject Subject line.
	 * @param string          $body    HTML body.
	 * @param string[]        $headers Additional headers.
	 * @return bool
	 */
	private function mail( $to, $subject, $body, $headers ) {
		$headers[] = sprintf(
			'From: %s <%s>',
			wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
			$this->get_from_address()
		);

		$sent = wp_mail( $to, $subject, $body, $headers );

		if ( ! $sent ) {
			// Logged so a mail outage is diagnosable instead of looking like
			// "the quote form does not work".
			error_log( // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
				sprintf(
					'[local-tasker] Quote calculator: wp_mail() failed sending to %s',
					is_array( $to ) ? implode( ', ', $to ) : $to
				)
			);
		}

		return (bool) $sent;
	}

	/**
	 * Resolve the inbox (or inboxes) that receive quote requests.
	 *
	 * Resolution order, first match wins:
	 *
	 *   1. LT_QUOTE_RECIPIENT in wp-config.php - per environment, never shipped
	 *      in the theme, so staging and live can point at different inboxes.
	 *      Accepts a comma separated list.
	 *   2. Theme Option > Contact Settings > Email (ACF `cm_email`) - the
	 *      client's permanent address once testing is finished.
	 *   3. The WordPress admin email, as a last resort.
	 *
	 * @return string[]
	 */
	private function get_recipient() {
		$recipient = '';

		if ( defined( 'LT_QUOTE_RECIPIENT' ) && LT_QUOTE_RECIPIENT ) {
			$recipient = (string) LT_QUOTE_RECIPIENT;
		}

		if ( '' === $recipient && function_exists( 'get_field' ) ) {
			$recipient = (string) get_field( 'cm_email', 'options' );
		}

		if ( '' === $recipient ) {
			$recipient = (string) get_option( 'admin_email' );
		}

		/**
		 * Filter the inbox that receives quote calculator submissions.
		 *
		 * @param string $recipient Email address, or a comma separated list.
		 */
		$recipient = (string) apply_filters( 'lt_quote_recipient', $recipient );

		$emails = array_values(
			array_filter(
				array_map( 'trim', explode( ',', $recipient ) ),
				'is_email'
			)
		);

		if ( empty( $emails ) ) {
			$emails = array( (string) get_option( 'admin_email' ) );
		}

		return $emails;
	}

	/**
	 * Build a deliverable From address on the site's own domain.
	 *
	 * @return string
	 */
	private function get_from_address() {
		$host = wp_parse_url( home_url(), PHP_URL_HOST );
		$host = preg_replace( '/^www\./i', '', (string) $host );
		$from = $host ? 'noreply@' . $host : (string) get_option( 'admin_email' );

		/**
		 * Filter the From address used for quote calculator emails.
		 *
		 * @param string $from Email address.
		 */
		return (string) apply_filters( 'lt_quote_from_address', $from );
	}

	/* --------------------------------------------------------------------- */
	/* Email template                                                        */
	/* --------------------------------------------------------------------- */

	/**
	 * Render the quote as an HTML email.
	 *
	 * @param array $customer    Sanitised customer data.
	 * @param array $lines       Validated line items.
	 * @param array $totals      Recalculated totals.
	 * @param bool  $is_customer Whether this copy is addressed to the customer.
	 * @return string
	 */
	private function render_email( $customer, $lines, $totals, $is_customer ) {
		$intro = $is_customer
			? __( 'Thanks for your quote request. Here is a copy of what you sent us - our team will be in touch shortly.', 'local-tasker' )
			: __( 'A new quote request was submitted through the website calculator.', 'local-tasker' );

		$rows = '';

		foreach ( $lines as $index => $line ) {
			$rows .= sprintf(
				'<tr>
					<td style="padding:10px 8px;border-bottom:1px solid #e5e7eb;">%1$d</td>
					<td style="padding:10px 8px;border-bottom:1px solid #e5e7eb;">%2$s<br><span style="color:#6b7280;font-size:12px;">%3$s</span></td>
					<td style="padding:10px 8px;border-bottom:1px solid #e5e7eb;">%4$s</td>
					<td style="padding:10px 8px;border-bottom:1px solid #e5e7eb;">%5$s</td>
					<td style="padding:10px 8px;border-bottom:1px solid #e5e7eb;">%6$s</td>
					<td style="padding:10px 8px;border-bottom:1px solid #e5e7eb;text-align:right;">%7$d</td>
					<td style="padding:10px 8px;border-bottom:1px solid #e5e7eb;text-align:right;">%8$s</td>
					<td style="padding:10px 8px;border-bottom:1px solid #e5e7eb;text-align:right;font-weight:700;">%9$s</td>
				</tr>',
				(int) $index + 1,
				esc_html( $line['product'] ),
				esc_html( $line['category'] ),
				esc_html( $line['size'] ),
				esc_html( '' !== $line['length'] ? $line['length'] : 'N/A' ),
				esc_html( $line['unit'] ),
				(int) $line['quantity'],
				esc_html( $this->money( $line['unitPrice'] ) ),
				esc_html( $this->money( $line['subtotal'] ) )
			);
		}

		$details = array(
			__( 'Name', 'local-tasker' )           => $customer['name'],
			__( 'Email', 'local-tasker' )          => $customer['email'],
			__( 'Phone', 'local-tasker' )          => $customer['phone'],
			__( 'Project suburb', 'local-tasker' ) => $customer['projectSuburb'],
			__( 'Submitted', 'local-tasker' )      => wp_date( 'j M Y, g:ia' ),
		);

		$detail_rows = '';

		foreach ( $details as $label => $value ) {
			if ( '' === $value ) {
				continue;
			}

			$detail_rows .= sprintf(
				'<tr><td style="padding:4px 12px 4px 0;color:#6b7280;">%1$s</td><td style="padding:4px 0;font-weight:600;">%2$s</td></tr>',
				esc_html( $label ),
				esc_html( $value )
			);
		}

		$notes = '' !== $customer['notes']
			? sprintf(
				'<h3 style="margin:24px 0 8px;font-size:15px;">%1$s</h3><p style="margin:0;white-space:pre-wrap;">%2$s</p>',
				esc_html__( 'Notes', 'local-tasker' ),
				esc_html( $customer['notes'] )
			)
			: '';

		return sprintf(
			'<!doctype html><html><body style="margin:0;padding:24px;background:#f4f6f8;font-family:Arial,Helvetica,sans-serif;color:#111827;">
			<div style="max-width:760px;margin:0 auto;background:#ffffff;border-radius:16px;padding:28px;">
				<h2 style="margin:0 0 6px;font-size:20px;">%1$s</h2>
				<p style="margin:0 0 20px;color:#6b7280;">%2$s</p>
				<table style="border-collapse:collapse;margin-bottom:24px;font-size:14px;">%3$s</table>
				<table style="width:100%%;border-collapse:collapse;font-size:13px;">
					<thead>
						<tr style="text-align:left;color:#6b7280;text-transform:uppercase;font-size:11px;">
							<th style="padding:0 8px 8px;">#</th>
							<th style="padding:0 8px 8px;">%4$s</th>
							<th style="padding:0 8px 8px;">%5$s</th>
							<th style="padding:0 8px 8px;">%6$s</th>
							<th style="padding:0 8px 8px;">%7$s</th>
							<th style="padding:0 8px 8px;text-align:right;">%8$s</th>
							<th style="padding:0 8px 8px;text-align:right;">%9$s</th>
							<th style="padding:0 8px 8px;text-align:right;">%10$s</th>
						</tr>
					</thead>
					<tbody>%11$s</tbody>
				</table>
				<table style="width:100%%;border-collapse:collapse;margin-top:20px;font-size:14px;">
					<tr><td style="padding:6px 0;">%12$s</td><td style="padding:6px 0;text-align:right;font-weight:700;">%13$s</td></tr>
					<tr><td style="padding:6px 0;">%14$s</td><td style="padding:6px 0;text-align:right;font-weight:700;">%15$s</td></tr>
					<tr><td style="padding:10px 0 0;border-top:2px solid #111827;font-size:16px;">%16$s</td><td style="padding:10px 0 0;border-top:2px solid #111827;text-align:right;font-weight:800;font-size:16px;">%17$s</td></tr>
				</table>
				%18$s
				<p style="margin:28px 0 0;color:#6b7280;font-size:12px;">%19$s</p>
			</div>
			</body></html>',
			esc_html__( 'Quote request', 'local-tasker' ),
			esc_html( $intro ),
			$detail_rows,
			esc_html__( 'Product', 'local-tasker' ),
			esc_html__( 'Size', 'local-tasker' ),
			esc_html__( 'Length', 'local-tasker' ),
			esc_html__( 'Unit', 'local-tasker' ),
			esc_html__( 'Qty', 'local-tasker' ),
			esc_html__( 'Unit price', 'local-tasker' ),
			esc_html__( 'Subtotal', 'local-tasker' ),
			$rows,
			esc_html__( 'Total excluding GST', 'local-tasker' ),
			esc_html( $this->money( $totals['exGST'] ) ),
			esc_html__( 'GST', 'local-tasker' ),
			esc_html( $this->money( $totals['gst'] ) ),
			esc_html__( 'Total including GST', 'local-tasker' ),
			esc_html( $this->money( $totals['incGST'] ) ),
			$notes,
			esc_html__( 'Prices are an estimate only and exclude delivery unless stated.', 'local-tasker' )
		);
	}

	/* --------------------------------------------------------------------- */
	/* Helpers                                                               */
	/* --------------------------------------------------------------------- */

	/**
	 * Format a value as AUD currency.
	 *
	 * @param float $value Amount.
	 * @return string
	 */
	private function money( $value ) {
		return '$' . number_format( (float) $value, 2 );
	}

	/**
	 * Read a scalar key from an array as a trimmed string.
	 *
	 * @param array  $source Source array.
	 * @param string $key    Key to read.
	 * @return string
	 */
	private function str( $source, $key ) {
		if ( ! isset( $source[ $key ] ) || ! is_scalar( $source[ $key ] ) ) {
			return '';
		}

		return trim( (string) $source[ $key ] );
	}

	/**
	 * Transient key for the current submitter.
	 *
	 * @return string
	 */
	private function throttle_key() {
		$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';

		return 'lt_quote_throttle_' . md5( $ip );
	}

	/**
	 * Whether this submitter is inside the cooldown window.
	 *
	 * @return bool
	 */
	private function is_throttled() {
		return (bool) get_transient( $this->throttle_key() );
	}

	/**
	 * Start the cooldown window for this submitter.
	 *
	 * @return void
	 */
	private function mark_throttled() {
		set_transient( $this->throttle_key(), 1, self::THROTTLE_SECONDS );
	}
}

new LT_Quote_Calculator_Ajax();
