<?php

/*
Plugin Name: Woocommerce Empty Cart On Browser Close
Plugin URI: http://wordpress.org/plugins/woocommerce-empty-cart-browser-close/
Description: Empty the cart of a woocommerce driven eShop when the visitor closes the window.
Author: MAK Joy
Version: 1.0
Author URI: http://nomfolio.com/me
*/


add_action('init','nom_empty_cart_init');
add_action('wp_login','nom_empty_cart_init_login');
add_action('wp_logout','nom_empty_cart_init_logout');

function nom_empty_cart_init(){
	session_start();

	if( !isset($_SESSION['nom_empty_cart_init_session_variable_key']) or !wp_verify_nonce($_SESSION['nom_empty_cart_init_session_variable_key'],'nom_empty_cart_init_session_variable_key_action') ){
		
		//	just married :D so just delete the cart and wait till the browser is closed :D
		
		global $woocommerce;		
		
		$opt = get_option('nom_empty_cart_browser_close_enable');
		$opt = $opt != 1 ? false : true;
		
		if( $opt and $woocommerce->cart != null){
			$woocommerce->cart->empty_cart();
		}
				
		
		$_SESSION['nom_empty_cart_init_session_variable_key'] = wp_create_nonce('nom_empty_cart_init_session_variable_key_action');
			
	}
	
	/**
	 * @todo make to listen window close someway 
	 * 
	 */
		
}

//	destroy session on user login
function nom_empty_cart_init_login(){
	$opt = get_option('nom_empty_cart_browser_close_do_login');
	$opt = $opt != 1 ? false : true;
	
	if( $opt ){
		session_destroy();
	}
}

//	destroy session on user logout
function nom_empty_cart_init_logout(){
	$opt = get_option('nom_empty_cart_browser_close_do_logout');
	$opt = $opt != 1 ? false : true;
	
	if( $opt ){
		session_destroy();
	}
}

add_action('admin_menu','nom_empty_cart_init_admin_init');
function nom_empty_cart_init_admin_init(){
	add_options_page( 'Woocommerce Clear Cart on Browser Closing', 'WC Clear Cart on Browser Close', 'manage_options', 'wc-clear-cart-on-browser-close', 'wc_clear_cart_on_browser_close' );	
}

function wc_clear_cart_on_browser_close(){
	
	if( isset( $_REQUEST['save_accconc'] ) and wp_verify_nonce($_REQUEST['wc-clear-cart-on-browser-close-name'],'wc-clear-cart-on-browser-close-action')):
		
		//	SAVING THE FORM DATA
			
			//	enable wcccobc
			if( isset($_REQUEST['enable_wcccobc']) )
				update_option('nom_empty_cart_browser_close_enable',1);
	
			//	enable wcccobc on login
			if( isset($_REQUEST['enable_wcccobc_on_login']) )
				update_option('nom_empty_cart_browser_close_do_login',1);
			
			//	enable wcccobc on logout
			if( isset($_REQUEST['enable_wcccobc_on_logout']) )
				update_option('nom_empty_cart_browser_close_do_logout',1);
	
			
		//	SAVING ;) ENDS
	
	endif;
	
	?>
	<div class="wrap">
		<div class="inside">
			<h2>Woocommerce Clear Cart on Browser Closing</h2>
			<p>Note: the cart will be empty if the visitor close the whole browser, not just the widow. (will be updated soon)</p>
			
			<form action="<?php admin_url('options-general.php?page=wc-clear-cart-on-browser-close');?>" method="post">
				<?php wp_nonce_field('wc-clear-cart-on-browser-close-action','wc-clear-cart-on-browser-close-name')?>
				<p>
					<input id="enable_wcccobc" type="checkbox" class="checkbox" name="enable_wcccobc" value="1" <?php checked(get_option('nom_empty_cart_browser_close_enable'),'1');?>>
					<label for="enable_wcccobc" >Enable clear cart on browser closing</label>					
				</p>
				<p>
					<input id="enable_wcccobc_on_login" type="checkbox" class="checkbox" name="enable_wcccobc_on_login" value="1" <?php checked(get_option('nom_empty_cart_browser_close_do_login'),1);?>>
					<label for="enable_wcccobc_on_login" >Enable clear cart on on user login</label>					
				</p>
				<p>
					<input id="enable_wcccobc_on_logout" type="checkbox" class="checkbox" name="enable_wcccobc_on_logout" value="1" <?php checked(get_option('nom_empty_cart_browser_close_do_logout'),1);?>>
					<label for="enable_wcccobc_on_logout">Enable clear cart on user logout</label>					
				</p>
				<p>
					<input type="submit" class="button-primary" value="Save" name="save_accconc">
				</p>
			</form>
		</div>
	</div>
	<?php 
}
