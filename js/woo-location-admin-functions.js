jQuery(document).on('ready', function(e) {
    // Creating map object
    var map = new google.maps.Map(document.getElementById('map_canvas'), {
        zoom: 12,
        center: new google.maps.LatLng(19.4326077, -99.13320799999997),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    // creates a draggable marker to the given coords
    var vMarker = new google.maps.Marker({
        position: new google.maps.LatLng(19.4326077, -99.13320799999997),
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