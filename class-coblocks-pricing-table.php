<?php
/**
 * Plugin Name: Gutenberg Pricing Table Block by CoBlocks
 * Plugin URI: https://coblocks.com
 * Description: Add pricing tables using the Pricing Table Block by CBlocks. CoBlocks is a suite of professional content blocks for the upcoming WordPress Gutenberg block editor. Our blocks are hyper-focused on empowering content marketers, writers and publishers to write beautifully rich content in WordPress.
 * Author: Rich Tabor from CoBlocks
 * Author URI: https://coblocks.com
 * Tags: gutenberg, editor, block, table, pricing
 * Version: 1.1.1
 * Text Domain: 'pricing-table-block-gutenberg'
 * Domain Path: languages
 * Tested up to: 4.9.7
 *
 * CoBlocks Pricing Table Block is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CoBlocks Pricing Table Block. If not, see <http://www.gnu.org/licenses/>.
 *
 * @package   CoBlocks Pricing Table Block
 * @author    Rich Tabor from CoBlocks
 * @license   GPL-3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main CoBlocks Pricing Table Block Class
 *
 * @since 1.0.0
 */
class CoBlocks_Pricing_Table {

	/**
	 * This plugin's instance.
	 *
	 * @var CoBlocks_Pricing_Table
	 */
	private static $instance;

	/**
	 * Registers the plugin.
	 */
	public static function register() {
		if ( null === self::$instance ) {
			self::$instance = new CoBlocks_Pricing_Table();
			self::$instance->includes();
		}
	}

	/**
	 * The base directory path (without trailing slash).
	 *
	 * @var string $_url
	 */
	private $_dir;

	/**
	 * The base URL path (without trailing slash).
	 *
	 * @var string $_url
	 */
	private $_url;

	/**
	 * The Plugin version.
	 *
	 * @var string $_version
	 */
	private $_version;

	/**
	 * The Plugin version.
	 *
	 * @var string $_slug
	 */
	private $_slug;

	/**
	 * The plugin's store.
	 *
	 * @var string $_store
	 */
	private $_store;

	/**
	 * The Constructor.
	 */
	private function __construct() {

		$this->_version = '1.1.1';
		$this->_slug    = 'coblocks-pricing-table-block-plugin';
		$this->_store   = 'https://coblocks.com';
		$this->_dir     = untrailingslashit( plugin_dir_path( '/', __FILE__ ) );
		$this->_url     = untrailingslashit( plugins_url( '/', __FILE__ ) );

		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'init', array( $this, 'block_assets' ) );
		add_action( 'init', array( $this, 'editor_assets' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		add_filter( 'plugin_action_links_' . plugin_basename( plugin_dir_path( __FILE__ ) . 'class-coblocks-pricing-table.php' ), array( $this, 'plugin_action_links' ) );
	}

	/**
	 * Check if CoBlocks lite or pro is activated.
	 *
	 * @access public
	 */
	public function has_coblocks() {

		if ( class_exists( 'CoBlocks' ) || class_exists( 'CoBlocks_Pro' ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Include classes
	 *
	 * @return void
	 */
	public function includes() {
		require_once 'includes/class-coblocks-pricing-table-notices.php';
	}

	/**
	 * Add actions to enqueue assets.
	 *
	 * @access public
	 */
	public function register_blocks() {

		// Return early if this function does not exist.
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Return early if CoBlocks Lite or Pro is active.
		if ( $this->has_coblocks() ) {
			return;
		}

		// Shortcut for the slug.
		$slug = $this->_slug;

		register_block_type(
			'coblocks/pricing-table', array(
				'editor_script' => $slug . '-editor',
				'editor_style'  => $slug . '-editor',
				'style'         => $slug . '-frontend',
			)
		);
	}

	/**
	 * Enqueue block assets for use within Gutenberg.
	 *
	 * @access public
	 */
	public function block_assets() {

		// Styles.
		wp_register_style(
			$this->_slug . '-frontend',
			$this->_url . '/dist/blocks.style.build.css',
			array( 'wp-blocks' ),
			$this->_version
		);
	}

	/**
	 * Enqueue block assets for use within Gutenberg.
	 *
	 * @access public
	 */
	public function editor_assets() {

		// Styles.
		wp_register_style(
			$this->_slug . '-editor',
			$this->_url . '/dist/blocks.editor.build.css',
			array( 'wp-edit-blocks' ),
			$this->_version
		);

		// Scripts.
		wp_register_script(
			$this->_slug . '-editor',
			$this->_url . '/dist/blocks.build.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
			$this->_version
		);
	}

	/**
	 * Returns the URL to upgrade the plugin to the pro version.
	 * Can be overridden by theme developers to use their affiliate
	 * link using the coblocks_affiliate_id filter.
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function get_affiliate_id() {

		$id = array( 'ref' => apply_filters( 'coblocks_affiliate_id', null ) );

		return $id;
	}

	/**
	 * Returns a URL that points to the Beaver Builder store.
	 *
	 * @since 1.0.0
	 * @param string|string $path A URL path to append to the store URL.
	 * @param array|array   $params An array of key/value params to add to the query string.
	 * @return string
	 */
	public function get_store_url( $path = '', $params = array() ) {

		$id = $this->get_affiliate_id();

		$params = array_merge( $params, $id );

		$url = trailingslashit( $this->_store . '/' . $path ) . '?' . http_build_query( $params, '', '&#038;' );

		return $url;
	}

	/**
	 * Add a link next to the Activate/Deactivate action
	 *
	 * @param       array|array $actions The plugin.
	 * @return      array
	 */
	public function plugin_action_links( $actions ) {

		$title = esc_html__( 'Get CoBlocks', 'pricing-table-block-gutenberg' );

		$url = $this->get_store_url(
			'support',
			array(
				'utm_medium'   => $this->_slug,
				'utm_source'   => 'plugins-page',
				'utm_campaign' => 'plugins-action-link',
				'utm_content'  => 'Get CoBlocks',
			)
		);

		// Use the WordPress.org URL for now.
		$url = 'https://wordpress.org/plugins/coblocks';

		// Merge and display each link.
		return array_merge(
			array( 'url' => sprintf( '<a href="%s" target="_blank">%s</a>', esc_url( $url ), $title ) ),
			$actions
		);
	}

	/**
	 * Plugin row meta links
	 *
	 * @param array|array   $input already defined meta links.
	 * @param string|string $file plugin file path and name being processed.
	 * @return array $input
	 */
	public function plugin_row_meta( $input, $file ) {

		if ( 'pricing-table-block-gutenberg/class-coblocks-pricing-table.php' !== $file ) {
			return $input;
		}

		$url = $this->get_store_url(
			'extensions',
			array(
				'utm_medium'   => $this->_slug,
				'utm_source'   => 'plugins-page',
				'utm_campaign' => 'plugins-row',
				'utm_content'  => 'extensions',
			)
		);

		// Use the WordPress.org URL for now.
		$url = 'https://wordpress.org/plugins/coblocks';

		$links = array(
			'<a href="' . esc_url( $url ) . '" target="_blank">' . esc_html__( 'More blocks', 'pricing-table-block-gutenberg' ) . '</a>',
		);

		$input = array_merge( $input, $links );

		return $input;
	}
}

CoBlocks_Pricing_Table::register();
