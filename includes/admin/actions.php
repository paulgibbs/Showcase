<?php

/**
 * Showcase Admin Actions
 *
 * @package Showcase
 * @subpackage Admin
 *
 * This file contains the actions that are used through-out showcase Admin. They
 * are consolidated here to make searching for them easier, and to help developers
 * understand at a glance the order in which things occur.
 *
 * There are a few common places that additional actions can currently be found
 *
 *  - showcase: In {@link Showcase::setup_actions()} in showcase.php
 *  - Admin: More in {@link BB_Admin::setup_actions()} in admin.php
 *
 * @see bbp-core-actions.php
 * @see bbp-core-filters.php
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Attach showcase to WordPress
 *
 * Showcase uses its own internal actions to help aid in third-party plugin
 * development, and to limit the amount of potential future code changes when
 * updates to WordPress core occur.
 *
 * These actions exist to create the concept of 'plugin dependencies'. They
 * provide a safe way for plugins to execute code *only* when showcase is
 * installed and activated, without needing to do complicated guesswork.
 *
 * For more information on how this works, see the 'Plugin Dependency' section
 * near the bottom of this file.
 *
 *           v--WordPress Actions       v--showcase Sub-actions
 */
add_action( 'admin_menu',              'dps_admin_menu'                    );
add_action( 'admin_init',              'dps_admin_init'                    );
add_action( 'admin_head',              'dps_admin_head'                    );
add_action( 'admin_notices',           'dps_admin_notices'                 );
add_action( 'wpmu_new_blog',           'dps_new_site',               10, 6 );

// Hook on to admin_init
add_action( 'dps_admin_init', 'dps_admin_forums'                );
add_action( 'dps_admin_init', 'dps_setup_updater',          999 );

// Initialize the admin area
add_action( 'dps_init', 'dps_admin' );

// Reset the menu order
add_action( 'dps_admin_menu', 'dps_admin_separator' );

// Activation
add_action( 'dps_activation', 'dps_delete_rewrite_rules'    );

// Deactivation
add_action( 'dps_deactivation', 'dps_remove_caps'          );
add_action( 'dps_deactivation', 'dps_delete_rewrite_rules' );

// New Site
add_action( 'dps_new_site', 'dps_create_initial_content', 8 );

// Contextual Helpers
add_action( 'load-settings_page_showcase', 'dps_admin_settings_help' );


/**
 * When a new site is created in a multisite installation, run the activation
 * routine on that site
 *
 * @since Showcase (1.0)
 *
 * @param int $blog_id
 * @param int $user_id
 * @param string $domain
 * @param string $path
 * @param int $site_id
 * @param array() $meta
 */
function dps_new_site( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

	// Bail if plugin is not network activated
	if ( ! is_plugin_active_for_network( showcase()->basename ) )
		return;

	// Switch to the new blog
	switch_to_blog( $blog_id );

	// Do the showcase activation routine
	do_action( 'dps_new_site', $blog_id, $user_id, $domain, $path, $site_id, $meta );

	// restore original blog
	restore_current_blog();
}

/** Sub-Actions ***************************************************************/

/**
 * Piggy back admin_init action
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_admin_init'
 */
function dps_admin_init() {
	do_action( 'dps_admin_init' );
}

/**
 * Piggy back admin_menu action
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_admin_menu'
 */
function dps_admin_menu() {
	do_action( 'dps_admin_menu' );
}

/**
 * Piggy back admin_head action
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_admin_head'
 */
function dps_admin_head() {
	do_action( 'dps_admin_head' );
}

/**
 * Piggy back admin_notices action
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_admin_notices'
 */
function dps_admin_notices() {
	do_action( 'dps_admin_notices' );
}
