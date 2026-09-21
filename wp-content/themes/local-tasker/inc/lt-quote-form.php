<?php
/**
 * Quote form + LVL calculator placement.
 *
 * Two related pieces of the same change:
 *
 *   1. `[lt_lvl_calculator]` renders the LVL & MGP10 calculator, which used to
 *      sit inside the global "Request a Quote" popup. It now belongs on its own
 *      page (Resources > LVL Calculator). The calculator itself is untouched —
 *      the same static document in /calculator/ embedded the same way, just
 *      reachable from a shortcode so it can be placed anywhere.
 *
 *   2. The quote form's "Service Required" dropdown is filled from the Our
 *      Services menu at render time, so adding a service to the menu adds it to
 *      the form. The list in the CF7 form itself is only a fallback for when
 *      the menu cannot be read.
 *
 * @package local-tasker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Name of the CF7 select that lists the services.
 *
 * Kept as a constant because both the form template (in the database) and this
 * filter have to agree on it, and that agreement is easy to break silently.
 */
const LT_QUOTE_SERVICE_FIELD = 'service';

/**
 * Build the iframe URL for the LVL calculator.
 *
 * Lifted as-is from the popup markup in footer.php. The calculator is a static
 * document served by Apache, so it cannot be localised with
 * wp_localize_script() — it is handed the admin-ajax URL on the query string
 * and fetches its own nonce at submit time, which keeps it working even when
 * the page HTML is cached.
 *
 * @return string
 */
function lt_lvl_calculator_src(): string {
	$args = array( 'ajaxUrl' => rawurlencode( admin_url( 'admin-ajax.php' ) ) );

	// reCAPTCHA keys are shared with Contact Form 7. When none are configured
	// the key is omitted and the calculator skips reCAPTCHA entirely, which is
	// what keeps local and staging working.
	if ( class_exists( 'LT_Quote_Calculator_Ajax' ) && LT_Quote_Calculator_Ajax::recaptcha_enabled() ) {
		$keys = LT_Quote_Calculator_Ajax::get_recaptcha_keys();

		$args['recaptchaKey'] = rawurlencode( $keys['sitekey'] );
	}

	return add_query_arg( $args, get_template_directory_uri() . '/calculator/index.html' );
}

/**
 * `[lt_lvl_calculator]` — embed the LVL & MGP10 calculator.
 *
 * @param array|string $atts Shortcode attributes. `height` in px, default 1200.
 * @return string
 */
function lt_lvl_calculator_shortcode( $atts = array() ): string {
	$atts = shortcode_atts(
		array( 'height' => '1200' ),
		$atts,
		'lt_lvl_calculator'
	);

	$height = absint( $atts['height'] );
	if ( $height < 400 ) {
		$height = 1200;
	}

	return sprintf(
		'<div class="lt-lvl-calculator th-form-style"><iframe src="%1$s" title="%2$s" style="width:100%%;height:%3$dpx;border:0;" loading="lazy"></iframe></div>',
		esc_url( lt_lvl_calculator_src() ),
		esc_attr__( 'LVL &amp; MGP10 quote calculator', 'local-tasker' ),
		$height
	);
}
add_shortcode( 'lt_lvl_calculator', 'lt_lvl_calculator_shortcode' );

/**
 * The services offered, in Our Services menu order.
 *
 * The client asked for the quote form to list "each option under the Our
 * Services menu", so the menu is the source of truth rather than a second list
 * maintained by hand — which is how the form came to be missing Custom
 * Cabinetry.
 *
 * Falls back to an empty array when the menu cannot be read (no menu assigned,
 * or the theme location renamed), and the caller then leaves the form's own
 * options alone.
 *
 * @return string[] Service titles.
 */
function lt_quote_service_options(): array {
	$cached = wp_cache_get( 'lt_quote_service_options' );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	$services = array();

	// Read the submenu under the Our Services parent rather than every service
	// that exists: the menu is what the visitor is being offered, and the client
	// pointed at the menu specifically.
	$locations = get_nav_menu_locations();
	$menu_id   = isset( $locations['menu-1'] ) ? (int) $locations['menu-1'] : 0;
	$items     = $menu_id ? wp_get_nav_menu_items( $menu_id ) : false;

	if ( $items ) {
		$parent_id = 0;
		foreach ( $items as $item ) {
			if ( 0 === (int) $item->menu_item_parent && 'Our Services' === trim( wp_strip_all_tags( $item->title ) ) ) {
				$parent_id = (int) $item->ID;
				break;
			}
		}

		// The parent is matched by title above; if it is ever renamed, fall back
		// to whichever top-level item holds the service posts.
		if ( ! $parent_id ) {
			foreach ( $items as $item ) {
				if ( 'service' === $item->object && (int) $item->menu_item_parent ) {
					$parent_id = (int) $item->menu_item_parent;
					break;
				}
			}
		}

		if ( $parent_id ) {
			foreach ( $items as $item ) {
				if ( (int) $item->menu_item_parent === $parent_id ) {
					$title = trim( wp_strip_all_tags( $item->title ) );
					if ( '' !== $title ) {
						$services[] = $title;
					}
				}
			}
		}
	}

	/**
	 * Filter the service list offered by the quote form.
	 *
	 * @param string[] $services Service titles, in menu order.
	 */
	$services = apply_filters( 'lt_quote_service_options', $services );

	wp_cache_set( 'lt_quote_service_options', $services );

	return $services;
}

/**
 * Fill the quote form's service dropdown from the Our Services menu.
 *
 * Hooked on `wpcf7_form_tag` rather than a render-only filter so the tag looks
 * the same when the submission is validated as it did when it was drawn —
 * otherwise a service added to the menu today would be rejected on submit.
 *
 * @param array $tag Form tag.
 * @return array
 */
function lt_quote_fill_service_options( $tag ) {
	if ( empty( $tag['name'] ) || LT_QUOTE_SERVICE_FIELD !== $tag['name'] ) {
		return $tag;
	}

	$services = lt_quote_service_options();
	if ( ! $services ) {
		return $tag; // Leave the form's own fallback list in place.
	}

	// `first_as_label` keeps the form's own placeholder as the first option, so
	// only the real choices after it are replaced.
	$placeholder = array();
	if ( ! empty( $tag['options'] ) && in_array( 'first_as_label', (array) $tag['options'], true ) ) {
		$placeholder = array_slice( (array) $tag['values'], 0, 1 );
	}

	$tag['values'] = array_merge( $placeholder, $services );
	$tag['labels'] = $tag['values'];

	return $tag;
}
add_filter( 'wpcf7_form_tag', 'lt_quote_fill_service_options' );
