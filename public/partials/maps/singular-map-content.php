<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="elm google-maps" id="<?php echo esc_attr( $map_id ) ?>" style="height: <?php echo $map_height ? esc_attr( $map_height ) : '500' ?>px; width: <?php echo $map_width ? esc_attr( $map_width ) : '600' ?>px; padding: 0px; margin: 0px;" ></div>
