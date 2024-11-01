<?php
/*
 * WP-Redmine-Issues (WPRI) by arcanasoft
**/

namespace arcanasoft\wpri;

	if ( ! class_exists( '\WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}
	class wpri_Ticket_Journal_Table extends wpri_Wp_List_Table {
		// Class constructor 
		public function __construct() {
			$cols = array(
				'created_on'    => __( 'Created', 'wpri' ),
				'user' => __( 'before', 'wpri' ),
				'notes'    => __( 'Comment', 'wpri' ),
			  );
			parent::__construct( [
				'singular' => __( 'Comment', 'wpri' ), //singular name of the listed records
				'plural'   => __( 'Comments', 'wpri' ), //plural name of the listed records
				'ajax'     => true //should this table support ajax?
			], $cols );
		}

		
		function pre_prepare_items($journaldata) {
			array_multisort($journaldata, SORT_DESC);
			$this->items = $journaldata;
		}
		
		public function getListData() {
			return array();
		}


	}
?>
