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

	<img class="dps-showcase-image" src="<?php dps_showcase_mshot( get_post_custom_values( 'showcase_url' ) ); ?>">
	<div class="dps-showcase-content"><?php dps_showcase_content(); ?></div>

</li>