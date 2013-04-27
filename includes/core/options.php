<?php

/**
 * Showcase Options
 *
 * @package Showcase
 * @subpackage Options
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get the default site options and their values.
 *
 * @since Showcase (1.0)
 * @return array Filtered option names and values
 */
function dps_get_default_options() {

	// Default options
	return apply_filters( 'dps_get_default_options', array(

		/** DB Version ********************************************************/
		'_dps_db_version' => showcase()->db_version,

		/** Settings **********************************************************/
		'_dps_theme_package_id' => 'default', // The ID for the current theme package.

		/** Per Page **********************************************************/
		'_dps_showcases_per_page' => 8, // Forums per page

		/** Archive Slugs *****************************************************/
		'_dps_root_slug' => 'showcase', // Showcase archive slug

	) );
}

/**
 * Add default options
 *
 * Hooked to dps_activate, it is only called once when showcase is activated.
 * This is non-destructive, so existing settings will not be overridden.
 *
 * @since Showcase (1.0)
 * @uses dps_get_default_options() To get default options
 * @uses add_option() Adds default options
 * @uses do_action() Calls 'dps_add_options'
 */
function dps_add_options() {

	// Add default options
	foreach ( dps_get_default_options() as $key => $value )
		add_option( $key, $value );

	// Allow previously activated plugins to append their own options.
	do_action( 'dps_add_options' );
}

/**
 * Delete default options
 *
 * Hooked to dps_uninstall, it is only called once when showcase is uninstalled.
 * This is destructive, so existing settings will be destroyed.
 *
 * @since Showcase (1.0)
 * @uses dps_get_default_options() To get default options
 * @uses delete_option() Removes default options
 * @uses do_action() Calls 'dps_delete_options'
 */
function dps_delete_options() {

	// Add default options
	foreach ( array_keys( dps_get_default_options() ) as $key )
		delete_option( $key );

	// Allow previously activated plugins to append their own options.
	do_action( 'dps_delete_options' );
}

/**
 * Add filters to each showcase option and allow them to be overloaded from
 * inside the $bbp->options array.
 *
 * @since Showcase (1.0)
 * @uses dps_get_default_options() To get default options
 * @uses add_filter() To add filters to 'pre_option_{$key}'
 * @uses do_action() Calls 'dps_add_option_filters'
 */
function dps_setup_option_filters() {

	// Add filters to each showcase option
	foreach ( array_keys( dps_get_default_options() ) as $key )
		add_filter( 'pre_option_' . $key, 'dps_pre_get_option' );

	// Allow previously activated plugins to append their own options.
	do_action( 'dps_setup_option_filters' );
}

/**
 * Filter default options and allow them to be overloaded from inside the
 * $bbp->options array.
 *
 * @since Showcase (1.0)
 * @param bool $value Optional. Default value false
 * @return mixed false if not overloaded, mixed if set
 */
function dps_pre_get_option( $value = '' ) {

	// Remove the filter prefix
	$option = str_replace( 'pre_option_', '', current_filter() );

	// Check the options global for preset value
	if ( isset( showcase()->options[$option] ) )
		$value = showcase()->options[$option];

	// Always return a value, even if false
	return $value;
}

/**
 * Get the current theme package ID
 *
 * @since Showcase (1.0)
 * @param $default string Optional. Default value 'default'
 * @uses get_option() To get the subtheme option
 * @return string ID of the subtheme
 */
function dps_get_theme_package_id( $default = 'default' ) {
	return apply_filters( 'dps_get_theme_package_id', get_option( '_dps_theme_package_id', $default ) );
}


/** Slugs *********************************************************************/

/**
 * Return the root slug
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_root_slug( $default = 'forums' ) {
	return apply_filters( 'dps_get_root_slug', get_option( '_dps_root_slug', $default ) );
}
