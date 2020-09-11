<?php
/** 
 * Plugin Name: WP Aws S3
 * Plugin URI: https://citrusbug.com/
 * Description: Just another another plugin.
 * Author: Ishan Vyas
 * Author URI: https://citrusbug.com/
 * Text Domain: citrusbug.com
 * Version: 1.0
*/

define( 'WPS3_VERSION', '1.0' );
define( 'WPS3_REQUIRED_WP_VERSION', '4.9' );
define( 'WPS3_PLUGIN', __FILE__ );
define( 'WPS3_PLUGIN_BASENAME', plugin_basename( WPS3_PLUGIN ) );
define( 'WPS3_PLUGIN_NAME', trim( dirname( WPS3_PLUGIN_BASENAME ), '/' ) );
define( 'WPS3_PLUGIN_DIR', untrailingslashit( dirname( WPS3_PLUGIN ) ) );

add_action('admin_menu','wps3_records_modifymenu');

function wps3_records_modifymenu() {
    add_menu_page('WP AWS S3', 'WP AWS S3', 'manage_options', 'wpawss3', 'wpawss3_shortcodes');
    add_options_page('WP AWS S3 Setting', 'WP AWS S3 Setting', 'manage_options', 'wpawss3-setting', 'wpawss3_options_page');
}

function wpawss3_register_settings() {
    add_option( 'wpawss3_db_name');
    add_option( 'wpawss3_host');
    add_option( 'wpawss3_username');
    add_option( 'wpawss3_password');
    add_option( 'wpawss3_aws_key');
    add_option( 'wpawss3_aws_secret_key');
    add_option( 'wpawss3_aws_region');
    add_option( 'wpawss3_aws_version');
    add_option( 'wpawss3_s3_bucket');
    add_option( 'wpawss3_identity_pool_id');
    add_option( 'wpawss3_s3_page_link');
    register_setting( 'wpawss3_options_group', 'wpawss3_db_name', 'wpawss3_callback' );
    register_setting( 'wpawss3_options_group', 'wpawss3_host', 'wpawss3_callback' );
    register_setting( 'wpawss3_options_group', 'wpawss3_username', 'wpawss3_callback' );
    register_setting( 'wpawss3_options_group', 'wpawss3_password', 'wpawss3_callback' );
    register_setting( 'wpawss3_options_group', 'wpawss3_aws_key', 'wpawss3_callback' );
    register_setting( 'wpawss3_options_group', 'wpawss3_aws_secret_key', 'wpawss3_callback' );
    register_setting( 'wpawss3_options_group', 'wpawss3_aws_region', 'wpawss3_callback' );
    register_setting( 'wpawss3_options_group', 'wpawss3_aws_version', 'wpawss3_callback' );
    register_setting( 'wpawss3_options_group', 'wpawss3_s3_bucket', 'wpawss3_callback' );
    register_setting( 'wpawss3_options_group', 'wpawss3_identity_pool_id', 'wpawss3_callback' );
    register_setting( 'wpawss3_options_group', 'wpawss3_s3_page_link', 'wpawss3_callback' );
 }
add_action( 'admin_init', 'wpawss3_register_settings' );
add_role( "uploader", "Uploader", array('read' => true, 'edit_posts'   => true));

function wpawss3_ajax_load_scripts() {
    wp_enqueue_script('wpawss3-script1', 'https://code.jquery.com/jquery-3.5.1.js', array('jquery'));
    wp_localize_script('wpawss3-script1', 'pw1_script_vars', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('wpawss3'),
    )); 
}
add_action('wp_print_scripts', 'wpawss3_ajax_load_scripts');

require_once WPS3_PLUGIN_DIR . '/includes/details.php';
require_once WPS3_PLUGIN_DIR . '/includes/settings.php';
require_once WPS3_PLUGIN_DIR . '/includes/functions.php';
require_once WPS3_PLUGIN_DIR . '/front/index.php';
require_once WPS3_PLUGIN_DIR . '/front/folder-list.php';
require_once WPS3_PLUGIN_DIR . '/front/process.php';
require_once WPS3_PLUGIN_DIR . '/front/process-status.php';
require_once WPS3_PLUGIN_DIR . '/front/view-process-status.php';
require_once WPS3_PLUGIN_DIR . '/front/view_error_reports_processes.php';
require_once WPS3_PLUGIN_DIR . '/front/view_error_reports_files.php';
require_once WPS3_PLUGIN_DIR . '/front/view_completed_reports_files.php';
require_once WPS3_PLUGIN_DIR . '/front/view_completed_reports_processes.php';
require_once WPS3_PLUGIN_DIR . '/front/add-meta.php';
require_once WPS3_PLUGIN_DIR . '/classes/magicWP.php';