<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
$options = array();
$options['dt_atp_plugin_twitter'] = array('dt_atp_customer_key', 'dt_atp_customer_secret', 'dt_atp_access_token', 'dt_atp_access_token_secret');
$options['dt_atp_plugin_settings'] = array('dt_atp_textbox', 'dt_atp_radio');
$options['dt_atp_plugin_additional_settings'] = array('dt_atp_number_of_saved_tweets', 'dt_atp_cron_time', 'dt_atp_number_of_tweets');
$options['dt_atp_dashboard_form1'] = array('dt_atp_status_enabled');
$options['dt_atp_dashboard_form2'] = array('dt_atp_wp_cron_enabled');
$options['dt_atp_display_style'] = array('dt_atp_textarea_style', 'dt_atp_wrapper_class');
$options['dt_atp_display_style'] = array('dt_atp_textarea_style', 'dt_atp_date_format');
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