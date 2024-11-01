<?php
/*
 * WP-Redmine-Issues (WPRI) by arcanasoft
**/

namespace arcanasoft\WP_Framework;

	class WP_Html {
/** Table **/
		static function get_wp_form_table($data, $columns, $extracols=false) {
			$htmlstring = '<table class="form-table"><tbody>';
			foreach($columns as $col) {
				$curheader=$col['display'];
				if(isset($data[$col['field']])) {
					$curcontent=self::get_redmine_field_to_text($data[$col['field']],$col['field']);
				} else {
					$curcontent=\arcanasoft\wpri\globals::EMPTY_FIELD;
				}
				$htmlstring.=self::get_wp_form_table_row($curheader,$curcontent);
			}
			if($extracols !== false) {
				foreach($extracols as $ecol) {
					
					if(!empty($ecol['value'])) {
						
					} else {
						$htmlstring.='<tr><th colspan="2">'.$ecol['header'].'</th></tr>';
					}
				}
			}
			$htmlstring .= '</tbody></table>';
			return $htmlstring;
		}
		
		static function get_wp_form_table_row($header, $content) {
			return '<tr><th scope="row">'.$header.'</th><td><p>'.$content.'</p></td></tr>';
		}
		
		static function get_wp_info_box($subject, $data, $columns) {					
			foreach($columns as $col) {
				$htmlstring.='<div>'.$col['display'].': <span>';
				if(isset($data[$col['field']])) {
					$htmlstring.=self::get_redmine_field_to_text($data[$col['field']], $col['field']);
				} else {
					$htmlstring.=\arcanasoft\wpri\globals::EMPTY_FIELD;
				}
				$htmlstring.='</span></div>';
			}
			
			$htmlstring=self::column_box($htmlstring, $subject);

			return $htmlstring;
		}
		
		
		static function get_redmine_field_to_text($fieldvalue, $fieldname=false) {
			if(is_array($fieldvalue) && !empty($fieldvalue['name'])) {
				return $fieldvalue['name'];
			} else {
				if($fieldname !== false && substr($fieldname,-3) == '_on') {
					return self::date_print($fieldvalue);
				} else {
					return $fieldvalue;
				}
			}
		}
		

		
		static function wpri_api_list_to_select_input($apiurl, $arrayfield, $fieldname, $preselect=false, $emptyoption=false) {
			$items = \arcanasoft\wpri\wpri_Redmine_Api::wpri_api_list_to_key_value_array($apiurl, $arrayfield);
			if($items === false) {
					return false;
			} 
			if(!empty($items) && $items !== false && is_array($items) && count($items) > 0) {
					if(count($items) == 1) {
						$htmlstring = '<select name="'.$fieldname.'" disabled>';
						foreach($items as $item) {
							$htmlstring.='<option selected="selected" value="'.$item['value'].'___'.$item['display'].'">'.$item['display'].'</option>';
							$hiddenarr=array($fieldname => $item['value'].'___'.$item['display']);
						}
						$htmlstring.='</select>';
						$htmlstring.=self::get_hidden_input_html($hiddenarr);
					 } else {
						$htmlstring = '<select name="'.$fieldname.'">';
						if($preselect === false) {
							$preselect=(!empty(get_option( $fieldname))?get_option( $fieldname):$preselect);
						}
						if($emptyoption !== false && is_array($emptyoption) && isset($emptyoption['value']) && isset($emptyoption['display'])) {
							$htmlstring.='<option '.($preselect === false?'selected="selected" ':'').'value="'.$emptyoption['value'].'">'.$emptyoption['display'].'</option>';
						}
						foreach($items as $item) {
								if(!empty($item['value']) && !empty($item['display'])) {
										$htmlstring.='<option '.($item['value'] == $preselect?'selected="selected" ':'').'value="'.$item['value'].'___'.$item['display'].'">'.$item['display'].'</option>';
								}
						}
						$htmlstring.='</select>';
					 }
			} else {
					//$htmlstring = '<input type="hidden" name="'.$fieldname.'" value="'.$emptyoptionordefault.'"><strong>Fehler bei Abruf der Ticket Status -> Standard-Wert 1 wird benutzt</strong>';
					$htmlstring = __( 'No selection available', 'wpri' );
			}
			return $htmlstring;
		}
	

	
/* Layout */
/** WRAPPER **/
/**
 * Add the div.wrap and the h1.headline for the content
 */
		static function wrap_page($subject, $contents, $prepend='') {
			return '<!-- wrap -->
			<div class="wrap">
				<h1 class="wp-heading-inline">' . $subject .'</h1>
				'.$prepend.'
				<hr class="wp-header-end">
				'.$contents.'
			</div>
			<!-- /wrap -->';
		}
	
/** Column Layout **/
/**
 * adds a two column layout in the backend
 */
		static function two_column_layout ($contentColumn1, $contentColumn2, $layout=75-25) {
			$htmlstring='<div class="wpri-row">';
			switch ($layout) {
				case 50-50:
				$htmlstring.='<div class="wpri-column wpri-column-50">'.$contentColumn1.'</div>
				<div class="wpri-column wpri-column-50" style="float:right;">'.$contentColumn2.'</div>';
				break;
				case 75-25:
				$htmlstring.='<div class="wpri-column wpri-column-75">'.$contentColumn1.'</div>
				<div class="wpri-column wpri-column-25" style="float:right;">'.$contentColumn2.'</div>';
				break;
				case 25-75:
				$htmlstring.='<div class="wpri-column wpri-column-25">'.$contentColumn1.'</div>
				<div class="wpri-column wpri-column-75" style="float:right;">'.$contentColumn2.'</div>';
				break;
			};
			$htmlstring.='</div>';			
			return $htmlstring;
		}
		static function one_column_layout ($contentColumn1) {
			$htmlstring='<div class="wpri-row">';
				$htmlstring.='<div class="wpri-column wpri-column-100">'.$contentColumn1.'</div>';			
			return $htmlstring;
		}
		
/** BOX **/
/**
 * adds a white frame around the reported data
 */
		static function column_box($data, $subject="") {
			$htmlstring = '<div class="wrip-box">';
			if (!empty($subject)) $htmlstring.='<div class="wrip-box-title"><span>'.self::get_sub_heading($subject, 2).'</span></div>';
			$htmlstring.= '<div class="inside">'.$data.'</div>
			</div>';
			return $htmlstring;
		}
		
		
/** Backend-Tabs **/
/**
 * Output the Plugin Settings in Tab-Navigation
 * 
 * Make sure $data is an associative array
 */
		static function setting_tabs($data) {
			if(is_array($data) && count($data) > 1)
			{
				$count=0;
				$htmlstring='<h2 class="nav-tab-wrapper">';
				foreach ($data as $tabkey => $tabvalue) 
				{
					$htmlstring.='<a href="'.$url.'&tab='.$tabkey.'" class="nav-tab ';
					if(isset($_GET['tab']) && $_GET['tab'] == $tabkey) $htmlstring.='nav-tab-active';
					elseif (!isset($_GET['tab']) && $count==0) $htmlstring.='nav-tab-active';
					$htmlstring.='">'.str_replace("_", " ", $tabkey).'</a>';
					$count++;
				}
				$htmlstring.='</h2>';

				if(isset($_GET['tab']) && array_key_exists($_GET['tab'], $data))
				{
					settings_fields( $_GET['tab'] );
					do_settings_sections( $_GET['tab'] );  
				 
					$htmlstring.='<div class="inside">'.$data[$_GET['tab']].'</div>';
				}
				else
				{
					settings_fields( key($data) );
					do_settings_sections( key($data) );  
					reset($data);
					$htmlstring.='<div class="inside">'.$data[key($data)].'</div>';
				}
			}
			else	
			{
				reset($data);
				$htmlstring='<div class="inside">'.$data[key($data)].'</div>';
			}
		             
			return $htmlstring;

		}

/** NOTICE **/
/**
 * Generate the Notice Output
 * 
 * Make sure that you at least pass the content as an associative array with the key "content"
 */
		static function massage ($massage) {
				if (empty($massage['type'])) $massage['type'] = 'success';
				$htmlstring='<div class="notice ';
				if($massage['type'] == "success")
				{
					$htmlstring.='notice-success ';
				} 
				else if ($massage['type'] == "error") 
				{
					$htmlstring.='notice-error ';
				}
				else if ($massage['type'] == "warning") 
				{
					$htmlstring.='notice-warning ';
				}
				else if ($massage['type'] == "info") 
				{
					$htmlstring.='notice-info ';
				}
				$htmlstring.='is-dismissible"><div>'.$massage['content'].'</div>';
				if (!empty($massage['url'])) $htmlstring.='<div>'.$massage['url'].'</div>';
				$htmlstring.='</div>';

				return $htmlstring;
		}

		static function write_error_and_cancel($errormsg) {
			$massage['type'] = 'error';
			$massage['content'] = '<p>'.$errormsg.'</p>';
			$massage['content'] = '<p>'.\arcanasoft\wpri\globals::get_plugin_urls()['settings']['html'].'</p>';
				
			echo \arcanasoft\WP_Framework\WP_Html::massage($massage);
			die();
		}

/** FORM **/
/**
 * $id html name attribute
 * $formattrs=array takes submittext, htmlattrs (for form element)
 * $inputs= descriptive array for inputs
 * $hiddeninputs=name/value pairs of hidden input
 * $formtable=true if html should be wp formtable rows
 */
		static function get_form($id, $formattrs, $inputs, $hiddeninputs=false, $formtable=false) {

			$htmlstring='<form name="'.$id.'"';
			$hasmethod=false;
			$hasaction=false;
			if(!empty($formattrs['htmlattrs'])) {
				foreach($formattrs['htmlattrs'] as $curattrs => $val) {
					if($hasmethod === false && $curattrs == 'method') {
						$hasmethod = true;
					}
					if($hasaction === false && $curattrs == 'action') {
						$hasaction = true;
					}
					$htmlstring.=' '.$curattrs.'="'.$val.'"';
				}
			}
			$htmlstring.=($hasmethod===false)?' method="post"':'';	//default method post
			$htmlstring.=($hasaction===false)?' action=""':'';	//default method post
			$htmlstring.='>';
			if($hiddeninputs !== false) {
				$htmlstring.=self::get_hidden_input_html($hiddeninputs);
			}
			$htmlstring.=(($formtable === true)?'<table class="form-table"><tbody>':'');
			foreach($inputs as $inp) {
				$htmlstring.=self::get_form_input_html($inp, $formtable);
			}
			$htmlstring.=(($formtable === true)?'<tr><th scope="row" colspan="2">':'');
			$htmlstring.='<p class="submit"><input type="submit" name="Submit" class="button-primary" value="' .((!empty($formattrs['submittext']))?$formattrs['submittext']:'Submit').'" /></p>';
			$htmlstring.=(($formtable === true)?'</th></tr>':'');
			if($formtable === true) {
				$htmlstring.='</tbody></table>';
			}
			$htmlstring.='</form>';
			return $htmlstring;
		}
		
		static function get_hidden_input_html($hiddeninputs) {
			$htmlstring='';
			foreach($hiddeninputs as $inpname => $inpval) {
				$htmlstring.='<input type="hidden" name="'.$inpname.'" value="'.$inpval.'" />';
			}
			return $htmlstring;
		}
		
		static function get_form_input_html($inputdata, $formtablerow=false) {
			$htmlstring='';
			if($formtablerow === true) {
				$htmlstring.='<tr><th scope="row">'.((!empty($inputdata['display']))?$inputdata['display']:'').'</th><td><p>';
			}
			if(!empty($inputdata['type'])) {
				if($inputdata['type'] == 'textarea') {
					$htmlstring.='<textarea name="'.$inputdata['name'].'" id="'.$inputdata['name'].'"';
					if(!empty($inputdata['htmlattrs']) && is_array($inputdata['htmlattrs'])) {
						foreach($inputdata['htmlattrs'] as $attr => $val) {
							$htmlstring.=' '.$attr.'="'.$val.'"';
						}
					}
					$htmlstring.='>';
					$htmlstring.='</textarea>';
				} elseif($inputdata['type'] == 'select') {
					if(!empty($inputdata['apiurl']) && !empty($inputdata['arrayfield']) && !empty($inputdata['fieldname'])) {
						$htmlstring.=self::wpri_api_list_to_select_input($inputdata['apiurl'],$inputdata['arrayfield'],$inputdata['fieldname'],
								(!empty($inputdata['preselect'])?$inputdata['preselect']:false),(!empty($inputdata['emptyoption'])?$inputdata['emptyoption']:false));
					} else {
						$htmlstring.=__('Error creating selection list','wpri');
					}
				} else {
					$htmlstring.='<input type="'.$inputdata['type'].'" name="'.$inputdata['name'].'" id="'.$inputdata['name'].'"'.((!empty($inputdata['value']))?' value="'.$inputdata['value'].'"':'');
					if(!empty($inputdata['htmlattrs']) && is_array($inputdata['htmlattrs'])) {
						foreach($inputdata['htmlattrs'] as $attr => $val) {
							$htmlstring.=' '.$attr.'="'.$val.'"';
						}
					}
					$htmlstring.=' />';
				}
			}
			$htmlstring.=(($formtablerow === true)?'</p></td></tr>':'');
			return $htmlstring;
		}



/** FOOTER **/
/**
 * Output the Plugin-Footer with Datetime.
 */
static function arcanasoft_footer () {
	$htmlstring='<div id="wpri-footer">
		<div class="wpri-aligncenter">
			Powered by <a href="https://arcanasoft.de">arcanasoft</a> © 2018';
			if(date("Y",$timestamp) > "2018")
			{
				$htmlstring.='-'.date("Y",$timestamp);
			};  
		$htmlstring.='</div>
	</div>';

	return $htmlstring;
}

/** Subheadings **/
		static function get_sub_heading($subject, $stage="2") {
			return '<h'.$stage.'>' . $subject .'</h'.$stage.'>';
		}
		
/** Date and Time **/
/*
 * $timestamp sould be a timestamp
 */
		static function date_print($timestamp) {
			return __( 'before', 'wpri' ).' '.human_time_diff(date("U",strtotime($timestamp))).' ('.date('d.m.Y H:i',strtotime($timestamp)).')';
		}

	}
?>
