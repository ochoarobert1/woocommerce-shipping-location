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

defined('ABSPATH') || exit;

require_once('vendor/autoload.php');

class WooShipLocation
{
    const PLUGIN_LANG = 'woocommerce-shipping-location';
    const PLUGIN_SLUG = 'woo-location';
    const PLUGIN_VERSION = '1.0.0';

    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'admin_scripts'), 99);
        add_action('wp_enqueue_scripts', array($this, 'public_scripts'), 99);
        add_filter( 'load_textdomain_mofile', array($this, 'plugin_textdomain'), 10, 2 );
    }

    function plugin_textdomain( $mofile, $domain ) {
        if ( self::PLUGIN_LANG === $domain && false !== strpos( $mofile, WP_LANG_DIR . '/plugins/' ) ) {
            $locale = apply_filters( 'plugin_locale', determine_locale(), $domain );
            $mofile = WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) . '/languages/' . $domain . '-' . $locale . '.mo';
        }
        return $mofile;
    }

    /**
     * Dump variables
     */
    public function d()
    {
        call_user_func_array('dump', func_get_args());
    }


    /**
     * Dump variables and die.
     */
    public function dd()
    {
        call_user_func_array('dump', func_get_args());
        die();
    }

    public function admin_scripts()
    {
        wp_enqueue_script('woo-location-admin-functions', plugins_url('/js/woo-location-admin-functions.js', __FILE__), ['jquery'], self::PLUGIN_VERSION, true);
        wp_enqueue_style('woo-location-admin-styles', plugins_url('/css/woo-location-admin-styles.css', __FILE__), [], self::PLUGIN_VERSION, 'all');
        
        $maps_apikey = get_option('wooshiplocation_maps_apikey');
        $coordinates = get_option('wooshiplocation_maps_coordinates');

        if ($maps_apikey != '') {
            wp_enqueue_script('woo-location-google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $maps_apikey, ['jquery', 'woo-location-admin-functions'], null, true);
            wp_localize_script('woo-location-admin-functions', 'custom_admin_url', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'woo_location_apikey' => $maps_apikey,
                'woo_location_center' => $coordinates
            ));
        }
    }

    public function public_scripts()
    {
        wp_enqueue_script('woo-location-public-functions', plugins_url('/js/woo-location-public-functions.js', __FILE__), ['jquery'], self::PLUGIN_VERSION, true);
        wp_enqueue_style('woo-location-public-styles', plugins_url('/css/woo-location-public-styles.css', __FILE__), [], self::PLUGIN_VERSION, 'all');

        $maps_apikey = get_option('wooshiplocation_maps_apikey');
        $coordinates = get_option('wooshiplocation_maps_coordinates');

        if ($maps_apikey != '') {
            wp_enqueue_script('woo-location-google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $maps_apikey, ['jquery', 'woo-location-public-functions'], null, true);
            wp_localize_script('woo-location-public-functions', 'custom_admin_url', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'woo_location_apikey' => $maps_apikey,
                'woo_location_center' => $coordinates
            ));
        }
    }
}

require_once('includes/custom-post-type.php');
require_once('includes/custom-metaboxes.php');
require_once('includes/custom-woocommerce-admin.php');
require_once('includes/custom-woocommerce-public.php');
require_once('includes/custom-shipping-method.php');

new WooShipLocation;
