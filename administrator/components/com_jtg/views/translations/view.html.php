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

// Import Joomla! libraries
jimport( 'joomla.application.component.view');

/**
 *
 */
class JtgViewTranslations extends JViewLegacy
{
	function display($tpl = null) {
		$mainframe = JFactory::getApplication();
		$option = JFactory::getApplication()->input->get('option');

		$model =$this->getModel();
		$languages = $model->getLanguages();
		$this->languages = $languages;

		parent::display($tpl);
	}
}
