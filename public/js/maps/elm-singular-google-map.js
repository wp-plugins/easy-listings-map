jQuery( function( $ ) {
    var map;

    function initialize_map() {
        var mapTypeIds = [];
        for ( i = 0, max = elm_singular_map.map_types.length; i < max; i++ ) {
            if ( 'ROADMAP' === elm_singular_map.map_types[i] ) {
                mapTypeIds.push( google.maps.MapTypeId.ROADMAP );
            } else if ( 'SATELLITE' ===  elm_singular_map.map_types[i] ) {
                mapTypeIds.push( google.maps.MapTypeId.SATELLITE );
            } else if ( 'HYBRID' === elm_singular_map.map_types[i] ) {
                mapTypeIds.push( google.maps.MapTypeId.HYBRID );
            } else if ( 'TERRAIN' === elm_singular_map.map_types[i] ) {
                mapTypeIds.push( google.maps.MapTypeId.TERRAIN );
            }
        }

        var latlng = new google.maps.LatLng( elm_singular_map.latitude, elm_singular_map.longitude );
        var map_options = {
            zoom: parseInt( elm_singular_map.zoom ),
            scrollwheel: false,
            center: latlng,
            mapTypeControl: true,
            mapTypeControlOptions: {
              style: google.maps.MapTypeControlStyle.DEFAULT,
              mapTypeIds: mapTypeIds
            },
            zoomControl: true,
            zoomControlOptions: {
              style: google.maps.ZoomControlStyle.LARGE
            }
        };
        map = new google.maps.Map( document.getElementById( elm_singular_map.map_id ), map_options);
        if ( elm_singular_map.latitude && elm_singular_map.longitude ) {
            var marker = new google.maps.Marker({
                position: latlng,
                map: map
            });
        }
    }
    google.maps.event.addDomListener(window, 'load', initialize_map);
});
