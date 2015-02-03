<?php

/**
 * The plugin bootstrap file
 *
 * @link              http://stehle-internet.de/
 * @since             3.0
 * @package           Hinjipwpm
 *
 * @wordpress-plugin
 * Plugin Name:       Purify WordPress Menus
 * Plugin URI:        https://wordpress.org/plugins/purify-wp-menues/
 * Description:       Slim down the HTML code of WordPress menus to only the CSS classes and ID attributes your theme needs to improve page speed
 * Version:           3.0
 * Author:            Martin Stehle
 * Author URI:        http://stehle-internet.de/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hinjipwpm
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-hinjipwpm-activator.php
 */
function activate_hinjipwpm() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hinjipwpm-activator.php';
	Hinjipwpm_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-hinjipwpm-deactivator.php
 */
function deactivate_hinjipwpm() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hinjipwpm-deactivator.php';
	Hinjipwpm_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_hinjipwpm' );
register_deactivation_hook( __FILE__, 'deactivate_hinjipwpm' );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-hinjipwpm.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    3.0
 */
function run_hinjipwpm() {

	$plugin = new Hinjipwpm();
	$plugin->run();

}
run_hinjipwpm();
