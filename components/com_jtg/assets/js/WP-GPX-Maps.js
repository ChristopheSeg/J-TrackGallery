/*

	WP-GPX-Maps

*/

var t;
var funqueue = [];
var infowindow;
var mapLoading = false;
var CustomMarker;

var wrapFunction = function(fn, context, params) {
    return function() {
        fn.apply(context, params);
    };
}

function wpgpxmaps(params)
{
	funqueue.push( wrapFunction(_wpgpxmaps, this, [params]));
	unqueue();
}

function unqueue()
{
	if (google == undefined || google.maps == undefined || Highcharts == undefined)
	{
		t = setTimeout("unqueue()",200);
	}
	else
	{
		setup();
		while (funqueue.length > 0) {
			(funqueue.shift())();   
		}
	}
}

function setup()
{

	CustomMarker = function( map, latlng, src, img_w, img_h) {
		this.latlng_ = latlng;
		this.setMap(map);
		this.src_ = src;
		this.img_w_ = img_w;
		this.img_h_ = img_h;
	}

	CustomMarker.prototype = new google.maps.OverlayView();

	CustomMarker.prototype.draw = function() {
	
		var me = this;

		// Check if the el has been created.
		var el = this.img_;
		if (!el) {

			this.img_ = document.createElement('img');
			el = this.img_;
			el.style.cssText = "width:"+(this.img_w_/3)+"px;height:"+(this.img_h_/3)+"px;";
			el.setAttribute("class", "myngimages");
			el.setAttribute("lat",this.latlng_.lat());
			el.setAttribute("lon",this.latlng_.lng());
			el.src=this.src_;

			google.maps.event.addDomListener(el, "click", function(event) {
				google.maps.event.trigger(me, "click", el);
			});	
			
			google.maps.event.addDomListener(el, "mouseover", function(event) {
				var _t = el.style.top.replace('px','');
				var _l = el.style.left.replace('px','');
				jQuery(el).animate({
					height: me.img_h_,
					width : me.img_w_,
					top   : _t - (me.img_h_ / 3),
					left  : _l - (me.img_w_ / 3),
					'z-index' : 100
				  }, 100);
			});

			google.maps.event.addDomListener(el, "mouseout", function(event) {
				jQuery(el).animate({
					height: me.img_h_ / 3,
					width: me.img_w_ / 3,
					top   : me.orig_top,
					left  : me.orig_left,
					'z-index' : 1
				  }, 100);
			});	

			// Then add the overlay to the DOM
			var panes = this.getPanes();
			panes.overlayImage.appendChild(el);
		}

		// Position the overlay 
		var point = this.getProjection().fromLatLngToDivPixel(this.latlng_);
			if (point) {
			  el.style.left = point.x + 'px';
			  el.style.top = point.y + 'px';
			  this.orig_left = point.x;
			  this.orig_top = point.y;
			}
	};

	CustomMarker.prototype.remove = function() {
		// Check if the overlay was on the map and needs to be removed.
		if (this.img_) {
		  this.img_.parentNode.removeChild(this.img_);
		  this.img_ = null;
		}
	};

}

function get_random_color() {
    var letters = '0123456789ABCDEF'.split('');
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.round(Math.random() * 15)];
    }
    return color;
}


function _wpgpxmaps(params)
{

	var targetId = params.targetId;
	var mapType = params.mapType;
	var mapData = params.mapData;
	var graphDist = params.graphDist;
	var graphEle = params.graphEle;
	var graphSpeed = params.graphSpeed;
	var graphHr = params.graphHr;
	var graphCad = params.graphCad;
	var graphGrade = params.graphGrade;
	var waypoints = params.waypoints;
	var unit = params.unit;
	var unitspeed = params.unitspeed;
	var color1 = params.color1;
	var color2 = params.color2;
	var color3 = params.color3;
	var color4 = params.color4;
	var color5 = params.color5;
	var color6 = params.color6;
	var chartFrom1 = params.chartFrom1;
	var chartTo1 = params.chartTo1;
	var chartFrom2 = params.chartFrom2;
	var chartTo2 = params.chartTo2;
	var startIcon = params.startIcon;
	var waypointIcon = params.waypointIcon;
	var endIcon = params.endIcon;
	var currentIcon = params.currentIcon;
	var zoomOnScrollWheel = params.zoomOnScrollWheel;
	var lng = params.langs;
	var pluginUrl = params.pluginUrl;
	
	var el = document.getElementById("wpgpxmaps_" + targetId);
	var el_map = document.getElementById("map_" + targetId);
	var el_chart = document.getElementById("chart_" + targetId);
	
	var mapWidth = el_map.style.width;
	
	var mapTypeIds = [];
	for(var type in google.maps.MapTypeId) {
		mapTypeIds.push(google.maps.MapTypeId[type]);
	}
	mapTypeIds.push("OSM1");
	mapTypeIds.push("OSM2");
	mapTypeIds.push("OSM3");
	mapTypeIds.push("OSM4");
	mapTypeIds.push("OSM5");
	
	var ngImageMarkers = [];
	
	switch (mapType)
	{
		case 'TERRAIN': { mapType = google.maps.MapTypeId.TERRAIN; break;}
		case 'SATELLITE': { mapType = google.maps.MapTypeId.SATELLITE; break;}
		case 'ROADMAP': { mapType = google.maps.MapTypeId.ROADMAP; break;}
		case 'OSM1': { mapType = "OSM1"; break;}
		case 'OSM2': { mapType = "OSM2"; break;}
		case 'OSM3': { mapType = "OSM3"; break;}
		case 'OSM4': { mapType = "OSM4"; break;}
		case 'OSM5': { mapType = "OSM5"; break;}
		default: { mapType = google.maps.MapTypeId.HYBRID; break;}
	}

	var map = new google.maps.Map(el_map, {
		mapTypeId: mapType,
		scrollwheel: (zoomOnScrollWheel == 'true'),
		mapTypeControlOptions: {
			style: google.maps.MapTypeControlStyle.DROPDOWN_MENU,
			mapTypeIds: mapTypeIds
		}
	}); 
	
	map.mapTypes.set("OSM1", new google.maps.ImageMapType({
		getTileUrl: function(coord, zoom) {
			return "http://tile.openstreetmap.org/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
		},
		tileSize: new google.maps.Size(256, 256),
		name: "Open Street Map",
		maxZoom: 18
	}));
	
	map.mapTypes.set("OSM2", new google.maps.ImageMapType({
		getTileUrl: function(coord, zoom) {
			return "http://a.tile.opencyclemap.org/cycle/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
		},
		tileSize: new google.maps.Size(256, 256),
		name: "Open Cycle Map",
		maxZoom: 18
	}));
	
	map.mapTypes.set("OSM4", new google.maps.ImageMapType({
		getTileUrl: function(coord, zoom) {
			return "http://a.tile2.opencyclemap.org/transport/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
		},
		tileSize: new google.maps.Size(256, 256),
		name: "Open Cycle Map - Transport",
		maxZoom: 18
	}));
	
	map.mapTypes.set("OSM5", new google.maps.ImageMapType({
		getTileUrl: function(coord, zoom) {
			return "http://a.tile3.opencyclemap.org/landscape/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
		},
		tileSize: new google.maps.Size(256, 256),
		name: "Open Cycle Map - Landscape",
		maxZoom: 18
	}));
	
	map.mapTypes.set("OSM3", new google.maps.ImageMapType({
		getTileUrl: function(coord, zoom) {
			return "http://toolserver.org/tiles/hikebike/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
		},
		tileSize: new google.maps.Size(256, 256),
		name: "Hike & Bike",
		maxZoom: 18
	}));
	
	
	// FULL SCREEN BUTTON
	var controlDiv = document.createElement('div');
	controlDiv.style.padding = '5px';

	// Set CSS for the control border
	var controlUI = document.createElement('img');
	controlUI.src = pluginUrl + "/wp-gpx-maps/img/goFullScreen.png";
	controlUI.style.cursor = 'pointer';
	controlUI.title = lng.goFullScreen;
	controlDiv.appendChild(controlUI);

	// Setup the click event listeners
	google.maps.event.addDomListener(controlUI, 'click', function(event) {
		var isFullScreen = (controlUI.isfullscreen == true);
		var fullScreenCss = "position: absolute;left:0;top:0;";
		var mapDiv = map.getDiv();
		var center = map.getCenter();
		
		if (isFullScreen)
		{
			map.setOptions( { scrollwheel : (zoomOnScrollWheel == 'true') } );
			jQuery(mapDiv).css("position", 'relative').
			  css('top', 0).
			  css("width", controlUI.googleMapWidth).
			  css("height", controlUI.googleMapHeight).
			  css("z-index", '');
			google.maps.event.trigger(map, 'resize');
			map.setCenter(center);
			controlUI.src = pluginUrl + "/wp-gpx-maps/img/goFullScreen.png";	
			controlUI.title = lng.gofullscreen;
		}
		else
		{
			map.setOptions( { scrollwheel : true } );		
			controlUI.googleMapWidth = jQuery(mapDiv).css('width');
			controlUI.googleMapHeight = jQuery(mapDiv).css('height');		
			jQuery(mapDiv).css("position", 'fixed').
			  css('top', 0).
			  css('left', 0).
			  css("width", '100%').
			  css("height", '100%').
			  css("z-index", '100');
			jQuery("#wpadminbar").each(function(){
				jQuery(mapDiv).css('top', jQuery(this).height());
			});
			google.maps.event.trigger(map, 'resize');
			map.setCenter(center);
			controlUI.src = pluginUrl + "/wp-gpx-maps/img/exitFullFcreen.png";
			controlUI.title = lng.exitFullFcreen;
		}
		controlUI.isfullscreen = !isFullScreen;
		return false;			
	});
	
	controlDiv.index = 1;
	map.controls[google.maps.ControlPosition.TOP_RIGHT].push(controlDiv);
	
	var bounds = new google.maps.LatLngBounds();
	
	// Print WayPoints
	if (waypoints != '')
	{

		var image = new google.maps.MarkerImage('http://maps.google.com/mapfiles/ms/micons/flag.png',
			new google.maps.Size(32, 32),
			new google.maps.Point(0,0),
			new google.maps.Point(16, 32)
		);
		var shadow = new google.maps.MarkerImage('http://maps.google.com/mapfiles/ms/micons/flag.shadow.png',
			new google.maps.Size(59, 32),
			new google.maps.Point(0,0),
			new google.maps.Point(16, 32)
		);
		
		if (waypointIcon!='')
		{
			image = new google.maps.MarkerImage(waypointIcon);
			shadow = '';
		}		
		
		for (i=0; i < waypoints.length; i++) 
		{
			var lat= waypoints[i][0];
			var lon= waypoints[i][1];
			addWayPoint(map, image, shadow, lat, lon, waypoints[i][2], waypoints[i][3]);
			bounds.extend(new google.maps.LatLng(lat, lon));
		}
	}
	
	// Print Images
	
	jQuery("#ngimages_" + targetId).attr("style","display:block;position:absolute;left:-50000px");
	jQuery("#ngimages_" + targetId + " span").each(function(){
	
		var imageLat  = jQuery(this).attr("lat");
		var imageLon  = jQuery(this).attr("lon");	
		
		jQuery("img",this).each(function() {
		
			jQuery(this).load(function(){

				var imageUrl  = jQuery(this).attr("src");
				var img_w = jQuery(this).width();
				var img_h = jQuery(this).height();
				
				var p = new google.maps.LatLng(imageLat, imageLon);
				bounds.extend(p);

				var mc = new CustomMarker(map, p, imageUrl, img_w, img_h );
				
				ngImageMarkers.push(mc);
				
				google.maps.event.addListener(mc, "click", function(div) {
					var lat = div.getAttribute("lat");
					var lon = div.getAttribute("lon");
					var a = getClosestImage(lat,lon,targetId).childNodes[0];			
					if (a)
					{
						a.click();
					}
				});

			});
		
			if (jQuery(this).width() + jQuery(this).height() > 0)
			{
				jQuery(this).trigger("load");
			}

		});
	
	});
	
	
	if ( jQuery("#ngimages_" + targetId + " span").length > 0 )
	{
	
		// Set CSS for the control border
		var controlUIhi = document.createElement('img');
		controlUIhi.src = pluginUrl + "/wp-gpx-maps/img/hideImages.png";
		controlUIhi.style.cursor = 'pointer';
		controlUIhi.title = lng.hideImages;
		controlDiv.appendChild(controlUIhi);

		// Setup the click event listeners
		google.maps.event.addDomListener(controlUIhi, 'click', function(event) {
			var isImagesHidden = (controlUIhi.isImagesHidden == true);
			var fullScreenCss = "position: absolute;left:0;top:0;";
			var mapDiv = map.getDiv();
			var center = map.getCenter();
			
			if (isImagesHidden)
			{
				for (var i=0; i<ngImageMarkers.length; i++) {
					ngImageMarkers[i].setMap(map);
				}			
				controlUIhi.src = pluginUrl + "/wp-gpx-maps/img/hideImages.png";	
				controlUIhi.title = lng.hideImages;
			}
			else
			{
				for (var i=0; i<ngImageMarkers.length; i++) {
					ngImageMarkers[i].setMap(null);
				}			
				controlUIhi.src = pluginUrl + "/wp-gpx-maps/img/showImages.png";
				controlUIhi.title = lng.showImages;
			}
			controlUIhi.isImagesHidden = !isImagesHidden;
			return false;			
		});

	}
	
	
	// Print Track
	if (mapData != '')		
	{
		var points = [];
		var lastCut=0;
		var polylinenes = [];
		var polyline_number=0;
		var color=0;
		for (i=0; i < mapData.length; i++) 
		{	
			if (mapData[i] == null)
			{
			

					color=color1[polyline_number % color1.length];


				var poly = new google.maps.Polyline({
					path: points.slice(lastCut,i),
					strokeColor: color,
					strokeOpacity: .7,
					strokeWeight: 4,
					map: map
				});
				polylinenes.push(poly);
				lastCut=i;
				polyline_number= polyline_number +1;
				//var p = new google.maps.LatLng(mapData[i-1][0], mapData[i-1][1]);
				//points.push(p);
				//bounds.extend(p);
			}
			else
			{
				var p = new google.maps.LatLng(mapData[i][0], mapData[i][1]);
				points.push(p);
				bounds.extend(p);			
			}
		}
		
		if (points.length != lastCut)
		{
				if ( polyline_number < color1.length)
				{
					color=color1[polyline_number];
				}
				else
				{
					color=color1[color1.length-1];
				}
			var poly = new google.maps.Polyline({
				path: points.slice(lastCut),
				strokeColor: color,
				strokeOpacity: .7,
				strokeWeight: 4,
				map: map
			});
			polylinenes.push(poly);			
			currentPoints = [];
            polyline_number= polyline_number +1;
		}
		
		if (startIcon != '')
		{
			var startIconImage = new google.maps.MarkerImage(startIcon);
			var startMarker = new google.maps.Marker({
					  position: points[0],
					  map: map,
					  title: "Start",
					  animation: google.maps.Animation.DROP,
					  icon: startIconImage,
					  zIndex: 10
				  });

		}

		if (endIcon != '')
		{
			var endIconImage = new google.maps.MarkerImage(endIcon);
			var startMarker = new google.maps.Marker({
					  position: points[ points.length -1 ],
					  map: map,
					  title: "Start",
					  animation: google.maps.Animation.DROP,
					  icon: endIconImage,
					  zIndex: 10
				  });
		
		}

		var first = getItemFromArray(mapData,0)
		
		if (currentIcon == '')
		{
			currentIcon = "http://maps.google.com/mapfiles/kml/pal4/icon25.png";
		}
		
		var current = new google.maps.MarkerImage(currentIcon,
			new google.maps.Size(32, 32),
			new google.maps.Point(0,0),
			new google.maps.Point(16, 16)
		);
		
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng(first[0], first[1]),
			title:"Start",
			icon: current,
			map: map,
			zIndex: 10
		});
		
		for (i=0; i < polylinenes.length; i++) 
		{	

			google.maps.event.addListener(polylinenes[i],'mouseover',function(event){
				if (marker)
				{
					marker.setPosition(event.latLng);	
					marker.setTitle(lng.currentPosition);
					if (hchart)
					{
						var tooltip = hchart.tooltip;
						var l1 = event.latLng.lat();
						var l2 = event.latLng.lng();
						var ci = getClosestIndex(mapData,l1,l2);
						var items = [];
						var seriesLen = hchart.series.length;
						for(var i=0; i<seriesLen;i++)
						{
							items.push(hchart.series[i].data[ci]);
						}
						if (items.length > 0)
							tooltip.refresh(items);
					}
				}
			});		
		}
	}
	
	map.setCenter(bounds.getCenter()); 
	map.fitBounds(bounds);
	
	var controlUIcenter = null;
	var idFirstCenterChanged = true;
	
	google.maps.event.addListener(map, 'center_changed', function() {

		if (idFirstCenterChanged == true)
		{
			idFirstCenterChanged = false;
			return;
		}
	
		if (controlUIcenter == null)
		{
			// Set CSS for the control border
			controlUIcenter = document.createElement('img');
			controlUIcenter.src = pluginUrl + "/wp-gpx-maps/img/backToCenter.png";
			controlUIcenter.style.cursor = 'pointer';
			controlUIcenter.title = lng.backToCenter;
			controlDiv.appendChild(controlUIcenter);

			// Setup the click event listeners
			google.maps.event.addDomListener(controlUIcenter, 'click', function(event) {
				map.setCenter(bounds.getCenter()); 
				map.fitBounds(bounds);
				controlDiv.removeChild(controlUIcenter);
				controlUIcenter = null;
				return false;			
			});		
		}

	});
	
	var graphh = jQuery('#hchart_' + params.targetId).css("height");
	
	if (graphDist != '' && (graphEle != '' || graphSpeed != '' || graphHr != '' || graphCad != '') && graphh != "0px")
	{

		var valLen = graphDist.length;
	
		var l_x;
		var l_y;
		var l_y_arr = [];
		
		if (unit=="1")
		{
			l_x = { suf : "mi", dec : 1 };
			l_y = { suf : "ft", dec : 0 };
		}
		else if (unit=="2")
		{
			l_x = { suf : "km", dec : 1 };
			l_y = { suf : "m", dec : 0 };
		}
		else
		{
			l_x = { suf : "m", dec : 0 };
			l_y = { suf : "m", dec : 0 };
		}
		
		var nn = 1111.1;
		var _nn = nn.toLocaleString();
		var _nnLen = _nn.length;
		var decPoint = _nn.substring(_nnLen - 2, _nnLen - 1);
		var thousandsSep = _nn.substring(1, 2);
		
		if (decPoint == "1")
			decPoint = ".";
			
		if (thousandsSep == "1")
			thousandsSep = "";		
			
		// define the options
		var hoptions = {
			chart: {
				renderTo: 'hchart_' + params.targetId,
				type: 'area'
			},
			title: {
				text: null
			},
			xAxis: {
				type: 'integer',
				//gridLineWidth: 1,
				//tickInterval: 1000,
				labels: {
					formatter: function() {
						return Highcharts.numberFormat(this.value, l_x.dec,decPoint,thousandsSep) + l_x.suf;
					}
				}
			},
			yAxis: [],
			legend: {
				align: 'center',
				verticalAlign: 'top',
				y: -5,
				floating: true,
				borderWidth: 0
			},
			tooltip: {
				shared: true,
				crosshairs: true,
				formatter: function() {
					if (marker)
					{
						var hchart_xserie = hchart.xAxis[0].series[0].data;				
						for(var i=0; i<hchart_xserie.length;i++){
							var item = hchart_xserie[i];
							if(item.x == this.x)
							{
								var point = getItemFromArray(mapData,i)
								if (point)
								{
									marker.setPosition(new google.maps.LatLng(point[0],point[1]));									
								}
								marker.setTitle(lng.currentPosition);
								i+=10000000;
							}
						}			
					}
					var tooltip = "<b>" + Highcharts.numberFormat(this.x, l_x.dec,decPoint,thousandsSep) + l_x.suf + "</b><br />"; 
					for (i=0; i < this.points.length; i++)
					{
						tooltip += this.points[i].series.name + ": " + Highcharts.numberFormat(this.points[i].y, l_y_arr[i].dec,decPoint,thousandsSep) + l_y_arr[i].suf + "<br />"; 					
					}
					return tooltip;
				}
			},
			plotOptions: {
				area: {
					fillOpacity: 0.1,
					connectNulls : true,
					marker: {
						enabled: false,
						symbol: 'circle',
						radius: 2,
						states: {
							hover: {
								enabled: true
							}
						}
					}					
				}
			},
			credits: {
				enabled: false
			},	
			series: []
		};
	
		if (graphEle != '')
		{
			
			var eleData = [];
			var myelemin = 99999;
			var myelemax = -99999;
	
			for (i=0; i<valLen; i++) 
			{
				if (graphDist[i] != null)
				{
					var _graphEle = graphEle[i];
					eleData.push([graphDist[i],_graphEle]);
					if (_graphEle > myelemax) 
						myelemax = _graphEle; 
					if (_graphEle < myelemin) 
						myelemin = _graphEle;
				}
			}

			var yaxe = { 
				title: { text: null },
				labels: {
					align: 'left',
					formatter: function() {
						return Highcharts.numberFormat(this.value, l_y.dec,decPoint,thousandsSep) + l_y.suf;
					}
				}
			}
	
			if ( chartFrom1 != '' )
			{
				yaxe.min = chartFrom1;
				yaxe.startOnTick = false;
			}
			else { 
				yaxe.min = myelemin; 
			}
			
			if ( chartTo1 != '' )
			{
				yaxe.max = chartTo1;
				yaxe.endOnTick = false;
			}
			else { 
				yaxe.max = myelemax; 
			}
								
			hoptions.yAxis.push(yaxe);
			hoptions.series.push({
									name: lng.altitude,
									lineWidth: 1,
									marker: { radius: 0 },
									data : eleData,
									color: color2,
									yAxis: hoptions.series.length
								});			
			
			l_y_arr.push(l_y);
		}
		
		if (graphSpeed != '')
		{
			
			var l_s;
			
			if (unitspeed == '5') // knos
			{
				l_s = { suf : "knos", dec : 2 };
			} 
			else if (unitspeed == '4') // min/miles
			{
				l_s = { suf : "min/mi", dec : 2 };
			} 
			else if (unitspeed == '3') // min/km
			{
				l_s = { suf : "min/km", dec : 2 };
			} 
			else if (unitspeed == '2') // miles/h
			{
				l_s = { suf : "mi/h", dec : 0 };
			} 
			else if (unitspeed == '1') // km/h
			{
				l_s = { suf : "km/h", dec : 0 };
			} 
			else
			{
				l_s = { suf : "m/s", dec : 0 };
			}
			
			var speedData = [];
		
			for (i=0; i<valLen; i++) 
			{
				if (graphDist[i] != null)
					speedData.push([graphDist[i],graphSpeed[i]]);
			}

			var yaxe = { 
				title: { text: null },
				labels: {
					//align: 'right',
					formatter: function() {
						return Highcharts.numberFormat(this.value, l_s.dec,decPoint,thousandsSep) + l_s.suf;
					}
				},
				opposite: true
			}
						
			if ( chartFrom2 != '' )
			{
				yaxe.min = chartFrom2;
				yaxe.startOnTick = false;				
			}
			
			if ( chartTo2 != '' )
			{
				yaxe.max = chartTo2;
				yaxe.endOnTick = false;				
			}
								
			hoptions.yAxis.push(yaxe);
			hoptions.series.push({
									name: lng.speed,
									lineWidth: 1,
									marker: { radius: 0 },
									data : speedData,
									color: color3,
									yAxis: hoptions.series.length
								});			
			
			l_y_arr.push(l_s);
		}
		
		if (graphHr != '')
		{
			
			var l_hr = { suf : "", dec : 0 };
			
			var hrData = [];
		
			for (i=0; i<valLen; i++) 
			{
				if (graphDist[i] != null)
				{
					var c = graphHr[i];
					if (c==0)
						c = null;
					hrData.push([graphDist[i],c]);				
				}
			}

			var yaxe = { 
				title: { text: null },
				labels: {
					//align: 'right',
					formatter: function() {
						return Highcharts.numberFormat(this.value, l_hr.dec,decPoint,thousandsSep) + l_hr.suf;
					}
				},
				opposite: true
			}
								
			hoptions.yAxis.push(yaxe);
			hoptions.series.push({
									name: lng.heartRate,
									lineWidth: 1,
									marker: { radius: 0 },
									data : hrData,
									color: color4,
									yAxis: hoptions.series.length
								});			
			
			l_y_arr.push(l_hr);
		}
		
		if (graphCad != '')
		{
			
			var l_cad = { suf : "", dec : 0 };
			
			var cadData = [];
		
			for (i=0; i<valLen; i++) 
			{
				if (graphDist[i] != null)
				{
					var c = graphCad[i];
					if (c==0)
						c = null;
					cadData.push([graphDist[i],c]);
				}
			}

			var yaxe = { 
				title: { text: null },
				labels: {
					//align: 'right',
					formatter: function() {
						return Highcharts.numberFormat(this.value, l_cad.dec,decPoint,thousandsSep) + l_cad.suf;
					}
				},
				opposite: true
			}
								
			hoptions.yAxis.push(yaxe);
			hoptions.series.push({
									name: lng.cadence,
									lineWidth: 1,
									marker: { radius: 0 },
									data : cadData,
									color: color5,
									yAxis: hoptions.series.length
								});			
			
			l_y_arr.push(l_cad);
		}

		if (graphGrade != '')
		{
			
			var l_grade = { suf : "%", dec : 1 };
			
			var cadData = [];
		
			for (i=0; i<valLen; i++) 
			{
				if (graphDist[i] != null)
				{
					var c = graphGrade[i];
					if (c==0)
						c = null;
					cadData.push([graphDist[i],c]);
				}
			}

			var yaxe = { 
				title: { text: null },
				labels: {
					//align: 'right',
					formatter: function() {
						return Highcharts.numberFormat(this.value, l_grade.dec,decPoint,thousandsSep) + l_grade.suf;
					}
				},
				opposite: true
			}
								
			hoptions.yAxis.push(yaxe);
			hoptions.series.push({
									name: lng.grade,
									lineWidth: 1,
									marker: { radius: 0 },
									data : cadData,
									color: color6,
									yAxis: hoptions.series.length
								});			
			
			l_y_arr.push(l_grade);
		}

		var hchart = new Highcharts.Chart(hoptions);
	
	}
	else  {
		jQuery("#hchart_" + params.targetId).css("display","none");
	}
	
}

function addWayPoint(map, image, shadow, lat, lon, title, descr)
{
	var p = new google.maps.LatLng(lat, lon);
	var m = new google.maps.Marker({
						  position: p,
						  map: map,
						  title: title,
						  animation: google.maps.Animation.DROP,
						  shadow: shadow,
						  icon: image,
						  zIndex: 5
					  });
	google.maps.event.addListener(m, 'mouseover', function() {
		if (infowindow)
		{
			infowindow.close(); 		
		}
		var cnt = '';	
		if (title=='')
		{
			cnt = "<div style='text-align:center;'>" + unescape(descr) + "</div>";
		}
		else
		{
			cnt = "<div style='font-size:0.8em; text-align:center;'><b>" + title + "</b><br />" + unescape(descr) + "</div>";
		}
		infowindow = new google.maps.InfoWindow({ content: cnt});
		infowindow.open(map,m);
	});	
}

function getItemFromArray(arr,index)
{
	try
	{
	  return arr[index];
	}
	catch(e)
	{
		return [0,0];
	}
}




function getClosestIndex(points,lat,lon)
{
	var dd=10000;
	var ii=0;
	for (i=0; i < points.length; i++) 
	{
		if (points[i]==null)
			continue;
	
		var d = dist(points[i][0], points[i][1], lat, lon);
		if ( d < dd )
		{
			ii = i;
			dd = d;
		}
	}
	return ii;
}

function getClosestImage(lat,lon,targetId)
{
	var dd=10000;
	var img;
	var divImages = document.getElementById("ngimages_"+targetId);
	var img_spans = divImages.getElementsByTagName("span");   
	for (var i = 0; i < img_spans.length; i++) {   
		var imageLat = img_spans[i].getAttribute("lat");
		var imageLon = img_spans[i].getAttribute("lon");	
		var d = dist(imageLat, imageLon, lat, lon);
		if ( d < dd )
		{
			img = img_spans[i];
			dd = d;
		}		
	}
	return img;
}

function isNumeric(input){
    var RE = /^-{0,1}\d*\.{0,1}\d+$/;
    return (RE.test(input));
}

function dist(lat1,lon1,lat2,lon2)
{
	// mathematically not correct but fast
	var dLat = (lat2-lat1);
	var dLon = (lon2-lon1);
	return Math.sqrt(dLat * dLat + dLon * dLon);
}