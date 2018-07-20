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

			$this->table_name = 'jbr_users';
		}


		//Get the data
		public function data() {

			//Assign new varaibale to avoid change in array for latter use
			$date_range = $this->date_range;

			//Get start date
			$reverse_date_range = array_reverse($date_range);
			$start_date_string = array_pop($reverse_date_range);
			$start_date = $start_date_string['start'];

			//Get end date
			$end_date_string = array_pop($date_range);
			$end_date = $end_date_string['end'];

			$total = $this->total_registration_get($start_date, $end_date);

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
		public function total_registration_get($start, $end) {

			global $wpdb;
			$registration = $wpdb->get_results(
				"SELECT * FROM {$wpdb->prefix}$this->table_name 
				WHERE registration_date > '$start' and registration_date <= '$end'", 'ARRAY_A'
			);
			if (count($registration) == 0) return;

			$employer = $this->get_value($registration, 'employer');
			$candidate = $this->get_value($registration, 'candidate');

			$data = array_merge($employer, $candidate);

			return array( 'total' => $data );
		}


		//Get monthly registration
		public function month_registration_get($start, $end) {

			global $wpdb;
			$registration = $wpdb->get_results(
				"SELECT * FROM {$wpdb->prefix}$this->table_name 
				WHERE registration_date > '$start' and registration_date <= '$end'"
				, 'ARRAY_A'
			);
			if (count($registration) == 0) return;

			$employer_month = $this->get_value($registration, 'employer');
			$candidate_month = $this->get_value($registration, 'candidate');

			$month = array_merge($employer_month, $candidate_month);
			return $month;
		}


		//Format the array to get value
		public function get_value($array, $type) {

			$values = array_count_values(
							array_filter(
								array_map(function($item) use ($type) {
									if ( $item['user_type'] == $type ) { return $type; }
								}, $array )));
			return $values;
		}
	}
} ?>