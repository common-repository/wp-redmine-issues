<?php
/*
 * WP-Redmine-Issues (WPRI) by arcanasoft
 *
 * WP-Redmine-Issues Header Information:
 * * Plugin Name: WP-Redmine-Issues (WPRI)
 * * Description: This plugin is supposed to be an interface between Redmine and Wordpress. To get issues, create issues and leave comments at issues from Wordpress in Redmine.
 * * Plugin URI: https://arcanasoft.de/projects/WP-Redmine-Issues/
 * * Version: 1.1
 * * Text Domain: wpri
 * * Domain Path: /i18n/
 * * Author: arcanasoft
 * * Author URI: https://arcanasoft.de
 * * License: GNU General Public License
 *
 * @link https://developer.wordpress.org/plugins/the-basics/header-requirements/
 *
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html
 * 
 * @copyright Copyright (c) 2018 by arcanasoft
**/

namespace arcanasoft\wpri;


include ("includes/wpri_globals.class.php");
include ("includes/lib/wpri_wp_list_table.class.php");
include ("includes/wpri_start.php");
include ("includes/wpri_settings.php");
include ("includes/lib/wpri_redmine_api.class.php");
include ("includes/lib/af_wp_html.class.php");

const PLUGIN_NAME = 'wp-redmine-issues/';
const PLUGIN_DIR = 'wp-redmine-issues/';
const TXTDOMAIN = 'wpri';

/* Menu */
add_action('admin_enqueue_scripts', 'arcanasoft\wpri\globals::callback_js_css');
add_action('admin_menu', 'arcanasoft\wpri\globals::plugin_setup');

/* i18n - Translation */
add_action( 'plugins_loaded', 'arcanasoft\wpri\globals::wpri_load_text_domain' );
?>
