<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Backend settings page class, can have settings fields or data table
 */
if ( ! class_exists( 'JBR_SETTINGS' ) ) {

	final class JBR_SETTINGS {

		public $capability;
		public $subMenuPage;


		// Add basic actions for menu and settings
		public function __construct() {

			if (isset($_POST['jbr-date-picker-start']) && isset($_POST['jbr-date-picker-end']) && isset($_POST['jbr-download-report'])) {
				add_action( 'init', array( $this, 'download_report' ) );
			}

			$this->capability = 'manage_options';
			$this->subMenuPage = array(
									array(
										'name' => __( 'Job Board Report', 'gbr' ),
										'heading' => __( 'Job Board Report', 'gbr' ),
										'slug' => 'job-board-report',
										'cb' => 'jbr_report_callback'
										),
									array(
										'name' => __( 'Job Board Email', 'gbr' ),
										'heading' => __( 'Job Board Email', 'gbr' ),
										'slug' => 'job-board-email',
										'cb' => 'jbr_email_callback'
										)
									);

			add_action( 'admin_menu', array( $this, 'sub_menu_page' ) );
		}


		//Action on report form submit
		public function download_report() {

			$date_range = $this->date_range();
			if ($date_range != false && class_exists('JBR_REPORT')) {

				$report = new JBR_REPORT();
				$report->execution = 'D';
				$report->date_range = $date_range;
				$file = $report->generate();
			}
		}


		//Action on email form submit
		public function email_report() {

			$emailed = false;
			$date_range = $this->date_range();
			$emails = $this->email_list();

			if ($date_range && $emails) {
				if ($date_range != false && class_exists('JBR_REPORT')) {

					$report = new JBR_REPORT();
					$report->execution = 'S';
					$report->date_range = $date_range;
					$file = $report->generate();

					foreach ($emails as $email) {
						$emailed = $this->send_email($email, $file);
					}
				}
			}

			if (false != $emailed) {
				$this->email_sent_msg($emailed);
			}
		}


		//Add a sample Submenu page callback
		public function sub_menu_page() {

			if ($this->subMenuPage) {
				foreach ($this->subMenuPage as $param) {
					$hook = add_options_page(
								$param['name'],
								$param['heading'],
								$this->capability,
								$param['slug'],
								array( $this, $param['cb'] )
							);
				}
			}
		}


		// Email Menu page callback
		public function jbr_email_callback() { ?>

			<div class="wrap">
				<h1><?php echo get_admin_page_title(); ?></h1>
				<br class="clear">
				<?php settings_errors(); ?>
				<?php $this->email_report(); ?>
				<p><?php _e( 'Select a date range. The month(s) and year(s) are taken into account to generate report.', 'jbr' ); ?></p>
				<form method="post" action="">
					<p>
						<input type="text" name="jbr-date-picker-start" value="" placeholder="<?php _e( 'Select Start Month', 'jbr' ); ?>" autocomplete="off" />
						<input type="text" name="jbr-date-picker-end" value="" placeholder="<?php _e( 'Select End Month', 'jbr' ); ?>" autocomplete="off" />
					</p>
					<input type="hidden" name="jbr-download-email" value="">
					<p><label for="jbr-email-list"><?php _e( 'Enter emails, one per line', 'jbr' ) ?></label><br>
						<textarea name="jbr-email-list" class="regular-text code" cols="200" rows="5"></textarea></p>
					<?php submit_button( __( 'Email Report', 'jbr' ), 'primary', 'jbr-submit', false); ?>
				</form>
				<br class="clear">
			</div>
		<?php
		}

		
		// Report Menu page callback
		public function jbr_report_callback() { ?>

			<div class="wrap">
				<h1><?php echo get_admin_page_title(); ?></h1>
				<br class="clear">
				<?php settings_errors(); ?>
				<p><?php _e( 'Select a date range. The month(s) and year(s) are taken into account to generate report.', 'jbr' ); ?></p>
				<form method="post" action="">
					<input type="text" name="jbr-date-picker-start" value="" placeholder="<?php _e( 'Select Start Month', 'jbr' ); ?>" autocomplete="off" />
					<input type="text" name="jbr-date-picker-end" value="" placeholder="<?php _e( 'Select End Month', 'jbr' ); ?>" autocomplete="off" />
					<input type="hidden" name="jbr-download-report" value="">
					<?php submit_button( __( 'Generate Report', 'jbr' ), 'primary', 'jbr-submit', false); ?>
				</form>
				<br class="clear">
			</div>
		<?php
		}


		public function date_range_order_error() { ?>

			<div class="notice notice-error is-dismissible">
				<p><?php echo __( 'End date should be greater than Start date.', 'jbr' ); ?></p>
 			</div>
			<?php
		}


		// Sent email success message
		public function email_sent_msg($emailed) { ?>

			<div class="notice notice-<?php echo ( $emailed ? 'success' : 'error' ); ?> is-dismissible">
				<p><?php echo ( $emailed ? __( 'Email sent successfully.', 'jbr' ) : __( 'Email couldn\'t be sent.', 'jbr' ) ); ?></p>
 			</div>
			<?php
		}


		//Prepare emails array
		public function email_list() {

			if (isset($_POST['jbr-email-list'])) {

				$emails_field = sanitize_textarea_field($_POST['jbr-email-list']);
				$emails = explode("\n", $emails_field);
				$email_list = array_values(
								array_filter(
									array_map(function($item) {
										if ( filter_var( $item, FILTER_SANITIZE_EMAIL ) ) {
											return $item;
										}
									}, $emails)));

				return $email_list;
			}
		}


		//Send email
		public function send_email($email, $file) {

			$to = $email; 
			$from = "me@example.com"; 
			$subject = __( 'Job Board Report', 'jbr');
			$message = '<p>'.__('Please check out the report.', 'jbr').'</p>';

			// a random hash will be necessary to send mixed content
			$separator = md5(time());
			$eol = PHP_EOL;
			$filename = "report.pdf";
			$attachment = chunk_split(base64_encode($file));

			// main header
			$headers  = "From: ".$from.$eol;
			$headers .= "MIME-Version: 1.0".$eol; 
			$headers .= "Content-Type: multipart/mixed; boundary=\"".$separator."\"";

			$body = "--".$separator.$eol;
			$body .= "Content-Transfer-Encoding: 7bit".$eol.$eol;
			$body .= "--".$separator.$eol;
			$body .= "Content-Type: text/html; charset=\"iso-8859-1\"".$eol;
			$body .= "Content-Transfer-Encoding: 8bit".$eol.$eol;
			$body .= $message.$eol;

			// attachment
			$body .= "--".$separator.$eol;
			$body .= "Content-Type: application/octet-stream; name=\"".$filename."\"".$eol; 
			$body .= "Content-Transfer-Encoding: base64".$eol;
			$body .= "Content-Disposition: attachment".$eol.$eol;
			$body .= $attachment.$eol;
			$body .= "--".$separator."--";

			// send message
			$emailed = @mail($to, $subject, $body, $headers);

			return $emailed;
		}


		//Format the date range
		public function date_range() {

			if (isset($_POST['jbr-date-picker-start']) && isset($_POST['jbr-date-picker-end'])) {

				$start_string = sanitize_text_field($_POST['jbr-date-picker-start']);
				$end_string = sanitize_text_field($_POST['jbr-date-picker-end']);

				$start_date = $start_string . '-01';
				$end_date = $end_string . '-28';

				if (strtotime($start_string) != false) {
					$start = (new DateTime($start_string))->modify('first day of this month');
				}

				if (strtotime($end_string) != false) {
					$end = (new DateTime($end_string))->modify('last day of this month');
				}

				if ($start && $end) {

					if ($end < $start) {
						$this->date_range_order_error();
						return;
					}

					$interval = DateInterval::createFromDateString('1 month');
					$period   = new DatePeriod($start, $interval, $end);

					$dates = array();
					foreach ($period as $dt) {

						$range = array();
						$start_range = $dt->format('Y-m-d');
						$range['start'] = $start_range;
						$end_range = (new DateTime($start_range))->modify('last day of this month');
						$range['end'] = $end_range->format('Y-m-d');
						$month = date('F-Y', strtotime($start_range));

						$dates[$month] = $range;
					}
					return $dates;
				}
			}
		}
	}
} ?>