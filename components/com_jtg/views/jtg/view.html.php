<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @package    Comjtg
 * @author     Christophe Seguinot <christophe@jtrackgallery.net>
 * @copyright  2013 J!Track Gallery, InJooosm and joomGPStracks teams
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the jtg component
 */
class JtgViewjtg extends JViewLegacy
{
	function display($tpl = null) {
		// @ToDo split in jtg and geoposition
		$mainframe = JFactory::getApplication();
		$cfg = JtgHelper::getConfig();
		$gpsData = new gpsDataClass($cfg->unit);
		$document = JFactory::getDocument();
		jimport('joomla.filesystem.file');
		// Load Openlayers stylesheet first (for overridding)
		// TODO add openlayers style in JTrackGallery (url may vary!)
		$document->addStyleSheet('http://dev.openlayers.org/theme/default/style.css');
		// Then load jtg_map stylesheet
		$tmpl = ($cfg->template = "") ? $cfg->template : 'default';
		$document->addStyleSheet(JUri::base().'components/com_jtg/assets/template/'.$tmpl.'/jtg_map_style.css');
		// Then override style with user templates
		$template = $mainframe->getTemplate();
		$template_jtg_map_style='templates/' . $template . '/css/jtg_map_style.css';
		if ( JFile::exists($template_jtg_map_style) )
		{
			$document->addStyleSheet('templates/' . $template . '/css/jtg_map_style.css' );
		}

		$sitename = $document->getTitle() . " - " . $mainframe->getCfg('sitename');
		// $mainframe->setPagetitle($sitename);
		$mapsxml = JPATH_COMPONENT_ADMINISTRATOR . '/views/maps/maps.xml';
		$this->params_maps = new JRegistry( 'com_jtg', $mapsxml );
		$params = JComponentHelper::getParams( 'com_jtg' );
		layoutHelper::parseMap($document);
		$tracks = (bool)$params->get('jtg_param_tracks');	// Show Tracks in Overview-Map?

		$model = $this->getModel();
		$cats = $model->getCatsData();
		$sortedcats = $model->getCatsData(true);
		$where = layoutHelper::filterTracks($cats);

		$access = JtgHelper::giveAccessLevel();
		$otherfiles = $params->get('jtg_param_otherfiles');
		$mayisee = JtgHelper::MayIsee($where,$access,$otherfiles);
		$boxlinktext = array(
				0 => JText::_('COM_JTG_LINK_VIEWABLE_FOR_PUBLIC'),
				1 => JText::_('COM_JTG_LINK_VIEWABLE_FOR_REGISTERED'),
				2 => JText::_('COM_JTG_LINK_VIEWABLE_FOR_SPECIAL'),
				9 => JText::_('COM_JTG_LINK_VIEWABLE_FOR_PRIVATE')
		);

		if ((bool)$params->get('jtg_param_lh'))
			$lh = layoutHelper::navigation();
		else
			$lh = null;
		$footer = layoutHelper::footer();
		$disclaimericons = layoutHelper::disclaimericons();
		$rows = $model->getTracksData(null,null,$where);
		$geo = JRoute::_('index.php?option=com_jtg&view=jtg&layout=geo', false);
		$this->newest =	null;
		if ($params->get('jtg_param_newest') != 0)
			$this->newest =	layoutHelper::parseTopNewest($where,$mayisee,$model,$params->get('jtg_param_newest'));
		$this->hits = null;
		if ($params->get('jtg_param_mostklicks') != 0)
			$this->hits = layoutHelper::parseTopHits($where,$mayisee,$model,$params->get('jtg_param_mostklicks'));
		$this->best = null;
		if ($params->get('jtg_param_best') != 0)
			$this->best = layoutHelper::parseTopBest($where,$mayisee,$model,$params->get('jtg_param_best'),$params->get('jtg_param_vote_show_stars'));
		$this->rand = null;
		if ($params->get('jtg_param_rand') != 0)
			$this->rand = layoutHelper::parseTopRand($where,$mayisee,$model,$params->get('jtg_param_rand'));
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
		$this->lh = $lh;
		$this->boxlinktext = $boxlinktext;
		$this->footer = $footer;
		$this->disclaimericons = $disclaimericons;
		$this->gpsData = $gpsData;
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
