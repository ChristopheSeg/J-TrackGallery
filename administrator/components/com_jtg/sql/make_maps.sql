INSERT IGNORE INTO `#__jtg_maps` (
	`name`,
	`ordering`,
	`published`,
	`param`,
	`script`,
	`code`
) VALUES (
	'OSMMAP_MAPNIK',
	1,
	1,
	'OpenLayers.Layer.OSM.Mapnik(&quot;{name}&quot;)',
	'',
	''
);

INSERT IGNORE INTO `#__jtg_maps` (
	`name`,
	`ordering`,
	`published`,
	`param`,
	`script`,
	`code`
) VALUES (
	'OSMMAP_OSMARENDER',
	2,
	0,
	'OpenLayers.Layer.OSM.Osmarender(&quot;{name}&quot;)',
	'',
	''
);

INSERT IGNORE INTO `#__jtg_maps` (
	`name`,
	`ordering`,
	`published`,
	`param`,
	`script`,
	`code`
) VALUES (
	'OSMMAP_CYCLEMAP',
	3,
	0,
	'OpenLayers.Layer.OSM.CycleMap(&quot;{name}&quot;)',
	'',
	''
);

INSERT IGNORE INTO `#__jtg_maps` (
	`name`,
	`ordering`,
	`published`,
	`param`,
	`script`,
	`code`
) VALUES (
	'OSMMAP_NONAME',
	4,
	0,
	'OpenLayers.Layer.OSM(&quot;{name}&quot;, [ &quot;http://a.tile.cloudmade.com/&quot;+nonamekey+&quot;/3/256/\${z}/\${x}/\${y}.png&quot;, &quot;http://b.tile.cloudmade.com/&quot;+nonamekey+&quot;/3/256/\${z}/\${x}/\${y}.png&quot;, &quot;http://c.tile.cloudmade.com/&quot;+nonamekey+&quot;/3/256/\${z}/\${x}/\${y}.png&quot;], {displayOutsideMaxExtent: true, wrapDateLine: true, numZoomLevels: 19, layerCode: &quot;N&quot;})',
	'',
	'var nonamekey = &quot;PASTE_YOUR_KEY_HERE&quot;;'
);

INSERT IGNORE INTO `#__jtg_maps` (
	`name`,
	`ordering`,
	`published`,
	`param`,
	`script`,
	`code`
) VALUES (
	'OSMMAP_GOOGLESAT',
	5,
	0,
	'OpenLayers.Layer.Google(&quot;{name}&quot;,{type: G_SATELLITE_MAP, sphericalMercator: true})',
	'http://maps.google.com/maps?file=api&amp;v=2&amp;key=PASTE_YOUR_KEY_HERE',
	''
);

INSERT IGNORE INTO `#__jtg_maps` (
	`name`,
	`ordering`,
	`published`,
	`param`,
	`script`,
	`code`
) VALUES (
	'OSMMAP_GOOGLESREET',
	6,
	0,
	'OpenLayers.Layer.Google(&quot;{name}&quot;,{type: G_NORMAL_MAP, sphericalMercator: true})',
	'http://maps.google.com/maps?file=api&amp;v=2&amp;key=PASTE_YOUR_KEY_HERE',
	''
);

INSERT IGNORE INTO `#__jtg_maps` (
	`name`,
	`ordering`,
	`published`,
	`param`,
	`script`,
	`code`
) VALUES (
	'OSMMAP_GOOGLEHYBRID',
	7,
	0,
	'OpenLayers.Layer.Google(&quot;{name}&quot;,{type: G_HYBRID_MAP, sphericalMercator: true})',
	'http://maps.google.com/maps?file=api&amp;v=2&amp;key=PASTE_YOUR_KEY_HERE',
	''
);

INSERT IGNORE INTO `#__jtg_maps` (
	`name`,
	`ordering`,
	`published`,
	`param`,
	`script`,
	`code`
) VALUES (
	'OSMMAP_YAHOOSTREET',
	8,
	0,
	'OpenLayers.Layer.Yahoo(&quot;{name}&quot;,{&quot;sphericalMercator&quot;: true})',
	'http://api.maps.yahoo.com/ajaxymap?v=3.0&amp;appid=PASTE_YOUR_KEY_HERE',
	''
);

INSERT IGNORE INTO `#__jtg_maps` (
	`name`,
	`ordering`,
	`published`,
	`param`,
	`script`,
	`code`
) VALUES (
	'OSMMAP_VE_AERIAL',
	9,
	1,
	'OpenLayers.Layer.VirtualEarth(&quot;{name}&quot;,{type: VEMapStyle.Aerial,&quot;sphericalMercator&quot; : true,animationEnabled: false})',
	'http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.3',
	''
);

INSERT IGNORE INTO `#__jtg_maps` (
	`name`,
	`ordering`,
	`published`,
	`param`,
	`script`,
	`code`
) VALUES (
	'OSMMAP_VE_ROAD',
	10,
	0,
	'OpenLayers.Layer.VirtualEarth(&quot;{name}&quot;,{type: VEMapStyle.Road,&quot;sphericalMercator&quot; : true,animationEnabled: false})',
	'http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.3',
	''
);

INSERT IGNORE INTO `#__jtg_maps` (
	`name`,
	`ordering`,
	`published`,
	`param`,
	`script`,
	`code`
) VALUES (
	'OSMMAP_VE_HYBRID',
	11,
	0,
	'OpenLayers.Layer.VirtualEarth(&quot;{name}&quot;,{type: VEMapStyle.Hybrid,&quot;sphericalMercator&quot; : true,animationEnabled: false})',
	'http://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.3',
	''
);

INSERT IGNORE INTO `#__jtg_maps` (
	`name`,
	`ordering`,
	`published`,
	`param`,
	`script`,
	`code`
) VALUES (
	'Own Server',
	99,
	0,
	'OpenLayers.Layer.OSM(&quot;{name}&quot;, &quot;http://server/\${z}/\${x}/\${y}.png&quot; ,{ &quot;type&quot;:&quot;png&quot;})',
	'',
	''
);
