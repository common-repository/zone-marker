// jQuery noCnflict wrapper:
( function($) {
	//"use strict";
	// declare vars
	var gilZmLat, gilZmLng, gilZmZoomLevel, gilZmMarkers,  gilZmPath, gilZmMap, gilZmPoly, infoWindow;
	$(document).ready( function(){
		if( $( '#gil-zm-google-map' ).length ) {
			gilZmInit();
			$( '#gilZmFindLocation' ).on( 'click', function() {
				gilZmGoToLocation();
				$( '#gilZmSiteName' ).focus();
				return false;
			} );
			$( '#gilZmReset' ).on( 'click', function(){ gilZmResetForm(); return false;} );
			//Call goToLocation if enter/return is pressed
			$( '#gilZmAddress' ).on( 'keypress', function( event ) {
				if( event.which == 13){
					event.preventDefault();
					gilZmGoToLocation();
				}
			});
			$( '#gilZmMyLocation' ).on( 'click', function(){
				gilZmMyLocation();
				return false;
			});
		}
	});/*(document).ready(function()*/



	/**
	* Set the map
	*/
	function gilZmGoogleMap() {
		var gilZmMap_options = {
			mapTypeId: google.maps.MapTypeId.HYBRID,
			mapTypeControl: true,
			navigationControlOptions: {
				style: google.maps.NavigationControlStyle.SMALL
			},
			center: new google.maps.LatLng( gilZmLat, gilZmLng ),
			zoom: gilZmZoomLevel,
			draggableCursor : 'crosshair'
		}
		gilZmMap = new google.maps.Map( document.getElementById( 'gil-zm-google-map' ), gilZmMap_options );
		var gilZmPolyOptions = {
			strokeWeight: 4,
			strokeColor: '#66ff00',
			strokeOpacity: 1,
			fillColor: '#66ff00',
			fillOpacity: 0.3
		}
		gilZmPoly = new google.maps.Polygon( gilZmPolyOptions );
		gilZmPoly.setMap( gilZmMap );
		gilZmPoly.setPaths( new google.maps.MVCArray( [ gilZmPath ] ) );
		google.maps.event.addListener( gilZmMap, 'click', gilZmAddLocation );
		google.maps.event.addListener( gilZmMap, 'zoom_changed', gilZmGetMarkers );
		google.maps.event.addListener( gilZmMap, 'drag', gilZmGetMarkers );
		infoWindow = new google.maps.InfoWindow;
	}



	function gilZmMyLocation(){
		// Try HTML5 geolocation.
		if ( navigator.geolocation ) {
			navigator.geolocation.getCurrentPosition( function( position ) {
				var pos = {
					lat: position.coords.latitude,
					lng: position.coords.longitude
				};
				infoWindow.setPosition( pos );
				infoWindow.setContent( 'Location found.' );
				infoWindow.open( gilZmMap );
				gilZmMap.setCenter( pos );
			}, function() {
				handleLocationError( true, infoWindow, gilZmMap.getCenter() );
			} );
		} else {
			// Browser doesn't support Geolocation
			handleLocationError( false, infoWindow, gilZmMap.getCenter() );
		}
	}



	/**
	* Handle location errors
	*/
	function handleLocationError( browserHasGeolocation, infoWindow, pos ) {
		infoWindow.setPosition( pos );
		infoWindow.setContent( browserHasGeolocation ? 'Error: The Geolocation service failed.' : 'Error: Your browser doesn\'t support geolocation.' );
		infoWindow.open( gilZmMap );
	}



	/**
	* Add the location markers
	*/
	function gilZmAddLocation( event ){
		gilZmPath.insertAt( gilZmPath.length, event.latLng );
		var gilZmImage = new google.maps.MarkerImage( gilZmLocalized.googleMarker,
			new google.maps.Size( 16, 16 ),
			new google.maps.Point( 0, 0 ),
			new google.maps.Point( 8, 8 )
		);
		var gilZmMarker = new google.maps.Marker( {
			position: event.latLng,
			map: gilZmMap,
			draggable: true,
			icon: gilZmImage
		} );
		gilZmMarkers.push( gilZmMarker );
		gilZmGetMarkers();
		google.maps.event.addListener( gilZmMarker, 'click', function() {
			gilZmMarker.setMap( null );
			for( var i = 0, I = gilZmMarkers.length; i < I && gilZmMarkers[i] != gilZmMarker; ++i );
				gilZmMarkers.splice( i, 1 );
				let removed = gilZmPath.removeAt( i );
				gilZmGetMarkers();
		} );
		google.maps.event.addListener( gilZmMarker, 'dragend', function() {
			for( var i = 0, I = gilZmMarkers.length; i < I && gilZmMarkers[i] != gilZmMarker; ++i );
			gilZmPath.setAt( i, gilZmMarker.getPosition() );
			gilZmGetMarkers();
		});
	}



	/**
	* Go to the submitted location
	*/
	function gilZmGoToLocation() {
		var gilZmAddress  = $( '#gilZmAddress' ).val();
		var gilZmGeocoder = new google.maps.Geocoder();
		if( gilZmGeocoder ){
			gilZmGeocoder.geocode( { 'address' : gilZmAddress }, function( results, status ){
				if( google.maps.GeocoderStatus.OK == status ) {
					gilZmLat       = results[0].geometry.location.jb;
					gilZmLng       = results[0].geometry.location.kb;
					gilZmZoomLevel = 17;
					gilZmMap.setCenter( results[0].geometry.location );
					gilZmMap.setZoom( gilZmZoomLevel );
					//map.setMarker(results[0].geometry.location);
				}
				else{
					if( gilZmAddress == '' ) gilZmAddress = '[EMPTY] ';
					alert( gilZmAddress + ' cannot be found.\nPlease check the location details\nand try again.' );
				}
			});
		}
	}



	/**
	* Get the area markers from the form
	*/
	function gilZmGetMarkers(){
		var gilZmMarker    = '',
				gilZmZoomLevel = gilZmMap.getZoom(),
				gilZmCentre    = gilZmMap.getCenter().toUrlValue(),
				gilZmPolygon   = [];
		if ( gilZmMarkers.length > 0 ){
			for( var i = 0; i < gilZmMarkers.length; i++ ){
				gilZmMarker += gilZmMarkers[i].position.lat().toPrecision(9) + ',' + gilZmMarkers[i].position.lng().toPrecision(9) + '|';
			}
			//if ( gilZmMarkers.length > 2 ){
				gilZmGetGeometry( gilZmPoly, gilZmMarkers.length );
			//}
			gilZmMarker = gilZmMarker.substr( 0, gilZmMarker.length - 1 );
			$( '#gilZmGoogleMarkers' ).val( gilZmMarker );
			gilZmMarker += '|' + gilZmMarkers[0].position.lat().toPrecision(9) + ',' + gilZmMarkers[0].position.lng().toPrecision(9);
			// get the width of the map
			var gilZmWidth = document.getElementById('gil-zm-google-map').offsetWidth;
			$( '#gilZmGoogleMapUrl' ).val( 'https://maps.googleapis.com/maps/api/staticmap?center=' + gilZmCentre + '&path=color:0x00FF00FF|fillcolor:0x00FF0033|weight:3|' + gilZmMarker + '&size=' + gilZmWidth + 'x500&zoom=' + gilZmZoomLevel + '&sensor=false&maptype=hybrid&key=' + gilZmLocalized.googleKey );
			$( '#gilZmHref' ).val( 'https://www.google.com/maps/@' + gilZmCentre + ',' +  + gilZmZoomLevel + 'z' );
		}
	}



	function gilZmGetGeometry( gilZmArea, markerLen ){
		if( markerLen > 2 ){
			let area = google.maps.geometry.spherical.computeArea(gilZmArea.getPath());
			let distance = google.maps.geometry.spherical.computeLength(gilZmArea.getPath());
			document.getElementById('gil-zm-google-map-zone-area').innerHTML = 'Area: ' + area.toFixed(0) + 'm<sup>2</sup>'; // - Perimeter: ' + distance.toFixed(2) + 'm';
			document.getElementById('gilZmGoogleMapZoneArea').value = area.toFixed(0);
		}
		else{
			document.getElementById('gil-zm-google-map-zone-area').innerHTML = '';
			document.getElementById('gilZmGoogleMapZoneArea').value = '';
			//document.getElementById('gilZmSubmit').disabled = true;
		}
	}



	/**
	* Reset the form
	*/
	function gilZmResetForm(){
		gilZmInit();
		$( '#gilZmGoogleMarkers' ).val('');
		var gilZmWidth  = document.getElementById( 'gil-zm-google-map' ).offsetWidth,
				gilZmCentre = gilZmMap.getCenter().toUrlValue(),
				gilZmMarker = '';
		$( '#gilZmGoogleMapUrl' ).val( 'https://maps.googleapis.com/maps/api/staticmap?center=' + gilZmCentre + '&path=color:0x00FF00FF|fillcolor:0x00FF0033|weight:3|' + gilZmMarker + '&size=' + gilZmWidth + 'x500&zoom=' + gilZmZoomLevel + '&sensor=false&maptype=hybrid&key=' + gilZmLocalized.googleKey );
		$( '#gilZmAddress' ).val('');
		$( '#gilZmHref' ).val('');
		document.getElementById('gilZmSubmit').disabled = false;
	}



	/**
	* Initialised the map
	*/
	function gilZmInit(){
		gilZmLat       = gilZmLocalized.defaultLat // 51.503181; // gilZmLocalized defaultLat
		gilZmLng       = gilZmLocalized.defaultLng // -0.119717; // gilZmLocalized.defaultLng
		gilZmZoomLevel = 15;
		gilZmMarkers   = [];
		gilZmPath      = new google.maps.MVCArray;
		//gilZmResetForm();
		gilZmGoogleMap();
	}



	let requiredCheckboxes = $( '#gil-zm-site-type-group :checkbox[required]' );
	requiredCheckboxes.on( 'change', function(){
		if(requiredCheckboxes.is( ':checked' )) {
			requiredCheckboxes.removeAttr( 'required');
		}
		else {
			requiredCheckboxes.attr( 'required', 'required' );
		}
	} );



	$( "#gilZmDataCapture" ).submit(function( event ) {
		let area = document.getElementById('gilZmGoogleMapZoneArea').value;
		if ( area.length < 1 ) {
			alert( "Please define an area." );
			event.preventDefault();
		}
});
})(jQuery); // Fully reference jQuery after this point.