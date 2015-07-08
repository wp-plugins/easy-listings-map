<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="<?php echo esc_url( $css_url ) . 'bootstrap' . $suffix . '.css' ?>">
<link rel="stylesheet" type="text/css" href="<?php echo esc_url( $css_url ) . 'elm-admin' . $suffix . '.css' ?>">
</head>
<body>
	<div class="elm-shortcode" id="elm-shortcode-container">
		<div class="elm-shortcode-body form-horizontal container-fluid">
			<div class="form-group">
				<div class="col-sm-10">
					<h2>
						<i class="icon-shortcode"></i>
						<span><?php _e( 'Google Maps Shortcode', 'elm' ) ?></span>
					</h2>
				</div>
			    <div class="col-sm-2">
			      <button type="submit" id="insert_shortcode" class="btn btn-success" style="float: right;"><?php _e('Insert', 'elm') ?></button>
			    </div>
			  </div>
			<div class="shortcode-row form-group">
				<label class="col-sm-4 control-label" for="title"><?php _e( 'Title', 'elm' ) ?></label>
				<div class="controls col-sm-8" data-tip="<?php _e( 'Title of the map', 'elm ' ) ?>">
					<input class="form-control" type="text" name="title" id="title">
				</div>
			</div>
			<div class="shortcode-row form-group">
				<label class="col-sm-4 control-label" for="property_types"><?php _e( 'Listing types that shown in the map', 'elm' ) ?></label>
				<div class="controls col-sm-8" data-tip="<?php _e( 'Listing types that shown in the map', 'elm' ) ?>">
					<select class="form-control" multiple name="property_types" id="property_types">
						<?php
						if ( count( $property_types ) ) {
							foreach ( $property_types as $property_type => $property_type_name ) {
								echo '<option value="' . esc_attr( $property_type ) . '" selected>' . __( $property_type_name, 'elm' ) . '</option>';
							}
						}
						?>
					</select>
				</div>
			</div>
			<div class="shortcode-row form-group">
				<label class="col-sm-4 control-label" for="property_status"><?php _e( 'Listing status', 'elm' ) ?></label>
				<div class="controls col-sm-8" data-tip="<?php _e( 'Status of listings', 'elm' ) ?>">
					<select class="form-control" multiple name="property_status" id="property_status">
						<?php
						if ( count( $property_status ) ) {
							foreach ( $property_status as $status => $status_name ) {
								$selected = in_array( $status, array( 'current', 'sold', 'leased' ) ) ? true : false;
								echo '<option value="' . esc_attr( $status ) . '"' . selected( $selected, true ) . '>' . __( $status_name, 'elm' ) . '</option>';
							}
						}
						?>
					</select>
				</div>
			</div>
			<div class="shortcode-row form-group">
				<label class="col-sm-4 control-label" for="limit"><?php _e( 'Number of listings in the map', 'elm' ) ?></label>
				<div class="controls col-sm-8" data-tip="<?php _e( 'Number of listings in the map, -1 for choosing all of listings', 'elm' ) ?>">
					<input class="form-control" type="text" value="-1" name="limit" id="limit"/>
				</div>
			</div>
			<div class="shortcode-row form-group">
				<label class="col-sm-4 control-label" for="order"><?php _e( 'Listings order', 'elm' ) ?></label>
				<div class="controls col-sm-8" data-tip="<?php _e( 'Choosing between new or old added listings', 'elm' ) ?>">
					<select class="form-control" name="order" id="order">
						<option value="DESC" selected="selected"><?php _e( 'DESC( Recent added )', 'elm' ) ?></option>
						<option value="ASC"><?php _e( 'ASC( First added )', 'elm' ) ?></option>
					</select>
				</div>
			</div>
			<div class="shortcode-row form-group">
				<label class="col-sm-4 control-label"><?php _e( 'Map display types', 'elm' ) ?></label>
				<div class="controls col-sm-8" data-tip="<?php _e( 'Display types of the map', 'elm' ) ?>">
					<label class="checkbox-inline">
						<input type="checkbox" class="map_types" checked="checked" name="map_types[0]" id="map_types[0]" value="ROADMAP">
						<?php _e( 'Roadmap', 'elm' ) ?>
					</label>
					<label class="checkbox-inline">
						<input type="checkbox" class="map_types" name="map_types[1]" id="map_types[1]" value="SATELLITE">
						<?php _e( 'Satellite', 'elm' ) ?>
					</label>
					<label class="checkbox-inline">
						<input type="checkbox" class="map_types" name="map_types[2]" id="map_types[2]" value="HYBRID">
						<?php _e( 'Hybrid', 'elm' ) ?>
					</label>
					<label class="checkbox-inline">
						<input type="checkbox" class="map_types" name="map_types[3]" id="map_types[3]" value="TERRAIN">
						<?php _e( 'Terrain', 'elm' ) ?>
					</label>
				</div>
			</div>
			<div class="shortcode-row form-group">
				<label class="col-sm-4 control-label" for="map_height"><?php _e( 'Map height', 'elm' ) ?></label>
				<div class="controls col-sm-8" data-tip="<?php _e( 'Map html element height', 'elm' ) ?>">
					<input class="form-control" type="text" value="500" name="map_height" id="map_height"/>
				</div>
			</div>
			<div class="shortcode-row form-group">
				<label class="col-sm-4 control-label" for="auto_zoom"><?php _e( 'Map Auto Zoom Feature', 'elm' ) ?></label>
				<div class="controls col-sm-8" data-tip="<?php _e( 'If enabled map will choose best zoom level for showing many of the listings in the map', 'elm' ) ?>">
					<select class="form-control" id="auto_zoom" name="auto_zoom">
						<option value="1" selected="selected"><?php _e( 'Enabled', 'elm' ) ?></option>
						<option value="0"><?php _e( 'Disabled', 'elm' ) ?></option>
					</select>
				</div>
			</div>
			<div class="shortcode-row form-group" id="zoom_level_control" style="display: none;">
				<label class="col-sm-4 control-label" for="map_zoom"><?php _e( 'Map zoom', 'elm' ) ?></label>
				<div class="controls col-sm-8" data-tip="<?php _e( 'Zoom level of the map when auto zoom disabled', 'elm' ) ?>">
					<select class="form-control" name="map_zoom" id="map_zoom">
						<option value="0">0</option>
						<option value="1" selected="selected">1</option>
						<option value="2">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
						<option value="6">6</option>
						<option value="7">7</option>
						<option value="8">8</option>
						<option value="9">9</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
						<option value="16">16</option>
						<option value="17">17</option>
						<option value="18">18</option>
					</select>
				</div>
			</div>
			<div class="shortcode-row form-group">
				<label class="col-sm-4 control-label" for="cluster_size"><?php _e( 'Cluster grid size', 'elm' ) ?></label>
				<div class="controls col-sm-8" data-tip="<?php _e( 'Grid size of the cluster', 'elm' ) ?>">
					<select class="form-control" id="cluster_size">
						<option value="-1"><?php _e( 'Default', 'elm' ) ?></option>
						<option value="40">40</option>
						<option value="50">50</option>
						<option value="70">70</option>
						<option value="80">80</option>
					</select>
				</div>
			</div>
			<div class="shortcode-row form-group">
				<label class="col-sm-4 control-label" for="default_latitude"><?php _e( 'Default latitude of the map', 'elm' ) ?></label>
				<div class="controls col-sm-8" data-tip="<?php _e( 'Default latitude of the map when there is not any listing in the map and map will show default coordinate', 'elm' ) ?>">
					<input class="form-control" type="text" value="39.911607" name="default_latitude" id="default_latitude">
				</div>
			</div>
			<div class="shortcode-row form-group">
				<label class="col-sm-4 control-label" for="default_longitude"><?php _e( 'Default longitude of the map', 'elm' ) ?></label>
				<div class="controls col-sm-8" data-tip="<?php _e( 'Default longitude of the map when there is not any listing in the map and map will show default coordinate', 'elm' ) ?>">
					<input class="form-control" type="text" value="-100.853613" name="default_longitude" id="default_longitude">
				</div>
			</div>
			<div class="shortcode-row form-group">
				<label class="col-sm-4 control-label" for="map_id"> <?php _e( 'Html element id of the map', 'elm' ) ?></label>
				<div class="controls col-sm-8" data-tip="<?php _e( 'Html element id of the map, when left empty it will produce id automatically based on time', 'elm' ) ?>">
					<input class="form-control" type="text" name="map_id" id="map_id">
				</div>
			</div>
		</div>
	</div>
	<script type="text/javascript" src="<?php echo esc_url( $includes_url ) ?>/js/jquery/jquery.js"></script>
	<script type="text/javascript" src="<?php echo esc_url( $includes_url ) ?>/js/tinymce/tiny_mce_popup.js"></script>
	<script type="text/javascript" src="<?php echo esc_url( $js_url ) . 'jquery-tiptip/jquery.tipTip' . $suffix . '.js'  ?>"></script>
	<script type="text/javascript" src="<?php echo esc_url( $js_url ) . 'editor/elm-google-maps-shortcode-content' . $suffix . '.js' ?>"></script>
</body>
</html>
