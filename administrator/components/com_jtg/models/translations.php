<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooosm and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');
/**
 * Model Class Terrain
 */
class JtgModelTranslations extends JModel
{
	function __construct() {
		parent::__construct();
	}

	function saveLanguage() {
		JRequest::checkToken() or die( 'Invalid Token' );
		$languages = $this->getRawLanguages();
		foreach ($languages as $lang) {
			$file = JPATH_SITE . DS . 'language' . DS . $lang['tag'] . DS . $lang['tag'] . DS ."com_jtg_additional.ini";
			$inhalt =& JRequest::getVar( $lang['tag'] );
			if(!JFile::write( $file, $inhalt ))
			return false;
		}
		return true;
	}

	function getRawLanguages() {
		$language = &JFactory::getLanguage();
		$languages = $language->getKnownLanguages();
		return $languages;
	}

	function getLanguages() {
		$languages = $this->getRawLanguages();
		$newlanguages = array();
		foreach ($languages as $lang) {
			$rows = 5;
			$cols = 10;
			$newlanguages[$lang['tag']] = array();
			$newlanguages[$lang['tag']]['name'] = $lang['name'];
			$newlanguages[$lang['tag']]['tag'] = $lang['tag'];
			$path = JPATH_SITE . DS . 'language' . DS . $lang['tag'] . DS;
			$file = $path . $lang['tag'] . DS ."com_jtg_additional.ini";
			$newlanguages[$lang['tag']]['file'] = $file;
			if (!JFile::exists($file))
// TODO These strings might appears in language file, not additional.....!!!
			{
				if ( $lang['tag'] == "de-DE" ) // Fillin Standard-Values for German or other Langauge in English
				$buffer = "COM_JTG_CAT_BIKE=Fahrrad
COM_JTG_CAT_MOTORBIKE=Motorrad
COM_JTG_CAT_PEDESTRIAN=Fußgänger

COM_JTG_CAT_CAR=Auto
COM_JTG_CAT_CAR_FOCUS=Ford Focus
COM_JTG_CAT_CAR_VECTRA=Opel Vectra
COM_JTG_CAT_GEOCACHE=Geocaching

COM_JTG_TERRAIN_FARM_TRACK=Feldweg
COM_JTG_TERRAIN_STREET=Straße

COM_JTG_LEVEL_1=Ganz leicht
COM_JTG_LEVEL_2=Leicht
COM_JTG_LEVEL_3=Mittel
COM_JTG_LEVEL_4=Schwer
COM_JTG_LEVEL_5=Richtig Schwer

COM_JTG_MAP_MAPNIK=Mapnik
COM_JTG_MAP_OSM_HIKE_AND_BIKE=OSM_HIKE_AND_BIKE
COM_JTG_MAP_CYCLEMAP=Radfahrerkarte
COM_JTG_MAP_NONAME=Straßen ohne Namen
COM_JTG_MAP_VE_HYBRID=bing Hybrid
COM_JTG_MAP_VE_ROAD=bing Straßen
COM_JTG_MAP_VE_AERIAL=bing Vogelperspektive
COM_JTG_MAP_YAHOOSTREET=Yahoo Straßen
COM_JTG_MAP_GOOGLESTREET=Google Straßen
COM_JTG_MAP_GOOGLESAT=Google Satellit
COM_JTG_MAP_GOOGLEHYBRID=Google Hybrid";
				else
				$buffer = "COM_JTG_CAT_BIKE=Bicycle
COM_JTG_CAT_MOTORBIKE=Motorbike
COM_JTG_CAT_PEDESTRIAN=Pedestrian

COM_JTG_CAT_CAR=Car
COM_JTG_CAT_CAR_FOCUS=Ford Focus
COM_JTG_CAT_CAR_VECTRA=Vauxhall Vectra
COM_JTG_CAT_GEOCACHE=Geocaching

COM_JTG_TERRAIN_FARM_TRACK=Farm Track
COM_JTG_TERRAIN_STREET=Street

COM_JTG_LEVEL_1=Completely easily
COM_JTG_LEVEL_2=Easy
COM_JTG_LEVEL_3=Medium
COM_JTG_LEVEL_4=Hard
COM_JTG_LEVEL_5=Very hard

COM_JTG_MAP_MAPNIK=Mapnik
COM_JTG_MAP_OSM_HIKE_AND_BIKE=OSM_HIKE_AND_BIKE
COM_JTG_MAP_CYCLEMAP=CycleMap
COM_JTG_MAP_NONAME=NoName
COM_JTG_MAP_VE_HYBRID=bing Hybrid
COM_JTG_MAP_VE_ROAD=bing Streets
COM_JTG_MAP_VE_AERIAL=bing Aerial
COM_JTG_MAP_YAHOOSTREET=Yahoo street
COM_JTG_MAP_GOOGLESTREET=Google Map
COM_JTG_MAP_GOOGLESAT=Google Satellite
COM_JTG_MAP_GOOGLEHYBRID=Google Hybride";
				$iswritable = JPath::getPermissions($path);
				$iswritable = $iswritable[1];
				if ( ( JPath::canChmod($path) ) AND  ( $iswritable == "w" ) )
				{
					JFile::write( $file, $buffer );
				}
			}
			if (JFile::exists($file))
			{
				$content = JFile::read($file);
				$text = explode("\n",$content);
				foreach ($text as $val) { // find out max line lengh
					if ( strlen($val) > $cols)
					$cols = strlen($val);
				}
				if ( JPath::canChmod($file) )
				{
					$header_color = "green";
					$header_desc = JText::_('COM_JTG_WRITABLE');
				} else {
					$header_color = "red";
					$header_desc = JText::_('COM_JTG_UNWRITABLE');
				}
			} else {
				if ( ( JPath::canChmod($path) ) AND  ( $iswritable == "w" ) )
				{
					$header_color = "green";
					$header_desc = JText::_('COM_JTG_WRITABLE');
				} else {
					$header_color = "red";
					$header_desc = JText::_('COM_JTG_UNWRITABLE');
				}
				$content = JText::_('COM_JTG_UNWRITABLE');
			}
			$newlanguages[$lang['tag']]['header'] = $lang['name'] . "<br /><font color=\"" . $header_color . "\"><small>(" . $header_desc . ")</small></font>";
			$size = substr_count($content,"\n");
			$rows = $size + $rows;
			$newlanguages[$lang['tag']]['rows'] = $rows;
			$newlanguages[$lang['tag']]['cols'] = $cols + 2;
			$newlanguages[$lang['tag']]['value'] = $content;
		}
		return $newlanguages;
	}
}
