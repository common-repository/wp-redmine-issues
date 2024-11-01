<?php
/*
 * WP-Redmine-Issues (WPRI) by arcanasoft
**/

namespace arcanasoft\wpri;

	class wpri_Ticket_Create_Page {
		// Class constructor 
		static function create_issue($url, $params) {
						$redmineurl=$wpriurls['tickets'];
			wpri_Redmine_Api::get_api_data($redmineurl, $params);
		}
		
		static function get_dialog_data_html($POST) {
			$inpre='create_issue_'; //inputnameprefix
			$formattrs=array('submittext' => __( 'Next', 'wpri' ), 'htmlattrs' => array('class' => 'wpri-input-form'));
			if(empty($POST['dialogpage']) ) {
				$inputs = array(array('type' => 'select','display' => __( 'Project', 'wpri' ),'apiurl' => \arcanasoft\wpri\globals::get_api_urls()['projects'], 'arrayfield' => 'projects', 'fieldname' => $inpre.'project'));
				
				$hiddeninputs=array('dialogpage' => 1);
				$cont = \arcanasoft\WP_Framework\WP_Html::get_form('ticket-create', $formattrs, $inputs, $hiddeninputs,true);
				
				
				$htmlstring=\arcanasoft\WP_Framework\WP_Html::wrap_page( __( 'Create issue', 'wpri' ).' - '. __( 'Select project', 'wpri' ),$cont);
			} else {
				if($POST['dialogpage'] == 1) {
					$project = false;
					if(!empty($POST[$inpre.'project'])) {
						$project = \arcanasoft\wpri\globals::split_param($POST[$inpre.'project']);
						$hiddeninputs=array('dialogpage' => 2,$inpre.'project' => $POST['create_issue_project']);
					}
					if($project !== false) {
							$inputs=array(array('type' => 'select','display' => __( 'Tracker', 'wpri' ),'apiurl' => '/projects/'.$project['value'].'.json?include=trackers', 'arrayfield' =>  array('project','trackers'), 'fieldname' => $inpre.'tracker'));
							if(\arcanasoft\wpri\globals::using_categories())
								$inputs[]=array('type' => 'select','display' => __( 'Category', 'wpri' ),'apiurl' => '/projects/'.$project['value'].'.json?include=issue_categories', 'arrayfield' =>  array('project','issue_categories'), 'fieldname' => $inpre.'category');
							$cont = \arcanasoft\WP_Framework\WP_Html::get_form('ticket-create', $formattrs, $inputs, $hiddeninputs,true);
							$htmlstring = \arcanasoft\WP_Framework\WP_Html::wrap_page(__( 'Create issue in', 'wpri' ).' "'.$project['name'].'" - '. __( 'Select tracker and category', 'wpri' ), $cont);
					} else {
							\arcanasoft\WP_Framework\WP_Html::write_error_and_cancel(__( 'Missing Project-ID or Tracker-ID -> Can not continue"', 'wpri' ));
					}
					
				} elseif($POST['dialogpage'] == 2) {
					if(!empty($POST[$inpre.'project']) && !empty($POST[$inpre.'tracker'])) {
						$project = \arcanasoft\wpri\globals::split_param($POST[$inpre.'project']);
						$tracker = \arcanasoft\wpri\globals::split_param($POST[$inpre.'tracker']);
						if($tracker !== false) {
							$hiddeninputs=array('dialogpage' => 3,$inpre.'project' => $POST[$inpre.'project'],$inpre.'tracker' => $POST[$inpre.'tracker']);
							if(\arcanasoft\wpri\globals::using_categories()) {
								if(!empty($POST[$inpre.'category'])) {
									$category = \arcanasoft\wpri\globals::split_param($POST[$inpre.'category']);
									if($category !== false)
										$hiddeninputs[$inpre.'category'] = $POST[$inpre.'category'];
								} else {
									$category=false;
								}
							}
							$inputs=array(
								array('type' => 'text', 'name' => $inpre.'subject', 'htmlattrs' => array('wprimandatoryinput' => '1', 'size' => '80'), 'display' => __( 'Subject', 'wpri' ).' <strong>('.__( 'Required field', 'wpri' ).')</strong>:'),
								array('type' => 'textarea', 'name' => $inpre.'details', 'htmlattrs' => array('wprimandatoryinput' => '1', 'cols' => '80', 'rows' => '5'), 'display' => __( 'Details', 'wpri' ).' <strong>('.__( 'Required field', 'wpri' ).')</strong>:'),
							);
							$cont = \arcanasoft\WP_Framework\WP_Html::get_form('ticket-create', $formattrs, $inputs, $hiddeninputs,true);
							$htmlstring = \arcanasoft\WP_Framework\WP_Html::wrap_page(__( 'Create issue in', 'wpri' ).' "'.$project['name'].'" - '. __( 'Complete details', 'wpri' ), $cont);
						} else {
							\arcanasoft\WP_Framework\WP_Html::write_error_and_cancel(__( 'Missing Project-ID or Tracker-ID -> Can not continue', 'wpri' ));
						}
					} else {
						\arcanasoft\WP_Framework\WP_Html::write_error_and_cancel(__( 'Missing Project-ID or Tracker-ID -> Can not continue', 'wpri' ));
					}
			} elseif($POST['dialogpage'] == 3) {
				//print_r($POST);
					if(!empty($POST[$inpre.'project']) && !empty($POST[$inpre.'tracker']) && !empty($POST[$inpre.'subject']) && !empty($POST[$inpre.'details'])) {
						$project = \arcanasoft\wpri\globals::split_param($POST[$inpre.'project']);
						$tracker = \arcanasoft\wpri\globals::split_param($POST[$inpre.'tracker']);
						if($project !== false && $tracker !== false) {
							$curuser=wp_get_current_user();
							$desc=htmlspecialchars($POST['create_issue_details']) . '
							
							----
							' . __( 'User', 'wpri' ).': '.htmlspecialchars($curuser->display_name).' '.__( 'email', 'wpri' ).': '.htmlspecialchars($curuser->user_email);
							$hiddeninputs=array('dialogpage' => 'final','project_id' => $project['value'],'tracker_id' => $tracker['value'],
									'subject' => htmlspecialchars($POST[$inpre.'subject']),
									'description' => $desc
								);
							
							if(\arcanasoft\wpri\globals::using_categories()) {
								if(!empty($POST[$inpre.'category'])) {
									$category = \arcanasoft\wpri\globals::split_param($POST[$inpre.'category']);
									if($category !== false)
										$hiddeninputs[$inpre.'category'] = $POST[$inpre.'category'];
								} else {
									$category=false;
								}
							}
							$tabledata=array('project' => $project['name'],'tracker' => $tracker['name'],'category' => (($category===false)?'':$category['name']),'subject' => $POST[$inpre.'subject'],'details' => $desc);
							$tablecols=array(
									array('display' => __( 'Project', 'wpri' ), 'field' => 'project'),
									array('display' => __( 'Tracker', 'wpri' ), 'field' => 'tracker'),
									);
							if(\arcanasoft\wpri\globals::using_categories() && $category !== false) {
								$tablecols[]=array('display' => __( 'Category', 'wpri' ), 'field' => 'category');
							}
							$tablecols[]=array('display' => __( 'Subject', 'wpri' ), 'field' => 'subject');
							$tablecols[]=array('display' => __( 'Details', 'wpri' ), 'field' => 'details');
							$formattrs['submittext'] = __('Submit', 'wpri');
							$formattrs['htmlattrs'] ['action'] = \arcanasoft\wpri\globals::get_plugin_urls()['tickets']['url'];
							$cont = \arcanasoft\WP_Framework\WP_Html::get_wp_form_table($tabledata,$tablecols). \arcanasoft\WP_Framework\WP_Html::get_form('ticket-create', $formattrs, array(), $hiddeninputs,true);
							
							$htmlstring = \arcanasoft\WP_Framework\WP_Html::wrap_page(__( 'Create issue', 'wpri' ).' - '. __( 'Check and send', 'wpri' ), $cont);
						} else {
							\arcanasoft\WP_Framework\WP_Html::write_error_and_cancel(__( 'Missing Project-ID or Tracker-ID -> Can not continue', 'wpri' ));
						}
					} else {
						\arcanasoft\WP_Framework\WP_Html::write_error_and_cancel(__( 'Required data missing', 'wpri' ));
					}
					
			}
		}
		unset($_POST['dialogpage']);
		unset($_POST['Submit']);

		return $htmlstring;
		}

		
	}
?>
