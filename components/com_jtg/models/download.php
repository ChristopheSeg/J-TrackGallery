<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: download.php,v 1.4 2011/04/08 15:48:23 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage frontend
 * @license GNU/GPL
 * @filesource
 *
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class jtgModelDownload extends JModel  {
	/**
	 *
	 * @global object $mainframe
	 * @param integer $id
	 * @param string $format
	 * @return string
	 */
	function download($id, $format, $track)  {
		$mainframe =& JFactory::getApplication();

		jimport('joomla.filesystem.file');
		$file = "." . DS . "components" . DS . "com_jtg" . DS . "uploads" . DS . $track->file;
		$ext = JFile::getExt($file['name']);
		$gps = new gpsClass();
		$coords = $gps->getCoords($file);

		switch($format){
			case "kml":
				$file = "";
				$file .= "<?xml version='1.0' encoding='UTF-8'?>";
				$file .= "<kml xmlns='http://www.opengis.net/kml/2.2'>";
				$file .= "<Document>";
				$file .= "<name>".$track->title."</name>";
				$file .= "<description>Generated by ".$mainframe->getCfg('sitename')." ".JURI::base()."</description>";
				$file .= "<Folder>";
				$file .= "<Placemark>";
				$file .= "<name>".$track->title."</name>";
				$file .= "<description>".htmlentities($track->description)."</description>";
				$file .= "<Point>";
				$file .= "<coordinates>".$track->start_e.",".$track->start_n.",0.0000000</coordinates>";
				$file .= "</Point>";
				$file .= "</Placemark>";
				$file .= "<Placemark>";
				$file .= "<name>".$track->title."</name>";
				$file .= "<description>Generated by ".$mainframe->getCfg('sitename')." ".JURI::base()."</description>";
				$file .= "<MultiGeometry>";
				$file .= "<LineString>";
				$file .= "<coordinates>";
				for($i=0, $n=count($coords); $i<$n; $i++){
					$coord = &$coords[$i];
					if($i != $n-1) {
						$file .= $coord[0].",".$coord[1].",".$coord[2]."";
					} else {
						$file .= $coord[0].",".$coord[1].",".$coord[2]."</coordinates>";
					}
				}
				$file .= "</LineString>";
				$file .= "</MultiGeometry>";
				$file .= "</Placemark>";
				$file .= "</Folder>";
				$file .= "</Document>";
				$file .= "</kml>";

				return $file;
				break;

			case "gpx":
				$header = "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>";
				$header .= "<gpx ";
				$header .= "xmlns=\"http://www.topografix.com/GPX/1/1\" ";
				$header .= "creator=\"J!Track Gallery - http://jtrackgallery.net/forum\" ";
				$header .= "version=\"1.1\" ";
				$header .= "xsi:schemaLocation=\"http://www.topografix.com/GPX/1/1 http://www.topografix.com/GPX/1/1/gpx.xsd\" ";
				$header .= "xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" ";
				$header .= ">";
				$metadata = "<metadata>";
				$metadata .= "<name>".$track->title."</name>";
				$metadata .= "<copyright author=\"".$mainframe->getCfg('sitename')."\" />";
				$metadata .= "<link href=\"".JURI::base()."index.php?option=com_jtg&amp;view=track&amp;layout=track&amp;id=".$id."\">";
				$metadata .= "<text>".$track->title." on ".$mainframe->getCfg('sitename')."</text>";
				$metadata .= "</link>";
				$date = gmdate("Y-m-d");
				$time = gmdate("H:i:s");
				$metadata .= "<time>" . $date . "T" . $time . "Z</time>";
				$trk = "<trk>";
				$trk .= "<name>".$track->title."</name>";
				$trk .= "<link href=\"".JURI::base()."index.php?option=com_jtg&amp;view=track&amp;layout=track&amp;id=".$id."\" />";
				$trk .= "<trkseg>";
				$minlat = 180;
				$maxlat = -180;
				$minlon = 90;
				$maxlon = -90;
				for($i=0, $n=count($coords); $i<$n; $i++)  {
					$coord = &$coords[$i];
					if ( $coord[0] < $minlat )	$minlat = $coord[0];
					if ( $coord[1] < $minlon )	$minlon = $coord[1];
					if ( $coord[0] > $maxlat )	$maxlat = $coord[0];
					if ( $coord[1] > $maxlon )	$maxlon = $coord[1];

					$trk .= "<trkpt lat=\"".$coord[1]."\" lon=\"".$coord[0]."\">";
					if($coord[2] != NULL)
					$trk .= "<ele>".$coord[2]."</ele>";
					if($coord[3] != NULL)
					$trk .= "<time>".$coord[3]."</time>";
					$trk .= "</trkpt>";
				}
				$metadata .= "<bounds minlat=\"" . $minlat . "\" minlon=\"" . $minlon . "\" maxlat=\"" . $maxlat . "\" maxlon=\"" . $maxlon . "\"/>";
				$metadata .= "</metadata>";
				$trk .= "</trkseg>";
				$trk .= "</trk>";
				$footer = "</gpx>";

				return $header.$metadata.$trk.$footer;
				break;

			case "tcx":
				$file ="";
				$file .= "<?xml version='1.0' encoding='UTF-8' standalone='no' ?>\n";
				$file .= "<TrainingCenterDatabase xmlns='http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://www.garmin.com/xmlschemas/TrainingCenterDatabase/v2 http://www.garmin.com/xmlschemas/TrainingCenterDatabasev2.xsd'>\n";
				$file .= "  <Folders/>\n";
				$file .= "  <Courses>\n";
				$file .= "    <Course>\n";
				$file .= "      <Name>".$track->title."</Name>\n";
				$file .= "        <Lap>\n";
				$file .= "          <TotalTimeSeconds></TotalTimeSeconds>\n";
				$file .= "          <DistanceMeters>".($track->distance * 1000)."</DistanceMeters>\n";
				$file .= "              <BeginPosition>\n";
				$file .= "                  <LatitudeDegrees>".$track->start_e."</LatitudeDegrees>\n";
				$file .= "                  <LongitudeDegrees>".$track->start_n."</LongitudeDegrees>\n";
				$file .= "              </BeginPosition>\n";
				$file .= "              <EndPosition>\n";
				$file .= "                  <LatitudeDegrees></LatitudeDegrees>\n";
				$file .= "                  <LongitudeDegrees></LongitudeDegrees>\n";
				$file .= "              </EndPosition>\n";
				$file .= "              <AverageHeartRateBpm xsi:type='HeartRateInBeatsPerMinute_t'>\n";
				$file .= "                  <Value></Value>\n";
				$file .= "              </AverageHeartRateBpm>\n";
				$file .= "              <MaximumHeartRateBpm xsi:type='HeartRateInBeatsPerMinute_t'>\n";
				$file .= "                  <Value></Value>\n";
				$file .= "              </MaximumHeartRateBpm>\n";
				$file .= "              <Intensity>Active</Intensity>\n";
				$file .= "         </Lap>\n";
				$file .= "         <Track>\n";
				for($i=0, $n=count($coords); $i<$n; $i++)  {
					$coord = &$coords[$i];
					$file .= "              <Trackpoint>\n";
					$file .= "                  <Time>".$coord[3]."</Time>\n";
					$file .= "                  <Position>\n";
					$file .= "                      <LatitudeDegrees>".$coord[1]."</LatitudeDegrees>\n";
					$file .= "                      <LongitudeDegrees>".$coord[0]."</LongitudeDegrees>\n";
					$file .= "                  </Position>\n";
					$file .= "                  <AltitudeMeters>".$coord[2]."</AltitudeMeters>\n";
					$file .= "                  <DistanceMeters>0</DistanceMeters>\n";
					$file .= "                  <HeartRateBpm xsi:type='HeartRateInBeatsPerMinute_t'>\n";
					$file .= "                      <Value>".$coord[4]."</Value>\n";
					$file .= "                  </HeartRateBpm>\n";
					$file .= "                  <SensorState>Absent</SensorState>\n";
					$file .= "              </Trackpoint>\n";
				}
				$file .= "      </Track>\n";
				$file .= "    </Course>\n";
				$file .= "  </Courses>\n";
				$file .= "  <Author xsi:type='Application_t'>\n";
				$file .= "      <Name>".$mainframe->getCfg('sitename')."</Name>\n";
				$file .= "      <Build>\n";
				$file .= "        <Version>\n";
				$file .= "          <VersionMajor>2</VersionMajor>\n";
				$file .= "          <VersionMinor>2</VersionMinor>\n";
				$file .= "          <BuildMajor>0</BuildMajor>\n";
				$file .= "          <BuildMinor>0</BuildMinor>\n";
				$file .= "        </Version>\n";
				$file .= "        <Type>Alpha</Type>\n";
				$file .= "        <Time>".$track->date."</Time>\n";
				$file .= "        <Builder>".$track->user."</Builder>\n";
				$file .= "      </Build>\n";
				$file .= "      <LangID>EN</LangID>\n";
				$file .= "      <PartNumber>".$track->id."</PartNumber>\n";
				$file .= "   </Author>\n";
				$file .= "</TrainingCenterDatabase>\n";

				return $file;
				break;
		}

	}

	/**
	 *
	 * @global object $mainframe
	 * @param integer $id
	 * @return object
	 */
	function getFile($id)  {
		$mainframe =& JFactory::getApplication();

		$db =& JFactory::getDBO();

		$query = "SELECT a.*, b.title AS cat, b.image AS image, c.username AS user"
		. "\n FROM #__jtg_files AS a"
		. "\n LEFT JOIN #__jtg_cats AS b ON a.catid=b.id"
		. "\n LEFT JOIN #__users AS c ON a.uid=c.id"
		. "\n WHERE a.id='" .$id. "'";

		$db->setQuery($query);
		$track = $db->loadObject();

		return $track;
	}

}
