<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJO3SM and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
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
class JtgViewComments extends JView
{
	/**
	 *
	 * @param object $tpl
	 */
	function display($tpl = null) {
		$mainframe =& JFactory::getApplication(); $option = JRequest::getCmd('option');

		if($this->getLayout() == 'form'):
		$this->_displayForm($tpl);
		return;
		endif;

		$model =& $this->getModel();
		$rows =& $this->get( 'Data');
		$total =& $this->get( 'Total');
		$pagination = & $this->get( 'Pagination' );
		if (!isset($lists)) {$lists=false;}
		$this->lists = $lists;
		$this->rows = $rows;
		$this->pagination = $pagination;

		parent::display($tpl);
	}

	function _displayForm($tpl) {

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		$model = $this->getModel();
		$comment = $model->getComment($cid);
		$editor =& JFactory::getEditor();

		$this->comment = $comment;
		$this->editor = $editor;

		parent::display($tpl);
	}
}
