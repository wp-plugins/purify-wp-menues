<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://stehle-internet.de/
 * @since      3.0
 *
 * @package    Hinjipwpm
 * @subpackage Hinjipwpm/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    Hinjipwpm
 * @subpackage Hinjipwpm/admin
 * @author     Martin Stehle <m.stehle@gmx.de>
 */
class Hinjipwpm_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    3.0
	 * @access   private
	 * @var      string    $hinjipwpm    The ID of this plugin.
	 */
	private $hinjipwpm;

	/**
	 * The version of this plugin.
	 *
	 * @since    3.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The settings of this plugin.
	 *
	 * @since    3.0
	 * @access   private
	 * @var      string    $settings    The current settings of this plugin.
	 */
	private $settings;

	/**
	 * The slug of the plugin screen.
	 *
	 * @since    3.0
	 * @access   private
	 * @var      string    $plugin_screen_id   The slug of the plugin screen.
	 */
	private $plugin_screen_id;

	/**
	 * The structure of the form sections with headline, description and options
	 *
	 * @since    2.0
	 * @access   private
	 * @var      array    $form_structure    The structure of the form sections with headline, description and options
	 */
	private $form_structure;

	/**
	 * The slug of the form elements.
	 *
	 * @since    2.0
	 * @access   private
	 * @var      string    $settings_fields_slug    The slug of the form elements.
	 */
	private $settings_fields_slug;

	/**
	 * The slug of the options page.
	 *
	 * @since    2.0
	 * @access   private
	 * @var      array    $main_options_page_slug    The slug of the options page.
	 */
	private $main_options_page_slug;

	/**
	 * The slug of the plugin's settings in the WP options table
	 *
	 * @since    2.0
	 * @access   private
	 * @var      string    $version    The current version of the plugin.
	 */
	private $settings_db_slug;

	/**
	 * The name of this plugin.
	 *
	 *
	 * @since    2.1.1
	 *
	 * @var      string    The name of this plugin.
	 */
	private $plugin_name;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    3.0
	 * @var      string    $hinjipwpm       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $hinjipwpm, $version, $settings, $settings_db_slug ) {

		$this->hinjipwpm = $hinjipwpm;
		$this->version = $version;
		$this->settings = $settings;
		$this->settings_db_slug = $settings_db_slug;
		$this->settings_fields_slug = 'pwpm_main_options_group';
		$this->main_options_page_slug = 'pwpm_main_options_page';
		$this->plugin_name = 'Purify WordPress Menus';

	}

	/**
	 * Do the admin main function 
	 *
	 * @since     2.0
	 *
	 */
	public function main() {
		// print options page
		include_once( 'partials/hinjipwpm-admin-display.php' );
	}

	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    3.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Hinjipwpm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Hinjipwpm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( ! isset( $this->plugin_screen_id ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_id == $screen->id ) {
			wp_enqueue_style( $this->hinjipwpm, plugin_dir_url( __FILE__ ) . 'css/hinjipwpm-admin.css', array(), $this->version, 'all' );
		}

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    3.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Hinjipwpm_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Hinjipwpm_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		if ( ! isset( $this->plugin_screen_id ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_id == $screen->id ) {
			wp_enqueue_script( $this->hinjipwpm, plugin_dir_url( __FILE__ ) . 'js/hinjipwpm-admin.js', array( 'jquery' ), $this->version, false );
		}

	}

	/**
	 * Print a message about the location of the plugin in the WP backend
	 * 
	 * @since    2.0
	 */
	public function display_activation_message () {
		$label = 'Settings';
		$url  = esc_url( admin_url( sprintf( 'options-general.php?page=%s', $this->hinjipwpm ) ) );
		$link = sprintf( '<a href="%s">%s =&gt; %s</a>', $url, __( $label ), $this->plugin_name );
		$msg  = sprintf( __( 'Welcome to %s! You can find the plugin at %s.', $this->hinjipwpm ), $this->plugin_name, $link );
		$html = sprintf( '<div class="updated"><p>%s</p></div>', $msg );
		print $html;
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    2.0
	 */
	public function add_plugin_admin_menu() {
		
		$label = 'Settings';
		$page_title = sprintf( '%s: %s', $this->plugin_name, __( $label ) );

		// Add a settings page for this plugin to the Settings menu.
		$this->plugin_screen_id = add_options_page(
			$page_title,
			$this->plugin_name,
			'manage_options',
			$this->hinjipwpm,
			array( $this, 'main' )
		);

	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    2.0
	 */
	public function add_action_links( $links ) {

		$label = 'Settings';
		
		return array_merge(
			$links,
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=df' . $this->hinjipwpm ) . '">' . __( $label ) . '</a>'
			)
		);

	}
	
	/**
	* Define and register the options
	*
	* @since    2.0
	*/
	public function register_options () {

		$title = null;
		$type = null;
		$html = null;

		/*
		 * Note: The order of the entries affects the order in the frontend page
		 *
		 */
		// define the form sections, order by appereance, with headlines, and options
		$this->form_structure = array(
			'1st_section' => array(
				'headline' => __( 'Current Page Navigation Menu Items', $this->hinjipwpm ),
				'description' => __( 'In this section you control the CSS class Wordpress adds to the current menu item.', $this->hinjipwpm ),
				'options' => array(
					'pwpm_print_current_menu_item' => array(
						'title'   => '.current-menu-item',
						'label'   => __( 'This class is added to menu items that correspond to the currently rendered page.', $this->hinjipwpm ),
					),
					'pwpm_print_current_page_item' => array(
						'title'   => '.current_page_item',
						'label'   => __( 'This class is added to page menu items that correspond to the currently rendered page.', $this->hinjipwpm ),
					),
				),
			), // end 1st_section
			'2nd_section' => array(
				'headline' => __( 'General Menu Items', $this->hinjipwpm ),
				'description' => __( 'In this section you control some general classes Wordpress adds to menu items.', $this->hinjipwpm ),
				'options' => array(
					'pwpm_print_menu_item' => array(
						'title'   => '.menu-item',
						'label'   => __( 'This class is added to every menu item.', $this->hinjipwpm ),
					),
					'pwpm_print_page_item' => array(
						'title'   => '.page_item',
						'label'   => __( 'This class is added to page menu items that correspond to a page.', $this->hinjipwpm ),
					),
					'pwpm_print_menu_item_id_as_class' => array(
						'title'   => '.menu-item-{id}',
						'label'   => __( 'This class with the item id is added to every menu item.', $this->hinjipwpm ),
					),
					'pwpm_print_page_item_id' => array(
						'title'   => '.page-item-{id}',
						'label'   => __( 'This class is added to page menu items that correspond to a page, where ID is the page ID.', $this->hinjipwpm ),
					),
					'pwpm_print_menu_item_home' => array(
						'title'   => '.menu-item-home',
						'label'   => __( 'This class is added to menu items that correspond to the site front page.', $this->hinjipwpm ),
					),
					'pwpm_print_menu_item_id' => array(
						'title'   => '#menu-item-{id}',
						'label'   => __( 'The id of the menu item is added to every menu item of navigation menus.', $this->hinjipwpm ),
					),
				),
			), // end 2nd_section
			'3rd_section' => array(
				'headline' => __( 'Current Page Parent Menu Items', $this->hinjipwpm ),
				'description' => __( 'In this section you control the CSS classes Wordpress adds to the hierarchical parent of the current menu item.', $this->hinjipwpm ),
				'options' => array(
					'pwpm_print_current_menu_parent' => array(
						'title'   => '.current-menu-parent',
						'label'   => __( 'This class is added to menu items that correspond to the hierarchical parent of the currently rendered page.', $this->hinjipwpm ),
					),
					'pwpm_print_current_page_parent' => array(
						'title'   => '.current_page_parent',
						'label'   => __( 'This class is added to page menu items that correspond to the hierarchical parent of the currently rendered page.', $this->hinjipwpm ),
					),
					'pwpm_print_current_object_any_parent' => array(
						'title'   => '.current-{object}-parent',
						'label'   => __( 'This class is added to menu items that correspond to the hierachical parent of the currently rendered object, where {object} corresponds to the the value used for .menu-item-object-{object}.', $this->hinjipwpm ),
					),
					'pwpm_print_current_type_any_parent' => array(
						'title'   => '.current-{type}-parent',
						'label'   => __( 'This class is added to menu items that correspond to the hierachical parent of the currently rendered type, where {type} corresponds to the the value used for .menu-item-type-{type}.', $this->hinjipwpm ),
					),
				),
			), // end 3rd_section
			'4th_section' => array(
				'headline' => __( 'Current Page Ancestor Menu Items', $this->hinjipwpm ),
				'description' => __( 'In this section you control the CSS classes Wordpress adds to the hierarchical anchestors of the current menu item.', $this->hinjipwpm ),
				'options' => array(
					'pwpm_print_current_menu_ancestor' => array(
						'title'   => '.current-menu-ancestor',
						'label'   => __( 'This class is added to menu items that correspond to a hierarchical ancestor of the currently rendered page.', $this->hinjipwpm ),
					),
					'pwpm_print_current_page_ancestor' => array(
						'title'   => '.current_page_ancestor',
						'label'   => __( 'This class is added to page menu items that correspond to a hierarchical ancestor of the currently rendered page.', $this->hinjipwpm ),
					),
					'pwpm_print_current_object_any_ancestor' => array(
						'title'   => '.current-{object}-ancestor',
						'label'   => __( 'This class is added to menu items that correspond to a hierachical ancestor of the currently rendered object, where {object} corresponds to the the value used for .menu-item-object-{object}.', $this->hinjipwpm ),
					),
					'pwpm_print_current_type_any_ancestor' => array(
						'title'   => '.current-{type}-ancestor',
						'label'   => __( 'This class is added to menu items that correspond to a hierachical ancestor of the currently rendered type, where {type} corresponds to the the value used for .menu-item-type-{type}.', $this->hinjipwpm ),
					),
					'pwpm_print_current_taxonomy_ancestor' => array(
						'title'   => '.current-{taxonomy}-ancestor',
						'label'   => __( 'This class is added to menu items that correspond to a hierachical ancestor of the currently taxonomy.', $this->hinjipwpm ),
					),
				),
			), // end 4th_section
			'5th_section' => array(
				'headline' => __( 'All Other Navigation Menu Items', $this->hinjipwpm ),
				'description' => __( 'In this section you control some all other classes Wordpress adds to menu items.', $this->hinjipwpm ),
				'options' => array(
					'pwpm_print_menu_item_object_page' => array(
						'title'   => '.menu-item-object-page',
						'label'   => __( 'This class is added to menu items that correspond to pages.', $this->hinjipwpm ),
					),
					'pwpm_print_menu_item_object_category' => array(
						'title'   => '.menu-item-object-category',
						'label'   => __( 'This class is added to menu items that correspond to a category.', $this->hinjipwpm ),
					),
					'pwpm_print_menu_item_object_tag' => array(
						'title'   => '.menu-item-object-tag',
						'label'   => __( 'This class is added to menu items that correspond to a tag.', $this->hinjipwpm ),
					),
					'pwpm_print_menu_item_object_any' => array(
						'title'   => '.menu-item-object-{object}',
						'label'   => __( 'This class is added to every menu item, where {object} is either a post type or a taxonomy.', $this->hinjipwpm ),
					),
					'pwpm_print_menu_item_object_custom' => array(
						'title'   => '.menu-item-object-{custom}',
						'label'   => __( 'This class is added to menu items that correspond to a custom post type or a custom taxonomy.', $this->hinjipwpm ),
					),
					'pwpm_print_menu_item_type_post_type' => array(
						'title'   => '.menu-item-type-post_type',
						'label'   => __( 'This class is added to menu items that correspond to post types, i.e. pages or custom post types.', $this->hinjipwpm ),
					),
					'pwpm_print_menu_item_type_taxonomy' => array(
						'title'   => '.menu-item-type-taxonomy',
						'label'   => __( 'This class is added to menu items that correspond to taxonomies, i.e. categories, tags, or custom taxonomies.', $this->hinjipwpm ),
					),
					'pwpm_print_menu_item_type_any' => array(
						'title'   => '.menu-item-type-{type}',
						'label'   => __( 'This class is added to menu items that correspond to any other type.', $this->hinjipwpm ),
					),
					'pwpm_print_menu_item_has_children' => array(
						'title'   => '.menu-item-has-children',
						'label'   => __( 'This class is added to menu items that have sub menu items.', $this->hinjipwpm ),
					),
					'pwpm_print_page_item_has_children' => array(
						'title'   => '.page_item_has_children',
						'label'   => __( 'This class is added to page menu items that have sub menu items.', $this->hinjipwpm ),
					),
				),
			), // end 5th_section
			'6th_section' => array(
				'headline' => __( 'Special Settings', $this->hinjipwpm ),
				'description' => __( 'In this section you control some special settings.', $this->hinjipwpm ),
				'options' => array(
					'pwpm_backward_compatibility_with_wp_page_menu' => array(
						'title'   => __( 'Maintain backward compatibility with wp_page_menu().', $this->hinjipwpm ),
						'label'   => __( 'Adds the CSS classes of page menus to navigation menus.', $this->hinjipwpm ),
					),
					'pwpm_do_not_print_parent_as_ancestor' => array(
						'title'   => __( 'Do not print parent as ancestor.', $this->hinjipwpm ),
						'label'   => __( 'Does not classified the menu item which is the current parent as anchestor.', $this->hinjipwpm ),
					),
				),
			), // end 6th_section
		);
		// build form with sections and options
		foreach ( $this->form_structure as $section_key => $section_values ) {
		
			// assign callback functions to form sections (options groups)
			add_settings_section(
				// 'id' attribute of tags
				$section_key, 
				// title of the section.
				$this->form_structure[ $section_key ][ 'headline' ],
				// callback function that fills the section with the desired content
				array( $this, 'print_section_' . $section_key ),
				// menu page on which to display this section
				$this->main_options_page_slug
			); // end add_settings_section()
			
			// set labels and callback function names per option name
			foreach ( $section_values[ 'options' ] as $option_name => $option_values ) {
				$title = $option_values[ 'title' ];
				$type = 'checkbox';
				$sub_html = isset( $this->settings[ $option_name ] ) ? checked( '1', $this->settings[ $option_name ], false ) : '';

				// register the option
				add_settings_field(
					// form field name for use in the 'id' attribute of tags
					$option_name,
					// title of the form field
					$title,
					// callback function to render the form field
					array( $this, 'print_option' ),
					// menu page on which to display this field for do_settings_section()
					$this->main_options_page_slug,
					// section where the form field appears
					$section_key,
					// arguments passed to the callback function 
					array(
						'id'    => $option_name,
						'label' => $option_values[ 'label' ],
						'type' => $type,
						'options' => $this->settings,
						'db_slug' => $this->settings_db_slug,
						'html' => sprintf( '<input type="checkbox" id="%s" name="%s[%s]" value="1" %s /><label for="%s">%s</label>', $option_name, $this->settings_db_slug, $option_name, $sub_html, $option_name, $option_values[ 'label' ] ),
					)
				); // end add_settings_field()

			} // end foreach( section_values )

		} // end foreach( section )

		// finally register all options. They will be stored in the database in the wp_options table under the options name $this->settings_db_slug.
		register_setting( 
			// group name in settings_fields()
			$this->settings_fields_slug,
			// name of the option to sanitize and save in the db
			$this->settings_db_slug,
			// callback function that sanitizes the option's value.
			array( $this, 'sanitize_options' )
		); // end register_setting()
		
	} // end register_options()

	/**
	* Print the option
	*
	* @since   1.0
	*
	*/
	public function print_option ( $args ) {
		print $args[ 'html' ];
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
	public function sanitize_options ( $input ) {
		foreach ( $this->form_structure as $section_name => $section_values ) {
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
	public function print_section_1st_section () { printf( "<p>%s</p>\n", $this->form_structure[ '1st_section' ][ 'description' ] ); }
	public function print_section_2nd_section () { printf( "<p>%s</p>\n", $this->form_structure[ '2nd_section' ][ 'description' ] ); }
	public function print_section_3rd_section () { printf( "<p>%s</p>\n", $this->form_structure[ '3rd_section' ][ 'description' ] ); }
	public function print_section_4th_section () { printf( "<p>%s</p>\n", $this->form_structure[ '4th_section' ][ 'description' ] ); }
	public function print_section_5th_section () { printf( "<p>%s</p>\n", $this->form_structure[ '5th_section' ][ 'description' ] ); }
	public function print_section_6th_section () { printf( "<p>%s</p>\n", $this->form_structure[ '6th_section' ][ 'description' ] ); }

}
