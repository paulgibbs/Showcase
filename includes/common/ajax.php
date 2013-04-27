<?php

/**
 * Showcase Common AJAX Functions
 *
 * Common AJAX functions are ones that are used to setup and/or use during
 * Showcase specific, theme-side  AJAX requests.
 *
 * @package Showcase
 * @subpackage Ajax
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Output the URL to use for theme-side showcase AJAX requests
 *
 * @since Showcase (1.0)
 *
 * @uses dps_get_ajax_url() To get the URL to use for AJAX requests
 */
function dps_ajax_url() {
	echo dps_get_ajax_url();
}
	/**
	 * Return the URL to use for theme-side showcase AJAX requests
	 *
	 * @since Showcase (1.0)
	 *
	 * @global WP $wp
	 * @return string
	 */
	function dps_get_ajax_url() {
		global $wp;

		$base_url = home_url( trailingslashit( $wp->request ), ( is_ssl() ? 'https' : 'http' ) );
		$ajaxurl  = add_query_arg( array( 'bbp-ajax' => 'true' ), $base_url );

		return apply_filters( 'dps_get_ajax_url', $ajaxurl );
	}

/**
 * Is this a showcase AJAX request?
 *
 * @since Showcase (1.0)
 *
 * @return bool Looking for bbp-ajax
 */
function dps_is_ajax() {
	return (bool) ( isset( $_GET['bbp-ajax'] ) && ! empty( $_REQUEST['action'] ) );
}

/**
 * Hooked to the 'dps_template_redirect' action, this is showcase's custom
 * theme-side ajax handler.
 *
 * @since Showcase (1.0)
 *
 * @return If not a showcase ajax request
 */
function dps_do_ajax() {

	// Bail if not an ajax request
	if ( ! dps_is_ajax() )
		return;

	// Set WordPress core ajax constant
	define( 'DOING_AJAX', true );

	// Set the header content type
	@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );

	// Disable content sniffing in browsers that support it
	send_nosniff_header();

	// Perform custom showcase ajax
	do_action( 'dps_ajax_' . $_REQUEST['action'] );

	// All done
	die( '0' );
}

/**
 * Helper method to return JSON response for the ajax calls
 *
 * @since Showcase (1.0)
 *
 * @param bool $success
 * @param string $content
 * @param array $extras
 */
function dps_ajax_response( $success = false, $content = '', $status = -1, $extras = array() ) {

	// Set status to 200 if setting response as successful
	if ( ( true === $success ) && ( -1 === $status ) )
		$status = 200;

	// Setup the response array
	$response = array(
		'success' => $success,
		'status'  => $status,
		'content' => $content
	);

	// Merge extra response parameters in
	if ( !empty( $extras ) && is_array( $extras ) ) {
		$response = array_merge( $response, $extras );
	}

	// Send back the JSON
	@header( 'Content-type: application/json' );
	echo json_encode( $response );
	die();
}
