<?php

/**
 * Showcase Core Theme Compatibility
 *
 * @package Showcase
 * @subpackage ThemeCompatibility
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/** Theme Compat **************************************************************/

/**
 * What follows is an attempt at intercepting the natural page load process
 * to replace the_content() with the appropriate showcase content.
 *
 * To do this, showcase does several direct manipulations of global variables
 * and forces them to do what they are not supposed to be doing.
 *
 * Don't try anything you're about to witness here, at home. Ever.
 */

/** Base Class ****************************************************************/

/**
 * Theme Compatibility base class
 *
 * This is only intended to be extended, and is included here as a basic guide
 * for future Theme Packs to use. @link BB_Twenty_Ten is a good example of
 * extending this class, as is @link dps_setup_theme_compat()
 *
 * @since Showcase (1.0)
 */
class DPS_Theme_Compat {

	/**
	 * Should be like:
	 *
	 * array(
	 *     'id'      => ID of the theme (should be unique)
	 *     'name'    => Name of the theme (should match style.css)
	 *     'version' => Theme version for cache busting scripts and styling
	 *     'dir'     => Path to theme
	 *     'url'     => URL to theme
	 * );
	 * @var array 
	 */
	private $_data = array();

	/**
	 * Pass the $properties to the object on creation.
	 *
	 * @since Showcase (1.0)
	 * @param array $properties
	 */
    public function __construct( Array $properties = array() ) {
		$this->_data = $properties;
	}

	/**
	 * Set a theme's property.
	 *
	 * @since Showcase (1.0)
	 * @param string $property
	 * @param mixed $value
	 * @return mixed
	 */
	public function __set( $property, $value ) {
		return $this->_data[$property] = $value;
	}

	/**
	 * Get a theme's property.
	 *
	 * @since Showcase (1.0)
	 * @param string $property
	 * @param mixed $value
	 * @return mixed
	 */
	public function __get( $property ) {
		return array_key_exists( $property, $this->_data ) ? $this->_data[$property] : '';
	}
}

/** Functions *****************************************************************/

/**
 * Setup the default theme compat theme
 *
 * @since Showcase (1.0)
 * @param DPS_Theme_Compat $theme
 */
function dps_setup_theme_compat( $theme = '' ) {

	// Make sure theme package is available, set to default if not
	if ( ! isset( showcase()->theme_compat->packages[$theme] ) || ! is_a( showcase()->theme_compat->packages[$theme], 'DPS_Theme_Compat' ) ) {
		$theme = 'default';
	}

	// Set the active theme compat theme
	showcase()->theme_compat->theme = showcase()->theme_compat->packages[$theme];
}

/**
 * Gets the name of the showcase compatable theme used, in the event the
 * currently active WordPress theme does not explicitly support showcase.
 * This can be filtered or set manually. Tricky theme authors can override the
 * default and include their own showcase compatibility layers for their themes.
 *
 * @since Showcase (1.0)
 * @uses apply_filters()
 * @return string
 */
function dps_get_theme_compat_id() {
	return apply_filters( 'dps_get_theme_compat_id', showcase()->theme_compat->theme->id );
}

/**
 * Gets the name of the showcase compatable theme used, in the event the
 * currently active WordPress theme does not explicitly support showcase.
 * This can be filtered or set manually. Tricky theme authors can override the
 * default and include their own showcase compatibility layers for their themes.
 *
 * @since Showcase (1.0)
 * @uses apply_filters()
 * @return string
 */
function dps_get_theme_compat_name() {
	return apply_filters( 'dps_get_theme_compat_name', showcase()->theme_compat->theme->name );
}

/**
 * Gets the version of the showcase compatable theme used, in the event the
 * currently active WordPress theme does not explicitly support showcase.
 * This can be filtered or set manually. Tricky theme authors can override the
 * default and include their own showcase compatibility layers for their themes.
 *
 * @since Showcase (1.0)
 * @uses apply_filters()
 * @return string
 */
function dps_get_theme_compat_version() {
	return apply_filters( 'dps_get_theme_compat_version', showcase()->theme_compat->theme->version );
}

/**
 * Gets the showcase compatable theme used in the event the currently active
 * WordPress theme does not explicitly support showcase. This can be filtered,
 * or set manually. Tricky theme authors can override the default and include
 * their own showcase compatibility layers for their themes.
 *
 * @since Showcase (1.0)
 * @uses apply_filters()
 * @return string
 */
function dps_get_theme_compat_dir() {
	return apply_filters( 'dps_get_theme_compat_dir', showcase()->theme_compat->theme->dir );
}

/**
 * Gets the showcase compatable theme used in the event the currently active
 * WordPress theme does not explicitly support showcase. This can be filtered,
 * or set manually. Tricky theme authors can override the default and include
 * their own showcase compatibility layers for their themes.
 *
 * @since Showcase (1.0)
 * @uses apply_filters()
 * @return string
 */
function dps_get_theme_compat_url() {
	return apply_filters( 'dps_get_theme_compat_url', showcase()->theme_compat->theme->url );
}

/**
 * Gets true/false if page is currently inside theme compatibility
 *
 * @since Showcase (1.0)
 * @return bool
 */
function dps_is_theme_compat_active() {
	if ( empty( showcase()->theme_compat->active ) )
		return false;

	return showcase()->theme_compat->active;
}

/**
 * Sets true/false if page is currently inside theme compatibility
 *
 * @since Showcase (1.0)
 * @param bool $set
 * @return bool
 */
function dps_set_theme_compat_active( $set = true ) {
	showcase()->theme_compat->active = $set;

	return (bool) showcase()->theme_compat->active;
}

/**
 * Set the theme compat templates global
 *
 * Stash possible template files for the current query. Useful if plugins want
 * to override them, or see what files are being scanned for inclusion.
 *
 * @since Showcase (1.0)
 */
function dps_set_theme_compat_templates( $templates = array() ) {
	showcase()->theme_compat->templates = $templates;

	return showcase()->theme_compat->templates;
}

/**
 * Set the theme compat template global
 *
 * Stash the template file for the current query. Useful if plugins want
 * to override it, or see what file is being included.
 *
 * @since Showcase (1.0)
 */
function dps_set_theme_compat_template( $template = '' ) {
	showcase()->theme_compat->template = $template;

	return showcase()->theme_compat->template;
}

/**
 * Set the theme compat original_template global
 *
 * Stash the original template file for the current query. Useful for checking
 * if showcase was able to find a more appropriate template.
 *
 * @since Showcase (1.0)
 */
function dps_set_theme_compat_original_template( $template = '' ) {
	showcase()->theme_compat->original_template = $template;

	return showcase()->theme_compat->original_template;
}

/**
 * Set the theme compat original_template global
 *
 * Stash the original template file for the current query. Useful for checking
 * if showcase was able to find a more appropriate template.
 *
 * @since Showcase (1.0)
 */
function dps_is_theme_compat_original_template( $template = '' ) {
	if ( empty( showcase()->theme_compat->original_template ) )
		return false;

	return (bool) ( showcase()->theme_compat->original_template == $template );
}

/**
 * Register a new showcase theme package to the active theme packages array
 *
 * @since Showcase (1.0)
 * @param array $theme
 */
function dps_register_theme_package( $theme = array(), $override = true ) {

	// Create new DPS_Theme_Compat object from the $theme array
	if ( is_array( $theme ) )
		$theme = new DPS_Theme_Compat( $theme );

	// Bail if $theme isn't a proper object
	if ( ! is_a( $theme, 'DPS_Theme_Compat' ) )
		return;

	// Only override if the
}
/**
 * This fun little function fills up some WordPress globals with dummy data to
 * stop your average page template from complaining about it missing.
 *
 * @since Showcase (1.0)
 * @global WP_Query $wp_query
 * @global object $post
 * @param array $args
 */
function dps_theme_compat_reset_post( $args = array() ) {
	global $wp_query, $post;

	// Default arguments
	$defaults = array(
		'ID'                    => -9999,
		'post_status'           => 'publish'),
		'post_author'           => 0,
		'post_parent'           => 0,
		'post_type'             => 'page',
		'post_date'             => 0,
		'post_date_gmt'         => 0,
		'post_modified'         => 0,
		'post_modified_gmt'     => 0,
		'post_content'          => '',
		'post_title'            => '',
		'post_excerpt'          => '',
		'post_content_filtered' => '',
		'post_mime_type'        => '',
		'post_password'         => '',
		'post_name'             => '',
		'guid'                  => '',
		'menu_order'            => 0,
		'pinged'                => '',
		'to_ping'               => '',
		'ping_status'           => '',
		'comment_status'        => 'closed',
		'comment_count'         => 0,

		'is_404'          => false,
		'is_page'         => false,
		'is_single'       => false,
		'is_archive'      => false,
		'is_tax'          => false,
	);

	// Switch defaults if post is set
	if ( isset( $wp_query->post ) ) {		  
		$defaults = array(
			'ID'                    => $wp_query->post->ID,
			'post_status'           => $wp_query->post->post_status,
			'post_author'           => $wp_query->post->post_author,
			'post_parent'           => $wp_query->post->post_parent,
			'post_type'             => $wp_query->post->post_type,
			'post_date'             => $wp_query->post->post_date,
			'post_date_gmt'         => $wp_query->post->post_date_gmt,
			'post_modified'         => $wp_query->post->post_modified,
			'post_modified_gmt'     => $wp_query->post->post_modified_gmt,
			'post_content'          => $wp_query->post->post_content,
			'post_title'            => $wp_query->post->post_title,
			'post_excerpt'          => $wp_query->post->post_excerpt,
			'post_content_filtered' => $wp_query->post->post_content_filtered,
			'post_mime_type'        => $wp_query->post->post_mime_type,
			'post_password'         => $wp_query->post->post_password,
			'post_name'             => $wp_query->post->post_name,
			'guid'                  => $wp_query->post->guid,
			'menu_order'            => $wp_query->post->menu_order,
			'pinged'                => $wp_query->post->pinged,
			'to_ping'               => $wp_query->post->to_ping,
			'ping_status'           => $wp_query->post->ping_status,
			'comment_status'        => $wp_query->post->comment_status,
			'comment_count'         => $wp_query->post->comment_count,

			'is_404'          => false,
			'is_page'         => false,
			'is_single'       => false,
			'is_archive'      => false,
			'is_tax'          => false,
		);
	}
	$dummy = dps_parse_args( $args, $defaults, 'theme_compat_reset_post' );

	// Clear out the post related globals
	unset( $wp_query->posts );
	unset( $wp_query->post  );
	unset( $post            );

	// Setup the dummy post object
	$wp_query->post                        = new stdClass; 
	$wp_query->post->ID                    = $dummy['ID'];
	$wp_query->post->post_status           = $dummy['post_status'];
	$wp_query->post->post_author           = $dummy['post_author'];
	$wp_query->post->post_parent           = $dummy['post_parent'];
	$wp_query->post->post_type             = $dummy['post_type'];
	$wp_query->post->post_date             = $dummy['post_date'];
	$wp_query->post->post_date_gmt         = $dummy['post_date_gmt'];
	$wp_query->post->post_modified         = $dummy['post_modified'];
	$wp_query->post->post_modified_gmt     = $dummy['post_modified_gmt'];
	$wp_query->post->post_content          = $dummy['post_content'];
	$wp_query->post->post_title            = $dummy['post_title'];
	$wp_query->post->post_excerpt          = $dummy['post_excerpt'];
	$wp_query->post->post_content_filtered = $dummy['post_content_filtered'];
	$wp_query->post->post_mime_type        = $dummy['post_mime_type'];
	$wp_query->post->post_password         = $dummy['post_password'];
	$wp_query->post->post_name             = $dummy['post_name'];
	$wp_query->post->guid                  = $dummy['guid'];
	$wp_query->post->menu_order            = $dummy['menu_order'];
	$wp_query->post->pinged                = $dummy['pinged'];
	$wp_query->post->to_ping               = $dummy['to_ping'];
	$wp_query->post->ping_status           = $dummy['ping_status'];
	$wp_query->post->comment_status        = $dummy['comment_status'];
	$wp_query->post->comment_count         = $dummy['comment_count'];

	// Set the $post global
	$post = $wp_query->post;

	// Setup the dummy post loop
	$wp_query->posts[0] = $wp_query->post;

	// Prevent comments form from appearing
	$wp_query->post_count = 1;
	$wp_query->is_404     = $dummy['is_404'];
	$wp_query->is_page    = $dummy['is_page'];
	$wp_query->is_single  = $dummy['is_single'];
	$wp_query->is_archive = $dummy['is_archive'];
	$wp_query->is_tax     = $dummy['is_tax'];

	/**
	 * Force the header back to 200 status if not a deliberate 404
	 *
	 * @see http://bbpress.trac.wordpress.org/ticket/1973
	 */
	if ( ! $wp_query->is_404() )
		status_header( 200 );

	// If we are resetting a post, we are in theme compat
	dps_set_theme_compat_active();
}

/**
 * Reset main query vars and filter 'the_content' to output a showcase
 * template part as needed.
 *
 * @since Showcase (1.0)
 * @param string $template
 * @uses dps_is_single_user() To check if page is single user
 * @uses dps_get_single_user_template() To get user template
 * @uses dps_is_single_user_edit() To check if page is single user edit
 * @uses dps_get_single_user_edit_template() To get user edit template
 * @uses dps_is_single_view() To check if page is single view
 * @uses dps_get_single_view_template() To get view template
 * @uses dps_is_search() To check if page is search
 * @uses dps_get_search_template() To get search template
 * @uses dps_is_forum_edit() To check if page is forum edit
 * @uses dps_get_forum_edit_template() To get forum edit template
 * @uses dps_is_topic_merge() To check if page is topic merge
 * @uses dps_get_topic_merge_template() To get topic merge template
 * @uses dps_is_topic_split() To check if page is topic split
 * @uses dps_get_topic_split_template() To get topic split template
 * @uses dps_is_topic_edit() To check if page is topic edit
 * @uses dps_get_topic_edit_template() To get topic edit template
 * @uses dps_is_reply_move() To check if page is reply move
 * @uses dps_get_reply_move_template() To get reply move template
 * @uses dps_is_reply_edit() To check if page is reply edit
 * @uses dps_get_reply_edit_template() To get reply edit template
 * @uses dps_set_theme_compat_template() To set the global theme compat template
 */
function dps_template_include_theme_compat( $template = '' ) {

	/**
	 * If BuddyPress is activated at a network level, the action order is
	 * reversed, which causes the template integration to fail. If we're looking
	 * at a BuddyPress page here, bail to prevent the extra processing.
	 *
	 * This is a bit more brute-force than is probably necessary, but gets the
	 * job done while we work towards something more elegant.
	 */
	if ( function_exists( 'is_buddypress' ) && is_buddypress() )
		return $template;

	/** Users *************************************************************/

	if ( dps_is_single_user_edit() || dps_is_single_user() ) {

		// Reset post
		dps_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => '',
			'post_title'     => esc_attr( dps_get_displayed_user_field( 'display_name' ) ),
			'post_status'    => dps_get_public_status_id(),
			'is_archive'     => false,
			'comment_status' => 'closed'
		) );

	/** Forums ************************************************************/

	// Forum archive
	} elseif ( dps_is_forum_archive() ) {

		// Reset post
		dps_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => dps_get_forum_archive_title(),
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => dps_get_showcase_post_type(),
			'post_status'    => dps_get_public_status_id(),
			'is_archive'     => true,
			'comment_status' => 'closed'
		) );

	// Single Forum
	} elseif ( dps_is_forum_edit() || dps_is_single_forum() ) {

		// Reset post
		dps_theme_compat_reset_post( array(
			'ID'             => dps_get_forum_id(),
			'post_title'     => dps_get_forum_title(),
			'post_author'    => dps_get_forum_author_id(),
			'post_date'      => 0,
			'post_content'   => get_post_field( 'post_content', dps_get_forum_id() ),
			'post_type'      => dps_get_showcase_post_type(),
			'post_status'    => dps_get_forum_visibility(),
			'is_single'      => true,
			'comment_status' => 'closed'
		) );

	/** Topics ************************************************************/

	// Topic archive
	} elseif ( dps_is_topic_archive() ) {

		// Reset post
		dps_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => dps_get_topic_archive_title(),
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => dps_get_topic_post_type(),
			'post_status'    => dps_get_public_status_id(),
			'is_archive'     => true,
			'comment_status' => 'closed'
		) );

	// Single Topic
	} elseif ( dps_is_topic_edit() || dps_is_single_topic() ) {

		// Reset post
		dps_theme_compat_reset_post( array(
			'ID'             => dps_get_topic_id(),
			'post_title'     => dps_get_topic_title(),
			'post_author'    => dps_get_topic_author_id(),
			'post_date'      => 0,
			'post_content'   => get_post_field( 'post_content', dps_get_topic_id() ),
			'post_type'      => dps_get_topic_post_type(),
			'post_status'    => dps_get_topic_status(),
			'is_single'      => true,
			'comment_status' => 'closed'
		) );

	/** Replies ***********************************************************/

	// Reply archive
	} elseif ( is_post_type_archive( dps_get_reply_post_type() ) ) {

		// Reset post
		dps_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => __( 'Replies', 'dps' ),
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => dps_get_reply_post_type(),
			'post_status'    => dps_get_public_status_id(),
			'comment_status' => 'closed'
		) );

	// Single Reply
	} elseif ( dps_is_reply_edit() || dps_is_single_reply() ) {

		// Reset post
		dps_theme_compat_reset_post( array(
			'ID'             => dps_get_reply_id(),
			'post_title'     => dps_get_reply_title(),
			'post_author'    => dps_get_reply_author_id(),
			'post_date'      => 0,
			'post_content'   => get_post_field( 'post_content', dps_get_reply_id() ),
			'post_type'      => dps_get_reply_post_type(),
			'post_status'    => dps_get_reply_status(),
			'comment_status' => 'closed'
		) );

	/** Views *************************************************************/

	} elseif ( dps_is_single_view() ) {

		// Reset post
		dps_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => dps_get_view_title(),
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => '',
			'post_status'    => dps_get_public_status_id(),
			'comment_status' => 'closed'
		) );

	/** Search ************************************************************/

	} elseif ( dps_is_search() ) {

		// Reset post
		dps_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_title'     => dps_get_search_title(),
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => '',
			'post_status'    => dps_get_public_status_id(),
			'comment_status' => 'closed'
		) );

	/** Topic Tags ********************************************************/

	// Topic Tag Edit
	} elseif ( dps_is_topic_tag_edit() || dps_is_topic_tag() ) {

		// Stash the current term in a new var
		set_query_var( 'dps_topic_tag', get_query_var( 'term' ) );

		// Reset the post with our new title
		dps_theme_compat_reset_post( array(
			'ID'             => 0,
			'post_author'    => 0,
			'post_date'      => 0,
			'post_content'   => '',
			'post_type'      => '',
			'post_title'     => sprintf( __( 'Topic Tag: %s', 'dps' ), '<span>' . dps_get_topic_tag_name() . '</span>' ),
			'post_status'    => dps_get_public_status_id(),
			'comment_status' => 'closed'
		) );
	}

	/**
	 * Bail if the template already matches a showcase template. This includes
	 * archive-* and single-* WordPress post_type matches (allowing
	 * themes to use the expected format) as well as all showcase-specific
	 * template files for users, topics, forums, etc...
	 *
	 * We do this after the above checks to prevent incorrect 404 body classes
	 * and header statuses.
	 *
	 * @see http://bbpress.trac.wordpress.org/ticket/1478/
	 */
	if ( !empty( showcase()->theme_compat->showcase_template ) )
		return $template;

	/**
	 * If we are relying on showcase's built in theme compatibility to load
	 * the proper content, we need to intercept the_content, replace the
	 * output, and display ours instead.
	 *
	 * To do this, we first remove all filters from 'the_content' and hook
	 * our own function into it, which runs a series of checks to determine
	 * the context, and then uses the built in shortcodes to output the
	 * correct results from inside an output buffer.
	 *
	 * Uses dps_get_theme_compat_templates() to provide fall-backs that
	 * should be coded without superfluous mark-up and logic (prev/next
	 * navigation, comments, date/time, etc...)
	 * 
	 * Hook into the 'dps_get_showcase_template' to override the array of
	 * possible templates, or 'dps_showcase_template' to override the result.
	 */
	if ( dps_is_theme_compat_active() ) {

		// Remove all filters from the_content
		dps_remove_all_filters( 'the_content' );

		// Add a filter on the_content late, which we will later remove
		add_filter( 'the_content', 'dps_replace_the_content' );

		// Find the appropriate template file
		$template = dps_get_theme_compat_templates();
	}

	return apply_filters( 'dps_template_include_theme_compat', $template );
}

/**
 * Replaces the_content() if the post_type being displayed is one that would
 * normally be handled by showcase, but proper single page templates do not
 * exist in the currently active theme.
 *
 * Note that we do *not* currently use is_main_query() here. This is because so
 * many existing themes either use query_posts() or fail to use wp_reset_query()
 * when running queries before the main loop, causing theme compat to fail.
 *
 * @since Showcase (1.0)
 * @param string $content
 * @return type
 */
function dps_replace_the_content( $content = '' ) {

	// Bail if not inside the query loop
	if ( ! in_the_loop() )
		return $content;

	// Define local variable(s)
	$new_content = '';

	// Bail if shortcodes are unset somehow
	if ( !is_a( showcase()->shortcodes, 'DPS_Shortcodes' ) )
		return $content;

	// Use shortcode API to display forums/topics/replies because they are
	// already output buffered and ready to fit inside the_content

	/** Users *************************************************************/

	// Profile View
	if ( dps_is_single_user_edit() || dps_is_single_user() ) {
		ob_start();

		dps_get_template_part( 'content', 'single-user' );

		$new_content = ob_get_contents();

		ob_end_clean();

	/** Forums ************************************************************/

	// Forum archive
	} elseif ( dps_is_forum_archive() ) {

		// Page exists where this archive should be
		$page = dps_get_page_by_path( dps_get_root_slug() );
		if ( !empty( $page ) ) {

			// Restore previously unset filters
			dps_restore_all_filters( 'the_content' );

			// Remove 'dps_replace_the_content' filter to prevent infinite loops
			remove_filter( 'the_content', 'dps_replace_the_content' );

			// Start output buffer
			ob_start();

			// Grab the content of this page
			$new_content = apply_filters( 'the_content', $page->post_content );

			// Clean up the buffer
			ob_end_clean();

			// Add 'dps_replace_the_content' filter back (@see $this::start())
			add_filter( 'the_content', 'dps_replace_the_content' );

		// No page so show the archive
		} else {
			$new_content = showcase()->shortcodes->display_forum_index();
		}

	// Forum Edit
	} elseif ( dps_is_forum_edit() ) {
		$new_content = showcase()->shortcodes->display_forum_form();

	// Single Forum
	} elseif ( dps_is_single_forum() ) {
		$new_content = showcase()->shortcodes->display_forum( array( 'id' => get_the_ID() ) );

	/** Topics ************************************************************/

	// Topic archive
	} elseif ( dps_is_topic_archive() ) {

		// Page exists where this archive should be
		$page = dps_get_page_by_path( dps_get_topic_archive_slug() );
		if ( !empty( $page ) ) {

			// Restore previously unset filters
			dps_restore_all_filters( 'the_content' );

			// Remove 'dps_replace_the_content' filter to prevent infinite loops
			remove_filter( 'the_content', 'dps_replace_the_content' );

			// Start output buffer
			ob_start();

			// Grab the content of this page
			$new_content = apply_filters( 'the_content', $page->post_content );

			// Clean up the buffer
			ob_end_clean();

			// Add 'dps_replace_the_content' filter back (@see $this::start())
			add_filter( 'the_content', 'dps_replace_the_content' );

		// No page so show the archive
		} else {
			$new_content = showcase()->shortcodes->display_topic_index();
		}

	// Topic Edit
	} elseif ( dps_is_topic_edit() ) {

		// Split
		if ( dps_is_topic_split() ) {
			ob_start();

			dps_get_template_part( 'form', 'topic-split' );

			$new_content = ob_get_contents();

			ob_end_clean();

		// Merge
		} elseif ( dps_is_topic_merge() ) {
			ob_start();

			dps_get_template_part( 'form', 'topic-merge' );

			$new_content = ob_get_contents();

			ob_end_clean();

		// Edit
		} else {
			$new_content = showcase()->shortcodes->display_topic_form();
		}

	// Single Topic
	} elseif ( dps_is_single_topic() ) {
		$new_content = showcase()->shortcodes->display_topic( array( 'id' => get_the_ID() ) );

	/** Replies ***********************************************************/

	// Reply archive
	} elseif ( is_post_type_archive( dps_get_reply_post_type() ) ) {
		//$new_content = showcase()->shortcodes->display_reply_index();

	// Reply Edit
	} elseif ( dps_is_reply_edit() ) {
	
		// Move
		if ( dps_is_reply_move() ) {
			ob_start();

			dps_get_template_part( 'form', 'reply-move' );

			$new_content = ob_get_contents();

			ob_end_clean();
	
		// Edit
		} else {
			$new_content = showcase()->shortcodes->display_reply_form();
		}

	// Single Reply
	} elseif ( dps_is_single_reply() ) {
		$new_content = showcase()->shortcodes->display_reply( array( 'id' => get_the_ID() ) );

	/** Views *************************************************************/

	} elseif ( dps_is_single_view() ) {
		$new_content = showcase()->shortcodes->display_view( array( 'id' => get_query_var( 'dps_view' ) ) );

	/** Search ************************************************************/

	} elseif ( dps_is_search() ) {
		$new_content = showcase()->shortcodes->display_search( array( 'search' => get_query_var( 'dps_search' ) ) );

	/** Topic Tags ********************************************************/

	// Show topics of tag
	} elseif ( dps_is_topic_tag() ) {
		$new_content = showcase()->shortcodes->display_topics_of_tag( array( 'id' => dps_get_topic_tag_id() ) );

	// Edit topic tag
	} elseif ( dps_is_topic_tag_edit() ) {
		$new_content = showcase()->shortcodes->display_topic_tag_form();
	}

	// Juggle the content around and try to prevent unsightly comments
	if ( !empty( $new_content ) && ( $new_content != $content ) ) {

		// Set the content to be the new content
		$content = apply_filters( 'dps_replace_the_content', $new_content, $content );

		// Clean up after ourselves
		unset( $new_content );

		// Reset the $post global
		wp_reset_postdata();
	}

	// Return possibly hi-jacked content
	return $content;
}

/** Filters *******************************************************************/

/**
 * Removes all filters from a WordPress filter, and stashes them in the $bbp
 * global in the event they need to be restored later.
 *
 * @since Showcase (1.0)
 * @global WP_filter $wp_filter
 * @global array $merged_filters
 * @param string $tag
 * @param int $priority
 * @return bool
 */
function dps_remove_all_filters( $tag, $priority = false ) {
	global $wp_filter, $merged_filters;

	// Filters exist
	if ( isset( $wp_filter[$tag] ) ) {

		// Filters exist in this priority
		if ( !empty( $priority ) && isset( $wp_filter[$tag][$priority] ) ) {

			// Store filters in a backup
			showcase()->filters->wp_filter[$tag][$priority] = $wp_filter[$tag][$priority];

			// Unset the filters
			unset( $wp_filter[$tag][$priority] );

		// Priority is empty
		} else {

			// Store filters in a backup
			showcase()->filters->wp_filter[$tag] = $wp_filter[$tag];

			// Unset the filters
			unset( $wp_filter[$tag] );
		}
	}

	// Check merged filters
	if ( isset( $merged_filters[$tag] ) ) {

		// Store filters in a backup
		showcase()->filters->merged_filters[$tag] = $merged_filters[$tag];

		// Unset the filters
		unset( $merged_filters[$tag] );
	}

	return true;
}

/**
 * Restores filters from the $bbp global that were removed using
 * dps_remove_all_filters()
 *
 * @since Showcase (1.0)
 * @global WP_filter $wp_filter
 * @global array $merged_filters
 * @param string $tag
 * @param int $priority
 * @return bool
 */
function dps_restore_all_filters( $tag, $priority = false ) {
	global $wp_filter, $merged_filters;

	// Filters exist
	if ( isset( showcase()->filters->wp_filter[$tag] ) ) {

		// Filters exist in this priority
		if ( !empty( $priority ) && isset( showcase()->filters->wp_filter[$tag][$priority] ) ) {

			// Store filters in a backup
			$wp_filter[$tag][$priority] = showcase()->filters->wp_filter[$tag][$priority];

			// Unset the filters
			unset( showcase()->filters->wp_filter[$tag][$priority] );

		// Priority is empty
		} else {

			// Store filters in a backup
			$wp_filter[$tag] = showcase()->filters->wp_filter[$tag];

			// Unset the filters
			unset( showcase()->filters->wp_filter[$tag] );
		}
	}

	// Check merged filters
	if ( isset( showcase()->filters->merged_filters[$tag] ) ) {

		// Store filters in a backup
		$merged_filters[$tag] = showcase()->filters->merged_filters[$tag];

		// Unset the filters
		unset( showcase()->filters->merged_filters[$tag] );
	}

	return true;
}
