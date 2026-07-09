<?php
/**
 * AJAX Handler: Project With Filter Block
 *
 * Handles the `pwf_load_posts` AJAX action used by both taxonomy filtering
 * and the "View All Projects" action. Registered for logged-in and
 * logged-out users.
 *
 * Include this file from functions.php:
 *   require_once get_template_directory() . '/inc/class-projects-filter-ajax.php';
 *
 * @package Local Tasker
 * @since   1.0.0
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class LT_Projects_Filter_Ajax
 *
 * Registers and processes the `pwf_load_posts` AJAX action.
 *
 * Expected $_POST fields
 * ─────────────────────
 * nonce            string   WordPress nonce (action: 'pwf_ajax_nonce').
 * term_id          int      lt_project_type term_id; 0 means no filter (all).
 * posts_per_page   int      Projects to return per page (capped server-side).
 * view_all         int      1 → return every matching project; 0 → first page.
 *
 * JSON success response shape
 * ───────────────────────────
 * {
 *   success: true,
 *   data: {
 *     html:        string,   // Rendered card HTML ready to inject.
 *     has_more:    bool,     // Whether more projects exist beyond this batch.
 *     found_posts: int       // Total matching projects.
 *   }
 * }
 */
class LT_Projects_Filter_Ajax
{
	/**
	 * The Custom Post Type queried by this handler.
	 */
	private const POST_TYPE = 'lt_projects';

	/**
	 * The taxonomy powering the category filter.
	 */
	private const TAXONOMY = 'lt_project_type';

	/**
	 * Maximum allowed posts_per_page for a paged (non view-all) request.
	 * Prevents a malicious client from dumping the entire table via the
	 * normal filter path. "View All" bypasses this intentionally.
	 */
	private const MAX_POSTS_PER_PAGE = 24;

	/**
	 * Constructor — registers AJAX hooks.
	 */
	public function __construct()
	{
		add_action('wp_ajax_pwf_load_posts', [$this, 'handle_request']);
		add_action('wp_ajax_nopriv_pwf_load_posts', [$this, 'handle_request']);
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

		if (!wp_verify_nonce($raw_nonce, 'pwf_ajax_nonce')) {
			wp_send_json_error(
				['message' => __('Security check failed. Please refresh the page and try again.', 'lt-theme')],
				403
			);
		}

		// 2. Sanitize and validate inputs.
		$term_id = isset($_POST['term_id']) ? absint($_POST['term_id']) : 0;
		$view_all = isset($_POST['view_all']) && '1' === (string) $_POST['view_all'];

		$posts_per_page = isset($_POST['posts_per_page'])
			? max(1, min(absint($_POST['posts_per_page']), self::MAX_POSTS_PER_PAGE))
			: 3;

		// Validate the term exists in the expected taxonomy before querying.
		if ($term_id > 0 && !term_exists((int) $term_id, self::TAXONOMY)) {
			wp_send_json_error(
				['message' => __('Invalid project category.', 'lt-theme')],
				400
			);
		}

		// 3. Build query arguments.
		//    View-all returns every matching project on a single page.
		$query_args = [
			'post_type'      => self::POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => $view_all ? -1 : $posts_per_page,
			'paged'          => 1,
			'no_found_rows'  => false,
		];

		if ($term_id > 0) {
			$query_args['tax_query'] = [
				[
					'taxonomy' => self::TAXONOMY,
					'field'    => 'term_id',
					'terms'    => $term_id,
				],
			];
		}

		// 4. Execute query.
		$query = new WP_Query($query_args);

		// More projects exist than were returned in this (paged) batch.
		$has_more = !$view_all && ((int) $query->found_posts > $posts_per_page);

		// 5. Render cards into a buffer using the SAME template part as SSR,
		//    guaranteeing the AJAX markup matches the initial render exactly.
		ob_start();

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				get_template_part('template-parts/blocks/project-filter/card');
			}
		} else {
			echo '<p class="col-span-full text-center text-lt-text-placeholder py-8">'
				. esc_html__('No projects found.', 'lt-theme')
				. '</p>';
		}

		// Always reset after a custom query.
		wp_reset_postdata();

		$html = ob_get_clean();

		// 6. Send JSON response.
		wp_send_json_success(
			[
				'html'        => $html,
				'has_more'    => $has_more,
				'found_posts' => (int) $query->found_posts,
			]
		);
	}
}

// Bootstrap — hooks are registered in the constructor.
new LT_Projects_Filter_Ajax();
