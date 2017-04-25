<?php

namespace Another_Twitter_Plugin\Includes;

use Another_Twitter_Plugin\Admin\Admin;
use Another_Twitter_Plugin\Admin\pages as Page;
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Plugin_Name
 * @subpackage Plugin_Name/includes
 * @author     Your Name <email@example.com>
 */
class AnotherTwitterPlugin {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Plugin_Name_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	const PLUGIN_NAME = 'another-twitter-plugin';

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	const VERSION = '1.1.0';

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->loader = new Loader();
		$this->define_admin_hooks();
		//$this->define_public_hooks();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Admin();
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
                $this->loader->add_action( 'wp_ajax_dt_atp_reset_tweets', $plugin_admin, 'reset_tweets' );
                $this->loader->add_action( 'wp_ajax_dt_atp_get_new_tweets', $plugin_admin, 'get_new_tweets' );
                $this->define_admin_pages();
	}
        
	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */        
        private function define_admin_pages(){
            
                Page\Dashboard::render();
                Page\DisplaySettings::render();
                
//                $display = new Page\Display();
//                $pluginSettings = new Page\PluginSettings();
//                $twitterSettings = new Page\TwitterSettings();
                
//                $this->loader->add_action( 'admin_init', $twitterSettings, 'register_twitter' );
//                $this->loader->add_action( 'admin_init', $pluginSettings, 'register_additional_settings' );
//                $this->loader->add_action( 'admin_init', $pluginSettings, 'register_settings' );
//                $this->loader->add_action( 'admin_init', $dashboard, 'register_dashboard_form1' );
//                $this->loader->add_action( 'admin_init', $dashboard, 'register_dashboard_form2' );
//                $this->loader->add_action( 'admin_init', $display, 'register_display_style' );
        }
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Plugin_Name_Public( );

		$this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

}
