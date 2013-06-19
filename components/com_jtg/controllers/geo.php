<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: geo.php,v 1.1 2011/04/03 08:41:55 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage frontend
 * @license GNU/GPL
 * @filesource
 *
 */
 
class JtgControllerGeo extends JtgController  {

	function __construct()
	{
		parent::__construct();
	}

	function save() {
		$user = JFactory::getUser();
		$userid = (int)$user->id;
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$lat =& JRequest::getVar('lat');
		$lon =& JRequest::getVar('lon');
		$visible =& JRequest::getVar('visible');
		$mainframe =& JFactory::getApplication();
		$query = "UPDATE #__users SET ".
			"osmlat='".$lat."',".
			"osmlon='".$lon."',".
			"osmvisible='".$visible."' ".
			"WHERE id='".$userid."'";
		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->query();
		if ($db->getErrorNum()) {
			JError::raiseWarning(0,JText::_('COM_JTG_DATABASE_ERROR_H') );
			echo JText::_('COM_JTG_DATABASE_ERROR')."<br /><br />\n".$db->stderr();
			return false;
		} else {
			$url = "index.php?option=com_jtg&view=jtg&layout=geo";
			$this->setRedirect( JRoute::_($url,false), JText::_('COM_JTG_POSITION_SUCCESSFUL_SAVED') );
			return true;
		}
	}
}
