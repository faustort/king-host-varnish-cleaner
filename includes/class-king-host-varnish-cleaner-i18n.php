<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.nw2web.com.br
 * @since      1.0.0
 *
 * @package    King_Host_Varnish_Cleaner
 * @subpackage King_Host_Varnish_Cleaner/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    King_Host_Varnish_Cleaner
 * @subpackage King_Host_Varnish_Cleaner/includes
 * @author     Fausto Rodrigo Toloi <fausto@nw2web.com.br>
 */
class King_Host_Varnish_Cleaner_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'king-host-varnish-cleaner',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
