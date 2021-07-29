jQuery(document).on('ready', function() {
    //jQuery('.cart-subtotal').after('<tr class="cart-shipping"><th>Shipping</th><td><button id="mapSelector"><span class="dashicons dashicons-location"></span> Select Location</button></td></tr>');

    // Creating map object
    var coordinates_center = custom_admin_url.woo_location_center;
    var commaPos = coordinates_center.indexOf(',');
    var coordinatesLat = parseFloat(coordinates_center.substring(0, commaPos));
    var coordinatesLong = parseFloat(coordinates_center.substring(commaPos + 1, coordinates_center.length));

    var map = new google.maps.Map(document.getElementById('map_canvas'), {
        zoom: 15,
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

jQuery(document).on('click', '#mapSelector', function(e) {
    e.preventDefault();

    jQuery('#mapsContainer').toggleClass('custom-metaboxes-hidden');
});

jQuery(document).on('click', '#select_coordinates', function(e) {
    e.preventDefault();

    var coordinates_center = custom_admin_url.woo_location_center;
    var commaPos = coordinates_center.indexOf(',');
    var coordinatesLat = parseFloat(coordinates_center.substring(0, commaPos));
    var coordinatesLong = parseFloat(coordinates_center.substring(commaPos + 1, coordinates_center.length));

    if (jQuery('#coordinates').val() == '') {
        jQuery('#coordinates').val(coordinatesLat + ',' + coordinatesLong);
    }

    dataString = 'action=calculate_shipping_price';
    dataString += '&coordinates=' + document.getElementById('coordinates').value;

    //var elements = document.getElementsByClassName('loader-css');
    //elements[0].innerHTML = '<div class="loader"><div>';
    /* SEND AJAX */
    newRequest = new XMLHttpRequest();
    newRequest.open('POST', custom_admin_url.ajax_url, true);
    newRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
    newRequest.onload = function() {

        //var elements = document.getElementsByClassName('loader-css');
        //elements[0].classList.toggle("d-none");

        var result = JSON.parse(newRequest.responseText);
        if (result.success == true) {
            console.log(result.data);
            
            jQuery('#woo-location_new_price').val(parseFloat(result.data.shipping_price));
            jQuery('#shipping_method_0_woo-location .amount').val(result.data.shipping_price);
            jQuery('#shipping_method .amount').html(result.data.shipping_price_html);
            jQuery('.order-total .amount').html(result.data.total_price_html);
            jQuery('#mapsContainer').toggleClass('custom-metaboxes-hidden');
            jQuery(document.body).trigger("update_checkout");
            jQuery( document.body ).trigger( 'wc_fragment_refresh' );
        } else {
            alert(result.data);
        }
    };
    newRequest.send(dataString);
});