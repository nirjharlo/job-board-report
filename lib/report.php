<?php
/**
 * Generate pdf report
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'JBR_REPORT' ) ) {

	final class JBR_REPORT {

		public $date_range;

		public function generate() {

			$this->report();
		}


		public function report() {

			$this->prepare_Data();
		}


		//
		public function prepare_Data() {

			$registration = $this->registration_data();
			$members = $this->members_data();
			$search = $this->search_data();

			var_dump($registration);
			var_dump($search);
			var_dump($members);
		}


		//Gether registration data
		public function registration_data() {

			if (class_exists('JBR_REGISTRATION_GET')) {
				
				$registration = new JBR_REGISTRATION_GET();
				$registration->date_range = $this->date_range;
				$registration_data = $registration->data();

				return $registration_data;
			}
		}


		//Gather search data
		public function search_data() {

			if (class_exists('JBR_SEARCH_GET')) {

				$search = new JBR_SEARCH_GET();
				$search->date_range = $this->date_range;
				$search_data = $search->data();

				return $search_data;
			}
		}


		//Gather members data
		public function members_data() {

			if (class_exists('JBR_MEMBER_GET')) {
				$members = new JBR_MEMBER_GET();
				$members_data = $members->data();

				return $members_data;
			}
		}
	}
} ?>