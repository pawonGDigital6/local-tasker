<?php
/**
 * AJAX Handler: Popular Products Block
 *
 * Handles the `ppf_load_products` AJAX action used by the "Popular Products"
 * block's category tabs. Reuses the shop's shared query engine
 * (`lt_shop_build_query_args()`) and card partial (`woocommerce/content-product.php`)
 * so AJAX markup matches the initial render exactly. Registered for
 * logged-in and logged-out users.
 *
 * Include this file from functions.php:
 *   require_once get_template_directory() . '/inc/class-popular-products-ajax.php';
 *
 * @package Local Tasker
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class LT_Popular_Products_Ajax
 *
 * Registers and processes the `ppf_load_products` AJAX action.
 *
 * Expected $_POST fields
 * ─────────────────────
 * nonce            string   WordPress nonce (action: 'ppf_ajax_nonce').
 * category         string   product_cat slug; empty means no filter (all).
 * posts_per_page   int      Products to return (capped server-side).
 *
 * JSON success response shape
 * ───────────────────────────
 * {
 *   success: true,
 *   data: {
 *     html: string   // Rendered card HTML ready to inject.
 *   }
 * }
 */
class LT_Popular_Products_Ajax
{
	/**
	 * Maximum allowed posts_per_page. Prevents a malicious client from
	 * dumping the entire catalogue via this endpoint.
	 */
	private const MAX_POSTS_PER_PAGE = 24;

	/**
	 * Constructor — registers AJAX hooks.
	 */
	public function __construct()
	{
		add_action('wp_ajax_ppf_load_products', [$this, 'handle_request']);
		add_action('wp_ajax_nopriv_ppf_load_products', [$this, 'handle_request']);
	}

	// ─── Public Handler ──────────────────────────────────────────────────────

	/**
	 * Process the AJAX request and emit a JSON response.
	 *
	 * @return void  Terminates via wp_send_json_*.
	 */
	public function handle_request(): void
	{
		// 1. Nonce verification — reject tampered or replayed requests.
		$raw_nonce = isset($_POST['nonce'])
			? sanitize_text_field(wp_unslash($_POST['nonce']))
			: '';

		if (!wp_verify_nonce($raw_nonce, 'ppf_ajax_nonce')) {
			wp_send_json_error(
				['message' => __('Security check failed. Please refresh the page and try again.', 'local-tasker')],
				403
			);
		}

		// 2. Sanitize and validate inputs.
		$category = isset($_POST['category'])
			? sanitize_title(wp_unslash($_POST['category']))
			: '';

		$posts_per_page = isset($_POST['posts_per_page'])
			? max(1, min(absint($_POST['posts_per_page']), self::MAX_POSTS_PER_PAGE))
			: 8;

		// Validate the term exists in the expected taxonomy before querying.
		if ($category && !term_exists($category, 'product_cat')) {
			wp_send_json_error(
				['message' => __('Invalid product category.', 'local-tasker')],
				400
			);
		}

		// 3. Build query args via the shop's shared query engine.
		$state = lt_shop_parse_request([
			'category' => $category,
			'per_page' => $posts_per_page,
			'orderby'  => 'popularity',
		]);
		$query = new WP_Query(lt_shop_build_query_args($state));

		// 4. Render cards into a buffer using the SAME template part as SSR,
		//    guaranteeing the AJAX markup matches the initial render exactly.
		ob_start();

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				wc_get_template_part('content', 'product');
			}
		} else {
			echo '<li class="col-span-full text-center text-lt-text-placeholder py-8">'
				. esc_html__('No products found.', 'local-tasker')
				. '</li>';
		}

		// Always reset after a custom query.
		wp_reset_postdata();

		$html = ob_get_clean();

		// 5. Send JSON response.
		wp_send_json_success(['html' => $html]);
	}
}

// Bootstrap — hooks are registered in the constructor.
new LT_Popular_Products_Ajax();
