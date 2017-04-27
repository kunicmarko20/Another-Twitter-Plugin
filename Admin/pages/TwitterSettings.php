<?php

namespace Another_Twitter_Plugin\Admin\pages;

class TwitterSettings extends AbstractPage
{
    private $twitter_settings_options;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'twitter_settings_add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'twitter_settings_page_init' ) );
    }

    public function twitter_settings_add_plugin_page() {
        add_submenu_page(
            'dt_atp_dashboard_admin_page',
            '',
            'Twitter Settings',
            'manage_options',
            'dt_atp_twitter_settings_admin_page',
            array( $this, 'twitter_settings_create_admin_page'
            )
        );
    }

    public function twitter_settings_create_admin_page() {
        $this->twitter_settings_options = get_option( 'twitter_settings_option_name' ); ?>

        <div class="wrap">
            <h2>Twitter Settings</h2>
            <p></p>
            <?php settings_errors(); ?>

            <form method="post" action="options.php">
                <?php
                settings_fields( 'twitter_settings_option_group' );
                do_settings_sections( 'twitter-settings-admin' );
                submit_button();
                ?>
            </form>
        </div>
    <?php }

    public function twitter_settings_page_init() {
        register_setting(
            'twitter_settings_option_group', // option_group
            'twitter_settings_option_name', // option_name
            array( $this, 'twitter_settings_sanitize' ) // sanitize_callback
        );

        add_settings_section(
            'twitter_settings_setting_section', // id
            null, // title
            null, // callback
            'twitter-settings-admin' // page
        );

        add_settings_field(
            'customer_key', // id
            'Customer Key', // title
            array( $this, 'customer_key_callback' ), // callback
            'twitter-settings-admin', // page
            'twitter_settings_setting_section' // section
        );

        add_settings_field(
            'customer_secret', // id
            'Customer Secret', // title
            array( $this, 'customer_secret_callback' ), // callback
            'twitter-settings-admin', // page
            'twitter_settings_setting_section' // section
        );

        add_settings_field(
            'access_token', // id
            'Access Token', // title
            array( $this, 'access_token_callback' ), // callback
            'twitter-settings-admin', // page
            'twitter_settings_setting_section' // section
        );

        add_settings_field(
            'access_token_secret', // id
            'Access Token Secret', // title
            array( $this, 'access_token_secret_callback' ), // callback
            'twitter-settings-admin', // page
            'twitter_settings_setting_section' // section
        );
    }

    public function twitter_settings_sanitize($input) {
        $sanitary_values = array();
        if ( isset( $input['customer_key'] ) ) {
            $sanitary_values['customer_key'] = sanitize_text_field( $input['customer_key'] );
        }

        if ( isset( $input['customer_secret'] ) ) {
            $sanitary_values['customer_secret'] = sanitize_text_field( $input['customer_secret'] );
        }

        if ( isset( $input['access_token'] ) ) {
            $sanitary_values['access_token'] = sanitize_text_field( $input['access_token'] );
        }

        if ( isset( $input['access_token_secret'] ) ) {
            $sanitary_values['access_token_secret'] = sanitize_text_field( $input['access_token_secret'] );
        }

        return $sanitary_values;
    }

    public function customer_key_callback() {
        printf(
            '<input class="regular-text" type="text" name="twitter_settings_option_name[customer_key]" id="customer_key" value="%s">',
            isset( $this->twitter_settings_options['customer_key'] ) ? esc_attr( $this->twitter_settings_options['customer_key']) : ''
        );
    }

    public function customer_secret_callback() {
        printf(
            '<input class="regular-text" type="text" name="twitter_settings_option_name[customer_secret]" id="customer_secret" value="%s">',
            isset( $this->twitter_settings_options['customer_secret'] ) ? esc_attr( $this->twitter_settings_options['customer_secret']) : ''
        );
    }

    public function access_token_callback() {
        printf(
            '<input class="regular-text" type="text" name="twitter_settings_option_name[access_token]" id="access_token" value="%s">',
            isset( $this->twitter_settings_options['access_token'] ) ? esc_attr( $this->twitter_settings_options['access_token']) : ''
        );
    }

    public function access_token_secret_callback() {
        printf(
            '<input class="regular-text" type="text" name="twitter_settings_option_name[access_token_secret]" id="access_token_secret" value="%s">',
            isset( $this->twitter_settings_options['access_token_secret'] ) ? esc_attr( $this->twitter_settings_options['access_token_secret']) : ''
        );
    }

}
/*
 * Retrieve this value with:
 * $twitter_settings_options = get_option( 'twitter_settings_option_name' ); // Array of All Options
 * $customer_key = $twitter_settings_options['customer_key']; // Customer Key
 * $customer_secret = $twitter_settings_options['customer_secret']; // Customer Secret
 * $access_token = $twitter_settings_options['access_token']; // Access Token
 * $access_token_secret = $twitter_settings_options['access_token_secret']; // Access Token Secret
 */
