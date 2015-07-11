jQuery( function( $ ) {
    var map = infoBubble = markerClusterer = null;
    var get_markers = true;    // Flag for checking should map get markers on bound changes or not
    var markers = [];          // Array of markers in the map.

    /**
     * Initialize Google Maps for listings
     * @return void
     */
    function initializeListingMap() {
        var mapTypeIds = [];
        for ( i = 0, max = elm_google_maps.map_types.length; i < max; i++ ) {
            if ( 'ROADMAP' === elm_google_maps.map_types[i] ) {
                mapTypeIds.push( google.maps.MapTypeId.ROADMAP );
            } else if ( 'SATELLITE' ===  elm_google_maps.map_types[i] ) {
                mapTypeIds.push( google.maps.MapTypeId.SATELLITE );
            } else if ( 'HYBRID' === elm_google_maps.map_types[i] ) {
                mapTypeIds.push( google.maps.MapTypeId.HYBRID );
            } else if ( 'TERRAIN' === elm_google_maps.map_types[i] ) {
                mapTypeIds.push( google.maps.MapTypeId.TERRAIN );
            }
        }

        if ( ! mapTypeIds.length ) {
            mapTypeIds.push( google.maps.MapTypeId.ROADMAP );
        }

        var latlng = new google.maps.LatLng( elm_google_maps.default_latitude, elm_google_maps.default_longitude );
        var map_options = {
            zoom: parseInt( elm_google_maps.zoom ),
            center: latlng,
            mapTypeControl: true,
            scrollwheel: false,
            mapTypeControlOptions: {
              style: google.maps.MapTypeControlStyle.DEFAULT,
              mapTypeIds: mapTypeIds
            },
            zoomControl: true,
            zoomControlOptions: {
              style: google.maps.ZoomControlStyle.DEFAULT
            }
        };
        map = new google.maps.Map( document.getElementById( elm_google_maps.map_id ), map_options );

        infoBubble = generateInfoBubble();

        var properties = elm_google_maps.markers;
        properties = jQuery.parseJSON( properties );
        if ( properties.length ) {
            createMarkers( properties );
            // Adding map markers to clusters.
            addMarkersToCluster();
        }
        /**
         * Auto zoom enabled.
         * Auto zoom and fitBounds to showing all of markers as good as possible.
         */
        if ( '1' === elm_google_maps.auto_zoom && markers.length ) {
            var bounds = new google.maps.LatLngBounds();
            for ( i = 0, max = markers.length; i < max; i++ ) {
                bounds.extend( markers[i].getPosition() );
            }
            //center the map to the geometric center of all markers
            map.setCenter( bounds.getCenter() );
            map.fitBounds( bounds );

            // Don't get markers when auto zoom changes zoom level.
            get_markers = false;
            //remove one zoom level to ensure no marker is on the edge.
            map.setZoom( map.getZoom() - 1 >= 0 ? map.getZoom() - 1 : map.getZoom() );
            // set a minimum zoom
            // if you got only 1 marker or all markers are on the same address map will be zoomed too much.
            if ( map.getZoom() > 15 ) {
                // Don't get markers when auto zoom changes zoom level.
                get_markers = false;
                map.setZoom( 15 );
            }
        }
        // Auto zoom disabled.
        else if ( markers.length ) {
            // Setting map center to first marker position.
            map.setCenter( markers[0].position );
        }

        // Load bound markers if all of listings doesn't loads already.
        if ( '-1' !== elm_google_maps.limit ) {
            google.maps.event.addListener( map, 'dragend', getBoundMarkers );
            google.maps.event.addListener( map, 'zoom_changed', getBoundMarkers );
        }
    }

    if ( 'object' === typeof google && 'object' === typeof google.maps ) {
        google.maps.event.addDomListener( window, 'load', initializeListingMap );
        google.maps.event.addDomListener(window, 'resize', function() {
            var center = map.getCenter();
            google.maps.event.trigger(map, "resize");
            map.setCenter(center);
        });
    }

    /**
     * Creating infowindow for property.
     * @param  google.maps.Marker marker
     * @param  {} property object of properties that are in the same coordinates.
     * @return void
     */
    function getInfoWindow( marker, property ) {
        return function() {
            var infoBubblePosition = infoBubble.get( 'position' );
            // Generate content of infobubble if it isn't defined already or previous infoBubble marker is not same as current marker.
            if ( typeof infoBubblePosition === 'undefined' ||
                ( typeof infoBubblePosition !== 'undefined' && marker.position.lat() !== infoBubblePosition.lat() &&
                    marker.position.lng() !== infoBubblePosition.lng() ) ) {
                infoBubble.close();
                // Creating a new infoBubble in order to not over writing on previous infoBubble content.
                infoBubble = generateInfoBubble();
                var content = '';
                infoBubble.setCloseButtonStyle( 'margin-right', '8px' );
                if ( property.info.length > 1 ) {
                    // Generating content for each property( properties that are in same coordinates ) in info window.
                    for ( i = 0, max = property.info.length; i < max; i++ ) {
                        content = '<div class="property-infobubble-content">' +
                            '<a href="' + decodeURIComponent( property.info[i].url ) + '">' + property.info[i].image_url + '</a>' +
                            '<div class="title"><a class="infobubble-property-title" href="' + decodeURIComponent( property.info[i].url ) + '">' + property.info[i].title + '</a></div>' +
                            '<div class="property-type-status">' + property.info[i].property_type + ' - ' + property.info[i].property_status + '</div>' +
                            // '<div class="property-meta pricing">' +  property.info[i].price + '</div>' +
                            '<div class="property-feature-icons epl-clearfix">' + property.info[i].icons + '</div>' +
                            '</div>';
                        infoBubble.addTab( property.info[i].tab_title, content );
                    }
                    infoBubble.setCloseButtonStyle( 'margin-top', '12px' );
                } else {
                    content = '<div class="property-infobubble-content">' +
                        '<a href="' + decodeURIComponent( property.info[0].url ) + '">' + property.info[0].image_url + '</a>' +
                        '<div class="title"><a class="infobubble-property-title" href="' + decodeURIComponent( property.info[0].url ) + '">' + property.info[0].title + '</a></div>' +
                        '<div class="property-type-status">' + property.info[0].property_type + ' - ' + property.info[0].property_status + '</div>' +
                        // '<div class="property-meta pricing">' +  property.info[0].price + '</div>' +
                        '<div class="property-feature-icons epl-clearfix">' + property.info[0].icons + '</div>' +
                        '</div>';
                    infoBubble.setContent( content );
                    infoBubble.setCloseButtonStyle( 'margin-top', '8px' );
                }
            }
            if ( ! infoBubble.isOpen() ) {
                infoBubble.open( map, marker );
            }
        }
    }

    /**
     * Getting markers from server when bounds of map changes.
     *
     * @since 1.0.0
     * @return void
     */
    function getBoundMarkers() {
        /**
         * Checking should this function get bound markers or not.
         * Don't get markers when auto zoom changes zoom level.
         */
        if ( ! get_markers ) {
            get_markers = true;
            return;
        }
        // First, determine the map bounds
        var bounds = map.getBounds();
        // Then the points
        var swPoint = bounds.getSouthWest();
        var nePoint = bounds.getNorthEast();

        // Now, each individual coordinate
        var swLat = swPoint.lat();
        var swLng = swPoint.lng();
        var neLat = nePoint.lat();
        var neLng = nePoint.lng();

        jQuery.ajax({
            type: 'POST',
            url : elmPublicAjaxUrl,
            data : {
                'action'       : 'load_map_markers',
                'nonce'        : elm_google_maps.nonce,
                'southWestLat' : swLat,
                'southWestLng' : swLng,
                'northEastLat' : neLat,
                'northEastLng' : neLng,
                'post_type'    : elm_google_maps.post_type,
                'status'       : elm_google_maps.status,
                'order'        : elm_google_maps.order,
                'cluster_size' : elm_google_maps.cluster_size
            }
        })
        .done( function( response ) {
            response = jQuery.parseJSON( response );
            if ( 1 === response.success ) {
                // Removing old markers.
                removeMarkers();
                if ( response.markers.length ) {
                    // Creating markers.
                    createMarkers( response.markers );
                    // Adding map markers to clusters.
                    addMarkersToCluster();
                }
            } else if ( 0 === response.success ) {
                console.log( response.message );
            }
        });
    }

    /**
     * Removing markers from map.
     *
     * @since 1.0.0
     * @return void
     */
    function removeMarkers() {
        if ( markerClusterer ) {
            markerClusterer.clearMarkers();
        }
        // Resetting markers of map.
        markers = [];
    }

    /**
     * Creating markers for properties.
     *
     * @since 1.0.0
     * @param  [] properties
     * @return void
     */
    function createMarkers( properties ) {
        if ( properties.length ) {
            var marker;
            for ( i = 0, max = properties.length; i < max; i++ ) {
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng( properties[i].latitude, properties[i].longitude ),
                    icon: properties[i]['marker_icon']
                });
                markers.push( marker );
                google.maps.event.addListener( marker, 'click', getInfoWindow( marker, properties[i] ) );
            }
        }
    }

    /**
     * Creating clusters for markers of map.
     *
     * @since 1.0.0
     */
    function addMarkersToCluster() {
        if ( markers.length ) {
            var gridSize = elm_google_maps.cluster_size == -1 ? null : parseInt( elm_google_maps.cluster_size, 10 );
            markerClusterer = new MarkerClusterer(map, markers, {
                ignoreHidden:true,
                maxZoom: 14,
                gridSize: gridSize,
                styles: elm_google_maps.cluster_style
            });

            google.maps.event.addListener(markerClusterer, 'click', function(clusterer) {
                console.log( clusterer.getMarkers() );
            });
        }
    }

    /**
     * Generating an info bubble object type.
     *
     * @since 1.0.0
     * @return InfoBubble
     */
    function generateInfoBubble() {
        return new InfoBubble({
                    maxWidth: 300,
                    maxHeight: 300,
                    closeSrc: elm_google_maps.info_window_close
                });
    }

    /**
     * Styling close button of InfoBubble.
     *
     * @since 1.0.0
     * @param string key
     * @param string value
     */
    InfoBubble.prototype.setCloseButtonStyle = function( key, value ) {
        this.close_.style[ key ] = value;
    };

});
