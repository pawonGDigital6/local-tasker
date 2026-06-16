<?php
/**
 * Register Custom Post Type: Services
 */

if (!function_exists('local_task_register_services_cpt')) {

    function local_task_register_services_cpt()
    {

        $labels = array(
            'name' => _x('Services', 'Post Type General Name', 'local-task'),
            'singular_name' => _x('Service', 'Post Type Singular Name', 'local-task'),
            'menu_name' => __('Services', 'local-task'),
            'name_admin_bar' => __('Service', 'local-task'),
            'archives' => __('Service Archives', 'local-task'),
            'attributes' => __('Service Attributes', 'local-task'),
            'parent_item_colon' => __('Parent Service:', 'local-task'),
            'all_items' => __('All Services', 'local-task'),
            'add_new_item' => __('Add New Service', 'local-task'),
            'add_new' => __('Add New', 'local-task'),
            'new_item' => __('New Service', 'local-task'),
            'edit_item' => __('Edit Service', 'local-task'),
            'update_item' => __('Update Service', 'local-task'),
            'view_item' => __('View Service', 'local-task'),
            'view_items' => __('View Services', 'local-task'),
            'search_items' => __('Search Services', 'local-task'),
            'not_found' => __('Not found', 'local-task'),
            'not_found_in_trash' => __('Not found in Trash', 'local-task'),
            'featured_image' => __('Featured Image', 'local-task'),
            'set_featured_image' => __('Set featured image', 'local-task'),
            'remove_featured_image' => __('Remove featured image', 'local-task'),
            'use_featured_image' => __('Use as featured image', 'local-task'),
            'insert_into_item' => __('Insert into service', 'local-task'),
            'uploaded_to_this_item' => __('Uploaded to this service', 'local-task'),
            'items_list' => __('Services list', 'local-task'),
            'items_list_navigation' => __('Services list navigation', 'local-task'),
            'filter_items_list' => __('Filter services list', 'local-task'),
        );

        $args = array(
            'label' => __('Service', 'local-task'),
            'description' => __('A custom post type for displaying services.', 'local-task'),
            'labels' => $labels,
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'custom-fields'),
            'hierarchical' => false,
            'public' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_position' => 20,
            'menu_icon' => 'dashicons-admin-tools',
            'show_in_admin_bar' => true,
            'show_in_nav_menus' => true,
            'can_export' => true,
            'has_archive' => true,
            'exclude_from_search' => false,
            'publicly_queryable' => true,
            'capability_type' => 'post',
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'services', 'with_front' => true),
        );

        register_post_type('service', $args);

    }
    add_action('init', 'local_task_register_services_cpt', 0);

}