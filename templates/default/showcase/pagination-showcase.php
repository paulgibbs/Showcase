<?php

/**
 * Pagination for pages of topics (when viewing a forum)
 *
 * @package Showcase
 * @subpackage Theme
 */

?>

<?php do_action( 'dps_template_before_pagination_loop' ); ?>

<div class="dps-pagination">
	<div class="dps-pagination-count">

		<?php dps_forum_pagination_count(); ?>

	</div>

	<div class="dps-pagination-links">

		<?php dps_forum_pagination_links(); ?>

	</div>
</div>

<?php do_action( 'dps_template_after_pagination_loop' ); ?>
