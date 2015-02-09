<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @package     Comjtg
 * @subpackage  Backend
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

// Import Joomla! libraries
jimport('joomla.application.component.view');
require_once JPATH_ROOT . '/components/com_jtg/helpers/gpsClass.php';
/**
 * JtgViewDefault class for the jtg component
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @since       0.8
 */

class JtgViewDefault extends JViewLegacy
{
	/**
	 * function_description
	 *
	 * @param   object  $tpl  param_description
	 * @param   unknown_type  $map  param_description
	 *
	 * @return return_description
	 */
	public function display($tpl = null,$map = false)
	{
		$cfg = JtgHelper::getConfig();
		$gpsData = new GpsDataClass($cfg->unit);
		$document = JFactory::getDocument();
		$this->cfg = $cfg;
		$this->gps = $gpsData;

		parent::display($tpl);
	}
}
