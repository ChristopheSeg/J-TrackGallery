<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: controller.php,v 1.1 2011/04/03 08:41:37 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage frontend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * jtg Component Controller
 */
class jtgController extends JController {
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
