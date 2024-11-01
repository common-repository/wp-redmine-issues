<?php
/*
 * WP-Redmine-Issues (WPRI) by arcanasoft
**/

namespace arcanasoft\wpri;

class globals {

const EMPTY_FIELD = 'leer'; 

static function plugin_setup() {
	add_menu_page( __( 'Issues', 'wpri' ), __( 'Issues', 'wpri' ), 'publish_posts', 'wpri', 'arcanasoft\wpri\tickets::call', self::get_menu_icon(), 75 );
	add_submenu_page( 'wpri', __( 'New Issue', 'wpri' ), __( 'New Issue', 'wpri' ), 'publish_posts', 'wpri-create-ticket', 'arcanasoft\wpri\tickets::create_ticket' );
	add_options_page( 'WPRI - '.__( 'Settings', 'wpri' ), 'WPRI - '.__( 'Settings', 'wpri' ), 'manage_options', 'wpri-settings', 'arcanasoft\wpri\settings::call' );
}

static function callback_js_css() {
	wp_enqueue_style( 'wpri_css', plugins_url( PLUGIN_DIR . 'css/style.css' ), false  );
	wp_register_script('wpri_js', plugins_url( PLUGIN_DIR . 'js/wpri.js'), array('jquery-core'));
	wp_enqueue_script('wpri_js');
}

static function config_check() {
		foreach(\arcanasoft\wpri\globals::get_option_fields() as $wpri_field) {
		if (empty(get_option( $wpri_field['option'] )) && empty($wpri_field['default']) ) {
			echo '<div class="notice notice-error is-dismissible">
				<p>'.__( 'Error', 'wpri' ).': '.__( $wpri_field['display'], 'wpri' ).' '.__( 'Not configured', 'wpri' ).'</p> <p><a href="'.\arcanasoft\wpri\globals::get_plugin_urls()['settings']['url'].'">'.__('Check settings', 'wpri').'</a></p>
			</div>';
			die();
		}
	}
}

static function split_param($text) {
	$split=explode('___',$text);
	if(!empty($split[0]) && is_numeric($split[0])) {
		return array('value' => $split[0], 'name' => $split[1]);
	} else {
		return false;
	}
}

static function using_categories() {

return ( (!empty(get_option(self::get_option_fields()['ticket_use_categories']['option'])) && get_option(self::get_option_fields()['ticket_use_categories']['option']) == 'on') 
								|| (!empty(get_option(self::get_option_fields()['ticket_use_categories']['default'])) && get_option(self::get_option_fields()['ticket_use_categories']['default']) == 'on') );

}

static function get_menu_icon() {
	return 'data:image/svg+xml;base64,'.base64_encode('<svg width="100%" height="100%" viewBox="0 0 150 142" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" xmlns:serif="http://www.serif.com/" style="fill-rule:evenodd;clip-rule:evenodd;stroke-linejoin:round;stroke-miterlimit:1.41421;"><path d="M85.947,11.464l12.457,-10.037c1.501,-1.21 3.466,-1.58 5.322,-1.373c1.855,0.208 3.578,0.99 4.78,2.501l13.509,17.128c1.045,1.32 1.092,3.175 0.076,4.52c-1.95,2.577 -1.906,6.064 0.089,8.571c1.95,2.461 5.455,3.272 8.286,1.967c1.523,-0.707 3.435,-0.241 4.477,1.079l13.522,17.013c1.199,1.5 1.697,3.336 1.489,5.257c-0.208,1.924 -1.104,3.698 -2.61,4.914l-12.512,10.081l-1.981,-2.493c-1.131,-1.419 -2.605,-2.155 -4.267,-2.341c-1.663,-0.185 -3.465,0.185 -4.876,1.319c-2.825,2.276 -3.275,6.362 -1.015,9.206l1.933,2.432l-72.99,58.912c-1.497,1.21 -3.306,1.713 -5.221,1.499c-1.914,-0.214 -3.667,-1.227 -4.868,-2.742l-13.522,-17.013c-1.045,-1.32 -1.092,-3.176 -0.076,-4.521c1.949,-2.573 1.903,-6.064 -0.089,-8.571c-1.954,-2.454 -5.458,-3.28 -8.286,-1.967c-1.527,0.707 -3.315,0.258 -4.364,-1.066l-13.635,-17.026c-2.48,-3.116 -1.975,-7.67 1.121,-10.171l72.983,-58.806l2.037,2.45c1.127,1.418 2.783,2.381 4.448,2.567c1.664,0.186 3.287,-0.411 4.695,-1.546c2.817,-2.27 3.268,-6.363 1.015,-9.206l-1.927,-2.537Zm26.199,36.106c1.663,0.185 3.34,0.941 4.47,2.363l3.952,5.204c2.26,2.844 1.809,6.931 -1.015,9.206c-1.408,1.135 -3.031,1.732 -4.695,1.546c-1.665,-0.185 -3.321,-1.149 -4.448,-2.567l-4.177,-5.023c-2.26,-2.844 -1.787,-7.135 1.037,-9.41c1.412,-1.138 3.213,-1.505 4.876,-1.319Zm-16.256,-20.455c1.662,0.186 3.136,0.919 4.267,2.341l4.154,5.227c2.26,2.844 1.809,6.937 -1.015,9.206c-1.408,1.135 -3.211,1.505 -4.875,1.319c-1.665,-0.186 -3.14,-0.923 -4.267,-2.341l-4.155,-5.227c-2.26,-2.844 -1.81,-6.93 1.015,-9.206c1.412,-1.137 3.213,-1.505 4.876,-1.319Z" style="fill:#9ca1a6;"/></svg>');
}

static function get_option_fields() {
	
	return array(
 'redmine_url' 		=> array (	'option'	=> 'wpri_redmine_url',
								'display'	=> 'Redmine URL'
							),
 'redmine_api_key'	=> array (	'option'	=> 'wpri_redmine_api_key',
								'display'	=> 'Redmine API-Key'
							),
 'tickets_per_page'	=> array (	'option'	=> 'wpri_redmine_tickets_per_page',
								'display'	=> __( 'Issues per page', 'wpri' ),
								'default'	=> 10
							),
 'show_ticket_journals'	=> array (	'option'	=> 'wpri_redmine_tickets_show_journals',
								'display'	=> __( 'Show issue comments', 'wpri' ),
								'default'	=> 'on',
								'type'		=> 'checkbox'
							),
 'ticket_use_categories'=> array (	'option'	=> 'wpri_redmine_ticket_use_categories',
								'display'	=> __( 'Use issue categories', 'wpri' ),
								'default'	=> 'off',
								'type'		=> 'checkbox',
							),
);
}

static function get_plugin_urls() {
return array(
 'create_ticket'			=>	array(	'url'		=> admin_url().'admin.php?page=wpri-create-ticket',
 								'text'		=> __('New Issue', 'wpri'),
								'html'		=>	'<a href="'.admin_url().'admin.php?page=wpri-create-ticket">Neues Ticket</a>'
						),
 'settings'			=>	array(	'url'		=> admin_url().'admin.php?page=wpri-settings',
								'text'		=> __('Issue settings', 'wpri'),
								'html'		=>	'<a href="'.admin_url().'admin.php?page=wpri-settings">WPRI - Einstellungen</a>'
						),
 'tickets'	=>	array(	'url'		=> admin_url().'admin.php?page=wpri',
								'text'		=> __('show issues', 'wpri'),
								'html'		=>	'<a href="'.admin_url().'admin.php?page=wpri">Tickets anzeigen</a>'
						),
);
}

static function get_api_urls() {
return array(
'tickets'					=> '/issues.json',
'projects'					=> '/projects.json',
'trackers'					=> '/trackers.json',
'issue_statuses'			=> '/issue_statuses.json',
'projects_trackers'			=> '/projects.json?include=trackers',
'projects_cats'				=> '/projects.json?include=issue_categories',
'projects_trackers_cats'	=> '/projects.json?include=trackers,issue_categories',
);
}

static function wpri_load_text_domain() {
    load_plugin_textdomain( TXTDOMAIN, false, PLUGIN_DIR . 'i18n/' );
}

}


?>
