<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooOSM and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
require_once JPATH_ROOT.DS.'components'.DS.'com_jtg'.DS.'helpers'.DS.'gpsClass.php';
/**
 *
 */
class jtgViewDefault extends JView {
    /**
     *
     * @param obejct $tpl
     */
    function display($tpl = null,$map=false) {

        $cfg =& jtgHelper::getConfig();

        $gps = new gpsClass();

        $document =& JFactory::getDocument();
	if ( $map == "google" ) {
		$document->addScript('http://www.google.com/jsapi?key='.$cfg->apikey);
		$document->addScript('http://www.google.com/uds/api?file=uds.js&v=1.0&key='.$cfg->apikey);
		$document->addStylesheet('http://www.google.com/uds/css/gsearch.css');
		$document->addStylesheet('http://www.google.com/uds/solutions/localsearch/gmlocalsearch.css');
	}
        $this->cfg = $cfg;
        $this->gps = $gps;
        
        parent::display($tpl);
    }
}
