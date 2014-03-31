<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Purify_WordPress_Menus
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/purify-wp-menues/
 * @copyright 2014 
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
* Delete options from the database while deleting the plugin files
* Run before deleting the plugin
*
* @since   1.0
* @uses    $settings_db_slug
* @uses    $wpdb
*/
// remove settings
delete_option( 'purify_wp_menu_options_set' ); 
// clean DB
global $wpdb;
$wpdb->query( "OPTIMIZE TABLE `" .$wpdb->options. "`" );

