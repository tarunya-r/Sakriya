<?php

if ( ! function_exists( 'atawcvs_get_default_theme_options' ) ) :

	/**
	 * Get default theme options
	 *
	 * @since 1.0.0
	 *
	 * @return array Default Plugins options.
	 */
	function atawcvs_get_default_theme_options() {

		$defaults = array();
		
		
		// Slider Section.
		$defaults['atawc_color'] = array(
			'color_variation_style' => 'round',
			'color_variation_width' => 40,
			'color_variation_height' => 40,
			'color_variation_tooltip' => 'yes',
		);
		
		$defaults['atawc_images'] = array(
			'image_variation_style' => 'round',
			'image_variation_width' => 40,
			'image_variation_height' => 40,
			'image_variation_tooltip' => 'yes',
		);
		
		$defaults['atawc_images'] = array(
			'lebel_variation_style' => 'square',
			'lebel_variation_width' => 40,
			'lebel_variation_height' => 30,
			'lebel_variation_size' => '13',
			'lebel_variation_color' => '#000',
			'lebel_variation_color_hover' => '#000',
			'lebel_variation_background' => '#c8c8c8',
			'lebel_variation_background_hover' => '#c8c8c8',
			'lebel_variation_border' => '#000',
			'lebel_variation_border_hover' => '#c8c8c8',
			'lebel_variation_ingredient' => 'opacity',
		);
	

		return $defaults;

	}

endif;



if ( ! function_exists( 'atawcvs_get_option' ) ) :

	/**
	 * Get theme option.
	 *
	 * @since 1.0.0
	 *
	 * @param string $key Option key.
	 * @return mixed Option value.
	 */
	function atawcvs_get_option( $key ) {

		if ( empty( $key ) ) {
			return;
		}

		$value = '';

		$default = atawcvs_get_default_theme_options();
		$default_value = null;

		if ( is_array( $default ) && isset( $default[ $key ] ) ) {
			$default_value = $default[ $key ];
		}

		if ( null !== $default_value ) {
			$value = get_option( $key, $default_value );
		}
		else {
			$value = get_option( $key );
		}
		

		return $value;
	}

endif;

