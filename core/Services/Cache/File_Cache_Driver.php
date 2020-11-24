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

use ErrorException;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use PC_Vendor\Psr\SimpleCache\CacheInterface;
use PinkCrab\Core\Services\Cache\CacheInterfaceTrait;

class File_Cache_Driver  implements CacheInterface {

	use CacheInterfaceTrait;

	protected $key;
	protected $filepath;
	protected $extension;

	public function __construct( string $filepath, string $extension = '.do' ) {
		$this->filepath  = $filepath;
		$this->extension = $extension;

		if ( ! is_dir( $this->filepath ) ) {
			\mkdir( $this->filepath, 644 );
		}
	}

	/**
	 * Checks if key is set.
	 *
	 * @param string $key
	 * @return bool
	 * @throws \PC_Vendor\Psr\SimpleCache\InvalidArgumentException
	 */
	public function has( $key ) {
		if ( ! $this->is_valid_key_value( $key ) ) {
			return false;
		}
		return ! is_null( $this->get( $key ) );
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
		return $this->store( $key, $value, $ttl ?? 0 );
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
		return $this->retrieve( $key ) ?? $default;
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
		$this->set_key( $key );
		return unlink( $this->file_path() );
	}

	/**
	 * Clears all elements from the cache.
	 *
	 * @return void
	 */
	public function clear() {
		$results = array_map(
			function( $e ) {
				return $this->delete( $e );
			},
			$this->get_all_files()
		);
		return ! in_array( false, $results, true );
	}

	/**
	 * Gets multiple values, will return default in lue of value
	 *
	 * @param array $keys
	 * @param string|float|int|array|object|resource|bool $default
	 * @return array
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
	 * Sets multiple values in a key=>value array.
	 *
	 * @param array $values
	 * @param int|null $ttl
	 * @return array
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
	 * Deletes multiple keys
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
	 * Sets the key using sanitize_title()
	 *
	 * @param string $key
	 * @return void
	 */
	public function set_key( string $key ) {
		$this->key = sanitize_title( $key );
	}

	/**
	 * Sets an item to the transient cache.
	 *
	 * @param string $key
	 * @param string|int|float|bool|array|object|null $data
	 * @param int $expiry
	 * @return bool
	 */
	public function store( string $key, $data, int $expiry = null ): bool {
		$this->set_key( $key );
		return $this->write_file( $data, $this->compose_expiry( $expiry ) ) > 0;
	}

	/**
	 * Gets a transient.
	 *
	 * @param string $key
	 * @return string|int|float|bool|array|object|null
	 */
	public function retrieve( string $key ) {
		$this->set_key( $key );
		return $this->maybe_get_contents();
	}



	/**
	 * Parses the file name based on the settings and key
	 *
	 * @return string
	 */
	private function file_path(): string {
		return sprintf(
			'%s%s%s',
			$this->filepath,
			$this->key,
			$this->extension
		);
	}

	/**
	 * Composes the expiry time with time added to current timestamp.
	 *
	 * @param int $expiry
	 * @return int
	 */
	private function compose_expiry( ?int $expiry = null ): int {
		return $expiry ? $expiry + time() : 0;
	}

	/**
	 * Creates the file if it doesnt exist.
	 *
	 * @return void
	 */
	private function maybe_create_file(): void {
		// Check if dir exists.
		if ( ! file_exists( $this->filepath ) ) {
			mkdir( $this->filepath, 0755 );
		}
		// Check file exists.
		if ( ! file_exists( $this->file_path() ) ) {
			$file = fopen( $this->file_path(), 'w+' ) or die( 'Error opening file: ' + $this->file_path() );
			fclose( $file );
		}
	}

	/**
	 * Writes data to the file.
	 *
	 * @param mixed $data
	 * @param bool $append
	 * @return void
	 */
	private function write_file( $data, int $expiry, bool $append = false ):? int {

			// Compose the storage
			$data = array(
				'key'    => $this->key,
				'expiry' => $expiry,
				'data'   => $data,
			);

			return $append
			? file_put_contents( $this->file_path(), serialize( $data ), LOCK_EX | FILE_APPEND )
			: file_put_contents( $this->file_path(), serialize( $data ), LOCK_EX );

	}

	/**
	 * Reads the contents of the file and checks it serialised.
	 *
	 * @return string|null
	 */
	private function read_file():?string {
		$data = @file_get_contents( $this->file_path() );
		return ( ! empty( $data ) && is_serialized( $data ) ) ? $data : null;
	}

	/**
	 * Attempts to get the data.
	 * If file exists and its not expired (or currupted).
	 *
	 * @return mixed
	 */
	public function maybe_get_contents() {

		$file_contents = $this->read_file();

		if ( ! $file_contents ) {
			return null;
		}

		$data = unserialize( $file_contents );
		return $this->validate_data( $data ) ? $data['data'] : null;
	}

	/**
	 * Validates the contents of a file read.
	 * Checks key matches, has data and hasnt expired.
	 *
	 * @param array|null $data
	 * @return bool
	 */
	private function validate_data( ?array $data ): bool {
		return ! empty( $data['key'] ) &&
			$data['key'] === $this->key &&
			! empty( $data['data'] ) &&
			// If expiry isnt 0 (no expiry) check the time has not expired
			( ! empty( $data['expiry'] ) && intval( $data['expiry'] ) > time()
				|| intval( $data['expiry'] ) === 0
			);
	}

	/**
	 * Returns an array of all cache files.
	 *
	 * @return array
	 */
	private function get_all_files(): array {

		if ( ! is_dir( $this->filepath ) ) {
			throw new ErrorException( "{$this->filepath} is not a valid directory.", 1 );
		}

		// Gets all files from dir and sub dirs, then passed to be mapped
		return array_map(
			function( $file ) {
				$file_name_parts = array_diff(
					explode( '/', $file->getPathname() ),
					explode( '/', $this->filepath )
				);
				return implode( '/', $file_name_parts );
			},
			array_filter(
				iterator_to_array( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $this->filepath, FilesystemIterator::SKIP_DOTS ) ) ),
				function( $e ) {
					return $e->isFile();
				}
			)
		);
	}
}
