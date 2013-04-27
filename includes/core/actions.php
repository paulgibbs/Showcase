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
 *  - Admin: More in {@link DPS_Admin::setup_actions()} in admin.php
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

add_action( 'dps_register', 'dps_register_post_types',     2  );
add_action( 'dps_register', 'dps_register_post_statuses',  4  );
add_action( 'dps_register', 'dps_register_taxonomies',     6  );
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

// Notices (loaded after dps_init for translations)
add_action( 'dps_template_notices', 'dps_template_notices' );

// User status
// @todo make these sub-actions
add_action( 'make_ham_user',  'dps_make_ham_user'  );
add_action( 'make_spam_user', 'dps_make_spam_user' );

// Template redirects
add_action( 'dps_template_redirect', 'dps_post_request',          10 );
add_action( 'dps_template_redirect', 'dps_get_request',           10 );
