<?php

/**
 * Showcase Updater
 *
 * @package Showcase
 * @subpackage Updater
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * If there is no raw DB version, this is the first installation
 *
 * @since Showcase (1.0)
 *
 * @uses get_option()
 * @uses dps_get_db_version() To get showcase's database version
 * @return bool True if update, False if not
 */
function dps_is_install() {
	return ! dps_get_db_version_raw();
}

/**
 * Compare the showcase version to the DB version to determine if updating
 *
 * @since Showcase (1.0)
 *
 * @uses get_option()
 * @uses dps_get_db_version() To get showcase's database version
 * @return bool True if update, False if not
 */
function dps_is_update() {
	$raw    = (int) dps_get_db_version_raw();
	$cur    = (int) dps_get_db_version();
	$retval = (bool) ( $raw < $cur );
	return $retval;
}

/**
 * Determine if showcase is being activated
 *
 * Note that this function currently is not used in showcase core and is here
 * for third party plugins to use to check for showcase activation.
 *
 * @since Showcase (1.0)
 *
 * @return bool True if activating showcase, false if not
 */
function dps_is_activation( $basename = '' ) {
	$bbp    = showcase();
	$action = false;

	if ( ! empty( $_REQUEST['action'] ) && ( '-1' != $_REQUEST['action'] ) ) {
		$action = $_REQUEST['action'];
	} elseif ( ! empty( $_REQUEST['action2'] ) && ( '-1' != $_REQUEST['action2'] ) ) {
		$action = $_REQUEST['action2'];
	}

	// Bail if not activating
	if ( empty( $action ) || !in_array( $action, array( 'activate', 'activate-selected' ) ) ) {
		return false;
	}

	// The plugin(s) being activated
	if ( $action == 'activate' ) {
		$plugins = isset( $_GET['plugin'] ) ? array( $_GET['plugin'] ) : array();
	} else {
		$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
	}

	// Set basename if empty
	if ( empty( $basename ) && !empty( $bbp->basename ) ) {
		$basename = $bbp->basename;
	}

	// Bail if no basename
	if ( empty( $basename ) ) {
		return false;
	}

	// Is showcase being activated?
	return in_array( $basename, $plugins );
}

/**
 * Determine if showcase is being deactivated
 *
 * @since Showcase (1.0)
 * @return bool True if deactivating showcase, false if not
 */
function dps_is_deactivation( $basename = '' ) {
	$bbp    = showcase();
	$action = false;
	
	if ( ! empty( $_REQUEST['action'] ) && ( '-1' != $_REQUEST['action'] ) ) {
		$action = $_REQUEST['action'];
	} elseif ( ! empty( $_REQUEST['action2'] ) && ( '-1' != $_REQUEST['action2'] ) ) {
		$action = $_REQUEST['action2'];
	}

	// Bail if not deactivating
	if ( empty( $action ) || !in_array( $action, array( 'deactivate', 'deactivate-selected' ) ) ) {
		return false;
	}

	// The plugin(s) being deactivated
	if ( $action == 'deactivate' ) {
		$plugins = isset( $_GET['plugin'] ) ? array( $_GET['plugin'] ) : array();
	} else {
		$plugins = isset( $_POST['checked'] ) ? (array) $_POST['checked'] : array();
	}

	// Set basename if empty
	if ( empty( $basename ) && !empty( $bbp->basename ) ) {
		$basename = $bbp->basename;
	}

	// Bail if no basename
	if ( empty( $basename ) ) {
		return false;
	}

	// Is showcase being deactivated?
	return in_array( $basename, $plugins );
}

/**
 * Update the DB to the latest version
 *
 * @since Showcase (1.0)
 * @uses update_option()
 * @uses dps_get_db_version() To get showcase's database version
 */
function dps_version_bump() {
	update_option( '_dps_db_version', dps_get_db_version() );
}

/**
 * Setup the showcase updater
 *
 * @since Showcase (1.0)
 *
 * @uses dps_version_updater()
 * @uses dps_version_bump()
 * @uses flush_rewrite_rules()
 */
function dps_setup_updater() {

	// Bail if no update needed
	if ( ! dps_is_update() )
		return;

	// Call the automated updater
	dps_version_updater();
}

/**
 * Create a default forum, topic, and reply
 *
 * @since Showcase (1.0)
 * @param array $args Array of arguments to override default values
 */
function dps_create_initial_content( $args = array() ) {

	// Parse arguments against default values
	$r = dps_parse_args( $args, array(
		'forum_parent'  => 0,
		'forum_status'  => 'publish',
		'forum_title'   => __( 'General',                                  'showcase' ),
		'forum_content' => __( 'General chit-chat',                        'showcase' ),
		'topic_title'   => __( 'Hello World!',                             'showcase' ),
		'topic_content' => __( 'I am the first topic in your new forums.', 'dps' ),
		'reply_title'   => __( 'Re: Hello World!',                         'showcase' ),
		'reply_content' => __( 'Oh, and this is what a reply looks like.', 'dps' ),
	), 'create_initial_content' );

	// Create the initial forum
	$forum_id = dps_insert_forum( array(
		'post_parent'  => $r['forum_parent'],
		'post_status'  => $r['forum_status'],
		'post_title'   => $r['forum_title'],
		'post_content' => $r['forum_content']
	) );

	// Create the initial topic
	$topic_id = dps_insert_topic(
		array(
			'post_parent'  => $forum_id,
			'post_title'   => $r['topic_title'],
			'post_content' => $r['topic_content']
		),
		array( 'forum_id'  => $forum_id )
	);

	// Create the initial reply
	$reply_id = dps_insert_reply(
		array(
			'post_parent'  => $topic_id,
			'post_title'   => $r['reply_title'],
			'post_content' => $r['reply_content']
		),
		array(
			'forum_id'     => $forum_id,
			'topic_id'     => $topic_id
		)
	);

	return array(
		'forum_id' => $forum_id,
		'topic_id' => $topic_id,
		'reply_id' => $reply_id
	);
}

/**
 * Showcase's version updater looks at what the current database version is, and
 * runs whatever other code is needed.
 *
 * This is most-often used when the data schema changes, but should also be used
 * to correct issues with showcase meta-data silently on software update.
 *
 * @since Showcase (1.0)
 */
function dps_version_updater() {

	// Get the raw database version
	$raw_db_version = (int) dps_get_db_version_raw();

	// Nothing for now!

	// Bump the version
	dps_version_bump();

	// Delete rewrite rules to force a flush
	dps_delete_rewrite_rules();
}
