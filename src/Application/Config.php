<?php

declare(strict_types=1);
/**
 * Config object
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\InventoryManagment
 */

namespace PinkCrab\InventoryManagment\Application;

use OutOfBoundsException;

class Config {


	/**
	 * Holds the current sites paths
	 *
	 * @var array
	 */
	protected $paths;

	/**
	 * Post types
	 *
	 * @var array
	 */
	protected $post_types = array(
		'test' => 'test',
	);

	/**
	 * The rest namespace root.
	 *
	 * @var string
	 */
	protected $rest_namespace = 'pc-invman';

	public function __construct( array $paths ) {
		$base_path   = dirname( __DIR__, 2 );
		$this->paths = apply_filters(
			'PC_InvMan\\config_paths',
			array_merge(
				array(
					'plugin_path' => $base_path,
					'view_path'   => $base_path . '/views',
					'assets_path' => $base_path . '/assets',
					'assets_url'  => plugins_url( 'woomandev' ) . '/assets',

				),
				$paths
			)
		);
	}

	/**
	 * Returns the key for a post type.
	 *
	 * @param string $key
	 * @return string
	 * @throws OutOfBoundsException
	 */
	public function post_type( string $key ): string {
		if ( ! array_key_exists( $key, $this->post_types ) ) {
			throw new OutOfBoundsException( 'Post Type doesnt exists', 1 );
		}
		return $this->post_types[ $key ];
	}

	/**
	 * Gets a path with trailing slash.
	 *
	 * @param string|null $path
	 * @return void
	 */
	public function path( ?string $path = null ) {
		if ( is_null( $path ) ) {
			return $this->paths;
		}

		return \array_key_exists( $path, $this->paths ) ? trailingslashit( $this->paths[ $path ] ) : null;
	}

	/**
	 * Returns the based namespace for all routes.
	 *
	 * @return string
	 */
	public function rest_namespace(): string {
		return $this->rest_namespace;
	}
}
