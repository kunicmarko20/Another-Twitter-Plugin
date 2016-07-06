<?php


/*
Plugin Name: Another Twitter Plugin
Plugin URI: 
Description: Twitter plugin that you want, fully customizable style, works with multiple hashtags or usernames and you are not limited to only your account for tweets.
Version: 1.0.2
Author: Marko Kunic
Author URI: http://kunicmarko.ml
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: another-twitter-plugin
*/



	
/* !0. TABLE OF CONTENTS */

/*
	
	1. HOOKS
	
	2. SHORTCODES
		
	3. FILTERS
		
	4. EXTERNAL SCRIPTS
		
	5. ACTIONS
		
	6. HELPERS
		
	7. CUSTOM POST TYPES
	
	8. ADMIN PAGES
	
	9. SETTINGS
	
	10. MISCELLANEOUS 

*/


define( 'dt_atp_plugin_dir', plugin_dir_path( __FILE__ ) );

/* !1. HOOKS */


add_action('admin_menu', 'dt_atp_admin_menus');

add_action('admin_init', 'dt_atp_register_twitter');
add_action('admin_init', 'dt_atp_register_settings');
add_action('admin_init', 'dt_atp_register_additional_settings');
add_action('admin_init', 'dt_atp_register_dashboard_form1');
add_action('admin_init', 'dt_atp_register_dashboard_form2');
add_action('admin_init', 'dt_atp_register_display_style');

add_action('wp_ajax_dt_atp_reset_tweets', 'dt_atp_reset_tweets');
add_action('wp_ajax_dt_atp_get_new_tweets', 'dt_atp_get_new_tweets');

add_action( 'admin_enqueue_scripts', 'dt_atp_extra_javascript_files' );

add_action('init', 'dt_atp_register_shortcodes');

register_uninstall_hook( __FILE__, 'dt_atp_uninstall_plugin' );
/* !2. SHORTCODES */
// 2.1
// hint: registers all our custom shortcodes
function dt_atp_register_shortcodes() {
	
	add_shortcode('dt_atp_twitter', 'dt_atp_shortcode');
	
}

// 2.2
// hint: our shortcode that displays plugin
function dt_atp_shortcode() { 
	$dt_atp_status = dt_atp_get_current_options('dashboard');
	if($dt_atp_status['dt_atp_status_enabled'] == 1){
		$dt_atp_style = dt_atp_get_current_options('display');
		$jsons = json_decode(file_get_contents(dt_atp_plugin_dir.'twitter.json'),true);
	
		$all = "<div class='".str_replace(',',' ',$dt_atp_style['dt_atp_wrapper_class'])."'>";
		
		foreach($jsons as $json){
			$json['status'] = preg_replace('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', '<a href="$0" target="_blank" title="$0">$0</a>', $json['status']);
			$types = array("[screen_name]" =>$json['screen_name'],"[status]" =>$json['status'],"[created_at]" => date('h:i A - d M Y',$json['created_at']),"[url]" => "http://www.twitter.com/".$json['url'],"[image]" => $json['image'],"[id]" =>$json['id']);
			$all .= strtr($dt_atp_style['dt_atp_textarea_style'], $types);
		}
		
		$all .= "</div>";
		$all = $text = preg_replace('/(\#)([^\s]+)/', '<a href="https://twitter.com/search?f=tweets&vertical=default&q=%23$2&src=typd" target="_blank">#$2</a>', $all);
		$all = $text = preg_replace('/(\@)([^\s]+)/', '<a href="https://twitter.com/$2" target="_blank">@$2</a>', $all);
		return $all;
	}
}



/* !3. FILTERS */
function dt_atp_admin_menus() {
	
	/* main menu */
	
		$top_menu_item = 'dt_atp_dashboard_admin_page';
	    
	    add_menu_page( '', 'Another Twitter', 'manage_options', 'dt_atp_dashboard_admin_page', 'dt_atp_dashboard_admin_page', 'dashicons-twitter' );
    
    /* submenu items */
    
	    // dashboard
	    add_submenu_page( $top_menu_item, '', 'Dashboard', 'manage_options', $top_menu_item, $top_menu_item );
	   	
	   	// plugin settings
	    add_submenu_page($top_menu_item, '', 'Display Style', 'manage_options', 'dt_atp_display_style_admin_page', 'dt_atp_display_style_admin_page' );
	    // plugin settings
	    add_submenu_page($top_menu_item, '', 'Plugin Settings', 'manage_options', 'dt_atp_plugin_settings_admin_page', 'dt_atp_plugin_settings_admin_page' );
	    // twitter settings
	    add_submenu_page($top_menu_item, '', 'Twitter Settings', 'manage_options', 'dt_atp_twitter_settings_admin_page', 'dt_atp_twitter_settings_admin_page' );

}



/* !4. EXTERNAL SCRIPTS */
//4.1 add script to display settings for adding buttons to textarea
function dt_atp_extra_javascript_files() {
    wp_enqueue_script( 'textarea', plugin_dir_url( __FILE__ ) . 'js/textarea.js');
    wp_enqueue_style('loader', plugin_dir_url( __FILE__ ) . 'css/loader.css');
}




/* !5. ACTIONS */
//5.1 get all new tweets
function dt_atp_get_new_tweets(){
	if(get_option('dt_atp_status_enabled') == 1 && count(get_option('dt_atp_textbox')) > 0){
		include_once(plugin_dir_path( __FILE__ ).'twitteroauth/twitteroauth.php');
		$options_twitter = dt_atp_get_current_options('twitter');
		$options_settings = dt_atp_get_current_options('settings');
		$currently_active = get_option('dt_atp_currently_active');
		$new_currently_active = array();
		
	    $twitter_customer_key           = $options_twitter['dt_atp_customer_key'];
	    $twitter_customer_secret        = $options_twitter['dt_atp_customer_secret'];
	    $twitter_access_token           = $options_twitter['dt_atp_access_token'];
	    $twitter_access_token_secret    = $options_twitter['dt_atp_access_token_secret'];
	    
	    $connection = new TwitterOAuth($twitter_customer_key, $twitter_customer_secret, $twitter_access_token, $twitter_access_token_secret);
	    
	    $json = json_decode(file_get_contents(plugin_dir_path( __FILE__ ).'twitter.json'),true);
	
		if($options_settings['dt_atp_radio'] == 'hashtags') {
			$tags = $options_settings['dt_atp_textbox'];
	    	$id = array();
	        foreach($tags as $tag){
	            if(!empty($tag)){
	                $title = $tag;
	                
	                $options = array();
	                $options['q'] = '#'.$title.' -filter:retweets';
	                $options['result_type'] = 'recent';
	                $options['count'] = $options_settings['dt_atp_number_of_tweets'];
	                
	                 if(!empty($currently_active)){
	                    if(array_key_exists($title,$currently_active)){
	                         $options['since_id'] = $currently_active[$title];
	                    }
	                }
	
	                $my_tweets = $connection->get('search/tweets', $options);
	                $my_tweets = json_decode(json_encode($my_tweets),true);
					
					if(!empty($my_tweets['statuses'][0]['id'])){
	                $new_currently_active[$title] = $my_tweets['statuses'][0]['id'];
	                
					
	                foreach($my_tweets['statuses'] as $t){
	                    if(in_array($t['id'], $id)){ 
	                        continue;
	                    }
	                    else {
	                        $id[] = $t['id'];
	                        $json[] = array(
	                            'id' => $t['id'],
	                            'created_at' => strtotime($t['created_at']),
	                            'url' => $t['user']['screen_name']."/status/".$t['id'],
	                            'screen_name' => $t['user']['screen_name'],
	                            'status' => $t['text'],
	                            'image' => $t['user']['profile_image_url']);
	                    }
	                }
	            }
	            else{
	            	 $new_currently_active[$title] = $currently_active[$title];
	            }
	        }
	    }
		}
		elseif ($options_settings['dt_atp_radio'] == 'username'){
			$usernames = $options_settings['dt_atp_textbox'];
	        foreach($usernames as $username){
	        	if(!empty($username)){
	                $options = array();
	                $options['screen_name'] = $username;
	                $options['count'] = $options_settings['dt_atp_number_of_tweets'];
	                if(!empty($currently_active)){
	                    if(array_key_exists($username,$currently_active)){
	                         $options['since_id'] = $currently_active[$username];
	                    }
	                }
	                
	                $my_tweets = $connection->get('statuses/user_timeline', $options);
	                $my_tweets = json_decode(json_encode($my_tweets),true);
	                if(!empty($my_tweets[0]['id'])){
	                    $new_currently_active[$username] = $my_tweets[0]['id'];
	                    
	                    foreach($my_tweets as $t){
	                            $json[] = array(
	                                'id' => $t['id'],
	                                'created_at' => strtotime($t['created_at']),
	                                'url' => $t['user']['screen_name']."/status/".$t['id'],
	                                'screen_name' => $t['user']['screen_name'],
	                                'status' => $t['text'],
	                                'image' => $t['user']['profile_image_url']);
	                    }
	                }
	                else {
	                	$new_currently_active[$username] = $currently_active[$username];
	                }
	    		}
	        }
		
		}
		else {
			return ;
		}
		
		update_option('dt_atp_currently_active',$new_currently_active);
		 foreach ($json as $key => $row) {
	        $mid[$key]  = $row['created_at'];
	    }
	    array_multisort($mid, SORT_DESC, $json);
	    $json = array_slice($json, 0, $options_settings['dt_atp_number_of_saved_tweets']);
	    file_put_contents(plugin_dir_path( __FILE__ ).'twitter.json', json_encode($json));
		update_option('dt_atp_last_update_time',time());
	}	
		$red = ($_SERVER["HTTP_REFERER"] != '' ? $_SERVER["HTTP_REFERER"] : site_url());
		header("Location: " . $red);
}


/* !6. HELPERS */

// 6.1
// hint: get's the current options and returns values in associative array
function dt_atp_get_current_options($for) {
	if(!get_option('dt_atp_number_of_tweets')){
			add_option('dt_atp_number_of_tweets',165);
			add_option('dt_atp_number_of_saved_tweets',200);
			add_option('dt_atp_cron_time',5);	
			add_option('dt_atp_textbox',array());	
			add_option('dt_atp_textarea_style','<div class="row twittHolder">
 <div class="col-xs-2 no-gutter">
   <a href="[url]" class="avatar" target="_blank">
    <img src="[image]" alt="">
   </a>
 </div>
 <div class="col-xs-10 no-left">
  <div class="twitterData">
   <p>[status]</p>
  </div>
 </div>
</div>');

		}
	// setup our return variable
	$current_options = array();
	
	if($for == 'twitter'){
		// build our current options associative array
		$current_options = array(
			'dt_atp_customer_key' => get_option('dt_atp_customer_key'),
			'dt_atp_customer_secret' => get_option('dt_atp_customer_secret'),
			'dt_atp_access_token' => get_option('dt_atp_access_token'),
			'dt_atp_access_token_secret' => get_option('dt_atp_access_token_secret')
		);
	}
	if($for == 'settings'){
		$current_options = array(
			'dt_atp_textbox' => get_option('dt_atp_textbox'),
			'dt_atp_radio' => get_option('dt_atp_radio'),
			'dt_atp_number_of_tweets' => (int)get_option('dt_atp_number_of_tweets'),
			'dt_atp_number_of_saved_tweets' => (int)get_option('dt_atp_number_of_saved_tweets'), 
			'dt_atp_cron_time' => (int)get_option('dt_atp_cron_time'),
			);
	}
	if($for == 'dashboard'){
		$current_options = array(
			'dt_atp_status_enabled' => (int)get_option('dt_atp_status_enabled'),
			'dt_atp_wp_cron_enabled' => (int)get_option('dt_atp_wp_cron_enabled'),

			);
	}
		if($for == 'display'){
		$current_options = array(
			'dt_atp_textarea_style' => get_option('dt_atp_textarea_style'),
			'dt_atp_wrapper_class' => get_option('dt_atp_wrapper_class'),

			);
	}
	
	// return current options
	return $current_options;
	
}


/* !8. ADMIN PAGES */

// 8.1
// hint: dashboard admin page
function dt_atp_dashboard_admin_page() {
	
	$options = dt_atp_get_current_options('dashboard');
	$last_updated = dt_atp_last_update_time();
	$tags = get_option('dt_atp_textbox');
	echo('<div class="wrap">
		
		<h2>Another Twitter Plugin</h2>');
		if($options['dt_atp_status_enabled'] == 0 ){
		echo('<div class="notice notice-error is-dismissible">
        	<p>Plugin is disabled and not visible on your website, some functions may not work if plugin is disabled.</p>
    	</div>');
		}
		if(count(get_option('dt_atp_textbox')) == 0){
		echo('<div class="notice notice-warning is-dismissible">
        	<p>You have to add hashtag/username <a href="?page=dt_atp_plugin_settings_admin_page">here</a> </p>
    	</div>');
		}
		settings_errors('TwitterError');
		echo('<div id="loader"></div><form action="options.php" method="post">');
		
			// outputs a unique nounce for our plugin options
			settings_fields('dt_atp_dashboard_form1');
			// generates a unique hidden field with our form handling url
			@do_settings_fields('dt_atp_dashboard_form1');
			
			echo('<table class="form-table">
		
				<tbody>
			
					<tr>
						<th scope="row"><strong>Status</strong></th>
						<td style="width:110px !important;">');
						
						if($options['dt_atp_status_enabled'] == 1){
						
						echo ('<label style="color:green;">Enabled</label></td><td>
						'); submit_button( "Disable", "secondary","submit",false,array( 'style' => 'color:red;' ) ); echo('</td>
						<td><input type="hidden" name="dt_atp_status_enabled" value="0" /></td>');
						}
						else {
						echo ('<label style="color:red;">Disabled</label></td><td>
						'); submit_button( "Enable", "secondary","submit",false,array( 'style' => 'color:green;' ) ); echo('</td>
						<td><input type="hidden" name="dt_atp_status_enabled" value="1" /></td>');	
						}

			echo('</tr></form><form action="options.php" method="post"><tr>
						<th scope="row"><strong>WP Cron</strong></th>
						<td style="width:110px !important;">');
							// outputs a unique nounce for our plugin options
							settings_fields('dt_atp_dashboard_form2');
							// generates a unique hidden field with our form handling url
							@do_settings_fields('dt_atp_dashboard_form2');
						if($options['dt_atp_wp_cron_enabled'] == 1){
						
						echo ('<label style="color:green;">Enabled</label></td><td>
						'); submit_button( "Disable", "secondary","submit",false,array( 'style' => 'color:red;' ) ); echo('</td>
						<td><input type="hidden" name="dt_atp_wp_cron_enabled" value="0" /></td>');
						}
						else {
						echo ('<label style="color:red;">Disabled</label></td><td>
						'); submit_button( "Enable", "secondary","submit",false,array( 'style' => 'color:green;' ) ); echo('</td>
						<td><input type="hidden" name="dt_atp_wp_cron_enabled" value="1" /></td>');	
						}		
			echo('</tr></form><form action="/wp-admin/admin-ajax.php?action=dt_atp_get_new_tweets" method="post">
					<tr>
							<th scope="row"><strong>Last Update</strong></th>
							<td>
							<label>'. $last_updated .'</label>
							</td>
							<td>') ;
							submit_button("Update Now", "secondary","btnUpdate",false ); 
							echo('</td>
					</tr>
					
						<tr>
							<th scope="row"><strong>Shortcode</strong></th>
							<td>
							<p>[dt_atp_twitter]</p>
							</td>
					</tr>
					
					<tr>
							<th scope="row"><strong>Currently Active</strong></th>
							<td>');
							
							if(!empty($tags)){
								echo ('<ol style="margin-top: 0;margin-left: 15px;">');
								foreach($tags as $tag){
									echo "<li>".$tag."</li>";
								}
								echo ('</ol>');	
							}
							else {
								echo "No Active Tags";
							}
							
						echo ('</td>
					</tr>
					
		</form><form action="/wp-admin/admin-ajax.php?action=dt_atp_reset_tweets" method="post">');
			
			
			echo('<tr>
							<th scope="row"><strong>Reset</strong></th>
							<td>') ;
							submit_button( "Reset", "secondary","btnReset",false ); 
							echo('</td>
					</tr>
					<tr><td colspan="15">
						<p class="description"><span style="color:red;">Reset will delete all saved tweets.</span> <br />
								<strong>SUGGESTION:</strong> Reset when you change hashtag/username so you don\'t see old Tweets .</p></td>
					</tr>
				</tbody>
				
			</table>');
		
			// outputs the WP submit button html

		
		
		echo('</form>			<script>

document.getElementById("btnUpdate").onclick = function() {document.getElementById("loader").style.display = "block";};
document.getElementById("btnReset").onclick = function() {document.getElementById("loader").style.display = "block";};
document.getElementById("submit").onclick = function() {document.getElementById("loader").style.display = "block";};
</script>	
	
	</div>');
	
	
}


// 8.2
// hint: plugin settings
function dt_atp_plugin_settings_admin_page() {
	
$options = dt_atp_get_current_options('settings');	
	
echo('<div class="wrap">

			
			<h2>Plugin Settings</h2>');
		$options1 = dt_atp_get_current_options('dashboard');
		if($options1['dt_atp_status_enabled'] == 0 ){
		echo('<div class="notice notice-error is-dismissible">
        	<p>Plugin is disabled and not visible on your website, some functions may not work if plugin is disabled.</p>
    	</div>');
		}
			settings_errors('updateSettings');
			
echo('<form action="options.php" method="post" style="width: 49%;
    display: inline-block; float:left;">');
		
			// outputs a unique nounce for our plugin options
			settings_fields('dt_atp_plugin_settings');
			// generates a unique hidden field with our form handling url
			@do_settings_fields('dt_atp_plugin_settings');
			if($options['dt_atp_textbox']){
			$options['dt_atp_textbox'] = array_slice($options['dt_atp_textbox'], 0, 5);
				
			}
			
			$dt_atp_fields =  count($options['dt_atp_textbox']) == 0 ? '1' : count($options['dt_atp_textbox']);
			
			echo('<table class="form-table" >
			
				<tbody id="test">
			
					<tr>
						<th scope="row"><label>Choose Option:</label></th>
						<td>
						<input type="radio" name="dt_atp_radio" class="radio" '.checked( $options['dt_atp_radio'], 'hashtags' , false).' id="Twitter-Hashtags" value="hashtags"  />
						<label for="Twitter-Hashtags">Hashtags</label><br/>
					    <input type="radio" name="dt_atp_radio" class="radio" '.checked( $options['dt_atp_radio'], 'username' ,false).' id="Twitter-Username" value="username"  />
					    <label for="Twitter-Username">Username</label>
						</td>
					</tr>
				
			 		<script>
			 		
var i = '.$dt_atp_fields.'+1;

function myFunction(){
	if( i <=5){
	var para = document.createElement("tr");
    var para1 = document.createElement("th");
    var para2 = document.createElement("td");
    textbox = document.createElement("input");
	textbox.type = "text";
	textbox.name = "dt_atp_textbox[]";
	button = document.createElement("input");
	button.type = "button";
	button.id = "dt_atp_button_remove_"+i++;
	button.value = "-";
	button.onclick = function() { removetext(this.id); };
	para2.appendChild(textbox);
	para2.appendChild(button);
    para.appendChild(para1);
    para.appendChild(para2);
    document.getElementById("test").appendChild(para);}
}

function removetext(e){
	var rmtb = document.getElementById(e).parentNode;
	rmtb.parentNode.parentNode.removeChild(rmtb.parentNode);
	i--;
}
</script>
			 		
			
					<tr >
							<th scope="row"></th>
							<td>
							<input type="text" name="dt_atp_textbox[]" value="'. reset($options['dt_atp_textbox']) .'"/>
							<input type="button" onclick="myFunction()" value="Add"/>
							</td>
					</tr>');
					for ($i=1; $i < $dt_atp_fields; $i++){
					echo('	
					<tr >
							<th scope="row"></th>
							<td>
							<input type="text" name="dt_atp_textbox[]" value="'. $options['dt_atp_textbox'][$i] .'"/>
							<input type="button" onclick="removetext(this.id)" id="dt_atp_button_remove_'.$i.'" value="-"/>
							</td>
					</tr>'	
						);	
					}	

					
				
				
			echo('</tbody></table>');
		
			// outputs the WP submit button html
			@submit_button('Save Changes','primary','btnChange');
		
		
		echo('</form>');
	echo('<form action="options.php" method="post" style="width: 49%;
    display: inline-block;">');
		
			// outputs a unique nounce for our plugin options
			settings_fields('dt_atp_plugin_additional_settings');
			// generates a unique hidden field with our form handling url
			@do_settings_fields('dt_atp_plugin_additional_settings');
				echo('<table class="form-table">
			
				<tbody>
				
					<tr>
							<th scope="row"><label for="dt_atp_number_of_tweets">Number of Tweets</label></th>
							<td>
							
							<input type="number" name="dt_atp_number_of_tweets" id="dt_atp_number_of_tweets" value="'. $options['dt_atp_number_of_tweets'].'"/>
							</td>
					</tr>
					<tr><td colspan="15">
						<p class="description">Number of tweets you want to get per username/hashtag per API Call. <br />
								<strong>IMPORTANT:</strong> Limit is 180 API Calls, one API Call equals 15 Tweets.<br />
								<strong>SUGGESTION:</strong> for 5 hashtags/usernames, maximum is 165 tweets every 5 minutes.<br/>
								</p></td>
					</tr>
							<tr>
							<th scope="row"><label for="dt_atp_number_of_saved_tweets">Number of Saved Tweets</label></th>
							<td>
							
							<input type="number" name="dt_atp_number_of_saved_tweets" id="dt_atp_number_of_saved_tweets" value="'. $options['dt_atp_number_of_saved_tweets'].'"/>
							</td>
					</tr>
						<tr><td colspan="15">
						<p class="description">Number of tweets you want to save and show on your website, some functions may not work if plugin is disabled.<br />
								<strong>IMPORTANT:</strong> Higher the number, higher is the loading time of your website, some functions may not work if plugin is disabled.<br />
								<strong>SUGGESTION:</strong> Limit to 200-300 Tweets, no one wants to scroll forever.</p></td>
					</tr>
						<tr>
							<th scope="row"><label for="dt_atp_cron_time">WP Cron</label></th>
							<td>
							
							<input type="number" name="dt_atp_cron_time" id="dt_atp_cron_time" value="'. $options['dt_atp_cron_time'].'"/> minutes
							</td>
					</tr>
						<tr><td colspan="15">
						<p class="description">How offten to collect new Tweets, you have to active this option in <a href="?page=dt_atp_dashboard_admin_page">Dashboard</a>.<br />
								<strong>IMPORTANT:</strong> There is a limit, combine it with "Number of Tweets" from above.<br />
								<strong>SUGGESTION:</strong> If you have 5 hashtags/usernames and you left Number of Tweets on 165, leave this at default value ( 5 minutes ).<br />
								WP Cron activates every X minutes if you have a visitor, if you don\'t have high traffic you should add your website to cronjob.</p></td>
					</tr>');
		echo('</tbody></table>');
		
			// outputs the WP submit button html
			@submit_button();	
	echo('</form>
	</div>');
	
	
}
// 8.3
// hint: twitter settings
function dt_atp_twitter_settings_admin_page() {
	
	// get the default values for our options
	
	$options = dt_atp_get_current_options('twitter');
	echo('<div class="wrap">
		
		<h2>Twitter Settings</h2>');
								$options1 = dt_atp_get_current_options('dashboard');
		if($options1['dt_atp_status_enabled'] == 0 ){
		echo('<div class="notice notice-error is-dismissible">
        	<p>Plugin is disabled and not visible on your website, some functions may not work if plugin is disabled.</p>
    	</div>');
		}
		echo('<p class="description">This is the page where you enter details about your Twitter Application <br />
								IMPORTANT: If you don\'t know how to make your twitter application, check our instructions under the form.</p>
		
		<form action="options.php" method="post">');
		
			// outputs a unique nounce for our plugin options
			settings_fields('dt_atp_plugin_twitter');
			// generates a unique hidden field with our form handling url
			@do_settings_fields('dt_atp_plugin_twitter');
			
			echo('<table class="form-table">
			
				<tbody>
			
					<tr>
						<th scope="row"><label for="dt_atp_customer_key">Customer Key</label></th>
						<td>
						<input type="text" class="add-some-width" name="dt_atp_customer_key" id="dt_atp_customer_key" value="'. $options['dt_atp_customer_key'] .'" />
						</td>
					</tr>
					
			
					<tr>
							<th scope="row"><label for="dt_atp_customer_secret">Customer Secret</label></th>
							<td>
							<input type="text" class="add-some-width" name="dt_atp_customer_secret" id="dt_atp_customer_secret" value="'. $options['dt_atp_customer_secret'] .'" />
							</td>
					</tr>
						
					<tr>
							<th scope="row"><label for="dt_atp_access_token">Access Token</label></th>
							<td>
							<input type="text" class="add-some-width" name="dt_atp_access_token" id="dt_atp_access_token" value="'. $options['dt_atp_access_token'] .'" />
							</td>
					</tr>

					<tr>
							<th scope="row"><label for="dt_atp_access_token_secret">Access Token Secret</label></th>
							<td>
							<input type="text" class="add-some-width" name="dt_atp_access_token_secret" id="dt_atp_access_token_secret" value="'. $options['dt_atp_access_token_secret'] .'" />
							</td>
					</tr>
			
				</tbody>
				
			</table>');
		
			// outputs the WP submit button html
			@submit_button();
		
		
		echo('</form>
	
	</div>');
	
		$output = '
		<div class="wrap">
			
			<h2>How to setup your Twitter Application</h2>
			<p> There is few simple steps you need to follow</p>
			<ol> 
				<li> Go to <a href="http://apps.twitter.com/" target="_blank">http://apps.twitter.com/</a> </li>
				<li> Login and click Create New App
					<ol><br/>
					<li><strong>Name:</strong>  Give your app a unique name </li>
					<li><strong>Description:</strong>  You don’t have to worry much about the description- you can change this later. </li>
					<li><strong>Website:</strong>  Put your website in the website field. It’s supposed to be your application’s publicly accessible home page. </li>
					<li><strong>Callback URL:</strong>  You can ignore the Callback URL field. </li>
					</ol>
				</li>
				<li>You’ll then be presented with lots of information, but we’re not quite done yet. We now need to authorise the Twitter app for your Twitter account.<br/> To do this, click the “Create my access token” button. This takes a few seconds, so if you don’t see the access tokens on the next screen,<br/> you may have to refresh the page a few times.</li>
				<li>Once you’ve done this, you can copy your <strong>Consumer Key</strong>, <strong>Consumer Secret</strong>, <strong>OAuth Access Token</strong>, <strong>OAuth Access Token Secret</strong> to form above.</li>
			</ol>
		</div>
	';
	
	echo $output;
	
}

//8.4
// Display Style 
function dt_atp_display_style_admin_page() {
	
	$options = dt_atp_get_current_options('display');
	
echo('<div class="wrap"><h2>Display Settings</h2>');
		$options1 = dt_atp_get_current_options('dashboard');
		if($options1['dt_atp_status_enabled'] == 0 ){
		echo('<div class="notice notice-error is-dismissible">
        	<p>Plugin is disabled and not visible on your website, some functions may not work if plugin is disabled.</p>
    	</div>');
		}
echo('<p class="description">You can customize your format as you wish, you can see simple example in textarea, <strong>hover over buttons for description</strong><br/>
								<strong>IMPORTANT:</strong> Use only twitter option from buttons above textarea, custom options won\'t work.</p>
	<form action="options.php" method="post" style="width: 59%;
    display: inline-block;"><h3>Format your Tweets</h3>');
		
			// outputs a unique nounce for our plugin options
			settings_fields('dt_atp_display_style');
			// generates a unique hidden field with our form handling url
			@do_settings_fields('dt_atp_display_style');
			echo('<span style="line-height: 2;"><strong>Atributes from Twitter:</strong>
					<span style="float:right;">
						<button  onclick="insertAtCaret(\'textareaid\',\'screen_name\');return false;" class="thickbox button" title="Username that Published Tweet">Username</button>
						<button  onclick="insertAtCaret(\'textareaid\',\'id\');return false;" class="thickbox button" title="ID of User that Published Tweet">User ID</button>
						<button  onclick="insertAtCaret(\'textareaid\',\'status\');return false;" class="thickbox button" title="Tweet Text">Status</button>
						<button  onclick="insertAtCaret(\'textareaid\',\'image\');return false;" class="thickbox button" title="Profile Image of User">Image</button>
						<button  onclick="insertAtCaret(\'textareaid\',\'url\');return false;" class="thickbox button" title="Twitter URL of Tweet">Url</button>
						<button  onclick="insertAtCaret(\'textareaid\',\'created_at\');return false;" class="thickbox button" title="Date when Published">Date</button>
						
					</span>
				  </span><br/>
				<textarea id="textareaid" name="dt_atp_textarea_style" cols="15" rows="15" class="large-text code" style="width:100%;resize: none;">'. $options['dt_atp_textarea_style'] .'</textarea>
				<br/>
				<p>
					<strong>Add class to wrapper div:</strong>
					<input type="text" name="dt_atp_wrapper_class" style="width:300px;" placeholder="Separate classes with comma" id="dt_atp_wrapper_class" value="'. $options['dt_atp_wrapper_class'].'" />
				</p>'); 
			@submit_button();
			echo('<span>Want to make it look better? add "scroll" class to wrapper and define it in your css as: <br/>.scroll {<br/>
    overflow: scroll;<br/>
    max-height: 410px;<br/>
}</span></form></div>');
	
}

/* !9. SETTINGS */
// 9.1
// hint: registers all our plugin options
function dt_atp_register_twitter() {
	// plugin options
	register_setting('dt_atp_plugin_twitter', 'dt_atp_customer_key','dt_atp_validate_plugin');
	register_setting('dt_atp_plugin_twitter', 'dt_atp_customer_secret','dt_atp_validate_plugin');
	register_setting('dt_atp_plugin_twitter', 'dt_atp_access_token','dt_atp_validate_plugin');
	register_setting('dt_atp_plugin_twitter', 'dt_atp_access_token_secret','dt_atp_validate_plugin');

	
}
						
function dt_atp_register_settings() {
	// plugin options
	
	
	register_setting('dt_atp_plugin_settings', 'dt_atp_textbox','dt_atp_validation_function');
	register_setting('dt_atp_plugin_settings', 'dt_atp_radio','dt_atp_validate_wp_cron_again');
		if(isset($_POST['btnChange'])){
	$type = 'notice-warning';
    $message = __( 'After changing hashtags or usernames we suggest you go to <a href="?page=dt_atp_dashboard_admin_page">Dashboard</a> and reset old tweets.');
	add_settings_error(
        'updateSettings',
        esc_attr( 'settings_updated' ),
        $message,
        $type
    );
    
}


}						

function dt_atp_register_additional_settings(){
	register_setting('dt_atp_plugin_additional_settings', 'dt_atp_number_of_saved_tweets');
	register_setting('dt_atp_plugin_additional_settings', 'dt_atp_cron_time');
	register_setting('dt_atp_plugin_additional_settings', 'dt_atp_number_of_tweets','dt_atp_validate_wp_cron');
	
}						

function dt_atp_register_dashboard_form1(){
	register_setting('dt_atp_dashboard_form1', 'dt_atp_status_enabled','dt_atp_validate_status');
	
}
function dt_atp_register_dashboard_form2(){
	register_setting('dt_atp_dashboard_form2', 'dt_atp_wp_cron_enabled','dt_atp_validate_status_settings');
	
}
function dt_atp_register_display_style(){
	register_setting('dt_atp_display_style', 'dt_atp_textarea_style');
	register_setting('dt_atp_display_style', 'dt_atp_wrapper_class');
}


/* !10. MISCELLANEOUS */

//if hashtag/username field empty don't save it
function dt_atp_validation_function( $input ) {
    
   $input = array_filter($input);
	
    return $input;
}

//if twitter information empty don't allow plugin to be enabled
function dt_atp_validate_status( $input ) {
    
   if($input == 1){
   	$options = dt_atp_get_current_options('twitter');
   	if(empty($options) || empty($options['dt_atp_customer_secret']) || empty($options['dt_atp_access_token']) || empty($options['dt_atp_access_token_secret']) || empty($options['dt_atp_customer_key'])){
   		return 	add_settings_error(
        'TwitterError',
        esc_attr( 'check_status' ),
        'You can\'t enable plugin if you did not add Twitter App information, you can do that  <a href="?page=dt_atp_twitter_settings_admin_page">here</a>.',
        'notice-warning'
    );
   	}
   }

    return $input;
}
// if twitter information empty disable plugin
function dt_atp_validate_plugin( $input ) {
    
  if(empty($input)){
  	update_option('dt_atp_status_enabled',0);
  }else {
  	return $input;
  }

}
// if wp cron settings pass limit, don't allow it
function dt_atp_validate_wp_cron( $input ) {
    
	$time = floor(15 / get_option('dt_atp_cron_time',5));
	$calls = ceil ($input/15);
	$time = $calls * count(get_option('dt_atp_textbox')) * $time;
	if($time > 179){
		add_settings_error(
        'updateSettings',
        esc_attr( 'settings_updated' ),
        'Your current settings pass Limit of 180 API Calls, decrease number of Tweets or increase time of WP Cron.',
        'error'
    );
    return 29; 
	}
	else{
		return $input;
	}

}
function dt_atp_validate_wp_cron_again( $input ) {
    
	$time = floor(15 / get_option('dt_atp_cron_time',5));
	$calls = ceil (get_option('dt_atp_number_of_tweets')/15);
	$time = $calls * count(get_option('dt_atp_textbox')) * $time;
	if($time > 179){
		add_settings_error(
        'updateSettings',
        esc_attr( 'settings_updated' ),
        'Your current settings pass Limit of 180 API Calls, decrease number of Tweets or increase time of WP Cron.',
        'error'
    );
    update_option('dt_atp_number_of_tweets',29);
	}

	return $input;


}
//if twitter information empty and/or plugin disabled, don't allow wp cron
function dt_atp_validate_status_settings( $input ) {
    
if(get_option('dt_atp_status_enabled') == 1){
	if(count(get_option('dt_atp_textbox')) > 0){
		return dt_atp_validate_status( $input );
	}else{
		return 	add_settings_error(
	        'TwitterError',
	        esc_attr( 'check_status' ),
	        'You have to add hashtag/username <a href="?page=dt_atp_plugin_settings_admin_page">here</a>',
	        'notice-error'
	    );
	}
	
}
else{
	return 	add_settings_error(
	        'TwitterError',
	        esc_attr( 'check_status' ),
	        'You have to enable plugin.',
	        'notice-warning'
	    );
}

}


//get the last time json was modified 
function dt_atp_last_update_time(){
		$time = get_option('dt_atp_last_update_time');
	if ($time) {
		 $time = time() - $time;
		 $time = ($time<1)? 1 : $time;
		 $tokens = array (
		 31536000 => 'year',
		 2592000 => 'month',
		 604800 => 'week',
		 86400 => 'day',
		 3600 => 'hour',
		 60 => 'minute',
		 1 => 'second'
		 );
		 foreach ($tokens as $unit => $text) {
			 if ($time < $unit) continue;
			 $numberOfUnits = floor($time / $unit);
			 return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s ':'').' ago';
		 }
	}
	else {
		return 'Never';
	}
}
// reset json and last ids
function dt_atp_reset_tweets(){
		if(isset($_POST['btnReset'])){
		file_put_contents(dt_atp_plugin_dir.'twitter.json', json_encode(new stdClass));
		update_option('dt_atp_last_update_time',time());
		update_option('dt_atp_currently_active','');
		}
		$red = ($_SERVER["HTTP_REFERER"] != '' ? $_SERVER["HTTP_REFERER"] : site_url());
		header("Location: " . $red);
		exit;
}

// SCHEDULE 'WP CRON' if you can call it that way
if ( ! get_transient( 'schedule' ) && get_option('dt_atp_wp_cron_enabled')) {
    set_transient( 'schedule', true, get_option('dt_atp_cron_time') * MINUTE_IN_SECONDS );
    dt_atp_get_new_tweets();
}

function dt_atp_uninstall_plugin() {

		$options = array();
		$options['dt_atp_plugin_twitter'] = array('dt_atp_customer_key', 'dt_atp_customer_secret', 'dt_atp_access_token', 'dt_atp_access_token_secret');
		$options['dt_atp_plugin_settings'] = array('dt_atp_textbox', 'dt_atp_radio');
		$options['dt_atp_plugin_additional_settings'] = array('dt_atp_number_of_saved_tweets', 'dt_atp_cron_time', 'dt_atp_number_of_tweets');
		$options['dt_atp_dashboard_form1'] = array('dt_atp_status_enabled');
		$options['dt_atp_dashboard_form2'] = array('dt_atp_wp_cron_enabled');
		$options['dt_atp_display_style'] = array('dt_atp_textarea_style', 'dt_atp_wrapper_class');
		// loop over all the settings
		
		foreach( $options as $g=>$k ):

			foreach($k as $s){
				unregister_setting( $g, $s );
				delete_option($s);
			}
			
		endforeach;
		delete_option( 'dt_atp_currently_active' );
		delete_option( 'dt_atp_last_update_time' );
		
		return true;
}