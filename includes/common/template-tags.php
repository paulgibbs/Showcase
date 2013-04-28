<?php

/**
 * Showcase Common Template Tags
 *
 * Common template tags are ones that are used by more than one component.
 *
 * @package Showcase
 * @subpackage TemplateTags
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/** Add-on Actions ************************************************************/

/**
 * Add our custom head action to wp_head
 *
 * @since Showcase (1.0)
 *
 * @uses do_action() Calls 'dps_head'
*/
function dps_head() {
	do_action( 'dps_head' );
}

/**
 * Add our custom head action to wp_head
 *
 * @since Showcase (1.0)
 *
 * @uses do_action() Calls 'dps_footer'
 */
function dps_footer() {
	do_action( 'dps_footer' );
}


/** is_ ***********************************************************************/

/**
 * Check if current page is a showcase
 *
 * @since Showcase (1.0)
 * @param int $post_id Possible post_id to check
 * @return bool
 */
function dps_is_showcase( $post_id = 0 ) {
	$retval = false;

	// Supplied ID is a showcase item
	if ( ! empty( $post_id ) && ( dps_get_showcase_post_type() == get_post_type( $post_id ) ) )
		$retval = true;

	return (bool) apply_filters( 'dps_is_showcase', $retval, $post_id );
}

/**
 * Check if we are viewing a showcase archive.
 *
 * @since Showcase (1.0)
 * @return bool
 */
function dps_is_showcase_archive() {
	$retval = false;

	// In showcase archive
	if ( is_post_type_archive( dps_get_showcase_post_type() ) || dps_is_query_name( 'dps_showcase_archive' ) )
		$retval = true;

	return (bool) apply_filters( 'dps_is_showcase_archive', $retval );
}

/**
 * Viewing a single showcase
 *
 * @since Showcase (1.0)
 * @return bool
 */
function dps_is_single_showcase() {
	$retval = false;

	// Single and a match
	if ( is_singular( dps_get_showcase_post_type() ) || dps_is_query_name( 'dps_single_showcase' ) )
		$retval = true;

	return (bool) apply_filters( 'dps_is_single_showcase', $retval );
}

/**
 * Check if the current post type is one of showcase's
 *
 * @since Showcase (1.0)
 * @param mixed $the_post Optional. Post object or post ID.
 * @return bool
 */
function dps_is_custom_post_type( $the_post = false ) {
	$retval = false;

	// Viewing one of the showcase post types
	if ( in_array( get_post_type( $the_post ), array(
		dps_get_showcase_post_type(),
	) ) )
		$retval = true;

	return (bool) apply_filters( 'dps_is_custom_post_type', $retval, $the_post );
}

/**
 * Use the above is_() functions to output a body class for each scenario
 *
 * @since Showcase (1.0)
 * @param array $wp_classes
 * @param array $custom_classes
 * @return array Body Classes
 */
function dps_get_the_body_class( $wp_classes, $custom_classes = false ) {

	$dps_classes = array();

	/** Archives **************************************************************/
	if ( dps_is_showcase_archive() ) {
		$dps_classes[] = dps_get_showcase_post_type() . '-archive';

	/** Components ************************************************************/
	} elseif ( dps_is_single_showcase() ) {
		$dps_classes[] = dps_get_showcase_post_type();
	}

	// Merge WP classes with showcase classes and remove any duplicates
	$classes = array_unique( array_merge( (array) $dps_classes, (array) $wp_classes ) );

	return apply_filters( 'dps_get_the_body_class', $classes, $dps_classes, $wp_classes, $custom_classes );
}

/**
 * Use the above is_() functions to return if in any showcase page
 *
 * @since Showcase (1.0)
 * @return bool In a showcase page
 */
function is_showcase() {
	$retval = false;

	if ( dps_is_showcase_archive() )
		$retval = true;
	elseif ( dps_is_single_showcase() )
		$retval = true;

	return (bool) apply_filters( 'is_showcase', $retval );
}


/** Query *********************************************************************/

/**
 * Check the passed parameter against the current _dps_query_name
 *
 * @since Showcase (1.0)
 *
 * @uses dps_get_query_name() Get the query var '_dps_query_name'
 * @return bool True if match, false if not
 */
function dps_is_query_name( $name = '' )  {
	return (bool) ( dps_get_query_name() == $name );
}

/**
 * Get the '_dps_query_name' setting
 *
 * @since Showcase (1.0)
 *
 * @uses get_query_var() To get the query var '_dps_query_name'
 * @return string To return the query var value
 */
function dps_get_query_name()  {
	return get_query_var( '_dps_query_name' );
}

/**
 * Set the '_dps_query_name' setting to $name
 *
 * @since Showcase (1.0)
 *
 * @param string $name What to set the query var to
 * @uses set_query_var() To set the query var '_dps_query_name'
 */
function dps_set_query_name( $name = '' )  {
	set_query_var( '_dps_query_name', $name );
}

/**
 * Used to clear the '_dps_query_name' setting
 *
 * @since Showcase (1.0)
 *
 * @uses dps_set_query_name() To set the query var '_dps_query_name' value to ''
 */
function dps_reset_query_name() {
	dps_set_query_name();
}


/** Errors & Messages *********************************************************/

/**
 * Display possible errors & messages inside a template file
 *
 * @since Showcase (1.0)
 */
function dps_template_notices() {

	// Bail if no notices or errors
	if ( ! dps_has_errors() )
		return;

	// Define local variable(s)
	$errors = $messages = array();

	// Loop through notices
	foreach ( showcase()->errors->get_error_codes() as $code ) {

		// Get notice severity
		$severity = showcase()->errors->get_error_data( $code );

		// Loop through notices and separate errors from messages
		foreach ( showcase()->errors->get_error_messages( $code ) as $error ) {
			if ( 'message' == $severity ) {
				$messages[] = $error;
			} else {
				$errors[]   = $error;
			}
		}
	}

	// Display errors first...
	if ( !empty( $errors ) ) : ?>

		<div class="dps-template-notice error">
			<p>
				<?php echo implode( "</p>\n<p>", $errors ); ?>
			</p>
		</div>

	<?php endif;

	// ...and messages last
	if ( !empty( $messages ) ) : ?>

		<div class="dps-template-notice">
			<p>
				<?php echo implode( "</p>\n<p>", $messages ); ?>
			</p>
		</div>

	<?php endif;
}


/** Title *********************************************************************/

/**
 * Custom page title for showcase pages
 *
 * @since Showcase (1.0)
 * @param string $title Optional. The title (not used).
 * @param string $sep Optional, default is '&raquo;'. How to separate the various items within the page title.
 * @param string $seplocation Optional. Direction to display title, 'right'.
 * @return string The title
 */
function dps_title( $title = '', $sep = '&raquo;', $seplocation = '' ) {

	// Store original title to compare
	$_title = $title;

	// Showcase Archive
	if ( dps_is_showcase_archive() ) {
		$title = dps_get_showcase_archive_title();

	// Showcase page
	} elseif ( dps_is_single_showcase() ) {
		$title = sprintf( __( 'Showcase: %s', 'dps' ), dps_get_showcase_title() );
	}

	// Filter the raw title
	$title = apply_filters( 'dps_raw_title', $title, $sep, $seplocation );

	// Compare new title with original title
	if ( $title == $_title )
		return $title;

	// Temporary separator, for accurate flipping, if necessary
	$t_sep  = '%WP_TITILE_SEP%';
	$prefix = '';

	if ( !empty( $title ) )
		$prefix = " $sep ";

	// sep on right, so reverse the order
	if ( 'right' == $seplocation ) {
		$title_array = array_reverse( explode( $t_sep, $title ) );
		$title       = implode( " $sep ", $title_array ) . $prefix;

	// sep on left, do not reverse
	} else {
		$title_array = explode( $t_sep, $title );
		$title       = $prefix . implode( " $sep ", $title_array );
	}

	// Filter and return
	return apply_filters( 'dps_title', $title, $sep, $seplocation );
}
