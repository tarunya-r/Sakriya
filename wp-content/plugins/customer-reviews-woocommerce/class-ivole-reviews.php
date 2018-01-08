<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

if ( ! class_exists( 'Ivole_Reviews' ) ) :

	class Ivole_Reviews {

		private $limit_file_size = 5000000;
		private $limit_file_count = 3;
		private $ivrating = 'ivrating';

	  public function __construct() {
			if( 'yes' == get_option( 'ivole_attach_image', 'no' ) ) {
				add_action( 'woocommerce_product_review_comment_form_args', array( $this, 'custom_fields_attachment' ) );
				add_filter( 'wp_insert_comment', array( $this, 'save_review_image' ) );
				add_filter( 'comments_array', array( $this, 'display_review_image' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'ivole_style_1' ) );
			}
			if( 'yes' == get_option( 'ivole_enable_captcha', 'no' ) ) {
				add_action( 'woocommerce_product_review_comment_form_args', array( $this, 'custom_fields_captcha' ) );
				add_filter( 'preprocess_comment', array( $this, 'validate_captcha' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'ivole_style_1' ) );
				add_action( 'wp_enqueue_scripts', array( $this, 'ivole_style_2' ) );
			}
			if( 'yes' == get_option( 'ivole_reviews_histogram', 'no' ) ) {
				add_filter( 'comments_template', array( $this, 'load_custom_comments_template' ), 100 );
				add_action( 'ivole_reviews_summary', array( $this, 'show_summary_table' ) );
				add_action( 'init', array( $this, 'add_query_var' ), 20 );
				add_filter( 'comments_template_query_args', array( $this, 'filter_comments2' ), 20);
				add_action( 'wp_enqueue_scripts', array( $this, 'ivole_style_1' ) );
			}
	  }
		public function custom_fields_attachment( $comment_form ) {
			$post_id = get_the_ID();
			$comment_form['comment_field'] .= '<p><label for="comment_image_' . $post_id . '">';
			$comment_form['comment_field'] .= __( 'Upload up to ' . $this->limit_file_count .
				' images for your review (GIF, PNG, JPG, JPEG):', IVOLE_TEXT_DOMAIN );
			$comment_form['comment_field'] .= '</label><input type="file" multiple="multiple" name="review_image_' . $post_id . '[]" id="review_image" />';
			$comment_form['comment_field'] .= '</p>';
			return $comment_form;
		}
		public function custom_fields_captcha( $comment_form ) {
			$site_key = get_option( 'ivole_captcha_site_key', '' );
			$comment_form['comment_field'] .= '<div class="g-recaptcha ivole-recaptcha" data-sitekey="' . $site_key . '"></div>';
			return $comment_form;
		}
		public function save_review_image( $comment_id ) {
			//error_log("comment_id: " . print_r($comment_id, true));
			if( isset( $_POST['comment_post_ID'] ) ) {
				$post_id = $_POST['comment_post_ID'];
				//error_log("post_id: " . print_r($_POST['comment_post_ID'], true));
				$comment_image_id = 'review_image_' . $post_id;
				$nFiles = count( $_FILES[$comment_image_id]['name'] );
				if( $nFiles > 0 ) {
					if( $nFiles > $this->limit_file_count ) {
						echo __( "Error: You tried to upload too many files. The maximum number of files that you can upload is " .
							$this->limit_file_count . ".<br/> Go back to: ", 'ivole' );
						echo '<a href="' . get_permalink( $post_id ) . '">' . get_the_title( $post_id ) . '</a>';
						die;
					}
					for( $i = 0; $i < $nFiles; $i++ ) {
						//check file size
						if ( $this->limit_file_size < $_FILES[ $comment_image_id ]['size'][$i] ) {
							echo __( "Error: Uploaded file is too large. <br/> Go back to: ", 'ivole' );
							echo '<a href="' . get_permalink( $post_id ) . '">' . get_the_title( $post_id ) . '</a>';
							die;
						}
						// Get file extension
						$file_name_parts = explode( '.', $_FILES[ $comment_image_id ]['name'][$i] );
						$file_ext = $file_name_parts[ count( $file_name_parts ) - 1 ];

						if( $this->is_valid_file_type( $file_ext ) ) {
							$comment_image_file = wp_upload_bits( $comment_id . '.' . $file_ext, null, file_get_contents( $_FILES[ $comment_image_id ]['tmp_name'][$i] ) );
							$img_url = media_sideload_image( $comment_image_file['url'], $post_id );
							preg_match_all( "#[^<img src='](.*)[^'alt='' />]#", $img_url, $matches );
							$comment_image_file['url'] = $matches[0][0];
							if( FALSE === $comment_image_file['error'] ) {
								// Since we've already added the key for this, we'll just update it with the file.
								add_comment_meta( $comment_id, 'ivole_review_image', $comment_image_file );
							}
						}
					}
				}
			}
		}
		private function is_valid_file_type( $type ) {
			$type = strtolower( trim ( $type ) );
			return  $type == 'png' || $type == 'gif' || $type == 'jpg' || $type == 'jpeg';
		}
		public function display_review_image( $comments ) {
			if( count( $comments ) > 0 ) {
				foreach( $comments as $comment ) {
					$pics = get_comment_meta( $comment->comment_ID, 'ivole_review_image' );
					$pics_n = count( $pics );
					if( $pics_n > 0 ) {
						//check WooCommerce version because PhotoSwipe lightbox is only supported in version 3.0+
						$class_a = 'ivole-comment-a-old';
						if ( ( version_compare( WC()->version, "3.0", ">=" ) ) ) {
            	$class_a = 'ivole-comment-a';
    				}
						$comment->comment_content .= '<p class="iv-comment-image-text">' . __( 'Uploaded image(s):', 'ivole') . '</p>';
						$comment->comment_content .= '<div class="iv-comment-images">';
						for( $i = 0; $i < $pics_n; $i ++) {
							$comment->comment_content .= '<div class="iv-comment-image">';
							$comment->comment_content .= '<a href="' . $pics[$i]['url'] . '" class="' . $class_a . '"><img src="' .
								$pics[$i]['url'] . '" alt="' . sprintf( __( 'Image #%1$d from ', 'ivole' ), $i + 1 ) .
								$comment->comment_author . '" /></a>';
							$comment->comment_content .= '</div>';
						}
						$comment->comment_content .= '<div style="clear:both;"></div></div';
					}
				}
			}
			return $comments;
		}
		public function ivole_style_1() {
			if( is_product() ) {
				wp_register_style( 'ivole-frontend-css', plugins_url( '/css/frontend.css', __FILE__ ), array(), null, 'all' );
				wp_register_script( 'ivole-frontend-js', plugins_url( '/js/frontend.js', __FILE__ ), array(), null, true );
				wp_enqueue_style( 'ivole-frontend-css' );
				wp_enqueue_script( 'ivole-frontend-js' );
			}
		}
		public function ivole_style_2() {
			if( is_product() ) {
				wp_register_script( 'ivole-recaptcha', 'https://www.google.com/recaptcha/api.js', array(), null, true );
				wp_enqueue_script( 'ivole-recaptcha' );
			}
		}
		public function validate_captcha( $commentdata ) {
			if( is_admin() && current_user_can( 'edit_posts' ) ) {
				return $commentdata;
			}
			if( get_post_type( $commentdata['comment_post_ID'] ) === 'product' ) {
				if( !$this->ping_captcha() ) {
					wp_die( __( 'reCAPTCHA vertification failed and your review cannot be saved.', 'ivole' ), __( 'Add Review Error', 'ivole' ), array( 'back_link' => true ) );
				}
			}
			return $commentdata;
		}
		private function ping_captcha() {
			if( isset( $_POST['g-recaptcha-response'] ) ) {
				$secret_key = get_option( 'ivole_captcha_secret_key', '' );
				$response = json_decode(wp_remote_retrieve_body( wp_remote_get( "https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=" .$_POST['g-recaptcha-response'] ) ), true );
				if( $response["success"] )
				{
						return true;
				}
			}
			return false;
		}
		public function load_custom_comments_template( $template ) {
			if ( get_post_type() !== 'product' ) {
				return $template;
			}
			return wc_locate_template( 'ivole-single-product-reviews.php', '', plugin_dir_path ( __FILE__ ) . '/templates/' );
		}
		public function show_summary_table( $product_id ) {
			$all = $this->count_ratings( $product_id, 0 );
			$five = (float)$this->count_ratings( $product_id, 5 );
			$five_percent = floor( $five / $all * 100 );
			$five_rounding = $five / $all * 100 - $five_percent;
			$four = (float)$this->count_ratings( $product_id, 4 );
			$four_percent = floor( $four / $all * 100 );
			$four_rounding = $four / $all * 100 - $four_percent;
			$three = (float)$this->count_ratings( $product_id, 3 );
			$three_percent = floor( $three / $all * 100 );
			$three_rounding = $three / $all * 100 - $three_percent;
			$two = (float)$this->count_ratings( $product_id, 2 );
			$two_percent = floor( $two / $all * 100 );
			$two_rounding = $two / $all * 100 - $two_percent;
			$one = (float)$this->count_ratings( $product_id, 1 );
			$one_percent = floor( $one / $all * 100 );
			$one_rounding = $one / $all * 100 - $one_percent;
			$hundred = $five_percent + $four_percent + $three_percent + $two_percent + $one_percent;
			// if( $hundred < 100 ) {
			// 	$to_distribute = 100 - $hundred;
			// 	$roundings = array( '5' => $five_rounding, '4' => $four_rounding, '3' => $three_rounding, '2' => $two_rounding, '1' => $one_rounding );
			// 	arsort($roundings);
			// 	error_log( print_r( $roundings, true ) );
			// }
			$output = '';
			$output .= '<div class="ivole-summaryBox">';
			$output .= '<table id="ivole-histogramTable">';
			$output .= '<tbody>';
			$output .= '<tr class="ivole-histogramRow">';
			if( $five > 0 ) {
				$output .= '<td class="ivole-histogramCell1"><a href="' . esc_url( add_query_arg( $this->ivrating, 5 ), get_permalink( $product_id ) ) . '#tab-reviews" title="' . __( '5 star', IVOLE_TEXT_DOMAIN ) . '">' . __( '5 star', IVOLE_TEXT_DOMAIN ) . '</a></td>';
				$output .= '<td class="ivole-histogramCell2"><a href="' . esc_url( add_query_arg( $this->ivrating, 5 ), get_permalink( $product_id ) ) . '#tab-reviews"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $five_percent . '%"></div></div></a></td>';
				$output .= '<td class="ivole-histogramCell3"><a href="' . esc_url( add_query_arg( $this->ivrating, 5 ), get_permalink( $product_id ) ) . '#tab-reviews">' . (string)$five_percent . '%</a></td>';
			} else {
				$output .= '<td class="ivole-histogramCell1">' . __( '5 star', IVOLE_TEXT_DOMAIN ) . '</td>';
				$output .= '<td class="ivole-histogramCell2"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $five_percent . '%"></div></div></td>';
				$output .= '<td class="ivole-histogramCell3">' . (string)$five_percent . '%</td>';
			}
			$output .= '</tr>';
			$output .= '<tr class="ivole-histogramRow">';
			if( $four > 0 ) {
				$output .= '<td class="ivole-histogramCell1"><a href="' . esc_url( add_query_arg( $this->ivrating, 4 ), get_permalink( $product_id ) ) . '#tab-reviews" title="' . __( '4 star', IVOLE_TEXT_DOMAIN ) . '">' . __( '4 star', IVOLE_TEXT_DOMAIN ) . '</a></td>';
				$output .= '<td class="ivole-histogramCell2"><a href="' . esc_url( add_query_arg( $this->ivrating, 4 ), get_permalink( $product_id ) ) . '#tab-reviews"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $four_percent . '%"></div></div></a></td>';
				$output .= '<td class="ivole-histogramCell3"><a href="' . esc_url( add_query_arg( $this->ivrating, 4 ), get_permalink( $product_id ) ) . '#tab-reviews">' . (string)$four_percent . '%</a></td>';
			} else {
				$output .= '<td class="ivole-histogramCell1">' . __( '4 star', IVOLE_TEXT_DOMAIN ) . '</td>';
				$output .= '<td class="ivole-histogramCell2"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $four_percent . '%"></div></div></td>';
				$output .= '<td class="ivole-histogramCell3">' . (string)$four_percent . '%</td>';
			}
			$output .= '</tr>';
			$output .= '<tr class="ivole-histogramRow">';
			if( $three > 0 ) {
				$output .= '<td class="ivole-histogramCell1"><a href="' . esc_url( add_query_arg( $this->ivrating, 3 ), get_permalink( $product_id ) ) . '#tab-reviews" title="' . __( '3 star', IVOLE_TEXT_DOMAIN ) . '">' . __( '3 star', IVOLE_TEXT_DOMAIN ) . '</a></td>';
				$output .= '<td class="ivole-histogramCell2"><a href="' . esc_url( add_query_arg( $this->ivrating, 3 ), get_permalink( $product_id ) ) . '#tab-reviews"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $three_percent . '%"></div></div></a></td>';
				$output .= '<td class="ivole-histogramCell3"><a href="' . esc_url( add_query_arg( $this->ivrating, 3 ), get_permalink( $product_id ) ) . '#tab-reviews">' . (string)$three_percent . '%</a></td>';
			} else {
				$output .= '<td class="ivole-histogramCell1">' . __( '3 star', IVOLE_TEXT_DOMAIN ) . '</td>';
				$output .= '<td class="ivole-histogramCell2"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $three_percent . '%"></div></div></td>';
				$output .= '<td class="ivole-histogramCell3">' . (string)$three_percent . '%</td>';
			}
			$output .= '</tr>';
			$output .= '<tr class="ivole-histogramRow">';
			if( $two > 0 ) {
				$output .= '<td class="ivole-histogramCell1"><a href="' . esc_url( add_query_arg( $this->ivrating, 2 ), get_permalink( $product_id ) ) . '#tab-reviews" title="' . __( '2 star', IVOLE_TEXT_DOMAIN ) . '">' . __( '2 star', IVOLE_TEXT_DOMAIN ) . '</a></td>';
				$output .= '<td class="ivole-histogramCell2"><a href="' . esc_url( add_query_arg( $this->ivrating, 2 ), get_permalink( $product_id ) ) . '#tab-reviews"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $two_percent . '%"></div></div></a></td>';
				$output .= '<td class="ivole-histogramCell3"><a href="' . esc_url( add_query_arg( $this->ivrating, 2 ), get_permalink( $product_id ) ) . '#tab-reviews">' . (string)$two_percent . '%</a></td>';
			} else {
				$output .= '<td class="ivole-histogramCell1">' . __( '2 star', IVOLE_TEXT_DOMAIN ) . '</td>';
				$output .= '<td class="ivole-histogramCell2"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $two_percent . '%"></div></div></td>';
				$output .= '<td class="ivole-histogramCell3">' . (string)$two_percent . '%</td>';
			}
			$output .= '</tr>';
			$output .= '<tr class="ivole-histogramRow">';
			if( $one > 0 ) {
				$output .= '<td class="ivole-histogramCell1"><a href="' . esc_url( add_query_arg( $this->ivrating, 1 ), get_permalink( $product_id ) ) . '#tab-reviews" title="' . __( '1 star', IVOLE_TEXT_DOMAIN ) . '">' . __( '1 star', IVOLE_TEXT_DOMAIN ) . '</a></td>';
				$output .= '<td class="ivole-histogramCell2"><a href="' . esc_url( add_query_arg( $this->ivrating, 1 ), get_permalink( $product_id ) ) . '#tab-reviews"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $one_percent . '%"></div></div></a></td>';
				$output .= '<td class="ivole-histogramCell3"><a href="' . esc_url( add_query_arg( $this->ivrating, 1 ), get_permalink( $product_id ) ) . '#tab-reviews">' . (string)$one_percent . '%</a></td>';
			} else {
				$output .= '<td class="ivole-histogramCell1">' . __( '1 star', IVOLE_TEXT_DOMAIN ) . '</td>';
				$output .= '<td class="ivole-histogramCell2"><div class="ivole-meter"><div class="ivole-meter-bar" style="width: ' . $one_percent . '%"></div></div></td>';
				$output .= '<td class="ivole-histogramCell3">' . (string)$one_percent . '%</td>';
			}
			$output .= '</tr>';
			if( 'yes' !== get_option( 'ivole_reviews_nobranding', 'no' ) ) {
				$output .= '<tr class="ivole-histogramRow">';
				$output .= '<td colspan="3" class="ivole-credits">';
				$output .= 'Powered by <a href="https://wordpress.org/plugins/customer-reviews-woocommerce/" target="_blank">Customer Reviews Plugin</a>';
				$output .= '</td>';
				$output .= '</tr>';
			}
			$output .= '</tbody>';
			$output .= '</table>';
			if( get_query_var( $this->ivrating ) ) {
				$rating = intval( get_query_var( $this->ivrating ) );
				if( $rating > 0 && $rating <= 5 ) {
					$filtered_comments = sprintf( esc_html( _n( 'Showing %1$d of %2$d review (%3$d star). ', 'Showing %1$d of %2$d reviews (%3$d star). ', $all, 'ivole'  ) ), $this->count_ratings( $product_id, $rating ), $all, $rating );
					$all_comments = sprintf( esc_html( _n( 'See all %d review', 'See all %d reviews', $all, 'ivole'  ) ), $all );
					$output .= '<span>' . $filtered_comments . '</span><a class="ivole-seeAllReviews" href="' . esc_url( get_permalink( $product_id ) ) . '#tab-reviews">' . $all_comments . '</a>';
				}
			}
			$output .= '</div>';
			echo $output;
		}
		private function count_ratings( $product_id, $rating ) {
			$args = array(
				'post_id' => $product_id,
				'status' => 'approve',
				'count' => true
			);
			if( $rating > 0 ){
				$args['meta_query'][] = array(
					'key' => 'rating',
					'value'   => $rating,
					'compare' => '=',
					'type'    => 'numeric'
				);
			}
			return get_comments( $args );
		}
		public function add_query_var() {
			global $wp;
    	$wp->add_query_var( $this->ivrating );
		}
		public function filter_comments2( $comment_args ) {
			if( get_post_type() === 'product' ) {
				if( get_query_var( $this->ivrating ) ) {
					$rating = intval( get_query_var( $this->ivrating ) );
					if( $rating > 0 && $rating <= 5 ) {
						$comment_args['meta_query'][] = array(
							'key' => 'rating',
							'value'   => $rating,
							'compare' => '=',
							'type'    => 'numeric'
						);
					}
				}
			}
			return $comment_args;
		}
	}

endif;

?>
