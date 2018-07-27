<?php
/**
 * Add scripts to the plugin. CSS and JS.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'JBR_SCRIPT' ) ) {

	final class JBR_SCRIPT {

		public function __construct() {

			add_action( 'admin_footer', array( $this, 'datepicker_trigger' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'backend_scripts' ) );
		}


		// Enter scripts into pages
		public function backend_scripts() {

			if ( ! isset( $_GET['page'] ) || $_GET['page'] != 'job-board-report' && $_GET['page'] != 'job-board-email' ) return;

			wp_register_script( 'jbr-bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js', array('jquery') );
			wp_register_script( 'jbr-moment', '//cdn.jsdelivr.net/momentjs/2.9.0/moment.min.js', array('jquery', 'jbr-bootstrap' ) );
			wp_register_script( 'jbr-datepicker-js', '//cdn.jsdelivr.net/bootstrap.daterangepicker/1/daterangepicker.js', array('jquery', 'jbr-bootstrap', 'jbr-moment') );

			wp_enqueue_script( 'jbr-datepicker-js' );
			wp_enqueue_style( 'jbr-bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css' );
			wp_enqueue_style( 'jbr-datepicker-css', '//cdn.jsdelivr.net/bootstrap.daterangepicker/1/daterangepicker-bs3.css' );
		}


		// Custom datepicker script
		public function datepicker_trigger() {

			if ( ! isset( $_GET['page'] ) || $_GET['page'] != 'job-board-report' && $_GET['page'] != 'job-board-email' ) return; ?>

			<script type="text/javascript">
				jQuery(function() {
					jQuery('input[name="jbr-date-picker-start"]').daterangepicker({
						'singleDatePicker': true,
						'showDropdowns': true,
						'minYear': 2017,
						'maxYear': <?php echo date('Y', strtotime('+1 year')); ?>,
						'autoUpdateInput': false,
						'autoApply': false,
					}).on('hide.daterangepicker', function (ev, picker) {
						var other = jQuery('input[name="jbr-date-picker-end"]').val();
						jQuery('.table-condensed tbody tr:nth-child(2) td').click();
						jQuery(this).val(picker.startDate.format('YYYY-MM'));
						jQuery('input[name="jbr-date-picker-end"]').val(other);
					});

					jQuery('input[name="jbr-date-picker-end"]').daterangepicker({
						'singleDatePicker': true,
						'showDropdowns': true,
						'minYear': 2017,
						'maxYear': <?php echo date('Y', strtotime('+1 year')); ?>,
						'autoUpdateInput': false,
						'autoApply': false,
					}).on('hide.daterangepicker', function (ev, picker) {
						var other = jQuery('input[name="jbr-date-picker-start"]').val();
						jQuery('.table-condensed tbody tr:nth-child(2) td').click();
						jQuery(this).val(picker.startDate.format('YYYY-MM'));
						jQuery('input[name="jbr-date-picker-start"]').val(other);
					});
				});
			</script>

			<style type="text/css">
				/**.drp-calendar .table-condensed thead tr:nth-child(2),
				.drp-calendar .table-condensed tbody {
					display: none
				}
				.drp-calendar .calendar-table {
					width: 244px;
				}*/
				.table-condensed thead tr:nth-child(2),
				.table-condensed tbody,
				.daterangepicker_start_input,
				.daterangepicker_end_input,
				.cancelBtn {
					display: none
				}
				.daterangepicker .calendar-date {
					width: 250px;
				}
				.daterangepicker .ranges {
					width: 60px;
					padding-top: 5px;
				}
				.range_inputs {
					width: 60px;
				}
			</style>
			<?php
		}
	}
} ?>