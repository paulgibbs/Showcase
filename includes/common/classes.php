<?php

/**
 * Showcase Classes
 *
 * @package Showcase
 * @subpackage Classes
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'BB_Component' ) ) :
/**
 * Showcase Component Class
 *
 * The barebones component class is responsible for simplifying the creation
 * of components that share similar behaviors and routines. It is used
 * internally by barebones to create forums, topics and replies, but can be
 * extended to create other really neat things.
 *
 * @package Showcase
 * @subpackage Classes
 *
 * @since Showcase (1.0)
 */
class BB_Component {

	/**
	 * @var string Unique name (for internal identification)
	 * @internal
	 */
	var $name;

	/**
	 * @var Unique ID (normally for custom post type)
	 */
	var $id;

	/**
	 * @var string Unique slug (used in query string and permalinks)
	 */
	var $slug;

	/**
	 * @var WP_Query The loop for this component
	 */
	var $query;

	/**
	 * @var string The current ID of the queried object
	 */
	var $current_id;


	/** Methods ***************************************************************/

	/**
	 * Showcase Component loader
	 *
	 * @since Showcase (1.0)
	 *
	 * @param mixed $args Required. Supports these args:
	 *  - name: Unique name (for internal identification)
	 *  - id: Unique ID (normally for custom post type)
	 *  - slug: Unique slug (used in query string and permalinks)
	 *  - query: The loop for this component (WP_Query)
	 *  - current_id: The current ID of the queried object
	 * @uses BB_Component::setup_globals() Setup the globals needed
	 * @uses BB_Component::includes() Include the required files
	 * @uses BB_Component::setup_actions() Setup the hooks and actions
	 */
	public function __construct( $args = '' ) {
		if ( empty( $args ) )
			return;

		$this->setup_globals( $args );
		$this->includes();
		$this->setup_actions();
	}

	/**
	 * Component global variables
	 *
	 * @since Showcase (1.0)
	 * @access private
	 *
	 * @uses apply_filters() Calls 'dps_{@link BB_Component::name}_id'
	 * @uses apply_filters() Calls 'dps_{@link BB_Component::name}_slug'
	 */
	private function setup_globals( $args = '' ) {
		$this->name = $args['name'];
		$this->id   = apply_filters( 'dps_' . $this->name . '_id',   $args['id']   );
		$this->slug = apply_filters( 'dps_' . $this->name . '_slug', $args['slug'] );
	}

	/**
	 * Include required files
	 *
	 * @since Showcase (1.0)
	 * @access private
	 *
	 * @uses do_action() Calls 'dps_{@link BB_Component::name}includes'
	 */
	private function includes() {
		do_action( 'dps_' . $this->name . 'includes' );
	}

	/**
	 * Setup the actions
	 *
	 * @since Showcase (1.0)
	 * @access private
	 *
	 * @uses add_action() To add various actions
	 * @uses do_action() Calls
	 *                    'dps_{@link BB_Component::name}setup_actions'
	 */
	private function setup_actions() {
		add_action( 'dps_register_post_types',    array( $this, 'register_post_types'    ), 10, 2 ); // Register post types
		add_action( 'dps_register_taxonomies',    array( $this, 'register_taxonomies'    ), 10, 2 ); // Register taxonomies
		add_action( 'dps_add_rewrite_tags',       array( $this, 'add_rewrite_tags'       ), 10, 2 ); // Add the rewrite tags
		add_action( 'dps_generate_rewrite_rules', array( $this, 'generate_rewrite_rules' ), 10, 2 ); // Generate rewrite rules

		// Additional actions can be attached here
		do_action( 'dps_' . $this->name . 'setup_actions' );
	}

	/**
	 * Setup the component post types
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses do_action() Calls 'dps_{@link BB_Component::name}_register_post_types'
	 */
	public function register_post_types() {
		do_action( 'dps_' . $this->name . '_register_post_types' );
	}

	/**
	 * Register component specific taxonomies
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses do_action() Calls 'dps_{@link BB_Component::name}_register_taxonomies'
	 */
	public function register_taxonomies() {
		do_action( 'dps_' . $this->name . '_register_taxonomies' );
	}

	/**
	 * Add any additional rewrite tags
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses do_action() Calls 'dps_{@link BB_Component::name}_add_rewrite_tags'
	 */
	public function add_rewrite_tags() {
		do_action( 'dps_' . $this->name . '_add_rewrite_tags' );
	}

	/**
	 * Generate any additional rewrite rules
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses do_action() Calls 'dps_{@link BB_Component::name}_generate_rewrite_rules'
	 */
	public function generate_rewrite_rules( $wp_rewrite ) {
		do_action_ref_array( 'dps_' . $this->name . '_generate_rewrite_rules', $wp_rewrite );
	}
}
endif; // BB_Component

if ( class_exists( 'Walker' ) ) :
/**
 * Create HTML dropdown list of barebones forums/topics.
 *
 * @package Showcase
 * @subpackage Classes
 *
 * @since Showcase (1.0)
 * @uses Walker
 */
class BB_Walker_Dropdown extends Walker {

	/**
	 * @see Walker::$tree_type
	 *
	 * @since Showcase (1.0)
	 *
	 * @var string
	 */
	var $tree_type;

	/**
	 * @see Walker::$db_fields
	 *
	 * @since Showcase (1.0)
	 *
	 * @var array
	 */
	var $db_fields = array( 'parent' => 'post_parent', 'id' => 'ID' );

	/** Methods ***************************************************************/

	/**
	 * Set the tree_type
	 *
	 * @since Showcase (1.0)
	 */
	public function __construct() {
		$this->tree_type = dps_get_forum_post_type();
	}

	/**
	 * @see Walker::start_el()
	 *
	 * @since Showcase (1.0)
	 *
	 * @param string $output Passed by reference. Used to append additional
	 *                        content.
	 * @param object $_post Post data object.
	 * @param int $depth Depth of post in reference to parent posts. Used
	 *                    for padding.
	 * @param array $args Uses 'selected' argument for selected post to set
	 *                     selected HTML attribute for option element.
	 * @uses dps_is_forum_category() To check if the forum is a category
	 * @uses current_user_can() To check if the current user can post in
	 *                           closed forums
	 * @uses dps_is_forum_closed() To check if the forum is closed
	 * @uses apply_filters() Calls 'dps_walker_dropdown_post_title' with the
	 *                        title, output, post, depth and args
	 */
	public function start_el( &$output, $_post, $depth, $args ) {
		$pad     = str_repeat( '&nbsp;', $depth * 3 );
		$output .= '<option class="level-' . $depth . '"';

		// Disable the <option> if:
		// - we're told to do so
		// - the post type is a forum
		// - the forum is a category
		// - forum is closed
		if (	( true == $args['disable_categories'] )
				&& ( dps_get_forum_post_type() == $_post->post_type )
				&& ( dps_is_forum_category( $_post->ID )
					|| ( !current_user_can( 'edit_forum', $_post->ID ) && dps_is_forum_closed( $_post->ID )
				)
			) ) {
			$output .= ' disabled="disabled" value=""';
		} else {
			$output .= ' value="' . $_post->ID .'"' . selected( $args['selected'], $_post->ID, false );
		}

		$output .= '>';
		$title   = apply_filters( 'dps_walker_dropdown_post_title', $_post->post_title, $output, $_post, $depth, $args );
		$output .= $pad . esc_html( $title );
		$output .= "</option>\n";
	}
}

endif; // class_exists check
