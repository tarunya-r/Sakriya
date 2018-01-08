<?php
/*
Plugin Name: Customer Reviews for WooCommerce
Description: Customer Reviews for WooCommerce plugin helps you get more customer reviews for your shop by sending automated reminders and coupons.
Plugin URI: https://wordpress.org/plugins/customer-reviews-woocommerce/
Version: 3.16
Author: ivole
Author URI: https://profiles.wordpress.org/ivole
Text Domain: customer-reviews-woocommerce
WC requires at least: 2.2.11
WC tested up to: 3.2.5
License: GPLv3

Customer Reviews for WooCommerce is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
any later version.

Customer Reviews for WooCommerce is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with WooCommerce Reviews. If not, see https://www.gnu.org/licenses/gpl.html.
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! defined( 'IVOLE_TEXT_DOMAIN' ) ) {
	define( 'IVOLE_TEXT_DOMAIN', 'customer-reviews-woocommerce' );
}


//@TODO: Delete stored settings on activation, uncomment it for development
/*
function my_ivole_activate() {
	// Activation code here...
	global $wpdb;
	$wpdb->query("DELETE FROM ".$wpdb->options." WHERE option_name LIKE 'ivole_%' ");
}
register_activation_hook( __FILE__, 'my_ivole_activate' );
*/

require_once( 'class-ivole.php' );
require_once( 'class-ivole-qtranslate.php' );

/**
 * Check if WooCommerce is active
**/
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
  add_action('init', 'ivole_init', 9);
  function ivole_init() {
		load_plugin_textdomain( IVOLE_TEXT_DOMAIN, FALSE, basename( dirname( __FILE__ ) ) . '/languages' );
    $ivole = new Ivole();
  }

	add_action('plugins_loaded', 'qtranslate_init', 1);
	function qtranslate_init() {
    $ivole_qtranslate = new Ivole_QTranslate();
  }
}

add_shortcode( 'ivole_unsubscribe', 'ivole_email_unsubscribe_shortcode' );
function ivole_email_unsubscribe_shortcode() {
	$email = '';
	if( isset( $_GET['ivole_email_unsubscribe'] ) ) {
		$email = strval( $_GET['ivole_email_unsubscribe'] );
	};
	if( isset( $_POST['ivole_submit'] ) && isset( $_POST['ivole_email'] ) ) {
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$opt_out_emails = get_option( 'ivole_unsubscribed_emails', array() );
			if( !in_array( $email, $opt_out_emails ) ) {
				$opt_out_emails[] = $email;
				update_option( 'ivole_unsubscribed_emails', $opt_out_emails );
				echo '<p>' . __('Success: you have unsubscribed from emails related to reviews!', 'ivole') . '</p>';
			} else {
				echo '<p>' . __('Success: you have unsubscribed from emails related to reviews!', 'ivole') . '</p>';
			}
		} else {
			echo '<p>' . __('Error: please provide a valid email address!', 'ivole') . '</p>';
		}
		echo '<a href="' . get_home_url() . '">' . __( 'Go to home page', 'ivole' ) . '</a>';
		return;
	}
	?>
	<div class="ivole-unsubscribe-form">
	    <form action="" method="post">
	        <input type="hidden" name="ivole_action" value="ivole_unsubscribe" />
	        <p>
	            <label for="ivole_email"><?php _e('Email Address:', 'ivole'); ?></label>
	            <input type="text" id="ivole_email" name="ivole_email" value="<?php echo esc_attr($email); ?>" size="25" />
	        </p>
	        <p>
	            <input type="submit" name="ivole_submit" value="<?php _e('Unsubscribe', 'ivole'); ?>" />
	        </p>
	    </form>
	</div>
	<?php
}

add_shortcode( 'cusrev_reviews', 'ivole_reviews_shortcode', 10, 2 );
function ivole_reviews_shortcode( $content, $atts )
{
	$shortcode_enabled = get_option( 'ivole_reviews_shortcode', 'no' );
	if( $shortcode_enabled === 'no' ) {
		return $content;
	} else {
		extract(shortcode_atts( array( 'comment_file' => '/comments.php' ), $atts) );
		$content = ivole_return_comment_form( $comment_file );
	}
	return $content;
}
function ivole_return_comment_form( $comment_file )
{
	ob_start();
	comments_template( $comment_file );
	$form = ob_get_contents();
	ob_end_clean();
	return $form;
}

register_activation_hook( __FILE__, 'ivole_activation_hook' );
function ivole_activation_hook() {
	update_option( 'ivole_activation_notice', 1 );
}

?>
