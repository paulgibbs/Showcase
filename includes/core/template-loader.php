<?php

/**
 * Showcase Template Loader
 *
 * @package Showcase
 * @subpackage TemplateLoader
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Possibly intercept the template being loaded
 *
 * Listens to the 'template_include' filter and waits for any showcase specific
 * template condition to be met. If one is met and the template file exists,
 * it will be used; otherwise 
 *
 * Note that the _edit() checks are ahead of their counterparts, to prevent them
 * from being stomped on accident.
 *
 * @since Showcase (1.0)
 * @param string $template
 * @return string The path to the template file that is being used
 */
function dps_template_include_theme_supports( $template = '' ) {

	// Single showcase
	if ( dps_is_single_showcase()      && ( $new_template = dps_get_single_showcase_template()  ) ) :

	// Showcase archive
	elseif ( dps_is_showcase_archive() && ( $new_template = dps_get_showcase_archive_template() ) ) :

	endif;

	// Showcase template file exists
	if ( ! empty( $new_template ) ) {

		// Override the WordPress template with a showcase one
		$template = $new_template;

		// @see: dps_template_include_theme_compat()
		showcase()->theme_compat->showcase_template = true;
	}

	return apply_filters( 'dps_template_include_theme_supports', $template );
}


/** Custom Functions **********************************************************/

/**
 * Attempt to load a custom showcase functions file, similar to each themes' functions.php file.
 *
 * @since Showcase (1.0)
 * @global string $pagenow
 */
function dps_load_theme_functions() {
	global $pagenow;

	// If showcase is being deactivated, do not load any more files
	if ( dps_is_deactivation() )
		return;

	if ( ! defined( 'WP_INSTALLING' ) || ( ! empty( $pagenow ) && ( 'wp-activate.php' !== $pagenow ) ) ) {
		dps_locate_template( 'showcase-functions.php', true );
	}
}


/** Individual Templates ******************************************************/

/**
 * Get the single showcase template
 *
 * @since Showcase (1.0)
 * @return string Path to template file
 */
function dps_get_single_showcase_template() {
	$templates = array(
		'single-' . dps_get_showcase_post_type() . '.php' // Single showcase
	);
	return dps_get_query_template( 'single_forum', $templates );
}

/**
 * Get the forum archive template
 *
 * @since Showcase (1.0)
 * @return string Path to template file
 */
function dps_get_forum_archive_template() {
	$templates = array(
		'archive-' . dps_get_showcase_post_type() . '.php' // Showcase archive
	);
	return dps_get_query_template( 'forum_archive', $templates );
}

/**
 * Get the templates to use as the endpoint for showcase template parts
 *
 * @since Showcase (1.0)
 *
 * @uses apply_filters()
 * @uses dps_set_theme_compat_templates()
 * @uses dps_get_query_template()
 * @return string Path to template file
 */
function dps_get_theme_compat_templates() {
	$templates = array(
		'plugin-showcase.php',
		'showcase.php',
		'generic.php',
		'page.php',
		'single.php',
		'index.php'
	);
	return dps_get_query_template( 'showcase', $templates );
}
