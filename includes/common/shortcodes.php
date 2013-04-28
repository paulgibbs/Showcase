<?php
/**
 * Showcase Shortcodes
 *
 * @package Showcase
 * @subpackage Shortcodes
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'DPS_Shortcodes' ) ) :
/**
 * Showcase Shortcode Class
 *
 * @since Showcase (1.0)
 */
class DPS_Shortcodes {

	/** Vars ******************************************************************/

	/**
	 * @var array Shortcode => function
	 */
	public $codes = array();

	/** Functions *************************************************************/

	/**
	 * Add the register_shortcodes action to dps_init
	 *
	 * @since Showcase (1.0)
	 */
	public function __construct() {
		$this->setup_globals();
		$this->add_shortcodes();
	}

	/**
	 * Shortcode globals
	 *
	 * @since Showcase (1.0)
	 */
	private function setup_globals() {

		// Setup the shortcodes
		$this->codes = apply_filters( 'dps_shortcodes', array(

			/** Showcase ********************************************************/
			'dps-showcase-index'  => array( $this, 'display_showcase_index' ), // Showcase index
			'dps-single-showcase' => array( $this, 'display_showcase'       ), // Specific showcase - pass an 'id' attribute
		) );
	}

	/**
	 * Register the showcase shortcodes
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses add_shortcode()
	 * @uses do_action()
	 */
	private function add_shortcodes() {
		foreach( (array) $this->codes as $code => $function ) {
			add_shortcode( $code, $function );
		}
	}

	/**
	 * Unset some globals in the showcase() object that hold query related info
	 *
	 * @since Showcase (1.0)
	 */
	private function unset_globals() {

		// Unset global queries
		showcase()->showcase_query = new stdClass;

		// Unset global IDs
		showcase()->current_showcase_id = 0;

		// Reset the post data
		wp_reset_postdata();
	}


	/** Output Buffers ********************************************************/

	/**
	 * Start an output buffer.
	 *
	 * This is used to put the contents of the shortcode into a variable rather
	 * than outputting the HTML at run-time. This allows shortcodes to appear
	 * in the correct location in the_content() instead of when it's created.
	 *
	 * @since Showcase (1.0)
	 * @param string $query_name
	 */
	private function start( $query_name = '' ) {

		// Set query name
		dps_set_query_name( $query_name );

		// Remove 'dps_replace_the_content' filter to prevent infinite loops
		remove_filter( 'the_content', 'dps_replace_the_content' );

		// Start output buffer
		ob_start();
	}

	/**
	 * Return the contents of the output buffer and flush its contents.
	 *
	 * @since Showcase (1.0)
	 * @return string Contents of output buffer.
	 */
	private function end() {

		// Put output into usable variable
		$output = ob_get_contents();

		// Unset globals
		$this->unset_globals();

		// Flush the output buffer
		ob_end_clean();

		// Reset the query name
		dps_reset_query_name();

		// Add 'dps_replace_the_content' filter back (@see $this::start())
		add_filter( 'the_content', 'dps_replace_the_content' );

		return $output;
	}


	/** Showcase shortcodes ******************************************************/

	/**
	 * Display an index of all visible root level forums in an output buffer
	 * and return to ensure that post/page contents are displayed first.
	 *
	 * @since Showcase (1.0)
	 * @param array $attr
	 * @param string $content
	 * @return string
	 */
	public function display_showcase_index() {

		// Unset globals
		$this->unset_globals();

		// Start output buffer
		$this->start( 'dps_showcase_archive' );

		dps_get_template_part( 'content-archive-showcase' );

		// Return contents of output buffer
		return $this->end();
	}

	/**
	 * Display the contents of a specific forum ID in an output buffer
	 * and return to ensure that post/page contents are displayed first.
	 *
	 * @since Showcase (1.0)
	 * @param array $attr
	 * @param string $content
	 * @return string
	 */
	public function display_showcase( $attr, $content = '' ) {

		// Sanity check required info
		if ( ! empty( $content ) || ( empty( $attr['id'] ) || !is_numeric( $attr['id'] ) ) )
			return $content;

		// Set passed attribute to $showcase_id for clarity
		$showcase_id = showcase()->current_showcase_id = $attr['id'];

		// Bail if ID passed is not a showcase item
		if ( ! dps_is_showcase( $showcase_id ) )
			return $content;

		// Start output buffer
		$this->start( 'dps_single_showcase' );

		dps_get_template_part( 'content-single-forum' );

		// Return contents of output buffer
		return $this->end();
	}
}
endif;
