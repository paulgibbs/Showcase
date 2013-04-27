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
 * These option
 *
 * @since Showcase (1.0)
 * @return array Filtered option names and values
 */
function dps_get_default_options() {

	// Default options
	return apply_filters( 'dps_get_default_options', array(

		/** DB Version ********************************************************/

		'_dps_db_version'           => showcase()->db_version,

		/** Settings **********************************************************/

		'_dps_edit_lock'            => 5,                          // Lock post editing after 5 minutes
		'_dps_throttle_time'        => 10,                         // Throttle post time to 10 seconds
		'_dps_enable_favorites'     => 1,                          // Favorites
		'_dps_enable_subscriptions' => 1,                          // Subscriptions
		'_dps_allow_topic_tags'     => 1,                          // Topic Tags
		'_dps_allow_anonymous'      => 0,                          // Allow anonymous posting
		'_dps_allow_global_access'  => 1,                          // Users from all sites can post
		'_dps_use_wp_editor'        => 1,                          // Use the WordPress editor if available
		'_dps_use_autoembed'        => 0,                          // Allow oEmbed in topics and replies
		'_dps_theme_package_id'     => 'default',                  // The ID for the current theme package.
		'_dps_default_role'         => dps_get_participant_role(), // Default forums role

		/** Per Page **********************************************************/

		'_dps_topics_per_page'      => 15,          // Topics per page
		'_dps_replies_per_page'     => 15,          // Replies per page
		'_dps_forums_per_page'      => 50,          // Forums per page
		'_dps_topics_per_rss_page'  => 25,          // Topics per RSS page
		'_dps_replies_per_rss_page' => 25,          // Replies per RSS page

		/** Page For **********************************************************/

		'_dps_page_for_forums'      => 0,           // Page for forums
		'_dps_page_for_topics'      => 0,           // Page for forums
		'_dps_page_for_login'       => 0,           // Page for login
		'_dps_page_for_register'    => 0,           // Page for register
		'_dps_page_for_lost_pass'   => 0,           // Page for lost-pass

		/** Archive Slugs *****************************************************/

		'_dps_root_slug'            => 'forums',    // Forum archive slug
		'_dps_topic_archive_slug'   => 'topics',    // Topic archive slug

		/** Single Slugs ******************************************************/

		'_dps_include_root'         => 1,           // Include forum-archive before single slugs
		'_dps_forum_slug'           => 'forum',     // Forum slug
		'_dps_topic_slug'           => 'topic',     // Topic slug
		'_dps_reply_slug'           => 'reply',     // Reply slug
		'_dps_topic_tag_slug'       => 'topic-tag', // Topic tag slug

		/** User Slugs ********************************************************/

		'_dps_user_slug'            => 'users',         // User profile slug
		'_dps_user_favs_slug'       => 'favorites',     // User favorites slug
		'_dps_user_subs_slug'       => 'subscriptions', // User subscriptions slug

		/** Other Slugs *******************************************************/

		'_dps_view_slug'            => 'view',      // View slug
		'_dps_search_slug'          => 'search',    // Search slug

		/** Topics ************************************************************/

		'_dps_title_max_length'     => 80,          // Title Max Length
		'_dps_super_sticky_topics'  => '',          // Super stickies

		/** Forums ************************************************************/

		'_dps_private_forums'       => '',          // Private forums
		'_dps_hidden_forums'        => '',          // Hidden forums

		/** BuddyPress ********************************************************/

		'_dps_enable_group_forums'  => 1,           // Enable BuddyPress Group Extension
		'_dps_group_forums_root_id' => 0,           // Group Forums parent forum id

		/** Akismet ***********************************************************/

		'_dps_enable_akismet'       => 1            // Users from all sites can post

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

/** Active? *******************************************************************/

/**
 * Checks if favorites feature is enabled.
 *
 * @since Showcase (1.0)
 * @param $default bool Optional.Default value true
 * @uses get_option() To get the favorites option
 * @return bool Is favorites enabled or not
 */
function dps_is_favorites_active( $default = 1 ) {
	return (bool) apply_filters( 'dps_is_favorites_active', (bool) get_option( '_dps_enable_favorites', $default ) );
}

/**
 * Checks if subscription feature is enabled.
 *
 * @since Showcase (1.0)
 * @param $default bool Optional.Default value true
 * @uses get_option() To get the subscriptions option
 * @return bool Is subscription enabled or not
 */
function dps_is_subscriptions_active( $default = 1 ) {
	return (bool) apply_filters( 'dps_is_subscriptions_active', (bool) get_option( '_dps_enable_subscriptions', $default ) );
}

/**
 * Are topic tags allowed
 *
 * @since Showcase (1.0)
 * @param $default bool Optional. Default value true
 * @uses get_option() To get the allow tags
 * @return bool Are tags allowed?
 */
function dps_allow_topic_tags( $default = 1 ) {
	return (bool) apply_filters( 'dps_allow_topic_tags', (bool) get_option( '_dps_allow_topic_tags', $default ) );
}

/**
 * Are topic and reply revisions allowed
 *
 * @since Showcase (1.0)
 * @param $default bool Optional. Default value true
 * @uses get_option() To get the allow revisions
 * @return bool Are revisions allowed?
 */
function dps_allow_revisions( $default = 1 ) {
	return (bool) apply_filters( 'dps_allow_revisions', (bool) get_option( '_dps_allow_revisions', $default ) );
}

/**
 * Is the anonymous posting allowed?
 *
 * @since Showcase (1.0)
 * @param $default bool Optional. Default value
 * @uses get_option() To get the allow anonymous option
 * @return bool Is anonymous posting allowed?
 */
function dps_allow_anonymous( $default = 0 ) {
	return apply_filters( 'dps_allow_anonymous', (bool) get_option( '_dps_allow_anonymous', $default ) );
}

/**
 * Is this forum available to all users on all sites in this installation?
 *
 * @since Showcase (1.0)
 * @param $default bool Optional. Default value false
 * @uses get_option() To get the global access option
 * @return bool Is global access allowed?
 */
function dps_allow_global_access( $default = 1 ) {
	return (bool) apply_filters( 'dps_allow_global_access', (bool) get_option( '_dps_allow_global_access', $default ) );
}

/**
 * Is this forum available to all users on all sites in this installation?
 *
 * @since Showcase (1.0)
 * @param $default string Optional. Default value empty
 * @uses get_option() To get the default forums role option
 * @return string The default forums user role
 */
function dps_get_default_role( $default = 'dps_participant' ) {
	return apply_filters( 'dps_get_default_role', get_option( '_dps_default_role', $default ) );
}

/**
 * Use the WordPress editor if available
 *
 * @since Showcase (1.0)
 * @param $default bool Optional. Default value true
 * @uses get_option() To get the WP editor option
 * @return bool Use WP editor?
 */
function dps_use_wp_editor( $default = 1 ) {
	return (bool) apply_filters( 'dps_use_wp_editor', (bool) get_option( '_dps_use_wp_editor', $default ) );
}

/**
 * Use WordPress's oEmbed API
 *
 * @since Showcase (1.0)
 * @param $default bool Optional. Default value true
 * @uses get_option() To get the oEmbed option
 * @return bool Use oEmbed?
 */
function dps_use_autoembed( $default = 1 ) {
	return (bool) apply_filters( 'dps_use_autoembed', (bool) get_option( '_dps_use_autoembed', $default ) );
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

/**
 * Output the maximum length of a title
 *
 * @since Showcase (1.0)
 * @param $default bool Optional. Default value 80
 */
function dps_title_max_length( $default = 80 ) {
	echo dps_get_title_max_length( $default );
}
	/**
	 * Return the maximum length of a title
	 *
	 * @since Showcase (1.0)
	 * @param $default bool Optional. Default value 80
	 * @uses get_option() To get the maximum title length
	 * @return int Is anonymous posting allowed?
	 */
	function dps_get_title_max_length( $default = 80 ) {
		return (int) apply_filters( 'dps_get_title_max_length', (int) get_option( '_dps_title_max_length', $default ) );
	}

/**
 * Output the grop forums root parent forum id
 *
 * @since Showcase (1.0)
 * @param $default int Optional. Default value
 */
function dps_group_forums_root_id( $default = 0 ) {
	echo dps_get_group_forums_root_id( $default );
}
	/**
	 * Return the grop forums root parent forum id
	 *
	 * @since Showcase (1.0)
	 * @param $default bool Optional. Default value 0
	 * @uses get_option() To get the root group forum ID
	 * @return int The post ID for the root forum
	 */
	function dps_get_group_forums_root_id( $default = 0 ) {
		return (int) apply_filters( 'dps_get_group_forums_root_id', (int) get_option( '_dps_group_forums_root_id', $default ) );
	}

/**
 * Checks if BuddyPress Group Forums are enabled
 *
 * @since Showcase (1.0)
 * @param $default bool Optional. Default value true
 * @uses get_option() To get the group forums option
 * @return bool Is group forums enabled or not
 */
function dps_is_group_forums_active( $default = 1 ) {
	return (bool) apply_filters( 'dps_is_group_forums_active', (bool) get_option( '_dps_enable_group_forums', $default ) );
}

/**
 * Checks if Akismet is enabled
 *
 * @since Showcase (1.0)
 * @param $default bool Optional. Default value true
 * @uses get_option() To get the Akismet option
 * @return bool Is Akismet enabled or not
 */
function dps_is_akismet_active( $default = 1 ) {
	return (bool) apply_filters( 'dps_is_akismet_active', (bool) get_option( '_dps_enable_akismet', $default ) );
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

/**
 * Are we including the root slug in front of forum pages?
 *
 * @since Showcase (1.0)
 * @return bool
 */
function dps_include_root_slug( $default = 1 ) {
	return (bool) apply_filters( 'dps_include_root_slug', (bool) get_option( '_dps_include_root', $default ) );
}

/**
 * Maybe return the root slug, based on whether or not it's included in the url
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_maybe_get_root_slug() {
	$retval = '';

	if ( dps_get_root_slug() && dps_include_root_slug() )
		$retval = trailingslashit( dps_get_root_slug() );

	return apply_filters( 'dps_maybe_get_root_slug', $retval );
}

/**
 * Return the single forum slug
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_showcase_slug( $default = 'forum' ) {;
	return apply_filters( 'dps_get_root_slug', dps_maybe_get_root_slug() . get_option( '_dps_forum_slug', $default ) );
}

/**
 * Return the topic archive slug
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_topic_archive_slug( $default = 'topics' ) {
	return apply_filters( 'dps_get_topic_archive_slug', get_option( '_dps_topic_archive_slug', $default ) );
}

/**
 * Return the single topic slug
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_topic_slug( $default = 'topic' ) {
	return apply_filters( 'dps_get_topic_slug', dps_maybe_get_root_slug() . get_option( '_dps_topic_slug', $default ) );
}

/**
 * Return the topic-tag taxonomy slug
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_topic_tag_tax_slug( $default = 'topic-tag' ) {
	return apply_filters( 'dps_get_topic_tag_tax_slug', dps_maybe_get_root_slug() . get_option( '_dps_topic_tag_slug', $default ) );
}

/**
 * Return the single reply slug (used mostly for editing)
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_reply_slug( $default = 'reply' ) {
	return apply_filters( 'dps_get_reply_slug', dps_maybe_get_root_slug() . get_option( '_dps_reply_slug', $default ) );
}

/**
 * Return the single user slug
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_user_slug( $default = 'user' ) {
	return apply_filters( 'dps_get_user_slug', dps_maybe_get_root_slug() . get_option( '_dps_user_slug', $default ) );
}

/**
 * Return the single user favorites slug
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_user_favorites_slug( $default = 'favorites' ) {
	return apply_filters( 'dps_get_user_favorites_slug', get_option( '_dps_user_favs_slug', $default ) );
}

/**
 * Return the single user subscriptions slug
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_user_subscriptions_slug( $default = 'subscriptions' ) {
	return apply_filters( 'dps_get_user_subscriptions_slug', get_option( '_dps_user_subs_slug', $default ) );
}

/**
 * Return the topic view slug
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_view_slug( $default = 'view' ) {
	return apply_filters( 'dps_get_view_slug', dps_maybe_get_root_slug() . get_option( '_dps_view_slug', $default ) );
}

/**
 * Return the search slug
 *
 * @since Showcase (1.0)
 *
 * @return string
 */
function dps_get_search_slug( $default = 'search' ) {
	return apply_filters( 'dps_get_search_slug', dps_maybe_get_root_slug() . get_option( '_dps_search_slug', $default ) );
}

/** Legacy ********************************************************************/

/**
 * Checks if there is a previous BuddyPress Forum configuration
 *
 * @since Showcase (1.0)
 * @param $default string Optional. Default empty string
 * @uses get_option() To get the old dps-config.php location
 * @return string The location of the dps-config.php file, if any
 */
function dps_get_config_location( $default = '' ) {
	return apply_filters( 'dps_get_config_location', get_option( 'dps-config-location', $default ) );
}
