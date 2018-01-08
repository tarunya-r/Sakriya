<?php

/**
 * Registers all out settings and fields in the admin
 *
 * @since 1.0
 * 
 */
function ji_register_admin_init() {
    add_settings_section('ji_setting_component', '', 'ji_description', 'ji_redirect');

    $wp_roles = new WP_Roles();
    $roles = $wp_roles->get_names();

    foreach ($roles as $role_value => $role_name) {
        //we register a setting for every role
        register_setting('ji_register_' . $role_value, 'ji_register_' . $role_value, 'ji_register_validate_options');
        add_settings_field('ji_setting_custom_page', 'Enter a redirect link manually. This can be to any internal or external url.', 'ji_redirect_to_custom_page', 'ji_redirect', 'ji_setting_component');
        add_settings_field('ji_setting_page_select', 'Or, select a page to redirect to on login', 'ji_redirect_page_select', 'ji_redirect', 'ji_setting_component');
        add_settings_field('ji_setting_category_select', 'Or select a category to redirect to on login', 'ji_redirect_category_select', 'ji_redirect', 'ji_setting_component');
        add_settings_field('ji_setting_tag_select', 'Or select a tag to redirect to on login', 'ji_redirect_tag_select', 'ji_redirect', 'ji_setting_component');

        if (function_exists('bp_is_active')) {
            add_settings_field('ji_setting_bp_select', 'Select a buddypress specific page to redirect to on login', 'ji_redirect_bp_select', 'ji_redirect', 'ji_setting_component');
        };
        add_settings_field('ji_setting_limit_select', 'Select the number of times you wish to redirect users with this role to your custom location.', 'ji_redirect_limit_select', 'ji_redirect', 'ji_setting_component');
    }
}

add_action('admin_init', 'ji_register_admin_init'); // register the admin

/**
 * Creates the login options page
 *
 * @since 1.0
 * 
 */
function ji_redirect_display_settings_page() {
    ji_redirect_display_about();
    ?>
    <div class="wrap">
        <h2>Redirect Settings</h2>
        <br>
        <h3>Select a role from a tab below</h3>
    <?php
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : ji_get_default_role();
    $wp_roles = new WP_Roles();
    $roles = $wp_roles->get_names();
    ?>
        <h2 class="nav-tab-wrapper"> 
        <?php
        foreach ($roles as $role_value => $role_name) {
            ?>
                <a href="?page=jonimo&tab=<?php echo $role_value ?>" class="nav-tab <?php echo $active_tab == $role_value ? 'nav-tab-active' : 'Owner'; ?>"><?php echo $role_name ?></a>  
            <?php } ?>
        </h2>
        <br>
            <?php
            //loop through the setting field contents 
            foreach ($roles as $role_value => $role_name) {
                if ($role_value == $active_tab) {
                    ?>
                <form action ="options.php" method="post">
                    <h3>Redirect users with the role '<?php echo $role_name ?>' on login</h3>
                <?php
                settings_fields('ji_register_' . $role_value);
                do_settings_sections('ji_redirect');
                ///pass role name to new selected link 
                ji_display_selected_link($role_name, $role_value);
                ?>
                    <br>
                    <input class='button-primary' type="submit" name="submit" value="<?php _e('Save Options') ?>" />
                </form>
            </div>
        <?php
        }
    }
}

/**
 * Returns a decription for the options page
 * (
 * @since 1.0
 * 
 * @return string HTML description
 */
function ji_description() {
    printf(_e('Direct users to a specific page, tag or category on login.<br> 
If you have buddypress installed, you can direct users to range of profile pages on login.'));
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
function ji_redirect_page_select() {
    //set the active as administrator if no other tab setting is indiacted in the GET array.
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : ji_get_default_role();
    $wp_roles = new WP_Roles();
    $roles = $wp_roles->get_names();
    foreach ($roles as $role_value => $role_name) {
        //lets use the roles to print out the right options   
        if ($role_value === $active_tab) {
            $options = get_option('ji_register_' . $role_value);
            if (!isset($options['page'])) {
                $options = array('page' => '');
            }
            $args = array(
                'echo' => 1,
                'id' => 'ji_register_page',
                'selected' => $options['page'],
                'name' => 'ji_register_' . $role_value . '[page]',
                'show_option_none' => 'None Selected',
                'option_none_value' => '0');
            wp_dropdown_pages($args);
            

        }
    }
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
function ji_redirect_category_select() {
    ?>
    <?php
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : ji_get_default_role();
    $wp_roles = new WP_Roles();
    $roles = $wp_roles->get_names();

    foreach ($roles as $role_value => $role_name) {
        //lets use the roles to print out the right options    
        if ($role_value === $active_tab) {
            $options = get_option('ji_register_' . $role_value);
            if (!isset($options['categories'])) {
                $options = array('categories' => '');
            }
            $args = array(
                'show_option_all' => 'None Selected',
                'show_option_none' => '',
                'orderby' => 'ID',
                'order' => 'ASC',
                'show_count' => 1,
                'hide_empty' => 1,
                'child_of' => 0,
                'exclude' => '',
                'echo' => 1,
                'selected' => $options['categories'],
                'hierarchical' => 0,
                'name' => 'ji_register_' . $role_value . '[categories]',
                'id' => 'ji_register_cats',
                'class' => '',
                'depth' => 0,
                'tab_index' => 0,
                'taxonomy' => 'category',
                'hide_if_empty' => false,
                'walker' => ''
            );
            wp_dropdown_categories($args);
        }
    }
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
function ji_redirect_tag_select() {
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : ji_get_default_role();
    $wp_roles = new WP_Roles();
    $roles = $wp_roles->get_names();

    foreach ($roles as $role_value => $role_name) {
        //lets use the roles to print out the right options for the right tabs        
        if ($role_value === $active_tab) {
            $options = get_option('ji_register_' . $role_value);
            if (!isset($options['tag'])) {
                $options = array('tag' => '');
            }
            $args = array(
                'show_option_all' => 'None Selected',
                'show_option_none' => '',
                'orderby' => 'ID',
                'order' => 'ASC',
                'show_count' => 1,
                'hide_empty' => 1,
                'child_of' => 0,
                'exclude' => '',
                'echo' => 1,
                'selected' => $options['tag'],
                'hierarchical' => 0,
                'name' => 'ji_register_' . $role_value . '[tag]',
                'id' => 'ji_register_tag',
                'class' => 'postform',
                'depth' => 0,
                'tab_index' => 0,
                'taxonomy' => 'post_tag',
                'hide_if_empty' => false,
                'walker' => ''
            );
            wp_dropdown_categories($args);
        }
    }
}

/**
 * Redirect to custom page
 *
 * @since 1.4
 * 
 * @return tick box
 */
function ji_redirect_to_custom_page() {
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : ji_get_default_role();
    $wp_roles = new WP_Roles();
    $roles = $wp_roles->get_names();

    foreach ($roles as $role_value => $role_name) {
        //lets use the roles to print out the right options for the right tabs        
        if ($role_value === $active_tab) {
            $options = get_option('ji_register_' . $role_value);
            if (!isset($options['custom'])) {
                $options = array('custom' => '');
            }
            ?>            
            <input type="text"  id="ji_register_custom" name="<?php echo 'ji_register_' . $role_value . '[custom]' ?>" value="<?php if ($options['custom'] != '') echo $options['custom']; ?>" size ="50">
            <?php
        }
    }
}

/**
 * Creates a drop down select box for various buddypress components.
 * 
 * @since 1.0
 * @uses get_option
 * @uses wp_dropdown_categories
 * 
 * @return string HTML select box
 */
function ji_redirect_bp_select() {
    ?>
    <?php
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : ji_get_default_role();
    //Lets get the roles
    $wp_roles = new WP_Roles();
    $roles = $wp_roles->get_names();

    foreach ($roles as $role_value => $role_name) {
        //lets use the roles to print out the right options    
        if ($role_value === $active_tab) {
            $options = array('bp' => null);
            $options = get_option('ji_register_' . $role_value);
            ?>
            <select name= "<?php echo 'ji_register_' . $role_value . '[bp]'; ?>" id ="ji_register_bp">
                <option value="0" <?php if ($options['bp'] == 0) echo 'selected="selected"'; ?>>None Selected</option>   
                <option value="1" <?php if ($options['bp'] == 1) echo 'selected="selected"'; ?>>User profile page</option>
            <?php if (bp_is_active('activity')) { ?>
                    <option value="2" <?php if ($options['bp'] == 2) echo 'selected="selected"'; ?>>Site wide activity</option>
            <?php } ?>
            <?php if (bp_is_active('friends')) { ?>
                    <option value="3" <?php if ($options['bp'] == 3) echo 'selected="selected"'; ?>>Friends Activity</option>
            <?php } ?>
                <option value="4" <?php if ($options['bp'] == 4) echo 'selected="selected"'; ?>>User profile edit</option>
            </select>
            <?php
        }
    }
}

/**
 * Creates a drop down select box where you can submit values from 1-9
 *
 * @since 1.3
 * @uses get_option
 * 
 * @return string HTML content only if 'echo' argument is 0.
 */
function ji_redirect_limit_select() {
    global $user;
    //set the active as administrator if no other tab setting is indiacted in the GET array.
    $active_tab = isset($_GET['tab']) ? $_GET['tab'] : ji_get_default_role();
    $wp_roles = new WP_Roles();
    $roles = $wp_roles->get_names();

    foreach ($roles as $role_value => $role_name) {
        //lets use the roles to print out the right options   
        if ($role_value === $active_tab) {
            $options = get_option('ji_register_' . $role_value);
            if (!isset($options['limit'])) {
                $options = array('limit' => 0);
            }
            ?>
            <select name= "<?php echo 'ji_register_' . $role_value . '[limit]'; ?>" id ="ji_register_limit" >
                <option value="10" <?php if ($options['limit'] == 10) echo 'selected="selected"'; ?>>Always</option>   
                <option value="1" <?php if ($options['limit'] == 1) echo 'selected="selected"'; ?>>1</option>
                <option value="2" <?php if ($options['limit'] == 2) echo 'selected="selected"'; ?>>2</option>
                <option value="3" <?php if ($options['limit'] == 3) echo 'selected="selected"'; ?>>3</option>  
                <option value="4" <?php if ($options['limit'] == 4) echo 'selected="selected"'; ?>>4</option>
                <option value="5" <?php if ($options['limit'] == 5) echo 'selected="selected"'; ?>>5</option>
                <option value="6" <?php if ($options['limit'] == 6) echo 'selected="selected"'; ?>>6</option>
                <option value="7" <?php if ($options['limit'] == 7) echo 'selected="selected"'; ?>>7</option>  
                <option value="8" <?php if ($options['limit'] == 8) echo 'selected="selected"'; ?>>8</option>
                <option value="9" <?php if ($options['limit'] == 9) echo 'selected="selected"'; ?>>9</option>
            </select>
            <?php
            //if the 
            if (isset($options['limit'])) {
                $role_value = strtolower($role_value);
                //get the users from the blog with the current role
                $blogusers = get_users('blog_id=1&role=' . $role_value . '');
                if (isset($blogusers)) {
                    //then add the limit for each of the users. 
                    foreach ($blogusers as $user) {
                        update_user_option($user->ID, 'ji_redirect_limit_' . $role_value, $options['limit']);
                    }
                }
            }
        }
    }
}

/**
 * Cleans our inputs to make sure they are integers. 
 * 
 * @since 1.0
 * 
 * @return array of cleaned values. 
 */
function ji_register_validate_options($input) {
    //lets clean up the input variables and make sure they are integers and place in an array
    $valid = array();
    $valid['custom'] = esc_url($input['custom']);
    $valid['page'] = (int) $input['page'];
    $valid['categories'] = (int) $input['categories'];
    $valid['tag'] = (int) $input['tag'];
    $valid['bp'] = (int) $input['bp'];
    $valid['limit'] = (int) $input['limit'];
    return $valid;
}

/**
 * displays where we are going to be redirected to on save.
 * 
 * @since 1.0
 * 
 * @return array of cleaned values. 
 */
function ji_display_selected_link($role_name, $role_value) {
    global $user;
    //we get the options 
    $options = get_option('ji_register_' . $role_value);
    if ($options) {
        foreach ($options as $option => $value) {
            //if we are dealing with a custom link
            if (isset($options['custom']) && $options['custom'] != '') {
                // add our custom link... 
                $url = $options['custom'];
                $link = esc_url($url);
                //next version $customlink = ji_redirect_custom_link_addon ( $link, $value ='', $user );  
                $message = ji_redirect_write_login_message($options, $link, $role_name);
                $message = apply_filters('ji_filter_write_login_message', $options, $link, $role_name);
                break;
            }

            //if we are dealing with a link generated from a dropdown (other than limit).
            if ($value != 0 && $option != 'limit') {
                $link = ji_redirect_get_login_link($redirect_url = null, $request_url = null, $user, $value, $option, $role_name);
                //if we succesffuly return a link..
                if ($link != null) {
                    $link = esc_url($link);
                    $message = ji_redirect_write_login_message($options, $link, $role_name);
                    $message = apply_filters('ji_filter_write_login_message', $options, $link, $role_name);
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
        if (array_sum($options) == 0 && (!isset($options['custom']) || $options['custom'] == '')) {
            //if all aptions are 0
            printf('<p>Select one of the options above to redirect users with the role of %s to on login. 
            <br>If no option is set these users will be directed to <a href ="%s">default link</a> below.<p>
            <div class="alert spb_content_element span8 alert-info">%s</div>', $role_name, admin_url('admin.php?page=default'), return_default_link_as_string());
        }
    }
    //if there are no options set... 
    else {
            printf('<p>Select one of the options above to redirect users with the role of %s to on login. 
            <br>If no option is set these users will be directed to <a href ="%s">default link</a> below.<p>
            <div class="alert spb_content_element span8 alert-info">%s</div>', $role_name, admin_url('admin.php?page=default'), return_default_link_as_string());
    }
}
?>