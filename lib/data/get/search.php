<?php
/**
 * Store registration data
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'JBR_SEARCH_GET' ) ) {

	final class JBR_SEARCH_GET {

		public $table_name;
		public $date_range;

		public function __construct() {

			$this->table_name = 'jbr_searches';
		}


		//Get the data
		public function data() {

			$candidates = array();

			//Assign new varaibale to avoid change in array for latter use
			$date_range = $this->date_range;

			//Get start date
			$reverse_date_range = array_reverse($date_range);
			$start_date_string = array_pop($reverse_date_range);
			$start_date = $start_date_string['start'];

			//Get end date
			$end_date_string = array_pop($date_range);
			$end_date = $end_date_string['end'];

			$candidates['candidates'] = $this->candidates_get($start_date, $end_date);

			$search_type = array();
			foreach ($this->date_range as $key => $value) {
				$current_month = array();
				$current_month[$key] = $this->search_type_get($value['start'], $value['end']);
				$search_type = array_merge($search_type, $current_month);
			}
			$search['search_type'] = $search_type;

			$data = array_merge($candidates, $search);
			return $data;
		}


		//Get total registration
		public function candidates_get($start, $end) {

			$count = array();

			global $wpdb;
			$candidates = $wpdb->get_results(
				"SELECT search_type, candidate_count FROM {$wpdb->prefix}$this->table_name 
				WHERE search_date > '$start' and search_date <= '$end'", 'ARRAY_A'
			);
			$formatted = $this->format($candidates);
			if (count($formatted) == 0) return;

			$count['pharmacist'] = $this->candidates($formatted, 'pharmacist');
			$count['pharmacy-intern-and-student'] = $this->candidates($formatted, 'pharmacy-intern-and-student');
			$count['dispensary-assistant'] = $this->candidates($formatted, 'dispensary-assistant');
			$count['pharmacy-assistant'] = $this->candidates($formatted, 'pharmacy-assistant');
			$count['pharmacy-manager'] = $this->candidates($formatted, 'pharmacy-manager');
			$count['retail-manager'] = $this->candidates($formatted, 'retail-manager');

			$count['total'] = $this->get_average( array_filter(
											array_map(function($item) {
												return $item['candidate_count'];
											}, $formatted )));

			return $count;
		}


		//Get monthly registration
		public function search_type_get($start, $end) {

			$type = array();

			global $wpdb;
			$searches = $wpdb->get_results(
				"SELECT * FROM {$wpdb->prefix}$this->table_name 
				WHERE search_date > '$start' and search_date <= '$end'"
				, 'ARRAY_A'
			);
			$formatted = $this->format($searches);
			if (count($formatted) == 0) return;


			$type['pharmacist'] = $this->search_type($formatted, 'pharmacist');
			$type['pharmacy-intern-and-student'] = $this->search_type($formatted, 'pharmacy-intern-and-student');
			$type['dispensary-assistant'] = $this->search_type($formatted, 'dispensary-assistant');
			$type['pharmacy-assistant'] = $this->search_type($formatted, 'pharmacy-assistant');
			$type['pharmacy-manager'] = $this->search_type($formatted, 'pharmacy-manager');
			$type['retail-manager'] = $this->search_type($formatted, 'retail-manager');

			$type['total'] = array_sum( array_filter(
								array_map(function($item) {

									$type = $item['search_type'];
									if ( is_array($type) ){

										$arr = array('pharmacist', 'pharmacy-intern-and-student', 'dispensary-assistant', 'pharmacy-assistant', 'pharmacy-manager', 'retail-manager');
										foreach ($arr as $value) {
											if ( in_array($value, $type) ) {
												return 1;
											}
										}
									}
								}, $formatted )));

			return $type;
		}


		//Fetch sesarch type data
		public function search_type($array, $index) {

			$data = array_sum( array_filter(
						array_map(function($item) use ($index) {
							$type = $item['search_type'];
							if ( is_array($type) && in_array($index, $type) ) {
								return 1;
							}
					}, $array )));
			return $data;
		}


		//Fetch candidate data
		public function candidates($array, $index) {

			$data = array_filter(
				array_map(function($item) use ($index) {
					$type = $item['search_type'];
					if ( is_array($type) && in_array($index, $type) ) {
						return $item['candidate_count'];
					}
				}, $array ));
			return $this->get_average($data);
		}


		//Format data from DB
		public function format($candidates) {

			$formatted = array();
			if (is_array($candidates)) {
				$formatted = array_map(function($item) {
								return array(
										'search_type' => unserialize($item['search_type']),
										'candidate_count' => $item['candidate_count']
									);
							}, $candidates );
			}
			return $formatted;
		}


		//Avarage an array
		public function get_average($data) {

			if (count($data) == 0) {
				$average = 'ERROR';
			} else {
				$average = round( array_sum($data)/count($data) );
			}
			return $average;
		}
	}
} ?>