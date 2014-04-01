<?php
/**
 * @package   Purify_WordPress_Menus
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/purify-wp-menues/
 * @copyright 2014 
 *
 * @wordpress-plugin
 * Plugin Name:      Purify WordPress Menus
 * Plugin URI:       http://wordpress.org/plugins/purify-wp-menues/
 * Description:      Slim down the HTML code of WordPress menus to only the CSS classes and ID attributes your theme needs to improve page speed
 * Version:          2.1
 * Author:           Martin Stehle
 * Author URI:       http://stehle-internet.de/
 * Text Domain:      purify_wp_menues
 * License:          GPL-2.0+
 * License URI:      http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:      /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-purify-wordpress-menus.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Purify_WordPress_Menus', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Purify_WordPress_Menus', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'Purify_WordPress_Menus', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-purify-wordpress-menus-admin.php' );
	add_action( 'plugins_loaded', array( 'Purify_WordPress_Menus_Admin', 'get_instance' ) );

}
