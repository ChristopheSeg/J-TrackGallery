// Version 2011-02-24 15:39

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
	layerHome.removeFeatures(layerHome.features);
	var lat = document.getElementById("lat").value;
	var lon = document.getElementById("lon").value;
	var latlon = lonLatToMercator(new OpenLayers.LonLat(lon,lat));
	var marker = [];
	marker.push(new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Point(latlon.lon,latlon.lat)));
	layerHome.addFeatures(marker);
}

function mapcenter() {
	var lat = document.getElementById("lat").value;
	var lon = document.getElementById("lon").value;
	var lonlat = lonLatToMercator(new OpenLayers.LonLat(lon,lat));
	map.setCenter(lonlat);
}

function mapfirstcenter() {
	var lat = document.getElementById("lat").value;
	var lon = document.getElementById("lon").value;
	if (!lat || !lon) {
		lat = jtg_param_geo_lat;
		lon = jtg_param_geo_lon;
		zoom = jtg_param_geo_zoom;
		alert(alerttext);
	} else zoom = jtg_param_geo_zoom_loggedin;
	var lonlat = lonLatToMercator(new OpenLayers.LonLat(lon,lat));
	map.setCenter(lonlat,zoom);
}

function handleMapClick(evt) {
//set Marker on click
	layerHome.removeFeatures(layerHome.features);
	var pixel = new OpenLayers.Pixel(evt.xy.x, evt.xy.y);
	var lonLat = map.getLonLatFromViewPortPx(pixel);
	var marker = [];
	marker.push(new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Point(lonLat.lon, lonLat.lat)));
	layerHome.addFeatures(marker);
	OpenLayers.Event.stop(evt);
};

/**
 * Function: addMarker
 * Add a new marker to the markers layer given the following lonlat, 
 *     popupClass, and popup contents HTML. Also allow specifying 
 *     whether or not to give the popup a close box.
 * 
 * Parameters:
 * ll - {<OpenLayers.LonLat>} Where to place the marker
 * popupClass - {<OpenLayers.Class>} Which class of popup to bring up 
 *     when the marker is clicked.
 * popupContentHTML - {String} What to put in the popup
 * closeBox - {Boolean} Should popup have a close box?
 * overflow - {Boolean} Let the popup overflow scrollbars?
 */
function addMarker(ll, popupClass, popupContentHTML, closeBox, overflow, icon) {
	var feature = new OpenLayers.Feature(layerOUsers, ll);
	feature.closeBox = closeBox;
	feature.popupClass = popupClass;
	feature.data.popupContentHTML = popupContentHTML;
	feature.data.overflow = (overflow) ? "auto" : "hidden";
	var marker = new OpenLayers.Marker(ll,icon);
	marker.feature = feature;
	var markerClick = function (evt) {
		if (this.popup == null) {
			this.popup = this.createPopup(this.closeBox);
			map.addPopup(this.popup);
			this.popup.show();
		} else {
			this.popup.toggle();
		}
	currentPopup = this.popup;
	OpenLayers.Event.stop(evt);
	};
//	layerOUsers.events.register("mouseover", feature, markerClick);
	layerOUsers.events.register("mousedown", feature, markerClick);
	layerOUsers.addMarker(marker);
}

function showOtherUserInfo(ll, userhtml) {
	var feature = new OpenLayers.Feature(layerOUsers, ll);
	var marker = new OpenLayers.Marker(ll, new OpenLayers.Icon(
		IconOtherUser,
		SizeIconOtherUser,
		OffsetIconOtherUser));
	marker.feature = feature;
	var markerClick = function (evt) {
		// mootols required
		document.getElementById("otheruser").setHTML(userhtml);
	}
	currentPopup = this.popup;
	layerOUsers.events.register("mouseover", feature, markerClick);
	layerOUsers.addMarker(marker);
}

function parseOtherUsers() {
	if ( otherusers ) {
		for(i=0;i<otherusers;i++) {
		layerOUsers = new OpenLayers.Layer.Markers(name[i]);
		map.addLayer(layerOUsers);
			var lonlat = lonLatToMercator(new OpenLayers.LonLat(lon[i],lat[i]));
			ll = new OpenLayers.LonLat(
				lonlat.lon,lonlat.lat).transform(
					new OpenLayers.Projection("EPSG:4326"),
					map.getProjectionObject());
					popupContentHTML = inittext + '<b>' + name[i] + '</b> <small>(' + link[i] + ')</small>  ' + distancetext + distance[i] + '<br />';
//					popupContentHTML = link[i];
					showOtherUserInfo(ll, popupContentHTML );
		}
	}
}

function init() {
	map = new OpenLayers.Map('map',{
		controls:[
				new OpenLayers.Control.MousePosition(),		// Koordinate des Mauszeigers (lat, lon)
				new OpenLayers.Control.PanZoomBar(),		// Zoombalken
				new OpenLayers.Control.Navigation(),		// mit Maus verschieb- und zoombar
				new OpenLayers.Control.Attribution()		// Lizenz
//				new OpenLayers.Control.LayerSwitcher(),		// Menü zum ein/aus-Schalten der Layer
			],
			maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
			maxResolution: 156543.0399,
			numZoomLevels: 19,
			units: "m",
			projection: new OpenLayers.Projection("EPSG:900913"),
			displayProjection: new OpenLayers.Projection("EPSG:4326")
	});
	if ( MarkerHomePosition != 'false' ) {
		layerHome = new OpenLayers.Layer.Vector(MarkerHomePosition, layerHome_options());
		map.addLayer(layerHome);
	}
	parseOtherUsers();
	var layerMapnik = new OpenLayers.Layer.OSM.Mapnik("Mapnik");
	map.addLayer(layerMapnik);
	mapfirstcenter();
	var click = new OpenLayers.Control.Click();
	map.addControl(click);
	click.activate();
	map.events.register('click', map, handleMapClick);
	handleFillLL();
}

OpenLayers.Control.Click = OpenLayers.Class(OpenLayers.Control, {
	defaultHandlerOptions: {'single': true},
	initialize: function(options) {
		this.handlerOptions = OpenLayers.Util.extend({}, this.defaultHandlerOptions);
		OpenLayers.Control.prototype.initialize.apply(this, arguments);
		this.handler = new OpenLayers.Handler.Click(this, {'click': this.trigger}, this.handlerOptions);
	},
	trigger: function(e) {
		var lonlat = map.getLonLatFromViewPortPx(e.xy);
			lonlat.transform(new OpenLayers.Projection("EPSG:900913"), new OpenLayers.Projection("EPSG:4326"));
		var output = document.getElementById("lat");
		output.value = lonlat.lat;
		var output = document.getElementById("lon");
		output.value = lonlat.lon;
	mapcenter();
	}
});

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
function showOtherUserInfo(ll, popupClass, popupContentHTML, closeBox, overflow, userhtml) {
	var feature = new OpenLayers.Feature(layerOUsers, ll);
	feature.closeBox = closeBox;
	feature.popupClass = popupClass;
	feature.data.popupContentHTML = popupContentHTML;
	feature.data.overflow = (overflow) ? "auto" : "hidden";
	var marker = new OpenLayers.Marker(ll, IconOtherUser);
//	var marker = new OpenLayers.Marker(ll);
	marker.feature = feature;
	var markerClick = function (evt) {
		if (this.popup == null) {
//			this.popup = this.createPopup(this.closeBox);
//			map.addPopup(this.popup);
//			this.popup.show();
			document.getElementById("otheruser").setHTML(userhtml);
		} else {
//			this.popup.toggle();
			document.getElementById("otheruser").setHTML(userhtml);
		}
	currentPopup = this.popup;
	OpenLayers.Event.stop(evt);
	};
	layerOUsers.events.register("mouseover", feature, markerClick);
	layerOUsers.addMarker(marker);
}
*/
