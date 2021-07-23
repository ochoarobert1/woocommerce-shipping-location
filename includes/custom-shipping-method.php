<?php



if (! defined('WPINC')) {
    die;
}

//Works with WooCommerce 3.2.6
add_action('woocommerce_shipping_init', 'location_shipping_method');

function location_shipping_method()
{
    if (! class_exists('WC_Location_Shipping_Method')) {

        class WC_Location_Shipping_Method extends WC_Shipping_Method
        {
            const PLUGIN_LANG = 'woocommerce-shipping-location';
            const PLUGIN_SLUG = 'woo-location';
            const PLUGIN_VERSION = '1.0.0';
    
            public function __construct($instance_id = 0)
            {
                $this->instance_id 	  = absint($instance_id);
                $this->id                 = self::PLUGIN_SLUG;//this is the id of our shipping method
                $this->method_title       = __('Shipping by Location', self::PLUGIN_LANG);
                $this->method_description = __('Delivery by Selected Location', self::PLUGIN_LANG);
                $this->supports = array(
                        'shipping-zones',
                        //'settings', //use this for separate settings page
                        'instance-settings',
                        'instance-settings-modal',
                );
                $this->title = __('Shipping by Location', self::PLUGIN_LANG);
                $this->enabled = 'yes';
                $this->init();
            }

            public function init()
            {
                // Load the settings API
                $this->init_form_fields();
                $this->init_settings();

                // Save settings in admin if you have any defined
                add_action('woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ));
            }

            //Fields for the settings page
            public function init_form_fields()
            {
                //fileds for the modal form from the Zones window
                $this->instance_form_fields = array(

                'title' => array(
                    'title' => __('Title', self::PLUGIN_LANG),
                    'type' => 'text',
                    'description' => __('Title to be display on site', self::PLUGIN_LANG),
                    'default' => __('Shipping by Location ', self::PLUGIN_LANG)
                    ),

                'cost' => array(
                    'title' => __('Minimum Cost (2 km)', self::PLUGIN_LANG),
                    'type' => 'number',
                    'description' => __('Minimum Cost of shipping', self::PLUGIN_LANG),
                    'default' => 2
                    ),

                );

                //$this->form_fields - use this with the same array as above for setting fields for separate settings page
            }

            public function calculate_shipping($package = array())
            {
                //as we are using instances for the cost and the title we need to take those values drom the instance_settings
                $intance_settings =  $this->instance_settings;
                // Register the rate
                $this->add_rate(
                    array(
                        'id'      => $this->id,
                        'label'   => $intance_settings['title'],
                        'cost'    => $intance_settings['cost'],
                        'fee'     => $intance_settings['cost'],
                        'minimum_fee'    => $intance_settings['cost'],
                        'package' => $package,
                        'taxes'   => false,
                    )
                );
            }
        }
    }

    //add your shipping method to WooCommers list of Shipping methods
    add_filter('woocommerce_shipping_methods', 'add_location_shipping_method');
    
    function add_location_shipping_method($methods)
    {
        $methods[WC_Location_Shipping_Method::PLUGIN_SLUG] = 'WC_Location_Shipping_Method';
        return $methods;
    }
}
