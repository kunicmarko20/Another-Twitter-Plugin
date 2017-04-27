<?php

namespace Another_Twitter_Plugin\Admin\pages;

class Dashboard extends AbstractPage
{
    private $dashboard_options;
    public function __construct()
    {
        add_action('admin_menu', array($this, 'dashboard_add_plugin_page'));
        add_action('admin_init', array($this, 'dashboard_page_init'));
    }

    public function dashboard_add_plugin_page()
    {
        add_menu_page(
            'Another Twitter', // page_title
            'Another Twitter', // menu_title
            'manage_options', // capability
            'dt_atp_dashboard_admin_page', // menu_slug
            array($this, 'dashboard_create_admin_page'),
            'dashicons-twitter', // icon_url
            81 // position
        );
        add_submenu_page(
            'dt_atp_dashboard_admin_page',
            '',
            'Dashboard',
            'manage_options',
            'dt_atp_dashboard_admin_page',
            array( $this, 'dashboard_create_admin_page' )
        );
    }

    public function dashboard_create_admin_page()
    {
    $this->dashboard_options = get_option('dt_atp_dashboard_options_name');
    ?>

        <div class="wrap">
            <h2>Dashboard</h2>
            <p></p>
            <?php settings_errors(); ?>

            <form method="post" action="options.php">
                <?php
                    settings_fields('dt_atp_dashboard_options_group');
                    do_settings_sections('dt_atp_dashboard_admin');
                ?>
            </form>
            <?php require_once __DIR__.'/../partials/dashboard.php'; ?>
        </div>
<?php
    }

    public function dashboard_page_init()
    {
        register_setting(
            'dt_atp_dashboard_options_group', // option_group
            'dt_atp_dashboard_options_name', // option_name
            array( $this, 'dt_atp_dashboard_options_sanitize' ) // sanitize_callback
        );
        add_settings_section(
            'dt_atp_dashboard_setting_section', // id
            null, // title
            null, // callback
            'dt_atp_dashboard_admin' // page
        );
        add_settings_field(
            'dt_atp_status', // id
            'Status', // title
            array( $this, 'dt_atp_status' ), // callback
            'dt_atp_dashboard_admin',
            'dt_atp_dashboard_setting_section'
        );
        add_settings_field(
            'dt_atp_wp_cron', // id
            'Cron', // title
            array( $this, 'dt_atp_wp_cron' ), // callback
            'dt_atp_dashboard_admin',
            'dt_atp_dashboard_setting_section'
        );


    }

    public function dt_atp_dashboard_options_sanitize($input)
    {
        $sanitary_values = array();

        if ($_POST['dt_atp_status_submit']) {
            $sanitary_values['dt_atp_status'] = (int)sanitize_text_field( $input['dt_atp_status'] );
        } else {
            $sanitary_values['dt_atp_status'] = $input['dt_atp_status'] ? 0 : 1;
        }

        if ( $_POST['dt_atp_wp_cron_submit'] ) {
            $sanitary_values['dt_atp_wp_cron'] = (int)sanitize_text_field( $input['dt_atp_wp_cron'] );
        } else {
            $sanitary_values['dt_atp_wp_cron'] = $input['dt_atp_wp_cron'] ? 0 : 1;
        }

        return $sanitary_values;
    }
    public function dt_atp_status()
    {
        $this->renderButtonField('dt_atp_status');
    }
    public function dt_atp_wp_cron()
    {
        $this->renderButtonField('dt_atp_wp_cron');
    }

    public function renderButtonField($name)
    {
        $value = $this->dashboard_options[$name];
        if ($value == 1) {
            echo ('<label style="color:green;">Enabled</label></td><td>');
            submit_button("Disable", "secondary", "{$name}_submit", false, array('style' => 'color:red;'));
            printf(
                '<input type="hidden" id="%s" name="dt_atp_dashboard_options_name[%1$s]" value="0" /></td>',
                $name
            );
        } else {
            echo ('<label style="color:red;">Disabled</label></td><td>');
            submit_button("Enable", "secondary", "{$name}_submit", false, array('style' => 'color:green;'));
            printf(
                '<input type="hidden" id="%s" name="dt_atp_dashboard_options_name[%1$s]" value="1" /></td>',
                $name
            );
        }
    }

    public function dt_atp_last_update_time()
    {
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
}
/* 
 * Retrieve this value with:
 * $dashboard_options = get_option( 'dashboard_option_name' ); // Array of All Options
 * $text1_0 = $dashboard_options['text1_0']; // text1
 * $text2_1 = $dashboard_options['text2_1']; // text2
 */
