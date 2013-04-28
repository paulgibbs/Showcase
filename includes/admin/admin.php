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


	/** Functions *************************************************************/

	/**
	 * The main showcase admin loader
	 *
	 * @since Showcase (1.0)
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
	 */
	private function setup_globals() {
		$this->admin_dir  = trailingslashit( showcase()->includes_dir . 'admin'  ); // Admin path
		$this->admin_url  = trailingslashit( showcase()->includes_url . 'admin'  ); // Admin url
	}

	/**
	 * Include required files
	 *
	 * @since Showcase (1.0)
	 */
	private function includes() {
	}

	/**
	 * Setup the admin hooks, actions and filters
	 *
	 * @since Showcase (1.0)
	 */
	private function setup_actions() {

		// Bail to prevent interfering with the deactivation process
		if ( dps_is_deactivation() )
			return;

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
