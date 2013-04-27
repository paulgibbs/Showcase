<?php

/**
 * Showcase Actions
 *
 * @package Showcase
 * @subpackage Core
 *
 * This file contains the actions that are used through-out showcase. They are
 * consolidated here to make searching for them easier, and to help developers
 * understand at a glance the order in which things occur.
 *
 * There are a few common places that additional actions can currently be found
 *
 *  - showcase: In {@link Showcase::setup_actions()} in showcase.php
 *  - Admin: More in {@link BB_Admin::setup_actions()} in admin.php
 *
 * @see /core/filters.php
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Attach showcase to WordPress
 *
 * Showcase uses its own internal actions to help aid in third-party plugin
 * development, and to limit the amount of potential future code changes when
 * updates to WordPress core occur.
 *
 * These actions exist to create the concept of 'plugin dependencies'. They
 * provide a safe way for plugins to execute code *only* when showcase is
 * installed and activated, without needing to do complicated guesswork.
 *
 * For more information on how this works, see the 'Plugin Dependency' section
 * near the bottom of this file.
 *
 *           v--WordPress Actions        v--showcase Sub-actions
 */
add_action( 'plugins_loaded',           'dps_loaded',                   10    );
add_action( 'init',                     'dps_init',                     0     ); // Early for dps_register
add_action( 'parse_query',              'dps_parse_query',              2     ); // Early for overrides
add_action( 'widgets_init',             'dps_widgets_init',             10    );
add_action( 'generate_rewrite_rules',   'dps_generate_rewrite_rules',   10    );
add_action( 'wp_enqueue_scripts',       'dps_enqueue_scripts',          10    );
add_action( 'wp_head',                  'dps_head',                     10    );
add_action( 'wp_footer',                'dps_footer',                   10    );
add_action( 'set_current_user',         'dps_setup_current_user',       10    );
add_action( 'setup_theme',              'dps_setup_theme',              10    );
add_action( 'after_setup_theme',        'dps_after_setup_theme',        10    );
add_action( 'template_redirect',        'dps_template_redirect',        8     ); // Before BuddyPress's 10 [BB2225]
add_action( 'login_form_login',         'dps_login_form_login',         10    );
add_action( 'profile_update',           'dps_profile_update',           10, 2 ); // user_id and old_user_data
add_action( 'user_register',            'dps_user_register',            10    );

/**
 * dps_loaded - Attached to 'plugins_loaded' above
 *
 * Attach various loader actions to the dps_loaded action.
 * The load order helps to execute code at the correct time.
 *                                                         v---Load order
 */
add_action( 'dps_loaded', 'dps_constants',                 2  );
add_action( 'dps_loaded', 'dps_boot_strap_globals',        4  );
add_action( 'dps_loaded', 'dps_includes',                  6  );
add_action( 'dps_loaded', 'dps_setup_globals',             8  );
add_action( 'dps_loaded', 'dps_setup_option_filters',      10 );
add_action( 'dps_loaded', 'dps_register_theme_packages',   14 );
add_action( 'dps_loaded', 'dps_filter_user_roles_option',  16 );

/**
 * dps_init - Attached to 'init' above
 *
 * Attach various initialization actions to the init action.
 * The load order helps to execute code at the correct time.
 *                                              v---Load order
 */
add_action( 'dps_init', 'dps_load_textdomain',  0   );
add_action( 'dps_init', 'dps_register',         0   );
add_action( 'dps_init', 'dps_add_rewrite_tags', 20  );
add_action( 'dps_init', 'dps_ready',            999 );

/**
 * There is no action API for roles to use, so hook in immediately after
 * everything is included (including the theme's functions.php. This is after
 * the $wp_roles global is set but before $wp->init().
 *
 * If it's hooked in any sooner, role names may not be translated correctly.
 *
 * @link http://bbpress.trac.wordpress.org/ticket/2219
 *
 * This is kind of lame, but is all we have for now.
 */
add_action( 'dps_after_setup_theme', 'dps_add_forums_roles', 1 );

/**
 * dps_register - Attached to 'init' above on 0 priority
 *
 * Attach various initialization actions early to the init action.
 * The load order helps to execute code at the correct time.
 *                                                         v---Load order
 */
add_action( 'dps_register', 'dps_register_post_types',     2  );
add_action( 'dps_register', 'dps_register_post_statuses',  4  );
add_action( 'dps_register', 'dps_register_taxonomies',     6  );
add_action( 'dps_register', 'dps_register_views',          8  );
add_action( 'dps_register', 'dps_register_shortcodes',     10 );

/**
 * dps_ready - attached to end 'dps_init' above
 *
 * Attach actions to the ready action after showcase has fully initialized.
 * The load order helps to execute code at the correct time.
 */
//add_action( 'dps_ready', '???', );

// Try to load the showcase-functions.php file from the active themes
add_action( 'dps_after_setup_theme', 'dps_load_theme_functions', 10 );

// Widgets
add_action( 'dps_widgets_init', array( 'BB_Login_Widget',   'register_widget' ), 10 );
add_action( 'dps_widgets_init', array( 'BB_Views_Widget',   'register_widget' ), 10 );
add_action( 'dps_widgets_init', array( 'BB_Search_Widget',  'register_widget' ), 10 );
add_action( 'dps_widgets_init', array( 'BB_Forums_Widget',  'register_widget' ), 10 );
add_action( 'dps_widgets_init', array( 'BB_Topics_Widget',  'register_widget' ), 10 );
add_action( 'dps_widgets_init', array( 'BB_Replies_Widget', 'register_widget' ), 10 );
add_action( 'dps_widgets_init', array( 'BB_Stats_Widget',   'register_widget' ), 10 );

// Notices (loaded after dps_init for translations)
add_action( 'dps_head',             'dps_login_notices'    );
add_action( 'dps_head',             'dps_topic_notices'    );
add_action( 'dps_template_notices', 'dps_template_notices' );

// Profile Page Messages
add_action( 'dps_template_notices', 'dps_notice_edit_user_success'           );
add_action( 'dps_template_notices', 'dps_notice_edit_user_is_super_admin', 2 );

// Before Delete/Trash/Untrash Topic
add_action( 'wp_trash_post', 'dps_trash_forum'   );
add_action( 'trash_post',    'dps_trash_forum'   );
add_action( 'untrash_post',  'dps_untrash_forum' );
add_action( 'delete_post',   'dps_delete_forum'  );

// After Deleted/Trashed/Untrashed Topic
add_action( 'trashed_post',   'dps_trashed_forum'   );
add_action( 'untrashed_post', 'dps_untrashed_forum' );
add_action( 'deleted_post',   'dps_deleted_forum'   );

// Auto trash/untrash/delete a forums topics
add_action( 'dps_delete_forum',  'dps_delete_forum_topics',  10 );
add_action( 'dps_trash_forum',   'dps_trash_forum_topics',   10 );
add_action( 'dps_untrash_forum', 'dps_untrash_forum_topics', 10 );

// New/Edit Forum
add_action( 'dps_new_forum',  'dps_update_forum', 10 );
add_action( 'dps_edit_forum', 'dps_update_forum', 10 );

// Save forum extra metadata
add_action( 'dps_new_forum_post_extras',         'dps_save_forum_extras', 2 );
add_action( 'dps_edit_forum_post_extras',        'dps_save_forum_extras', 2 );
add_action( 'dps_forum_attributes_metabox_save', 'dps_save_forum_extras', 2 );

// New/Edit Reply
add_action( 'dps_new_reply',  'dps_update_reply', 10, 6 );
add_action( 'dps_edit_reply', 'dps_update_reply', 10, 6 );

// Before Delete/Trash/Untrash Reply
add_action( 'wp_trash_post', 'dps_trash_reply'   );
add_action( 'trash_post',    'dps_trash_reply'   );
add_action( 'untrash_post',  'dps_untrash_reply' );
add_action( 'delete_post',   'dps_delete_reply'  );

// After Deleted/Trashed/Untrashed Reply
add_action( 'trashed_post',   'dps_trashed_reply'   );
add_action( 'untrashed_post', 'dps_untrashed_reply' );
add_action( 'deleted_post',   'dps_deleted_reply'   );

// New/Edit Topic
add_action( 'dps_new_topic',  'dps_update_topic', 10, 5 );
add_action( 'dps_edit_topic', 'dps_update_topic', 10, 5 );

// Split/Merge Topic
add_action( 'dps_merged_topic',     'dps_merge_topic_count', 1, 3 );
add_action( 'dps_post_split_topic', 'dps_split_topic_count', 1, 3 );

// Move Reply
add_action( 'dps_post_move_reply', 'dps_move_reply_count', 1, 3 );

// Before Delete/Trash/Untrash Topic
add_action( 'wp_trash_post', 'dps_trash_topic'   );
add_action( 'trash_post',    'dps_trash_topic'   );
add_action( 'untrash_post',  'dps_untrash_topic' );
add_action( 'delete_post',   'dps_delete_topic'  );

// After Deleted/Trashed/Untrashed Topic
add_action( 'trashed_post',   'dps_trashed_topic'   );
add_action( 'untrashed_post', 'dps_untrashed_topic' );
add_action( 'deleted_post',   'dps_deleted_topic'   );

// Favorites
add_action( 'dps_trash_topic',  'dps_remove_topic_from_all_favorites' );
add_action( 'dps_delete_topic', 'dps_remove_topic_from_all_favorites' );

// Subscriptions
add_action( 'dps_trash_topic',  'dps_remove_topic_from_all_subscriptions'       );
add_action( 'dps_delete_topic', 'dps_remove_topic_from_all_subscriptions'       );
add_action( 'dps_new_reply',    'dps_notify_subscribers',                 11, 5 );

// Sticky
add_action( 'dps_trash_topic',  'dps_unstick_topic' );
add_action( 'dps_delete_topic', 'dps_unstick_topic' );

// Update topic branch
add_action( 'dps_trashed_topic',   'dps_update_topic_walker' );
add_action( 'dps_untrashed_topic', 'dps_update_topic_walker' );
add_action( 'dps_deleted_topic',   'dps_update_topic_walker' );
add_action( 'dps_spammed_topic',   'dps_update_topic_walker' );
add_action( 'dps_unspammed_topic', 'dps_update_topic_walker' );

// Update reply branch
add_action( 'dps_trashed_reply',   'dps_update_reply_walker' );
add_action( 'dps_untrashed_reply', 'dps_update_reply_walker' );
add_action( 'dps_deleted_reply',   'dps_update_reply_walker' );
add_action( 'dps_spammed_reply',   'dps_update_reply_walker' );
add_action( 'dps_unspammed_reply', 'dps_update_reply_walker' );

// User status
// @todo make these sub-actions
add_action( 'make_ham_user',  'dps_make_ham_user'  );
add_action( 'make_spam_user', 'dps_make_spam_user' );

// User role
add_action( 'dps_profile_update', 'dps_profile_update_role' );

// Hook WordPress admin actions to showcase profiles on save
add_action( 'dps_user_edit_after', 'dps_user_edit_after' );

// Caches
add_action( 'dps_new_forum_pre_extras',  'dps_clean_post_cache' );
add_action( 'dps_new_forum_post_extras', 'dps_clean_post_cache' );
add_action( 'dps_new_topic_pre_extras',  'dps_clean_post_cache' );
add_action( 'dps_new_topic_post_extras', 'dps_clean_post_cache' );
add_action( 'dps_new_reply_pre_extras',  'dps_clean_post_cache' );
add_action( 'dps_new_reply_post_extras', 'dps_clean_post_cache' );

/**
 * Showcase needs to redirect the user around in a few different circumstances:
 *
 * 1. POST and GET requests
 * 2. Accessing private or hidden content (forums/topics/replies)
 * 3. Editing forums, topics, replies, users, and tags
 * 4. showcase specific AJAX requests
 */
add_action( 'dps_template_redirect', 'dps_forum_enforce_blocked', 1  );
add_action( 'dps_template_redirect', 'dps_forum_enforce_hidden',  1  );
add_action( 'dps_template_redirect', 'dps_forum_enforce_private', 1  );
add_action( 'dps_template_redirect', 'dps_post_request',          10 );
add_action( 'dps_template_redirect', 'dps_get_request',           10 );
add_action( 'dps_template_redirect', 'dps_check_user_edit',       10 );
add_action( 'dps_template_redirect', 'dps_check_forum_edit',      10 );
add_action( 'dps_template_redirect', 'dps_check_topic_edit',      10 );
add_action( 'dps_template_redirect', 'dps_check_reply_edit',      10 );
add_action( 'dps_template_redirect', 'dps_check_topic_tag_edit',  10 );

// Theme-side POST requests
add_action( 'dps_post_request', 'dps_do_ajax',                1  );
add_action( 'dps_post_request', 'dps_edit_topic_tag_handler', 1  );
add_action( 'dps_post_request', 'dps_edit_user_handler',      1  );
add_action( 'dps_post_request', 'dps_edit_forum_handler',     1  );
add_action( 'dps_post_request', 'dps_edit_reply_handler',     1  );
add_action( 'dps_post_request', 'dps_edit_topic_handler',     1  );
add_action( 'dps_post_request', 'dps_merge_topic_handler',    1  );
add_action( 'dps_post_request', 'dps_split_topic_handler',    1  );
add_action( 'dps_post_request', 'dps_move_reply_handler',     1  );
add_action( 'dps_post_request', 'dps_new_forum_handler',      10 );
add_action( 'dps_post_request', 'dps_new_reply_handler',      10 );
add_action( 'dps_post_request', 'dps_new_topic_handler',      10 );

// Theme-side GET requests
add_action( 'dps_get_request', 'dps_toggle_topic_handler',   1  );
add_action( 'dps_get_request', 'dps_toggle_reply_handler',   1  );
add_action( 'dps_get_request', 'dps_favorites_handler',      1  );
add_action( 'dps_get_request', 'dps_subscriptions_handler',  1  );

// Maybe convert the users password
add_action( 'dps_login_form_login', 'dps_user_maybe_convert_pass' );

add_action( 'dps_activation', 'dps_add_activation_redirect' );
