<?php
/**
 * Options Page
 *
 * @package   Purify_WordPress_Menus
 * @author    Martin Stehle <m.stehle@gmx.de>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/purify-wp-menues/
 * @copyright 2014 
 */
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<p><?php _e( 'Slim down the HTML code of WordPress menus to only the CSS classes and ID attributes your theme needs to improve page speed', $this->plugin_slug ); ?>.</p>
<div class="th_wrapper">
	<div id="th_main">
		<div class="th_content">
			<form method="post" action="options.php">
<?php 
settings_fields( $this->settings_fields_slug );
do_settings_sections( $this->main_options_page_slug );
submit_button(); 
?>
			</form>
			</div><!-- .th_content -->
		</div><!-- #th_main -->
		<div id="th_footer">
			<div class="th_content">
				<h3><?php _e( 'Credits and informations', $this->plugin_slug ); ?></h3>
				<dl>
					<dt><?php _e( 'Do you like the plugin?', $this->plugin_slug ); ?></dt>
					<dd><a href="http://wordpress.org/support/view/plugin-reviews/purify-wp-menues"><?php _e( 'Rate it at wordpress.org!', $this->plugin_slug ); ?></a></dd>
					<dt><?php _e( 'Do you need support or have an idea for the plugin?', $this->plugin_slug ); ?></dt>
					<dd><a href="http://wordpress.org/support/plugin/purify-wp-menues"><?php _e( 'Post your questions and ideas about Purify WordPress Menus in the forum at wordpress.org!', $this->plugin_slug ); ?></a></dd>
					<dt><?php _e( 'Special thanks for the fine style of the plugin go to', $this->plugin_slug ); ?></dt>
					<dd><a href="http://alexandra-mutter.de/?ref=purify-wp-menues">Alexandra Mutter Design</a></dd>
				</dl>
			</div><!-- .th_content -->
		</div><!-- #th_footer -->
	</div><!-- .th_wrapper -->
</div><!-- .wrap -->
