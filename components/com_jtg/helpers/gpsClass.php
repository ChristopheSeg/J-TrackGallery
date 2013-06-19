<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: gpsClass.php,v 1.3 2011/04/14 06:19:09 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage frontend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Mainclass to write the map
 */
class gpsClass {

	var $gpsFile = NULL;
	var $sortedcats = NULL;

	public function parseCatIcon($catid,$istrack=0,$iswp=0,$isroute=0) {
		$catid = explode(",",$catid);
		$catid = $catid[0];
		$cfg =& jtgHelper::getConfig();
		$iconpath = JURI::root()."components/com_jtg/assets/template/".$cfg->template."/images/";
		$catimage = false;
		$cats = $this->getCats();
		foreach ( $cats AS $cat ) {
			if ( $cat->id == $catid ) {
				if ( $cat->image ) {
					$catimage = $cat->image;
					break;
				}
			}
		}
		$marker = "";

		if ( $catimage !== false ) {
			$catimage = "images".DS."jtg".DS."cats".DS.$catimage;
			if ( is_file($catimage) ) {
				$simagesize = getimagesize($catimage);
				$sizex = $simagesize[0];
				$sizey = $simagesize[1];
				$maximagesize = 26;
				if ( ( $sizex > $maximagesize ) OR ( $sizey > $maximagesize) ) {
					$oldsizex = $sizex;
					$oldsizey = $sizey;
					$ratio = $sizex / $sizey;
					if ( $ratio > 1 ) {
						// Pic is letterbox
						$sizex = $maximagesize;
						$sizey = $sizex / $ratio;
					} else {
						// Pic is upright
						$sizey = $maximagesize;
						$sizex = $sizey * $ratio;
					}

				}
				$offsetx = round(-($sizex/2));
				$offsety = round(-($sizey/2));
				$marker .= "var size = new OpenLayers.Size(".$sizex.", ".$sizey."); ";
				$marker .= "var offset = new OpenLayers.Pixel(".$offsetx.", ".$offsety."); ";
				$marker .= "var file = '".JURI::base().$catimage."'; ";
				$marker .= "var icon = new OpenLayers.Icon(file,size,offset);";
				$marker .= "addMarker(ll, popupClass, popupContentHTML, true, true, icon);\n";
				return $marker;
			}
		}
		if ( $istrack == "1" )
		$marker .= "addMarker(ll, popupClass, popupContentHTML, true, true );\n";
		else {
			// ToDo: Typeigenes Icon (Track, WP, Route)
			$hddpath = JPATH_SITE.DS."components".DS."com_jtg".DS."assets".DS."template".DS.$cfg->template.DS."images".DS;
			$wpcoords = simplexml_load_file($hddpath."unknown_Cat_wp.xml");
			$marker .= "var size = new OpenLayers.Size(".$wpcoords->sizex.", ".$wpcoords->sizey."); ";
			$marker .= "var offset = new OpenLayers.Pixel(".$wpcoords->offsetx.", ".$wpcoords->offsety."); ";
			$marker .= "var file = '".$iconpath."unknown_Cat_wp.png';";
			$marker .= "var icon = new OpenLayers.Icon(file,size,offset);";
			$marker .= "addMarker(ll, popupClass, popupContentHTML, true, true, icon);\n";
			// 		$marker .= "addMarker(ll, popupClass, popupContentHTML, true, true, ";
			// 		$marker .= "'components/com_jtg/assets/images/symbols/Pin Green.png');\n";
		}
		return $marker;
	}

	/**
	 * Checkt auf WPs
	 * @return (int) Anzahl
	 */
	public function parseOwnIcon($ownicon=false) {
		$cfg =& jtgHelper::getConfig();
		$Tpath = JPATH_SITE.DS."components".DS."com_jtg".DS."assets".DS."template".DS.$cfg->template.DS."images".DS;
		$Tbase = JURI::root()."components/com_jtg/assets/template/".$cfg->template."/images/";
		$unknownicon = "";
		$jpath = JPATH_SITE.DS."components".DS."com_jtg".DS."assets".DS."images".DS."symbols".DS;
		$jbase = JURI::root()."components/com_jtg/assets/images/symbols/";

		$filename = JFile::makeSafe($ownicon);
		$pngfile = $jbase.$filename.".png";
		$xmlfile = $jpath.$filename.".xml";
		if ( ( $ownicon == false ) OR (!is_file($jpath.$filename.".png")) ) {
			if ((!JFile::exists($xmlfile)) AND (is_writable($jpath))) {
				// Vorlage zur Erstellung unbekannter Icons
				$xmlcontent = "<xml>\n	<sizex>16</sizex>\n	<sizey>16</sizey>\n	<offsetx>8</offsetx>\n	<offsety>8</offsety>\n</xml>\n<!--\nUm dieses Icon verfügbar zu machen, erstelle dieses Bild: \"".$filename.".png\",\nund vervollständige obige 4 Parameter.\n\"offsetx\" beschreibt die Anzahl der Pixel von links bis zum Punkt (negativ) und\n\"offsety\" beschreibt die Anzahl der Pixel von oben bis zum Punkt (ebenfalls negativ).\nMit \"Punkt\" ist der Punkt gemeint, der auf der Koordinate sitzt.\n-->\n";
				JFile::write($xmlfile,$xmlcontent);
				JPath::setPermissions($xmlfile,"0666");
			}
			// Standardicon
			$pngfile = $Tbase."unknown_WP.png";
			$xmlfile = $Tpath."unknown_WP.xml";
			$unknownicon = "// Unknown Icon: \"".$jpath.$ownicon.".png\"\n";
		}
		$icon = $pngfile;
		$this->gpsFile = $xmlfile;
		$xml = $this->loadFile();
		$sizex = $xml->sizex;
		$sizey = $xml->sizey;
		$offsetx = $xml->offsetx;
		$offsety = $xml->offsety;
		return $unknownicon."var icon = new OpenLayers.Icon('".$icon."',\n			new OpenLayers.Size(".$sizex.", ".$sizey."),\n			new OpenLayers.Pixel(".$offsetx.", ".$offsety."));\n";

	}

	public function isGeocache($wp) {
		if ( ( isset($wp->sym) ) AND ( preg_match('/Geocache/', $wp->sym) ) AND ( isset($wp->type) ) )
		return true;
		return false;
	}

	public function hasURL($wp) {
		if ( ( isset($wp->url) ) AND ( isset($wp->urlname) ) )
		return true;
		return false;
	}
	/**
	 * Checkt auf WPs
	 * @return (int) Anzahl
	 */
	public function parseWPs($wps) {
		if( $wps == false) return false;
		$wp = "// <!-- parseWPs BEGIN -->\n";
		$wp .= "wps = new OpenLayers.Layer.Markers(\"".JText::_('COM_JTG_WAYPOINTS')."\");";
		$wp .= "map.addLayer(wps);";
		$wp .= "addWPs();";
		$wp .= "function addWPs() {\n";
		foreach($wps as $key => $value){
			$lonlat = $value->attributes();
			$lon = $lonlat['lon'];
			$lat = $lonlat['lat'];
			$replace = array("
","'");
			$with = array("<br />","\'");
			$hasURL = $this->hasURL($value);
			$isGeocache = $this->isGeocache($value);
			if($hasURL)
			$URL = " <a href=\"".$value->url."\" target=\"_blank\">".
			trim(str_replace($replace,$with,$value->urlname))."</a>";		// URL
			else
			$URL = "";
			$name = trim(str_replace($replace,$with,$value->name));		// Name
			$cmt = trim(str_replace($replace,$with,$value->cmt));		// ?
			$desc = trim(str_replace($replace,$with,$value->desc));		// Beschreibung
			$ele = (float)$value->ele;	// Höhe
			if ($isGeocache)			// Symbol
			$sym = (string) $value->type;
			else
			$sym = $value->sym;
			$wp .= "llwp = new OpenLayers.LonLat(".$lon.",".$lat.").transform(new OpenLayers.";
			$wp .= "Projection(\"EPSG:4326\"), map.getProjectionObject());\n";
			$wp .= "popupClasswp = AutoSizeAnchored;\n";
			$wp .= "popupContentHTMLwp = '<b>".JText::_('COM_JTG_NAME').":</b> ".$name.$URL."<br /><small>";
			if ($desc) $wp .= "<b>".JText::_('COM_JTG_DESCRIPTION').":</b> ".$desc;
			if ( ($cmt) AND ($desc != $cmt) ) $wp .= "<br /><b>".JText::_('COM_JTG_COMMENT').":</b> ".$cmt;
			if ($ele) $wp .= "<br /><b>".JText::_('COM_JTG_ELE')." :</b> ca. ".round($ele,1)."m<small>";
			$wp .= "';\n";
			$wp .= $this->parseOwnIcon($sym);
			$wp .= "addWP(llwp, popupClasswp, popupContentHTMLwp, true, true, icon);\n";
		}
		$wp .= "	}\n";
		//	$wp .= "	//
		//	* Function: addWP
		//	* Add a new marker to the markers layer given the following lonlat,
		//	*	 popupClass, and popup contents HTML. Also allow specifying
		//	*	 whether or not to give the popup a close box.
		//	*
		//	* Parameters:
		//	* ll - {<OpenLayers.LonLat>} Where to place the marker
		//	* popupClass - {<OpenLayers.Class>} Which class of popup to bring up
		//	*	 when the marker is clicked.
		//	* popupContentHTML - {String} What to put in the popup
		//	* closeBox - {Boolean} Should popup have a close box?
		//	* overflow - {Boolean} Let the popup overflow scrollbars?
		//	*/

		$wp .= "	function addWP(ll, popupClass, popupContentHTML, closeBox, overflow, icon) {
		var feature = new OpenLayers.Feature(wps, ll);
		feature.closeBox = closeBox;
		feature.popupClass = popupClass;
		feature.data.popupContentHTML = popupContentHTML;
		feature.data.overflow = (overflow) ? \"auto\" : \"hidden\";
		var wp = new OpenLayers.Marker(ll,icon);
		wp.feature = feature;
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
		wp.events.register(\"mousedown\", feature, markerClick);
		wps.addMarker(wp);
	}\n";
		$wp .= "// <!-- parseWPs END -->\n";
		return $wp;
	}

	/**
	 * Checkt auf WPs
	 * @return (int) Anzahl
	 */
	public function extractWPs($xml) {
		$bbox_lat_max = -90;
		$bbox_lat_min = 90;
		$bbox_lon_max = -180;
		$bbox_lon_min = 180;

		if($xml->wpt) {
			$i = 0;
			$wp = array();
			while (true) {
				if($xml->wpt[$i])
				{
					$lat = (float)($xml->wpt[$i]->attributes()->lat);
					$lon = (float)($xml->wpt[$i]->attributes()->lon);
					if ( $lat > $bbox_lat_max ) $bbox_lat_max = $lat;
					if ( $lat < $bbox_lat_min ) $bbox_lat_min = $lat;
					if ( $lon > $bbox_lon_max ) $bbox_lon_max = $lon;
					if ( $lon < $bbox_lon_min ) $bbox_lon_min = $lon;
					$wp[] = $xml->wpt[$i];
				}
				else
				break;
				$i++;
			}
		} else return false;
		$center = "// <!-- parseOSMMapCenterSingleTrack BEGIN -->\n";
		$center .= "var min = lonLatToMercator(new OpenLayers.LonLat";
		$center .= "(".$bbox_lon_min.",".$bbox_lat_min."));\n";
		$center .= "var max = lonLatToMercator(new OpenLayers.LonLat";
		$center .= "(".$bbox_lon_max.",".$bbox_lat_max."));\n";
		$center .= "map.zoomToExtent(new OpenLayers.Bounds(min.lon, min.lat, max.lon, max.lat));\n";
		$center .= "// <!-- parseOSMMapCenterSingleTrack END -->\n";
		return array( "wps" => $wp, "center" => $center );
	}
	/**
	 * Formatiert die Beschreibung und kürzt bei Bedarf
	 * @return string
	 */
	public function showDesc($desc) {
		$stringlength = 200;
		$maxslperrow = 50;

		// Trennung nach <p>Katitel</p> BEGIN
		$desc = str_replace('</p>',"",$desc);
		$desc = explode('<p>',$desc);
		$newdesc = "";
		$count_letters = 0;
		$return = "";
		foreach ( $desc AS $chapter ) {
			if ( $chapter != "" ) {
				$chapter = trim($chapter);
				// 	Trennung nach Wörter BEGIN
				$words = explode(' ',$chapter);
				$return .= "<p>";
				$rowlen = 0;
				foreach($words AS $word) {
					$count_letters = ( $count_letters + strlen($word) +1 ); // "1" wegen der Leerstelle
					// 		Einfügung von Zeilensprung BEGIN
					$rowlen = ( $rowlen + strlen($word) );
					if ( $rowlen > $maxslperrow ) {
						$return = trim($return)."<br />";
						$rowlen = 0;
					}
					if ( ( $count_letters + strlen($word) ) > $stringlength )
					return $return."[...]</p>";
					// 		Einfügung von Zeilensprung END
					$return .= $word." ";
				}
				$return = trim($return)."</p>";
				// 	Trennung nach Wörter END
				$newdesc[] = $chapter;
			}
		}
		// Trennung nach <p>Katitel</p> END

		if ( $count_letters == 0 )
		return "<p>".JText::_('COM_JTG_NO_DESC')."</p>";
		return $return;
	}

	/**
	 *
	 * @return array()
	 */
	public function maySee($rows) {
		if(!$rows) return false;
		$user = JFactory::getUser();
		$return = array();
		foreach ( $rows AS $row ) {
			if
			(
			( (int)$row->published )
			AND
			(
			( !$row->access ) // public
			OR
			(
			( $row->access ) // not public = registred or special
			AND
			(
			( isset( $user->userid ) AND ( $user->userid ) )
			OR
			( isset( $user->id ) AND ( $user->id ) )
			)
			)
			)
			) {
				$return[] = $row;
			}
		}
		return $return;
	}

	/**
	 * Löscht den aktuellen Track aus der
	 * Gesamtansicht
	 *
	 * @return array()
	 */
	public function deleteTrack($rows,$track) {
		
		foreach ( $track AS $key => $value ) {
			$trackid = $value;	// Track-ID herausfinden und Schleife verlassen
			break;
		}
		$return = array();
		foreach ( $rows AS $key => $value ) {
			foreach ( $value AS $key_b => $value_b ) {
				if ( $value_b != $trackid )
				$store = true;
				else
				$store = false;
				break;
			}
			if ( $store == true ) $return[] = $value;
		}
		return $return;
	}

	/**
	 *
	 */
	public function transformTtRGB($t) {
		if ($t <= 60) {
			$r = dechex(255);
			$g = dechex(round($t*4.25));
			$b = dechex(0);
		}elseif ($t <= 120) {
			$r = dechex(round(255-(($t-60)*4.25)));
			$g = dechex(255);
			$b = dechex(0);
		}elseif ($t <= 180) {
			$r = dechex(0);
			$g = dechex(255);
			$b = dechex(round((($t-120)*4.25)));
		}elseif ($t <= 240) {
			$r = dechex(0);
			$g = dechex(round(255-(($t-180)*4.25)));
			$b = dechex(255);
		}elseif ($t <= 300) {
			$r = dechex(round((($t-240)*4.25)));
			$g = dechex(0);
			$b = dechex(255);
		}elseif ($t < 360) {
			$r = dechex(255);
			$g = dechex(0);
			$b = dechex(round(255-(($t-300)*4.25)));
		}elseif ($t >= 360)
		return false;
		if (strlen($r)==1) $r = (string)"0".$r;
		if (strlen($g)==1) $g = (string)"0".$g;
		if (strlen($b)==1) $b = (string)"0".$b;
		return $r.$g.$b;
	}

	/**
	 *
	 */
	public function calculateAllColors($count) {
		$color = array();
		for($i=1;$i<=$count;$i++){
			$color[($i-1)] = $this->transformTtRGB(round(300/$count*$i));
		}
		return $color;
	}

	/**
	 *
	 * @return color (#000000 - #ffffff) or own wish
	 */
	public function getHexColor($wish=false) {
		if ($wish !== false) return $wish;
		$color = "";
		for($i=0;$i<3;$i++) {
			$dec = (int)rand(16,128);
			$color .= dechex($dec);
		}
		return ("#".$color);
	}

	/**
	 *
	 * @return object
	 */
	public function loadFile() {

		if (file_exists($this->gpsFile)) {
			$xml = simplexml_load_file($this->gpsFile);
			return $xml;
		} else {
			return false;
		}
	}

	/**
	 *
	 * @return array
	 */
	public function getStartCoordinates() {
		jimport('joomla.filesystem.file');

		if(JFile::getExt($this->gpsFile) == 'kml'):
		$start = $this->getStartKML($this->gpsFile);
		endif;

		if(JFile::getExt($this->gpsFile) == 'gpx'):
		$start = $this->getStartGPX();
		endif;

		if(JFile::getExt($this->gpsFile) == 'tcx'):
		$start = $this->getStartTCX();
		endif;

		return $start;

	}

	/**
	 *
	 * @return array
	 */
	private function getStartKML($file) {

		$coords = $this->getCoords($file);

		$start = $coords[0];

		return $start;

	}

	/**
	 *
	 * @return number or false
	 */
	public function isTrack($file = false) {
		if ($file == false)
		$file = $this->loadFile();
		$xml = $file;
		if(!$xml->trk) return false;
		for($i=0;$i<10;$i++) { // Unternehme 10 Versuche LonLat zu finden
			$trackpoint = @$xml->trk[$i]->trkseg->trkpt;
			if (!empty($trackpoint))
			return (int)$i;
		}
		return false;
	}

	/**
	 *
	 * @return number or false
	 */
	public function isCache($file = false) {
		if ($file == false)
		$file = $this->loadFile();
		$xml = $file;
		$pattern = "/groundspeak/";
		if ( preg_match($pattern,$xml->attributes()->creator))
		return true;
		else
		return false;
	}

	/**
	 *
	 * @return array
	 */
	public function isWaypoint($file = false) {
		if ($file == false)
		$file = $this->loadFile();
		$xml = $file;
		for($i=0;$i<10;$i++) { // Unternehme 10 Versuche LonLat zu finden
			$trackpoint = $xml->wpt[$i];
			if (!empty($trackpoint))
			return true;
			//			return (int)$i;
		}
		return false;
	}

	/**
	 *
	 * @return array
	 */
	private function getStartGPX() {
		$isTrack = $this->isTrack();
		$xml = $this->loadFile();
		if ($isTrack !== false) {
			$trackpoint = $xml->trk[$isTrack]->trkseg->trkpt;
			$startpoint = $trackpoint->attributes();
			$start = array((string)$startpoint['lon'],(string)$startpoint['lat']);
			return $start;
		}
		$isWaypoint = $this->isWaypoint();
		if ($isWaypoint !== false) {
			$trackpoint = $xml->wpt;
			$startpoint = $trackpoint->attributes();
			$start = array((string)$startpoint['lon'],(string)$startpoint['lat']);
			return $start;
		}
		return false;
	}

	private function getStartTCX() {
		$xml = $this->loadFile();

		if(isset($xml->Activities->Activity->Lap->Track)) {
			$startpoint = $xml->Activities->Activity->Lap->Track[0]->Trackpoint;
		} elseif (isset($xml->Courses->Course->Track)) {
			$startpoint = $xml->Courses->Course->Track[0]->Trackpoint;
		}

		$lat = $startpoint->Position->LatitudeDegrees;
		$lon = $startpoint->Position->LongitudeDegrees;

		$start = array((string)$lon,(string)$lat);

		return $start;
	}

	/**
	 *
	 * @global object $mainframe
	 * @return array
	 */
	function getMapNates() {
		$mainframe =& JFactory::getApplication();

		$db =& JFactory::getDBO();

		$query = "SELECT start_n FROM #__jtg_files"
		. "\n ORDER BY start_n ASC"
		. "\n LIMIT 1";

		$db->setQuery($query);
		$north = $db->loadResult();

		$query = "SELECT start_e FROM #__jtg_files"
		. "\n ORDER BY start_e DESC"
		. "\n LIMIT 1";

		$db->setQuery($query);
		$east = $db->loadResult();

		$cnates = array();
		$cnates[] = $north;
		$cnates[] = $east;

		return $cnates;

	}

	/**
	 *
	 * @global object $mainframe
	 * @return array
	 */
	function getTracks($where="") {
		$mainframe =& JFactory::getApplication();

		$db = JFactory::getDBO();

		$query = "\nSELECT a.*, b.title AS cat FROM #__jtg_files AS a"
		. "\n LEFT JOIN #__jtg_cats AS b"
		. "\n ON a.catid=b.id".$where
		;
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		return $rows;
	}

	/**
	 *
	 * @global object $mainframe
	 * @return array
	 */
	function getCats() {
		$mainframe =& JFactory::getApplication();

		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__jtg_cats"
		;

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	/**
	 *
	 * @param string $url
	 * @return string
	 */
	private function parseKml($url)
	{
		if ($url)
		{
			$string = "var geoXml = new GGeoXml(\"$url\");\n
		map.addOverlay(geoXml);\n";
			return $string;
		}
		else
		{
			return true;
		}
	}

	/**
	 * counts the distance of a track
	 * $koords look like this: $array($point1(array(lat,lon)),$point2(array(lat,lon)))...
	 *
	 * @param array $koord
	 * @return int kilometers
	 */
	public function getDistance($koord) {
		if (!is_array($koord))
		return false;
		else {

			$ent = 0;
			$welt = 6378.137; // Erdradius, ca. Angabe
			//		$last = array_pop($koord);
			//		What is this for?? It doesn't work if only 2 coordinates given (homeposition)
			foreach($koord as $key => $fetch)
			{
				if (isset($koord[$key + 1]))
				{
					$erste_breite = $koord[$key][1]; // lat
					$erste_laenge = $koord[$key][0]; // lon
					$erste_breite_rad = deg2rad($erste_breite);
					$erste_laenge_rad = deg2rad($erste_laenge);

					$zweite_breite = $koord[$key + 1][1]; // lat
					$zweite_laenge = $koord[$key + 1][0]; // lon
					$zweite_breite_rad = deg2rad($zweite_breite);
					$zweite_laenge_rad = deg2rad($zweite_laenge);

					$dis = acos(
					(sin($erste_breite_rad) * sin($zweite_breite_rad)) +
					(cos($erste_breite_rad) * cos($zweite_breite_rad) *
					cos($zweite_laenge_rad - $erste_laenge_rad))) * $welt;

					if(is_nan($dis))
					$ent = $ent;
					else
					$ent = $ent + $dis;
				}
			}
			$entfernung = $ent;
			$entfernung = round($entfernung, 2);
			return $entfernung;
		}
	}

	/**
	 *
	 * @param array $coords
	 * @return array
	 */
	public function getElevation($coords) {
		$asc = 0;
		$desc = 0;
		//		for($i=0, $n=(count($coords)-1); $i<$n; $i++) {
		//			if($coords[$i][2] < $coords[$i+1][2])
		//			$asc = $asc + ($coords[$i+1][2] - $coords[$i][2]);
		//		}
		//
		//		for($i=0, $n=(count($coords)-1); $i<$n-1; $i++) {
		//			if($coords[$i][2] > $coords[$i+1][2])
		//			$desc = $desc + ($coords[$i][2] - $coords[$i+1][2]);
		//		}

		for($i=0, $n=(count($coords)-1); $i<$n; $i++) {
			if($coords[$i][2] < $coords[$i+1][2])
			$asc = $asc + ($coords[$i+1][2] - $coords[$i][2]);
			if($coords[$i][2] > $coords[$i+1][2])
			$desc = $desc + ($coords[$i][2] - $coords[$i+1][2]);
		}

		$ele = array(round($asc,0),round($desc,0));

		return $ele;
	}

	/**
	 *
	 * @param array $coords
	 * @return string
	 */
	public function createChartsData($coords) {
		$cht = "";

		$n = count($coords);
		if($n > 600) {
			$c = $n / 600;
			$c = round($c,0);
		} else {
			$c = 1;
		}
		$ele = array();
		for($i=0; $i<$n; $i = $i+$c) {
			$coord = $coords[$i];
			$cht .= round($coord[2],0).",";

			array_push($ele,round($coord[2],0));
		}
		$chtn = substr($cht, 0, -1);

		return $chtn;
	}

	/**
	 *
	 * @param string $date
	 * @return (int) timestamp
	 */
	public function giveTimestamp($date) {
		// ToDo: unterschiedliche Zeittypen können hier eingefügt werden
		if ( $date == 0 ) return false;
		$date = explode('T',$date);
		$time_tmp_date = explode('-',$date[0]);
		$time_tmp_date_year = $time_tmp_date[0];
		$time_tmp_date_month = $time_tmp_date[1];
		$time_tmp_date_day = $time_tmp_date[2];
		$time_tmp_time = explode(':',str_replace("Z","",$date[1]));
		$time_tmp_time_hour = $time_tmp_time[0];
		$time_tmp_time_minute = $time_tmp_time[1];
		$time_tmp_time_sec = (int)round($time_tmp_time[2],0);
		return mktime(	$time_tmp_time_hour,$time_tmp_time_minute,$time_tmp_time_sec,
		$time_tmp_date_month,$time_tmp_date_day,$time_tmp_date_year);
	}

	/**
	 *
	 * @return (int) speed
	 */
	public function giveSpeed($lonA,$latA,$timeA,$lonB,$latB,$timeB,$unit,$digits=1) {

		$Erdradius=6378.137;

		if ((($latB-$latA)==0) AND (($lonB-$lonA)==0))
		$e = 0;
		else {
			// Haversine
			$lonA = $lonA / 180 * pi() ;
			$latA = $latA / 180 * pi() ;
			$lonB = $lonB / 180 * pi() ;
			$latB = $latB / 180 * pi() ;
			$e = acos( sin($latA)*sin($latB) + cos($latA)*cos($latB)*cos($lonB-$lonA) );
		}

		$entfernung = (($e * $Erdradius)*1000);		// Meter
		$time_distance = ( $timeA - $timeB );		// Sekunden
		if ($time_distance == 0) return 0;
		$speed = ($entfernung/$time_distance);		// Meter pro Sekunde
		if($speed<0) return false;
		if ( $speed > (1000*1000/3600) ) $speed = 0;	// wenn mehr als 1000 km/h
		if ( $unit == "Kilometer" )
		return round(($speed/1000*3600),$digits);
		if ( $unit == "Miles" )
		return round(($speed/1609.344*3600),$digits);
		/* Formel für Umrechnung Meter -> Meilen:
		 http://www.din-formate.de/kalkulator-berechnung-laenge-masse-groesse-umrechnung-meter.html
		 */
		return false; // wenn Einheit unbekannt
	}

	/**
	 *
	 * @param array $coords
	 * @return string
	 */
	public function createSpeedData($coords,$unit) {
		$cht = "";
		$n = count($coords);
		if($n > 600) {
			$count = $n / 600;
			$count = round($count,0);
		} else {
			$count = 1;
		}
		$time = array();
		$lon = array();
		$lat = array();
		$j = 0;
		for($i=0; $i<$n; $i = $i+$count) {
			$lon[$j] = ($coords[$j][0]);
			$lat[$j] = ($coords[$j][1]);
			$time[$j] = $this->giveTimestamp($coords[$j][3]);
			if ($time[$j] === false) return false;
			if ( $j > 0 ) {
				$speed = $this->giveSpeed($lon[$j],$lat[$j],$time[$j],$lon[($j-1)],$lat[($j-1)],$time[($j-1)],$unit);
				if($speed!==false)
				$cht .= $speed.",";
			}
			$j++;
		}
		$chtn = substr($cht, 0, -1);
		return $chtn;
	}

	public function createBeatsData($coords) {
		$cht = "";

		$n = count($coords);
		if($n > 600) {
			$c = $n / 600;
			$c = round($c,0);
		} else {
			$c = 1;
		}
		$beats = array();
		for($i=0; $i<$n; $i = $i+$c) {
			$coord = $coords[$i];
			$cht .= $coord[4].",";
			array_push($beats,$coord[4]);
		}
		$chtn = substr($cht, 0, -1);

		return $chtn;
	}

	/**
	 *
	 * @param string $distance
	 * @return string
	 */
	public function distance2chart($distance) {
		$end = round($distance,1);
		$first = round($distance/4,1);
		$mid = round($distance/2,1);
		$third = round($first * 3,1);
		$dis = "0|".$first."|".$mid."|".$third."|".$end;

		return $dis;
	}
	// Osm BEGIN
	public function writeOSMMap($where,$tracks,$params) {
		$cfg =& jtgHelper::getConfig();

		// 	$cnates = $this->getMapNates();
		$rows = $this->getTracks($where);
		// 	$user = JFactory::getUser();
		// 	$userid = $user->id;
		//		$rows = $this->maySee($rows);
		$map = false;
		$map .= $this->parseScriptOSMHead();
		$map .= $this->parseOSMMapControl(false, $params);
		$map .= $this->parseOSMLayer();
		// 	$map .= $this->parseOSMPOIs(); // currently not active
		if ($tracks)
		{
			//			die();
			$map .= $this->parseOSMTracks($rows); // Schlecht bei vielen verfügbaren Tracks
			//			$map .= $this->parseXMLlinesOSM($rows,"/".$file);
		}
		$file = JPATH_SITE.DS."components".DS."com_jtg".DS."models".DS."jtg.php";
		require_once $file;
		$this->sortedcats = jtgModeljtg::getCatsData(true);

		$map .= $this->parseOSMMarker($rows);
		$map .= $this->parseOSMMapCenter($rows);
		$map .= $this->parseOSMMapFunctions();
		$map .= $this->parseScriptOSMFooter();
		// return $this->buildColorBalken();
		// die();
		return $map;
	}

	public function writePersonelMap() {
		//		deprecadet
		$map = "
<script type=\"text/javascript\" src=\"components/com_jtg/assets/js/homeposition.js\"></script>
";
		$cfg =& jtgHelper::getConfig();
		$cnates = $this->getMapNates();
		$rows = $this->getTracks();
		// 	$user = JFactory::getUser();
		// 	$userid = $user->id;
		$rows = $this->maySee($rows);

		$map .= $this->parseScriptOSMHead();
		$map .= $this->parseOSMMapControl();
		$map .= $this->parseOSMLayer();
		//	$map .= $this->parseOSMPOIs();
		// 	$map .= $this->parseOSMTracks($rows); // Schlecht bei vielen verfügbaren Tracks
		// 	$map .= $this->parseXMLlinesOSM($rows,"/".$file);
		//	$map .= $this->parseOSMMarker($rows);
		//	$map .= $this->parseOSMMapCenter($rows);
		$map .= "// <!-- parseOSMMapCenter BEGIN -->
	\n";
		$map .= "	layerMarker_options= {styleMap: new OpenLayers.StyleMap({
		externalGraphic: \"components/com_jtg/assets/images/home.png\",
		backgroundGraphic: \"components/com_jtg/assets/images/home_shdw.png\",
		graphicXOffset:-15,
		graphicYOffset:-20,
		backgroundXOffset:-15,
		backgroundYOffset:-20,
		// Set the z-indexes of both graphics to make sure the background
		// graphics stay in the background (shadows on top of markers looks
		// odd; let's not do that).
		graphicZIndex: 11,
		backgroundGraphicZIndex: 10,
		pointRadius: 20
	}),
	displayInLayerSwitcher: false,
	isBaseLayer:false,
//	isBaseLayer:true,
	rendererOptions: {yOrdering: true},
	eventListeners:{'featureadded': function(){map.getControlsByClass('OpenLayers.Control.Permalink')[0].updateLink()},
		'featuresremoved': function(){map.getControlsByClass('OpenLayers.Control.Permalink')[0].updateLink()}
		}
	};
	//Layer to draw a personalized marker within
	layerMarker = new OpenLayers.Layer.Vector(\"Marker\", layerMarker_options);
	// Add a drag feature control to move features around.
	var dragFeature = new OpenLayers.Control.DragFeature(layerMarker,
	{onComplete:function(){map.getControlsByClass('OpenLayers.Control.Permalink')[0].updateLink()}});
	map.addControl(dragFeature);
	dragFeature.activate();
	map.addLayers([layerMarker]);


function handleMapClick(evt) {
//set Marker on click
	layerMarker.removeFeatures(layerMarker.features);
	var pixel = new OpenLayers.Pixel( evt.xy.x, evt.xy.y);
	var lonLat = map.getLonLatFromViewPortPx(pixel);
	var marker = [];
	marker.push(new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Point(lonLat.lon, lonLat.lat)));
	layerMarker.addFeatures(marker);
	OpenLayers.Event.stop(evt);
};
map.events.register('click', map, handleMapClick);

\n";

		$map .= "var min = lonLatToMercator(new OpenLayers.LonLat";
		$bbox_lon_min = -180;
		$bbox_lat_min = 83;
		$bbox_lon_max = 180;
		$bbox_lat_max = -55;
		$map .= "(".$bbox_lon_min.",".$bbox_lat_min."));\nvar max = lonLatToMercator";
		$map .= "(new OpenLayers.LonLat(".$bbox_lon_max.",".$bbox_lat_max."));\n";
		$map .= "map.zoomToExtent(new OpenLayers.Bounds(min.lon, min.lat, max.lon, max.lat));\n";
		$map .= "// <!-- parseOSMMapCenter END -->\n";

		$map .= $this->parseOSMMapFunctions();
		$map .= $this->parseScriptOSMFooter();
		return $map;
	}

	/**
	 * counts the MapCenter and ZoomLevel of Boundingbox
	 *
	 * @param flow $lon_min,$lon_max,$lat_min,$lat_max
	 * @return array('lon'=>lon,'lat'=>lat)
	 */
	public function calcMapCenter($lon_min,$lon_max,$lat_min,$lat_max) {
		$lat = 47;	// Weltansicht ohne Südpol
		$lon = 0;
		$zoom_min = 2;
		$zoom_max = 14;
		$return = array();
		if ( ( $lon_min == $lon_max ) AND ( $lat_min == $lat_max ) ) {
			// 			Nur eine Koordinate wurde übergeben
			$return['lon'] = $lon_min;
			$return['lat'] = $lat_min;
			// 			$return['zoom'] = $zoom_max;
		} else {
			$return['lon'] = ( ( $lon_max + $lon_min ) / 2 );
			$return['lat'] = ( ( $lat_max + $lat_min ) / 2 );
		}
		// 		return $return;
	}

	/**
	 *
	 * @return string
	 */
	private function parseOSMMapCenter($rows) {
		if(!$rows)
		{
			//			Worldview without southpole
			$bbox_lat_max = 47;
			$bbox_lat_min = 47;
			$bbox_lon_max = 180;
			$bbox_lon_min = -180;
		}
		else
		{
			$bbox_lat_max = -90;
			$bbox_lat_min = 90;
			$bbox_lon_max = -180;
			$bbox_lon_min = 180;
			foreach ( $rows AS $row ) {
				if ( ( $row->start_n ) AND ( $row->start_n > $bbox_lat_max ) )
				$bbox_lat_max = $row->start_n;
				if ( ( $row->start_n ) AND ( $row->start_n < $bbox_lat_min ) )
				$bbox_lat_min = $row->start_n;
				if ( ( $row->start_e ) AND ( $row->start_e > $bbox_lon_max ) )
				$bbox_lon_max = $row->start_e;
				if ( ( $row->start_e ) AND ( $row->start_e < $bbox_lon_min ) )
				$bbox_lon_min = $row->start_e;
			}
		}
		// 	echo ("\n<!--\n\$bbox_lat_max = ".$bbox_lat_max."\n\$bbox_lat_min = ".$bbox_lat_min."\n-->");
		$center = "// <!-- parseOSMMapCenter BEGIN -->\n";
		$center .= "var min = lonLatToMercator(new OpenLayers.LonLat";
		$center .= "(".$bbox_lon_min.",".$bbox_lat_min."));\nvar max = lonLatToMercator";
		$center .= "(new OpenLayers.LonLat(".$bbox_lon_max.",".$bbox_lat_max."));\n";
		$center .= "map.zoomToExtent(new OpenLayers.Bounds(min.lon, min.lat, max.lon, max.lat));\n";
		$center .= "// <!-- parseOSMMapCenter END -->\n";
		return $center;
	}

	/**
	 *
	 * @return string
	 */
	private function parseOSMMapCenterSingleTrack($file) {
		//		echo "deprecated function parseOSMMapCenterSingleTrack";
		//		global $osmmicrotime;
		//		if ( ( microtime(true) - $osmmicrotime ) > 29 )
		//		return "map.zoomToMaxExtent();\n"; // emergency brake
		if (!is_file($file)) return false;
		$xml = simplexml_load_file($file);
		$bbox_lat_max = -90;
		$bbox_lat_min = 90;
		$bbox_lon_max = -180;
		$bbox_lon_min = 180;
		$this->gpsFile = $file;
		$isWaypoint = $this->isWaypoint();
		if ( $isWaypoint !== false ) {
			$i = $isWaypoint;
			$found = 0;
			while(true) {
				if (isset($xml->wpt[$i])) {
					$wp = $xml->wpt[$i];
					if ( $wp != null ) {
						$wp = $wp->attributes();
						$lon = (float)$wp->lon;
						$lat = (float)$wp->lat;
						if ( $lat > $bbox_lat_max ) $bbox_lat_max = $lat;
						if ( $lat < $bbox_lat_min ) $bbox_lat_min = $lat;
						if ( $lon > $bbox_lon_max ) $bbox_lon_max = $lon;
						if ( $lon < $bbox_lon_min ) $bbox_lon_min = $lon;
						$found = 0;
					}
					$i++;
				}
				$found++;
				if ( $found > 10 ) break;
			}
		}
		$isTrack = $this->isTrack();
		if ( $isTrack !== false ) {
			$i = $isTrack;
			$found = 0;
			$foundj = 0;
			while(true) {
				if (isset($xml->trk[$i]->trkseg->trkpt)) {
					$found = 0;
					$j = 0;
					while(true) {
						if (isset($xml->trk[$i]->trkseg->trkpt[$j])) {
							$trk = $xml->trk[$i]->trkseg->trkpt[$j]->attributes();
							if ( $trk != null ) {
								$lon = (float)$trk->lon;
								$lat = (float)$trk->lat;
								if ( $lat > $bbox_lat_max ) $bbox_lat_max = $lat;
								if ( $lat < $bbox_lat_min ) $bbox_lat_min = $lat;
								if ( $lon > $bbox_lon_max ) $bbox_lon_max = $lon;
								if ( $lon < $bbox_lon_min ) $bbox_lon_min = $lon;
								$foundj = 0;
							}
							$j++;
						}
						$foundj++;
						if ( $foundj > 10 ) break;
					}
				}
				$found++;
				if ( $found > 10 ) break;
				$i++;
			}
		}
		$center = "// <!-- parseOSMMapCenterSingleTrack BEGIN -->\n";
		$center .= "var min = lonLatToMercator(new OpenLayers.LonLat";
		$center .= "(".$bbox_lon_min.",".$bbox_lat_min."));\n";
		$center .= "var max = lonLatToMercator(new OpenLayers.LonLat";
		$center .= "(".$bbox_lon_max.",".$bbox_lat_max."));\n";
		$center .= "map.zoomToExtent(new OpenLayers.Bounds(min.lon, min.lat, max.lon, max.lat));\n";
		$center .= "// <!-- parseOSMMapCenterSingleTrack END -->\n";
		return $center;
	}

	/**
	 *
	 * @return string
	 */
	private function parseOSMMapFunctions() {
		$marker = "// <!-- parseOSMMapFunctions BEGIN -->\n";
		/*
		 $marker .= "	/**
		 * Function: lonLatToMercator
		 * OpenLayers-Karte mit OSM-Daten aufbauen
		 * Version 2009-09-07
		 * \"EPSG:41001\" -> 4326
		 * http://www.fotodrachen.de/javascripts/map.js
		 * Parameters:
		 * ll - {<OpenLayers.LonLat>}
		 */

		$marker .= "	function lonLatToMercator(ll) {
		var lon = ll.lon * 20037508.34 / 180;
		var lat = Math.log(Math.tan((90 + ll.lat) * Math.PI / 360)) / (Math.PI / 180);
		lat = lat * 20037508.34 / 180;
		return new OpenLayers.LonLat(lon, lat);
	}\n";

		$marker .= "// <!-- parseOSMMapFunctions END -->\n";
		return $marker;
	}

	/**
	 *
	 * @return string
	 */
	//	 private function parseOSMStartZiel() {
	// 	$startziel = "// <!-- parseOSMStartZiel BEGIN -->\n";
	// 	$startziel .= "// <!-- parseOSMStartZiel END -->\n";
	// 	return $startziel;
	// }

	/**
	 *
	 * @return string
	 */
	private function parseOSMMarker($track_array,$visibility=true) {
		$cfg =& jtgHelper::getConfig();
		if(!$track_array) return false;
		$marker = "// <!-- parseOSMMarker BEGIN -->\n";
		if ( $visibility != true )
		$marker .= "	markers = new OpenLayers.Layer.Markers(\"".JText::_('COM_JTG_OTHER_STARTPOINTS')."\");\n";
		else
		$marker .= "	markers = new OpenLayers.Layer.Markers(\"".JText::_('COM_JTG_STARTPOINTS')."\");\n";
		$marker .= "	map.addLayer(markers);\n";
		$marker .= "	addMarkers();\n";
		if ( $visibility != true )
		$marker .= "	markers.setVisibility(false);\n";
		$marker .= "	function addMarkers() {\n";
		$i = 0;
		foreach ( $track_array AS $row ) {
			//			if ( $i < 100 ) break;
			//			$i++;
			$link = JROUTE::_("index.php?option=com_jtg&view=files&layout=file&id=".$row->id);

			$lon = $row->start_e;
			$lat = $row->start_n;
			if ( ( $row->published == 1 ) AND ( ( $lon ) OR ( $lon ) ) ){
				$marker .= "ll = new OpenLayers.LonLat(".$lon.",".$lat.").";
				$marker .= "transform(new OpenLayers.Projection(\"EPSG:4326\"), map.getProjectionObject()); ";
				$marker .= "popupClass = AutoSizeFramedCloud; ";
				$marker .= "popupContentHTML = '<b>".JText::_('COM_JTG_TITLE').": <a href=\"".$link."\"";
				switch ($row->access) {
					case 0: // Public
						$marker .= ">";
						break;
					case 9: // Private
						$marker .= " title=\\\"".JText::_('COM_JTG_PRIVATE_ONLY')."\">";
						break;
					case 1: // Registered
						$marker .= " title=\\\"".JText::_('COM_JTG_REGISTERED_ONLY')."\">";
						break;
					case 2: // Admin
						$marker .= " title=\\\"".JText::_('COM_JTG_SPECIAL_ONLY')."\">";
						break;
				}
				if ($row->title)
				$marker .= $row->title;
				else
				$marker .= "<i>".JText::_('COM_JTG_NO_TITLE')."</i>";
				if ( $row->access != 0 )
				{
					$iconpath = JURI::root()."components/com_jtg/assets/template/".$cfg->template."/images/";
				}
				switch ($row->access) {
					case 1:
						$marker .= "&nbsp;<img alt=\\\"".JText::_('COM_JTG_REGISTERED_ONLY')."\" src=\\\"".$iconpath."registered_only.png\\\" />";
						break;
					case 2:
						$marker .= "&nbsp;<img alt=\\\"".JText::_('COM_JTG_SPECIAL_ONLY')."\" src=\\\"".$iconpath."special_only.png\\\" />";
						break;
					case 9:
						$marker .= "&nbsp;<img alt=\\\"".JText::_('COM_JTG_PRIVATE_ONLY')."\" src=\\\"".$iconpath."private_only.png\\\" />";
						break;
				}
				$marker .= "</a></b>";
				if ( $row->cat != "" ) {
					$marker .= "<br />".JText::_('COM_JTG_CAT').": ";
					$marker .= jtgHelper::parseMoreCats($this->sortedcats,$row->catid,"box",true);
					//					"<a href=\"index.php?option=com_jtg&amp;view=files&amp;layout=list&amp;search=".$row->cat."\">".$row->cat."</a><br />";
					//					$marker .= "<a href=\"index.php?option=com_jtg&amp;view=files&amp;layout=list&amp;search=".$row->cat."\">".$row->cat."</a><br />";
				} else
				$marker .= "<br /><i>".JText::_('COM_JTG_CAT_NONE')."</i>";
				$marker .= str_replace(
				array(
					"'","\n"
				),
				array(
					"\'","<br />"
				),($this->showDesc($row->description)));
				$marker .= "'; ";

				// start icon
				$marker .= $this->parseCatIcon($row->catid,$row->istrack,$row->iswp,$row->isroute);
				// end icon
			}
		}
		$marker .= "	}\n";
		//	$marker .= "	//
		//	* Function: addMarker
		//	* Add a new marker to the markers layer given the following lonlat,
		//	*	 popupClass, and popup contents HTML. Also allow specifying
		//	*	 whether or not to give the popup a close box.
		//	*
		//	* Parameters:
		//	* ll - {<OpenLayers.LonLat>} Where to place the marker
		//	* popupClass - {<OpenLayers.Class>} Which class of popup to bring up
		//	*	 when the marker is clicked.
		//	* popupContentHTML - {String} What to put in the popup
		//	* closeBox - {Boolean} Should popup have a close box?
		//	* overflow - {Boolean} Let the popup overflow scrollbars?
		//	*/

		$marker .= "function addMarker(ll, popupClass, popupContentHTML, closeBox, overflow, icon) {
		var feature = new OpenLayers.Feature(markers, ll);
		feature.closeBox = closeBox;
		feature.popupClass = popupClass;
		feature.data.popupContentHTML = popupContentHTML;
		feature.data.overflow = (overflow) ? \"auto\" : \"hidden\";
		var marker = new OpenLayers.Marker(ll,icon);
		marker.feature = feature;
";

		$marker .= "
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
";
		/*
		 $marker .= "		var markerHover = function (evt) {
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

		 var markerDestroy = function (evt) {
		 if (this.popup != null) {
		 this.popup.dest();
		 }
		 currentPopup = this.popup;
		 OpenLayers.Event.stop(evt);
		 };
		 ";
		 */

		// MouseDown
		$marker .= "		marker.events.register(\"mousedown\", feature, markerClick);\n";

		// MouseHover
		//$marker .= "		marker.events.register(\"mouseover\", feature, markerHover);\n";
		//$marker .= "		marker.events.register(\"mouseout\", feature, markerHover);\n";

		$marker .= "		markers.addMarker(marker);}\n";
		$marker .= "// <!-- parseOSMMarker END -->\n";
		return $marker;
	}
	/**
	 *
	 * @return string
	 */
	private function parseOSMTracks($rows) {
		if ( $rows === null ) return false;
		$color = $this->calculateAllColors(count($rows));
		$string = "// <!-- parseOSMTracks BEGIN -->\n";
		$string .= "layer_vectors = new OpenLayers.Layer.Vector(\"".JText::_('COM_JTG_TRACKS')."\", { displayInLayerSwitcher: true } );\n";
		$string .= "map.addLayer(layer_vectors);\n";
		$i=0;
		foreach($rows AS $row) {
			$file = "components".DS."com_jtg".DS."uploads".DS.$row->file;
			$coords = $this->getCoords($file);
			$string .= "geometries = new Array();geometries.push(drawLine([\n";
			if ($coords){
				foreach($coords as $key => $fetch) {
					$string .= "[".$coords[$key][0].",".$coords[$key][1]."],\n";
				}
			}
			$string .= "],\n{strokeColor:\"".$this->getHexColor("#".$color[$i])."\",\nstrokeWidth: 2,\nfillColor: \"".$this->getHexColor("#".$color[$i])."\",\nfillOpacity: 0.4}));\n";
			$i++;
		}
		$string .= "// <!-- parseOSMTracks END -->\n";
		return $string;
	}

	/**
	 *
	 * @return string
	 */
	private function parseOSMPOIs() {
		$pois = "// <!-- parseOSMPOIs BEGIN -->\n";
		$pois .= "// <!-- parseOSMPOIs END -->\n";
		return $pois;
	}

	/**
	 * @return Object
	 */
	private function getMaps($desc=false) {
		$db = &JFactory::getDBO();
		$sql='Select * from #__jtg_maps';
		if($desc)
		$sql .= ' ORDER BY '.$desc;
		$db->setQuery($sql);
		$maps = $db->loadObjectlist();
		return $maps;
	}

	/**
	 * @return Object
	 */
	private function buildMaps() {
		$maps = $this->getMaps("ordering");
		$return = "";
		$document = & JFactory :: getDocument();
		for($i=0;$i<count($maps);$i++){
			$map = $maps[$i];
			$name = strtolower(str_replace(array(" ","_"),"",html_entity_decode($map->name)));
			$realname = JText::_(html_entity_decode($map->name));
			$param = str_replace("{name}",$realname,html_entity_decode($map->param));
			$script = html_entity_decode($map->script);
			if($map->published==1){
				if($script){
					if(!preg_match("/|/",$script))
					$document->addScript($script);
					else {
						$scripts = explode("|",$script);
						foreach($scripts AS $eachscript)
						$document->addScript($eachscript);
					}
				}
				$code = html_entity_decode($map->code);
				if ($code != "") $return .= $code."\n";
				if(!isset($baselayer))
				$baselayer = "		map.setBaseLayer(layer".$name.");\n";
				$return .= "layer".$name." = new ".$param.";\n".
			"map.addLayer(layer".$name.");\n";
			}
		}
		
		//TODO move this to overlays
		// relief ombragé "Hillshade of Europe" http://www.osm-wms.de/
		$return .= "hs_name = \"".JText::_('COM_JTG_HILL_SHADE_EUROPE')."\"\n";
		$return .= "hs_url = \"http://129.206.228.72/cached/hillshade?\";\n";
		$return .= "hs_options = {layers: 'europe_wms:hs_srtm_europa',srs: 'EPSG:900913', format: 'image/jpeg', transparent: 'true',numZoomLevels: 19};\n";
		$return .= "hs2_1_options = {layers: 'europe_wms:hs_srtm_europa',srs: 'EPSG:900913', format: 'image/jpeg',numZoomLevels: 19};\n";

		$return .= "hs2 =  new OpenLayers.Layer.WMS( hs_name , hs_url , hs_options, {'buffer':1, removeBackBufferDelay:0, className:'olLayerGridCustom'});\n";
		$return .= "hs2.setOpacity(0.3);\n";
		$return .= "hs2_1 =  new OpenLayers.Layer.WMS( hs_name , hs_url , hs2_1_options,{'buffer':1, transitionEffect:'resize', removeBackBufferDelay:0, className:'olLayerGridCustom'});\n";
		$return .= "map.addLayer( hs2,hs2_1 );";

// TODO osm_getTileURL see http://wiki.openstreetmap.org/wiki/Talk:Openlayers_POI_layer_example
		$document->addScript('/components/com_jtg/assets/js/osm_getTileURL.js');
		$return .= "hill = new OpenLayers.Layer.TMS(\"".JText::_('COM_JTG_HILL_SHADE_NASA')."\"\n,
			\"http://toolserver.org/~cmarqu/hill/\",
			{
					type: 'png', getURL: osm_getTileURL,
					displayOutsideMaxExtent: true, isBaseLayer: false,
				transparent: true, \"visibility\": false
				}
			);
		map.addLayer( hill );\n";

			
					
		if ( !isset($baselayer)) return false; // no map available
		$return = $return.$baselayer;
		return $return;
	}

	/**
	 *
	 * @return string
	 */
	//	private function parseOSMLayer() {
	private function parseOSMLayer() {
		$maps = $this->buildMaps();
		$layer = "// <!-- parseOSMLayer BEGIN -->\n";

		$layertoshow = array();

		/*	$layertoshow[0] = "		layerMapnik = new OpenLayers.Layer.OSM.Mapnik(\"Mapnik\");\n			map.addLayer(layerMapnik);\n";
		 $layertoshow[1] = "		layerTilesAtHome = new OpenLayers.Layer.OSM.Osmarender(\"Osmarender\");\n			map.addLayer(layerTilesAtHome);\n";
		 $layertoshow[2] = "		layerCycleMap = new OpenLayers.Layer.OSM.CycleMap(\"CycleMap\");\n			map.addLayer(layerCycleMap);\n";
		 /*	$layertoshow[3] = "
		 layerol_wms = new OpenLayers.Layer.WMS( \"OpenLayers WMS (metacarta)\",
		 \"http://labs.metacarta.com/wms/vmap0?\", {layers: \"basic\"} );
		 map.addLayer(layerol_wms);\n
		 ";
		 $layertoshow[3] .= "		layerOpenLayers = new OpenLayers.Layer.WMS( \"OpenLayers WMS (cubewerx)\",
		 \"http://demo.cubewerx.com/demo/cubeserv/cubeserv.cgi?\",
		 {layers: 'Foundation.GTOPO30', version: '1.3.0'},
		 {singleTile: true});
		 map.addLayer(layerOpenLayers);\n
		 ";*/
		/*
		 for google you need:
		 <script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key='></script>
		 */

		/* 	$layertoshow[3] .= "
		 // create Google Mercator layers
		 var gmap = new OpenLayers.Layer.Google(
		 \"Google Streets\",
		 {'sphericalMercator': true});
		 map.addLayer(gmap);\n
		 ";
		 $layertoshow[3] .= "		var gsat = new OpenLayers.Layer.Google(\"Google Satellite\",
		 {type: G_SATELLITE_MAP, 'sphericalMercator': true, numZoomLevels: 22});
		 map.addLayer(gsat);\n
		 ";
		 $layertoshow[3] .= "		var ghyb = new OpenLayers.Layer.Google(\"Google Hybrid\",
		 {type: G_HYBRID_MAP, 'sphericalMercator': true});
		 map.addLayer(ghyb);\n
		 ";


		 // for VE you need:
		 // <script src='http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1'></script>

		 $layertoshow[3] .= "// create Virtual Earth layers
		 var veroad = new OpenLayers.Layer.VirtualEarth(\"Virtual Earth Roads\",
		 {'type': VEMapStyle.Road, 'sphericalMercator': true});
		 map.addLayer(veroad);\n
		 ";
		 $layertoshow[3] .= "		var veaer = new OpenLayers.Layer.VirtualEarth(\"Virtual Earth Aerial\",
		 {'type': VEMapStyle.Aerial, 'sphericalMercator': true});
		 map.addLayer(veaer);\n
		 ";
		 $layertoshow[3] .= "		var vehyb = new OpenLayers.Layer.VirtualEarth(\"Virtual Earth Hybrid\",
		 {'type': VEMapStyle.Hybrid, 'sphericalMercator': true});
		 map.addLayer(vehyb);\n
		 ";
		 $layertoshow[3] .= "// create Yahoo layer
		 var yahoo = new OpenLayers.Layer.Yahoo(\"Yahoo Street\",
		 {'sphericalMercator': true});
		 map.addLayer(yahoo);\n
		 ";
		 $layertoshow[3] .= "		var yahoosat = new OpenLayers.Layer.Yahoo(
		 \"Yahoo Satellite\",
		 {'type': YAHOO_MAP_SAT, 'sphericalMercator': true});
		 map.addLayer(yahoosat);\n
		 ";
		 $layertoshow[3] .= "			var yahoohyb = new OpenLayers.Layer.Yahoo(
		 \"Yahoo Hybrid\",
		 {'type': YAHOO_MAP_HYB, 'sphericalMercator': true});
		 map.addLayer(yahoohyb);\n
		 ";
		 $layertoshow[3] .= "var layerWoE = new OpenLayers.Layer.WMS(
		 \"WMS of Europe\",
		 \"http://openls.giub.uni-bonn.de/ors-tilecache/tilecache.py?\",
		 {layers: 'ors-osm',srs: 'EPSG:900913', format: 'image/png',numZoomLevels: 19},
		 {'buffer':2});
		 map.addLayer(layerWoE);\n";*/
		/*	tot?
		 $layertoshow[3] .= "			var oam = new OpenLayers.Layer.XYZ(
		 \"OpenAerialMap\",
		 \"http://tile.openaerialmap.org/tiles/1.0.0/openaerialmap-900913/${z}/${x}/${y}.png\",
		 {sphericalMercator: true});
		 map.addLayer(oam);\n
		 ";
		 */
		/* 	$layertoshow[3] .= "
		 var osmarender = new OpenLayers.Layer.OSM(
		 \"OpenStreetMap (Tiles@Home)\",
		 \"http://tah.openstreetmap.org/Tiles/tile/${z}/${x}/${y}.png\");
		 map.addLayer(osmarender);\n
		 ";
		 */
		/*	tot?
		 $layertoshow[3] .= "			// create WMS layer
		 var wms = new OpenLayers.Layer.WMS(
		 \"World Map\",
		 \"http://world.freemap.in/tiles/\",
		 {'layers': 'factbook-overlay', 'format':'png'},
		 {
		 'opacity': 0.4, visibility: false,
		 'isBaseLayer': false,'wrapDateLine': true
		 }
		 );
		 map.addLayer(wms);\n
		 ";
		 */

		/*	$layertoshow[3] .= "// Hillshading layer provided by University of Bonn
		 // based on CIAT-CSI SRTM data, available for
		 // non-commercial use only
		 var hs_name = \"Hillshade (Uni Bonn)\";
		 var hs_url = \"http://services.giub.uni-bonn.de/hillshade?\";
		 var hs_options = {layers: 'europe_wms:hs_srtm_europa',srs: 'EPSG:900913', format: 'image/JPEG'};
		 var hs = new OpenLayers.Layer.WMS(hs_name , hs_url , hs_options, {'buffer':2});
		 map.addLayer(hs);\n";*/
		/*
		 $layertoshow[3] .= "// Hillshading layer provided by University of Bonn
		 // based on CIAT-CSI SRTM data, available for
		 // non-commercial use only
		 var hs_name = \"Hillshade (Uni Bonn)\";
		 var hs_url = \"http://services.giub.uni-bonn.de/hillshade?\";
		 var hs_options = {layers: 'europe_wms:hs_srtm_europa',srs: 'EPSG:900913', format: 'image/JPEG', transparent: 'true', isBaseLayer: true};
		 var hs = new OpenLayers.Layer.WMS(hs_name , hs_url , hs_options, {'buffer':2});
		 hs.setOpacity(0.15);
		 hs.visibility = false;
		 map.addLayer(hs);\n";

		 // 	$layertoshow[3] = "		layerOffline = new OpenLayers.Layer.OSM(\"-keine-\",\"\");\n			map.addLayer(layerOffline);\n";
		 $layertoshow[3] = "		NASAGlobalMosaic = new OpenLayers.Layer.WMS(\n			\"NASA Global Mosaic\",\n			\"http://t1.hypercube.telascience.org/cgi-bin/landsat7/\",\n			{layers: \"landsat7\"});\n			map.addLayer(NASAGlobalMosaic);\n";
		 // 	$layertoshow[5] = "		layerKosmos = new OpenLayers.Layer.OSM.Kosmos(\"Kosmos\");\n		layerKosmos = new OpenLayers.Layer.OSM(\"Kosmos\",\"http://home.vrweb.de/~thomas.buedel/Tiles/\", \"basic\", { 'format':'image/png',maxResolution: 180/256 } );\n			map.addLayer(layerKosmos);\n";

		 $defaultmapisset = false;							// Gültiger Default-Layer nicht gefunden
		 $baselayer = false;
		 for($i=-1;$i<9;$i++) {
		 if ( ( $i == -1 ) AND							// erster Durchlauf nur für Def.-Layer
		 ( isset($osm_allow_map[$osm_allow_map_default])) AND			// existiert der Eintrag auf den der Def.-Layer verweist?
		 ( $osm_allow_map[$osm_allow_map_default] != 0) ) {			// ... und ist dieser zugelassen?
		 $layer .= $layertoshow[$osm_allow_map_default];			// Def.-Layer an erste Stelle
		 $defaultmapisset = $osm_allow_map_default;			// Das wird der Def.-Layer (für die Schleife)
		 $baselayer = explode('=',$layertoshow[$osm_allow_map_default]);	// Baselayer ebenfalls benannt
		 $baselayer = trim($baselayer[0]);
		 } elseif ( ( $osm_allow_map[$i] == 1 ) AND ( $defaultmapisset != $i ) )	// Füge Layer nur hinzu, wenn zugelassen ...
		 $layer .= $layertoshow[$i];					// ... und es kein Def.-Layer ist
		 }

		 echo("<br>\nHallo Welt \"".$layer."\"");
		 */
		$layer .= $maps."// <!-- parseOSMLayer END -->\n";
		return $layer;

		if ( $baselayer != false ) {
			$layer .= "		map.setBaseLayer(".$baselayer.");\n";
			return $layer;
		} else return $layertoshow[0];	// Gib nur Mapnik aus, für den Fall der Fehlkonfiguration
	}

	// Pass in GPS.GPSLatitude or GPS.GPSLongitude or something in that format
	// http://stackoverflow.com/questions/2526304/php-extract-gps-exif-data/2572991#2572991
	// Thanks to Gerald Kaszuba http://geraldkaszuba.com/
	private function getGps($exifCoord, $hemi)
	{
		$degrees = count($exifCoord) > 0 ? $this->gps2Num($exifCoord[0]) : 0;
		$minutes = count($exifCoord) > 1 ? $this->gps2Num($exifCoord[1]) : 0;
		$seconds = count($exifCoord) > 2 ? $this->gps2Num($exifCoord[2]) : 0;
		$flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

		//	return $flip * ($degrees + $minutes / 60);
		return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
	}

	private function gps2Num($coordPart)
	{
		$parts = explode('/', $coordPart);
		if((count($parts)) <= 0)
		return 0;
		if((count($parts)) == 1)
		return $parts[0];
		return floatval($parts[0]) / floatval($parts[1]);
	}

	private function parseOSMGeotaggedImgs($id,$maxsize,$iconfolder,$httpiconpath)
	{
		$maxsize = (int)$maxsize;
		$foundpics = false;
		$map = "// <!-- parseOSMGeotaggedImgs BEGIN -->\n";
		$httppath = JURI::base()."images/jtg/".$id."/";
		$folder = JPATH_SITE.DS."images".DS."jtg".DS.$id.DS;
		$map .= "layer_geotaggedImgs = new OpenLayers.Layer.Markers(\"".JText::_('COM_JTG_GEOTAGGED_IMAGES')."\",".
	" { displayInLayerSwitcher: true });".
	"\n	map.addLayer(layer_geotaggedImgs);".
	"\n	layer_geotaggedImgs.setVisibility(true);\n";
		if(JFolder::exists($folder)) {
			$imgs = JFolder::files($folder);
			if($imgs)
			{
				foreach($imgs AS $image)
				{
					$exif = exif_read_data($folder.$image);
					if ( isset($exif['GPSLatitude']))
					{
						// is geotagged
						if(isset($exif["GPSImgDirection"]))
						{
							$direction = $exif["GPSImgDirection"];
							$direction = explode('/',$direction);
							$direction = (float)((int)$direction[0]/(int)$direction[1]);
						} else $direction = false;
						$foundpics = true;
						$height = (int)$exif["COMPUTED"]["Height"];
						$width = (int)$exif["COMPUTED"]["Width"];
						if ( ( $height > $maxsize ) OR ( $width > $maxsize ) )
						{
							if ( $height == $width ) // square
							{
								$height = $maxsize;
								$width = $maxsize;
							}
							elseif ( $height < $width ) // landscape
							{
								$height = $maxsize / $width * $height;
								$width = $maxsize;
							}
							else // portrait
							{
								$height = $maxsize;
								$width = $height * $maxsize / $width;
							}
						}
						$lon = $this->getGps($exif['GPSLongitude'], $exif['GPSLongitudeRef']);
						$lat = $this->getGps($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
						//	$lon = 6.18+(float)(rand(1000,2000)-1000)/100000;
						//	$lat = 50.99+(float)(rand(1000,2000)-1000)/100000;
						$size = "width=\"".(int)$width."\" height=\"".(int)$height."\"";
						$image = "<img ".$size." src=\"".$httppath.$image."\" alt=\"".$image."\" title=\"".$image."\">";
						$this->gpsFile = $httpiconpath."foto.xml";
						$xml = $this->loadFile();
						$sizex = $xml->sizex;
						$sizey = $xml->sizey;
						$offsetx = $xml->offsetx;
						$offsety = $xml->offsety;

						$map .= "var lonLatlayer_geotaggedImgs = new OpenLayers.LonLat(".$lon.",".$lat.").transform(new OpenLayers.Projection(\"EPSG:4326\"),map.getProjectionObject());".
					"\n	var sizelayer_geotaggedImgs = new OpenLayers.Size(".$sizex.",".$sizey.");".
					"\n	var offsetlayer_geotaggedImgs = new OpenLayers.Pixel(".$offsetx.",".$offsety.");".
					"\n	var iconlayer_geotaggedImgs = new OpenLayers.Icon(\"".$iconfolder."foto.png\",sizelayer_geotaggedImgs,offsetlayer_geotaggedImgs);".
					"\n	popupContentHTML_geotaggedImgs = '".$image."';".
					"\n	popupClass_geotaggedImgs = AutoSizeAnchored;".
					"\n	addlayer_geotaggedImgs(lonLatlayer_geotaggedImgs, popupClass_geotaggedImgs, popupContentHTML_geotaggedImgs, true, false, iconlayer_geotaggedImgs, map);\n";
					}
					/*				elseif ( isset($DateTime))
					 {
					 // i.e.: "2009:05:28 13:56:06"
					 $DateTime = $exif["DateTime"];
					 $DateTime = explode(' ',$DateTime);
					 $Date = $DateTime[0];
					 $Date = explode(':',$Date);
					 $Day = $Date[2];
					 $Mon = $Date[1];
					 $Year = $Date[0];
					 $Time = $DateTime[1];
					 $Time = explode(':',$Time);
					 $Hour = $Time[0];
					 $Min = $Time[1];
					 $Sec = $Time[2];
					 $timestamp = mktime($Hour,$Min,$Sec,$Mon,$Day,$Year);
					 }
					 */			}
			}
		}
		if ( $foundpics == false ) return false;
		$map .= "// <!-- parseOSMGeotaggedImgs END -->\n";
		return $map;
	}

	public function writeTrackOSM($track,$params) {
		$mainframe =& JFactory::getApplication();
		$params = &JComponentHelper::getParams( 'com_jtg' );
		$osmmicrotime = microtime(true);
		$zeiten = "<br />\n";
		$cfg =& jtgHelper::getConfig();
		$maxsize = $cfg->max_size;
		$iconpath = JURI::root()."components/com_jtg/assets/template/".$cfg->template."/images/";
		$httpiconpath = JPATH_SITE.DS."components".DS."com_jtg".DS."assets".DS."template".DS.$cfg->template.DS."images".DS;
		jimport('joomla.filesystem.file');
		// $rows = $this->getTracks(" WHERE a.id != ".$track->id);
		$zeiten .= (int) round( ( microtime(true) - $osmmicrotime ),0 )." ".JText::_('COM_JTG_DEBUG_TIMES')." getTracks<br />\n";
		$file = JPATH_SITE.DS."components".DS."com_jtg".DS."uploads".DS.$track->file;
		$this->gpsFile = $file;
		$xml = $this->loadFile();
		$map = "\n<!-- writeTrackCOM_JTG BEGIN -->\n";
		//		$rows = $this->maySee($rows);		// Berechtigung Okay?
		// ToDo: Berechtigungen vorher schon checken?
		$map .= $this->parseScriptOSMHead();
		$zeiten .= (int) round( ( microtime(true) - $osmmicrotime ),0 )." ".JText::_('COM_JTG_DEBUG_TIMES')." parseScriptOSMHead<br />\n";
		$map .= $this->parseOSMMap();
		$zeiten .= (int) round( ( microtime(true) - $osmmicrotime ),0 )." ".JText::_('COM_JTG_DEBUG_TIMES')." parseOSMMap<br />\n";
		$map .= $this->parseOSMMapControl(false,$params);
		$zeiten .= (int) round( ( microtime(true) - $osmmicrotime ),0 )." ".JText::_('COM_JTG_DEBUG_TIMES')." parseOSMMapControl<br />\n";
		$map .= $this->parseOSMLayer();
		$zeiten .= (int) round( ( microtime(true) - $osmmicrotime ),0 )." ".JText::_('COM_JTG_DEBUG_TIMES')." parseOSMLayer<br />\n";
		$coords = $this->parseXMLlinesOSM($file,$xml);
		$zeiten .= (int) round( ( microtime(true) - $osmmicrotime ),0 )." ".JText::_('COM_JTG_DEBUG_TIMES')." parseXMLlinesOSM<br />\n";
		if ( $coords !== null )
		$map .= $coords['coords'];
		//	if ( isset($rows) )
		//	$map .= $this->parseOSMMarker($rows,false); // Andere Tracks standardmäßig ausblenden
		$zeiten .= (int) round( ( microtime(true) - $osmmicrotime ),0 )." ".JText::_('COM_JTG_DEBUG_TIMES')." parseOSMMarker<br />\n";
		$map .= $this->parseOSMGeotaggedImgs($track->id,$maxsize,$iconpath,$httpiconpath);
		$zeiten .= (int) round( ( microtime(true) - $osmmicrotime ),0 )." ".JText::_('COM_JTG_DEBUG_TIMES')." parseOSMGeotaggedImgs<br />\n";
		// 	$map .= $this->parseStartPointOSM($coords); // ist jetzt für jede einzelne Spur definiert
		$file_tmp = $file;
		$wp = $this->extractWPs($xml);
		$zeiten .= (int) round( ( microtime(true) - $osmmicrotime ),0 )." ".JText::_('COM_JTG_DEBUG_TIMES')." extractWPs<br />\n";
		if ($wp !== false);
		$map .= $this->parseWPs($wp['wps'] );
		// 	$map .= $this->parseOSMStartZiel();
		$file = $file_tmp;
		if ( $coords !== null )
		$map .= $coords['center'];
		else
		$map .= $wp['center'];
		//	$map .= $this->parseOSMMapCenterSingleTrack($file);
		$zeiten .= (int) round( ( microtime(true) - $osmmicrotime ),0 )." ".JText::_('COM_JTG_DEBUG_TIMES')." parseOSMMapCenterSingleTrack<br />\n";
		$map .= $this->parseOSMMapFunctions();
		$zeiten .= (int) round( ( microtime(true) - $osmmicrotime ),0 )." ".JText::_('COM_JTG_DEBUG_TIMES')." parseOSMMapFunctions<br />\n";

		$map .= $this->parseScriptOSMFooter();
		$zeiten .= (int) round( ( microtime(true) - $osmmicrotime ),0 )." ".JText::_('COM_JTG_DEBUG_TIMES')." parseScriptOSMFooter<br />\n";
		$map .= "\n<!-- writeTrackCOM_JTG END -->\n";
		return $map;
	}

	public function writeSingleTrackOSM($file,$params=false) {
		// for little Map in Administration
		$cfg =& jtgHelper::getConfig();
		$mainframe =& JFactory::getApplication();
		jimport('joomla.filesystem.file');
		$file = JPATH_SITE.DS."components".DS."com_jtg".DS."uploads".DS.$file;
		$this->gpsFile = $file;
		$xml = $this->loadFile();
		$map = "\n<!-- writeSingleTrackCOM_JTG BEGIN -->\n";
		$map .= $this->parseScriptOSMHead();
		$map .= $this->parseOSMMap();
		$map .= $this->parseOSMMapControl(true,$params);
		$map .= $this->parseOSMLayer();
		$coords = $this->parseXMLlinesOSM($file,$xml);
		$map .= $coords['coords'];
		// 	$map .= $this->parseOSMMarker($rows,false); // Andere Tracks standardmäßig ausblenden
		// 	$map .= $this->parseStartPointOSM($coords); // ist jetzt für jede einzelne Spur definiert
		// 	$file_tmp = $file;
		$wp = $this->extractWPs($xml);
		if ($wp !== false);
		$map .= $this->parseWPs($wp['wps'] );
		// 	$map .= $this->parseOSMStartZiel();
		// 	$file = $file_tmp;
		if ( $coords !== null )
		$map .= $coords['center'];
		else
		$map .= $wp['center'];
		$map .= $this->parseOSMMapFunctions();

		$map .= $this->parseScriptOSMFooter();
		$map .= "<!-- writeSingleTrackCOM_JTG END -->\n";
		return $map;
	}

	/**
	 *
	 * @return string
	 */
	private function parseScriptOSMHead() {

		$map = "\n<!-- parseScriptOSMHead BEGIN -->\n";
		$map .= "<script type=\"text/javascript\">\n";
		$map .= "	OpenLayers.Popup.FramedCloud.prototype.autoSize = false;\n";
		$map .= "	var AutoSizeFramedCloud = OpenLayers.Class(OpenLayers.Popup.FramedCloud, {\n	'autoSize': true\n	});\n";
		$map .= "	var AutoSizeAnchored = OpenLayers.Class(OpenLayers.Popup.Anchored, {\n	'autoSize': true\n		});\n";
		$map .= "	function slippymap_init(divName) {\n";
		$map .= "		var map = new OpenLayers.Map (divName, {\n";
		$map .= "// <!-- parseScriptOSMHead END -->\n";

		return $map;
	}

	/**
	 *
	 * @return string
	 */
	private function parseScriptOSMFooter() {

		$map = "// <!-- parseScriptOSMFooter BEGIN -->\n";
		$map .= "}\n";
		$map .= "</script>\n";
		$map .= "<!-- parseScriptOSMFooter END -->\n";
		// $map .= "<center><div id=\"map\" style=\"width: 600px; height: 400px;\" ><script type=\"text/javascript\">slippymap_init();</script></div></center>\"";

		return $map;
	}

	/**
	 *
	 * @return string
	 */
	private function parseOSMMapControl( $adminonly = false, $params ) {
		/*

		*/
		$cfg =& jtgHelper::getConfig();
		if ( $cfg->unit == "Kilometer" )
		{
			$topOutUnits = "km";
			$topInUnits = "m";
			$bottomOutUnits = "mi";
			$bottomInUnits = "ft";
		}
		else
		{
			$topOutUnits = "mi";
			$topInUnits = "ft";
			$bottomOutUnits = "km";
			$bottomInUnits = "m";
		}
		$control = "// <!-- parseOSMMapControl BEGIN -->\n";
		$control .= "		controls:[\n";
		$control .= "//		Don't forget to remove comma in last line.\n//		Otherwise it doesn't work with IE.\n";
		//$control .= "				new OpenLayers.Control.ArgParser(),	// ?\n";
		if ( ( $params === false ) OR ( $params->get('osm_allow_keymove') != "0" ) )
		$control .= "				new OpenLayers.Control.KeyboardDefaults(),	// Tastatur: hoch, runter, links, rechts, +, -\n";
		if ( ( $params === false ) OR ( $params->get('osm_show_mouselocation') != "0" ) )
		$control .= "				new OpenLayers.Control.MousePosition(),		// Koordinate des Mauszeigers (lat, lon)\n";
		if ( ( $params === false ) OR ( $params->get('osm_allow_mousemove') != "0" ) )
		$control .= "				new OpenLayers.Control.Navigation(),		// mit Maus verschieb- und zoombar\n";
		if ( ( $params === false ) OR ( $params->get('osm_show_layerswitcher') != "0" ) )
		$control .= "				new OpenLayers.Control.LayerSwitcher(),		// Menue zum ein/aus-Schalten der Layer\n";
		if ( $adminonly === false){
			if ( ( $params === false ) OR ( $params->get('osm_show_panzoombar') != "0" ) )
			$control .= "				new OpenLayers.Control.PanZoomBar(),		// Zoombalken\n";
			if ( ( $params === false ) OR ( $params->get('osm_show_attribution') != "0" ) )
			$control .= "				new OpenLayers.Control.Attribution(),		// CC-By-SA ... \n";
			//	$control .= "				new OpenLayers.Control.ScaleLine(),		// Maszstab (nur am Aequator genau?)\n";
			if ( ( $params === false ) OR ( $params->get('osm_show_scale') != "0" ) )
			$control .= "				new OpenLayers.Control.ScaleLine({\n					topOutUnits: '" . $topOutUnits . "',\n					topInUnits: '" . $topInUnits . "',\n					bottomOutUnits: '" . $bottomOutUnits . "',\n					bottomInUnits: '" . $bottomInUnits . "'\n				}),					// Maßstab (nur an Äquator genau?)\n";
			//	$control .= "				new OpenLayers.Control.Permalink(null,'t', permalinkOptions),\n";
			//	$control .= "				new OpenLayers.Control.Permalink(),		// Permalink\n";
		}
		if ( ( $params === false ) OR ( $params->get('osm_allow_mousemove') != "0" ) )
//		$control .= "				new OpenLayers.Control.MouseDefaults()		// mit Maus verschieb- und zoombar\n";
		$control .= "				new OpenLayers.Control.Navigation()		// mit Maus verschieb- und zoombar\n";
		$control .= "			],\n";
		$control .= "				maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),\n";
		$control .= "				maxResolution: 156543.0399,\n";
		$control .= "				numZoomLevels: 19,\n";
		$control .= "				units: \"m\",\n";
		$control .= "				projection: new OpenLayers.Projection(\"EPSG:900913\"),\n";
		$control .= "				displayProjection: new OpenLayers.Projection(\"EPSG:4326\")\n";
		$control .= "			} );\n";

		if ( $adminonly === false) {
			//	$control .= "				var layer_overviewmap = new OpenLayers.Layer.OSM.Mapnik(\"Mapnik\");\n";
			//	$control .= "				map.addControl(new OpenLayers.Control.OverviewMap({layers: [layer_overviewmap]}));\n";
			//	$control .= "			// Uebersicht\n";
		}
		$control .= "// <!-- parseOSMMapControl END -->\n";
		return $control;
	}

	/**
	 *
	 * @return string
	 */
	private function markerFunctionOSM() {
		$map = "// <!-- markerFunctionCOM_JTG BEGIN -->\n";
		// $map .= "function createMarker(point,html) {\n";
		// $map .= "var marker = new GMarker(point);\n";
		// $map .= "GEvent.addListener(marker, 'click', function() {\n";
		// $map .= "marker.openInfoWindowHtml(html);\n";
		// $map .= "});\n";
		// $map .= "return marker;\n";
		// $map .= "}\n";
		$map .= "// <!-- markerFunctionCOM_JTG END -->\n";

		return $map;
	}

	/**
	 *
	 * @return string
	 */
	private function parseOSMMap() {
		$string = "// <!-- parseOSMMap BEGIN -->\n";
		/*
		 $cfg =& jtgHelper::getConfig();
		 switch($cfg->map_type) {
		 case "0":
		 $type = "G_NORMAL_MAP";
		 break;

		 case "1":
		 $type = "G_SATELLITE_MAP";
		 break;

		 case "2":
		 $type = "G_HYBRID_MAP";
		 break;

		 case "3":
		 $type = "G_PHYSICAL_MAP";
		 break;

		 default:
		 $type = "G_NORMAL_MAP";
		 break;
		 }
		 */
		// $string .= "map = new GMap2(document.getElementById('map'));\n";
		// $string .= "map.addMapType(G_PHYSICAL_MAP);\n";
		// $string .= "map.setMapType(".$type.");\n";

		$string .= "// <!-- parseOSMMap END -->\n";
		return $string;
	}

	/**
	 *
	 * @param object $track
	 * @return string
	 */
	private function parseStartPointOSM($coords) {
		//		deprecated
		$map = "// <!-- parseStartPointCOM_JTG BEGIN -->\n";
		$rows = explode("\n",$coords);
		$fetch = false;
		foreach ( $rows AS $row ) {
			if ( trim($row) == "// fetchCoordsEnd" ) $fetch = false;
			if ( $fetch == true ) {
				$row = preg_replace(array('/\[/','/\]/'),'',trim($row));
				$row = explode(',',$row);
				$lon = $row[0];
				$lat = $row[1];
				if (!isset($start))
				$start = $lon.", ".$lat;
				$stop = $lon.", ".$lat;
			}
			if ( trim($row) == "// fetchCoordsBegin" ) $fetch = true;
		}
		$map .= "		layerStartZiel = new OpenLayers.Layer.Markers(\"Start/Ziel\");
			map.addLayer(layerStartZiel);
			layerStartZiel.setVisibility(true);

		var lonLatStart = new OpenLayers.LonLat(".$start.").transform(new OpenLayers.Projection(\"EPSG:4326\"), map.getProjectionObject());
		var lonLatZiel = new OpenLayers.LonLat(".$stop.").transform(new OpenLayers.Projection(\"EPSG:4326\"), map.getProjectionObject());
		var sizeStart = new OpenLayers.Size(24,24);
		var sizeZiel = new OpenLayers.Size(24,24);
		var offsetStart = new OpenLayers.Pixel(-3,-22);
		var offsetZiel = new OpenLayers.Pixel(-11,-21);
		var iconStart = new OpenLayers.Icon(\"".JURI::root()."components".DS."com_jtg".DS."assets".DS."images".DS."start.png\",sizeStart,offsetStart);
		var iconZiel = new OpenLayers.Icon(\"".JURI::root()."components".DS."com_jtg".DS."assets".DS."images".DS."ziel.png\",sizeZiel,offsetZiel);
		layerStartZiel.addMarker(new OpenLayers.Marker(lonLatStart,iconStart));
		layerStartZiel.addMarker(new OpenLayers.Marker(lonLatZiel,iconZiel));
// <!-- parseStartPointCOM_JTG END -->\n";
		return $map;
	}

	/**
	 *
	 * @param string $file
	 * @return array
	 */
	private function parseXMLlinesOSM($file,$xml) {
		//		global $osmmicrotime;
		$bbox_lat_max = -90;
		$bbox_lat_min = 90;
		$bbox_lon_max = -180;
		$bbox_lon_min = 180;

		$cfg =& jtgHelper::getConfig();
		$iconpath = JURI::root()."components/com_jtg/assets/template/".$cfg->template."/images/";
		//		$i = 0;
		//		$foundtracks = 0;
//TODO REMOVE THIS deprecated
//		$link = JFactory::getURI();	
//		$link = $link->_uri;
		$link = JURI::current();
		//		$this->gpsFile = $file;
		//		$xml = ($this->loadFile());
		$string_se = "";
		$string = "// <!-- parseXMLlinesCOM_JTG BEGIN -->\n";
		$counttracks=0;
		//		while (true) {
		//			$m = microtime(true);
		$counttracks = $this->findTracks($file,$xml);
		//			if ( $coords != false ) {
		//				$foundtracks = 0;
		//				$counttracks++;
		//			} elseif ( $foundtracks > 10 ) {
		//				break;
		//			}
		//			$foundtracks++;
		//			$i++;
		//		}
		if($counttracks == 0) return;
		$counttracks = $this->calculateAllColors($counttracks);
		//		$foundtracks = 0;
		$i = 0;
		while (true) {
			$m = microtime(true);
			$coords = $this->getCoords($file,$i);
			if ( $coords != false ) {
				$track = $xml->trk[$i];
				$subid = $link."&amp;subid=".$i;
				$string .= "layer_vectors = new OpenLayers.Layer.Vector(";
				$string .= "\"".JText::_('COM_JTG_TRACK').$i.": ".$track->name."\"";
				$string .= ", { displayInLayerSwitcher: true }";
				$string .= ")";
				$string .= ";map.addLayer(layer_vectors)";
				// 			$string .= ";map.addLayer(layerMapnik)";
				$string .= ";\n";
				// 		if ( $i != 0 ) // nur erste Spur standardmäßig anzeigen
				// 			$string .= "layer_vectors.setVisibility(false);\n";
				$string .= "var geometries = new Array();geometries.push(drawLine([\n";
				//$string .= "// fetchCoordsBegin\n";
				if ($coords) {
					$j = 0;
					$coordscount = (count($coords)-1);
					$n=0;
					foreach($coords as $key => $fetch) {

						if ( $coords[$key][1] > $bbox_lat_max ) $bbox_lat_max = $coords[$key][1];
						if ( $coords[$key][1] < $bbox_lat_min ) $bbox_lat_min = $coords[$key][1];
						if ( $coords[$key][0] > $bbox_lon_max ) $bbox_lon_max = $coords[$key][0];
						if ( $coords[$key][0] < $bbox_lon_min ) $bbox_lon_min = $coords[$key][0];
						if ($j == 0)
						$start = ($coords[$key][0].",".$coords[$key][1]);
						elseif ($j == $coordscount)
						$stop = ($coords[$key][0].",".$coords[$key][1]);
						$string .= "[".$coords[$key][0].",".$coords[$key][1]."]";
						if($n != $coordscount)
						$string .= ",\n";
						else
						$string .= "\n";
						$j++;
						$n++;
						//						if ( $i > 1000 ) break 2; // emergency brake
						//						if ( ( microtime(true) - $osmmicrotime ) > 30 )
						//						break;

					}
				}
				//			$string .= "// fetchCoordsEnd\n";
				$string .= "],\n{";
				$color = "#".$counttracks[$i];
				$string .= "strokeColor:\"".$color."\",\n";
				$string .= "strokeWidth: 3,\n";
				// 			$string .= "fillColor: \"".$this->getHexColor()."\",\n";
				// 			$string .= "fillOpacity: 0.4";
				$string .= "strokeOpacity: 0.7";
				$string .= "}));\n";

				$string_se .= "var lonLatStart".$i." = new OpenLayers.LonLat(".$start.").";
				$string_se .= "transform(new OpenLayers.Projection(\"EPSG:4326\"), map.getProjectionObject());\n";
				$string_se .= "var lonLatZiel".$i." = new OpenLayers.LonLat(".$stop.").";
				$string_se .= "transform(new OpenLayers.Projection(\"EPSG:4326\"), map.getProjectionObject());\n";
				$string_se .= "var sizeStart".$i." = new OpenLayers.Size(24,24);\n";
				$string_se .= "var sizeZiel".$i." = new OpenLayers.Size(24,24);\n";
				$string_se .= "var offsetStart".$i." = new OpenLayers.Pixel(-3,-22);\n";
				$string_se .= "var offsetZiel".$i." = new OpenLayers.Pixel(-11,-21);\n";
				$string_se .= "var iconStart".$i." = ";
				$string_se .= "new OpenLayers.Icon(\"".$iconpath."trackStart.png\",";
				$string_se .= "sizeStart".$i.",offsetStart".$i.");\n";
				$string_se .= "var iconZiel".$i." = new OpenLayers.Icon(\"".$iconpath."trackDest.png\",";
				$string_se .= "sizeZiel".$i.",offsetZiel".$i.");\n";
				// 			$string_se .= "layer_startziel.addMarker(new OpenLayers.Marker(lonLatStart".$i.",iconStart".$i."));\n";
				$string_se .= "layer_startziel.addMarker(new OpenLayers.Marker(lonLatZiel".$i.",iconZiel".$i."));\n";
				$string_se .= "popupClassStart = AutoSizeAnchored;\n";
				// 			$name[$i] = $track->name;
				// 			$string_se .= "popupContentHTMLStart = '<span style=\"background-color:#000\"<a href=\"".$subid."\"><b>";
				$string_se .= "popupContentHTMLStart = '";
				//				$string_se .= "<b>";
				$string_se .= JText::_('COM_JTG_TRACK').$i.": ".
					"<font style=\"font-weight: bold;\" color=\"".$color."\">".$track->name;
				// 			$string_se .= "</font></b></a></span>';\n";
				$string_se .= "</font>";
				//				$string_se .= "</b>";
				$string_se .= "';\n";
				$string_se .= "addlayer_startziel(lonLatStart".$i.", popupClassStart, popupContentHTMLStart, true, false, iconStart".$i.", map);\n";

				$foundtracks = 0;
			} elseif ( $foundtracks > 10 ) break;
			// 			Überspringe 10 Spuren ohne Punkte
			$foundtracks++;
			$i++;
		}
		if (isset($track))
		$trackname = $track->name;
		else
		$trackname = $file;
		$string .= "layer_startziel = new OpenLayers.Layer.Markers(";
		$string .= "\"".$i.": ".$trackname."\"";
		$string .= ", { displayInLayerSwitcher: false }";
		$string .= ");";
		$string .= "map.addLayer(layer_startziel);";
		$string .= "layer_startziel.setVisibility(true);";
		$string .= $string_se;
		$string .= "// <!-- parseXMLlinesCOM_JTG END -->\n";

		$center = "// <!-- parseOSMMapCenterSingleTrack BEGIN -->\n";
		$center .= "var min = lonLatToMercator(new OpenLayers.LonLat";
		$center .= "(".$bbox_lon_min.",".$bbox_lat_min."));\n";
		$center .= "var max = lonLatToMercator(new OpenLayers.LonLat";
		$center .= "(".$bbox_lon_max.",".$bbox_lat_max."));\n";
		$center .= "map.zoomToExtent(new OpenLayers.Bounds(min.lon, min.lat, max.lon, max.lat));\n";
		$center .= "// <!-- parseOSMMapCenterSingleTrack END -->\n";

		return array( "coords" => $string, "center" => $center );
	}

	/**
	 *
	 * @param string $file
	 * @return array
	 */
	public function giveClickLinks($file) {
		$i = 0;
		$links = array();
		$this->gpsFile = $file;
		while (true) {
			$coords = $this->getCoords($file,$i);
			if ( $coords == false ) break;
			$xml = $this->loadFile();
			$xml = $xml->trk[$i];
			$link = JFactory::getURI();
			$link = $link->_uri;
			$link = $link."&amp;subid=".$i;
			$name = (string)$xml->name;
			$links[$i]['link'] = $link;
			$links[$i]['name'] = $name;
			$i++;
		}
		return $links;
	}

	/**
	 * checks if the given file is a GPX or KML file and call the function for it
	 *
	 * @param string $file
	 * @return array
	 */
	public function getCoords($file,$trackid=0) {
		jimport('joomla.filesystem.file');

		$ext = JFile::getExt($file);

		if($ext == 'kml') {
			$coords =$this->getCoordsKML($file,$trackid);
			return $coords;
		} else if($ext == 'gpx') {
			$coords = $this->getCoordsGPX($file,$trackid);
			if ( $coords == false ) return false;
			return $coords;
		} else if ($ext == 'tcx') {
			$coords = $this->getCoordsTCX($file,$trackid);
			return $coords;
		} else {
			return JText::_('COM_JTG_FILE_ERROR');
		}

	}

	public function findTracks($file,$xml) {
		jimport('joomla.filesystem.file');

		$ext = JFile::getExt($file);

		if($ext == 'kml') {
			$coords =$this->findTracksKML($xml);
			return $coords;
		} else if($ext == 'gpx') {
			$coords = $this->findTracksGPX($xml);
			if ( $coords == false ) return false;
			return $coords;
		} else if ($ext == 'tcx') {
			$coords = $this->findTracksTCX($xml);
			return $coords;
		} else {
			return JText::_('COM_JTG_FILE_ERROR');
		}

	}

	/**
	 *
	 * @param string $file
	 * @return array
	 */
	private function getCoordsKML($file,$trackid=0) {

		if (file_exists($file)) {
			$xml = simplexml_load_file($file);
			$namespaces = $xml->getNamespaces();

			$newns = array();
			foreach($namespaces as $key => $ns) {
				array_push($newns, $ns);
			}

			$doc = new DOMDocument;
			$doc->load($file);

			$xpath = new DOMXPath($doc);
			$xpath->registerNamespace('kml', $newns[0]);

			$query = $xpath->query('//kml:LineString/kml:coordinates/text()');

			$coords = $query->item(0)->nodeValue;
			// catch different types coordinates are written in kml files
			$coords = str_replace("\n", '/',$coords);
			$coords = str_replace(" ", '/',$coords);

			$coords = explode('/', $coords);
			$newco = array();
			for($i=0, $n=count($coords); $i<$n; $i++) {
				if($coords[$i] != NULL) {
					$file = explode(',', $coords[$i]);
					$a = array($file[0],$file[1],$file[2],0,0);
					array_push($newco, $a);
				}
			}
			$start = array_pop($newco);

			return $newco;

		} else {
			return false;
		}

	}

	/**
	 *
	 * @param string $file
	 * @return array
	 */
	private function getCoordsGPX($file,$trackid=0) {
		if (!file_exists($file)) return false;
		$xml = simplexml_load_file($file);
		$start = array();
		for($j=0; $j < @count($xml->trk[$trackid]->trkseg); $j++) {
			for($i=0; $i < count($xml->trk[$trackid]->trkseg[$j]->trkpt); $i++) {
				//				if ( $i > 100 ) return $start; // emergency brake
				$trkpt = $xml->trk[$trackid]->trkseg[$j]->trkpt[$i];
				if(isset($trkpt->attributes()->lat) && isset($trkpt->attributes()->lon)) {
					$lat = $trkpt->attributes()->lat;
					$lon = $trkpt->attributes()->lon;
					if(isset($trkpt->ele)) {
						$ele = $trkpt->ele;
					} else {
						$ele = "0";
					}
					if(isset($trkpt->time)) {
						$time = $trkpt->time;
					} else {
						$time = "0";
					}
					$start[] = array((string)$lon, (string)$lat,(string)$ele,(string)$time,0);
				}
			}
		}
		return $start;
	}

	/**
	 *
	 * @param string $file
	 * @return array
	 */
	private function findTracksGPX($xml) {
		$start = 0;
		$trackid = 0;
		$foundtracks = 0;
		$notfoundtracks = 0;
		while ($notfoundtracks <= 10)
		{
			for($j=0; $j < @count($xml->trk[$trackid]); $j++)
			{
				$trkpt = @$xml->trk[$trackid]->trkseg[$j]->trkpt;
				if (
				( $trkpt !== null )
				AND (@isset($trkpt->attributes()->lat)
				AND @isset($trkpt->attributes()->lon))
				)
				{
					$start++;
					$foundtracks++;
				}
			}
			if ( $j == 0 ) $notfoundtracks++;
			$trackid++;
		}
		return $start;
	}

	/**
	 *
	 * @param string $file
	 * @return array
	 */
	private function getCoordsTCX($file,$trackid=0) {
		if (file_exists($file)) {
			$xml = simplexml_load_file($file);
			if(isset($xml->Activities->Activity->Lap->Track)) {
				$startpoint = $xml->Activities->Activity->Lap->Track[$trackid];
			} elseif (isset($xml->Courses->Course->Track)) {
				$startpoint = $xml->Courses->Course->Track[$trackid];
			}

			$coords = array();
			if (!$startpoint[0]) return false;
			foreach($startpoint[0] as $start) {
				if(isset($start->Position->LatitudeDegrees) && isset($start->Position->LongitudeDegrees)) {
					$lat = $start->Position->LatitudeDegrees;
					$lon = $start->Position->LongitudeDegrees;
					$ele = $start->AltitudeMeters;
					$time = $start->Time;
					if(isset($start->HeartRateBpm->Value)) {
						$heart = $start->HeartRateBpm->Value;
					} else {
						$heart = "0";
					}

					$bak = array((string)$lon,(string)$lat,(string)$ele,(string)$time,(string)$heart);
					array_push($coords,$bak);
				}
			}
			return $coords;
		} else {
			return false;
		}
	}

	// Osm END

	// Google BEGIN

	/**
	 *
	 * this writes the Javascript code for the overview map
	 * the map is shown in a div container with <strong>id="map"</strong>
	 * <code>
	 * $gps = new gpsClass();
	 * $track = loadObject($query);
	 * $gps->writeMap($track);
	 * </code>
	 *
	 * @return string Javascriptcode
	 */
	public function writeGoogleMap() {

		$cnates = $this->getMapNates();
		$rows = $this->getTracks();

		$map = "\n";
		$map .= "<script type=\"text/javascript\">//<![CDATA[\n";
		$map .= "	google.load(\"maps\", \"2\");\n";
		$map .= "	google.load(\"search\", \"1\");\n";
		$map .= "	var map;\n";
		$map .= $this->markerFunctionGoogle();
		$map .= "	function initialize() {\n";
		$map .= $this->parseGoogleMap();
		$map .= "		var bounds = new GLatLngBounds();\n";

		$map .= $this->parseGoogleMapControl();
		$map .= "		map.addControl(new google.maps.LocalSearch(),\n";
		$map .= "			new GControlPosition(G_ANCHOR_BOTTOM_LEFT, new GSize(10,20)));\n";
		$map .= $this->parseMarkersGoogle();
		$map .= "		map.setCenter(bounds.getCenter(), map.getBoundsZoomLevel(bounds));\n";
		$map .= $this->parseScriptGoogleFooter();

		return $map;
	}

	/**
	 * this function shows the map for a single track
	 * the map is shown in a div container with id="map"
	 * $track is the database object of the track
	 * <code>
	 * $gps = new gpsClass();
	 * $track = loadObject($query);
	 * $gps->writeTrack($track);
	 * </code>
	 *
	 * @global object $mainframe
	 * @param object $track
	 * @return string Javascriptcode
	 */
	public function writeTrackGoogle($track) {
		$mainframe =& JFactory::getApplication();
		jimport('joomla.filesystem.file');

		$file = "components".DS."com_jtg".DS."uploads".DS.$track->file;
		$map = "";

		$map .= $this->parseScriptGoogleHead();
		$map .= $this->parseGoogleMap();
		$map .= 'var bounds = new GLatLngBounds();';
		$map .= $this->parseGoogleMapControl();
		$map .= $this->parseStartPointGoogle($track);
		$map .= $this->parseXMLlinesGoogle("./".$file);
		$map .= "map.setCenter(bounds.getCenter(), map.getBoundsZoomLevel(bounds));\n";
		$map .= $this->parseScriptGoogleFooter();

		return $map;
	}

	/**
	 *
	 * @return string
	 */
	private function parseScriptGoogleHead() {

		$map = "";
		$map .= "\n<script type='text/javascript'>//<![CDATA[\n";
		$map .= "google.load('maps', '2');\n";
		$map .= "var map\n";
		$map .= "function initialize() {\n";

		return $map;
	}

	/**
	 *
	 * @return string
	 */
	private function parseScriptGoogleFooter() {

		$map = "";
		$map .= "	}\n";
		$map .= "	google.setOnLoadCallback(initialize);\n";
		$map .= "//]]>\n";
		$map .= "</script>\n";

		return $map;
	}

	/**
	 *
	 * @return string
	 */
	private function parseGoogleMapControl() {

		$control = "";
		$control .= "		var mapControl = new GMapTypeControl();\n";
		$control .= "		map.addControl(mapControl);\n";
		$control .= "		map.addControl(new GLargeMapControl());\n";
		$control .= "		map.addControl(new GOverviewMapControl());\n";

		return $control;
	}

	/**
	 *
	 * @return string
	 */
	private function markerFunctionGoogle() {
		$map = "";
		$map .= "	function createMarker(point,html) {\n";
		$map .= "		var marker = new GMarker(point);\n";
		$map .= "		GEvent.addListener(marker, 'click', function() {\n";
		$map .= "			marker.openInfoWindowHtml(html);\n";
		$map .= "		});\n";
		$map .= "		return marker;\n";
		$map .= "	}\n";

		return $map;
	}

	/**
	 *
	 * @return string
	 */
	private function parseMarkersGoogle() {
		$rows = $this->getTracks();
		$map = "";
		foreach($rows as $row) {
			$link = JROUTE::_("index.php?option=com_jtg&view=files&layout=file&id=".$row->id);
			$map .= "		var point = new GLatLng(".$row->start_n.", ".$row->start_e.");";
			$map .= "bounds.extend(point);";
			$map .= "var marker = createMarker( point,\"";
			$map .= "<a href=\\\"".$link."\\\">";
			$map .= "<b>".$row->title."</b>";		// ToDo: Link für Nicht-Registrierte nur als Text anzeigen
			$map .= "</a>";
			if ( $row->access == 1 )
			$map .= "&nbsp;<img src=\\\"components/com_jtg/assets/images/registred_only.png\\\" />";
			$map .= "<br />".JText::_('COM_JTG_CAT').": ".$row->cat."\"); map.addOverlay(marker);\n";
		}

		return $map;
	}

	/**
	 *
	 * @return string
	 */
	private function parseGoogleMap() {
		$cfg =& jtgHelper::getConfig();
		switch($cfg->map_type) {
			case "0":
				$type = "G_NORMAL_MAP";
				break;

			case "1":
				$type = "G_SATELLITE_MAP";
				break;

			case "2":
				$type = "G_HYBRID_MAP";
				break;

			case "3":
				$type = "G_PHYSICAL_MAP";
				break;

			default:
				$type = "G_NORMAL_MAP";
				break;
		}

		$string = "";
		$string .= "		map = new GMap2(document.getElementById('map'));\n";
		$string .= "		map.addMapType(G_PHYSICAL_MAP);\n";
		$string .= "		map.setMapType(".$type.");\n";

		return $string;
	}

	/**
	 *
	 * @param object $track
	 * @return string
	 */
	private function parseStartPointGoogle($track) {
		$map = "";
		$map .= "var blueIcon = new GIcon(G_DEFAULT_ICON);\n";
		$map .= "blueIcon.image = '".JURI::base()."components".DS."com_jtg".DS."assets".DS."images".DS."gruen.png';\n";
		$map .= "markerOptions = { icon:blueIcon };\n";
		$map .= "var point = new GLatLng(".$track->start_n.",".$track->start_e.")\n";
		$map .= "map.addOverlay(new GMarker(point, markerOptions));\n";

		return $map;
	}

	/**
	 *
	 * @param string $file
	 * @return array
	 */
	private function parseXMLlinesGoogle($file) {

		$coords = $this->getCoords($file);

		$string = "";
		$string .= "var polyline = new GPolyline([\n";
		foreach($coords as $key => $fetch) {
			$string .= "new GLatLng(".$coords[$key][1].",".$coords[$key][0]."),\n";
		}
		$string .= "], '#ff0000', 4);\n";
		$string .= "map.addOverlay(polyline);\n";
		foreach($coords as $key => $fetch) {
			$string .= "bounds.extend(new GLatLng(".$coords[$key][1].",".$coords[$key][0]."));";
		}

		return $string;
		// print_r($coords);
	}

	// Google END

}
