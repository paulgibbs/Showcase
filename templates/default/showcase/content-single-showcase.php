<?php

/**
 * Single Showcase Content Part
 *
 * @package Showcase
 * @subpackage Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div id="dps-showcase">

	<?php do_action( 'dps_template_before_single_showcase' ); ?>

	<?php if ( dps_has_showcases() ) : ?>

		<?php dps_get_template_part( 'loop-showcase' ); ?>

	<?php endif; ?>

	<?php do_action( 'dps_template_after_single_showcase' ); ?>

</div>
