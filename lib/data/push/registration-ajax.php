<?php
/**
 * Store registration data
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'JBR_REGISTRATION_STATE' ) ) {

	final class JBR_REGISTRATION_STATE {

		public $table_name;

		public function __construct() {

			$this->table_name = 'jbr_users';

			add_action( 'wp_footer', array( $this, 'jbr_registration_state_js' ), 250 );
			add_action( 'wp_ajax_jbr_registration_state', array( $this, 'jbr_registration_state' ) );
			add_action( 'wp_ajax_nopriv_jbr_registration_state', array( $this, 'jbr_registration_state' ) );
		}


		//The javascript
		public function jbr_registration_state_js() {

			if ( is_page('dashboard') && isset($_GET['iwj_tab']) && $_GET['iwj_tab'] == 'profile' ) {

				if( is_user_logged_in() ) {

					$user = wp_get_current_user();
					$ID = $user->ID; ?>

					<script type="text/javascript">
						jQuery(document).ready(function() {

							jQuery('body').delegate('.iwj-candidate-btn', 'click', function(event) {

								var state = jQuery('select[name="locations[]"]').next('.btn-group').children('button').attr('title');

								if (state != undefined && state != false) {

									jQuery(this).off();

									jQuery.post(
										<?php if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") { ?>
											'<?php echo admin_url("admin-ajax.php", "https"); ?>',
										<?php } else { ?>
											'<?php echo admin_url("admin-ajax.php"); ?>',
										<?php } ?>
										{ 'action': 'jbr_registration_state', 'ID': <?php echo $ID; ?>, 'state': state },
										function(response) {
											if ( response == 'ok' || response == 'ok0' ) {
												console.log('<?php _e( 'Location Saved', 'jbr'); ?>');
											}
										}
									);
								}
							});

							jQuery('body').delegate('.iwj-employer-btn', 'click', function(event) {

								var location = jQuery('input[name="_iwj_address"]').val().toLowerCase();

								if(location.indexOf('act') != -1) {
									state = 'ACT';
								}
								if(location.indexOf('new south wales') != -1) {
									state = 'New South Wales';
								}
								if(location.indexOf('northern territory') != -1) {
									state = 'Northern Territory';
								}
								if(location.indexOf('queensland') != -1) {
									state = 'Queensland';
								}
								if(location.indexOf('victoria') != -1) {
									state = 'victoria';
								}
								if(location.indexOf('western australia') != -1) {
									state = 'Western Australia';
								}

								if (state != undefined && state != false) {

									jQuery(this).off();

									jQuery.post(
										<?php if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") { ?>
											'<?php echo admin_url("admin-ajax.php", "https"); ?>',
										<?php } else { ?>
											'<?php echo admin_url("admin-ajax.php"); ?>',
										<?php } ?>
										{ 'action': 'jbr_registration_state', 'ID': <?php echo $ID; ?>, 'state': state },
										function(response) {
											if ( response == 'ok' || response == 'ok0' ) {
												console.log('<?php _e( 'Location Saved', 'jbr'); ?>');
											}
										}
									);
								}
							});
						});
					</script>
					<?php
				}
			}
		}


		public function jbr_registration_state() {

			$ID = $_POST['ID'];
			$state = trim($_POST['state']);

			global $wpdb;

			$update = $wpdb->update(
				$wpdb->prefix . $this->table_name,
					array(
						'state' => maybe_serialize( explode(', ', $state) ),
						'registration_date' => current_time('mysql')
					),
					array(
						'user_id' => $ID
					),
					array('%s', '%s'),
					array('%d')
			);

			if ($update) {
				echo 'ok';
			}

			wp_die();
		}
	}
} ?>