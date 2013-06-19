<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: view.html.php,v 1.1 2011/04/03 08:41:42 christianknorr Exp $
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
class jtgViewComments extends JView {
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
