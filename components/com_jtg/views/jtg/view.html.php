<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooOSM and joomGPStracks teams
 * @package    com_jtg
 * @subpackage frontend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the jtg component
 */
class JtgViewjtg extends JView
{
	function display($tpl = null) {
// @ToDo split in jtg and geoposition
		$mainframe =& JFactory::getApplication();
		$cfg = JtgHelper::getConfig();
		$gps = new gpsClass();
		$document =& JFactory::getDocument();
		$sitename = $document->getTitle() . " - " . $mainframe->getCfg('sitename');
		// $mainframe->setPagetitle($sitename);
		$mapsxml = JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'maps' . DS . 'maps.xml';
		$this->params_maps = new JRegistry( 'com_jtg', $mapsxml );
		$params = &JComponentHelper::getParams( 'com_jtg' );
		layoutHelper::parseMap($document,$cfg->map);
		$tracks = (bool)$params->get('osm_tracks');	// show Tracks in Overview-Map?

		$model = $this->getModel();
		$cats = $model->getCatsData();
		$sortedcats = $model->getCatsData(true);
		$where = layoutHelper::filterTracks($cats);

		$access = JtgHelper::giveAccessLevel();
		$otherfiles = $params->get('osm_otherfiles');
		$mayisee = JtgHelper::MayIsee($where,$access,$otherfiles);
		$boxlinktext = array(
		0 => JText::_('COM_JTG_LINK_VIEWABLE_FOR_PUBLIC'),
		1 => JText::_('COM_JTG_LINK_VIEWABLE_FOR_REGISTERED'),
		2 => JText::_('COM_JTG_LINK_VIEWABLE_FOR_SPECIAL'),
		9 => JText::_('COM_JTG_LINK_VIEWABLE_FOR_PRIVATE')
		);

		if((bool)$params->get('osm_lh'))
		$lh = layoutHelper::navigation();
		else
		$lh = null;
		$footer = layoutHelper::footer();
		$disclaimericons = layoutHelper::disclaimericons();
		$rows = $model->getTracksData(NULL,NULL,$where);
		$geo = JRoute :: _('index.php?option=com_jtg&view=jtg&layout=geo', false);
		$this->newest =	null;
		if($params->get('osm_newest') != 0)
		$this->newest =	layoutHelper::parseTopNewest($where,$mayisee,$model,$params->get('osm_newest'));
		$this->hits = null;
		if($params->get('osm_mostklicks') != 0)
		$this->hits =		layoutHelper::parseTopHits($where,$mayisee,$model,$params->get('osm_mostklicks'));
		$this->best = null;
		if($params->get('osm_best') != 0)
		$this->best =		layoutHelper::parseTopBest($where,$mayisee,$model,$params->get('osm_best'),$params->get('osm_vote_show_stars'));
		$this->rand = null;
		if($params->get('osm_rand') != 0)
		$this->rand =		layoutHelper::parseTopRand($where,$mayisee,$model,$params->get('osm_rand'));
		$toptracks = layoutHelper::parseToptracks($params);

		$published = "\na.published = 1 AND a.hidden = 0";

		switch ($mayisee) {
			case null:
				switch ($where) {
					case "":
						$where = " WHERE " . $published;
						break;
					default:
						$where = " WHERE " . $where . " AND " . $published;
						break;
				}
				break;
			default:
				$where = " WHERE " . $mayisee . " AND " . $published;
				break;
		}
		$this->map = $cfg->map;	// Zusatz für Kartenauswahl
		$this->lh = $lh;
		$this->boxlinktext = $boxlinktext;
		$this->footer = $footer;
		$this->disclaimericons = $disclaimericons;
		$this->gps = $gps;
		$this->rows = $rows;
		$this->where = $where;
		$this->cats = $cats;
		$this->sortedcats = $sortedcats;
		$this->cfg = $cfg;
		$this->geo = $geo;
		$this->toptracks = $toptracks;
		$this->tracks = $tracks;
		$this->params = $params;

		parent::display($tpl);
	}
}
