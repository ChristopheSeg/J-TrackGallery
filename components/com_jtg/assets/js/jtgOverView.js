/*
 functions used to setup a clustered layer of marker with popup
 This is used in the overview of J!TrackGallery
*/
function addMarkers() {
	arrayOfMarkers = [];
	for (i=0; i<markers.length; i++)
	{
		ll = new OpenLayers.LonLat(markers[i].lon, markers[i].lat) . transform(new OpenLayers.Projection("EPSG:4326"), olmap.getProjectionObject());
		var f= new OpenLayers.Feature.Vector(
			new OpenLayers.Geometry.Point(ll.lon, ll.lat),
				{description:'Marker_'+i} ,
			markers[i].iconStyle
		);
		f.attributes = {
			description : markers[i].description,
			name : markers[i].link
		}
		arrayOfMarkers.push(f);
	}
}

// Clustering
//--------------------
//original source: http://acuriousanimal.com/code/animatedCluster/
function addClusteredLayerOfMarkers(){
            // Define three colors that will be used to style the cluster features
            // depending on the number of features they contain.
            var colors = {
                low: "rgb(255,153,51)",
                middle: "rgb(255,128,0)",
                high: "rgb(204,102,0)"
            };

            // Define three rules to style the cluster features.
            var lowRule = new OpenLayers.Rule({
                filter: new OpenLayers.Filter.Comparison({
                    type: OpenLayers.Filter.Comparison.BETWEEN,
                    property: "count",
                    lowerBoundary: 2,
                    upperBoundary: 5
                }),
                symbolizer: {
                    fillColor: colors.low,
                    fillOpacity: 0.9,
                    strokeColor: colors.low,
                    strokeOpacity: 0.5,
                    strokeWidth: 12,
                    pointRadius: 10,
                    label: "${count}",
                    labelOutlineWidth: 1,
                    fontColor: "#ffffff",
                    fontOpacity: 0.8,
                    fontSize: "12px"
                }
            });
            var middleRule = new OpenLayers.Rule({
                filter: new OpenLayers.Filter.Comparison({
                    type: OpenLayers.Filter.Comparison.BETWEEN,
                    property: "count",
                    lowerBoundary: 5,
                    upperBoundary: 20
                }),
                symbolizer: {
                    fillColor: colors.middle,
                    fillOpacity: 0.9,
                    strokeColor: colors.middle,
                    strokeOpacity: 0.5,
                    strokeWidth: 12,
                    pointRadius: 15,
                    label: "${count}",
                    labelOutlineWidth: 1,
                    fontColor: "#ffffff",
                    fontOpacity: 0.8,
                    fontSize: "12px"
                }
            });
            var highRule = new OpenLayers.Rule({
                filter: new OpenLayers.Filter.Comparison({
                    type: OpenLayers.Filter.Comparison.GREATER_THAN,
                    property: "count",
                    value: 20
                }),
                symbolizer: {
                    fillColor: colors.high,
                    fillOpacity: 0.9,
                    strokeColor: colors.high,
                    strokeOpacity: 0.5,
                    strokeWidth: 12,
                    pointRadius: 20,
                    label: "${count}",
                    labelOutlineWidth: 1,
                    fontColor: "#ffffff",
                    fontOpacity: 0.8,
                    fontSize: "12px"
                }
            });

            // Create a Style that uses the three previous rules
            var style = new OpenLayers.Style(null, {
                rules: [lowRule, middleRule, highRule]
            });

            // Create a vector layers
            var layerVectorForMarkers = new OpenLayers.Layer.Vector("Features", {
                renderers: ['Canvas','SVG'],
                displayInLayerSwitcher: false,
            strategies: [
			new OpenLayers.Strategy.Cluster({distance: 15, threshold: 2})],
                styleMap:  new OpenLayers.StyleMap(style)
            });

            // Create a vector layers and add markers
	addMarkers();
	olmap.addLayer(layerVectorForMarkers);
	layerVectorForMarkers.addFeatures(arrayOfMarkers);



	// Create control and add some layers
	// ----------------------------------
	// original source: https://github.com/jorix/OL-FeaturePopups

	var featurePopupControl = new OpenLayers.Control.FeaturePopups({
	    boxSelectionOptions: {},
	    layers: [
		[
		// Uses: Templates for hover & select and safe selection
		layerVectorForMarkers, {templates: {
		    // hover: single & list
		    hover: '${.name}',
		    hoverList: '${html}',
		    hoverItem: '${.name}<br>',
		    // select: single & list
		    single: '<div>${.name}${.description}</div>',
		    item: '<li>${.name}</li>',
		    list: '${html}'
		}}]
	    ]
	});
	// featurePopupControl.layerListTemplate: 'XXX${html}'
	olmap.addControl(featurePopupControl);

}


