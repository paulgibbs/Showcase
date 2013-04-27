<?php

/**
 * Functions of Showcase' default theme
 *
 * @package Showcase
 * @subpackage BB_Theme_Compat
 * @since Showcase (1.0)
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/** Theme Setup ***************************************************************/

if ( !class_exists( 'BB_Default' ) ) :

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
 * See @link BB_Theme_Compat() for more.
 *
 * @since Showcase (1.0)
 *
 * @package Showcase
 * @subpackage BB_Theme_Compat
 */
class BB_Default extends BB_Theme_Compat {

	/** Functions *************************************************************/

	/**
	 * The main Showcase (Default) Loader
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses BB_Default::setup_globals()
	 * @uses BB_Default::setup_actions()
	 */
	public function __construct() {
		$this->setup_globals();
		$this->setup_actions();
	}

	/**
	 * Component global variables
	 *
	 * Note that this function is currently commented out in the constructor.
	 * It will only be used if you copy this file into your current theme and
	 * uncomment the line above.
	 *
	 * You'll want to customize the values in here, so they match whatever your
	 * needs are.
	 *
	 * @since Showcase (1.0)
	 * @access private
	 */
	private function setup_globals() {
		$bbp           = showcase();
		$this->id      = 'default';
		$this->name    = __( 'Showcase Default', 'dps' );
		$this->version = dps_get_version();
		$this->dir     = trailingslashit( $bbp->themes_dir . 'default' );
		$this->url     = trailingslashit( $bbp->themes_url . 'default' );
	}

	/**
	 * Setup the theme hooks
	 *
	 * @since Showcase (1.0)
	 * @access private
	 *
	 * @uses add_filter() To add various filters
	 * @uses add_action() To add various actions
	 */
	private function setup_actions() {

		/** Scripts ***********************************************************/

		add_action( 'dps_enqueue_scripts',   array( $this, 'enqueue_styles'        ) ); // Enqueue theme CSS
		add_action( 'dps_enqueue_scripts',   array( $this, 'enqueue_scripts'       ) ); // Enqueue theme JS


		/** Override **********************************************************/

		do_action_ref_array( 'dps_theme_compat_actions', array( &$this ) );
	}

	/**
	 * Load the theme CSS
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses wp_enqueue_style() To enqueue the styles
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
	 *
	 * @uses dps_is_single_topic() To check if it's the topic page
	 * @uses dps_is_single_user_edit() To check if it's the profile edit page
	 * @uses wp_enqueue_script() To enqueue the scripts
	 */
	public function enqueue_scripts() {
		$file = 'js/showcase.js';

		// Check child theme
		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $file ) ) {
			$location = trailingslashit( get_stylesheet_directory_uri() );
			$handle   = 'dps-child-javascript';

		// Check parent theme
		} elseif ( file_exists( trailingslashit( get_template_directory() ) . $file ) ) {
			$location = trailingslashit( get_template_directory_uri() );
			$handle   = 'dps-parent-javascript';

		// Showcase theme compatibility
		} else {
			$location = trailingslashit( dps_get_theme_compat_url() );
			$handle   = 'dps-default-javascript';
		}

		// Enqueue the stylesheet
		wp_enqueue_script( $handle, $location . $file, array(), $this->version, 'screen', true );
	}
}
new BB_Default();
endif;
