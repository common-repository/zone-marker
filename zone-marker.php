<?php
/*
* Plugin Name: Zone Marker
* Description: Highlight an area on a map and submit it. Ideal for marking out an area for drones to capture or land boundaries for legal or other purposes.
* Author: Andy Gilbert
* Text Domain: zone-marker
* Domain Path: /languages
* Version: 1.0.46
* Author URI: https://www.routetoweb.co.uk
* Plugin URI: https://www.routetoweb.co.uk/zone-marker
*/

namespace zonemarker;

define( 'GIL_ZM_PLUGIN_NAME', 'Zone Marker' );
define( 'GIL_ZM_PLUGIN_VERSION', '1.0.45' );

if( ! function_exists('is_plugin_active')) {
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
}

// Set the text domain for the languages. Also set Domain Path in the metadata
load_plugin_textdomain( 'zone-marker', false ,'zone-marker/languages' );



/*
* Activate
* Check for WordPress version
* Set the defaults in the options table
*/
if( !function_exists( 'gil_zm_plugin_install' ) ) {
	function gil_zm_plugin_install() {
		if ( version_compare( get_bloginfo( 'version' ), '4.5', '<' ) ) {
			deactivate_plugins( basename( __FILE__ ) ); // Deactivate our plugin
		}
		$opts = array(
			'gil_zm_google_key'  => '',
			'gil_zm_default_lat' => '51.503181',
			'gil_zm_default_lng' => '-0.119717',
		);
		add_option ( 'gil_zm_settings', $opts, '', '' );
		
		$opts = array(
			'gil_zm_heading'        => 'Draw your area',
			'gil_zm_intro'          => 'The map below helps you plot a detailed outline of the location you wish us to use. When you submit the form it will be emailed to us to ensure that we understand this and can formulate an accurate proposal.',
			'gil_zm_checkbox_1'     => 'Option 1',
			'gil_zm_checkbox_2'     => 'Option 2',
			'gil_zm_show_submitted' => ''
		);
		add_option ( 'gil_zm_layout', $opts, '', '' );
	}
}
register_activation_hook( __FILE__, 'zonemarker\gil_zm_plugin_install' );



if( !function_exists( 'gil_zm_plugin_uninstall' ) ) {
	function gil_zm_plugin_uninstall() {
		// Nothing to do
	}
}
register_deactivation_hook( __FILE__, 'zonemarker\gil_zm_plugin_uninstall' );



// Just in case another plugin has called this
if( !function_exists( 'gil_zm_scripts' ) ) {
	function gil_zm_scripts() {
		$options = get_option( 'gil_zm_settings' );
		$gil_zm_google_key = isset( $options['gil_zm_google_key'] ) ? $options['gil_zm_google_key'] : '';
		wp_enqueue_script( 'gil-zm-maps', '//maps.googleapis.com/maps/api/js?key=' . $gil_zm_google_key . '&libraries=geometry' );
		wp_enqueue_script('gil-zm-script', plugins_url( 'public/js/zone-marker.js', __FILE__ ), array( 'jquery' ), GIL_ZM_PLUGIN_VERSION, true);
		if ( is_admin() ) {
			wp_enqueue_style( 'gil-zm-css', plugins_url( 'admin/css/zone-marker-admin.css', __FILE__ ) );
		}
		else{
			wp_enqueue_style( 'gil-zm-css', plugins_url( 'public/css/zone-marker.css', __FILE__ ) );
		}
	}
}
add_action( 'admin_init', 'zonemarker\gil_zm_scripts' );
add_action( 'gil_zm_load_scripts', 'zonemarker\gil_zm_scripts' );



/*
* accepts nothing
* Output the public page content
* returns nothing
*/
if( !function_exists( 'gil_zm_display_form' ) ) {
	function gil_zm_display_form(){
		$options = get_option( 'gil_zm_layout' );
		// Check that it is frontend and not backend
		$ret = 'ABC';
		if ( ! is_admin() || is_preview() ) {
			ob_start();
			?>
			<div id="siteLocator" class="gil-zm-wrapper">
				<h2><?php esc_html_e( $options['gil_zm_heading'], 'zone-marker' ) ?></h2>
				<p><?php esc_html_e( $options['gil_zm_intro'], 'zone-marker' ) ?></p>
				<!-- form -->
				<form id="gilZmDataCapture" action="<?php echo esc_url( $_SERVER['REQUEST_URI'] ) ?>" method="post">
					<div class="gil-zm-search">
						<p for="gilZmAddress"><?php esc_html_e( 'Please enter the address, town or postcode of the location that you wish to highlight', 'zone-marker' ) ?>.</p>

						<div>
							<input type="text" name="gil-zm-address" id="gilZmAddress" class="gil-zm-location gil-zm-search-control" placeholder="<?php esc_html_e( 'Address, town or postcode', 'zone-marker' ) ?>">
						</div>
						<div class="gil-zm-buttons">
							<button type="button" name="gil-zm-find-location" id="gilZmFindLocation" class="button gil-zm-search-control"><span class="gil-zm-search-icon" aria-hidden="true"><?php esc_html_e( 'Find location', 'zone-marker' ) ?></span></button>

							<button id="gilZmMyLocation" class="button gil-zm-search-control"><span class="gil-zm-location-icon" aria-hidden="true"><?php esc_html_e( 'Current Location', 'zone-marker' ) ?></span></button>

							<button id="gilZmReset" value="Reset" class="button gil-zm-search-control"><span class="gil-zm-reset-icon" aria-hidden="true"><?php esc_html_e( 'Reset Map', 'zone-marker' ) ?></span></button>
						</div>
					</div>

					<div id="gil-zm-google-map"></div>
					<div id="gil-zm-google-map-zone-area" class="gil-zm-google-map-zone-area"></div>

					<div class="gil-zm-data-capture">
						<p><?php esc_html_e( 'Create an area on the map by clicking on the map to create boundary points. These points can be dragged to improve the accuracy. To remove a point, click it', 'zone-marker' ) ?>.</p>

						<input type="hidden" id="gilZmGoogleMarkers" name="gil-zm-google-markers">
						<input type="hidden" id="gilZmGoogleMapUrl" name="gil-zm-google-map-url">
						<input type="hidden" id="gilZmGoogleMapZoneArea" name="gil-zm-google-map-zone-area">
						<input type="hidden" id="gilZmHref" name="gil-zm-href">

						<p><label>
						<?php esc_html_e( 'Site name', 'zone-marker' ) ?> <span class="asterisk">*</span>
						<input class="gil-zm-input" type="text" name="gil-zm-site-name" id="gilZmSiteName" required></label></p>
						<p><label>
						<?php esc_html_e( 'Brief description', 'zone-marker' ) ?>
						<textarea class="gil-zm-input" aria-gilZmFindLocation="false" name="gil-zm-description"></textarea>
						</label></p>

						<p><?php esc_html_e( 'Enquiry type', 'zone-marker' ) ?> <span class="asterisk">*</span></p>

						<section id="gil-zm-site-type-group">
							<?php if( isset( $options['gil_zm_checkbox_1'] ) && strlen( $options['gil_zm_checkbox_1'] ) > 0 ){ ?>
							<label><?php echo esc_html( $options['gil_zm_checkbox_1'] ) ?> <input type="checkbox" value="<?php echo esc_attr( $options['gil_zm_checkbox_1'] ) ?>" name="gil-zm-site-type[]" required></label>
							<?php }

							if( isset( $options['gil_zm_checkbox_2'] ) && strlen( $options['gil_zm_checkbox_2'] ) > 0 ){ ?>
							<label><?php echo esc_html( $options['gil_zm_checkbox_2'] ) ?> <input type="checkbox" value="<?php echo esc_attr( $options['gil_zm_checkbox_2'] ) ?>" name="gil-zm-site-type[]" required></label>
							<?php } ?>
						</section>
						<p><label>
							<?php esc_html_e( 'Your email address', 'zone-marker' ) ?> <span class="asterisk">*</span>
							<input type="email" name="gil-zm-email-address" id="gilZmEmailAddress" class="wpcf7-form-control wpcf7-text gil-zm-input" placeholder="abc@example.com" required>
						</label></p>

						<p><label>
							<?php esc_html_e( 'Your name', 'zone-marker' ) ?> <span class="asterisk">*</span>
							<input type="text" name="gil-zm-person-name" id="gilZmPersonName" class="wpcf7-form-control wpcf7-text gil-zm-input" placeholder="<?php esc_html_e( 'First and last name', 'zone-marker' ) ?>" required>
						</label></p>

						<p><input type="submit" value="<?php esc_html_e( 'Submit', 'zone-marker' ) ?>" name="gil-zm-submit" id="gilZmSubmit" class="button"></p>
					</div>
				</form>
			</div>
			<?php
			$ret = ob_get_contents();
			ob_end_clean();
		}
		return $ret;
	} //gil_zm_display_form()
} // end if
add_filter('gil_zm_display_form_action', 'zonemarker\gil_zm_display_form');



/*
* Accepts nothing
* The controller
* Sets the localized variable to pass data to the JavaScript
* Calls the page output using the shortcode: [zone-marker]
* Calls the emailer
* Returns nothing
*/
if( !function_exists( 'gil_zm_map_area' ) ) {
	function gil_zm_map_area(){
		do_action( 'gil_zm_load_scripts' );
		$settings_options = (array) get_option( 'gil_zm_settings' );
		$layout_options   = (array) get_option( 'gil_zm_layout' );
		// localize the JavaScript
		wp_localize_script( 'gil-zm-script', 'gilZmLocalized', array(
			'googleMarker' => plugins_url('public/images/marker.png', __FILE__ ),
			'googleKey'    => $settings_options['gil_zm_google_key'],
			'defaultLat'   => $settings_options['gil_zm_default_lat'],
			'defaultLng'   => $settings_options['gil_zm_default_lng'],
			'heading'      => $layout_options['gil_zm_heading'],
			'intro'        => $layout_options['gil_zm_intro'],
		) );

		$action = empty( $_POST['gil-zm-site-name'] ) ? '' : $_POST['gil-zm-site-name'];

		// only send the data if the Zone Marker site is named
		// otherwise display the form
		if ( ! empty( $action ) ) {
			return apply_filters( 'gil_zm_deliver_mail_action','' );
		}
		else{
			return apply_filters('gil_zm_display_form_action','');
		}
	}
}
add_shortcode('zonemarker','zonemarker\gil_zm_map_area');



/*
* Uses the POST vars from the form
* Sends the mail from the public form
* Echos a sussess or failure message
* retruns nothing
*/
if( !function_exists( 'gil_zm_deliver_mail' ) ) {
	function gil_zm_deliver_mail() {
		// if the submit button is clicked, send the email
		$ret = '';
		if ( isset( $_POST['gil-zm-submit'] ) ) {
			$name      = stripslashes( sanitize_text_field( $_POST["gil-zm-person-name"] ) );
			$email     = sanitize_email( $_POST["gil-zm-email-address"] );
			$site_name = stripslashes( sanitize_text_field( $_POST["gil-zm-site-name"] ) );
			$map_url   = esc_url( $_POST["gil-zm-google-map-url"] );
			$map_href  = esc_url( $_POST["gil-zm-href"]);
			$coords    = esc_html( $_POST["gil-zm-google-markers"] );
			$coords    = explode('|', $coords);
			$area      = esc_html( $_POST["gil-zm-google-map-zone-area"] );
			$p_coords  = '';
			$count = 0;
			foreach( $coords as $coord ) {
				$count ++;
				$p_coords .= 'P' . $count . ': ' . $coord . '<br>';
			}
			$msg       = '<div style="font-family: sans-serif">';

			$msg      .= '<p><strong>' . esc_html__( 'Name', 'zone-marker' ) . ':</strong> ' . $name . '</p>';
			$msg      .= '<p><strong>' . esc_html__( 'Email', 'zone-marker' ) . ':</strong> ' . $email . '</p>';
			$msg      .= '<p><strong>' . esc_html__( 'Site name', 'zone-marker' ) . ':</strong> ' . $site_name . '</p>';
			$msg      .= '<p><img src="' . $map_url . '"></p>';
			if ( $map_href ) {
				$msg .= '<p><strong>' . esc_html__( 'Link', 'zone-marker' ) . ':</strong> <a href="' . $map_href . '" target="_blank">' . $map_href . '</a> (' . esc_html__( 'no markers', 'zone-marker' ) . ')</p>';
			}
			$msg      .= '<p><strong>' . esc_html__( 'Search Address', 'zone-marker' ) . ':</strong> ' . esc_html( $_POST["gil-zm-address"] ) . '</p>';
			$msg      .= '<p><strong>' . esc_html__( 'Description', 'zone-marker' ) . ':</strong> ' . esc_html( $_POST["gil-zm-description"] ) . '</p>';
			$msg      .= '<p><strong>' . esc_html__( 'Project type', 'zone-marker' ) . ':</strong> ' . esc_html( implode( ', ', $_POST["gil-zm-site-type"] ) ) . '</p>';
			$msg      .= '<p><strong>' . esc_html__( 'Latitude, longitude', 'zone-marker' ) . ':</strong><br>' . $p_coords . '</p>';
			if ( $area ) {
				$msg .= '<p><strong>' . esc_html__( 'Area', 'zone-marker' ) . ':</strong> ' . number_format_i18n( $area, 0 ) . 'm<sup>2</sup></p>';
			}

			$msg      .= '</div>';
			// sanitize form values
			//$name    = sanitize_text_field( $_POST["gil-zm-person-name"] );
			//$email   = sanitize_email( $_POST["gil-zm-email-address"] );
			$subject = GIL_ZM_PLUGIN_NAME . ' Enquiry | ' . $site_name;
			// Hidden values

			$email_only_msg = '';
			$email_only_msg .= '<div style="font-family: sans-serif">';
			$email_only_msg .= '<p><strong>Submitting URL:</strong> ' . $_SERVER['HTTP_REFERER'] . '</p>';
			$email_only_msg .= '</div>';
			$message = $msg . $email_only_msg;

			// get the blog administrator's email address
			$to      = get_option( 'admin_email' );
			$headers = array(
				'From: ' . $name . ' <' . $email . '>',
				'Content-Type: text/html; charset=UTF-8',
				'Reply-To: ' . $name . '<' . $email . '>',
			);

			// If email has been process for sending, display a success message
			if ( wp_mail( $to, $subject, $message, $headers ) ) {
				$options = (array) get_option( 'gil_zm_layout' );
				if ($options['gil_zm_show_submitted'] ) {
					$ret .= '<div style="font-size: smaller;">' . $msg . '</div>';
				}
				$ret .= '<p>' . esc_html__( 'Thanks for contacting us, expect a response soon', 'zone-marker' ) . '</p>';
				$ret .= '<p><a href="#" onclick="javascript:window.history.back();">' . esc_html__( 'Back to the map', 'zone-marker' ) . '</a></p>';
			}
			else {
				$ret = esc_html__( 'An unexpected error occurred', 'zonemarker\zone-marker' );
			}
			return $ret;
		}
	}
}
add_filter( 'gil_zm_deliver_mail_action', 'zonemarker\gil_zm_deliver_mail' );



/* ***************************** Admin Section ****************************** */
if ( is_admin() ) {
	// we are in admin mode
	require_once( dirname( __FILE__ ) . '/admin/zone-marker-admin.php' );
}
?>