<?php
/**
 * Admin notices
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
 * Main CoBlocks Pricing Table Block Notices Class
 *
 * @since 1.0.0
 */
class CoBlocks_Pricing_Table_Notices {

	/**
	 * Constructor.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return void
	 */
	public function __construct() {

		// We need plugin.php.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		// Check to see if CoBlocks Lite or Pro is activated.
		$coblocks_dir = basename( dirname( dirname( __FILE__ ) ) );

		$coblocks_handle = 'coblocks';
		$coblocks_active = is_plugin_active( $coblocks_handle . '/class-coblocks.php' );

		$coblocks_pro_handle = 'coblocks-pro';
		$coblocks_pro_active = is_plugin_active( $coblocks_pro_handle . '/class-coblocks-pro.php' );

		// If so, add a notice to disable it.
		if ( ( $coblocks_dir !== $coblocks_handle && $coblocks_active ) || ( $coblocks_dir !== $coblocks_pro_handle && $coblocks_pro_active ) ) {
			add_action( 'admin_notices', array( $this, 'notice' ) );
			add_action( 'network_admin_notices', array( $this, 'notice' ) );
			return;
		}
	}

	/**
	 * Get activation or deactivation link of a plugin
	 *
	 * @param string $plugin plugin file name.
	 * @param string $action action to perform. activate or deactivate.
	 * @return string $url action url
	 */
	private function plugin_action_link( $plugin, $action = 'activate' ) {
		if ( strpos( $plugin, '/' ) ) {
			$plugin = str_replace( '\/', '%2F', $plugin );
		}
		$url                = sprintf( admin_url( 'plugins.php?action=' . $action . '&plugin=%s&plugin_status=all&paged=1&s' ), $plugin );
		$_REQUEST['plugin'] = $plugin;
		$url                = wp_nonce_url( $url, $action . '-plugin_' . $plugin );
		return $url;
	}

	/**
	 * Display notice if CoBlocks is activated.
	 *
	 * @access public
	 */
	public function notice() {

		// Return if permissions are not acceptable.
		if ( ! is_admin() ) {
			return;
		} elseif ( ! is_user_logged_in() ) {
			return;
		} elseif ( ! current_user_can( 'update_core' ) ) {
			return;
		}

		// Array of allowed HTML.
		$allowed_html_array = array(
			'div' => array(
				'class' => array(),
			),
			'p'   => array(),
			'a'   => array(
				'href'   => array(),
				'target' => array(),
			),
		);

		// Notice.
		printf(
			/* translators: 1: plugin name. 2: brand. 3: deactivation url. */
			wp_kses( __( '<div class="error"><p>The %1$1s is not necessary with %2$2s activated. <a href="%3$3s">Deactivate %1$1s &rarr;</a></p></div>', 'pricing-table-block-gutenberg' ), $allowed_html_array ),
			esc_html( 'CoBlocks Pricing Tables Block' ),
			esc_html( 'CoBlocks' ),
			esc_url( $this->plugin_action_link( 'pricing-table-block-gutenberg/class-coblocks-pricing-table.php', 'deactivate' ) )
		);
	}
}

return new CoBlocks_Pricing_Table_Notices();
