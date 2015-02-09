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
 * JtgControllerGeo class for the jtg component
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @since       0.8
 */

class JtgControllerGeo extends JtgController
{
	/**
	 * function_description
	 *
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * function_description
	 *
	 * @return return_description
	 */
	function save()
	{
		$user = JFactory::getUser();
		$userid = (int) $user->id;
		JSession::checkToken() or jexit('Invalid Token');
		$lat = JFactory::getApplication()->input->get('lat');
		$lon = JFactory::getApplication()->input->get('lon');
		$visible = JFactory::getApplication()->input->get('visible');
		$mainframe = JFactory::getApplication();
		$query = "INSERT INTO #__jtg_users (jtglat,jtglon,jtgvisible,user_id) VALUES " .
				"('" . $lat . "','" . $lon . "','" . $visible . "','" . $userid . "') " .
				"ON DUPLICATE KEY UPDATE " .
				"jtglat='" . $lat . "', " .
				"jtglon='" . $lon . "', " .
				"jtgvisible='" . $visible . "' ";
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->execute();

		if ($db->getErrorNum)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_DATABASE_ERROR_H'), 'Warning');
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_DATABASE_ERROR') . "<br /><br />\n" . $db->stderr(), 'Warning');

			return false;
		}
		else
		{
			$url = "index.php?option=com_jtg&view=jtg&layout=geo";
			$this->setRedirect(JRoute::_($url, false), JText::_('COM_JTG_POSITION_SUCCESSFUL_SAVED'));

			return true;
		}
	}
}
