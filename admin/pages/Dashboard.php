<?php

class Dashboard {
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
			'dt_atp_dashboard_admin_page',// function
			'dashicons-twitter', // icon_url
			81 // position
		);
                add_submenu_page( 'dt_atp_dashboard_admin_page', '', 'Dashboard', 'manage_options', 'dt_atp_dashboard_admin_page',
			array( $this, 'dashboard_create_admin_page' ) 
		);
	}

	public function dashboard_create_admin_page() {
		$this->dashboard_options = get_option( 'dt_atp_dashboard' ); ?>

		<div class="wrap">
			<h2>Another Twitter Plugin</h2>
			<p></p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
                                        settings_fields( 'dt_atp_dashboard_group' );
                                        do_settings_sections( 'dt_atp_dashboard' );
                                        submit_button();
				?>
			</form>
		</div>
	<?php }

	public function header_page_init() {
		register_setting(
			'dt_atp_dashboard_group', // option_group
			'dt_atp_dashboard', // option_name
			array( $this, 'header_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'dt_atp_dashboard_section', // id
			'', // title
			array( $this, 'dt_atp_dashboard_section_info' ), // callback
			'dt_atp_dashboard' // page
		);

		add_settings_field(
			'title_0', // id
			'Title', // title
			array( $this, 'title_0_callback' ), // callback
			'header-admin', // page
			'dt_atp_dashboard_section' // section
		);

		add_settings_field(
			'text_1', // id
			'Text', // title
			array( $this, 'text_1_callback' ), // callback
			'header-admin', // page
			'dt_atp_dashboard_section' // section
		);

		add_settings_field(
			'url_2', // id
			'url', // title
			array( $this, 'url_2_callback' ), // callback
			'header-admin', // page
			'dt_atp_dashboard_section' // section
		);
	}

	public function header_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['title_0'] ) ) {
			$sanitary_values['title_0'] = sanitize_text_field( $input['title_0'] );
		}

		if ( isset( $input['text_1'] ) ) {
			$sanitary_values['text_1'] = esc_textarea( $input['text_1'] );
		}

		if ( isset( $input['url_2'] ) ) {
			$sanitary_values['url_2'] = sanitize_text_field( $input['url_2'] );
		}

		return $sanitary_values;
	}

	public function header_section_info() {

	}

	public function title_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="header_option_name[title_0]" id="title_0" value="%s">',
			isset( $this->header_options['title_0'] ) ? esc_attr( $this->header_options['title_0']) : ''
		);
	}

	public function text_1_callback() {
		printf(
			'<textarea class="large-text" rows="5" name="header_option_name[text_1]" id="text_1">%s</textarea>',
			isset( $this->header_options['text_1'] ) ? esc_attr( $this->header_options['text_1']) : ''
		);
	}

	public function url_2_callback() {
		printf(
			'<input class="regular-text" type="text" name="header_option_name[url_2]" id="url_2" value="%s">',
			isset( $this->header_options['url_2'] ) ? esc_attr( $this->header_options['url_2']) : ''
		);
	}

}
if ( is_admin() )
	$header = new Header();

	?>
