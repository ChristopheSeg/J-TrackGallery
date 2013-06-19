<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: view.html.php,v 1.2 2011/04/22 07:36:33 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');

/**
 *
 */
class jtgViewTranslations extends JView {
	function display($tpl = null) {
		$mainframe =& JFactory::getApplication(); $option = JRequest::getCmd('option');

		$model =& $this->getModel();
		$languages = $model->getLanguages();
		$this->languages = $languages;
		
		parent::display($tpl);
	}
}
