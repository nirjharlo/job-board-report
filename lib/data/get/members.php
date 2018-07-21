<?php
/**
 * Store registration data
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'JBR_MEMBER_GET' ) ) {

	final class JBR_MEMBER_GET {


		public function data() {

			$input = $this->general_input();
			$data = $this->prepare();

			$output = array();
			foreach ($input as $key) {
				if ($key != 'pharmacist' && $key != 'pharmacy-intern-and-student') {
					$output[$key] = array_sum(array_column($data, $key));
				} else {
					$output[$key] = array_column($data, $key);
				}
			}

			return $output;
		}


		//Data preparation
		public function prepare() {

			$data = array();
			$post_types = array( 'iwj_employer', 'iwj_candidate' );//'iwj_job', 'iwj_employer', 'iwj_candidate' );
			foreach ($post_types as $post_type) {
				if ($post_type == 'iwj_candidate') {
					$data[$post_type] = $this->candidate_data($post_type);
				} else {
					$data[$post_type] = $this->job_employer_data($post_type);
				}
			}

			return $data;
		}


		//Get job and employer data
		public function job_employer_data($post_type) {

			$input = $this->general_input();

			$data = array();
			foreach ($input as $cat) {
				$data[$cat] = $this->posts_get($post_type, $cat, false);
			}

			return $data;
		}


		//Get data by post type
		public function candidate_data($post_type) {

			$data = array();
			$input = $this->candidate_input();

			foreach ($input as $key => $value) {

				if (is_array($value)) {

					$type_array = array();
					foreach ($value as $type) {
				 		$type_array[$type] = $this->posts_get($post_type, $key, $type);
					}
		 			$type_array['or'] = $this->posts_get($post_type, $key, array( $value[0], $value[1] ));
					$output = $type_array;

				} else {

					$output = $this->posts_get($post_type, $key, false);
				}

				$data[$key] = $output;
			 }

			return $data;
		}


		//Get the input for job and employer post type
		public function general_input() {

			return array(
						'pharmacist',
						'pharmacy-intern-and-student',
						'dispensary-assistant',
						'pharmacy-assistant',
						'retail-manager',
						'pharmacy-manager'
					);
		}

		//Get the input for candidate post type
		public function candidate_input() {

			return array(
						'pharmacist' => array( 'locum', 'permanent-work' ),
						'pharmacy-intern-and-student' => array( 'placement-work', 'permanent-work' ),
						'dispensary-assistant' => 'all',
						'pharmacy-assistant' => 'all',
						'retail-manager' => 'all',
						'pharmacy-manager' => 'all'
					);
		}


		//Get monthly registration
		public function posts_get($post_type, $slug_term, $slug_type) {

			global $wp_query;
			$args = array(
							'post_type' => $post_type,
							'numberposts' => -1,
							'tax_query' => $this->tax_args($slug_term, $slug_type)
						);

			$candidates = new WP_Query($args);
			$count = $this->count_posts($candidates);
			wp_reset_query();

			return $count;
		}


		//Format the array to get value
		public function count_posts($data) {

			$values = $data->post_count;
			return $values;
		}


		//Build taxonomy args
		public function tax_args($slug_term, $slug_type) {

			$term_array = $this->term_array($slug_term);
			$type_array = $this->type_array($slug_type);

			if (false != $slug_term && $slug_type == false) {
				$tax_query = array($term_array);
			}
			if (false != $slug_term && false != $slug_type) {
				$tax_query = array('relation' => 'AND', $term_array, $type_array);
			}

			return $tax_query;
		}


		//Category taxonomy array
		public function term_array($slug_term) {

			return array(
						'taxonomy' => 'iwj_cat',
						'field' => 'slug',
						'terms' => $slug_term,
						'include_children' => false
					);
		}


		//Type taxonomy array
		public function type_array($slug_type) {

			if (is_array($slug_type)) {
				
				$types = array();
				foreach ($slug_type as $type) {
					$types[] = array(
									'taxonomy' => 'iwj_type',
									'field'    => 'slug',
									'terms'    => $type,
									'include_children' => false
								);
				}

				return array_merge( array('relation' => 'OR' ), $types );
	
			} else {

				return array(
						'taxonomy' => 'iwj_type',
						'field'    => 'slug',
						'terms'    => $slug_type,
						'include_children' => false
					);
			}
		}
	}
} ?>