<?php
/**
 * Store registration data
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'JBR_REGISTRATION_OLD_DATA' ) ) {

	final class JBR_REGISTRATION_OLD_DATA {

		public $table_name;

		public function __construct() {

			$this->table_name = 'jbr_users';

			$data = $this->get_old_data();
			$this->store_old_data($data);
		}


		public function get_old_data() {

			global $wpdb;

			$data = array();

			//Get usermeta by role
			$rows = $wpdb->get_results(
						"SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'wp_capabilities'", 'ARRAY_A'
					);

			foreach ($rows as $row) {

				$value = unserialize($row['meta_value']);
				$roles = array_keys($value);
				$role = $roles[0];

				if ( $role == 'iwj_employer' || $role == 'iwj_candidate') {

					$user_id = $row['user_id']; 

					//Get user data by ID
					$user_data = $wpdb->get_results(
						"SELECT user_registered FROM {$wpdb->users} WHERE ID = '$user_id'", 'ARRAY_A'
					);
					$user = $user_data[0];
					$timestamp = $user['user_registered'];
					$formatted_role = substr($role, 4);

					$data[] = array(
							'user_type' => $formatted_role,
							'user_id' => $user_id,
							'registration_date' => $timestamp
							);
				}
			}

			return $data;
		}


		public function store_old_data($data) {

			global $wpdb;

			foreach ($data as $value) {

				$user_id = $value['user_id'];
				$row = $wpdb->get_row(
							"SELECT * FROM {$wpdb->prefix}$this->table_name WHERE user_id = '$user_id'", 'ARRAY_A'
						);

				// Insert data if not present
				if ($row == NULL) {
					$wpdb->insert(
						$wpdb->prefix . $this->table_name,
						$value,
						array( '%s', '%d', '%s' )
					);
				}
			}
		}
	}
} ?>