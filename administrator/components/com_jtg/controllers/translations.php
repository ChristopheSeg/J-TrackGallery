<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: translations.php,v 1.2 2011/04/22 07:36:33 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
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