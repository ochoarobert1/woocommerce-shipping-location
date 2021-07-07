<?php
/**
 * Plugin Name: WooCommerce Shipping by Location
 * Plugin URI: https://robertochoaweb.com/
 * Description: Plugin for Locations and Shipping Overrides
 * Version: 1.0.0
 * Author: Robert Ochoa
 * Author URI: https://robertochoaweb.com
 * Text Domain: woocommerce-shipping-location
 * Domain Path: /languages/
 * Requires at least: 5.5
 * Requires PHP: 7.0
 *
 * @package WooShipLocation
 */

defined( 'ABSPATH' ) || exit;

class WooShipLocation 
{
    const PLUGIN_LANG = 'woocommerce-shipping-location';
    const PLUGIN_VERSION = '1.0.0';

    public function __construct() {
        add_action( 'admin_enqueue_scripts', array($this, 'admin_scripts'), 99 );
        add_action( 'wp_enqueue_scripts', array($this, 'public_scripts'), 99 );
    }

    public function admin_scripts() {
        wp_enqueue_script( 'woo-location-admin-functions', plugins_url( '/js/woo-location-admin-functions.js', __FILE__ ), ['jquery'], self::PLUGIN_VERSION, true );
        wp_enqueue_style( 'woo-location-admin-styles', plugins_url( '/css/woo-location-admin-styles.css', __FILE__ ), [], self::PLUGIN_VERSION, 'all' );
    }

    public function public_scripts() {
        wp_enqueue_script( 'woo-location-public-functions', plugins_url( '/js/woo-location-public-functions.js', __FILE__ ), ['jquery'], self::PLUGIN_VERSION, true );
        wp_enqueue_style( 'woo-location-public-styles', plugins_url( '/css/woo-location-public-styles.css', __FILE__ ), [], self::PLUGIN_VERSION, 'all' );
    }
}

require_once('includes/custom-post-type.php');
require_once('includes/custom-metaboxes.php');

new WooShipLocation;