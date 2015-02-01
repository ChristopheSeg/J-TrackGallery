<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @package    Comjtg
 * @author     Christophe Seguinot <christophe@jtrackgallery.net>
 * @copyright  2013 J!Track Gallery, InJooosm and joomGPStracks teams
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class JtgModelMaps extends JModelLegacy
{
	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;

	/**
	 * Category total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 *
	 * @param   string  $direction
	 * @return boolean
	 */
	function move($direction)
	{
		$row = $this->getTable('jtg_maps');

		if (!$row->load($this->_id))
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		if (!$row->move($direction))
		{
			$this->setError($this->_db->getErrorMsg());

			return false;
		}

		return true;
	}

	/**
	 *
	 * @global array $mainframe
	 * @global string $option
	 */
	function __construct()
	{
		parent::__construct();
		$mainframe = JFactory::getApplication();

		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart	= $mainframe->getUserStateFromRequest($this->option . '.limitstart', 'limitstart', 0, 'int');

		// In case limit has been changed, adjust limitstart accordingly
		// $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$array = JFactory::getApplication()->input->get('cid', array(0), 'array');
		$edit	= JFactory::getApplication()->input->get('edit', true);

		if ($edit)
		{
			$this->setId((int) $array[0]);
		}
	}

	/**
	 *
	 * @global array $mainframe
	 * @return string
	 */
	function _buildQuery()
	{
		$mainframe = JFactory::getApplication();
		$orderby = $this->_buildContentOrderBy();
		$query = "SELECT * FROM #__jtg_maps"
		. $where
		. $orderby;

		return $query;
	}

	/**
	 *
	 * @param   array  $cid
	 * @return boolean
	 */
	function delete($cid = array())
	{
		if (count($cid))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode(',', $cid);
			$query = 'DELETE FROM #__jtg_maps WHERE id IN ( ' . $cids . ' )';
			$this->_db->setQuery($query);

			if (!$this->_db->execute())
			{
				($this->_db->getErrorMsg());

				return false;
			}
		}
		return true;
	}


	/**
	 *
	 * @global object $mainframe
	 * @param   string  $id
	 * @return object
	 */
	function getMap($id)
	{
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__jtg_maps"
		. "\n WHERE id='" . $id . "'";
		$db->setQuery($query);
		$result = $db->loadObject();

		if ($db->getErrorNum())
		{
			echo $db->stderr();

			return false;
		}
		return $result;
	}

	/**
	 *
	 * @global object $mainframe
	 * @global string $option
	 * @return string
	 */
	function _buildContentOrderBy()
	{
		$mainframe = JFactory::getApplication();

		$filter_order		= $mainframe->getUserStateFromRequest(
				$this->option . 'filter_order', 'filter_order', 'ordering', 'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest(
				$this->option . 'filter_order_Dir', 'filter_order_Dir', '', 'word');

		if ($filter_order == 'ordering')
		{
			$orderby 	= ' ORDER BY ordering ' . $filter_order_Dir;
		}
		else
		{
			$orderby 	= ' ORDER BY ' . $filter_order . ' ' . $filter_order_Dir . ' , id ';
		}

		return $orderby;
	}


	/**
	 *
	 * @global object $mainframe
	 * @global string $option
	 * @return string
	 */
	function _buildContentWhere()
	{
		$mainframe = JFactory::getApplication();

		$search = JFactory::getApplication()->input->get('search');
		$where = array();
		$db = JFactory::getDBO();

		if ($search)
		{
			$where[] = 'LOWER(a.name) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false);
			$where[] = 'LOWER(b.name) LIKE ' . $db->Quote('%' . $db->getEscaped($search, true) . '%', false);
		}

		$where = (count($where) ? ' WHERE ' . implode(' OR ', $where) : '');

		return $where;
	}

	/**
	 *
	 * @return string
	 */
	function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$maps = $this->getMaps();
			$this->_total = count($maps);
		}

		return $this->_total;
	}

	/**
	 * @return Object
	 */
	function getMaps($order=false)
	{
		$db = JFactory::getDBO();
		$sql = 'Select * from #__jtg_maps ';

		if ($order)
		{
			$sql .= 'ORDER BY ' . $order;
		}
		else
		{
			$sql .= 'ORDER BY ordering asc';
		}

		$db->setQuery($sql);
		$maps = $db->loadObjectlist();

		return $maps;
	}

	/**
	 *
	 * @return array $pagination
	 */
	function getPagination()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	/**
	 *
	 * @param   array  $cid
	 * @param   string  $publish
	 * @return bool
	 */
	function publish($cid = array(), $publish = 1)
	{
		$user = JFactory::getUser();

		if (count($cid))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode(',', $cid);
			$query = 'UPDATE #__jtg_maps'
			. ' SET published = ' . (int) $publish
			. ' WHERE id IN ( ' . $cids . ' )';
			$this->_db->setQuery($query);

			if (!$this->_db->execute())
			{
				$this->setError($this->_db->getErrorMsg());

				return false;
			}
		}
		return true;
	}

	function saveMap()
	{
		$mainframe = JFactory::getApplication();
		jimport('joomla.filesystem.file');

		$db = JFactory::getDBO();

		// Get the post data
		$publish = JRequest::getInt('publish');
		$order = JRequest::getInt('order');
		$name = JFactory::getApplication()->input->get('name');
		$name = htmlentities($name);
		$param = JFactory::getApplication()->input->get('param', '', 'raw');
		$checked_out = JFactory::getApplication()->input->get('checked_out');
		$param = str_replace("'", '"', htmlentities($param));

		if ( ( $name == "" ) OR ( $param == "" ) )
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_MAP_NOT_SAVED'), 'Warning');

			return false;
		}

		$script = JFactory::getApplication()->input->get('script', '', 'raw');

		$script = htmlentities($script);
		$code = JFactory::getApplication()->input->get('code', '', 'raw');
		$code = htmlentities($code);
		$query = "INSERT INTO #__jtg_maps SET"
		. "\n name='" . $name . "',"
		. "\n ordering='" . $order . "',"
		. "\n published='" . $publish . "',"
		. "\n param='" . $param . "',"
		. "\n checked_out='" . $checked_out . "',"
		. "\n code='" . $code . "'";

		if ($script)
		{
			$query .= ",\n script='" . $script . "'";
		}

		$db->setQuery($query);
		$db->execute();

		if ($db->getErrorNum())
		{
			die($db->_errorMsg);
		}

		return true;
	}

	/**
	 *
	 * @param   string  $id
	 */
	function setId($id)
	{
		// Set weblink id and wipe data
		$this->_id = $id;
		$this->_data = null;
	}

	/**
	 *
	 */
	function updateMap()
	{
		$db = JFactory::getDBO();
		// Get the post data
		$publish = JRequest::getInt('publish');
		$order = JRequest::getInt('order');
		$id = JRequest::getInt('id');
		$name = JFactory::getApplication()->input->get('name');
		$name = htmlentities($name);
		$param = JFactory::getApplication()->input->get('param', '', 'raw');

		// TODO was this usefull???
		$param = str_replace("'", '"', htmlentities($param));
		$checked_out = JFactory::getApplication()->input->get('checked_out');
		$code = JFactory::getApplication()->input->get('code', '', 'raw');
		$code = htmlentities($code);

		if ( ( $name == "" ) OR ( $param == "" ) )
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_MAP_NOT_SAVED'), 'Warning');

			return false;
		}

		$script = JFactory::getApplication()->input->get('script', '', 'raw');
		$script = htmlentities($script);
		$query = "UPDATE #__jtg_maps SET"
		. "\n name='" . $name . "',"
		. "\n ordering='" . $order . "',"
		. "\n published='" . $publish . "',"
		. "\n param='" . $param . "',"
		. "\n script='" . $script . "',"
		. "\n checked_out='" . $checked_out . "',"
		. "\n code='" . $code . "'"
		. "\n WHERE id IN (" . $id . ")";

		$db->setQuery($query);
		$db->execute();

		if ($db->getErrorNum())
		{
			die($db->_errorMsg);
		}

		return true;
	}
}
