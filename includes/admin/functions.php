<?php
/**
 * Showcase Admin Functions
 *
 * @package Showcase
 * @subpackage Administration
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Uninstall all showcase options and capabilities from a specific site.
 *
 * @since Showcase (1.0)
 * @param type $site_id
 */
function dps_do_uninstall( $site_id = 0 ) {
	if ( empty( $site_id ) )
		$site_id = get_current_blog_id();

	switch_to_blog( $site_id );
	dps_delete_options();
	flush_rewrite_rules();
	restore_current_blog();
}
