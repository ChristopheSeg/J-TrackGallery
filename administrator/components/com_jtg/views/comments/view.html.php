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
class JtgViewComments extends JViewLegacy
{
	/**
	 *
	 * @param object $tpl
	 */
	function display($tpl = null) {
		$mainframe = JFactory::getApplication();
		$option = JFactory::getApplication()->input->get('option');

		if ($this->getLayout() == 'form'):
		$this->_displayForm($tpl);
		return;
		endif;

		$model =$this->getModel();
		$rows =$this->get('Data');
		$total =$this->get('Total');
		$pagination = $this->get('Pagination' );
		if (!isset($lists)) {
			$lists=false;
		}
		$this->lists = $lists;
		$this->rows = $rows;
		$this->pagination = $pagination;

		parent::display($tpl);
	}

	function _displayForm($tpl) {

		$cid = JFactory::getApplication()->input->get('cid', array(), 'array' );
		JArrayHelper::toInteger($cid);

		$model = $this->getModel();
		$comment = $model->getComment($cid);
		$editor = JFactory::getEditor();

		$this->comment = $comment;
		$this->editor = $editor;

		parent::display($tpl);
	}
}
