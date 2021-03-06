=== Purify WordPress Menus ===
Contributors: Hinjiriyo
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=SBF76TCGTRNX2
Tags: plugin, navigation, menu, menus, navigation menus, page menus, navigation menu, page menu, wordpress, html, css, optimization, optimisation, slim html, purification
Requires at least: 3.0
Tested up to: 4.1
Stable tag: 3.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Slim down the HTML code of WordPress menus to only the CSS classes and ID attributes your theme needs to improve page speed

== Description ==

= Less code, higher page speed =

This plugin deletes the CSS classes you do not need in a navigation menu and page menu. It slims down the HTML code of Wordpress menus to the only neccessary CSS classes you want for your theme. This results in less HTML code and so in higher page speed.

= What users said =

* **Number 1** in [Cool List of Free Navigation Menu WordPress Plugins](http://codeknows.com/inspiration/free-navigation-menu-wordpress-plugins/) by Inspiration on January 8, 2015
* **Number 7** in [13 Excellent Free WordPress Widgets for Menus](http://cssclick.com/wordpress/free-wordpress-widgets-for-menus/) by mike on November 24, 2014
* **Number 6** in [13 Great Free HTML Widgets for WordPress](http://wpaisle.com/wordpress-widgets/free-html-widgets-for-wordpress/) by sam on August 27, 2014
* **Number 8** in [10 Magnificent Free Menus Widgets for WordPress](http://creativevore.com/wordpress/free-menus-widgets-for-wordpress/) by jatin on July 26, 2014

= No undesiderable visual effects =

The visual appereance of menus in the frontend remains unchanged in most cases. If you should see an undesirable visual effect to the menus in your theme you can activate the needed CSS classes on the plugin's options page.

= Deactivate it and keep your settings =

If you deactivate the plugin, your settings remains. If you activate the plugin again your last settings will be used. You do not need to go over all settings again.

= Residue-free deletion =

If you delete the plugin via the WordPress 'Plugin' menu, your settings will be deleted, too. No useless option remains in the WordPress database.

= Default setting: Marks the current menu item only =

The default setting is to output only the CSS classes for the current menu item.

= Stops displaying CSS classes of parents and ancestors of menu items =

Menu items which are parents of the current item will not be classified as ancestors additionally. The output of class="current-menu-ancestor current-menu-parent" is reduced to class="current-menu-parent".

= Stops displaying CSS classes of outdated page menus =

This plugin filters out the old CSS classes of page menus in navigation menus. Using the WordPress menu configurator the page menu classes are not necessary anymore.

= Stops displaying #menu-{id} =

This plugin deletes the ID attribute of each menu item. In most cases the ID of every menu item is not needed.

= Uses WordPress standard functions =

This plugin hooks into the WordPress core functions wp_nav_menu() and wp_page_menu(). It changed the results of both functions to the settings you chose.

= Switch on and off every CSS menu item class =

You can:

* select and deselect in detail every CSS menu item class the WordPress core functions wp_nav_menu() and wp_page_menu() generate
* control whether the id attribute of each navigation menu item is printed out or not
* control whether parent items will be additionally classified as ancestors item or not. You can activate to print out both classes on parent items or just parents classes
* control whether navigation menus will be additionally classified with the older page menu classes for compatibility or not.

== Installation ==

= Installation description for WordPress experts =

1. Upload it.
2. Activate it.
3. Relax yourself. If you want, you can refine the plugin's settings to your needs.

= Installation in detail =

1. Download the zip file 'purify-wp-menues.zip' to you local computer.
2. Unzip the zip file. You should find a new directory 'purify-wp-menues' with files and sub directories in it.
3. Upload the directory 'purify-wp-menues' with all its content per FTP to your '/wp-content/plugins/' directory.
4. Go to the 'Plugins' page in the admin panel of your WordPress site.
5. Activate the plugin through the 'Plugins' menu in WordPress.
6. If you want you can refine the plugin's output on the option page 'Purify WP Menus'. You will find the page under 'Settings' in the admin panel.

== Frequently Asked Questions ==

= Does the plugin take effects on both navigation menus and page menus? =

Yes, it does.

= Does the plugin take effects on the visual appeareance of menus? =

Short answer: Normally not and if yes, you can take control of it.

Long answer: The default settings print out only the CSS class for the current active menu item. If the theme's CSS uses also the other CSS classes and/or item ID attribute you will notice some undesirable visual effects on menus. In this case just find out which classes and/or IDs the theme uses and activate them via the plugin's options page until the effects disappear.

= What are the default settings of this plugin? =

After activating the plugin deletes the id attributes an all CSS classes on every menue item except the CSS classes ".current-menu-item" in navigation menus and ".current_page_item" in page menus.

= What happens with my settings if I would deactivate the plugin through the 'Plugins' menu in WordPress? =

Your settings will be still stored in the WordPress database. After you re-activate the plugin all your settings are back.

= What happens with my settings if I would delete the plugin through the 'Plugins' menu in WordPress? =

Your settings will be deleted, too. In other words: There would not remain any useless settings of this plugin.

= Would this plugin also deletes the id attribute of every menu item? =

Yes. It does this way as default. You can activate the output of every menu item's id on the plugin's options page.

= Why is in page menus still the empty attribute ' class=""' at every menu item? =

Normally, if you deselect every checkbox for page menus on the plugin's options page no class attribute should be there in page menus. But the WordPress files does not offer a gentle way to suppress the class attribute if it has no values. The plugin saves time and ressources by not trying an own way. If you would have a trick for deleting the empty class attibute with little effort please tell me about it.

= Which languages does the plugin support? =

Actually these languages are supported:

* English
* German
* Spanish

Further translations are welcome. If you have one please send me an email.

= Where is the *.pot file for translating the plugin in any language? =

If you want to contribute a translation of the plugin in your language it would be great! You would find the *.pot file in the 'languages' directory of this plugin. If you would send the *.po file to me I would include it in the next release of the plugin.

== Screenshots ==

1. The first screen shot shows a sample of the results of the HTML output of wp_nav_menu() before and after activating the plugin.
2. The second screen shot shows a part of the plugin's options page in german language.
3. The third screen shot shows where you can find the link to the plugin's option page in the german version of WordPress.

== Changelog ==

= 3.0 =
* Rebuild fundamentally
* Added option 'Current taxonomy ancestor'
* Added option 'Any menu item object'
* Added option 'Any menu item type'
* Added option 'Menu item has children'
* Added option 'Page menu item has children'
* Revised option 'Menu item id as class'
* Revised option 'Page item id as class'
* Removed option 'Custom menu item object'
* Updated translations and *.pot file

= 2.3 =
* Improved: Custom CSS classes keep untouched
* Updated translations and *.pot file

= 2.2.2 =
Successfully tested with WordPress 4.1, especially with the revised filters 'nav_menu_css_class' and 'nav_menu_item_id'

= 2.2.1 =
Successfully tested with WordPress 4.0

= 2.2 =
* Improved uninstall routine
* Tested successfully with WordPress 3.9.2
* Refactored for more compatibility

= 2.1.1 =
* Tested successfully with WordPress 3.8.2
* Some refactoring and fixed a typo
* Updated translations and *.pot file

= 2.1 =
* Some refactoring and tests passed

= 2.0.1 =
* Fixed a coding error

= 2.0 =
* Rebuilded fundamentally to improve the plugin's performance at frontend runtime and your page speed.
* In spite of that no worry about your plugin's settings: They stay untouched and will continue to work
* Better understandable grouping of the options on the options page
* Slight grafic design changes
* Updated translations and *.pot file

= 1.3 =
* Added 'static' property to some functions to prevent warnings at strict error level
* Removed deprecated use of screen_icon()
* Corrected typo 'menues' to 'menus'
* Checked compatibilty with WP 3.8

= 1.2 =
* Fixed a typo
* Added spanish translation. Thank you, Hector!

= 1.1 =
* Improved performance: Hooks in to 'nav_menu_item_id' only when desired instead of every time
* Some improved translation into german
* Improved labeling on options page
* Refined POT file

= 1.0 =
* The plugin was released initially.

== Upgrade Notice ==

= 3.0 =
Rebuild fundamentally and enhanced with new options

= 2.3 =
Improved: Custom CSS classes keep untouched

= 2.2.2 =
Successfully tested with WordPress 4.1

= 2.2.1 =
Successfully tested with WordPress 4.0

= 2.2 =
Improved uninstall routine, tested with WordPress 3.9.2

= 2.1.1 =
Tested with WordPress 3.8.2 and corrected a typo

= 2.1 =
* Some refactoring and tests passed

= 2.0.1 =
* Fixed a coding error

= 2.0 =
* Fundamental rebuild for higher page speed
* More understandable grouping of the options

= 1.3 =
* Added slight corrections

= 1.2 =
* Added spanish translation. Thank you, Hector!


= 1.1 =
* Improved performance and german translation

= 1.0 =
No upgrade neccessary.
