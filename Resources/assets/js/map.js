$(function () {
    var geocoder;
    var map;
    var marker;

    function initializeMap() {
        geocoder = new google.maps.Geocoder();
        initialPoint = $('[data-map-point]').val().split(',');
        var latlng = new google.maps.LatLng(initialPoint[0], initialPoint[1]);
        var mapOptions = {
            zoom: 14,
            center: latlng,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: false
        }

        map = new google.maps.Map($('[data-map]')[0], mapOptions);

        marker = new google.maps.Marker({
            map: map,
            position: latlng,
            draggable: true
        });

        google.maps.event.addListener(marker, 'drag', function () {
            $('[data-map-point]').val(marker.position.lat() + ',' + marker.position.lng());
        });

        google.maps.event.addListener(marker, 'dragend', function () {
            setTimeout(function () {
                map.panTo(marker.getPosition());
            }, 500);
        });
    }

    function geocodeAddress() {
        var address = $('[data-map-address]').val();

        if (!address) {
            return;
        }

        geocoder.geocode({'address': address}, function (results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                marker.setPosition(results[0].geometry.location);
                map.setCenter(results[0].geometry.location);
                $('[data-map-point]').val(results[0].geometry.location.lat() + ',' + results[0].geometry.location.lng());
            } else {
                alert('Geocode was not successful for the following reason: ' + status);
            }
        });
    }

    // ------
    // EVENTS
    // ------
    $('[data-map-search]').click(function (e) {
        geocodeAddress();
        e.preventDefault();
    });

    $('[data-map-address]').keydown(function (e) {
        if (e.keyCode == 13) {
            geocodeAddress();
            e.preventDefault();
        }
    });

    // ----
    // INIT
    // ----
    if (!!$('[data-map-point]').length) {
        initializeMap();
    }
});
