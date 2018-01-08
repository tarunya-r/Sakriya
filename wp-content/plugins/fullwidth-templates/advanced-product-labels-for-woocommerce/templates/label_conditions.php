<?php
global $pagenow, $post;
$label = array(
    'data'                  => array()
);
if( ! in_array( $pagenow, array( 'post-new.php' ) ) ) {
    $label_type = get_post_meta( $post->ID, 'br_label', true );
    if( isset($label_type) && is_array($label_type) ) {
        $label = array_merge($label, $label_type);
    }
}
echo '<div class="submitbox" id="submitpost">';
$product_categories = get_terms( 'product_cat' );
$condition_types = array(
    'product'       => __('Product', 'BeRocket_products_label_domain'),
    'sale'          => __('On Sale', 'BeRocket_products_label_domain'),
    'bestsellers'   => __('Bestsellers', 'BeRocket_products_label_domain'),
    'price'         => __('Price', 'BeRocket_products_label_domain'),
    'stockstatus'   => __('Stock status', 'BeRocket_products_label_domain'),
    'totalsales'    => __('Total sales', 'BeRocket_products_label_domain'),
);
if( is_array($product_categories) && count($product_categories) > 0 ) {
    $condition_types['category'] = __('Product category', 'BeRocket_products_label_domain');
}
$condition_types = apply_filters('berocket_label_condition_types', $condition_types);
?>
    <div class="br_framework_settings">
        <div class="br_label_example" style="display:none;">
            <div class="br_cond_select" data-current="1">
                <span>
                    <select class="br_cond_type">
                        <?php
                        foreach($condition_types as $condition_type_slug => $condition_type_name) {
                            echo '<option value="', $condition_type_slug, '">', $condition_type_name, '</option>';
                        }
                        ?>
                    </select>
                </span>
                <span class="button berocket_remove_condition"><i class="fa fa-minus"></i></span>
                <div class="br_current_cond">
                </div>
            </div>
            <span class="button berocket_add_condition"><i class="fa fa-plus"></i></span>
            <span class="button br_remove_group"><i class="fa fa-minus"></i></span>
        </div>
        <div class="br_cond_example" style="display:none;">
            <?php
            foreach($condition_types as $condition_type_slug => $condition_type_name) {
                $condition_html = apply_filters('berocket_label_condition_type_' . $condition_type_slug, '', '%name%[data][%id%][%current_id%]', array());
                if( ! empty($condition_html) ) {
                    echo '<div class="br_cond br_cond_', $condition_type_slug, '">
                    ', $condition_html, '
                    <input type="hidden" name="%name%[data][%id%][%current_id%][type]" value="', $condition_type_slug, '">
                    </div>';
                }
            }
            ?>
        </div>
        <div class="br_conditions">
            <?php
            $last_id = 0;
            foreach($label['data'] as $id => $data) {
                $current_id = 1;
                ob_start();
                foreach($data as $current => $conditions) {
                    if( $current > $current_id ) {
                        $current_id = $current;
                    }
                    ?>
                    <div class="br_cond_select" data-current="<?php echo $current; ?>">
                        <span>
                            <select class="br_cond_type">
                                <?php
                                foreach($condition_types as $condition_type_slug => $condition_type_name) {
                                    echo '<option value="', $condition_type_slug, '"', ( isset($conditions['type']) && $conditions['type'] == $condition_type_slug ? ' selected' : '' ) , '>', $condition_type_name, '</option>';
                                }
                                ?>
                            </select>
                        </span>
                        <span class="button berocket_remove_condition"><i class="fa fa-minus"></i></span>
                        <div class="br_current_cond">
                        </div>
                    <?php 
                    $condition_html = apply_filters('berocket_label_condition_type_' . $conditions['type'], '', 'br_label[data][' . $id . '][' . $current . ']', $conditions);
                    if( ! empty($condition_html) ) {
                        echo '<div class="br_cond br_cond_', $conditions['type'], '">
                        ', $condition_html, '
                        <input type="hidden" name="br_label[data][' . $id . '][' . $current . '][type]" value="', $conditions['type'], '">
                        </div>';
                    }
                    ?>
                    </div>
                    <?php
                }
                ?>
                <span class="button berocket_add_condition"><i class="fa fa-plus"></i></span>
                <span class="button br_remove_group"><i class="fa fa-minus"></i></span>
                <?php
                $html = ob_get_clean();
                echo '<div class="br_label_condition" data-id="'.$id.'" data-current="'.$current_id.'">';
                echo $html;
                echo '</div>';
                if( $id > $last_id ) {
                    $last_id = $id;
                }
            }
            $last_id++;
            ?>
            <span class="button br_add_group"><i class="fa fa-plus"></i></span>
        </div>
        <script>
            var last_id = <?php echo $last_id; ?>;
            var $html = jQuery('.br_label_example').html();
            $html = '<div class="br_cond_one">'+$html+'</div>';
            jQuery(document).on('change', '.br_cond_type', function(event) {
                var $parent = jQuery(this).parents('.br_cond_select');
                $parent.find('.br_cond').remove();
                var id = $parent.parents('.br_label_condition');
                var current_id = $parent.data('current');
                id = id.data('id');
                var html_need = jQuery('.br_cond_example .br_cond_'+jQuery(this).val()).get(0);
                html_need = html_need.outerHTML;
                html_need = html_need.replace(/%id%/g, id);
                html_need = html_need.replace(/%current_id%/g, current_id);
                html_need = html_need.replace(/%name%/g, 'br_label');
                console.log(html_need);
                $parent.find('.br_current_cond').html(html_need);
            });
            jQuery(document).on('click', '.berocket_add_condition', function() {
                var id = jQuery(this).parents('.br_label_condition');
                var current_id = id.data('current');
                current_id = current_id + 1;
                id.data('current', current_id);
                id = id.data('id');
                var $html = jQuery('.br_label_example .br_cond_select').html();
                $html = '<div class="br_cond_select" data-current="'+current_id+'">'+$html+'</div>'; 
                $html = $html.replace('%id%', id);
                jQuery(this).before($html);
                $parent = jQuery(this).prev();
                $parent.find('.br_cond_type').trigger('change');
            });
            jQuery(document).on('click', '.berocket_remove_condition', function() {
                $parent = jQuery(this).parents('.br_cond_select');
                $parent.remove();
            });
            jQuery(document).on('click', '.br_add_group', function() {
                last_id++;
                var html = $html.replace( '%id%', last_id );
                var html = '<div class="br_label_condition" data-id="'+last_id+'" data-current="1">'+html+'</div>';
                jQuery(this).before(html);
                $parent = jQuery(this).prev();
                $parent.find('.br_cond_type').trigger('change');
            });
            jQuery(document).on('click', '.br_remove_group', function() {
                $parent = jQuery(this).parents('.br_label_condition');
                $parent.remove();
            });
            jQuery(document).on('change', '.br_cond_attr_select', function() {
                var $attr_block = jQuery(this).parents('.br_cond_attribute');
                $attr_block.find('.br_attr_values').hide();
                $attr_block.find('.br_attr_value_'+jQuery(this).val()).show();
            });
            jQuery(document).on('change', '.price_from', function() {
                var val_price_from = jQuery(this).val();
                var val_price_to = jQuery(this).parents('.br_cond').first().find('.price_to').val();
                price_from = parseFloat(val_price_from);
                price_to = parseFloat(val_price_to);
                price_to_int = parseInt(val_price_to);
                if( val_price_from == '' ) {
                    jQuery(this).val(0);
                    price_from = 0;
                }
                if( price_from > price_to ) {
                    jQuery(this).val(price_to_int);
                }
            });
            jQuery(document).on('change', '.price_to', function() {
                var val_price_from = jQuery(this).parents('.br_cond').first().find('.price_from').val();
                var val_price_to = jQuery(this).val();
                price_from = parseFloat(val_price_from);
                price_from_int = parseInt(val_price_from);
                price_to = parseFloat(val_price_to);
                if( val_price_to == '' ) {
                    jQuery(this).val(0);
                    price_to = 0;
                }
                if( price_from > price_to ) {
                    jQuery(this).val(price_from_int);
                }
            });
        </script>
        <style>
            .br_conditions .br_label_condition {
                margin-top: 40px;
            }
            .br_conditions .br_label_condition:first-child {
                margin-top: 0;
            }
            .br_conditions .br_label_condition:before {
                content: "OR";
                display: block;
                position: absolute;
                top: -30px;
                font-size: 30px;
            }
            .br_conditions .br_label_condition:first-child:before {
                display: none;
            }
            .br_label_condition .br_cond_select {
                margin-top: 40px;
                position: relative;
            }
            .br_label_condition .br_cond_select:first-child {
                margin-top: 0;
            }
            .br_label_condition .br_cond_select:before {
                content: "AND";
                display: block;
                position: absolute;
                top: -30px;
                font-size: 30px;
            }
            .br_label_condition .br_cond_select:first-child:before {
                display: none;
            }
        </style>
    </div>
</div>
