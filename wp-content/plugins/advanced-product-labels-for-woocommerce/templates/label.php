<?php 
global $pagenow, $post;
$label = array(
    'content_type'          => 'text',
    'text'                  => '',
    'image'                 => '',
    'type'                  => 'label',
    'padding_top'           => '-10',
    'padding_horizontal'    => '0',
    'border_radius'         => '50',
    'border_width'          => '0',
    'border_color'          => 'ffffff',
    'image_height'          => '50',
    'image_width'           => '50',
    'color_use'             => '1',
    'color'                 => 'ff0000',
    'font_color'            => 'ffffff',
    'font_size'             => '14',
    'line_height'           => '50',
    'position'              => 'left',
    'rotate'                => '0deg',
    'zindex'                => '500',
    'data'                  => array()
);
$label = apply_filters('berocket_labels_default_values', $label);
if( ! in_array( $pagenow, array( 'post-new.php' ) ) ) {
    $label_type = get_post_meta( $post->ID, 'br_label', true );
    if( isset($label_type) && is_array($label_type) ) {
        $label = array_merge($label, $label_type);
    }
}
if(! empty($one_product)) {
    echo '<div class="panel wc-metaboxes-wrapper" id="br_alabel" style="display: none;">';
} else {
    echo '<div class="submitbox" id="submitpost">';
}
$BeRocket_products_label_var = BeRocket_products_label::getInstance();
echo '<div class="br_framework_settings br_alabel_settings">';
?>
<div class="berocket_label_preview_wrap">
    <div class="berocket_label_preview">
        <img class="berocket_product_image" src="<?php echo plugin_dir_url(__FILE__).'../images/labels.png'; ?>">
    </div>
</div>
<?php
$BeRocket_products_label_var->display_admin_settings(
    array(
        'General' => array(
            'icon' => 'cog',
        ),
        'Position'     => array(
            'icon' => 'arrows',
        ),
        'Style'     => array(
            'icon' => 'css3',
        ),
    ),
    array(
        'General' => array(
            'content_type' => array(
                "type"     => "selectbox",
                "options"  => array(
                    array('value' => 'text', 'text' => __('Text', 'BeRocket_products_label_domain')),
                    array('value' => 'sale_p', 'text' => __('Discount percentage', 'BeRocket_products_label_domain')),
                ),
                "class"    => 'berocket_label_content_type',
                "label"    => __('Content type', 'BeRocket_products_label_domain'),
                "name"     => "content_type",
                "value"    => $label['content_type'],
            ),
            'text' => array(
                "type"     => "text",
                "label"    => __('Text', 'BeRocket_products_label_domain'),
                "class"    => 'berocket_label_ berocket_label_text',
                "name"     => "text",
                "value"    => $label['text'],
            ),
            'discount_minus' => array(
                "type"     => "checkbox",
                "label"    => __('Use minus symbol', 'BeRocket_products_label_domain'),
                "class"    => 'berocket_label_ berocket_label_sale_p',
                "name"     => "discount_minus",
                "value"    => "1",
                "selected" => false
            ),
        ),
        'Position'     => array(
            'type' => array(
                "type"     => "selectbox",
                "options"  => array(
                    array('value' => 'label', 'text' => __('Label', 'BeRocket_products_label_domain')),
                    array('value' => 'image', 'text' => __('On image', 'BeRocket_products_label_domain')),
                ),
                "class"    => 'berocket_label_type_select',
                "label"    => __('Type', 'BeRocket_products_label_domain'),
                "name"     => "type",
                "value"    => $label['type'],
            ),
            'padding_top' => array(
                "type"     => "number",
                "label"    => __('Padding from top', 'BeRocket_products_label_domain'),
                "class"    => 'berocket_label_type_ berocket_label_type_image br_js_change',
                "name"     => "padding_top",
                "extra"    => ' data-for=".br_alabel" data-style="top" data-ext="px"',
                "value"    => $label['padding_top'],
            ),
            'padding_horizontal' => array(
                "type"     => "number",
                "label"    => '<span class="pos__ pos__left">' . __('Padding from left: ', 'BeRocket_products_label_domain') . '</span><span class="pos__ pos__right">' . __('Padding from right: ', 'BeRocket_products_label_domain') . '</span>',
                "class"    => 'berocket_label_type_ berocket_label_type_image pos_label_ pos_label_right pos_label_left br_js_change',
                "name"     => "padding_horizontal",
                "extra"    => ' data-for=".br_alabel" data-from=".pos_label" data-ext="px"',
                "value"    => $label['padding_horizontal'],
            ),
            'position' => array(
                "type"     => "selectbox",
                "options"  => array(
                    array('value' => 'left', 'text' => __('Left', 'BeRocket_products_label_domain')),
                    array('value' => 'center', 'text' => __('Center', 'BeRocket_products_label_domain')),
                    array('value' => 'right', 'text' => __('Right', 'BeRocket_products_label_domain')),
                ),
                "class"    => 'pos_label',
                "label"    => __('Position', 'BeRocket_products_label_domain'),
                "name"     => "position",
                "value"    => $label['position'],
            ),
        ),
        'Style'     => array(
            'color_use' => array(
                "type"     => "checkbox",
                "label"    => __('Use background color', 'BeRocket_products_label_domain'),
                "class"    => 'br_label_backcolor_use br_js_change',
                "name"     => "color_use",
                "value"    => "1",
                "extra"    => ' data-for=".br_alabel span" data-style="use:background-color" data-ext=""',
                "selected" => false
            ),
            'color' => array(
                "type"     => "color",
                "label"    => __('Background color', 'BeRocket_products_label_domain'),
                "name"     => "color",
                "class"    => 'br_label_backcolor br_js_change',
                "extra"    => ' data-for=".br_alabel span" data-style="background-color" data-ext=""',
                "value"    => $label['color'],
            ),
            'font_color' => array(
                "type"     => "color",
                "label"    => __('Font color', 'BeRocket_products_label_domain'),
                "name"     => "font_color",
                "class"    => 'berocket_label_ berocket_label_text berocket_label_sale_end berocket_label_sale_p br_js_change',
                "extra"    => ' data-for=".br_alabel span" data-style="color" data-ext=""',
                "value"    => $label['font_color'],
            ),
            'border_radius' => array(
                "type"     => "number",
                "label"    => __('Border radius', 'BeRocket_products_label_domain'),
                "name"     => "border_radius",
                "class"    => "br_js_change",
                "extra"    => ' min="0" max="400" data-for=".br_alabel span" data-style="border-radius" data-ext="px"',
                "value"    => '10',
            ),
            'line_height' => array(
                "type"     => "number",
                "label"    => __('Line height', 'BeRocket_products_label_domain'),
                "name"     => "line_height",
                "class"    => "br_js_change",
                "extra"    => ' min="0" max="400" data-for=".br_alabel span" data-style="line-height" data-ext="px"',
                "value"    => $label['line_height'],
            ),
            'image_height' => array(
                "type"     => "number",
                "label"    => __('Height', 'BeRocket_products_label_domain'),
                "name"     => "image_height",
                "class"    => "br_js_change",
                "extra"    => ' data-for=".br_alabel span" data-style="height" data-ext="px"',
                "value"    => $label['image_height'],
            ),
            'image_width' => array(
                "type"     => "number",
                "label"    => __('Width', 'BeRocket_products_label_domain'),
                "name"     => "image_width",
                "class"    => "br_js_change",
                "extra"    => ' data-for=".br_alabel span" data-style="width" data-ext="px"',
                "value"    => $label['image_width'],
            ),
        ),
    ),
    array(
        'name_for_filters' => 'berocket_advanced_label_editor',
        'hide_header' => true,
        'hide_form' => true,
        'hide_additional_blocks' => true,
        'hide_save_button' => true,
        'settings_name' => 'br_label',
        'options' => $label
    )
);
echo '</div>';
?>
</div>
<style>
    .br_label_condition .br_cond_one .br_cond_select:first-child .berocket_remove_condition {
        display: none;
    }
    .br_label_condition {
        border: 1px solid #999;
        background-color: #fafafa;
        padding: 0.5em;
        margin-bottom: 1em;
        position: relative;
    }
    .br_label_condition .br_remove_group {
        position: absolute!important;
        top:-10px;
        right: -10px;
    }
    .br_cond_select {
        padding-bottom: 1em;
    }
    .br_cond_select {
        border: 1px solid #999;
        padding: 0.5em;
        margin-bottom: 0.5em;
        background-color: #eee;
    }
    .br_framework_settings .button.berocket_remove_condition,
    .br_framework_settings .button.berocket_add_condition,
    .br_framework_settings .button.br_remove_group,
    .br_framework_settings .button.br_add_group {
        padding: 0 10px;
        margin: 0;
        width: initial;
        min-width: initial;
    }
    
    .berocket_label_preview_wrap {
        display: inline-block;
        width: 240px;
        padding: 20px;
        background: white;
        position: fixed;
        top: 100%;
        margin-top: -320px;
        min-height: 320px;
        right: 20px;
        box-sizing: border-box;
    }
    .berocket_label_preview_wrap .berocket_label_preview {
        position: relative;
    }
    .berocket_label_preview_wrap .berocket_product_image {
        display: block;
        width: 200px;
    }
    @media screen and (max-width: 850px) {
        .berocket_label_preview_wrap {
            position: relative;
        }
    }
</style>
