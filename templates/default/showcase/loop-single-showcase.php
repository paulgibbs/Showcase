<?php

/**
 * Showcase loop - single showcase
 *
 * @package Showcase
 * @subpackage Theme
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<li id="dps-showcase-<?php dps_showcase_id(); ?>" <?php dps_showcase_class(); ?>>

	<div class="dps-showcase-meta">
		<p>Paul is cool</p>
	</div>

	<a href="<?php echo esc_url( dps_get_showcase_permalink() ); ?>" alt="<?php echo esc_attr( dps_get_showcase_title() ); ?>">
		<img class="dps-showcase-image" src="<?php dps_showcase_mshot(); ?>">
	</a>

	<div class="dps-showcase-content">
		<a href="<?php echo esc_url( dps_get_showcase_permalink() ); ?>" alt="<?php echo esc_attr( dps_get_showcase_title() ); ?>">
			<h2><?php dps_showcase_title(); ?></h2>
		</a>

		<?php dps_showcase_excerpt(); ?>
	</div>

</li>