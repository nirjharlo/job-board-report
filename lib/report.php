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

			$this->registration_data();
			$this->search_data();
		}


		public function registration_data() {

			$registration = new JBR_REGISTRATION_GET();
			$registration->date_range = $this->date_range;
			$registration_data = $registration->data();
		}


		public function search_data() {

			
		}
	}
} ?>