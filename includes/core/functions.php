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

/** Post Meta *****************************************************************/

/**
 * Update a posts forum meta ID
 *
 * @since Showcase (1.0)
 *
 * @param int $post_id The post to update
 * @param int $forum_id The forum
 */
function dps_update_forum_id( $post_id, $forum_id ) {

	// Allow the forum ID to be updated 'just in time' before save
	$forum_id = apply_filters( 'dps_update_forum_id', $forum_id, $post_id );

	// Update the post meta forum ID
	update_post_meta( $post_id, '_dps_forum_id', (int) $forum_id );
}

/**
 * Update a posts topic meta ID
 *
 * @since Showcase (1.0)
 *
 * @param int $post_id The post to update
 * @param int $forum_id The forum
 */
function dps_update_topic_id( $post_id, $topic_id ) {

	// Allow the topic ID to be updated 'just in time' before save
	$topic_id = apply_filters( 'dps_update_topic_id', $topic_id, $post_id );

	// Update the post meta topic ID
	update_post_meta( $post_id, '_dps_topic_id', (int) $topic_id );
}

/**
 * Update a posts reply meta ID
 *
 * @since Showcase (1.0)
 *
 * @param int $post_id The post to update
 * @param int $forum_id The forum
 */
function dps_update_reply_id( $post_id, $reply_id ) {

	// Allow the reply ID to be updated 'just in time' before save
	$reply_id = apply_filters( 'dps_update_reply_id', $reply_id, $post_id );

	// Update the post meta reply ID
	update_post_meta( $post_id, '_dps_reply_id',(int) $reply_id );
}

/** Views *********************************************************************/

/**
 * Get the registered views
 *
 * Does nothing much other than return the {@link $bbp->views} variable
 *
 * @since Showcase (1.0)
 *
 * @return array Views
 */
function dps_get_views() {
	return showcase()->views;
}

/**
 * Register a showcase view
 *
 * @todo Implement feeds - See {@link http://trac.example.org/ticket/1422}
 *
 * @since Showcase (1.0)
 *
 * @param string $view View name
 * @param string $title View title
 * @param mixed $query_args {@link dps_has_topics()} arguments.
 * @param bool $feed Have a feed for the view? Defaults to true. NOT IMPLEMENTED
 * @param string $capability Capability that the current user must have
 * @uses sanitize_title() To sanitize the view name
 * @uses esc_html() To sanitize the view title
 * @return array The just registered (but processed) view
 */
function dps_register_view( $view, $title, $query_args = '', $feed = true, $capability = '' ) {

	// Bail if user does not have capability
	if ( ! empty( $capability ) && ! current_user_can( $capability ) )
		return false;

	$bbp   = showcase();
	$view  = sanitize_title( $view );
	$title = esc_html( $title );

	if ( empty( $view ) || empty( $title ) )
		return false;

	$query_args = dps_parse_args( $query_args, '', 'register_view' );

	// Set show_stickies to false if it wasn't supplied
	if ( !isset( $query_args['show_stickies'] ) )
		$query_args['show_stickies'] = false;

	$bbp->views[$view] = array(
		'title'  => $title,
		'query'  => $query_args,
		'feed'   => $feed
	);

	return $bbp->views[$view];
}

/**
 * Deregister a showcase view
 *
 * @since Showcase (1.0)
 *
 * @param string $view View name
 * @uses sanitize_title() To sanitize the view name
 * @return bool False if the view doesn't exist, true on success
 */
function dps_deregister_view( $view ) {
	$bbp  = showcase();
	$view = sanitize_title( $view );

	if ( !isset( $bbp->views[$view] ) )
		return false;

	unset( $bbp->views[$view] );

	return true;
}

/**
 * Run the view's query
 *
 * @since Showcase (1.0)
 *
 * @param string $view Optional. View id
 * @param mixed $new_args New arguments. See {@link dps_has_topics()}
 * @uses dps_get_view_id() To get the view id
 * @uses dps_get_view_query_args() To get the view query args
 * @uses sanitize_title() To sanitize the view name
 * @uses dps_has_topics() To make the topics query
 * @return bool False if the view doesn't exist, otherwise if topics are there
 */
function dps_view_query( $view = '', $new_args = '' ) {

	$view = dps_get_view_id( $view );
	if ( empty( $view ) )
		return false;

	$query_args = dps_get_view_query_args( $view );

	if ( !empty( $new_args ) ) {
		$new_args   = dps_parse_args( $new_args, '', 'view_query' );
		$query_args = array_merge( $query_args, $new_args );
	}

	return dps_has_topics( $query_args );
}

/**
 * Return the view's query arguments
 *
 * @since Showcase (1.0)
 *
 * @param string $view View name
 * @uses dps_get_view_id() To get the view id
 * @return array Query arguments
 */
function dps_get_view_query_args( $view ) {
	$view   = dps_get_view_id( $view );
	$retval = !empty( $view ) ? showcase()->views[$view]['query'] : false;

	return apply_filters( 'dps_get_view_query_args', $retval, $view );
}

/** Errors ********************************************************************/

/**
 * Adds an error message to later be output in the theme
 *
 * @since Showcase (1.0)
 *
 * @see WP_Error()
 * @uses WP_Error::add();
 *
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
 *
 * @see WP_Error()
 *
 * @uses is_wp_error()
 * @usese WP_Error::get_error_codes()
 */
function dps_has_errors() {
	$has_errors = showcase()->errors->get_error_codes() ? true : false;

	return apply_filters( 'dps_has_errors', $has_errors, showcase()->errors );
}

/** Mentions ******************************************************************/

/**
 * Searches through the content to locate usernames, designated by an @ sign.
 *
 * @since Showcase (1.0)
 *
 * @param string $content The content
 * @return bool|array $usernames Existing usernames. False if no matches.
 */
function dps_find_mentions( $content = '' ) {
	$pattern   = '/[@]+([A-Za-z0-9-_\.@]+)\b/';
	preg_match_all( $pattern, $content, $usernames );
	$usernames = array_unique( array_filter( $usernames[1] ) );

	// Bail if no usernames
	if ( empty( $usernames ) )
		return false;

	return $usernames;
}

/**
 * Finds and links @-mentioned users in the content
 *
 * @since Showcase (1.0)
 *
 * @uses dps_find_mentions() To get usernames in content areas
 * @return string $content Content filtered for mentions
 */
function dps_mention_filter( $content = '' ) {

	// Get Usernames and bail if none exist
	$usernames = dps_find_mentions( $content );
	if ( empty( $usernames ) )
		return $content;

	// Loop through usernames and link to profiles
	foreach( (array) $usernames as $username ) {

		// Skip if username does not exist or user is not active
		$user = get_user_by( 'slug', $username );
		if ( empty( $user->ID ) || dps_is_user_inactive( $user->ID ) )
			continue;

		// Replace name in content
		$content = preg_replace( '/(@' . $username . '\b)/', sprintf( '<a href="%1$s" rel="nofollow">@%2$s</a>', dps_get_user_profile_url( $user->ID ), $username ), $content );
	}

	// Return modified content
	return $content;
}

/** Post Statuses *************************************************************/

/**
 * Return the public post status ID
 *
 * @since Showcase (1.0)
 *
 * @return string
 */
function dps_get_public_status_id() {
	return showcase()->public_status_id;
}

/**
 * Return the pending post status ID
 *
 * @since Showcase (1.0)
 *
 * @return string
 */
function dps_get_pending_status_id() {
	return showcase()->pending_status_id;
}

/**
 * Return the private post status ID
 *
 * @since Showcase (1.0)
 *
 * @return string
 */
function dps_get_private_status_id() {
	return showcase()->private_status_id;
}

/**
 * Return the hidden post status ID
 *
 * @since Showcase (1.0)
 *
 * @return string
 */
function dps_get_hidden_status_id() {
	return showcase()->hidden_status_id;
}

/**
 * Return the closed post status ID
 *
 * @since Showcase (1.0)
 *
 * @return string
 */
function dps_get_closed_status_id() {
	return showcase()->closed_status_id;
}

/**
 * Return the spam post status ID
 *
 * @since Showcase (1.0)
 *
 * @return string
 */
function dps_get_spam_status_id() {
	return showcase()->spam_status_id;
}

/**
 * Return the trash post status ID
 *
 * @since Showcase (1.0)
 *
 * @return string
 */
function dps_get_trash_status_id() {
	return showcase()->trash_status_id;
}

/**
 * Return the orphan post status ID
 *
 * @since Showcase (1.0)
 *
 * @return string
 */
function dps_get_orphan_status_id() {
	return showcase()->orphan_status_id;
}

/** Rewrite IDs ***************************************************************/

/**
 * Return the unique ID for user profile rewrite rules
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_user_rewrite_id() {
	return showcase()->user_id;
}

/**
 * Return the unique ID for all edit rewrite rules (forum|topic|reply|tag|user)
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_edit_rewrite_id() {
	return showcase()->edit_id;
}

/**
 * Return the unique ID for all search rewrite rules
 *
 * @since Showcase (1.0)
 *
 * @return string
 */
function dps_get_search_rewrite_id() {
	return showcase()->search_id;
}

/**
 * Return the unique ID for user topics rewrite rules
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_user_topics_rewrite_id() {
	return showcase()->tops_id;
}

/**
 * Return the unique ID for user replies rewrite rules
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_user_replies_rewrite_id() {
	return showcase()->reps_id;
}

/**
 * Return the unique ID for user caps rewrite rules
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_user_favorites_rewrite_id() {
	return showcase()->favs_id;
}

/**
 * Return the unique ID for user caps rewrite rules
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_user_subscriptions_rewrite_id() {
	return showcase()->subs_id;
}

/**
 * Return the unique ID for topic view rewrite rules
 *
 * @since Showcase (1.0)
 * @return string
 */
function dps_get_view_rewrite_id() {
	return showcase()->view_id;
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

