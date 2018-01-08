<?php

/**
 * Registers all out settings and fields in the admin
 *
 * @since 1.0
 * 
 */
function ji_register_logout_init(){
        add_settings_section('ji_setting_component','Redirect all users on logout','ji_description_logout','ji_redirect_logout');
        
        register_setting(
                'ji_redirect_logout',
                'ji_redirect_logout',
                'ji_redirect_logout_options'
                );
        add_settings_field( 'ji_setting_custom_page','Enter a redirect link manually. This can be to any internal or external url.', 'ji_redirect_to_custom_page_logout', 'ji_redirect_logout', 'ji_setting_component' );                 
        add_settings_field('ji_setting_page_select','Or, select a page to redirect to on logout', 'ji_redirect_page_logout_select', 'ji_redirect_logout', 'ji_setting_component' );
        add_settings_field('ji_setting_categories_select','Or, select a category to redirect to on logout', 'ji_redirect_category_logout_select', 'ji_redirect_logout', 'ji_setting_component' ); 
        add_settings_field('ji_setting_tag_select','Or, select a tag to redirect to on logout', 'ji_redirect_tag_logout_select', 'ji_redirect_logout', 'ji_setting_component' );  
}
add_action('admin_init','ji_register_logout_init');



/**
 * Creates the logout options page
 *
 * @since 1.0
 * 
 */
function ji_redirect_display_logout_page() {
        ?>
        <div class="wrap">
        <div id="icon-themes" class="icon32"></div>
        <h2>Redirect Settings</h2>
        <br>
        <form action ="options.php" method="post">
        <?php            
        settings_fields( 'ji_redirect_logout' );
        do_settings_sections( 'ji_redirect_logout' );
        ji_display_selected_logout_link();
        ?>    
        <input class='button-primary' type="submit" name="submit" value="<?php _e('Save Options')?>" />
        </form></div>
        <?php
}
      

/**
 * Returns a decription for the options page
 *
 * @since 1.0
 * 
 * @return string HTML description
 */
function ji_description_logout(){
    echo'Here you can choose direct users to a specific page, tag, category on logout.';
}



/**
 * Creates a drop down page select box.
 *
 * @since 1.0
 * @uses get_option
 * @uses wp_dropdown_pages
 * 
 * @return string HTML content only if 'echo' argument is 0.
 */
function ji_redirect_page_logout_select(){?>
   <?php
   $options = get_option( 'ji_redirect_logout' );
   $args = array(
        'echo'             => 1,
        'id' => 'ji_register_page',
        'selected' => $options['page'],
        'name'  => 'ji_redirect_logout[page]',
        'show_option_none' => 'None Selected',
        'option_none_value' => 0
        );
   wp_dropdown_pages( $args ); 
}

   
/**
 * Creates a drop down category select box
 *
 * @since 1.0
 * @uses get_option
 * @uses wp_dropdown_categories
 * 
 * @return string HTML content only if 'echo' argument is 0.
 */
function ji_redirect_category_logout_select(){?>
   <?php
   $options = get_option( 'ji_redirect_logout' );
   $args = array(
	'show_option_all'    => 'None Selected',
	'show_option_none'   => '',
	'orderby'            => 'ID', 
	'order'              => 'ASC',
	'show_count'         => 1,
	'hide_empty'         => 1, 
	'child_of'           => 0,
	'exclude'            => '',
	'echo'               => 1,
	'selected'           => $options['categories'],
	'hierarchical'       => 0, 
	'name'               => 'ji_redirect_logout[categories]',
	'id'                 => 'ji_register_cats',
	'class'              => '',
	'depth'              => 0,
	'tab_index'          => 0,
	'taxonomy'           => 'category',
	'hide_if_empty'      => false,
        'walker'             => ''
        );
   wp_dropdown_categories( $args );
}


/**
 * Creates a drop down tag select box
 *
 * @since 1.0
 * @uses get_option
 * @uses wp_dropdown_categories
 * 
 * @return string HTML content only if 'echo' argument is 0.
 */
function ji_redirect_tag_logout_select(){?>
   <?php
   $options = get_option( 'ji_redirect_logout' );
   $args = array(
	'show_option_all'    => 'None Selected',
	'show_option_none'   => '',
	'orderby'            => 'ID', 
	'order'              => 'ASC',
	'show_count'         => 1,
	'hide_empty'         => 1, 
	'child_of'           => 0,
	'exclude'            => '',
	'echo'               => 1,
	'selected'           => $options['tag'],
	'hierarchical'       => 0, 
	'name'               => 'ji_redirect_logout[tag]',
	'id'                 => 'ji_register_tag',
	'class'              => 'postform',
	'depth'              => 0,
	'tab_index'          => 0,
	'taxonomy'           => 'post_tag',
	'hide_if_empty'      => false,
        'walker'             => ''
        );
   wp_dropdown_categories( $args );
}


/**
 * Redirect to custom page
 *
 * @since 1.4
 * 
 * @return tick box
 */
function ji_redirect_to_custom_page_logout(){ 
    
            $options = get_option( 'ji_redirect_logout' );
            if (!isset($options['custom'])){
            $options = array('custom'=>'');
            }
            ?>            
            <input type="text"  id="ji_register_custom" name="<?php echo 'ji_redirect_logout[custom]'?>" value="<?php if ( $options['custom'] != '' ) echo $options['custom']; ?>"  size ="50">
            <?php
  }
  

  /**
 * displays where we are going to be redirected to on save.
 * 
 * @since 1.0
 * 
 * @return array of cleaned values. 
 */
function ji_display_selected_logout_link() {
    global $user;
    //we get the options 
    $options = get_option( 'ji_redirect_logout'); 
    if ( $options ){        
        foreach ( $options as $option => $value ){
                //if we are dealing with a custom link
              if (isset($options['custom']) && $options['custom'] != ''){
                        // add our custom link... 
                        $url = $options['custom'];
                        $link = esc_url($url);
                        //next version $customlink = ji_redirect_custom_link_addon ( $link, $value ='', $user );  
                        $message = ji_redirect_write_logout_message($options, $link);
                        $message = apply_filters( 'ji_filter_write_logout_message' , $options, $link);
                        break; 
             }
            
            //if we are dealing with a link generated from a dropdown (other than limit).
            if ($value != 0 && $option != 'limit' ){
                        $link = ji_redirect_get_logout_link($value, $option);
                        //if we succesffuly return a link..
                        if ($link != null ){ 
                        $link = esc_url( $link );
                            $message = ji_redirect_write_logout_message($options, $link);
                            $message = apply_filters( 'ji_filter_write_logout_message' , $options, $link);
                            break; 
                        }

                        //if we dont return a link (despite a value being passed to ji_redirect_get_login_link
                        else {
                             printf('<p>Select one of the options above to redirect users with the role %s to on login.<p>', $role_name);
                        }
            }    
            
         }
    //We want the limit to always be 0, otherwise it screws up our display logic. 
    $options['limit'] = 0;
    if (array_sum( $options) == 0 && (!isset($options['custom']) || $options['custom'] == '')){
        printf( '<p>Select one of the options above to redirect users to on logout. 
        <br>If no option is set these users will be directed to <a href ="%s" target="_blank">your site homepage</a><p>', get_home_url()); 
    }
    }
    //if there are no options set... 
    else {
        printf( '<p>Select one of the options above to redirect users to on login. 
        <br>If no option is set these users will be directed to <a href ="%s" target="_blank">your site homepage</a><p>', get_home_url()); 
        
    }
}
  
/**
 * Saves and cleans the options
 *
 * @since 1.0
 * 
 * @return array of valid values
 */
function ji_redirect_logout_options( $input ) {
    $valid = array();
    $valid['custom'] = esc_url($input['custom']);
    $valid['page'] = (int)$input['page'];
    $valid['categories'] = (int)$input['categories'];
    $valid['tag'] = (int)$input['tag'];
    return $valid;
}
  

?>