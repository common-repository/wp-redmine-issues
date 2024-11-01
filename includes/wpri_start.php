<?php
/*
 * WP-Redmine-Issues (WPRI) by arcanasoft
**/

namespace arcanasoft\wpri;

require_once('lib/wpri_ticket_list_table.class.php');
require_once('lib/wpri_ticket_details.class.php');
require_once('lib/wpri_ticket_create_page.class.php');
//add_action( 'admin_notices', 'wpri_admin_notice_vars_missing' );

class tickets {

static function call(){
	
	/*	if (empty(get_option( $opt_name_redmine_url )) || empty(get_option( $opt_name_redmine_api_key ))) {
		return;
	} else {
*/	
	\arcanasoft\wpri\globals::config_check();
	//wpri_admin_notice_vars_missing();

	
	if ($_POST['dialogpage'] == 'final') self::create_ticket($_POST);


		$filters=false;
		$checkget=array('tracker_id','status_id','project_id');
		if(!empty($_GET)) {
			foreach($checkget as $check) {
				if(!empty($_GET[$check]) && $_GET[$check] != 0) {
					if($filters === false)
						$filters=array();
					$cur=\arcanasoft\wpri\globals::split_param($_GET[$check]);
					$filters[$check]=$cur['value'];
				}
			}
		}
		$filters['paged']=(!empty($_GET[ 'paged' ])?$_GET[ 'paged' ]:0);
		if(!empty($_GET['perpage']) || !empty(get_option( \arcanasoft\wpri\globals::get_option_fields()['tickets_per_page']['option'] ))) {
			$filters['perpage']=(!empty($_GET['perpage'])?$_GET['perpage']:get_option( \arcanasoft\wpri\globals::get_option_fields()['tickets_per_page']['option']) );
		} else {
			$filters['perpage'] = null;
		}
		error_log(print_r($_POST,true));
		if(!empty($_POST['sendnoteform'])) {
			
			$curuser=wp_get_current_user();
			$noteuserdetails= htmlspecialchars($_POST['newnote']).'

			----			
			Benutzer: '.$curuser->display_name.' E-Mail: '.$curuser->user_email;

			$ans = wpri_Redmine_Api::put_api_data('/issues/'.$_POST['ticketid'].'.json',array('issue' => array('notes' => $noteuserdetails)));
			if(!empty($ans['Error'])) {
				\arcanasoft\WP_Framework\WP_Html::write_error_and_cancel($ans['Error']);
			} else {
				$_GET['wpriticketid']=$_POST['ticketid'];
			}
		}
		
		if(!empty($_GET['wpriticketid'])) {
			self::show_ticket($_GET['wpriticketid'], $filters);
		} else {
			self::list_tickets($filters);
		}

//}
}

static function create_ticket($POST) {
		if (!empty($POST)) {
			if($POST['dialogpage'] == 'final' ) {
			//error_log(json_encode(array('issue' => $POST)));
			$ans = wpri_Redmine_Api::post_api_data(\arcanasoft\wpri\globals::get_api_urls()['tickets'], array('issue' => $POST));
			if(!empty($ans['Error'])) {
				\arcanasoft\WP_Framework\WP_Html::write_error_and_cancel($ans['Error']);
			} else {
				if(is_array($ans) && !empty($ans['issue']) && !empty($ans['issue']['id']) ) {
				
					$massage['content'] = '<p>'.__( 'Issue', 'wpri' ).' '.(!empty($ans['issue']['subject'])?'"'.$ans['issue']['subject'].'" ':' ').' '.__( 'was created', 'wpri' ).'</p>'.
						(!empty($ans['issue']['id'])?'<p>'.__( 'ID', 'wpri' ).': '. $ans['issue']['id']. '</p>':'').
						(!empty($ans['issue']['id'])?'<p>'.__( 'Project', 'wpri' ).': '. $ans['issue']['project']['name']. '</p>':'');
					$massage['url'] = '<p><a href="'.admin_url().'admin.php?page=wpri&wpriticketid='.$ans['issue']['id'].'">'.__( 'Show issue', 'wpri' ).'</a></p>';
						
					echo \arcanasoft\WP_Framework\WP_Html::massage($massage);
					//die();
				} else {
					\arcanasoft\WP_Framework\WP_Html::write_error_and_cancel(__( 'Unexpected data returned', 'wpri' ).' '.print_r($ans,true));
				}
			}
		}
	}
	else
	{
		echo wpri_Ticket_Create_Page::get_dialog_data_html($_POST);
	}
}


static function show_ticket($ticketid, $filters=false) {
	wpri_Ticket_Details::get_ticket_data_html(
			get_option(\arcanasoft\wpri\globals::get_option_fields()['redmine_url']['option']),
			get_option(\arcanasoft\wpri\globals::get_option_fields()['redmine_api_key']['option']),$_GET['wpriticketid'],
		// replace paged parameter with full query_string					(!empty($_GET[ 'paged' ])?$_GET[ 'paged' ]:1),
			(!empty($filters)?$filters:array('paged' => 1)),
			( (!empty(get_option(\arcanasoft\wpri\globals::get_option_fields()['show_ticket_journals']['option'])) && get_option(\arcanasoft\wpri\globals::get_option_fields()['show_ticket_journals']['option']) == 'on') ? true : \arcanasoft\wpri\globals::get_option_fields()['show_ticket_journals']['default'] == 'on' ? true : false  )
		);
}

static function list_tickets($filters) {
			/*echo '<div class="wrap">
				<h1 class="wp-heading-inline">' . __( 'Issues', 'wpri' ).'</h1>
				<a href="'.\arcanasoft\wpri\globals::get_plugin_urls()['create_ticket']['url'].'" class="page-title-action">'.__( 'New Issue', 'wpri' ).'</a>
				<hr class="wp-header-end">';*/
			$prepend = '<a href="'.\arcanasoft\wpri\globals::get_plugin_urls()['create_ticket']['url'].'" class="page-title-action">'.__( 'New Issue', 'wpri' ).'</a>';
			$htmlstring=self::ticket_list_build_filters($filters, $filters['perpage']);
			//var_dump($filters);
			$tab=new wpri_Ticket_List_Table();
			$tab->pre_prepare_items($filters['paged'], $filters['perpage'] , $filters);
			$tab->prepare_items();
			//$tab->display();
			$htmlstring.=$tab->return_html();
			//echo '</div>';
			echo \arcanasoft\WP_Framework\WP_Html::wrap_page( __( 'Issues', 'wpri' ), $htmlstring, $prepend);
}

static function ticket_list_build_filters($postfilters, $perpage) {
$htmlstring = '<div class="tablenav top"><div class="alignright"><form id="filter_form" method="get" action="">';
$htmlstring.= '<input type="hidden" name="page" value="wpri">';
$htmlstring.='<label for="perpage">'.__( 'Issues', 'wpri' ).' '.__( 'per page', 'wpri' ).':</label><select id="perpage" name="perpage">';
$i=10;
while($i<=30) {
$htmlstring.='<option '.((intval($perpage) == $i)?'selected="selected" ':'').'>'.$i.'</option>';
$i=$i+5;
}
$htmlstring.='</select>';

$issuestatusesfilter = \arcanasoft\WP_Framework\WP_Html::wpri_api_list_to_select_input(\arcanasoft\wpri\globals::get_api_urls()['issue_statuses'], 'issue_statuses', 'status_id', (!empty($postfilters['status_id'])?$postfilters['status_id']:false), array('value' => '0', 'display' => __('All status','wpri')));
$htmlstring .= ($issuestatusesfilter === false)?__('No issue status found','wpri'):$issuestatusesfilter;
$projectsfilter = \arcanasoft\WP_Framework\WP_Html::wpri_api_list_to_select_input(\arcanasoft\wpri\globals::get_api_urls()['projects'], 'projects', 'project_id', (!empty($postfilters['project_id'])?$postfilters['project_id']:false), array('value' => '0', 'display' => __('All projects','wpri')));
$htmlstring .= ($projectsfilter === false)?__('No projects found','wpri'):$projectsfilter;
$trackerfilter = \arcanasoft\WP_Framework\WP_Html::wpri_api_list_to_select_input(\arcanasoft\wpri\globals::get_api_urls()['trackers'], 'trackers', 'tracker_id', (!empty($postfilters['tracker_id'])?$postfilters['tracker_id']:false), array('value' => '0', 'display' => __('All trackers','wpri')));
$htmlstring .= ($trackerfilter === false)?__('No trackers found','wpri'):$trackerfilter;

$htmlstring .= '<input type="submit" name="Submit" class="button-primary" value="'.esc_attr(__( 'Save', 'wpri' )) .'" />
';
$htmlstring .= '</form></div></div>';
return $htmlstring;
}
}
?>
