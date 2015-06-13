<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The admin-facing notices of the plugin.
 *
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/admin
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */

class ELM_Admin_Notices {

	/**
	 * Constructor of admin-area notices.
	 *
	 * @since 1.0.0
	 * @param Easy_Listings_Map_Loader $loader
	 */
	public function __construct( Easy_Listings_Map_Loader $loader ) {
		$loader->add_action( 'admin_notices', $this, 'admin_notices' );
	}

	/**
	 * Admin notices of the plugin.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function admin_notices() {
		settings_errors( 'elm-notices' );
	}
}
