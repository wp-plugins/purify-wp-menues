<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://stehle-internet.de/
 * @since      3.0
 *
 * @package    Hinjipwpm
 * @subpackage Hinjipwpm/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      3.0
 * @package    Hinjipwpm
 * @subpackage Hinjipwpm/includes
 * @author     Martin Stehle <m.stehle@gmx.de>
 */
class Hinjipwpm {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    3.0
	 * @access   protected
	 * @var      Hinjipwpm_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    3.0
	 * @access   protected
	 * @var      string    $hinjipwpm    The string used to uniquely identify this plugin.
	 */
	protected $hinjipwpm;

	/**
	 * The current version of the plugin.
	 *
	 * @since    3.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The slug of the plugin's settings in the WP options table
	 *
	 * @since    2.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $settings_db_slug;

	/**
	 * The settings of the plugin as stored in the WP options table, else default settings.
	 *
	 * @since    2.0
	 * @access   protected
	 * @var      array    $current_settings    The settings of the plugin as stored in the WP options table, else default settings.
	 */
	protected $current_settings;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    3.0
	 */
	public function __construct() {

		$this->hinjipwpm = 'hinjipwpm';
		$this->version = '3.0';
		$this->settings_db_slug = 'purify_wp_menu_options_set';

		$this->load_dependencies();
		$this->set_locale();
		$this->get_stored_settings();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Hinjipwpm_Loader. Orchestrates the hooks of the plugin.
	 * - Hinjipwpm_i18n. Defines internationalization functionality.
	 * - Hinjipwpm_Admin. Defines all hooks for the dashboard.
	 * - Hinjipwpm_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    3.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hinjipwpm-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hinjipwpm-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-hinjipwpm-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-hinjipwpm-public.php';

		$this->loader = new Hinjipwpm_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Hinjipwpm_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    3.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Hinjipwpm_i18n();
		$plugin_i18n->set_domain( $this->get_hinjipwpm() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    3.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Hinjipwpm_Admin( $this->get_hinjipwpm(), $this->get_version(), $this->get_stored_settings(), $this->get_settings_db_slug() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		//$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		// Add the options page and menu item.
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'add_plugin_admin_menu' );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . 'purify_wp_menus.php' );
		$this->loader->add_action( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'add_action_links' );

		// define the options
		$this->loader->add_action( 'admin_init', $plugin_admin, 'register_options' );

		// hook on displaying a message after plugin activation
		// if single activation via link or multisite activation
		if ( isset( $_GET[ 'activate' ] ) or isset( $_GET[ 'activate-multi' ] ) ) {
			$plugin_was_activated = get_transient( 'purify_wp_menues' );
			if ( false !== $plugin_was_activated ) {
				$this->loader->add_action( 'admin_notices', $plugin_admin, 'display_activation_message' );
				delete_transient( 'purify_wp_menues' );
			}
		}
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    3.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Hinjipwpm_Public( $this->get_hinjipwpm(), $this->get_version(), $this->get_stored_settings() );

		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		// Purify WP custom menus
		$this->loader->add_action( 'nav_menu_css_class', $plugin_public, 'purify_custom_menu_item_classes', 10, 2 );

		// Purify WP page menus
		$this->loader->add_action( 'page_css_class', $plugin_public, 'purify_page_menu_item_classes', 10, 2 );

		// Purify item id in WP custom menus
		//to do: move if-clause to function purify_custom_menu_item_id
		if ( 0 == $this->current_settings[ 'pwpm_print_menu_item_id' ] ) {
			$this->loader->add_action( 'nav_menu_item_id', $plugin_public, 'purify_custom_menu_item_id' );
		}

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    3.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     3.0
	 * @return    string    The name of the plugin.
	 */
	public function get_hinjipwpm() {
		return $this->hinjipwpm;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     3.0
	 * @return    Hinjipwpm_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     3.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Return the options slug in the WP options table.
	 *
	 * @since    2.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_settings_db_slug() {
		return $this->settings_db_slug;
	}

	/**
	 * Get current or default settings
	 *
	 * @since    2.0
	 */
	public function get_stored_settings() {

		// if no settings defined: get them
		if ( ! $this->current_settings ) {
			// try to load current settings. If they are not in the DB return set default settings
			$this->current_settings = get_option( $this->settings_db_slug, false );
			// if empty array set and store default values
			if ( false === $this->current_settings ) {
				$this->set_default_settings();
			}
			// try to load current settings again. Now there should be the data
			$this->current_settings = get_option( $this->settings_db_slug );
		}
		
		// return settings
		return $this->current_settings; # todo: return $this->sanitize_options( $current_settings );
	}
	
	/**
	 * Set default settings
	 *
	 * @since    2.0
	 */
	private static function set_default_settings() {

		$db_slug = 'purify_wp_menu_options_set'; // todo: get the value from $settings_db_slug both in object and non-object context
		// check if there are already stored settings under the option's database slug
		if ( false === get_option( $db_slug, false ) ) {
			// store default values in the db as a single and serialized entry
			add_option( 
				$db_slug, 
				array(
					'pwpm_backward_compatibility_with_wp_page_menu' => 0,
					'pwpm_do_not_print_parent_as_ancestor' => 0,
					'pwpm_print_current_menu_ancestor' => 0,
					'pwpm_print_current_menu_item' => 1,
					'pwpm_print_current_menu_parent' => 0,
					'pwpm_print_current_object_any_ancestor' => 0,
					'pwpm_print_current_object_any_parent' => 0,
					'pwpm_print_current_page_ancestor' => 0,
					'pwpm_print_current_page_item' => 1,
					'pwpm_print_current_page_parent' => 0,
					'pwpm_print_current_taxonomy_ancestor' => 0,
					'pwpm_print_current_type_any_ancestor' => 0,
					'pwpm_print_current_type_any_parent' => 0,
					'pwpm_print_menu_item' => 0,
					'pwpm_print_menu_item_has_children' => 0,
					'pwpm_print_menu_item_home' => 0,
					'pwpm_print_menu_item_id' => 0,
					'pwpm_print_menu_item_id_as_class' => 0,
					'pwpm_print_menu_item_object_any' => 0,
					'pwpm_print_menu_item_object_category' => 0,
					'pwpm_print_menu_item_object_custom' => 0,
					'pwpm_print_menu_item_object_page' => 0,
					'pwpm_print_menu_item_object_tag' => 0,
					'pwpm_print_menu_item_type_any' => 0,
					'pwpm_print_menu_item_type_post_type' => 0,
					'pwpm_print_menu_item_type_taxonomy' => 0,
					'pwpm_print_page_item' => 0,
					'pwpm_print_page_item_has_children' => 0,
					'pwpm_print_page_item_id' => 0,
				)
			);
		} // if ( false )
		
		/** 
		* to do: finish check
		* // test if the options are stored successfully
		* if ( false === get_option( $this->settings_db_slug ) ) {
		* 	// warn if there is something wrong with the options
		* 	something like: printf( '<div class="error"><p>%s</p></div>', __( 'The settings for plugin Purify WP Menus are not stored in the database. Is the database server ok?', $this->hinjipwpm ) );
		* }
		*/
	}

}
