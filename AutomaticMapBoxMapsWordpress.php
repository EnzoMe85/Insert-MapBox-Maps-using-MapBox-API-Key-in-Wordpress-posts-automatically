function my_map_shortcode() {
    $latitude = get_post_meta(get_the_ID(), 'latitude', true);
    $longitude = get_post_meta(get_the_ID(), 'longitude', true);

    if (empty($latitude) || empty($longitude)) {
        return '';
    }

    return '<img src="' . $map_url . '" />';
}

add_shortcode('my_map', 'my_map_shortcode');

function my_add_map_to_post_content($content) {
    if (is_singular('post')) {
        $map = do_shortcode('[my_map]');
        $content .= $map;
    }

    return $content;
}

add_filter('the_content', 'my_add_map_to_post_content');

function my_add_meta_box() {
    add_meta_box('my_location', 'Posizione', 'my_location_callback', 'post', 'normal', 'default');
}

add_action('add_meta_boxes', 'my_add_meta_box');

function my_location_callback($post) {
    wp_nonce_field(basename(__FILE__), 'my_location_nonce');

    $latitude = get_post_meta($post->ID, 'latitude', true);
    $longitude = get_post_meta($post->ID, 'longitude', true);
?>
    <p>
        <label for="latitude">Latitudine</label>
        <br />
        <input type="text" name="latitude" id="latitude" value="<?php echo esc_attr($latitude); ?>" />
    </p>
    <p>
        <label for="longitude">Longitudine</label>
        <br />
        <input type="text" name="longitude" id="longitude" value="<?php echo esc_attr($longitude); ?>" />
    </p>
<?php
}

function my_save_location_meta($post_id) {
    if (!isset($_POST['my_location_nonce']) || !wp_verify_nonce($_POST['my_location_nonce'], basename(__FILE__))) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['latitude'])) {
        update_post_meta($post_id, 'latitude', sanitize_text_field($_POST['latitude']));
    }

    if (isset($_POST['longitude'])) {
        update_post_meta($post_id, 'longitude', sanitize_text_field($_POST['longitude']));
    }
}

add_action('save_post', 'my_save_location_meta');
