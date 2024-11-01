<?php
/*
 * WP-Redmine-Issues (WPRI) by arcanasoft
**/

namespace arcanasoft\wpri;

	class wpri_Redmine_Api {
		static function put_api_data($url, $params) { //probably only updating issues with notes
			
			$redmineurl=rtrim(get_option( \arcanasoft\wpri\globals::get_option_fields()['redmine_url']['option'] ),'/').$url;
			$request = wp_remote_post($redmineurl, array(
				'method' => 'PUT',
				'headers' => array(
					'Content-Type'		=> 'application/json',
					'X-Redmine-API-Key' => get_option( \arcanasoft\wpri\globals::get_option_fields()['redmine_api_key']['option'] )
				),
				'body' => json_encode($params),
			));
			if(is_a($request, 'WP_Error')) {
				if(!empty($request->errors['http_request_failed'])) {
					return array('Error' => '<div class="notice notice-error">
						<p>'.__( 'Internal error retrieving data', 'wpri' ).'</p>
						<p>'.$request->errors['http_request_failed'][0].'</p>
					</div>');
				} else {
					return array('Error' =>  '<div class="notice notice-error">
						<p>'.__( 'Internal error retrieving data', 'wpri' ).'</p>
						<p>'.print_r($request,true).'</p>
					</div>');
				}
				die();
			}
			if(!empty($request['response']) && !empty($request['response']['code'])) {
				switch($request['response']['code']) {
					case 200: 	if(!empty($request['body'])) {
									return json_decode($request['body'], true);
								} else {
									return array();
								}
								break;
					case 201: 	if(!empty($request['body'])) {
									//error_log(print_r($request['body'],true));
									return json_decode($request['body'], true);
								} else {
									return array();
								}
								break;
					/*case 404:	wpri_Redmine_Api::write_error_and_cancel('Fehler 404 (nicht gefunden) bei Aufruf der URL "'.$redmineurl.'"');
								break;
					case 403:	wpri_Redmine_Api::write_error_and_cancel('Fehler 403 (Zugriff verweigert) bei Aufruf der URL "'.$redmineurl.'"');
								break;*/
					default:	return array('Error' => __( 'Error', 'wpri' ).': '.$request['response']['code'].'('.__($request['response']['message']).') '.__( 'accessing URL', 'wpri' ).' "'.$redmineurl.'"');
								break;
					
				}
			} else {
				return array('Error' => ''.__( 'Unexpected error accessing URL', 'wpri' ).' "'.$redmineurl.'" <br>Request Dump:<br>'.print_r($request,true));
			}
		}
		
		static function post_api_data($url, $params) {
			
			
			$redmineurl=rtrim(get_option( \arcanasoft\wpri\globals::get_option_fields()['redmine_url']['option'] ),'/').$url;
//			error_log('POST URL -------------------------------'.$redmineurl.'----------------------');
//			error_log('POST PARAMS -------------------------------'.print_r($params,true).'----------------------');
			$request = wp_remote_post($redmineurl, array(
				'method' => 'POST',
				'headers' => array(
					'Content-Type'		=> 'application/json',
					'X-Redmine-API-Key' => get_option( \arcanasoft\wpri\globals::get_option_fields()['redmine_api_key']['option'] )
				),
				'body' => json_encode($params),
			));
			
			//error_log('REQUEST -------------------------------'.print_r($request,true).'----------------------');
			if(is_a($request, 'WP_Error')) {
				if(!empty($request->errors['http_request_failed'])) {
					return array('Error' => '<div class="notice notice-error">
						<p>'.__( 'Internal error retrieving data', 'wpri' ).'</p>
						<p>'.$request->errors['http_request_failed'][0].'</p>
					</div>');
				} else {
					return array('Error' =>  '<div class="notice notice-error">
						<p>'.__( 'Internal error retrieving data', 'wpri' ).'</p>
						<p>'.print_r($request,true).'</p>
					</div>');
				}
				die();
			}
			if(!empty($request['response']) && !empty($request['response']['code'])) {
				switch($request['response']['code']) {
					case 200: 	if(!empty($request['body'])) {
									//error_log(print_r($request['body'],true));
									return json_decode($request['body'], true);
								} else {
									return array();
								}
								break;
					case 201: 	if(!empty($request['body'])) {
									//error_log(print_r($request['body'],true));
									return json_decode($request['body'], true);
								} else {
									return array();
								}
								break;
					/*case 404:	wpri_Redmine_Api::write_error_and_cancel('Fehler 404 (nicht gefunden) bei Aufruf der URL "'.$redmineurl.'"');
								break;
					case 403:	wpri_Redmine_Api::write_error_and_cancel('Fehler 403 (Zugriff verweigert) bei Aufruf der URL "'.$redmineurl.'"');
								break;*/
					default:	return array('Error' => __( 'Error', 'wpri' ).$request['response']['code'].'('.__($request['response']['message']).') '.__( 'accessing URL', 'wpri' ).' "'.$redmineurl.'"');
								break;
					
				}
			} else {
				return array('Error' => __( 'Unexpected error accessing URL', 'wpri' ).' "'.$redmineurl.'" <br>Request Dump:<br>'.print_r($request,true));
			}
		}
		
		static function get_api_data($url) {
			
			$redmineurl=rtrim(get_option( \arcanasoft\wpri\globals::get_option_fields()['redmine_url']['option'] ),'/').$url;
			$request = wp_remote_get($redmineurl, array(
				'headers' => array(
					'Content-Type'		=> 'application/json',
					'X-Redmine-API-Key' => get_option( \arcanasoft\wpri\globals::get_option_fields()['redmine_api_key']['option'] )
				),
			));
			if(is_a($request, 'WP_Error')) {
				if(!empty($request->errors['http_request_failed'])) {
					return array('Error' => '<div class="notice notice-error">
						<p>'.__( 'Internal error retrieving data', 'wpri' ).'</p>
						<p>'.$request->errors['http_request_failed'][0].'</p>
					</div>');
				} else {
					return array('Error' =>  '<div class="notice notice-error">
						<p>'.__( 'Internal error retrieving data', 'wpri' ).'</p>
						<p>'.print_r($request,true).'</p>
					</div>');
				}
				die();
			}
			if(!empty($request['response']) && !empty($request['response']['code'])) {
				switch($request['response']['code']) {
					case 200: 	if(!empty($request['body'])) {
									return json_decode($request['body'], true);
								} else {
									return array();
								}
								break;
					/*case 404:	wpri_Redmine_Api::write_error_and_cancel('Fehler 404 (nicht gefunden) bei Aufruf der URL "'.$redmineurl.'"');
								break;
					case 403:	wpri_Redmine_Api::write_error_and_cancel('Fehler 403 (Zugriff verweigert) bei Aufruf der URL "'.$redmineurl.'"');
								break;*/
					default:	return array('Error' => __('Error', 'wpri').' '.$request['response']['code'].'('.__($request['response']['message']).') '.__('accessing URL', 'wpri').' "'.$redmineurl.'"');
								break;
					
				}
			} else {
				return array('Error' => __( 'Unexpected error accessing URL', 'wpri' ).' "'.$redmineurl.'" <br>Request Dump:<br>'.print_r($request,true));
			}
			
		}

		static function wpri_api_list_to_key_value_array($apiurl, $arrayfield) {
			$arr = self::get_api_data($apiurl);
			if(!empty($arr['Error'])) {
				\arcanasoft\WP_Framework\WP_Html::write_error_and_cancel($arr['Error']);
			}
			$primearrfield=$arrayfield;
			if(is_array($arrayfield) && count($arrayfield) > 0) {
				$primearrfield=$arrayfield[0];
			}
			if(!empty($arr[$primearrfield]) && is_array($arr[$primearrfield]) && count($arr[$primearrfield]) > 0) {
				$retarr=array();
				if(is_array($arrayfield) && count($arrayfield) > 0) {
					$curitems=$arr;
					$items=false;
					foreach($arrayfield as $field) {
						if(isset($curitems[$field]) && is_array($curitems[$field])) {
							$curitems = $curitems[$field];
						} else {
							$curitems=false;
							break;
						}
					}
					$items=$curitems;
				} else {
					$items = $arr[$arrayfield];
				}
				if($items === false) {
					\arcanasoft\WP_Framework\WP_Html::write_error_and_cancel(__( 'Error retrieving list from Redmine', 'wpri' ).' wpri_Redmine_Api::wpri_api_list_to_key_value_array');
				}
				foreach($items as $item) {
					$retarr[]=array('value' => $item['id'], 'display' => $item['name']);
				}
				//return (count($retarr)>0?$retarr:false);
				return $retarr;
			}
			return false;
		}
		
	}
?>
