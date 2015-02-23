/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/*
@Source : http://utagawa.free.fr
@Author : Jérémy Bouquain
*/

function switch_fullscreen2(){
    var ol_buttonFullScreen = olmap.getControlsBy("displayClass","buttonFullScreen")[0];
    var jtg_map = document.getElementById("jtg_map");
    var currentClass = jtg_map.className;

    if (currentClass == "olMap") { // map is in normal mode
	OpenLayers.Element.addClass(ol_buttonFullScreen.panel_div,"buttonFullScreenItemActive");
	OpenLayers.Element.removeClass(ol_buttonFullScreen.panel_div,"buttonFullScreenItemInactive");
        jtg_map.className = "fullscreen";
 	// new_zoom = olmap.getZoom()+1;
   }
else
{ // map is already in fullscreen mode
	OpenLayers.Element.addClass(ol_buttonFullScreen.panel_div,"buttonFullScreenItemInactive");
	OpenLayers.Element.removeClass(ol_buttonFullScreen.panel_div,"buttonFullScreenItemActive");
        jtg_map.className = "olMap";
 	// new_zoom = olmap.getZoom()-1;
   }

	olmap.updateSize();

	// SetTimeout("olmap.zoomTo(new_zoom);	", 1000);
}
function switch_fullscreen()
{
	var obj_map = document.getElementById("jtg_map");
	var ol_buttonFullScreen = olmap.getControlsBy("displayClass","buttonFullScreen")[0];
	//map_center = olmap.center;
	if (obj_map.style.position == "fixed")			// If map is already in fullscreen mode
	{
		OpenLayers.Element.addClass(ol_buttonFullScreen.panel_div,"buttonFullScreenItemInactive");
		OpenLayers.Element.removeClass(ol_buttonFullScreen.panel_div,"buttonFullScreenItemActive");

		// Reset normal olmap style from saved data
		obj_map.style.backgroundColor = obj_map.style.backgroundColor;

		obj_map.style.position = obj_map_style_position;
		obj_map.style.height = obj_map_style_height;
		obj_map.style.width = obj_map_style_width;
		obj_map.style.margin = obj_map_style_margin ;

		//setTimeout("document.getElementById(\"carteOL\").style.width = \"98%\";", 700);

		//obj_footer.innerHTML = obj_footer_save;

		obj_map.style.zIndex = 0;

		new_zoom = olmap.getZoom()-1;
	}
	else  // map is in normal screen
	{
		OpenLayers.Element.addClass(ol_buttonFullScreen.panel_div,"buttonFullScreenItemActive");
		OpenLayers.Element.removeClass(ol_buttonFullScreen.panel_div,"buttonFullScreenItemInactive");

		// Save normal olmap div style
		obj_map_style_backgroundColor = obj_map.style.backgroundColor ;
		obj_map_style_position = obj_map.style.position;
		obj_map_style_height = obj_map.style.height;
		obj_map_style_width = obj_map.style.width;
		obj_map_style_margin = obj_map.style.margin;

		obj_map.style.backgroundColor = "#D1D6BE";

		obj_map.style.position = "fixed";
		if (isIE)
		{
			var hY = document.documentElement.clientHeight;
			obj_map.style.height = hY + "px";
		}
		else
		{
			obj_map.style.height = "100%";
		}

		obj_map.style.width = "100%";

		obj_map.style.top = "0";
		obj_map.style.bottom = "0";
		obj_map.style.right = "0";
		obj_map.style.left = "0";
		obj_map.style.margin = "0px 0px";

		obj_map.style.zIndex = 20;

		new_zoom = olmap.getZoom()+1;

	}
	olmap.updateSize();
	//setTimeout("olmap.setCenter(map_center);", 1000);
	setTimeout("olmap.zoomTo(new_zoom);	", 1000);
}



