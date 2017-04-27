<?php

use Another_Twitter_Plugin\Includes\AnotherTwitterPlugin;
use Another_Twitter_Plugin\Includes\Activator;
/**
 *
 * @link              http://example.com
 * @since             1.1.0
 * @package           Another_Twitter_Plugin
 *
 * @wordpress-plugin
 * Plugin Name:       Another Twitter Plugin
 * Plugin URI:        https://wordpress.org/plugins/another-twitter-extension/
 * Description:       Twitter plugin for developers, plugin that you want and need, fully customizable style, works with multiple hashtags or usernames and you are not limited to only your account for tweets.
 * Version:           1.1.0
 * Author:            Marko Kunic
 * Author URI:        http://kunicmarko.ml
 * License:           GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       another-twitter-plugin
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
require_once plugin_dir_path( __FILE__ ) . 'autoload.php';
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_plugin_name() {
    Activator::activate();
}
register_activation_hook( __FILE__, 'activate_plugin_name' );
define( 'dt_atp_plugin_dir', plugin_dir_path( __FILE__ ) );
define( 'dt_atp_plugin_url', home_url().'/' );
/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run() {

	$plugin = new AnotherTwitterPlugin();
	$plugin->run();

}
run();
