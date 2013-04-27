<?php

/**
 * Showcase Extentions
 *
 * There's a world of really cool plugins out there, and showcase comes with
 * support for some of the most popular ones.
 *
 * @package Showcase
 * @subpackage Extend
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Loads Akismet inside the showcase global class
 *
 * @since Showcase (1.0)
 *
 * @return If showcase is not active
 */
function dps_setup_akismet() {

	// Bail if no akismet
	if ( !defined( 'AKISMET_VERSION' ) ) return;

	// Bail if Akismet is turned off
	if ( !dps_is_akismet_active() ) return;

	// Include the Akismet Component
	require( showcase()->includes_dir . 'extend/akismet.php' );

	// Instantiate Akismet for showcase
	showcase()->extend->akismet = new BB_Akismet();
}

/**
 * Requires and creates the BuddyPress extension, and adds component creation
 * action to bp_init hook. @see dps_setup_buddypress_component()
 *
 * @since Showcase (1.0)
 * @return If BuddyPress is not active
 */
function dps_setup_buddypress() {

	if ( ! function_exists( 'buddypress' ) ) {

		/**
		 * Helper for BuddyPress 1.6 and earlier
		 *
		 * @since Showcase (1.0)
		 * @return BuddyPress
		 */
		function buddypress() {
			return isset( $GLOBALS['bp'] ) ? $GLOBALS['bp'] : false;
		}
	}

	// Bail if in maintenance mode
	if ( ! buddypress() || buddypress()->maintenance_mode )
		return;

	// Include the BuddyPress Component
	require( showcase()->includes_dir . 'extend/buddypress/loader.php' );

	// Instantiate BuddyPress for showcase
	showcase()->extend->buddypress = new BB_Forums_Component();
}
