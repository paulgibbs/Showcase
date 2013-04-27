<?php

/**
 * Showcase Core Functions
 *
 * @package Showcase
 * @subpackage Functions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/** Versions ******************************************************************/

/**
 * Output the showcase version
 *
 * @since Showcase (1.0)
 * @uses dps_get_version() To get the showcase version
 */
function dps_version() {
	echo dps_get_version();
}
	/**
	 * Return the showcase version
	 *
	 * @since Showcase (1.0)
	 * @retrun string The showcase version
	 */
	function dps_get_version() {
		return showcase()->version;
	}

/**
 * Output the showcase database version
 *
 * @since Showcase (1.0)
 * @uses dps_get_version() To get the showcase version
 */
function dps_db_version() {
	echo dps_get_db_version();
}
	/**
	 * Return the showcase database version
	 *
	 * @since Showcase (1.0)
	 * @retrun string The showcase version
	 */
	function dps_get_db_version() {
		return showcase()->db_version;
	}

/**
 * Output the showcase database version directly from the database
 *
 * @since Showcase (1.0)
 * @uses dps_get_version() To get the current showcase version
 */
function dps_db_version_raw() {
	echo dps_get_db_version_raw();
}
	/**
	 * Return the showcase database version directly from the database
	 *
	 * @since Showcase (1.0)
	 * @retrun string The current showcase version
	 */
	function dps_get_db_version_raw() {
		return get_option( '_dps_db_version', '' );
	}


/** Errors ********************************************************************/

/**
 * Adds an error message to later be output in the theme
 *
 * @since Showcase (1.0)
 * @see WP_Error()
 * @param string $code Unique code for the error message
 * @param string $message Translated error message
 * @param string $data Any additional data passed with the error message
 */
function dps_add_error( $code = '', $message = '', $data = '' ) {
	showcase()->errors->add( $code, $message, $data );
}

/**
 * Check if error messages exist in queue
 *
 * @since Showcase (1.0)
 * @see WP_Error()
 */
function dps_has_errors() {
	$has_errors = showcase()->errors->get_error_codes() ? true : false;

	return apply_filters( 'dps_has_errors', $has_errors, showcase()->errors );
}

/**
 * Delete a blogs rewrite rules, so that they are automatically rebuilt on
 * the subsequent page load.
 *
 * @since Showcase (1.0)
 */
function dps_delete_rewrite_rules() {
	delete_option( 'rewrite_rules' );
}


/** Requests ******************************************************************/

/**
 * Return true|false if this is a POST request
 *
 * @since Showcase (1.0)
 * @return bool
 */
function dps_is_post_request() {
	return (bool) ( 'POST' == strtoupper( $_SERVER['REQUEST_METHOD'] ) );
}

/**
 * Return true|false if this is a GET request
 *
 * @since Showcase (1.0)
 * @return bool
 */
function dps_is_get_request() {
	return (bool) ( 'GET' == strtoupper( $_SERVER['REQUEST_METHOD'] ) );
}

