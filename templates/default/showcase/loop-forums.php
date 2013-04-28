<?php

/**
 * Forums Loop
 *
 * @package Showcase
 * @subpackage Theme
 */

?>

<?php do_action( 'dps_template_before_forums_loop' ); ?>

<ul id="forums-list-<?php dps_forum_id(); ?>" class="dps-forums">

	<li class="dps-header">

		<ul class="forum-titles">
			<li class="dps-forum-info"><?php _e( 'Forum', 'dps' ); ?></li>
			<li class="dps-forum-topic-count"><?php _e( 'Topics', 'dps' ); ?></li>
			<li class="dps-forum-reply-count"><?php dps_show_lead_topic() ? _e( 'Replies', 'dps' ) : _e( 'Posts', 'dps' ); ?></li>
			<li class="dps-forum-freshness"><?php _e( 'Freshness', 'dps' ); ?></li>
		</ul>

	</li><!-- .dps-header -->

	<li class="dps-body">

		<?php while ( dps_forums() ) : dps_the_forum(); ?>

			<?php dps_get_template_part( 'loop', 'single-forum' ); ?>

		<?php endwhile; ?>

	</li><!-- .dps-body -->

	<li class="dps-footer">

		<div class="tr">
			<p class="td colspan4">&nbsp;</p>
		</div><!-- .tr -->

	</li><!-- .dps-footer -->

</ul><!-- .forums-directory -->

<?php do_action( 'dps_template_after_forums_loop' ); ?>
