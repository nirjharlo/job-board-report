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

			if ( ! isset( $_GET['page'] ) || $_GET['page'] != 'job-board-report' ) return;

			wp_register_script( 'jbr-moment', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', array('jquery') );
			wp_register_script( 'jbr-datepicker-js', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array('jquery', 'jbr-moment') );

			wp_enqueue_script( 'jbr-datepicker-js' );
			wp_enqueue_style( 'jbr-datepicker-css', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css' );
		}


		// Custom datepicker script
		public function datepicker_trigger() { ?>

			<script type="text/javascript">
				jQuery(function() {
					jQuery('input[name="jbr-date-picker"]').daterangepicker({
						autoUpdateInput: false,
						locale: {
							cancelLabel: 'Clear'
						}
					});
					jQuery('input[name="jbr-date-picker"]').on('apply.daterangepicker', function(ev, picker) {
						jQuery(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
					});
					jQuery('input[name="jbr-date-picker"]').on('cancel.daterangepicker', function(ev, picker) {
						jQuery(this).val('');
					});
				});
			</script>
			<?php
		}
	}
} ?>