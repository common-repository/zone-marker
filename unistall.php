<?php
// If uninstall is not called from WordPress exit
if( !defined( 'WP_UNISTALL_PLUGIN' ) ) {
	exit;
}

// Delete options from the options table
delete_option( 'gil_zm_settings' );
delete_option( 'gil_zm_layout' );
?>