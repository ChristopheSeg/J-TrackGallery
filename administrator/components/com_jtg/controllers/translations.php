<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 * 
 * @package    Comjtg
 * @author     Christophe Seguinot <christophe@jtrackgallery.net>
 * @copyright  2013 J!Track Gallery, InJooosm and joomGPStracks teams
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JtgControllertranslations extends JtgController
{
	/**
	 *
	 */
	function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}

	function saveLanguages() {
		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );

		$model = $this->getModel('translations');
		$success = $model->saveLanguage();

		// redirect to translations
		$link = JRoute::_( "index.php?option=com_jtg&task=translations&controller=translations",false);
		if ($success)
		{
			$this->setRedirect($link, JText::_('COM_JTG_TRANSLATIONS_SAVED'));
		} else {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_TRANSLATIONS_NOTSAVED'), 'Warning');
			$this->setRedirect($link);
		}
	}
}