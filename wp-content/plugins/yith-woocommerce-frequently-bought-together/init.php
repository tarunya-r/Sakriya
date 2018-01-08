<?php
/**
 * Plugin Name: YITH WooCommerce Frequently Bought Together
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-frequently-bought-together/
 * Description: YITH WooCommerce Frequently Bought Together add a box in single product page with products suggested as "frequently bought together"
 * Version: 1.1.3
 * Author: YITHEMES
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-frequently-bought-together
 * Domain Path: /languages/
 *
 * @author Yithemes
 * @package YITH WooCommerce Frequently Bought Together
 * @version 1.1.3
 */
/*  Copyright 2015  Your Inspiration Themes  (email : plugins@yithemes.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

function yith_wfbt_free_install_woocommerce_admin_notice() {
	?>
	<div class="error">
		<p><?php _e( 'YITH WooCommerce Frequently Bought Together is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-frequently-bought-together' ); ?></p>
	</div>
<?php
}


function yith_wfbt_install_free_admin_notice() {
	?>
	<div class="error">
		<p><?php _e( 'You can\'t activate the free version of YITH WooCommerce Frequently Bought Together while you are using the premium one.', 'yith-woocommerce-frequently-bought-together' ); ?></p>
	</div>
	<?php
}

if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );


if ( ! defined( 'YITH_WFBT_VERSION' ) ){
	define( 'YITH_WFBT_VERSION', '1.1.3' );
}

if ( ! defined( 'YITH_WFBT_FREE_INIT' ) ) {
	define( 'YITH_WFBT_FREE_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WFBT_INIT' ) ) {
	define( 'YITH_WFBT_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_WFBT' ) ) {
	define( 'YITH_WFBT', true );
}

if ( ! defined( 'YITH_WFBT_FILE' ) ) {
	define( 'YITH_WFBT_FILE', __FILE__ );
}

if ( ! defined( 'YITH_WFBT_URL' ) ) {
	define( 'YITH_WFBT_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'YITH_WFBT_DIR' ) ) {
	define( 'YITH_WFBT_DIR', plugin_dir_path( __FILE__ )  );
}

if ( ! defined( 'YITH_WFBT_TEMPLATE_PATH' ) ) {
	define( 'YITH_WFBT_TEMPLATE_PATH', YITH_WFBT_DIR . 'templates' );
}

if ( ! defined( 'YITH_WFBT_ASSETS_URL' ) ) {
	define( 'YITH_WFBT_ASSETS_URL', YITH_WFBT_URL . 'assets' );
}

if ( ! defined( 'YITH_WFBT_META' ) ) {
	define( 'YITH_WFBT_META', '_yith_wfbt_ids' );
}

/* Plugin Framework Version Check */
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_WFBT_DIR . 'plugin-fw/init.php' ) ) {
	require_once( YITH_WFBT_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_WFBT_DIR  );


function yith_wfbt_free_init() {

	load_plugin_textdomain( 'yith-woocommerce-frequently-bought-together', false, dirname( plugin_basename( __FILE__ ) ). '/languages/' );

	// Load required classes and functions
	require_once('includes/class.yith-wfbt.php');

	// Let's start the game!
	YITH_WFBT();
}
add_action( 'yith_wfbt_free_init', 'yith_wfbt_free_init' );


function yith_wfbt_free_install() {

	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'yith_wfbt_free_install_woocommerce_admin_notice' );
	}
	elseif ( defined( 'YITH_WFBT_PREMIUM' ) ) {
		add_action( 'admin_notices', 'yith_wfbt_install_free_admin_notice' );
		deactivate_plugins( plugin_basename( __FILE__ ) );
	}
	else {
		do_action( 'yith_wfbt_free_init' );
	}
}
add_action( 'plugins_loaded', 'yith_wfbt_free_install', 11 );