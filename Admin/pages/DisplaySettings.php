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
			'dt_atp_style', // id
			'', // title
			array( $this, 'dt_atp_style_callback' ), // callback
			'display-settings-admin', // page
			'display_settings_setting_section' // section
		);
        add_settings_field(
            'dt_atp_wrapper_class', // id
            '<strong>Output date format:</strong>', // title
            array( $this, 'dt_atp_wrapper_class_callback' ), // callback
            'display-settings-admin', // page
            'display_settings_setting_section' // section
        );
                add_settings_field(
			'dt_atp_date_format', // id
			'<strong>Add class to wrapper div:</strong>', // title
			array( $this, 'dt_atp_date_format_callback' ), // callback
			'display-settings-admin', // page
			'display_settings_setting_section' // section
		);
                                

	}

	public function display_settings_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['dt_atp_date_format'] ) ) {
			$sanitary_values['dt_atp_date_format'] = sanitize_text_field( $input['dt_atp_date_format'] );
		}

		if ( isset( $input['dt_atp_style'] ) ) {
			$sanitary_values['dt_atp_style'] = esc_textarea( $input['dt_atp_style'] );
		}

		if ( isset( $input['dt_atp_wrapper_class'] ) ) {
			$sanitary_values['dt_atp_wrapper_class'] = sanitize_text_field( $input['dt_atp_wrapper_class'] );
		}

		return $sanitary_values;
	}

	public function display_settings_section_info() {
		
	}

	public function dt_atp_date_format_callback() {
		printf(
			' <input class="regular-text" type="text" name="display_settings_option_name[dt_atp_date_format]" id="dt_atp_date_format" value="%s">',
			isset( $this->display_settings_options['dt_atp_date_format'] ) ? esc_attr( $this->display_settings_options['dt_atp_date_format']) : ''
		);
	}

	public function dt_atp_style_callback() {
           require_once __DIR__.'/../partials/display-settings-textarea.php';
	}

	public function dt_atp_wrapper_class_callback() {
		printf(
			' <input class="regular-text" type="text" name="display_settings_option_name[dt_atp_wrapper_class]" id="dt_atp_wrapper_class" value="%s"><br/>',
			isset( $this->display_settings_options['dt_atp_wrapper_class'] ) ? esc_attr( $this->display_settings_options['dt_atp_wrapper_class']) : ''
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
