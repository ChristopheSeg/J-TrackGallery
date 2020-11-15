/*
 *   Some definitions that used by the for the IGN geoportail maps from 
 *        the French national geographic institute
 *   Taken from the example code: https://openlayers.org/en/latest/examples/wmts-ign.html
 *
 *   For information about apiKeys: https://geoservices.ign.fr/blog/2017/06/28/geoportail_sans_compte.html
 */

function getIGNTileGrid() {
  var resolutions = [];
  var matrixIds = [];
  var maxResolution = ol.extent.getWidth(ol.proj.get('EPSG:3857').getExtent()) / 256;

  for (var i = 0; i < 18; i++) {
    matrixIds[i] = i.toString();
    resolutions[i] = maxResolution / Math.pow(2, i);
  }

  return new WMTSTileGrid({
    origin: [-20037508, 20037508],
    resolutions: resolutions,
    matrixIds: matrixIds
  });
}
