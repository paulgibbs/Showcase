<?php

/**
 * Showcase Forum Template Tags
 *
 * @package Showcase
 * @subpackage TemplateTags
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Post Type *****************************************************************/

/**
 * Output the unique id of the custom post type for forums
 *
 * @since Showcase (1.0)
 */
function dps_showcase_post_type() {
	echo dps_get_showcase_post_type();
}
	/**
	 * Return the unique id of the custom post type for forums
	 *
	 * @since Showcase (1.0)
	 * @return string The unique forum post type id
	 */
	function dps_get_showcase_post_type() {
		return apply_filters( 'dps_get_showcase_post_type', showcase()->showcase_post_type );
	}

/** Forum Loop ****************************************************************/

/**
 * The main forum loop.
 *
 * WordPress makes this easy for us.
 *
 * @since Showcase (1.0)
 * @param mixed $args All the arguments supported by {@link WP_Query}
 * @return object Multidimensional array of forum information
 */
function dps_has_forums( $args = '' ) {

	// Parse arguments with default forum query for most circumstances
	$dps_f = dps_parse_args( $args, array(
		'post_type'      => dps_get_showcase_post_type(),
		'post_parent'    => dps_is_forum_archive() ? 0 : dps_get_forum_id() ,
		'post_status'    => dps_get_public_status_id(),
		'posts_per_page' => get_option( '_dps_forums_per_page', 50 ),
		'orderby'        => 'menu_order',
		'order'          => 'ASC'
	), 'has_forums' );

	// Run the query
	$bbp              = showcase();
	$bbp->forum_query = new WP_Query( $dps_f );

	return apply_filters( 'dps_has_forums', $bbp->forum_query->have_posts(), $bbp->forum_query );
}

/**
 * Whether there are more forums available in the loop
 *
 * @since Showcase (1.0)
 *
 * @uses showcase:forum_query::have_posts() To check if there are more forums
 *                                          available
 * @return object Forum information
 */
function dps_forums() {

	// Put into variable to check against next
	$have_posts = showcase()->forum_query->have_posts();

	// Reset the post data when finished
	if ( empty( $have_posts ) )
		wp_reset_postdata();

	return $have_posts;
}

/**
 * Loads up the current forum in the loop
 *
 * @since Showcase (1.0)
 *
 * @uses showcase:forum_query::the_post() To get the current forum
 * @return object Forum information
 */
function dps_the_forum() {
	return showcase()->forum_query->the_post();
}

/** Forum *********************************************************************/

/**
 * Output forum id
 *
 * @since Showcase (1.0)
 *
 * @param $forum_id Optional. Used to check emptiness
 * @uses dps_get_forum_id() To get the forum id
 */
function dps_forum_id( $forum_id = 0 ) {
	echo dps_get_forum_id( $forum_id );
}
	/**
	 * Return the forum id
	 *
	 * @since Showcase (1.0)
	 *
	 * @param $forum_id Optional. Used to check emptiness
	 * @uses Showcase::forum_query::in_the_loop To check if we're in the loop
	 * @uses Showcase::forum_query::post::ID To get the forum id
	 * @uses WP_Query::post::ID To get the forum id
	 * @uses dps_is_forum() To check if the search result is a forum
	 * @uses dps_is_single_forum() To check if it's a forum page
	 * @uses dps_is_single_topic() To check if it's a topic page
	 * @uses dps_get_topic_forum_id() To get the topic forum id
	 * @uses get_post_field() To get the post's post type
	 * @uses apply_filters() Calls 'dps_get_forum_id' with the forum id and
	 *                        supplied forum id
	 * @return int The forum id
	 */
	function dps_get_forum_id( $forum_id = 0 ) {
		global $wp_query;

		$bbp = showcase();

		// Easy empty checking
		if ( !empty( $forum_id ) && is_numeric( $forum_id ) ) {
			$dps_forum_id = $forum_id;

		// Currently inside a forum loop
		} elseif ( !empty( $bbp->forum_query->in_the_loop ) && isset( $bbp->forum_query->post->ID ) ) {
			$dps_forum_id = $bbp->forum_query->post->ID;

		// Currently inside a search loop
		} elseif ( !empty( $bbp->search_query->in_the_loop ) && isset( $bbp->search_query->post->ID ) && dps_is_forum( $bbp->search_query->post->ID ) ) {
			$dps_forum_id = $bbp->search_query->post->ID;

		// Currently viewing a forum
		} elseif ( dps_is_single_forum() && !empty( $bbp->current_forum_id ) ) {
			$dps_forum_id = $bbp->current_forum_id;

		// Currently viewing a forum
		} elseif ( dps_is_single_forum() && isset( $wp_query->post->ID ) ) {
			$dps_forum_id = $wp_query->post->ID;

		// Currently viewing a topic
		} elseif ( dps_is_single_topic() ) {
			$dps_forum_id = dps_get_topic_forum_id();

		// Fallback
		} else {
			$dps_forum_id = 0;
		}

		return (int) apply_filters( 'dps_get_forum_id', (int) $dps_forum_id, $forum_id );
	}

/**
 * Gets a forum
 *
 * @since Showcase (1.0)
 *
 * @param int|object $forum forum id or forum object
 * @param string $output Optional. OBJECT, ARRAY_A, or ARRAY_N. Default = OBJECT
 * @param string $filter Optional Sanitation filter. See {@link sanitize_post()}
 * @uses get_post() To get the forum
 * @uses apply_filters() Calls 'dps_get_forum' with the forum, output type and
 *                        sanitation filter
 * @return mixed Null if error or forum (in specified form) if success
 */
function dps_get_forum( $forum, $output = OBJECT, $filter = 'raw' ) {

	// Use forum ID
	if ( empty( $forum ) || is_numeric( $forum ) )
		$forum = dps_get_forum_id( $forum );

	// Attempt to load the forum
	$forum = get_post( $forum, OBJECT, $filter );
	if ( empty( $forum ) )
		return $forum;

	// Bail if post_type is not a forum
	if ( $forum->post_type !== dps_get_showcase_post_type() )
		return null;

	// Tweak the data type to return
	if ( $output == OBJECT ) {
		return $forum;

	} elseif ( $output == ARRAY_A ) {
		$_forum = get_object_vars( $forum );
		return $_forum;

	} elseif ( $output == ARRAY_N ) {
		$_forum = array_values( get_object_vars( $forum ) );
		return $_forum;

	}

	return apply_filters( 'dps_get_forum', $forum, $output, $filter );
}

/**
 * Output the link to the forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_permalink() To get the permalink
 */
function dps_forum_permalink( $forum_id = 0 ) {
	echo dps_get_forum_permalink( $forum_id );
}
	/**
	 * Return the link to the forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @param $string $redirect_to Optional. Pass a redirect value for use with
	 *                              shortcodes and other fun things.
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses get_permalink() Get the permalink of the forum
	 * @uses apply_filters() Calls 'dps_get_forum_permalink' with the forum
	 *                        link
	 * @return string Permanent link to forum
	 */
	function dps_get_forum_permalink( $forum_id = 0, $redirect_to = '' ) {
		$forum_id = dps_get_forum_id( $forum_id );

		// Use the redirect address
		if ( !empty( $redirect_to ) ) {
			$forum_permalink = esc_url_raw( $redirect_to );

		// Use the topic permalink
		} else {
			$forum_permalink = get_permalink( $forum_id );
		}

		return apply_filters( 'dps_get_forum_permalink', $forum_permalink, $forum_id );
	}

/**
 * Output the title of the forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_title() To get the forum title
 */
function dps_forum_title( $forum_id = 0 ) {
	echo dps_get_forum_title( $forum_id );
}
	/**
	 * Return the title of the forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses get_the_title() To get the forum title
	 * @uses apply_filters() Calls 'dps_get_forum_title' with the title
	 * @return string Title of forum
	 */
	function dps_get_forum_title( $forum_id = 0 ) {
		$forum_id = dps_get_forum_id( $forum_id );
		$title    = get_the_title( $forum_id );

		return apply_filters( 'dps_get_forum_title', $title, $forum_id );
	}

/**
 * Output the forum archive title
 *
 * @since Showcase (1.0)
 *
 * @param string $title Default text to use as title
 */
function dps_forum_archive_title( $title = '' ) {
	echo dps_get_forum_archive_title( $title );
}
	/**
	 * Return the forum archive title
	 *
	 * @since Showcase (1.0)
	 * @return string The forum archive title
	 */
	function dps_get_forum_archive_title( $title = '' ) {

		// If no title was passed
		if ( empty( $title ) ) {

			// Set root text to page title
			$page = dps_get_page_by_path( dps_get_root_slug() );
			if ( !empty( $page ) ) {
				$title = get_the_title( $page->ID );

			// Default to forum post type name label
			} else {
				$fto    = get_post_type_object( dps_get_showcase_post_type() );
				$title  = $fto->labels->name;
			}
		}

		return apply_filters( 'dps_get_forum_archive_title', $title );
	}

/**
 * Output the content of the forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Topic id
 * @uses dps_get_forum_content() To get the forum content
 */
function dps_forum_content( $forum_id = 0 ) {
	echo dps_get_forum_content( $forum_id );
}
	/**
	 * Return the content of the forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Topic id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses post_password_required() To check if the forum requires pass
	 * @uses get_the_password_form() To get the password form
	 * @uses get_post_field() To get the content post field
	 * @uses apply_filters() Calls 'dps_get_forum_content' with the content
	 *                        and forum id
	 * @return string Content of the forum
	 */
	function dps_get_forum_content( $forum_id = 0 ) {
		$forum_id = dps_get_forum_id( $forum_id );

		// Check if password is required
		if ( post_password_required( $forum_id ) )
			return get_the_password_form();

		$content = get_post_field( 'post_content', $forum_id );

		return apply_filters( 'dps_get_forum_content', $content, $forum_id );
	}

/**
 * Allow forum rows to have adminstrative actions
 *
 * @since Showcase (1.0)
 * @uses do_action()
 * @todo Links and filter
 */
function dps_forum_row_actions() {
	do_action( 'dps_forum_row_actions' );
}

/**
 * Output the forums last active ID
 *
 * @since Showcase (1.0)
 *
 * @uses dps_get_forum_last_active_id() To get the forum's last active id
 * @param int $forum_id Optional. Forum id
 */
function dps_forum_last_active_id( $forum_id = 0 ) {
	echo dps_get_forum_last_active_id( $forum_id );
}
	/**
	 * Return the forums last active ID
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses get_post_meta() To get the forum's last active id
	 * @uses apply_filters() Calls 'dps_get_forum_last_active_id' with
	 *                        the last active id and forum id
	 * @return int Forum's last active id
	 */
	function dps_get_forum_last_active_id( $forum_id = 0 ) {
		$forum_id  = dps_get_forum_id( $forum_id );
		$active_id = get_post_meta( $forum_id, '_dps_last_active_id', true );

		return (int) apply_filters( 'dps_get_forum_last_active_id', (int) $active_id, $forum_id );
	}

/**
 * Output the forums last update date/time (aka freshness)
 *
 * @since Showcase (1.0)
 *
 * @uses dps_get_forum_last_active_time() To get the forum freshness
 * @param int $forum_id Optional. Forum id
 */
function dps_forum_last_active_time( $forum_id = 0 ) {
	echo dps_get_forum_last_active_time( $forum_id );
}
	/**
	 * Return the forums last update date/time (aka freshness)
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses get_post_meta() To retrieve forum last active meta
	 * @uses dps_get_forum_last_reply_id() To get forum's last reply id
	 * @uses get_post_field() To get the post date of the reply
	 * @uses dps_get_forum_last_topic_id() To get forum's last topic id
	 * @uses dps_get_topic_last_active_time() To get time when the topic was
	 *                                    last active
	 * @uses dps_convert_date() To convert the date
	 * @uses dps_get_time_since() To get time in since format
	 * @uses apply_filters() Calls 'dps_get_forum_last_active' with last
	 *                        active time and forum id
	 * @return string Forum last update date/time (freshness)
	 */
	function dps_get_forum_last_active_time( $forum_id = 0 ) {

		// Verify forum and get last active meta
		$forum_id    = dps_get_forum_id( $forum_id );
		$last_active = get_post_meta( $forum_id, '_dps_last_active_time', true );

		if ( empty( $last_active ) ) {
			$reply_id = dps_get_forum_last_reply_id( $forum_id );
			if ( !empty( $reply_id ) ) {
				$last_active = get_post_field( 'post_date', $reply_id );
			} else {
				$topic_id = dps_get_forum_last_topic_id( $forum_id );
				if ( !empty( $topic_id ) ) {
					$last_active = dps_get_topic_last_active_time( $topic_id );
				}
			}
		}

		$active_time = !empty( $last_active ) ? dps_get_time_since( dps_convert_date( $last_active ) ) : '';

		return apply_filters( 'dps_get_forum_last_active', $active_time, $forum_id );
	}

/**
 * Output link to the most recent activity inside a forum.
 *
 * Outputs a complete link with attributes and content.
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_freshness_link() To get the forum freshness link
 */
function dps_forum_freshness_link( $forum_id = 0) {
	echo dps_get_forum_freshness_link( $forum_id );
}
	/**
	 * Returns link to the most recent activity inside a forum.
	 *
	 * Returns a complete link with attributes and content.
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses dps_get_forum_last_active_id() To get the forum last active id
	 * @uses dps_get_forum_last_reply_id() To get the forum last reply id
	 * @uses dps_get_forum_last_topic_id() To get the forum last topic id
	 * @uses dps_get_forum_last_reply_url() To get the forum last reply url
	 * @uses dps_get_forum_last_reply_title() To get the forum last reply
	 *                                         title
	 * @uses dps_get_forum_last_topic_permalink() To get the forum last
	 *                                             topic permalink
	 * @uses dps_get_forum_last_topic_title() To get the forum last topic
	 *                                         title
	 * @uses dps_get_forum_last_active_time() To get the time when the forum
	 *                                         was last active
	 * @uses apply_filters() Calls 'dps_get_forum_freshness_link' with the
	 *                        link and forum id
	 */
	function dps_get_forum_freshness_link( $forum_id = 0 ) {
		$forum_id  = dps_get_forum_id( $forum_id );
		$active_id = dps_get_forum_last_active_id( $forum_id );
		$link_url  = $title = '';

		if ( empty( $active_id ) )
			$active_id = dps_get_forum_last_reply_id( $forum_id );

		if ( empty( $active_id ) )
			$active_id = dps_get_forum_last_topic_id( $forum_id );

		if ( dps_is_topic( $active_id ) ) {
			$link_url = dps_get_forum_last_topic_permalink( $forum_id );
			$title    = dps_get_forum_last_topic_title( $forum_id );
		} elseif ( dps_is_reply( $active_id ) ) {
			$link_url = dps_get_forum_last_reply_url( $forum_id );
			$title    = dps_get_forum_last_reply_title( $forum_id );
		}

		$time_since = dps_get_forum_last_active_time( $forum_id );

		if ( !empty( $time_since ) && !empty( $link_url ) )
			$anchor = '<a href="' . $link_url . '" title="' . esc_attr( $title ) . '">' . $time_since . '</a>';
		else
			$anchor = __( 'No Topics', 'dps' );

		return apply_filters( 'dps_get_forum_freshness_link', $anchor, $forum_id, $time_since, $link_url, $title, $active_id );
	}

/**
 * Output parent ID of a forum, if exists
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Forum ID
 * @uses dps_get_forum_parent_id() To get the forum's parent ID
 */
function dps_forum_parent_id( $forum_id = 0 ) {
	echo dps_get_forum_parent_id( $forum_id );
}
	/**
	 * Return ID of forum parent, if exists
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses get_post_field() To get the forum parent
	 * @uses apply_filters() Calls 'dps_get_forum_parent' with the parent & forum id
	 * @return int Forum parent
	 */
	function dps_get_forum_parent_id( $forum_id = 0 ) {
		$forum_id  = dps_get_forum_id( $forum_id );
		$parent_id = get_post_field( 'post_parent', $forum_id );

		return (int) apply_filters( 'dps_get_forum_parent_id', (int) $parent_id, $forum_id );
	}

/**
 * Return array of parent forums
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_id() To get the forum id
 * @uses dps_get_forum() To get the forum
 * @uses apply_filters() Calls 'dps_get_forum_ancestors' with the ancestors
 *                        and forum id
 * @return array Forum ancestors
 */
function dps_get_forum_ancestors( $forum_id = 0 ) {
	$forum_id  = dps_get_forum_id( $forum_id );
	$ancestors = array();
	$forum     = dps_get_forum( $forum_id );

	if ( !empty( $forum ) ) {
		while ( 0 !== (int) $forum->post_parent ) {
			$ancestors[] = $forum->post_parent;
			$forum       = dps_get_forum( $forum->post_parent );
		}
	}

	return apply_filters( 'dps_get_forum_ancestors', $ancestors, $forum_id );
}

/**
 * Return subforums of given forum
 *
 * @since Showcase (1.0)
 *
 * @param mixed $args All the arguments supported by {@link WP_Query}
 * @uses dps_get_forum_id() To get the forum id
 * @uses current_user_can() To check if the current user is capable of
 *                           reading private forums
 * @uses get_posts() To get the subforums
 * @uses apply_filters() Calls 'dps_forum_get_subforums' with the subforums
 *                        and the args
 * @return mixed false if none, array of subs if yes
 */
function dps_forum_get_subforums( $args = '' ) {

	// Use passed integer as post_parent
	if ( is_numeric( $args ) )
		$args = array( 'post_parent' => $args );

	// Setup possible post__not_in array
	$post_stati[] = dps_get_public_status_id();

	// Super admin get whitelisted post statuses
	if ( dps_is_user_keymaster() ) {
		$post_stati = array( dps_get_public_status_id(), dps_get_private_status_id(), dps_get_hidden_status_id() );

	// Not a keymaster, so check caps
	} else {

		// Check if user can read private forums
		if ( current_user_can( 'read_private_forums' ) ) {
			$post_stati[] = dps_get_private_status_id();
		}

		// Check if user can read hidden forums
		if ( current_user_can( 'read_hidden_forums' ) ) {
			$post_stati[] = dps_get_hidden_status_id();
		}
	}

	// Parse arguments against default values
	$r = dps_parse_args( $args, array(
		'post_parent'         => 0,
		'post_type'           => dps_get_showcase_post_type(),
		'post_status'         => implode( ',', $post_stati ),
		'posts_per_page'      => get_option( '_dps_forums_per_page', 50 ),
		'orderby'             => 'menu_order',
		'order'               => 'ASC',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true
	), 'forum_get_subforums' );
	$r['post_parent'] = dps_get_forum_id( $r['post_parent'] );

	// Create a new query for the subforums
	$get_posts = new WP_Query();

	// No forum passed
	$sub_forums = !empty( $r['post_parent'] ) ? $get_posts->query( $r ) : array();

	return (array) apply_filters( 'dps_forum_get_subforums', $sub_forums, $r );
}

/**
 * Output a list of forums (can be used to list subforums)
 *
 * @param mixed $args The function supports these args:
 *  - before: To put before the output. Defaults to '<ul class="bbp-forums">'
 *  - after: To put after the output. Defaults to '</ul>'
 *  - link_before: To put before every link. Defaults to '<li class="bbp-forum">'
 *  - link_after: To put after every link. Defaults to '</li>'
 *  - separator: Separator. Defaults to ', '
 *  - forum_id: Forum id. Defaults to ''
 *  - show_topic_count - To show forum topic count or not. Defaults to true
 *  - show_reply_count - To show forum reply count or not. Defaults to true
 * @uses dps_forum_get_subforums() To check if the forum has subforums or not
 * @uses dps_get_forum_permalink() To get forum permalink
 * @uses dps_get_forum_title() To get forum title
 * @uses dps_is_forum_category() To check if a forum is a category
 * @uses dps_get_forum_topic_count() To get forum topic count
 * @uses dps_get_forum_reply_count() To get forum reply count
 */
function dps_list_forums( $args = '' ) {

	// Define used variables
	$output = $sub_forums = $topic_count = $reply_count = $counts = '';
	$i = 0;
	$count = array();

	// Parse arguments against default values
	$r = dps_parse_args( $args, array(
		'before'            => '<ul class="bbp-forums-list">',
		'after'             => '</ul>',
		'link_before'       => '<li class="bbp-forum">',
		'link_after'        => '</li>',
		'count_before'      => ' (',
		'count_after'       => ')',
		'count_sep'         => ', ',
		'separator'         => ', ',
		'forum_id'          => '',
		'show_topic_count'  => true,
		'show_reply_count'  => true,
	), 'list_forums' );

	// Loop through forums and create a list
	$sub_forums = dps_forum_get_subforums( $r['forum_id'] );
	if ( !empty( $sub_forums ) ) {

		// Total count (for separator)
		$total_subs = count( $sub_forums );
		foreach ( $sub_forums as $sub_forum ) {
			$i++; // Separator count

			// Get forum details
			$count     = array();
			$show_sep  = $total_subs > $i ? $r['separator'] : '';
			$permalink = dps_get_forum_permalink( $sub_forum->ID );
			$title     = dps_get_forum_title( $sub_forum->ID );

			// Show topic count
			if ( !empty( $r['show_topic_count'] ) && !dps_is_forum_category( $sub_forum->ID ) ) {
				$count['topic'] = dps_get_forum_topic_count( $sub_forum->ID );
			}

			// Show reply count
			if ( !empty( $r['show_reply_count'] ) && !dps_is_forum_category( $sub_forum->ID ) ) {
				$count['reply'] = dps_get_forum_reply_count( $sub_forum->ID );
			}

			// Counts to show
			if ( !empty( $count ) ) {
				$counts = $r['count_before'] . implode( $r['count_sep'], $count ) . $r['count_after'];
			}

			// Build this sub forums link
			$output .= $r['link_before'] . '<a href="' . $permalink . '" class="bbp-forum-link">' . $title . $counts . '</a>' . $show_sep . $r['link_after'];
		}

		// Output the list
		echo apply_filters( 'dps_list_forums', $r['before'] . $output . $r['after'], $r );
	}
}

/** Forum Last Topic **********************************************************/

/**
 * Output the forum's last topic id
 *
 * @since Showcase (1.0)
 *
 * @uses dps_get_forum_last_topic_id() To get the forum's last topic id
 * @param int $forum_id Optional. Forum id
 */
function dps_forum_last_topic_id( $forum_id = 0 ) {
	echo dps_get_forum_last_topic_id( $forum_id );
}
	/**
	 * Return the forum's last topic id
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses get_post_meta() To get the forum's last topic id
	 * @uses apply_filters() Calls 'dps_get_forum_last_topic_id' with the
	 *                        forum and topic id
	 * @return int Forum's last topic id
	 */
	function dps_get_forum_last_topic_id( $forum_id = 0 ) {
		$forum_id = dps_get_forum_id( $forum_id );
		$topic_id = get_post_meta( $forum_id, '_dps_last_topic_id', true );

		return (int) apply_filters( 'dps_get_forum_last_topic_id', (int) $topic_id, $forum_id );
	}

/**
 * Output the title of the last topic inside a forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_last_topic_title() To get the forum's last topic's title
 */
function dps_forum_last_topic_title( $forum_id = 0 ) {
	echo dps_get_forum_last_topic_title( $forum_id );
}
	/**
	 * Return the title of the last topic inside a forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses dps_get_forum_last_topic_id() To get the forum's last topic id
	 * @uses dps_get_topic_title() To get the topic's title
	 * @uses apply_filters() Calls 'dps_get_forum_last_topic_title' with the
	 *                        topic title and forum id
	 * @return string Forum's last topic's title
	 */
	function dps_get_forum_last_topic_title( $forum_id = 0 ) {
		$forum_id = dps_get_forum_id( $forum_id );
		$topic_id = dps_get_forum_last_topic_id( $forum_id );
		$title    = !empty( $topic_id ) ? dps_get_topic_title( $topic_id ) : '';

		return apply_filters( 'dps_get_forum_last_topic_title', $title, $forum_id );
	}

/**
 * Output the link to the last topic in a forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_last_topic_permalink() To get the forum's last topic's
 *                                             permanent link
 */
function dps_forum_last_topic_permalink( $forum_id = 0 ) {
	echo dps_get_forum_last_topic_permalink( $forum_id );
}
	/**
	 * Return the link to the last topic in a forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses dps_get_forum_last_topic_id() To get the forum's last topic id
	 * @uses dps_get_topic_permalink() To get the topic's permalink
	 * @uses apply_filters() Calls 'dps_get_forum_last_topic_permalink' with
	 *                        the topic link and forum id
	 * @return string Permanent link to topic
	 */
	function dps_get_forum_last_topic_permalink( $forum_id = 0 ) {
		$forum_id = dps_get_forum_id( $forum_id );
		return apply_filters( 'dps_get_forum_last_topic_permalink', dps_get_topic_permalink( dps_get_forum_last_topic_id( $forum_id ) ), $forum_id );
	}

/**
 * Return the author ID of the last topic of a forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_id() To get the forum id
 * @uses dps_get_forum_last_topic_id() To get the forum's last topic id
 * @uses dps_get_topic_author_id() To get the topic's author id
 * @uses apply_filters() Calls 'dps_get_forum_last_topic_author' with the author
 *                        id and forum id
 * @return int Forum's last topic's author id
 */
function dps_get_forum_last_topic_author_id( $forum_id = 0 ) {
	$forum_id  = dps_get_forum_id( $forum_id );
	$author_id = dps_get_topic_author_id( dps_get_forum_last_topic_id( $forum_id ) );
	return (int) apply_filters( 'dps_get_forum_last_topic_author_id', (int) $author_id, $forum_id );
}

/**
 * Output link to author of last topic of forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_last_topic_author_link() To get the forum's last topic's
 *                                               author link
 */
function dps_forum_last_topic_author_link( $forum_id = 0 ) {
	echo dps_get_forum_last_topic_author_link( $forum_id );
}
	/**
	 * Return link to author of last topic of forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses dps_get_forum_last_topic_author_id() To get the forum's last
	 *                                             topic's author id
	 * @uses dps_get_user_profile_link() To get the author's profile link
	 * @uses apply_filters() Calls 'dps_get_forum_last_topic_author_link'
	 *                        with the author link and forum id
	 * @return string Forum's last topic's author link
	 */
	function dps_get_forum_last_topic_author_link( $forum_id = 0 ) {
		$forum_id    = dps_get_forum_id( $forum_id );
		$author_id   = dps_get_forum_last_topic_author_id( $forum_id );
		$author_link = dps_get_user_profile_link( $author_id );
		return apply_filters( 'dps_get_forum_last_topic_author_link', $author_link, $forum_id );
	}

/** Forum Last Reply **********************************************************/

/**
 * Output the forums last reply id
 *
 * @since Showcase (1.0)
 *
 * @uses dps_get_forum_last_reply_id() To get the forum's last reply id
 * @param int $forum_id Optional. Forum id
 */
function dps_forum_last_reply_id( $forum_id = 0 ) {
	echo dps_get_forum_last_reply_id( $forum_id );
}
	/**
	 * Return the forums last reply id
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses get_post_meta() To get the forum's last reply id
	 * @uses dps_get_forum_last_topic_id() To get the forum's last topic id
	 * @uses apply_filters() Calls 'dps_get_forum_last_reply_id' with
	 *                        the last reply id and forum id
	 * @return int Forum's last reply id
	 */
	function dps_get_forum_last_reply_id( $forum_id = 0 ) {
		$forum_id = dps_get_forum_id( $forum_id );
		$reply_id = get_post_meta( $forum_id, '_dps_last_reply_id', true );

		if ( empty( $reply_id ) )
			$reply_id = dps_get_forum_last_topic_id( $forum_id );

		return (int) apply_filters( 'dps_get_forum_last_reply_id', (int) $reply_id, $forum_id );
	}

/**
 * Output the title of the last reply inside a forum
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_last_reply_title() To get the forum's last reply's title
 */
function dps_forum_last_reply_title( $forum_id = 0 ) {
	echo dps_get_forum_last_reply_title( $forum_id );
}
	/**
	 * Return the title of the last reply inside a forum
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses dps_get_forum_last_reply_id() To get the forum's last reply id
	 * @uses dps_get_reply_title() To get the reply title
	 * @uses apply_filters() Calls 'dps_get_forum_last_reply_title' with the
	 *                        reply title and forum id
	 * @return string
	 */
	function dps_get_forum_last_reply_title( $forum_id = 0 ) {
		$forum_id = dps_get_forum_id( $forum_id );
		return apply_filters( 'dps_get_forum_last_reply_title', dps_get_reply_title( dps_get_forum_last_reply_id( $forum_id ) ), $forum_id );
	}

/**
 * Output the link to the last reply in a forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_last_reply_permalink() To get the forum last reply link
 */
function dps_forum_last_reply_permalink( $forum_id = 0 ) {
	echo dps_get_forum_last_reply_permalink( $forum_id );
}
	/**
	 * Return the link to the last reply in a forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses dps_get_forum_last_reply_id() To get the forum's last reply id
	 * @uses dps_get_reply_permalink() To get the reply permalink
	 * @uses apply_filters() Calls 'dps_get_forum_last_reply_permalink' with
	 *                        the reply link and forum id
	 * @return string Permanent link to the forum's last reply
	 */
	function dps_get_forum_last_reply_permalink( $forum_id = 0 ) {
		$forum_id = dps_get_forum_id( $forum_id );
		return apply_filters( 'dps_get_forum_last_reply_permalink', dps_get_reply_permalink( dps_get_forum_last_reply_id( $forum_id ) ), $forum_id );
	}

/**
 * Output the url to the last reply in a forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_last_reply_url() To get the forum last reply url
 */
function dps_forum_last_reply_url( $forum_id = 0 ) {
	echo dps_get_forum_last_reply_url( $forum_id );
}
	/**
	 * Return the url to the last reply in a forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses dps_get_forum_last_reply_id() To get the forum's last reply id
	 * @uses dps_get_reply_url() To get the reply url
	 * @uses dps_get_forum_last_topic_permalink() To get the forum's last
	 *                                             topic's permalink
	 * @uses apply_filters() Calls 'dps_get_forum_last_reply_url' with the
	 *                        reply url and forum id
	 * @return string Paginated URL to latest reply
	 */
	function dps_get_forum_last_reply_url( $forum_id = 0 ) {
		$forum_id = dps_get_forum_id( $forum_id );

		// If forum has replies, get the last reply and use its url
		$reply_id = dps_get_forum_last_reply_id( $forum_id );
		if ( !empty( $reply_id ) ) {
			$reply_url = dps_get_reply_url( $reply_id );

		// No replies, so look for topics and use last permalink
		} else {
			$reply_url = dps_get_forum_last_topic_permalink( $forum_id );

			// No topics either, so set $reply_url as empty string
			if ( empty( $reply_url ) ) {
				$reply_url = '';
			}
		}

		// Filter and return
		return apply_filters( 'dps_get_forum_last_reply_url', $reply_url, $forum_id );
	}

/**
 * Output author ID of last reply of forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_last_reply_author_id() To get the forum's last reply
 *                                             author id
 */
function dps_forum_last_reply_author_id( $forum_id = 0 ) {
	echo dps_get_forum_last_reply_author_id( $forum_id );
}
	/**
	 * Return author ID of last reply of forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses dps_get_forum_last_reply_author_id() To get the forum's last
	 *                                             reply's author id
	 * @uses dps_get_reply_author_id() To get the reply's author id
	 * @uses apply_filters() Calls 'dps_get_forum_last_reply_author_id' with
	 *                        the author id and forum id
	 * @return int Forum's last reply author id
	 */
	function dps_get_forum_last_reply_author_id( $forum_id = 0 ) {
		$forum_id  = dps_get_forum_id( $forum_id );
		$author_id = dps_get_reply_author_id( dps_get_forum_last_reply_id( $forum_id ) );
		return apply_filters( 'dps_get_forum_last_reply_author_id', $author_id, $forum_id );
	}

/**
 * Output link to author of last reply of forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_last_reply_author_link() To get the forum's last reply's
 *                                               author link
 */
function dps_forum_last_reply_author_link( $forum_id = 0 ) {
	echo dps_get_forum_last_reply_author_link( $forum_id );
}
	/**
	 * Return link to author of last reply of forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses dps_get_forum_last_reply_author_id() To get the forum's last
	 *                                             reply's author id
	 * @uses dps_get_user_profile_link() To get the reply's author's profile
	 *                                    link
	 * @uses apply_filters() Calls 'dps_get_forum_last_reply_author_link'
	 *                        with the author link and forum id
	 * @return string Link to author of last reply of forum
	 */
	function dps_get_forum_last_reply_author_link( $forum_id = 0 ) {
		$forum_id    = dps_get_forum_id( $forum_id );
		$author_id   = dps_get_forum_last_reply_author_id( $forum_id );
		$author_link = dps_get_user_profile_link( $author_id );
		return apply_filters( 'dps_get_forum_last_reply_author_link', $author_link, $forum_id );
	}

/** Forum Counts **************************************************************/

/**
 * Output the topics link of the forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Topic id
 * @uses dps_get_forum_topics_link() To get the forum topics link
 */
function dps_forum_topics_link( $forum_id = 0 ) {
	echo dps_get_forum_topics_link( $forum_id );
}

	/**
	 * Return the topics link of the forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Topic id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses dps_get_forum() To get the forum
	 * @uses dps_get_forum_topic_count() To get the forum topic count
	 * @uses dps_get_forum_permalink() To get the forum permalink
	 * @uses remove_query_arg() To remove args from the url
	 * @uses dps_get_forum_topic_count_hidden() To get the forum hidden
	 *                                           topic count
	 * @uses current_user_can() To check if the current user can edit others
	 *                           topics
	 * @uses add_query_arg() To add custom args to the url
	 * @uses apply_filters() Calls 'dps_get_forum_topics_link' with the
	 *                        topics link and forum id
	 */
	function dps_get_forum_topics_link( $forum_id = 0 ) {
		$forum    = dps_get_forum( $forum_id );
		$forum_id = $forum->ID;
		$topics   = sprintf( _n( '%s topic', '%s topics', dps_get_forum_topic_count( $forum_id, true, false ), 'showcase' ), dps_get_forum_topic_count( $forum_id ) );
		$retval   = '';

		// First link never has view=all
		if ( dps_get_view_all( 'edit_others_topics' ) )
			$retval .= "<a href='" . esc_url( dps_remove_view_all( dps_get_forum_permalink( $forum_id ) ) ) . "'>$topics</a>";
		else
			$retval .= $topics;

		// Get deleted topics
		$deleted = dps_get_forum_topic_count_hidden( $forum_id );

		// This forum has hidden topics
		if ( !empty( $deleted ) && current_user_can( 'edit_others_topics' ) ) {

			// Extra text
			$extra = sprintf( __( ' (+ %d hidden)', 'dps' ), $deleted );

			// No link
			if ( dps_get_view_all() ) {
				$retval .= " $extra";

			// Link
			} else {
				$retval .= " <a href='" . esc_url( dps_add_view_all( dps_get_forum_permalink( $forum_id ), true ) ) . "'>$extra</a>";
			}
		}

		return apply_filters( 'dps_get_forum_topics_link', $retval, $forum_id );
	}

/**
 * Output total sub-forum count of a forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id to check
 * @param boolean $integer Optional. Whether or not to format the result
 * @uses dps_get_forum_subforum_count() To get the forum's subforum count
 */
function dps_forum_subforum_count( $forum_id = 0, $integer = false ) {
	echo dps_get_forum_subforum_count( $forum_id, $integer );
}
	/**
	 * Return total subforum count of a forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @param boolean $integer Optional. Whether or not to format the result
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses get_post_meta() To get the subforum count
	 * @uses apply_filters() Calls 'dps_get_forum_subforum_count' with the
	 *                        subforum count and forum id
	 * @return int Forum's subforum count
	 */
	function dps_get_forum_subforum_count( $forum_id = 0, $integer = false ) {
		$forum_id    = dps_get_forum_id( $forum_id );
		$forum_count = (int) get_post_meta( $forum_id, '_dps_forum_subforum_count', true );
		$filter      = ( true === $integer ) ? 'dps_get_forum_subforum_count_int' : 'dps_get_forum_subforum_count';

		return apply_filters( $filter, $forum_count, $forum_id );
	}

/**
 * Output total topic count of a forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @param bool $total_count Optional. To get the total count or normal count?
 * @param boolean $integer Optional. Whether or not to format the result
 * @uses dps_get_forum_topic_count() To get the forum topic count
 */
function dps_forum_topic_count( $forum_id = 0, $total_count = true, $integer = false ) {
	echo dps_get_forum_topic_count( $forum_id, $total_count, $integer );
}
	/**
	 * Return total topic count of a forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @param bool $total_count Optional. To get the total count or normal
	 *                           count? Defaults to total.
	 * @param boolean $integer Optional. Whether or not to format the result
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses get_post_meta() To get the forum topic count
	 * @uses apply_filters() Calls 'dps_get_forum_topic_count' with the
	 *                        topic count and forum id
	 * @return int Forum topic count
	 */
	function dps_get_forum_topic_count( $forum_id = 0, $total_count = true, $integer = false ) {
		$forum_id = dps_get_forum_id( $forum_id );
		$meta_key = empty( $total_count ) ? '_dps_topic_count' : '_dps_total_topic_count';
		$topics   = (int) get_post_meta( $forum_id, $meta_key, true );
		$filter   = ( true === $integer ) ? 'dps_get_forum_topic_count_int' : 'dps_get_forum_topic_count';

		return apply_filters( $filter, $topics, $forum_id );
	}

/**
 * Output total reply count of a forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @param bool $total_count Optional. To get the total count or normal count?
 * @param boolean $integer Optional. Whether or not to format the result
 * @uses dps_get_forum_reply_count() To get the forum reply count
 */
function dps_forum_reply_count( $forum_id = 0, $total_count = true, $integer = false ) {
	echo dps_get_forum_reply_count( $forum_id, $total_count, $integer );
}
	/**
	 * Return total post count of a forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @param bool $total_count Optional. To get the total count or normal
	 *                           count?
	 * @param boolean $integer Optional. Whether or not to format the result
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses get_post_meta() To get the forum reply count
	 * @uses apply_filters() Calls 'dps_get_forum_reply_count' with the
	 *                        reply count and forum id
	 * @return int Forum reply count
	 */
	function dps_get_forum_reply_count( $forum_id = 0, $total_count = true, $integer = false ) {
		$forum_id = dps_get_forum_id( $forum_id );
		$meta_key = empty( $total_count ) ? '_dps_reply_count' : '_dps_total_reply_count';
		$replies  = (int) get_post_meta( $forum_id, $meta_key, true );
		$filter   = ( true === $integer ) ? 'dps_get_forum_reply_count_int' : 'dps_get_forum_reply_count';

		return apply_filters( $filter, $replies, $forum_id );
	}

/**
 * Output total post count of a forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @param bool $total_count Optional. To get the total count or normal count?
 * @param boolean $integer Optional. Whether or not to format the result
 * @uses dps_get_forum_post_count() To get the forum post count
 */
function dps_forum_post_count( $forum_id = 0, $total_count = true, $integer = false ) {
	echo dps_get_forum_post_count( $forum_id, $total_count, $integer );
}
	/**
	 * Return total post count of a forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @param bool $total_count Optional. To get the total count or normal
	 *                           count?
	 * @param boolean $integer Optional. Whether or not to format the result
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses get_post_meta() To get the forum post count
	 * @uses apply_filters() Calls 'dps_get_forum_post_count' with the
	 *                        post count and forum id
	 * @return int Forum post count
	 */
	function dps_get_forum_post_count( $forum_id = 0, $total_count = true, $integer = false ) {
		$forum_id = dps_get_forum_id( $forum_id );
		$topics   = dps_get_forum_topic_count( $forum_id, $total_count, true );
		$meta_key = empty( $total_count ) ? '_dps_reply_count' : '_dps_total_reply_count';
		$replies  = (int) get_post_meta( $forum_id, $meta_key, true );
		$retval   = $replies + $topics;
		$filter   = ( true === $integer ) ? 'dps_get_forum_post_count_int' : 'dps_get_forum_post_count';

		return apply_filters( $filter, $retval, $forum_id );
	}

/**
 * Output total hidden topic count of a forum (hidden includes trashed and
 * spammed topics)
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Topic id
 * @param boolean $integer Optional. Whether or not to format the result
 * @uses dps_get_forum_topic_count_hidden() To get the forum hidden topic count
 */
function dps_forum_topic_count_hidden( $forum_id = 0, $integer = false ) {
	echo dps_get_forum_topic_count_hidden( $forum_id, $integer );
}
	/**
	 * Return total hidden topic count of a forum (hidden includes trashed
	 * and spammed topics)
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Topic id
	 * @param boolean $integer Optional. Whether or not to format the result
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses get_post_meta() To get the hidden topic count
	 * @uses apply_filters() Calls 'dps_get_forum_topic_count_hidden' with
	 *                        the hidden topic count and forum id
	 * @return int Topic hidden topic count
	 */
	function dps_get_forum_topic_count_hidden( $forum_id = 0, $integer = false ) {
		$forum_id = dps_get_forum_id( $forum_id );
		$topics   = (int) get_post_meta( $forum_id, '_dps_topic_count_hidden', true );
		$filter   = ( true === $integer ) ? 'dps_get_forum_topic_count_hidden_int' : 'dps_get_forum_topic_count_hidden';

		return apply_filters( $filter, $topics, $forum_id );
	}

/**
 * Output the status of the forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_status() To get the forum status
 */
function dps_forum_status( $forum_id = 0 ) {
	echo dps_get_forum_status( $forum_id );
}
	/**
	 * Return the status of the forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses get_post_status() To get the forum's status
	 * @uses apply_filters() Calls 'dps_get_forum_status' with the status
	 *                        and forum id
	 * @return string Status of forum
	 */
	function dps_get_forum_status( $forum_id = 0 ) {
		$forum_id = dps_get_forum_id( $forum_id );
		$status   = get_post_meta( $forum_id, '_dps_status', true );
		if ( empty( $status ) )
			$status = 'open';

		return apply_filters( 'dps_get_forum_status', $status, $forum_id );
	}

/**
 * Output the visibility of the forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_visibility() To get the forum visibility
 */
function dps_forum_visibility( $forum_id = 0 ) {
	echo dps_get_forum_visibility( $forum_id );
}
	/**
	 * Return the visibility of the forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses get_post_visibility() To get the forum's visibility
	 * @uses apply_filters() Calls 'dps_get_forum_visibility' with the visibility
	 *                        and forum id
	 * @return string Status of forum
	 */
	function dps_get_forum_visibility( $forum_id = 0 ) {
		$forum_id = dps_get_forum_id( $forum_id );

		return apply_filters( 'dps_get_forum_visibility', get_post_status( $forum_id ), $forum_id );
	}

/**
 * Output the type of the forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_type() To get the forum type
 */
function dps_forum_type( $forum_id = 0 ) {
	echo dps_get_forum_type( $forum_id );
}
	/**
	 * Return the type of forum (category/forum/etc...)
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses get_post_meta() To get the forum category meta
	 * @return bool Whether the forum is a category or not
	 */
	function dps_get_forum_type( $forum_id = 0 ) {
		$forum_id = dps_get_forum_id( $forum_id );
		$retval   = get_post_meta( $forum_id, '_dps_forum_type', true );
		if ( empty( $retval ) )
			$retval = 'forum';

		return apply_filters( 'dps_get_forum_type', $retval, $forum_id );
	}

/**
 * Is the forum a category?
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_type() To get the forum type
 * @return bool Whether the forum is a category or not
 */
function dps_is_forum_category( $forum_id = 0 ) {
	$forum_id = dps_get_forum_id( $forum_id );
	$type     = dps_get_forum_type( $forum_id );
	$retval   = ( !empty( $type ) && 'category' == $type );

	return (bool) apply_filters( 'dps_is_forum_category', (bool) $retval, $forum_id );
}

/**
 * Is the forum open?
 *
 * @since Showcase (1.0)
 * @param int $forum_id Optional. Forum id
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_is_forum_closed() To check if the forum is closed or not
 * @return bool Whether the forum is open or not
 */
function dps_is_forum_open( $forum_id = 0 ) {
	return !dps_is_forum_closed( $forum_id );
}

	/**
	 * Is the forum closed?
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @param bool $check_ancestors Check if the ancestors are closed (only
	 *                               if they're a category)
	 * @uses dps_get_forum_status() To get the forum status
	 * @uses dps_get_forum_ancestors() To get the forum ancestors
	 * @uses dps_is_forum_category() To check if the forum is a category
	 * @uses dps_is_forum_closed() To check if the forum is closed
	 * @return bool True if closed, false if not
	 */
	function dps_is_forum_closed( $forum_id = 0, $check_ancestors = true ) {

		$forum_id = dps_get_forum_id( $forum_id );
		$retval    = ( dps_get_closed_status_id() == dps_get_forum_status( $forum_id ) );

		if ( !empty( $check_ancestors ) ) {
			$ancestors = dps_get_forum_ancestors( $forum_id );

			foreach ( (array) $ancestors as $ancestor ) {
				if ( dps_is_forum_category( $ancestor, false ) && dps_is_forum_closed( $ancestor, false ) ) {
					$retval = true;
				}
			}
		}

		return (bool) apply_filters( 'dps_is_forum_closed', (bool) $retval, $forum_id, $check_ancestors );
	}

/**
 * Is the forum public?
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @param bool $check_ancestors Check if the ancestors are public (only if
 *                               they're a category)
 * @uses get_post_meta() To get the forum public meta
 * @uses dps_get_forum_ancestors() To get the forum ancestors
 * @uses dps_is_forum_category() To check if the forum is a category
 * @uses dps_is_forum_closed() To check if the forum is closed
 * @return bool True if closed, false if not
 */
function dps_is_forum_public( $forum_id = 0, $check_ancestors = true ) {

	$forum_id   = dps_get_forum_id( $forum_id );
	$visibility = dps_get_forum_visibility( $forum_id );

	// If post status is public, return true
	$retval = ( dps_get_public_status_id() == $visibility );

	// Check ancestors and inherit their privacy setting for display
	if ( !empty( $check_ancestors ) ) {
		$ancestors = dps_get_forum_ancestors( $forum_id );

		foreach ( (array) $ancestors as $ancestor ) {
			if ( dps_is_forum( $ancestor ) && dps_is_forum_public( $ancestor, false ) ) {
				$retval = true;
			}
		}
	}

	return (bool) apply_filters( 'dps_is_forum_public', (bool) $retval, $forum_id, $check_ancestors );
}

/**
 * Is the forum private?
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @param bool $check_ancestors Check if the ancestors are private (only if
 *                               they're a category)
 * @uses get_post_meta() To get the forum private meta
 * @uses dps_get_forum_ancestors() To get the forum ancestors
 * @uses dps_is_forum_category() To check if the forum is a category
 * @uses dps_is_forum_closed() To check if the forum is closed
 * @return bool True if closed, false if not
 */
function dps_is_forum_private( $forum_id = 0, $check_ancestors = true ) {

	$forum_id   = dps_get_forum_id( $forum_id );
	$visibility = dps_get_forum_visibility( $forum_id );

	// If post status is private, return true
	$retval = ( dps_get_private_status_id() == $visibility );

	// Check ancestors and inherit their privacy setting for display
	if ( !empty( $check_ancestors ) ) {
		$ancestors = dps_get_forum_ancestors( $forum_id );

		foreach ( (array) $ancestors as $ancestor ) {
			if ( dps_is_forum( $ancestor ) && dps_is_forum_private( $ancestor, false ) ) {
				$retval = true;
			}
		}
	}

	return (bool) apply_filters( 'dps_is_forum_private', (bool) $retval, $forum_id, $check_ancestors );
}

/**
 * Is the forum hidden?
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @param bool $check_ancestors Check if the ancestors are private (only if
 *                               they're a category)
 * @uses get_post_meta() To get the forum private meta
 * @uses dps_get_forum_ancestors() To get the forum ancestors
 * @uses dps_is_forum_category() To check if the forum is a category
 * @uses dps_is_forum_closed() To check if the forum is closed
 * @return bool True if closed, false if not
 */
function dps_is_forum_hidden( $forum_id = 0, $check_ancestors = true ) {

	$forum_id   = dps_get_forum_id( $forum_id );
	$visibility = dps_get_forum_visibility( $forum_id );

	// If post status is private, return true
	$retval = ( dps_get_hidden_status_id() == $visibility );

	// Check ancestors and inherit their privacy setting for display
	if ( !empty( $check_ancestors ) ) {
		$ancestors = dps_get_forum_ancestors( $forum_id );

		foreach ( (array) $ancestors as $ancestor ) {
			if ( dps_is_forum( $ancestor ) && dps_is_forum_hidden( $ancestor, false ) ) {
				$retval = true;
			}
		}
	}

	return (bool) apply_filters( 'dps_is_forum_hidden', (bool) $retval, $forum_id, $check_ancestors );
}

/**
 * Output the author of the forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_author() To get the forum author
 */
function dps_forum_author_display_name( $forum_id = 0 ) {
	echo dps_get_forum_author_display_name( $forum_id );
}
	/**
	 * Return the author of the forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses dps_get_forum_author_id() To get the forum author id
	 * @uses get_the_author_meta() To get the display name of the author
	 * @uses apply_filters() Calls 'dps_get_forum_author' with the author
	 *                        and forum id
	 * @return string Author of forum
	 */
	function dps_get_forum_author_display_name( $forum_id = 0 ) {
		$forum_id = dps_get_forum_id( $forum_id );
		$author   = get_the_author_meta( 'display_name', dps_get_forum_author_id( $forum_id ) );

		return apply_filters( 'dps_get_forum_author_display_name', $author, $forum_id );
	}

/**
 * Output the author ID of the forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum id
 * @uses dps_get_forum_author_id() To get the forum author id
 */
function dps_forum_author_id( $forum_id = 0 ) {
	echo dps_get_forum_author_id( $forum_id );
}
	/**
	 * Return the author ID of the forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum id
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses get_post_field() To get the forum author id
	 * @uses apply_filters() Calls 'dps_get_forum_author_id' with the author
	 *                        id and forum id
	 * @return string Author of forum
	 */
	function dps_get_forum_author_id( $forum_id = 0 ) {
		$forum_id  = dps_get_forum_id( $forum_id );
		$author_id = get_post_field( 'post_author', $forum_id );

		return (int) apply_filters( 'dps_get_forum_author_id', (int) $author_id, $forum_id );
	}

/**
 * Replace forum meta details for users that cannot view them.
 *
 * @since Showcase (1.0)
 *
 * @param string $retval
 * @param int $forum_id
 *
 * @uses dps_is_forum_private()
 * @uses current_user_can()
 *
 * @return string
 */
function dps_suppress_private_forum_meta( $retval, $forum_id ) {
	if ( dps_is_forum_private( $forum_id, false ) && !current_user_can( 'read_private_forums' ) )
		$retval = '-';

	return apply_filters( 'dps_suppress_private_forum_meta', $retval );
}

/**
 * Replace forum author details for users that cannot view them.
 *
 * @since Showcase (1.0)
 *
 * @param string $retval
 * @param int $forum_id
 *
 * @uses dps_is_forum_private()
 * @uses get_post_field()
 * @uses dps_get_topic_post_type()
 * @uses dps_is_forum_private()
 * @uses dps_get_topic_forum_id()
 * @uses dps_get_reply_post_type()
 * @uses dps_get_reply_forum_id()
 *
 * @return string
 */
function dps_suppress_private_author_link( $author_link, $args ) {

	// Assume the author link is the return value
	$retval = $author_link;

	// Show the normal author link
	if ( !empty( $args['post_id'] ) && !current_user_can( 'read_private_forums' ) ) {

		// What post type are we looking at?
		$post_type = get_post_field( 'post_type', $args['post_id'] );

		switch ( $post_type ) {

			// Topic
			case dps_get_topic_post_type() :
				if ( dps_is_forum_private( dps_get_topic_forum_id( $args['post_id'] ) ) )
					$retval = '';

				break;

			// Reply
			case dps_get_reply_post_type() :
				if ( dps_is_forum_private( dps_get_reply_forum_id( $args['post_id'] ) ) )
					$retval = '';

				break;

			// Post
			default :
				if ( dps_is_forum_private( $args['post_id'] ) )
					$retval = '';

				break;
		}
	}

	return apply_filters( 'dps_suppress_private_author_link', $retval );
}

/**
 * Output the row class of a forum
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id Optional. Forum ID.
 * @param array Extra classes you can pass when calling this function
 * @uses dps_get_forum_class() To get the row class of the forum
 */
function dps_forum_class( $forum_id = 0, $classes = array() ) {
	echo dps_get_forum_class( $forum_id, $classes );
}
	/**
	 * Return the row class of a forum
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum ID
	 * @param array Extra classes you can pass when calling this function
	 * @uses dps_get_forum_id() To validate the forum id
	 * @uses dps_is_forum_category() To see if forum is a category
	 * @uses dps_get_forum_status() To get the forum status
	 * @uses dps_get_forum_visibility() To get the forum visibility
	 * @uses dps_get_forum_parent_id() To get the forum parent id
	 * @uses get_post_class() To get all the classes including ours
	 * @uses apply_filters() Calls 'dps_get_forum_class' with the classes
	 * @return string Row class of the forum
	 */
	function dps_get_forum_class( $forum_id = 0, $classes = array() ) {
		$bbp       = showcase();
		$forum_id  = dps_get_forum_id( $forum_id );
		$count     = isset( $bbp->forum_query->current_post ) ? $bbp->forum_query->current_post : 1;
		$classes   = (array) $classes;

		// Get some classes
		$classes[] = 'loop-item-' . $count;
		$classes[] = ( (int) $count % 2 )                      ? 'even'              : 'odd';
		$classes[] = dps_is_forum_category( $forum_id )        ? 'status-category'   : '';
		$classes[] = dps_get_forum_subforum_count( $forum_id ) ? 'bbp-has-subforums' : '';
		$classes[] = dps_get_forum_parent_id( $forum_id )      ? 'bbp-parent-forum-' . dps_get_forum_parent_id( $forum_id ) : '';
		$classes[] = 'bbp-forum-status-'     . dps_get_forum_status( $forum_id );
		$classes[] = 'bbp-forum-visibility-' . dps_get_forum_visibility( $forum_id );

		// Ditch the empties
		$classes   = array_filter( $classes );
		$classes   = get_post_class( $classes, $forum_id );

		// Filter the results
		$classes   = apply_filters( 'dps_get_forum_class', $classes, $forum_id );
		$retval    = 'class="' . join( ' ', $classes ) . '"';

		return $retval;
	}

/** Single Forum **************************************************************/

/**
 * Output a fancy description of the current forum, including total topics,
 * total replies, and last activity.
 *
 * @since Showcase (1.0)
 *
 * @param array $args Arguments passed to alter output
 * @uses dps_get_single_forum_description() Return the eventual output
 */
function dps_single_forum_description( $args = '' ) {
	echo dps_get_single_forum_description( $args );
}
	/**
	 * Return a fancy description of the current forum, including total
	 * topics, total replies, and last activity.
	 *
	 * @since Showcase (1.0)
	 *
	 * @param mixed $args This function supports these arguments:
	 *  - forum_id: Forum id
	 *  - before: Before the text
	 *  - after: After the text
	 *  - size: Size of the avatar
	 * @uses dps_get_forum_id() To get the forum id
	 * @uses dps_get_forum_topic_count() To get the forum topic count
	 * @uses dps_get_forum_reply_count() To get the forum reply count
	 * @uses dps_get_forum_freshness_link() To get the forum freshness link
	 * @uses dps_get_forum_last_active_id() To get the forum last active id
	 * @uses dps_get_author_link() To get the author link
	 * @uses add_filter() To add the 'view all' filter back
	 * @uses apply_filters() Calls 'dps_get_single_forum_description' with
	 *                        the description and args
	 * @return string Filtered forum description
	 */
	function dps_get_single_forum_description( $args = '' ) {

		// Parse arguments against default values
		$r = dps_parse_args( $args, array(
			'forum_id'  => 0,
			'before'    => '<div class="bbp-template-notice info"><p class="bbp-forum-description">',
			'after'     => '</p></div>',
			'size'      => 14,
			'feed'      => true
		), 'get_single_forum_description' );

		// Validate forum_id
		$forum_id = dps_get_forum_id( $r['forum_id'] );

		// Unhook the 'view all' query var adder
		remove_filter( 'dps_get_forum_permalink', 'dps_add_view_all' );

		// Get some forum data
		$tc_int      = dps_get_forum_topic_count( $forum_id, false );
		$rc_int      = dps_get_forum_reply_count( $forum_id, false );
		$topic_count = dps_get_forum_topic_count( $forum_id );
		$reply_count = dps_get_forum_reply_count( $forum_id );
		$last_active = dps_get_forum_last_active_id( $forum_id );

		// Has replies
		if ( !empty( $reply_count ) ) {
			$reply_text = sprintf( _n( '%s reply', '%s replies', $rc_int, 'showcase' ), $reply_count );
		}

		// Forum has active data
		if ( !empty( $last_active ) ) {
			$topic_text      = dps_get_forum_topics_link( $forum_id );
			$time_since      = dps_get_forum_freshness_link( $forum_id );
			$last_updated_by = dps_get_author_link( array( 'post_id' => $last_active, 'size' => $r['size'] ) );

		// Forum has no last active data
		} else {
			$topic_text      = sprintf( _n( '%s topic', '%s topics', $tc_int, 'showcase' ), $topic_count );
		}

		// Forum has active data
		if ( !empty( $last_active ) ) {

			if ( !empty( $reply_count ) ) {

				if ( dps_is_forum_category( $forum_id ) ) {
					$retstr = sprintf( __( 'This category contains %1$s and %2$s, and was last updated by %3$s %4$s.', 'dps' ), $topic_text, $reply_text, $last_updated_by, $time_since );
				} else {
					$retstr = sprintf( __( 'This forum contains %1$s and %2$s, and was last updated by %3$s %4$s.',    'showcase' ), $topic_text, $reply_text, $last_updated_by, $time_since );
				}

			} else {

				if ( dps_is_forum_category( $forum_id ) ) {
					$retstr = sprintf( __( 'This category contains %1$s, and was last updated by %2$s %3$s.', 'dps' ), $topic_text, $last_updated_by, $time_since );
				} else {
					$retstr = sprintf( __( 'This forum contains %1$s, and was last updated by %2$s %3$s.',    'showcase' ), $topic_text, $last_updated_by, $time_since );
				}
			}

		// Forum has no last active data
		} else {

			if ( !empty( $reply_count ) ) {

				if ( dps_is_forum_category( $forum_id ) ) {
					$retstr = sprintf( __( 'This category contains %1$s and %2$s.', 'dps' ), $topic_text, $reply_text );
				} else {
					$retstr = sprintf( __( 'This forum contains %1$s and %2$s.',    'showcase' ), $topic_text, $reply_text );
				}

			} else {

				if ( !empty( $topic_count ) ) {

					if ( dps_is_forum_category( $forum_id ) ) {
						$retstr = sprintf( __( 'This category contains %1$s.', 'dps' ), $topic_text );
					} else {
						$retstr = sprintf( __( 'This forum contains %1$s.',    'showcase' ), $topic_text );
					}

				} else {
					$retstr = __( 'This forum is empty.', 'dps' );
				}
			}
		}

		// Add feeds
		//$feed_links = ( !empty( $r['feed'] ) ) ? dps_get_forum_topics_feed_link ( $forum_id ) . dps_get_forum_replies_feed_link( $forum_id ) : '';

		// Add the 'view all' filter back
		add_filter( 'dps_get_forum_permalink', 'dps_add_view_all' );

		// Combine the elements together
		$retstr = $r['before'] . $retstr . $r['after'];

		// Return filtered result
		return apply_filters( 'dps_get_single_forum_description', $retstr, $r );
	}

/** Forms *********************************************************************/

/**
 * Output the value of forum title field
 *
 * @since Showcase (1.0)
 *
 * @uses dps_get_form_forum_title() To get the value of forum title field
 */
function dps_form_forum_title() {
	echo dps_get_form_forum_title();
}
	/**
	 * Return the value of forum title field
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses dps_is_forum_edit() To check if it's forum edit page
	 * @uses apply_filters() Calls 'dps_get_form_forum_title' with the title
	 * @return string Value of forum title field
	 */
	function dps_get_form_forum_title() {

		// Get _POST data
		if ( dps_is_post_request() && isset( $_POST['dps_forum_title'] ) ) {
			$forum_title = $_POST['dps_forum_title'];

		// Get edit data
		} elseif ( dps_is_forum_edit() ) {
			$forum_title = dps_get_global_post_field( 'post_title', 'raw' );

		// No data
		} else {
			$forum_title = '';
		}

		return apply_filters( 'dps_get_form_forum_title', esc_attr( $forum_title ) );
	}

/**
 * Output the value of forum content field
 *
 * @since Showcase (1.0)
 *
 * @uses dps_get_form_forum_content() To get value of forum content field
 */
function dps_form_forum_content() {
	echo dps_get_form_forum_content();
}
	/**
	 * Return the value of forum content field
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses dps_is_forum_edit() To check if it's the forum edit page
	 * @uses apply_filters() Calls 'dps_get_form_forum_content' with the content
	 * @return string Value of forum content field
	 */
	function dps_get_form_forum_content() {

		// Get _POST data
		if ( dps_is_post_request() && isset( $_POST['dps_forum_content'] ) ) {
			$forum_content = stripslashes( $_POST['dps_forum_content'] );

		// Get edit data
		} elseif ( dps_is_forum_edit() ) {
			$forum_content = dps_get_global_post_field( 'post_content', 'raw' );

		// No data
		} else {
			$forum_content = '';
		}

		return apply_filters( 'dps_get_form_forum_content', esc_textarea( $forum_content ) );
	}

/**
 * Output value of forum parent
 *
 * @since Showcase (1.0)
 *
 * @uses dps_get_form_forum_parent() To get the topic's forum id
 */
function dps_form_forum_parent() {
	echo dps_get_form_forum_parent();
}
	/**
	 * Return value of forum parent
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses dps_is_topic_edit() To check if it's the topic edit page
	 * @uses dps_get_forum_parent_id() To get the topic forum id
	 * @uses apply_filters() Calls 'dps_get_form_forum_parent' with the forum
	 * @return string Value of topic content field
	 */
	function dps_get_form_forum_parent() {

		// Get _POST data
		if ( dps_is_post_request() && isset( $_POST['dps_forum_id'] ) ) {
			$forum_parent = $_POST['dps_forum_id'];

		// Get edit data
		} elseif ( dps_is_forum_edit() ) {
			$forum_parent = dps_get_forum_parent_id();

		// No data
		} else {
			$forum_parent = 0;
		}

		return apply_filters( 'dps_get_form_forum_parent', esc_attr( $forum_parent ) );
	}

/**
 * Output value of forum type
 *
 * @since Showcase (1.0)
 *
 * @uses dps_get_form_forum_type() To get the topic's forum id
 */
function dps_form_forum_type() {
	echo dps_get_form_forum_type();
}
	/**
	 * Return value of forum type
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses dps_is_topic_edit() To check if it's the topic edit page
	 * @uses dps_get_forum_type_id() To get the topic forum id
	 * @uses apply_filters() Calls 'dps_get_form_forum_type' with the forum
	 * @return string Value of topic content field
	 */
	function dps_get_form_forum_type() {

		// Get _POST data
		if ( dps_is_post_request() && isset( $_POST['dps_forum_type'] ) ) {
			$forum_type = $_POST['dps_forum_type'];

		// Get edit data
		} elseif ( dps_is_forum_edit() ) {
			$forum_type = dps_get_forum_type();

		// No data
		} else {
			$forum_type = 'forum';
		}

		return apply_filters( 'dps_get_form_forum_type', esc_attr( $forum_type ) );
	}

/**
 * Output value of forum visibility
 *
 * @since Showcase (1.0)
 *
 * @uses dps_get_form_forum_visibility() To get the topic's forum id
 */
function dps_form_forum_visibility() {
	echo dps_get_form_forum_visibility();
}
	/**
	 * Return value of forum visibility
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses dps_is_topic_edit() To check if it's the topic edit page
	 * @uses dps_get_forum_visibility_id() To get the topic forum id
	 * @uses apply_filters() Calls 'dps_get_form_forum_visibility' with the forum
	 * @return string Value of topic content field
	 */
	function dps_get_form_forum_visibility() {

		// Get _POST data
		if ( dps_is_post_request() && isset( $_POST['dps_forum_visibility'] ) ) {
			$forum_visibility = $_POST['dps_forum_visibility'];

		// Get edit data
		} elseif ( dps_is_forum_edit() ) {
			$forum_visibility = dps_get_forum_visibility();

		// No data
		} else {
			$forum_visibility = showcase()->public_status_id;
		}

		return apply_filters( 'dps_get_form_forum_visibility', esc_attr( $forum_visibility ) );
	}

/** Form Dropdows *************************************************************/

/**
 * Output value forum type dropdown
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id The forum id to use
 * @uses dps_get_form_forum_type() To get the topic's forum id
 */
function dps_form_forum_type_dropdown( $forum_id = 0 ) {
	echo dps_get_form_forum_type_dropdown( $forum_id );
}
	/**
	 * Return the forum type dropdown
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id The forum id to use
	 * @uses dps_is_topic_edit() To check if it's the topic edit page
	 * @uses dps_get_forum_type() To get the forum type
	 * @uses apply_filters()
	 * @return string HTML select list for selecting forum type
	 */
	function dps_get_form_forum_type_dropdown( $forum_id = 0 ) {
		$forum_id   = dps_get_forum_id( $forum_id );
		$forum_attr = apply_filters( 'dps_forum_types', array(
			'forum'    => __( 'Forum',    'showcase' ),
			'category' => __( 'Category', 'dps' )
		) );
		$type_output = '<select name="dps_forum_type" id="dps_forum_type_select">' . "\n";

		foreach( $forum_attr as $value => $label )
			$type_output .= "\t" . '<option value="' . $value . '"' . selected( dps_get_forum_type( $forum_id ), $value, false ) . '>' . esc_html( $label ) . '</option>' . "\n";

		$type_output .= '</select>';

		return apply_filters( 'dps_get_form_forum_type_dropdown', $type_output, $forum_id, $forum_attr );
	}

/**
 * Output value forum status dropdown
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id The forum id to use
 * @uses dps_get_form_forum_status() To get the topic's forum id
 */
function dps_form_forum_status_dropdown( $forum_id = 0 ) {
	echo dps_get_form_forum_status_dropdown( $forum_id );
}
	/**
	 * Return the forum status dropdown
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id The forum id to use
	 * @uses dps_is_topic_edit() To check if it's the topic edit page
	 * @uses dps_get_forum_status() To get the forum status
	 * @uses apply_filters()
	 * @return string HTML select list for selecting forum status
	 */
	function dps_get_form_forum_status_dropdown( $forum_id = 0 ) {
		$forum_id   = dps_get_forum_id( $forum_id );
		$forum_attr = apply_filters( 'dps_forum_statuses', array(
			'open'   => _x( 'Open',   'Forum Status', 'dps' ),
			'closed' => _x( 'Closed', 'Forum Status', 'dps' )
		) );
		$status_output = '<select name="dps_forum_status" id="dps_forum_status_select">' . "\n";

		foreach( $forum_attr as $value => $label )
			$status_output .= "\t" . '<option value="' . $value . '"' . selected( dps_get_forum_status( $forum_id ), $value, false ) . '>' . esc_html( $label ) . '</option>' . "\n";

		$status_output .= '</select>';

		return apply_filters( 'dps_get_form_forum_status_dropdown', $status_output, $forum_id, $forum_attr );
	}

/**
 * Output value forum visibility dropdown
 *
 * @since Showcase (1.0)
 *
 * @param int $forum_id The forum id to use
 * @uses dps_get_form_forum_visibility() To get the topic's forum id
 */
function dps_form_forum_visibility_dropdown( $forum_id = 0 ) {
	echo dps_get_form_forum_visibility_dropdown( $forum_id );
}
	/**
	 * Return the forum visibility dropdown
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id The forum id to use
	 * @uses dps_is_topic_edit() To check if it's the topic edit page
	 * @uses dps_get_forum_visibility() To get the forum visibility
	 * @uses apply_filters()
	 * @return string HTML select list for selecting forum visibility
	 */
	function dps_get_form_forum_visibility_dropdown( $forum_id = 0 ) {
		$forum_id   = dps_get_forum_id( $forum_id );
		$forum_attr = apply_filters( 'dps_forum_visibilities', array(
			dps_get_public_status_id()  => __( 'Public',  'showcase' ),
			dps_get_private_status_id() => __( 'Private', 'dps' ),
			dps_get_hidden_status_id()  => __( 'Hidden',  'showcase' )
		) );
		$visibility_output = '<select name="dps_forum_visibility" id="dps_forum_visibility_select">' . "\n";

		foreach( $forum_attr as $value => $label )
			$visibility_output .= "\t" . '<option value="' . $value . '"' . selected( dps_get_forum_visibility( $forum_id ), $value, false ) . '>' . esc_html( $label ) . '</option>' . "\n";

		$visibility_output .= '</select>';

		return apply_filters( 'dps_get_form_forum_visibility_dropdown', $visibility_output, $forum_id, $forum_attr );
	}

/** Feeds *********************************************************************/

/**
 * Output the link for the forum feed
 *
 * @since Showcase (1.0)
 * @param type $forum_id Optional. Forum ID.
 */
function dps_forum_topics_feed_link( $forum_id = 0 ) {
	echo dps_get_forum_topics_feed_link( $forum_id );
}
	/**
	 * Retrieve the link for the forum feed
	 *
	 * @since Showcase (1.0)
	 * @param int $forum_id Optional. Forum ID.
	 * @return string
	 */
	function dps_get_forum_topics_feed_link( $forum_id = 0 ) {

		// Validate forum id
		$forum_id = dps_get_forum_id( $forum_id );

		// Forum is valid
		if ( !empty( $forum_id ) ) {

			// Define local variable(s)
			$link = '';

			// Pretty permalinks
			if ( get_option( 'permalink_structure' ) ) {

				// Forum link
				$url = trailingslashit( dps_get_forum_permalink( $forum_id ) ) . 'feed';
				$url = user_trailingslashit( $url, 'single_feed' );

			// Unpretty permalinks
			} else {
				$url = home_url( add_query_arg( array(
					'feed'                    => 'rss2',
					dps_get_showcase_post_type() => get_post_field( 'post_name', $forum_id )
				) ) );
			}

			$link = '<a href="' . $url . '" class="bbp-forum-rss-link topics"><span>' . __( 'Topics', 'dps' ) . '</span></a>';
		}

		return apply_filters( 'dps_get_forum_topics_feed_link', $link, $url, $forum_id );
	}

/**
 * Output the link for the forum replies feed
 *
 * @since Showcase (1.0)
 *
 * @param type $forum_id Optional. Forum ID.
 *
 * @uses dps_get_forum_replies_feed_link()
 */
function dps_forum_replies_feed_link( $forum_id = 0 ) {
	echo dps_get_forum_replies_feed_link( $forum_id );
}
	/**
	 * Retrieve the link for the forum replies feed
	 *
	 * @since Showcase (1.0)
	 *
	 * @param int $forum_id Optional. Forum ID.
	 * @return string
	 */
	function dps_get_forum_replies_feed_link( $forum_id = 0 ) {

		// Validate forum id
		$forum_id = dps_get_forum_id( $forum_id );

		// Forum is valid
		if ( !empty( $forum_id ) ) {

			// Define local variable(s)
			$link = '';

			// Pretty permalinks
			if ( get_option( 'permalink_structure' ) ) {

				// Forum link
				$url = trailingslashit( dps_get_forum_permalink( $forum_id ) ) . 'feed';
				$url = user_trailingslashit( $url, 'single_feed' );
				$url = add_query_arg( array( 'type' => 'reply' ), $url );

			// Unpretty permalinks
			} else {
				$url = home_url( add_query_arg( array(
					'type'                    => 'reply',
					'feed'                    => 'rss2',
					dps_get_showcase_post_type() => get_post_field( 'post_name', $forum_id )
				) ) );
			}

			$link = '<a href="' . $url . '" class="bbp-forum-rss-link replies"><span>' . __( 'Replies', 'dps' ) . '</span></a>';
		}

		return apply_filters( 'dps_get_forum_replies_feed_link', $link, $url, $forum_id );
	}
