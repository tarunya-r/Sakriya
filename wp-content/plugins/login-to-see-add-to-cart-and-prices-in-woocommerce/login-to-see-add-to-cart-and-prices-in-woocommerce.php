<?php
/*
Plugin Name: Login to see add to cart and prices in WooCommerce
Plugin URI: http://iacopocutino.it/login-see-add-cart-prices/
Description: A simple plugin useful to hide add to cart buttons and prices for not registered users. Requires WooCommerce plugin.
Author: Iacopo C
Version: 1.2
Author URI: http://iacopocutino.it
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
WC requires at least: 2.1
WC tested up to: 3.2.3

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see http://www.gnu.org/licenses/gpl-3.0.html.
*/


if (! defined('ABSPATH')) {
    exit();
}


// Include pluggable.php to use the is_user_logged_in function

include_once(ABSPATH . 'wp-includes/pluggable.php');

// Add settings page

require ('settings.php');

// Add warning banner if the plugin in active but WooCommerce is inactive

function hatc_login_error_notice() {
  
  if( !class_exists('woocommerce')) {
    ?>
    <div class="error notice">
        <p><?php _e( 'Login to see add to cart and prices plugin requires WooCommerce, please activate WooCommerce to use this plugin', 'hatc_login_plugin' ); ?></p>
    </div>
    <?php
  }
}
add_action( 'admin_notices', 'hatc_login_error_notice' );

// Check if WooCommerce is active 

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	

// Deactivate WooCommerce buttons for every product for guest costumers


$checkbox_allproducts = isset(get_option('ic_settings')['hatc_login_checkbox_field_0']);
	
 if ($checkbox_allproducts == '1' && !is_user_logged_in())   {
	 
 function hatc_login_product_is_purchasable( $purchasable ) {
	
        $purchasable = false;
    return $purchasable;

}
add_filter( 'woocommerce_is_purchasable', 'hatc_login_product_is_purchasable', 10, 2 );
	 
	 
// Custom messages for hide add to cart option

function hatc_login_add_to_cart_option() { 
	
	$custom_message = get_option('ic_settings')['hatc_login_text_field_0'];
	
	$default_message = __('Login first','hatc_login_plugin');
	
	$custom_page_url = get_option('ic_settings')['hatc_login_select_field_1'];
  
	
	if($custom_message !== '') {
	
    	echo '<a class="button wltspab_custom_login_link" href="' . $custom_page_url . '">' . $custom_message . '</a>';
		
	} else {
	
		echo '<a class="button wltspab_custom_login_link" href="' . $custom_page_url . '">' . $default_message . '</a>';
		
	} 
	 
}        

add_action( 'woocommerce_single_product_summary', 'hatc_login_add_to_cart_option', 10, 0 ); 
add_filter( 'woocommerce_loop_add_to_cart_link', 'hatc_login_add_to_cart_option' );	 
	 
 }

// Hide prices in WooCommerce


$checkbox_prices = isset(get_option('ic_settings')['hatc_login_checkbox_field_3']);

 if ( $checkbox_prices == '1' && !is_user_logged_in()) {

 
function hatc_login_remove_prices( $price, $product ) {

  $custom_price_text = get_option('ic_settings')['hatc_login_text_field_2'];
	
  if ($custom_price_text !=='') {
	  
	  $price = $custom_price_text;
	  return $price;
 
  } else {
	
  	$price = __('Login to see prices','hatc_login_plugin');

  return $price;
  }
}

add_filter( 'woocommerce_variable_sale_price_html', 'hatc_login_remove_prices', 10, 2 );
add_filter( 'woocommerce_variable_price_html', 'hatc_login_remove_prices', 10, 2 );
add_filter( 'woocommerce_get_price_html', 'hatc_login_remove_prices', 10, 2 );

}

 
}



?>
