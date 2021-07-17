jQuery(document).on('ready', function(e) {
    console.log(custom_admin_url.woo_location_center);
    // Creating map object
    var coordinates_center = custom_admin_url.woo_location_center;
    var commaPos = coordinates_center.indexOf(',');
    var coordinatesLat = parseFloat(coordinates_center.substring(0, commaPos));
    var coordinatesLong = parseFloat(coordinates_center.substring(commaPos + 1, coordinates_center.length));

    console.log(coordinatesLat);
    console.log(coordinatesLong);
    
    var map = new google.maps.Map(document.getElementById('map_canvas'), {
        zoom: 12,
        center: new google.maps.LatLng(coordinatesLat, coordinatesLong),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    // creates a draggable marker to the given coords
    var vMarker = new google.maps.Marker({
        position: new google.maps.LatLng(coordinatesLat, coordinatesLong),
        draggable: true
    });

    // adds a listener to the marker
    // gets the coords when drag event ends
    // then updates the input with the new coords
    google.maps.event.addListener(vMarker, 'dragend', function(evt) {
        jQuery('#coordinates').val(evt.latLng.lat().toFixed(6) + ',' + evt.latLng.lng().toFixed(6));
        map.panTo(evt.latLng);
    });

    // centers the map on markers coords
    map.setCenter(vMarker.position);

    // adds the marker on the map
    vMarker.setMap(map);
});

jQuery('#mapSelector').on('click', function(e) {
    e.preventDefault();

    jQuery('#mapsContainer').toggleClass('custom-metaboxes-hidden');
});