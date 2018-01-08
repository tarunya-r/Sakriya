<?php
/**
 * UserRegistration Admin.
 *
 * @class    UR_Admin
 * @version  1.0.0
 * @package  UserRegistration/Form
 * @category Admin
 * @author   WPEverest
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * UR_Admin Class
 */
class UR_Checkbox extends UR_Form_Field {

	private static $_instance;

	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Hook in tabs.
	 */
	public function __construct() {

		$this->id = 'user_registration_checkbox';

		$this->form_id = 1;

		$this->registered_fields_config = array(

			'label' => __( 'Checkbox', 'user-registration' ),

			'icon' => 'dashicons dashicons-yes',
		);

		$this->field_defaults = array(

			'default_label' => __( 'Checkbox', 'user-registration' ),

			'default_field_name' => 'check_box_' . ur_get_random_number(),
		);
	}

	/**
	 * @return string
	 */
	public function get_registered_admin_fields() {

		return '<li id="' . $this->id . '_list "

				class="ur-registered-item draggable"

                data-field-id="' . $this->id . '"><span class="' . $this->registered_fields_config['icon'] . '"></span>' . $this->registered_fields_config['label'] . '</li>';
	}


	/**
	 * @param $single_form_field
	 * @param $form_data
	 * @param $filter_hook
	 * @param $form_id
	 */
	public function validation( $single_form_field, $form_data, $filter_hook, $form_id ) {
		// TODO: Implement validation() method.
	}
}

return UR_Checkbox::get_instance();
