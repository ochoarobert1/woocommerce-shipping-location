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
        add_filter('woocommerce_calculated_total', array($this, 'change_calculated_total'), 10, 2);
        add_filter('woocommerce_package_rates', array($this, 'woocommerce_cart_shipping_total_filter_callback'), 100, 2);

        add_action('woocommerce_after_order_notes', array($this, 'my_custom_checkout_field' ));
        add_action('woocommerce_checkout_update_order_meta', array($this, 'my_custom_checkout_field_update_order_meta' ));
        
        add_filter('woocommerce_email_headers', array($this, 'custom_cc_email_headers'), 10, 3);
        add_filter('woocommerce_email_order_meta_fields', array($this, 'custom_woocommerce_email_order_meta_fields'), 10, 3);
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
            if (!is_checkout()) {
                ?>
<button id="mapSelector"><span class="dashicons dashicons-location"></span> <?php _e('Select Location', parent::PLUGIN_LANG); ?></button>
<?php
            }
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
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
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

        if ($distance <= $short_distance) {
            $short_distance = $distance;
            $location_id = get_the_ID();
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

        if (! WC()->cart->prices_include_tax) {
            $total_price = WC()->cart->cart_contents_total;
        } else {
            $total_price = WC()->cart->cart_contents_total + WC()->cart->tax_total;
        }

        $total = floatval($total_price) + floatval($final_price);

        WC()->session->__unset('total');
        WC()->session->__unset('shipping_total');
        WC()->session->__unset('coordinates');
        WC()->session->__unset('location_id');

        WC()->session->set('total', floatval($total));
        WC()->session->set('shipping_total', floatval($final_price));
        WC()->session->set('coordinates', $coordinates);
        WC()->session->set('location_id', $location_id);

        ob_start(); ?>
<bdi><span class="woocommerce-Price-currencySymbol">$</span><?php echo number_format($final_price, 2); ?></bdi>
<?php
        $content = ob_get_clean();

        ob_start(); ?>
<bdi><span class="woocommerce-Price-currencySymbol">$</span><?php echo number_format($total_price + $final_price, 2); ?></bdi>
<?php
        $content2 = ob_get_clean();

        $response = array(
            'shipping_price' => $final_price,
            'shipping_price_html' => $content,
            'total_price'    => $total,
            'total_price_html'    => $content2
        );
        
        wp_send_json_success($response, 200);

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
    }

    public function change_calculated_total($total, $cart)
    {
        if (WC()->session->get('total')) {
            $total = floatval(WC()->session->get('total'));
            return number_format(floatval($total), 2, '.', ',');
        } else {
            return $total;
        }
    }

    public function woocommerce_cart_shipping_total_filter_callback($rates, $package)
    {
        if (WC()->session->get('shipping_total')) {
            $targeted_rate_id = 'woo-location';
    
            foreach ($rates as $rate_key => $rate) {
                if ($targeted_rate_id === $rate_key) {
                    $rate_cost = $rate->cost;

                    if ($rate_cost < 100) {
                        $rate_label = __('Subsidized shipping fee');
                    } elseif ($rate_cost >= 100) {
                        $rate_label = __('Flat rate shipping fee');
                    }

                    $total = floatval(WC()->session->get('shipping_total'));
            
                    if (isset($rate_label)) {
                        $rates[$rate_key]->cost = number_format(floatval($total), 2, '.', ',');
                    }
                }
            }
        }
        return $rates;
    }

    public function my_custom_checkout_field($checkout)
    {
        woocommerce_form_field('location_id', array(
            'type'          => 'hidden',
            'autocomplete'  => 'off',
            'class'         => array('my-field-class time-takeout-class form-row-wide d-none'),
        ), WC()->session->get('location_id'));

        woocommerce_form_field('coordinates', array(
            'type'          => 'hidden',
            'autocomplete'  => 'off',
            'class'         => array('my-field-class time-takeout-class form-row-wide d-none'),
        ), WC()->session->get('coordinates'));
    }

    /**
    * Update the order meta with field value
    */

    public function my_custom_checkout_field_update_order_meta($order_id)
    {
        WC()->session->__unset('total');
        WC()->session->__unset('shipping_total');
        WC()->session->__unset('coordinates');
        WC()->session->__unset('location_id');
        
        if (! empty($_POST['location_id'])) {
            update_post_meta($order_id, 'location_id', sanitize_text_field($_POST['location_id']));
        }
        if (! empty($_POST['coordinates'])) {
            update_post_meta($order_id, 'coordinates', sanitize_text_field($_POST['coordinates']));
        }
    }

    
    public function custom_cc_email_headers($header, $email_id, $order)
    {
        $order_id  = $order->get_id();

        $location_id = get_post_meta($order_id, 'location_id', true);
        $location = get_post($location_id);
        $email_address = get_post_meta($location_id, 'email_address', true);
        $location_name = $location->post_title;

        $formatted_email = utf8_decode($location_name . ' <' . $email_address . '>');
        $header .= 'Cc: '.$formatted_email .'\r\n';

        return $header;
    }

    

    public function custom_woocommerce_email_order_meta_fields($fields, $sent_to_admin, $order)
    {
        $order_id  = $order->get_id();
        $google_address = get_post_meta($order_id, 'coordinates', true);

        $fields['coordinates'] = array(
            'label' => __('Dirección de Entrega'),
            'value' => '<a href="https://www.google.com.ec/maps/place/' . $google_address . '" target="_blank">Ver en Google Maps</a>',
        );
        return $fields;
    }
}

new WC_Public_Shipping_Settings;
