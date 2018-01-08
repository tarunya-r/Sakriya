<?php

/**
 * Hooks into login_redirect. On login, redirects different users to different areas. If no area is selected then we redirect to admin.
 * 
 * @since 1.1
 * @uses WP_Roles
 * @uses strtolower
 * @uses get_option
 * @uses ji_redirect_get_login_link
 * @return string link
 * @todo merge with ji_redirect_logout
 * 
*/
function ji_redirect_woocommerce_login( $redirect, $user ){
   
        $current_user_options = '';
      
            $wp_roles = new WP_Roles();
            $roles = $wp_roles->get_names();
            //we go through each role and it matches with the current users role we pull the option.
            if ( $roles ) { 
                foreach ( $roles as $role_value => $role_name ) {
                     $role_value = strtolower( $role_value );
                      
                    if ( isset( $user->roles[0] )){
                       
                        if( $role_value == $user->roles[0] ){
                             
                            $current_user_options = get_option ( 'ji_register_'.$role_value );
                            
                            break;
                        }
                    }
                }    
            }
            
       
            //if we have an option for that role, we get the options and see which one does not equal 0. 
            if ( $current_user_options != null  ){
               // wp_die( ''.var_export( $current_user_options, true ).'');
                foreach ( $current_user_options as $optionname => $optionvalue ){
                    //if the option value equals something other than 0, and that value is not part of the limit option, OR. the value is a string.
                    if (isset($current_user_options['custom']) && $current_user_options['custom'] != ''){
                        $link  = $current_user_options['custom'];
                        $link = esc_url($link);
                        
                        $returnlink = apply_filters( 'ji_filter_login_link' , $link, $user, $role_value ); 
                        
                        wp_redirect($returnlink);
                        //wp_die( ''.var_export( $returnlink, true ).'');
                        exit();
                    }
                    
                    if ($optionvalue != 0 && $optionname != 'limit' ){
                        $link = ji_redirect_get_login_link( $redirect_url = null , $request_url = null, $user, $optionvalue,  $optionname, $role_value);
                        //if we succesffuly return a link..
                        if ($link != null ){ 
                        $link = esc_url( $link );
                        $returnlink = apply_filters( 'ji_filter_login_link' , $link, $user, $role_value );
                        return $returnlink;
                        exit();
                        }

                        //if we dont return a link (despite a value being passed to ji_redirect_get_login_link
                        else {
                             printf('<p>Select one of the options above to redirect users with the role %s to on login.<p>', $role_name);
                        }
                    }    
                }
            }
            //if there are no current options set, or if there is no link set, we revert to the default link. 
            $default_link = admin_url();
            $default = apply_filters( 'ji_filter_default_link' , $default_link, $user, $role_value ); 
            return $default;
        
}
add_filter( "woocommerce_login_redirect","ji_redirect_woocommerce_login",100, 2 );   
?>