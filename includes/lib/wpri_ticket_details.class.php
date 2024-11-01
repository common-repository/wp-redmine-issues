<?php
/*
 * WP-Redmine-Issues (WPRI) by arcanasoft
**/

namespace arcanasoft\wpri;

require_once('wpri_ticket_journal_table.class.php');
	class wpri_Ticket_Details {
		// Class constructor 
		static function fetch_ticket_data($url, $apikey, $ticketid, $paged, $addurl='') {
			$redmineurl='/issues/'.$ticketid.'.json';
			if($addurl != '')
				$redmineurl.='?'.$addurl;
				
			return wpri_Redmine_Api::get_api_data($redmineurl);
		}
		
		static function get_ticket_data_html($url, $apikey, $ticketid, $filters, $journal) {
			$htmlstr='';
			if($journal === false) 
				$ticketdata=self::fetch_ticket_data($url, $apikey, $ticketid, $filters);
			else
				$ticketdata=self::fetch_ticket_data($url, $apikey, $ticketid, $filters, 'include=journals');
			if(!empty($ticketdata['Error'])) {
				\arcanasoft\WP_Framework\WP_Html::write_error_and_cancel($ticketdata['Error']);
			} else {
				self::ticket_data_to_wp_table($ticketdata, $filters);
			}
		}
		
		static function ticket_data_to_wp_table($ticketdata, $filters) {
			
				$htmlstring = '<strong>'.__( 'Error reading issue data', 'wpri' ).'</strong>';
			if(!empty($ticketdata['issue'])) {
				$backtoticketsbtn.='<p><a class="button button-primary button-large" href="'.\arcanasoft\wpri\globals::get_plugin_urls()['tickets']['url'];
				if(is_array($filters) && count($filters) > 0) {
					foreach($filters as $filtername => $filterval) {
						$backtoticketsbtn.='&'.$filtername.'='.$filterval;
					}
				}
				$backtoticketsbtn.='">'.__( 'Back to issue list', 'wpri' ).'</a></p>';
				
				/* 2 Column - Ticket Details */
				$cols=array(array('field' => 'subject', 'display' => __('Subject', 'wpri')),array('field' => 'description', 'display' => __('Description', 'wpri')));
				$extracols=array(array('header' => $backtoticketsbtn));
				$column1=\arcanasoft\WP_Framework\WP_Html::get_wp_form_table($ticketdata['issue'],$cols, $extracols);

				$cols=array(array('field' => 'created_on', 'display' => __('Created', 'wpri')),array('field' => 'updated_on', 'display' => __('Updated', 'wpri')));
				$column2=\arcanasoft\WP_Framework\WP_Html::get_wp_info_box(__('Status', 'wpri').': '.$ticketdata['issue']['status']['name'],$ticketdata['issue'],$cols, $extracols);

				$htmlstring=\arcanasoft\WP_Framework\WP_Html::two_column_layout($column1, $column2);
				unset($column1); unset($column2);

				
				if(get_option(\arcanasoft\wpri\globals::get_option_fields()['show_ticket_journals']['option']) != "off")
				{
					$column1=\arcanasoft\WP_Framework\WP_Html::get_sub_heading(__( 'Comment', 'wpri' ));

					$tab=new wpri_Ticket_Journal_Table();
					$tab->pre_prepare_items(self::ticket_journal_data_to_wp_table($ticketdata['issue']['journals']));
					$tab->prepare_items();
					$column1.=$tab->return_html();
					
					$column1.='<div class="wpri-sendform">';
					
					$hiddeninputs=array('ticketid'=> $_GET['wpriticketid'], 'sendnoteform' => 'sendnoteform');
					$inputs=array(array('type'=> 'textarea', 'name'=> 'newnote', 
								'htmlattrs' => array('rows' => 5, 'placeholder' => __( 'New comment', 'wpri' ), 'autocomplete' => 'off', 'class' => 'wp-editor-area')
								));
					$column1.=\arcanasoft\WP_Framework\WP_Html::get_form('sendnote', array('submittext' => esc_attr(__('Send', 'wpri'))),$inputs, $hiddeninputs);

					$column1.='</div>';

					$htmlstring.=\arcanasoft\WP_Framework\WP_Html::one_column_layout($column1);
					
					unset($columm1);

					$htmlstring.='<br class="clear" />';

				}
				
				echo \arcanasoft\WP_Framework\WP_Html::wrap_page( __( 'Issue', 'wpri-issue' ).' '.$_GET['wpriticketid'].' ('.$ticketdata['issue']['status']['name'].') <a href="'.\arcanasoft\wpri\globals::get_plugin_urls()['create_ticket']['url'].'" class="page-title-action">'.__( 'New Issue', 'wpri' ).'</a>',$htmlstring);
				
			}

		
		}


		static function ticket_journal_data_to_wp_table($journaldata) {
			$retarr=array();
			
			for($i=count($journaldata)-1;$i>=0;$i--) {
				if(!empty($journaldata[$i]['notes'])) {
					$temparr=array(
							'created_on'	=> \arcanasoft\WP_Framework\WP_Html::date_print($journaldata[$i]['created_on']),
							'user'			=> (!empty($journaldata[$i]['user']['name'])?$journaldata[$i]['user']['name']:'N/A'),
							'notes'			=> $journaldata[$i]['notes']
							);
					$retarr[]=$temparr;
				}
			}
			return $retarr;
		}
		
		static function ticket_table_add_row($key, $value) {
			return '<tr><th scope="row">'.$key.'</th><td><p>'.$value.'</p></td></tr>';
		}
				
	}
?>
