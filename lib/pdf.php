<?php
/**
 * Generate pdf report
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'JBR_PDF' ) ) {

	final class JBR_PDF extends FPDF {

		// Page footer
		function Footer() {

			$this->SetY(-15);
			$this->SetFont('Arial','I',8);
			$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
		}


		// Total registration list table
		function RegistrationTotalTable($header,$data,$report_range,$title) {

			$this->SetFillColor(234,234,234);
			$this->SetTextColor(5,5,5);
			$this->SetLineWidth(.3);

			$this->Cell(90,14,$title,0,0,'L');
			$this->Ln(10);

			$this->SetFont('Arial','',12);
			$this->Cell(90,14,$report_range,0,0,'L');
			$this->Ln(20);

			$this->Cell(90,9,$header,0,0,'L');
			$this->Ln(10);

			$this->SetFont('Arial','',10);
			$heading = array( 'total', 'employer', 'candidate');

			$fill = true;
			$this->Cell(60,6,ucwords($heading[0]),0,0,'L',$fill);
			$this->Cell(60,6,ucwords($heading[1]),0,0,'C',$fill);
			$this->Cell(60,6,ucwords($heading[2]),0,0,'R',$fill);
			$this->Ln();
			$fill = false;
			$this->Cell(60,6,number_format($data[$heading[1]]+$data[$heading[2]]),0,0,'L',$fill);
			$this->Cell(60,6,number_format($data[$heading[1]]),0,0,'C',$fill);
			$this->Cell(60,6,number_format($data[$heading[2]]),0,0,'R',$fill);
			$this->Ln();
		}


		// Monthly registration list table
		function RegistrationMonthTable($header,$data) {

			$this->SetFillColor(234,234,234);
			$this->SetTextColor(5,5,5);
			$this->SetLineWidth(.3);
			$this->SetFont('Arial','',12);

			$this->Cell(90,9,$header,0,0,'L');
			$this->Ln(10);

			$this->SetFont('Arial','',10);
			$heading = array( 'total', 'employer', 'candidate');

			$fill = true;
			$this->Cell(45,6,__('Month','jbr'),0,0,'L',$fill);
			$this->Cell(45,6,ucwords($heading[0]),0,0,'C',$fill);
			$this->Cell(45,6,ucwords($heading[1]),0,0,'C',$fill);
			$this->Cell(45,6,ucwords($heading[2]),0,0,'R',$fill);
			$this->Ln();

			$fill = false;
			foreach($data as $month => $row) {

				if ($row != NULL && $row != false) {

					$this->Cell(45,6,str_replace('-', ', ', ($month)),0,0,'L',$fill);
					$this->Cell(45,6,number_format($row[$heading[1]]+$row[$heading[2]]),0,0,'C',$fill);
					$this->Cell(45,6,number_format($row[$heading[1]]),0,0,'C',$fill);
					$this->Cell(45,6,number_format($row[$heading[2]]),0,0,'R',$fill);
					$this->Ln();
					$fill = !$fill;
				}
			}
		}


		// Members list table
		function MemberTable($header,$data) {

			$this->SetFillColor(234,234,234);
			$this->SetTextColor(5,5,5);
			$this->SetLineWidth(.3);
			$this->SetFont('Arial','',12);

			$this->Cell(90,9,$header,0,0,'L');
			$this->Ln(10);

			$this->SetFont('Arial','',10);
			$heading = array( 'Category', 'Type', 'Members');

			$fill = true;
			$this->Cell(60,6,ucwords($heading[0]),0,0,'L',$fill);
			$this->Cell(60,6,ucwords($heading[1]),0,0,'L',$fill);
			$this->Cell(60,6,ucwords($heading[2]),0,0,'R',$fill);
			$this->Ln();

			$fill = false;
			foreach($data as $key => $value) {

				if ($key != 'pharmacist' && $key != 'pharmacy-intern-and-student') {

					$this->Cell(60,6,ucwords(str_replace('-', ' ', $key)),0,0,'L',$fill);
					$this->Cell(60,6,'',0,0,'L',$fill);
					$this->Cell(60,6,number_format($value),0,0,'R',$fill);

				} else {

					/**
					 * 
					 * Activate to show pure category data without type
					 * for 'iwj_cat' = pharmacist and pharmacy-intern-and-student
					 *
					 * Also create adequate fallback on JBR_MEMBER_GET
					 *
					$total_count = array_sum($value);
					$this->Cell(60,6,ucwords(str_replace('-', ' ', $key)),0,0,'L',$fill);
					$this->Cell(60,6,'',0,0,'L',$fill);
					$this->Cell(60,6,number_format($total_count),0,0,'R',$fill);
					$this->Ln();
					*/

					//Using $value[2] or $loop == 3 or array_slice($type_array, 1, 2) because of array format
					$type_array = $value[1];
					$loop = 1;
					//$fill = true;
					foreach ($type_array as $type => $count) {

						$this->Cell(60,6,ucwords(str_replace('-', ' ', $key)),0,0,'L',$fill);
						if ($type == 'or') {

							$types = array_keys($type_array);
							$new_type = ucwords(str_replace('-', ' ', $types[0])) . ' or ' . ucwords(str_replace('-', ' ', $types[1]));
							$this->Cell(60,6,$new_type,0,0,'L',$fill);

						} else {
							$this->Cell(60,6,ucwords(str_replace('-', ' ', $type)),0,0,'L',$fill);
						}
						$this->Cell(60,6,number_format($count),0,0,'R',$fill);

						if ( $loop != 3 ) $this->Ln();
						if ( $loop != 3 ) $fill = !$fill;
						$loop++;
					}
				}

				$this->Ln();
				$fill = !$fill;
			}
			$this->Ln();
		}


		// Search list table
		function SearchTable($header,$data) {

			$this->SetFillColor(234,234,234);
			$this->SetTextColor(5,5,5);
			$this->SetLineWidth(.3);
			$this->SetFont('Arial','',12);

			$this->Cell(90,9,$header,0,0,'L');
			$this->Ln(10);

			$this->SetFont('Arial','',10);
			foreach($data as $month => $row) {

				if ($row != NULL && $row != false) {

					$this->Cell(40,6,str_replace('-', ', ', ($month)),0,0,'L','true');
					$this->Ln(10);

					$fill = true;
					foreach ($row as $key => $value) {

						$this->Cell(90,6,ucwords(str_replace('-', ' ', $key)),0,0,'L',$fill);
						$this->Cell(90,6,number_format($value),0,0,'R',$fill);
						$this->Ln();
						$fill = !$fill;
					}
				}
			}
			$this->Ln();
		}


		// Candidates list table
		function CandidateTable($header,$data) {

			$this->SetFillColor(234,234,234);
			$this->SetTextColor(5,5,5);
			$this->SetLineWidth(.3);
			$this->SetFont('Arial','',12);

			$this->Cell(90,9,$header,0,0,'L');
			$this->Ln(10);

			$this->SetFont('Arial','',10);
			$fill = true;
			foreach($data as $key => $value) {

				$this->Cell(90,6,ucwords(str_replace('-', ' ', $key)),0,0,'L',$fill);
				$this->Cell(90,6,number_format($value),0,0,'R',$fill);
				$this->Ln();
				$fill = !$fill;
			}
			$this->Ln();
		}
	}
} ?>