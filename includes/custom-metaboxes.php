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
        add_action('save_post', array($this, 'save_postdata'));
    }

    public function custom_metaboxes()
    {
        $screens = [ 'location' ];
        foreach ($screens as $screen) {
            add_meta_box(
                'location_box_id',
                __('Extra Information', parent::PLUGIN_LANG),
                array($this, 'custom_box_html'),
                $screen
            );
        }
    }

    public function custom_box_html($post)
    {
        ?>
<div class="custom-metaboxes-table">
    <div class="custom-metaboxes-table-item">
        <?php $min_distance = get_post_meta($post->ID, 'min_distance', true); ?>
        <label for="min_distance" class="custom-label-attributes"><span class="dashicons dashicons-remove"></span> <?php _e('Minimun Distance (in km)', parent::PLUGIN_LANG) ?></label>
        <input class="custom-input-attributes" type="number" name="min_distance" id="min_distance" value="<?php echo $min_distance; ?>" />
    </div>
    <div class="custom-metaboxes-table-item">
        <?php $max_distance = get_post_meta($post->ID, 'max_distance', true); ?>
        <label for="max_distance" class="custom-label-attributes"><span class="dashicons dashicons-insert"></span> <?php _e('Maximum Distance (in km)', parent::PLUGIN_LANG) ?></label>
        <input class="custom-input-attributes" type="number" name="max_distance" id="max_distance" value="<?php echo $max_distance; ?>" />
    </div>
    <div class="custom-metaboxes-table-item">
        <?php $email_address = get_post_meta($post->ID, 'email_address', true); ?>
        <label for="email_address" class="custom-label-attributes"><span class="dashicons dashicons-email"></span> <?php _e('Email Address', parent::PLUGIN_LANG) ?></label>
        <input class="custom-input-attributes" type="email" name="email_address" id="email_address" value="<?php echo $email_address; ?>" />
    </div>
    <div class="custom-metaboxes-table-item">
        <?php $coordinates = get_post_meta($post->ID, 'min_distance', true); ?>
        <label for="coordinates" class="custom-label-attributes"><span class="dashicons dashicons-location"></span> <?php _e('Coordinates (in Google Maps)', parent::PLUGIN_LANG) ?> <button id="mapSelector" class="map-selector" title="<?php _e('Select coordinates using google maps', parent::PLUGIN_LANG); ?>"><span class="dashicons dashicons-location"></span></button></label>
        <input class="custom-input-attributes" type="text" name="coordinates" id="coordinates" placeholder="<?php _e('E.G. 25.7825452,-80.2996705', parent::PLUGIN_LANG); ?>" value="<?php echo $coordinates; ?>" />
    </div>
</div>
<?php
    }

    public function save_postdata($post_id)
    {
        if (array_key_exists('min_distance', $_POST)) {
            update_post_meta(
                $post_id,
                'min_distance',
                $_POST['min_distance']
            );
        }

        if (array_key_exists('max_distance', $_POST)) {
            update_post_meta(
                $post_id,
                'max_distance',
                $_POST['max_distance']
            );
        }

        if (array_key_exists('email_address', $_POST)) {
            update_post_meta(
                $post_id,
                'email_address',
                $_POST['email_address']
            );
        }

        if (array_key_exists('coordinates', $_POST)) {
            update_post_meta(
                $post_id,
                'coordinates',
                $_POST['coordinates']
            );
        }
    }
}

new WooShipLocationMetabox;
