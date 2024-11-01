<?php
/*
 * WP-Redmine-Issues (WPRI) by arcanasoft
**/

namespace arcanasoft\wpri;

class settings {
static function call() {
		//must check that the user has the required capability 
	if (!current_user_can('manage_options'))
	{
	  wp_die( __('You are not authorized to access this page.') );
	}

	// variables for the field and option names 
	// Read in existing option value from database
	
	if( isset($_POST['wpri_hidden_submit_field']) ) {
		$updatedoptions='';
		$settingsrec=$_POST;
		foreach(\arcanasoft\wpri\globals::get_option_fields() as $wpri_field) {
			if(!empty($wpri_field['type']) && $wpri_field['type'] == 'checkbox')  {
				if(!empty($_POST[ $wpri_field['option'] ])) {
					$settingsrec[ $wpri_field['option'] ] = $_POST[ $wpri_field['option'] ];
				} else {
					$settingsrec[ $wpri_field['option'] ] = 'off';
				}
			} 
			if(!empty($settingsrec[ $wpri_field['option'] ]) ) {
				if(get_option($wpri_field['option']) != $settingsrec[ $wpri_field['option'] ]) {
					update_option($wpri_field['option'], $settingsrec[ $wpri_field['option'] ] );
					$updatedoptions.='<p>* '.$wpri_field['display'].': '.$settingsrec[ $wpri_field['option'] ].'</p>';
				}
			}
		}
		if($updatedoptions != '') {
			$massage['content']='<p><strong>'.__('Settings saved', 'wpri' ).'</strong></p>'.$updatedoptions;
			$massage['url']='<p>'.\arcanasoft\wpri\globals::get_plugin_urls()['tickets']['html'].'</p>';
		} else {
			$massage['type']='error';
			$massage['content']='<p><strong>'.__('No changes', 'wpri' ).'</strong></p>';
			$massage['url']='<p>'.\arcanasoft\wpri\globals::get_plugin_urls()['tickets']['html'].'</p>';
		}

		echo \arcanasoft\WP_Framework\WP_Html::massage($massage);

		// Put a "settings saved" message on the screen
	} 
		
	$column1='<form name="form1" method="post" action="">';
	
	$column1_tab[__('General_Settings', 'wrip')]='<input type="hidden" name="wpri_hidden_submit_field" value="1" />
		<table class="form-table"><tbody>';
	foreach(\arcanasoft\wpri\globals::get_option_fields() as $wpri_field) {
		$column1_tab[__('General_Settings', 'wrip')].='<tr>
		<th scope="row">'.__($wpri_field['display'], 'wpri' ).'</th>
		<td>'. self::settings_to_html_input($wpri_field) .'</td></tr>';
	}
	
	$column1_tab[__('General_Settings', 'wrip')].='</tbody></table>
	<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="' .esc_attr(__('Save changes', 'wrip')) .'" />
	</p>';
	
	$column1.= \arcanasoft\WP_Framework\WP_Html::setting_tabs($column1_tab, globals::get_plugin_urls()['settings']['url']);
	
	$column1.='</form>';
		
	$column2Data='<p><img src="'. plugins_url(PLUGIN_DIR . 'img/icon-html-code.png') . '" style="vertical-align:middle;">'.__('Developed by', 'wrip').' <a href="https://arcanasoft.de">arcanasoft</a></p>
	<p><img src="'. plugins_url(PLUGIN_DIR . 'img/icon-star.png').'" style="vertical-align:middle;"> <a href="https://wordpress.org/support/view/plugin-reviews/wp-redmine-issues?filter=5" target="_blank">'.__('Rate this plugin on Wordpress.org','wrip').'</a></p>
	<p><img src="'. plugins_url(PLUGIN_DIR . 'img/icon-coin.png').'" style="vertical-align:middle;"> <a href="https://arcanasoft.de/projects/WP-Redmine-Issues/donate" target="_blank">'.__('Donate', 'wrip').'</a> </p>';
	
	
	
	$column2=\arcanasoft\WP_Framework\WP_Html::column_box($column2Data, __('About the', 'wrip').' WP-Redmine-Issues '.__('Plugin', 'wrip'));
	$column2.=\arcanasoft\WP_Framework\WP_Html::arcanasoft_footer();

	
	$htmlstring=\arcanasoft\WP_Framework\WP_Html::two_column_layout($column1, $column2);
	unset($column1); unset($column2);

	echo \arcanasoft\WP_Framework\WP_Html::wrap_page('WPRI - '. __( 'Settings', 'wpri' ),$htmlstring);

	
	
}

static function settings_to_html_input($wpri_field) {
	if(empty($wpri_field['type']) || $wpri_field['type'] == 'text') {
	return '<input type="text" name="'.$wpri_field['option'].'" value="' .(!empty(get_option( $wpri_field['option'] ))?get_option( $wpri_field['option'] ):(!empty($wpri_field['default'])?$wpri_field['default']:'' )  ) .'" size="50">';
	} else {
		if($wpri_field['type'] == 'checkbox') {
			$htmlstring = '<input type="checkbox" name="'.$wpri_field['option'].'" value="on" ';
			if(!empty(get_option( $wpri_field['option']))) {
				$htmlstring.=(get_option( $wpri_field['option']) == 'on' )?'checked="checked"':'';
			} else {
				$htmlstring.=($wpri_field['default']=='on')?'checked="checked"':'';
			}
			return $htmlstring.'>';
//		}elseif($wpri_field['type'] == 'select' && !empty($wpri_field['function'])) {
		}elseif($wpri_field['type'] == 'select' && !empty($wpri_field['apiurl'])) {
			$htmlstring = \arcanasoft\WP_Framework\WP_Html::wpri_api_list_to_select_input($wpri_field['apiurl'], 'issue_statuses', $wpri_field['option'],$wpri_field['default']);

			return $htmlstring;
		}
	}
}


}
?>
