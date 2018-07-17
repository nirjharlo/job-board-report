<?php
/**
 * Store registration data
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'JBR_REGISTRATION' ) ) {

	final class JBR_REGISTRATION {

		public $table_name;

		public function __construct() {

			$this->table_name = 'jbr_users';
			add_action( 'user_register', array( $this, 'registration_save' ), 50, 1 );
		}


		// Save user role in plugin table
		public function registration_save($user_id) {

			if ( isset( $_POST['role'] ) ) {

				$role = $_POST['role'];

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
	}
} ?>