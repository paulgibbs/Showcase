<?php

/**
 * Showcase Filters
 *
 * @package Showcase
 * @subpackage Core
 *
 * This file contains the filters that are used through-out barebones. They are
 * consolidated here to make searching for them easier, and to help developers
 * understand at a glance the order in which things occur.
 *
 * There are a few common places that additional filters can currently be found
 *
 *  - barebones: In {@link Showcase::setup_actions()} in barebones.php
 *  - Admin: More in {@link BB_Admin::setup_actions()} in admin.php
 *
 * @see /core/actions.php
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Attach barebones to WordPress
 *
 * Showcase uses its own internal actions to help aid in third-party plugin
 * development, and to limit the amount of potential future code changes when
 * updates to WordPress core occur.
 *
 * These actions exist to create the concept of 'plugin dependencies'. They
 * provide a safe way for plugins to execute code *only* when barebones is
 * installed and activated, without needing to do complicated guesswork.
 *
 * For more information on how this works, see the 'Plugin Dependency' section
 * near the bottom of this file.
 *
 *           v--WordPress Actions       v--barebones Sub-actions
 */
add_filter( 'request',                 'dps_request',            10    );
add_filter( 'template_include',        'dps_template_include',   10    );
add_filter( 'wp_title',                'dps_title',              10, 3 );
add_filter( 'body_class',              'dps_body_class',         10, 2 );
add_filter( 'map_meta_cap',            'dps_map_meta_caps',      10, 4 );
add_filter( 'allowed_themes',          'dps_allowed_themes',     10    );
add_filter( 'redirect_canonical',      'dps_redirect_canonical', 10    );
add_filter( 'login_redirect',          'dps_redirect_login',     2,  3 );
add_filter( 'logout_url',              'dps_logout_url',         2,  2 );
add_filter( 'plugin_locale',           'dps_plugin_locale',      10, 2 );

// Fix post author id for anonymous posts (set it back to 0) when the post status is changed
add_filter( 'wp_insert_post_data', 'dps_fix_post_author', 30, 2 );

// Force comments_status on barebones post types
add_filter( 'comments_open', 'dps_force_comment_status' );

// Add post_parent__in to posts_where
add_filter( 'posts_where', 'dps_query_post_parent__in', 10, 2 );

// Remove forums roles from list of all roles
add_filter( 'editable_roles', 'dps_filter_blog_editable_roles' );

/**
 * Feeds
 *
 * Showcase comes with a number of custom RSS2 feeds that get handled outside
 * the normal scope of feeds that WordPress would normally serve. To do this,
 * we filter every page request, listen for a feed request, and trap it.
 */
add_filter( 'dps_request', 'dps_request_feed_trap' );

/**
 * Template Compatibility
 *
 * If you want to completely bypass this and manage your own custom barebones
 * template hierarchy, start here by removing this filter, then look at how
 * dps_template_include() works and do something similar. :)
 */
add_filter( 'dps_template_include',   'dps_template_include_theme_supports', 2, 1 );
add_filter( 'dps_template_include',   'dps_template_include_theme_compat',   4, 2 );

// Filter barebones template locations
add_filter( 'dps_get_template_stack', 'dps_add_template_stack_locations'          );

// Links
add_filter( 'paginate_links',            'dps_add_view_all' );
add_filter( 'dps_get_topic_permalink',   'dps_add_view_all' );
add_filter( 'dps_get_reply_permalink',   'dps_add_view_all' );
add_filter( 'dps_get_forum_permalink',   'dps_add_view_all' );

// wp_filter_kses on new/edit topic/reply title
add_filter( 'dps_new_reply_pre_title',    'wp_filter_kses'  );
add_filter( 'dps_new_topic_pre_title',    'wp_filter_kses'  );
add_filter( 'dps_edit_reply_pre_title',   'wp_filter_kses'  );
add_filter( 'dps_edit_topic_pre_title',   'wp_filter_kses'  );

// Code filters on output (hooked in early for plugin compatibility)
add_filter( 'dps_get_reply_content', 'dps_code_trick', 3 );
add_filter( 'dps_get_topic_content', 'dps_code_trick', 3 );

// Code filters on input
add_filter( 'dps_new_reply_pre_content',  'dps_code_trick_reverse' );
add_filter( 'dps_edit_reply_pre_content', 'dps_code_trick_reverse' );
add_filter( 'dps_new_topic_pre_content',  'dps_code_trick_reverse' );
add_filter( 'dps_edit_topic_pre_content', 'dps_code_trick_reverse' );

// balanceTags, wp_filter_kses and wp_rel_nofollow on new/edit topic/reply text
add_filter( 'dps_new_reply_pre_content',  'wp_rel_nofollow'    );
add_filter( 'dps_new_reply_pre_content',  'dps_filter_kses'    );
add_filter( 'dps_new_reply_pre_content',  'balanceTags',    50 );
add_filter( 'dps_new_topic_pre_content',  'wp_rel_nofollow'    );
add_filter( 'dps_new_topic_pre_content',  'dps_filter_kses'    );
add_filter( 'dps_new_topic_pre_content',  'balanceTags',    50 );
add_filter( 'dps_edit_reply_pre_content', 'wp_rel_nofollow'    );
add_filter( 'dps_edit_reply_pre_content', 'dps_filter_kses'    );
add_filter( 'dps_edit_reply_pre_content', 'balanceTags',    50 );
add_filter( 'dps_edit_topic_pre_content', 'wp_rel_nofollow'    );
add_filter( 'dps_edit_topic_pre_content', 'dps_filter_kses'    );
add_filter( 'dps_edit_topic_pre_content', 'balanceTags',    50 );

// No follow and stripslashes on user profile links
add_filter( 'dps_get_reply_author_link',      'wp_rel_nofollow' );
add_filter( 'dps_get_reply_author_link',      'stripslashes'    );
add_filter( 'dps_get_topic_author_link',      'wp_rel_nofollow' );
add_filter( 'dps_get_topic_author_link',      'stripslashes'    );
add_filter( 'dps_get_user_favorites_link',    'wp_rel_nofollow' );
add_filter( 'dps_get_user_favorites_link',    'stripslashes'    );
add_filter( 'dps_get_user_subscribe_link',    'wp_rel_nofollow' );
add_filter( 'dps_get_user_subscribe_link',    'stripslashes'    );
add_filter( 'dps_get_user_profile_link',      'wp_rel_nofollow' );
add_filter( 'dps_get_user_profile_link',      'stripslashes'    );
add_filter( 'dps_get_user_profile_edit_link', 'wp_rel_nofollow' );
add_filter( 'dps_get_user_profile_edit_link', 'stripslashes'    );

// Run filters on reply content
add_filter( 'dps_get_reply_content', 'make_clickable',     4    );
add_filter( 'dps_get_reply_content', 'dps_mention_filter', 5    );
add_filter( 'dps_get_reply_content', 'wptexturize',        6    );
add_filter( 'dps_get_reply_content', 'convert_chars',      8    );
add_filter( 'dps_get_reply_content', 'capital_P_dangit',   10   );
add_filter( 'dps_get_reply_content', 'convert_smilies',    20   );
add_filter( 'dps_get_reply_content', 'force_balance_tags', 30   );
add_filter( 'dps_get_reply_content', 'wpautop',            40   );

// Run filters on topic content
add_filter( 'dps_get_topic_content', 'make_clickable',     4    );
add_filter( 'dps_get_topic_content', 'dps_mention_filter', 5    );
add_filter( 'dps_get_topic_content', 'wptexturize',        6    );
add_filter( 'dps_get_topic_content', 'convert_chars',      8    );
add_filter( 'dps_get_topic_content', 'capital_P_dangit',   10   );
add_filter( 'dps_get_topic_content', 'convert_smilies',    20   );
add_filter( 'dps_get_topic_content', 'force_balance_tags', 30   );
add_filter( 'dps_get_topic_content', 'wpautop',            40   );

// Add number format filter to functions requiring numeric output
add_filter( 'dps_get_user_topic_count',     'dps_number_format', 10 );
add_filter( 'dps_get_user_reply_count',     'dps_number_format', 10 );
add_filter( 'dps_get_user_post_count',      'dps_number_format', 10 );
add_filter( 'dps_get_forum_subforum_count', 'dps_number_format', 10 );
add_filter( 'dps_get_forum_topic_count',    'dps_number_format', 10 );
add_filter( 'dps_get_forum_reply_count',    'dps_number_format', 10 );
add_filter( 'dps_get_forum_post_count',     'dps_number_format', 10 );
add_filter( 'dps_get_topic_voice_count',    'dps_number_format', 10 );
add_filter( 'dps_get_topic_reply_count',    'dps_number_format', 10 );
add_filter( 'dps_get_topic_post_count',     'dps_number_format', 10 );

// Run wp_kses_data on topic/reply content in admin section
if ( is_admin() ) {
	add_filter( 'dps_get_reply_content', 'dps_kses_data' );
	add_filter( 'dps_get_topic_content', 'dps_kses_data' );

// Revisions (only when not in admin)
} else {
	add_filter( 'dps_get_reply_content', 'dps_reply_content_append_revisions',  99,  2 );
	add_filter( 'dps_get_topic_content', 'dps_topic_content_append_revisions',  99,  2 );
}

// Suppress private forum details
add_filter( 'dps_get_forum_topic_count',    'dps_suppress_private_forum_meta',  10, 2 );
add_filter( 'dps_get_forum_reply_count',    'dps_suppress_private_forum_meta',  10, 2 );
add_filter( 'dps_get_forum_post_count',     'dps_suppress_private_forum_meta',  10, 2 );
add_filter( 'dps_get_forum_freshness_link', 'dps_suppress_private_forum_meta',  10, 2 );
add_filter( 'dps_get_author_link',          'dps_suppress_private_author_link', 10, 2 );
add_filter( 'dps_get_topic_author_link',    'dps_suppress_private_author_link', 10, 2 );
add_filter( 'dps_get_reply_author_link',    'dps_suppress_private_author_link', 10, 2 );

// Topic and reply author display names
add_filter( 'dps_get_topic_author_display_name', 'wptexturize'   );
add_filter( 'dps_get_topic_author_display_name', 'convert_chars' );
add_filter( 'dps_get_topic_author_display_name', 'esc_html'      );
add_filter( 'dps_get_reply_author_display_name', 'wptexturize'   );
add_filter( 'dps_get_reply_author_display_name', 'convert_chars' );
add_filter( 'dps_get_reply_author_display_name', 'esc_html'      );

/**
 * Add filters to anonymous post author data
 */
// Post author name
add_filter( 'dps_pre_anonymous_post_author_name',    'trim',                10 );
add_filter( 'dps_pre_anonymous_post_author_name',    'sanitize_text_field', 10 );
add_filter( 'dps_pre_anonymous_post_author_name',    'wp_filter_kses',      10 );
add_filter( 'dps_pre_anonymous_post_author_name',    '_wp_specialchars',    30 );

// Save email
add_filter( 'dps_pre_anonymous_post_author_email',   'trim',                10 );
add_filter( 'dps_pre_anonymous_post_author_email',   'sanitize_email',      10 );
add_filter( 'dps_pre_anonymous_post_author_email',   'wp_filter_kses',      10 );

// Save URL
add_filter( 'dps_pre_anonymous_post_author_website', 'trim',                10 );
add_filter( 'dps_pre_anonymous_post_author_website', 'wp_strip_all_tags',   10 );
add_filter( 'dps_pre_anonymous_post_author_website', 'esc_url_raw',         10 );
add_filter( 'dps_pre_anonymous_post_author_website', 'wp_filter_kses',      10 );

// Queries
add_filter( 'posts_request', '_dps_has_replies_where', 10, 2 );

// Capabilities
add_filter( 'dps_map_meta_caps', 'dps_map_primary_meta_caps',   10, 4 ); // Primary caps
add_filter( 'dps_map_meta_caps', 'dps_map_forum_meta_caps',     10, 4 ); // Forums
add_filter( 'dps_map_meta_caps', 'dps_map_topic_meta_caps',     10, 4 ); // Topics
add_filter( 'dps_map_meta_caps', 'dps_map_reply_meta_caps',     10, 4 ); // Replies
add_filter( 'dps_map_meta_caps', 'dps_map_topic_tag_meta_caps', 10, 4 ); // Topic tags

/** Deprecated ****************************************************************/

/**
 * The following filters are deprecated.
 *
 * These filters were most likely replaced by dps_parse_args(), which includes
 * both passive and aggressive filters anywhere parse_args is used to compare
 * default arguments to passed arguments, without needing to litter the
 * codebase with _before_ and _after_ filters everywhere.
 */

/**
 * Deprecated locale filter
 *
 * @since Showcase (1.0)
 *
 * @param type $locale
 * @return type
 */
function _dps_filter_locale( $locale = '' ) {
	return apply_filters( 'barebones_locale', $locale );
}
add_filter( 'dps_plugin_locale', '_dps_filter_locale', 10, 1 );

/**
 * Deprecated forums query filter
 *
 * @since Showcase (1.0)
 * @param type $args
 * @return type
 */
function _dps_has_forums_query( $args = array() ) {
	return apply_filters( 'dps_has_forums_query', $args );
}
add_filter( 'dps_after_has_forums_parse_args', '_dps_has_forums_query' );

/**
 * Deprecated topics query filter
 *
 * @since Showcase (1.0)
 * @param type $args
 * @return type
 */
function _dps_has_topics_query( $args = array() ) {
	return apply_filters( 'dps_has_topics_query', $args );
}
add_filter( 'dps_after_has_topics_parse_args', '_dps_has_topics_query' );

/**
 * Deprecated replies query filter
 *
 * @since Showcase (1.0)
 * @param type $args
 * @return type
 */
function _dps_has_replies_query( $args = array() ) {
	return apply_filters( 'dps_has_replies_query', $args );
}
add_filter( 'dps_after_has_replies_parse_args', '_dps_has_replies_query' );
