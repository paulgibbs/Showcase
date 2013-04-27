<?php

/**
 * New/Edit Forum
 *
 * @package Showcase
 * @subpackage Theme
 */

?>

<?php if ( dps_is_forum_edit() ) : ?>

<div id="showcase-forums">

	<?php dps_breadcrumb(); ?>

	<?php dps_single_forum_description( array( 'forum_id' => dps_get_forum_id() ) ); ?>

<?php endif; ?>

<?php if ( dps_current_user_can_access_create_forum_form() ) : ?>

	<div id="new-forum-<?php dps_forum_id(); ?>" class="bbp-forum-form">

		<form id="new-post" name="new-post" method="post" action="<?php the_permalink(); ?>">

			<?php do_action( 'dps_theme_before_forum_form' ); ?>

			<fieldset class="bbp-form">
				<legend>

					<?php
						if ( dps_is_forum_edit() )
							printf( __( 'Now Editing &ldquo;%s&rdquo;', 'dps' ), dps_get_forum_title() );
						else
							dps_is_single_forum() ? printf( __( 'Create New Forum in &ldquo;%s&rdquo;', 'dps' ), dps_get_forum_title() ) : _e( 'Create New Forum', 'dps' );
					?>

				</legend>

				<?php do_action( 'dps_theme_before_forum_form_notices' ); ?>

				<?php if ( !dps_is_forum_edit() && dps_is_forum_closed() ) : ?>

					<div class="bbp-template-notice">
						<p><?php _e( 'This forum is closed to new content, however your account still allows you to do so.', 'dps' ); ?></p>
					</div>

				<?php endif; ?>

				<?php if ( current_user_can( 'unfiltered_html' ) ) : ?>

					<div class="bbp-template-notice">
						<p><?php _e( 'Your account has the ability to post unrestricted HTML content.', 'dps' ); ?></p>
					</div>

				<?php endif; ?>

				<?php do_action( 'dps_template_notices' ); ?>

				<div>

					<?php do_action( 'dps_theme_before_forum_form_title' ); ?>

					<p>
						<label for="dps_forum_title"><?php printf( __( 'Forum Name (Maximum Length: %d):', 'dps' ), dps_get_title_max_length() ); ?></label><br />
						<input type="text" id="dps_forum_title" value="<?php dps_form_forum_title(); ?>" tabindex="<?php dps_tab_index(); ?>" size="40" name="dps_forum_title" maxlength="<?php dps_title_max_length(); ?>" />
					</p>

					<?php do_action( 'dps_theme_after_forum_form_title' ); ?>

					<?php do_action( 'dps_theme_before_forum_form_content' ); ?>

					<?php if ( !function_exists( 'wp_editor' ) ) : ?>

						<p>
							<label for="dps_forum_content"><?php _e( 'Forum Description:', 'dps' ); ?></label><br />
							<textarea id="dps_forum_content" tabindex="<?php dps_tab_index(); ?>" name="dps_forum_content" cols="60" rows="10"><?php dps_form_forum_content(); ?></textarea>
						</p>

					<?php else : ?>

						<?php dps_the_content( array( 'context' => 'forum' ) ); ?>

					<?php endif; ?>

					<?php do_action( 'dps_theme_after_forum_form_content' ); ?>

					<?php if ( !current_user_can( 'unfiltered_html' ) ) : ?>

						<p class="form-allowed-tags">
							<label><?php _e( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes:','showcase' ); ?></label><br />
							<code><?php dps_allowed_tags(); ?></code>
						</p>

					<?php endif; ?>

					<?php do_action( 'dps_theme_before_forum_form_type' ); ?>

					<p>
						<label for="dps_forum_type"><?php _e( 'Forum Type:', 'dps' ); ?></label><br />
						<?php dps_form_forum_type_dropdown(); ?>
					</p>

					<?php do_action( 'dps_theme_after_forum_form_type' ); ?>

					<?php do_action( 'dps_theme_before_forum_form_status' ); ?>

					<p>
						<label for="dps_forum_status"><?php _e( 'Status:', 'dps' ); ?></label><br />
						<?php dps_form_forum_status_dropdown(); ?>
					</p>

					<?php do_action( 'dps_theme_after_forum_form_status' ); ?>

					<?php do_action( 'dps_theme_before_forum_form_status' ); ?>

					<p>
						<label for="dps_forum_visibility"><?php _e( 'Visibility:', 'dps' ); ?></label><br />
						<?php dps_form_forum_visibility_dropdown(); ?>
					</p>

					<?php do_action( 'dps_theme_after_forum_visibility_status' ); ?>

					<?php do_action( 'dps_theme_before_forum_form_parent' ); ?>

					<p>
						<label for="dps_forum_parent_id"><?php _e( 'Parent Forum:', 'dps' ); ?></label><br />

						<?php
							dps_dropdown( array(
								'select_id' => 'dps_forum_parent_id',
								'show_none' => __( '(No Parent)', 'dps' ),
								'selected'  => dps_get_form_forum_parent(),
								'exclude'   => dps_get_forum_id()
							) );
						?>
					</p>

					<?php do_action( 'dps_theme_after_forum_form_parent' ); ?>

					<?php do_action( 'dps_theme_before_forum_form_submit_wrapper' ); ?>

					<div class="bbp-submit-wrapper">

						<?php do_action( 'dps_theme_before_forum_form_submit_button' ); ?>

						<button type="submit" tabindex="<?php dps_tab_index(); ?>" id="dps_forum_submit" name="dps_forum_submit" class="button submit"><?php _e( 'Submit', 'dps' ); ?></button>

						<?php do_action( 'dps_theme_after_forum_form_submit_button' ); ?>

					</div>

					<?php do_action( 'dps_theme_after_forum_form_submit_wrapper' ); ?>

				</div>

				<?php dps_forum_form_fields(); ?>

			</fieldset>

			<?php do_action( 'dps_theme_after_forum_form' ); ?>

		</form>
	</div>

<?php elseif ( dps_is_forum_closed() ) : ?>

	<div id="no-forum-<?php dps_forum_id(); ?>" class="bbp-no-forum">
		<div class="bbp-template-notice">
			<p><?php printf( __( 'The forum &#8216;%s&#8217; is closed to new content.', 'dps' ), dps_get_forum_title() ); ?></p>
		</div>
	</div>

<?php else : ?>

	<div id="no-forum-<?php dps_forum_id(); ?>" class="bbp-no-forum">
		<div class="bbp-template-notice">
			<p><?php is_user_logged_in() ? _e( 'You cannot create new forums.', 'dps' ) : _e( 'You must be logged in to create new forums.', 'dps' ); ?></p>
		</div>
	</div>

<?php endif; ?>

<?php if ( dps_is_forum_edit() ) : ?>

</div>

<?php endif; ?>
