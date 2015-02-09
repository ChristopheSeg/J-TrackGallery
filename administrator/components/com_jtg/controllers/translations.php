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

jimport('joomla.application.component.controller');
/**
 * JtgControllertranslations class for the jtg component
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @since       0.8
 */

class JtgControllertranslations extends JtgController
{
	/**
	 * function_description
	 *
	 * @param   boolean  $cachable   true if display is cachable
	 * @param   string   $urlparams  URL
	 *
	 * @return return_description
	 */
	public function display($cachable = false, $urlparams = false)
	{
		parent::display();
	}

	/**
	 * function_description
	 *
	 * @return return_description
	 */
	function saveLanguages()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$model = $this->getModel('translations');
		$success = $model->saveLanguage();

		// Redirect to translations
		$link = JRoute::_("index.php?option=com_jtg&task=translations&controller=translations", false);

		if ($success)
		{
			$this->setRedirect($link, JText::_('COM_JTG_TRANSLATIONS_SAVED'));
		}
		else
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_TRANSLATIONS_NOTSAVED'), 'Warning');
			$this->setRedirect($link);
		}
	}
}
