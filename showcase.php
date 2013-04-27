<?php
/**
 * The Showcase plugin
 *
 * @package Showcase
 */

/**
 * Plugin Name: Showcase
 * Plugin URI:  http://example.org
 * Description: Showcase is a kit that helps you build a powerful, modern WordPress plugin.
 * Author: Paul Gibbs
 * Author URI: http://byotos.com/
 * Text Domain: dps
 * License:     GPLv2
 * Version:     1.0
 * Domain Path: ../../languages/plugins/
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'Showcase' ) ) :
/**
 * Main Showcase class
 *
 * @since Showcase (1.0)
 */
final class Showcase {

	/** Magic *****************************************************************/

	/**
	 * Showcase uses many variables, several of which can be filtered to
	 * customize the way it operates. Most of these variables are stored in a
	 * private array that gets updated with the help of PHP magic methods.
	 *
	 * This is a precautionary measure, to avoid potential errors produced by
	 * unanticipated direct manipulation of Showcase's run-time data.
	 *
	 * @see Showcase::setup_globals()
	 * @var array
	 */
	private $data;


	/** Not Magic *************************************************************/

	/**
	 * @var obj Add-ons append to this (Akismet, BuddyPress, etc...)
	 */
	public $extend;

	/**
	 * @var array Overloads get_option()
	 */
	public $options      = array();

	/**
	 * @var array Overloads get_user_meta()
	 */
	public $user_options = array();


	/** Singleton *************************************************************/

	/**
	 * @var Showcase The one true Showcase
	 */
	private static $instance;

	/**
	 * Main Showcase instance
	 *
	 * Insures that only one instance of Showcase exists in memory at any one
	 * time. Also prevents needing to define globals all over the place.
	 *
	 * @since Showcase (1.0)
	 * @uses Showcase::setup_globals() Setup the globals needed
	 * @uses Showcase::includes() Include the required files
	 * @uses Showcase::setup_actions() Setup the hooks and actions
	 * @see showcase()
	 * @return The one true Showcase
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new Showcase;
			self::$instance->setup_globals();
			self::$instance->includes();
			self::$instance->setup_actions();
		}
		return self::$instance;
	}


	/** Magic Methods *********************************************************/

	/**
	 * A dummy constructor to prevent Showcase from being loaded more than once.
	 *
	 * @since Showcase (1.0)
	 * @see Showcase::instance()
	 * @see showcase();
	 */
	private function __construct() { /* Do nothing here */ }

	/**
	 * A dummy magic method to prevent Showcase from being cloned
	 *
	 * @since Showcase (1.0)
	 */
	public function __clone() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'dps' ), '1.0' ); }

	/**
	 * A dummy magic method to prevent Showcase from being unserialized
	 *
	 * @since Showcase (1.0)
	 */
	public function __wakeup() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'dps' ), '1.0' ); }

	/**
	 * Magic method for checking the existence of a certain custom field
	 *
	 * @since Showcase (1.0)
	 */
	public function __isset( $key ) { return isset( $this->data[$key] ); }

	/**
	 * Magic method for getting Showcase variables
	 *
	 * @since Showcase (1.0)
	 */
	public function __get( $key ) { return isset( $this->data[$key] ) ? $this->data[$key] : null; }

	/**
	 * Magic method for setting Showcase variables
	 *
	 * @since Showcase (1.0)
	 */
	public function __set( $key, $value ) { $this->data[$key] = $value; }

	/**
	 * Magic method for unsetting Showcase variables
	 *
	 * @since Showcase (1.0)
	 */
	public function __unset( $key ) { if ( isset( $this->data[$key] ) ) unset( $this->data[$key] ); }

	/**
	 * Magic method to prevent notices and errors from invalid method calls
	 *
	 * @since Showcase (1.0)
	 */
	public function __call( $name = '', $args = array() ) { unset( $name, $args ); return null; }


	/** Private Methods *******************************************************/

	/**
	 * Set some smart defaults to class variables. Allow some of them to be
	 * filtered to allow for early overriding.
	 *
	 * @since Showcase (1.0)
	 */
	private function setup_globals() {

		/** Versions **********************************************************/

		$this->version    = '1.0-bleeding-1';
		$this->db_version = '1';


		/** Paths *************************************************************/

		// Setup some base path and URL information
		$this->file       = __FILE__;
		$this->basename   = apply_filters( 'dps_plugin_basenname', plugin_basename( $this->file ) );
		$this->plugin_dir = apply_filters( 'dps_plugin_dir_path',  plugin_dir_path( $this->file ) );
		$this->plugin_url = apply_filters( 'dps_plugin_dir_url',   plugin_dir_url ( $this->file ) );

		// Includes
		$this->includes_dir = apply_filters( 'dps_includes_dir', trailingslashit( $this->plugin_dir . 'includes'  ) );
		$this->includes_url = apply_filters( 'dps_includes_url', trailingslashit( $this->plugin_url . 'includes'  ) );

		// Languages
		$this->lang_dir = apply_filters( 'dps_lang_dir', trailingslashit( $this->plugin_dir . 'languages' ) );

		// Templates
		$this->themes_dir = apply_filters( 'dps_themes_dir', trailingslashit( $this->plugin_dir . 'templates' ) );
		$this->themes_url = apply_filters( 'dps_themes_url', trailingslashit( $this->plugin_url . 'templates' ) );


		/** Identifiers *******************************************************/

		// Post type identifiers
		$this->showcase_post_type = apply_filters( 'dps_showcase_post_type', 'showcase' );


		/** Queries ***********************************************************/

		$this->current_showcase_id = 0;              // Current showcase id
		$this->showcase_query      = new stdClass(); // Main showcase query


		/** Theme Compat ******************************************************/

		$this->theme_compat = new stdClass(); // Base theme compatibility class
		$this->filters      = new stdClass(); // Used when adding/removing filters


		/** Misc **************************************************************/

		$this->domain = 'dps';          // Unique identifier for retrieving translated strings
		$this->extend = new stdClass(); // Plugins add data here
		$this->errors = new WP_Error(); // Feedback
	}

	/**
	 * Include required files
	 *
	 * @since Showcase (1.0)
	 * @access private
	 * @uses is_admin() If in WordPress admin, load additional file
	 */
	private function includes() {

		/** Core **************************************************************/

		require( $this->includes_dir . 'core/sub-actions.php'        );
		require( $this->includes_dir . 'core/functions.php'          );
		require( $this->includes_dir . 'core/cache.php'              );
		require( $this->includes_dir . 'core/options.php'            );
		require( $this->includes_dir . 'core/capabilities.php'       );
		require( $this->includes_dir . 'core/update.php'             );
		require( $this->includes_dir . 'core/template-functions.php' );
		require( $this->includes_dir . 'core/template-loader.php'    );
		require( $this->includes_dir . 'core/theme-compat.php'       );

		/** Components ********************************************************/

		// Common
		require( $this->includes_dir . 'common/ajax.php'           );
		require( $this->includes_dir . 'common/classes.php'        );
		require( $this->includes_dir . 'common/functions.php'      );
		require( $this->includes_dir . 'common/formatting.php'     );
		require( $this->includes_dir . 'common/template-tags.php'  );
		require( $this->includes_dir . 'common/widgets.php'        );
		require( $this->includes_dir . 'common/shortcodes.php'     );

		// Showcase
		require( $this->includes_dir . 'showcase/capabilities.php'   );
		require( $this->includes_dir . 'showcase/functions.php'      );
		require( $this->includes_dir . 'showcase/template-tags.php'  );


		/** Hooks *************************************************************/

		require( $this->includes_dir . 'core/extend.php'  );
		require( $this->includes_dir . 'core/actions.php' );
		require( $this->includes_dir . 'core/filters.php' );


		/** Admin *************************************************************/

		// Quick admin check and load if needed
		if ( is_admin() ) {
			/*require( $this->includes_dir . 'admin/admin.php'   );
			require( $this->includes_dir . 'admin/actions.php' );*/
		}
	}

	/**
	 * Setup the default hooks and actions
	 *
	 * @since Showcase (1.0)
	 * @access private
	 * @uses add_action() To add various actions
	 */
	private function setup_actions() {

		// Add actions to plugin activation and deactivation hooks
		add_action( 'activate_'   . $this->basename, 'dps_activation'   );
		add_action( 'deactivate_' . $this->basename, 'dps_deactivation' );

		// If Showcase is being deactivated, do not add any actions
		if ( dps_is_deactivation( $this->basename ) )
			return;

		// Array of Showcase core actions
		$actions = array(
			'setup_theme',              // Setup the default theme compat
			'register_post_types',      // Register post types (showcase)
			'register_post_statuses',   // Register post statuses
			'register_taxonomies',      // Register taxonomies
			'register_shortcodes',      // Register shortcodes
			'register_theme_packages',  // Register bundled theme packages (showcase)
			'load_textdomain',          // Load textdomain (showcase)
			'add_rewrite_tags',         // Add rewrite tags
			'generate_rewrite_rules'    // Generate rewrite rules
		);

		// Add the actions
		foreach( $actions as $class_action )
			add_action( 'dps_' . $class_action, array( $this, $class_action ), 5 );

		// All Showcase actions are setup (includes dps-core-hooks.php)
		do_action_ref_array( 'dps_after_setup_actions', array( &$this ) );
	}


	/** Public Methods ********************************************************/

	/**
	 * Register bundled theme packages
	 *
	 * @since Showcase (1.0)
	 */
	public function register_theme_packages() {

		// Register the default theme compatibility package
		dps_register_theme_package( array(
			'id'      => 'default',
			'name'    => __( 'Showcase Default', 'dps' ),
			'version' => dps_get_version(),
			'dir'     => trailingslashit( $this->themes_dir . 'default' ),
			'url'     => trailingslashit( $this->themes_url . 'default' )
		) );

		// Register the basic theme stack. This is really dope.
		dps_register_template_stack( 'get_stylesheet_directory', 10 );
		dps_register_template_stack( 'get_template_directory',   12 );
		dps_register_template_stack( 'dps_get_theme_compat_dir', 14 );
	}

	/**
	 * Setup the default Showcase theme compatibility location.
	 *
	 * @since Showcase (1.0)
	 */
	public function setup_theme() {

		// Bail if something already has this under control
		if ( ! empty( $this->theme_compat->theme ) )
			return;

		// Setup the theme package to use for compatibility
		dps_setup_theme_compat( dps_get_theme_package_id() );
	}

	/**
	 * Load the translation file for current language. Checks the default languages folder.
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses apply_filters() Calls 'showcase_locale' with the {@link get_locale()} value
	 * @uses load_textdomain() To load the textdomain
	 * @return bool True on success, false on failure
	 */
	public function load_textdomain() {
		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale',  get_locale(), $this->domain );
		$mofile = sprintf( '%1$s-%2$s.mo', $this->domain, $locale );

		// Look in global /wp-content/languages/plugins/ folder
		$mofile_global = WP_LANG_DIR . '/plugins/' . $mofile;

		if ( file_exists( $mofile_global ) )
			load_textdomain( $this->domain, $mofile_global );
	}

	/**
	 * Setup the post type for the showcase
	 *
	 * @since Showcase (1.0)
	 * @uses register_post_type() To register the post types
	 * @uses apply_filters() Calls various filters to modify the arguments sent to register_post_type()
	 */
	public static function register_post_types() {

		// Define local variable(s)
		$post_type = array();

		/** Showcase ************************************************************/

		// Showcase labels
		$post_type['labels'] = array(
			'name'               => __( 'Showcase',                'dps' ),
			'menu_name'          => __( 'Showcase',                'dps' ),
			'singular_name'      => __( 'Showcase',                'dps' ),
			'all_items'          => __( 'All Sites',               'dps' ),
			'add_new'            => __( 'New Site',                'dps' ),
			'add_new_item'       => __( 'Add New Site',            'dps' ),
			'edit'               => __( 'Edit',                    'dps' ),
			'edit_item'          => __( 'Edit Site',               'dps' ),
			'new_item'           => __( 'New Site',                'dps' ),
			'view'               => __( 'View Showcase',           'dps' ),
			'view_item'          => __( 'View Sites',              'dps' ),
			'search_items'       => __( 'Search Showcase',         'dps' ),
			'not_found'          => __( 'No sites found',          'dps' ),
			'not_found_in_trash' => __( 'No sites found in Trash', 'dps' ),
		);

		// Showcase rewrite
		$post_type['rewrite'] = array(
			'slug'       => dps_get_showcase_slug(),
			'with_front' => false
		);

		// Showcase supports
		$post_type['supports'] = array(
			'editor',
			'excerpt',
			'revisions',
			'thumbnail',
			'title',
		);

		// Register Showcase content type
		register_post_type(
			dps_get_showcase_post_type(),
			apply_filters( 'dps_register_showcase_post_type', array(
				'description'         => __( 'Showcase', 'dps' ),
				'has_archive'         => dps_get_root_slug(),
				'labels'              => $post_type['labels'],
				'public'              => true,
				'query_var'           => true,
				'rewrite'             => $post_type['rewrite'],
				'supports'            => $post_type['supports'],
			) )
		);
	}

	/**
	 * Register the post statuses used by Showcase
	 *
	 * @since Showcase (1.0)
	 */
	public static function register_post_statuses() {
	}

	/**
	 * Register taxonomies
	 *
	 * @since Showcase (1.0)
	 */
	public static function register_taxonomies() {
	}

	/**
	 * Register the Showcase shortcodes
	 *
	 * @since Showcase (1.0)
	 * @uses DPS_Shortcodes
	 */
	public function register_shortcodes() {
		$this->shortcodes = new DPS_Shortcodes();
	}


	/** Custom Rewrite Rules **************************************************/

	/**
	 * Add the Showcase-specific rewrite tags
	 *
	 * @since Showcase (1.0)
	 */
	public static function add_rewrite_tags() {
	}

	/**
	 * Register Showcase-specific rewrite rules for URIs that are not setup for us by way of custom post types or taxonomies.
	 *
	 * @since Showcase (1.0)
	 * @param WP_Rewrite $wp_rewrite Varebones-sepecific rules are appended in $wp_rewrite->rules
	 */
	public static function generate_rewrite_rules( $wp_rewrite ) {
		return $wp_rewrite;
	}
}

/**
 * The main function responsible for returning the one true Showcase instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing to declare the global.
 * Example: <?php $showcase = showcase(); ?>
 *
 * @return The one true Showcase Instance
 */
function showcase() {
	return Showcase::instance();
}

/**
 * Hook Showcase early onto the 'plugins_loaded' action.
 *
 * This gives all other plugins the chance to load before Showcase, to get their
 * actions, filters, and overrides setup without Showcase being in the way.
 */
if ( defined( 'SHOWCASE_LATE_LOAD' ) ) {
	add_action( 'plugins_loaded', 'dps', (int) SHOWCASE_LATE_LOAD );

} else {
	showcase();
}

endif; // class_exists check
