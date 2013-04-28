<?php

/**
 * Forums Loop - Single Forum
 *
 * @package Showcase
 * @subpackage Theme
 */

?>

<ul id="dps-forum-<?php dps_forum_id(); ?>" <?php dps_forum_class(); ?>>

	<li class="dps-forum-info">

		<?php do_action( 'dps_theme_before_forum_title' ); ?>

		<a class="dps-forum-title" href="<?php dps_forum_permalink(); ?>" title="<?php dps_forum_title(); ?>"><?php dps_forum_title(); ?></a>

		<?php do_action( 'dps_theme_after_forum_title' ); ?>

		<?php do_action( 'dps_theme_before_forum_description' ); ?>

		<div class="dps-forum-content"><?php the_content(); ?></div>

		<?php do_action( 'dps_theme_after_forum_description' ); ?>

		<?php do_action( 'dps_theme_before_forum_sub_forums' ); ?>

		<?php dps_list_forums(); ?>

		<?php do_action( 'dps_theme_after_forum_sub_forums' ); ?>

		<?php dps_forum_row_actions(); ?>

	</li>

	<li class="dps-forum-topic-count"><?php dps_forum_topic_count(); ?></li>

	<li class="dps-forum-reply-count"><?php dps_show_lead_topic() ? dps_forum_reply_count() : dps_forum_post_count(); ?></li>

	<li class="dps-forum-freshness">

		<?php do_action( 'dps_theme_before_forum_freshness_link' ); ?>

		<?php dps_forum_freshness_link(); ?>

		<?php do_action( 'dps_theme_after_forum_freshness_link' ); ?>

		<p class="dps-topic-meta">

			<?php do_action( 'dps_theme_before_topic_author' ); ?>

			<span class="dps-topic-freshness-author"><?php dps_author_link( array( 'post_id' => dps_get_forum_last_active_id(), 'size' => 14 ) ); ?></span>

			<?php do_action( 'dps_theme_after_topic_author' ); ?>

		</p>
	</li>

</ul><!-- #dps-forum-<?php dps_forum_id(); ?> -->
