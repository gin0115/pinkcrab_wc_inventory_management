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

	protected $tab_key = WooCommece_Settings::TAB_KEY;

	/**
	 * Application config.
	 *
	 * @var PinkCrab\InventoryManagment\Application\Config
	 */
	protected $config;

	public function __construct( Config $config ) {
		$this->config = $config;
	}


	/**
	 * Registers all hook and filter call.
	 *
	 * @param PinkCrab\Core\Services\Registration\Loader $loader
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
		$sections[ $this->tab_key ] = __( WooCommece_Settings::TAB_LABEL, 'pc_invman' );
		return $sections;
	}

	/**
	 * Returns the fields for the tab.
	 *
	 * @filtered PinkCrab\InvMan\wc_settings_page_fields array
	 * @param array|null $current_section
	 * @return array
	 */
	public function fields( ?array $current_section = null ):array {
		$settings = array(
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
		);

		return apply_filters(
			'PinkCrab\\InvMan\\wc_settings_page_fields',
			array_merge( $current_section ?? array(), $settings )
		);
	}


	/**
	 * Renders the form output.
	 *
	 * @return void
	 */
	public function output(): void {
		global $current_section;
		WC_Admin_Settings::output_fields( $this->fields( $current_section ) );
	}


	/**
	 * Saves any values from the fields (in $_POST)
	 *
	 * @return void
	 */
	public function save(): void {
		global $current_section;
		WC_Admin_Settings::save_fields( $this->fields( $current_section ) );
	}
}
