<?php

/**
 * Forums Loop
 *
 * @package Showcase
 * @subpackage Theme
 */

?>

<?php do_action( 'dps_template_before_forums_loop' ); ?>

<ul id="forums-list-<?php dps_forum_id(); ?>" class="bbp-forums">

	<li class="bbp-header">

		<ul class="forum-titles">
			<li class="bbp-forum-info"><?php _e( 'Forum', 'dps' ); ?></li>
			<li class="bbp-forum-topic-count"><?php _e( 'Topics', 'dps' ); ?></li>
			<li class="bbp-forum-reply-count"><?php dps_show_lead_topic() ? _e( 'Replies', 'dps' ) : _e( 'Posts', 'dps' ); ?></li>
			<li class="bbp-forum-freshness"><?php _e( 'Freshness', 'dps' ); ?></li>
		</ul>

	</li><!-- .bbp-header -->

	<li class="bbp-body">

		<?php while ( dps_forums() ) : dps_the_forum(); ?>

			<?php dps_get_template_part( 'loop', 'single-forum' ); ?>

		<?php endwhile; ?>

	</li><!-- .bbp-body -->

	<li class="bbp-footer">

		<div class="tr">
			<p class="td colspan4">&nbsp;</p>
		</div><!-- .tr -->

	</li><!-- .bbp-footer -->

</ul><!-- .forums-directory -->

<?php do_action( 'dps_template_after_forums_loop' ); ?>
