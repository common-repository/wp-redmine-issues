<?php
/*
 * WP-Redmine-Issues (WPRI) by arcanasoft
**/

namespace arcanasoft\wpri;

	if ( ! class_exists( '\WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}
	abstract class wpri_Wp_List_Table extends \WP_List_Table {
		protected $curpaged;
		protected $start;
		protected $count;
		protected $filters;
		protected $cols;
		protected $sortablecols;
		
		
		// Class constructor 
		public function __construct($attrs, $cols, $sortablecols=false) {
			$this->curpaged=1;
			$this->items = false;
			$this->cols = $cols;
			$this->sortablecols = (($sortablecols === false)?array():$sortablecols);
			parent::__construct($attrs);
		}
		
		public function no_items() {
			__( 'No '.$this->_args['plural'].' Comments to show', 'wpri' );
		}
		
		function get_columns() {
			return $this->cols;
		}
		
		function get_sortable_columns() {
			return $this->sortablecols;
		}
		
		function prepare_items() {
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array($columns, $hidden, $sortable);
			if($this->items === false || !is_array($this->items))
				$this->items = $this->getListData();
		}
		
		/*
		 * ugly solution to get html instead of echoing it...
		 */
		public function return_html() {
			ob_start();
			$this->display();
			return ob_get_clean();
		}
		
		public function column_default( $item, $column_name ) {
			if(!empty($item[ $column_name ])) {
				return $item[ $column_name ];
			} else {
				return '<strong>'.__( 'empty', 'wpri' ).'</strong>';
			}
		}

		abstract public function getListData();
		

	}
?>
