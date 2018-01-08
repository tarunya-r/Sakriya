<?php
/**
 * Hooks into our custom hook ji_redirect_default_addon
 * @returns $link as string. 
 * 
 * @since 1.2
*/
function ji_redirect_default_addon ( $default_link, $user_id ){
    //if the default link is the admin_url() path 
    if ($default_link == admin_url()){ 
         if ($options = get_option( 'ji_redirect_default' )){
            foreach ( $options as $option => $value ){
                //if the value does not equal -1 i.e it is the one selected..
                if ( $value != 0 && $option != 'custom' ){
                   $link = ji_redirect_get_logout_link( $value, $option );
                   return $link;
                   break;
                }
                elseif ($option == 'custom' && $value != ''){
                   $link = esc_url($value);
                   return $link;
                   break;
                }
            }
        }
        else {
          return admin_url(); 
        }
   }
}
add_filter( "ji_filter_default_link","ji_redirect_default_addon",100, 2 );  
    
/**
 * @returns string message for the set deafult link. 
 * @uses get_option()
 * @uses admin_url()
 * @uses ji_redirect_get_login_link()
 * @since 1.2
*/
function ji_redirect_get_default_link($user){
               if ($options = get_option( 'ji_redirect_default' )){
            foreach ( $options as $option => $value ){
                
                if ( $value != 0 && $option != 'custom' ){
                   $link = return_default_link_as_string();
                     printf('<p>For user roles where you have not selected a <a href ="%s">custom login redirect link</a>, users will be directed to:</p>
                     <div class="alert spb_content_element span8 alert-info"><a href ="%s" target="_blank">%s</a></div>', admin_url().'admin.php?page=jonimo', $link, $link);
                     break;
                   echo 'arse';
                }
                elseif ($option == 'custom' && $value != ''){
                    $link = esc_url($value);
                    printf('<p>For user roles where you have not selected a <a href ="%s">custom login redirect link</a>, users will be directed to:</p>
                        <div class="alert spb_content_element span8 alert-info"><a href ="%s" target="_blank">%s</a></div>', admin_url().'admin.php?page=jonimo', $link, $link);
                   break;
                }
            }
        }
         else {
            printf('<p>For user roles where you have not selected a <a href ="%s">custom login redirect link</a>, users will be directed to:</p> 
                <div class="alert spb_content_element span8 alert-info"><a href ="%s" target="_blank">%s</a></div>' ,admin_url().'admin.php?page=jonimo',  admin_url(), admin_url()); 
        }
}


function return_default_link_as_string(){
        if ($options = get_option( 'ji_redirect_default' )){
            foreach ( $options as $option => $value ){
                //if the value does not equal -1 i.e it is the one selected..
                if ( $value != 0 && $option != 'custom' ){
                   $link = ji_redirect_get_logout_link( $value, $option );
                   return $link;
                   break;
                }
                elseif ($option == 'custom' && $value != ''){
                   $link = esc_url($value);
                   return $link;
                   break;
                }
            }
        }
        else {
          return admin_url(); 
        }
}
?>