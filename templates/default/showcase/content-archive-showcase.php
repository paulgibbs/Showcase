<?php

/**
 * Archive Showcase Content Part
 *
 * @package Showcase
 * @subpackage Theme
 */

?>

<div id="dps-showcase">

	<?php do_action( 'dps_template_before_showcase_index' ); ?>

	<?php if ( dps_has_showcase() ) : ?>

		<?php dps_get_template_part( 'loop-showcase' ); ?>

	<?php else : ?>

		<?php dps_get_template_part( 'feedback-no-showcase' ); ?>

	<?php endif; ?>

	<?php do_action( 'dps_template_after_showcase_index' ); ?>

</div>
