<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: cats.php,v 1.1 2011/04/03 08:41:41 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage frontend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class jtgModelCats extends JModel {

	function getCats()
	{
		$mainframe =& JFactory::getApplication();
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__jtg_cats"
		. "\n WHERE published=1"
		. "\n ORDER BY ordering";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$limit = count($rows);
		$children = array();
		foreach ($rows as $v )
		{
			$v->name = $v->title;
			$pt 	= $v->parent_id;
			$list 	= @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children );
		$list = array_slice($list,0,$limit);
		return $list;
	}
}
