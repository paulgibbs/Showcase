<?php

/**
 * Plugin Dependency
 *
 * The purpose of the following hooks is to mimic the behavior of something
 * called 'plugin dependency' which enables a plugin to have plugins of their
 * own in a safe and reliable way.
 *
 * We do this in barebones by mirroring existing WordPress hookss in many places
 * allowing dependant plugins to hook into the barebones specific ones, thus
 * guaranteeing proper code execution only when barebones is active.
 *
 * The following functions are wrappers for hookss, allowing them to be
 * manually called and/or piggy-backed on top of other hooks if needed.
 *
 * @todo use anonymous functions when PHP minimun requirement allows (5.3)
 */

/** Activation Actions ********************************************************/

/**
 * Runs on barebones activation
 *
 * @since Showcase (1.0)
 * @uses register_uninstall_hook() To register our own uninstall hook
 * @uses do_action() Calls 'dps_activation' hook
 */
function dps_activation() {
	do_action( 'dps_activation' );
}

/**
 * Runs on barebones deactivation
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_deactivation' hook
 */
function dps_deactivation() {
	do_action( 'dps_deactivation' );
}

/**
 * Runs when uninstalling barebones
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_uninstall' hook
 */
function dps_uninstall() {
	do_action( 'dps_uninstall' );
}

/** Main Actions **************************************************************/

/**
 * Main action responsible for constants, globals, and includes
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_loaded'
 */
function dps_loaded() {
	do_action( 'dps_loaded' );
}

/**
 * Setup constants
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_constants'
 */
function dps_constants() {
	do_action( 'dps_constants' );
}

/**
 * Setup globals BEFORE includes
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_boot_strap_globals'
 */
function dps_boot_strap_globals() {
	do_action( 'dps_boot_strap_globals' );
}

/**
 * Include files
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_includes'
 */
function dps_includes() {
	do_action( 'dps_includes' );
}

/**
 * Setup globals AFTER includes
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_setup_globals'
 */
function dps_setup_globals() {
	do_action( 'dps_setup_globals' );
}

/**
 * Register any objects before anything is initialized
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_register'
 */
function dps_register() {
	do_action( 'dps_register' );
}

/**
 * Initialize any code after everything has been loaded
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_init'
 */
function dps_init() {
	do_action( 'dps_init' );
}

/**
 * Initialize widgets
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_widgets_init'
 */
function dps_widgets_init() {
	do_action( 'dps_widgets_init' );
}

/**
 * Setup the currently logged-in user
 *
 * @since Showcase (1.0)
 * @uses did_action() To make sure the user isn't loaded out of order
 * @uses do_action() Calls 'dps_setup_current_user'
 */
function dps_setup_current_user() {

	// If the current user is being setup before the "init" action has fired,
	// strange (and difficult to debug) role/capability issues will occur.
	if ( ! did_action( 'after_setup_theme' ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'The current user is being initialized without using $wp->init().', 'barebones' ), '2.3' );
	}

	do_action( 'dps_setup_current_user' );
}

/** Supplemental Actions ******************************************************/

/**
 * Load translations for current language
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_load_textdomain'
 */
function dps_load_textdomain() {
	do_action( 'dps_load_textdomain' );
}

/**
 * Setup the post types
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_register_post_type'
 */
function dps_register_post_types() {
	do_action( 'dps_register_post_types' );
}

/**
 * Setup the post statuses
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_register_post_statuses'
 */
function dps_register_post_statuses() {
	do_action( 'dps_register_post_statuses' );
}

/**
 * Register the built in barebones taxonomies
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_register_taxonomies'
 */
function dps_register_taxonomies() {
	do_action( 'dps_register_taxonomies' );
}

/**
 * Register the default barebones views
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_register_views'
 */
function dps_register_views() {
	do_action( 'dps_register_views' );
}

/**
 * Register the default barebones shortcodes
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_register_shortcodes'
 */
function dps_register_shortcodes() {
	do_action( 'dps_register_shortcodes' );
}

/**
 * Enqueue barebones specific CSS and JS
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_enqueue_scripts'
 */
function dps_enqueue_scripts() {
	do_action( 'dps_enqueue_scripts' );
}

/**
 * Add the barebones-specific rewrite tags
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_add_rewrite_tags'
 */
function dps_add_rewrite_tags() {
	do_action( 'dps_add_rewrite_tags' );
}

/**
 * Add the barebones-specific login forum action
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_login_form_login'
 */
function dps_login_form_login() {
	do_action( 'dps_login_form_login' );
}

/** User Actions **************************************************************/

/**
 * The main action for hooking into when a user account is updated
 *
 * @since Showcase (1.0)
 *
 * @param int $user_id ID of user being edited
 * @param array $old_user_data The old, unmodified user data
 * @uses do_action() Calls 'dps_profile_update'
 */
function dps_profile_update( $user_id = 0, $old_user_data = array() ) {
	do_action( 'dps_profile_update', $user_id, $old_user_data );
}

/**
 * The main action for hooking into a user being registered
 *
 * @since Showcase (1.0)
 * @param int $user_id ID of user being edited
 * @uses do_action() Calls 'dps_user_register'
 */
function dps_user_register( $user_id = 0 ) {
	do_action( 'dps_user_register', $user_id );
}

/** Final Action **************************************************************/

/**
 * Showcase has loaded and initialized everything, and is okay to go
 *
 * @since Showcase (1.0)
 * @uses do_action() Calls 'dps_ready'
 */
function dps_ready() {
	do_action( 'dps_ready' );
}

/** Theme Permissions *********************************************************/

/**
 * The main action used for redirecting barebones theme actions that are not
 * permitted by the current_user
 *
 * @since Showcase (1.0)
 * @uses do_action()
 */
function dps_template_redirect() {
	do_action( 'dps_template_redirect' );
}

/** Theme Helpers *************************************************************/

/**
 * The main action used for executing code before the theme has been setup
 *
 * @since Showcase (1.0)
 * @uses do_action()
 */
function dps_register_theme_packages() {
	do_action( 'dps_register_theme_packages' );
}

/**
 * The main action used for executing code before the theme has been setup
 *
 * @since Showcase (1.0)
 * @uses do_action()
 */
function dps_setup_theme() {
	do_action( 'dps_setup_theme' );
}

/**
 * The main action used for executing code after the theme has been setup
 *
 * @since Showcase (1.0)
 * @uses do_action()
 */
function dps_after_setup_theme() {
	do_action( 'dps_after_setup_theme' );
}

/**
 * The main action used for handling theme-side POST requests
 *
 * @since Showcase (1.0)
 * @uses do_action()
 */
function dps_post_request() {

	// Bail if not a POST action
	if ( ! dps_is_post_request() )
		return;

	// Bail if no action
	if ( empty( $_POST['action'] ) )
		return;

	do_action( 'dps_post_request', $_POST['action'] );
}

/**
 * The main action used for handling theme-side GET requests
 *
 * @since Showcase (1.0)
 * @uses do_action()
 */
function dps_get_request() {

	// Bail if not a POST action
	if ( ! dps_is_get_request() )
		return;

	// Bail if no action
	if ( empty( $_GET['action'] ) )
		return;

	do_action( 'dps_get_request', $_GET['action'] );
}

/** Filters *******************************************************************/

/**
 * Filter the plugin locale and domain.
 *
 * @since Showcase (1.0)
 *
 * @param string $locale
 * @param string $domain
 */
function dps_plugin_locale( $locale = '', $domain = '' ) {
	return apply_filters( 'dps_plugin_locale', $locale, $domain );
}

/**
 * Piggy back filter for WordPress's 'request' filter
 *
 * @since Showcase (1.0)
 * @param array $query_vars
 * @return array
 */
function dps_request( $query_vars = array() ) {
	return apply_filters( 'dps_request', $query_vars );
}

/**
 * The main filter used for theme compatibility and displaying custom barebones
 * theme files.
 *
 * @since Showcase (1.0)
 * @uses apply_filters()
 * @param string $template
 * @return string Template file to use
 */
function dps_template_include( $template = '' ) {
	return apply_filters( 'dps_template_include', $template );
}

/**
 * Generate barebones-specific rewrite rules
 *
 * @since Showcase (1.0)
 * @param WP_Rewrite $wp_rewrite
 * @uses do_action() Calls 'dps_generate_rewrite_rules' with {@link WP_Rewrite}
 */
function dps_generate_rewrite_rules( $wp_rewrite ) {
	do_action_ref_array( 'dps_generate_rewrite_rules', array( &$wp_rewrite ) );
}

/**
 * Filter the allowed themes list for barebones specific themes
 *
 * @since Showcase (1.0)
 * @uses apply_filters() Calls 'dps_allowed_themes' with the allowed themes list
 */
function dps_allowed_themes( $themes ) {
	return apply_filters( 'dps_allowed_themes', $themes );
}

/**
 * Maps forum/topic/reply caps to built in WordPress caps
 *
 * @since Showcase (1.0)
 *
 * @param array $caps Capabilities for meta capability
 * @param string $cap Capability name
 * @param int $user_id User id
 * @param mixed $args Arguments
 */
function dps_map_meta_caps( $caps = array(), $cap = '', $user_id = 0, $args = array() ) {
	return apply_filters( 'dps_map_meta_caps', $caps, $cap, $user_id, $args );
}
