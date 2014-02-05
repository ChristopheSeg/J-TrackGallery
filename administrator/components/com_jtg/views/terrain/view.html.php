<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooosm and joomGPStracks teams
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
class JtgViewTerrain extends JViewLegacy
{
	/**
	 *
	 * @global object $mainframe
	 * @global string $option
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
		$total	=& $this->get( 'Total');
		$pagination =& $this->get( 'Pagination' );
		if (!isset($lists)) {$lists=false;}
		$this->lists = $lists;
		$this->rows = $rows;
		$this->pagination = $pagination;

		parent::display($tpl);
	}

	function _displayForm($tpl) {

		$model = $this->getModel();
		$cid =& JRequest::getVar( 'cid', array(), 'post', 'array' );
		if ( count($cid) != 0 )
		{
			$id = $cid[0];
			$terrain = $model->getData($id);
			$terrain = $terrain[0];
			$published = $terrain->published;
		}
		else
		{
			$id=0;
			$terrain = $model->getData();
			$published = 1;
		}
		$lists['block'] 	= JHtml::_('select.booleanlist', 'published', 'class="inputbox" size="1"', $published );
		
		$this->id = $id;
		$this->lists = $lists;
		$this->terrain = $terrain;
		parent::display($tpl);
	}
}
