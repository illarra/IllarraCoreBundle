
  var geocoder;
  var map;
  var marker;
  
  function initializeMap() {
      geocoder = new google.maps.Geocoder();
      initialPoint = $('[data-map-point]').val().split(',');
      var latlng = new google.maps.LatLng(initialPoint[0], initialPoint[1]);
      var mapOptions = {
          zoom: 8,
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
      
      google.maps.event.addListener(
          marker,
          'dragend',
          function() {
              $('[data-map-point]').val(marker.position.lat() + ',' + marker.position.lng());
          }
      );
      
      google.maps.event.addListener(map, 'center_changed', function() {
          // 2 seconds after the center of the map has changed, pan back to the marker.
          window.setTimeout(function() {
              map.panTo(marker.getPosition());
          }, 2000);
      });
  }

  function geocodeAddress() {
      var address = $('[data-map-address]').val();
      geocoder.geocode( { 'address': address }, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
              marker.setPosition(results[0].geometry.location);
              map.setCenter(results[0].geometry.location);
              $('[data-map-point]').val(results[0].geometry.location.lat() + ',' + results[0].geometry.location.lng());
          } else {
              alert('Geocode was not successful for the following reason: ' + status);
          }
      });
  }
  
  $(document).ready( function () {
      if (!!$('[data-map-point]').length) {
        initializeMap();
      }
  });
    
  $('[data-map-search]').click(function (event) {
      geocodeAddress();
      
      event.preventDefault();
  });
  
  