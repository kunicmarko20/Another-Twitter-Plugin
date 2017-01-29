<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/public
 * @author     Your Name <email@example.com>
 */
class Another_Twitter_Plugin_Public {
        
        const TwitterURL = 'http://www.twitter.com/';
	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;
        
        /**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
        private $dateFormat;
        
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register plugins shortcode.
	 *
	 * @since    1.1.0
	 */
	public function register_shortcode() {
            add_shortcode('another_twitter_plugin_shortcode', 'shortcode_rendering');
            //add_shortcode( 'another_twitter_plugin_shortcode', array( 'Another_Twitter_Plugin_Public', 'shortcode_rendering' ) );
	}
        
        /**
	 * Shortcode rendering on frontend.
	 *
	 * @since    1.1.0
	 */
        public function shortcode_rendering(){

            if(get_option('dt_atp_status_enabled',0) == 0) return;
            $tweets = json_decode(file_get_contents(plugin_dir_path( dirname( __FILE__ ) ) . 'includes/shared/twitter.json'),true);
            if (!is_array($tweets)) return;
            return $this->render($tweets); 
        }
        /**
	 * Shortcode rendering on frontend.
	 *
	 * @since    1.1.0
	 */        
        private function render($tweets){
            $i = 0;
            $number_of_saved_tweets = get_option('dt_atp_number_of_tweets');
            $this->dateFormat = get_option('dt_atp_date_format','h:i A - d M Y');
            $html = get_option('dt_atp_textarea_style');
            
            $all = "<div class='".str_replace(',',' ',get_option('dt_atp_wrapper_class'))."'>";

            foreach($tweets as $tweet) {
                if ($this->checkArray($tweet)) continue;
                if (++$i == $number_of_saved_tweets) break;
                $all .= strtr($html, $this->getTypes($tweet));
            } 
            $all .= "</div>";
            return $all;
        }
         /**
	 * Shortcode rendering on frontend.
	 *
	 * @since    1.1.0
	 */       
        private function getTypes($array){
            return [
                "[screen_name]" =>$array['screen_name'],
                "[status]" =>$array['status'],
                "[created_at]" => date($this->dateFormat, $array['created_at']),
                "[url]" => self::TwitterURL.$array['url'],
                "[image]" => $array['image'],
                "[id]" =>$array['id'] 
            ];
        }
         /**
	 * Shortcode rendering on frontend.
	 *
	 * @since    1.1.0
	 */               
        private function checkArray($array){
            if(in_array(null, $array)) return true;
            if($array['id'] != intval($array['id'])) return true;
            
        }
}
