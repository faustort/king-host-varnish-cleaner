<?php

/**
 * @link              https://www.nw2web.com.br
 * @since             1.0.0
 * @package           King_Host_Varnish_Cleaner
 *
 * @wordpress-plugin
 * Plugin Name:       King Host Varnish Cleaner
 * Plugin URI:        https://king-host-varnish-cleaner.nw2.com.br/
 * Description:       This plugin try to clean Varnish Cache based on King Host documentation
 * Version:           1.0.1
 * Author:            Fausto Rodrigo Toloi
 * Author URI:        https://www.nw2web.com.br
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       king-host-varnish-cleaner
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 */
define( 'KING_HOST_VARNISH_CLEANER_VERSION', '1.0.1' );

/**
 * This action is documented in includes/class-king-host-varnish-cleaner-activator.php
 */
function activate_king_host_varnish_cleaner() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-king-host-varnish-cleaner-activator.php';
	King_Host_Varnish_Cleaner_Activator::activate();
}

/**
 * This action is documented in includes/class-king-host-varnish-cleaner-deactivator.php
 */
function deactivate_king_host_varnish_cleaner() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-king-host-varnish-cleaner-deactivator.php';
	King_Host_Varnish_Cleaner_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_king_host_varnish_cleaner' );
register_deactivation_hook( __FILE__, 'deactivate_king_host_varnish_cleaner' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-king-host-varnish-cleaner.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_king_host_varnish_cleaner() {

	$plugin = new King_Host_Varnish_Cleaner();
	$plugin->run();

}
run_king_host_varnish_cleaner();
