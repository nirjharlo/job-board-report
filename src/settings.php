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

			$this->capability = 'manage_options';
			$this->subMenuPage = array(
									'name' => __( 'Job Board Report', 'gbr' ),
									'heading' => __( 'Job Board Report', 'gbr' ),
									'slug' => 'job-board-report'
								);

			add_action( 'admin_menu', array( $this, 'sub_menu_page' ) );
		}


		//Add a sample Submenu page callback
		public function sub_menu_page() {

			if ($this->subMenuPage) {
				$hook = add_options_page(
							$this->subMenuPage['name'],
							$this->subMenuPage['heading'],
							$this->capability,
							$this->subMenuPage['slug'],
							array( $this, 'menu_page_callback' )
						);
				}
			}


		// Menu page callback
		public function menu_page_callback() { ?>

			<div class="wrap">
				<h1><?php echo get_admin_page_title(); ?></h1>
				<br class="clear">
				<?php settings_errors(); ?>
				<?php $this->generate_report(); ?>
				<p><?php _e( 'Select a date range. The month(s) and year(s) are taken into account to generate report.', 'jbr' ); ?></p>
				<form method="post" action="">
					<input type="text" name="jbr-date-picker" value="" placeholder="<?php _e( 'Select Date Range', 'jbr' ); ?>" />
					<?php submit_button( __( 'Generate Report', 'jbr' ), 'primary', 'jbr-submit', false); ?>
				</form>
				<br class="clear">
			</div>
		<?php
		}


		public function generate_report() {

			$date_range = $this->date_range();
			if ($date_range != false && class_exists('JBR_REPORT')) {

				$report = new JBR_REPORT();
				$report->date_range = $date_range;
				$report->generate();
			}
		}


		//Format the date range
		public function date_range() {

			if (isset($_POST['jbr-date-picker'])) {

				$date_picker = $_POST['jbr-date-picker'];
				if (strpos($date_picker, ' - ') !== false) {

					$date_explode = explode(' - ', $date_picker);
					$start_string = $date_explode[0];
					$end_string = $date_explode[1];

					if (strtotime($start_string) != false) {
						$start = (new DateTime($start_string))->modify('first day of this month');
					}

					if (strtotime($end_string) != false) {
						$end = (new DateTime($end_string))->modify('last day of this month');
					}

					if ($start && $end) {
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
	}
} ?>