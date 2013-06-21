/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 * 
 * Javascript from http://toolserver.org/~cmarqu/map3.html~
 */
      function osm_getTileURL(bounds) {
        var res = this.map.getResolution();
        var x = Math.round((bounds.left - this.map.maxExtent.left) / (res * this.tileSize.w));
        var y = Math.round((this.map.maxExtent.top - bounds.top) / (res * this.tileSize.h));
        var z = this.map.getZoom();
        var limit = Math.pow(2, z);

        if (y < 0 || y >= limit) {
          return OpenLayers.Util.getImagesLocation() + "404.png";
          } else {
          x = ((x % limit) + limit) % limit;
          return this.url + z + "/" + x + "/" + y + " . " + this.type;
        }
      }

