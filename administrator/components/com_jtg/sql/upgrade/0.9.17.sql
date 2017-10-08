UPDATE `#__jtg_maps` SET param ='OpenLayers.Layer.XYZ("{name}", "http://a.tiles.wmflabs.org/hikebike/${z}/${x}/${y}.png",{displayOutsideMaxExtent: true, isBaseLayer: true, attribution: "Map Data © <a href=http://www.openstreetmap.org/copyright>OpenStreetMap</a> contributors", transitionEffect: "resize" })' WHERE param like '%OpenLayers.Layer.TMS("{name}", "http://toolserver.org/tiles/hikebike/", { type: "png", getURL: osm_getTileURL, displayOutsideMaxExtent: true, isBaseLayer: true, attribution: "Map Data © OpenStreetMap contributors", transitionEffect: "resize" })' ;
INSERT IGNORE INTO `#__jtg_maps` (`name`, `published`, `ordering`, `param`, `script`, `code`) VALUES
('COM_JTG_MAP_HIKE_AND_BIKE_HILLSHADE', 1, 15, 'OpenLayers.Layer.XYZ("{name}", "http://a.tiles.wmflabs.org/hillshading/${z}/${x}/${y}.png",{displayOutsideMaxExtent: true, isBaseLayer: false, visibility: false, attribution: "Hillshading: SRTM3 v2 (<a href=http://www2.jpl.nasa.gov/srtm/>NASA</a>)" , transitionEffect: "resize" })', '', '');
