<?php
/**
 * Purify WordPress Menus.
 *
 * @package   Purify_WordPress_Menus
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/purify-wp-menues/
 * @copyright 2014 
 */

class Purify_WordPress_Menus {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since    2.0
	 *
	 * @var     string
	 */
	const VERSION = '2.1.1';

	/**
	 * Lowest Wordpress version to run with this plugin
	 *
	 * @since    2.0
	 *
	 * @var     string
	 */
	const REQUIRED_WP_VERSION = '3.0'; // because of wp menu functions

	/**
	 * Name of this plugin.
	 *
	 *
	 * @since    2.1.1
	 *
	 * @var      string
	 */
	protected static $plugin_name = 'Purify WordPress Menus';

	/**
	 * Unique identifier for this plugin.
	 *
	 *
	 * The variable is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    2.0
	 *
	 * @var      string
	 */
	protected static $plugin_slug = 'purify_wp_menues';

	/**
	 * Instance of this class.
	 *
	 * @since    2.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Unique identifier in the WP options table
	 *
	 *
	 * @since    2.0
	 *
	 * @var      string
	 */
	protected static $settings_db_slug = 'purify_wp_menu_options_set';

	/**
	 * Stored settings in an array
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	protected static $stored_settings = array();

	/**
	 * Initial and default settings for the plugin's start
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	protected static $default_settings = array();
	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( __CLASS__, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( __CLASS__, 'activate_new_site' ) );

		// load options once. If the options are not in the DB return an empty array
		self::$stored_settings = get_option( self::$settings_db_slug );
		if ( ! is_admin() ) {
			add_filter( 'nav_menu_css_class', array( __CLASS__, 'purify_menu_item_classes' ), 10, 1 );
			add_filter( 'page_css_class',     array( __CLASS__, 'purify_page_item_classes' ), 10, 1 );
			if ( 0 == self::$stored_settings[ 'pwpm_print_menu_item_id' ] ) {
				add_filter( 'nav_menu_item_id', array( __CLASS__, 'purify_menu_item_id' ), 10, 0 );
			}
		}

		// hook on displaying a message after plugin activation
		// if single activation via link or bulk activation
		if ( isset( $_GET[ 'activate' ] ) or isset( $_GET[ 'activate-multi' ] ) ) {
			$plugin_was_activated = get_transient( self::$plugin_slug );
			if ( false !== $plugin_was_activated ) {
				add_action( 'admin_notices', array( $this, 'display_activation_message' ) );
				delete_transient( self::$plugin_slug );
			}
		}
	}

	/**
	 * Return the plugin name.
	 *
	 * @since    2.1.1
	 *
	 * @return    Plugin name variable.
	 */
	public function get_plugin_name() {
		return self::$plugin_name;
	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    2.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return self::$plugin_slug;
	}

	/**
	 * Return the options slug in the WP options table.
	 *
	 * @since    2.0
	 *
	 * @return    Plugin slug variable.
	 */
	public function get_settings_db_slug() {
		return self::$settings_db_slug;
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    2.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		// check minimum version
		if ( ! version_compare( $GLOBALS['wp_version'], self::REQUIRED_WP_VERSION, '>=' ) ) {
			// deactivate plugin
			deactivate_plugins( plugin_basename( __FILE__ ), false, is_network_admin() );
			// load language file for a message in the language of the WP installation
			self::load_plugin_textdomain();
			// stop WP request and display the message with backlink. Is there a proper way than wp_die()?
			wp_die( 
				// message in browser viewport
				sprintf( 
					'<p>%s</p>', 
					sprintf( 
						__( 'The plugin requires WordPress version %s or higher. Therefore, WordPress did not activate it. If you want to use this plugin update the Wordpress files to the latest version.', self::$plugin_slug ), 
						self::REQUIRED_WP_VERSION 
					)
				),
				// title in title tag
				'Wordpress &rsaquo; Plugin Activation Error', 
				array( 
					// HTML status code returned
					'response'  => 200, 
					// display a back link in the returned page
					'back_link' => true 
				)
			);
		}

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide  ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    2.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    2.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    2.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    2.0
	 */
	private static function single_activate() {
		// store default settings
		self::set_default_settings();
		// store the flag into the db to trigger the display of a message after activation
		set_transient( self::$plugin_slug, '1', 60 );
	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    2.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.0
	 */
	public static function load_plugin_textdomain() {

		#$domain = self::$plugin_slug;
		#$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		#load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( self::$plugin_slug, false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );

	}

	/* -------------- Plugin specific functions --------------- */

	/**
	 * Set default settings
	 *
	 * @since    2.0
	 */
	private static function set_default_settings() {
		if ( ! current_user_can( 'manage_options' ) )  {
			// use WordPress standard message for this case
			wp_die( __( 'You do not have sufficient permissions to manage options for this site.' ) );
		}
		
		// define the default settings
		self::$default_settings = array(
			'pwpm_print_menu_item_id' => 0,
			'pwpm_backward_compatibility_with_wp_page_menu' => 0,
			'pwpm_do_not_print_parent_as_ancestor' => 0,
			'pwpm_print_current_menu_ancestor' => 0,
			'pwpm_print_current_menu_item' => 1,
			'pwpm_print_current_menu_parent' => 0,
			'pwpm_print_current_object_any_parent' => 0,
			'pwpm_print_current_object_any_ancestor' => 0,
			'pwpm_print_current_page_item' => 1,
			'pwpm_print_current_page_parent' => 0,
			'pwpm_print_current_page_ancestor' => 0,
			'pwpm_print_current_type_any_parent' => 0,
			'pwpm_print_current_type_any_ancestor' => 0,
			'pwpm_print_menu_item' => 0,
			'pwpm_print_menu_item_home' => 0,
			'pwpm_print_menu_item_id_as_class' => 0,
			'pwpm_print_menu_item_object_category' => 0,
			'pwpm_print_menu_item_object_page' => 0,
			'pwpm_print_menu_item_object_tag' => 0,
			'pwpm_print_menu_item_object_custom' => 0,
			'pwpm_print_menu_item_object_any' => 0,
			'pwpm_print_menu_item_type_post_type' => 0,
			'pwpm_print_menu_item_type_taxonomy' => 0,
			'pwpm_print_page_item' => 0,
			'pwpm_print_page_item_id' => 0,
		);
		
		// store default values in the db as a single and serialized entry
		add_option( self::$settings_db_slug, self::$default_settings );
		
		/** 
		* to do: finish check
		* // test if the options are stored successfully
		* if ( false === get_option( self::$settings_db_slug ) ) {
		* 	// warn if there is something wrong with the options
		* 	something like: printf( '<div class="error"><p>%s</p></div>', __( 'The settings for plugin Purify WP Menus are not stored in the database. Is the database server ok?', 'purify_wp_menus' ) );
		* }
		*/
	}

	/**
	 * Get current or default settings
	 *
	 * @since    2.0
	 */
	public function get_stored_settings() {
		// try to load current settings. If they are not in the DB return set default settings
		$stored_settings = get_option( self::$settings_db_slug, array() );
		// if empty array set and store default values
		if ( empty( $stored_settings ) ) {
			$this->set_default_settings();
		}
		// try to load current settings again. Now there should be the data
		$stored_settings = get_option( self::$settings_db_slug );
		
		return $stored_settings;
	}
	
	/**
	* Clean the CSS classes of items in navigation menus
	*
	* @since   1.0
	*
	* @param   array    $css_classes    Strings wp_nav_menu() builded for a single menu item
	* @uses    $stored_settings
	* @uses    purify_page_item_classes()
	* @return  array|string             Empty string if param is not an array, else the array with strings for the menu item
	*/
	public static function purify_menu_item_classes ( $css_classes ) {
		if ( ! is_array( $css_classes ) ) {
			return '';
		}

		$item_is_parent = false;
		$classes = array();
		$options = self::$stored_settings;

		foreach ( $css_classes as $class ) {

			// This class is added to every menu item. 
			if ( $options['pwpm_print_menu_item'] && 'menu-item' == $class ) {
				$classes[] = 'menu-item';
				continue;
			}

			// This class with the item id is added to every menu item. 
			if ( $options['pwpm_print_menu_item_id_as_class'] && preg_match( '/menu-item-[0-9]+/', $class, $matches ) ) {
				$classes[] = $matches[0]; # 'menu-item-' . $item->ID;
				continue;
			}

			// This class is added to menu items that correspond to a category. 
			if ( $options['pwpm_print_menu_item_object_category'] && 'menu-item-object-category' == $class ) {
				$classes[] = 'menu-item-object-category';
				continue;
			}

			// This class is added to menu items that correspond to a tag. 
			if ( $options['pwpm_print_menu_item_object_tag'] && 'menu-item-object-tag' == $class ) {
				$classes[] = 'menu-item-object-tag';
				continue;
			}

			// This class is added to menu items that correspond to static pages. 
			if ( $options['pwpm_print_menu_item_object_page'] && 'menu-item-object-page' == $class ) {
				$classes[] = 'menu-item-object-page';
				continue;
			}

			// This class is added to every menu item, where {object} is either a post type or a taxonomy.
			if ( $options['pwpm_print_menu_item_object_any'] && preg_match( '/menu-item-object-[^-]+/', $class, $matches ) ) {
				$classes[] = $matches[0];
				continue;
			}

			/* double of menu_item_object_any? */
			// This class is added to menu items that correspond to a custom post type or a custom taxonomy. 
			if ( $options['pwpm_print_menu_item_object_custom'] && preg_match( '/menu-item-object-[^-]+/', $class, $matches ) ) {
				$classes[] = $matches[0];
				continue;
			}

			// This class is added to menu items that correspond to post types { i.e. static pages or custom post types. 
			if ( $options['pwpm_print_menu_item_type_post_type'] && 'menu-item-type-post_type' == $class ) {
				$classes[] = 'menu-item-type-post_type';
				continue;
			}

			// This class is added to menu items that correspond to taxonomies { i.e. categories, tags, or custom taxonomies. 
			if ( $options['pwpm_print_menu_item_type_taxonomy'] && 'menu-item-type-taxonomy' == $class ) {
				$classes[] = 'menu-item-type-taxonomy';
				continue;
			}

			// This class is added to menu items that correspond to the currently rendered page. 
			if ( $options['pwpm_print_current_menu_item'] && 'current-menu-item' == $class ) {
				$classes[] = 'current-menu-item';
				continue;
			}

			// This class is added to menu items that correspond to the hierarchical parent of the currently rendered page. 
			if ( $options['pwpm_print_current_menu_parent'] && 'current-menu-parent' == $class ) {
				$classes[] = 'current-menu-parent';
				$item_is_parent = true;
				continue;
			}

			// This class is added to menu items that correspond to the hierachical parent of the currently rendered type, where {type} corresponds to the the value used for .menu-item-type-{type}. 
			if ( $options['pwpm_print_current_type_any_parent'] && preg_match( '/current-( post_type|taxonomy )-parent/', $class, $matches ) ) {
				$classes[] = $matches[0];
				$item_is_parent = true;
				continue;
			}

			// This class is added to menu items that correspond to the hierachical parent of the currently rendered object, where {object} corresponds to the the value used for .menu-item-object-{object}. 
			if ( $options['pwpm_print_current_object_any_parent'] && preg_match( '/current-[^-]+-parent/', $class, $matches ) ) {
				$classes[] = $matches[0];
				$item_is_parent = true;
				continue;
			}

			// This class is added to menu items that correspond to a hierarchical ancestor of the currently rendered page. 
			if ( $options['pwpm_print_current_menu_ancestor'] && 'current-menu-ancestor' == $class ) {
				$classes[] = 'current-menu-ancestor';
				continue;
			}

			// This class is added to menu items that correspond to a hierachical ancestor of the currently rendered type, where {type} corresponds to the the value used for .menu-item-type-{type}. 
			if ( $options['pwpm_print_current_type_any_ancestor'] && preg_match( '/current-( post_type|taxonomy )-ancestor/', $class, $matches ) ) {
				$classes[] = $matches[0];
				continue;
			}

			// This class is added to menu items that correspond to a hierachical ancestor of the currently rendered object, where {object} corresponds to the the value used for .menu-item-object-{object}. 
			if ( $options['pwpm_print_current_object_any_ancestor'] && preg_match( '/current-[^-]+-ancestor/', $class, $matches ) ) {
				$classes[] = $matches[0];
				continue;
			}

			// This class is added to menu items that correspond to the site front page. 
			if ( $options['pwpm_print_menu_item_home'] && 'menu-item-home' == $class ) {
				$classes[] = 'menu-item-home';
				// last statement before loop end does not need a continue
			}

		} // end foreach()

		// delete ancestor classes if users does not wish them on parent items
		if ( $options['pwpm_do_not_print_parent_as_ancestor'] && $item_is_parent ) {
			// regular expression search on array values
			$keys = array();
			foreach ( $classes as $key => $val ) {
				if ( preg_match( '/current-[^-]+-ancestor/', $val ) ) {
					$keys[] = $key;
				}
			}
			// delete ancestor classes if found
			if ( $keys ) {
				foreach ( $keys as $key ) {
					unset( $classes[ $key ] );
				}
			}
		} // end if()

		// Backward Compatibility with wp_page_menu() 
		// the following classes are added to maintain backward compatibility with the wp_page_menu() function output
		if ( $options['pwpm_backward_compatibility_with_wp_page_menu'] ) {
			$classes = array_merge( $classes, self::purify_page_item_classes( $css_classes ) );
		}

		// Returns the new set of css classes for the item
		return array_intersect( $css_classes, $classes );

	} // end purify_menu_item_classes()

	/**
	* Clean the id attribute of items in navigation menus
	*
	* @since   1.0
	*
	* @uses    $stored_settings
	* @return  string                     Empty string if param should not be returned, else the param itself
	*/
	public static function purify_menu_item_id () {
		return '';
	} // end purify_menu_item_id()

	/**
	* Clean the CSS classes of items in page menus
	*
	* @since   1.0
	*
	* @param   array    $css_classes    Strings wp_page_menu() builded for a single item
	* @uses    $stored_settings
	* @return  array|string             Empty string if param is not an array, else the array with strings for the menu item
	*/
	public static function purify_page_item_classes( $css_classes ) {
		if ( ! is_array( $css_classes ) ) {
			return '';
		}

		$options = self::$stored_settings;
		$item_is_parent = false;
		$classes = array();

		foreach ( $css_classes as $class ) {
			// This class is added to menu items that correspond to a static page. 
			if ( $options['pwpm_print_page_item'] && 'page_item' == $class ) {
				$classes[] = 'page_item';
				continue;
			}

			// This class is added to menu items that correspond to a static page, where $ID is the static page ID. 
			if ( $options['pwpm_print_page_item_id'] && preg_match( '/page-item-[0-9]+/', $class, $matches ) ) {
				$classes[] = $matches[0];
				continue;
			}

			// This class is added to menu items that correspond to the currently rendered static page. 
			if ( $options['pwpm_print_current_page_item'] && 'current_page_item' == $class ) {
				$classes[] = 'current_page_item';
				continue;
			}

			// This class is added to menu items that correspond to the hierarchical parent of the currently rendered static page. 
			if ( $options['pwpm_print_current_page_parent'] && 'current_page_parent' == $class ) {
				$classes[] = 'current_page_parent';
				$item_is_parent = true;
				continue;
			}

			// This class is added to menu items that correspond to a hierarchical ancestor of the currently rendered static page. 
			if ( $options['pwpm_print_current_page_ancestor'] && 'current_page_ancestor' == $class ) {
				$classes[] = 'current_page_ancestor';
				// last, no continue;
			}
		} // end foreach

		// delete ancestor class if users does not wish it on parent items
		if ( $options['pwpm_do_not_print_parent_as_ancestor'] && $item_is_parent ) {
			// regular expression search on array values
			$key = array_search( 'current_page_ancestor', $classes );
			// delete ancestor classes if found
			unset( $classes[ $key ] );
		}

		// Returns the classes for the item
		return array_intersect( $css_classes, $classes );
	} // end purify_page_item_classes()

	/**
	 * Print a message about the location of the plugin in the WP backend
	 * 
	 * @since    2.0
	 */
	public function display_activation_message () {
		$url  = esc_url( admin_url( sprintf( 'options-general.php?page=%s', self::$plugin_slug ) ) );
		$link = sprintf( '<a href="%s">%s =&gt; %s</a>', $url, __( 'Settings' ), self::$plugin_name );
		$msg  = sprintf( __( 'Welcome to %s! You can find the plugin at %s.', self::$plugin_slug ), self::$plugin_name, $link );
		$html = sprintf( '<div class="updated"><p>%s</p></div>', $msg );
		print $html;
	}

	/**
	 * For development: Display a var_dump() of the variable; die if true
	 *
	 * @since    2.0
	 */
	public static function dump ( $v, $die = false ) {
		print "<pre>";
		var_dump( $v );
		print "</pre>";
		if ( $die ) die();
	} // dump()

}
