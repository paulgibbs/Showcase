<?php

/**
 * Showcase Template Functions
 *
 * This file contains functions necessary to mirror the WordPress core template
 * loading process. Many of those functions are not filterable, and even then
 * would not be robust enough to predict where showcase templates might exist.
 *
 * @package Showcase
 * @subpackage TemplateFunctions
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Adds showcase theme support to any active WordPress theme
 *
 * @since Showcase (1.0)
 *
 * @param string $slug
 * @param string $name Optional. Default null
 * @uses dps_locate_template()
 * @uses load_template()
 * @uses get_template_part()
 */
function dps_get_template_part( $slug, $name = null ) {

	// Execute code for this part
	do_action( 'get_template_part_' . $slug, $slug, $name );

	// Setup possible parts
	$templates = array();
	if ( isset( $name ) )
		$templates[] = $slug . '-' . $name . '.php';
	$templates[] = $slug . '.php';


	// Allow template parst to be filtered
	$templates = apply_filters( 'dps_get_template_part', $templates, $slug, $name );

	// Return the part that is found
	return dps_locate_template( $templates, true, false );
}

/**
 * Retrieve the name of the highest priority template file that exists.
 *
 * Searches in the child theme before parent theme so that themes which
 * inherit from a parent theme can just overload one file. If the template is
 * not found in either of those, it looks in the theme-compat folder last.
 *
 * @since Showcase (1.0)
 *
 * @param string|array $template_names Template file(s) to search for, in order.
 * @param bool $load If true the template file will be loaded if it is found.
 * @param bool $require_once Whether to require_once or require. Default true. Has no effect if $load is false.
 * @return string The template filename if one is located.
 */
function dps_locate_template( $template_names, $load = false, $require_once = true ) {

	// No file found yet
	$located            = false;
	$template_locations = dps_get_template_stack();

	// Try to find a template file
	foreach ( (array) $template_names as $template_name ) {

		// Continue if template is empty
		if ( empty( $template_name ) ) {
			continue;
		}

		// Trim off any slashes from the template name
		$template_name  = ltrim( $template_name, '/' );

		// Loop through template stack
		foreach ( (array) $template_locations as $template_location ) {

			// Continue if $template_location is empty
			if ( empty( $template_location ) ) {
				continue;
			}

			// Check child theme first
			if ( file_exists( trailingslashit( $template_location ) . $template_name ) ) {
				$located = trailingslashit( $template_location ) . $template_name;
				break 2;
			}
		}
	}


	/**
	 * This action exists only to follow the standard showcase coding convention,
	 * and should not be used to short-circuit any part of the template locator.
	 *
	 * If you want to override a specific template part, please either filter
	 * 'dps_get_template_part' or add a new location to the template stack.
	 */
	do_action( 'dps_locate_template', $located, $template_name, $template_names, $template_locations, $load, $require_once );

	// Maybe load the template if one was located
	if ( ( true == $load ) && ! empty( $located ) ) {
		load_template( $located, $require_once );
	}

	return $located;
}

/**
 * This is really cool. This function registers a new template stack location,
 * using WordPress's built in filters API.
 *
 * This allows for templates to live in places beyond just the parent/child
 * relationship, to allow for custom template locations. Used in conjunction
 * with dps_locate_template(), this allows for easy template overrides.
 *
 * @since Showcase (1.0)
 *
 * @param string $location Callback function that returns the 
 * @param int $priority
 */
function dps_register_template_stack( $location_callback = '', $priority = 10 ) {

	// Bail if no location, or function does not exist
	if ( empty( $location_callback ) || ! function_exists( $location_callback ) )
		return false;

	// Add location callback to template stack
	return add_filter( 'dps_template_stack', $location_callback, (int) $priority );
}

/**
 * Deregisters a previously registered template stack location.
 *
 * @since Showcase (1.0)
 *
 * @param string $location Callback function that returns the
 * @param int $priority
 * @see dps_register_template_stack()
 */
function dps_deregister_template_stack( $location_callback = '', $priority = 10 ) {

	// Bail if no location, or function does not exist
	if ( empty( $location_callback ) || ! function_exists( $location_callback ) )
		return false;

	// Remove location callback to template stack
	return remove_filter( 'dps_template_stack', $location_callback, (int) $priority );
}

/**
 * Call the functions added to the 'dps_template_stack' filter hook, and return
 * an array of the template locations.
 *
 * @see dps_register_template_stack()
 *
 * @since Showcase (1.0)
 *
 * @global array $wp_filter Stores all of the filters
 * @global array $merged_filters Merges the filter hooks using this function.
 * @global array $wp_current_filter stores the list of current filters with the current one last
 *
 * @return array The filtered value after all hooked functions are applied to it.
 */
function dps_get_template_stack() {
	global $wp_filter, $merged_filters, $wp_current_filter;

	// Setup some default variables
	$tag  = 'dps_template_stack';
	$args = $stack = array();

	// Add 'dps_template_stack' to the current filter array
	$wp_current_filter[] = $tag;

	// Sort
	if ( ! isset( $merged_filters[ $tag ] ) ) {
		ksort( $wp_filter[$tag] );
		$merged_filters[ $tag ] = true;
	}

	// Ensure we're always at the beginning of the filter array
	reset( $wp_filter[ $tag ] );

	// Loop through 'dps_template_stack' filters, and call callback functions
	do {
		foreach( (array) current( $wp_filter[$tag] ) as $the_ ) {
			if ( ! is_null( $the_['function'] ) ) {
				$args[1] = $stack;
				$stack[] = call_user_func_array( $the_['function'], array_slice( $args, 1, (int) $the_['accepted_args'] ) );
			}
		}
	} while ( next( $wp_filter[$tag] ) !== false );

	// Remove 'dps_template_stack' from the current filter array
	array_pop( $wp_current_filter );

	// Remove empties and duplicates
	$stack = array_unique( array_filter( $stack ) );

	return (array) apply_filters( 'dps_get_template_stack', $stack ) ;
}

/**
 * Retrieve path to a template
 *
 * Used to quickly retrieve the path of a template without including the file
 * extension. It will also check the parent theme and theme-compat theme with
 * the use of {@link dps_locate_template()}. Allows for more generic template
 * locations without the use of the other get_*_template() functions.
 *
 * @since Showcase (1.0)
 *
 * @param string $type Filename without extension.
 * @param array $templates An optional list of template candidates
 * @uses dps_set_theme_compat_templates()
 * @uses dps_locate_template()
 * @uses dps_set_theme_compat_template()
 * @return string Full path to file.
 */
function dps_get_query_template( $type, $templates = array() ) {
	$type = preg_replace( '|[^a-z0-9-]+|', '', $type );

	if ( empty( $templates ) )
		$templates = array( "{$type}.php" );

	// Filter possible templates, try to match one, and set any showcase theme
	// compat properties so they can be cross-checked later.
	$templates = apply_filters( "dps_get_{$type}_template", $templates );
	$templates = dps_set_theme_compat_templates( $templates );
	$template  = dps_locate_template( $templates );
	$template  = dps_set_theme_compat_template( $template );

	return apply_filters( "dps_{$type}_template", $template );
}

/**
 * Get the possible subdirectories to check for templates in
 *
 * @since Showcase (1.0)
 * @param array $templates Templates we are looking for
 * @return array Possible subfolders to look in
 */
function dps_get_template_locations( $templates = array() ) {
	$locations = array(
		'showcase',
		''
	);
	return apply_filters( 'dps_get_template_locations', $locations, $templates );
}

/**
 * Add template locations to template files being searched for
 *
 * @since Showcase (1.0)
 *
 * @param array $templates
 * @return array() 
 */
function dps_add_template_stack_locations( $stacks = array() ) {
	$retval = array();

	// Get alternate locations
	$locations = dps_get_template_locations();

	// Loop through locations and stacks and combine
	foreach ( (array) $stacks as $stack )
		foreach ( (array) $locations as $custom_location )
			$retval[] = untrailingslashit( trailingslashit( $stack ) . $custom_location );

	return apply_filters( 'dps_add_template_stack_locations', array_unique( $retval ), $stacks );
}

/**
 * Add checks for showcase conditions to parse_query action
 *
 * @since Showcase (1.0)
 * @param WP_Query $posts_query
 */
function dps_parse_query( $posts_query ) {
}
