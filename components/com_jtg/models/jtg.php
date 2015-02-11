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
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
/**
 * JtgModeljtg class for the jtg component
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @since       0.8
 */

class JtgModeljtg extends JModelLegacy
{
	/**
	 * Constructor
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * function_description
	 *
	 * @param   integer  $id  file id
	 *
	 * @return return_description
	 */
	function getFile($id)
	{
		$mainframe = JFactory::getApplication();

		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__jtg_files WHERE id='" . $id . "'";

		$db->setQuery($query);
		$result = $db->loadObject();

		if (!$result)
		{
			return JTable::getInstance('jtg_files', 'table');
		}

		return $result;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $order   param_description
	 * @param   unknown_type  $limit   param_description
	 * @param   unknown_type  $where   param_description
	 * @param   unknown_type  $access  param_description
	 *
	 * @return return_description
	 */
	static public function getTracksData($order, $limit, $where = "",$access = null)
	{
		if ( $where != "" )
		{
			$where = " AND ( " . $where . " )";
		}

		// 	if ( $access !== null ) $where .= " AND a.access <= " . $access;
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		$query = "SELECT a.*, b.title AS cat FROM #__jtg_files AS a"
		. "\n LEFT JOIN #__jtg_cats AS b"
		. "\n ON a.catid=b.id WHERE a.published = 1" . $where
		. "\n" . $order
		. "\n" . $limit;
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}

	/**
	 * sort categories
	 *
	 * @param   boolean  $sort  parameter array
	 *
	 * @return sorted rows
	 */
	static public function getCatsData($sort=false)
	{
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__jtg_cats WHERE published = 1"
		. "\n ORDER BY title ASC";

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if ( $sort === false )
		{
			return $rows;
		}
		else
		{
			$nullcat = array(
					"id"			=> 0,
					"parent"		=> 0,
					"title"			=> "<label title=\"" . JText::_('COM_JTG_CAT_NONE') . "\">-</label>",
					"description"	=> null,
					"image"			=> null,
					"ordering"		=> 0,
					"published"		=> 1,
					"checked_out"	=> 0
			);
			$nullcat = JArrayHelper::toObject($nullcat);
			$sortedrow = array();

			foreach ( $rows AS $cat )
			{
				$sortedrow[$cat->id] = $cat;
			}

			$sortedrow[0] = $nullcat;

			return $sortedrow;
		}
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $sort  param_description
	 *
	 * @return return_description
	 */
	static public function getTerrainData($sort=false)
	{
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__jtg_terrains"
		. "\n ORDER BY title ASC";

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		if ( $sort === false )
		{
			return $rows;
		}
		else
		{
			$nullter = array(
					"id"			=> 0,
					"title"			=> "<label title=\"" . JText::_('COM_JTG_TERRAIN_NONE') . "\">-</label>",
					"ordering"		=> 0,
					"published"		=> 1,
					"checked_out"	=> 0
			);
			$nullter = JArrayHelper::toObject($nullter);
			$sortedrow = array();

			foreach ( $rows AS $ter )
			{
				$sortedrow[$ter->id] = $ter;
			}

			$sortedrow[0] = $nullter;

			return $sortedrow;
		}
	}

	/**
	 * function_description
	 *
	 * @return return_description
	 */
	static public function getVotesData()
	{
		$mainframe = JFactory::getApplication();

		$db = JFactory::getDBO();

		$query = "SELECT trackid AS id ,rating FROM #__jtg_votes"
		. "\n ORDER BY trackid ASC";

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		return $rows;
	}
}
