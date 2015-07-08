<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @var int $map_height
 * @var string $map_id
 */
?>
<div class="map_container" style="height: <?php echo absint( $map_height ) ? absint( $map_height ) : '500' ?>px;">
	<div class="elm google-maps" id="<?php echo esc_attr( $map_id ) ?>" style="height: <?php echo absint( $map_height ) ? absint( $map_height ) : '500' ?>px; padding: 0px; margin: 0px;"></div>
</div>
