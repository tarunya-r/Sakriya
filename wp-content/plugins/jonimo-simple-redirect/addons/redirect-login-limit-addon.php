<?php
/**
 * Hooks into our custom hook ji_redirect_login_link and redirects the user a set number of times. 
 * If this decreases to 0, the user is redirected to the default link. 
 * 
 * @returns $link as string. 
 * 
 * @since 1.3
*/

function ji_redirect_login_limit( $link, $user, $role ) {
        //We get the number of times the specific user on a specific site still has to be redirected (for that role)
        $option = (int)get_user_option( 'ji_redirect_limit_'.$role, $user->ID );
        //if we still have some redirects left to perform
        if ( $option > 0 && $option < 10 ) {
            //we knock off one of the redirects,
            $newoption = $option - 1;
            //update the options for that user to reflect the changes
            update_user_option($user->ID , 'ji_redirect_limit_'.$role,  $newoption);
            //and return the custom redirect link unchanged.
            return $link;
        }
        elseif ($option == 10) {
            //option is set to always
            return $link;
        }
        elseif ($option == 0){
            // if the user option is now at 0
            $default_link = admin_url();
            $default = apply_filters( 'ji_filter_default_link' , $default_link, $user->ID ); 
            return  $default;
        }
}

add_filter( "ji_filter_login_link","ji_redirect_login_limit",100, 3 );  





/**
 * Gets a properly formulated message for the limit message
 *
 * @since 1.3
 */
function ji_redirect_write_login_message( array $options, $link, $role_name) {
     
            if (($options['limit'] == 10)){
                 printf('<p>On login any user with the role %s will <strong>always</strong> be redirected to:</p>
                     <div class="alert spb_content_element span8 alert-success"><a href ="%s" target="_blank" >%s<a/></div>',$role_name ,$link, $link);
            }
            else {  
              printf('<p>On login any user with the role %s will be redirected <strong> %d time(s)</strong> to: 
                   <div class="alert spb_content_element span8 alert-success"><a href ="%s" target="_blank" >%s</a></div>
                   <p>After this, they will be redirected to the <a href ="%s">default login link</a> below:</p>
                   <div class="alert spb_content_element span8 alert-info"><a href ="%s" target="_blank" >%s<a/></div>', $role_name, $options['limit'], $link, $link, admin_url().'admin.php?page=default', return_default_link_as_string(), return_default_link_as_string());                  
            
               
            }          
}




/**
 * Gets a properly formulated message for the limit message
 *
 * @since 1.3
 */
function ji_redirect_write_logout_message( array $options, $link) {
       printf('<p>On logout all users will be redirected to:</p>
       <div class="alert spb_content_element span8 alert-success"><a href ="%s" target="_blank" >%s<a/></div>', $link, $link);
}

?>