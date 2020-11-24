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

use PinkCrab\Core\Interfaces\Registerable;
use PinkCrab\Core\Services\Registration\Loader;
use PinkCrab\InventoryManagment\Application\Config;

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
					'name' => __( 'Important Stuff', 'my-textdomain' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'myplugin_important_options',
				),

				array(
					'type'     => 'select',
					'id'       => 'myplugin_select_1',
					'name'     => __( 'Choose your favorite', 'my-textdomain' ),
					'options'  => array(
						'vanilla'    => __( 'Vanilla', 'my-textdomain' ),
						'chocolate'  => __( 'Chocolate', 'my-textdomain' ),
						'strawberry' => __( 'Strawberry', 'my-textdomain' ),
					),
					'class'    => 'wc-enhanced-select',
					'desc_tip' => __( 'Be honest!', 'my-textdomain' ),
					'default'  => 'vanilla',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'myplugin_important_options',
				),
				array(
					'name' => __( 'Group 1', 'my-textdomain' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'myplugin_group1_options',
				),

				array(
					'type'    => 'checkbox',
					'id'      => 'myplugin_checkbox_1',
					'name'    => __( 'Do a thing?', 'my-textdomain' ),
					'desc'    => __( 'Enable to do something', 'my-textdomain' ),
					'default' => 'no',
				),

				array(
					'type' => 'sectionend',
					'id'   => 'myplugin_group1_options',
				),

				array(
					'name' => __( 'Group 2', 'my-textdomain' ),
					'type' => 'title',
					'desc' => '',
					'id'   => 'myplugin_group2_options',
				),

				array(
					'type'     => 'select',
					'id'       => 'myplugin_select_1',
					'name'     => __( 'What should happen?', 'my-textdomain' ),
					'options'  => array(
						'something' => __( 'Something', 'my-textdomain' ),
						'nothing'   => __( 'Nothing', 'my-textdomain' ),
						'idk'       => __( 'IDK', 'my-textdomain' ),
					),
					'class'    => 'wc-enhanced-select',
					'desc_tip' => __( 'Don\'t ask me!', 'my-textdomain' ),
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
		\WC_Admin_Settings::output_fields( $this->get_settings( $current_section ) );
	}


		/**
		 * Save settings
		 *
		 * @since 1.0
		 */
	public function save() {

		global $current_section;
		\WC_Admin_Settings::save_fields( $this->get_settings( $current_section ) );
	}
}
