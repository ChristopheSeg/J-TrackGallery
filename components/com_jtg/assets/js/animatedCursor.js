// ************************************************
// Animated cursor on track
// Design and javascript Code inspired from http://www.utagawavtt.com/

function hover_profil_graph(longitude, latitude, originalIdx)
{
	// enable moving cursor
	animatedCursorIcon.setVisibility(true); // for opera broken in Firefox
	animatedCursorIcon.opacity = 0.7; // for Firefox
	var lonlat = new OpenLayers.LonLat(longitude, latitude) . transform(new OpenLayers.Projection("EPSG:4326"), olmap.getProjectionObject());
	animatedCursorIcon.markers[0].lonlat = lonlat ;
	animatedCursorIcon.redraw();

	// ************************************************
	// highlight the track depending on the position
	// create the gpxHover feature if it doesn't exist yet
	if (!animatedCursorLayer.gpxHover) {
		animatedCursorLayer.gpxHover = animatedCursorLayer.gpxfeature.clone();
		animatedCursorLayer.gpxHover.fid = "gpxHover";
		animatedCursorLayer.gpxHover.style = OpenLayers.Util.applyDefaults({strokeWidth: 2, strokeColor: "#000000", graphicZIndex: 10}, animatedCursorLayer.gpxHover.style);
	}
	// cut or add point if needed
	if (animatedCursorLayer.gpxHover.geometry.components.length <= originalIdx) {
		// the displayed track is shorter than need, add points | slice stop before index so +1
		var lastPoint = originalIdx + 1 < animatedCursorLayer.gpxfeature.geometry.components.length ? originalIdx + 1 : animatedCursorLayer.gpxfeature.geometry.components.length;
		var pointsToAdd = animatedCursorLayer.gpxfeature.geometry.components.slice(animatedCursorLayer.gpxHover.geometry.components.length, lastPoint);
		animatedCursorLayer.gpxHover.geometry.components = animatedCursorLayer.gpxHover.geometry.components.concat(pointsToAdd);
	} else {
		// the displayed track is longer than needed, just cut it
		animatedCursorLayer.gpxHover.geometry.components.splice(originalIdx + 1, animatedCursorLayer.gpxHover.geometry.components.length - originalIdx - 1);
	}
	// draw / redraw
	if (animatedCursorLayer.getFeatureByFid("gpxHover") === null) {
		animatedCursorLayer.addFeatures(animatedCursorLayer.gpxHover);
	} else {
//		animatedCursorLayer.drawFeature(animatedCursorLayer.gpxHover);	  // bug : doesn't work when only a part of the track is visible
		animatedCursorLayer.redraw();
	}
	// ************************************************

	// if the point is out of the map, center on it
	if (animatedCursorIcon.markers.length > 0 && !animatedCursorIcon.markers[0].onScreen()) {
		olmap.setCenter(lonlat);
	}
}

function out_profil_graph()
{
	animatedCursorLayer.removeFeatures(animatedCursorLayer.gpxHover);
	animatedCursorLayer.redraw;

	animatedCursorIcon.opacity = 0; // for Firefox
	animatedCursorIcon.setVisibility(false);  // for opera broken in Firefox

}