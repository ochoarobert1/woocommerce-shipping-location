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

    const THEME_LANG = 'woocommerce-shipping-location';
    const THEME_SLUG = 'woo_ship_location';

    public function __construct() {
        
    }
}

require_once('includes/custom-post-type.php');

new WooShipLocation;