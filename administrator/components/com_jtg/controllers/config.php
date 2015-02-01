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
	function display($cachable = false, $urlparams = false)
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
		$mainframe = JFactory::getApplication();
		// 	check the token
		JSession::checkToken() or die( 'Invalid Token' );
		$model = $this->getModel('config');
		$error = $model->saveConfig();
		if ($error !== true)
		{
			JFactory::getApplication()->enqueueMessage($error, 'Warning');
		}

		$link = JRoute::_( "index.php?option=com_jtg&task=config&controller=config",false);
		$this->setRedirect($link, JText::_('COM_JTG_CONFIG_SAVED'));
	}
}
