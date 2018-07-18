<?php
/**
 * Store registration data
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'JBR_SEARCH_AJAX_SAVE' ) ) {

	final class JBR_SEARCH_AJAX_SAVE {

		public $table_name;

		public function __construct() {

			$this->table_name = 'jbr_searches';

			add_action( 'wp_footer', array( $this, 'jbr_candidate_category_filter_js' ), 200 );
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

							var candidate_count = {};
							jQuery('.iwjob-list-iwj_cat .iwjob-filter-candidates-cbx').each(function(){
								var id_value = jQuery(this).val();
								var count = jQuery('#iwj-count-'+id_value).text();
								candidate_count[id_value] = count;
							});

							var search_type = new Array();
							var candidates = 0;
							jQuery('body').delegate('.iwjob-list-iwj_cat .iwjob-filter-candidates-cbx', 'change', function(e) {

									var id_value = jQuery(this).val();
									search_type.push(id_value);
									candidates += parseInt(candidate_count[id_value]);
console.log(candidates);
									jQuery(this).off();
									jQuery.post(
										<?php if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") { ?>
											'<?php echo admin_url("admin-ajax.php", "https"); ?>',
										<?php } else { ?>
											'<?php echo admin_url("admin-ajax.php"); ?>',
										<?php } ?>
										{ 'action': 'jbr_candidate_category_filter', 'search_type': search_type, 'candidates': candidates },
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
			$candidate_count = $_POST['candidates'];

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