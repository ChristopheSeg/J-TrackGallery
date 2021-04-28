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
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @since       0.8
 */
class JtgController extends JControllerLegacy
{
	/**
	 * View method for JTG
	 *
	 * This function override joomla.application.component.controller
	 * View Cache not yet implemented in JTrackGallery
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return void
	 */
	public function display ($cachable = false, $urlparams = false)
	{
		// Make sure we have a default view
		$input = JFactory::getApplication()->input;
		if (! $input->get('view'))
		{
			$input->set('view', 'files');
			$input->set('layout', 'map');
		}

		// Update the hit count for the file
		if ($input->get('view') == 'files')
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
	public function displayimg ()
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
