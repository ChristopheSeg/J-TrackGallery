// Updated to openlayers 6
/**
* Function: lonLatToMercator
* OpenLayers-Karte mit OSM-Daten aufbauen
* Version 2009-09-07 
* "EPSG:41001" -> 4326
* http://www.fotodrachen.de/javascripts/map.js
* Parameters:
* ll - {<OpenLayers.LonLat>}
*/
var map;
function lonLatToMercator(ll) {
	var lon = ll.lon * 20037508.34 / 180;
	var lat = Math.log(Math.tan((90 + ll.lat) * Math.PI / 360)) / (Math.PI / 180);
	lat = lat * 20037508.34 / 180;
	return new OpenLayers.LonLat(lon, lat);
}

function handleFillLL() {
    // Set home position marker to lat, lon entered on page
	layerHome.getSource().clear();
	var lat = document.getElementById("lat").value;
	var lon = document.getElementById("lon").value;
	layerHome.getSource().addFeature(new ol.Feature( {
	        geometry: new ol.geom.Point(ol.proj.fromLonLat( [lon, lat], olview.getProjection()))} )
	        );
}

function mapcenter() {
	var lat = document.getElementById("lat").value;
	var lon = document.getElementById("lon").value;
    olview.setCenter(ol.proj.fromLonLat([lon, lat], olview.getProjection()));
}

function mapfirstcenter() {
	var lat = document.getElementById("lat").value;
	var lon = document.getElementById("lon").value;
	zoom = jtg_param_geo_zoom;
	if (!lat || !lon) {
		// FIXME: These are currently not settable in the config
		lat = jtg_param_geo_lat;
		lon = jtg_param_geo_lon;
		alert(alerttext);
	}
	olview.setZoom(zoom);
	olview.setCenter(ol.proj.fromLonLat([lon, lat], olview.getProjection())); 
}

function handleMapClick(evt) {
//set Marker on click
	layerHome.getSource().clear();
    var lonlat = ol.proj.toLonLat(evt.coordinate, olview.getProjection() );
    var output = document.getElementById("lat");
	output.value = lonlat[1];
    output = document.getElementById("lon");
	output.value = lonlat[0];

	var marker = [];
	marker.push(new ol.Feature({ geometry: new ol.geom.Point(evt.coordinate)}));
	layerHome.getSource().addFeatures(marker);
	mapcenter();
}

function showOtherUserInfo(lon, lat, userhtml) {
	//OpenLayers.Feature(layerOUsers, ll);
	var feature = new ol.Feature( {
	        geometry: new ol.geom.Point(ol.proj.fromLonLat([lon, lat], olview.getProjection()))}
	    );
	/*
	var marker = new OpenLayers.Marker(ll, new OpenLayers.Icon(
		IconOtherUser,
		SizeIconOtherUser,
		OffsetIconOtherUser));
	marker.feature = feature;
	var markerClick = function (evt) {
		// mootols required
		document.getElementById("otheruser").innerHTML=userhtml;
	}
	*/
	//currentPopup = this.popup;
	//layerOUsers.events.register("mouseover", feature, markerClick);
	// TODO: add popup info
	layerOUsers.getSource().addFeature(feature);
}

function parseOtherUsers() {
	if ( otherusers ) {
		for (i=0;i<otherusers;i++) {
		    // TODO: don't need a layer for every user? check where to set name
		    layerOUsers = new ol.layer.Vector(
		        { source: new ol.source.Vector(),
                style: new ol.style.Style({
                    image: new ol.style.Icon({ src: IconOtherUser }),
		        name: name[i]
                })
            });
		    map.addLayer(layerOUsers);
			popupContentHTML = inittext + '<b>' + name[i] + '</b> <small>(' + link[i] + ')</small>  ' + distancetext + distance[i] + '<br />';
// 				popupContentHTML = link[i];
			showOtherUserInfo(lon[i], lat[i], popupContentHTML );
		}
	}
}
//TODO ACCOUNT FOR TEMPLATES See gpsclass.php
function init() {

    olview = new ol.View( { 
        center: [0, 0],
        units: "m",
        zoom: 8
    } );
    map = new ol.Map ( { target: "jtg_map",
                controls: [
                    new ol.control.MousePosition( {coordinateFormat: ol.coordinate.createStringXY(4), projection: 'EPSG:4326' }),
                    new ol.control.ZoomSlider(),
                    new ol.control.Attribution() ],
                view: olview
    } );
    var layerMapnik = new ol.layer.Tile({ source: new ol.source.OSM() });
    map.addLayer(layerMapnik);

    layerHome = new ol.layer.Vector( {
	    source: new ol.source.Vector(),
	    style: new ol.style.Style({
	        image: new ol.style.Icon({ src: imgpath+'home.png'})
		}),
		name: MarkerHomePosition
    });
	if ( MarkerHomePosition != 'false' ) {
	    // Todo correctly set icon path
        //TODO: use layerHome_options()          
        //OpenLayers.Layer.Vector(MarkerHomePosition, layerHome_options());
		map.addLayer(layerHome);
	}
	parseOtherUsers();
    mapfirstcenter();
    map.on('click', handleMapClick);
	handleFillLL();
	// TODO: make popup work
}

function layerHome_options() {
	return {		styleMap: new OpenLayers.StyleMap({
			externalGraphic: iconpath + "home.png",
			backgroundGraphic: iconpath + "home_shdw.png",
			graphicXOffset:-15,
			graphicYOffset:-20,
			backgroundXOffset:-15,
			backgroundYOffset:-20,
			graphicZIndex: 11,
			backgroundGraphicZIndex: 10,
			pointRadius: 20
		}),
		displayInLayerSwitcher: true,
		isBaseLayer:false,
		rendererOptions: {yOrdering: true}
};
}

/*
// popup code?
function showOtherUserInfo(ll, popupClass, popupContentHTML, closeBox, overflow, userhtml) {
	var feature = new OpenLayers.Feature(layerOUsers, ll);
	feature.closeBox = closeBox;
	feature.popupClass = popupClass;
	feature.data.popupContentHTML = popupContentHTML;
	feature.data.overflow = (overflow) ? "auto" : "hidden";
	var marker = new OpenLayers.Marker(ll, IconOtherUser);
// var marker = new OpenLayers.Marker(ll);
	marker.feature = feature;
	var markerClick = function (evt) {
		if (this.popup == null) {
// 		this.popup = this.createPopup(this.closeBox);
// 		map.addPopup(this.popup);
// 		this.popup.show();
			document.getElementById("otheruser").setHTML(userhtml);
		}
else
{
// 		this.popup.toggle();
			document.getElementById("otheruser").setHTML(userhtml);
		}
	currentPopup = this.popup;
	OpenLayers.Event.stop(evt);
	};
	layerOUsers.events.register("mouseover", feature, markerClick);
	layerOUsers.addMarker(marker);
}
*/
