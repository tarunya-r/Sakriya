<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ivole_Email' ) ) :

if ( is_file( plugin_dir_path( __DIR__ ) . '/woocommerce/includes/libraries/class-emogrifier.php' ) ) {
	include_once( plugin_dir_path( __DIR__ ) . '/woocommerce/includes/libraries/class-emogrifier.php' );
} else {
	//backup for Emogrifier class that is missing in early versions of WooCommerce
	if ( is_file( plugin_dir_path( __FILE__ ) . '/dependencies/class-emogrifier.php' ) ) {
		include_once( plugin_dir_path( __FILE__ ) . '/dependencies/class-emogrifier.php' );
	}
}

require_once('class-ivole-email-footer.php');

/**
 * Reminder email for product reviews
 */
class Ivole_Email {

	public $id;
	public $to;
	public $heading;
	public $subject;
	public $template_html;
	public $template_items_html;
	public $from;
	public $from_name;
	public $bcc;
	public $replyto;
	public $language;
	public $footer;
	public $find = array();
	public $replace = array();
	public static $default_body = "Hi {customer_first_name},\n\nThank you for shopping with us!\n\nWe would love if you could help us and other customers by reviewing products that you recently purchased in order #{order_id}. It only takes a minute and it would really help others. Click the button below and leave your review!\n\nBest wishes,\nJohn Doe\nCEO of Sky Shop";
	public static $default_body_coupon = "Hi {customer_first_name},\n\nThank you for reviewing your order!\n\nAs a token of appreciation, we’d like to offer you a discount coupon for your next purchases in our shop. Please apply the following coupon code during checkout to receive {discount_amount} discount.\n\n<strong>{coupon_code}</strong>\n\nBest wishes,\nJohn Doe\nCEO of Sky Shop";
	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->id               = 'ivole_reminder';
		$this->heading          = strval( get_option( 'ivole_email_heading', __( 'How did we do?', 'ivole' ) ) );
		$this->subject          = strval( get_option( 'ivole_email_subject', '[{site_title}] ' . __( 'Review Your Experience with Us', 'ivole' ) ) );
		$this->form_header      = strval( get_option( 'ivole_form_header', __( 'How did we do?', 'ivole' ) ) );
		$this->form_body        = strval( get_option( 'ivole_form_body', __( 'Please review your experience with products and services that you purchased at {site_title}.', 'ivole' ) ) );
		$this->template_html    = Ivole_Email::plugin_path() . '/templates/email.php';
		$this->template_items_html    = Ivole_Email::plugin_path() . '/templates/email_items.php';
		$this->language					= get_option( 'ivole_language', 'EN' );
		$this->from_name				= get_option( 'ivole_email_from_name', Ivole_Email_Footer::get_from_name() );
		$this->footer						= get_option( 'ivole_email_footer', Ivole_Email_Footer::get_text() );

		$this->find['site-title'] = '{site_title}';
		$this->replace['site-title'] = Ivole_Email::get_blogname();

		//qTranslate integration
		if( function_exists( 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {
			$this->heading = qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage( $this->heading );
			$this->subject = qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage( $this->subject );
			$this->form_header = qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage( $this->form_header );
			$this->form_body = qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage( $this->form_body );
			$this->from_name = qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage( $this->from_name );
			$this->footer = qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage( $this->footer );
			if( 'QQ' === $this->language ) {
				global $q_config;
				$this->language = strtoupper( $q_config['language'] );
			}
		}
	}

	/**
	 * Trigger version 2.
	 */
	public function trigger2( $order_id, $to = null ) {
		$this->find['customer-first-name']  = '{customer_first_name}';
		$this->find['customer-name'] = '{customer_name}';
		$this->find['order-id'] = '{order_id}';
		$this->find['order-date'] = '{order_date}';
		$this->find['order-items'] = '{order_items}';
		$api_url = '';

		$this->from = get_option( 'ivole_email_from', 'feedback@cusrev.com' );

		// check if Reply-To address needs to be added to email
		$this->replyto = get_option( 'ivole_email_replyto', get_option( 'admin_email' ) );
		if( filter_var( $this->replyto, FILTER_VALIDATE_EMAIL ) ) {
			$this->replyto = $this->replyto;
		} else {
			$this->replyto = get_option( 'admin_email' );
		}

		$comment_required = get_option( 'ivole_form_comment_required', 'no' );
		if( 'no' === $comment_required ) {
			$comment_required = 0;
		} else {
			$comment_required = 1;
		}

		if ( $order_id ) {
			$order = new WC_Order( $order_id );
			// check if we are dealing with old WooCommerce version
			$customer_first_name = '';
			$customer_last_name = '';
			$order_date = '';
			$order_currency = '';
			$order_items = array();
			if( method_exists( $order, 'get_billing_email' ) ) {
				// Woocommerce version 3.0 or later
				$this->to = $order->get_billing_email();
				$this->replace['customer-first-name'] = $order->get_billing_first_name();
				$customer_first_name = $order->get_billing_first_name();
				$customer_last_name = $order->get_billing_last_name();
				$this->replace['customer-name'] = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
				$this->replace['order-id'] = $order_id;
				$this->replace['order-date']   = date_i18n( wc_date_format(), strtotime( $order->get_date_created() ) );
				$order_date = date_i18n( 'd.m.Y', strtotime( $order->get_date_created() ) );
				$order_currency = $order->get_currency();
			} else {
				// Woocommerce before version 3.0
				$this->to = get_post_meta( $order_id, '_billing_email', true );
				$this->replace['customer-first-name'] = get_post_meta( $order_id, '_billing_first_name', true );
				$customer_first_name = get_post_meta( $order_id, '_billing_first_name', true );
				$customer_last_name = get_post_meta( $order_id, '_billing_last_name', true );
				$this->replace['customer-name'] = get_post_meta( $order_id, '_billing_first_name', true ) . ' ' . get_post_meta( $order_id, '_billing_last_name', true );
				$this->replace['order-id'] = $order_id;
				$this->replace['order-date']   = date_i18n( wc_date_format(), strtotime( $order->order_date ) );
				$order_date = date_i18n( 'd.m.Y', strtotime( $order->order_date ) );
				$order_currency = $order->get_order_currency();
			}
			// check if BCC address needs to be added to email
			$bcc_address = get_option( 'ivole_email_bcc', '' );
			if( filter_var( $bcc_address, FILTER_VALIDATE_EMAIL ) ) {
				$this->bcc = $bcc_address;
			} else {
				$this->bcc = '';
			}

			$message = $this->get_content();
			$message = $this->replace_variables( $message );

			//save secret key for callback to DB
			$secret_key = bin2hex(openssl_random_pseudo_bytes(16));
			update_post_meta( $order_id, 'ivole_secret_key', $secret_key );

			$data = array(
				'token' => '164592f60fbf658711d47b2f55a1bbba',
				'shop' => array( "name" => Ivole_Email::get_blogname(),
			 		'domain' => Ivole_Email::get_blogurl(),
				 	'country' => apply_filters( 'woocommerce_get_base_location', get_option( 'woocommerce_default_country' ) ) ),
				'email' => array( 'to' => $this->to,
					'from' => $this->from,
					'fromText' => $this->from_name,
					'bcc' => $this->bcc,
					'replyTo' => $this->replyto,
			 		'subject' => $this->replace_variables( $this->subject ),
					'header' => $this->replace_variables( $this->heading ),
					'body' => $message,
				 	'footer' => $this->footer ),
				'customer' => array( 'firstname' => $customer_first_name,
					'lastname' => $customer_last_name ),
				'order' => array( 'id' => strval( $order_id ),
			 		'date' => $order_date,
					'currency' => $order_currency,
				 	'items' => Ivole_Email::get_order_items2( $order ) ),
				'callback' => array( //'url' => get_option( 'home' ) . '/wp-json/ivole/v1/review',
					'url' => get_rest_url( null, '/ivole/v1/review' ),
					'key' => $secret_key ),
				'form' => array('header' => $this->replace_variables( $this->form_header ),
					'description' => $this->replace_variables( $this->form_body ),
				 	'commentRequired' => $comment_required ),
				'colors' => array(
					'form' => array(
						'bg' => get_option( 'ivole_form_color_bg', '#0f9d58' ),
						'text' => get_option( 'ivole_form_color_text', '#ffffff' )
					),
					'email' => array(
						'bg' => get_option( 'ivole_email_color_bg', '#0f9d58' ),
						'text' => get_option( 'ivole_email_color_text', '#ffffff' )
					)
				),
				'language' => $this->language
			);
			$api_url = 'https://z4jhozi8lc.execute-api.us-east-1.amazonaws.com/v1/review-reminder';
		} else {
			// no order number means this is a test and we should provide some dummy information
			$this->replace['customer-first-name'] = __( 'Jane', 'ivole' );
			$this->replace['customer-name'] = __( 'Jane Doe', 'ivole' );
			$this->replace['order-id'] = 12345;
			$this->replace['order-date'] = date_i18n( wc_date_format(), time() );

			$message = $this->get_content();
			$message = $this->replace_variables( $message );

			$data = array(
				'token' => '164592f60fbf658711d47b2f55a1bbba',
				'shop' => array( "name" => Ivole_Email::get_blogname(),
			 		'domain' => Ivole_Email::get_blogurl() ),
				'email' => array( 'to' => $to,
					'from' => $this->from,
					'fromText' => $this->from_name,
					'replyTo' => $this->replyto,
			 		'subject' => $this->replace_variables( $this->subject ),
					'header' => $this->replace_variables( $this->heading ),
					'body' => $message,
				 	'footer' => $this->footer ),
				'form' => array( 'header' => $this->replace_variables( $this->form_header ),
					'description' => $this->replace_variables( $this->form_body ),
				 	'commentRequired' => $comment_required ),
				'colors' => array(
					'form' => array(
						'bg' => get_option( 'ivole_form_color_bg', '#0f9d58' ),
						'text' => get_option( 'ivole_form_color_text', '#ffffff' )
					),
					'email' => array(
						'bg' => get_option( 'ivole_email_color_bg', '#0f9d58' ),
						'text' => get_option( 'ivole_email_color_text', '#ffffff' )
					)
				),
				'language' => $this->language
			);
			$api_url = 'https://z4jhozi8lc.execute-api.us-east-1.amazonaws.com/v1/test-email';
		}
		$license = get_option( 'ivole_license_key', '' );
		if( strlen( $license ) > 0 ) {
			$data['licenseKey'] = $license;
		}
		$data_string = json_encode( $data );
		//error_log( $data_string );
		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, $api_url );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen( $data_string ) )
		);
		$result = curl_exec( $ch );
		if( false === $result ) {
			return array( 2, curl_error( $ch ) );
		}
		//error_log( $result );
		$result = json_decode( $result );
		if( isset( $result->status ) && $result->status === 'OK' ) {
			//update count of review reminders sent in order meta
			if( $order_id ) {
				$count = get_post_meta( $order_id, '_ivole_review_reminder', true );
				$new_count = 0;
				if( '' === $count ) {
					$new_count = 1;
				} else {
					$count = intval( $count );
					$new_count = $count + 1;
				}
				update_post_meta( $order_id, '_ivole_review_reminder', $new_count );
			}
			return 0;
		} else {
			return 1;
		}
	}

	/**
	 * Get content
	 *
	 * @access public
	 * @return string
	 */
	public function get_content() {
		ob_start();
		//$email_heading = $this->heading;
		$def_body = Ivole_Email::$default_body;
		include( $this->template_html );
		return ob_get_clean();
	}

	public static function plugin_path() {
    return untrailingslashit( plugin_dir_path( __FILE__ ) );
  }

	public function send() {

		add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		$subject = $this->replace_variables( $this->subject );
		$message = $this->get_content();
		$message = $this->replace_variables( $message );
		$message = $this->style_inline( $message );
		$headers = array();
		if($this->bcc) {
			$headers[] = 'Bcc: ' . $this->bcc;
		}
		$return  = wp_mail( $this->to, $subject, $message, $headers, array() );

		remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		return $return;
	}

	public function get_from_address() {
		$from_address = apply_filters( 'woocommerce_email_from_address', get_option( 'woocommerce_email_from_address' ), $this );
		return sanitize_email( $from_address );
	}

	public function get_from_name() {
		$from_name = apply_filters( 'woocommerce_email_from_name', get_option( 'woocommerce_email_from_name' ), $this );
		return wp_specialchars_decode( esc_html( $from_name ), ENT_QUOTES );
	}

	public function get_content_type() {
		return 'text/html';
	}

	public function style_inline( $content ) {
		// make sure we only inline CSS for html emails
		if ( in_array( $this->get_content_type(), array( 'text/html', 'multipart/alternative' ) ) && class_exists( 'DOMDocument' ) ) {
			ob_start();
			if ( is_file( plugin_dir_path( __DIR__ ) . '/woocommerce/templates/emails/email-styles.php' ) ) {
				wc_get_template( 'emails/email-styles.php' );
			}
			$css = apply_filters( 'woocommerce_email_styles', ob_get_clean() );

			// apply CSS styles inline for picky email clients
			try {
				$emogrifier = new Emogrifier( $content, $css );
				$content    = $emogrifier->emogrify();
			} catch ( Exception $e ) {
				$logger = new WC_Logger();
				$logger->add( 'emogrifier', $e->getMessage() );
			}
		}
		return $content;
	}

	public function replace_variables( $input ) {
		return str_replace( $this->find, $this->replace, __( $input ) );
	}

	public static function get_blogname() {
		return wp_specialchars_decode( get_option( 'ivole_shop_name', get_option( 'blogname' ) ), ENT_QUOTES );
	}

	public static function get_blogurl() {
		$temp = get_option( 'home' );
		$disallowed = array('http://', 'https://');
		foreach($disallowed as $d) {
      if(strpos($temp, $d) === 0) {
         return str_replace($d, '', $temp);
      }
   }
   return $temp;
	}

	// public function get_order_items( $ivole_test, $order = null ) {
	// 	$items = null;
	// 	$enabled_for = get_option( 'ivole_enable_for', 'all' );
	// 	$enabled_categories = get_option( 'ivole_enabled_categories', array() );
	// 	ob_start();
	// 	if ( $order ) {
	// 		$items = $order->get_items();
	// 	}
	// 	include( $this->template_items_html );
	// 	return ob_get_clean();
	// }

	public static function get_order_items2( $order ) {
		$items_return = array();
		$enabled_for = get_option( 'ivole_enable_for', 'all' );
		$enabled_categories = get_option( 'ivole_enabled_categories', array() );
		$items = $order->get_items();
		//error_log( 'items' );
		//error_log( print_r( $items, true) );
		foreach ( $items as $item_id => $item ) {
			// check if an item needs to be skipped because none of categories it belongs to has been enabled for reminders
			if( $enabled_for === 'categories' ) {
				$skip = true;
				$categories = get_the_terms( $item['product_id'], 'product_cat' );
				foreach ( $categories as $category_id => $category ) {
					if( in_array( $category->term_id, $enabled_categories ) ) {
						$skip = false;
						break;
					}
				}
				if( $skip ) {
					continue;
				}
			}
			if ( apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
				//create WC_Product to use its function for getting name of the product
				$prod_temp = new WC_Product( $item['product_id'] );
				$q_name = $prod_temp->get_title();

				//qTranslate integration
				if( function_exists( 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {
					$q_name = qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage( $q_name );
				}

				//check if we have several variations of the same product in our order
				//review requests should be sent only once per each product
				$same_product_exists = false;
				for($i = 0; $i < sizeof( $items_return ); $i++ ) {
					if( isset( $items_return[$i]['id'] ) && $item['product_id'] === $items_return[$i]['id'] ) {
						$same_product_exists = true;
						$items_return[$i]['price'] += $order->get_line_subtotal( $item );
					}
				}
				if( !$same_product_exists ) {
					$items_return[] = array( 'id' => $item['product_id'], 'name' => $q_name, 'price' => $order->get_line_subtotal( $item ) );
				}
			}
		}
		//error_log( 'items_return' );
		//error_log( print_r( $items_return, true) );
		return $items_return;
	}
}

endif;
