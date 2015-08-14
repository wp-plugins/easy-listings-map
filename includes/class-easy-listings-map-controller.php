<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base Controller class of the plugin.
 *
 * @since      1.2.0
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/includes
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */

abstract class ELM_Controller {

	/**
	 * Getting path of the plugin.
	 *
	 * @since  1.2.0
	 * @return string plugin directory path.
	 */
	protected function get_plugin_path() {
		return plugin_dir_path( dirname( __FILE__ ) );
	}

}
