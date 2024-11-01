<?php
/* ***************************** Admin Section ****************************** */

if ( !function_exists( 'gil_zm_add_options' ) ) {
	function gil_zm_add_options(){
		global $gil_zm_active_tab;
		// validate $_GET or set default
		$valid_tabs = array('gil_zm_disp_set', 'gil_zm_disp_lay');
		$gil_zm_active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'gil_zm_disp_set';
		$gil_zm_active_tab = in_array( $gil_zm_active_tab, $valid_tabs ) ? $gil_zm_active_tab : 'gil_zm_disp_set';
		add_options_page( GIL_ZM_PLUGIN_NAME, GIL_ZM_PLUGIN_NAME, 'manage_options', 'gil_zm_section', 'gil_zm_settings_page' );
	}
}
add_action('admin_menu', 'gil_zm_add_options');



/*
*
*/
if ( !function_exists( 'gil_zm_settings_page' ) ) {
	function gil_zm_settings_page(){
		global $gil_zm_active_tab;
	?>
	<style>
		.rTW.dashicons-before:before {
			content: url(<?php echo plugins_url( 'images/logo.png',__FILE__ ); ?>);
			height: 32px;
			margin-right: 4px;
			width: 32px;
		}
	</style>
	<div class="wrap">
		<h2 class="rTW dashicons-before dashicons-analytics"><?php echo GIL_ZM_PLUGIN_NAME ?></h2>
		<form action="options.php" method="post">
		<h2 class="nav-tab-wrapper">
			<a href="?page=gil_zm_section&tab=gil_zm_disp_set" class="nav-tab <?php echo $gil_zm_active_tab == 'gil_zm_disp_set' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Settings', 'gil-zone-marker' ) ?></a>

			<a href="?page=gil_zm_section&tab=gil_zm_disp_lay" class="nav-tab <?php echo $gil_zm_active_tab == 'gil_zm_disp_lay' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Layout', 'gil-zone-marker' ) ?></a>
		</h2>
	<?php
		switch ( $gil_zm_active_tab ) {
			case 'gil_zm_disp_set':
				settings_fields( 'gil_zm_settings' );
				do_settings_sections( 'gil_zm_settings_section' );
				submit_button();
				break;
			case 'gil_zm_disp_lay':
				settings_fields( 'gil_zm_layout' );
				do_settings_sections( 'gil_zm_layout_section' );
				submit_button();
				break;
			default:
				echo 'Error: ' . __LINE__;
		}
	?>
		</form>
	</div>
	<?php
	}  //gil_zm_settings_page
}



/*
* Register and define the settings
*/
if ( !function_exists( 'gil_zm_admin_init' ) ) {
	function gil_zm_admin_init(){
		global $gil_zm_active_tab;

		// The following fails on the second register_setting if included in the if statements below
		// The reason is unknown
		register_setting(
			'gil_zm_settings',
			'gil_zm_settings',
			'gil_zm_validate_settings'
		);

		register_setting(
			'gil_zm_layout',
			'gil_zm_layout',
			'gil_zm_validate_layout'
		);

		if ( $gil_zm_active_tab == 'gil_zm_disp_set' ) {
			add_settings_section(
				'gil_zm_settings',
				GIL_ZM_PLUGIN_NAME . ' Plugin Settings',
				'gil_zm_settings_section_text',
				'gil_zm_settings_section'
			);

			add_settings_field(
				'gil_zm_google_key',
				esc_html__( 'Google Key', 'gil-zone-marker' ),
				'gil_zm_input_text',
				'gil_zm_settings_section',
				'gil_zm_settings',
				array(
					'name'        => 'gil_zm_settings',
					'id'          => 'gil_zm_google_key',
					'placeholder' => 'The is a long string of mixed characters',
					'max_length'  => '39',
					'description' => 'See instructions above'
				)
			);

			add_settings_field(
				'gil_zm_default_lat',
				esc_html__( 'Default Latitude', 'gil-zone-marker' ),
				'gil_zm_input_text',
				'gil_zm_settings_section',
				'gil_zm_settings',
				array(
					'name'        => 'gil_zm_settings',
					'id'          => 'gil_zm_default_lat',
					'placeholder' => '12.345678',
					'max_length'  => '10',
					'description' => 'Please change the default value (max. 6 decimal points)'
				)
			);

			add_settings_field(
				'gil_zm_default_lng',
				esc_html__( 'Default Longitude', 'gil-zone-marker' ),
				'gil_zm_input_text',
				'gil_zm_settings_section',
				'gil_zm_settings',
				array(
					'name'        => 'gil_zm_settings',
					'id'          => 'gil_zm_default_lng',
					'placeholder' => '12.345678',
					'max_length'  => '10',
					'description' => 'Please change the default value (max. 6 decimal points)'
				)
			);
		}

		elseif ( $gil_zm_active_tab == 'gil_zm_disp_lay' ){
			add_settings_section(
				'gil_zm_layout',
				GIL_ZM_PLUGIN_NAME . ' Plugin Layout Options',
				'gil_zm_layout_section_text',
				'gil_zm_layout_section'
			);

			add_settings_field(
				'gil_zm_heading',
				esc_html__( 'Heading', 'gil-zone-marker' ),
				'gil_zm_input_text',
				'gil_zm_layout_section',
				'gil_zm_layout',
				array(
					'name'        => 'gil_zm_layout',
					'id'          => 'gil_zm_heading',
					'placeholder' => esc_html__( 'Max 40 characters', 'gil-zone-marker' ),
					'max_length'  => '40',
					'description' => ''
				)
			);

			add_settings_field(
				'gil_zm_intro',
				esc_html__( 'Intro', 'gil-zone-marker' ),
				'gil_zm_input_textarea',
				'gil_zm_layout_section',
				'gil_zm_layout',
				array(
					'name'        => 'gil_zm_layout',
					'id'          => 'gil_zm_intro',
					'placeholder' => esc_html__( 'Introductory paragraph (450 characters)', 'gil-zone-marker' ),
					'max_length'  => '450',
					'description' => 'Max 450 characters including spaces'
				)
			);

			add_settings_field(
				'gil_zm_checkbox_1',
				esc_html__( 'Checkbox 1', 'gil-zone-marker' ),
				'gil_zm_input_text',
				'gil_zm_layout_section',
				'gil_zm_layout',
				array(
					'name'        => 'gil_zm_layout',
					'id'          => 'gil_zm_checkbox_1',
					'placeholder' => esc_html__( 'Max 25 characters', 'gil-zone-marker' ),
					'max_length'  => '25',
					'description' => ''
				)
			);

			add_settings_field(
				'gil_zm_checkbox_2',
				esc_html__( 'Checkbox 2', 'gil-zone-marker' ),
				'gil_zm_input_text',
				'gil_zm_layout_section',
				'gil_zm_layout',
				array(
					'name'        => 'gil_zm_layout',
					'id'          => 'gil_zm_checkbox_2',
					'placeholder' => esc_html__( 'Max 25 characters', 'gil-zone-marker' ),
					'max_length'  => '25',
					'description' => ''
				)
			);

			add_settings_field(
				'gil_zm_show_submitted',
				esc_html__( 'Show submitted', 'gil-zone-marker' ),
				'gil_zm_input_checkbox',
				'gil_zm_layout_section',
				'gil_zm_layout',
				array(
					'name'        => 'gil_zm_layout',
					'id'          => 'gil_zm_show_submitted',
					'placeholder' => esc_html__( 'Display the email contents after submission', 'gil-zone-marker' ),
					'max_length'  => '1',
					'description' => ''
				)
			);
		}
	}
}
add_action('admin_init', 'gil_zm_admin_init');



// Draw the section header
if ( !function_exists( 'gil_zm_settings_section_text' ) ) {
	function gil_zm_settings_section_text() {
	?>
		<p class="gil-zm-warning"><?php esc_html_e( 'Note, this plugin will not work without a valid Google Key.' ) ?></p>
		<p><?php esc_html_e( 'Enter your Google Key below (obtainable from your Google Cloud Platform).', 'gil-zone-marker' ) ?></p>
		<p><?php esc_html_e( 'Ensure that The restrictions have been set and the correct API enabled.', 'gil-zone-marker' ) ?></p>
		<p>
		<h3><?php esc_html_e( 'To get your Google Map API.', 'gil-zone-marker' ) ?></h3>
		<ol>
			<li><?php esc_html_e( 'login into', 'gil-zone-marker' ) ?> <a href="https://cloud.google.com/maps-platform" target="_blank">https://cloud.google.com/maps-platform</a> <?php esc_html__( 'and selected maps in the popup', 'gil-zone-marker' ) ?></li>
			<li><?php esc_html_e( 'Create a new project', 'gil-zone-marker' ) ?></li>
			<li><?php esc_html_e( 'Enable billing', 'gil-zone-marker' ) ?></li>
			<li><?php esc_html_e( 'Enable Google API', 'gil-zone-marker' ) ?></li>
			<li><?php esc_html_e( 'Restrict the keys usage by following the link to the API Console', 'gil-zone-marker' ) ?></li>
			<li><?php esc_html_e( "Select restrictions. e.g. HTTP referrers", 'gil-zone-marker' ) ?></li>
			<li><?php esc_html_e( 'Add an item', 'gil-zone-marker' ) ?></li>
			<li>//*.example.com/*</li>
			<li><?php esc_html_e( 'Restrict key', 'gil-zone-marker' ) ?></li>
			<li><?php esc_html_e( 'Add Maps Javascript API, Maps Embed API, Maps Static API, Geolocation API, Geocoding API to the selected filter', 'gil-zone-marker' ) ?></li>
			<li>Save</li>
			</ol>
		</p>
		<p>Place the shortcode [zonemarker] into you page or post.</p>
	<?php
	}
}



// Draw the section header
if ( !function_exists( 'gil_zm_layout_section_text' ) ) {
	function gil_zm_layout_section_text() {
	?>
		<p><?php esc_html_e( 'Settings for the form and other layout options', 'gil-zone-marker' ) ?>.</p>
	<?php
	}
}



/*
* Display and fill the text field
*/
if ( !function_exists( 'gil_zm_input_text' ) ) {
	function gil_zm_input_text( $args ) {
		// get option value from the database
		$options = (array) get_option( $args['name'] );
		$opt     = $args['id'] ? $args['id'] : '';
		$val     = isset( $options[$opt] ) ? $options[$opt] : '';
		// echo the field
		echo '<input id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $args['name'] ) . '[' . esc_attr( $args['id'] ) . ']" type="text" value="' . esc_attr( $val ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" class="regular-text" maxlength="' . esc_attr( $args['max_length'] ) . '"><br>' . esc_attr( $args['description'] );
	}
}



/*
* Display and fill the textarea field
*/
if ( !function_exists( 'gil_zm_input_textarea' ) ) {
	function gil_zm_input_textarea( $args ) {
		// get option value from the database
		$options = (array) get_option( $args['name'] );
		$opt     = $args['id'] ? $args['id'] : '';
		$val     = isset( $options[$opt] ) ? $options[$opt] : '';
		// echo the field
		echo '<textarea id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $args['name'] ) . '[' . esc_attr( $args['id'] ) . ']" placeholder="' . esc_attr( $args['placeholder'] ) . '" class="regular-text" maxlength="' . esc_attr( $args['max_length'] ) . '">' . esc_attr( $val ) . '</textarea><br>' . esc_attr( $args['description'] );
	}
}


/*
* Display and fill the text field
*/
if ( !function_exists( 'gil_zm_input_checkbox' ) ) {
	function gil_zm_input_checkbox( $args ) {
		// get option value from the database
		$options = (array) get_option( $args['name'] );
		$opt     = $args['id'] ? $args['id'] : '';
		$val     = isset( $options[$opt] ) ? $options[$opt] : '';
		// echo the field
		echo '<input type="checkbox" id="' . esc_attr( $args['id'] ) . '" name="' . esc_attr( $args['name'] ) . '[' . esc_attr( $args['id'] ) . ']" class="regular-checkbox"' . ( esc_attr( $val ) ? ' checked' : '' ) . '> <span class="description">' . esc_attr( $args['placeholder'] ) . '</span><br>' . esc_attr( $args['description'] );
	}
}


/*
* Validate user input
*/
if ( !function_exists( 'gil_zm_validate_settings' ) ) {
	function gil_zm_validate_settings( $input ) {
		//var_dump( $input );
		//exit();
		$options = get_option('gil_zm_settings');
		$err_msg = array();
		$gil_zm_google_key_length = isset( $input['gil_zm_google_key'] ) ? strlen( trim( $input['gil_zm_google_key'] ) ) : 0;

		if( preg_match( '/[A-Za-z0-9_-]{39}/', $input['gil_zm_google_key'] ) && $gil_zm_google_key_length == 39 ){
			$options['gil_zm_google_key'] = trim( sanitize_text_field( $input['gil_zm_google_key'] ) );
			$valid = $options;
		}
		else{
			$options['gil_zm_google_key'] = '';
			$valid     = false;
			$err_msg[] = 'Incorrect length of ' . esc_html( $gil_zm_google_key_length ) . ' submitted for the Google key';
		}

		if( preg_match( '/^[+-]?(([1-8]?[0-9])(\.[0-9]{1,6})?|90(\.0{1,6})?)$/', $input['gil_zm_default_lat'] ) ){
			$options['gil_zm_default_lat'] = trim( sanitize_text_field( $input['gil_zm_default_lat'] ) );
			$valid = $options;
		}
		else{
			$options['gil_zm_default_lat'] = '';
			$valid     = false;
			$err_msg[] = 'Invalid latitude submitted';
		}

		if( preg_match( '/^[+-]?((([1-9]?[0-9]|1[0-7][0-9])(\.[0-9]{1,6})?)|180(\.0{1,6})?)$/', $input['gil_zm_default_lng'] ) ){
			$options['gil_zm_default_lng'] = trim( sanitize_text_field( $input['gil_zm_default_lng'] ) );
			$valid = $options;
		}
		else{
			$options['gil_zm_default_lng'] = '';
			$valid     = false;
			$err_msg[] = 'Invalid longitude submitted';
		}

		if( $valid != $input ) {
			add_settings_error(
				'gil_zm_text_string',
				'gil_zm_texterror',
				'Error! - ' . implode (', ', $err_msg ),
				'error'
			);
		}
		//var_dump( $options );
		//exit();
		return $options;
	}
}


/*
* Validate user input 
*/
if ( !function_exists( 'gil_zm_validate_layout' ) ) {
	function gil_zm_validate_layout( $input ) {
		$gil_zm_options = get_option('gil_zm_layout');
		$gil_zm_err_msg = array();

		$gil_zm_san_txt = gil_zm_sanitize_text( $input['gil_zm_heading'], 'heading' );
		$gil_zm_options['gil_zm_heading'] = $gil_zm_san_txt['input'];
		isset( $gil_zm_san_txt['err'] ) ? $gil_zm_err_msg[] = $gil_zm_san_txt['err'] : NULL;
		$gil_zm_valid = $gil_zm_san_txt['err'] ? false : $gil_zm_options;

		$gil_zm_san_txt = gil_zm_sanitize_text( $input['gil_zm_checkbox_1'], 'checkbox 1' );
		$gil_zm_options['gil_zm_checkbox_1'] = $gil_zm_san_txt['input'];
		isset( $gil_zm_san_txt['err'] ) ? $gil_zm_err_msg[] = $gil_zm_san_txt['err'] : NULL;
		$gil_zm_valid = $gil_zm_san_txt['err'] ? false : $gil_zm_options;

		$gil_zm_san_txt = gil_zm_sanitize_text( $input['gil_zm_checkbox_2'], 'checkbox 2' );
		$gil_zm_options['gil_zm_checkbox_2'] = $gil_zm_san_txt['input'];
		isset( $gil_zm_san_txt['err'] ) ? $gil_zm_err_msg[] = $gil_zm_san_txt['err'] : NULL;
		$gil_zm_valid = $gil_zm_san_txt['err'] ? false : $gil_zm_options;

		if( $input['gil_zm_intro'] ){
			$gil_zm_options['gil_zm_intro'] = sanitize_textarea_field( $input['gil_zm_intro'] );
			$gil_zm_valid = $gil_zm_options;
		}
		else{
			$gil_zm_options['gil_zm_intro'] = '';
			$gil_zm_valid     = false;
			$gil_zm_err_msg[] = 'Invalid intro submitted';
		}
		$gil_zm_options['gil_zm_show_submitted'] = isset( $input['gil_zm_show_submitted'] ) ? $input['gil_zm_show_submitted'] : '';

		if( ! empty( $err_msg ) ) {
			add_settings_error(
				'gil_zm_text_string',
				'gil_zm_texterror',
				'Error! - ' . implode (', ', $gil_zm_err_msg ),
				'error'
			);
		}
		//var_dump( $options );
		//exit();
		return $gil_zm_options;
	}
}


/*
* Validate user text input
* accepts string & string
* returns array
*/
if ( !function_exists( 'gil_zm_sanitize_text' ) ) {
	function gil_zm_sanitize_text( $val, $err ) {
		$ret = array();
		if( $val ){
			$ret['input'] = sanitize_text_field( $val );
			$ret['err']   = NULL;
		}
		elseif( $ret['input'] == '' ){
			$ret['input'] = '';
			$ret['err']   = NULL;
		}
		else{
			$ret['input'] = '';
			$ret['err']   = 'Invalid ' . $err . ' submitted';
		}
		return $ret;
	}
}



/* 
* Accepts a global array $links
* Adds the 'Settings' link below the Plugin title on the Installed Plugins' page
* Returns the populated $links array
*/
if( !function_exists( 'plugin_add_settings_link' ) ) {
	function plugin_add_settings_link( $links ) {
		$links['settings'] = '<a href="options-general.php?page=gil_zm_section">' . __( 'Settings' ) . '</a>';
		return $links;
	}
}
add_filter( 'plugin_action_links_' . str_replace( 'admin/', '', str_replace( '-admin', '', plugin_basename(  __FILE__ ) ) ), 'plugin_add_settings_link' );
?>