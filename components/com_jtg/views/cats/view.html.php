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

jimport('joomla.application.component.view');

/**
 * HTML View class for the jtg component
 */
class JtgViewCats extends JViewLegacy
{
	/**
	 * function_description
	 *
	 * @param   unknown_type  $tpl
	 *
	 * @return return_description
 */
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$this->lh = LayoutHelper::navigation();
		$this->footer = LayoutHelper::footer();
		$pathway = $mainframe->getPathway();
		$pathway->addItem(JText::_('COM_JTG_CATS'), '');
		$sitename = $mainframe->getCfg('sitename');
		$document = JFactory::getDocument();
		$document->setTitle(JText::_('COM_JTG_CATS') . " - " . $sitename);
		$model = $this->getModel();
		$this->cats = $model->getCats();

		parent::display($tpl);
	}
}
