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

jimport('joomla.application.component.controller');

/**
 * jtg Component Controller
 */
class JtgController extends JControllerLegacy
{
	/**
	 * function_description
	 *
	 * @param   unknown_type  $cachable
	 * @param   unknown_type  $urlparams
	 *
	 * @return return_description
	 */
	function display ($cachable = false, $urlparams = false)
	{
		// Make sure we have a default view
		// if ( !JRequest::getCmd( 'view' ))
		if (! JFactory::getApplication()->input->get('view'))
		{
			JRequest::setVar('view', 'jtg');
		}

		// Update the hit count for the file
		if (JFactory::getApplication()->input->get('view') == 'files')
		{
			$model = $this->getModel('files');
			$model->hit();
		}

		parent::display();
	}

	/**
	 * function_description
	 *
	 * @return return_description
	 */
	function displayimg ()
	{
		$mainframe = JFactory::getApplication();

		// By default, just display an image
		$document = JFactory::getDocument();
		$doc = JDocument::getInstance('raw');

		// Swap the objects
		$document = $doc;
		$ok = null;
		$mainframe->triggerEvent('onCaptcha_Display', array($ok));

		if (! $ok)
		{
			echo "<br/>Error displaying Captcha<br/>";
		}
	}
}
