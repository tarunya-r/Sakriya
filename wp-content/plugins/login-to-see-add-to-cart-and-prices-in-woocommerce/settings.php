<?php


if (! defined('ABSPATH')) {
    exit();
}

// Setting API of option page


add_action( 'admin_menu', 'hatc_login_add_admin_menu' );
add_action( 'admin_init', 'hatc_login_settings_init' );

// Submenu page in WooCommerce menu
function hatc_login_add_admin_menu() { 

	add_submenu_page( 'woocommerce', 'Login to see add to cart and prices', 'Login to see add to cart and prices', 'manage_options', 'login_to_see_add_to_cart_prices', 'hatc_login_options_page' );

}


function hatc_login_settings_init() { 

	register_setting( 'pluginPage_option', 'ic_settings' );

	add_settings_section(
		'hatc_pluginPage_section', 
		__( 'Settings of the plugin', 'hatc_login_plugin' ), 
		'hatc_login_settings_section_callback', 
		'pluginPage_option'
	);

	add_settings_field( 
		'hatc_login_checkbox_field_0', 
		__( 'Hide add to cart buttons for guest costumers', 'hatc_login_plugin' ), 
		'hatc_login_checkbox_field_0_render', 
		'pluginPage_option', 
		'hatc_pluginPage_section' 
	);

		
	add_settings_field( 
		'hatc_login_text_field_0', 
		__( 'Personalized text for add to cart button for guests', 'hatc_login_plugin' ), 
		'hatc_login_text_field_0_render', 
		'pluginPage_option', 
		'hatc_pluginPage_section' 
	);
	
	
	add_settings_field( 
		'hatc_login_select_field_1', 
		__( 'Redirect guest costumers to a page', 'hatc_login_plugin' ), 
		'hatc_login_select_field_1_render', 
		'pluginPage_option', 
		'hatc_pluginPage_section' 
	);


	add_settings_field( 
		'hatc_login_checkbox_field_3', 
		__( 'Turn off products prices for guests', 'hatc_login_plugin' ), 
		'hatc_login_checkbox_field_3_render', 
		'pluginPage_option', 
		'hatc_pluginPage_section' 
	);

	
		add_settings_field( 
		'hatc_login_text_field_2', 
		__( 'Personalized text for prices field for guests', 'hatc_login_plugin' ), 
		'hatc_login_text_field_2_render', 
		'pluginPage_option', 
		'hatc_pluginPage_section' 
	);

}

// Checkbox for hide WooCommerce add to cart
function hatc_login_checkbox_field_0_render() { 

	$options = get_option('ic_settings');
	?>
	<input type='checkbox' name='ic_settings[hatc_login_checkbox_field_0]' <?php if(isset($options['hatc_login_checkbox_field_0'])) { checked( $options['hatc_login_checkbox_field_0'], 1 ); } ?> value='1'>
	<label><?php _e('Check to hide add to cart buttons for guest costumers','hatc_login_plugin') ?></label>
	<?php

}


function hatc_login_text_field_0_render() { 
	
	$default_message = __('Login first','hatc_login_plugin');

	$options = get_option('ic_settings');
	?>
	<input type='text' class='regular-text' name='ic_settings[hatc_login_text_field_0]' value='<?php if(isset($options['hatc_login_text_field_0'])) { echo $options['hatc_login_text_field_0']; } ?>' placeholder='<?php echo $default_message; ?>'>
	<?php

}

// Dropdown menu for link in Add to Cart buttons

function hatc_login_select_field_1_render() { 

	$options = get_option('ic_settings');
	?>
	<select name='ic_settings[hatc_login_select_field_1]'>
	<?php $pages = get_pages(); ?>
		<option value="" selected><?php _e('Select a page','hatc_login_plugin');?></option>
  	<?php foreach ( $pages as $page ): ?>
		<option value="<?php echo get_page_link( $page->ID ); ?>" <?php if(isset($options['hatc_login_select_field_1'])) {selected( $options['hatc_login_select_field_1'], get_page_link( $page->ID )); }; ?>><?php echo $page->post_title; ?></option>
		<?php endforeach; ?>
	</select>

	<?php
}

// Checkbox for hide prices in WooCommerce

function hatc_login_checkbox_field_3_render() { 

	$options = get_option('ic_settings');
	?>
	<input type='checkbox' name='ic_settings[hatc_login_checkbox_field_3]' <?php if(isset($options['hatc_login_checkbox_field_3'])) { checked( $options['hatc_login_checkbox_field_3'], 1 ); } ?> value='1'>
	<label><?php _e('Check to hide prices for guest costumers','hatc_login_plugin') ?></label>
	<?php

}

function hatc_login_text_field_2_render() { 

	$options = get_option('ic_settings');
	?>
	<input type='text' class='regular-text' name='ic_settings[hatc_login_text_field_2]' value='<?php if(isset($options['hatc_login_text_field_2'])) { echo $options['hatc_login_text_field_2']; } ?>' placeholder='Login to see prices'>
	<?php

}


function hatc_login_settings_section_callback() { 

	echo __( 'Check the following options to hide add to cart buttons and prices for guest customers', 'hatc_login_plugin' );

}


function hatc_login_options_page() { 

 // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
    // Add error/update messages
 
    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if (isset($_GET['settings-updated'])) {
        // add settings saved message with the class of "updated"
        add_settings_error('hatc_login_messages', 'hatc_login_message', __('Settings Saved', 'hatc_login_plugin'), 'updated');
    }
 
    // Show error/update messages
    settings_errors('hatc_login_messages');

	?>
	<form action='options.php' method='post'>

		<h2><?php _e('Login to see add to cart and prices in WooCommerce','hatc_login_plugin') ?></h2>

		<?php
		settings_fields('pluginPage_option');
		do_settings_sections('pluginPage_option');
		submit_button();
		?>

	</form>
	<?php
}

// Footer admin custom text for plugin page

function hatc_login_footer_admin_text () {
		
	
  $current_screen = get_current_screen();

    if( $current_screen ->id === "woocommerce_page_login_to_see_add_to_cart_prices" ) {


	$custom_footer_text = sprintf(__('Thanks for using <a href="%s" target="_blank">Login to see add to cart and prices in WooCommerce</a>','hatc_login_plugin'), __('https://wordpress.org/plugins/login-to-see-add-to-cart-and-prices-in-woocommerce/'));

	return $custom_footer_text;

	}

}

add_filter('admin_footer_text', 'hatc_login_footer_admin_text');
