<?php
define( "BeRocket_products_label_domain", 'BeRocket_products_label_domain');
define( "products_label_TEMPLATE_PATH", plugin_dir_path( __FILE__ ) . "templates/" );
load_plugin_textdomain('BeRocket_products_label_domain', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');
require_once(plugin_dir_path( __FILE__ ).'berocket/framework.php');

/**
 * Class BeRocket_products_label
 * REPLACE
 * products_label - plugin name
 * Products Labels - normal plugin name
 * WooCommerce Advanced Product Labels - full plugin name
 * 18 - id on BeRocket
 * woocommerce-advanced-product-labels - slug on BeRocket
 * 24 - price on BeRocket
 */
class BeRocket_products_label extends BeRocket_Framework {
    public static $settings_name = 'br-products_label-options';
    protected static $instance;
    public $info, $defaults, $values;

    function __construct () {
        $this->info = array(
            'id'          => 18,
            'version'     => BeRocket_products_label_version,
            'plugin'      => '',
            'slug'        => '',
            'key'         => '',
            'name'        => '',
            'plugin_name' => 'products_label',
            'full_name'   => 'WooCommerce Advanced Product Labels',
            'norm_name'   => 'Products Labels',
            'price'       => '24',
            'domain'      => 'BeRocket_products_label_domain',
            'templates'   => products_label_TEMPLATE_PATH,
            'plugin_file' => BeRocket_products_label_file,
            'plugin_dir'  => __DIR__,
        );

        $this->defaults = array(
            'disable_labels'    => '0',
            'disable_plabels'   => '0',
            'disable_ppage'     => '0',
            'custom_css'        => '.product .images {position: relative;}',
            'script'            => '',
            'plugin_key'        => '',
        );

        $this->values = array(
            'settings_name' => 'br-products_label-options',
            'option_page'   => 'br-products_label',
            'premium_slug'  => 'woocommerce-advanced-product-labels',
        );

        // List of the features missed in free version of the plugin
        $this->feature_list = array(
            'Labels for each product',
            'Label by: product age, stock quantity, sale price',
            'Custom image and time left for discount type of label',
            'Custom border and font size for each labels',
            'Labels can be rotated from -90deg to 90deg',
            'Labels can be added to products with specific attribute values',
        );

        parent::__construct( $this );

        add_action ( 'init', array( $this, 'init' ) );
        add_filter( 'bulk_actions-edit-br_labels', array( $this, 'bulk_actions_edit' ) );
        add_filter( 'views_edit-br_labels', array( $this, 'views_edit' ) );
        add_filter( 'manage_edit-br_labels_columns', array( $this, 'manage_edit_columns' ) );
        add_action( 'manage_br_labels_posts_custom_column', array( $this, 'columns_replace' ), 2 );
        add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
        add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'product_edit_advanced_label' ) );
        if ( version_compare(br_get_woocommerce_version(), '2.7', '>=' ) ) {
            add_action( 'woocommerce_product_data_panels', array( $this, 'product_edit_tab' ) );
        } else {
            add_action( 'woocommerce_product_write_panels', array( $this, 'product_edit_tab' ) );
        }
        add_action( 'wp_ajax_br_label_ajax_demo', array($this, 'ajax_get_label') );
        add_action( 'save_post', array( $this, 'wc_save_product' ) );
    }
    public function init () {
        $options = $this->get_option();
        $theme = wp_get_theme();
        $theme = ($theme->offsetGet('Parent Theme') ? $theme->offsetGet('Parent Theme') : $theme->Name);
        if( strpos($theme, 'LEGENDA') !== FALSE ) {
            add_action ( 'woocommerce_before_shop_loop_item', array( $this, 'set_all_label'), 20 );
        } else {
            add_action ( 'woocommerce_before_shop_loop_item_title', array( $this, 'set_all_label'), 20 );
        }
        add_action ( 'product_of_day_before_thumbnail_widget', array( $this, 'set_image_label'), 20 );
        add_action ( 'product_of_day_before_title_widget', array( $this, 'set_label_label'), 20 );
        add_action ( 'lgv_advanced_after_img', array( $this, 'set_all_label'), 20 );
        if( ! @ $options['disable_ppage'] ) {
            add_action( 'woocommerce_product_thumbnails', array( $this, 'set_all_label'), 10 );
            add_action( 'woocommerce_product_thumbnails', array( $this, 'move_labels_from_zoom'), 20 );
        }
        register_post_type( "br_labels",
			array(
				'labels' => array(
					'name'               => __( 'Advanced Label', 'BeRocket_products_label_domain' ),
					'singular_name'      => __( 'Advanced Label', 'BeRocket_products_label_domain' ),
					'menu_name'          => _x( 'Advanced Labels', 'Admin menu name', 'BeRocket_products_label_domain' ),
					'add_new'            => __( 'Add Label', 'BeRocket_products_label_domain' ),
					'add_new_item'       => __( 'Add New Label', 'BeRocket_products_label_domain' ),
					'edit'               => __( 'Edit', 'BeRocket_products_label_domain' ),
					'edit_item'          => __( 'Edit Label', 'BeRocket_products_label_domain' ),
					'new_item'           => __( 'New Label', 'BeRocket_products_label_domain' ),
					'view'               => __( 'View Labels', 'BeRocket_products_label_domain' ),
					'view_item'          => __( 'View Label', 'BeRocket_products_label_domain' ),
					'search_items'       => __( 'Search Advanced Labels', 'BeRocket_products_label_domain' ),
					'not_found'          => __( 'No Advanced Labels found', 'BeRocket_products_label_domain' ),
					'not_found_in_trash' => __( 'No Advanced Labels found in trash', 'BeRocket_products_label_domain' ),
				),
				'description'     => __( 'This is where you can add advanced labels.', 'BeRocket_products_label_domain' ),
				'public'          => true,
				'show_ui'         => true,
				'capability_type' => 'post',
				'publicly_queryable'  => false,
				'exclude_from_search' => true,
				'show_in_menu'        => 'edit.php?post_type=product',
				'hierarchical'        => false,
				'rewrite'             => false,
				'query_var'           => false,
				'supports'            => array( 'title' ),
				'show_in_nav_menus'   => false,
			)
		);
        wp_register_style( 'berocket_products_label_style', 
            plugins_url( 'css/frontend.css', __FILE__ ), 
            "", 
            BeRocket_products_label_version );
        wp_enqueue_style( 'berocket_products_label_style' );
    }
    public function bulk_actions_edit ( $actions ) {
        unset( $actions['edit'] );
        return $actions;
    }
    public function views_edit ( $view ) {
        unset( $view['publish'], $view['private'], $view['future'] );
        return $view;
    }
    public function manage_edit_columns ( $columns ) {
        $columns = array();
        $columns["cb"]   = '<input type="checkbox" />';
        $columns["name"] = __( "Label Name", 'BeRocket_products_label_domain' );
        $columns["products"] = __( "Label text", 'BeRocket_products_label_domain' );
        $columns["data"] = __( "Position", 'BeRocket_products_label_domain' );
        return $columns;
    }
    public function columns_replace ( $column ) {
        global $post;
        switch ( $column ) {
            case "name":

                $edit_link = get_edit_post_link( $post->ID );
                $title = '<a class="row-title" href="' . $edit_link . '">' . _draft_or_post_title() . '</a>';

                echo '<strong>' . $title . '</strong>';

                // Get actions
                $actions = array();

                $post_type_object = get_post_type_object( $post->post_type );

                if ( current_user_can( $post_type_object->cap->delete_post, $post->ID ) ) {
                    if ( 'trash' == $post->post_status )
                        $actions['untrash'] = "<a title='" . __( 'Restore this item from the Trash', 'BeRocket_products_label_domain' ) . "' href='" . wp_nonce_url( admin_url( sprintf( $post_type_object->_edit_link . '&amp;action=untrash', $post->ID ) ), 'untrash-post_' . $post->ID ) . "'>" . __( 'Restore', 'BeRocket_products_label_domain' ) . "</a>";
                    elseif ( EMPTY_TRASH_DAYS )
                        $actions['trash'] = "<a class='submitdelete' title='" . __( 'Move this item to the Trash', 'BeRocket_products_label_domain' ) . "' href='" . get_delete_post_link( $post->ID ) . "'>" . __( 'Trash', 'BeRocket_products_label_domain' ) . "</a>";
                    if ( 'trash' == $post->post_status || ! EMPTY_TRASH_DAYS )
                        $actions['delete'] = "<a class='submitdelete' title='" . __( 'Delete this item permanently', 'BeRocket_products_label_domain' ) . "' href='" . get_delete_post_link( $post->ID, '', true ) . "'>" . __( 'Delete Permanently', 'BeRocket_products_label_domain' ) . "</a>";
                }

                $actions = apply_filters( 'post_row_actions', $actions, $post );

                echo '<div class="row-actions">';

                $i = 0;
                $action_count = count( $actions );

                foreach ( $actions as $action => $link ) {
                    ( $i == $action_count - 1 ) ? $sep = '' : $sep = ' | ';
                    echo '<span class="' . sanitize_html_class( $action ) . '">' . $link . $sep . '</span>';
                    $i++;
                }
                echo '</div>';
                
                break;
            case "products":
                $label_type = get_post_meta( $post->ID, 'br_label', true );
                $text = '';
                if( isset($label_type['text']) ) {
                    $text = $label_type['text'];
                }
                if( $label_type['content_type'] == 'sale_p' ) {
                    $text = __('Discount percentage', 'BeRocket_products_label_domain');
                }
                echo apply_filters('berocket_labels_products_column_text', $text, $label_type);;
                break;
            case "data":
                $label_type = get_post_meta( $post->ID, 'br_label', true );
                $position = array('left' => __('Left', 'BeRocket_products_label_domain'), 'center' => __('Center', 'BeRocket_products_label_domain'), 'right' => __('Right', 'BeRocket_products_label_domain'));
                $type = array('image' => __('On image', 'BeRocket_products_label_domain'), 'label' => __('Label', 'BeRocket_products_label_domain'));
                if( isset($label_type['position']) && isset($label_type['type']) ) {
                    echo $type[$label_type['type']].' ( '.$position[$label_type['position']].' )';
                }
                break;
        }
    }
    public  function add_meta_boxes () {
        add_meta_box( 'submitdiv', __( 'Save label content', 'BeRocket_products_label_domain' ), array( $this, 'meta_box' ), 'br_labels', 'side', 'high' );
        add_meta_box( 'labels_consitions', __( 'Label conditions', 'BeRocket_products_label_domain' ), array( $this, 'meta_box_conditions' ), 'br_labels', 'normal', 'high' );
        add_meta_box( 'labels_setup', __( 'Label settings', 'BeRocket_products_label_domain' ), array( $this, 'meta_box_settings' ), 'br_labels', 'normal', 'high' );
        add_meta_box( 'labels_description', __( 'Description', 'BeRocket_products_label_domain' ), array( $this, 'meta_box_description' ), 'br_labels', 'side', 'default' );
    }
    public  function meta_box($post) {
        wp_enqueue_script( 'berocket_products_label_admin', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), BeRocket_products_label_version );
        wp_enqueue_script( 'berocket_framework_admin' );
        wp_enqueue_style( 'berocket_framework_admin_style' );
        wp_enqueue_script( 'berocket_widget-colorpicker' );
        wp_enqueue_style( 'berocket_widget-colorpicker-style' );
        wp_enqueue_style( 'berocket_font_awesome' );
        ?>
        <div class="submitbox" id="submitpost">

            <div id="minor-publishing">
                <div id="major-publishing-actions">
                    <div id="delete-action">
                        <?php
                        global $pagenow;
                        if( in_array( $pagenow, array( 'post-new.php' ) ) ) {
                        } else {
                            if ( current_user_can( "delete_post", $post->ID ) ) {
                                if ( ! EMPTY_TRASH_DAYS )
                                    $delete_text = __( 'Delete Permanently', 'BeRocket_products_label_domain' );
                                else
                                    $delete_text = __( 'Move to Trash', 'BeRocket_products_label_domain' );
                                ?>
                                <a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post->ID ) ); ?>"><?php echo esc_attr( $delete_text ); ?></a>
                            <?php 
                            }
                        } ?>
                    </div>

                    <div id="publishing-action">
                        <span class="spinner"></span>
                        <input type="submit" class="button button-primary tips" name="publish" value="<?php _e( 'Save Label', 'BeRocket_products_label_domain' ); ?>" data-tip="<?php _e( 'Save/update notice', 'BeRocket_products_label_domain' ); ?>" />
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <?php
    }
    public function meta_box_conditions($post) {
        include products_label_TEMPLATE_PATH . "label_conditions.php";
    }
    public function meta_box_settings($post) {
        include products_label_TEMPLATE_PATH . "label.php";
    }
    public function meta_box_description($post) {
        include products_label_TEMPLATE_PATH . "label_description.php";
    }
    public function move_labels_from_zoom() {
        add_action('wp_footer', array( $this, 'set_label_js_script'));
    }
    public function set_label_js_script() {
        ?>
        <script>
            jQuery(".woocommerce-product-gallery .br_alabel").each(function(i, o) {
                jQuery(o).hide().parents(".woocommerce-product-gallery").append(jQuery(o));
            });
            galleryReadyCheck = setInterval(function() {
                if( jQuery(".woocommerce-product-gallery .woocommerce-product-gallery__trigger").length > 0 ) {
                    clearTimeout(galleryReadyCheck);
                    jQuery(".woocommerce-product-gallery .br_alabel").each(function(i, o) {
                        jQuery(o).show().parents(".woocommerce-product-gallery").append(jQuery(o));
                    });
                }
                else if(jQuery('.woocommerce-product-gallery__wrapper').length > 0) {
                    clearTimeout(galleryReadyCheck);
                    jQuery(".woocommerce-product-gallery .br_alabel").each(function(i, o) {
                        jQuery(o).show().parents(".woocommerce-product-gallery").append(jQuery(o));
                    });
                }
            }, 250);
        </script>
        <?php
    }
    public function set_all_label() {
        $this->set_label();
    }
    public function set_image_label() {
        $this->set_label('image');
    }
    public function set_label_label() {
        echo '<div>';
        $this->set_label('label');
        echo '<div style="clear:both;"></div></div>';
    }
    public function set_label($type = TRUE) {
        global $product;
        do_action('berocket_apl_set_label_start', $product);
        if( apply_filters('berocket_apl_set_label_prevent', false, $type, $product) ) {
            return true;
        }
        $product_post = br_wc_get_product_post($product);
        $options = $this->get_option();
        if( ! $options['disable_plabels'] ) {
            $label_type = get_post_meta( $product_post->ID, 'br_label', true );
            $this->show_label_on_product($label_type, $product);
        }
        if( ! $options['disable_labels'] ) {
            $args = array(
                'posts_per_page'   => -1,
                'offset'           => 0,
                'category'         => '',
                'category_name'    => '',
                'orderby'          => 'date',
                'order'            => 'DESC',
                'include'          => '',
                'exclude'          => '',
                'meta_key'         => '',
                'meta_value'       => '',
                'post_type'        => 'br_labels',
                'post_mime_type'   => '',
                'post_parent'      => '',
                'author'	   => '',
                'post_status'      => 'publish',
                'suppress_filters' => false 
            );
            $posts_array = get_posts( $args );
            foreach($posts_array as $label) {
                $br_label = get_post_meta( $label->ID, 'br_label', true );
                if( $type === TRUE || $type == $br_label['type'] ) {
                    if( ! isset($br_label['data']) || $this->check_label_on_post($label->ID, $br_label['data'], $product) ) {
                        $this->show_label_on_product($br_label, $product);
                    }
                }
            }
        }
        do_action('berocket_apl_set_label_end', $product);
    }
    public function ajax_get_label() {
        if ( current_user_can( 'manage_options' ) ) {
            do_action('berocket_apl_set_label_start', 'demo');
            $this->show_label_on_product($_POST['br_label'], 'demo');
            do_action('berocket_apl_set_label_end', 'demo');
        }
        wp_die();
    }
    public function product_edit_advanced_label () {
        echo '<li class="product_tab_manager"><a href="#br_alabel">' . __( 'Advanced label', 'BeRocket_tab_manager_domain' ) . '</a></li>';
    }
    public function product_edit_tab () {
        wp_enqueue_script( 'berocket_products_label_admin', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), BeRocket_products_label_version );
        wp_enqueue_script( 'berocket_framework_admin' );
        wp_enqueue_style( 'berocket_framework_admin_style' );
        wp_enqueue_script( 'berocket_widget-colorpicker' );
        wp_enqueue_style( 'berocket_widget-colorpicker-style' );
        wp_enqueue_style( 'berocket_font_awesome' );
        $one_product = true;
        set_query_var( 'one_product', true );
        include products_label_TEMPLATE_PATH . "label.php";
    }
    public function show_label_on_product($br_label, $product) {
        if( $product !== 'demo' ) {
            $product_post = br_wc_get_product_post($product);
        }
        if( empty($br_label) || ! is_array($br_label) ) {
            return false;
        }
        if( empty($br_label['content_type']) ) {
            $br_label['content_type'] = 'text';
        }
        if ( $br_label['color'][0] != '#' ) {
            $br_label['color'] = '#'.$br_label['color'];
        }
        if ( isset($br_label['font_color']) && $br_label['font_color'][0] != '#' ) {
            $br_label['font_color'] = '#'.$br_label['font_color'];
        }
        if( @ $br_label['content_type'] == 'sale_p' ) {
            $br_label['text'] = '';
            if( $product == 'demo' || $product->is_on_sale() ) {
                $price_ratio = false;
                if( $product == 'demo' ) {
                    $product_sale = '250.5';
                    $product_regular = '430.25';
                    $price_ratio = $product_sale / $product_regular;
                } else {
                    $product_sale = br_wc_get_product_attr($product, 'sale_price');
                    $product_regular = br_wc_get_product_attr($product, 'regular_price');
                    if( ! empty($product_sale) && $product_sale != $product_regular ) {
                        $price_ratio = $product_sale / $product_regular;
                    }
                    if( $product->has_child() ) {
                        foreach($product->get_children() as $child_id) {
                            $child = br_wc_get_product_attr($product, 'child', $child_id);
                            $child_sale = br_wc_get_product_attr($child, 'sale_price');
                            $child_regular = br_wc_get_product_attr($child, 'regular_price');
                            if( ! empty($child_sale) && $child_sale != $child_regular ) {
                                $price_ratio2 = $child_sale / $child_regular;
                                if( $price_ratio === false || $price_ratio2 < $price_ratio ) {
                                    $price_ratio = $price_ratio2;
                                }
                            }
                        }
                    }
                }
                if( $price_ratio !== false ) {
                    $price_ratio = ($price_ratio * 100);
                    $price_ratio = number_format($price_ratio, 0, '', '');
                    $price_ratio = $price_ratio * 1;
                    $br_label['text'] = (100 - $price_ratio)."%";
                    if( ! empty($br_label['discount_minus']) ) {
                        $br_label['text'] = '-'.$br_label['text'];
                    }
                }
            }
        }
        $label_style = '';
        if( ! empty($br_label['image_height']) ) {
            $label_style .= 'height: ' . $br_label['image_height'] . 'px;';
        }
        if( ! empty($br_label['image_width']) ) {
            $label_style .= 'width: ' . $br_label['image_width'] . 'px;';
        }
        if( empty($br_label['image_height']) && empty($br_label['image_width']) ) {
            $label_style .= 'padding: 0.2em 0.5em;';
        }
        if( ! empty($br_label['color']) && ! empty($br_label['color_use']) ) {
            $label_style .= 'background-color:' . $br_label['color'].';';
        }
        if( ! empty($br_label['font_color']) ) {
            $label_style .= 'color:'.@ $br_label['font_color'].';';
        }
        if( isset($br_label['border_radius']) ) {
            $label_style .= 'border-radius:' . $br_label['border_radius'] . 'px;';
        }
        if( isset($br_label['line_height']) ) {
            $label_style .= 'line-height:' . $br_label['line_height'] . 'px;';
        }
        $div_style = '';
        if( isset($br_label['padding_top']) ) {
            $div_style .= 'top:' . $br_label['padding_top'] . 'px;';
        }
        if( isset($br_label['padding_horizontal']) && $br_label['position'] != 'center' ) {
            $div_style .= ($br_label['position'] == 'left' ? 'left:' : 'right:' ) . $br_label['padding_horizontal'] . 'px;';
        }
        $div_class = 'br_alabel br_alabel_'.$br_label['type'].' br_alabel_type_'. @ $br_label['content_type'] . ' br_alabel_'.$br_label['position'];

        $br_label['text'] = apply_filters('berocket_apl_label_show_text', ( empty($br_label['text']) ? '' : $br_label['text'] ), $br_label, $product);
        $label_style = apply_filters('berocket_apl_label_show_label_style', ( empty($label_style) ? '' : $label_style ), $br_label, $product);
        $div_style = apply_filters('berocket_apl_label_show_div_style', ( empty($div_style) ? '' : $div_style ), $br_label, $product);
        $div_class = apply_filters('berocket_apl_label_show_div_class', ( empty($div_class) ? '' : $div_class ), $br_label, $product);
        if( $br_label['content_type'] == 'text' && empty($br_label['text']) ) {
            $br_label['text'] = FALSE;
        }
        if( $br_label['text'] === FALSE ) {
            return FALSE;
        }
        if( ! is_array($br_label['text']) ) {
            $br_label['text'] = array($br_label['text']);
        }
        foreach($br_label['text'] as $text ) {
            if( ! empty($text) && $text[0] == '#' ) {
                $label_style = $label_style . ' background-color:' .$text . ';';
                $text = '';
            }
            $html = '<div class="' . $div_class . '" style="' . $div_style . '">';
            $html .= '<span style="' . $label_style . '">'.$text.'</span>';
            $html .= '</div>';
            $html = apply_filters('berocket_apl_show_label_on_product_html', $html, $br_label, $product);
            echo $html;
        }
    }
    public function check_label_on_post($label_id, $label_data, $product) {
        $product_id = br_wc_get_product_id($product);
        $show_label = wp_cache_get( 'WC_Product_'.$product_id, 'brapl_'.$label_id );
        if( $show_label === false ) {
            $product_post = br_wc_get_product_post($product);
            $show_label = false;
            foreach($label_data as $label) {
                $show_label = false;
                foreach($label as $condition) {
                    $show_label = apply_filters('berocket_label_condition_check_type_' . $condition['type'], false, $condition, $product_id, $product, $product_post);
                    if( !$show_label ) {
                        break;
                    }
                }
                if( $show_label ) {
                    break;
                }
            }
            wp_cache_set( 'WC_Product_'.$product_id, ($show_label ? 1 : -1), 'brapl_'.$label_id, 60*60*24 );
        } else {
            $show_label = ( $show_label == 1 ? true : false );
        }
        return $show_label;
    }
    public function wc_save_product( $product_id ) {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }
        if ( isset( $_POST['br_label'] ) ) {
            if( ! isset($_POST['br_label']['color_use']) ) {
                $_POST['br_label']['color_use'] = 0;
            }
            $_POST['br_label'] = apply_filters('berocket_apl_wc_save_product', $_POST['br_label'], $product_id);
            update_post_meta( $product_id, 'br_label', $_POST['br_label'] );
        }
    }

    public function admin_menu() {
        if( parent::admin_menu() ) {
            add_submenu_page(
                'woocommerce',
                __( $this->info[ 'norm_name' ] . ' settings', $this->info[ 'domain' ] ),
                __( $this->info[ 'norm_name' ], $this->info[ 'domain' ] ),
                'manage_options',
                $this->values[ 'option_page' ],
                array(
                    $this,
                    'option_form'
                )
            );
        }
    }
    public function admin_settings( $tabs_info = array(), $data = array() ) {
        parent::admin_settings(
            array(
                'General' => array(
                    'icon' => 'cog',
                ),
                'Add Label' => array(
                    'icon' => 'plus-square',
                    'link' => admin_url( 'post-new.php?post_type=br_labels' ),
                ),
                'CSS'     => array(
                    'icon' => 'css3',
                ),
                'JavaScript'     => array(
                    'icon' => 'code',
                ),
                'License' => array(
                    'icon' => 'unlock-alt'
                ),
            ),
            array(
            'General' => array(
                'disable_labels' => array(
                    "type"     => "checkbox",
                    "label"    => __('Disable global labels', 'BeRocket_products_label_domain'),
                    "name"     => "disable_labels",
                    "value"    => "1",
                    "selected" => false,
                ),
                'disable_plabels' => array(
                    "type"     => "checkbox",
                    "label"    => __('Disable product labels', 'BeRocket_products_label_domain'),
                    "name"     => "disable_plabels",
                    "value"    => "1",
                    "selected" => false,
                ),
                'disable_ppage' => array(
                    "type"     => "checkbox",
                    "label"    => __('Disable labels on product page', 'BeRocket_products_label_domain'),
                    "name"     => "disable_ppage",
                    "value"    => "1",
                    "selected" => false,
                ),
            ),
            'CSS'     => array(
                array(
                    "type"  => "textarea",
                    "label" => __('Custom CSS', 'BeRocket_products_label_domain'),
                    "name"  => "custom_css",
                ),
            ),
            'JavaScript'     => array(
                array(
                    "type"      => "textarea",
                    "label"     => __('On Page Load', 'BeRocket_products_label_domain'),
                    "name"      => array("script", "js_page_load"),
                    "value"     => "",
                ),
            ),
            'License' => array(
                array(
                    "section" => "license",
                    "label"   => __('Plugin key', 'BeRocket_products_label_domain'),
                    "name"    => "plugin_key",
                    "test"    => true
                ),
            ),
        ) );
    }

}

new BeRocket_products_label;

berocket_admin_notices::generate_subscribe_notice();

/**
 * Creating admin notice if it not added already
 */
new berocket_admin_notices(array(
    'start' => 1511281980, // timestamp when notice start
    'end'   => 1514764803, // timestamp when notice end
    'name'  => 'SALE_LOAD_MORE2', //notice name must be unique for this time period
    'html'  => 'Only <strong>$10</strong> for <strong>Premium</strong> WooCommerce Load More Products!
            <a class="berocket_button" href="http://berocket.com/product/woocommerce-load-more-products" target="_blank">Buy Now</a>
             &nbsp; <span>Get your <strong class="red">60% discount</strong> and save <strong>$15</strong> today</span>
            ', //text or html code as content of notice
    'righthtml'  => '<a class="berocket_no_thanks">No thanks</a>', //content in the right block, this is default value. This html code must be added to all notices
    'rightwidth'  => 80, //width of right content is static and will be as this value. berocket_no_thanks block is 60px and 20px is additional
    'nothankswidth'  => 60, //berocket_no_thanks width. set to 0 if block doesn't uses. Or set to any other value if uses other text inside berocket_no_thanks
    'contentwidth'  => 910, //width that uses for mediaquery is image + contentwidth + rightwidth + 210 other elements
    'subscribe'  => false, //add subscribe form to the righthtml
    'priority'  => 7, //priority of notice. 1-5 is main priority and displays on settings page always
    'height'  => 50, //height of notice. image will be scaled
    'repeat'  => '+1 week', //repeat notice after some time. time can use any values that accept function strtotime
    'repeatcount'  => 4, //repeat count. how many times notice will be displayed after close
    'image'  => array(
        'local' => plugin_dir_url( __FILE__ ) . 'images/60p_sale.jpg', //notice will be used this image directly
    ),
));