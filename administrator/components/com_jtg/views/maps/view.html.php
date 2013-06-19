<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: view.html.php,v 1.1 2011/04/03 08:41:57 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
 *
 */

{
//jimport('joomla.filesystem.file');
//$path = ("components/com_jtg/install.jtg.php");
//echo "<html><body>";
//require $path;
//echo "</body></html>";
//die();
}


// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');

/**
 *
 */
class jtgViewMaps extends JView {
	//$id = false;
	/**
	*
	* @param object $tpl
	*/
	function display($tpl = null) {
		$mainframe =& JFactory::getApplication(); $option = JRequest::getCmd('option');
		//		$order = JRequest::getVar('order',true);

		if($this->getLayout() == 'form'):
		$this->_displayForm($tpl);
		// return;
		endif;
		jimport('joomla.filesystem.file');
		$config =& jtgHelper::getConfig();
		$model = $this->getModel();
		$total = $model->getTotal();
		$maps = $model->getMaps();
		//	$tmpl = $model->getTemplates();

		//	$lists['serviceprovider']	=	$this->createCheckbox("serviceprovider","google","Google")."<br />\n".
		//									$this->createCheckbox("serviceprovider","osm","OpenStreetMap",true);
		$lists['block']	= JHTML::_('select.booleanlist', 'publish', 'class="inputbox" size="1"', 1 );
//		$order = JRequest::getVar( 'order', 'order', 'post', 'string' );

		$filter_order		= $mainframe->getUserStateFromRequest( $option."filter_order",
 	'filter_order',
 	'ordering',
 	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option."filter_order_Dir",
 	'filter_order_Dir',
 	'',
 	'word' );
		$lists['order']		= $filter_order;
		$lists['order_Dir']	= $filter_order_Dir;

		$search = $mainframe->getUserStateFromRequest( $option."search",
		'search',
		'',
		'string' );
		$search = JString::strtolower( $search );
		$lists['search'] = $search;
		$state = $search;

		$pagination = & $this->get( 'Pagination' );
		$this->pagination = $pagination;
		$this->state = $state;
		$this->config = $config;
		$this->total = $total;
		$this->maps = $maps;
		$this->lists = $lists;
		parent::display($tpl);
	}
	/**
	 * Gibt den Klicklink zurück mit dem man Spuren direkt editieren kann
	 * @param Map
	 * @param count
	 * @return string
	 */
	function buildEditKlicks($map,$count){
		return "<a href=\"javascript:void(0);\" onclick=\"javascript:return listItemTask('cb".$count.
			"','editmap')\">".$map."</a>";
	}

	function _displayForm($tpl) {
		$model = $this->getModel();
		$id = $this->_models["maps"]->_id;
		$map = $model->getMap($id);
		if($map){
			$published = $map->published;
			$this->map = $map;
		}else{
			$published = 0;
		}
		$list['published']	= JHTML::_('select.booleanlist', 'publish', 'class="inputbox" size="1"', $published );
		$this->list = $list;
		// parent::display($tpl);
	}
}
