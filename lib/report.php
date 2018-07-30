<?php
/**
 * Generate pdf report
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'JBR_REPORT' ) ) {

	final class JBR_REPORT {

		public $date_range;
		public $execution;

		public function generate() {

			$registration = $this->registration_data();
			$member = $this->members_data();
			$search = $this->search_data();

			$date_range = array_keys($this->date_range);

			$start = str_replace('-', ', ', $date_range[0]);
			$end = str_replace('-', ', ', $date_range[count($date_range)-1]);
			$date_count = count($date_range);
			$report_range = $start . __(' to ', 'gbr') . $end . ' ('.$date_count.' months)';


			$title = __('Pharmacist Locum and Team Recruitment Program Monthly Report Data', 'jbr');

			$registration_total_header = __('Total number of registrations', 'jbr');
			$registration_total_data = $registration['total'];

			$registration_month_header = __('Monthly number of registrations', 'jbr');
			$registration_month_data = $registration['month'];

			$member_header = __('Total Database Members, Candidates and Employers.', 'jbr');
			$member_data = $member;

			$search_header = __('Number of Employer Database Searches', 'jbr');
			$search_data = $search['search_type'];

			$candidate_header = __('Average Total number of candidates appearing in database search', 'jbr');
			$candidate_data = $search['candidates'];

			//http headers for Downloads
			ob_start();
			$pdf = new JBR_PDF();
			$pdf->SetFont('Arial','B',14);
			$pdf->AliasNbPages();
			$pdf->AddPage();
			$pdf->RegistrationTotalTable($registration_total_header,$registration_total_data,$report_range,$title);
			$pdf->Ln(10);
			$pdf->RegistrationMonthTable($registration_month_header,$registration_month_data);
			$pdf->Ln(10);
			$pdf->MemberTable($member_header,$member_data);
			$pdf->Ln(10);
			$pdf->SearchTable($search_header,$search_data);
			$pdf->Ln(10);
			$pdf->CandidateTable($candidate_header,$candidate_data);
/**

*/

			return $pdf->Output($this->execution, 'report.pdf', true);
			ob_clean();
			flush();
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