<?php

/**
 * Showcase loop - single showcase
 *
 * @package Showcase
 * @subpackage Theme
 */

?>

<li id="dps-showcase-<?php dps_showcase_id(); ?>" <?php dps_showcase_class(); ?>>

	<?php do_action( 'dps_template_before_showcase_content' ); ?>

	<div class="dps-forum-content"><?php dps_showcase_content(); ?></div>

	<?php do_action( 'dps_template_after_showcase_content' ); ?>

</li>