<?php
/**
 * AJAX Handler: Blog With Filter Block
 *
 * Handles the `bwf_load_posts` AJAX action used by both category filtering
 * and "Load More" pagination. Registered for logged-in and logged-out users.
 *
 * Include this file from functions.php:
 *   require_once get_template_directory() . '/inc/class-blogs-filter-ajax.php';
 *
 * @package YourTheme
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class LT_Blogs_Filter_Ajax
 *
 * Registers and processes the `bwf_load_posts` AJAX action.
 *
 * Expected $_POST fields
 * ─────────────────────
 * nonce            string   WordPress nonce (action: 'bwf_ajax_nonce').
 * category_id      int      Category term_id; 0 means no filter (all posts).
 * page             int      1-based page number.
 * posts_per_page   int      Posts to return per page (capped server-side).
 *
 * JSON success response shape
 * ───────────────────────────
 * {
 *   success: true,
 *   data: {
 *     html:        string,   // Rendered card HTML ready to inject.
 *     has_more:    bool,     // Whether a next page exists.
 *     found_posts: int       // Total matching posts (useful for UI counters).
 *   }
 * }
 */
class LT_Blogs_Filter_Ajax
{

	/**
	 * Maximum allowed posts_per_page value.
	 * Prevents a malicious client from dumping the entire post table.
	 */
	private const MAX_POSTS_PER_PAGE = 24;

	/**
	 * Constructor — registers AJAX hooks.
	 */
	public function __construct()
	{
		add_action('wp_ajax_bwf_load_posts', [$this, 'handle_request']);
		add_action('wp_ajax_nopriv_bwf_load_posts', [$this, 'handle_request']);
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

		if (!wp_verify_nonce($raw_nonce, 'bwf_ajax_nonce')) {
			wp_send_json_error(
				['message' => __('Security check failed. Please refresh the page and try again.', 'lt-theme')],
				403
			);
		}

		// 2. Sanitize and validate inputs.
		$category_id = isset($_POST['category_id'])
			? absint($_POST['category_id'])
			: 0;

		$page = isset($_POST['page'])
			? max(1, absint($_POST['page']))
			: 1;

		$posts_per_page = isset($_POST['posts_per_page'])
			? min(absint($_POST['posts_per_page']), self::MAX_POSTS_PER_PAGE)
			: 6;

		// Validate category existence before querying.
		if ($category_id > 0 && !term_exists((int) $category_id, 'category')) {
			wp_send_json_error(
				['message' => __('Invalid category.', 'lt-theme')],
				400
			);
		}

		// 3. Build query arguments.
		$query_args = [
			'post_type' => 'post',
			'post_status' => 'publish',
			'posts_per_page' => $posts_per_page,
			'paged' => $page,
		];

		if ($category_id > 0) {
			// `cat` accepts a single term_id and respects category hierarchy.
			$query_args['cat'] = $category_id;
		}

		// 4. Execute query.
		$query = new WP_Query($query_args);
		$has_more = $query->max_num_pages > $page;

		// 5. Render cards into a buffer.
		//
		// get_template_part() outputs directly, so we capture it with
		// output buffering. This mirrors how the initial render works in
		// block.php, ensuring a pixel-perfect match between SSR and AJAX cards.
		ob_start();

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				get_template_part('template-parts/blocks/blogs-filter/card');
			}
		} else {
			// Inline no-results message.
			echo '<p class="col-span-full text-center text-lt-text-placeholder py-8">'
				. esc_html__('No posts found in this category.', 'lt-theme')
				. '</p>';
		}

		// Always reset after a custom query.
		wp_reset_postdata();

		$html = ob_get_clean();

		// 6. Send JSON response.
		wp_send_json_success(
			[
				'html' => $html,
				'has_more' => $has_more,
				'found_posts' => (int) $query->found_posts,
			]
		);
	}
}

// Bootstrap — hooks are registered in the constructor.
new LT_Blogs_Filter_Ajax();