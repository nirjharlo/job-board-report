<?php
if ( ! defined( 'ABSPATH' ) ) exit;

//Main plugin object to define the plugin
if ( ! class_exists( 'JBR_BUILD' ) ) {
	
	final class JBR_BUILD {



		public function installation() {

			if (class_exists('JBR_INSTALL')) {

				$install = new JBR_INSTALL();
				$install->textDomin = 'jbr';
				$install->phpVerAllowed = '5.3';
				$install->pluginPageLinks = array(
												array(
													'slug' => admin_url( 'options-general.php?page=job-board-report' ),
													'label' => __( 'Generaate Report', 'jbr' )
												),
											);
				$install->execute();
			}
		}



		public function db_install() {

			// Add users DB table
			if ( class_exists( 'JBR_DB' ) ) {
				$db = new JBR_DB();
				$db->table = 'jbr_users';
				$db->sql = "ID mediumint(9) NOT NULL AUTO_INCREMENT,
							user_type VARCHAR(16) NOT NULL,
							registration_date DATE NOT NULL,
							UNIQUE KEY ID (ID)";
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
							search_type VARCHAR(32) NOT NULL,
							candidate_count SMALLINT(5) NOT NULL,
							search_date DATE NOT NULL,
							UNIQUE KEY ID (ID)";
				$db->build();
			}

			if (get_option('_jbr_db_exist') == '0') {
				add_action( 'admin_notices', array( $this, 'db_error_msg' ) );
			}
		}



		//Notice of DB
		public function db_error_msg() { ?>

			<div class="notice notice-error is-dismissible">
				<p><?php _e( 'Database table Not installed correctly.', 'textdomain' ); ?></p>
 			</div>
			<?php
		}



		public function db_uninstall() {

			$tableName = 'jbr_search';

			global $wpdb;
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



		//Include settings pages
		public function settings() {

			if ( class_exists( 'JBR_SETTINGS' ) ) new JBR_SETTINGS();
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
		}



		public function __construct() {

			$this->helpers();
			$this->functionality();

			register_activation_hook( JBR_FILE, array( $this, 'db_install' ) );

			//remove the DB upon uninstallation
			register_uninstall_hook( JBR_FILE, array( 'JBR_BUILD', 'db_uninstall' ) ); //$this won't work here.

			add_action('init', array($this, 'installation'));

			$this->scripts();

			$this->settings();
		}
	}
} ?>