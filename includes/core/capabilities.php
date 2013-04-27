<?php

/**
 * Showcase Capabilites
 *
 * The functions in this file are used primarily as convenient wrappers for
 * capability output in user profiles. This includes mapping capabilities and
 * groups to human readable strings,
 *
 * @package Showcase
 * @subpackage Capabilities
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Mapping *******************************************************************/

/**
 * Returns an array of capabilities based on the role that is being requested.
 *
 * @since Showcase (1.0)
 *
 * @todo Map all of these and deprecate
 *
 * @param string $role Optional. Defaults to The role to load caps for
 * @uses apply_filters() Allow return value to be filtered
 *
 * @return array Capabilities for $role
 */
function dps_get_caps_for_role( $role = '' ) {

	// Which role are we looking for?
	switch ( $role ) {

		// Keymaster
		case dps_get_keymaster_role() :
			$caps = array(

				// Keymasters only
				'keep_gate'             => true,

				// Primary caps
				'spectate'              => true,
				'participate'           => true,
				'moderate'              => true,
				'throttle'              => true,
				'view_trash'            => true,

				// Forum caps
				'publish_forums'        => true,
				'edit_forums'           => true,
				'edit_others_forums'    => true,
				'delete_forums'         => true,
				'delete_others_forums'  => true,
				'read_private_forums'   => true,
				'read_hidden_forums'    => true,

				// Topic caps
				'publish_topics'        => true,
				'edit_topics'           => true,
				'edit_others_topics'    => true,
				'delete_topics'         => true,
				'delete_others_topics'  => true,
				'read_private_topics'   => true,

				// Reply caps
				'publish_replies'       => true,
				'edit_replies'          => true,
				'edit_others_replies'   => true,
				'delete_replies'        => true,
				'delete_others_replies' => true,
				'read_private_replies'  => true,

				// Topic tag caps
				'manage_topic_tags'     => true,
				'edit_topic_tags'       => true,
				'delete_topic_tags'     => true,
				'assign_topic_tags'     => true
			);

			break;

		// Moderator
		case dps_get_moderator_role() :
			$caps = array(

				// Primary caps
				'spectate'              => true,
				'participate'           => true,
				'moderate'              => true,
				'throttle'              => true,
				'view_trash'            => true,

				// Forum caps
				'publish_forums'        => true,
				'edit_forums'           => true,
				'edit_others_forums'    => false,
				'delete_forums'         => false,
				'delete_others_forums'  => false,
				'read_private_forums'   => true,
				'read_hidden_forums'    => true,

				// Topic caps
				'publish_topics'        => true,
				'edit_topics'           => true,
				'edit_others_topics'    => true,
				'delete_topics'         => true,
				'delete_others_topics'  => true,
				'read_private_topics'   => true,

				// Reply caps
				'publish_replies'       => true,
				'edit_replies'          => true,
				'edit_others_replies'   => true,
				'delete_replies'        => true,
				'delete_others_replies' => true,
				'read_private_replies'  => true,

				// Topic tag caps
				'manage_topic_tags'     => true,
				'edit_topic_tags'       => true,
				'delete_topic_tags'     => true,
				'assign_topic_tags'     => true,
			);

			break;

		// Spectators can only read
		case dps_get_spectator_role()   :
			$caps = array(

				// Primary caps
				'spectate'              => true,
				'participate'           => false,
				'moderate'              => false,
				'throttle'              => false,
				'view_trash'            => false,

				// Forum caps
				'publish_forums'        => false,
				'edit_forums'           => false,
				'edit_others_forums'    => false,
				'delete_forums'         => false,
				'delete_others_forums'  => false,
				'read_private_forums'   => false,
				'read_hidden_forums'    => false,

				// Topic caps
				'publish_topics'        => false,
				'edit_topics'           => false,
				'edit_others_topics'    => false,
				'delete_topics'         => false,
				'delete_others_topics'  => false,
				'read_private_topics'   => false,

				// Reply caps
				'publish_replies'       => false,
				'edit_replies'          => false,
				'edit_others_replies'   => false,
				'delete_replies'        => false,
				'delete_others_replies' => false,
				'read_private_replies'  => false,

				// Topic tag caps
				'manage_topic_tags'     => false,
				'edit_topic_tags'       => false,
				'delete_topic_tags'     => false,
				'assign_topic_tags'     => false,
			);

			break;

		// Explicitly blocked
		case dps_get_blocked_role() :
			$caps = array(

				// Primary caps
				'spectate'              => false,
				'participate'           => false,
				'moderate'              => false,
				'throttle'              => false,
				'view_trash'            => false,

				// Forum caps
				'publish_forums'        => false,
				'edit_forums'           => false,
				'edit_others_forums'    => false,
				'delete_forums'         => false,
				'delete_others_forums'  => false,
				'read_private_forums'   => false,
				'read_hidden_forums'    => false,

				// Topic caps
				'publish_topics'        => false,
				'edit_topics'           => false,
				'edit_others_topics'    => false,
				'delete_topics'         => false,
				'delete_others_topics'  => false,
				'read_private_topics'   => false,

				// Reply caps
				'publish_replies'       => false,
				'edit_replies'          => false,
				'edit_others_replies'   => false,
				'delete_replies'        => false,
				'delete_others_replies' => false,
				'read_private_replies'  => false,

				// Topic tag caps
				'manage_topic_tags'     => false,
				'edit_topic_tags'       => false,
				'delete_topic_tags'     => false,
				'assign_topic_tags'     => false,
			);

			break;

		// Participant/Default
		case dps_get_participant_role() :
		default :
			$caps = array(

				// Primary caps
				'spectate'              => true,
				'participate'           => true,
				'moderate'              => false,
				'throttle'              => false,
				'view_trash'            => false,

				// Forum caps
				'publish_forums'        => false,
				'edit_forums'           => false,
				'edit_others_forums'    => false,
				'delete_forums'         => false,
				'delete_others_forums'  => false,
				'read_private_forums'   => true,
				'read_hidden_forums'    => false,

				// Topic caps
				'publish_topics'        => true,
				'edit_topics'           => true,
				'edit_others_topics'    => false,
				'delete_topics'         => false,
				'delete_others_topics'  => false,
				'read_private_topics'   => false,

				// Reply caps
				'publish_replies'       => true,
				'edit_replies'          => true,
				'edit_others_replies'   => false,
				'delete_replies'        => false,
				'delete_others_replies' => false,
				'read_private_replies'  => false,

				// Topic tag caps
				'manage_topic_tags'     => false,
				'edit_topic_tags'       => false,
				'delete_topic_tags'     => false,
				'assign_topic_tags'     => true,
			);

			break;
	}

	return apply_filters( 'dps_get_caps_for_role', $caps, $role );
}

/**
 * Adds capabilities to WordPress user roles.
 *
 * @since Showcase (1.0)
 */
function dps_add_caps() {

	// Loop through available roles and add caps
	foreach( dps_get_wp_roles()->role_objects as $role ) {
		foreach ( dps_get_caps_for_role( $role->name ) as $cap => $value ) {
			$role->add_cap( $cap, $value );
		}
	}

	do_action( 'dps_add_caps' );
}

/**
 * Removes capabilities from WordPress user roles.
 *
 * @since Showcase (1.0)
 */
function dps_remove_caps() {

	// Loop through available roles and remove caps
	foreach( dps_get_wp_roles()->role_objects as $role ) {
		foreach ( array_keys( dps_get_caps_for_role( $role->name ) ) as $cap ) {
			$role->remove_cap( $cap );
		}
	}

	do_action( 'dps_remove_caps' );
}

/**
 * Get the $wp_roles global without needing to declare it everywhere
 *
 * @since Showcase (1.0)
 *
 * @global WP_Roles $wp_roles
 * @return WP_Roles
 */
function dps_get_wp_roles() {
	global $wp_roles;

	// Load roles if not set
	if ( ! isset( $wp_roles ) )
		$wp_roles = new WP_Roles();

	return $wp_roles;
}

/** Forum Roles ***************************************************************/

/**
 * Add the barebones roles to the $wp_roles global.
 *
 * We do this to avoid adding these values to the database.
 *
 * @since Showcase (1.0)
 * @return WP_Roles The main $wp_roles global
 */
function dps_add_forums_roles() {
	$wp_roles = dps_get_wp_roles();

	foreach( dps_get_dynamic_roles() as $role_id => $details ) {
		$wp_roles->roles[$role_id]        = $details;
		$wp_roles->role_objects[$role_id] = new WP_Role( $role_id, $details['capabilities'] );
		$wp_roles->role_names[$role_id]   = $details['name'];
	}

	return $wp_roles;
}

/**
 * Helper function to add filter to option_wp_user_roles
 *
 * @since Showcase (1.0)
 *
 * @see _dps_reinit_dynamic_roles()
 *
 * @global WPDB $wpdb Used to get the database prefix
 */
function dps_filter_user_roles_option() {
	global $wpdb;

	$role_key = $wpdb->prefix . 'user_roles';

	add_filter( 'option_' . $role_key, '_dps_reinit_dynamic_roles' );
}

/**
 * This is necessary because in a few places (noted below) WordPress initializes
 * a blog's roles directly from the database option. When this happens, the
 * $wp_roles global gets flushed, causing a user to magically lose any
 * dynamically assigned roles or capabilities when $current_user in refreshed.
 *
 * Because dynamic multiple roles is a new concept in WordPress, we work around
 * it here for now, knowing that improvements will come to WordPress core later.
 *
 * Also note that if using the $wp_user_roles global non-database approach,
 * Showcase does not have an intercept point to add its dynamic roles.
 *
 * @see switch_to_blog()
 * @see restore_current_blog()
 * @see WP_Roles::_init()
 *
 * @since Showcase (1.0)
 *
 * @internal Used by barebones to reinitialize dynamic roles on blog switch
 *
 * @param array $roles
 * @return array Combined array of database roles and dynamic barebones roles
 */
function _dps_reinit_dynamic_roles( $roles = array() ) {
	foreach( dps_get_dynamic_roles() as $role_id => $details ) {
		$roles[$role_id] = $details;
	}
	return $roles;
}

/**
 * Fetch a filtered list of forum roles that the current user is
 * allowed to have.
 *
 * Simple function who's main purpose is to allow filtering of the
 * list of forum roles so that plugins can remove inappropriate ones depending
 * on the situation or user making edits.
 *
 * Specifically because without filtering, anyone with the edit_users
 * capability can edit others to be administrators, even if they are
 * only editors or authors. This filter allows admins to delegate
 * user management.
 *
 * @since Showcase (1.0)
 *
 * @return array
 */
function dps_get_dynamic_roles() {
	return (array) apply_filters( 'dps_get_dynamic_roles', array(

		// Keymaster
		dps_get_keymaster_role() => array(
			'name'         => __( 'Keymaster', 'barebones' ),
			'capabilities' => dps_get_caps_for_role( dps_get_keymaster_role() )
		),

		// Moderator
		dps_get_moderator_role() => array(
			'name'         => __( 'Moderator', 'barebones' ),
			'capabilities' => dps_get_caps_for_role( dps_get_moderator_role() )
		),

		// Participant
		dps_get_participant_role() => array(
			'name'         => __( 'Participant', 'barebones' ),
			'capabilities' => dps_get_caps_for_role( dps_get_participant_role() )
		),

		// Spectator
		dps_get_spectator_role() => array(
			'name'         => __( 'Spectator', 'barebones' ),
			'capabilities' => dps_get_caps_for_role( dps_get_spectator_role() )
		),

		// Blocked
		dps_get_blocked_role() => array(
			'name'         => __( 'Blocked', 'barebones' ),
			'capabilities' => dps_get_caps_for_role( dps_get_blocked_role() )
		)
	) );
}

/**
 * Gets a translated role name from a role ID
 *
 * @since Showcase (1.0)
 *
 * @param string $role_id
 * @return string Translated role name
 */
function dps_get_dynamic_role_name( $role_id = '' ) {
	$roles = dps_get_dynamic_roles();
	$role  = isset( $roles[$role_id] ) ? $roles[$role_id]['name'] : '';

	return apply_filters( 'dps_get_dynamic_role_name', $role, $role_id, $roles );
}

/**
 * Removes the barebones roles from the editable roles array
 *
 * This used to use array_diff_assoc() but it randomly broke before 2.2 release.
 * Need to research what happened, and if there's a way to speed this up.
 *
 * @since Showcase (1.0)
 *
 * @param array $all_roles All registered roles
 * @return array 
 */
function dps_filter_blog_editable_roles( $all_roles = array() ) {

	// Loop through barebones roles
	foreach ( array_keys( dps_get_dynamic_roles() ) as $dps_role ) {

		// Loop through WordPress roles
		foreach ( array_keys( $all_roles ) as $wp_role ) {

			// If keys match, unset
			if ( $wp_role == $dps_role ) {
				unset( $all_roles[$wp_role] );
			}
		}
	}

	return $all_roles;
}

/**
 * The keymaster role for barebones users
 *
 * @since Showcase (1.0)
 *
 * @uses apply_filters() Allow override of hardcoded keymaster role
 * @return string
 */
function dps_get_keymaster_role() {
	return apply_filters( 'dps_get_keymaster_role', 'dps_keymaster' );
}

/**
 * The moderator role for barebones users
 *
 * @since Showcase (1.0)
 *
 * @uses apply_filters() Allow override of hardcoded moderator role
 * @return string
 */
function dps_get_moderator_role() {
	return apply_filters( 'dps_get_moderator_role', 'dps_moderator' );
}

/**
 * The participant role for registered user that can participate in forums
 *
 * @since Showcase (1.0)
 *
 * @uses apply_filters() Allow override of hardcoded participant role
 * @return string
 */
function dps_get_participant_role() {
	return apply_filters( 'dps_get_participant_role', 'dps_participant' );
}

/**
 * The spectator role is for registered users without any capabilities
 *
 * @since Showcase (1.0)
 *
 * @uses apply_filters() Allow override of hardcoded spectator role
 * @return string
 */
function dps_get_spectator_role() {
	return apply_filters( 'dps_get_spectator_role', 'dps_spectator' );
}

/**
 * The blocked role is for registered users that cannot spectate or participate
 *
 * @since Showcase (1.0)
 *
 * @uses apply_filters() Allow override of hardcoded blocked role
 * @return string
 */
function dps_get_blocked_role() {
	return apply_filters( 'dps_get_blocked_role', 'dps_blocked' );
}

/** Deprecated ****************************************************************/

/**
 * Adds barebones-specific user roles.
 *
 * @since Showcase (1.0)
 * @deprecated since version 2.2
 */
function dps_add_roles() {
	_doing_it_wrong( 'dps_add_roles', __( 'Editable forum roles no longer exist.', 'barebones' ), '2.2' );
}

/**
 * Removes barebones-specific user roles.
 *
 * @since Showcase (1.0)
 * @deprecated since version 2.2
 */
function dps_remove_roles() {
	_doing_it_wrong( 'dps_remove_roles', __( 'Editable forum roles no longer exist.', 'barebones' ), '2.2' );
}
