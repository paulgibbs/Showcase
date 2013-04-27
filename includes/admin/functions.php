<?php

/**
 * Showcase Admin Functions
 *
 * @package Showcase
 * @subpackage Administration
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Admin Menus ***************************************************************/

/**
 * Add a separator to the WordPress admin menus
 *
 * @since Showcase (1.0)
 */
function dps_admin_separator() {

	// Caps necessary where a separator is necessary
	$caps = array(
		'dps_forums_admin',
		'dps_topics_admin',
		'dps_replies_admin',
	);

	// Loop through caps, and look for a reason to show the separator
	foreach ( $caps as $cap ) {
		if ( current_user_can( $cap ) ) {
			showcase()->admin->show_separator = true;
			break;
		}
	}

	// Bail if no separator
	if ( false === showcase()->admin->show_separator ) {
		return;
	}

	global $menu;

	$menu[] = array( '', 'read', 'separator-showcase', '', 'wp-menu-separator showcase' );
}

/**
 * Uninstall all showcase options and capabilities from a specific site.
 *
 * @since Showcase (1.0)
 * @param type $site_id
 */
function dps_do_uninstall( $site_id = 0 ) {
	if ( empty( $site_id ) )
		$site_id = get_current_blog_id();

	switch_to_blog( $site_id );
	dps_delete_options();
	dps_remove_caps();
	flush_rewrite_rules();
	restore_current_blog();
}

/**
 * Redirect user to showcase's What's New page on activation
 *
 * @since Showcase (1.0)
 *
 * @internal Used internally to redirect showcase to the about page on activation
 *
 * @uses get_transient() To see if transient to redirect exists
 * @uses delete_transient() To delete the transient if it exists
 * @uses is_network_admin() To bail if being network activated
 * @uses wp_safe_redirect() To redirect
 * @uses add_query_arg() To help build the URL to redirect to
 * @uses admin_url() To get the admin URL to index.php
 *
 * @return If no transient, or in network admin, or is bulk activation
 */
function dps_do_activation_redirect() {

	// Bail if no activation redirect
    if ( ! get_transient( '_dps_activation_redirect' ) )
		return;

	// Delete the redirect transient
	delete_transient( '_dps_activation_redirect' );

	// Bail if activating from network, or bulk
	if ( is_network_admin() || isset( $_GET['activate-multi'] ) )
		return;

	// Redirect to showcase about page
	wp_safe_redirect( add_query_arg( array( 'page' => 'bbp-about' ), admin_url( 'index.php' ) ) );
}

/**
 * This tells WP to highlight the Tools > Forums menu item,
 * regardless of which actual showcase Tools screen we are on.
 *
 * The conditional prevents the override when the user is viewing settings or
 * any third-party plugins.
 *
 * @since Showcase (1.0)
 * @global string $plugin_page
 * @global array $submenu_file
 */
function dps_tools_modify_menu_highlight() {
	global $plugin_page, $submenu_file;

	// This tweaks the Tools subnav menu to only show one showcase menu item
	if ( ! in_array( $plugin_page, array( 'bbp-settings' ) ) )
		$submenu_file = 'bbp-repair';
}

/**
 * Output the tabs in the admin area
 *
 * @since Showcase (1.0)
 * @param string $active_tab Name of the tab that is active
 */
function dps_tools_admin_tabs( $active_tab = '' ) {
	echo dps_get_tools_admin_tabs( $active_tab );
}

	/**
	 * Output the tabs in the admin area
	 *
	 * @since Showcase (1.0)
	 * @param string $active_tab Name of the tab that is active
	 */
	function dps_get_tools_admin_tabs( $active_tab = '' ) {

		// Declare local variables
		$tabs_html    = '';
		$idle_class   = 'nav-tab';
		$active_class = 'nav-tab nav-tab-active';

		// Setup core admin tabs
		$tabs = apply_filters( 'dps_tools_admin_tabs', array(
			'0' => array(
				'href' => get_admin_url( '', add_query_arg( array( 'page' => 'bbp-repair'    ), 'tools.php' ) ),
				'name' => __( 'Repair Forums', 'dps' )
			),
			'1' => array(
				'href' => get_admin_url( '', add_query_arg( array( 'page' => 'bbp-converter' ), 'tools.php' ) ),
				'name' => __( 'Import Forums', 'dps' )
			),
			'2' => array(
				'href' => get_admin_url( '', add_query_arg( array( 'page' => 'bbp-reset'     ), 'tools.php' ) ),
				'name' => __( 'Reset Forums', 'dps' )
			)
		) );

		// Loop through tabs and build navigation
		foreach( $tabs as $tab_id => $tab_data ) {
			$is_current = (bool) ( $tab_data['name'] == $active_tab );
			$tab_class  = $is_current ? $active_class : $idle_class;
			$tabs_html .= '<a href="' . $tab_data['href'] . '" class="' . $tab_class . '">' . $tab_data['name'] . '</a>';
		}

		// Output the tabs
		return $tabs_html;
	}
