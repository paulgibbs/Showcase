<?php

/**
 * Main showcase Admin Class
 *
 * @package Showcase
 * @subpackage Administration
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'BB_Admin' ) ) :
/**
 * Loads showcase plugin admin area
 *
 * @package Showcase
 * @subpackage Administration
 * @since Showcase (1.0)
 */
class BB_Admin {

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
	 * @uses BB_Admin::setup_globals() Setup the globals needed
	 * @uses BB_Admin::includes() Include the required files
	 * @uses BB_Admin::setup_actions() Setup the hooks and actions
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
		$bbp = showcase();
		$this->admin_dir  = trailingslashit( $bbp->includes_dir . 'admin'  ); // Admin path
		$this->admin_url  = trailingslashit( $bbp->includes_url . 'admin'  ); // Admin url
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

		/** General Actions ***************************************************/

		add_action( 'dps_admin_menu',              array( $this, 'admin_menus'                ) ); // Add menu item to settings menu
		add_action( 'dps_admin_notices',           array( $this, 'activation_notice'          ) ); // Add notice if not using a showcase theme
		add_action( 'dps_register_admin_style',    array( $this, 'register_admin_style'       ) ); // Add green admin style
		add_action( 'dps_register_admin_settings', array( $this, 'register_admin_settings'    ) ); // Add settings
		add_action( 'dps_activation',              array( $this, 'new_install'                ) ); // Add menu item to settings menu
		add_action( 'admin_enqueue_scripts',       array( $this, 'enqueue_scripts'            ) ); // Add enqueued JS and CSS
		add_action( 'wp_dashboard_setup',          array( $this, 'dashboard_widget_right_now' ) ); // Forums 'Right now' Dashboard widget

		/** Ajax **************************************************************/

		add_action( 'wp_ajax_dps_suggest_topic',        array( $this, 'suggest_topic' ) );
		add_action( 'wp_ajax_nopriv_dps_suggest_topic', array( $this, 'suggest_topic' ) );

		/** Filters ***********************************************************/

		// Modify showcase's admin links
		add_filter( 'plugin_action_links', array( $this, 'modify_plugin_action_links' ), 10, 2 );

		// Hide the theme compat package selection
		add_filter( 'dps_admin_get_settings_sections', array( $this, 'hide_theme_compat_packages' ) );

		// Allow keymasters to save forums settings
		add_filter( 'option_page_capability_showcase',  array( $this, 'option_page_capability_showcase' ) );

		/** Network Admin *****************************************************/

		// Add menu item to settings menu
		add_action( 'network_admin_menu',  array( $this, 'network_admin_menus' ) );

		/** Dependencies ******************************************************/

		// Allow plugins to modify these actions
		do_action_ref_array( 'dps_admin_loaded', array( &$this ) );
	}

	/**
	 * Add the admin menus
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses add_management_page() To add the Recount page in Tools section
	 * @uses add_options_page() To add the Forums settings page in Settings
	 *                           section
	 */
	public function admin_menus() {

		$hooks = array();

		// Are settings enabled?
		if ( current_user_can( 'dps_settings_page' ) ) {
			add_options_page(
				__( 'Forums',  'showcase' ),
				__( 'Forums',  'showcase' ),
				$this->minimum_capability,
				'showcase',
				'dps_admin_settings'
			);
		}
	}

	/**
	 * Add the network admin menus
	 *
	 * @since Showcase (1.0)
	 * @uses add_submenu_page() To add the Update Forums page in Updates
	 */
	public function network_admin_menus() {

		// Bail if plugin is not network activated
		if ( ! is_plugin_active_for_network( showcase()->basename ) )
			return;

		add_submenu_page(
			'upgrade.php',
			__( 'Update Forums', 'dps' ),
			__( 'Update Forums', 'dps' ),
			'manage_network',
			'showcase-update',
			array( $this, 'network_update_screen' )
		);
	}

	/**
	 * If this is a new installation, create some initial forum content.
	 *
	 * @since Showcase (1.0)
	 * @return type
	 */
	public static function new_install() {
		if ( !dps_is_install() )
			return;

		dps_create_initial_content();
	}

	/**
	 * Register the settings
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses add_settings_section() To add our own settings section
	 * @uses add_settings_field() To add various settings fields
	 * @uses register_setting() To register various settings
	 * @todo Put fields into multidimensional array
	 */
	public static function register_admin_settings() {

		// Bail if no sections available
		$sections = dps_admin_get_settings_sections();
		if ( empty( $sections ) )
			return false;

		// Loop through sections
		foreach ( (array) $sections as $section_id => $section ) {

			// Only proceed if current user can see this section
			if ( ! current_user_can( $section_id ) )
				continue;

			// Only add section and fields if section has fields
			$fields = dps_admin_get_settings_fields_for_section( $section_id );
			if ( empty( $fields ) )
				continue;

			// Add the section
			add_settings_section( $section_id, $section['title'], $section['callback'], $section['page'] );

			// Loop through fields for this section
			foreach ( (array) $fields as $field_id => $field ) {

				// Add the field
				add_settings_field( $field_id, $field['title'], $field['callback'], $section['page'], $section_id, $field['args'] );

				// Register the setting
				register_setting( $section['page'], $field_id, $field['sanitize_callback'] );
			}
		}
	}

	/**
	 * Maps settings capabilities
	 *
	 * @since Showcase (1.0)
	 *
	 * @param array $caps Capabilities for meta capability
	 * @param string $cap Capability name
	 * @param int $user_id User id
	 * @param mixed $args Arguments
	 * @uses get_post() To get the post
	 * @uses get_post_type_object() To get the post type object
	 * @uses apply_filters() Calls 'dps_map_meta_caps' with caps, cap, user id and
	 *                        args
	 * @return array Actual capabilities for meta capability
	 */
	public static function map_settings_meta_caps( $caps = array(), $cap = '', $user_id = 0, $args = array() ) {

		// What capability is being checked?
		switch ( $cap ) {

			// showcase
			case 'dps_about_page'            : // About and Credits
			case 'dps_tools_page'            : // Tools Page
			case 'dps_tools_repair_page'     : // Tools - Repair Page
			case 'dps_tools_import_page'     : // Tools - Import Page
			case 'dps_tools_reset_page'      : // Tools - Reset Page
			case 'dps_settings_page'         : // Settings Page
			case 'dps_settings_main'         : // Settings - General
			case 'dps_settings_theme_compat' : // Settings - Theme compat
			case 'dps_settings_root_slugs'   : // Settings - Root slugs
			case 'dps_settings_single_slugs' : // Settings - Single slugs
			case 'dps_settings_per_page'     : // Settings - Per page
			case 'dps_settings_per_rss_page' : // Settings - Per RSS page
				$caps = array( showcase()->admin->minimum_capability );
				break;
		}

		return apply_filters( 'dps_map_settings_meta_caps', $caps, $cap, $user_id, $args );
	}

	/**
	 * Register the importers
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses apply_filters() Calls 'dps_importer_path' filter to allow plugins
	 *                        to customize the importer script locations.
	 */
	public function register_importers() {

		// Leave if we're not in the import section
		if ( !defined( 'WP_LOAD_IMPORTERS' ) )
			return;

		// Load Importer API
		require_once( ABSPATH . 'wp-admin/includes/import.php' );

		// Load our importers
		$importers = apply_filters( 'dps_importers', array( 'showcase' ) );

		// Loop through included importers
		foreach ( $importers as $importer ) {

			// Allow custom importer directory
			$import_dir  = apply_filters( 'dps_importer_path', $this->admin_dir . 'importers', $importer );

			// Compile the importer path
			$import_file = trailingslashit( $import_dir ) . $importer . '.php';

			// If the file exists, include it
			if ( file_exists( $import_file ) ) {
				require( $import_file );
			}
		}
	}

	/**
	 * Admin area activation notice
	 *
	 * Shows a nag message in admin area about the theme not supporting showcase
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses current_user_can() To check notice should be displayed.
	 */
	public function activation_notice() {
		// @todo - something fun
	}

	/**
	 * Add Settings link to plugins area
	 *
	 * @since Showcase (1.0)
	 *
	 * @param array $links Links array in which we would prepend our link
	 * @param string $file Current plugin basename
	 * @return array Processed links
	 */
	public static function modify_plugin_action_links( $links, $file ) {

		// Return normal links if not showcase
		if ( plugin_basename( showcase()->file ) != $file )
			return $links;

		// Add a few links to the existing links array
		return array_merge( $links, array(
			'settings' => '<a href="' . add_query_arg( array( 'page' => 'showcase'   ), admin_url( 'options-general.php' ) ) . '">' . esc_html__( 'Settings', 'dps' ) . '</a>',
			'about'    => '<a href="' . add_query_arg( array( 'page' => 'bbp-about' ), admin_url( 'index.php'           ) ) . '">' . esc_html__( 'About',    'showcase' ) . '</a>'
		) );
	}

	/**
	 * Add the 'Right now in Forums' dashboard widget
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses wp_add_dashboard_widget() To add the dashboard widget
	 */
	public static function dashboard_widget_right_now() {
		wp_add_dashboard_widget( 'bbp-dashboard-right-now', __( 'Right Now in Forums', 'dps' ), 'dps_dashboard_widget_right_now' );
	}

	/**
	 * Enqueue any admin scripts we might need
	 * @since Showcase (1.0)
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'suggest' );
	}

	/**
	 * Registers the showcase admin color scheme
	 *
	 * Because wp-content can exist outside of the WordPress root there is no
	 * way to be certain what the relative path of the admin images is.
	 * We are including the two most common configurations here, just in case.
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses wp_admin_css_color() To register the color scheme
	 */
	public function register_admin_style () {

		// Updated admin color scheme CSS
		if ( function_exists( 'wp_enqueue_media' ) ) {
			$green_scheme = $this->styles_url . 'green.css';

		} else {
			$green_scheme = $this->styles_url . 'green-34.css';
		}

		// Register the green scheme
		wp_admin_css_color( 'showcase', esc_html_x( 'Green', 'admin color scheme', 'dps' ), $green_scheme, array( '#222222', '#006600', '#deece1', '#6eb469' ) );
	}

	/**
	 * Hide theme compat package selection if only 1 package is registered
	 *
	 * @since Showcase (1.0)
	 *
	 * @param array $sections Forums settings sections
	 * @return array
	 */
	public function hide_theme_compat_packages( $sections = array() ) {
		if ( count( showcase()->theme_compat->packages ) <= 1 )
			unset( $sections['dps_settings_theme_compat'] );

		return $sections;
	}

	/**
	 * Allow keymaster role to save Forums settings
	 *
	 * @since Showcase (1.0)
	 *
	 * @param string $capability
	 * @return string Return 'keep_gate' capability
	 */
	public function option_page_capability_showcase( $capability = 'manage_options' ) {
		$capability = 'keep_gate';
		return $capability;
	}

	/** Ajax ******************************************************************/

	/**
	 * Ajax action for facilitating the forum auto-suggest
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses get_posts()
	 * @uses dps_get_topic_post_type()
	 * @uses dps_get_topic_id()
	 * @uses dps_get_topic_title()
	 */
	public function suggest_topic() {

		// TRy to get some topics
		$topics = get_posts( array(
			's'         => like_escape( $_REQUEST['q'] ),
			'post_type' => dps_get_topic_post_type()
		) );

		// If we found some topics, loop through and display them
		if ( ! empty( $topics ) ) {
			foreach ( (array) $topics as $post ) {
				echo sprintf( __( '%s - %s', 'dps' ), dps_get_topic_id( $post->ID ), dps_get_topic_title( $post->ID ) ) . "\n";
			}
		}
		die();
	}

	/** About *****************************************************************/

	/**
	 * Output the about screen
	 *
	 * @since Showcase (1.0)
	 */
	public function about_screen() {

		list( $display_version ) = explode( '-', dps_get_version() ); ?>

		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to showcase %s', 'dps' ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! showcase %s goes great with pizza and popcorn, and will nicely complement your community too!', 'dps' ), $display_version ); ?></div>
			<div class="bbp-badge"><?php printf( __( 'Version %s', 'dps' ), $display_version ); ?></div>

			<h2 class="nav-tab-wrapper">
				<a class="nav-tab nav-tab-active" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'bbp-about' ), 'index.php' ) ) ); ?>">
					<?php _e( 'What&#8217;s New', 'dps' ); ?>
				</a><a class="nav-tab" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'bbp-credits' ), 'index.php' ) ) ); ?>">
					<?php _e( 'Credits', 'dps' ); ?>
				</a>
			</h2>

			<div class="changelog">
				<h3><?php _e( 'Forum Search', 'dps' ); ?></h3>

				<div class="feature-section">
					<h4><?php _e( 'Only Forum Content', 'dps' ); ?></h4>
					<p><?php _e( 'Allow your forums to be searched without mixing in your posts or pages.', 'dps' ); ?></p>

					<h4><?php _e( 'Choose Your Own Slug', 'dps' ); ?></h4>
					<p><?php _e( 'Setup your forum search to live anywhere relative to the forum index.', 'dps' ); ?></p>
				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'New & Improved Forum Importers', 'dps' ); ?></h3>

				<div class="feature-section">
					<h4><?php _e( 'BBCodes & Smilies', 'dps' ); ?></h4>
					<p><?php _e( 'Happy faces all-around now that the importers properly convert BBCodes & smilies. :)', 'dps' ); ?></p>

					<h4><?php _e( 'Vanilla', 'dps' ); ?></h4>
					<p><?php _e( 'Tired of plain old Vanilla? Now you can easily switch to <del>Mint Chocolate Chip</del> showcase!', 'dps' ); ?></p>

					<h4><?php _e( 'SimplePress', 'dps' ); ?></h4>
					<p><?php _e( 'Converting an existing SimplePress powered forum to showcase has never been "simpler!"', 'dps' ); ?></p>

					<h4><?php _e( 'Mingle', 'dps' ); ?></h4>
					<p><?php _e( 'No time to... chit-chat; convert your Mingle forums to showcase today!', 'dps' ); ?></p>
				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Even Better BuddyPress Integration', 'dps' ); ?></h3>

				<div class="feature-section">
					<h4><?php _e( 'showcase powered BuddyPress Group Forums', 'dps' ); ?></h4>
					<p><?php _e( 'Use showcase to manage your BuddyPress Group Forums, allowing for seamless integration and improved plugin performance.', 'dps' ); ?></p>
				</div>
			</div>

			<div class="changelog">
				<h3><?php _e( 'Under the Hood', 'dps' ); ?></h3>

				<div class="feature-section col three-col">
					<div>
						<h4><?php _e( 'Smarter Fancy Editor', 'dps' ); ?></h4>
						<p><?php _e( 'We simplified the Fancy Editor, and the allowed HTML tags that work with it.', 'dps' ); ?></p>

						<h4><?php _e( 'Better Code Posting', 'dps' ); ?></h4>
						<p><?php _e( 'Your users can now post code snippets without too much hassle.', 'dps' ); ?></p>
					</div>

					<div>
						<h4><?php _e( 'Template Stacking', 'dps' ); ?></h4>
						<p><?php _e( 'Now you can replace specific template parts on the fly without modifying the existing theme.', 'dps' ); ?></p>

						<h4><?php _e( 'TwentyThirteen Tested', 'dps' ); ?></h4>
						<p><?php _e( 'showcase 2.3 already works with the in-development TwentyThirteen theme, coming in a future version of WordPress.', 'dps' ); ?></p>
					</div>

					<div class="last-feature">
						<h4><?php _e( 'Statistics Shortcode', 'dps' ); ?></h4>
						<p><?php _e( 'The old statistics easter-egg page was turned into an easy to use shortcode.', 'dps' ); ?></p>

						<h4><?php _e( 'Green Theme Updated', 'dps' ); ?></h4>
						<p><?php _e( 'The green admin theme easter-egg was updated to work with WordPress 3.5 changes.', 'dps' ); ?></p>
					</div>
				</div>
			</div>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'showcase' ), 'options-general.php' ) ) ); ?>"><?php _e( 'Go to Forum Settings', 'dps' ); ?></a>
			</div>

		</div>

		<?php
	}

	/**
	 * Output the credits screen
	 *
	 * Hardcoding this in here is pretty janky. It's fine for 2.2, but we'll
	 * want to leverage api.wordpress.org eventually.
	 *
	 * @since Showcase (1.0)
	 */
	public function credits_screen() {

		list( $display_version ) = explode( '-', dps_get_version() ); ?>

		<div class="wrap about-wrap">
			<h1><?php printf( __( 'Welcome to showcase %s', 'dps' ), $display_version ); ?></h1>
			<div class="about-text"><?php printf( __( 'Thank you for updating to the latest version! showcase %s goes great with pizza and popcorn, and will nicely complement your community too!', 'dps' ), $display_version ); ?></div>
			<div class="bbp-badge"><?php printf( __( 'Version %s' ), $display_version ); ?></div>

			<h2 class="nav-tab-wrapper">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'bbp-about' ), 'index.php' ) ) ); ?>" class="nav-tab">
					<?php _e( 'What&#8217;s New', 'dps' ); ?>
				</a><a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'bbp-credits' ), 'index.php' ) ) ); ?>" class="nav-tab nav-tab-active">
					<?php _e( 'Credits', 'dps' ); ?>
				</a>
			</h2>

			<p class="about-description"><?php _e( 'showcase is created by a worldwide swarm of busy, busy bees.', 'dps' ); ?></p>

			<h4 class="wp-people-group"><?php _e( 'Project Leaders', 'dps' ); ?></h4>
			<ul class="wp-people-group " id="wp-people-group-project-leaders">
				<li class="wp-person" id="wp-person-matt">
					<a href="http://profiles.wordpress.org/matt"><img src="http://0.gravatar.com/avatar/767fc9c115a1b989744c755db47feb60?s=60" class="gravatar" alt="Matt Mullenweg" /></a>
					<a class="web" href="http://profiles.wordpress.org/matt">Matt Mullenweg</a>
					<span class="title"><?php _e( 'Founding Developer', 'dps' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-johnjamesjacoby">
					<a href="http://profiles.wordpress.org/johnjamesjacoby"><img src="http://0.gravatar.com/avatar/81ec16063d89b162d55efe72165c105f?s=60" class="gravatar" alt="John James Jacoby" /></a>
					<a class="web" href="http://profiles.wordpress.org/johnjamesjacoby">John James Jacoby</a>
					<span class="title"><?php _e( 'Lead Developer', 'dps' ); ?></span>
				</li>
				<li class="wp-person" id="wp-person-jmdodd">
					<a href="http://profiles.wordpress.org/jmdodd"><img src="http://0.gravatar.com/avatar/6a7c997edea340616bcc6d0fe03f65dd?s=60" class="gravatar" alt="Jennifer M. Dodd" /></a>
					<a class="web" href="http://profiles.wordpress.org/jmdodd">Jennifer M. Dodd</a>
					<span class="title"></span>
				</li>
			</ul>

			<h4 class="wp-people-group"><?php _e( 'Contributing Developers', 'dps' ); ?></h4>
			<ul class="wp-people-group " id="wp-people-group-contributing-developers">
				<li class="wp-person" id="wp-person-netweb">
					<a href="http://profiles.wordpress.org/netweb"><img src="http://0.gravatar.com/avatar/97e1620b501da675315ba7cfb740e80f?s=60" class="gravatar" alt="Stephen Edgar" /></a>
					<a class="web" href="http://profiles.wordpress.org/netweb">Stephen Edgar</a>
					<span class="title"></span>
				</li>
				<li class="wp-person" id="wp-person-jaredatch">
					<a href="http://profiles.wordpress.org/jaredatch"><img src="http://0.gravatar.com/avatar/e341eca9e1a85dcae7127044301b4363?s=60" class="gravatar" alt="Jared Atchison" /></a>
					<a class="web" href="http://profiles.wordpress.org/jaredatch">Jared Atchison</a>
					<span class="title"></span>
				</li>
				<li class="wp-person" id="wp-person-gautamgupta">
					<a href="http://profiles.wordpress.org/gautamgupta"><img src="http://0.gravatar.com/avatar/b0810422cbe6e4eead4def5ae7a90b34?s=60" class="gravatar" alt="Gautam Gupta" /></a>
					<a class="web" href="http://profiles.wordpress.org/gautamgupta">Gautam Gupta</a>
					<span class="title"></span>
				</li>
			</ul>

			<h4 class="wp-people-group"><?php _e( 'Core Contributors to showcase 2.3', 'dps' ); ?></h4>
			<p class="wp-credits-list">
				<a href="http://profiles.wordpress.org/alexvorn2">alexvorn2</a>,
				<a href="http://profiles.wordpress.org/alex-ye">alex-ye</a>,
				<a href="http://profiles.wordpress.org/anointed">anointed</a>,
				<a href="http://profiles.wordpress.org/boonebgorges">boonebgorges</a>,
				<a href="http://profiles.wordpress.org/chexee">chexee</a>,
				<a href="http://profiles.wordpress.org/cnorris23">cnorris23</a>,
				<a href="http://profiles.wordpress.org/DanielJuhl">DanielJuhl</a>,
				<a href="http://profiles.wordpress.org/daveshine">daveshine</a>,
				<a href="http://profiles.wordpress.org/dimadin">dimadin</a>,
				<a href="http://profiles.wordpress.org/DJPaul">DJPaul</a>,
				<a href="http://profiles.wordpress.org/duck_">duck_</a>,
				<a href="http://profiles.wordpress.org/gawain">gawain</a>,
				<a href="http://profiles.wordpress.org/iamzippy">iamzippy</a>,
				<a href="http://profiles.wordpress.org/isaacchapman">isaacchapman</a>,
				<a href="http://profiles.wordpress.org/jane">jane</a>,
				<a href="http://profiles.wordpress.org/jkudish">jkudish</a>,
				<a href="http://profiles.wordpress.org/mamaduka">mamaduka</a>,
				<a href="http://profiles.wordpress.org/mercime">mercime</a>,
				<a href="http://profiles.wordpress.org/mesayre">mesayre</a>,
				<a href="http://profiles.wordpress.org/mordauk">mordauk</a>,
				<a href="http://profiles.wordpress.org/MZAWeb">MZAWeb</a>,
				<a href="http://profiles.wordpress.org/nexia">nexia</a>,
				<a href="http://profiles.wordpress.org/Omicron7">Omicron7</a>,
				<a href="http://profiles.wordpress.org/otto42">otto42</a>,
				<a href="http://profiles.wordpress.org/pavelevap">pavelevap</a>,
				<a href="http://profiles.wordpress.org/plescheff">plescheff</a>,
				<a href="http://profiles.wordpress.org/scribu">scribu</a>,
				<a href="http://profiles.wordpress.org/sorich87">sorich87</a>,
				<a href="http://profiles.wordpress.org/SteveAtty">SteveAtty</a>,
				<a href="http://profiles.wordpress.org/tmoorewp">tmoorewp</a>,
				<a href="http://profiles.wordpress.org/tott">tott</a>,
				<a href="http://profiles.wordpress.org/tungdo">tungdo</a>,
				<a href="http://profiles.wordpress.org/vibol">vibol</a>,
				<a href="http://profiles.wordpress.org/wonderboymusic">wonderboymusic</a>,
				<a href="http://profiles.wordpress.org/westi">westi</a>,
				<a href="http://profiles.wordpress.org/xiosen">xiosen</a>,
			</p>

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'showcase' ), 'options-general.php' ) ) ); ?>"><?php _e( 'Go to Forum Settings', 'dps' ); ?></a>
			</div>

		</div>

		<?php
	}

	/** Updaters **************************************************************/

	/**
	 * Update all showcase forums across all sites
	 *
	 * @since Showcase (1.0)
	 *
	 * @global WPDB $wpdb
	 * @uses get_blog_option()
	 * @uses wp_remote_get()
	 */
	public static function update_screen() {

		// Get action
		$action = isset( $_GET['action'] ) ? $_GET['action'] : ''; ?>

		<div class="wrap">
			<div id="icon-edit" class="icon32 icon32-posts-topic"><br /></div>
			<h2><?php _e( 'Update Forum', 'dps' ); ?></h2>

		<?php

		// Taking action
		switch ( $action ) {
			case 'bbp-update' :

				// Run the full updater
				dps_version_updater(); ?>

				<p><?php _e( 'All done!', 'dps' ); ?></p>
				<a class="button" href="index.php?page=bbp-update"><?php _e( 'Go Back', 'dps' ); ?></a>

				<?php

				break;

			case 'show' :
			default : ?>

				<p><?php _e( 'You can update your forum through this page. Hit the link below to update.', 'dps' ); ?></p>
				<p><a class="button" href="index.php?page=bbp-update&amp;action=bbp-update"><?php _e( 'Update Forum', 'dps' ); ?></a></p>

			<?php break;

		} ?>

		</div><?php
	}

	/**
	 * Update all showcase forums across all sites
	 *
	 * @since Showcase (1.0)
	 *
	 * @global WPDB $wpdb
	 * @uses get_blog_option()
	 * @uses wp_remote_get()
	 */
	public static function network_update_screen() {
		global $wpdb;

		// Get action
		$action = isset( $_GET['action'] ) ? $_GET['action'] : ''; ?>

		<div class="wrap">
			<div id="icon-edit" class="icon32 icon32-posts-topic"><br /></div>
			<h2><?php _e( 'Update Forums', 'dps' ); ?></h2>

		<?php

		// Taking action
		switch ( $action ) {
			case 'showcase-update' :

				// Site counter
				$n = isset( $_GET['n'] ) ? intval( $_GET['n'] ) : 0;

				// Get blogs 5 at a time
				$blogs = $wpdb->get_results( "SELECT * FROM {$wpdb->blogs} WHERE site_id = '{$wpdb->siteid}' AND spam = '0' AND deleted = '0' AND archived = '0' ORDER BY registered DESC LIMIT {$n}, 5", ARRAY_A );

				// No blogs so all done!
				if ( empty( $blogs ) ) : ?>

					<p><?php _e( 'All done!', 'dps' ); ?></p>
					<a class="button" href="update-core.php?page=showcase-update"><?php _e( 'Go Back', 'dps' ); ?></a>

					<?php break; ?>

				<?php

				// Still have sites to loop through
				else : ?>

					<ul>

						<?php foreach ( (array) $blogs as $details ) :

							$siteurl = get_blog_option( $details['blog_id'], 'siteurl' ); ?>

							<li><?php echo $siteurl; ?></li>

							<?php

							// Get the response of the showcase update on this site
							$response = wp_remote_get(
								trailingslashit( $siteurl ) . 'wp-admin/index.php?page=bbp-update&action=bbp-update',
								array( 'timeout' => 30, 'httpversion' => '1.1' )
							);

							// Site errored out, no response?
							if ( is_wp_error( $response ) )
								wp_die( sprintf( __( 'Warning! Problem updating %1$s. Your server may not be able to connect to sites running on it. Error message: <em>%2$s</em>', 'dps' ), $siteurl, $response->get_error_message() ) );

							// Switch to the new blog
							switch_to_blog( $details[ 'blog_id' ] );

							$basename = showcase()->basename;

							// Run the updater on this site
							if ( is_plugin_active_for_network( $basename ) || is_plugin_active( $basename ) ) {
								dps_version_updater();
							}

							// restore original blog
							restore_current_blog();

							// Do some actions to allow plugins to do things too
							do_action( 'after_showcase_upgrade', $response             );
							do_action( 'dps_upgrade_site',      $details[ 'blog_id' ] );

						endforeach; ?>

					</ul>

					<p>
						<?php _e( 'If your browser doesn&#8217;t start loading the next page automatically, click this link:', 'dps' ); ?>
						<a class="button" href="update-core.php?page=showcase-update&amp;action=showcase-update&amp;n=<?php echo ( $n + 5 ); ?>"><?php _e( 'Next Forums', 'dps' ); ?></a>
					</p>
					<script type='text/javascript'>
						<!--
						function nextpage() {
							location.href = 'update-core.php?page=showcase-update&action=showcase-update&n=<?php echo ( $n + 5 ) ?>';
						}
						setTimeout( 'nextpage()', 250 );
						//-->
					</script><?php

				endif;

				break;

			case 'show' :
			default : ?>

				<p><?php _e( 'You can update all the forums on your network through this page. It works by calling the update script of each site automatically. Hit the link below to update.', 'dps' ); ?></p>
				<p><a class="button" href="update-core.php?page=showcase-update&amp;action=showcase-update"><?php _e( 'Update Forums', 'dps' ); ?></a></p>

			<?php break;

		} ?>

		</div><?php
	}
}
endif; // class_exists check

/**
 * Setup showcase Admin
 *
 * @since Showcase (1.0)
 *
 * @uses BB_Admin
 */
function dps_admin() {
	showcase()->admin = new BB_Admin();
}
