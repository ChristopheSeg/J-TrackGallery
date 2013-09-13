--
-- TODO Obsolete: this file might be deleted (now included in install.sql)
--

INSERT IGNORE INTO `#__jtg_maps` (`name`, `ordering`, `published`, `param`, `script`, `code`) VALUES
('COM_JTG_MAP_MAPNIK', 1, 1, 'OpenLayers.Layer.OSM.Mapnik("{name}")', '', ''),
('COM_JTG_MAP_OSM_HIKE_AND_BIKE', 2, 1, 'OpenLayers.Layer.TMS("{name}", "http://toolserver.org/tiles/hikebike/", { type: "png", getURL: osm_getTileURL, displayOutsideMaxExtent: true, isBaseLayer: true, attribution: "Map Data © OpenStreetMap contributors", transitionEffect: "resize" })', '', ''),
('COM_JTG_MAP_CYCLEMAP', 3, 1, 'OpenLayers.Layer.OSM.CycleMap("{name}")', '', ''),
('COM_JTG_MAP_NONAME', 4, 0, 'OpenLayers.Layer.OSM("{name}", [ "http://a.tile.cloudmade.com/"+nonamekey+"/3/256/${z}/${x}/${y}.png", "http://b.tile.cloudmade.com/"+nonamekey+"/3/256/${z}/${x}/${y}.png", "http://c.tile.cloudmade.com/"+nonamekey+"/3/256/${z}/${x}/${y}.png"], {displayOutsideMaxExtent: true, wrapDateLine: true, numZoomLevels: 19, layerCode: "N"})', '', 'var nonamekey = &quot;PASTE_YOUR_KEY_HERE&quot;;'),
('COM_JTG_MAP_GOOGLESAT', 5, 1, 'OpenLayers.Layer.Google( "{name}", {type: google.maps.MapTypeId.SATELLITE, numZoomLevels: 22})', 'http://maps.google.com/maps/api/js?v=3&amp;sensor=false', ''),
('COM_JTG_MAP_GOOGLESTREET', 6, 1, 'OpenLayers.Layer.Google( "{name}",  {numZoomLevels: 20})', 'http://maps.google.com/maps/api/js?v=3&amp;sensor=false', ''),
('COM_JTG_MAP_GOOGLEHYBRID', 7, 1, 'OpenLayers.Layer.Google( "{name}",  {type: google.maps.MapTypeId.HYBRID, numZoomLevels: 20})', 'http://maps.google.com/maps/api/js?v=3&amp;sensor=false', ''),
('COM_JTG_MAP_GOOGLEPHYSICAL', 8, 1, 'OpenLayers.Layer.Google("{name}", {type: google.maps.MapTypeId.TERRAIN} )', 'http://maps.google.com/maps/api/js?v=3&amp;sensor=false', ''),
('COM_JTG_MAP_VE_AERIAL', 9, 1, 'OpenLayers.Layer.Bing({ name: "{name}", key: BingApiKey, type: "Aerial", wrapDateLine: true })', '', 'var BingApiKey =  &quot;AqTGBsziZHIJYYxgivLBf0hVdrAk9mWO5cQcb8Yux8sW5M8c8opEC2lZqKR1ZZXf&quot;;'),
('COM_JTG_MAP_VE_ROAD', 10, 1, 'OpenLayers.Layer.Bing({ name: "{name}", key: BingApiKey, type: "Road", wrapDateLine: true })', '', 'var BingApiKey = &quot;AqTGBsziZHIJYYxgivLBf0hVdrAk9mWO5cQcb8Yux8sW5M8c8opEC2lZqKR1ZZXf&quot;;'),
('COM_JTG_MAP_VE_HYBRID', 11, 1, 'OpenLayers.Layer.Bing({ name: "{name}", key: BingApiKey, type: "AerialWithLabels", wrapDateLine: true })', '', 'var BingApiKey = &quot;AqTGBsziZHIJYYxgivLBf0hVdrAk9mWO5cQcb8Yux8sW5M8c8opEC2lZqKR1ZZXf&quot;;'),
('Own Server', 99, 0, 'OpenLayers.Layer.OSM(&quot;{name}&quot;, &quot;http://server/${z}/${x}/${y}.png&quot; ,{ &quot;type&quot;:&quot;png&quot;})', '', '');
