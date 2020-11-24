<?php declare(strict_types=1);
/**
 * The WordPress transient driver for the PinkCrab Peristant Cache interface.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Core\Services\Cache
 */

namespace PinkCrab\Core\Services\Cache;

use PC_Vendor\Psr\SimpleCache\CacheInterface;
use PinkCrab\Core\Services\Cache\CacheInterfaceTrait;


class Transient_Cache_Driver implements CacheInterface {

	use CacheInterfaceTrait;

	const CACHE_KEY_PREFIX = PC_TRANSIENT_CACHE_KEY ?? 'pc_transient_cache_item';

	protected $group = '';

	public function __construct( ?string $group = null ) {
		$this->group = $group;
	}

	/**
	 * Sets a key.
	 * Used to conform with Psr\Simple-Cache
	 *
	 * @param string                 $key   The key of the item to store.
	 * @param mixed                  $value The value of the item to store, must be serializable.
	 * @param null|int|\DateInterval $ttl
	 * @return void
	 * @throws \PC_Vendor\Psr\SimpleCache\InvalidArgumentException
	 */
	public function set( $key, $value, $ttl = null ) {
		if ( ! $this->is_valid_key_value( $key ) ) {
			return false;
		}
		return \set_transient( $this->set_key( $key ), $value, $ttl );
	}

	/**
	 * Attempts to get from cache, return defualt if nothing returned.
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return void
	 * @throws \PC_Vendor\Psr\SimpleCache\InvalidArgumentException
	 */
	public function get( $key, $default = null ) {
		if ( ! $this->is_valid_key_value( $key ) ) {
			return $default;
		}
		return \get_transient( $this->set_key( $key ) ) ?: $default;
	}

	/**
	 * Clears a defined cached instance.
	 *
	 * @param string $key
	 * @return bool
	 * @throws \PC_Vendor\Psr\SimpleCache\InvalidArgumentException
	 */
	public function delete( $key ) {
		if ( ! $this->is_valid_key_value( $key ) ) {
			return false;
		}
		return \delete_transient( $this->set_key( $key ) );
	}

	/**
	 * Clears all transients for the defined group.
	 *
	 * @return void
	 */
	public function clear() {
		$results = array_map(
			function( $e ) {
				return \delete_transient( $this->set_key( $e ) );
			},
			$this->get_group_keys()
		);
		return ! \in_array( false, $results, true );
	}

	/**
	 * Gets multiple cache values based on an array of keys.
	 *
	 * @param array $keys
	 * @param mixed $default
	 * @return void
	 */
	public function getMultiple( $keys, $default = null ) {
		return array_reduce(
			$keys,
			function( $carry, $key ) use ( $default ) {
				$carry[ $key ] = $this->get( $key, $default );
				return $carry;
			},
			array()
		);
	}

	/**
	 * Sets multiple keys based ona  key => value array.
	 *
	 * @param array $values
	 * @param int|null $ttl
	 * @return void
	 */
	public function setMultiple( $values, $ttl = null ) {
		return array_reduce(
			array_keys( $values ),
			function( $carry, $key ) use ( $values, $ttl ) {
				$carry[ $key ] = $this->set( $key, $values[ $key ], $ttl );
				return $carry;
			},
			array()
		);
	}

	/**
	 * Deletes multiple keys based on an arrya of keys.
	 *
	 * @param array $keys
	 * @return void
	 */
	public function deleteMultiple( $keys ) {
		return array_reduce(
			$keys,
			function( $carry, $key ) {
				$carry[ $key ] = $this->delete( $key );
				return $carry;
			},
			array()
		);
	}

	/**
	 * Checks if a key is defined in transient.
	 *
	 * @param string $key
	 * @return bool
	 */
	public function has( $key ) {
		return ! is_null( $this->get( $key ) );
	}

	/**
	 * Sets the key base on the group
	 *
	 * @param string $key
	 * @return string
	 */
	protected function set_key( string $key ): string {
		return \sprintf(
			'%s%s',
			$this->group_key_prefix(),
			$key
		);
	}

	/**
	 * Sets the defined prefix to keys.
	 *
	 * @return void
	 */
	protected function group_key_prefix(): string {
		return $this->group
			? self::CACHE_KEY_PREFIX . '_' . $this->group . '_'
			: self::CACHE_KEY_PREFIX . '_';
	}

	/**
	 * Returns all keys that match for this group.
	 *
	 * @return void
	 */
	protected function get_group_keys(): array {

		// Not a fan of this, but not way to access current cache object without.
		global $wp_object_cache;

		// Extract only transient keys.
		$keys = array_filter(
			array_keys( $wp_object_cache->cache['options']['alloptions'] ?? array() ),
			function( $key ) {
				return \str_contains( $key, '_transient_' . $this->group_key_prefix() );
			}
		);

		// Extract the base cache keys (excluding pre/postfixes)
		return array_map(
			function( $key ) {
				return \str_replace( '_transient_' . $this->group_key_prefix(), '', $key );
			},
			$keys
		);
	}
}
