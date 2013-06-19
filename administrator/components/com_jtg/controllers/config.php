<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: config.php,v 1.1 2011/04/03 08:41:46 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_petcare'.DS.'tables');
/**
 * Controller Class Configuration
 */
class jtgControllerConfig extends jtgController
{
	/**
	 *
	 */
	function display()
	{
		parent::display();
	}

	/**
	 *
	 * @global object $mainframe
	 * @uses jtgModelConfigat::saveConfig
	 * @return  redirect
	 */
	function saveconfig()
	{
		$mainframe =& JFactory::getApplication();
//		check the token
		JRequest::checkToken() or die( 'Invalid Token' );
		$model = $this->getModel('config');
		$error = $model->saveConfig();
		if ($error !== true)
			JError::raiseWarning( "1", $error );
		$link = JRoute::_( "index.php?option=com_jtg&task=config&controller=config",false);
		$this->setRedirect($link, JText::_('COM_JTG_CONFIG_SAVED'));
	}

}
