<?php
/**
 * Store registration data
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'JBR_SEARCH' ) ) {

	final class JBR_SEARCH {

		public $table_name;

		public function __construct() {

			$this->table_name = 'jbr_searches';
			if ( is_page( 'candidates' ) ) {
				if( is_user_logged_in() ) {
					var_dump('As%Tu');
				}
			}
		}


		// Save user role in plugin table
		public function search_save() {

			if ( isset( $_GET['role'] ) ) {
				$role = $_GET['role'];
			}

				global $wpdb;
				$wpdb->insert(
						$wpdb->prefix . $this->table_name,
						array(
							'user_type' => $role,
							'user_id' => $user_id,
							'registration_date' => current_time('mysql')
						),
						array( '%s', '%d', '%s' )
				);
		}
	}
} ?>