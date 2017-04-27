<?php

namespace Another_Twitter_Plugin\Admin\pages;

class Manual extends AbstractPage
{

    public function __construct()
    {
        add_action('admin_menu', array($this, 'manual_add_plugin_page'));
    }

    public function manual_add_plugin_page()
    {
        add_submenu_page(
            'dt_atp_dashboard_admin_page',
            '',
            'Manual',
            'manage_options',
            'dt_atp_manual_admin_page',
            array( $this, 'manual_create_admin_page'
            )
        );
    }

    public function manual_create_admin_page()
    {
        require_once __DIR__.'/../partials/manual.php';
    }
}
