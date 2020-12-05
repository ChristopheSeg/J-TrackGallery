function getAvgTime(speed_str, length, decimal_separator)  {

		// Speed format is with decimal separator or . or ,
		var speed = speed_str.replace(decimal_separator, '.');
		speed = speed.replace(',', '.');
		if (speed ==0)
		{
			// set speed to 1 when null!
			document.getElementById('speed').value = '1';
			speed = 1;
		}
		var time = length / speed;
		var timestring = time.toString();
		var parts = timestring.split(".");
		if (!parts[1]) parts[1] = 0;
		var m1 = 0+"."+parts[1].toString();
		var m2 = m1 / 10 * 6;
		m2 = runde(m2,2);
		var m = m2.split(".");
		var time2 = parts[0] + "h " + m[1] + "m";
		document.getElementById('time').value = time2;
		//document.getElementById('pace').value = '';
}

function getAvgTimeFromPace(pace_str, length, decimal_separator) {

	// check pace format
	//var n = pace_str.indexOf(":");
	if (pace_str.indexOf(":")>=0)
	{
		// Pace format is time format mm:ss
		var pace_parts = pace_str.split(":");
		if (!pace_parts[1]) pace_parts[1] = 0;
		if (!pace_parts[0]) pace_parts[0] = 0;
		var pace = pace_parts[1]/60 + pace_parts[0]/1;
	}
	else
	{
		// Pace format is decimal separator or . or ,
		var pace_str = pace_str.replace(decimal_separator, '.');
		pace = pace.replace(',', '.');
	}

	var time = length * pace / 60;
	var timestring = time.toString();
	var parts = timestring.split(".");
	if (!parts[1]) parts[1] = 0;
	var m1 = 0+"."+parts[1].toString();
	var m2 = m1 / 10 * 6;
	m2 = runde(m2,2);
	var m = m2.split(".");
	var time2 = parts[0] + "h " + m[1] + "m";
	document.getElementById('time').value = time2;
	document.getElementById('speed').value = '';
}

function runde(x, n) {
  if (n < 1 || n > 14) return false;
  var e = Math.pow(10, n);
  var k = (Math.round(x * e) / e).toString();
  if (k.indexOf('.') == -1) k += '.';
  k += e.toString().substring(1);
  return k.substring(0, k.indexOf('.') + n+1);
}


function submitform(pressbutton){
	if (pressbutton) {
		document.adminForm.task.value=pressbutton;
	}
	if (typeof document.adminForm.onsubmit == "function") {
		document.adminForm.onsubmit();
	}
	document.adminForm.submit();
}

function getCycleTileURL(bounds) {
   var res = this.map.getResolution();
   var x = Math.round((bounds.left - this.maxExtent.left) / (res * this.tileSize.w));
   var y = Math.round((this.maxExtent.top - bounds.top) / (res * this.tileSize.h));
   var z = this.map.getZoom();
   var limit = Math.pow(2, z);

   if (y < 0 || y >= limit)
   {
     return null;
   }
   else
   {
     x = ((x % limit) + limit) % limit;

     return this.url + z + "/" + x + "/" + y + "." + this.type;
   }
}

/*
 * Funktion zum Zerlegen der URL um die Parameter zu erhalten (für den Permalink)
 * Splits the URL in its parameters
 */
function get_parameters() {
 // erzeugt für jeden in der url übergebenen parameter einen wert
 // bsp: x.htm?nachname=Munch&vorname=Alex&bildfile=wasserfall.jpg  erzeugt
 // variable nachname mit wert Munch  und
 // variable vorname mit wert Alex
 // variable bildfile mit wert wasserfall.jpg
 var hier = document.URL;
 var parameterzeile = hier.substr((hier.indexOf("?")+1));
 var trennpos;
 var endpos;
 var paramname;
 var paramwert;
 var parameters = new Object();
 while (parameterzeile != "") {
  trennpos = parameterzeile.indexOf("=");
  endpos = parameterzeile.indexOf("&");
  if (endpos < 0) { endpos = 500000; }
  paramname = parameterzeile.substr(0,trennpos);
  paramwert = parameterzeile.substring(trennpos+1,endpos);
  parameters[paramname] = paramwert;
  //eval (paramname + " = \"" + paramwert + "\"");
  parameterzeile = parameterzeile.substr(endpos+1);
 }
 return parameters;
}

/*
 * Wie der Name schon sagt ebenfalls für den Permalink, überprüft ob die Parameter in der URL gefunden wurden und überschreibt
 * sie gegebenenfalls.
 * Checks the url for parameters of the permalink and overwrites the default values if necessary.
 */
function checkForPermalink() {
	var parameters = get_parameters();

	if (parameters['zoom'] != null)
		zoom = parseInt(parameters['zoom']);
	if (parameters['lat'] != null)
		lat = parseFloat(parameters['lat']);
	if (parameters['lon'] != null)
		lon = parseFloat(parameters['lon']);
}
/*
 * Debugging Funktion
 */
function var_dump(obj) {
   if (typeof obj == "object") {
      return "Type: "+typeof(obj)+((obj.constructor) ? "\nConstructor: "+obj.constructor : "")+"\nValue: " + obj;
   }
else
{
      return "Type: "+typeof(obj)+"\nValue: "+obj;
   }
}//end function var_dump

/*
 * Für den Layer-Switcher mit Buttons
 */
function setLayer(id) {
	if (document.getElementById("layer") != null) {
		for (var i=0;i<layers.length;++i)
			document.getElementById(layers[i][1]).className = "";
	}
	varName = layers[id][0];
	name = layers[id][1];
	map.setBaseLayer(varName);
	if (document.getElementById("layer") != null)
		document.getElementById(name).className = "active";
}
/*
 * Schaltet die Beschreibung der Karte an- und aus.
 * Toggles the description of the map.
 */
function toggleInfo() {
	var state = document.getElementById('description').className;
	if (state == 'hide') {
		// Info anzeigen
		document.getElementById('description').className = '';
		document.getElementById('descriptionToggle').innerHTML = text[1];
	}
	else {
		// Info verstecken
		document.getElementById('description').className = 'hide';
		document.getElementById('descriptionToggle').innerHTML = text[0];
	}
}

/*
 * Zeichnet verschiedene Arten von geometrischen Objekten
 * Draws different kinds of geometric objects
 */

function drawLine(coordinates,style) {
	var linePoints = createPointsArrayFromCoordinates(coordinates);

	var line = new OpenLayers.Geometry.LineString(linePoints);
	var vector = new OpenLayers.Feature.Vector(line,null,style);

	layer_vectors.addFeatures(vector);
	return vector;
}
function drawPolygon(coordinates,style) {
	var points = createPointsArrayFromCoordinates(coordinates);

	var linearRing = new OpenLayers.Geometry.LinearRing(points);
	var polygon = new OpenLayers.Geometry.Polygon([linearRing]);
	var vector = new OpenLayers.Feature.Vector(polygon,null,style);

	layer_vectors.addFeatures(vector);
	return vector;
}
function createPointsArrayFromCoordinates(coordinates) {
	var points = new Array();
	for (var i=0;i<coordinates.length;++i) {
		var lonlat = new OpenLayers.LonLat(coordinates[i][0],coordinates[i][1]).transform(new OpenLayers.Projection("EPSG:4326"),new OpenLayers.Projection("EPSG:900913"));
		points.push(new OpenLayers.Geometry.Point(lonlat.lon,lonlat.lat));
	}
	return points;
}

/*
 * Gibt eine Fehlermeldung aus, wenn die Version der JavaScript Datei nicht mit der erforderlichen übereinstimmt
 * Outputs an error if the version of the JavaScript-File does not match the required one
 */

function checkUtilVersion(version) {
	var thisFileVersion = 4;
	if (version != thisFileVersion) {
		alert("map.html and util.js versions do not match.\n\nPlease reload the page using your browsers 'reload' feature.\n\nIf the problem persists and you are the owner of this site, you may need to update the map's files . ");
	}
}

// MvL: could move this to a separate file ?; similar function used in jtgOverview.js, but no clusterig here?
function addPopup() {
    /**
     * Elements that make up the popup.
     */
    var container = document.getElementById('popup');
    var closer = document.getElementById('popup-closer');
    if (container == null || closer == null)
       return;

    var popupActive = false;
    /**
     * Create an overlay to anchor the popup to the map.
     */
    var overlay = new ol.Overlay({
        element: container,
        autoPan: true,
        autoPanAnimation: {
            duration: 250
        }
    });

    /**
     * Add a click handler to hide the popup.
     * @return {boolean} Don't follow the href.
     */
    closer.onclick = function() {
        overlay.setPosition(undefined);
        closer.blur();
       popupActive = false;
        return false;
    };

    olmap.addOverlay(overlay);
    
    function popupInfo(event) {
       var point = 0;
       var pixel = olmap.getEventPixel(event.originalEvent);
       olmap.forEachFeatureAtPixel(pixel, function(feature) {
            if (feature.getGeometry().getType() == 'Point') { 
               point = feature;
            }
        });
       if (point) {
       // Set content of popup
       var content = document.getElementById('popup-content');
       content.innerHTML = point.get('name');
       // show image if available?
       // and position
       var coordinate = event.coordinate; // MvL TODO: change to point.getGeometry().getCoordinate; converted to pixels
       overlay.setPosition(coordinate);
       popupActive = true;
       }
       else {
           overlay.setPosition(undefined);
           popupActive = false;
       }
    }
 
    /**
     * Add a click handler to the map to render the popup.
     * MvL: change this to add handler to points
     */
    olmap.on('singleclick', function(evt) {
        var pixel = olmap.getEventPixel(evt.originalEvent);
        popupInfo(evt);
    });
};
