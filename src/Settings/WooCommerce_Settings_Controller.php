<?php

declare(strict_types=1);
/**
 * Handles all woocomerce settings
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\InventoryManagment
 */

namespace PinkCrab\InventoryManagment\Settings;

use WC_Admin_Settings;
use PinkCrab\Core\Interfaces\Registerable;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\InventoryManagment\Application\Config;
use PinkCrab\InventoryManagment\Settings\WooCommece_Settings;

class WooCommerce_Settings_Controller implements Registerable {

	protected $tab_key = 'pc_invman';

	protected $config;

	public function __construct( Config $config ) {
		$this->config = $config;
	}


	/**
	 * Registers all hook and filter call.
	 *
	 * @param Loader $loader
	 * @return void
	 */
	public function register( Loader $loader ): void {
		$loader->filter( 'woocommerce_settings_tabs_array', array( $this, 'register_settings_tab' ), 30 );
		$loader->action( 'woocommerce_settings_' . $this->tab_key, array( $this, 'output' ) );
		$loader->action( 'woocommerce_settings_save_' . $this->tab_key, array( $this, 'save' ) );
	}

	/**
	 * Include in the tab row
	 *
	 * @param array $sections
	 * @return array
	 */
	public function register_settings_tab( array $sections ): array {
		$sections[ $this->tab_key ] = __( 'Inventory Management', 'pc_invman' );
		return $sections;
	}


	/**
	 * Get settings array
	 *
	 * @since 1.0.0
	 * @param string $current_section Optional. Defaults to empty string.
	 * @return array Array of settings
	 */
	public function get_settings( $current_section = '' ) {
		$settings = apply_filters(
			'myplugin_section1_settings',
			array(

				array(
					'name' => __( 'Multipack Controls', 'pc-invman' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'pc-invman_wc_settings_mp_header',
				),

				array(
					'type'    => 'checkbox',
					'id'      => WooCommece_Settings::ALLOW_MULTIPACK_MODIFIER,
					'name'    => __( 'Use multipack modifiers?', 'pc-invman' ),
					'desc'    => __( 'If enabled all products and variations can make use of the packsize modifiers.', 'pc-invman' ),
					'default' => 'no',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'pc-invman_wc_settings_mp_header',
				),

				/** ---------------------------------------------------- */
// PLACEHOLDERS BELOW!!!!
				array(
					'name' => __( 'Group 1', 'pc-invman' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'myplugin_group1_options',
				),

				array(
					'type'    => 'checkbox',
					'id'      => 'myplugin_checkbox_1',
					'name'    => __( 'Do a thing?', 'pc-invman' ),
					'desc'    => __( 'Enable to do something', 'pc-invman' ),
					'default' => 'no',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'myplugin_group1_options',
				),

				array(
					'name' => __( 'Group 2', 'pc-invman' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'myplugin_group2_options',
				),

				array(
					'type'     => 'select',
					'id'       => 'myplugin_select_1',
					'name'     => __( 'What should happen?', 'pc-invman' ),
					'options'  => array(
						'something' => __( 'Something', 'pc-invman' ),
						'nothing'   => __( 'Nothing', 'pc-invman' ),
						'idk'       => __( 'IDK', 'pc-invman' ),
					),
					'class'    => 'wc-enhanced-select',
					'desc_tip' => __( 'Don\'t ask me!', 'pc-invman' ),
					'default'  => 'idk',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'myplugin_group2_options',
				),
			)
		);

		return $settings;

	}


	/**
	 * Output the settings
	 *
	 * @since 1.0
	 */
	public function output() {

		global $current_section;
		WC_Admin_Settings::output_fields( $this->get_settings( $current_section ) );
	}


	/**
	 * Save settings
	 *
	 * @since 1.0
	 */
	public function save() {

		global $current_section;
		WC_Admin_Settings::save_fields( $this->get_settings( $current_section ) );
	}
}
