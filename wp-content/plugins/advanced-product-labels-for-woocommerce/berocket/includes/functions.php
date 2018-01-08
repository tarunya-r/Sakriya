<?php
if( ! function_exists( 'br_get_woocommerce_version' ) ){
    /**
     * Public function to get WooCommerce version
     *
     * @return float|NULL
     */
    function br_get_woocommerce_version() {
        if ( ! function_exists( 'get_plugins' ) )
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
        $plugin_folder = get_plugins( '/woocommerce' );
        $plugin_file = 'woocommerce.php';

        if ( isset( $plugin_folder[$plugin_file]['Version'] ) ) {
            return $plugin_folder[$plugin_file]['Version'];
        }

        return NULL;
    }
}

if( ! function_exists( 'br_woocommerce_version_check' ) ){
    function br_woocommerce_version_check( $version = '2.7' ) {
        $wc_version = br_get_woocommerce_version();
		if( $wc_version !== NULL ) {
			if( version_compare( $wc_version, $version, ">=" ) ) {
                return true;
            }
        }
        return false;
    }
}

if( ! function_exists( 'br_wc_get_product_id' ) ){
    function br_wc_get_product_id($product) {
        if( method_exists($product, 'get_id') ) {
            return $product->get_id();
        } else {
            return @ $product->id;
        }
    }
}

if( ! function_exists( 'br_wc_get_product_post' ) ){
    function br_wc_get_product_post($product) {
        if( method_exists($product, 'get_post') ) {
            return $product->get_post();
        } elseif( method_exists($product, 'get_id') && br_woocommerce_version_check() ) {
            return get_post( $product->get_id() );
         } else {
            return @ $product->post;
        }
    }
}

if( ! function_exists( 'br_wc_get_product_attr' ) ){
    function br_wc_get_product_attr($product, $attr, $data = '') {
        switch($attr) {
            case 'child':
                if( function_exists('wc_get_product') ) {
                    $return = wc_get_product($data);
                } else {
                    $return = $product->get_child($data);
                }
                break;
            default:
                if( method_exists($product, 'get_'.$attr) ) {
                    $return = $product->{'get_'.$attr}('edit');
                } else {
                    $return = $product->{$attr};
                }
                break;
        }
        return $return;
    }
}

if( ! function_exists( 'br_is_plugin_active' ) ) {
    /**
     * Public function to add to plugin settings buttons to upload or select icons
     *
     * @var $plugin_name - should be class name without BeRocket_ part
     *
     * @return boolean
     */
    function br_is_plugin_active( $plugin_name, $version = '1.0.0.0', $version_end = '9.9.9.9' ) {
        if ( defined( "BeRocket_" . $plugin_name . "_version" ) &&
             constant( "BeRocket_" . $plugin_name . "_version" ) >= $version &&
             constant( "BeRocket_" . $plugin_name . "_version" ) <= $version_end
        ) {
            return true;
        }

        return false;
    }
}

if( ! function_exists( 'berocket_insert_to_array' ) ) {
    /**
     * Public function to select color
     *
     * @param array $array - array with options
     * @param string $key_in_array - key in array where additional options must be added
     * @param array $array_to_insert - array with additional options
     * @param boolean $before - insert additional options before option with key $key_in_array
     *
     * @return string html code with all needed blocks and buttons
     */
    function berocket_insert_to_array($array, $key_in_array, $array_to_insert, $before = false) {
        $position = array_search($key_in_array, array_keys($array), true);
        if( $position !== FALSE ) {
            if( ! $before ) {
                $position++;
            }
            $array = array_merge(array_slice($array, 0, $position, true),
                                $array_to_insert,
                                array_slice($array, $position, NULL, true));
        }
        return $array;
    }
}

if( ! function_exists( 'br_color_picker' ) ) {
    /**
     * Public function to select color
     *
     * @param string $name - input name
     * @param string $value - current value color
     * @param string $default - default value color
     * @param array $additional - array with additional settings array:
     *  boolean default_button - display button to set color to default value
     *  string class - additional classes for input with color value
     *  string extra - need to add data-something="5"? use this field. It will add data as is
     *
     * @return string html code with all needed blocks and buttons
     */
    function br_color_picker($name, $value, $default, $additional = array()) {
        $default_button = ( isset($additional['default_button']) ? $additional['default_button'] : true );
        $class = ( ( isset($additional['class']) && trim( $additional['class'] ) ) ? ' ' . trim( $additional['class'] ) : '' );
        $extra = ( ( isset($additional['extra']) && trim( $additional['extra'] ) ) ? ' ' . trim( $additional['extra'] ) : '' );
        $value = ( empty($value) ? '#000000' : ( $value[0] == '#' ? $value : '#' . $value ) );
        $default = ( isset($default) && strlen($default) > 1 ? ( $default[0] == '#' ? $default : '#' . $default ) : '#000000' );
        $return = '';
        $return .= '<div class="berocket_color"><div class="br_colorpicker" data-default="' . $default . '" data-color="' . $value . '" style="background-color:' . $value . ';"></div>
            <input class="br_colorpicker_value' . $class . '" type="hidden" value="' . $value . '" name="' . $name . '"' . $extra . '/>';
        if( $default_button ) {
            $return .= '<input type="button" value="' . __('Default', 'BeRocket_domain') . '" class="br_colorpicker_default button">';
        }
        $return .= '</div>';
        return $return;
    }
}

if ( ! function_exists( 'br_upload_image' ) ) {
    /**
     * Public function to upload images or select it from media library
     *
     * @param string $name - input name
     * @param string $value - current value link to image
     * @param array $additional - array with additional settings array:
     *  boolean remove_button - display button to remove image
     *  string class - additional classes for input with image value link
     *  string extra - need to add data-something="5"? use this field. It will add data as is
     *
     * @return string html code with all needed blocks and buttons
     */
    function br_upload_image( $name, $value, $additional = array() ) {
        $remove_button = ( isset($additional['remove_button']) ? $additional['remove_button'] : true );
        $class = ( ( isset($additional['class']) && trim( $additional['class'] ) ) ? ' ' . trim( $additional['class'] ) : '' );
        $extra = ( ( isset($additional['extra']) && trim( $additional['extra'] ) ) ? ' ' . trim( $additional['extra'] ) : '' );
        $result = '<div>';
        $result .= '<input type="hidden" name="' . $name . '" value="' . $value . '" readonly class="berocket_image_value ' . $class . '"' . $extra . '/>
        <span class="berocket_selected_image">' . ( empty($value) ? '' : '<image src="' . $value . '">' ) . '</span>
        <input type="button" class="berocket_upload_image button" value="'.__('Upload', 'BeRocket_domain').'"/> ';
        if ( $remove_button ) {
            $result .= '<input type="button" class="berocket_remove_image button" value="'.__('Remove', 'BeRocket_domain').'"/>';
        }
        $result .= '</div>';

        return $result;
    }
}

if ( ! function_exists( 'berocket_fa_dark' ) ) {
    function berocket_fa_dark () {
        global $berocket_fa_dark;
        $result = '';
        if( empty($berocket_fa_dark) ) {
            $fa_icons = fa_icons_list();
            $result = '<div class="berocket_fa_dark"><div class="berocket_fa_popup">
            <input type="text" class="berocket_fa_search"><span class="berocket_fa_close"><i class="fa fa-times"></i></span>
            <div class="berocket_fa_list">';
            foreach($fa_icons as $fa_icon) {
                $result .= '<span class="berocket_fa_icon"><span class="berocket_fa_hover"></span><span class="berocket_fa_preview"><i class="fa ' . $fa_icon . '"></i><span>' . $fa_icon . '</span></span></span>';
            }
            $result .= '</div></div></div>';
            $berocket_fa_dark = true;
        }
        return $result;
    }
}

if ( ! function_exists( 'br_fontawesome_image' ) ) {
    /**
     * Public function to upload images or select it from media library
     *
     * @param string $name - input name
     * @param string $value - current value link to image
     * @param array $additional - array with additional settings array:
     *  boolean remove_button - display button to remove image
     *  string class - additional classes for input with image value link
     *  string extra - need to add data-something="5"? use this field. It will add data as is
     *
     * @return string html code with all needed blocks and buttons
     */
    function br_fontawesome_image( $name, $value, $additional = array() ) {
        $remove_button = ( isset($additional['remove_button']) ? $additional['remove_button'] : true );
        $class = ( ( isset($additional['class']) && trim( $additional['class'] ) ) ? ' ' . trim( $additional['class'] ) : '' );
        $extra = ( ( isset($additional['extra']) && trim( $additional['extra'] ) ) ? ' ' . trim( $additional['extra'] ) : '' );
        $result = '<div class="berocket_select_fontawesome berocket_select_image">';
        $result .= berocket_fa_dark();
        $result .= '<input type="hidden" name="' . $name . '" value="' . $value . '" readonly class="berocket_image_value berocket_fa_value ' . $class . '"' . $extra . '/>
        <span class="berocket_selected_image berocket_selected_fa">' . ( empty($value) ? '' : (substr($value, 0, 3) == 'fa-' ? '<i class="fa ' . $value . '"></i>' : '<image src="' . $value . '">' ) ) . '</span>
        <input type="button" class="berocket_upload_image button" value="'.__('Upload', 'BeRocket_domain').'"/>
        <input type="button" class="berocket_select_fa button" value="'.__('Font Awesome', 'BeRocket_domain').'"/>';
        if ( $remove_button ) {
            $result .= '<input type="button" class="berocket_remove_image button" value="'.__('Remove', 'BeRocket_domain').'"/>';
        }
        $result .= '</div>';

        return $result;
    }
}

if ( ! function_exists( 'br_select_fontawesome' ) ) {
    /**
     * Public function to upload images or select it from media library
     *
     * @param string $name - input name
     * @param string $value - current value FA icon
     * @param array $additional - array with additional settings array:
     *  boolean remove_button - display button to remove image
     *  string class - additional classes for input with image value link
     *  string extra - need to add data-something="5"? use this field. It will add data as is
     *
     * @return string html code with all needed blocks and buttons
     */
    function br_select_fontawesome( $name, $value, $additional = array() ) {
        $remove_button = ( isset($additional['remove_button']) ? $additional['remove_button'] : true );
        $class = ( ( isset($additional['class']) && trim( $additional['class'] ) ) ? ' ' . trim( $additional['class'] ) : '' );
        $extra = ( ( isset($additional['extra']) && trim( $additional['extra'] ) ) ? ' ' . trim( $additional['extra'] ) : '' );
        $result = '<div class="berocket_select_fontawesome">';
        $result .= berocket_fa_dark();
        $result .= '<input type="hidden" name="' . $name . '" value="' . $value . '" readonly class="berocket_fa_value ' . $class . '"' . $extra . '/>
        <span class="berocket_selected_fa">' . ( empty($value) ? '' : '<i class="fa ' . $value . '"></i>' ) . '</span>
        <input type="button" class="berocket_select_fa button" value="'.__('Font Awesome', 'BeRocket_domain').'"/> ';
        if ( $remove_button ) {
            $result .= '<input type="button" class="berocket_remove_fa button" value="'.__('Remove', 'BeRocket_domain').'"/>';
        }
        $result .= '</div>';

        return $result;
    }
}

if( ! function_exists( 'br_products_selector' ) ) {
    function br_products_selector($name, $value, $additional = array()) {
        $multiple = ( isset($additional['multiple']) ? $additional['multiple'] : true );
        $class = ( ( isset($additional['class']) && trim( $additional['class'] ) ) ? ' ' . trim( $additional['class'] ) : '' );
        $extra = ( ( isset($additional['extra']) && trim( $additional['extra'] ) ) ? ' ' . trim( $additional['extra'] ) : '' );
        if ( $class ) {
            $class = " class='" . $class . "'";
        }
        if( $multiple ) {
            $name = $name . '[]';
        }
        $value = ( ! empty($value) ? ( is_array($value) ? $value : array($value) ) : array() );
        $html = '<div class="berocket_search_box' . ( empty($multiple) ? ' single_product' : '' ) . '" data-name="' . $name . '">
            <ul class="berocket_products_search"' . $class . $extra . '>';
                $product_objects = array_filter( array_map( 'wc_get_product', $value ), 'berocket_array_filter_editable' );
                foreach ( $product_objects as $product_object ) {
                    $html .= '<li class="berocket_product_selected button"><input name="' . $name . '" type="hidden" value="' . $product_object->get_id() . '"><i class="fa fa-times"></i> ' . $product_object->get_formatted_name() . '</li>';
                }
                $html .= '<li class="berocket_product_search"' . ( ! $multiple && count($product_objects) > 0 ? ' style="display: none;"' : '' ) . '><input type="text" data-action="woocommerce_json_search_products_and_variations" class="berocket_search_input" placeholder="'.__('Enter 3 or more characters', 'BeRocket_domain').'"></li>
            </ul>
        </div>';
        return $html;
    }
}

if ( ! function_exists( 'berocket_array_filter_editable' ) ) {
    function berocket_array_filter_editable( $product ) {
        return $product && is_a( $product, 'WC_Product' ) && current_user_can( 'edit_product', $product->get_id() );
    }
}

if ( ! function_exists( 'berocket_font_select_upload' ) ) {
    /**
     * Public function to add to plugin settings buttons to upload or select icons
     *
     * @param string $text - Text above buttons
     * @param string $id - input ID
     * @param string $name - input name
     * @param string $value - current value link or font awesome icon class
     * @param bolean $show_fa - show font awesome button and generate font awesome icon table
     * @param bolean $show_upload - show upload button that allow upload custom icons
     * @param bolean $show_remove - show remove button that allow clear input
     * @param string $data_sc - add data-sc options with this value into input 
     *
     * @return string html code with all needed blocks and buttons
     */
    function berocket_font_select_upload( $text, $id, $name, $value, $show_fa = true, $show_upload = true, $show_remove = true, $data_sc = '' ) {
        if ( $show_fa ) {
            $font_awesome_list = fa_icons_list();
            $font_awesome      = "";
            foreach ( $font_awesome_list as $font ) {
                $font_awesome .= '<label><span data-value="' . $font . '" 
                    class="berocket_aapf_icon_select"></span><i class="fa ' . $font . '"></i></label>';
            }
        }
        $result = '<div>';
        if ( $text && $text != '' ) {
            $result .= '<p>' . $text . '</p>';
        }
        $result .= '<input id="' . $id . '" type="text" name="' . $name . '" '.( ( $data_sc ) ? 'data-sc="'.$data_sc.'" ' : '' ).'value="' . $value . '"
            readonly style="display:none;" class="' . $name . ' '.( ( $data_sc ) ? 'berocket_aapf_widget_sc ' : '' ).'berocket_aapf_icon_text_value"/><span class="berocket_aapf_selected_icon_show">
            ' . ( ( ! empty($value) ) ? ( ( substr( $value, 0, 3 ) == 'fa-' ) ? '<i class="fa ' . $value . '"></i>' : '<i class="fa">
            <image src="' . $value . '" alt=""></i>' ) : '' ) . '</span>';
        if ( $show_fa ) {
            $result .= '<input type="button" class="berocket_aapf_font_awesome_icon_select button" value="'.__('Font awesome', 'BeRocket_domain').'"/>
            <div style="display: none;" class="berocket_aapf_select_icon"><div><p>Font Awesome Icons<i class="fa fa-times"></i></p>
            ' . $font_awesome . '</div></div>';
        }
        if ( $show_upload ) {
            $result .= '<input type="button" class="berocket_aapf_upload_icon button" value="'.__('Upload', 'BeRocket_domain').'"/> ';
        }
        if ( $show_remove ) {
            $result .= '<input type="button" class="berocket_aapf_remove_icon button" value="'.__('Remove', 'BeRocket_domain').'"/>';
        }
        $result .= '</div>';

        return $result;
    }
}
if( ! function_exists( 'br_get_value_from_array' ) ){
    function br_get_value_from_array(&$arr, $index, $default = '') {
        if( ! isset($arr) || ! is_array($arr) ) {
            return $default;
        }
        $array = $arr;
        if( ! is_array($index) ) {
            $index = array($index);
        }
        foreach($index as $i) {
            if( ! isset($array[$i]) ) {
                return $default;
            } else {
                $array = $array[$i];
            }
        }
        return $array;
    }
}
if( ! function_exists( 'berocket_isset' ) ){
    function berocket_isset(&$var, $property_name = false, $default = null) {
        if( $property_name === false ) {
            return ( isset($var) ? $var : $default );
        } else {
            return ( isset($var) ? ( property_exists($var, $property_name) ? $var->$property_name : $default ) : $default );
        }
    }
}

if ( ! function_exists( 'fa_icons_list' ) ) {
    function fa_icons_list() {
        return array(
            "fa-glass",
            "fa-music",
            "fa-search",
            "fa-envelope-o",
            "fa-heart",
            "fa-star",
            "fa-star-o",
            "fa-user",
            "fa-film",
            "fa-th-large",
            "fa-th",
            "fa-th-list",
            "fa-check",
            "fa-times",
            "fa-search-plus",
            "fa-search-minus",
            "fa-power-off",
            "fa-signal",
            "fa-cog",
            "fa-trash-o",
            "fa-home",
            "fa-file-o",
            "fa-clock-o",
            "fa-road",
            "fa-download",
            "fa-arrow-circle-o-down",
            "fa-arrow-circle-o-up",
            "fa-inbox",
            "fa-play-circle-o",
            "fa-repeat",
            "fa-refresh",
            "fa-list-alt",
            "fa-lock",
            "fa-flag",
            "fa-headphones",
            "fa-volume-off",
            "fa-volume-down",
            "fa-volume-up",
            "fa-qrcode",
            "fa-barcode",
            "fa-tag",
            "fa-tags",
            "fa-book",
            "fa-bookmark",
            "fa-print",
            "fa-camera",
            "fa-font",
            "fa-bold",
            "fa-italic",
            "fa-text-height",
            "fa-text-width",
            "fa-align-left",
            "fa-align-center",
            "fa-align-right",
            "fa-align-justify",
            "fa-list",
            "fa-outdent",
            "fa-indent",
            "fa-video-camera",
            "fa-picture-o",
            "fa-pencil",
            "fa-map-marker",
            "fa-adjust",
            "fa-tint",
            "fa-pencil-square-o",
            "fa-share-square-o",
            "fa-check-square-o",
            "fa-arrows",
            "fa-step-backward",
            "fa-fast-backward",
            "fa-backward",
            "fa-play",
            "fa-pause",
            "fa-stop",
            "fa-forward",
            "fa-fast-forward",
            "fa-step-forward",
            "fa-eject",
            "fa-chevron-left",
            "fa-chevron-right",
            "fa-plus-circle",
            "fa-minus-circle",
            "fa-times-circle",
            "fa-check-circle",
            "fa-question-circle",
            "fa-info-circle",
            "fa-crosshairs",
            "fa-times-circle-o",
            "fa-check-circle-o",
            "fa-ban",
            "fa-arrow-left",
            "fa-arrow-right",
            "fa-arrow-up",
            "fa-arrow-down",
            "fa-share",
            "fa-expand",
            "fa-compress",
            "fa-plus",
            "fa-minus",
            "fa-asterisk",
            "fa-exclamation-circle",
            "fa-gift",
            "fa-leaf",
            "fa-fire",
            "fa-eye",
            "fa-eye-slash",
            "fa-exclamation-triangle",
            "fa-plane",
            "fa-calendar",
            "fa-random",
            "fa-comment",
            "fa-magnet",
            "fa-chevron-up",
            "fa-chevron-down",
            "fa-retweet",
            "fa-shopping-cart",
            "fa-folder",
            "fa-folder-open",
            "fa-arrows-v",
            "fa-arrows-h",
            "fa-bar-chart",
            "fa-twitter-square",
            "fa-facebook-square",
            "fa-camera-retro",
            "fa-key",
            "fa-cogs",
            "fa-comments",
            "fa-thumbs-o-up",
            "fa-thumbs-o-down",
            "fa-star-half",
            "fa-heart-o",
            "fa-sign-out",
            "fa-linkedin-square",
            "fa-thumb-tack",
            "fa-external-link",
            "fa-sign-in",
            "fa-trophy",
            "fa-github-square",
            "fa-upload",
            "fa-lemon-o",
            "fa-phone",
            "fa-square-o",
            "fa-bookmark-o",
            "fa-phone-square",
            "fa-twitter",
            "fa-facebook",
            "fa-github",
            "fa-unlock",
            "fa-credit-card",
            "fa-rss",
            "fa-hdd-o",
            "fa-bullhorn",
            "fa-bell",
            "fa-certificate",
            "fa-hand-o-right",
            "fa-hand-o-left",
            "fa-hand-o-up",
            "fa-hand-o-down",
            "fa-arrow-circle-left",
            "fa-arrow-circle-right",
            "fa-arrow-circle-up",
            "fa-arrow-circle-down",
            "fa-globe",
            "fa-wrench",
            "fa-tasks",
            "fa-filter",
            "fa-briefcase",
            "fa-arrows-alt",
            "fa-users",
            "fa-link",
            "fa-cloud",
            "fa-flask",
            "fa-scissors",
            "fa-files-o",
            "fa-paperclip",
            "fa-floppy-o",
            "fa-square",
            "fa-bars",
            "fa-list-ul",
            "fa-list-ol",
            "fa-strikethrough",
            "fa-underline",
            "fa-table",
            "fa-magic",
            "fa-truck",
            "fa-pinterest",
            "fa-pinterest-square",
            "fa-google-plus-square",
            "fa-google-plus",
            "fa-money",
            "fa-caret-down",
            "fa-caret-up",
            "fa-caret-left",
            "fa-caret-right",
            "fa-columns",
            "fa-sort",
            "fa-sort-desc",
            "fa-sort-asc",
            "fa-envelope",
            "fa-linkedin",
            "fa-undo",
            "fa-gavel",
            "fa-tachometer",
            "fa-comment-o",
            "fa-comments-o",
            "fa-bolt",
            "fa-sitemap",
            "fa-umbrella",
            "fa-clipboard",
            "fa-lightbulb-o",
            "fa-exchange",
            "fa-cloud-download",
            "fa-cloud-upload",
            "fa-user-md",
            "fa-stethoscope",
            "fa-suitcase",
            "fa-bell-o",
            "fa-coffee",
            "fa-cutlery",
            "fa-file-text-o",
            "fa-building-o",
            "fa-hospital-o",
            "fa-ambulance",
            "fa-medkit",
            "fa-fighter-jet",
            "fa-beer",
            "fa-h-square",
            "fa-plus-square",
            "fa-angle-double-left",
            "fa-angle-double-right",
            "fa-angle-double-up",
            "fa-angle-double-down",
            "fa-angle-left",
            "fa-angle-right",
            "fa-angle-up",
            "fa-angle-down",
            "fa-desktop",
            "fa-laptop",
            "fa-tablet",
            "fa-mobile",
            "fa-circle-o",
            "fa-quote-left",
            "fa-quote-right",
            "fa-spinner",
            "fa-circle",
            "fa-reply",
            "fa-github-alt",
            "fa-folder-o",
            "fa-folder-open-o",
            "fa-smile-o",
            "fa-frown-o",
            "fa-meh-o",
            "fa-gamepad",
            "fa-keyboard-o",
            "fa-flag-o",
            "fa-flag-checkered",
            "fa-terminal",
            "fa-code",
            "fa-reply-all",
            "fa-star-half-o",
            "fa-location-arrow",
            "fa-crop",
            "fa-code-fork",
            "fa-chain-broken",
            "fa-question",
            "fa-info",
            "fa-exclamation",
            "fa-superscript",
            "fa-subscript",
            "fa-eraser",
            "fa-puzzle-piece",
            "fa-microphone",
            "fa-microphone-slash",
            "fa-shield",
            "fa-calendar-o",
            "fa-fire-extinguisher",
            "fa-rocket",
            "fa-maxcdn",
            "fa-chevron-circle-left",
            "fa-chevron-circle-right",
            "fa-chevron-circle-up",
            "fa-chevron-circle-down",
            "fa-html5",
            "fa-css3",
            "fa-anchor",
            "fa-unlock-alt",
            "fa-bullseye",
            "fa-ellipsis-h",
            "fa-ellipsis-v",
            "fa-rss-square",
            "fa-play-circle",
            "fa-ticket",
            "fa-minus-square",
            "fa-minus-square-o",
            "fa-level-up",
            "fa-level-down",
            "fa-check-square",
            "fa-pencil-square",
            "fa-external-link-square",
            "fa-share-square",
            "fa-compass",
            "fa-caret-square-o-down",
            "fa-caret-square-o-up",
            "fa-caret-square-o-right",
            "fa-eur",
            "fa-gbp",
            "fa-usd",
            "fa-inr",
            "fa-jpy",
            "fa-rub",
            "fa-krw",
            "fa-btc",
            "fa-file",
            "fa-file-text",
            "fa-sort-alpha-asc",
            "fa-sort-alpha-desc",
            "fa-sort-amount-asc",
            "fa-sort-amount-desc",
            "fa-sort-numeric-asc",
            "fa-sort-numeric-desc",
            "fa-thumbs-up",
            "fa-thumbs-down",
            "fa-youtube-square",
            "fa-youtube",
            "fa-xing",
            "fa-xing-square",
            "fa-youtube-play",
            "fa-dropbox",
            "fa-stack-overflow",
            "fa-instagram",
            "fa-flickr",
            "fa-adn",
            "fa-bitbucket",
            "fa-bitbucket-square",
            "fa-tumblr",
            "fa-tumblr-square",
            "fa-long-arrow-down",
            "fa-long-arrow-up",
            "fa-long-arrow-left",
            "fa-long-arrow-right",
            "fa-apple",
            "fa-windows",
            "fa-android",
            "fa-linux",
            "fa-dribbble",
            "fa-skype",
            "fa-foursquare",
            "fa-trello",
            "fa-female",
            "fa-male",
            "fa-gittip",
            "fa-sun-o",
            "fa-moon-o",
            "fa-archive",
            "fa-bug",
            "fa-vk",
            "fa-weibo",
            "fa-renren",
            "fa-pagelines",
            "fa-stack-exchange",
            "fa-arrow-circle-o-right",
            "fa-arrow-circle-o-left",
            "fa-caret-square-o-left",
            "fa-dot-circle-o",
            "fa-wheelchair",
            "fa-vimeo-square",
            "fa-try",
            "fa-plus-square-o",
            "fa-space-shuttle",
            "fa-slack",
            "fa-envelope-square",
            "fa-wordpress",
            "fa-openid",
            "fa-university",
            "fa-graduation-cap",
            "fa-yahoo",
            "fa-google",
            "fa-reddit",
            "fa-reddit-square",
            "fa-stumbleupon-circle",
            "fa-stumbleupon",
            "fa-delicious",
            "fa-digg",
            "fa-pied-piper",
            "fa-pied-piper-alt",
            "fa-drupal",
            "fa-joomla",
            "fa-language",
            "fa-fax",
            "fa-building",
            "fa-child",
            "fa-paw",
            "fa-spoon",
            "fa-cube",
            "fa-cubes",
            "fa-behance",
            "fa-behance-square",
            "fa-steam",
            "fa-steam-square",
            "fa-recycle",
            "fa-car",
            "fa-taxi",
            "fa-tree",
            "fa-spotify",
            "fa-deviantart",
            "fa-soundcloud",
            "fa-database",
            "fa-file-pdf-o",
            "fa-file-word-o",
            "fa-file-excel-o",
            "fa-file-powerpoint-o",
            "fa-file-image-o",
            "fa-file-archive-o",
            "fa-file-audio-o",
            "fa-file-video-o",
            "fa-file-code-o",
            "fa-vine",
            "fa-codepen",
            "fa-jsfiddle",
            "fa-life-ring",
            "fa-circle-o-notch",
            "fa-rebel",
            "fa-empire",
            "fa-git-square",
            "fa-git",
            "fa-hacker-news",
            "fa-tencent-weibo",
            "fa-qq",
            "fa-weixin",
            "fa-paper-plane",
            "fa-paper-plane-o",
            "fa-history",
            "fa-circle-thin",
            "fa-header",
            "fa-paragraph",
            "fa-sliders",
            "fa-share-alt",
            "fa-share-alt-square",
            "fa-bomb",
            "fa-futbol-o",
            "fa-tty",
            "fa-binoculars",
            "fa-plug",
            "fa-slideshare",
            "fa-twitch",
            "fa-yelp",
            "fa-newspaper-o",
            "fa-wifi",
            "fa-calculator",
            "fa-paypal",
            "fa-google-wallet",
            "fa-cc-visa",
            "fa-cc-mastercard",
            "fa-cc-discover",
            "fa-cc-amex",
            "fa-cc-paypal",
            "fa-cc-stripe",
            "fa-bell-slash",
            "fa-bell-slash-o",
            "fa-trash",
            "fa-copyright",
            "fa-at",
            "fa-eyedropper",
            "fa-paint-brush",
            "fa-birthday-cake",
            "fa-area-chart",
            "fa-pie-chart",
            "fa-line-chart",
            "fa-lastfm",
            "fa-lastfm-square",
            "fa-toggle-off",
            "fa-toggle-on",
            "fa-bicycle",
            "fa-bus",
            "fa-ioxhost",
            "fa-angellist",
            "fa-cc",
            "fa-ils",
            "fa-meanpath",
        );
    }
}

/**
 * BeRocket Debug function
 */
if ( ! function_exists( "bd" ) ) {
    function bd( $var = '', $tag = 'pre', $die = false ) {
        if ( $tag === true or $tag === 1 ) {
            $tag = 'pre';
            $die = true;
        }

        echo "<{$tag}>";
        print_r( $var );
        echo "</{$tag}>";

        if ( $die ) {
            die();
        }
    }
}
