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
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class JtgModeljtg extends JModelLegacy
{
	/**
	 * Constructor
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 *
	 * @global <type> $mainframe
	 * @param <type> $id
	 * @return <type>
	 */
	function getFile($id)  {
		$mainframe =& JFactory::getApplication();

		$db =& JFactory::getDBO();

		$query = "SELECT * FROM #__jtg_files WHERE id='" .$id. "'";

		$db->setQuery($query);
		$result = $db->loadObject();
		if (!$result)
		return JTable::getInstance('jtg_files', 'table');
		return $result;
	}

	function getTracksData($order, $limit,$where="",$access=null) {
		if ( $where != "" ) $where = " AND ( " . $where . " )";
//		if ( $access !== null ) $where .= " AND a.access <= " . $access;
		$mainframe =& JFactory::getApplication();
		$db =& JFactory::getDBO();
		$query = "SELECT a.*, b.title AS cat FROM #__jtg_files AS a"
		. "\n LEFT JOIN #__jtg_cats AS b"
		. "\n ON a.catid=b.id WHERE a.published = 1" . $where
		. "\n" . $order
		. "\n" . $limit;
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		return $rows;
	}

	function getCatsData($sort=false) {

		$mainframe =& JFactory::getApplication();

		$db =& JFactory::getDBO();

		$query = "SELECT * FROM #__jtg_cats WHERE published = 1"
		. "\n ORDER BY title ASC";

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if ( $sort === false )
			return $rows;
		else
		{
			$nullcat = array(
				"id"			=> 0,
				"parent"		=> 0,
				"title"			=> "<label title=\"".JText::_('COM_JTG_CAT_NONE') . "\">-</label>",
				"description"	=> null,
				"image"			=> null,
				"ordering"		=> 0,
				"published"		=> 1,
				"checked_out"	=> 0
			);
			$nullcat = JArrayHelper::toObject($nullcat);
			$sortedrow = array();
			foreach ( $rows AS $cat )
				$sortedrow[$cat->id] = $cat;
			$sortedrow[0] = $nullcat;
//			ksort($sortedrow);
			return $sortedrow;
		}
	}

	function getTerrainData($sort=false) {

		$mainframe =& JFactory::getApplication();

		$db =& JFactory::getDBO();

		$query = "SELECT * FROM #__jtg_terrains"
		. "\n ORDER BY title ASC";

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if ( $sort === false )
			return $rows;
		else
		{
			$nullter = array(
				"id"			=> 0,
				"title"			=> "<label title=\"".JText::_('COM_JTG_TERRAIN_NONE') . "\">-</label>",
				"ordering"		=> 0,
				"published"		=> 1,
				"checked_out"	=> 0
			);
			$nullter = JArrayHelper::toObject($nullter);
			$sortedrow = array();
			foreach ( $rows AS $ter )
				$sortedrow[$ter->id] = $ter;
			$sortedrow[0] = $nullter;
//			ksort($sortedrow);
			return $sortedrow;
		}
	}

	function getVotesData() {
		$mainframe =& JFactory::getApplication();

		$db =& JFactory::getDBO();

		$query = "SELECT trackid AS id ,rating FROM #__jtg_votes"
		. "\n ORDER BY trackid ASC";

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}
}
