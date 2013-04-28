<?php
/**
 * Showcase Template Tags
 *
 * @package Showcase
 * @subpackage TemplateTags
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/** Post Type *****************************************************************/

/**
 * Output the unique id of the custom post type for showcase
 *
 * @since Showcase (1.0)
 */
function dps_showcase_post_type() {
	echo dps_get_showcase_post_type();
}
	/**
	 * Return the unique id of the custom post type for showcase
	 *
	 * @since Showcase (1.0)
	 * @return string 
	 */
	function dps_get_showcase_post_type() {
		return apply_filters( 'dps_get_showcase_post_type', showcase()->showcase_post_type );
	}


/** Showcase loop ****************************************************************/

/**
 * The main showcase loop.
 *
 * @since Showcase (1.0)
 * @param mixed $args All the arguments supported by {@link WP_Query}
 * @return object Multidimensional array of showcase information
 */
function dps_has_showcases( $args = '' ) {
	$filtered_args = dps_parse_args( $args, array(
		'post_type'      => dps_get_showcase_post_type(),
		'posts_per_page' => get_option( '_dps_showcases_per_page', 8 ),
	), 'has_showcases' );

	// Run the query
	showcase()->showcase_query = new WP_Query( $filtered_args );

	return apply_filters( 'dps_has_showcases', showcase()->showcase_query->have_posts(), showcase()->showcase_query );
}

/**
 * Whether there are more showcase items available in the loop
 *
 * @since Showcase (1.0)
 * @return object
 */
function dps_showcases() {
	$have_posts = showcase()->showcase_query->have_posts();

	// Reset the post data when finished
	if ( empty( $have_posts ) )
		wp_reset_postdata();

	return $have_posts;
}

/**
 * Loads up the current showcase in the loop
 *
 * @since Showcase (1.0)
 * @return object
 */
function dps_the_showcase() {
	return showcase()->showcase_query->the_post();
}


/** Showcase *********************************************************************/

/**
 * Output showcase id
 *
 * @since Showcase (1.0)
 * @param $showcase_id Optional. Used to check emptiness
 * @uses dps_get_showcase_id() To get the showcase id
 */
function dps_showcase_id( $showcase_id = 0 ) {
	echo dps_get_showcase_id( $showcase_id );
}
	/**
	 * Return the showcase id
	 *
	 * @since Showcase (1.0)
	 * @param $showcase_id Optional. Used to check emptiness
	 * @return int
	 */
	function dps_get_showcase_id( $showcase_id = 0 ) {
		global $wp_query;

		// Easy empty checking
		if ( ! empty( $showcase_id ) && is_numeric( $showcase_id ) ) {
			$dps_showcase_id = $showcase_id;

		// Currently inside a showcase loop
		} elseif ( ! empty( showcase()->showcase_query->in_the_loop ) && isset( showcase()->showcase_query->post->ID ) ) {
			$dps_showcase_id = showcase()->showcase_query->post->ID;

		// Currently viewing a showcase
		} elseif ( dps_is_single_showcase() && ! empty( showcase()->current_showcase_id ) ) {
			$dps_showcase_id = showcase()->current_showcase_id;

		// Currently viewing a showcase
		} elseif ( dps_is_single_showcase() && isset( $wp_query->post->ID ) ) {
			$dps_showcase_id = $wp_query->post->ID;

		} else {
			$dps_showcase_id = 0;
		}

		return (int) apply_filters( 'dps_get_showcase_id', (int) $dps_showcase_id, $showcase_id );
	}

/**
 * Gets a showcase item
 *
 * @since Showcase (1.0)
 * @param int|object $showcase Showcase id or object
 * @param string $output Optional. OBJECT, ARRAY_A, or ARRAY_N. Default = OBJECT
 * @param string $filter Optional Sanitation filter. See {@link sanitize_post()}
 * @return mixed Null if error or showcase (in specified form) if success
 */
function dps_get_showcase( $showcase, $output = OBJECT, $filter = 'raw' ) {

	// Use showcase ID
	if ( empty( $showcase ) || is_numeric( $showcase ) )
		$showcase = dps_get_showcase_id( $showcase );

	// Attempt to load the showcase
	$showcase = get_post( $showcase, OBJECT, $filter );
	if ( empty( $showcase ) )
		return $showcase;

	// Bail if post_type is not a showcase
	if ( $showcase->post_type !== dps_get_showcase_post_type() )
		return null;

	// Tweak the data type to return
	if ( $output == OBJECT ) {
		return $showcase;

	} elseif ( $output == ARRAY_A ) {
		$_showcase = get_object_vars( $showcase );
		return $_showcase;

	} elseif ( $output == ARRAY_N ) {
		$_showcase = array_values( get_object_vars( $showcase ) );
		return $_showcase;

	}

	return apply_filters( 'dps_get_showcase', $showcase, $output, $filter );
}

/**
 * Output the link to the showcase
 *
 * @since Showcase (1.0)
 * @param int $showcase_id Optional. Showcase id
 */
function dps_showcase_permalink( $showcase_id = 0 ) {
	echo dps_get_showcase_permalink( $showcase_id );
}
	/**
	 * Return the link to the showcase
	 *
	 * @since Showcase (1.0)
	 * @param int $showcase_id Optional. Showcase id
	 * @param $string $redirect_to Optional. Pass a redirect value for use with shortcodes and other fun things.
	 * @return string Permanent link to showcase
	 */
	function dps_get_showcase_permalink( $showcase_id = 0, $redirect_to = '' ) {
		$showcase_id = dps_get_showcase_id( $showcase_id );

		// Use the redirect address
		if ( ! empty( $redirect_to ) )
			$showcase_permalink = esc_url_raw( $redirect_to );

		// Use the topic permalink
		else
			$showcase_permalink = get_permalink( $showcase_id );

		return apply_filters( 'dps_get_showcase_permalink', $showcase_permalink, $showcase_id );
	}

/**
 * Output the title of the showcase item
 *
 * @since Showcase (1.0)
 * @param int $showcase_id Optional. Showcase id
 */
function dps_showcase_title( $showcase_id = 0 ) {
	echo dps_get_showcase_title( $showcase_id );
}
	/**
	 * Return the title of the showcase item
	 *
	 * @since Showcase (1.0)
	 * @param int $showcase_id Optional. Showcase id
	 * @return string
	 */
	function dps_get_showcase_title( $showcase_id = 0 ) {
		$showcase_id = dps_get_showcase_id( $showcase_id );
		$title       = get_the_title( $showcase_id );

		return apply_filters( 'dps_get_showcase_title', $title, $showcase_id );
	}

/**
 * Output the showcase archive title
 *
 * @since Showcase (1.0)
 * @param string $title Default text to use as title
 */
function dps_showcase_archive_title( $title = '' ) {
	echo dps_get_showcase_archive_title( $title );
}
	/**
	 * Return the showcase archive title
	 *
	 * @since Showcase (1.0)
	 * @return string The showcase archive title
	 */
	function dps_get_showcase_archive_title( $title = '' ) {

		// If no title was passed
		if ( empty( $title ) ) {

			// Set root text to page title
			$page = dps_get_page_by_path( dps_get_root_slug() );
			if ( ! empty( $page ) ) {
				$title = get_the_title( $page->ID );

			// Default to showcase post type name label
			} else {
				$fto    = get_post_type_object( dps_get_showcase_post_type() );
				$title  = $fto->labels->name;
			}
		}

		return apply_filters( 'dps_get_showcase_archive_title', $title );
	}

/**
 * Output the content of the showcase item
 *
 * @param int $showcase_id Optional. Showcase id
 */
function dps_showcase_content( $showcase_id = 0 ) {
	echo dps_get_showcase_content( $showcase_id );
}
	/**
	 * Return the content of the showcase item
	 *
	 * @since Showcase (1.0)
	 * @param int $showcase_id Optional. Showcase id
	 * @return string
	 */
	function dps_get_showcase_content( $showcase_id = 0 ) {
		$showcase_id = dps_get_showcase_id( $showcase_id );

		// Check if password is required
		if ( post_password_required( $showcase_id ) )
			return get_the_password_form();

		$content = get_post_field( 'post_content', $showcase_id );
		return apply_filters( 'dps_get_showcase_content', $content, $showcase_id );
	}

/**
 * Output the row class of a showcase item
 *
 * @since Showcase (1.0)
 * @param int $showcase_id Optional. Showcase ID.
 * @param array $classes Extra classes you can pass when calling this function
 */
function dps_showcase_class( $showcase_id = 0, $classes = array() ) {
	echo dps_get_showcase_class( $showcase_id, $classes );
}
	/**
	 * Return the row class of a showcase
	 *
	 * @since Showcase (1.0)
	 * @param int $showcase_id Optional. Showcase ID
	 * @param array $classes Extra classes you can pass when calling this function
	 * @return string
	 */
	function dps_get_showcase_class( $showcase_id = 0, $classes = array() ) {
		$showcase_id = dps_get_showcase_id( $showcase_id );
		$count       = isset( $dps->showcase_query->current_post ) ? $dps->showcase_query->current_post : 1;

		$classes   = (array) $classes;
		$classes[] = 'loop-item-' . $count;
		$classes[] = ( (int) $count % 2 ) ? 'even' : 'odd';

		// "Featured" category item used to style the output
		if ( has_category( 'featured' ) )
			$classes[] = 'featured';

		// Filter the results
		$classes = array_filter( $classes );
		$classes = get_post_class( $classes, $showcase_id );
		$classes = apply_filters( 'dps_get_showcase_class', $classes, $showcase_id );
		$retval  = 'class="' . join( ' ', $classes ) . '"';

		return $retval;
	}

/**
 * Output the author ID of the showcase
 *
 * @since Showcase (1.0)
 * @param int $showcase_id Optional. Showcase id
 */
function dps_showcase_author_id( $showcase_id = 0 ) {
	echo dps_get_showcase_author_id( $showcase_id );
}
	/**
	 * Return the author ID of the showcase
	 *
	 * @since Showcase (1.0)
	 * @param int $showcase_id Optional. Showcase id
	 * @return string
	 */
	function dps_get_showcase_author_id( $showcase_id = 0 ) {
		$showcase_id = dps_get_showcase_id( $showcase_id );
		$author_id   = get_post_field( 'post_author', $showcase_id );

		return (int) apply_filters( 'dps_get_showcase_author_id', (int) $author_id, $showcase_id );
	}

/**
 * Output the mshot URL of the specified site
 *
 * @param string $site_url URl to fetch mshot for
 * @return string
 */
function dps_showcase_mshot( $site_url ) {
	echo esc_url( dps_get_showcase_mshot( $site_url ) );
}
	/**
	 * Return the mshot URL of the specified site
	 *
	 * @param string $site_url URl to fetch mshot for
	 * @return string
	 */
	function dps_get_showcase_mshot( $site_url ) {
		if ( empty( $site_url ) )
			$site_url = 'http://buddypress.org';

		$size = has_category( 'featured' ) ? 960 : 288;
		$url  = sprintf( 'http://s.wordpress.com/mshots/v1/%s?w=%d', urlencode( esc_url_raw( $site_url ) ), $size );

		return apply_filters( 'dps_get_showcase_mshot', $url, $site_url, $size );
	}
