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

jimport('joomla.application.component.model');
/**
 * JtgModelCats class for the jtg component
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @since       0.8
 */

class JtgModelCats extends JModelLegacy
{
	/**
	 * function_description
	 *
	 * @return return_description
	 */
	function getCats()
	{
		$mainframe = JFactory::getApplication();
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
			$v->title = JText::_($v->title);
			$pt 	= $v->parent_id;
			$list 	= @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;
		}

		$list = JHtml::_('menu.treerecurse', 0, '', array(), $children);
		$list = array_slice($list, 0, $limit);

		return $list;
	}
}
