<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Ivole_Admin' ) ) :

	require_once('class-ivole-email.php');
	require_once('class-ivole-license.php');
	require_once('class-ivole-email-verify.php');
	require_once('class-ivole-email-footer.php');
	require_once('class-ivole-milestones.php');

	global $woocommerce;

	class Ivole_Admin {

		private $milestones;

	  public function __construct() {
	    $this->id    = 'ivole';
			$this->label = __( 'Reviews', 'ivole' );
			$this->ver = 3.16;

			add_filter( 'woocommerce_settings_tabs_array', array( $this, 'add_settings_page' ), 30 );
			add_action( 'woocommerce_settings_' . $this->id, array( $this, 'output' ) );
			add_action( 'woocommerce_settings_save_' . $this->id, array( $this, 'save' ) );
			add_action( 'woocommerce_sections_' . $this->id, array( $this, 'output_sections' ) );
			add_action( 'woocommerce_admin_field_cselect', array( $this, 'show_cselect' ) );
			add_action( 'woocommerce_admin_settings_sanitize_option_ivole_enabled_categories', array( $this, 'save_cselect' ), 10, 3 );
			add_action( 'woocommerce_admin_field_htmltext', array( $this, 'show_htmltext' ) );
			add_action( 'woocommerce_admin_settings_sanitize_option_ivole_email_body', array( $this, 'save_htmltext' ), 10, 3 );
			add_action( 'woocommerce_admin_field_emailtest', array( $this, 'show_emailtest' ) );
			add_action( 'woocommerce_admin_field_license_status', array( $this, 'show_license_status' ) );
			add_action( 'woocommerce_admin_field_email_from', array( $this, 'show_email_from' ) );
			add_action( 'woocommerce_admin_field_email_from_name', array( $this, 'show_email_from_name' ) );
			add_action( 'woocommerce_admin_field_footertext', array( $this, 'show_footertext' ) );
			add_action( 'woocommerce_admin_field_nobranding', array( $this, 'show_nobranding_checkbox' ) );
			add_action( 'woocommerce_admin_settings_sanitize_option_ivole_email_from', array( $this, 'save_email_from' ), 10, 3 );
			add_action( 'woocommerce_admin_settings_sanitize_option_ivole_email_footer', array( $this, 'save_footertext' ), 10, 3 );
			add_action( 'woocommerce_admin_settings_sanitize_option_ivole_reviews_nobranding', array( $this, 'save_nobranding_checkbox' ), 10, 3 );
			add_action( 'admin_footer', array( $this, 'test_email_javascript' ) );
			add_action( 'wp_ajax_ivole_send_test_email', array( $this, 'send_test_email' ) );
			add_action( 'wp_ajax_ivole_dismiss_activated_notice', array( $this, 'dismiss_activated_notice' ) );
			add_action( 'wp_ajax_ivole_dismiss_updated_notice', array( $this, 'dismiss_updated_notice' ) );
			add_action( 'wp_ajax_ivole_check_license_ajax', array( $this, 'check_license_ajax' ) );
			add_action( 'wp_ajax_ivole_check_license_email_ajax', array( $this, 'check_license_email_ajax' ) );
			add_action( 'wp_ajax_ivole_verify_email_ajax', array( $this, 'ivole_verify_email_ajax' ) );
			add_action( 'wp_ajax_ivole_dismiss_reviews_notice_later', array( $this, 'dismiss_reviews_notice_later' ) );
			add_action( 'wp_ajax_ivole_dismiss_reviews_notice_never', array( $this, 'dismiss_reviews_notice_never' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_color_picker' ) );

			$no_notices = true;
			if( 1 == get_option( 'ivole_activation_notice', 0 ) ) {
				add_action( 'admin_notices', array( $this, 'admin_notice_install' ) );
				update_option( 'ivole_version', $this->ver );
				$no_notices = false;
			} else {
				$version = floatval( get_option( 'ivole_version', 0 ) );
				if( $version < 3.0 ) {
					add_action( 'admin_notices', array( $this, 'admin_notice_update' ) );
					$no_notices = false;
				}
			}

			$this->milestones = new Ivole_Milestones();
			//count reviews
			if( $no_notices && $this->milestones->show_notices() ) {
				add_action( 'admin_notices', array( $this, 'admin_notice_reviews' ) );
			}
	  }

	  	/**
		 * Add this page to settings.
		 */
		public function add_settings_page( $pages ) {
			$pages[ $this->id ] = $this->label;

			return $pages;
		}

	    /**
		 * Get settings array.
		 *
		 * @return array
		 */
		public function get_settings( $current_section = '' ) {
			if ( 'ivole_reviews' == $current_section ) {
				$settings = array(
					array(
		        		'title' => __( 'Extensions for Customer Reviews', 'ivole' ),
		        		'type' => 'title',
		        		'desc' => __( 'Settings for WooCommerce Customer Reviews plugin. Configure various extensions for standard WooCommerce reviews.', 'ivole' ),
		        		'id' => 'ivole_options'
		      		),
					array(
						'title'         => __( 'Attach Image', 'ivole' ),
						'desc'          => __( 'Enable attachment of images to reviews.', 'ivole' ),
						'id'            => 'ivole_attach_image',
						'default'       => 'no',
						'type'          => 'checkbox'
					),
					array(
						'title'         => __( 'reCAPTCHA V2 for Reviews', 'ivole' ),
						'desc'          => __( 'Enable reCAPTCHA to eliminate fake reviews. You must enter Site Key and Secret Key in the fields below if you want to use reCAPTCHA. You will receive Site Key and Secret Key after registration at reCAPTCHA website.', 'ivole' ),
						'id'            => 'ivole_enable_captcha',
						'default'       => 'no',
						'type'          => 'checkbox'
					),
					array(
		        'title' => __( 'reCAPTCHA V2 Site Key', 'ivole' ),
						'type' => 'text',
		        'desc' => __( 'If you want to use reCAPTCHA V2, insert here Site Key that you will receive after registration at reCAPTCHA website.', 'ivole' ),
						'default'  => '',
		        'id' => 'ivole_captcha_site_key',
						'css'      => 'min-width:400px;',
						'desc_tip' => true
		      ),
					array(
		        'title' => __( 'reCAPTCHA V2 Secret Key', 'ivole' ),
		        'type' => 'text',
		        'desc' => __( 'If you want to use reCAPTCHA V2, insert here Secret Key that you will receive after registration at reCAPTCHA website.', 'ivole' ),
						'default'  => '',
		        'id' => 'ivole_captcha_secret_key',
						'css'      => 'min-width:400px;',
						'desc_tip' => true
		      ),
					array(
						'title'         => __( 'Reviews Shortcode', 'ivole' ),
						'desc'          => __( 'Enable [cusrev_reviews] shortcode to display reviews at different locations on product pages. ' .
						'You can use this shortcode as [cusrev_reviews comment_file =”/comments.php”] or simply as [cusrev_reviews]. ' .
						'Here, \'comment_file\' is an optional argument. If you have a custom comment file, you should specify it here.', 'ivole' ),
						'id'            => 'ivole_reviews_shortcode',
						'default'       => 'no',
						'type'          => 'checkbox'
					),
					array(
						'title'         => __( 'Reviews Summary Bar', 'ivole' ),
						'desc'          => __( 'Enable display of a histogram table with a summary of reviews on a product page.', 'ivole' ),
						'id'            => 'ivole_reviews_histogram',
						'default'       => 'no',
						'type'          => 'checkbox'
					),
					array(
						'title'         => __( 'Remove Plugin\'s Branding', 'ivole' ),
						'desc'          => __( 'Enable this option to remove plugin\'s branding ("Powered by Customer Reviews Plugin") from the reviews summary bar.', 'ivole' ),
						'id'            => 'ivole_reviews_nobranding',
						'default'       => 'no',
						'type'          => 'nobranding'
					),
					array(
						'type' => 'sectionend',
						'id' => 'ivole_options'
						)
				);
			} else if( 'ivole_premium' == $current_section ) {
				$settings = array(
					array(
		        		'title' => __( 'Customer Reviews Premium', 'ivole' ),
		        		'type' => 'title',
		        		'desc' => __( 'You can unlock premium features of the Customer Reviews plugin by purchasing a license key => <a href="https://www.cusrev.com/" target="_blank">Buy License Key</a>', 'ivole' ),
		        		'id' => 'ivole_options'
		      		),
					array(
		        'title' => __( 'License Status', 'ivole' ),
						'type' => 'license_status',
		        'desc' => __( 'Information about license status.', 'ivole' ),
						'default'  => '',
		        'id' => 'ivole_license_status',
						'css'      => 'min-width:400px;',
						'desc_tip' => true
		      ),
					array(
		        'title' => __( 'License Key', 'ivole' ),
						'type' => 'text',
		        'desc' => __( 'Enter your license key here.', 'ivole' ),
						'default'  => '',
		        'id' => 'ivole_license_key',
						'css'      => 'min-width:400px;',
						'desc_tip' => true
		      ),
					array(
						'type' => 'sectionend',
						'id' => 'ivole_options'
						)
				);
			} else {
				$language_desc = __( 'Choose language that will be used for different elements of emails and review forms. If your language is not in the list, submit a translation by filling out <a href="https://goo.gl/forms/8D4poyIBRq2MtWEP2" target="_blank">this form</a>. Your translation will added with the next update of the plugin.', 'ivole' );
				$available_languages = array(
					'CS'  => __( 'Czech', 'ivole' ),
					'DA'  => __( 'Danish', 'ivole' ),
					'NL'	=> __( 'Dutch', 'ivole' ),
					'EN'  => __( 'English', 'ivole' ),
					'FI'  => __( 'Finnish', 'ivole' ),
					'FR'  => __( 'French', 'ivole' ),
					'DE'  => __( 'German', 'ivole' ),
					'HU'  => __( 'Hungarian', 'ivole' ),
					'ID'  => __( 'Indonesian', 'ivole' ),
					'IT'  => __( 'Italian', 'ivole' ),
					'PT'  => __( 'Portuguese', 'ivole' ),
					'RO'  => __( 'Romanian', 'ivole' ),
					'RU'  => __( 'Russian', 'ivole' ),
					'SR'  => __( 'Serbian', 'ivole' ),
					'SL'  => __( 'Slovenian', 'ivole' ),
					'ES'  => __( 'Spanish', 'ivole' ),
					'SV'  => __( 'Swedish', 'ivole' )
				);
				//qTranslate integration
				if( function_exists( 'qtranxf_useCurrentLanguageIfNotFoundUseDefaultLanguage' ) ) {
					$language_desc = $language_desc . ' ' . __( 'It looks like you have qTranslate-X plugin activated. You might want to choose "qTranslate-X Automatic" option to enable automatic selection of language.', 'ivole' );
					$available_languages = array( 'QQ' => __( 'qTranslate-X Automatic', 'ivole' ) ) + $available_languages;
				}
				$settings = array(
		      array(
        		'title' => __( 'Reminders for Customer Reviews', 'ivole' ),
        		'type' => 'title',
        		'desc' => __( 'Settings for WooCommerce Customer Reviews plugin. Configure WooCommerce to send automatic follow-up emails (reminders) that gather product reviews. By enabling and using this plugin, you agree to <a href="https://www.cusrev.com/terms.html" target="_blank">terms and conditions</a>.', 'ivole' ),
        		'id' => 'ivole_options'
		      ),
		      array(
						'title'         => __( 'Enable Automatic Reminders', 'ivole' ),
						'desc'          => __( 'Enable automatic follow-up emails with a reminder to submit a review.', 'ivole' ),
						'id'            => 'ivole_enable',
						'default'       => 'no',
						'type'          => 'checkbox'
					),
					array(
		        		'title' => __( 'Sending Delay (Days)', 'ivole' ),
		        		'type' => 'number',
		        		'desc' => __( 'Emails will be sent N days after order status is set to "Completed". N is a sending delay that needs to be defined in this field.', 'ivole' ),
						'default'  => 5,
		        		'id' => 'ivole_delay',
						'desc_tip' => true
		      		),
					array(
		        		'title' => __( 'Enable for', 'ivole' ),
		        		'type' => 'select',
		        		'desc' => __( 'Define if reminders will be send for all or only specific categories of products.', 'ivole' ),
						'default'  => 'all',
		        		'id' => 'ivole_enable_for',
						'desc_tip' => true,
						'class'    => 'wc-enhanced-select',
						'css'      => 'min-width:300px;',
						'options'  => array(
							'all'  => __( 'All Categories', 'ivole' ),
							'categories' => __( 'Specific Categories', 'ivole' )
							)
		      		),
					array(
		      	'title' => __( 'Categories', 'ivole' ),
		        'type' => 'cselect',
		        'desc' => __( 'If reminders are enabled only for specific categories of products, this field enables you to choose these categories.', 'ivole' ),
		        'id' => 'ivole_enabled_categories',
						'desc_tip' => true,
						'class'    => 'wc-enhanced-select',
						'css'      => 'min-width:300px;'
		      ),
					array(
						'title'         => __( 'Enable Manual Reminders', 'ivole' ),
						'desc'          => __( 'Enable manual sending of follow-up emails with a reminder to submit a review. Manual reminders can be sent for completed orders from <a href="' . admin_url( 'edit.php?post_type=shop_order' ) . '">Orders</a> page after enabling this option.', 'ivole' ),
						'id'            => 'ivole_enable_manual',
						'default'       => 'no',
						'type'          => 'checkbox'
					),
					array(
						'title'         => __( 'Moderation of Reviews', 'ivole' ),
						'desc'          => __( 'Enable manual moderation of reviews submitted by your verified customers. This setting applies only to reviews submitted in response to reminders sent by this plugin.', 'ivole' ),
						'id'            => 'ivole_enable_moderation',
						'default'       => 'no',
						'type'          => 'checkbox'
					),
					array(
		        'title' => __( 'Shop Name', 'ivole' ),
		        'type' => 'text',
		        'desc' => __( 'Specify your shop name that will be used in emails and review forms generated by this plugin.', 'ivole' ),
						'default'  => Ivole_Email::get_blogname(),
		        'id' => 'ivole_shop_name',
						'css'      => 'min-width:300px;',
						'desc_tip' => true
		      ),
					array(
		        'title' => __( 'From Address', 'ivole' ),
		        'type' => 'email_from',
		        'desc' => __( 'Emails will be sent from the email address specified in this field. Modification of this field is possible in premium version of the plugin.', 'ivole' ),
						'default'  => 'feedback@cusrev.com',
		        'id' => 'ivole_email_from',
						'css'      => 'min-width:300px;',
						'desc_tip' => true
		      ),
					array(
		        'title' => __( 'From Name', 'ivole' ),
		        'type' => 'email_from_name',
		        'desc' => __( 'Name that will be used together with From Address to send emails. Modification of this field is possible in premium version of the plugin.', 'ivole' ),
						'default'  => Ivole_Email_Footer::get_from_name(),
		        'id' => 'ivole_email_from_name',
						'css'      => 'min-width:300px;',
						'desc_tip' => true
		      ),
					array(
		        'title' => __( 'BCC Address', 'ivole' ),
		        'type' => 'text',
		        'desc' => __( 'Add a BCC recipient for emails with reminders. It can be useful to verify that emails are being sent properly.', 'ivole' ),
						'default'  => '',
		        'id' => 'ivole_email_bcc',
						'css'      => 'min-width:300px;',
						'desc_tip' => true
		      ),
					array(
		        'title' => __( 'Reply-To Address', 'ivole' ),
		        'type' => 'text',
		        'desc' => __( 'Add a Reply-To address for emails with reminders. If customers decide to reply to automatic emails, their replies will be sent to this address.', 'ivole' ),
						'default'  => get_option( 'admin_email' ),
		        'id' => 'ivole_email_replyto',
						'css'      => 'min-width:300px;',
						'desc_tip' => true
		      ),
					array(
						'type' => 'sectionend',
						'id' => 'ivole_options'
					),
					array(
		        		'title' => __( 'Language', 'ivole' ),
		        		'type' => 'title',
		        		'desc' => $language_desc,
		        		'id' => 'ivole_options_language'
		      		),
					array(
		        'title' => __( 'Language', 'ivole' ),
		        'type' => 'select',
		        'desc' => __( 'Choose one of the available languages.', 'ivole' ),
						'default'  => 'EN',
		        'id' => 'ivole_language',
						'class'    => 'wc-enhanced-select',
						'desc_tip' => true,
						'options'  => $available_languages
		      ),
					array(
						'type' => 'sectionend',
						'id' => 'ivole_options_language'
					),
					array(
		        		'title' => __( 'Email Template', 'ivole' ),
		        		'type' => 'title',
		        		'desc' => __( 'Adjust template of the email that will be sent to customers.', 'ivole' ),
		        		'id' => 'ivole_options_email'
		      		),
					array(
		        		'title' => __( 'Email Subject', 'ivole' ),
		        		'type' => 'text',
		        		'desc' => __( 'Subject of the email that will be sent to customers.', 'ivole' ),
						'default'  => '[{site_title}] Review Your Experience with Us',
		        		'id' => 'ivole_email_subject',
						'css'      => 'min-width:600px;',
						'desc_tip' => true
		      		),
					array(
		        		'title' => __( 'Email Heading', 'ivole' ),
		        		'type' => 'text',
		        		'desc' => __( 'Heading of the email that will be sent to customers.', 'ivole' ),
								'default'  => 'How did we do?',
		        		'id' => 'ivole_email_heading',
								'css'      => 'min-width:600px;',
								'desc_tip' => true
		      		),
					array(
		        'title' => __( 'Email Body', 'ivole' ),
		        'type' => 'htmltext',
		        'desc' => __( 'Body of the email that will be sent to customers.', 'ivole' ),
		        'id' => 'ivole_email_body',
						'desc_tip' => true
					),
					array(
		        'title' => __( 'Email Footer', 'ivole' ),
		        'type' => 'footertext',
		        'desc' => __( 'Footer of the email that will be sent to customers. Modification of this field is possible in premium version of the plugin.', 'ivole' ),
		        'id' => 'ivole_email_footer',
						'css' => 'min-width:600px;height:8em;',
						'desc_tip' => true
					),
					array(
						'title' => __( 'Email Color 1', 'ivole' ),
						'type' => 'text',
						'id' => 'ivole_email_color_bg',
						'default' => '#0f9d58',
						'desc' => __( 'Background color for heading of the email and review button.', 'ivole' ),
						'desc_tip' => true
					),
					array(
						'title' => __( 'Email Color 2', 'ivole' ),
						'type' => 'text',
						'id' => 'ivole_email_color_text',
						'default' => '#ffffff',
						'desc' => __( 'Text color for heading of the email and review button.', 'ivole' ),
						'desc_tip' => true
					),
					array(
						'type' => 'sectionend',
						'id' => 'ivole_options_email'
							),
					array(
		        		'title' => __( 'Review Form Template', 'ivole' ),
		        		'type' => 'title',
		        		'desc' => __( 'Adjust template of the review form that will be sent to customers.', 'ivole' ),
		        		'id' => 'ivole_options_form'
		      ),
					array(
		        		'title' => __( 'Form Header', 'ivole' ),
		        		'type' => 'text',
		        		'desc' => __( 'Header of the review form that will be sent to customers.', 'ivole' ),
								'default'  => 'How did we do?',
		        		'id' => 'ivole_form_header',
								'css'      => 'min-width:600px;',
								'desc_tip' => true
		      		),
					array(
								'title' => __( 'Form Body', 'ivole' ),
								'type' => 'textarea',
								'desc' => __( 'Body of the review form that will be sent to customers.', 'ivole' ),
								'default'  => 'Please review your experience with products and services that you purchased at {site_title}.',
								'id' => 'ivole_form_body',
								'css'      => 'min-width:600px;height:5em;',
								'desc_tip' => true
					),
					array(
						'title' => __( 'Comment Required', 'ivole' ),
						'type' => 'checkbox',
						'id' => 'ivole_form_comment_required',
						'default' => 'no',
						'desc' => __( 'Enable this option if you would like to make it mandatory for your customers to write something in their review.', 'ivole' )
					),
					array(
						'title' => __( 'Form Color 1', 'ivole' ),
						'type' => 'text',
						'id' => 'ivole_form_color_bg',
						'default' => '#0f9d58',
						'desc' => __( 'Background color for heading of the form and product names.', 'ivole' ),
						'desc_tip' => true
					),
					array(
						'title' => __( 'Form Color 2', 'ivole' ),
						'type' => 'text',
						'id' => 'ivole_form_color_text',
						'default' => '#ffffff',
						'desc' => __( 'Text color for product names.', 'ivole' ),
						'desc_tip' => true
					),
					array(
		        		'title' => __( 'Send Test', 'ivole' ),
		        		'type' => 'emailtest',
		        		'desc' => __( 'Send a test email to this address. You must save changes before sending a test email.', 'ivole' ),
								'default'  => '',
								'placeholder' => 'Email address',
		        		'id' => 'ivole_email_test',
								'css'      => 'min-width:300px;',
								'desc_tip' => true
		      		),
					array(
						'type' => 'sectionend',
						'id' => 'ivole_options_form'
					)
		    );
			}
	    return $settings;
	  }

	  	/**
		 * Output the settings.
		 */
		public function output() {
			global $current_section;
			update_option( 'ivole_activation_notice', 0 );
			if($current_section!='ivole_coupons') {
				$settings = $this->get_settings($current_section);
				WC_Admin_Settings::output_fields($settings);
			}
		}

	  	/**
		 * Save settings.
		 */
		public function save() {
			global $current_section;
			if( 'ivole_premium' == $current_section ) {
				// try to register license key
				$field_id = 'ivole_license_key';
				if( !empty( $_POST ) && isset( $_POST[$field_id] ) ) {
					//error_log( print_r( $_POST[$field_id], true ) );
					$license = new Ivole_License();
					$license->register_license( $_POST[$field_id] );
				}
			}
			if( $current_section != 'ivole_coupons' ) {
				$settings = $this->get_settings( $current_section );
				WC_Admin_Settings::save_fields( $settings );

				if( $current_section ) {
					do_action( 'woocommerce_update_options_' . $this->id . '_' . $current_section );
				}
			}
		}



		/**
		 * Custom field type for categories
		 */
		public function show_cselect($value) {
			//$tmp = WC_Admin_Settings::get_field_description($value);
			$tmp = Ivole_Admin::ivole_get_field_description($value);
			$tooltip_html = $tmp['tooltip_html'];
			$description = $tmp['description'];
			$args = array(
				'number'     => 0,
				'orderby'    => 'name',
				'order'      => 'ASC',
				'hide_empty' => false,
				'fields'		 => 'id=>name'
			);
			$categories = get_terms( 'product_cat', $args );
			$selections = (array) WC_Admin_Settings::get_option( $value['id'] );
			?><tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
					<?php echo $tooltip_html; ?>
				</th>
				<td class="forminp">
					<select multiple="multiple" name="<?php echo esc_attr( $value['id'] ); ?>[]" style="width:350px" data-placeholder="<?php esc_attr_e( 'Choose product categories&hellip;', 'ivole' ); ?>" aria-label="<?php esc_attr_e( 'Category', 'ivole' ) ?>" class="wc-enhanced-select">
						<option value="" selected="selected"></option>
						<?php
							if ( ! empty( $categories ) ) {
								foreach ( $categories as $key => $val ) {
									echo '<option value="' . esc_attr( $key ) . '" ' . selected( in_array( $key, $selections ), true, false ) . '>' . $val . '</option>';
								}
							}
						?>
					</select> <?php echo ( $description ) ? $description : ''; ?> <br />
					<a class="select_all button" href="#"><?php _e( 'Select all', 'ivole' ); ?></a>
					<!--<a class="select_none button" href="#"><?php _e( 'Select none', 'ivole' ); ?>--></a>
				</td>
			</tr><?php
		}

		/**
		 * Custom field type for categories
		 */
		public function save_cselect( $value, $option, $raw_value ) {
			if(is_array($value)){
				$value=array_filter($value,function($v){return $v!="";});
			}
			return $value;
		}

		/**
		 * Custom field type for body email
		 */
		public function show_htmltext($value) {
			//$tmp = WC_Admin_Settings::get_field_description($value);
			$tmp = Ivole_Admin::ivole_get_field_description($value);
			$tooltip_html = $tmp['tooltip_html'];
			$description = $tmp['description'];
			$default_text = Ivole_Email::$default_body;
			$body = wp_kses_post( WC_Admin_Settings::get_option( $value['id'], $default_text ) );
			$settings = array (
				'teeny' => true,
				'editor_css' => '<style>#wp-ivole_email_body-wrap {max-width: 700px !important;}</style>',
				'textarea_rows' => 20
			);
			?><tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
					<?php echo $tooltip_html; ?>
				</th>
				<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
					<?php echo $description; ?>
					<?php wp_editor( $body, 'ivole_email_body', $settings );
					echo '<div">';
					echo '<p style="font-weight:bold;margin-top:1.5em;font-size=1em;">' . __( 'Variables', 'ivole' ) . '</p>';
					echo '<p>' . __( 'You can use the following variables in the email and the review form:' ) . '</p>';
					echo '<p><strong>{site_title}</strong> - ' . __( 'The title of your WordPress website.' ) . '</p>';
					echo '<p><strong>{customer_first_name}</strong> - ' . __( 'The first name of the customer who purchased from your store.' ) . '</p>';
					echo '<p><strong>{customer_name}</strong> - ' . __( 'The full name of the customer who purchased from your store.' ) . '</p>';
					echo '<p><strong>{order_id}</strong> - ' . __( 'The order number for the purchase.' ) . '</p>';
					echo '<p><strong>{order_date}</strong> - ' . __( 'The date that the order was made.' ) . '</p>';
					echo '</div>';
					?>
				</td>
			</tr>
			<?php
		}

		/**
		 * Custom field type for saving body email
		 */
		public function save_htmltext( $value, $option, $raw_value ) {
			//error_log( print_r( $raw_value, true ) );
			//error_log( print_r( wp_kses_post( $raw_value ), true ) );
			return wp_kses_post( $raw_value );
		}

		/**
		 * Custom field type for email test
		 */
		public function show_emailtest($value) {
			//$tmp = WC_Admin_Settings::get_field_description($value);
			$tmp = Ivole_Admin::ivole_get_field_description($value);
			$tooltip_html = $tmp['tooltip_html'];
			$description = $tmp['description'];
			$coupon_class='';
			if($value['id']=='ivole_email_test_coupon') {
				$coupon_class=' coupon_mail';
			}

				?><tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
					<?php echo $tooltip_html; ?>
				</th>
				<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
					<input
						name="<?php echo esc_attr( $value['id'] ); ?>"
						id="<?php echo esc_attr( $value['id'] ); ?>"
						type="text"
						style="<?php echo esc_attr( $value['css'] ); ?>"
						class="<?php echo esc_attr( $value['class'] ); ?>"
						placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
						/> <?php echo $description; ?>
					<input
						type="button"
						id="ivole_test_email_button"
						value="Send Test"
						class="button-primary <?php echo $coupon_class; ?>"
						/>
					<p id="ivole_test_email_status" style="font-style:italic;visibility:hidden;"></p>
				</td>
			</tr>
			<?php
		}

		/**
		 * Custom field type for email footer text
		 */
		public function show_footertext($value) {
			//$tmp = WC_Admin_Settings::get_field_description($value);
			$tmp = Ivole_Admin::ivole_get_field_description($value);
			$tooltip_html = $tmp['tooltip_html'];
			$description = $tmp['description'];
			?><tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
					<?php echo $tooltip_html; ?>
				</th>
				<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
					<?php echo $description; ?>
					<textarea
						name="<?php echo esc_attr( $value['id'] ); ?>"
						id="<?php echo esc_attr( $value['id'] ); ?>"
						style="<?php echo esc_attr( $value['css'] ); ?>"
						class="<?php echo esc_attr( $value['class'] ); ?>"
						readonly
						></textarea>
						<p id="ivole_email_footer_status" style="font-style:italic;visibility:hidden;"></p>
				</td>
			</tr>
			<?php
		}

		/**
		 * Custom field type for email footer text save
		 */
		public function save_footertext( $value, $option, $raw_value ) {
			//error_log( print_r( $raw_value, true ) );
			//error_log( print_r( $value, true ) );
			return $raw_value;
		}

		/**
		 * Custom field type for license status
		 */
		public function show_license_status($value) {
			//$tmp = WC_Admin_Settings::get_field_description($value);
			$tmp = Ivole_Admin::ivole_get_field_description($value);
			$tooltip_html = $tmp['tooltip_html'];
			$description = $tmp['description'];

			?><tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
					<?php echo $tooltip_html; ?>
				</th>
				<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
					<input
						name="<?php echo esc_attr( $value['id'] ); ?>"
						id="<?php echo esc_attr( $value['id'] ); ?>"
						type="text"
						style="<?php echo esc_attr( $value['css'] ); ?>"
						class="<?php echo esc_attr( $value['class'] ); ?>"
						placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
						readonly /> <?php echo $description; ?>
					<p id="ivole_test_email_status" style="font-style:italic;visibility:hidden;">A</p>
				</td>
			</tr>
			<?php
		}

		/**
		 * Custom field type for from email
		 */
		public function show_email_from( $value ) {
			//$tmp = WC_Admin_Settings::get_field_description($value);
			$tmp = Ivole_Admin::ivole_get_field_description($value);
			$tooltip_html = $tmp['tooltip_html'];
			$description = $tmp['description'];

			?><tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
					<?php echo $tooltip_html; ?>
				</th>
				<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
					<input
						name="<?php echo esc_attr( $value['id'] ); ?>"
						id="<?php echo esc_attr( $value['id'] ); ?>"
						type="text"
						style="<?php echo esc_attr( $value['css'] ); ?>"
						class="<?php echo esc_attr( $value['class'] ); ?>"
						placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
						readonly /> <?php echo $description; ?>
					<span id="ivole_email_from_verify_status" style="display:inline-block;padding:2px;margin-left:10px;margin-right:10px;visibility:hidden;"></span>
					<input
						type="button"
						id="ivole_email_from_verify_button"
						value="Verify"
						class="button-primary"
						style="visibility:hidden;"
					/>
					<p id="ivole_email_from_status" style="font-style:italic;visibility:hidden;"></p>
				</td>
			</tr>
			<?php
		}

		/**
		 * Custom field type for from  name
		 */
		public function show_email_from_name( $value ) {
			//$tmp = WC_Admin_Settings::get_field_description($value);
			$tmp = Ivole_Admin::ivole_get_field_description($value);
			$tooltip_html = $tmp['tooltip_html'];
			$description = $tmp['description'];

			?><tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
					<?php echo $tooltip_html; ?>
				</th>
				<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ) ?>">
					<input
						name="<?php echo esc_attr( $value['id'] ); ?>"
						id="<?php echo esc_attr( $value['id'] ); ?>"
						type="text"
						style="<?php echo esc_attr( $value['css'] ); ?>"
						class="<?php echo esc_attr( $value['class'] ); ?>"
						placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
						readonly /> <?php echo $description; ?>
					<p id="ivole_email_from_name_status" style="font-style:italic;visibility:hidden;"></p>
				</td>
			</tr>
			<?php
		}

		/**
		 * Custom field type for body email save
		 */
		public function save_email_from( $value, $option, $raw_value ) {
			//error_log( print_r( $raw_value, true ) );
			//error_log( print_r( $value, true ) );
			if ( filter_var( $value, FILTER_VALIDATE_EMAIL ) ) {
				return $value;
			}
			return;
		}

		/**
		 * Custom field type for nobranding checkbox
		 */
		public function show_nobranding_checkbox( $value ) {
			//$tmp = WC_Admin_Settings::get_field_description($value);
			$tmp = Ivole_Admin::ivole_get_field_description($value);
			$description = $tmp['description'];
			$option_value = get_option( $value['id'], $value['default'] );

			//error_log( print_r( $value, true ) );

			?><tr valign="top">
				<th scope="row" class="titledesc">
					<?php echo esc_html( $value['title'] ); ?>
				</th>
				<td class="forminp forminp-checkbox">
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo esc_html( $value['title'] ) ?></span></legend>
						<label for="<?php echo $value['id'] ?>">
							<input
								name="<?php echo esc_attr( $value['id'] ); ?>"
								id="<?php echo esc_attr( $value['id'] ); ?>"
								type="checkbox"
								class="<?php echo esc_attr( isset( $value['class'] ) ? $value['class'] : '' ); ?>"
								value="1"
								disabled="disabled"
							/> <?php echo $description ?>
						</label>
						<p id="ivole_nobranding_status" style="font-style:italic;visibility:hidden;"></p>
					</fieldset>
				</td>
			</tr>
			<?php
		}

		/**
		 * Custom field type for nobranding checkbox save
		 */
		public function save_nobranding_checkbox( $value, $option, $raw_value ) {
			$value = '1' === $raw_value || 'yes' === $raw_value ? 'yes' : 'no';
			return $value;
		}

		/**
		 * Function to include JS with AJAX that is necessary for testing email and other things
		 */
		public function test_email_javascript() {
			?>
			<script type="text/javascript" >
			jQuery(document).ready(function($) {
				jQuery('#ivole_test_email_button').click(function(){
					var is_coupon='';
					var q_language = -1;
					if(jQuery(this).hasClass("coupon_mail")){
						is_coupon = '_coupon';
					}
					if (typeof qTranslateConfig !== 'undefined' && typeof qTranslateConfig.qtx !== 'undefined') {
						q_language = qTranslateConfig.qtx.getActiveLanguage();
					}
					if(is_coupon == "") {
						var data = {
							'action': 'ivole_send_test_email' + is_coupon,
							'email': jQuery('#ivole_email_test' + is_coupon).val(),
							'q_language': q_language
						};
					} else {
						var data = {
							'action': 'ivole_send_test_email' + is_coupon,
							'email': jQuery('#ivole_email_test' + is_coupon).val(),
							'coupon_type' : jQuery('#ivole_coupon_type').val(),
							'existing_coupon' : jQuery('#ivole_existing_coupon').val(),
							'discount_type': jQuery('#ivole_coupon__discount_type').val(),
							'discount_amount': jQuery('#ivole_coupon__coupon_amount').val(),
							'q_language': q_language
						};
					}
					jQuery('#ivole_test_email_status').text('Sending...');
					jQuery('#ivole_test_email_status').css('visibility', 'visible');
					jQuery('#ivole_test_email_button').prop('disabled', true);
					jQuery.post(ajaxurl, data, function(response) {
						jQuery('#ivole_test_email_status').css('visibility', 'visible');
						jQuery('#ivole_test_email_button').prop('disabled', false);
						if(response.code === 0) {
							jQuery('#ivole_test_email_status').text('Success: email has been successfully sent!');
						} else if (response.code === 1) {
							jQuery('#ivole_test_email_status').text('Error: email could not be sent, please check if your settings are correct and saved.');
						} else if (response.code === 2) {
							jQuery('#ivole_test_email_status').text('Error: cannot connect to the email server (' + response.message + ').');
						} else if (response.code === 97) {
							jQuery('#ivole_test_email_status').text('Error: "Shop Name" is empty. Please enter name of your shop in the corresponding field.');
						} else if (response.code === 99) {
							jQuery('#ivole_test_email_status').text('Error: please enter a valid email address!');
						} else {
							jQuery('#ivole_test_email_status').text('Error: unknown error!');
						}
					}, 'json');
		    });
				jQuery(document).on( 'click', '.ivole-activated .notice-dismiss', function() {
			    jQuery.ajax({
			        url: ajaxurl,
			        data: {
			            action: 'ivole_dismiss_activated_notice'
			        }
			    })
				});
				jQuery(document).on( 'click', '.ivole-updated .notice-dismiss', function() {
			    jQuery.ajax({
			        url: ajaxurl,
			        data: {
			            action: 'ivole_dismiss_updated_notice'
			        }
			    })
				});
				jQuery(document).on( 'click', '.ivole-reviews-milestone .notice-dismiss', function() {
			    jQuery.ajax({
			        url: ajaxurl,
			        data: {
			            action: 'ivole_dismiss_reviews_notice_later'
			        }
			    })
				});
				jQuery(document).on( 'click', '.ivole-reviews-milestone a.ivole-reviews-milestone-later', function() {
					var notice_container = jQuery('.notice.notice-info.is-dismissible.ivole-reviews-milestone');
					if(  notice_container.length > 0 ) {
						notice_container.remove();
					}
			    jQuery.ajax({
			        url: ajaxurl,
			        data: {
			            action: 'ivole_dismiss_reviews_notice_later'
			        }
			    })
					return false;
				});
				jQuery(document).on( 'click', '.ivole-reviews-milestone a.ivole-reviews-milestone-never', function() {
					var notice_container = jQuery('.notice.notice-info.is-dismissible.ivole-reviews-milestone');
					if(  notice_container.length > 0 ) {
						notice_container.remove();
					}
			    jQuery.ajax({
			        url: ajaxurl,
			        data: {
			            action: 'ivole_dismiss_reviews_notice_never'
			        }
			    })
					return false;
				});
				if( jQuery('#ivole_license_status').length > 0 ) {
					var data = {
						'action': 'ivole_check_license_ajax'
					};
					jQuery('#ivole_license_status').val( 'Checking...' );
					jQuery.post(ajaxurl, data, function(response) {
						jQuery('#ivole_license_status').val( response.message );
					});
				}
				// Load of Review Reminder page and check of From Email verification
				if( jQuery('#ivole_email_from').length > 0 ) {
					var data = {
						'action': 'ivole_check_license_email_ajax',
						'email': '<?php echo get_option( 'ivole_email_from', 'feedback@cusrev.com' ); ?>'
					};
					jQuery('#ivole_email_from').val( 'Checking license...' );
					jQuery('#ivole_email_from_name').val( 'Checking license...' );
					jQuery('#ivole_email_footer').val( 'Checking license...' );
					jQuery.post(ajaxurl, data, function(response) {
						jQuery('#ivole_email_footer_status').css('visibility', 'visible');
						if( 'Active' === response.license ) {
							jQuery('#ivole_email_from').val( '<?php echo get_option( 'ivole_email_from', 'feedback@cusrev.com' ); ?>' );
							jQuery('#ivole_email_from').prop( 'readonly', false );
							jQuery('#ivole_email_from_verify_status').css('visibility', 'visible');
							jQuery('#ivole_email_from_status').css('visibility', 'visible');
							jQuery('#ivole_email_from_name').val( <?php echo json_encode( get_option( 'ivole_email_from_name', Ivole_Email_Footer::get_from_name() ), JSON_HEX_APOS|JSON_HEX_QUOT ); ?> );
							jQuery('#ivole_email_from_name').prop( 'readonly', false );
							jQuery('#ivole_email_footer').val( <?php echo json_encode( get_option( 'ivole_email_footer', Ivole_Email_Footer::get_text() ), JSON_HEX_APOS|JSON_HEX_QUOT ); ?> );
							jQuery('#ivole_email_footer').prop( 'readonly', false );
							jQuery('#ivole_email_footer_status').text( 'While editing footer text please make sure to keep unsubscribe link markup: <a href="{{{unsubscribeLink}}}" style="color:#555555; text-decoration: underline; line-height: 12px; font-size: 10px;">unsubscribe</a>.' );
							if( 1 == response.email ){
								jQuery('#ivole_email_from_verify_status').css('background', '#00FF00');
								jQuery('#ivole_email_from_verify_status').text( 'Verified' );
							} else {
								jQuery('#ivole_email_from_verify_status').css('background', '#FA8072');
								jQuery('#ivole_email_from_verify_status').text( 'Unverified' );
								jQuery('#ivole_email_from_verify_button').css('visibility', 'visible');
								jQuery('#ivole_email_from_status').text( 'This email address is unverified. You must verify it to send emails.' );
							}
						} else {
							jQuery('#ivole_email_from').val( 'feedback@cusrev.com' );
							jQuery('#ivole_email_from_status').css('visibility', 'visible');
							jQuery('#ivole_email_from_status').html( 'This email address can be modified in the <a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=ivole&section=ivole_premium' ); ?>">premium version</a> of the plugin.' );
							jQuery('#ivole_email_from_name').val( <?php echo json_encode( Ivole_Email_Footer::get_from_name(), JSON_HEX_APOS|JSON_HEX_QUOT ); ?> );
							jQuery('#ivole_email_from_name_status').css('visibility', 'visible');
							jQuery('#ivole_email_from_name_status').html( 'This name can be modified in the <a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=ivole&section=ivole_premium' ); ?>">premium version</a> of the plugin.' );
							jQuery('#ivole_email_footer').val( <?php echo json_encode( Ivole_Email_Footer::get_text(), JSON_HEX_APOS|JSON_HEX_QUOT ); ?> );
							jQuery('#ivole_email_footer_status').html( 'This text can be modified in the <a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=ivole&section=ivole_premium' ); ?>">premium version</a> of the plugin.' );
						}
						// integration with qTranslate-X - add translation for elements that are loaded with a delay
						if (typeof qTranslateConfig !== 'undefined' && typeof qTranslateConfig.qtx !== 'undefined') {
							qTranslateConfig.qtx.addContentHook( document.getElementById( 'ivole_email_from_name' ), null, null );
							qTranslateConfig.qtx.addContentHook( document.getElementById( 'ivole_email_footer' ), null, null );
						}
					});
				}
				// Click on Verify From Email button
				jQuery('#ivole_email_from_verify_button').click(function(){
					var data = {
						'action': 'ivole_verify_email_ajax',
						'email': jQuery('#ivole_email_from').val()
					};
					jQuery('#ivole_email_from_verify_button').prop('disabled', true);
					jQuery('#ivole_email_from_status').text( 'Sending verification email...' );
					jQuery.post(ajaxurl, data, function(response) {
						if( 1 === response.verification ) {
							jQuery('#ivole_email_from_status').text( 'A verification email from Amazon Web Services has been sent to \'' + response.email + '\'. Please open the email and click the verification URL to confirm that you are the owner of this email address. After verification, reload this page to see updated status of verification.' );
							jQuery('#ivole_email_from_verify_button').css('visibility', 'hidden');
						} else if( 2 === response.verification ) {
							jQuery('#ivole_email_from_status').text( 'Verification error: ' + response.message + '.' );
							jQuery('#ivole_email_from_verify_button').prop('disabled', false);
						} else if( 3 === response.verification ) {
							jQuery('#ivole_email_from_status').text( 'Verification error: ' + response.message + '. Please refresh the page to see the updated verification status.' );
							jQuery('#ivole_email_from_verify_button').prop('disabled', false);
						} else if( 99 === response.verification ) {
							jQuery('#ivole_email_from_status').text( 'Verification error: please enter a valid email address.' );
							jQuery('#ivole_email_from_verify_button').prop('disabled', false);
						} else {
							jQuery('#ivole_email_from_status').text( 'Verification error.' );
							jQuery('#ivole_email_from_verify_button').prop('disabled', false);
						}
					});
				});
				// Load of Review Extensions page and check of license
				if( jQuery('#ivole_reviews_nobranding').length > 0 ) {
					var data = {
						'action': 'ivole_check_license_ajax'
					};
					jQuery('#ivole_nobranding_status').text('Checking license...');
					jQuery('#ivole_nobranding_status').css('visibility', 'visible');
					jQuery.post(ajaxurl, data, function(response) {
						if( 'Active' === response.message ) {
							jQuery('#ivole_reviews_nobranding').prop('disabled', false);
							jQuery('#ivole_reviews_nobranding').prop('checked', <?php echo 'yes' === get_option( 'ivole_reviews_nobranding', 'no' ) ? 'true' : 'false'; ?>);
							jQuery('#ivole_nobranding_status').css('visibility', 'hidden');
							//jQuery('#ivole_nobranding_status').text('');
						} else {
							jQuery('#ivole_nobranding_status').html( 'This checkbox can be modified in the <a href="<?php echo admin_url( 'admin.php?page=wc-settings&tab=ivole&section=ivole_premium' ); ?>">premium version</a> of the plugin.' );
						}
					});
				}
			});
			</script>
			<?php
		}

		/**
		 * Function that sends testing email
		 */
		public function send_test_email() {
			$email = strval( $_POST['email'] );
			$q_language = $_POST['q_language'];
			//integration with qTranslate
			if( $q_language >= 0 ) {
				global $q_config;
				$q_config['language'] = $q_language;
			}
			if( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
				$shop_name = Ivole_Email::get_blogname();
				// check that shop name field (blog name) is not empty
				if( strlen( $shop_name ) > 0 ) {
					$e = new Ivole_Email();
					$result = $e->trigger2( null, $email );
					if( is_array( $result ) && count( $result)  > 1 && 2 === $result[0] ) {
						wp_send_json( array( 'code' => 2, 'message' => $result[1] ) );
					} elseif( 0 === $result ) {
						wp_send_json( array( 'code' => 0, 'message' => '' ) );
					} elseif( 1 === $result ) {
						wp_send_json( array( 'code' => 1, 'message' => '' ) );
					}
				} else {
					wp_send_json( array( 'code' => 97, 'message' => '' ) );
				}
			} else {
				wp_send_json( array( 'code' => 99, 'message' => '' ) );
			}
			wp_send_json( array( 'code' => 98, 'message' => '' ) );
		}

		/**
		 * Function to dismiss activation notice in admin area
		 */
		public function dismiss_activated_notice() {
			update_option( 'ivole_activation_notice', 0 );
		}

		/**
		 * Function to dismiss update notice in admin area
		 */
		public function dismiss_updated_notice() {
			update_option( 'ivole_version', $this->ver );
		}

		/**
		 * Function to dismiss review milestone notice in admin area until the next milestone
		 */
		public function dismiss_reviews_notice_later() {
			$this->milestones->increase_milestone();
		}

		/**
		 * Function to dismiss review milestone notice in admin area forever
		 */
		public function dismiss_reviews_notice_never() {
			$this->milestones->milestone_never();
		}

		/**
		 * Function to check status of the license
		 */
		public function check_license_ajax() {
			$license = new Ivole_License();
			$lval = $license->check_license();
			wp_send_json( array( 'message' => $lval ) );
		}

		/**
		 * Function to check status of the license and verification of email
		 */
		public function check_license_email_ajax() {
			$license = new Ivole_License();
			$lval = $license->check_license();
			if( __( 'Active', 'ivole' ) == $lval ) {
				// the license is active, so check if current from email address is verified
				$verify = new Ivole_Email_Verify();
				$vval = $verify->is_verified();
				wp_send_json( array( 'license' => $lval, 'email' => $vval ) );
			} else {
				wp_send_json( array( 'license' => $lval, 'email' => 0 ) );
			}
		}

		/**
		 * Function to verify an email
		 */
		public function ivole_verify_email_ajax() {
			$email = strval( $_POST['email'] );
			$verify = new Ivole_Email_Verify();
			$vval = $verify->verify_email( $email );
			wp_send_json( array( 'verification' => $vval['res'], 'email' => $email, 'message' => $vval['message'] ) );
		}

		/**
		 * Function to show activation notice in admin area
		 */
		public function admin_notice_install() {
			if( current_user_can('manage_options') ) {
				$class = 'notice notice-info is-dismissible ivole-activated';
				$settings_url = admin_url( 'admin.php?page=wc-settings&tab=ivole' );
				$message = sprintf( __( '<strong>Customer Reviews for WooCommerce</strong> plugin has been activated. Please go to <a href="%s">WooCommerce settings</a> and configure this plugin to start receiving more authentic reviews!', 'ivole' ), $settings_url );
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
			}
		}

		/**
		 * Function to show activation notice in admin area
		 */
		public function admin_notice_update() {
			if( current_user_can('manage_options') ) {
				$class = 'notice notice-info is-dismissible ivole-updated';
				$settings_url = admin_url( 'admin.php?page=wc-settings&tab=ivole' );
				$message = sprintf( __( '<strong>Customer Reviews for WooCommerce</strong> plugin has been updated. This is a big update that makes submission of reviews easier and quicker for your customers. It means that you will receive more customer reviews but first we recommend you to verify <a href="%s">plugin settings</a> by sending several test emails to make sure that everything works fine.', 'ivole' ), $settings_url );
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
			}
		}

		/**
		 * Function to show milestone in collected reviews notice in admin area
		 */
		public function admin_notice_reviews() {
			if( current_user_can('manage_options') ) {
				$reviews_count = $this->milestones->count_reviews();
				$class = 'notice notice-info is-dismissible ivole-reviews-milestone';
				$message = sprintf( '<p style="font-weight:bold;color:#008000">Hey, I noticed you have collected %d reviews with "Customer Reviews for WooCommerce" – that’s awesome! Could you please do me a BIG favor and give it a 5-star rating on WordPress? Just to help us spread the word and boost our motivation.<br><span style="font-style:italic;">~ John Brown</span></p><ul style="list-style-type:disc;list-style-position:inside;font-weight:bold;"><li><a href="https://wordpress.org/support/plugin/customer-reviews-woocommerce/reviews/#new-post" target="_blank">OK, you deserve it</a></li><li><a href="#" class="ivole-reviews-milestone-later">Nope, maybe later</a></li><li><a href="#" class="ivole-reviews-milestone-never">I already did</a></li></ul>', $reviews_count );
				printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), $message );
			}
		}

		public function get_sections() {
		    $sections = array(
		        '' => __( 'Review Reminder', 'ivole' ),
		        'ivole_reviews' => __( 'Review Extensions', 'ivole' ),
						'ivole_coupons' => __( 'Review for Discount', 'ivole' ),
						'ivole_premium' => __( '&#9733; Premium &#9733;', 'ivole' )
		    );
		    return $sections;
		}

		public function output_sections() {
        global $current_section;

        $sections = $this->get_sections();

        if ( empty( $sections ) || 1 === sizeof( $sections ) ) {
            return;
        }

        echo '<ul class="subsubsub">';

        $array_keys = array_keys( $sections );

        foreach ( $sections as $id => $label ) {
            echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . $this->id . '&section=' . sanitize_title( $id ) ) . '" class="' . ( $current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
        }

        echo '</ul><br class="clear" />';
    }

		public static function ivole_get_field_description( $value ) {
			// a copy of WC_Admin_Settings::get_field_description() because this function is not included with early versions of WooCommerce
			$description  = '';
      $tooltip_html = '';

      if ( true === $value['desc_tip'] ) {
          $tooltip_html = $value['desc'];
      } elseif ( ! empty( $value['desc_tip'] ) ) {
          $description  = $value['desc'];
          $tooltip_html = $value['desc_tip'];
      } elseif ( ! empty( $value['desc'] ) ) {
          $description  = $value['desc'];
      }

      if ( $description && in_array( $value['type'], array( 'textarea', 'radio' ) ) ) {
          $description = '<p style="margin-top:0">' . wp_kses_post( $description ) . '</p>';
      } elseif ( $description && in_array( $value['type'], array( 'checkbox', 'nobranding' ) ) ) {
          $description = wp_kses_post( $description );
      } elseif ( $description ) {
          $description = '<span class="description">' . wp_kses_post( $description ) . '</span>';
      }

      if ( $tooltip_html && in_array( $value['type'], array( 'checkbox' ) ) ) {
          $tooltip_html = '<p class="description">' . $tooltip_html . '</p>';
      } elseif ( $tooltip_html ) {
          $tooltip_html = Ivole_Admin::ivole_wc_help_tip( $tooltip_html );
      }

      return array(
          'description'  => $description,
          'tooltip_html' => $tooltip_html,
      );
		}

		private static function ivole_wc_help_tip( $tip, $allow_html = false ) {
	    if ( $allow_html ) {
	        $tip = wc_sanitize_tooltip( $tip );
	    } else {
	        $tip = esc_attr( $tip );
	    }

	    return '<span class="woocommerce-help-tip" data-tip="' . $tip . '"></span>';
		}

		public function enqueue_color_picker( $hook ) {
			//error_log( $hook );
			if( 'woocommerce_page_wc-settings' == $hook ) {
				if( is_admin() ) {
					wp_enqueue_style( 'wp-color-picker' );
					wp_enqueue_script( 'ivole-color-picker', plugins_url('js/admin-color-picker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
				}
			} else if( 'edit.php' == $hook ) {
				wp_enqueue_script( 'ivole-manual-review-reminder', plugins_url('js/admin-manual.js', __FILE__ ), array(), false, false );
			}
		}

	}

endif;

?>
