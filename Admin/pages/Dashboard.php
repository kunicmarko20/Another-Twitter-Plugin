<?php

namespace Another_Twitter_Plugin\Admin\pages;

class Dashboard extends AbstractPage
{
	private $dashboard_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'dashboard_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'dashboard_page_init' ) );
	}

	public function dashboard_add_plugin_page() {
		add_menu_page(
			'Another Twitter', // page_title
			'Another Twitter', // menu_title
			'manage_options', // capability
			'dt_atp_dashboard_admin_page', // menu_slug
			array( $this, 'dashboard_create_admin_page' ) ,
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

	public function dashboard_create_admin_page() {
		$this->dashboard_options = get_option( 'dashboard_option_name' ); ?>

		<div class="wrap">
			<h2>Dashboard</h2>
			<p>dashboard</p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'dashboard_option_group' );
					do_settings_fields( 'dashboard-admin','dashboard_setting_section' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function dashboard_page_init() {
		register_setting(
			'dashboard_option_group', // option_group
			'dashboard_option_name', // option_name
			array( $this, 'dashboard_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'dashboard_setting_section', // id
			'Settings', // title
			array( $this, 'dashboard_section_info' ), // callback
			'dashboard-admin' // page
		);

		add_settings_field(
			'text1_0', // id
			'text1', // title
			array( $this, 'text1_0_callback' ), // callback
			'dashboard-admin', // page
			'dashboard_setting_section' // section
		);

		add_settings_field(
			'text2_1', // id
			'text2', // title
			array( $this, 'text2_1_callback' ), // callback
			'dashboard-admin', // page
			'dashboard_setting_section' // section
		);
	}

	public function dashboard_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['text1_0'] ) ) {
			$sanitary_values['text1_0'] = sanitize_text_field( $input['text1_0'] );
		}

		if ( isset( $input['text2_1'] ) ) {
			$sanitary_values['text2_1'] = sanitize_text_field( $input['text2_1'] );
		}

		return $sanitary_values;
	}

	public function dashboard_section_info() {
		
	}

	public function text1_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="dashboard_option_name[text1_0]" id="text1_0" value="%s">',
			isset( $this->dashboard_options['text1_0'] ) ? esc_attr( $this->dashboard_options['text1_0']) : ''
		);
	}

	public function text2_1_callback() {
		printf(
			'<input class="regular-text" type="text" name="dashboard_option_name[text2_1]" id="text2_1" value="%s">',
			isset( $this->dashboard_options['text2_1'] ) ? esc_attr( $this->dashboard_options['text2_1']) : ''
		);
	}

}
/* 
 * Retrieve this value with:
 * $dashboard_options = get_option( 'dashboard_option_name' ); // Array of All Options
 * $text1_0 = $dashboard_options['text1_0']; // text1
 * $text2_1 = $dashboard_options['text2_1']; // text2
 */
