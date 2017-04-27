<?php

namespace Another_Twitter_Plugin\Admin;

use Another_Twitter_Plugin\Includes\AnotherTwitterPlugin;
use Another_Twitter_Plugin\Admin\pages as Page;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class Admin {
        
        const USERNAME_URL = 'statuses/user_timeline';
        const HASHTAG_URL = 'search/tweets';

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
        private $already_saved_tweet_ids = [];
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {


	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
            
            wp_enqueue_style( AnotherTwitterPlugin::PLUGIN_NAME, plugin_dir_url( __FILE__ ) . 'css/loader.css', [], AnotherTwitterPlugin::VERSION, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
            
		wp_enqueue_script( AnotherTwitterPlugin::PLUGIN_NAME, plugin_dir_url( __FILE__ ) . 'js/script.js', ['jquery' ], AnotherTwitterPlugin::VERSION, false );

	}

        public function get_new_tweets()
        {
            $terms = get_option('dt_atp_term');
            if (get_option('dt_atp_status_enabled') == 0 || count($terms) == 0) return;
            include_once(plugin_dir_path( __FILE__ ).'twitteroauth/autoload.php');

	    $connection = new TwitterOAuth(get_option('dt_atp_customer_key'), get_option('dt_atp_customer_secret'), get_option('dt_atp_access_token'), get_option('dt_atp_access_token_secret'));
	    $json = $this->buildJSON($terms, $connection);
            
            $newJson = $this->sortJSON($json);
	    file_put_contents(plugin_dir_path( dirname( __FILE__ ) ) . 'includes/shared/twitter.json', json_encode($newJson));
            update_option('dt_atp_last_update_time',time());
	
            $redirectURL = !empty($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : site_url();
            header("Location: " . $redirectURL);
        }
        
        private function buildJSON($terms , $connection){
            $number_of_tweets_to_fetch = get_option('dt_atp_number_of_tweets');
            $last_saved_term_id = get_option('dt_atp_last_saved_term_id');
            $json = json_decode(file_get_contents(plugin_dir_path( dirname( __FILE__ ) ) . 'includes/shared/twitter.json'),true);
            $new_term_ids = [];
            
            foreach($terms as $term){
                if (empty($term['value'])) continue;
                $options = $this->setupOptions($term,$number_of_tweets_to_fetch,$last_saved_term_id );
                $tweets_result = $connection->get($term['type'] == 'username' ? self::USERNAME_URL : self::HASHTAG_URL, $options);
	        $tweets = json_decode(json_encode($tweets_result),true);
                if($term['type'] == 'hashtag'){
                    $tweets = $tweets['statuses'];
                }
                if(empty($tweets[0]['id_str'])){
                   $new_term_ids[$term['value']] = $last_saved_term_id[$term['value']]; 
                   continue;
                }
                $new_term_ids[$term['value']] = $tweets[0]['id_str'];
                $json = array_merge($json, $this->buildJSONElement($tweets));
            }
            
            update_option('dt_atp_currently_active',$new_term_ids);
            return $json;
        }        
        private function setupOptions($term,$number_of_tweets_to_fetch,$last_saved_term_id){
            if($term['type'] == 'hashtag'){
                $options = [
                    'q' => '#'.$term['value'].' -filter:retweets',
                    'result_type' => 'recent',
                    'count' => $number_of_tweets_to_fetch
                ];
            }
            if($term['type'] == 'username'){
                $options = [
                    'screen_name' => $term['value'],
                    'count' => $number_of_tweets_to_fetch
                ];
            }
            if(empty($last_saved_term_id)) return $options;
            
            if(array_key_exists($term['value'],$last_saved_term_id)){
                $options['since_id'] = $last_saved_term_id[$term['value']];
            }
	    return $options;         
        }

        private function buildJSONElement($tweets){
            $json = [];
            foreach($tweets as $tweet){
                if(in_array($tweet['id_str'], $this->already_saved_tweet_ids)) continue;
                    $this->already_saved_tweet_ids[] = $tweet['id_str'];
                    $json[] = [
                        'id' => $tweet['id_str'],
                        'created_at' => strtotime($tweet['created_at']),
                        'url' => $tweet['user']['screen_name']."/status/".$tweet['id_str'],
                        'screen_name' => $tweet['user']['screen_name'],
                        'status' => $this->formatTwitterStatus($tweet['text']),
                        'image' => $tweet['user']['profile_image_url']
                    ];
                }
            return $json;
        }
        
        private function formatTwitterStatus($text){
            $twitter_status = preg_replace('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', '<a href="$0" target="_blank" title="$0">$0</a>', $text);
            $twitter_status = preg_replace('/#(\w*\p{L}+\w*)/u', '<a href="https://twitter.com/search?f=tweets&vertical=default&q=%23\1&src=typd" target="_blank">#\1</a>', $twitter_status);
            $twitter_status = preg_replace('/@(\w*[a-zA-Z_]+\w*)/', '<a href="https://twitter.com/\1" target="_blank">@\1</a>', $twitter_status);  
        
            return $twitter_status;
        }
        
        private function sortJSON($json){
            foreach ($json as $key => $row) {
	        $mid[$key]  = $row['created_at'];
	    }
	    array_multisort($mid, SORT_DESC, $json);
            
	    $json = array_slice($json, 0, get_option('dt_atp_number_of_saved_tweets'));
            
            return $json;
        }

        public function reset_tweets(){
            if (isset($_POST['btnReset'])) {
                update_option('dt_atp_last_update_time',time());
                file_put_contents(dt_atp_plugin_dir.'Includes/shared/twitter.json', json_encode(new \stdClass));
            }
            $redirect = ($_SERVER["HTTP_REFERER"] != '' ? $_SERVER["HTTP_REFERER"] : site_url());
            header("Location: " . $redirect);
            exit;
        }
}
