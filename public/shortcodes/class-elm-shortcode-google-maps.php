<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Google maps shortcode of the plugin.
 *
 * @package    Easy_Listings_Map
 * @subpackage Easy_Listings_Map/public/shortcodes
 * @author     Taher Atashbar <taher.atashbar@gmail.com>
 */

class ELM_Shortcode_Google_Maps extends ELM_Public_Controller {

	private $data = array();
	private $attributes = array();
	private $content;
	private $plugin_public;
	private $elm_properties;

	public function __construct( Easy_Listings_Map_Public $plugin_public, ELM_Properties $elm_properties ) {
		$this->plugin_public  = $plugin_public;
		$this->elm_properties = $elm_properties;

		// Registering actions for loading markers in the map in ajax request.
		$plugin_public->get_loader()->add_action( 'wp_ajax_load_map_markers', $this, 'load_map_markers' );
		$plugin_public->get_loader()->add_action( 'wp_ajax_nopriv_load_map_markers', $this, 'load_map_markers' );
	}

	/**
	 * Outputting content of maps shortcode.
	 *
	 * @since   1.0.0
	 * @param   $atts array of shortcode attributes.
	 * @return  string content of shortcode
	 */
	public function output( $atts, $content = '' ) {
		$this->content  = $content;
		$property_types = epl_get_active_post_types();
		if ( ! empty( $property_types ) ) {
			$property_types = array_keys( $property_types );
		}
		// @todo adding transient support for shortcode.
		$this->attributes = shortcode_atts(
			array(
				'post_type'         => $property_types,
				'status'			=> array( 'current', 'sold', 'leased' ),
				'page_properties'   => false, // Show only properties of current page in the map
				'clustering'        => true, // Showing clusters in map.
				'limit'				=> -1,	 // Show all of posts.
				'orderby'           => 'date',
				'order'             => 'DESC',
				'location'			=> '',	// Location slug. Should be a name like sorrento
				'default_latitude'  => '39.911607',
				'default_longitude' => '-100.853613',
				'zoom'              => 1,
				'map_id'            => '',
				'output_map_div'    => true, // if == false, map will output to a div that already specified, so map_id should be sent to shortcode.
				'map_style_height'  => '500',
				'cluster_size'		=> -1,
				'map_types'			=> array( 'ROADMAP' ),
				'auto_zoom'			=> 1,
			), $atts
		);

		// Changing post_type attribute to array
		if ( is_string( $this->attributes['post_type'] ) && trim( $this->attributes['post_type'] ) ) {
			$this->attributes['post_type'] = explode( ',', $this->attributes['post_type'] );
			array_map( 'trim', $this->attributes['post_type'] );
		}

		// If post_type is not array or has not any element return.
		if ( ! is_array( $this->attributes['post_type'] ) || ! count( $this->attributes['post_type'] ) ) {
			return '';
		}

		// Changing status attribute to array
		if ( ! empty( $this->attributes['status'] ) && is_string( $this->attributes['status'] ) ) {
			$this->attributes['status'] = explode( ',', $this->attributes['status'] );
			array_map( 'trim', $this->attributes['status'] );
		}

		// If status is not array or has not any element return.
		if ( ! is_array( $this->attributes['status'] ) || ! count( $this->attributes['status'] ) ) {
			return '';
		}

		// Changing location attribute to array.
		if ( ! empty( $this->attributes['location'] ) && ! is_array( $this->attributes['location'] ) ) {
			$this->attributes['location'] = array_map( 'trim', explode( ',', $this->attributes['location'] ) );
		}

		// Changing map_types to array
		if ( is_string( $this->attributes['map_types'] ) && strlen( trim( $this->attributes['map_types'] ) ) ) {
			$this->attributes['map_types'] = explode( ',', $this->attributes['map_types'] );
			array_map( 'trim', $this->attributes['map_types'] );
		} else if ( ! is_array( $this->attributes['map_types'] ) ) {
			$this->attributes['map_types'] = array( 'ROADMAP' );
		}

		$this->data = array(
			'nonce'				=> wp_create_nonce( 'elm_bound_markers' ),
			'post_type'			=> $this->attributes['post_type'],
			'status'			=> $this->attributes['status'],
			'order'				=> $this->attributes['order'],
			'limit'				=> $this->attributes['limit'],
			'default_latitude'  => $this->attributes['default_latitude'],
			'default_longitude' => $this->attributes['default_longitude'],
			'auto_zoom'			=> $this->attributes['auto_zoom'],
			'map_id'            => trim( $this->attributes['map_id'] ) ? trim( $this->attributes['map_id'] ) : 'elm_google_maps_' . current_time( 'timestamp' ),
			'map_types'			=> $this->attributes['map_types'],
			'zoom'              => (int) $this->attributes['zoom'],
			'cluster_size'		=> (int) $this->attributes['cluster_size'],
			'info_window_close' => $this->plugin_public->get_images_folder() . 'map/info-window-close-button.png',
			'cluster_style'     => array(
				(object) array(
					'url'       => $this->plugin_public->get_images_folder() . 'map/m1.png',
					'height'    => 53,
					'width'     => 53,
				),
				(object) array(
					'url'       => $this->plugin_public->get_images_folder() . 'map/m2.png',
					'height'    => 56,
					'width'     => 56,
				),
				(object) array(
					'url'       => $this->plugin_public->get_images_folder() . 'map/m3.png',
					'height'    => 66,
					'width'     => 66,
				),
				(object) array(
					'url'       => $this->plugin_public->get_images_folder() . 'map/m4.png',
					'height'    => 78,
					'width'     => 78,
				),
				(object) array(
					'url'       => $this->plugin_public->get_images_folder() . 'map/m5.png',
					'height'    => 90,
					'width'     => 90,
				),
			),
		);
		if ( $this->attributes['page_properties'] ) {
			//add_action( 'epl_property_loop_start', array( $this, 'current_page_properties' ), 5 );
			//$this->current_page_properties_map();
		}
		return $this->create_map();
	}

	/**
	 * Creating map based on post type and conditions passed to shortcode.
	 *
	 * @since   1.0.0
	 * @return  string
	 */
	protected function create_map() {
		$args = array(
			'post_type'      => $this->attributes['post_type'],
			'posts_per_page' => (int) $this->attributes['limit'],
			'orderby'        => 'date',
			'order'          => $this->attributes['order'],
			'meta_query'     => array(
				array(
					'key'     => 'property_status',
					'value'   => $this->attributes['status'],
					'compare' => 'IN',
				),
			),
		);
		// Adding locations to query.
		if ( ! empty( $this->attributes['location'] ) ) {
			$args['tax_query'][] = array(
				'taxonomy' => 'location',
				'field'    => 'slug',
				'terms'    => $this->attributes['location'],
			);
		}

		$properties = new WP_Query( $args );
		$markers = array();
		if ( $properties->have_posts() ) {
			while ( $properties->have_posts() ) {
				$properties->the_post();
				// Adding property marker with it's information to markers array.
				$this->set_property_marker( $markers );
			}
			wp_reset_postdata();
		}
		return $this->draw_map( $markers );
	}

	/**
	 * Drawing a map in front-end based on markers sent to it.
	 *
	 * @since   1.0.0
	 * @param   $markers
	 * @return  string
	 */
	protected function draw_map( $markers ) {
		// Merging markers that are in same coordinates.
		$markers = $this->merge_markers( $markers );
		$this->data['markers'] = json_encode( $markers );
		// Registering scripts for Google maps.
		$this->register_scripts();
		// Registerring styles for Google maps.
		$this->register_styles();
		/*
		 * if $output_map_div == 0 and $map_id specified already don't output map div. In other words developer wants
		 * to output map to else where by specifying map output_div and it's id.
		 */
		if ( $this->attributes['output_map_div'] || ! trim( $this->attributes['map_id'] ) ) {
			ob_start();
			$this->render_view( 'shortcodes.google-maps.default',
				array(
					'content' => trim( $this->content ),
					'id'      => $this->data['map_id'],
					'height'  => $this->attributes['map_style_height'],
				)
			);
			return ob_get_clean();
		}
	}

	/**
	 * Adding property coordinates ( latitude and longitude ) and other information about property to markers.
	 *
	 * @since   1.0.0
	 * @param   array $markers
	 */
	protected function set_property_marker( array & $markers ) {
		$property_coordinates = $this->elm_properties->get_property_coordinates( get_the_ID() );
		if ( count( $property_coordinates ) ) {
			// Getting extra info about property, like it's image and etc.
			$image_url = '<img src="' . $this->plugin_public->get_images_folder() .
				'map/default-infowindow-image.png" style="width: 300px; height: 200px;" class="elm-infobubble-image" alt="' . trim( get_the_title() ) . '" />';
			if ( has_post_thumbnail() ) {
				$image_url = get_the_post_thumbnail( get_the_ID(), 'epl-image-medium-crop', array( 'class' => 'elm-infobubble-image' ) );
			}

			/**
			 * Setting property marker icon.
			 * Using marker that set in settings or use default marker for it.
			 */
			$property_status = get_post_meta( get_the_ID(), 'property_status', true );
			$marker_icon = $this->elm_properties->get_property_marker( get_post_type(), $property_status );

			$markers[] = array(
				'latitude'        => $property_coordinates['latitude'],
				'longitude'       => $property_coordinates['longitude'],
				'image_url'       => $image_url,
				'url'             => esc_url( get_permalink() ),
				'title'           => wp_trim_words( get_the_title(), 6 ),
				'tab_title'		  => wp_trim_words( get_the_title(), 2 ),
				'icons'           => $this->elm_properties->get_property_icons(),
				// 'price'           => epl_get_property_price(),
				'marker_icon'     => esc_url( $marker_icon ),
				'property_status' => $property_status,
				'property_type'   => get_post_type(),
			);
		}
	}

	/**
	 * Creating a map based on properties in the current page.
	 */
	protected function current_page_properties_map() {
		$queried_object = get_queried_object();
		if ( $queried_object->have_posts() ) {
			$markers = array();
			while ( $queried_object->have_posts() ) {
				$queried_object->the_post();
				$this->set_property_marker( $markers );
			}
			wp_reset_postdata();

			if ( count( $markers ) ) {
				$this->draw_map( $markers );
			}
		}
	}

	/**
	 * Merging markers if they are in same coordinates.
	 *
	 * @since   1.0.0
	 * @param   array $markers
	 * @return  array
	 */
	protected function merge_markers( array $markers ) {
		$merged_markers = array();
		if ( count( $markers ) ) {
			// Getting multiple marker icon.
			$elm_settings = ELM_IOC::make( 'settings' )->get_settings();
			$multiple_marker = ! empty( $elm_settings['map_multiple_marker'] ) ? trim( $elm_settings['map_multiple_marker'] ) :
				ELM_IOC::make( 'asset_manager' )->get_admin_images() . 'markers/multiple.png';

			for ( $i = 0; $i < count( $markers ); $i++ ) {
				// Did merged current marker already so don't use it again.
				if ( isset( $markers[ $i ]['merged'] ) ) {
					continue;
				}
				$merged_marker = array(
					'latitude'    => $markers[ $i ]['latitude'],
					'longitude'   => $markers[ $i ]['longitude'],
					'marker_icon' => $markers[ $i ]['marker_icon'],
					'info'        => array(
						$markers[ $i ],
					),
				);

				for ( $j = 0; $j < count( $markers ); $j++ ) {
					if ( $i == $j ) {
						continue;
					}
					if ( $markers[ $i ]['latitude'] == $markers[ $j ]['latitude'] && $markers[ $i ]['longitude'] == $markers[ $j ]['longitude'] ) {
						// Merging details of markers that are in same coordinates.
						$merged_marker['info'][] = $markers[ $j ];
						// Setting marker icon to multiple property icon.
						$merged_marker['marker_icon'] = esc_url( $multiple_marker );
						// Marker that is in position j are merged so don't use it again.
						$markers[ $j ]['merged'] = true;
					}
				}
				$merged_markers[] = $merged_marker;
			}
		}
		return $merged_markers;
	}

	/**
	 * Registering scripts for shortcode.
	 *
	 * @since   1.0.0
	 */
	protected function register_scripts() {
		$protocol = is_ssl() ? 'https' : 'http';
		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		// Register the script first.
		wp_enqueue_script( 'elm_google_maps', $this->plugin_public->get_js_folder() . 'maps/elm-google-maps' . $suffix . '.js',
			array( 'jquery' ), $this->plugin_public->get_version(), true );
		if ( count( $this->data ) ) {
			wp_localize_script( 'elm_google_maps', 'elm_google_maps', $this->data );
		}
		wp_enqueue_script( 'google-map-v-3', $protocol . '://maps.googleapis.com/maps/api/js?v=3.exp' );
		wp_enqueue_script( 'google-maps-clusters', $this->plugin_public->get_js_folder() . 'maps/markerclusterer' . $suffix . '.js',
			array(), $this->plugin_public->get_version(), true );
		wp_enqueue_script( 'google-maps-infobubble', $this->plugin_public->get_js_folder() . 'maps/infobubble' . $suffix . '.js',
			array(), $this->plugin_public->get_version(), true );
	}

	/**
	 * Registering styles for shortcode.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	protected function register_styles() {
		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		wp_enqueue_style( 'elm-google-maps', $this->plugin_public->get_css_folder() . 'elm-google-maps' . $suffix . '.css', array(),
			$this->plugin_public->get_version() );
	}

	/**
	 * Ajax Loading markers that are in map bounds.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function load_map_markers() {
		// Checking nonce.
		if ( ! wp_verify_nonce( $_POST['nonce'], 'elm_bound_markers' ) ) {
			die( json_encode( array( 'success' => 0, 'message' => 'Security check!' ) ) );
		}

		$south_west_lat = (float) $_POST['southWestLat'];
		$south_west_lng = (float) $_POST['southWestLng'];
		$north_east_lat = (float) $_POST['northEastLat'];
		$north_east_lng = (float) $_POST['northEastLng'];
		$post_type      = $_POST['post_type'];
		$status         = $_POST['status'];
		$cluster_size   = (int) $_POST['cluster_size'];
		$order          = in_array( strtoupper( $_POST['order'] ), array( 'DESC', 'ASC' ) ) ? strtoupper( $_POST['order'] ) : 'DESC';
		// Markers that should be returned.
		$markers = array();
		if ( count( $post_type ) && count( $status ) && ! is_nan( $south_west_lat ) && ! is_nan( $south_west_lng )
			&& ! is_nan( $north_east_lat ) && ! is_nan( $north_east_lng ) ) {
			$properties = new WP_Query( array(
				'post_type'      => $post_type,
				'orderby'        => 'date',
				'order'          => $order,
				'meta_query'     => array(
					array(
						'key'     => 'property_status',
						'value'   => $status,
						'compare' => 'IN',
					),
				),
			) );

			if ( $properties->have_posts() ) {
				// Getting ELM_Location class.
				$elm_location = ELM_IOC::make( 'location' );
				while ( $properties->have_posts() ) {
					$properties->the_post();
					$property_coordinates = $this->elm_properties->get_property_coordinates( get_the_ID() );
					if ( count( $property_coordinates ) ) {
						// Is property in bounds of map.
						if ( $elm_location->is_in_bound( $property_coordinates['latitude'], $property_coordinates['longitude'],
								$south_west_lat, $south_west_lng, $north_east_lat, $north_east_lng ) ) {
							// Adding property marker to markers.
							$this->set_property_marker( $markers );
						}
					}
				}
				wp_reset_postdata();

				// Merging markers that are in same coordinates.
				if ( count( $markers ) ) {
					$markers = $this->merge_markers( $markers );
				}
			}
		}

		die( json_encode( array( 'success' => 1, 'markers' => $markers ) ) );
	}
}
