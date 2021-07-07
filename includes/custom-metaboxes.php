<?php
/**
 * Custom Metaboxes for Location
 *
 * Creates a new metaboxes for location post type
 *
 * @package WooShipLocationMetabox
 * @version 1.0.0
 */

defined('ABSPATH') || exit;

class WooShipLocationMetabox extends WooShipLocation
{
    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'custom_metaboxes'), 0);
    }

    public function custom_metaboxes()
    {
        $screens = [ 'location' ];
        foreach ($screens as $screen) {
            add_meta_box(
                'location_box_id',                 // Unique ID
                __('Extra Information', parent::PLUGIN_LANG),      // Box title
                array($this, 'custom_box_html'),  // Content callback, must be of type callable
                $screen                            // Post type
            );
        }
    }

    public function custom_box_html($post)
    {
        ?>
<div class="custom-metaboxes-table">
    <div class="custom-metaboxes-table-item">
        <label for="min_distance" class="custom-label-attributes"><?php _e('Minimun Distance (in km)', parent::PLUGIN_LANG) ?></label>
        <input class="custom-input-attributes" type="number" name="min_distance" id="min_distance" />
    </div>
    <div class="custom-metaboxes-table-item">
        <label for="max_distance" class="custom-label-attributes"><?php _e('Maximum Distance (in km)', parent::PLUGIN_LANG) ?></label>
        <input class="custom-input-attributes" type="number" name="max_distance" id="max_distance" />
    </div>
    <div class="custom-metaboxes-table-item">
        <label for="email_address" class="custom-label-attributes"><?php _e('Email Address', parent::PLUGIN_LANG) ?></label>
        <input class="custom-input-attributes" type="email" name="email_address" id="email_address" />
    </div>
    <div class="custom-metaboxes-table-item">
        <label for="coordinates" class="custom-label-attributes"><?php _e('Coordinates (in Google Maps)', parent::PLUGIN_LANG) ?></label>
        <input class="custom-input-attributes" type="text" name="coordinates" id="coordinates" placeholder="<?php _e('E.G. 25.7825452,-80.2996705', parent::PLUGIN_LANG); ?>" />
    </div>
</div>
<?php
    }
}

new WooShipLocationMetabox;