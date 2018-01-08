<?php
/*
Plugin Name: Smart Variation Swatches for WooCommerce
Plugin URI: https://athemeart.com/downloads/smart-variation-swatches-woocommerce-pro/
Description: An extension of WooCommerce that make variable products be more beauty and friendly with customers.
Version: 1.0 
Author: aThemeArt
Author URI: http://athemeart.com/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * The main plugin class
 */
final class ATA_WC_Variation_Swatches {
	/**
	 * The single instance of the class
	 *
	 * @var ATA_WC_Variation_Swatches
	 */
	protected static $instance = null;

	/**
	 * Extra attribute types
	 *
	 * @var array
	 */
	public $types = array();

	/**
	 * Main instance
	 *
	 * @return ATA_WC_Variation_Swatches
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
		$this->types = array(
			'color' => esc_html__( 'Color', 'atawc_lang' ),
			'image' => esc_html__( 'Image', 'atawc_lang' ),
			'label' => esc_html__( 'Label', 'atawc_lang' ),
			//'radio' => esc_html__( 'Radio', 'atawc_lang' ),
		);

		$this->includes();
		$this->init_hooks();
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		require_once 'inc/class-admin.php';
		require_once 'inc/class-options.php';
		require_once 'inc/default.php';
		require_once 'inc/class-frontend.php';
		require_once 'inc/class-wc-ex-product-data-tab.php';
		
	
		
	}

	/**
	 * Initialize hooks
	 */
	public function init_hooks() {
		add_action( 'init', array( $this, 'load_textdomain' ) );

		add_filter( 'product_attributes_type_selector', array( $this, 'add_attribute_types' ) );

		if ( is_admin() ) {
			add_action( 'init', array( 'ATA_WC_Variation_Swatches_Admin', 'instance' ) );
			add_action( 'init', array( 'ATA_WC_Variation_Swatches_Options', 'instance' ) );
			add_action( 'init', array( 'WC_EX_Product_Data_Tab_Swatches', 'instance' ) );
			
		} else {
			add_action( 'init', array( 'ATA_WC_Variation_Swatches_Frontend', 'instance' ) );
		}
		
		add_filter( 'plugin_action_links', array( $this, 'go_pro' ), 10, 2 );
	}
	

	/**
	 * Load plugin text domain
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'atawc_lang', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Add extra attribute types
	 * Add color, image and label type
	 *
	 * @param array $types
	 *
	 * @return array
	 */
	public function add_attribute_types( $types ) {
		$types = array_merge( $types, $this->types );

		return $types;
	}

	/**
	 * Get attribute's properties
	 *
	 * @param string $taxonomy
	 *
	 * @return object
	 */
	public function get_tax_attribute( $taxonomy ) {
		global $wpdb;

		$attr = substr( $taxonomy, 3 );
		$attr = $wpdb->get_row( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies WHERE attribute_name = '$attr'" );

		return $attr;
	}

	

	/**
	 * Instance of frontend
	 *
	 * @return ATA_WC_Variation_Swatches_Frontend
	 */
	public function frontend() {
		return ATA_WC_Variation_Swatches_Frontend::instance();
	}
	
	public function go_pro( $actions, $file ) {
		if ( $file == plugin_basename( __FILE__ )) {
			$actions['ata_go_pro'] = '<a href="https://athemeart.com/downloads/smart-variation-swatches-woocommerce-pro/" target="_blank" style="color: red; font-weight: bold">Go Pro!</a>';
			$action = $actions['ata_go_pro'];
			unset( $actions['ata_go_pro'] );
			array_unshift( $actions, $action );
		}
		return $actions;
	}
}



/**
 * Main instance of plugin
 *
 * @return ATA_WC_Variation_Swatches
 */
function ATA_WCVS() {
	return ATA_WC_Variation_Swatches::instance();
}

/**
 * Display notice in case of WooCommerce plugin is not activated
 */
function ata_wc_variation_swatches_wc_notice() {
	?>

	<div class="error">
		<p><?php esc_html_e( 'Soo Product Attribute Swatches is enabled but not effective. It requires WooCommerce in order to work.', 'atawc_lang' ); ?></p>
	</div>

	<?php
}

/**
 * Construct plugin when plugins loaded in order to make sure WooCommerce API is fully loaded
 * Check if WooCommerce is not activated then show an admin notice
 * or create the main instance of plugin
 */
function ata_wc_variation_swatches_constructor() {
	if ( ! function_exists( 'WC' ) ) {
		add_action( 'admin_notices', 'ata_wc_variation_swatches_wc_notice' );
	} else {
		ATA_WCVS();
	}
}

add_action( 'plugins_loaded', 'ata_wc_variation_swatches_constructor' );

