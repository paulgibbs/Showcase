<?php

/**
 * Main showcase Admin Class
 *
 * @package Showcase
 * @subpackage Administration
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'DPS_Admin' ) ) :
/**
 * Loads showcase plugin admin area
 *
 * @package Showcase
 * @subpackage Administration
 * @since Showcase (1.0)
 */
class DPS_Admin {

	/** Directory *************************************************************/

	/**
	 * @var string Path to the showcase admin directory
	 */
	public $admin_dir = '';

	/** URLs ******************************************************************/

	/**
	 * @var string URL to the showcase admin directory
	 */
	public $admin_url = '';

	/**
	 * @var string URL to the showcase images directory
	 */
	public $images_url = '';

	/**
	 * @var string URL to the showcase admin styles directory
	 */
	public $styles_url = '';

	/** Capability ************************************************************/

	/**
	 * @var bool Minimum capability to access Tools and Settings
	 */
	public $minimum_capability = 'keep_gate';

	/** Separator *************************************************************/

	/**
	 * @var bool Whether or not to add an extra top level menu separator
	 */
	public $show_separator = false;

	/** Functions *************************************************************/

	/**
	 * The main showcase admin loader
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses DPS_Admin::setup_globals() Setup the globals needed
	 * @uses DPS_Admin::includes() Include the required files
	 * @uses DPS_Admin::setup_actions() Setup the hooks and actions
	 */
	public function __construct() {
		$this->setup_globals();
		$this->includes();
		$this->setup_actions();
	}

	/**
	 * Admin globals
	 *
	 * @since Showcase (1.0)
	 * @access private
	 */
	private function setup_globals() {
		$this->admin_dir  = trailingslashit( showcase()->includes_dir . 'admin'  ); // Admin path
		$this->admin_url  = trailingslashit( showcase()->includes_url . 'admin'  ); // Admin url
		$this->images_url = trailingslashit( $this->admin_url   . 'images' ); // Admin images URL
		$this->styles_url = trailingslashit( $this->admin_url   . 'styles' ); // Admin styles URL
	}

	/**
	 * Include required files
	 *
	 * @since Showcase (1.0)
	 * @access private
	 */
	private function includes() {
		require( $this->admin_dir . 'settings.php'  );
		require( $this->admin_dir . 'functions.php' );
		require( $this->admin_dir . 'forums.php'    );
		require( $this->admin_dir . 'users.php'     );
	}

	/**
	 * Setup the admin hooks, actions and filters
	 *
	 * @since Showcase (1.0)
	 * @access private
	 *
	 * @uses add_action() To add various actions
	 * @uses add_filter() To add various filters
	 */
	private function setup_actions() {

		// Bail to prevent interfering with the deactivation process
		if ( dps_is_deactivation() )
			return;

		/** Dependencies ******************************************************/

		// Allow plugins to modify these actions
		do_action_ref_array( 'dps_admin_loaded', array( &$this ) );
	}
}
endif; // class_exists check

/**
 * Setup showcase Admin
 *
 * @since Showcase (1.0)
 *
 * @uses DPS_Admin
 */
function dps_admin() {
	showcase()->admin = new DPS_Admin();
}
