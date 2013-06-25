<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJO3SM and joomGPStracks teams
 * @package    com_jtg
 * @subpackage frontend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * jtg Component Controller
 */
class JtgController extends JController
{
	function display() {
		// Make sure we have a default view
		if( !JRequest::getCmd( 'view' )) {
			JRequest::setVar('view', 'jtg' );
		}

		//update the hit count for the file
		if(JRequest::getCmd('view') == 'files')
		{
			$model =& $this->getModel('files');
			$model->hit();
		}

		parent::display();
	}

	/**
	 * calls the captcha image
	 */
	function displayimg()
	{
		$mainframe =& JFactory::getApplication();
		// By default, just display an image
		$document = &JFactory::getDocument();
		$doc = &JDocument::getInstance('raw');
		// Swap the objects
		$document = $doc;
		$ok = null;
		$mainframe->triggerEvent('onCaptcha_Display', array($ok));
		if (!$ok) {
			echo "<br/>Error displaying Captcha<br/>";
		}
	}
}
