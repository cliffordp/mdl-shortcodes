<?php

/*
Plugin Name: MDL Shortcodes
Description: Material Design Lite (MDL) components are viewable at http://www.getmdl.io/components/
Author: TourKick (Clifford P)
Author URI: http://tourkick.com/
Plugin URI: http://tourkick.com/mdl-shortcodes/
Text Domain: mdl-shortcodes
Domain Path: /languages
License: GPLv3
Version: 1.0.1
*/

defined( 'ABSPATH' ) or die(); //do not allow plugin file to be called directly (security protection)

define( 'MDL_SHORTCODES_VERSION', '1.0.1' );
define( 'MDL_SHORTCODES_URL_ROOT', plugin_dir_url( __FILE__ ) );



/*
	
	LICENSE INFO:
	
	1)
	Plugin core structure forked from https://wordpress.org/plugins/shortcake-bakery/
	Thanks, guys!
	
	2)
	Why not "GPLv2" or "GPLv2 or later"?
	Because:
	- http://www.getmdl.io/started/index.html#license is licensed as Apache-2
	- https://wordpress.org/plugins/about/guidelines/ requires "GPLv2 or later" (which includes GPLv3)
	- https://www.gnu.org/licenses/rms-why-gplv3.html says GPLv3 is compatible with Apache licensing but GPLv2 is not
	- http://www.apache.org/licenses/GPL-compatibility.html agrees Apache v2 is compatible with GPLv3 but not GPLv2
	
*/


/*
	* START notes
	
	TODO:
	- http://tgmpluginactivation.com/ for others besides Shortcake (Shortcake is already done)
	- test Customizer color settings prior to WP 4.3 vs in WP 4.3 (not updating on preview)
	- Possibly add icon font sizing (CSS and shortcode option). See https://github.com/google/material-design-lite/issues/1227#issuecomment-125573309
	- WP Customizer colorpicker to either Sass/Scss or to PHP CSS stylesheet to override .primary, .accent, etc --> 81 changes when diff between 2 Google-hosted files (just as an idea of workload) --> not just these few here: https://github.com/google/material-design-lite/blob/master/src/palette/_palette.scss#L2263 --> http://mdlhut.com/2015/08/creating-an-mdl-theme/
	- resolve <br> in nested Grid/Cell
	- not affect all of wp-admin
	- more useful Quick Add buttons (single click add snippet)
	- add Footer component?
	- add hooks throughout
	
	
	Inspirations:
	https://medium.com/google-developers/introducing-material-design-lite-3ce67098c031
	https://wordpress.org/themes/corpobox-lite/
	http://www.premiumwp.com/premium-and-free-material-design-wordpress-themes/
	http://themeforest.net/item/material-design-wordpress-theme-rare/11408042
	http://www.getmdl.io/templates/
	http://www.getmdl.io/components/
	http://www.getmdl.io/styles/
	http://www.getmdl.io/faq/
	https://www.google.com/design/icons/
	https://www.google.com/design/spec/components/
	http://mdlhut.com/
	http://www.materialpalette.com/light-blue/deep-orange
	https://material.angularjs.org/
	http://materialdesignblog.com/material-design-wordpress/
	https://www.google.com/design/articles/expressing-brand-in-material/
	
	Maybe complementary plugins:
	https://wordpress.org/plugins/shortcode-factory/ is a great complement! Doesn't add any styles either. Just what I was looking for, especially if not using Views!
	https://wp-types.com/home/views-create-elegant-displays-for-your-content/?aid=5336&affiliate_key=Lsvk04DjJOhq -- WP Views
	https://wordpress.org/plugins/easy-google-fonts/ to override fonts, including colors
	https://wordpress.org/plugins/display-posts-shortcode/ -- http://www.billerickson.net/code-tag/display-posts-shortcode/
	https://wordpress.org/plugins/custom-sidebars/
	https://wordpress.org/plugins/amr-shortcode-any-widget/
	https://wordpress.org/plugins/wp-page-widget/ -- no longer in WP Plugin Repo as of 2015-09-14
	https://wordpress.org/plugins/custom-post-widget/
	
* END notes
*/




/**
 * Load the MDL Shortcodes
 */
// @codingStandardsIgnoreStart
function MDL_Shortcodes() {
	return MDL_Shortcodes::get_instance();
}
// @codingStandardsIgnoreEnd
add_action( 'after_setup_theme', 'MDL_Shortcodes' );


require_once dirname( __FILE__ ) . '/inc/class-mdl-shortcodes.php';




/**
 * This file represents an example of the code that themes would use to register
 * the required plugins.
 *
 * It is expected that theme authors would copy and paste this code into their
 * functions.php file, and amend to suit.
 *
 * @see http://tgmpluginactivation.com/configuration/ for detailed documentation.
 * @see https://github.com/TGMPA/TGM-Plugin-Activation/blob/develop/example.php
 *
 * @package    TGM-Plugin-Activation
 * @subpackage Example
 * @version    2.5.2
 * @author     Thomas Griffin, Gary Jones, Juliette Reinders Folmer
 * @copyright  Copyright (c) 2011, Thomas Griffin
 * @license    http://opensource.org/licenses/gpl-2.0.php GPL v2 or later
 * @link       https://github.com/TGMPA/TGM-Plugin-Activation
 */

/**
 * Include the TGM_Plugin_Activation class.
 */
require_once dirname( __FILE__ ) . '/class-tgm-plugin-activation.php';

add_action( 'tgmpa_register', 'mdl_shortcodes_tgm_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function mdl_shortcodes_tgm_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(

		// This is an example of how to include a plugin from the WordPress Plugin Repository.
		array(
			'name'		=> 'Shortcake (Shortcode UI)',
			'slug'		=> 'shortcode-ui',
			'required'	=> false,
		),
	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'mdl_tgmpa',	// Unique ID for hashing notices for multiple instances of TGMPA.
		'has_notices'  => true,			// Show admin notices or not.
		'dismissable'  => true,			// If false, a user cannot dismiss the nag message.
		'is_automatic' => true,			// Automatically activate plugins after installation or not.
	);

	tgmpa( $plugins, $config );
}
