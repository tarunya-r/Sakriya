<?php

/**
 * Registers all out settings and fields in the admin
 *
 * @since 1.0
 * 
 */
function ji_register_default_init(){
        add_settings_section('ji_setting_default','Set the default login link','ji_description_default','ji_redirect_default');
        
        register_setting(
                'ji_redirect_default',
                'ji_redirect_default',
                'ji_redirect_default_options'
                );
        add_settings_field('ji_setting_custom_select','Select a default custom url to redirect to.', 'ji_redirect_custom_default_select', 'ji_redirect_default', 'ji_setting_default' );
        add_settings_field('ji_setting_page_select','Select a default page to redirect to.', 'ji_redirect_page_default_select', 'ji_redirect_default', 'ji_setting_default' );
        add_settings_field('ji_setting_categories_select','Select a category to redirect to on logout', 'ji_redirect_category_default_select', 'ji_redirect_default', 'ji_setting_default' ); 
        add_settings_field('ji_setting_tag_select','Select a tag to redirect to on logout', 'ji_redirect_tag_default_select', 'ji_redirect_default', 'ji_setting_default' );  
}
add_action('admin_init','ji_register_default_init');



/**
 * Creates the logout options page
 *
 * @since 1.0
 * 
 */
function ji_redirect_display_default_page() {
        ?>
        <div class="wrap">
        <h2>Redirect Settings</h2>
        <br>
        <form action ="options.php" method="post">
        <?php            
        settings_fields( 'ji_redirect_default' );
        do_settings_sections( 'ji_redirect_default' );
        ji_display_selected_default_link();
        ?>    
        <input class='button-primary' type="submit" name="submit" value="<?php _e('Save Options')?>" />
        </form></div>
        <?php

}     


function ji_display_selected_default_link(){
        if ($options = get_option( 'ji_redirect_default' )){
            foreach ( $options as $option => $value ){
                //if the value does not equal -1 i.e it is the one selected..
                if ( $value != 0 && $option != 'custom' ){
                   $link = ji_redirect_get_logout_link( $value, $option );
                   printf('<p>For user roles where you have not selected a <a href ="%s">custom login redirect link</a>, users will be directed to:</p>
                        <div class="alert spb_content_element span8 alert-info"><a href ="%s" target="_blank">%s</a></div>', admin_url().'admin.php?page=jonimo', $link, $link);
                }
                elseif ($option == 'custom' && $value != ''){
                    $link = esc_url($value);
                    printf('<p>For user roles where you have not selected a <a href ="%s">custom login redirect link</a>, users will be directed to:</p>
                        <div class="alert spb_content_element span8 alert-info"><a href ="%s" target="_blank">%s</a></div>', admin_url().'admin.php?page=jonimo', $link, $link);
                   break;
                }
 
                elseif (array_sum($options) == 0){
                    printf('<p>For user roles where you have not selected a <a href ="%s">custom login redirect link</a>, users will be directed to:</p> 
                    <div class="alert spb_content_element span8 alert-info"><a href ="%s" target="_blank">%s</a></div>', admin_url().'admin.php?page=jonimo',  admin_url(), admin_url()); 
                break;
                }
            }
        }
         else {
            printf('<p>For user roles where you have not selected a <a href ="%s">custom login redirect link</a>, users will be directed to:</p> 
                <div class="alert spb_content_element span8 alert-info">%s</div>' ,admin_url().'admin.php?page=jonimo',  admin_url()); 
        }
}

/**
 * Returns a decription for the options page
 *
 * @since 1.0
 * 
 * @return string HTML description
 */
function ji_description_default(){
    echo'If you do not set a custom login redirect, users will be redirected to the deafult link. You can set the default link here.';
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
function ji_redirect_page_default_select(){?>
   <?php
   $options = get_option( 'ji_redirect_default' );
   $args = array(
        'echo'             => 1,
        'id' => 'ji_register_page',
        'selected' => $options['page'],
        'name'  => 'ji_redirect_default[page]',
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
function ji_redirect_category_default_select(){?>
   <?php
   $options = get_option( 'ji_redirect_default' );
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
	'name'               => 'ji_redirect_default[categories]',
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
function ji_redirect_tag_default_select(){?>
   <?php
   $options = get_option( 'ji_redirect_default' );
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
	'name'               => 'ji_redirect_default[tag]',
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
 * Redirect to custom page from default
 *
 * @since 1.4.1
 * 
 * @return tick box
 */
function ji_redirect_custom_default_select(){ 
            $options = get_option( 'ji_redirect_default');
            if (!isset($options['custom'])){
            $options = array('custom'=>'');
            }
            ?>            
            <input type="text"  id="ji_register_custom" name="<?php echo 'ji_redirect_default[custom]'?>" value="<?php if ( $options['custom'] != '' ) echo $options['custom']; ?>" size ="50">
            <?php
 }
 
 
/**
 * Saves and cleans the options
 *
 * @since 1.0
 * 
 * @return array of valid values
 */
function ji_redirect_default_options( $input ) {
    $valid = array();
    $valid['custom'] = $input['custom'];
    $valid['page'] = (int)$input['page'];
    $valid['categories'] = (int)$input['categories'];
    $valid['tag'] = (int)$input['tag'];
    return $valid;
}
  
?>