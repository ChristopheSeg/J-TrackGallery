<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @author      Christophe Seguinot <christophe@jtrackgallery.net>
 * @author      Pfister Michael, JoomGPStracks <info@mp-development.de>
 * @author      Christian Knorr, InJooOSM  <christianknorr@users.sourceforge.net>
 * @copyright   2015 J!TrackGallery, InJooosm and joomGPStracks teams
 *
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link        http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Mainclass to write the map
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @since       0.8
 */
class GpsDataClass
{
	var $gpsFile = null;

	var $sortedcats = null;

	// Array tracks[j]->coords; // array containing longitude latitude elevation time and heartbeat data
	var $track = array();

	var $speedDataExists = false;

	var $elevationDataExists  = false;

	var $beatDataExists = false;

	var $speedData = '';

	var $elevationData = '';

	var $beatData = '';

	var $error = false;

	var $errorMessages = array();

	var $trackCount = 0;

	var $wps = array();

	var $isTrack = false;

	var $isCache = false;

	var $isRoute = false;

	var $isWaypoint = false;

	var $distance = 0;

	var $Date = "";

	var $trackname = "";

	var $fileChecked = false;

	var $description = "";

	var $bbox_lat_max = -90;

	var $bbox_lat_min = 90;

	var $bbox_lon_max = -180;

	var $bbox_lon_min = 180;

	/**
	 * function_description
	 *
	 * @param   unknown_type  $unit  param_description
	 */
	public function __construct($unit)
	{
		$this->unit = $unit;
	}

	/**
	 * This function load and xml file if it exits
	 *
	 * @param   string  $file  the xml file (path) to load
	 *
	 * @return <boolean> gpsclass object
	 */
	public function loadFile($file)
	{
		if (file_exists($file))
		{
			$xml = simplexml_load_file($file);

			return $xml;
		}
		else
		{
			return false;
		}
	}

	/**
	 * function_description
	 *
	 * @param   string  $gpsFile        the gps file (path) to load
	 * @param   string  $trackfilename  track filename
	 *
	 * @return <boolean> gpsclass object
	 */
	public function loadFileAndData($gpsFile, $trackfilename)
	{
		// $xml can not belong to $this (SimpleXMLElement can not be serialized => not cached)

		$this->gpsFile = $gpsFile;
		$this->trackfilename = $trackfilename;

		$xml = $this->loadXmlFile($gpsFile);

		if ($this->error)
		{
			$this->fileChecked = 6;

			return $this;
		}

		if ($xml === false)
		{
			$this->error = true;
			$this->fileChecked = 6;
			$this->errorMessages[] = JText::sprintf('COM_JTG_GPS_FILE_ERROR_0', $this->trackfilename);

			return $this;
		}

		// Extract datas from xml
		switch ($this->ext)
		{
			case "gpx":
				$extract_result = $this->extractCoordsGPX($xml);
				unset($xml);
				break;
			case "kml":
				$extract_result = $this->extractCoordsKML($xml);
				break;
			case "tcx":
				$extract_result = $this->extractCoordsTCX($xml);
				break;
			default:
				$extract_result = null;
				$this->error = true;
				$this->errorMessages[] = JText::_('COM_JTG_GPS_FILE_ERROR');

				return $this;
		}

		if ($this->trackCount == 0)
		{
			$this->fileChecked = 7;
			$this->error = true;
			$this->errorMessages[] = JText::sprintf('COM_JTG_GPS_FILE_ERROR_2', $this->trackfilename);

			return $this;
		}

		// Calculate start,
		$this->start = $this->track[1]->coords[0];
		$this->speedDataExists = ( ( isset ($this->start[3])  && $this->start[3] > 0) ? true: false);
		$this->elevationDataExists = ( isset ($this->start[2])? true: false);
		$this->beatDataExists = ( (isset ($this->start[4]) && $this->start[4] > 0)? true: false);

		// Calculate allCoords, distance, elevation max lon...
		$this->extractAllTracksCoords();

		// Calculate chartData
		$this->createChartData();

		// TODO include WP in new function extractCoordsGPX
		$this->extractWPs();

		$this->fileChecked = true;

		return $this;
	}

	/**
	 * function_description
	 *
	 * @param   string  $gpsFile  the gps file to load
	 *
	 * @return <simplexmlelement> if file exists and is loaded , null otherwise
	 */
	public function loadXmlFile($gpsFile=false)
	{
		jimport('joomla.filesystem.file');
		$xml = false;

		if ( ($gpsFile) and (JFile::exists($gpsFile)) )
		{
			$this->gpsFile = $gpsFile;
			$this->ext = JFile::getExt($gpsFile);
		}
		elseif  (JFile::exists($this->gpsFile))
		{
			// $this->gpsFile = $gpsFile;
			$this->ext = JFile::getExt($this->gpsFile);
		}
		else
		{
			$this->error = true;
			$this->errorMessages[] = JText::sprintf('COM_JTG_GPS_FILE_ERROR_1', ($this->trackfilename?  $this->trackfilename: $gpsFile));

			return false;
		}

		// Enable user error handling
		libxml_use_internal_errors(true);
		libxml_clear_errors();

		if ($this->ext == 'gpx')
		{
			// Open (don't load) GPX xml files using XMLReader
			$xml = new XMLReader;
			$xml->open($this->gpsFile);
		}
		else
		{
			// Load KML and TCX xml files using simplexml_load_file
			$xml = simplexml_load_file($this->gpsFile);
		}

		if ($xml === false)
		{
			// "Failed loading XML\n";

			$this->error = true;

			foreach (libxml_get_errors() as $error)
			{
				switch ($error->level)
				{
					case LIBXML_ERR_WARNING:
						$this->errorMessages[] = "Warning $error->code: ";
						break;
					case LIBXML_ERR_ERROR:
						$this->errorMessages[] = "Error $error->code: ";
						break;
					case LIBXML_ERR_FATAL:
						$this->errorMessages[] = "Fatal Error $error->code: ";
						break;
				}

				$this->errorMessages[] = trim($error->message) .
				"\n  Line: $error->line" .
				"\n  Column: $error->column";
			}
		}

		return $xml;
	}

	/**
	 * function_description
	 *
	 * @return void
	 */
	public function displayErrors()
	{
		$error = "";

		foreach ($this->errorMessages as $errorMessage)
		{
			JFactory::getApplication()->enqueueMessage($errorMessage, 'Warning');
			$error .= '\n' . $errorMessage;
		}

		return $error;
	}

	/**
	 * function_description
	 *
	 * @param   string  $xml  track xml object
	 *
	 * @return array
	 */
	private function extractCoordsKML($xml)
	{
		// TODO use XMLReader
		$xmldom = new DOMDocument;
		$xmldom->loadXML($xml->asXML());

		$rootNamespace = $xmldom->lookupNamespaceUri($xmldom->namespaceURI);
		$xpath = new DomXPath($xmldom);
		$xpath->registerNamespace('kml', $rootNamespace);

		$documentNodes = $xpath->query('kml:Document/kml:name|kml:Document/kml:description');
		$gps_file_name = '';
		$gps_file_description = '';

		// Search for NAME (Title) and description of GPS file
		foreach ($documentNodes as $documentNode)
		{
			switch ($documentNode->nodeName)
			{
				case 'name':
					$gps_file_name .= preg_replace('/<!\[CDATA\[(.*?)\]\]>/s', '', $documentNode->nodeValue);
					break;
				case 'description':
					$gps_file_description .= preg_replace('/<!\[CDATA\[(.*?)\]\]>/s', '', $documentNode->nodeValue);
					break;
			}
		}

		// Search for tracks (name (title), description and coordinates
		$placemarkNodes = $xpath->query('//kml:Placemark');
		$this->trackCount = 0;
		$tracks_description = '';
		$track_name = '';

		foreach ($placemarkNodes as $placemarkNode)
		{
			$nodes = $xpath->query('.//kml:name|.//kml:description|.//kml:LineString|.//kml:coordinates', $placemarkNode);

			if ($nodes)
			{
				$found_linestring = false;
				$name = '';
				$description = '';
				$coordinates = null;

				foreach ($nodes as $node)
				{
					switch ($node->nodeName)
					{
						case 'name':
							$name = $node->nodeValue;
							break;
						case 'description':
							$description = $node->nodeValue;
							$tracks_description .= $description;
							$description = ( ($description = '&nbsp;')? '' : $description);
							break;
						case 'LineString':
							$found_linestring = true;
							break;
						case 'coordinates':
							// Exploit coordinates only when it is a child of LineString

							if ($found_linestring)
							{
								$coordinates = $this->extractKmlCoordinates($node->nodeValue);

								if ($coordinates)
								{
									$coordinatesCount = count($coordinates);
									$this->trackCount++;
									$this->track[$this->trackCount] = new stdClass;
									$this->track[$this->trackCount]->coords = $coordinates;
									$this->track[$this->trackCount]->start = ($coordinates[0][0] . "," . $coordinates[0][1]);
									$this->track[$this->trackCount]->stop = ($coordinates[$coordinatesCount - 1][0] . "," . $coordinates[$coordinatesCount - 1][1]);
								}
							}
							break;
					}
				}
			}

			if ($this->trackCount AND $coordinates)
			{
				$this->track[$this->trackCount]->trackname = ($name? $name : $description);
				$this->track[$this->trackCount]->description = $description;
			}

			// Use description and name for file description
			if ($name OR $description)
			{
				$gps_file_description .= '<br />' . $name . ':' . $description;
			}
		}

		if ($this->trackCount)
		{
			// GPS file name (title) and description
			$this->trackname = $gps_file_name;

			if ( strlen($gps_file_name) > 2)
			{
				$this->trackname = $gps_file_name;
			}

			if ( ( strlen($this->trackname) < 10 ) AND ($this->trackCount == 1))
			{
				$this->trackname .= $this->track[1]->trackname;
			}

			if ( strlen($this->trackname) < 10 )
			{
				$this->trackname .= $this->gpsFile;
			}

			if (($gps_file_description) AND ($tracks_description))
			{
				$this->description = $gps_file_description . '<br />' . $tracks_description;
			}
			elseif ($tracks_description)
			{
				$this->description = $tracks_description;
			}
			else
			{
				$this->description = $this->trackname;
			}

			$this->isTrack = ($this->trackCount > 0);
			$this->isCache = $this->isThisCache($xml);
		}
		// Nothing to return
		return true;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $xml  param_description
	 *
	 * @return return_description
	 */
	public function isThisCache($xml)
	{
		$pattern = "/groundspeak/";

		if ( preg_match($pattern, $xml->attributes()->creator))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $coord_sets  param_description
	 *
	 * @return return_description
	 */
	private function extractKmlCoordinates($coord_sets)
	{
		$coordinates = array();

		if ($coord_sets)
		{
			$coord_sets = str_replace("\n", '/', $coord_sets);
			$coord_sets = str_replace(" ", '/', $coord_sets);
			$coord_sets = explode('/', $coord_sets);

			foreach ($coord_sets as $set_string)
			{
				$set_string = trim($set_string);

				if ($set_string)
				{
					$set_array = explode(',', $set_string);
					$set_size = count($set_array);

					if ($set_size == 2)
					{
						array_push($coordinates, array($set_array[0],$set_array[1], 0, 0, 0));
					}
					elseif ($set_size == 3)
					{
						array_push($coordinates, array($set_array[0],$set_array[1],$set_array[2], 0, 0));
					}
				}
			}
			// Suppress coordinates set with less than 5 points

			if (count($coordinates) < 5)
			{
				$coordinates = null;
			}
		}

		return $coordinates;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $xmlcontents  XMLreader object
	 *
	 * @return return_description
	 */

private function extractCoordsGPX($xmlcontents)
{
	$this->trackname = '';
	$this->trackCount = 0;
	{
		// Iterate nodes
		$countElements = 0;
		$i_wpt = 0;
		$i_trk = 0;
		$wp = array();
		$this->trackCount = 0;

		while ($xmlcontents->read() )
		{
			// Check to ensure nodeType is an Element not attribute or #Text
			if ($xmlcontents->nodeType == XMLReader::ELEMENT)
			{
				// Start element found
				$currentElement = $xmlcontents->localName;
				$endElement = '';
				$countElements++;

				switch ($currentElement)
				{
					case 'time':
						// GPS file Time
						$xmlcontents->read();
						$time = $xmlcontents->value;
						$dt = new DateTime($time);
						$this->Date = $dt->format('Y-m-d');

						// Read end tag
						$xmlcontents->read();
						break;
					case 'wpt':
						$wp[] = (array) $xmlcontents->readInnerXML();
						$i_wpt++;
						$this->wps[$i_wpt] = new WpClass;
						$lat = (float) $xmlcontents->getAttribute('lat');
						$lon = (float) $xmlcontents->getAttribute('lon');
						$this->wps[$i_wpt]->sym = 'wp';
						$this->wps[$i_wpt]->lat = $lat;
						$this->wps[$i_wpt]->lon = $lon;

						if ( $lat > $this->bbox_lat_max )
						{
							$this->bbox_lat_max = $lat;
						}

						if ( $lat < $this->bbox_lat_min )
						{
							$this->bbox_lat_min = $lat;
						}

						if ( $lon > $this->bbox_lon_max )
						{
							$this->bbox_lon_max = $lon;
						}

						if ( $lon < $this->bbox_lon_min )
						{
							$this->bbox_lon_min = $lon;
						}

						$endWptElement = false;

						while ( !$endWptElement )
						{
							$xmlcontents->read();

							if ($xmlcontents->nodeType == XMLReader::END_ELEMENT)
							{
								$endWptElement = ($xmlcontents->localName == 'wpt');
							}
							else
							{
								$endWptElement = false;
							}

							// Extract wpt data
							if ($xmlcontents->nodeType == XMLReader::ELEMENT)
							{
								$key = $xmlcontents->localName;
								$xmlcontents->read();
								$value = $xmlcontents->value;
								$this->wps[$i_wpt]->$key = $value;
								$xmlcontents->read();
							}
						}
						break;

					case 'trk':
						// Track
						$i_trk++;
						$coords = array();
						$trkname = '';

						while ( ('trk' !== $endElement) )
						{
							$xmlcontents->read();

							if ($xmlcontents->nodeType == XMLReader::END_ELEMENT)
							{
								// </xxx> found
								$endElement = $xmlcontents->localName;
							}
							else
							{
								$endElement = '';
							}
							// Extract trk data
							if ( ($xmlcontents->name == 'name') AND ($xmlcontents->nodeType == XMLReader::ELEMENT) )
							{
								$xmlcontents->read();
								$trackname = $xmlcontents->value;

								// Read end tag
								$xmlcontents->read();
							}

							elseif ( ($xmlcontents->name == 'trkseg') AND ($xmlcontents->nodeType == XMLReader::ELEMENT) )
							{
								// Trkseg found
								$endTrksegElement = false;
								$i_trkpt = 0;
								$ele = 0;
								$time = '0';

								while ( !$endTrksegElement )
								{
									$xmlcontents->read();

									if ($xmlcontents->nodeType == XMLReader::END_ELEMENT)
									{
										$endTrksegElement = ($xmlcontents->localName == 'trkseg');
									}
									else
									{
										$endTrksegElement = false;
									}

									if ( ($xmlcontents->name == 'trkpt') AND ($xmlcontents->nodeType == XMLReader::ELEMENT) )
									{
										// Trkpt found

										$i_trkpt++;
										$lat = (float) $xmlcontents->getAttribute('lat');
										$lon = (float) $xmlcontents->getAttribute('lon');

										if ( $lat > $this->bbox_lat_max )
										{
											$this->bbox_lat_max = $lat;
										}

										if ( $lat < $this->bbox_lat_min )
										{
											$this->bbox_lat_min = $lat;
										}

										if ( $lon > $this->bbox_lon_max )
										{
											$this->bbox_lon_max = $lon;
										}

										if ( $lon < $this->bbox_lon_min )
										{
											$this->bbox_lon_min = $lon;
										}
										// Read end tag
										// $xmlcontents->read();
									}

									if ( ($xmlcontents->name == 'ele') AND ($xmlcontents->nodeType == XMLReader::ELEMENT) )
									{
										// Trkpt elevation found
										$xmlcontents->read();
										$ele = (float) $xmlcontents->value;

										// Read end tag
										$xmlcontents->read();
									}

									if ( ($xmlcontents->name == 'time') AND ($xmlcontents->nodeType == XMLReader::ELEMENT) )
									{
										// Trkpt time found
										$xmlcontents->read();
										$time = (string) $xmlcontents->value;

										// Read end tag
										$xmlcontents->read();
									}

									if ( ($xmlcontents->name == 'trkpt') AND ($xmlcontents->nodeType == XMLReader::END_ELEMENT) )
									{
										// End Trkpt
										$coords[] = array((string) $lon, (string) $lat, (string) $ele, (string) $time, 0);
									}
								}

								// End trkseg

								$endTrksegElement = true;
								$coordinatesCount = count($coords);

								if ($coordinatesCount > 1 )
								{
									// This is a track with more than 2 points
									$this->isTrack = true;
									$this->trackCount++;
									$this->track[$this->trackCount] = new stdClass;
									$this->track[$this->trackCount]->description = '';

									if ($trackname)
									{
										$this->track[$this->trackCount]->trackname = $trackname;
									}
									else
									{
										$this->track[$this->trackCount]->trackname = $this->trackname . '-' . (string) $this->trackCount;
									}

									$this->track[$this->trackCount]->coords = $coords;
									$this->track[$this->trackCount]->start = ($coords[0][0] . "," . $coords[0][1]);
									$this->track[$this->trackCount]->stop = ($coords[$coordinatesCount - 1][0] . "," . $coords[$coordinatesCount - 1][1]);
								}
							}
							else
							{
								// Tag is not trk, trkseg, nor Name: proceed
							}
						}
						break;
				}
			}
		}

		if (strlen($this->trackname) == 0)
		{
			if ($this->trackCount == 1)
			{
				if ($this->track[1]->trackname)
				{
					$this->trackname = $this->track[1]->trackname;
				}
				else
				{
					$this->trackname = $this->trackfilename;
				}
			}
			else
			{
				$this->trackname = $this->trackfilename;
			}
		}

		if (!$this->description)
		{
			if ($this->trackCount == 1)
			{
				$this->description = $this->track[1]->description? $this->track[1]->description: '';
			}
			elseif ($this->trackCount > 1)
			{
				$this->description = $this->track[1]->description? $this->track[1]->description: '';

				for ($i = 2; $i <= $this->trackCount; $i++)
				{
				$this->description .= '<br>' . $this->track[$i]->description? $this->track[$i]->description: '';
				}
			}
		}

		$xmlcontents->close();

	}

// Nothing to return
return true;
}

	/**
	 * function_description
	 *
	 * @param   string   $file     param_description
	 * @param   integer  $trackid  track id
	 *
	 * @return array
	 */
	private function getCoordsTCX($file,$trackid=0)
	{
		// TODO REWRITE TCX FILE NOT YET SUPPORTED
		$this->error = true;
		$this->errorMessages[] = " ERROR TCX file not yet supported";

		return false;

		if (file_exists($file))
		{
			$xml = simplexml_load_file($file);

			if (isset($xml->Activities->Activity->Lap->Track))
			{
				$startpoint = $xml->Activities->Activity->Lap->Track[$trackid];
			}
			elseif (isset($xml->Courses->Course->Track))
			{
				$startpoint = $xml->Courses->Course->Track[$trackid];
			}

			$coords = array();

			if (!$startpoint[0])
			{
				return false;
			}

			foreach ($startpoint[0] as $start)
			{
				if (isset($start->Position->LatitudeDegrees) && isset($start->Position->LongitudeDegrees))
				{
					$lat = $start->Position->LatitudeDegrees;
					$lon = $start->Position->LongitudeDegrees;
					$ele = $start->AltitudeMeters;
					$time = $start->Time;

					if (isset($start->HeartRateBpm->Value))
					{
						$heart = $start->HeartRateBpm->Value;
					}
					else
					{
						$heart = "0";
					}

					$bak = array((string) $lon, (string) $lat, (string) $ele, (string) $time, (string) $heart);
					array_push($coords, $bak);
				}
			}

			return $coords;
		}
		else
		{
			return false;
		}
	}

	/**
	 * function_description
	 *
	 * @return return_description
	 */
	private function extractAllTracksCoords()
	{
		$this->allCoords = array();
		$this->allDistances = array();
		$this->totalAscent = 0;
		$this->totalDescent = 0;
		$d = 0;
		$this->allDistances[0] = 0;
		/*
		 if ( $this->unit == "Kilometer" )
		 {
		$earthRadius = 6378.137;
		}
		else
		{
		$earthRadius = 6378.137/1.609344;
		}
		*/
		$earthRadius = 6378.137;

		for ($t = 1; $t <= $this->trackCount; $t++)
		{
			$this->allCoords = array_merge($this->allCoords, $this->track[$t]->coords);

			// Calculate distances
			$next_coord = $this->track[$t]->coords[0];
			$next_lat_rad = deg2rad($next_coord[1]);
			$next_lon_rad = deg2rad($next_coord[0]);

			if ( $next_coord[1] > $this->bbox_lat_max )
			{
				$this->bbox_lat_max = $next_coord[1];
			}

			if ( $next_coord[1] < $this->bbox_lat_min )
			{
				$this->bbox_lat_min = $next_coord[1];
			}

			if ( $next_coord[0] > $this->bbox_lon_max )
			{
				$this->bbox_lon_max = $next_coord[0];
			}

			if ( $next_coord[0] < $this->bbox_lon_min )
			{
				$this->bbox_lon_min = $next_coord[0];
			}

			if ($this->elevationDataExists)
			{
				$next_elv = $next_coord[2];
				$this->allElevation[$d] = (int) $next_elv;
			}

			if ($this->speedDataExists)
			{
				$next_time = $this->giveTimestamp($next_coord[3]);
			}

			for ($i = 0; $i < (count($this->track[$t]->coords) - 1); $i++)
			{
				$next_coord = $this->track[$t]->coords[$i + 1];

				if (isset($next_coord))
				{
					$current_lat_rad = $next_lat_rad;
					$current_lon_rad = $next_lon_rad;

					$next_lat_rad = deg2rad($next_coord[1]);
					$next_lon_rad = deg2rad($next_coord[0]);

					if ( $next_coord[1] > $this->bbox_lat_max )
					{
						$this->bbox_lat_max = $next_coord[1];
					}

					if ( $next_coord[1] < $this->bbox_lat_min )
					{
						$this->bbox_lat_min = $next_coord[1];
					}

					if ( $next_coord[0] > $this->bbox_lon_max )
					{
						$this->bbox_lon_max = $next_coord[0];
					}

					if ( $next_coord[0] < $this->bbox_lon_min )
					{
						$this->bbox_lon_min = $next_coord[0];
					}

					$dis = acos(
							(sin($current_lat_rad) * sin($next_lat_rad)) +
							(cos($current_lat_rad) * cos($next_lat_rad) *
									cos($next_lon_rad - $current_lon_rad))
					) * $earthRadius;

					if (is_nan($dis))
					{
						$dis = 0;
					}

					$this->allDistances[$d + 1] = $this->allDistances[$d] + $dis;

					if ($this->elevationDataExists)
					{
						$current_elv = $next_elv;
						$next_elv = $next_coord[2];
						$this->allElevation[$d + 1] = (int) $next_elv;
						$ascent = $next_elv - $current_elv;

						if ($ascent >= 0)
						{
							$this->totalAscent = $this->totalAscent + $ascent;
						}
						else
						{
							$this->totalDescent = $this->totalDescent - $ascent;
						}
					}

					// Speed
					if ($this->speedDataExists)
					{
						$current_time  = $next_time;
						$next_time = $this->giveTimestamp($next_coord[3]);

						if ($current_time and $next_time)
						{
							$elapsedTime = $next_time - $current_time;

							if ($elapsedTime > 0)
							{
								$this->allSpeed[$d + 1] = $dis / $elapsedTime * 3600;
							}
							else
							{
								$this->allSpeed[$d + 1] = 0;
							}
						}
						else
						{
							$this->allSpeed[$d + 1] = 0;
						}
					}

					// Heart Beat
					if ($this->beatDataExists)
					{
						$this->allBeat[$d + 1] = $next_coord[4];
					}

					$d++;
				}
			}
		}

		$this->distance = $this->allDistances[$d];

		if ($this->elevationDataExists)
		{
			$this->totalAscent = (int) $this->totalAscent;
			$this->totalDescent = (int) $this->totalDescent;
		}

		if ( ( $this->totalAscent == 0 ) or ($this->totalDescent == 0) )
		{
			$this->elevationDataExists = false;
		}

		return;
	}

	/**
	 * function_description
	 *
	 * @param   string  $date  param_description
	 *
	 * @return (int) timestamp
	 */
	public function giveTimestamp($date)
	{
		// ToDo: unterschiedliche Zeittypen können hier eingefügt werden
		if ( $date == 0 )
		{
			return false;
		}

		$date = explode('T', $date);
		$time_tmp_date = explode('-', $date[0]);
		$time_tmp_date_year = $time_tmp_date[0];
		$time_tmp_date_month = $time_tmp_date[1];
		$time_tmp_date_day = $time_tmp_date[2];
		$time_tmp_time = explode(':', str_replace("Z", "", $date[1]));
		$time_tmp_time_hour = $time_tmp_time[0];
		$time_tmp_time_minute = $time_tmp_time[1];
		$time_tmp_time_sec = (int) round($time_tmp_time[2], 0);

		return mktime(
				$time_tmp_time_hour, $time_tmp_time_minute, $time_tmp_time_sec,
				$time_tmp_date_month, $time_tmp_date_day, $time_tmp_date_year
		);
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $t  param_description
	 *
	 * @return return_description
	 */
	public function transformTtRGB($t)
	{
		if ($t <= 60)
		{
			$r = dechex(255);
			$g = dechex(round($t * 4.25));
			$b = dechex(0);
		}
		elseif ($t <= 120)
		{
			$r = dechex(round(255 - (($t - 60) * 4.25)));
			$g = dechex(255);
			$b = dechex(0);
		}
		elseif ($t <= 180)
		{
			$r = dechex(0);
			$g = dechex(255);
			$b = dechex(round((($t - 120) * 4.25)));
		}elseif ($t <= 240) {
			$r = dechex(0);
			$g = dechex(round(255 - (($t - 180) * 4.25)));
			$b = dechex(255);
		}elseif ($t <= 300) {
			$r = dechex(round((($t - 240) * 4.25)));
			$g = dechex(0);
			$b = dechex(255);
		}elseif ($t < 360) {
			$r = dechex(255);
			$g = dechex(0);
			$b = dechex(round(255 - (($t - 300) * 4.25)));
		}
		elseif ($t >= 360)
		{
			return false;
		}

		if (strlen($r) == 1)
		{
			$r = (string) "0" . $r;
		}

		if (strlen($g) == 1)
		{
			$g = (string) "0" . $g;
		}

		if (strlen($b) == 1)
		{
			$b = (string) "0" . $b;
		}

		return $r . $g . $b;
	}

	/**
	 * function_description
	 *
	 * @param   integer  $count  color number
	 *
	 * @return array of count color in RGB format
	 */
	public function calculateAllColors($count)
	{
		$color = array();

		for ($i = 1;$i <= $count;$i++)
		{
			$color[($i - 1)] = $this->transformTtRGB(round(300 / $count * $i));
		}

		return $color;
	}

	/**
	 * function_description
	 *
	 * @return (int) Anzahl
	 */
	public function extractWPs()
	{

		if (empty($this->wps))
		{
			$this->isWaypoint = false;
			$this->wps = null;

			return false;
		}

		$this->isWaypoint = true;

		$center = "// <!-- parseOLMapCenterSingleTrack BEGIN -->\n";
		$center .= "var min = lonLatToMercator(new OpenLayers.LonLat";
		$center .= "(" . $this->bbox_lon_min . "," . $this->bbox_lat_min . "));\n";
		$center .= "var max = lonLatToMercator(new OpenLayers.LonLat";
		$center .= "(" . $this->bbox_lon_max . "," . $this->bbox_lat_max . "));\n";
		$center .= "olmap.zoomToExtent(new OpenLayers.Bounds(min.lon, min.lat, max.lon, max.lat));\n";
		$center .= "// <!-- parseOLMapCenterSingleTrack END -->\n";
		$this->wpCenter = $center;
	}

	/**
	 * function_description
	 *
	 * @return return_description
	 */
	public function createChartData()
	{
		$elevationChartData = "";
		$beatChartData = "";
		$speedChartData = "";
		$longitudeChartData = "";
		$latitudeChartData = "";

		$cfg = JtgHelper::getConfig();
		$n = count($this->allDistances);

		/*
		* Adjust max number of points to display on tracks (maxTrkptDisplay)
		* $c is the step for scanning allDistances/speed and others datas
		* $width is half the width over which speed data are smoothed
		* Smoothed speed is average from $i-$witdh<=index<=$i+$width
		*/
		if ( ($cfg->maxTrkptDisplay > 0) AND ($n > $cfg->maxTrkptDisplay))
		{
			$c = $n / $cfg->maxTrkptDisplay / 2;
			$c = round($c, 0);
			$width = 2 * $c;
		}
		else
		{
			$c = 1;
			$width = 2;
		}

		for ($i = 0; $i < $n; $i = $i + $c)
		{
			$distance = (string) round($this->allDistances[$i], 2);
			$i2 = max($i - $width, 1);
			$i3 = min($i + $width, $n - 1);
			$longitudeChartData .= $this->allCoords[$i][0] . ',';
			$latitudeChartData .= $this->allCoords[$i][1] . ',';

			if ($this->speedDataExists)
			{
				// $speedChartData .= '[' . $distance  . ',' . round($this->allSpeed[$i2],1) . '],' ;
				// Calculate average speed (smoothing)
				$speed = 0;

				for ($j = $i2; $j <= $i3; $j++)
				{
					$speed = $speed + $this->allSpeed[$j];
				}

				$speed = $speed / ($i3 - $i2 + 1);
				$speedChartData .= '[' . $distance . ',' . round($speed, 1) . '],';
			}

			if ($this->elevationDataExists)
			{
				$elevationChartData .= '[' . $distance . ',' . round($this->allElevation[$i], 0) . '],';
			}

			if ($this->beatDataExists)
			{
				$beatChartData .= '[' . $distance . ',' . round($this->allBeat[$i2], 0) . '],';
			}
		}

		$this->longitudeData = '[' . substr($longitudeChartData, 0, -1) . ']';
		$this->latitudeData = '[' . substr($latitudeChartData, 0, -1) . ']';

		if ($this->speedDataExists)
		{
			$this->speedData = '[' . substr($speedChartData, 0, -1) . ']';
		}

		if ($this->elevationDataExists)
		{
			$this->elevationData = '[' . substr($elevationChartData, 0, -1) . ']';
		}

		if ($this->beatDataExists)
		{
			$this->beatData = '[' . substr($beatChartData, 0, -1) . ']';
		}

		return;
	}

	/**
	 * Function parseCatIcon
	 *
	 * @param   integer  $catid    category ID
	 * @param   boolean  $istrack  true if GPS file contains track(s)
	 * @param   boolean  $iswp     true if GPS file contains waypoint(s)
	 * @param   boolean  $isroute  true if GPS file contains route(s)
	 *
	 * @return return_description
	 */
	public function parseCatIcon($catid, $istrack = 0, $iswp = 0, $isroute = 0)
	{
		$catid = explode(",", $catid);
		$catid = $catid[0];
		$cfg = JtgHelper::getConfig();
		$iconpath = JUri::root() . "components/com_jtg/assets/template/" . $cfg->template . "/images/";
		$catimage = false;
		$cats = $this->getCats();

		// TODO find a most efficient way to do this!!
		foreach ( $cats AS $cat )
		{
			if ( $cat->id == $catid )
			{
				if ( $cat->image )
				{
					$catimage = $cat->image;
					break;
				}
			}
		}

		if (! $catimage )
		{
			$catimage = 'symbol_inter.png';
		}

		$catimage = "images/jtrackgallery/cats/" . $catimage;

		if ( !is_file($catimage) )
		{
			$catimage = "images/jtrackgallery/cats/symbol_inter.png";
		}

		$simagesize = getimagesize($catimage);
		$sizex = $simagesize[0];
		$sizey = $simagesize[1];
		$maximagesize = 26;

		if ( ( $sizex > $maximagesize ) OR ( $sizey > $maximagesize) )
		{
			$oldsizex = $sizex;
			$oldsizey = $sizey;
			$ratio = $sizex / $sizey;

			if ( $ratio > 1 )
			{
				// Pic is letterbox
				$sizex = $maximagesize;
				$sizey = $sizex / $ratio;
			}
			else
			{
				// Pic is upright
				$sizey = $maximagesize;
				$sizex = $sizey * $ratio;
			}
		}

		$offsetx = round(-($sizex / 2));
		$offsety = round(-($sizey / 2));
		$iconuri = JUri::base() . $catimage;
		$iconStyle = "{graphicWidth: $sizex , graphicHeight: $sizey, graphicXOffset: $offsetx, graphicYOffset: $offsety, externalGraphic: '$iconuri'}";

		return $iconStyle;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $ownicon  param_description
	 *
	 * @return (int) Anzahl
	 */
	public function parseOwnIcon($ownicon=false)
	{
		$cfg = JtgHelper::getConfig();
		$Tpath = JPATH_SITE . "/components/com_jtg/assets/template/" . $cfg->template . "/images/";
		$Tbase = JUri::root() . "components/com_jtg/assets/template/" . $cfg->template . "/images/";
		$unknownicon = "";
		$jpath = JPATH_SITE . "/components/com_jtg/assets/images/symbols/";
		$jbase = JUri::root() . "components/com_jtg/assets/images/symbols/";

		$filename = JFile::makeSafe($ownicon);
		$pngfile = $jbase . $filename . ".png";
		$xmlfile = $jpath . $filename . ".xml";

		if ( ( $ownicon == false ) OR (!is_file($jpath . $filename . ".png")) )
		{
			if ((!JFile::exists($xmlfile)) AND (is_writable($jpath)))
			{
				// Vorlage zur Erstellung unbekannter Icons
				$xmlcontent = "<xml>\n	<sizex>16</sizex>\n	<sizey>16</sizey>\n	<offsetx>8</offsetx>\n	<offsety>8</offsety>\n</xml>\n<!--\nUm dieses Icon verfügbar zu machen, erstelle dieses Bild: \"" . $filename . ".png\",\nund vervollständige obige 4 Parameter.\n\"offsetx\" beschreibt die Anzahl der Pixel von links bis zum Punkt (negativ) und\n\"offsety\" beschreibt die Anzahl der Pixel von oben bis zum Punkt (ebenfalls negativ).\nMit \"Punkt\" ist der Punkt gemeint, der auf der Koordinate sitzt.\n-->\n";
				JFile::write($xmlfile, $xmlcontent);
				JPath::setPermissions($xmlfile, "0666");
			}
			// Standardicon
			$pngfile = $Tbase . "unknown_WP.png";
			$xmlfile = $Tpath . "unknown_WP.xml";
			$unknownicon = "// Unknown Icon: \"" . $jpath . $ownicon . ".png\"\n";
		}

		$icon = $pngfile;
		$xml = $this->loadFile($xmlfile);
		$sizex = $xml->sizex;
		$sizey = $xml->sizey;
		$offsetx = $xml->offsetx;
		$offsety = $xml->offsety;

		return $unknownicon . "var icon = new OpenLayers.Icon('" . $icon . "',\n			new OpenLayers.Size(" . $sizex . ", " . $sizey . "),\n			new OpenLayers.Pixel(" . $offsetx . ", " . $offsety . "));\n";
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $wp  param_description
	 *
	 * @return return_description
	 */
	public function isGeocache($wp)
	{
		if ( ( isset($wp->sym) ) AND ( preg_match('/Geocache/', $wp->sym) ) AND ( isset($wp->type) ) )
		{
			return true;
		}

		return false;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $wp  param_description
	 *
	 * @return return_description
	 */
	public function hasURL($wp)
	{
		if ( ( isset($wp->url) ) AND ( isset($wp->urlname) ) )
		{
			return true;
		}

		return false;
	}

	/**
	 * function_description
	 *
	 * @return (int) Anzahl
	 */
	public function parseWPs()
	{
		if ( empty($this->wps))
		{
			return false;
		}

		$wpcode = "// <!-- parseWPs BEGIN -->\n";
		$wpcode .= "wps = new OpenLayers.Layer.Markers(\"" . JText::_('COM_JTG_WAYPOINTS') . "\");";
		$wpcode .= "olmap.addLayer(wps);";
		$wpcode .= "addWPs();";
		$wpcode .= "function addWPs() {\n";

		foreach ($this->wps as $wp)
		{
			$replace = array("
					","'");
			$with = array("<br />","\'");
			$hasURL = isset($wp->URL);
			$isGeocache = $this->isGeocache($wp->value);

			if ($hasURL)
			{
				$URL = " <a href=\"" . $wp->url . "\" target=\"_blank\">" .
						trim(str_replace($replace, $with, $wp->urlname)) . "</a>";
			}
			else
			{
				$URL = "";
			}

			$name = trim(str_replace($replace, $with, $wp->name));
			$cmt = trim(str_replace($replace, $with, $wp->cmt));
			$desc = trim(str_replace($replace, $with, $wp->desc));
			$ele = (float) $wp->ele;

			if ($isGeocache)
			{
				$sym = (string) $wp->type;
			}
			else
			{
				$sym = $wp->sym;
			}

			$wpcode .= "llwp = new OpenLayers.LonLat(" . $wp->lon . "," . $wp->lat . ").transform(new OpenLayers . ";
			$wpcode .= "Projection(\"EPSG:4326\"), olmap.getProjectionObject());\n";
			$wpcode .= "popupClasswp = AutoSizeFramedCloud;\n";
			$wpcode .= "popupContentHTMLwp = '" . $name . $URL;

			if ($desc)
			{
				$wpcode .= "<b>" . JText::_('COM_JTG_DESCRIPTION') . ":</b> " . $desc;
			}

			if ( ($cmt) AND ($desc != $cmt) )
			{
				$wpcode .= "<br /><b>" . JText::_('COM_JTG_COMMENT') . ":</b> " . $cmt;
			}

			if ($ele)
			{
				// TODO unit in elevation !!
				$wpcode .= "<br /><b>" . JText::_('COM_JTG_ELEVATION') . " :</b> ca. " . round($ele, 1) . "m<small>";
			}

			$wpcode .= "';\n";
			$wpcode .= $this->parseOwnIcon($sym);
			$wpcode .= "addWP(llwp, popupClasswp, popupContentHTMLwp, true, true, icon);\n";
		}

		$wpcode .= "	}\n";
		/*
		 * $wp .= "	//
		* Function: addWP
		* Add a new marker to the markers layer given the following lonlat,
		*	 popupClass, and popup contents HTML. Also allow specifying
		*	 whether or not to give the popup a close box.
		*
		* Parameters:
		* ll - {<OpenLayers.LonLat>} Where to place the marker
		* popupClass - {<OpenLayers.Class>} Which class of popup to bring up
		*	 when the marker is clicked.
		* popupContentHTML - {String} What to put in the popup
		* closeBox - {Boolean} Should popup have a close box?
		* overflow - {Boolean} Let the popup overflow scrollbars?
		*/

		$wpcode .= "	function addWP(ll, popupClass, popupContentHTML, closeBox, overflow, icon) {
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
		olmap.addPopup(this.popup);
		this.popup.show();
	}
	else
	{
	this.popup.toggle();
	}
	currentPopup = this.popup;
	OpenLayers.Event.stop(evt);
	};
	wp.events.register(\"mousedown\", feature, markerClick);
	wps.addMarker(wp);
	}\n";
		$wpcode .= "// <!-- parseWPs END -->\n";

		return $wpcode;
	}

	/**
	 * function_description
	 *
	 * @param   string  $desc  track description
	 *
	 * @return string
	 */
	public function showDesc($desc)
	{
		$stringlength = 200;
		$maxslperrow = 50;

		// Strip all tags but <p>
		$desc = str_replace(array("'","\n","\r"), array("\'","<br/>"," "), $desc);
		$desc = strip_tags($desc, '<p>');

		// Trennung nach <p>Katitel</p> BEGIN
		$desc = str_replace('</p>', "", $desc);
		$desc = explode('<p>', $desc);
		$newdesc = "";
		$count_letters = 0;
		$return = "";

		foreach ( $desc AS $chapter )
		{
			if ( $chapter != "" )
			{
				$chapter = strip_tags($chapter);
				$chapter = trim($chapter);

				// Trennung nach Wörter BEGIN
				$words = explode(' ', $chapter);
				$return .= "<p>";
				$rowlen = 0;

				foreach ($words AS $word)
				{
					// Strip additionnal (non <p>) tags, quote and return "1" wegen der Leerstelle
					$count_letters = ( $count_letters + strlen($word) + 1);

					// Einfügung von Zeilensprung BEGIN
					$rowlen = ( $rowlen + strlen($word) );

					if ( $rowlen > $maxslperrow )
					{
						$return = trim($return) . "<br />";
						$rowlen = 0;
					}

					if ( ( $count_letters + strlen($word) ) > $stringlength )
					{
						return $return . "[...]</p>";
					}

					// Einfügung von Zeilensprung END
					$return .= $word . " ";
				}

				$return = trim($return) . "</p>";

				// Trennung nach Wörter END
				$newdesc[] = $chapter;
			}
		}
		// Trennung nach <p>Katitel</p> END

		if ( $count_letters == 0 )
		{
			return "<p>" . str_replace(array("'","\n","\r"), array("\'","<br/>"," "), JText::_('COM_JTG_NO_DESC')) . "</p>";
		}

		return $return;
	}

	/**
	 * function_description
	 *
	 * @param   object  $rows  rows
	 *
	 * @return array()
	 */
	public function maySee($rows)
	{
		if (!$rows)
		{
			return false;
		}

		$user = JFactory::getUser();
		$return = array();

		foreach ( $rows AS $row )
		{
			if (( (int) $row->published )
				AND ( ( !$row->access )
				OR ( ( $row->access )
				AND ( ( isset( $user->userid ) AND ( $user->userid ) )
				OR ( isset( $user->id ) AND ( $user->id ) ) ) ) ) )
			{
				$return[] = $row;
			}
		}

		return $return;
	}

	/**
	 * Löscht den aktuellen Track aus der
	 * Gesamtansicht
	 *
	 * @param   unknown_type  $rows   param_description
	 * @param   unknown_type  $track  param_description
	 *
	 * @return array()
	 */
	public function deleteTrack($rows, $track)
	{
		foreach ( $track AS $key => $value )
		{
			// Track-ID herausfinden und Schleife verlassen
			$trackid = $value;
			break;
		}

		$return = array();

		foreach ( $rows AS $key => $value )
		{
			foreach ( $value AS $key_b => $value_b )
			{
				if ( $value_b != $trackid )
				{
					$store = true;
				}
				else
				{
					$store = false;
				}

				break;
			}

			if ( $store == true )
			{
				$return[] = $value;
			}
		}

		return $return;
	}

	/**
	 *
	 */

	/**
	 * function_description
	 *
	 * @param   string  $wish  optionnal expected color
	 *
	 * @return color (#000000 - #ffffff) or own wish
	 */
	public function getHexColor($wish = false)
	{
		if ($wish !== false)
		{
			return $wish;
		}

		$color = "";

		for ($i = 0;$i < 3;$i++)
		{
			$dec = (int) rand(16, 128);
			$color .= dechex($dec);
		}

		return ("#" . $color);
	}

	/**
	 * function_description
	 *
	 * @return return_description
	 */
	private function getStartTCX()
	{
		$xml = $this->loadFile();

		if (isset($xml->Activities->Activity->Lap->Track))
		{
			$startpoint = $xml->Activities->Activity->Lap->Track[0]->Trackpoint;
		}
		elseif (isset($xml->Courses->Course->Track))
		{
			$startpoint = $xml->Courses->Course->Track[0]->Trackpoint;
		}

		$lat = $startpoint->Position->LatitudeDegrees;
		$lon = $startpoint->Position->LongitudeDegrees;

		$start = array((string) $lon, (string) $lat);

		return $start;
	}

	/**
	 * function_description
	 *
	 * @return array
	 */
	function getMapNates()
	{
		$mainframe = JFactory::getApplication();

		$db = JFactory::getDBO();

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
	 * function_description
	 *
	 * @param   sstring  $where  where query string
	 *
	 * @return array
	 */
	function getTracks($where="")
	{
		$mainframe = JFactory::getApplication();

		$db = JFactory::getDBO();

		$query = "\nSELECT a.*, b.title AS cat FROM #__jtg_files AS a"
		. "\n LEFT JOIN #__jtg_cats AS b"
		. "\n ON a.catid=b.id" . $where;
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	/**
	 * function_description
	 *
	 * @return array
	 */
	function getCats()
	{
		$mainframe = JFactory::getApplication();

		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__jtg_cats";

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	/**
	 * Openlayers write maps
	 *
	 * @param   unknown_type  $where   param_description
	 * @param   unknown_type  $tracks  param_description
	 * @param   unknown_type  $params  param_description
	 *
	 * @return return_description
	 */
	public function writeOLMap($where,$tracks,$params)
	{
		$cfg = JtgHelper::getConfig();

		// 	$cnates = $this->getMapNates();
		$rows = $this->getTracks($where);

		/*
		 $user = JFactory::getUser();
		$userid = $user->id;
		$rows = $this->maySee($rows);
		*/

		$map = false;
		$map .= $this->parseScriptOLHead();
		$map .= $this->parseOLMapControl($params, false);
		$map .= $this->parseOLLayer();
		/*
		 $map .= $this->parseOLPOIs(); // Currently not active
		*/

		if ($tracks)
		{
			// Schlecht bei vielen verfügbaren Tracks
			$map .= $this->parseOLTracks($rows);
		}

		$file = JPATH_SITE . "/components/com_jtg/models/jtg.php";
		require_once $file;
		$this->sortedcats = JtgModeljtg::getCatsData(true);

		$map .= $this->parseOLMarker($rows);
		$map .= $this->parseOLMapCenter($rows);
		$map .= $this->parseOLMapFunctions();
		$map .= $this->parseScriptOLFooter();

		return $map;
	}

	/**
	 * counts the MapCenter and ZoomLevel of Boundingbox
	 *
	 * @param   unknown_type  $lon_min  param_description
	 * @param   unknown_type  $lon_max  param_description
	 * @param   unknown_type  $lat_min  param_description
	 * @param   unknown_type  $lat_max  param_description
	 *
	 * @return array('lon'=>lon,'lat'=>lat)
	 */
	public function calcMapCenter($lon_min, $lon_max, $lat_min, $lat_max)
	{
		// Weltansicht ohne Südpol
		$lat = 47;
		$lon = 0;
		$zoom_min = 2;
		$zoom_max = 14;
		$return = array();

		if ( ( $lon_min == $lon_max ) AND ( $lat_min == $lat_max ) )
		{
			// Nur eine Koordinate wurde übergeben
			$return['lon'] = $lon_min;
			$return['lat'] = $lat_min;
		}
		else
		{
			$return['lon'] = ( ( $lon_max + $lon_min ) / 2 );
			$return['lat'] = ( ( $lat_max + $lat_min ) / 2 );
		}

		return $return;
	}

	/**
	 * function_description
	 *
	 * @param   object  $rows  rows
	 *
	 * @return string
	 */
	private function parseOLMapCenter($rows)
	{
		if (!$rows)
		{
			// 		Worldview without southpole
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

			foreach ( $rows AS $row )
			{
				if ( ( $row->start_n ) AND ( $row->start_n > $bbox_lat_max ) )
				{
					$bbox_lat_max = $row->start_n;
				}

				if ( ( $row->start_n ) AND ( $row->start_n < $bbox_lat_min ) )
				{
					$bbox_lat_min = $row->start_n;
				}

				if ( ( $row->start_e ) AND ( $row->start_e > $bbox_lon_max ) )
				{
					$bbox_lon_max = $row->start_e;
				}

				if ( ( $row->start_e ) AND ( $row->start_e < $bbox_lon_min ) )
				{
					$bbox_lon_min = $row->start_e;
				}
			}
		}

		$center = "// <!-- parseOLMapCenter BEGIN -->\n";
		$center .= "var min = lonLatToMercator(new OpenLayers.LonLat";
		$center .= "(" . $bbox_lon_min . "," . $bbox_lat_min . "));\nvar max = lonLatToMercator";
		$center .= "(new OpenLayers.LonLat(" . $bbox_lon_max . "," . $bbox_lat_max . "));\n";
		$center .= "olmap.zoomToExtent(new OpenLayers.Bounds(min.lon, min.lat, max.lon, max.lat));\n";
		$center .= "// <!-- parseOLMapCenter END -->\n";

		return $center;
	}

	/**
	 * function_description
	 *
	 * @return string
	 */
	private function parseOLMapFunctions()
	{
		$marker = "// <!-- parseOLMapFunctions BEGIN -->\n";
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

		$marker .= "// <!-- parseOLMapFunctions END -->\n";

		return $marker;
	}

	/**
	 * function_description
	 *
	 * @return string
	 */
	/*
	 * private function parseOLStartStop() {
	.* $startstop = "// <!-- parseOLStartStop BEGIN -->\n";
	.* $startstop .= "// <!-- parseOLStartStop END -->\n";
	.* return $startstop;
	* }
	*/
	/**
	 * function_description
	 *
	 * @param   unknown_type  $track_array  param_description
	 * @param   unknown_type  $visibility   param_description
	 *
	 * @return string
	 */
	private function parseOLMarker($track_array, $visibility = true)
	{
		$cfg = JtgHelper::getConfig();

		if (!$track_array)
		{
			return false;
		}

		$document = JFactory::getDocument();
		$document->addScript('components/com_jtg/assets/js/patches_OL-popup-autosize.js');
		$document->addScript('components/com_jtg/assets/js/FeaturePopups.js');
		$document->addScript('components/com_jtg/assets/js/animatedCursor.js');
		$document->addScript('components/com_jtg/assets/js/jtgOverView.js');

		$marker = "// <!-- parseOLMarker BEGIN -->\n";
		$marker .= "markers = [\n";
		$i = 0;

		foreach ( $track_array AS $row )
		{
			$i++;
			$url = JROUTE::_("index.php?option=com_jtg&view=files&layout=file&id=" . $row->id);
			$lon = $row->start_e;
			$lat = $row->start_n;

			if ($row->published == 1 AND ( ( $lon ) OR ( $lon ) ))
			{
				$link = "<a href=\"" . $url . "\"";

				switch ($row->access)
				{
					case 0:
						// Public
						$link .= ">";
						break;
					case 9:
						// Private
						$link .= " title=\\\"" . JText::_('COM_JTG_PRIVATE') . "\">";
						break;
					case 1:
						// Registered
						$link .= " title=\\\"" . JText::_('COM_JTG_REGISTERED') . "\">";
						break;
					case 2:
						// Admin
						$link .= " title=\\\"" . JText::_('COM_JTG_ADMINISTRATORS') . "\">";
						break;
				}

				if ($row->title)
				{
					$link .= str_replace(array("'"), array("\'"), $row->title);
				}
				else
				{
					$link .= "<i>" . str_replace(array("'"), array("\'"), JText::_('COM_JTG_NO_TITLE')) . "</i>";
				}

				if ( $row->access != 0 )
				{
					$iconpath = JUri::root() . "components/com_jtg/assets/template/" . $cfg->template . "/images/";
				}

				switch ($row->access)
				{
					case 1:
						$link .= "&nbsp;<img alt=\\\"" . JText::_('COM_JTG_REGISTERED') . "\" src=\\\"" . $iconpath . "registered_only.png\\\" />";
						break;
					case 2:
						$link .= "&nbsp;<img alt=\\\"" . JText::_('COM_JTG_ADMINISTRATORS') . "\" src=\\\"" . $iconpath . "special_only.png\\\" />";
						break;
					case 9:
						$link .= "&nbsp;<img alt=\\\"" . JText::_('COM_JTG_PRIVATE') . "\" src=\\\"" . $iconpath . "private_only.png\\\" />";
						break;
				}

				$link .= "</a></b>";

				if ( $row->cat != "" )
				{
					$cats = str_replace(array("'"), array("\'"), JText::_('COM_JTG_CAT')) . ": ";
					$cats = JtgHelper::parseMoreCats($this->sortedcats, $row->catid, "box", true);
				}
				else
				{
					$cats = "<br /><i>" . str_replace(array("'"), array("\'"), JText::_('COM_JTG_CAT_NONE')) . "</i>";
				}

				// Add track description, after striping HTML tags
				$description = $this->showDesc($row->description);

				// Start icon
				$iconStyle = $this->parseCatIcon($row->catid, $row->istrack, $row->iswp, $row->isroute);

				// Javacsript code
				if ($i > 1)
				{
					$marker .= "	,\n";
				}

				$marker .= "	{\n" .
					'		"lon" : ' . $lon . ",\n" .
					'		"lat" : ' . $lat . ",\n" .
					'		"cats" : \'' . $cats . "',\n" .
					'		"link": \'' . $link . "',\n" .
					'		"description": \'' . $description . "',\n" .
					'		"iconStyle": ' . $iconStyle . "\n " .
					"	}\n";
			}
			else
			{
				// Dummy line for Coding standard
			}
		}

		$marker .= "	];\n";
		$marker .= "addClusteredLayerOfMarkers();\n";
		$marker .= "// <!-- parseOLMarker END -->\n";

		return $marker;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $track_array  param_description
	 * @param   unknown_type  $visibility   param_description
	 *
	 * @return string
	 */
	private function parseOLMarker_old($track_array, $visibility = true)
	{
		$cfg = JtgHelper::getConfig();

		if (!$track_array)
		{
			return false;
		}

		$marker = "// <!-- parseOLMarker BEGIN -->\n";

		if ( $visibility != true )
		{
			$marker .= "	markers = new OpenLayers.Layer.Markers(\"" . JText::_('COM_JTG_OTHER_STARTPOINTS') . "\");\n";
		}
		else
		{
			$marker .= "	markers = new OpenLayers.Layer.Markers(\"" . JText::_('COM_JTG_STARTPOINTS') . "\");\n";
		}

		$marker .= "	olmap.addLayer(markers);\n";
		$marker .= "	addMarkers();\n";

		if ( $visibility != true )
		{
			$marker .= "	markers.setVisibility(false);\n";
		}

		$marker .= "	function addMarkers() {\n";
		$i = 0;

		foreach ( $track_array AS $row )
		{
			$link = JROUTE::_("index.php?option=com_jtg&view=files&layout=file&id=" . $row->id);
			$lon = $row->start_e;
			$lat = $row->start_n;

			if ($row->published == 1 AND ( ( $lon ) OR ( $lon ) ))
			{
				$marker .= "ll = new OpenLayers.LonLat(" . $lon . "," . $lat . ") . ";
				$marker .= "transform(new OpenLayers.Projection(\"EPSG:4326\"), olmap.getProjectionObject()); ";
				$marker .= "popupClass = AutoSizeFramedCloud; ";
				$marker .= "popupContentHTML = '<b>" . JText::_('COM_JTG_TITLE') . ": <a href=\"" . $link . "\"";

				switch ($row->access)
				{
					case 0:
						// Public
						$marker .= ">";
						break;
					case 9:
						// Private
						$marker .= " title=\\\"" . JText::_('COM_JTG_PRIVATE') . "\">";
						break;
					case 1:
						// Registered
						$marker .= " title=\\\"" . JText::_('COM_JTG_REGISTERED') . "\">";
						break;
					case 2:
						// Admin
						$marker .= " title=\\\"" . JText::_('COM_JTG_ADMINISTRATORS') . "\">";
						break;
				}

				if ($row->title)
				{
					$marker .= str_replace(array("'"), array("\'"), $row->title);
				}
				else
				{
					$marker .= "<i>" . str_replace(array("'"), array("\'"), JText::_('COM_JTG_NO_TITLE')) . "</i>";
				}

				if ( $row->access != 0 )
				{
					$iconpath = JUri::root() . "components/com_jtg/assets/template/" . $cfg->template . "/images/";
				}

				switch ($row->access)
				{
					case 1:
						$marker .= "&nbsp;<img alt=\\\"" . JText::_('COM_JTG_REGISTERED') . "\" src=\\\"" . $iconpath . "registered_only.png\\\" />";
						break;
					case 2:
						$marker .= "&nbsp;<img alt=\\\"" . JText::_('COM_JTG_ADMINISTRATORS') . "\" src=\\\"" . $iconpath . "special_only.png\\\" />";
						break;
					case 9:
						$marker .= "&nbsp;<img alt=\\\"" . JText::_('COM_JTG_PRIVATE') . "\" src=\\\"" . $iconpath . "private_only.png\\\" />";
						break;
				}

				$marker .= "</a></b>";

				if ( $row->cat != "" )
				{
					$marker .= "<br />" . str_replace(array("'"), array("\'"), JText::_('COM_JTG_CAT')) . ": ";
					$marker .= JtgHelper::parseMoreCats($this->sortedcats, $row->catid, "box", true);
				}
				else
				{
					$marker .= "<br /><i>" . str_replace(array("'"), array("\'"), JText::_('COM_JTG_CAT_NONE')) . "</i>";
				}

				// Add track description, after striping HTML tags
				$marker .= $this->showDesc($row->description);
				$marker .= "'; ";

				// Start icon
				$marker .= $this->parseCatIcon($row->catid, $row->istrack, $row->iswp, $row->isroute);

				// End icon
			}
			else
			{
				// Dummy line for Coding standard
			}
		}

		$marker .= "	}\n";

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
		olmap.addPopup(this.popup);
		this.popup.show();
	}
	else
	{
	this.popup.toggle();
	}
	currentPopup = this.popup;
	OpenLayers.Event.stop(evt);
	};
	";

		// MouseDown
		$marker .= "		marker.events.register(\"mousedown\", feature, markerClick);\n";
		$marker .= "		markers.addMarker(marker);}\n";
		$marker .= "// <!-- parseOLMarker END -->\n";

		return $marker;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $rows  param_description
	 *
	 * @return string
	 */
	private function parseOLTracks($rows)
	{
		if ($rows === null)
		{
			return false;
		}
		else
		{
			// Dummy line for Coding standard
		}

		$color = $this->calculateAllColors(count($rows));
		$string = "// <!-- parseOLTracks BEGIN -->\n";
		$string .= "layer_vectors = new OpenLayers.Layer.Vector(\"" . JText::_('COM_JTG_TRACKS') . "\", { displayInLayerSwitcher: true } );\n";
		$string .= "olmap.addLayer(layer_vectors);\n";
		$i = 0;

		foreach ($rows AS $row)
		{
			$file = "images/jtrackgallery/uploaded_tracks/" . $row->file;
			$coords = $this->getCoords($file);
			$string .= "geometries = new Array();geometries.push(drawLine([\n";

			if ($coords)
			{
				foreach ($coords as $key => $fetch)
				{
					$string .= "[" . $coords[$key][0] . "," . $coords[$key][1] . "],";
				}
			}
			else
			{
				// Dummy line for Coding standard (else required!)
			}

			$string .= "],\n{strokeColor:\"" . $this->getHexColor("#" . $color[$i]) . "\",\nstrokeWidth: 2,\nfillColor: \"" . $this->getHexColor("#" . $color[$i]) . "\",\nfillOpacity: 0.4}));\n";
			$i++;
		}

		$string .= "// <!-- parseOLTracks END -->\n";

		return $string;
	}

	/**
	 * function_description
	 *
	 * @return string
	 */
	private function parseOLPOIs()
	{
		$pois = "// <!-- parseOLPOIs BEGIN -->\n";
		$pois = "// <!-- POI not handled by J!TrackGallery -->\n";
		$pois .= "// <!-- parseOLPOIs END -->\n";

		return $pois;
	}

	/**
	 * function_description
	 *
	 * @param   string  $desc  query order
	 *
	 * @return maps Object
	 */
	private function getMaps($desc=false)
	{
		$db = JFactory::getDBO();
		$sql = 'Select * from #__jtg_maps';

		if ($desc)
		{
			$sql .= ' ORDER BY ' . $desc;
		}
		else
		{
			// Dummy line for Coding standard
		}

		$db->setQuery($sql);
		$maps = $db->loadObjectlist();

		return $maps;
	}

	/**
	 * function_description
	 *
	 * @return Object
	 */
	private function buildMaps()
	{
		$maps = $this->getMaps("ordering");
		$return = "";
		$document = JFactory::getDocument();

		for ($i = 0;$i < count($maps);$i++)
		{
			$map = $maps[$i];
			$name = strtolower(str_replace(array(" ","_"), "", html_entity_decode($map->name)));
			$realname = JText::_(html_entity_decode($map->name));
			$param = str_replace("{name}", $realname, html_entity_decode($map->param));
			$script = html_entity_decode($map->script);

			if ($map->published == 1)
			{
				if ($script)
				{
					if (!preg_match("/|/", $script))
					{
						$document->addScript($script);
					}
					else
					{
						$scripts = explode("|", $script);

						foreach ($scripts AS $eachscript)
						{
							$document->addScript($eachscript);
						}
					}
				}

				$code = html_entity_decode($map->code);

				if ($code != "")
				{
					$return .= $code . "\n";
				}

				if (!isset($baselayer))
				{
					$baselayer = "		olmap.setBaseLayer(layer" . $name . ");\n";
				}

				$return .= "layer" . $name . " = new " . $param . ";\n" .
						"olmap.addLayer(layer" . $name . ");\n";
			}
		}

		// TODO: move this to overlays
		$return .= "hs_name = \"" . JText::_('COM_JTG_HILL_SHADE_EUROPE') . "\";\n";
		$return .= "hs_url = \"http://129.206.228.72/cached/hillshade?\";\n";
		$return .= "hs_options = {layers: 'europe_wms:hs_srtm_europa',srs: 'EPSG:900913', format: 'image/jpeg', transparent: 'true',numZoomLevels: 19};\n";
		$return .= "hs2_1_options = {layers: 'europe_wms:hs_srtm_europa',srs: 'EPSG:900913', format: 'image/jpeg',numZoomLevels: 19};\n";

		$return .= "hs2 =  new OpenLayers.Layer.WMS( hs_name , hs_url , hs_options, {'buffer':1, removeBackBufferDelay:0, className:'olLayerGridCustom'});\n";
		$return .= "hs2.setOpacity(0.3);\n";
		$return .= "hs2_1 =  new OpenLayers.Layer.WMS( hs_name , hs_url , hs2_1_options,{'buffer':1, transitionEffect:'resize', removeBackBufferDelay:0, className:'olLayerGridCustom'});\n";
		$return .= "olmap.addLayer( hs2,hs2_1 );\n";

		// TODO osm_getTileURL see http://wiki.openstreetmap.org/wiki/Talk:Openlayers_POI_layer_example
		$document->addScript('components/com_jtg/assets/js/jtg_getTileURL.js');

		if ( !isset($baselayer))
		{
			// No map available
			return false;
		}

		$return = $return . $baselayer;

		return $return;
	}

	/**
	 * function_description
	 *
	 * @return string
	 */
	private function parseOLLayer()
	{
		$maps = $this->buildMaps();
		$layer = "// <!-- parseOLLayer BEGIN -->\n";
		$layer .= $maps . "// <!-- parseOLLayer END -->\n";

		return $layer;

		// TODO Set base Layer (actually return before !!
		// What is layertoshow ??,
		$layertoshow = array();

		if ( $baselayer != false )
		{
			$layer .= "		olmap.setBaseLayer(" . $baselayer . ");\n";

			return $layer;
		}
		else
		{
			return $layertoshow[0];
		}
		// Gib nur Mapnik aus, für den Fall der Fehlkonfiguration
	}

	/**
	 * Pass in GPS.GPSLatitude or GPS.GPSLongitude or something in that format
	 *
	 * http://stackoverflow.com/questions/2526304/php-extract-gps-exif-data/2572991#2572991
	 * Thanks to Gerald Kaszuba http://geraldkaszuba.com/
	 *
	 * @param   unknown_type  $exifCoord  param_description
	 * @param   unknown_type  $hemi       param_description
	 *
	 * @return number
	 */
	private function getGps($exifCoord, $hemi)
	{
		$degrees = count($exifCoord) > 0 ? $this->gps2Num($exifCoord[0]) : 0;
		$minutes = count($exifCoord) > 1 ? $this->gps2Num($exifCoord[1]) : 0;
		$seconds = count($exifCoord) > 2 ? $this->gps2Num($exifCoord[2]) : 0;
		$flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

		return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $coordPart  param_description
	 *
	 * @return return_description
	 */
	private function gps2Num($coordPart)
	{
		$parts = explode('/', $coordPart);

		if ((count($parts)) <= 0)
		{
			return 0;
		}

		if ((count($parts)) == 1)
		{
			return $parts[0];
		}

		return floatval($parts[0]) / floatval($parts[1]);
	}

	/**
	 * function_description
	 *
	 * @param   integer  $id                track ID
	 * @param   integer  $max_geoim_height  geotagged image height on maps
	 * @param   string   $iconfolder        icons folder path
	 * @param   string   $httpiconpath      icons folder URL
	 *
	 * @return string html code to display geotagged images
	 */
	private function parseOLGeotaggedImgs($id, $max_geoim_height, $iconfolder, $httpiconpath)
	{
		jimport('joomla.filesystem.folder');
		$max_geoim_height = (int) $max_geoim_height;
		$foundpics = false;
		$map = "// <!-- parseOLGeotaggedImgs BEGIN -->\n";
		$httppath = JUri::base() . "images/jtrackgallery/uploaded_tracks_images/track_" . $id . "/";
		$folder = JPATH_SITE . "/images/jtrackgallery/uploaded_tracks_images/" . 'track_' . $id . '/';
		$map .= "layer_geotaggedImgs = new OpenLayers.Layer.Markers(\"" . JText::_('COM_JTG_GEOTAGGED_IMAGES') . "\"," .
				" { displayInLayerSwitcher: true });" .
				"\n	olmap.addLayer(layer_geotaggedImgs);" .
				"\n	layer_geotaggedImgs.setVisibility(true);\n";

		if (JFolder::exists($folder))
		{
			$imgs = JFolder::files($folder, false);

			if ($imgs)
			{
				foreach ($imgs AS $image)
				{
					// Retrieve thumbnail path
					if ( JFile::exists($folder . 'thumbs' . '//thumb1_' . $image))
					{
						$imagepath = $httppath . 'thumbs/thumb1_' . $image;
					}
					else
					{
						// TODO recreate thumbnail if it does not exists (case direct FTP upload of images)
						$imagepath = $httppath . $image;
					}

					// TODO does thumbnail have original image exif data??
					// TODO CACHE THIS
					$exif = exif_read_data($folder . $image);

					if ( isset($exif['GPSLatitude']))
					{
						// Is geotagged
						if (isset($exif["GPSImgDirection"]))
						{
							$direction = $exif["GPSImgDirection"];
							$direction = explode('/', $direction);
							$direction = (float) ((int) $direction[0] / (int) $direction[1]);
						}
						else
						{
							$direction = false;
						}

						$foundpics = true;
						$height = (int) $exif["COMPUTED"]["Height"];
						$width = (int) $exif["COMPUTED"]["Width"];

						if ( ( $height > $max_geoim_height ) OR ( $width > $max_geoim_height ) )
						{
							if ( $height == $width ) // Square
							{
								$height = $max_geoim_height;
								$width = $max_geoim_height;
							}
							elseif ( $height < $width ) // Landscape
							{
								$height = $max_geoim_height / $width * $height;
								$width = $max_geoim_height;
							}
							else // Portrait
							{
								$height = $max_geoim_height;
								$width = $height * $max_geoim_height / $width;
							}
						}

						$lon = $this->getGps($exif['GPSLongitude'], $exif['GPSLongitudeRef']);
						$lat = $this->getGps($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
						$size = "width=\"" . (int) $width . "\" height=\"" . (int) $height . "\"";
						$image = "<img " . $size . " src=\"" . $imagepath . "\" alt=\"" . $image . "\" title=\"" . $image . "\">";
						$xml = simplexml_load_file($httpiconpath . "foto.xml");
						$sizex = $xml->sizex;
						$sizey = $xml->sizey;
						$offsetx = $xml->offsetx;
						$offsety = $xml->offsety;

						$map .= "var lonLatlayer_geotaggedImgs = new OpenLayers.LonLat(" . $lon . "," . $lat . ").transform(new OpenLayers.Projection(\"EPSG:4326\"),olmap.getProjectionObject());" .
								"\n	var sizelayer_geotaggedImgs = new OpenLayers.Size(" . $sizex . "," . $sizey . ");" .
								"\n	var offsetlayer_geotaggedImgs = new OpenLayers.Pixel(" . $offsetx . "," . $offsety . ");" .
								"\n	var iconlayer_geotaggedImgs = new OpenLayers.Icon(\"" . $iconfolder . "foto.png\",sizelayer_geotaggedImgs,offsetlayer_geotaggedImgs);" .
								"\n	popupContentHTML_geotaggedImgs = '" . $image . "';" .
								"\n	popupClass_geotaggedImgs = AutoSizeAnchored;" .
								"\n	addlayer_geotaggedImgs(lonLatlayer_geotaggedImgs, popupClass_geotaggedImgs, popupContentHTML_geotaggedImgs, true, false, iconlayer_geotaggedImgs, olmap);\n";
					}
				}
			}
		}

		if ( $foundpics == false )
		{
			return false;
		}

		$map .= "// <!-- parseOLGeotaggedImgs END -->\n";

		return $map;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $track   param_description
	 * @param   unknown_type  $params  param_description
	 *
	 * @return return_description
	 */
	public function writeTrackOL($track, $params)
	{
		$mainframe = JFactory::getApplication();
		$jtg_microtime = microtime(true);
		$zeiten = "<br />\n";
		$cfg = JtgHelper::getConfig();
		$iconpath = JUri::root() . "components/com_jtg/assets/template/" . $cfg->template . "/images/";
		$httpiconpath = JPATH_SITE . "/components/com_jtg/assets/template/" . $cfg->template . "/images/";
		jimport('joomla.filesystem.file');
		$zeiten .= (int) round(( microtime(true) - $jtg_microtime), 0) . " " . JText::_('COM_JTG_DEBUG_TIMES') . " getTracks<br />\n";

		$map = "\n<!-- writeTrackCOM_JTG BEGIN -->\n";
		$map .= $this->parseScriptOLHead();
		$zeiten .= (int) round((microtime(true) - $jtg_microtime), 0) . " " . JText::_('COM_JTG_DEBUG_TIMES') . " parseScriptOLHead<br />\n";
		$map .= $this->parseOLMap();
		$zeiten .= (int) round((microtime(true) - $jtg_microtime), 0) . " " . JText::_('COM_JTG_DEBUG_TIMES') . " parseOLMap<br />\n";

		$map .= $this->parseOLMapControl($params, false);
		$zeiten .= (int) round((microtime(true) - $jtg_microtime), 0) . " " . JText::_('COM_JTG_DEBUG_TIMES') . " parseOLMapControl<br />\n";
		$map .= $this->parseOLLayer();

		$zeiten .= (int) round((microtime(true) - $jtg_microtime), 0) . " " . JText::_('COM_JTG_DEBUG_TIMES') . " parseOLLayer<br />\n";
		$coords = $this->parseXMLlinesOL();
		$zeiten .= (int) round((microtime(true) - $jtg_microtime), 0) . " " . JText::_('COM_JTG_DEBUG_TIMES') . " parseXMLlinesOL<br />\n";

		if ( $this->allCoords !== null )
		{
			$map .= $coords['center'];
		}
		else
		{
			$map .= $this->wpCenter;
		}

		if ( $coords !== null )
		{
			$map .= $coords['coords'];
		}

		$zeiten .= (int) round((microtime(true) - $jtg_microtime ), 0) . " " . JText::_('COM_JTG_DEBUG_TIMES') . " parseOLMarker<br />\n";
		$map .= $this->parseOLGeotaggedImgs($track->id, $cfg->max_geoim_height, $iconpath, $httpiconpath);
		$zeiten .= (int) round((microtime(true) - $jtg_microtime ), 0) . " " . JText::_('COM_JTG_DEBUG_TIMES') . " parseOLGeotaggedImgs<br />\n";

		if ($this->wps !== false)
		{
			$map .= $this->parseWPs();
		}

		$zeiten .= (int) round((microtime(true) - $jtg_microtime ), 0) . " " . JText::_('COM_JTG_DEBUG_TIMES') . " parseOLMapCenterSingleTrack<br />\n";
		$map .= $this->parseOLMapFunctions();
		$zeiten .= (int) round((microtime(true) - $jtg_microtime ), 0) . " " . JText::_('COM_JTG_DEBUG_TIMES') . " parseOLMapFunctions<br />\n";

		$map .= $this->parseScriptOLFooter();
		$zeiten .= (int) round((microtime(true) - $jtg_microtime ), 0) . " " . JText::_('COM_JTG_DEBUG_TIMES') . " parseScriptOLFooter<br />\n";
		$map .= "\n<!-- writeTrackCOM_JTG END -->\n";

		return $map;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $file    param_description
	 * @param   unknown_type  $params  param_description
	 *
	 * @return return_description
	 */
	public function writeSingleTrackOL($file,$params=false)
	{
		// For little Map in Administration
		$mainframe = JFactory::getApplication();
		jimport('joomla.filesystem.file');

		$map = "\n<!-- writeSingleTrackCOM_JTG BEGIN -->\n";
		$map .= $this->parseScriptOLHead();

		$map .= $this->parseOLMap();
		$map .= $this->parseOLMapControl($params, true);
		$map .= $this->parseOLLayer();
		$coords = $this->parseXMLlinesOL();
		$map .= $coords['coords'];

		if ($this->wps)
		{
			$map .= $this->parseWPs();
		}

		if ( $coords !== null )
		{
			$map .= $coords['center'];
		}
		else
		{
			$map .= $wp['center'];
		}

		$map .= $this->parseOLMapFunctions();

		$map .= $this->parseScriptOLFooter();
		$map .= "<!-- writeSingleTrackCOM_JTG END -->\n";

		return $map;
	}

	/**
	 * function_description
	 *
	 * @return string
	 */
	private function parseScriptOLHead()
	{
		$mainframe = JFactory::getApplication();
		jimport('joomla.filesystem.folder');
		$template = $mainframe->getTemplate();
		$imgpath = 'templates/' . $template . '/css/ol_images';

		if ( JFolder::exists(JPATH_SITE . '/' . $imgpath))
		{
			$imgpath = JUri::root() . $imgpath;
		}
		else
		{
			$imgpath = JUri::root() . 'components/com_jtg/assets/template/default/ol_images/';
		}

		$map = "\n<!-- parseScriptOLHead BEGIN -->\n";
		$map .= "<script type=\"text/javascript\">\n";
		$map .= "	OpenLayers.Popup.FramedCloud.prototype.autoSize = false;\n";
		$map .= "	var AutoSizeFramedCloud = OpenLayers.Class(OpenLayers.Popup.FramedCloud, {\n	'autoSize': true\n	});\n";
		$map .= "	var AutoSizeAnchored = OpenLayers.Class(OpenLayers.Popup.Anchored, {\n	'autoSize': true\n		});\n";
		$map .= "	function slippymap_init() {\n";
		$map .= "			// Control images folder : remember the trailing slash\n";

		// TODO ACCOUNT FOR TEMPLATES
		$map .= "			OpenLayers.ImgPath = \"$imgpath\" \n";
		$map .= "			olmap = new OpenLayers.Map ( {theme: null, div: \"jtg_map\",\n";
		$map .= "// <!-- parseScriptOLHead END -->\n";

		return $map;
	}

	/**
	 * function_description
	 *
	 * @return string
	 */
	private function parseScriptOLFooter()
	{
		$map = "// <!-- parseScriptOLFooter BEGIN -->\n";

		// Close slippymap_s_init script
		$map .= "}\n";
		$map .= "</script>\n";
		$map .= "<!-- parseScriptOLFooter END -->\n";

		return $map;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $params     param_description
	 * @param   unknown_type  $adminonly  param_description
	 *
	 * @return string
	 */
	private function parseOLMapControl($params, $adminonly = false)
	{
		if ( $this->unit == "Kilometer" )
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

		$control = "// <!-- parseOLMapControl BEGIN -->\n";
		$control .= "		controls:[\n";
		$control .= "// 	Don't forget to remove comma in last line.\n// 	Otherwise it doesn't work with IE.\n";

		if ( ( $params === false ) OR ( $params->get('jtg_param_allow_keymove') != "0" ) )
		{
			$control .= "				new OpenLayers.Control.KeyboardDefaults(),	// Tastatur: hoch, runter, links, rechts, +, -\n";
		}

		if ( ( $params === false ) OR ( $params->get('jtg_param_show_mouselocation') != "0" ) )
		{
			$control .= "				new OpenLayers.Control.MousePosition(),		// Koordinate des Mauszeigers (lat, lon)\n";
		}

		if ( ( $params === false ) OR ( $params->get('jtg_param_show_layerswitcher') != "0" ) )
		{
			$control .= "				new OpenLayers.Control.LayerSwitcher(),		// Menue zum ein/aus-Schalten der Layer\n";
		}

		if ( $adminonly === false)
		{
			if ( ( $params === false ) OR ( $params->get('jtg_param_show_panzoombar') != "0" ) )
			{
				$control .= "				new OpenLayers.Control.PanZoomBar(),		// Zoombalken\n";
			}

			if ( ( $params === false ) OR ( $params->get('jtg_param_show_attribution') != "0" ) )
			{
				$control .= "				new OpenLayers.Control.Attribution(),		// CC-By-SA ... \n";
			}

			if ( ( $params === false ) OR ( $params->get('jtg_param_show_scale') != "0" ) )
			{
				$control .= "				new OpenLayers.Control.ScaleLine({\n					topOutUnits: '" .
					$topOutUnits . "',\n					topInUnits: '" .
					$topInUnits . "',\n					bottomOutUnits: '" .
					$bottomOutUnits . "',\n					bottomInUnits: '" .
					$bottomInUnits . "'\n				}),
									// Maßstab (nur an Äquator genau?)\n";
			}
		}

		$control .= "			],\n";
		$control .= "				maxExtent: new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),\n";
		$control .= "				maxResolution: 156543.0399,\n";
		$control .= "				numZoomLevels: 19,\n";
		$control .= "				units: \"m\",\n";
		$control .= "				projection: new OpenLayers.Projection(\"EPSG:900913\"),\n";
		$control .= "				displayProjection: new OpenLayers.Projection(\"EPSG:4326\")\n";
		$control .= "			} );\n\n";

		// Don't use fullscreen option on admin site
		if ( $_SERVER['SCRIPT_URL'] !== '/administrator/index.php')
		{
			// Add FullScreen Toggle control Source from http://www.utagawavtt.com
			$control .= "		// <!-- parseOLMapFullscreen button BEGIN -->\n";
			$control .= "		var fullscreenToolbar = new OpenLayers.Control.NavToolbar();\n";
			$control .= "		var button_fullscreen = new OpenLayers.Control.Button({\n";
			$control .= "			displayClass: \"buttonFullScreen\",\n";
			$control .= "			trigger: switch_fullscreen2 // Switch_fullscreen\n";
			$control .= "		});\n";
			$control .= "		button_fullscreen.title = \"Plein écran\";\n";
			$control .= "		fullscreenToolbar.addControls([button_fullscreen]);\n";
			$control .= "		olmap.addControl(fullscreenToolbar);\n";
			$control .= "		// <!-- parseOLMapFullscreen button END -->\n";
		}

		$control .= "		// <!-- parseOLMapmouse wheel (this must stay after NavToolbar) BEGIN -->\n";

		if ( ( $params === false ) OR ( $params->get('jtg_param_allow_mousemove') != "0" ) )
		{
			$control .= "				var mousewheelcontrol = new OpenLayers.Control.Navigation();		// with mouse Zoom/move\n";
			$control .= "				olmap.addControl(mousewheelcontrol);\n";
		}
		else
		{
			// See http://www.stoimen.com/blog/2009/06/20/openlayers-disable-mouse-wheel-on-zoom/
			$control .= "				var controls = olmap.getControlsByClass('OpenLayers.Control.Navigation'); \n";
			$control .= "				for (var i = 0; i < controls.length; ++i) \n";
			$control .= "				controls[i].disableZoomWheel(); \n";
		}

		$control .= "		// <!-- parseOLMapmouse wheel (this must stay after NavToolbar) END -->\n";
		$control .= "// <!-- parseOLMapControl END -->\n";

		return $control;
	}

	/**
	 * function_description
	 *
	 * @return string
	 */
	private function markerFunctionOL()
	{
		$map = "// <!-- markerFunctionCOM_JTG BEGIN -->\n";
		/*
		 $map .= "function createMarker(point,html) {\n";
		$map .= "var marker = new GMarker(point);\n";
		$map .= "GEvent.addListener(marker, 'click', function() {\n";
				$map .= "marker.openInfoWindowHtml(html);\n";
				$map .= "});\n";
		$map .= "return marker;\n";
		$map .= "}\n";
		*/
		$map .= "// <!-- markerFunctionCOM_JTG END -->\n";

		return $map;
	}

	/**
	 * function_description
	 *
	 * @return string
	 */
	private function parseOLMap()
	{
		$string = "// <!-- parseOLMap BEGIN -->\n";

		$string .= "// <!-- parseOLMap END -->\n";

		return $string;
	}

	/**
	 * function_description
	 *
	 * @return array
	 */
	private function parseXMLlinesOL()
	{
		// 	global $jtg_microtime;

		$cfg = JtgHelper::getConfig();
		$iconpath = JUri::root() . "components/com_jtg/assets/template/" . $cfg->template . "/images/";

		$link = JUri::current();

		$string_se = "";
		$center = "";

		// TODO if (AnimatedCursorLayer)
		if (true)
		{
			/* AnimatedCursorLayer
			 * This MUST be added after olmap.zoomToExtent
			*/
			$document = JFactory::getDocument();
			$document->addScript('components/com_jtg/assets/js/animatedCursor.js');

			$center .= "\n// <!-- parseOLMapAnimatedCursorLayer BEGIN -->\n";
			$center .= "longitudeData = $this->longitudeData;\n";
			$center .= "latitudeData = $this->latitudeData;\n";
			$center .= "var points = [];\n";
			$center .= "var style ={strokeOpacity: 0.7, strokeColor: \"#ff00ff\", strokeWidth: 5, graphicZIndex: 5}\n";
			$center .= "for (var i = 0; i < longitudeData.length; i++) {\n";
			$center .= "	var point = new OpenLayers.Geometry.Point(longitudeData[i], latitudeData[i]). transform(new OpenLayers.Projection(\"EPSG:4326\"), olmap.getProjectionObject());\n";
			$center .= "	points.push(point);\n";
			$center .= "}\n";
			$center .= "var line = new OpenLayers.Geometry.LineString(points);\n";
			$center .= "var linefeature  = new OpenLayers.Feature.Vector(line, null, style);\n";

			$center .= "animatedCursorLayer = new OpenLayers.Layer.Vector(\"$this->trackname\");\n";
			$center .= "animatedCursorLayer.addFeatures([linefeature]);\n";
			$center .= "animatedCursorLayer.gpxfeature = animatedCursorLayer.features[0];\n";
			$center .= "olmap.addLayer(animatedCursorLayer);\n";
			$center .= "\n// <!-- parseOLMapAnimatedCursorIcon BEGIN -->\n";
			$center .= "animatedCursorIcon = new OpenLayers.Layer.Markers(\"Animated Cursor\", {\n";
			$center .= "displayInLayerSwitcher: false });\n";
			$center .= "olmap.addLayer(animatedCursorIcon);\n";
			$center .= "animatedCursorIcon.setVisibility(false);\n";
			$center .= "var size = new OpenLayers.Size(32,32);\n";
			$center .= "var offset = new OpenLayers.Pixel(-16,-32);\n";
			$center .= "var cursorIcon = new OpenLayers.Icon(\"" . JUri::base() . "/components/com_jtg/assets/images/orange-dot.png\",size,offset);\n";
			$center .= "var lonLatStart = new OpenLayers.LonLat(" . $this->track[1]->start . ") . ";
			$center .= "transform(new OpenLayers.Projection(\"EPSG:4326\"), olmap.getProjectionObject());\n";
			$center .= "animatedCursorIcon.addMarker(new OpenLayers.Marker(lonLatStart,cursorIcon));\n";
			$center .= "// <!-- parseOLMapAnimatedCursorIcon END -->\n";
		}
		else
		{
			$center .= "<!-- AnimatedCursorLayer not activated -->\n";
		}

		$center .= "// <!-- parseOLMapAnimatedCursorLayer END -->\n";

		$string = "// <!-- parseXMLlines BEGIN -->\n";

		if ($this->trackCount == 0)
		{
			return;
		}

		$tracksColors = $this->calculateAllColors($this->trackCount);

		for ($i = 1; $i <= $this->trackCount; $i++)
		{
			$m = microtime(true);
			$coords = $this->track[$i]->coords;
			$subid = $link . "&amp;subid=" . $i;
			$color = "#" . $tracksColors[$i - 1];

			$string_se .= "var lonLatStart" . $i . " = new OpenLayers.LonLat(" . $this->track[$i]->start . ") . ";
			$string_se .= "transform(new OpenLayers.Projection(\"EPSG:4326\"), olmap.getProjectionObject());\n";
			$string_se .= "var lonLatStop" . $i . " = new OpenLayers.LonLat(" . $this->track[$i]->stop . ") . ";
			$string_se .= "transform(new OpenLayers.Projection(\"EPSG:4326\"), olmap.getProjectionObject());\n";
			$string_se .= "var sizeStart" . $i . " = new OpenLayers.Size(24,24);\n";
			$string_se .= "var sizeStop" . $i . " = new OpenLayers.Size(24,24);\n";
			$string_se .= "var offsetStart" . $i . " = new OpenLayers.Pixel(-3,-22);\n";
			$string_se .= "var offsetStop" . $i . " = new OpenLayers.Pixel(-19,-22);\n";
			$string_se .= "var iconStart" . $i . " = ";
			$string_se .= "new OpenLayers.Icon(\"" . $iconpath . "trackStart.png\",";
			$string_se .= "sizeStart" . $i . ",offsetStart" . $i . ");\n";
			$string_se .= "var iconStop" . $i . " = new OpenLayers.Icon(\"" . $iconpath . "trackDest.png\",";
			$string_se .= "sizeStop" . $i . ",offsetStop" . $i . ");\n";
			$string_se .= "layer_startstop.addMarker(new OpenLayers.Marker(lonLatStop" . $i . ",iconStop" . $i . "));\n";
			$string_se .= "popupClassStart = AutoSizeFramedCloud;\n";
			$string_se .= "popupContentHTMLStart = '";
			$string_se .= "<font style=\"font-weight: bold;\" color=\"" . $color . "\">";
			$string_se .= ($this->track[$i]->trackname? $this->track[$i]->trackname : JText::_('COM_JTG_TRACK') . $i);
			$string_se .= "</font>";
			$string_se .= "';\n";
			$string_se .= "addlayer_startstop(lonLatStart" . $i . ", popupClassStart, popupContentHTMLStart, true, false, iconStart" . $i . ", olmap);\n";
		}

		$string .= "layer_startstop = new OpenLayers.Layer.Markers(";
		$string .= "\"" . $i . ": " . $this->trackname . "\"";
		$string .= ", { displayInLayerSwitcher: false }";
		$string .= ");";
		$string .= "olmap.addLayer(layer_startstop);";
		$string .= "layer_startstop.setVisibility(true);";
		$string .= $string_se;
		$string .= "// <!-- parseXMLlines END -->\n";

		$center .= "// <!-- parseOLMapCenterSingleTrack BEGIN -->\n";
		$center .= "var min = lonLatToMercator(new OpenLayers.LonLat";
		$center .= "(" . $this->bbox_lon_min . "," . $this->bbox_lat_min . "));\n";
		$center .= "var max = lonLatToMercator(new OpenLayers.LonLat";
		$center .= "(" . $this->bbox_lon_max . "," . $this->bbox_lat_max . "));\n";
		$center .= "olmap.zoomToExtent(new OpenLayers.Bounds(min.lon, min.lat, max.lon, max.lat));\n";
		$center .= "// <!-- parseOLMapCenterSingleTrack END -->\n";

		return array( "coords" => $string, "center" => $center );
	}

	/**
	 * function_description
	 *
	 * @param   string  $file  param_description
	 *
	 * @return array
	 */
	public function giveClickLinks($file)
	{
		$i = 0;
		$links = array();
		$this->gpsFile = $file;

		while (true)
		{
			$coords = $this->getCoords($file, $i);

			if ( $coords == false )
			{
				break;
			}

			$xml = $this->loadFile();
			$xml = $xml->trk[$i];
			$link = JFactory::getURI();
			$link = $link->_uri;
			$link = $link . "&amp;subid=" . $i;
			$name = (string) $xml->name;
			$links[$i]['link'] = $link;
			$links[$i]['name'] = $name;
			$i++;
		}

		return $links;
	}

	/**
	 * Return Filename (trackid=-1) or track name
	 *
	 * @param   string   $file     GPS file name
	 * @param   object   $xml      parsed xml file
	 * @param   integer  $trackid  trackid
	 *
	 * @return string
	 */
	public function getTrackName($file, $xml, $trackid = -1)
	{
		// TODO function keeped for TCX, move this in extractCoordsTCX
		jimport('joomla.filesystem.file');
		$ext = JFile::getExt($file);

		if ($ext == 'tcx')
		{
			if ($trackid < 0) // Search for file name
			{
				$trackname = 'filename';

				if ( strlen($trackname) == 0)
				{
					$trackname = @$xml->trk[0]->name;
				}
			}
			else // Search for track name
			{
				$trackname = "track_$trackid";
			}

			return $trackname;
		}
		else
		{
			return null;
		}
	}
	// Osm END
}

/**
 * WpClass class for the jtg component
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @since       0.9.10
 */
class WpClass
{
	var $lon = 0;

	var $lat = 0;

	var $value = null;

	var $url = null;

	var $urlname = null;

	var $name = null;

	var $cmt = null;

	var $desc = null;

	var $ele = null;

	var $type = null;

	var $sym = null;
}

/**
 * GpsCoordsClass class for the jtg component
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @since       0.8
 */
class GpsCoordsClass
{
	/**
	 * counts the total distance of a track
	 * $koords look like this: $array($point1(array(lat,lon)),$point2(array(lat,lon)))...
	 *
	 * @param   array  $koord  param_description
	 *
	 * @return int kilometers
	 */
	public function getDistance($koord)
	{
		if (!is_array($koord))
		{
			return false;
		}

		$temp = 0;

		// Erdradius, ca. Angabe
		$earthRadius = 6378.137;

		foreach ($koord as $key => $fetch)
		{
			if (isset($koord[$key + 1]))
			{
				$first_latitude = $koord[$key][1];
				$first_longitude = $koord[$key][0];
				$first_latitude_rad = deg2rad($first_latitude);
				$first_longitude_rad = deg2rad($first_longitude);

				$second_latitude = $koord[$key + 1][1];
				$second_longitude = $koord[$key + 1][0];
				$second_latitude_rad = deg2rad($second_latitude);
				$second_longitude_rad = deg2rad($second_longitude);

				$dis = acos(
						(sin($first_latitude_rad) * sin($second_latitude_rad)) +
						(cos($first_latitude_rad) * cos($second_latitude_rad) *
								cos($second_longitude_rad - $first_longitude_rad))
				) * $earthRadius;

				if (!is_nan($dis))
				{
					$temp = $temp + $dis;
				}
			}
		}

		$distance = round($temp, 2);

		return $distance;
	}
}
