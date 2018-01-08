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
function ji_redirect_login( $redirect_url, $request_url, $user ){
        global $user;
        $current_user_options = '';
       
            $wp_roles = new WP_Roles();
            $roles = $wp_roles->get_names();
            //we go through each role and it matches with the current users role we pull the option.
            if ( $roles ) { 
                foreach ( $roles as $role_value => $role_name ) {
                    $role_name = strtolower( $role_value );
                    if ( isset( $user->roles[0] )){
                        if( $role_name == $user->roles[0] ){
                            $current_user_options = get_option ( 'ji_register_'.$role_value ); 
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
                        $returnlink = apply_filters( 'ji_filter_login_link' , $link, $user, $role_value ); 
                        wp_redirect($returnlink);
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
add_filter( "login_redirect","ji_redirect_login",100, 3 );          


/**
 * Hooks into wp_logout. On logout, redirects users to a selected area. If no area is selected then no redirect occurs.
 * 
 * @since 1.0
 * @uses get_option
 * @uses ji_redirect_get_logout_link
 * @return string link
 * @todo merge with ji_redirect_logout
 * 
*/
function ji_redirect_logout() {
        //we get the user object to pass in case we want to use it in the ji_filter_logout_link
        global $user;
        $options = get_option( 'ji_redirect_logout' );
        if ( $options ){
                foreach ( $options as $option => $value ){
                     if (isset($options['custom']) && $options['custom'] != ''){
                            // add our custom link... 
                            $url = $options['custom'];
                            $link = esc_url($url);
                            wp_redirect( $link );
                            exit();
                    }

                //if we are dealing with a link generated from a dropdown (other than limit).
                    if ($value != 0 && $option != 'limit' ){
                            $link = ji_redirect_get_logout_link($value, $option);
                            //if we succesffuly return a link..
                            if ($link != null ){ 
                            $link = esc_url( $link );
                            wp_redirect( $link );
                            exit();
                            }

                            //if we dont return a link (despite a value being passed to ji_redirect_get_login_link
                            else {
                                 printf('<p>Select one of the options above to redirect users on logout.<p>');
                            }
                    }    

                if (array_sum( $options) == 0 && (!isset($options['custom']) || $options['custom'] == '')){
                wp_redirect(get_home_url()); 
                }

            }
         }
        else{
        //if options are not set then we revert to normal behaviour    
        wp_redirect(get_home_url());
        } 
      
}
add_filter( "wp_logout","ji_redirect_logout",100 );


/**
 * Gets the links that we will redirect to on logout
 *
 * @since 1.1
 * @uses get_page_link
 * @uses get_tag_link
 * @return url string
 * @Credit to Jatinder Pal Singh for certain elements of this code. You can find his work on http://www.appinstore.com
 * @Credit to bpdev for some ideas that led to parts of this code being written. You can find his work on http://buddydev.com/ 
*/
function ji_redirect_get_login_link( $redirect_url, $request_url, $user, $value, $option, $role_name ) {
        //If its a category we return the category url
        if( $option === 'categories' ){
        $uri = get_category_link( $value );
        $uri_link = apply_filters( 'ji_filter_login_category_link' , $uri, $value, $user ); 
        return $uri_link;
        }
        //If ts a page we return the page url
         elseif ( $option === 'page' ){
            $uri = get_page_link( $value );
            $uri_link = apply_filters( 'ji_filter_login_page_link' , $uri, $value, $user ); 
            return $uri_link;
         }
        //If its a tag we return the tag url
        elseif ( $option === 'tag' ){
            $uri = get_tag_link( $value );
            $uri_link = apply_filters( 'ji_filter_login_tag_link' , $uri, $value, $user ); 
            return $uri_link;
        }
        //If its a tag we return the tag url
        elseif ( $option === 'custom' ){
            $url = esc_url($value);
            //next version.. $url = apply_filters( 'ji_filter_login_custom_link', $url, $value, $user );
            wp_redirect($url);
        }
         //If its a Buddypress we return the specified url
        elseif ( $option === 'bp' ){
             if(function_exists('bp_is_active')){
                        global $bp;
                        if( $value == 1 ){                    
                                //just in case this is depricated...
                                if(function_exists('bp_core_get_user_domain')){
                                //if we are logged in lets get the domain by the loggedin_user_id
                                if (is_user_logged_in()){
                                $profile_url = bp_core_get_user_domain( bp_loggedin_user_id() );   
                                }
                                //if not we use the user object
                                else{
                                $profile_url = bp_core_get_user_domain( $user->ID );
                                }
                                return $profile_url;
                                }
                        }
                        elseif( $value === 2 ){
                                if(function_exists('bp_get_activity_root_slug')){
                                $activity_slug = bp_get_activity_root_slug();
                                $activity_url = $bp->root_domain."/".$activity_slug;
                                return $activity_url;
                                }
                        }
                        elseif( $value === 3 ){
                                if(function_exists('bp_get_activity_root_slug')){
                                $activity_slug = bp_get_activity_root_slug();
                                $friends_activity = $bp->root_domain."/".$ctivity_slug."/friends/";
                                return $friends_activity;
                                }
                        }
                        elseif( $value === 4 ){
                                //just in case this is depricated...
                                if(function_exists('bp_core_get_user_domain')){
                                //if we are logged in lets get the domain by the loggedin_user_id
                                if (is_user_logged_in()){
                                $profile_url = bp_core_get_user_domain( bp_loggedin_user_id() );   
                                }
                                //if not we use the user object
                                else{
                                $profile_url = bp_core_get_user_domain( $user->ID );
                                }
                                return $profile_url.'profile/edit';
                                }
                        }
                    }
        }
                //if buddypress is deactivated or uninstalled, and there is a BP option set, we reset all options to 0 and redirect users to the admin.
        else{
            $args = array();
            $args['page'] = 0;
            $args['categories'] = 0;
            $args['tag'] = 0;
            $args['bp'] = 0;
            update_option( 'ji_register_'.$role_name, $args );

            $default_link = admin_url();
            $default = apply_filters( 'ji_filter_default_link' , $default_link, $user->ID ); 
            return  $default;
        }
}



/**
 * Gets the links that we will redirect to on logout
 *
 * @since 1.0
 * @uses get_page_link
 * @uses get_tag_link
 * @return url string
 */
function ji_redirect_get_logout_link( $value, $option ) {
        global $user;
        if( $option == 'categories' ){
        $uri = get_category_link( $value );
        $uri_link = apply_filters( 'ji_filter_logout_category_link' , $uri, $value, $user ); 
        return $uri_link;
        }
        //If ts a page we return the tag url
        elseif ( $option == 'page' ){
            $uri = get_page_link( $value );
            $uri_link = apply_filters( 'ji_filter_logout_page_link' , $uri, $value, $user ); 
            return $uri_link;
        }
                //If ts a tag we return the tag url
        elseif ( $option == 'tag' ){
            $uri = get_tag_link( $value );
            $uri_link = apply_filters( 'ji_filter_logout_tag_link' , $uri, $value, $user ); 
            return $uri_link;
        }
        elseif ( $option === 'custom' ){
            $url = esc_url($value);
            //next version.. $url = apply_filters( 'ji_filter_login_custom_link', $url, $value, $user );
            //wp_redirect($url);
        }
        else{
            $args = array();
            $args['page'] = 0;
            $args['categories'] = 0;
            $args['tag'] = 0;
            update_option( 'ji_redirect_logout', $args );

            $default_link = home_url();
            $default = apply_filters( 'ji_filter_default_logout_link' , $default_link, $user->ID ); 
            return $default;
        }
}





?>