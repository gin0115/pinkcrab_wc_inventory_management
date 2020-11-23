<?php

declare(strict_types=1);
/**
 * Config map 
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
 * @package PinkCrab\Core
 */

namespace PinkCrab\Core\Application;

use OutOfBoundsException;

class Config
{

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
    protected $post_types = [
        'test' => 'test'
    ];

    /**
     * The rest namespace root.
     *
     * @var string
     */
    protected $rest_namespace = 'pc-invman';

    public function __construct(array $paths)
    {
        $base_path = dirname(__DIR__, 2);
        $this->paths = array_merge(
            [
                'plugin_path' => $base_path,
                'view_path' => $base_path . '/views',
                'assets_path' => $base_path . '/assets',
                'assets_url' => plugins_url('woomandev') . '/assets'

            ],
            apply_filters('jhjkhkj', $paths)
        );
    }

    /**
     * Returns the key for a post type.
     *
     * @param string $key
     * @return string
     * @throws OutOfBoundsException
     */
    public function post_type(string $key): string
    {
        if (!array_key_exists($key, $this->post_types)) {
            throw new OutOfBoundsException("Post Type doesnt exists", 1);
        }
        return $this->post_types[$key];
    }

    /**
     * Gets a path with trailing slash.
     *
     * @param string|null $path
     * @return void
     */
    public function path(?string $path = null)
    {
        if (is_null($path)) {
            return $this->paths;
        }

        return \array_key_exists($path, $this->paths) ? trailingslashit($this->paths[$path]) : null;
    }

    /**
     * Returns the based namespace for all routes.
     *
     * @return string
     */
    public function rest_namespace(): string
    {
        return $this->rest_namespace;
    }
}
