<?php


namespace Another_Twitter_Plugin\Admin\pages;

class DisplaySettings extends AbstractPage
{
	private $display_settings_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'display_settings_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'display_settings_page_init' ) );
	}

	public function display_settings_add_plugin_page() {
            add_submenu_page( 
                    'dt_atp_dashboard_admin_page', 
                    '', 
                    'Display Settings', 
                    'manage_options', 
                    'dt_atp_display_settings_admin_page',
			array( $this, 'display_settings_create_admin_page' 
                            ) 
		);
	}

	public function display_settings_create_admin_page() {
		$this->display_settings_options = get_option( 'display_settings_option_name' ); ?>

		<div class="wrap">
			<h2>Display Settings</h2>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
                            <h3>Format your Tweets</h3>
				<?php
					settings_fields( 'display_settings_option_group' );
                                        do_settings_fields( 'display-settings-admin', 'display_settings_setting_section' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function display_settings_page_init() {
		register_setting(
			'display_settings_option_group', // option_group
			'display_settings_option_name', // option_name
			array( $this, 'display_settings_sanitize' ) // sanitize_callback
		);


		add_settings_field(
			'test2_1', // id
			'', // title
			array( $this, 'test2_1_callback' ), // callback
			'display-settings-admin', // page
			'display_settings_setting_section' // section
		);
                add_settings_field(
			'test_0', // id
			'<strong>Add class to wrapper div:</strong>', // title
			array( $this, 'test_0_callback' ), // callback
			'display-settings-admin', // page
			'display_settings_setting_section' // section
		);
                                
		add_settings_field(
			'test3_2', // id
			'<strong>Output date format:</strong>', // title
			array( $this, 'test3_2_callback' ), // callback
			'display-settings-admin', // page
			'display_settings_setting_section' // section
		);
	}

	public function display_settings_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['test_0'] ) ) {
			$sanitary_values['test_0'] = sanitize_text_field( $input['test_0'] );
		}

		if ( isset( $input['test2_1'] ) ) {
			$sanitary_values['test2_1'] = esc_textarea( $input['test2_1'] );
		}

		if ( isset( $input['test3_2'] ) ) {
			$sanitary_values['test3_2'] = sanitize_text_field( $input['test3_2'] );
		}

		return $sanitary_values;
	}

	public function display_settings_section_info() {
		
	}

	public function test_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="display_settings_option_name[test_0]" id="test_0" value="%s"><br/>',
			isset( $this->display_settings_options['test_0'] ) ? esc_attr( $this->display_settings_options['test_0']) : ''
		);
	}

	public function test2_1_callback() {
           require_once __DIR__.'/../partials/display-settings-textarea.php';
	}

	public function test3_2_callback() {
		printf(
			'<input class="regular-text" type="text" name="display_settings_option_name[test3_2]" id="test3_2" value="%s">',
			isset( $this->display_settings_options['test3_2'] ) ? esc_attr( $this->display_settings_options['test3_2']) : ''
		);
	}

}

/* 
 * Retrieve this value with:
 * $display_settings_options = get_option( 'display_settings_option_name' ); // Array of All Options
 * $test_0 = $display_settings_options['test_0']; // test
 * $test2_1 = $display_settings_options['test2_1']; // test2
 * $test3_2 = $display_settings_options['test3_2']; // test3
 */
