<?php
/**
 * Woocommerce Public Functions
 *
 * Creates various functions to public in Woocommerce
 *
 * @package WC_Public_Shipping_Settings
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

class WC_Public_Shipping_Settings extends WooShipLocation
{
    public function __construct()
    {
        add_action('woocommerce_cart_totals_after_order_total', array($this, 'custom_shipping_method_map'), 999);
    }

    public function custom_shipping_method_map()
    {
        ?>
        <div id="mapsContainer" class="custom-metaboxes-hidden">
            <div id="map_canvas" style="width: 100%; height: 500px;"></div>
        </div>
        <?php

    }
}

new WC_Public_Shipping_Settings;
