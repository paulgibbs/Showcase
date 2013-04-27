<?php

/**
 * Showcase Users Admin Class
 *
 * @package Showcase
 * @subpackage Administration
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !class_exists( 'BB_Users_Admin' ) ) :
/**
 * Loads showcase users admin area
 *
 * @package Showcase
 * @subpackage Administration
 * @since Showcase (1.0)
 */
class BB_Users_Admin {

	/**
	 * The showcase users admin loader
	 *
	 * @since Showcase (1.0)
	 *
	 * @uses BB_Users_Admin::setup_globals() Setup the globals needed
	 * @uses BB_Users_Admin::setup_actions() Setup the hooks and actions
	 */
	public function __construct() {
		$this->setup_actions();
	}

	/**
	 * Setup the admin hooks, actions and filters
	 *
	 * @since Showcase (1.0)
	 * @access private
	 *
	 * @uses add_action() To add various actions
	 */
	function setup_actions() {

		// Bail if in network admin
		if ( is_network_admin() )
			return;

		// User profile edit/display actions
		add_action( 'edit_user_profile', array( $this, 'secondary_role_display' ) );

		// WordPress user screen
		add_action( 'restrict_manage_users',      array( $this, 'user_role_bulk_dropdown' )        );
		add_filter( 'manage_users_columns',       array( $this, 'user_role_column'        )        );
		add_filter( 'manage_users_custom_column', array( $this, 'user_role_row'           ), 10, 3 );

		// Process bulk role change
		add_action( 'load-users.php',             array( $this, 'user_role_bulk_change'   )        );
	}

	/**
	 * Default interface for setting a forum role
	 *
	 * @since Showcase (1.0)
	 *
	 * @param WP_User $profileuser User data
	 * @return bool Always false
	 */
	public static function secondary_role_display( $profileuser ) {

		// Bail if current user cannot edit users
		if ( ! current_user_can( 'edit_user', $profileuser->ID ) )
			return;

		// Get the roles
		$dynamic_roles = dps_get_dynamic_roles();

		// Only keymasters can set other keymasters
		if ( ! dps_is_user_keymaster() )
			unset( $dynamic_roles[ dps_get_keymaster_role() ] ); ?>

		<h3><?php _e( 'Forums', 'dps' ); ?></h3>

		<table class="form-table">
			<tbody>
				<tr>
					<th><label for="bbp-forums-role"><?php _e( 'Forum Role', 'dps' ); ?></label></th>
					<td>

						<?php $user_role = dps_get_user_role( $profileuser->ID ); ?>

						<select name="bbp-forums-role" id="bbp-forums-role">

							<?php if ( ! empty( $user_role ) ) : ?>

								<option value=""><?php _e( '&mdash; No role for these forums &mdash;', 'dps' ); ?></option>

							<?php else : ?>

								<option value="" selected="selected"><?php _e( '&mdash; No role for these forums &mdash;', 'dps' ); ?></option>

							<?php endif; ?>

							<?php foreach ( $dynamic_roles as $role => $details ) : ?>

								<option <?php selected( $user_role, $role ); ?> value="<?php echo esc_attr( $role ); ?>"><?php echo translate_user_role( $details['name'] ); ?></option>

							<?php endforeach; ?>

						</select>
					</td>
				</tr>

			</tbody>
		</table>

		<?php
	}

	/**
	 * Add bulk forums role dropdown to the WordPress users table
	 *
	 * @since Showcase (1.0)
	 */
	public static function user_role_bulk_dropdown() {

		// Bail if current user cannot promote users 
		if ( !current_user_can( 'promote_users' ) )
			return;

		// Get the roles
		$dynamic_roles = dps_get_dynamic_roles();

		// Only keymasters can set other keymasters
		if ( ! dps_is_user_keymaster() )
			unset( $dynamic_roles[ dps_get_keymaster_role() ] ); ?>

		<label class="screen-reader-text" for="bbp-new-role"><?php _e( 'Change forum role to&hellip;', 'dps' ) ?></label>
		<select name="bbp-new-role" id="bbp-new-role" style="display:inline-block; float:none;">
			<option value=''><?php _e( 'Change forum role to&hellip;', 'dps' ) ?></option>
			<?php foreach ( $dynamic_roles as $role => $details ) : ?>
				<option value="<?php echo esc_attr( $role ); ?>"><?php echo translate_user_role( $details['name'] ); ?></option>
			<?php endforeach; ?>
		</select><?php submit_button( __( 'Change', 'dps' ), 'secondary', 'bbp-change-role', false );
	}

	/**
	 * Process bulk dropdown form submission from the WordPress Users
	 * Table
	 *
	 * @uses current_user_can() to check for 'promote users' capability
	 * @uses dps_get_dynamic_roles() to get forum roles
	 * @uses dps_get_user_role() to get a user's current forums role
	 * @uses dps_set_user_role() to set the user's new forums role
	 * @return bool Always false
	 */
	public function user_role_bulk_change() {

		// Bail if current user cannot promote users 
		if ( !current_user_can( 'promote_users' ) )
			return;

		// Bail if no users specified
		if ( empty( $_REQUEST['users'] ) )
			return;

		// Bail if this isn't a showcase action
		if ( empty( $_REQUEST['bbp-new-role'] ) || empty( $_REQUEST['bbp-change-role'] ) )
			return;

		// Check that the new role exists
		$dynamic_roles = dps_get_dynamic_roles();
		if ( empty( $dynamic_roles[ $_REQUEST['bbp-new-role'] ] ) )
			return;

		// Get the current user ID
		$current_user_id = (int) dps_get_current_user_id();

		// Run through user ids
		foreach ( (array) $_REQUEST['users'] as $user_id ) {
			$user_id = (int) $user_id;

			// Don't let a user change their own role
			if ( $user_id == $current_user_id ) 
				continue;

			// Set up user and role data
			$user_role = dps_get_user_role( $user_id );			
			$new_role  = sanitize_text_field( $_REQUEST['bbp-new-role'] );

			// Only keymasters can set other keymasters
			if ( in_array( dps_get_keymaster_role(), array( $user_role, $new_role ) ) && ! dps_is_user_keymaster() )
				continue;

			// Set the new forums role
			if ( $new_role != $user_role ) {
				dps_set_user_role( $user_id, $new_role );
			}
		}
	}

	/**
	 * Add Forum Role column to the WordPress Users table, and change the
	 * core role title to "Site Role"
	 *
	 * @since Showcase (1.0)
	 *
	 * @param array $columns Users table columns
	 * @return array $columns
	 */
	public static function user_role_column( $columns = array() ) {
		$columns['role']          = __( 'Site Role',  'showcase' );
		$columns['dps_user_role'] = __( 'Forum Role', 'dps' );

		return $columns;
	}

	/**
	 * Return user's forums role for display in the WordPress Users list table
	 *
	 * @since Showcase (1.0)
	 *
	 * @param string $retval
	 * @param string $column_name
	 * @param int $user_id
	 *
	 * @return string Displayable showcase user role
	 */
	public static function user_role_row( $retval = '', $column_name = '', $user_id = 0 ) {

		// Only looking for showcase's user role column
		if ( 'dps_user_role' == $column_name ) {

			// Get the users role
			$user_role = dps_get_user_role( $user_id );
			$retval    = false;

			// Translate user role for display
			if ( ! empty( $user_role ) ) {
				$roles  = dps_get_dynamic_roles();
				$retval = translate_user_role( $roles[$user_role]['name'] );
			}
		}

		// Pass retval through
		return $retval;
	}
}
new BB_Users_Admin();
endif; // class exists
