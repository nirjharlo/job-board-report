<?php
/**
 * Store registration data
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'JBR_SEARCH_AJAX' ) ) {

	final class JBR_SEARCH_AJAX {

		public $table_name;

		public function __construct() {

			$this->table_name = 'jbr_searches';

			add_action( 'wp_footer', array( $this, 'jbr_candidate_category_filter_js' ), 20 );
			add_action( 'wp_ajax_jbr_candidate_category_filter', array( $this, 'jbr_candidate_category_filter' ) );
			add_action( 'wp_ajax_nopriv_jbr_candidate_category_filter', array( $this, 'jbr_candidate_category_filter' ) );
		}


		//The javascript
		public function jbr_candidate_category_filter_js() { 

			if ( is_page( 'candidates' ) ) {

				if( is_user_logged_in() ) {

					$user = wp_get_current_user();
					$roles = (array) $user->roles;
					$role = $roles[0];

					if ($role == 'iwj_employer') {
					?>

					<script type="text/javascript">
						jQuery(document).ready(function() {
							var search_type = new Array();
							jQuery(".iwjob-list-iwj_cat li div .iwjob-filter-candidates-cbx").on('click', function() {

									search_type.push(jQuery(this).val());

									jQuery(this).off();
									jQuery.post(
										<?php if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") { ?>
											'<?php echo admin_url("admin-ajax.php", "https"); ?>',
										<?php } else { ?>
											'<?php echo admin_url("admin-ajax.php"); ?>',
										<?php } ?>
										{ 'action': 'jbr_candidate_category_filter', 'search_type': search_type },
										function(response) {
											if ( response == 'ok' || response == 'ok0' ) {
												console.log('<?php _e( 'Search Saved', 'jbr'); ?>');
											}
										}
									);
								});
						});
					</script>
					<?php
					}
				}
			}
		}


		public function jbr_candidate_category_filter() {

			$categories = $_POST['search_type'];
			$candidate_count = 0;

			global $wpdb;

			$terms = array();
			foreach ($categories as $value) {
				$term = $wpdb->get_results( "SELECT slug FROM {$wpdb->terms} WHERE term_id = '$value'", 'ARRAY_A' );
				$terms[] = $term[0]['slug'];
			}

			$save = $wpdb->insert(
				$wpdb->prefix . $this->table_name,
					array(
						'search_type' => maybe_serialize($terms),
						'candidate_count' => $candidate_count,
						'search_date' => current_time('mysql')
					),
					array( '%s', '%d', '%s' )
			);

			if ($save) {
				echo 'ok';
			}
			wp_die();
		}
	}
} ?>