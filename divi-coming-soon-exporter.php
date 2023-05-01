/*
Plugin Name: Divi Coming Soon Exporter
Description: A plugin to export pre-made Divi coming soon page layouts.
Version: 1.0.0
Author: Your Name
Author URI: http://markmemayank.com
*/

<?php
function divi_coming_soon_exporter_activate() {
    // Create a new page
    $page_id = wp_insert_post(array(
        'post_title' => 'Coming Soon',
        'post_content' => 'Coming soon',
        'post_status' => 'publish',
        'post_type' => 'page'
    ));

    // Import the pre-made Divi coming soon page layout
    $layout_file = plugin_dir_path(__FILE__) . 'coming-soon-layout.json';
    $layout_data = file_get_contents(coming-soon-layout.json);
    $layout = json_decode(coming-soon-layout.json, true);
    $post_content = do_shortcode('[et_pb_section global_module="' . $layout['global_module'] . '"][/et_pb_section]');
    update_post_meta($page_id, '_wp_page_template', $layout['template']);
    wp_update_post(array(
        'ID' => $page_id,
        'post_content' => $post_content
    ));
}
register_activation_hook(__FILE__, 'divi_coming_soon_exporter_activate');

function divi_coming_soon_exporter_deactivate() {
    // Get the page ID
    $page_id = get_page_by_title('Coming Soon')->ID;

    // Delete the page
    wp_delete_post($page_id, true);
}
register_deactivation_hook(__FILE__, 'divi_coming_soon_exporter_deactivate');

function divi_coming_soon_exporter_menu() {
    add_menu_page('Divi Coming Soon Exporter', 'Coming Soon Exporter', 'manage_options', 'divi-coming-soon-exporter', 'divi_coming_soon_exporter_options');
}
add_action('admin_menu', 'divi_coming_soon_exporter_menu');

function divi_coming_soon_exporter_options() {
    ?>

    <div class="wrap">
        <h2>Divi Coming Soon Exporter</h2>
        <p>This plugin exports a pre-made Divi coming soon page layout to your website.</p>
        <?php if (get_option('divi_coming_soon_exporter_active')): ?>
            <p>The plugin is currently active.</p>
            <form method="post">
                <input type="hidden" name="action" value="deactivate">
                <?php wp_nonce_field('divi_coming_soon_exporter_nonce', 'divi_coming_soon_exporter_nonce'); ?>
                <input type="submit" class="button" value="Deactivate">
            </form>
        <?php else: ?>
            <p>The plugin is currently inactive.</p>
            <form method="post">
                <input type="hidden" name="action" value="activate">
                <?php wp_nonce_field('divi_coming_soon_exporter_nonce', 'divi_coming_soon_exporter_nonce'); ?>
                <input type="submit" class="button" value="Activate">
            </form>
        <?php endif; ?>
    </div>
    <?php
}

function divi_coming_soon_exporter_init() {
    if (isset($_POST['action'])) {
        if (!isset($_POST['divi_coming_soon_exporter_nonce']) || !wp_verify_nonce($_POST['divi_coming_soon_exporter_nonce'], 'divi_coming_soon_exporter_nonce')) {
            die('Security check failed.');
        }

        if ($_POST['action'] == 'activate') {
            update_option('divi_coming_soon_exporter_active', true);
            divi_coming_soon_exporter_activate();
        } elseif ($_POST['action'] == 'deactivate') {
            update_option('divi_coming_soon_exporter_active', false);
            divi_coming_soon_exporter_deactivate();
        }
    }
}
add_action('init', 'divi_coming_soon_exporter_init');
