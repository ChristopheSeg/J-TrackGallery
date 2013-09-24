<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooosm and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 */


// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
/**
 * Controller Class Configuration
 */
class JtgControllerConfig extends JtgController
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
	 * @uses JtgModelConfigat::saveConfig
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
