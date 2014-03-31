<?php
/**
 * Purify WordPress Menus.
 *
 * @package   Purify_WordPress_Menus_Admin
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/purify-wp-menues/
 * @copyright 2014 
 */

/**
 * @package Purify_WordPress_Menus_Admin
 * @author    Martin Stehle <m.stehle@gmx.de>
 */
class Purify_WordPress_Menus_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    2.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    2.0
	 *
	 * @var      string
	 */
	protected static $plugin_screen_hook_suffix = null;

	/**
	 * Unique identifier for this plugin.
	 *
	 * It is the same as in class Purify_WordPress_Menus
	 * Has to be set here to be used in non-object context, e.g. callback functions
	 *
	 * @since    2.0
	 *
	 * @var      string
	 */
	protected static $plugin_slug = null;

	/**
	 * Unique identifier in the WP options table
	 *
	 *
	 * @since    2.0
	 *
	 * @var      string
	 */
	protected static $settings_db_slug = null;

	/**
	 * Slug of the menu page on which to display the form sections
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	protected static $main_options_page_slug = 'pwpm_options_page'; #purify_wp_menu_options_page

	/**
	 * Group name of options
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	protected static $settings_fields_slug = 'pwpm_options_group'; #purify_wp_menu_options_group
	
	/**
	 * Structure of the form sections with headline, description and options
	 *
	 *
	 * @since    2.0
	 *
	 * @var      array
	 */
	protected static $form_structure = null;

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
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Call $plugin_slug from public plugin class and get some properties
		$plugin = Purify_WordPress_Menus::get_instance();
		self::$plugin_slug = $plugin->get_plugin_slug();
		self::$settings_db_slug = $plugin->get_settings_db_slug();
		
		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		#add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __DIR__ ) . self::$plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		// define the options
		add_action( 'admin_init', array( $this, 'register_options' ) );

		// get current or default settings
		self::$stored_settings = $plugin->get_stored_settings();


	}

	/**
	 * Do the admin main function 
	 *
	 * @since     2.0
	 *
	 */
	public function main() {
		// print options page
		include_once( 'views/admin.php' );
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
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( self::$plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( self::$plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( self::$plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), Purify_WordPress_Menus::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( self::$plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( self::$plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( self::$plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), Purify_WordPress_Menus::VERSION );
		}
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    2.0
	 */
	public function add_plugin_admin_menu() {

		// Add a settings page for this plugin to the Settings menu.
		self::$plugin_screen_hook_suffix = add_options_page(
			__( 'Purify WordPress Menus Options', self::$plugin_slug ),
			__( 'Purify WordPress Menus', self::$plugin_slug ),
			'manage_options',
			self::$plugin_slug,
			array( $this, 'main' )
		);

	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    2.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . self::$plugin_slug ) . '">' . __( 'Settings' ) . '</a>'
			),
			$links
		);

	}
	
	/**
	* Define and register the options
	*
	* @since    2.0
	*/
	public static function register_options () {

		$title = null;
		$type = null;
		$html = null;

		/*
		 * Note: The order of the entries affects the order in the frontend page
		 *
		 */
		// define the form sections, order by appereance, with headlines, and options
		self::$form_structure = array(
			'1st_section' => array(
				'headline' => __( 'Current Page Navigation Menu Items', self::$plugin_slug ),
				'description' => __( 'In this section you control the CSS class Wordpress adds to the current menu item.', self::$plugin_slug ),
				'options' => array(
					'pwpm_print_current_menu_item' => array(
						'title'   => '.current-menu-item',
						'label'   => __( 'This class is added to menu items that correspond to the currently rendered page.', self::$plugin_slug ),
					),
					'pwpm_print_current_page_item' => array(
						'title'   => '.current_page_item',
						'label'   => __( 'This class is added to page menu items that correspond to the currently rendered static page.', self::$plugin_slug ),
					),
				),
			), // end 1st_section
			'2nd_section' => array(
				'headline' => __( 'General Menu Items', self::$plugin_slug ),
				'description' => __( 'In this section you control some general classes Wordpress adds to menu items.', self::$plugin_slug ),
				'options' => array(
					'pwpm_print_menu_item' => array(
						'title'   => '.menu-item',
						'label'   => __( 'This class is added to every menu item.', self::$plugin_slug ),
					),
					'pwpm_print_page_item' => array(
						'title'   => '.page_item',
						'label'   => __( 'This class is added to page menu items that correspond to a static page.', self::$plugin_slug ),
					),
					'pwpm_print_menu_item_id_as_class' => array(
						'title'   => '.menu-item-{id}',
						'label'   => __( 'This class with the item id is added to every menu item.', self::$plugin_slug ),
					),
					'pwpm_print_page_item_id' => array(
						'title'   => '.page-item-{id}',
						'label'   => __( 'This class is added to page menu items that correspond to a static page, where ID is the static page ID.', self::$plugin_slug ),
					),
					'pwpm_print_menu_item_home' => array(
						'title'   => '.menu-item-home',
						'label'   => __( 'This class is added to menu items that correspond to the site front page.', self::$plugin_slug ),
					),
					'pwpm_print_menu_item_id' => array(
						'title'   => '#menu-item-{id}',
						'label'   => __( 'The id of the menu item is added to every menu item of navigation menus.', self::$plugin_slug ),
					),
				),
			), // end 2nd_section
			'3rd_section' => array(
				'headline' => __( 'Current Page Parent Menu Items', self::$plugin_slug ),
				'description' => __( 'In this section you control the CSS classes Wordpress adds to the hierarchical parent of the current menu item.', self::$plugin_slug ),
				'options' => array(
					'pwpm_print_current_menu_parent' => array(
						'title'   => '.current-menu-parent',
						'label'   => __( 'This class is added to menu items that correspond to the hierarchical parent of the currently rendered page.', self::$plugin_slug ),
					),
					'pwpm_print_current_page_parent' => array(
						'title'   => '.current_page_parent',
						'label'   => __( 'This class is added to page menu items that correspond to the hierarchical parent of the currently rendered static page.', self::$plugin_slug ),
					),
					'pwpm_print_current_object_any_parent' => array(
						'title'   => '.current-{object}-parent',
						'label'   => __( 'This class is added to menu items that correspond to the hierachical parent of the currently rendered object, where {object} corresponds to the the value used for .menu-item-object-{object}.', self::$plugin_slug ),
					),
					'pwpm_print_current_type_any_parent' => array(
						'title'   => '.current-{type}-parent',
						'label'   => __( 'This class is added to menu items that correspond to the hierachical parent of the currently rendered type, where {type} corresponds to the the value used for .menu-item-type-{type}.', self::$plugin_slug ),
					),
				),
			), // end 3rd_section
			'4th_section' => array(
				'headline' => __( 'Current Page Ancestor Menu Items', self::$plugin_slug ),
				'description' => __( 'In this section you control the CSS classes Wordpress adds to the hierarchical anchestors of the current menu item.', self::$plugin_slug ),
				'options' => array(
					'pwpm_print_current_menu_ancestor' => array(
						'title'   => '.current-menu-ancestor',
						'label'   => __( 'This class is added to menu items that correspond to a hierarchical ancestor of the currently rendered page.', self::$plugin_slug ),
					),
					'pwpm_print_current_page_ancestor' => array(
						'title'   => '.current_page_ancestor',
						'label'   => __( 'This class is added to page menu items that correspond to a hierarchical ancestor of the currently rendered static page.', self::$plugin_slug ),
					),
					'pwpm_print_current_object_any_ancestor' => array(
						'title'   => '.current-{object}-ancestor',
						'label'   => __( 'This class is added to menu items that correspond to a hierachical ancestor of the currently rendered object, where {object} corresponds to the the value used for .menu-item-object-{object}.', self::$plugin_slug ),
					),
					'pwpm_print_current_type_any_ancestor' => array(
						'title'   => '.current-{type}-ancestor',
						'label'   => __( 'This class is added to menu items that correspond to a hierachical ancestor of the currently rendered type, where {type} corresponds to the the value used for .menu-item-type-{type}.', self::$plugin_slug ),
					),
				),
			), // end 4th_section
			'5th_section' => array(
				'headline' => __( 'All Other Navigation Menu Items', self::$plugin_slug ),
				'description' => __( 'In this section you control some all other classes Wordpress adds to menu items.', self::$plugin_slug ),
				'options' => array(
					'pwpm_print_menu_item_object_page' => array(
						'title'   => '.menu-item-object-page',
						'label'   => __( 'This class is added to menu items that correspond to static pages.', self::$plugin_slug ),
					),
					'pwpm_print_menu_item_object_category' => array(
						'title'   => '.menu-item-object-category',
						'label'   => __( 'This class is added to menu items that correspond to a category.', self::$plugin_slug ),
					),
					'pwpm_print_menu_item_object_tag' => array(
						'title'   => '.menu-item-object-tag',
						'label'   => __( 'This class is added to menu items that correspond to a tag.', self::$plugin_slug ),
					),
					'pwpm_print_menu_item_object_any' => array(
						'title'   => '.menu-item-object-{object}',
						'label'   => __( 'This class is added to every menu item, where {object} is either a post type or a taxonomy.', self::$plugin_slug ),
					),
					'pwpm_print_menu_item_object_custom' => array(
						'title'   => '.menu-item-object-{custom}',
						'label'   => __( 'This class is added to menu items that correspond to a custom post type or a custom taxonomy.', self::$plugin_slug ),
					),
					'pwpm_print_menu_item_type_post_type' => array(
						'title'   => '.menu-item-type-post_type',
						'label'   => __( 'This class is added to menu items that correspond to post types, i.e. static pages or custom post types.', self::$plugin_slug ),
					),
					'pwpm_print_menu_item_type_taxonomy' => array(
						'title'   => '.menu-item-type-taxonomy',
						'label'   => __( 'This class is added to menu items that correspond to taxonomies, i.e. categories, tags, or custom taxonomies.', self::$plugin_slug ),
					),
				),
			), // end 5th_section
			'6th_section' => array(
				'headline' => __( 'Special Settings', self::$plugin_slug ),
				'description' => __( 'In this section you control some special settings.', self::$plugin_slug ),
				'options' => array(
					'pwpm_backward_compatibility_with_wp_page_menu' => array(
						'title'   => __( 'Maintain backward compatibility with wp_page_menu().', self::$plugin_slug ),
						'label'   => __( 'Adds the CSS classes of page menus to navigation menus.', self::$plugin_slug ),
					),
					'pwpm_do_not_print_parent_as_ancestor' => array(
						'title'   => __( 'Do not print parent as ancestor.', self::$plugin_slug ),
						'label'   => __( 'Does not classified the menu item which is the current parent as anchestor.', self::$plugin_slug ),
					),
				),
			), // end 6th_section
		);
		// build form with sections and options
		foreach ( self::$form_structure as $section_key => $section_values ) {
		
			// assign callback functions to form sections (options groups)
			add_settings_section(
				// 'id' attribute of tags
				$section_key, 
				// title of the section.
				self::$form_structure[ $section_key ][ 'headline' ],
				// callback function that fills the section with the desired content
				array( __CLASS__, 'print_section_' . $section_key ),
				// menu page on which to display this section
				self::$main_options_page_slug
			); // end add_settings_section()
			
			// set labels and callback function names per option name
			foreach ( $section_values[ 'options' ] as $option_name => $option_values ) {
				$title = $option_values[ 'title' ];
				$type = 'checkbox';
				$html = isset( self::$stored_settings[ $option_name ] ) ? checked( '1', self::$stored_settings[ $option_name ], false ) : '';

				// register the option
				add_settings_field(
					// form field name for use in the 'id' attribute of tags
					$option_name,
					// title of the form field
					$title,
					// callback function to render the form field
					array( __CLASS__, 'print_option_checkbox' ),
					// menu page on which to display this field for do_settings_section()
					self::$main_options_page_slug,
					// section where the form field appears
					$section_key,
					// arguments passed to the callback function 
					array(
						'id'    => $option_name,
						'label' => $option_values[ 'label' ],
						'type' => $type,
						'options' => self::$stored_settings,
						'db_slug' => self::$settings_db_slug,
						'html' => $html,
					)
				); // end add_settings_field()

			} // end foreach( section_values )

		} // end foreach( section )

		// finally register all options. They will be stored in the database in the wp_options table under the options name self::$settings_db_slug.
		register_setting( 
			// group name in settings_fields()
			self::$settings_fields_slug,
			// name of the option to sanitize and save in the db
			self::$settings_db_slug,
			// callback function that sanitizes the option's value.
			array( __CLASS__, 'sanitize_options' )
		); // end register_setting()
		
	} // end register_options()

	/**
	* Print the option checkbox
	*
	* @since   1.0
	*
	* @param   array    $args    Strings accessible by key
	* @uses    $stored_settings
	* @uses    $settings_db_slug
	*/
	public static function print_option_checkbox ( $args ) {
		$id = $args[ 'id' ];
		printf( '<label for="%s"><input type="checkbox" id="%s" name="%s[%s]" value="1" ', $id, $id, $args[ 'db_slug' ], $id );
		print $args[ 'html' ];
		printf( ' /> %s</label>', $args[ 'label' ] );
	}

	/**
	* Check and return correct values for the settings
	*
	* @since    2.0
	*
	* @param   array    $input    Options and their values after submitting the form
	* 
	* @return  array              Options and their sanatized values
	*/
	public static function sanitize_options ( $input ) {
		foreach ( self::$form_structure as $section_name => $section_values ) {
			foreach ( array_keys( $section_values[ 'options' ] ) as $option_name ) {
				// if checkbox is set assign '1', else '0'
				$input[ $option_name ] = isset( $input[ $option_name ] ) ? 1 : 0 ;
			} // foreach( options )
		} // foreach( sections )
		return $input;
	} // end sanitize_options()

	/**
	* Print the form sections
	*
	* @since    2.0
	*/
	public static function print_section_1st_section () { printf( "<p>%s</p>\n", self::$form_structure[ '1st_section' ][ 'description' ] ); }
	public static function print_section_2nd_section () { printf( "<p>%s</p>\n", self::$form_structure[ '2nd_section' ][ 'description' ] ); }
	public static function print_section_3rd_section () { printf( "<p>%s</p>\n", self::$form_structure[ '3rd_section' ][ 'description' ] ); }
	public static function print_section_4th_section () { printf( "<p>%s</p>\n", self::$form_structure[ '4th_section' ][ 'description' ] ); }
	public static function print_section_5th_section () { printf( "<p>%s</p>\n", self::$form_structure[ '5th_section' ][ 'description' ] ); }
	public static function print_section_6th_section () { printf( "<p>%s</p>\n", self::$form_structure[ '6th_section' ][ 'description' ] ); }

}
