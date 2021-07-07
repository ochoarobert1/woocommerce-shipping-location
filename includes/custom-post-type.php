<?php
/**
 * Custom Post Type for Location
 *
 * Creates a new custom post type for location type
 *
 * @package WooShipLocationCPT
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

class WooShipLocationCPT extends WooShipLocation
{
    public function __construct()
    {
        add_action('init', array($this, 'custom_post_type'), 0);
    }

    // Register Custom Post Type
    public function custom_post_type()
    {
        $labels = array(
            'name'                  => _x('Locations', 'Post Type General Name', parent::THEME_LANG),
            'singular_name'         => _x('Location', 'Post Type Singular Name', parent::THEME_LANG),
            'menu_name'             => __('Locations', parent::THEME_LANG),
            'name_admin_bar'        => __('Locations', parent::THEME_LANG),
            'archives'              => __('Locations Archives', parent::THEME_LANG),
            'attributes'            => __('Location Attributes', parent::THEME_LANG),
            'parent_item_colon'     => __('Location Item:', parent::THEME_LANG),
            'all_items'             => __('All Locations', parent::THEME_LANG),
            'add_new_item'          => __('Add New Location', parent::THEME_LANG),
            'add_new'               => __('Add New', parent::THEME_LANG),
            'new_item'              => __('New Location', parent::THEME_LANG),
            'edit_item'             => __('Edit Location', parent::THEME_LANG),
            'update_item'           => __('Update Location', parent::THEME_LANG),
            'view_item'             => __('View Location', parent::THEME_LANG),
            'view_items'            => __('View Locations', parent::THEME_LANG),
            'search_items'          => __('Search Location', parent::THEME_LANG),
            'not_found'             => __('Not found', parent::THEME_LANG),
            'not_found_in_trash'    => __('Not found in Trash', parent::THEME_LANG),
            'featured_image'        => __('Featured Image', parent::THEME_LANG),
            'set_featured_image'    => __('Set featured image', parent::THEME_LANG),
            'remove_featured_image' => __('Remove featured image', parent::THEME_LANG),
            'use_featured_image'    => __('Use as featured image', parent::THEME_LANG),
            'insert_into_item'      => __('Insert into Location', parent::THEME_LANG),
            'uploaded_to_this_item' => __('Uploaded to this Location', parent::THEME_LANG),
            'items_list'            => __('Locations list', parent::THEME_LANG),
            'items_list_navigation' => __('Locations list navigation', parent::THEME_LANG),
            'filter_items_list'     => __('Filter Locations list', parent::THEME_LANG),
        );
        $args = array(
            'label'                 => __('Location', parent::THEME_LANG),
            'description'           => __('Locations for this business', parent::THEME_LANG),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'thumbnail' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'menu_icon'             => 'dashicons-store',
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => false,
            'exclude_from_search'   => true,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'show_in_rest'          => true,
        );
        register_post_type('location', $args);
    }
}

new WooShipLocationCPT;