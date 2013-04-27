<?php

/**
 * Showcase Formatting
 *
 * @package Showcase
 * @subpackage Formatting
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/** Kses **********************************************************************/

/**
 * Custom allowed tags for forum topics and replies
 *
 * Allows all users to post links, quotes, code, formatting, lists, and images
 *
 * @since Showcase (1.0)
 *
 * @return array Associative array of allowed tags and attributes
 */
function dps_kses_allowed_tags() {
	return apply_filters( 'dps_kses_allowed_tags', array(

		// Links
		'a' => array(
			'href'     => array(),
			'title'    => array(),
			'rel'      => array()
		),

		// Quotes
		'blockquote'   => array(
			'cite'     => array()
		),

		// Code
		'code'         => array(),
		'pre'          => array(),

		// Formatting
		'em'           => array(),
		'strong'       => array(),
		'del'          => array(
			'datetime' => true,
		),

		// Lists
		'ul'           => array(),
		'ol'           => array(
			'start'    => true,
		),
		'li'           => array(),

		// Images
		'img'          => array(
			'src'      => true,
			'border'   => true,
			'alt'      => true,
			'height'   => true,
			'width'    => true,
		)
	) );
}

/**
 * Custom kses filter for forum topics and replies, for filtering incoming data
 *
 * @since Showcase (1.0)
 *
 * @param string $data Content to filter, expected to be escaped with slashes
 * @return string Filtered content
 */
function dps_filter_kses( $data = '' ) {
	return addslashes( wp_kses( stripslashes( $data ), dps_kses_allowed_tags() ) );
}

/**
 * Custom kses filter for forum topics and replies, for raw data
 *
 * @since Showcase (1.0)
 *
 * @param string $data Content to filter, expected to not be escaped
 * @return string Filtered content
 */
function dps_kses_data( $data = '' ) {
	return wp_kses( $data , dps_kses_allowed_tags() );
}
