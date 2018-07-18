<?php
/**
 * Store registration data
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'JBR_REGISTRATION_GET' ) ) {

	final class JBR_REGISTRATION_GET {

		public $table_name;
		public $date_range;

		public function __construct() {

			$this->table_name = 'jbr_searches';
		}


		//Get the data
		public function data() {

			$total = $this->candidtae_get();

			$month = array();
			foreach ($this->date_range as $key => $value) {
				$current_month = array();
				$current_month[$key] = $this->month_registration_get($value['start'], $value['end']);
				$month = array_merge($month, $current_month);
			}

			$data = array_merge($total, $month);
			return $data;
		}


		//Get total registration
		public function candidtae_get() {

			global $wpdb;
			$registration = $wpdb->get_results(
				"SELECT * FROM {$wpdb->prefix}$this->table_name", 'ARRAY_A'
			);

			$employer_total = array_count_values(
								array_filter(
									array_map(function($item) {
										if ( $item['user_type'] == 'employer' ) { return 'employer'; }
									}, $registration )));

			$candidate_total = array_count_values(
								array_filter(
									array_map(function($item) {
										if ( $item['user_type'] == 'candidate' ) { return 'candidate'; }
									}, $registration )));
			$total = array();
			$total['total'] = array_merge($employer_total, $candidate_total);
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
										if ( $item['user_type'] == 'employer' ) { return 'employer'; }
									}, $registration )));

			$candidate_month = array_count_values(
								array_filter(
									array_map(function($item) {
										if ( $item['user_type'] == 'candidate' ) { return 'candidate'; }
									}, $registration )));

			$month = array_merge($employer_month, $candidate_month);
			return $month;
		}
	}
} ?>