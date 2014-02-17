<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooosm and joomGPStracks teams
 * @package    com_jtg
 * @subpackage frontend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */
 
class JtgControllerGeo extends JtgController
{
	function __construct()
	{
		parent::__construct();
	}

	function save() {
		$user = JFactory::getUser();
		$userid = (int)$user->id;
		JSession::checkToken() or jexit( 'Invalid Token' );
		$lat =& JRequest::getVar('lat');
		$lon =& JRequest::getVar('lon');
		$visible =& JRequest::getVar('visible');
		$mainframe =& JFactory::getApplication();
		$query = "INSERT INTO #__jtg_users (jtglat,jtglon,jtgvisible,user_id) VALUES ".
			"('" . $lat . "','" . $lon . "','" . $visible . "','" . $userid . "') ". 
			"ON DUPLICATE KEY UPDATE ".
			"jtglat='" . $lat . "', ".
			"jtglon='" . $lon . "', ".
			"jtgvisible='" . $visible . "' ";
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		if ($db->getErrorNum()) 
		{
		    JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_DATABASE_ERROR_H'), 'Warning');
		    JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_DATABASE_ERROR') . "<br /><br />\n" . $db->stderr(), 'Warning');
			return false;
		} else {
			$url = "index.php?option=com_jtg&view=jtg&layout=geo";
			$this->setRedirect( JRoute::_($url,false), JText::_('COM_JTG_POSITION_SUCCESSFUL_SAVED') );
			return true;
		}
	}
}
