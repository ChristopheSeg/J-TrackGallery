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

// Import Joomla! libraries
jimport('joomla.application.component.view');
require_once JPATH_ROOT . '/components/com_jtg/helpers/gpsClass.php';
/**
 *
 */
class JtgViewDefault extends JViewLegacy
{
	/**
	 *
	 * @param obejct $tpl
	 */
	function display($tpl = null,$map = false)
	{
		$cfg = JtgHelper::getConfig();
		$gpsData = new gpsDataClass($cfg->unit);
		$document = JFactory::getDocument();
		$this->cfg = $cfg;
		$this->gps = $gpsData;

		parent::display($tpl);
	}
}
