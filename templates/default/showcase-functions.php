<?php

/**
 * Functions of Showcase' default theme
 *
 * @package Showcase
 * @subpackage DPS_Theme_Compat
 * @since Showcase (1.0)
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/** Theme Setup ***************************************************************/

if ( ! class_exists( 'DPS_Default' ) ) :

/**
 * Loads Showcase default theme functionality
 *
 * This is not a real theme by WordPress standards, and is instead used as the
 * fallback for any WordPress theme that does not have showcase templates in it.
 *
 * To make your custom theme Showcase compatible and customize the templates, you
 * can copy these files into your theme without needing to merge anything
 * together; Showcase should safely handle the rest.
 *
 * See @link DPS_Theme_Compat() for more.
 *
 * @since Showcase (1.0)
 * @package Showcase
 * @subpackage DPS_Theme_Compat
 */
class DPS_Default extends DPS_Theme_Compat {

	/**
	 * The main Showcase (Default) Loader
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses DPS_Default::setup_globals()
	 * @uses DPS_Default::setup_actions()
	 */
	public function __construct() {
		$this->setup_globals();
		$this->setup_actions();
	}

	/**
	 * Component global variables
	 *
	 * Note that this function is currently commented out in the constructor.
	 * It will only be used if you copy this file into your current theme and uncomment the line above.
	 *
	 * You'll want to customize the values in here, so they match whatever your needs are.
	 *
	 * @since Showcase (1.0)
	 */
	private function setup_globals() {
		$this->id      = 'default';
		$this->name    = __( 'Showcase Default', 'dps' );
		$this->version = dps_get_version();
		$this->dir     = trailingslashit( showcase()->themes_dir . 'default' );
		$this->url     = trailingslashit( showcase()->themes_url . 'default' );
	}

	/**
	 * Setup the theme hooks
	 *
	 * @since Showcase (1.0)
	 */
	private function setup_actions() {

		/** Scripts ***********************************************************/
		add_action( 'dps_enqueue_scripts', array( $this, 'enqueue_styles'  ) ); // Enqueue theme CSS
		add_action( 'dps_enqueue_scripts', array( $this, 'enqueue_scripts' ) ); // Enqueue theme JS

		/** Override **********************************************************/
		do_action_ref_array( 'dps_theme_compat_actions', array( &$this ) );
	}

	/**
	 * Load the theme CSS
	 *
	 * @since Showcase (1.0)
	 */
	public function enqueue_styles() {

		// LTR or RTL
		$file = is_rtl() ? 'css/showcase-rtl.css' : 'css/showcase.css';

		// Check child theme
		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $file ) ) {
			$location = trailingslashit( get_stylesheet_directory_uri() );
			$handle   = 'dps-child-showcase';

		// Check parent theme
		} elseif ( file_exists( trailingslashit( get_template_directory() ) . $file ) ) {
			$location = trailingslashit( get_template_directory_uri() );
			$handle   = 'dps-parent-showcase';

		// Showcase theme compatibility
		} else {
			$location = trailingslashit( $this->url );
			$handle   = 'dps-default-showcase';
		}

		// Enqueue the Showcase styling
		wp_enqueue_style( $handle, $location . $file, array(), $this->version, 'screen' );
	}

	/**
	 * Enqueue the required Javascript files
	 *
	 * @since Showcase (1.0)
	 */
	public function enqueue_scripts() {
	}
}
new DPS_Default();
endif;
