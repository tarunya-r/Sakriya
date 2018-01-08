<?php
class BeRocket_apl_default_conditions {
    function __construct() {
        //CONDITIONS HTML
        add_filter('berocket_label_condition_type_product', array( __CLASS__, 'condition_product'), 10, 3);
        add_filter('berocket_label_condition_type_category', array( __CLASS__, 'condition_category'), 10, 3);
        add_filter('berocket_label_condition_type_sale', array( __CLASS__, 'condition_sale'), 10, 3);
        add_filter('berocket_label_condition_type_bestsellers', array( __CLASS__, 'condition_bestsellers'), 10, 3);
        add_filter('berocket_label_condition_type_price', array( __CLASS__, 'condition_price'), 10, 3);
        add_filter('berocket_label_condition_type_stockstatus', array( __CLASS__, 'condition_stockstatus'), 10, 3);
        add_filter('berocket_label_condition_type_totalsales', array( __CLASS__, 'condition_totalsales'), 10, 3);
        //CONDITIONS CHECK
        add_filter('berocket_label_condition_check_type_product', array( __CLASS__, 'check_condition_product'), 10, 5);
        add_filter('berocket_label_condition_check_type_category', array( __CLASS__, 'check_condition_category'), 10, 5);
        add_filter('berocket_label_condition_check_type_sale', array( __CLASS__, 'check_condition_sale'), 10, 5);
        add_filter('berocket_label_condition_check_type_bestsellers', array( __CLASS__, 'check_condition_bestsellers'), 10, 5);
        add_filter('berocket_label_condition_check_type_price', array( __CLASS__, 'check_condition_price'), 10, 5);
        add_filter('berocket_label_condition_check_type_stockstatus', array( __CLASS__, 'check_condition_stockstatus'), 10, 5);
        add_filter('berocket_label_condition_check_type_totalsales', array( __CLASS__, 'check_condition_totalsales'), 10, 5);
    }
    public static function supcondition_equal($name, $options, $extension = array()) {
        $equal = 'equal';
        if( is_array($options) && isset($options['equal'] ) ) {
            $equal = $options['equal'];
        }
        $equal_list = array(
            'equal' => __('Equal', 'BeRocket_products_label_domain'),
            'not_equal' => __('Not equal', 'BeRocket_products_label_domain'),
        );
        if( ! empty($extension['equal_less']) ) {
            $equal_list['equal_less'] = __('Equal or less', 'BeRocket_products_label_domain');
        }
        if( ! empty($extension['equal_more']) ) {
            $equal_list['equal_more'] = __('Equal or more', 'BeRocket_products_label_domain');
        }
        $html = '<select name="' . $name . '[equal]">';
        foreach($equal_list as $equal_slug => $equal_name) {
            $html .= '<option value="' . $equal_slug . '"' . ($equal == $equal_slug ? ' selected' : '') . '>' . $equal_name . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    public static function condition_product($html, $name, $options) {
        $def_options = array('product' => array());
        $options = array_merge($def_options, $options);
        $html .= self::supcondition_equal($name, $options) . '
        ' . br_products_selector( $name . '[product]', $options['product']);
        return $html;
    }

    public static function check_condition_product($show_label, $condition, $product_id, $product, $product_post) {
        if( isset($condition['product']) && is_array($condition['product']) ) {
            $show_label = in_array($product_id, $condition['product']);
            if( $condition['equal'] == 'not_equal' ) {
                $show_label = ! $show_label;
            }
        }
        return $show_label;
    }

    public static function condition_category($html, $name, $options) {
        $product_categories = get_terms( 'product_cat' );
        if( is_array($product_categories) && count($product_categories) > 0 ) {
            $def_options = array('category' => '');
            $options = array_merge($def_options, $options);
            $html .= self::supcondition_equal($name, $options);
            $html .= '<select name="' . $name . '[category]">';
            foreach($product_categories as $category) {
                $html .= '<option value="' . $category->term_id . '"' . ($options['category'] == $category->term_id ? ' selected' : '') . '>' . $category->name . '</option>';
            }
            $html .= '</select>';
        }
        return $html;
    }
    
    public static function check_condition_category($show_label, $condition, $product_id, $product, $product_post) {
        $terms = get_the_terms( $product_id, 'product_cat' );
        if( is_array( $terms ) ) {
            foreach( $terms as $term ) {
                if( $term->term_id == $condition['category']) {
                    $show_label = true;
                    break;
                }
            }
        }
        if( $condition['equal'] == 'not_equal' ) {
            $show_label = ! $show_label;
        }
        return $show_label;
    }

    public static function condition_sale($html, $name, $options) {
        $def_options = array('sale' => 'yes');
        $options = array_merge($def_options, $options);
        $html .= '<label>' . __('Is on sale', 'BeRocket_products_label_domain') . '<select name="' . $name . '[sale]">
            <option value="yes"' . ($options['sale'] == 'yes' ? ' selected' : '') . '>' . __('Yes', 'BeRocket_products_label_domain') . '</option>
            <option value="no"' . ($options['sale'] == 'no' ? ' selected' : '') . '>' . __('No', 'BeRocket_products_label_domain') . '</option>
        </select></label>';
        return $html;
    }
    
    public static function check_condition_sale($show_label, $condition, $product_id, $product, $product_post) {
        $show_label = $product->is_on_sale();
        if( $condition['sale'] == 'no' ) {
            $show_label = ! $show_label;
        }
        return $show_label;
    }

    public static function condition_bestsellers($html, $name, $options) {
        $def_options = array('bestsellers' => '1');
        $options = array_merge($def_options, $options);
        $html .= '<label>' . __('Count of product', 'BeRocket_products_label_domain') . '<input type="number" min="1" name="' . $name . '[bestsellers]" value="' . $options['bestsellers'] . '"></label>';
        return $html;
    }
    
    public static function check_condition_bestsellers($show_label, $condition, $product_id, $product, $product_post) {
        $args = array(
            'post_type' 			=> 'product',
            'post_status' 			=> 'publish',
            'ignore_sticky_posts'   => 1,
            'posts_per_page'		=> $condition['bestsellers'],
            'meta_key' 		 		=> 'total_sales',
            'orderby' 		 		=> 'meta_value_num',
            'meta_query' 			=> array(
                array(
                    'key' 		=> '_visibility',
                    'value' 	=> array( 'catalog', 'visible' ),
                    'compare' 	=> 'IN'
                )
            )
        );
        $posts = get_posts( $args );
        if( is_array( $posts ) ) {
            foreach($posts as $post) {
                if( $product_post->ID == $post->ID ) {
                    $show_label = true;
                    break;
                }
            }
        }
        return $show_label;
    }

    public static function condition_price($html, $name, $options) {
        $def_options = array('price' => array('from' => '1', 'to' => '1'));
        $options = array_merge($def_options, $options);
        if( ! is_array($options['price']) ) {
            $options['price'] = array();
        }
        $options['price'] = array_merge($def_options['price'], $options['price']);
        $html .= self::supcondition_equal($name, $options);
        $html .= __('From:', 'BeRocket_products_label_domain') . '<input class="price_from" type="number" min="0" name="' . $name . '[price][from]" value="' . $options['price']['from'] . '">' .
                 __('To:', 'BeRocket_products_label_domain')   . '<input class="price_to"   type="number" min="1" name="' . $name . '[price][to]"   value="' . $options['price']['to']   . '">';
        return $html;
    }
    
    public static function check_condition_price($show_label, $condition, $product_id, $product, $product_post) {
        $product_price = br_wc_get_product_attr($product, 'price');
        $show_label = $product_price >= $condition['price']['from'] && $product_price <= $condition['price']['to'];
        if( ! $show_label && $product->has_child() ) {
            foreach($product->get_children() as $child_id) {
                $child = br_wc_get_product_attr($product, 'child', $child_id);
                $child_price = br_wc_get_product_attr($child, 'price');
                $show_label = $child_price >= $condition['price']['from'] && $child_price <= $condition['price']['to'];
                if( $show_label ) {
                    break;
                }
            }
        }
        if( $condition['equal'] == 'not_equal' ) {
            $show_label = ! $show_label;
        }
        return $show_label;
    }

    public static function condition_stockstatus($html, $name, $options) {
        $def_options = array('stockstatus' => 'in_stock');
        $options = array_merge($def_options, $options);
        $html .= '
        <select name="' . $name . '[stockstatus]">
            <option value="in_stock"' . ($options['stockstatus'] == 'in_stock' ? ' selected' : '') . '>' . __('In stock', 'BeRocket_products_label_domain') . '</option>
            <option value="out_of_stock"' . ($options['stockstatus'] == 'out_of_stock' ? ' selected' : '') . '>' . __('Out of stock', 'BeRocket_products_label_domain') . '</option>
        </select>';
        return $html;
    }
    
    public static function check_condition_stockstatus($show_label, $condition, $product_id, $product, $product_post) {
        $show_label = $product->is_in_stock();
        if( $condition['stockstatus'] == 'out_of_stock' ) {
            $show_label = ! $show_label;
        }
        return $show_label;
    }

    public static function condition_totalsales($html, $name, $options) {
        $def_options = array('totalsales' => '1');
        $options = array_merge($def_options, $options);
        $html .= self::supcondition_equal($name, $options, array('equal_less' => true, 'equal_more' => true));
        $html .= '<label>' . __('Count of product', 'BeRocket_products_label_domain') . '<input type="number" min="0" name="' . $name . '[totalsales]" value="' . $options['totalsales'] . '"></label>';
        return $html;
    }
    
    public static function check_condition_totalsales($show_label, $condition, $product_id, $product, $product_post) {
        $total_sales = get_post_meta( $product_id, 'total_sales', true );
        if( $condition['equal'] == 'equal' ) {
            $show_label = $condition['totalsales'] == $total_sales;
        } elseif( $condition['equal'] == 'not_equal' ) {
            $show_label = $condition['totalsales'] != $total_sales;
        } elseif( $condition['equal'] == 'equal_less' ) {
            $show_label = $condition['totalsales'] >= $total_sales;
        } elseif( $condition['equal'] == 'equal_more' ) {
            $show_label = $condition['totalsales'] <= $total_sales;
        }
        return $show_label;
    }
}
new BeRocket_apl_default_conditions();
