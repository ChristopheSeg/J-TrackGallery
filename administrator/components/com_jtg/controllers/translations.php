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

jimport( 'joomla.application.component.controller' );

class jtgControllertranslations extends jtgController {
	/**
	 *
	 */
	function display()
	{
		parent::display();
	}

	function saveLanguages() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$model = $this->getModel('translations');
		$success = $model->saveLanguage();

		// redirect to translations
		$link = JRoute::_( "index.php?option=com_jtg&task=translations&controller=translations",false);
		if ($success)
		{
			$this->setRedirect($link, JText::_('COM_JTG_TRANSLATIONS_SAVED'));
		} else {
			JError::raiseWarning( 1, JText::_('COM_JTG_TRANSLATIONS_NOTSAVED'));
			$this->setRedirect($link);
		}
	}
}