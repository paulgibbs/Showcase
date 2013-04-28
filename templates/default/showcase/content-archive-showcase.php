<?php

/**
 * Archive Showcase Content Part
 *
 * @package Showcase
 * @subpackage Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div id="dps-showcase">

	<?php do_action( 'dps_template_before_showcase_index' ); ?>

	<?php if ( dps_has_showcases() ) : ?>

		<?php dps_get_template_part( 'loop-showcase' ); ?>

	<?php else : ?>

		<?php dps_get_template_part( 'feedback-no-showcase' ); ?>

	<?php endif; ?>

	<?php do_action( 'dps_template_after_showcase_index' ); ?>

</div>
