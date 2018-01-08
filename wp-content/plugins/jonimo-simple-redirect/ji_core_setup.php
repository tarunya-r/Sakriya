<?php
/* 
Plugin Name: jonimo Simple Redirect
Plugin URI: http://www.jonimo.com
Description: Easily redirect different user roles to any custom url, page, tag or category a set number of times when they login (and much more..)
 * If you have buddypress installed, redirect users to their profile, their activity or their friends activity tabs.
Version: 1.5
Author: jonimo
Author URI: http://www.jonimo.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

require_once( plugin_dir_path(__FILE__) . 'class/redirect-core.php' );
require_once( plugin_dir_path(__FILE__) . 'class/redirect-login.php' );
require_once( plugin_dir_path(__FILE__) . 'class/redirect-logout.php' );
require_once( plugin_dir_path(__FILE__) . 'class/redirect-default.php' );
require_once( plugin_dir_path(__FILE__) . 'class/redirect-jonimo.php' );
require_once( plugin_dir_path(__FILE__) . 'addons/redirect-default-addon.php' );
require_once( plugin_dir_path(__FILE__) . 'addons/redirect-woocommerce-addon.php' );
require_once( plugin_dir_path(__FILE__) . 'addons/redirect-login-limit-addon.php' );
//require_once( plugin_dir_path(__FILE__) . 'addons/redirect-custom-page-addon.php' );

//require_once( plugin_dir_path(__FILE__) . 'class/redirect-settings.php' );
add_action( 'admin_menu', 'ji_redirect_admin_menu' );

/**
 * Define our links and register as constants
 *
 * @since 1.0
 * 
 */
define("JONIMO", "http://www.jonimo.com");
define("JONIMO_ABOUT", "http://www.jonimo.com/about-us");
define("JONIMO_SUPPORT", "http://www.jonimo.com/support");
define("JONIMO_REDIRECT_PRO", "http://www.jonimo.com/product/jonimo-simple-redirect-pro");


/**
 * Register our custom jQuery on our plugin page
 *
 * @since 1.0
 * 
 */
function ji_enqueue_jquery() {
        if ( is_admin()){
            
            wp_enqueue_style( 'ji-styles', plugins_url( '/css/ji-styles.css' , __FILE__ ), array(), '1.0.0', 'all' );
            
            if(function_exists( 'bp_is_active' )){
                wp_enqueue_script(
                        'queuescript',
                        plugins_url( '/js/admin-script-with-bp.js' , __FILE__ ),
                        array( 'jquery' )
                );
            }
            else{
                wp_enqueue_script(
                        'queuescript',
                        plugins_url( '/js/admin-script-without-bp.js' , __FILE__ ),
                        array( 'jquery' )
                );
            }
        }
}
add_action( 'admin_init', 'ji_enqueue_jquery' );

/**
 * Setup the admin menus.
 *
 * @since 1.0
 * @uses add_submenu_page
 * @uses add_menu_page
 * @uses plugins_url
 * 
 */
function ji_redirect_admin_menu() {
        add_menu_page( 'Redirect', 'Redirect Settings',
        'manage_options', 'jonimo', 'ji_redirect_display_settings_page',
        plugins_url( 'assets/images/ji_redirect.png', __FILE__ ) );
        //check what the first role is and append this to the 
        add_submenu_page( 'jonimo', 'Login', 'Login', 'manage_options',
        'jonimo', 'ji_redirect_display_settings_page' );
        add_submenu_page( 'jonimo', 'Logout', 'Logout', 'manage_options',
        'logout', 'ji_redirect_display_logout_page' );
        //add_submenu_page( __FILE__, 'Settings', 'Settings', 'manage_options',
        //__FILE__.'_settings', 'ji_redirect_display_settings' );
        add_submenu_page( 'jonimo', 'Default Link', 'Default Link', 'manage_options',
        'default', 'ji_redirect_display_default_page' );
        add_submenu_page( 'jonimo', 'About', 'About', 'manage_options',
        'about', 'ji_redirect_display_about_page' );
}

/**
 * Setup the about page
 * Since 1.0
 */
function ji_redirect_display_about_page() {
        ?>
        <div class="wrap">
        <?php screen_icon(); ?>
        <h2>About</h2>
         <div class ="wrap">
        <div id ="welcome-panel" class ="welcome-panel">
        <div class="welcome-panel-content">
	<h3><?php _e( 'Welcome to jonimo' ); ?></h3>
	<p class="about-description"><?php _e( 'jonimo creates, sources and sells premium WordPress plugins at a price <strong>you decide</strong>.' ); ?></p>
	<div class="welcome-panel-column-container">
	<div class="welcome-panel-column">
		<h4><?php _e( 'Find our more' ); ?></h4>
		<a class="button button-primary button-hero load-customize hide-if-no-customize" href="<?php echo JONIMO.'/plugins'; ?>" target="_blank"><?php _e( 'Premium jonimo plugins ' ); ?></a>
	
	</div>
	<div class="welcome-panel-column">
		<h4><?php _e( 'Browse our premium plugins' ); ?></h4>
		<ul>
                        <li><?php printf( '<a href="%s" class="welcome-icon welcome-learn-more" target="_blank">' . __( 'Premium Plugins' ) . '</a>', JONIMO.'/plugins' ); ?></li>
                        <li><?php printf( '<a href="%s" class="welcome-icon welcome-learn-more" target="_blank">' . __( 'Support' ) . '</a>', JONIMO_SUPPORT ); ?></li>
		</ul>
	</div>
	<div class="welcome-panel-column welcome-panel-last">
		<h4><?php _e( 'About jonimo' ); ?></h4>
		<ul>
			<li><?php printf( '<div class="welcome-icon welcome-edit-page"  target="_blank">' . __( '<a href="%1$s" target="_blank">About Us' ) . '</a>', JONIMO.'/about-us' ); ?></li>
                        <li><?php printf( '<div class="welcome-icon welcome-widgets-menus" >' . __( '<a href="%1$s" target="_blank">Newsletter' ) . '</a>', JONIMO.'/newsletter' ); ?></li>
                        <li>
			<a href="http://twitter.com/jonimo3" target="_new" ><img width="29" height="29" alt="Twitter" src="<?php echo plugins_url( 'assets/images/twitter_29.png', __FILE__ )?>"></a>
                        <a href="http://google.com/+jonimo" target="_new" ><img width="29" height="29" alt="Google" src="<?php echo plugins_url( 'assets/images/g_btn_white.png', __FILE__ )?>"></a> 
                        <a href="https://www.facebook.com/pages/Jonimo/240313332823121" target="_new" ><img width="29" height="29" alt="Facebook" src="<?php echo plugins_url( 'assets/images/FB-f-Logo__blue_29.png', __FILE__ )?>"></a> </li>
                </ul>
	</div>
	</div>
	</div>
        </div>
        </div>
        <p><?php printf('This plugin was created by <a href="%s" target="_blank">jonimo</a> with a little help from:', JONIMO); ?><br><br>
        <?php printf('Jatinder Pal Singh for certain small elements of this code. You can find his fantastic work at <a href="%s" target="_blank">appinstore.com</a><br>', 'http://www.appinstore.com'); ?>
         <?php printf('and bpdev for some ideas that led to parts of this code being written. You can find his great work on <a href="%s" target="_blank">buddydev.com</a><br>', 'http://www.buddydev.com'); ?>  
        <?php printf('and to Igor for some code that helped set up the welcome message for new users. Say hi to Igor at <a href="%s" target="_blank">lenslider.com</a>', 'http://www.lenslider.com'); ?>  
            
        </p>
        </div>
        <?php
}




/**
 * get the first role registered in an install. 
 * Since 1.0
 */
function ji_get_default_role(){
        //returns the first role set in an install.
        $wp_roles = new WP_Roles();
	$roles = $wp_roles->get_names();
        foreach ($roles as $role_value => $role_name) {
        return $role_value;
        }
}



function ji_on_activation() {
    ji_set_users_meta('ji_welcome_panel', 1, 'administrator');
}
//my-plugin-index.php must be an main file of plugin
register_activation_hook( __FILE__, 'ji_on_activation');


function ji_uninstall_function() {
   //OLD CODE: delete_user_meta(get_current_user_id(), 'the_plugin_welcome_panel');
   ji_set_users_meta('ji_welcome_panel', 1, 'administrator', 'delete');
}
register_uninstall_hook( __FILE__, 'ji_uninstall_function');


/**
 * adds, updates or deletes our custom option
 *
 * Thanks to igor at lenslider.com
 * @since 1.3
 * @uses add_user_meta
 * @uses update_user_meta
 * @uses delete_user_meta
 * 
 */
function ji_set_users_meta($meta_name, $meta_value, $role, $action = 'add') {
   $args = array('role' => $role);
   $users = get_users('owner');
   if(!empty($users) && is_array($users)) {
      foreach ($users as $user) {
         switch ($action) {
            case 'add':
               add_user_meta($user->ID, $meta_name, $meta_value, true);
               break;
            case 'update':
               update_user_meta($user->ID, $meta_name, $meta_value);
               break;
            case 'delete':
               delete_user_meta($user->ID, $meta_name);
               break;
         }
      }
   }
}


/**
 * 
 * Thanks to igor at lenslider.com
 * @since 1.3
 * @uses check_ajax_referer
 * @uses update_user_meta
 * @uses wp_die
 * 
 */
function ji_redirect_welcome_panel_close() {
   check_ajax_referer('ji-welcome-panel-nonce', 'welcomepanelnonce_ji');
   delete_user_meta(get_current_user_id(), 'ji_welcome_panel');
   wp_die(1);
}
add_action('wp_ajax_ji_redirect_welcome_panel_close', 'ji_redirect_welcome_panel_close');



/**
 * Displays the about messages
 * Thanks to wordress for this stuff!
 * 
 */
function ji_redirect_display_about(){
        if(get_user_meta(get_current_user_id(), 'ji_welcome_panel')){?>
        <div class ="wrap">
        <?php wp_nonce_field('ji-welcome-panel-nonce', 'welcomepanelnonce_ji', false);?>
        <div id ="welcome-panel" class ="welcome-panel">
            
        <a class="ji-welcome-panel-close" href="javascript:;">Dismiss</a>
        <div class="welcome-panel-content">
	<h3><?php _e( 'Welcome to jonimo' ); ?></h3>
	<p class="about-description"><?php _e( 'jonimo sells the best premium WordPress plugins at a price <strong>you decide</strong>.' ); ?></p>
	<div class="welcome-panel-column-container">
	<div class="welcome-panel-column">
		<h4><?php _e( 'Find our more' ); ?></h4>
		<a class="button button-primary button-hero load-customize hide-if-no-customize" href="<?php echo JONIMO.'/plugins'; ?>" target="_blank"><?php _e( 'Premium jonimo plugins ' ); ?></a>
	
	</div>
	<div class="welcome-panel-column">
		<h4><?php _e( 'Browse our premium plugins' ); ?></h4>
		<ul>
                        <li><?php printf( '<a href="%s" class="welcome-icon welcome-learn-more" target="_blank">' . __( 'Premium Plugins' ) . '</a>', JONIMO.'/plugins' ); ?></li>
                        <li><?php printf( '<a href="%s" class="welcome-icon welcome-learn-more" target="_blank">' . __( 'Support' ) . '</a>', JONIMO_SUPPORT ); ?></li>
		</ul>
	</div>
	<div class="welcome-panel-column welcome-panel-last">
		<h4><?php _e( 'About jonimo' ); ?></h4>
		<ul>
			<li><?php printf( '<div class="welcome-icon welcome-edit-page"  target="_blank">' . __( '<a href="%1$s" target="_blank">About Us' ) . '</a>', JONIMO.'/about-us' ); ?></li>
                        <li><?php printf( '<div class="welcome-icon welcome-widgets-menus" >' . __( '<a href="%1$s" target="_blank">Newsletter' ) . '</a>', JONIMO.'/newsletter' ); ?></li>
                        <li>
			<a href="http://twitter.com/jonimo3" target="_new" ><img width="29" height="29" alt="Twitter" src="<?php echo plugins_url( 'assets/images/twitter_29.png', __FILE__ )?>"></a>
                        <a href="http://google.com/+jonimo" target="_new" ><img width="29" height="29" alt="Google" src="<?php echo plugins_url( 'assets/images/g_btn_white.png', __FILE__ )?>"></a> 
                        <a href="https://www.facebook.com/pages/Jonimo/240313332823121" target="_new" ><img width="29" height="29" alt="Facebook" src="<?php echo plugins_url( 'assets/images/FB-f-Logo__blue_29.png', __FILE__ )?>"></a> </li>
                </ul>
	</div>
	</div>
	</div>
        </div>
        </div>
        <?php } else { ?>

        <div class ="wrap">
        <div id ="welcome-panel" class ="welcome-panel">
            <h3 style =" padding-bottom: 10px;"><a class="button button-primary" href="<?php echo JONIMO.'/plugins'; ?>" target="_blank"><?php _e( 'jonimo premium plugins ' ); ?></a>
                        <a style ="padding-left: 5px; "href="http://twitter.com/jonimo3" target="_new" ><img width="15" height="15" alt="Twitter" src="<?php echo plugins_url( 'assets/images/twitter_29.png', __FILE__ )?>"></a>
                        <a style ="padding-left: 5px; "href="http://google.com/+jonimo" target="_new" ><img width="15" height="15" alt="Google" src="<?php echo plugins_url( 'assets/images/g_btn_white.png', __FILE__ )?>"></a> 
                        <a style="padding-left: 5px; "href="https://www.facebook.com/pages/Jonimo/240313332823121" target="_new" ><img width="15" height="15" alt="Facebook" src="<?php echo plugins_url( 'assets/images/FB-f-Logo__blue_29.png', __FILE__ )?>"></a>
        </h3>
        <p class="about-description"><?php _e( 'The best premium WordPress plugins at a price <strong>you decide</strong>.' ); ?></p>
        &nbsp;
        </div>
	</div>
        <?php
        };
}       
?>