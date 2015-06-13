<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_tab = isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ? $_GET['tab'] : 'general';
?>
<div class="wrap">
	<h2 class="nav-tab-wrapper">
		<?php
		foreach ( $tabs as $tab_id => $tab_name ) {
			$tab_url = esc_url_raw( add_query_arg( array(
				'settings-updated' => false,
				'tab'              => $tab_id,
			) ) );

			$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

			echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">' .
					esc_html( $tab_name ) . '</a>';
		}
		?>
	</h2>
	<div id="tab-container">
		<form method="post" action="options.php">
			<?php
			settings_fields( 'elm_settings' );
			do_settings_sections( 'elm_settings_' . $active_tab );

			submit_button();
			?>
		</form>
	</div>
</div>