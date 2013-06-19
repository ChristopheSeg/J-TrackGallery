<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: view.html.php,v 1.1 2011/04/03 08:41:56 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');

/**
 *
 */
class jtgViewInfo extends JView {
    /**
     *
     * @param object $tpl
     */
    function display($tpl = null) {

        parent::display($tpl);
    }

	function getVersion() {
		$return = array();
		$latest = file ("https://github.com/ChristopheSeg/J-TrackGallery/raw/master/version.php");
		$latest = $latest[0];
		$xml = simplexml_load_file(JPATH_SITE.DS."administrator".DS."components".DS."com_jtg".DS."jtg.xml");
		$thisversion = (string)$xml->version;
		

		if (! version_compare( strtolower($latest),strtolower($thisversion) ) ) {
		        // $latest  > $thisversion (current).. must upgrade
			$return["this"] = $thisversion;
			$return["latest"] = "<font color=\"green\">$latest: ".JText::_('COM_JTG_STATE_LATEST')."</font>\n";
		} else {
			$return["this"] = "<font color=\"orange\">".$thisversion."</font>";
			$return["latest"] = "<font color=\"red\">".$latest.": ".JText::_('COM_JTG_UPDATE')."</font> ".
				JText::_('COM_JTG_UPDATE_LINK_A')." ".
				"<a href=\"http://jtrackgallery.net/download\" target=\"_blank\">".
				JText::_('COM_JTG_UPDATE_LINK_B').
				"</a> ".JText::_('COM_JTG_UPDATE_LINK_C')." <font color=\"orange\"></font>".
				JText::_('COM_JTG_UPDATE_LINK_D');
		}
		return $return;
	}
}
