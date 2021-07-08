<?php
/**
 * Woocommerce Admin Functions
 *
 * Creates various functions to admin in Woocommerce
 *
 * @package WooShipLocationAdmin
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

class WC_woo_shipping_settings extends WooShipLocation
{
    public static function init()
    {
        add_filter('woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50);
        add_action('woocommerce_settings_tabs_woo_shipping_settings', __CLASS__ . '::settings_tab');
        add_action('woocommerce_update_options_woo_shipping_settings', __CLASS__ . '::update_settings');
    }
   
    public static function add_settings_tab($settings_tabs)
    {
        $settings_tabs['woo_shipping_settings'] = __('Shipping by Location', parent::PLUGIN_LANG);
        return $settings_tabs;
    }

    public static function settings_tab()
    {
        woocommerce_admin_fields(self::get_settings());
    }

    public static function update_settings()
    {
        woocommerce_update_options(self::get_settings());
    }

    public static function get_settings()
    {
        $settings = array(
           'section_title' => array(
                'id'       => 'wc_woo_shipping_settings_section_title',
                'name'     => __('Location Settings', parent::PLUGIN_LANG),
                'desc'     => '',
                'type'     => 'title'
           ),
           'maps_apikey' => array(
                'id'   => 'wooshiplocation_maps_apikey',
                'name' => __('Google Maps APIKey', parent::PLUGIN_LANG),
                'desc' => __('Add the Google Maps APIKey in order to use it into this custom shipping method', parent::PLUGIN_LANG),
                'type' => 'text'
           ),
           'section_end' => array(
                'id' => 'wc_woo_shipping_settings_section_end',
                'type' => 'sectionend'
           )
       );

        return apply_filters('wc_woo_shipping_settings_settings', $settings);
    }
}

WC_woo_shipping_settings::init();