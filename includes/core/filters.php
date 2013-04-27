<?php

/**
 * Showcase Filters
 *
 * @package Showcase
 * @subpackage Core
 *
 * This file contains the filters that are used through-out showcase. They are
 * consolidated here to make searching for them easier, and to help developers
 * understand at a glance the order in which things occur.
 *
 * There are a few common places that additional filters can currently be found
 *
 *  - showcase: In {@link Showcase::setup_actions()} in showcase.php
 *  - Admin: More in {@link DPS_Admin::setup_actions()} in admin.php
 *
 * @see /core/actions.php
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
 *           v--WordPress Actions       v--showcase Sub-actions
 */
add_filter( 'request',                 'dps_request',            10    );
add_filter( 'template_include',        'dps_template_include',   10    );
add_filter( 'wp_title',                'dps_title',              10, 3 );
add_filter( 'body_class',              'dps_body_class',         10, 2 );
add_filter( 'plugin_locale',           'dps_plugin_locale',      10, 2 );

/**
 * Template Compatibility
 *
 * If you want to completely bypass this and manage your own custom showcase
 * template hierarchy, start here by removing this filter, then look at how
 * dps_template_include() works and do something similar. :)
 */
add_filter( 'dps_template_include',   'dps_template_include_theme_supports', 2, 1 );
add_filter( 'dps_template_include',   'dps_template_include_theme_compat',   4, 2 );

// Filter showcase template locations
add_filter( 'dps_get_template_stack', 'dps_add_template_stack_locations' );
