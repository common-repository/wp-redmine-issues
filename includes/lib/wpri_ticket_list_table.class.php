<?php
/*
 * WP-Redmine-Issues (WPRI) by arcanasoft
**/

namespace arcanasoft\wpri;

	class wpri_Ticket_List_Table extends wpri_Wp_List_Table {
		
		// Class constructor 
		public function __construct() {
			$cols = array(
					'id'    => __( 'ID', 'wpri' ),
					'tracker' => __( 'Tracker', 'wpri' ),
					'author' => __( 'Created by', 'wpri' ),
					'updated_on'    => __( 'Updated', 'wpri' ),
					'status'    => __( 'Status', 'wpri' ),
					'assigned_to'    => __( 'Assigned to', 'wpri' ),
					'subject'    => __( 'Subject', 'wpri' )
				);
			
			$sortablecols = array(
					'id'			=> array('id',false),
					'updated_on'	=> array('updated_on',false),
				);
			parent::__construct( [
				'singular' => __( 'Issue', 'wpri' ), //singular name of the listed records
				'plural'   => __( 'Issues', 'wpri' ), //plural name of the listed records
				'ajax'     => true //should this table support ajax?
			], $cols, $sortablecols );
		}
		
		function pre_prepare_items($start=0, $count=10, $filters=false) {
			$this->start=$start;
			$this->count=$count;
			$this->filters=$filters;
		}
		
		
		public function column_default( $item, $column_name ) {
			if($column_name == 'id') {
				$retstring = '<a href="'.admin_url().'admin.php?page=wpri&wpriticketid='.$item[ $column_name ];
				if(is_array($this->filters) && count($this->filters) > 0) {
					foreach($this->filters as $filtername => $filterval) {
						$retstring.='&'.$filtername.'='.$filterval;
					}
				}
				
				$retstring.='">'.$item[ $column_name ].'</a>';
				return $retstring;
			} else {
				if(!empty($item[ $column_name ])) {
					return $item[ $column_name ];
				} else {
					return '<strong>'.__( 'empty', 'wpri' ).'</strong>';
				}
			}
		}
		
		function getListData() {
			$htmlstr='';
			if($this->start == 0) 
				$this->start=1;
			$this->curpaged=$this->start;
			//$redmineurl=rtrim(get_option( \arcanasoft\wpri\globals::get_option_fields()['redmine_url']['option'] ),'/').'/issues.json?offset='.(($this->start - 1) * $this->count).'&limit='.$this->count.'&status_id=open';
			$redmineurl=\arcanasoft\wpri\globals::get_api_urls()['tickets'].'?status_id='.(!empty($this->filters['status_id'])?$this->filters['status_id']:'open').'&offset='.(($this->start - 1) * $this->count).'&limit='.$this->count;
			
			if(! empty( $_GET['orderby'])) {
				$orderby = $_GET['orderby'];
				$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'desc';			
			} else {
				$orderby='updated_on';
				$order = 'desc';
			}
			
			
			
			if($this->filters !== false) {
				foreach($this->filters as $filter => $val) {
					if($filter !== 'status_id') {
						$redmineurl.='&'.$filter.'='.$val;
					}
				}
			}
			
			$redmineurl.='&sort='.$orderby.':'.$order;

			$redminedata = wpri_Redmine_Api::get_api_data($redmineurl);
			
			
			if(!empty($redminedata['Error'])) {
				\arcanasoft\WP_Framework\WP_Html::write_error_and_cancel($redminedata['Error']);
			} else {
				 return $this->ticketArrayToWPATArray($redminedata);
			}
			
		}

		function ticketArrayToWPATArray($arr) {
			$newarr = array();
			if(!empty($arr['issues'])) {
				foreach($arr['issues'] as $item) {
					$itemarr=array();
					foreach($this->get_columns() as $col => $displ) {
						if(!empty($item[$col])) {
							if(is_array($item[$col]) && !empty($item[$col]['name'])) {
								$itemarr[$col] = $item[$col]['name'];
							} else {
								if(substr($col,-3) == '_on') {
									$itemarr[$col] = \arcanasoft\WP_Framework\WP_Html::date_print($item[$col]);
								} else {
									$itemarr[$col] = $item[$col];
								}
							}
						}
					}

					if(count($itemarr) > 0) 
						$newarr[]=$itemarr;
				}
				if(!empty($arr['total_count']) && !empty($arr['limit'])) {
					$this->set_pagination_args(
							array(
									'total_items'	=> $arr['total_count'],
									'per_page'		=> $arr['limit'],
							)
					);
				}
				//var_dump($newarr);
				
			}
			
			return $newarr;
		}
		




	}
?>
