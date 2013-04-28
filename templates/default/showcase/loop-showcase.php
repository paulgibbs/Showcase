<?php

/**
 * Showcase Loop
 *
 * @package Showcase
 * @subpackage Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<?php do_action( 'dps_template_before_showcase_loop' ); ?>

<ul id="dps-showcase-list-<?php dps_showcase_id(); ?>" class="dps-showcase-list">

	<?php while ( dps_showcases() ) : dps_the_showcase(); ?>

		<?php dps_get_template_part( 'loop-single-showcase' ); ?>

	<?php endwhile; ?>

</ul><!-- .dps-showcase-list -->

<?php do_action( 'dps_template_after_showcase_loop' ); ?>
