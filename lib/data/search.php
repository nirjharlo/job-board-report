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

					$user = wp_get_current_user();
					$roles = (array) $user->roles;
					$role = $roles[0];

					if ($role == 'iwj_employer') {

						// Save search data for logged in employers
						$this->search_save();
					}
				}
			}
		}


		// Save user role in plugin table
		public function search_save() {

			if ( isset( $_GET['iwj_cat'] ) ) {

				$categories = $_GET['iwj_cat'];
				$candidate_count = 0;

				global $wpdb;
				$wpdb->insert(
						$wpdb->prefix . $this->table_name,
						array(
							'search_type' => maybe_serialize($categories),
							'candidate_count' => $candidate_count,
							'search_date' => current_time('mysql')
						),
						array( '%s', '%d', '%s' )
				);
			}
		}
	}
} ?>