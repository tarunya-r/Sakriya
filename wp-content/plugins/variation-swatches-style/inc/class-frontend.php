<?php

/**
 * Class ATA_WC_Variation_Swatches_Frontend
 */
class ATA_WC_Variation_Swatches_Frontend {
	/**
	 * The single instance of the class
	 *
	 * @var ATA_WC_Variation_Swatches_Frontend
	 */
	protected static $instance = null;

	/**
	 * Main instance
	 *
	 * @return ATA_WC_Variation_Swatches_Frontend
	 */
	public static function instance() {
		if ( null == self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array( $this, 'get_swatch_html' ), 100, 2 );
		add_filter( 'atawc_swatch_html', array( $this, 'swatch_html' ), 5, 5 );
		add_filter( 'atawc_swatch_meta_html', array( $this, 'atawc_swatch_meta_html' ), 5, 5);
		add_action('wp_head',  array( $this, 'dynamic_style' ), 999);
	}
	
	/**
	 * Dynamic stylesheets
	 */
	public function dynamic_style() {
		$options = atawcvs_get_option('atawc_label');	
	?>
		<style type="text/css">
			
			<?php if ( isset($options) && is_array( $options )):?> 
				.atawc-swatches .swatch.swatch-label{
					font-size:<?php echo ( isset( $options ['lebel_variation_size'] ) ) ? $options ['lebel_variation_size'] : 12;?>px;
					color:<?php echo ( isset( $options ['lebel_variation_color'] ) ) ? $options ['lebel_variation_color'] : '#c8c8c8';?>;
					background:<?php echo ( isset( $options ['lebel_variation_background'] ) ) ? $options ['lebel_variation_background'] : '#fff';?>;
					border:1px solid <?php echo ( isset( $options ['lebel_variation_border'] ) ) ? $options ['lebel_variation_border'] : '#000';?>;
					
				}
				.atawc-swatches .swatch.swatch-label:hover,
				.atawc-swatches .swatch.swatch-label.selected{
					color:<?php echo ( isset( $options ['lebel_variation_color_hover'] ) ) ? $options ['lebel_variation_color_hover'] : '#000';?>;
					background:<?php echo ( isset( $options ['lebel_variation_background_hover'] ) ) ? $options ['lebel_variation_background_hover'] : '#c8c8c8';?>;
					border:1px solid <?php echo ( isset( $options ['lebel_variation_border_hover'] ) ) ? $options ['lebel_variation_border_hover'] : '#c8c8c8';?>;
				}
		<?php endif;?>
        </style>
    <?php
	}

	/**
	 * Enqueue scripts and stylesheets
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'atawc-frontend', plugins_url( 'assets/css/frontend.css', dirname( __FILE__ ) ), array(), '20160615' );
		wp_enqueue_script( 'atawc-frontend', plugins_url( 'assets/js/frontend.js', dirname( __FILE__ ) ), array( 'jquery' ), '20160615', true );
	}

	/**
	 * Filter function to add swatches bellow the default selector
	 *
	 * @param $html
	 * @param $args
	 *
	 * @return string
	 */
	public function get_swatch_html( $html, $args ) {
		$swatch_types = ATA_WCVS()->types;
		$attr         = ATA_WCVS()->get_tax_attribute( $args['attribute'] );

		// Return if this is normal attribute
		
		
	
		$options   = $args['options'];
		$product   = $args['product'];
		$attribute = $args['attribute'];
		$name      = $args['name'] ? $args['name'] : sanitize_title( $attribute );
		$id        = $args['id'] ? $args['id'] : sanitize_title( $attribute );
		$class     = isset($attr->attribute_type) ? "variation-selector variation-select-{$attr->attribute_type}" : '';
		$swatches  = '';
		$swatch_type_options = $product->get_meta('_swatch_type_options', true);
		$meta_options = array();
	
	
		
		 
		
		
		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}
		
		
		
		if ( ! empty( $options ) ) {
			
			
				if ( ! empty( $options ) && $product && taxonomy_exists( $attribute ) ) {
					// Get terms if this is a taxonomy - ordered. We need the names too.
					$terms = wc_get_product_terms( $product->get_id(), $attribute, array( 'fields' => 'all' ) );
					
					$key = md5( sanitize_title( $attribute ) );
					
					foreach ( $terms as $term ) {
						
						if ( in_array( $term->slug, $options ) ) {
							
								$meta_key = md5( $term->slug );
								
								if ( isset( $swatch_type_options[$key] ) && $swatch_type_options[$key]['type'] == 'product_color' ) {
									$meta_options['value'] =  ( isset (  $swatch_type_options[ $key ][ $meta_key ]['color'] ) && $swatch_type_options[ $key ][ $meta_key ]['color'] != "" ) ? $swatch_type_options[ $key ][ $meta_key ]['color'] :''; 
									$meta_options['type'] = 'color';
								}
								
								if ( isset( $swatch_type_options[$key] ) && $swatch_type_options[$key]['type'] == 'product_image' ) {
									$meta_options['value'] =  ( isset (  $swatch_type_options[ $key ][ $meta_key ]['image'] ) && $swatch_type_options[ $key ][ $meta_key ]['image'] != "" ) ? $swatch_type_options[ $key ][ $meta_key ]['image'] :''; 
									$meta_options['type'] = 'image';
									
								}
								if ( isset( $swatch_type_options[$key] ) && $swatch_type_options[$key]['type'] == 'product_label' ) {
									$meta_options['value'] =  ''; 
									$meta_options['type'] = 'label';
								}
								
							
							$swatches .= apply_filters( 'atawc_swatch_html', '', $term, $attr, $args, $meta_options );
						}
					}
				
		}else{
			$attributes = $product->get_variation_attributes();
			if ( isset($options) && $options != "" ) :
				
				$key = md5( sanitize_title( $attribute ) );
				$type = NULL;
				
				
				foreach ( $options as $option ) :
					$meta_key = ( md5( sanitize_title( strtolower( $option ) ) ) );
					
					$selected = ( sanitize_title( $args['selected'] ) === $args['selected'] &&  $args['selected'] ==  sanitize_title( $option ) ) ? 'selected' : '';
					
			
					
					if ( isset( $swatch_type_options[$key] ) && $swatch_type_options[$key]['type'] == 'product_color' ) {
						
						$meta_value =  ( isset (  $swatch_type_options[ $key ][ $meta_key ]['color'] ) && $swatch_type_options[ $key ][ $meta_key ]['color'] != "" ) ? $swatch_type_options[ $key ][ $meta_key ]['color'] :'';
						$type = 'color';
						
					}elseif(  isset( $swatch_type_options[$key] ) && $swatch_type_options[$key]['type'] == 'product_image'  ){
						
						$meta_value =  ( isset (  $swatch_type_options[ $key ][ $meta_key ]['image'] ) && $swatch_type_options[ $key ][ $meta_key ]['image'] != "" ) ? $swatch_type_options[ $key ][ $meta_key ]['image'] :'';
						$type = 'image';
					
						
					}elseif(  isset( $swatch_type_options[$key] ) && $swatch_type_options[$key]['type'] == 'product_label'  ){
						
						$meta_value = '';
						$type = 'label';
						
						
					}
					if( $type != "" )
					$swatches .= apply_filters( 'atawc_swatch_meta_html', '',$type, $option, $meta_value, $selected );
				endforeach;
				
			endif;	
			
		}
			
			if ( ! empty( $swatches ) ) {
				$class .= ' hidden';

				$swatches = '<div class="atawc-swatches" data-attribute_name="attribute_' . esc_attr( $attribute ) . '">' . $swatches . '</div>';
				$html     = '<div class="' . esc_attr( $class ) . '">' . $html . '</div>' . $swatches;
			}
		}
	
		return $html;
	}
	
		/**
	 * Print HTML of a single swatch
	 *
	 * @param $html
	 * @param $term
	 * @param $attr
	 * @param $args
	 *
	 * @return string
	 */
	public function atawc_swatch_meta_html( $html, $type, $meta_option, $meta_value , $selected = NULL ) {
		
	
		
		
		switch ( $type ) {
			
			case 'color':
			
				$options = atawcvs_get_option('atawc_color');
				$width = ( isset( $options['color_variation_width'] ) && $options['color_variation_width'] != "" ) ? $options['color_variation_width'] : 40 ;
				$height = ( isset( $options['color_variation_height'] ) && $options['color_variation_height'] != "" ) ? $options['color_variation_height'] : 40 ;
				$style = ( isset( $options['color_variation_style'] ) && $options['color_variation_style'] != "" ) ? $options['color_variation_style'] : 'round' ;
				$active = ( isset( $options['color_variation_ingredient'] ) && $options['color_variation_ingredient'] != "" ) ? $options['color_variation_ingredient'] : 'tick_sign' ;
				//masterTooltip 
				$tooltip = ( isset( $options['color_variation_tooltip'] ) && $options['color_variation_tooltip'] == "yes" ) ? 'masterTooltip' : '' ;
				
				
				$html = sprintf(
					'<span class="swatch swatch-color swatch-%s %s %s %s %s" style="background-color:%s; width:%spx; height:%spx;" title="%s" data-value="%s">%s</span>',
					esc_attr( $meta_option ),
					$selected,
					$style,
					$active,
					$tooltip,
					esc_attr( $meta_value ),
					$width,
					$height,
					esc_attr( $meta_option ),
					esc_attr( $meta_option ),
					$meta_option
				);
				break;
				case 'image':
				$options = atawcvs_get_option('atawc_images');
				$width = ( isset( $options['image_variation_width'] ) && $options['image_variation_width'] != "" ) ? $options['image_variation_width'] : 44 ;
				$height = ( isset( $options['image_variation_height'] ) && $options['image_variation_height'] != "" ) ? $options['image_variation_height'] : 44 ;
				$style = ( isset( $options['image_variation_style'] ) && $options['image_variation_style'] != "" ) ? $options['image_variation_style'] : 'round_corner' ;
				$active = ( isset( $options['image_variation_ingredient'] ) && $options['image_variation_ingredient'] != "" ) ? $options['image_variation_ingredient'] : 'tick_sign' ;
				
				$tooltip = ( isset( $options['image_variation_tooltip'] ) && $options['image_variation_tooltip'] == "yes" ) ? 'masterTooltip' : '' ;
				
				$image = $meta_value;
				$image = $image ? wp_get_attachment_image_src( $image ) : '';
				$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';
				$html  = sprintf(
					'<span class="swatch swatch-image swatch-%s %s %s %s %s" title="%s" data-value="%s"  style="width:%spx; height:%spx;"><img src="%s" alt="%s">%s</span>',
					esc_attr( $term->slug ),
					$selected,
					$style,
					$active,
					$tooltip,
					esc_attr( $meta_option ),
					esc_attr( $meta_option ),
					$width,
					$height,
					esc_url( $image ),
					esc_attr( $meta_option ),
					esc_attr( $meta_option )
				);
				break;
				
				case 'label':
			
				$options = atawcvs_get_option('atawc_label');
				$width = ( isset( $options['lebel_variation_width'] ) && $options['lebel_variation_width'] != "" ) ? $options['lebel_variation_width'] : 44 ;
				$height = ( isset( $options['lebel_variation_height'] ) && $options['lebel_variation_height'] != "" ) ? $options['lebel_variation_height'] : 44 ;
				$style = ( isset( $options['lebel_variation_style'] ) && $options['lebel_variation_style'] != "" ) ? $options['lebel_variation_style'] : 'square' ;
				
				$active = ( isset( $options['lebel_variation_ingredient'] ) && $options['lebel_variation_ingredient'] != "" ) ? $options['lebel_variation_ingredient'] : 'opacity' ;
				
				
				$html  = sprintf(
					'<span class="swatch swatch-label swatch-%s %s %s %s" title="%s" data-value="%s"  style="width:%spx; height:%spx; line-height:%spx;">%s</span>',
					esc_attr( $meta_option ),
					$selected,
					$style,
					$active,
					esc_attr( $meta_option ),
					esc_attr( $meta_option ),
					$width,
					$height,
					$height,
					esc_html( $meta_option )
				);
				break;
				
		}

		return $html;
	}
	
	/**
	 * Print HTML of a single swatch
	 *
	 * @param $html
	 * @param $term
	 * @param $attr
	 * @param $args
	 *
	 * @return string
	 */
	public function swatch_html( $html, $term, $attr, $args, $swatch_meta_options = array() ) {
		
		$selected = sanitize_title( $args['selected'] ) == $term->slug ? 'selected' : '';
		$name     = esc_html( apply_filters( 'woocommerce_variation_option_name', $term->name ) );
	
		
		
		$type =  isset( $swatch_meta_options['type'] ) ? $swatch_meta_options['type'] : $attr->attribute_type;
	
		
		switch ( $type ) {
			case 'color':
				$options = atawcvs_get_option('atawc_color');
				$width = ( isset( $options['color_variation_width'] ) && $options['color_variation_width'] != "" ) ? $options['color_variation_width'] : 40 ;
				$height = ( isset( $options['color_variation_height'] ) && $options['color_variation_height'] != "" ) ? $options['color_variation_height'] : 40 ;
				$style = ( isset( $options['color_variation_style'] ) && $options['color_variation_style'] != "" ) ? $options['color_variation_style'] : 'round' ;
				$active = ( isset( $options['color_variation_ingredient'] ) && $options['color_variation_ingredient'] != "" ) ? $options['color_variation_ingredient'] : 'tick_sign' ;
				//masterTooltip 
				$tooltip = ( isset( $options['color_variation_tooltip'] ) && $options['color_variation_tooltip'] == "yes" ) ? 'masterTooltip' : '' ;
				
				
				
				$color = isset( $swatch_meta_options['value'] ) ? $swatch_meta_options['value'] : get_term_meta( $term->term_id, 'color', true );
				
				list( $r, $g, $b ) = sscanf( $color, "#%02x%02x%02x" );
				$html = sprintf(
					'<span class="swatch swatch-color swatch-%s %s %s %s %s" style="background-color:%s;color:%s; width:%spx; height:%spx;" title="%s" data-value="%s">%s</span>',
					esc_attr( $term->slug ),
					$selected,
					$style,
					$active,
					$tooltip,
					esc_attr( $color ),
					"rgba($r,$g,$b,0.5)",
					$width,
					$height,
					esc_attr( $name ),
					esc_attr( $term->slug ),
					$name
				);
				break;

			case 'image':
				$options = atawcvs_get_option('atawc_images');
				$width = ( isset( $options['image_variation_width'] ) && $options['image_variation_width'] != "" ) ? $options['image_variation_width'] : 44 ;
				$height = ( isset( $options['image_variation_height'] ) && $options['image_variation_height'] != "" ) ? $options['image_variation_height'] : 44 ;
				$style = ( isset( $options['image_variation_style'] ) && $options['image_variation_style'] != "" ) ? $options['image_variation_style'] : 'round_corner' ;
				$active = ( isset( $options['image_variation_ingredient'] ) && $options['image_variation_ingredient'] != "" ) ? $options['image_variation_ingredient'] : 'tick_sign' ;
				
				$tooltip = ( isset( $options['image_variation_tooltip'] ) && $options['image_variation_tooltip'] == "yes" ) ? 'masterTooltip' : '' ;
				
				
				$image = isset( $swatch_meta_options['value'] ) ? $swatch_meta_options['value'] : get_term_meta( $term->term_id, 'image', true );
			
				
				$image = $image ? wp_get_attachment_image_src( $image ) : '';
				$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';
				$html  = sprintf(
					'<span class="swatch swatch-image swatch-%s %s %s %s %s" title="%s" data-value="%s"  style="width:%spx; height:%spx;"><img src="%s" alt="%s">%s</span>',
					esc_attr( $term->slug ),
					$selected,
					$style,
					$active,
					$tooltip,
					esc_attr( $name ),
					esc_attr( $term->slug ),
					$width,
					$height,
					esc_url( $image ),
					esc_attr( $name ),
					esc_attr( $name )
				);
				break;

			case 'label':
			
				$options = atawcvs_get_option('atawc_label');
				$width = ( isset( $options['lebel_variation_width'] ) && $options['lebel_variation_width'] != "" ) ? $options['lebel_variation_width'] : 44 ;
				$height = ( isset( $options['lebel_variation_height'] ) && $options['lebel_variation_height'] != "" ) ? $options['lebel_variation_height'] : 44 ;
				$style = ( isset( $options['lebel_variation_style'] ) && $options['lebel_variation_style'] != "" ) ? $options['lebel_variation_style'] : 'square' ;
				
				$active = ( isset( $options['lebel_variation_ingredient'] ) && $options['lebel_variation_ingredient'] != "" ) ? $options['lebel_variation_ingredient'] : 'opacity' ;
				
				
				$html  = sprintf(
					'<span class="swatch swatch-label swatch-%s %s %s %s" title="%s" data-value="%s"  style="width:%spx; height:%spx; line-height:%spx;">%s</span>',
					esc_attr( $term->slug ),
					$selected,
					$style,
					$active,
					esc_attr( $name ),
					esc_attr( $term->slug ),
					$width,
					$height,
					$height,
					esc_html( $name )
				);
				break;
				
				case 'radio':
				$label = get_term_meta( $term->term_id, 'radio', true );
				$label = $label ? $label : $name;
				$html  = sprintf(
					'<label class="swatch_radio swatch-label swatch-%s %s" title="%s" data-value="%s"><input type="radio"  name="saiful[]"/> %s </label>',
					esc_attr( $term->slug ),
					$selected,
					esc_attr( $name ),
					esc_attr( $term->slug ),
					esc_html( $label )
				);
				break;
				
				
		}

		return $html;
	}
}