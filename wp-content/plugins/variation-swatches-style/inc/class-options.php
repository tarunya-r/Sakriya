<?php

/**
 * WordPress settings API demo class
 *
 * @author Tareq Hasan
 */
if ( !class_exists('ATA_WC_Variation_Swatches_Options' ) ):

class ATA_WC_Variation_Swatches_Options {
	/**
	 * The single instance of the class
	 *
	 * @var ATA_WC_Variation_Swatches_Admin
	 */
	protected static $instance = null;
	
    private $settings_api;

	/**
	 * Main instance
	 *
	 * @return ATA_WC_Variation_Swatches_Admin
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
    function __construct() {
		require_once 'class.settings-api.php';
        $this->settings_api = new WeDevs_Settings_API;

        add_action( 'admin_init', array($this, 'admin_init') );
        add_action( 'admin_menu', array($this, 'admin_menu') );
    }

    function admin_init() {

        //set the settings
        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        //initialize settings
        $this->settings_api->admin_init();
    }

    function admin_menu() {
       // add_options_page( 'Settings API', 'Settings API', 'woocommerce', 'settings_api_test', array($this, 'plugin_page') );
		   add_submenu_page( 'woocommerce', 'Variation Swatches ', 'Variation Swatches', 'manage_options', 'ata-variation-swatches', array($this, 'plugin_page') ); 
    }

    function get_settings_sections() {
		
        $sections = array(
			 array(
                'id'    => 'atawc_label',
                'title' => __( 'Label Variation Settings', 'atawc_lang' )
            ),
            array(
                'id'    => 'atawc_color',
                'title' => __( 'Color Variation Settings', 'atawc_lang' )
            ),
            array(
                'id'    => 'atawc_images',
                'title' => __( 'Images Variation Settings', 'atawc_lang' )
            )
           
			
        );
        return $sections;
    }
	

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'atawc_label' => array(
				array(
                    'name'    => 'lebel_variation_style',
                    'label'   => __( 'label Variation Style', 'atawc_lang' ),
                    'type'    => 'select',
                    'default' => 'square',
                    'options' => array(
                        'square' => __( 'Square', 'atawc_lang' ),
                        'round'  => __( 'Circle', 'atawc_lang' ),
						'round_corner'  => __( 'Round corner', 'atawc_lang' ),
                    )
                ),
                array(
                    'name'              => 'lebel_variation_width',
                    'label'             => __( 'label Variation Button Width', 'atawc_lang' ),
                    'default' 			=> 40,
                    'type'              => 'number',
                    'sanitize_callback' => 'number'
                ),
				array(
                    'name'              => 'lebel_variation_height',
                    'label'             => __( 'label Variation Button Height', 'atawc_lang' ),
                    'default' 			=> 40,
                    'type'              => 'number',
                    'sanitize_callback' => 'number'
                ),
				array(
                    'name'              => 'lebel_variation_size',
                    'label'             => __( 'label Variation Font Size', 'atawc_lang' ),
                    'default' 			=> 13,
                    'type'              => 'number',
                    'sanitize_callback' => 'number',
					'desc'    => __( 'PX', 'atawc_lang' ),
                ),
               array(
                    'name'    => 'lebel_variation_color',
                    'label'   => __( 'label Variation Button Color', 'atawc_lang' ),
                    'type'    => 'color',
                    'default' => '#c8c8c8'
                ),
				array(
                    'name'    => 'lebel_variation_color_hover',
                    'label'   => __( 'label Variation Button Hover Color', 'atawc_lang' ),
                    'type'    => 'color',
                    'default' => '#000'
                ),
				array(
                    'name'    => 'lebel_variation_background',
                    'label'   => __( 'label Variation Button Background', 'atawc_lang' ),
                    'type'    => 'color',
                    'default' => '#fff'
                ),
				array(
                    'name'    => 'lebel_variation_background_hover',
                    'label'   => __( 'label Variation Button Background Hover', 'atawc_lang' ),
                    'type'    => 'color',
                    'default' => '#c8c8c8'
                ),
				
				array(
                    'name'    => 'lebel_variation_border',
                    'label'   => __( 'label Variation border Color', 'atawc_lang' ),
                    'type'    => 'color',
                    'default' => '#000'
                ),
				array(
                    'name'    => 'lebel_variation_border_hover',
                    'label'   => __( 'label Variation border Color Hover', 'atawc_lang' ),
                    'type'    => 'color',
                    'default' => '#c8c8c8'
                ),
				array(
                    'name'    => 'lebel_variation_ingredient',
                    'label'   => __( 'label active ingredient', 'atawc_lang' ),
                    'type'    => 'select',
                    'default' => 'opacity',
                    'options' => array(
                        'tick_sign' => __( 'Tick sign', 'atawc_lang' ),
                        'opacity'  => __( 'Opacity', 'atawc_lang' ),
						'zoom_up'  => __( 'Zoom Up', 'atawc_lang' ),
						'zoom_down'  => __( 'Zoom Down', 'atawc_lang' ),
                    )
                ),
            ),
            'atawc_color' => array(
               array(
                    'name'    => 'color_variation_style',
                    'label'   => __( 'Color Variation Style', 'atawc_lang' ),
                    'type'    => 'select',
                    'default' => 'round',
                    'options' => array(
                        'square' => __( 'Square', 'atawc_lang' ),
                        'round'  => __( 'Circle', 'atawc_lang' ),
						'round_corner'  => __( 'Round corner', 'atawc_lang' ),
                    )
                ),
                array(
                    'name'              => 'color_variation_width',
                    'label'             => __( 'Color Variation Button Width', 'atawc_lang' ),
                    'default' 			=> 40,
                    'type'              => 'number',
                    'sanitize_callback' => 'number'
                ),
				array(
                    'name'              => 'color_variation_height',
                    'label'             => __( 'Color Variation Button Height', 'atawc_lang' ),
                    'default' 			=> 40,
                    'type'              => 'number',
                    'sanitize_callback' => 'number'
                ),
				
				array(
                    'name'    => 'color_variation_tooltip',
                    'label'   => __( 'Color Variation tooltip', 'atawc_lang' ),
                    'type'    => 'select',
                    'default' => 'yes',
                    'options' => array(
                        'yes' => __( 'Yes', 'atawc_lang' ),
                        'no'  => __( 'No', 'atawc_lang' ),
                    )
                ),
				array(
                    'name'    => 'color_variation_ingredient',
                    'label'   => __( 'Color active ingredient', 'atawc_lang' ),
                    'type'    => 'select',
                    'default' => 'tick_sign',
                    'options' => array(
                        'tick_sign' => __( 'Tick sign', 'atawc_lang' ),
                        'opacity'  => __( 'Opacity', 'atawc_lang' ),
						'zoom_up'  => __( 'Zoom Up', 'atawc_lang' ),
						'zoom_down'  => __( 'Zoom Down', 'atawc_lang' ),
                    )
                ),
				
            ),
            'atawc_images' => array(
               array(
                    'name'    => 'image_variation_style',
                    'label'   => __( 'Color Variation Style', 'atawc_lang' ),
                    'type'    => 'select',
                    'default' => 'round_corner',
                    'options' => array(
                        'square' => __( 'Square', 'atawc_lang' ),
                        'round'  => __( 'Circle', 'atawc_lang' ),
						'round_corner'  => __( 'Round corner', 'atawc_lang' ),
                    )
                ),
                array(
                    'name'              => 'image_variation_width',
                    'label'             => __( 'Image Variation Button Width', 'atawc_lang' ),
                    'default' 			=> 44,
                    'type'              => 'number',
                    'sanitize_callback' => 'number'
                ),
				array(
                    'name'              => 'image_variation_height',
                    'label'             => __( 'Image Variation Button Height', 'atawc_lang' ),
                    'default' 			=> 44,
                    'type'              => 'number',
                    'sanitize_callback' => 'number'
                ),
				
				array(
                    'name'    => 'image_variation_tooltip',
                    'label'   => __( 'Image Variation tooltip', 'atawc_lang' ),
                    'type'    => 'select',
                    'default' => 'yes',
                    'options' => array(
                        'yes' => __( 'Yes', 'atawc_lang' ),
                        'no'  => __( 'No', 'atawc_lang' ),
                    )
                ),
				
				array(
                    'name'    => 'image_variation_ingredient',
                    'label'   => __( 'Image active ingredient', 'atawc_lang' ),
                    'type'    => 'select',
                    'default' => 'tick_sign',
                    'options' => array(
                        'tick_sign' => __( 'Tick sign', 'atawc_lang' ),
                        'opacity'  => __( 'Opacity', 'atawc_lang' ),
						'zoom_up'  => __( 'Zoom Up', 'atawc_lang' ),
						'zoom_down'  => __( 'Zoom Down', 'atawc_lang' ),
                    )
                ),
				
            )
        );

        return $settings_fields;
    }

    function plugin_page() {
        echo '<div class="wrap">';
        $this->settings_api->show_navigation();
        $this->settings_api->show_forms();
        echo '</div>';
    }


}


endif;
