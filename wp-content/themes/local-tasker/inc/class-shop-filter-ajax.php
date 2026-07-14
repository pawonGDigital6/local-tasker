<?php
/**
 * AJAX Handler: Shop Archive Filter.
 *
 * Handles the `lt_filter_products` action used by the shop-archive block for
 * facet filtering, sorting and "Load More" pagination. Registered for both
 * logged-in and logged-out users.
 *
 * Response shape
 * --------------
 * {
 *   success: true,
 *   data: {
 *     html:        string,  // Rendered product cards.
 *     has_more:    bool,    // Whether a next page exists.
 *     found_posts: int,     // Total matching products.
 *     append:      bool     // Echoes back the request mode for the client.
 *   }
 * }
 *
 * @package local-tasker
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class LT_Shop_Filter_Ajax
 */
class LT_Shop_Filter_Ajax {

	/**
	 * Register the AJAX endpoints.
	 */
	public function __construct() {
		add_action( 'wp_ajax_lt_filter_products', array( $this, 'handle' ) );
		add_action( 'wp_ajax_nopriv_lt_filter_products', array( $this, 'handle' ) );
	}

	/**
	 * Process the request and emit JSON.
	 *
	 * @return void
	 */
	public function handle() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'lt_shop_filter' ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Security check failed. Please refresh and try again.', 'local-tasker' ) ),
				403
			);
		}

		// Reuse the exact same parser + query builder as the initial SSR render.
		$state = lt_shop_parse_request( wp_unslash( $_POST ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- sanitised inside the parser.
		$args  = lt_shop_build_query_args( $state );
		$query = new WP_Query( $args );

		$has_more = $query->max_num_pages > $state['paged'];
		$append   = ! empty( $_POST['append'] );

		ob_start();
		lt_shop_render_cards( $query );
		$html = ob_get_clean();

		wp_send_json_success(
			array(
				'html'        => $html,
				'has_more'    => $has_more,
				'found_posts' => (int) $query->found_posts,
				'append'      => $append,
			)
		);
	}
}

new LT_Shop_Filter_Ajax();
