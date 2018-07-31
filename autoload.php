<?php
if ( ! defined( 'ABSPATH' ) ) exit;

//Main plugin object to define the plugin
if ( ! class_exists( 'JBR_BUILD' ) ) {
	
	final class JBR_BUILD {



		public function installation() {

			if (class_exists('JBR_INSTALL')) {

				$install = new JBR_INSTALL();
				$install->textDomin = 'jbr';
				$install->phpVerAllowed = '5.4';
				$install->pluginPageLinks = array(
												array(
													'slug' => admin_url( 'options-general.php?page=job-board-report' ),
													'label' => __( 'Generate Report', 'jbr' )
												),
											);
				$install->execute();
			}
		}


		public function old_data_install() {

			if (class_exists('JBR_REGISTRATION_PAST_DATA')) new JBR_REGISTRATION_PAST_DATA();
		}


		public function db_install() {

			// Add users DB table
			if ( class_exists( 'JBR_DB' ) ) {
				$db = new JBR_DB();
				$db->table = 'jbr_users';
				$db->sql = "ID mediumint(9) NOT NULL AUTO_INCREMENT,
							user_type VARCHAR(16) NOT NULL,
							user_id SMALLINT(5) NOT NULL,
							registration_date DATETIME NOT NULL,
							UNIQUE KEY (ID, user_id)";
				$db->build();
			}

			if (get_option('_jbr_db_exist') == '0') {
				add_action( 'admin_notices', array( $this, 'db_error_msg' ) );
			}


			// Add search DB table
			if ( class_exists( 'JBR_DB' ) ) {
				$db = new JBR_DB();
				$db->table = 'jbr_searches';
				$db->sql = "ID mediumint(9) NOT NULL AUTO_INCREMENT,
							search_type VARCHAR(512) NOT NULL,
							candidate_count SMALLINT(5) NOT NULL,
							search_date DATETIME NOT NULL,
							UNIQUE KEY (ID)";
				$db->build();
			}

			if (get_option('_jbr_db_exist') == '0') {
				add_action( 'admin_notices', array( $this, 'db_error_msg' ) );
			}
		}



		//Notice of DB
		public function db_error_msg() { ?>

			<div class="notice notice-error is-dismissible">
				<p><?php _e( 'Database table Not installed correctly.', 'jbr' ); ?></p>
 			</div>
			<?php
		}



		public function db_uninstall() {

			global $wpdb;

			$tableName = 'jbr_users';
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}$tableName" );

			$tableName = 'jbr_searches';
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}$tableName" );

			$options = array(
								'_jbr_db_exist',

							);
			foreach ($options as $value) {
				delete_option($value);
			}
		}


		//Include scripts
		public function scripts() {

			if ( class_exists( 'JBR_SCRIPT' ) ) new JBR_SCRIPT();
		}


		//Include data in plugin's tables
		public function get_user_data() {

			if ( class_exists( 'JBR_REGISTRATION_SAVE' ) ) new JBR_REGISTRATION_SAVE();
		}


		//Include search data
		public function get_search_data() {

			if ( class_exists( 'JBR_SEARCH_SAVE' ) ) new JBR_SEARCH_SAVE();
		}


		//Include candidate filter AJAX search data
		public function get_search_data_ajax() {

			if ( class_exists( 'JBR_SEARCH_AJAX_SAVE' ) ) new JBR_SEARCH_AJAX_SAVE();
		}

		//Include settings pages
		public function settings() {

			if ( class_exists( 'JBR_SETTINGS' ) ) new JBR_SETTINGS();
		}


		//Add vendor files of fPdf
		public function vendor() {

			require_once ('vendor/fpdf181/fpdf.php');
		}


		//Add functionality files
		public function functionality() {

			require_once ('src/install.php');
			require_once ('src/db.php');
			require_once ('src/settings.php');
		}


		//Call the dependency files
		public function helpers() {

			require_once ('lib/script.php');
			require_once ('lib/pdf.php');
			require_once ('lib/report.php');

			require_once ('lib/data/push/search.php');
			require_once ('lib/data/push/search-ajax.php');
			require_once ('lib/data/push/registration.php');
			require_once ('lib/data/push/registration-old.php');

			require_once ('lib/data/get/registartion.php');
			require_once ('lib/data/get/members.php');
			require_once ('lib/data/get/search.php');
		}


		public function __construct() {

			//Get dependencies
			$this->vendor();
			$this->helpers();
			$this->functionality();

			//Plugin installation
			register_activation_hook( JBR_FILE, array( $this, 'db_install' ) );
			add_action('init', array($this, 'installation'));

			//remove the DB upon uninstallation
			register_uninstall_hook( JBR_FILE, array( 'JBR_BUILD', 'db_uninstall' ) ); //$this won't work here.

			//Store registration and search data
			register_activation_hook( JBR_FILE, array( $this, 'old_data_install' ) );
			$this->get_user_data();
			add_action('template_redirect', array($this, 'get_search_data'));
			$this->get_search_data_ajax();

			//Plugin admin side
			$this->scripts();
			$this->settings();
		}
	}
} ?>