<?php
/**
 * local-tasker functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package local-tasker
 */

if (!defined('_S_VERSION')) {
	// Replace the version number of the theme on each release.
	define('_S_VERSION', '1.0.0');
}

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function local_tasker_setup()
{
	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on local-tasker, use a find and replace
	 * to change 'local-tasker' to the name of your theme in all the template files.
	 */
	load_theme_textdomain('local-tasker', get_template_directory() . '/languages');

	// Add default posts and comments RSS feed links to head.
	add_theme_support('automatic-feed-links');

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support('title-tag');

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support('post-thumbnails');

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		array(
			'menu-1' => esc_html__('Primary', 'local-tasker'),
			'footer-menu-1' => esc_html__('Footer Menu 1', 'local-tasker'),
			'footer-menu-2' => esc_html__('Footer Menu 2', 'local-tasker'),
			'footer-menu-3' => esc_html__('Footer Menu 3', 'local-tasker'),
			'footer-menu-4' => esc_html__('Footer Menu 4', 'local-tasker'),
			'footer-bottom' => esc_html__('Footer Bottom', 'local-tasker'),
		)
	);

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		)
	);

	// Set up the WordPress core custom background feature.
	add_theme_support(
		'custom-background',
		apply_filters(
			'local_tasker_custom_background_args',
			array(
				'default-color' => 'ffffff',
				'default-image' => '',
			)
		)
	);

	// Add theme support for selective refresh for widgets.
	add_theme_support('customize-selective-refresh-widgets');

	/**
	 * Add support for core custom logo.
	 *
	 * @link https://codex.wordpress.org/Theme_Logo
	 */
	add_theme_support(
		'custom-logo',
		array(
			'height' => 250,
			'width' => 250,
			'flex-width' => true,
			'flex-height' => true,
		)
	);
}
add_action('after_setup_theme', 'local_tasker_setup');

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function local_tasker_content_width()
{
	$GLOBALS['content_width'] = apply_filters('local_tasker_content_width', 640);
}
add_action('after_setup_theme', 'local_tasker_content_width', 0);

/**

 * Register widget area.

 *

 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar

 */

function local_tasker_widgets_init()
{
	register_sidebar(
		array(
			'name' => esc_html__('Sidebar', 'local-tasker'),
			'id' => 'sidebar-1',
			'description' => esc_html__('Add widgets here.', 'local-tasker'),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h2 class="widget-title">',
			'after_title' => '</h2>',
		)
	);
}

add_action('widgets_init', 'local_tasker_widgets_init');

// /**
//  * Enqueue scripts and styles.
//  */
// function local_tasker_scripts()
// {
// 	wp_enqueue_style('local-tasker-style', get_stylesheet_uri(), array(), _S_VERSION);
// 	wp_style_add_data('local-tasker-style', 'rtl', 'replace');

// 	wp_enqueue_script('local-tasker-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true);

// 	if (is_singular() && comments_open() && get_option('thread_comments')) {
// 		wp_enqueue_script('comment-reply');
// 	}
// }
// add_action('wp_enqueue_scripts', 'local_tasker_scripts');


/**
 * Enqueue scripts and styles.
 */
function local_tasker_scripts()
{

	$path = '/build/global/';

	$version = '';
	$dependencies = array();

	if (file_exists(get_template_directory() . $path . 'index.asset.php')) {
		$version_details = require get_template_directory() . $path . 'index.asset.php';

		if (isset($version_details['version'])) {
			$version = $version_details['version'];
		}

		if (isset($version_details['dependencies'])) {
			$dependencies = array_merge($dependencies, $version_details['dependencies']);
		}
	}

	wp_enqueue_style('local-tasker-style', get_stylesheet_uri(), array(), $version);
	wp_style_add_data('local-tasker-style', 'rtl', 'replace');

	// Theme custom styles.
	wp_register_style('local-tasker-custom-style', get_template_directory_uri() . $path . 'index.css', '', $version);
	wp_enqueue_style('local-tasker-custom-style');
	wp_style_add_data('local-tasker-custom-style', 'rtl', 'replace');

	// Theme custom scripts.
	wp_register_script('local-tasker-custom-script', get_template_directory_uri() . $path . 'index.js', $dependencies, $version, true);
	wp_enqueue_script('local-tasker-custom-script');

}
add_action('wp_enqueue_scripts', 'local_tasker_scripts');

/**
 * Enqueue Block Editor style
 */
function local_tasker_global_editor_style()
{
	$path = '/build/global/';

	$version = '';
	$dependencies = array();

	if (file_exists(get_template_directory() . $path . 'editor.asset.php')) {
		$version_details = require get_template_directory() . $path . 'editor.asset.php';

		if (isset($version_details['version'])) {
			$version = $version_details['version'];
		}

		if (isset($version_details['dependencies'])) {
			$dependencies = array_merge($dependencies, $version_details['dependencies']);
		}
	}

	// Editor custom styles.
	wp_register_style('local-tasker-editor-style', get_template_directory_uri() . $path . 'editor.css', '', $version);
	wp_enqueue_style('local-tasker-editor-style');
	wp_style_add_data('local-tasker-editor-style', 'rtl', 'replace');

}
add_action('enqueue_block_editor_assets', 'local_tasker_global_editor_style');

/**
 * We use WordPress's admin_enqueue_scripts hook to
 * enqueue our custom admin scripts and styles.
 *
 * @link https://developer.wordpress.org/reference/hooks/admin_enqueue_scripts/
 */
function enqueue_admin_scripts_and_styles()
{
	wp_enqueue_script('admin-scripts', get_stylesheet_directory_uri() . '/js/acf_block_preview.js', array('wp-blocks', 'wp-element', 'wp-hooks'), '', true);

	// We use wp_localize_script to pass data
	wp_localize_script('admin-scripts', 'passed_data', array('templateUrl' => get_stylesheet_directory_uri()));
}

add_action('admin_enqueue_scripts', 'enqueue_admin_scripts_and_styles');

/**
 * Load ACF Blocks.
 */
require get_template_directory() . '/inc/acf-register-blocks.php';
require get_template_directory() . '/inc/acf-block-settings.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if (defined('JETPACK__VERSION')) {
	require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Support SVG
 */
require get_template_directory() . '/inc/support-svg.php';


/**
 * CPTs
 */
require_once get_template_directory() . '/inc/cpt/services.php';

/**
 * ACF field group registration (products + site options).
 */
require_once get_template_directory() . '/inc/acf-product-fields.php';

/**
 * Load WooCommerce compatibility file.
 */
if (class_exists('WooCommerce')) {
	require get_template_directory() . '/inc/woocommerce.php';
	require_once get_template_directory() . '/inc/cpt/product-attributes.php';
	require_once get_template_directory() . '/inc/lt-storefront.php';
	require_once get_template_directory() . '/inc/lt-shop-filter.php';
}

// ─ Include the AJAX handler class ────────────────────────────────────────
require_once get_template_directory() . '/inc/class-blogs-filter-ajax.php';

function lt_enqueue_blogs_filter_assets(): void
{
	// Data-only handle (src = false) — the BlogsFilter class ships with the
	// block's viewScript, so this handle exists purely to print the localized
	// AJAX URL inline. Previously this pointed at /assets/js/blogs-filter.js,
	// which does not exist and 404'd site-wide.
	wp_register_script('lt-blogs-filter', false, [], '1.0.0', ['in_footer' => true]);
	wp_enqueue_script('lt-blogs-filter');

	// Expose the admin AJAX URL so the script never has to guess or hardcode it.
	wp_localize_script(
		'lt-blogs-filter',
		'bwfData',
		[
			'ajaxUrl' => esc_url(admin_url('admin-ajax.php')),
		]
	);
}
add_action('wp_enqueue_scripts', 'lt_enqueue_blogs_filter_assets');

// ─ Include the Projects filter AJAX handler ──────────────────────────────
require_once get_template_directory() . '/inc/class-projects-filter-ajax.php';

/**
 * Expose the admin-ajax URL for the "Project With Filter" block.
 *
 * Uses a src-less (data-only) script handle so the correct URL is printed
 * inline — robust on sub-directory installs — without shipping a physical file.
 */
function lt_enqueue_projects_filter_assets(): void
{
	wp_register_script('lt-projects-filter', false, [], '1.0.0', ['in_footer' => true]);
	wp_enqueue_script('lt-projects-filter');

	wp_localize_script(
		'lt-projects-filter',
		'pwfData',
		[
			'ajaxUrl' => esc_url(admin_url('admin-ajax.php')),
		]
	);
}
add_action('wp_enqueue_scripts', 'lt_enqueue_projects_filter_assets');

// ─ Include the Popular Products filter AJAX handler ──────────────────────
if (class_exists('WooCommerce')) {
	require_once get_template_directory() . '/inc/class-popular-products-ajax.php';
}

/**
 * Expose the admin-ajax URL for the "Popular Products" block.
 *
 * Uses a src-less (data-only) script handle so the correct URL is printed
 * inline — robust on sub-directory installs — without shipping a physical file.
 */
function lt_enqueue_popular_products_filter_assets(): void
{
	wp_register_script('lt-popular-products-filter', false, [], '1.0.0', ['in_footer' => true]);
	wp_enqueue_script('lt-popular-products-filter');

	wp_localize_script(
		'lt-popular-products-filter',
		'ppfData',
		[
			'ajaxUrl' => esc_url(admin_url('admin-ajax.php')),
		]
	);
}
add_action('wp_enqueue_scripts', 'lt_enqueue_popular_products_filter_assets');

add_filter('block_editor_settings_all', function ($settings) {
	$settings['styles'] = array(); // Disabling custom block styles often drops the iframe
	return $settings;
});


/* 
============================================================
# Default WP function to search only product
==============================================================
*/
function filter_search_by_woocommerce_products($query)
{
	// Check if it is the front-end search page and the main database query
	if (!is_admin() && $query->is_main_query() && $query->is_search()) {
		$query->set('post_type', 'product');
	}
	return $query;
}
add_filter('pre_get_posts', 'filter_search_by_woocommerce_products');

/* 
============================================================
# Default WP function to search placeholder to search products
==============================================================
*/
function custom_search_placeholder($form)
{
	// Replace the default placeholder text with 'Search products...'
	$form = str_replace('placeholder="Search &hellip;"', 'placeholder="Search products..."', $form);
	$form = str_replace('placeholder="Search..."', 'placeholder="Search products..."', $form);
	return $form;
}
add_filter('get_search_form', 'custom_search_placeholder', 20);


/* 
============================================================
# Default WP function to search listing post navigation
==============================================================
*/
function rename_search_pagination_text($translated_text, $text, $domain)
{
	if (!is_admin() && is_search()) {
		switch ($text) {
			case 'Older posts':
			case '&larr; Older posts':
				$translated_text = '&larr; &nbsp; Previous';
				break;
			case 'Newer posts':
			case 'Newer posts &rarr;':
				$translated_text = 'Next &nbsp; &rarr;';
				break;
		}
	}
	return $translated_text;
}
add_filter('gettext', 'rename_search_pagination_text', 20, 3);
