<?php

/**
 * Single Forum Content Part
 *
 * @package Showcase
 * @subpackage Theme
 */

?>

<div id="barebones-forums">

	<?php dps_breadcrumb(); ?>

	<?php do_action( 'dps_template_before_single_forum' ); ?>

	<?php if ( post_password_required() ) : ?>

		<?php dps_get_template_part( 'form', 'protected' ); ?>

	<?php else : ?>

		<?php dps_single_forum_description(); ?>

		<?php if ( dps_has_forums() ) : ?>

			<?php dps_get_template_part( 'loop', 'forums' ); ?>

		<?php endif; ?>

		<?php if ( !dps_is_forum_category() && dps_has_topics() ) : ?>

			<?php dps_get_template_part( 'pagination', 'topics'    ); ?>

			<?php dps_get_template_part( 'loop',       'topics'    ); ?>

			<?php dps_get_template_part( 'pagination', 'topics'    ); ?>

			<?php dps_get_template_part( 'form',       'topic'     ); ?>

		<?php elseif ( !dps_is_forum_category() ) : ?>

			<?php dps_get_template_part( 'feedback',   'no-topics' ); ?>

			<?php dps_get_template_part( 'form',       'topic'     ); ?>

		<?php endif; ?>

	<?php endif; ?>

	<?php do_action( 'dps_template_after_single_forum' ); ?>

</div>
