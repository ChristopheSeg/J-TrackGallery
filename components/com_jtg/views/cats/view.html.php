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

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the jtg component
 */
class JtgViewCats extends JViewLegacy
{
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$this->lh = layoutHelper::navigation();
		$this->footer = layoutHelper::footer();
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
