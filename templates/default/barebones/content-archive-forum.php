<?php

/**
 * Archive Forum Content Part
 *
 * @package Showcase
 * @subpackage Theme
 */

?>

<div id="showcase-forums">

	<div class="bbp-search-form">

		<?php dps_get_template_part( 'form', 'search' ); ?>

	</div>

	<?php dps_breadcrumb(); ?>

	<?php do_action( 'dps_template_before_forums_index' ); ?>

	<?php if ( dps_has_forums() ) : ?>

		<?php dps_get_template_part( 'loop',     'forums'    ); ?>

	<?php else : ?>

		<?php dps_get_template_part( 'feedback', 'no-forums' ); ?>

	<?php endif; ?>

	<?php do_action( 'dps_template_after_forums_index' ); ?>

</div>
