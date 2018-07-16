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
				<p><?php _e( 'Select a date range of a month(at least 30 dyas) and click Generate Report', 'jbr' ); ?></p>
				<form method="post" action="">
					<input type="text" name="jbr-date-picker" value="" placeholder="<?php _e( 'Select Date Range', 'jbr' ); ?>" />
					<?php submit_button( __( 'Generate Report', 'jbr' ), 'primary', 'jbr-submit', false); ?>
				</form>
				<br class="clear">
			</div>
		<?php
		}


		public function generate_report() {

			if (class_exists('JBR_REPORT')) {
				new JBR_REPORT();
			}
		}
	}
} ?>