// from https://stackoverflow.com/questions/65379949/geolocation-getcurrentposition-on-safari-on-ios
// see also: https://crate.io/a/geolocation-101-get-users-location/

function geolocate() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(centerOnLocation, showError);
  } else { 
    document.getElementById('geo-msg').innerHTML = "Geolocation is not supported by this browser.";
  }
}

function centerOnLocation(position) {
	olview.setCenter(ol.proj.fromLonLat([position.coords.longitude, position.coords.latitude], olview.getProjection()));
	olview.setZoom(6); // TODO: set from configuration
}

function showError(error) {
	// Show error/status message on page when geolocation failed
	// TODO: Use language settings/translations here
  var msgElement = document.getElementById('geo-msg');
  switch(error.code) {
    case error.PERMISSION_DENIED:
      msgElement.innerHTML = "User denied the request for Geolocation."
      break;
    case error.POSITION_UNAVAILABLE:
      msgElement.innerHTML = "Location information is unavailable."
      break;
    case error.TIMEOUT:
      msgElement.innerHTML = "The request to get user location timed out."
      break;
    case error.UNKNOWN_ERROR:
      msgElement.innerHTML = "An unknown error occurred."
      break;
  }
}

// from: https://openlayers.org/en/latest/examples/custom-controls.html
// change to geolocation button?
// This import statement is in the original code
// import {Control, defaults as defaultControls} from 'ol/control';
var CenterOnGeoControl = /*@__PURE__*/(function (Control) {
  function CenterOnGeoControl(opt_options) {
    var options = opt_options || {};

    var button = document.createElement('button');
    button.innerHTML = 'my_location';

    var element = document.createElement('div');
    element.className = 'rotate-north ol-unselectable ol-control material-icons-outlines'; // Use google icon font
	// Need to load style sheet with
// <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
//      rel="stylesheet">
    element.appendChild(button);

    Control.call(this, {
      element: element,
      target: options.target,
    });

    button.addEventListener('click', this.handleCenterOnGeo.bind(this), false);
  }

  if ( Control ) CenterOnGeoControl.__proto__ = Control;
  CenterOnGeoControl.prototype = Object.create( Control && Control.prototype );
  CenterOnGeoControl.prototype.constructor = CenterOnGeoControl;

  CenterOnGeoControl.prototype.handleCenterOnGeo = function handleCenterOnGeo () {
    this.getMap().getView().setRotation(0); // TODO: Change this!
  };

  return CenterOnGeoControl;
}(ol.control.Control));
