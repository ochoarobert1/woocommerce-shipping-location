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
        add_action('woocommerce_after_shipping_rate', array($this, 'custom_button_location_handler'), 999);
        add_action('wp_ajax_calculate_shipping_price', array($this, 'calculate_shipping_price_handler'));
        add_action('wp_ajax_nopriv_calculate_shipping_price', array($this, 'calculate_shipping_price_handler'));
    }

    public function custom_button_location_handler()
    {
        $shipping_for_package_0 = WC()->session->get('shipping_for_package_0');
        $found = false;

        if (isset($shipping_for_package_0['rates']) && ! empty($shipping_for_package_0['rates'])) {
            // Loop through available shipping methods rate data
            foreach ($shipping_for_package_0['rates'] as $rate) {
                // Targeting "Free Shipping"
                if ('woo-location' === $rate->method_id) {
                    $found = true;
                    break;
                }
            }
        }

        if ($found) {
            ?>
<button id="mapSelector"><span class="dashicons dashicons-location"></span> <?php _e('Select Location', parent::PLUGIN_LANG); ?></button>
<?php
        }
    }

    public function custom_shipping_method_map()
    {
        ?>
<div id="mapsContainer" class="custom-metaboxes-hidden">
    <h4><?php _e('Select your current position', parent::PLUGIN_LANG); ?></h4>
    <div id="map_canvas" style="width: 100%; height: 500px;"></div>
    <input id="coordinates" type="hidden" value="" />
    <input id="woo-location_new_price" type="hidden" value="" />
    <button id="select_coordinates"><?php _e('Calculate Shipping', parent::PLUGIN_LANG); ?></button>
</div>
<?php
    }

    public function calculate_shipping_price_handler()
    {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_reporting( E_ALL );
            ini_set( 'display_errors', 1 );
        }

        global $woocommerce;

        $coordinates = $_POST['coordinates'];

        $arr_coordinates = explode(',', $coordinates);
        $arr_locations = new WP_Query(array('post_type' => 'location', 'posts_per_page' => -1));

        $short_distance = 0;
        $i = 1;

        if ($arr_locations->have_posts()) :
            while ($arr_locations->have_posts()) : $arr_locations->the_post();
        $location_coordinates = get_post_meta(get_the_ID(), 'coordinates', true);
        $arr_locations_coordinates = explode(',', $location_coordinates);

        $distance = self::distance(floatval($arr_coordinates[0]), floatval($arr_coordinates[1]), floatval($arr_locations_coordinates[0]), floatval($arr_locations_coordinates[1]), "K");

        if ($i == 1) {
            $short_distance = $distance;
        }

        if ($distance >= $short_distance) {
            $short_distance = $distance;
        }

        $i++;

        endwhile;
        endif;

        $rate_table = array();

        // Get all your existing shipping zones IDS
        $zone_ids = array_keys(array('') + WC_Shipping_Zones::get_zones());

        // Loop through shipping Zones IDs
        foreach ($zone_ids as $zone_id) {
            // Get the shipping Zone object
            $shipping_zone = new WC_Shipping_Zone($zone_id);

            // Get all shipping method values for the shipping zone
            $shipping_methods = $shipping_zone->get_shipping_methods(true, 'values');

            // Loop through each shipping methods set for the current shipping zone
            foreach ($shipping_methods as $instance_id => $shipping_method) {
                // The dump of protected data from the current shipping method
                $rate_table[$shipping_method->id] = $shipping_method->instance_settings['cost'];
            }
        }

        $shipping_price = $rate_table[WC()->session->get('chosen_shipping_methods')[0]];
        $final_price = 0;

        if ($short_distance <= 2.5) {
            $final_price = $shipping_price;
        } else {
            $exact_distance = floor($short_distance);
            
            $discont_distance = $exact_distance - 2;

            $final_price = $shipping_price + ($discont_distance * 0.70);
        }

        $cart = $woocommerce->cart;
        
        $cart->shipping_total = $final_price;
        $total_price = $cart->total;

        $cart->total = $total_price + $final_price;

        parent::d($cart);
        
        wp_send_json_success(number_format($final_price, 2), 200);

        wp_die();
    }

    public function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);
      
            if ($unit == "K") {
                return ($miles * 1.609344);
            } elseif ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }

        
        /*
        echo self::distance(32.9697, -96.80322, 29.46786, -98.53506, "M") . " Miles<br>";
        echo self::distance(32.9697, -96.80322, 29.46786, -98.53506, "K") . " Kilometers<br>";
        echo self::distance(32.9697, -96.80322, 29.46786, -98.53506, "N") . " Nautical Miles<br>";
        */
    }
}

new WC_Public_Shipping_Settings;
