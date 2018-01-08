<?php
// If uninstall not called from WordPress exit
if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
exit ();
// Delete options for each role from options table
$wp_roles = new WP_Roles();
$roles = $wp_roles->get_names();
foreach ($roles as $role_value => $role_name) {
delete_option( 'ji_register_'.$role_name);
}

//delete the default link option on uninstall. 
delete_option('ji_redirect_default');

?>