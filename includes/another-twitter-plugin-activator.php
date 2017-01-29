<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.1.0
 * @package    Another_Twitter_Plugin
 * @subpackage another-twitter-plugin/includes
 * @author     Marko Kunic <kunicmarko20@gmail.com>
 */
class Another_Twitter_Plugin_Activator {

	/**
	 * Add default values for options to database.
	 *
	 * @since    1.1.0
	 */
	public static function activate() {
            add_option('dt_atp_date_format','h:i A - d M Y');
            add_option('dt_atp_status_enabled',0);
            add_option('dt_atp_number_of_tweets',165);
            add_option('dt_atp_number_of_tweets',165);
            add_option('dt_atp_number_of_saved_tweets',200);
            add_option('dt_atp_cron_time',5);
            add_option('dt_atp_textbox',[]);
            add_option('dt_atp_textarea_style',$this->getDefaultPreviewHtml());
	}
        /**
	 * Default html placeholder for preview of data.
	 *
	 * @since    1.1.0
	 */ 
        private static function getDefaultPreviewHtml(){
            return '<section>
                        <div>
                            <a href="[url]" target="_blank">
                                <img src="[image]" alt="">
                            </a>
                        </div>
                        <div>
                            <p>[status]</p>
                        </div>
                    </section>';
        }

}
