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

			$data = array_merge($total, array( 'month' => $month ));

			return $data;
		}


		//Get total registration
		public function total_registration_get($start, $end) {

			return array( 'total' => $this->fetch_data($start, $end) );
		}


		//Get monthly registration
		public function month_registration_get($start, $end) {

			return $this->fetch_data($start, $end);
		}


		// Fetch data from DB
		public function fetch_data($start, $end) {

			global $wpdb;
			$registration = $wpdb->get_results(
				"SELECT * FROM {$wpdb->prefix}$this->table_name 
				WHERE registration_date > '$start' and registration_date <= '$end'"
				, 'ARRAY_A'
			);
			if (count($registration) == 0) return;

			$employers = $this->get_value($registration, 'employer');
			$candidates = $this->get_value($registration, 'candidate');

			$employer_state = array();
			foreach ($employers as $state => $count) {
				$employer_state[$state] = $count;
			}

			$candidate_state = array();
			foreach ($candidates as $state => $count) {
				$candidate_state[$state] = $count;
			}

			$state_list = array('ACT','New South Wales', 'Northern Territory', 'Queensland', 'Victoria', 'Western Australia');

			$states = array();
			foreach ($state_list as $state) {
				$transient = array();
				if (array_key_exists($state, $employer_state)) {
					$transient['employer'] = $employer_state[$state]; 
				}
				if (array_key_exists($state, $candidate_state)) {
					$transient['candidate'] = $candidate_state[$state];
				}
				$states[$state] = $transient;
			}

			$states = array_filter($states);

			return $states;
		}


		//Format the array to get value
		public function get_value($array, $type) {

			$values = array_values(
						array_filter(
							array_map(function($item) use ($type) {
								if ( $item['user_type'] == $type ) { return maybe_unserialize($item['state']); }
						}, $array )));

			$output = array();
			foreach ($values as $value) {
				$output = array_merge($output, array_filter($value));
			}
			
			return array_count_values($output);
		}
	}
} ?>