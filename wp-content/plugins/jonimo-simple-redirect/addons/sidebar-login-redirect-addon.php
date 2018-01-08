<?php

/**
 * Hooks into our custom hook sidebar_login_widget_login_redirect and redirects the user to the JSredirect location 
 * 
 * @returns $link as string. 
 * 
 * @since 1.4.2
*/
function ji_sidebar_login_redirect($redirect = null){
          
            //$user = wp_get_current_user();
            $current_user_options = '';
            $wp_roles = new WP_Roles();
            $roles = $wp_roles->get_names();
            //we go through each role and it matches with the current users role we pull the option.
            if ( $roles ) { 
                foreach ( $roles as $role_value => $role_name ) {
                    $role_name = strtolower( $role_name );
                    $user = get_user_by( 'id', 1 );
                    
                    //return 'arse';
                    if ( isset( $user->roles[0] )){
                        if( $role_name == $user->roles[0] ){
                           
                            $current_user_options = get_option ( 'ji_register_'.$role_name ); 
                            //var_dump($current_user_options);
                            break;
                        }
                    }
                }    
            }
            //if we have an option for that role, we get the options and see which one does not equal 0. 
            if ( $current_user_options != null  ){
                  
                foreach ( $current_user_options as $optionname => $optionvalue ){
                    //if the option value equals something other than 0, and that value is not part of the limit option, OR. the value is a string.
                    if (isset($current_user_options['custom']) && $current_user_options['custom'] != ''){
                        $link  = $current_user_options['custom'];
                        $link = esc_url($link);
                        $returnlink = apply_filters( 'ji_filter_login_link' , $link, $user, $role_name ); 
                        return $returnlink;
                        exit();
                    }
                    
                    if ($optionvalue != 0 && $optionname != 'limit' ){
                        $link = ji_redirect_get_login_link( $redirect_url = null , $request_url = null, $user, $optionvalue,  $optionname, $role_name);
                        //if we succesffuly return a link..
                        if ($link != null ){ 
                        $link = esc_url( $link );
                        $returnlink = apply_filters( 'ji_filter_login_link' , $link, $user, $role_name );
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
            $default = apply_filters( 'ji_filter_default_link' , $default_link, $user, $role_name ); 
            return $default;
}
//add_filter( "sidebar_login_widget_login_redirect","ji_sidebar_login_redirect",100, 1 );  
?>