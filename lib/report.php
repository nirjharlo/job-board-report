<?php
/**
 * Generate pdf report
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'JBR_REPORT' ) ) {

	final class JBR_REPORT {

		public function __construct() {

		}


		public function report() {

			
		}


		public function registration_data() {

			$registration = new JBR_REGISTRATION_GET();
			$registration->start_date = '2018-07-04';
			$registration->end_date = '2018-08-30';
			$registration_data = $registration->data();
		}
	}
} ?>