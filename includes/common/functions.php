<?php

/**
 * Showcase Common Functions
 *
 * Common functions are ones that are used by more than one component, like
 * forums, topics, replies, users, topic tags, etc...
 *
 * @package Showcase
 * @subpackage Functions
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Formatting ****************************************************************/

/**
 * A showcase specific method of formatting numeric values
 *
 * @since Showcase (1.0)
 *
 * @param string $number Number to format
 * @param string $decimals Optional. Display decimals
 * @uses apply_filters() Calls 'dps_number_format' with the formatted values,
 *                        number and display decimals bool
 * @return string Formatted string
 */
function dps_number_format( $number = 0, $decimals = false, $dec_point = '.', $thousands_sep = ',' ) {

	// If empty, set $number to (int) 0
	if ( ! is_numeric( $number ) )
		$number = 0;

	return apply_filters( 'dps_number_format', number_format( $number, $decimals, $dec_point, $thousands_sep ), $number, $decimals, $dec_point, $thousands_sep );
}

/**
 * A showcase specific method of formatting numeric values
 *
 * @since Showcase (1.0)
 *
 * @param string $number Number to format
 * @param string $decimals Optional. Display decimals
 * @uses apply_filters() Calls 'dps_number_format' with the formatted values,
 *                        number and display decimals bool
 * @return string Formatted string
 */
function dps_number_format_i18n( $number = 0, $decimals = false ) {

	// If empty, set $number to (int) 0
	if ( ! is_numeric( $number ) )
		$number = 0;

	return apply_filters( 'dps_number_format_i18n', number_format_i18n( $number, $decimals ), $number, $decimals );
}

/**
 * Convert time supplied from database query into specified date format.
 *
 * @since Showcase (1.0)
 *
 * @param int|object $post Optional. Default is global post object. A post_id or
 *                          post object
 * @param string $d Optional. Default is 'U'. Either 'G', 'U', or php date
 *                             format
 * @param bool $translate Optional. Default is false. Whether to translate the
 *                                   result
 * @uses mysql2date() To convert the format
 * @uses apply_filters() Calls 'dps_convert_date' with the time, date format
 *                        and translate bool
 * @return string Returns timestamp
 */
function dps_convert_date( $time, $d = 'U', $translate = false ) {
	$time = mysql2date( $d, $time, $translate );

	return apply_filters( 'dps_convert_date', $time, $d, $translate );
}

/**
 * Output formatted time to display human readable time difference.
 *
 * @since Showcase (1.0)
 *
 * @param string $older_date Unix timestamp from which the difference begins.
 * @param string $newer_date Optional. Unix timestamp from which the
 *                            difference ends. False for current time.
 * @uses dps_get_time_since() To get the formatted time
 */
function dps_time_since( $older_date, $newer_date = false ) {
	echo dps_get_time_since( $older_date, $newer_date );
}
	/**
	 * Return formatted time to display human readable time difference.
	 *
	 * @since Showcase (1.0)
	 *
	 * @param string $older_date Unix timestamp from which the difference begins.
	 * @param string $newer_date Optional. Unix timestamp from which the
	 *                            difference ends. False for current time.
	 * @uses current_time() To get the current time in mysql format
	 * @uses human_time_diff() To get the time differene in since format
	 * @uses apply_filters() Calls 'dps_get_time_since' with the time
	 *                        difference and time
	 * @return string Formatted time
	 */
	function dps_get_time_since( $older_date, $newer_date = false ) {

		// Setup the strings
		$unknown_text   = apply_filters( 'dps_core_time_since_unknown_text',   __( 'sometime',  'showcase' ) );
		$right_now_text = apply_filters( 'dps_core_time_since_right_now_text', __( 'right now', 'dps' ) );
		$ago_text       = apply_filters( 'dps_core_time_since_ago_text',       __( '%s ago',    'showcase' ) );

		// array of time period chunks
		$chunks = array(
			array( 60 * 60 * 24 * 365 , __( 'year',   'showcase' ), __( 'years',   'showcase' ) ),
			array( 60 * 60 * 24 * 30 ,  __( 'month',  'showcase' ), __( 'months',  'showcase' ) ),
			array( 60 * 60 * 24 * 7,    __( 'week',   'showcase' ), __( 'weeks',   'showcase' ) ),
			array( 60 * 60 * 24 ,       __( 'day',    'showcase' ), __( 'days',    'showcase' ) ),
			array( 60 * 60 ,            __( 'hour',   'showcase' ), __( 'hours',   'showcase' ) ),
			array( 60 ,                 __( 'minute', 'dps' ), __( 'minutes', 'dps' ) ),
			array( 1,                   __( 'second', 'dps' ), __( 'seconds', 'dps' ) )
		);

		if ( !empty( $older_date ) && !is_numeric( $older_date ) ) {
			$time_chunks = explode( ':', str_replace( ' ', ':', $older_date ) );
			$date_chunks = explode( '-', str_replace( ' ', '-', $older_date ) );
			$older_date  = gmmktime( (int) $time_chunks[1], (int) $time_chunks[2], (int) $time_chunks[3], (int) $date_chunks[1], (int) $date_chunks[2], (int) $date_chunks[0] );
		}

		// $newer_date will equal false if we want to know the time elapsed
		// between a date and the current time. $newer_date will have a value if
		// we want to work out time elapsed between two known dates.
		$newer_date = ( !$newer_date ) ? strtotime( current_time( 'mysql' ) ) : $newer_date;

		// Difference in seconds
		$since = $newer_date - $older_date;

		// Something went wrong with date calculation and we ended up with a negative date.
		if ( 0 > $since ) {
			$output = $unknown_text;

		// We only want to output two chunks of time here, eg:
		//     x years, xx months
		//     x days, xx hours
		// so there's only two bits of calculation below:
		} else {

			// Step one: the first chunk
			for ( $i = 0, $j = count( $chunks ); $i < $j; ++$i ) {
				$seconds = $chunks[$i][0];

				// Finding the biggest chunk (if the chunk fits, break)
				$count = floor( $since / $seconds );
				if ( 0 != $count ) {
					break;
				}
			}

			// If $i iterates all the way to $j, then the event happened 0 seconds ago
			if ( !isset( $chunks[$i] ) ) {
				$output = $right_now_text;

			} else {

				// Set output var
				$output = ( 1 == $count ) ? '1 '. $chunks[$i][1] : $count . ' ' . $chunks[$i][2];

				// Step two: the second chunk
				if ( $i + 2 < $j ) {
					$seconds2 = $chunks[$i + 1][0];
					$name2    = $chunks[$i + 1][1];
					$count2   = floor( ( $since - ( $seconds * $count ) ) / $seconds2 );

					// Add to output var
					if ( 0 != $count2 ) {
						$output .= ( 1 == $count2 ) ? _x( ',', 'Separator in time since', 'dps' ) . ' 1 '. $name2 : _x( ',', 'Separator in time since', 'dps' ) . ' ' . $count2 . ' ' . $chunks[$i + 1][2];
					}
				}

				// No output, so happened right now
				if ( ! (int) trim( $output ) ) {
					$output = $right_now_text;
				}
			}
		}

		// Append 'ago' to the end of time-since if not 'right now'
		if ( $output != $right_now_text ) {
			$output = sprintf( $ago_text, $output );
		}

		return apply_filters( 'dps_get_time_since', $output, $older_date, $newer_date );
	}

/**
 * Formats the reason for editing the topic/reply.
 *
 * Does these things:
 *  - Trimming
 *  - Removing periods from the end of the string
 *  - Trimming again
 *
 * @since Showcase (1.0)
 *
 * @param int $topic_id Optional. Topic id
 * @return string Status of topic
 */
function dps_format_revision_reason( $reason = '' ) {
	$reason = (string) $reason;

	// Format reason for proper display
	if ( empty( $reason ) )
		return $reason;

	// Trimming
	$reason = trim( $reason );

	// We add our own full stop.
	while ( substr( $reason, -1 ) == '.' )
		$reason = substr( $reason, 0, -1 );

	// Trim again
	$reason = trim( $reason );

	return $reason;
}

/** Misc **********************************************************************/

/**
 * Assist pagination by returning correct page number
 *
 * @since Showcase (1.0)
 *
 * @uses get_query_var() To get the 'paged' value
 * @return int Current page number
 */
function dps_get_paged() {
	global $wp_query;

	// Check the query var
	if ( get_query_var( 'paged' ) ) {
		$paged = get_query_var( 'paged' );

	// Check query paged
	} elseif ( !empty( $wp_query->query['paged'] ) ) {
		$paged = $wp_query->query['paged'];
	}

	// Paged found
	if ( !empty( $paged ) )
		return (int) $paged;

	// Default to first page
	return 1;
}

/**
 * Check the date against the _dps_edit_lock setting.
 *
 * @since Showcase (1.0)
 *
 * @param string $post_date_gmt
 *
 * @uses get_option() Get the edit lock time
 * @uses current_time() Get the current time
 * @uses strtotime() Convert strings to time
 * @uses apply_filters() Allow output to be manipulated
 *
 * @return bool
 */
function dps_past_edit_lock( $post_date_gmt ) {

	// Assume editing is allowed
	$retval = false;

	// Bail if empty date
	if ( ! empty( $post_date_gmt ) ) {

		// Period of time
		$lockable  = '+' . get_option( '_dps_edit_lock', '5' ) . ' minutes';

		// Now
		$cur_time  = current_time( 'timestamp', true );

		// Add lockable time to post time
		$lock_time = strtotime( $lockable, strtotime( $post_date_gmt ) );

		// Compare
		if ( $cur_time >= $lock_time ) {
			$retval = true;
		}
	}

	return apply_filters( 'dps_past_edit_lock', (bool) $retval, $cur_time, $lock_time, $post_date_gmt );
}

/** Statistics ****************************************************************/

/**
 * Get the forum statistics
 *
 * @since Showcase (1.0)
 *
 * @param mixed $args Optional. The function supports these arguments (all
 *                     default to true):
 *  - count_users: Count users?
 *  - count_forums: Count forums?
 *  - count_topics: Count topics? If set to false, private, spammed and trashed
 *                   topics are also not counted.
 *  - count_private_topics: Count private topics? (only counted if the current
 *                           user has read_private_topics cap)
 *  - count_spammed_topics: Count spammed topics? (only counted if the current
 *                           user has edit_others_topics cap)
 *  - count_trashed_topics: Count trashed topics? (only counted if the current
 *                           user has view_trash cap)
 *  - count_replies: Count replies? If set to false, private, spammed and
 *                   trashed replies are also not counted.
 *  - count_private_replies: Count private replies? (only counted if the current
 *                           user has read_private_replies cap)
 *  - count_spammed_replies: Count spammed replies? (only counted if the current
 *                           user has edit_others_replies cap)
 *  - count_trashed_replies: Count trashed replies? (only counted if the current
 *                           user has view_trash cap)
 *  - count_tags: Count tags? If set to false, empty tags are also not counted
 *  - count_empty_tags: Count empty tags?
 * @return object Walked forum tree
 */
function dps_get_statistics( $args = '' ) {

	// Parse arguments against default values
	$r = dps_parse_args( $args, array(
		'count_users'           => true,
		'count_forums'          => true,
		'count_topics'          => true,
		'count_private_topics'  => true,
		'count_spammed_topics'  => true,
		'count_trashed_topics'  => true,
		'count_replies'         => true,
		'count_private_replies' => true,
		'count_spammed_replies' => true,
		'count_trashed_replies' => true,
		'count_tags'            => true,
		'count_empty_tags'      => true
	), 'get_statistics' );

	// Defaults
	$user_count            = 0;
	$forum_count           = 0;
	$topic_count           = 0;
	$topic_count_hidden    = 0;
	$reply_count           = 0;
	$reply_count_hidden    = 0;
	$topic_tag_count       = 0;
	$empty_topic_tag_count = 0;

	// Users
	if ( !empty( $r['count_users'] ) ) {
		$user_count = dps_get_total_users();
	}

	// Forums
	if ( !empty( $r['count_forums'] ) ) {
		$forum_count = wp_count_posts( dps_get_showcase_post_type() )->publish;
	}

	// Post statuses
	$private = dps_get_private_status_id();
	$spam    = dps_get_spam_status_id();
	$trash   = dps_get_trash_status_id();
	$closed  = dps_get_closed_status_id();

	// Topics
	if ( !empty( $r['count_topics'] ) ) {
		$all_topics  = wp_count_posts( dps_get_topic_post_type() );

		// Published (publish + closed)
		$topic_count = $all_topics->publish + $all_topics->{$closed};

		if ( current_user_can( 'read_private_topics' ) || current_user_can( 'edit_others_topics' ) || current_user_can( 'view_trash' ) ) {

			// Private
			$topics['private'] = ( !empty( $r['count_private_topics'] ) && current_user_can( 'read_private_topics' ) ) ? (int) $all_topics->{$private} : 0;

			// Spam
			$topics['spammed'] = ( !empty( $r['count_spammed_topics'] ) && current_user_can( 'edit_others_topics'  ) ) ? (int) $all_topics->{$spam}    : 0;

			// Trash
			$topics['trashed'] = ( !empty( $r['count_trashed_topics'] ) && current_user_can( 'view_trash'          ) ) ? (int) $all_topics->{$trash}   : 0;

			// Total hidden (private + spam + trash)
			$topic_count_hidden = $topics['private'] + $topics['spammed'] + $topics['trashed'];

			// Generate the hidden topic count's title attribute
			$topic_titles[] = !empty( $topics['private'] ) ? sprintf( __( 'Private: %s', 'dps' ), number_format_i18n( $topics['private'] ) ) : '';
			$topic_titles[] = !empty( $topics['spammed'] ) ? sprintf( __( 'Spammed: %s', 'dps' ), number_format_i18n( $topics['spammed'] ) ) : '';
			$topic_titles[] = !empty( $topics['trashed'] ) ? sprintf( __( 'Trashed: %s', 'dps' ), number_format_i18n( $topics['trashed'] ) ) : '';

			// Compile the hidden topic title
			$hidden_topic_title = implode( ' | ', array_filter( $topic_titles ) );
		}
	}

	// Replies
	if ( !empty( $r['count_replies'] ) ) {

		$all_replies = wp_count_posts( dps_get_reply_post_type() );

		// Published
		$reply_count = $all_replies->publish;

		if ( current_user_can( 'read_private_replies' ) || current_user_can( 'edit_others_replies' ) || current_user_can( 'view_trash' ) ) {

			// Private
			$replies['private'] = ( !empty( $r['count_private_replies'] ) && current_user_can( 'read_private_replies' ) ) ? (int) $all_replies->{$private} : 0;

			// Spam
			$replies['spammed'] = ( !empty( $r['count_spammed_replies'] ) && current_user_can( 'edit_others_replies'  ) ) ? (int) $all_replies->{$spam}    : 0;

			// Trash
			$replies['trashed'] = ( !empty( $r['count_trashed_replies'] ) && current_user_can( 'view_trash'           ) ) ? (int) $all_replies->{$trash}   : 0;

			// Total hidden (private + spam + trash)
			$reply_count_hidden = $replies['private'] + $replies['spammed'] + $replies['trashed'];

			// Generate the hidden topic count's title attribute
			$reply_titles[] = !empty( $replies['private'] ) ? sprintf( __( 'Private: %s', 'dps' ), number_format_i18n( $replies['private'] ) ) : '';
			$reply_titles[] = !empty( $replies['spammed'] ) ? sprintf( __( 'Spammed: %s', 'dps' ), number_format_i18n( $replies['spammed'] ) ) : '';
			$reply_titles[] = !empty( $replies['trashed'] ) ? sprintf( __( 'Trashed: %s', 'dps' ), number_format_i18n( $replies['trashed'] ) ) : '';

			// Compile the hidden replies title
			$hidden_reply_title = implode( ' | ', array_filter( $reply_titles ) );

		}
	}

	// Topic Tags
	if ( !empty( $r['count_tags'] ) && dps_allow_topic_tags() ) {

		// Get the count
		$topic_tag_count = wp_count_terms( dps_get_topic_tag_tax_id(), array( 'hide_empty' => true ) );

		// Empty tags
		if ( !empty( $r['count_empty_tags'] ) && current_user_can( 'edit_topic_tags' ) ) {
			$empty_topic_tag_count = wp_count_terms( dps_get_topic_tag_tax_id() ) - $topic_tag_count;
		}
	}

	// Tally the tallies
	$statistics = array_map( 'number_format_i18n', array_map( 'absint', compact(
		'user_count',
		'forum_count',
		'topic_count',
		'topic_count_hidden',
		'reply_count',
		'reply_count_hidden',
		'topic_tag_count',
		'empty_topic_tag_count'
	) ) );

	// Add the hidden (topic/reply) count title attribute strings because we
	// don't need to run the math functions on these (see above)
	$statistics['hidden_topic_title'] = isset( $hidden_topic_title ) ? $hidden_topic_title : '';
	$statistics['hidden_reply_title'] = isset( $hidden_reply_title ) ? $hidden_reply_title : '';

	return apply_filters( 'dps_get_statistics', $statistics, $r );
}

/** New/edit topic/reply helpers **********************************************/

/**
 * Filter anonymous post data
 *
 * We use REMOTE_ADDR here directly. If you are behind a proxy, you should
 * ensure that it is properly set, such as in wp-config.php, for your
 * environment. See {@link http://core.trac.wordpress.org/ticket/9235}
 *
 * Note that dps_pre_anonymous_filters() is responsible for sanitizing each
 * of the filtered core anonymous values here.
 *
 * If there are any errors, those are directly added to {@link showcase:errors}
 *
 * @since Showcase (1.0)
 *
 * @param mixed $args Optional. If no args are there, then $_POST values are
 *                     used.
 * @uses apply_filters() Calls 'dps_pre_anonymous_post_author_name' with the
 *                        anonymous user name
 * @uses apply_filters() Calls 'dps_pre_anonymous_post_author_email' with the
 *                        anonymous user email
 * @uses apply_filters() Calls 'dps_pre_anonymous_post_author_website' with the
 *                        anonymous user website
 * @return bool|array False on errors, values in an array on success
 */
function dps_filter_anonymous_post_data( $args = '' ) {

	// Parse arguments against default values
	$r = dps_parse_args( $args, array (
		'dps_anonymous_name'    => !empty( $_POST['dps_anonymous_name']    ) ? $_POST['dps_anonymous_name']    : false,
		'dps_anonymous_email'   => !empty( $_POST['dps_anonymous_email']   ) ? $_POST['dps_anonymous_email']   : false,
		'dps_anonymous_website' => !empty( $_POST['dps_anonymous_website'] ) ? $_POST['dps_anonymous_website'] : false,
	), 'filter_anonymous_post_data' );

	// Filter variables and add errors if necessary
	$r['dps_anonymous_name'] = apply_filters( 'dps_pre_anonymous_post_author_name',  $r['dps_anonymous_name']  );
	if ( empty( $r['dps_anonymous_name'] ) )
		dps_add_error( 'dps_anonymous_name',  __( '<strong>ERROR</strong>: Invalid author name submitted!',   'showcase' ) );

	$r['dps_anonymous_email'] = apply_filters( 'dps_pre_anonymous_post_author_email', $r['dps_anonymous_email'] );
	if ( empty( $r['dps_anonymous_email'] ) )
		dps_add_error( 'dps_anonymous_email', __( '<strong>ERROR</strong>: Invalid email address submitted!', 'dps' ) );

	// Website is optional
	$r['dps_anonymous_website'] = apply_filters( 'dps_pre_anonymous_post_author_website', $r['dps_anonymous_website'] );

	// Return false if we have any errors
	$retval = dps_has_errors() ? false : $r;

	// Finally, return sanitized data or false
	return apply_filters( 'dps_filter_anonymous_post_data', $retval, $r );
}

/**
 * Check for duplicate topics/replies
 *
 * Check to make sure that a user is not making a duplicate post
 *
 * @since Showcase (1.0)
 *
 * @param array $post_data Contains information about the comment
 * @uses current_user_can() To check if the current user can throttle
 * @uses get_meta_sql() To generate the meta sql for checking anonymous email
 * @uses apply_filters() Calls 'dps_check_for_duplicate_query' with the
 *                        duplicate check query and post data
 * @uses wpdb::get_var() To execute our query and get the var back
 * @uses get_post_meta() To get the anonymous user email post meta
 * @uses do_action() Calls 'dps_post_duplicate_trigger' with the post data when
 *                    it is found that it is a duplicate
 * @return bool True if it is not a duplicate, false if it is
 */
function dps_check_for_duplicate( $post_data = array() ) {

	// No duplicate checks for those who can throttle
	if ( current_user_can( 'throttle' ) )
		return true;

	// Define global to use get_meta_sql() and get_var() methods
	global $wpdb;

	// Parse arguments against default values
	$r = dps_parse_args( $post_data, array(
		'post_author'    => 0,
		'post_type'      => array( dps_get_topic_post_type(), dps_get_reply_post_type() ),
		'post_parent'    => 0,
		'post_content'   => '',
		'post_status'    => dps_get_trash_status_id(),
		'anonymous_data' => false
	), 'check_for_duplicate' );

	// Check for anonymous post
	if ( empty( $r['post_author'] ) && ( !empty( $r['anonymous_data'] ) && !empty( $r['anonymous_data']['dps_anonymous_email'] ) ) ) {
		$clauses = get_meta_sql( array( array(
			'key'   => '_dps_anonymous_email',
			'value' => $r['anonymous_data']['dps_anonymous_email']
		) ), 'post', $wpdb->posts, 'ID' );

		$join    = $clauses['join'];
		$where   = $clauses['where'];
	} else {
		$join    = $where = '';
	}

	// Unslash $r to pass through $wpdb->prepare()
	//
	// @see: http://core.trac.wordpress.org/changeset/23973/
	$r = function_exists( 'wp_unslash' ) ? wp_unslash( $r ) : stripslashes_deep( $r );

	// Prepare duplicate check query
	$query  = $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} {$join} WHERE post_type = %s AND post_status != %s AND post_author = %d AND post_content = %s {$where}", $r['post_type'], $r['post_status'], $r['post_author'], $r['post_content'] );
	$query .= !empty( $r['post_parent'] ) ? $wpdb->prepare( " AND post_parent = %d", $r['post_parent'] ) : '';
	$query .= " LIMIT 1";
	$dupe   = apply_filters( 'dps_check_for_duplicate_query', $query, $r );

	if ( $wpdb->get_var( $dupe ) ) {
		do_action( 'dps_check_for_duplicate_trigger', $post_data );
		return false;
	}

	return true;
}

/**
 * Check for flooding
 *
 * Check to make sure that a user is not making too many posts in a short amount
 * of time.
 *
 * @since Showcase (1.0)
 *
 * @param false|array $anonymous_data Optional - if it's an anonymous post. Do
 *                                     not supply if supplying $author_id.
 *                                     Should have key 'dps_author_ip'.
 *                                     Should be sanitized (see
 *                                     {@link dps_filter_anonymous_post_data()}
 *                                     for sanitization)
 * @param int $author_id Optional. Supply if it's a post by a logged in user.
 *                        Do not supply if supplying $anonymous_data.
 * @uses get_option() To get the throttle time
 * @uses get_transient() To get the last posted transient of the ip
 * @uses dps_get_user_last_posted() To get the last posted time of the user
 * @uses current_user_can() To check if the current user can throttle
 * @return bool True if there is no flooding, false if there is
 */
function dps_check_for_flood( $anonymous_data = false, $author_id = 0 ) {

	// Option disabled. No flood checks.
	$throttle_time = get_option( '_dps_throttle_time' );
	if ( empty( $throttle_time ) )
		return true;

	// User is anonymous, so check a transient based on the IP
	if ( !empty( $anonymous_data ) && is_array( $anonymous_data ) ) {
		$last_posted = get_transient( '_dps_' . dps_current_author_ip() . '_last_posted' );

		if ( !empty( $last_posted ) && time() < $last_posted + $throttle_time ) {
			return false;
		}

	// User is logged in, so check their last posted time
	} elseif ( !empty( $author_id ) ) {
		$author_id   = (int) $author_id;
		$last_posted = dps_get_user_last_posted( $author_id );

		if ( isset( $last_posted ) && time() < $last_posted + $throttle_time && !current_user_can( 'throttle' ) ) {
			return false;
		}
	} else {
		return false;
	}

	return true;
}

/**
 * Checks topics and replies against the discussion moderation of blocked keys
 *
 * @since Showcase (1.0)
 *
 * @param array $anonymous_data Anonymous user data
 * @param int $author_id Topic or reply author ID
 * @param string $title The title of the content
 * @param string $content The content being posted
 * @uses dps_is_user_keymaster() Allow keymasters to bypass blacklist
 * @uses dps_current_author_ip() To get current user IP address
 * @uses dps_current_author_ua() To get current user agent
 * @return bool True if test is passed, false if fail
 */
function dps_check_for_moderation( $anonymous_data = false, $author_id = 0, $title = '', $content = '' ) {

	// Allow for moderation check to be skipped
	if ( apply_filters( 'dps_bypass_check_for_moderation', false, $anonymous_data, $author_id, $title, $content ) )
		return true;

	// Bail if keymaster is author
	if ( dps_is_user_keymaster( $author_id ) )
		return true;

	// Define local variable(s)
	$_post     = array();
	$match_out = '';

	/** Blacklist *************************************************************/

	// Get the moderation keys
	$blacklist = trim( get_option( 'moderation_keys' ) );

	// Bail if blacklist is empty
	if ( empty( $blacklist ) )
		return true;

	/** User Data *************************************************************/

	// Map anonymous user data
	if ( !empty( $anonymous_data ) ) {
		$_post['author'] = $anonymous_data['dps_anonymous_name'];
		$_post['email']  = $anonymous_data['dps_anonymous_email'];
		$_post['url']    = $anonymous_data['dps_anonymous_website'];

	// Map current user data
	} elseif ( !empty( $author_id ) ) {

		// Get author data
		$user = get_userdata( $author_id );

		// If data exists, map it
		if ( !empty( $user ) ) {
			$_post['author'] = $user->display_name;
			$_post['email']  = $user->user_email;
			$_post['url']    = $user->user_url;
		}
	}

	// Current user IP and user agent
	$_post['user_ip'] = dps_current_author_ip();
	$_post['user_ua'] = dps_current_author_ua();

	// Post title and content
	$_post['title']   = $title;
	$_post['content'] = $content;

	/** Max Links *************************************************************/

	$max_links = get_option( 'comment_max_links' );
	if ( !empty( $max_links ) ) {

		// How many links?
		$num_links = preg_match_all( '/<a [^>]*href/i', $content, $match_out );

		// Allow for bumping the max to include the user's URL
		$num_links = apply_filters( 'comment_max_links_url', $num_links, $_post['url'] );

		// Das ist zu viele links!
		if ( $num_links >= $max_links ) {
			return false;
		}
	}

	/** Words *****************************************************************/

	// Get words separated by new lines
	$words = explode( "\n", $blacklist );

	// Loop through words
	foreach ( (array) $words as $word ) {

		// Trim the whitespace from the word
		$word = trim( $word );

		// Skip empty lines
		if ( empty( $word ) ) { continue; }

		// Do some escaping magic so that '#' chars in the
		// spam words don't break things:
		$word    = preg_quote( $word, '#' );
		$pattern = "#$word#i";

		// Loop through post data
		foreach( $_post as $post_data ) {

			// Check each user data for current word
			if ( preg_match( $pattern, $post_data ) ) {

				// Post does not pass
				return false;
			}
		}
	}

	// Check passed successfully
	return true;
}

/**
 * Checks topics and replies against the discussion blacklist of blocked keys
 *
 * @since Showcase (1.0)
 *
 * @param array $anonymous_data Anonymous user data
 * @param int $author_id Topic or reply author ID
 * @param string $title The title of the content
 * @param string $content The content being posted
 * @uses dps_is_user_keymaster() Allow keymasters to bypass blacklist
 * @uses dps_current_author_ip() To get current user IP address
 * @uses dps_current_author_ua() To get current user agent
 * @return bool True if test is passed, false if fail
 */
function dps_check_for_blacklist( $anonymous_data = false, $author_id = 0, $title = '', $content = '' ) {

	// Allow for blacklist check to be skipped
	if ( apply_filters( 'dps_bypass_check_for_blacklist', false, $anonymous_data, $author_id, $title, $content ) )
		return true;

	// Bail if keymaster is author
	if ( dps_is_user_keymaster( $author_id ) )
		return true;

	// Define local variable
	$_post = array();

	/** Blacklist *************************************************************/

	// Get the moderation keys
	$blacklist = trim( get_option( 'blacklist_keys' ) );

	// Bail if blacklist is empty
	if ( empty( $blacklist ) )
		return true;

	/** User Data *************************************************************/

	// Map anonymous user data
	if ( !empty( $anonymous_data ) ) {
		$_post['author'] = $anonymous_data['dps_anonymous_name'];
		$_post['email']  = $anonymous_data['dps_anonymous_email'];
		$_post['url']    = $anonymous_data['dps_anonymous_website'];

	// Map current user data
	} elseif ( !empty( $author_id ) ) {

		// Get author data
		$user = get_userdata( $author_id );

		// If data exists, map it
		if ( !empty( $user ) ) {
			$_post['author'] = $user->display_name;
			$_post['email']  = $user->user_email;
			$_post['url']    = $user->user_url;
		}
	}

	// Current user IP and user agent
	$_post['user_ip'] = dps_current_author_ip();
	$_post['user_ua'] = dps_current_author_ua();

	// Post title and content
	$_post['title']   = $title;
	$_post['content'] = $content;

	/** Words *****************************************************************/

	// Get words separated by new lines
	$words = explode( "\n", $blacklist );

	// Loop through words
	foreach ( (array) $words as $word ) {

		// Trim the whitespace from the word
		$word = trim( $word );

		// Skip empty lines
		if ( empty( $word ) ) { continue; }

		// Do some escaping magic so that '#' chars in the
		// spam words don't break things:
		$word    = preg_quote( $word, '#' );
		$pattern = "#$word#i";

		// Loop through post data
		foreach( $_post as $post_data ) {

			// Check each user data for current word
			if ( preg_match( $pattern, $post_data ) ) {

				// Post does not pass
				return false;
			}
		}
	}

	// Check passed successfully
	return true;
}


/** Queries *******************************************************************/

/**
 * Merge user defined arguments into defaults array.
 *
 * This function is used throughout showcase to allow for either a string or array
 * to be merged into another array. It is identical to wp_parse_args() except
 * it allows for arguments to be passively or aggressively filtered using the
 * optional $filter_key parameter.
 *
 * @since Showcase (1.0)
 *
 * @param string|array $args Value to merge with $defaults
 * @param array $defaults Array that serves as the defaults.
 * @param string $filter_key String to key the filters from
 * @return array Merged user defined values with defaults.
 */
function dps_parse_args( $args, $defaults = '', $filter_key = '' ) {

	// Setup a temporary array from $args
	if ( is_object( $args ) )
		$r = get_object_vars( $args );
	elseif ( is_array( $args ) )
		$r =& $args;
	else
		wp_parse_str( $args, $r );

	// Passively filter the args before the parse
	if ( !empty( $filter_key ) )
		$r = apply_filters( 'dps_before_' . $filter_key . '_parse_args', $r );

	// Parse
	if ( is_array( $defaults ) )
		$r = array_merge( $defaults, $r );

	// Aggressively filter the args after the parse
	if ( !empty( $filter_key ) )
		$r = apply_filters( 'dps_after_' . $filter_key . '_parse_args', $r );

	// Return the parsed results
	return $r;
}

/**
 * Query the DB and get the last public post_id that has parent_id as post_parent
 *
 * @param int $parent_id Parent id
 * @param string $post_type Post type. Defaults to 'post'
 * @uses dps_get_topic_post_type() To get the topic post type
 * @uses wp_cache_get() To check if there is a cache of the last child id
 * @uses wpdb::prepare() To prepare the query
 * @uses wpdb::get_var() To get the result of the query in a variable
 * @uses wp_cache_set() To set the cache for future use
 * @uses apply_filters() Calls 'dps_get_public_child_last_id' with the child
 *                        id, parent id and post type
 * @return int The last active post_id
 */
function dps_get_public_child_last_id( $parent_id = 0, $post_type = 'post' ) {
	global $wpdb;

	// Bail if nothing passed
	if ( empty( $parent_id ) )
		return false;

	// The ID of the cached query
	$cache_id    = 'dps_parent_' . $parent_id . '_type_' . $post_type . '_child_last_id';
	$post_status = array( dps_get_public_status_id() );

	// Add closed status if topic post type
	if ( $post_type == dps_get_topic_post_type() )
		$post_status[] = dps_get_closed_status_id();

	// Join post statuses together
	$post_status = "'" . join( "', '", $post_status ) . "'";

	// Check for cache and set if needed
	$child_id = wp_cache_get( $cache_id, 'showcase_posts' );
	if ( empty( $child_id ) ) {
		$child_id = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_parent = %d AND post_status IN ( {$post_status} ) AND post_type = '%s' ORDER BY ID DESC LIMIT 1;", $parent_id, $post_type ) );
		wp_cache_set( $cache_id, $child_id, 'showcase_posts' );
	}

	// Filter and return
	return apply_filters( 'dps_get_public_child_last_id', (int) $child_id, (int) $parent_id, $post_type );
}

/**
 * Query the DB and get a count of public children
 *
 * @param int $parent_id Parent id
 * @param string $post_type Post type. Defaults to 'post'
 * @uses dps_get_topic_post_type() To get the topic post type
 * @uses wp_cache_get() To check if there is a cache of the children count
 * @uses wpdb::prepare() To prepare the query
 * @uses wpdb::get_var() To get the result of the query in a variable
 * @uses wp_cache_set() To set the cache for future use
 * @uses apply_filters() Calls 'dps_get_public_child_count' with the child
 *                        count, parent id and post type
 * @return int The number of children
 */
function dps_get_public_child_count( $parent_id = 0, $post_type = 'post' ) {
	global $wpdb;

	// Bail if nothing passed
	if ( empty( $parent_id ) )
		return false;

	// The ID of the cached query
	$cache_id    = 'dps_parent_' . $parent_id . '_type_' . $post_type . '_child_count';
	$post_status = array( dps_get_public_status_id() );

	// Add closed status if topic post type
	if ( $post_type == dps_get_topic_post_type() )
		$post_status[] = dps_get_closed_status_id();

	// Join post statuses together
	$post_status = "'" . join( "', '", $post_status ) . "'";

	// Check for cache and set if needed
	$child_count = wp_cache_get( $cache_id, 'showcase_posts' );
	if ( empty( $child_count ) ) {
		$child_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM {$wpdb->posts} WHERE post_parent = %d AND post_status IN ( {$post_status} ) AND post_type = '%s';", $parent_id, $post_type ) );
		wp_cache_set( $cache_id, $child_count, 'showcase_posts' );
	}

	// Filter and return
	return apply_filters( 'dps_get_public_child_count', (int) $child_count, (int) $parent_id, $post_type );
}

/**
 * Query the DB and get a the child id's of public children
 *
 * @param int $parent_id Parent id
 * @param string $post_type Post type. Defaults to 'post'
 * @uses dps_get_topic_post_type() To get the topic post type
 * @uses wp_cache_get() To check if there is a cache of the children
 * @uses wpdb::prepare() To prepare the query
 * @uses wpdb::get_col() To get the result of the query in an array
 * @uses wp_cache_set() To set the cache for future use
 * @uses apply_filters() Calls 'dps_get_public_child_ids' with the child ids,
 *                        parent id and post type
 * @return array The array of children
 */
function dps_get_public_child_ids( $parent_id = 0, $post_type = 'post' ) {
	global $wpdb;

	// Bail if nothing passed
	if ( empty( $parent_id ) )
		return false;

	// The ID of the cached query
	$cache_id    = 'dps_parent_public_' . $parent_id . '_type_' . $post_type . '_child_ids';
	$post_status = array( dps_get_public_status_id() );

	// Add closed status if topic post type
	if ( $post_type == dps_get_topic_post_type() )
		$post_status[] = dps_get_closed_status_id();

	// Join post statuses together
	$post_status = "'" . join( "', '", $post_status ) . "'";

	// Check for cache and set if needed
	$child_ids = wp_cache_get( $cache_id, 'showcase_posts' );
	if ( empty( $child_ids ) ) {
		$child_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_parent = %d AND post_status IN ( {$post_status} ) AND post_type = '%s' ORDER BY ID DESC;", $parent_id, $post_type ) );
		wp_cache_set( $cache_id, $child_ids, 'showcase_posts' );
	}

	// Filter and return
	return apply_filters( 'dps_get_public_child_ids', $child_ids, (int) $parent_id, $post_type );
}
/**
 * Query the DB and get a the child id's of all children
 *
 * @param int $parent_id Parent id
 * @param string $post_type Post type. Defaults to 'post'
 * @uses dps_get_topic_post_type() To get the topic post type
 * @uses wp_cache_get() To check if there is a cache of the children
 * @uses wpdb::prepare() To prepare the query
 * @uses wpdb::get_col() To get the result of the query in an array
 * @uses wp_cache_set() To set the cache for future use
 * @uses apply_filters() Calls 'dps_get_public_child_ids' with the child ids,
 *                        parent id and post type
 * @return array The array of children
 */
function dps_get_all_child_ids( $parent_id = 0, $post_type = 'post' ) {
	global $wpdb;

	// Bail if nothing passed
	if ( empty( $parent_id ) )
		return false;

	// The ID of the cached query
	$cache_id    = 'dps_parent_all_' . $parent_id . '_type_' . $post_type . '_child_ids';
	$post_status = array( dps_get_public_status_id() );

	// Extra post statuses based on post type
	switch ( $post_type ) {

		// Forum
		case dps_get_showcase_post_type() :
			$post_status[] = dps_get_private_status_id();
			$post_status[] = dps_get_hidden_status_id();
			break;

		// Topic
		case dps_get_topic_post_type() :
			$post_status[] = dps_get_closed_status_id();
			$post_status[] = dps_get_trash_status_id();
			$post_status[] = dps_get_spam_status_id();
			break;

		// Reply
		case dps_get_reply_post_type() :
			$post_status[] = dps_get_trash_status_id();
			$post_status[] = dps_get_spam_status_id();
			break;
	}

	// Join post statuses together
	$post_status = "'" . join( "', '", $post_status ) . "'";

	// Check for cache and set if needed
	$child_ids = wp_cache_get( $cache_id, 'showcase_posts' );
	if ( empty( $child_ids ) ) {
		$child_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_parent = %d AND post_status IN ( {$post_status} ) AND post_type = '%s' ORDER BY ID DESC;", $parent_id, $post_type ) );
		wp_cache_set( $cache_id, $child_ids, 'showcase_posts' );
	}

	// Filter and return
	return apply_filters( 'dps_get_all_child_ids', $child_ids, (int) $parent_id, $post_type );
}

/** Globals *******************************************************************/

/**
 * Get the unfiltered value of a global $post's key
 *
 * Used most frequently when editing a forum/topic/reply
 *
 * @since Showcase (1.0)
 *
 * @global WP_Query $post
 * @param string $field Name of the key
 * @param string $context How to sanitize - raw|edit|db|display|attribute|js
 * @return string Field value
 */
function dps_get_global_post_field( $field = 'ID', $context = 'edit' ) {
	global $post;

	$retval = isset( $post->$field ) ? $post->$field : '';
	$retval = sanitize_post_field( $field, $retval, $post->ID, $context );

	return apply_filters( 'dps_get_global_post_field', $retval, $post );
}

/** Nonces ********************************************************************/

/**
 * Makes sure the user requested an action from another page on this site.
 *
 * To avoid security exploits within the theme.
 *
 * @since Showcase (1.0)
 *
 * @uses do_action() Calls 'dps_check_referer' on $action.
 * @param string $action Action nonce
 * @param string $query_arg where to look for nonce in $_REQUEST
 */
function dps_verify_nonce_request( $action = '', $query_arg = '_wpnonce' ) {

	// Parse home_url() into pieces to remove query-strings, strange characters,
	// and other funny things that plugins might to do to it.
	$parsed_home   = parse_url( home_url( '/', ( is_ssl() ? 'https://' : 'http://' ) ) );
	$home_url      = trim( strtolower( $parsed_home['scheme'] . '://' . $parsed_home['host'] . $parsed_home['path'] ), '/' );

	// Build the currently requested URL
	$scheme        = is_ssl() ? 'https://' : 'http://';
	$requested_url = strtolower( $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

	// Filter the requested URL, for configurations like reverse proxying
	$matched_url   = apply_filters( 'dps_verify_nonce_request_url', $requested_url );

	// Check the nonce
	$result = isset( $_REQUEST[$query_arg] ) ? wp_verify_nonce( $_REQUEST[$query_arg], $action ) : false;

	// Nonce check failed
	if ( empty( $result ) || empty( $action ) || ( strpos( $matched_url, $home_url ) !== 0 ) )
		$result = false;

	// Do extra things
	do_action( 'dps_verify_nonce_request', $action, $result );

	return $result;
}

/** Templates ******************************************************************/

/**
 * Used to guess if page exists at requested path
 *
 * @since Showcase (1.0)
 *
 * @uses get_option() To see if pretty permalinks are enabled
 * @uses get_page_by_path() To see if page exists at path
 *
 * @param string $path
 * @return mixed False if no page, Page object if true
 */
function dps_get_page_by_path( $path = '' ) {

	// Default to false
	$retval = false;

	// Path is not empty
	if ( !empty( $path ) ) {

		// Pretty permalinks are on so path might exist
		if ( get_option( 'permalink_structure' ) ) {
			$retval = get_page_by_path( $path );
		}
	}

	return apply_filters( 'dps_get_page_by_path', $retval, $path );
}

/**
 * Sets the 404 status.
 *
 * Used primarily with topics/replies inside hidden forums.
 *
 * @since Showcase (1.0)
 *
 * @global WP_Query $wp_query
 * @uses WP_Query::set_404()
 */
function dps_set_404() {
	global $wp_query;

	if ( ! isset( $wp_query ) ) {
		_doing_it_wrong( __FUNCTION__, __( 'Conditional query tags do not work before the query is run. Before then, they always return false.', 'dps' ), '3.1' );
		return false;
	}

	$wp_query->set_404();
}
