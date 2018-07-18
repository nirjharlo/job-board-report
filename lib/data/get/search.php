<?php
/**
 * Store registration data
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'JBR_SEARCH_GET' ) ) {

	final class JBR_SEARCH_GET {

		public $table_name;
		public $start_date;
		public $end_date;

		public function __construct() {

			$this->table_name = 'jbr_users';
		}


		//Get the data
		public function data() {

			$total = $this->total_registration_get();
			$month = $this->month_registration_get($this->start_date, $this->end_date);

			$data = array_merge($total, $month);
			return $data;
		}


		//Get total registration
		public function total_registration_get() {

			global $wpdb;
			$registration = $wpdb->get_results(
				"SELECT * FROM {$wpdb->prefix}$this->table_name", 'ARRAY_A'
			);

			$employer_total = array_count_values(
								array_filter(
									array_map(function($item) {
										if ( $item['user_type'] == 'employer' ) { return 'employer_total'; }
									}, $registration )));

			$candidate_total = array_count_values(
								array_filter(
									array_map(function($item) {
										if ( $item['user_type'] == 'candidate' ) { return 'candidate_total'; }
									}, $registration )));

			$total = array_merge($employer_total, $candidate_total);
			return $total;
		}


		//Get monthly registration
		public function month_registration_get($start, $end) {

			global $wpdb;
			$registration = $wpdb->get_results(
				"SELECT * FROM {$wpdb->prefix}$this->table_name 
				WHERE registration_date > '$start' and registration_date <= '$end'"
				, 'ARRAY_A'
			);

			$employer_month = array_count_values(
								array_filter(
									array_map(function($item) {
										if ( $item['user_type'] == 'employer' ) { return 'employer_month'; }
									}, $registration )));

			$candidate_month = array_count_values(
								array_filter(
									array_map(function($item) {
										if ( $item['user_type'] == 'candidate' ) { return 'candidate_month'; }
									}, $registration )));

			$month = array_merge($employer_month, $candidate_month);
			return $month;
		}
	}
} ?>