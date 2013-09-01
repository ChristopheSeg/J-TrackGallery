<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJO3SM and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
require_once JPATH_ROOT . DS . 'components' . DS . 'com_jtg' . DS . 'helpers' . DS . 'gpsClass.php';
/**
 *
 */
class JtgViewDefault extends JView
{
    /**
     *
     * @param obejct $tpl
     */
    function display($tpl = null,$map=false) {

        $cfg =& JtgHelper::getConfig();

        $gps = new gpsClass();

        $document =& JFactory::getDocument();
        $this->cfg = $cfg;
        $this->gps = $gps;
        
        parent::display($tpl);
    }
}
