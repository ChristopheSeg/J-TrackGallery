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
jimport('joomla.application.component.model');
/**
 * Model Class Terrain
 */
class JtgModelTerrain extends JModelLegacy
{
	var $_data = null;

	var $_total = null;

	var $_pagination = null;

	function __construct() {
		parent::__construct();
		$mainframe = JFactory::getApplication(); // global _ $option;

		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $this->option.'.limitstart', 'limitstart', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = JFactory::getApplication()->input->get('limitstart',0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$array = JFactory::getApplication()->input->get('cid', array(0), 'array');
		$edit	= JFactory::getApplication()->input->get('edit',true);
		if($edit)
		$this->setId((int)$array[0]);
	}

	/**
	 *
	 * @return object
	 */
	function getData($tid=null)
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery($tid);
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
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
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 *
	 * @return int
	 */
	function getTotal() {

		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$query = $this->_buildQuery();
			$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
	}

	/**
	 *
	 * @global object $mainframe
	 * @return string
	 */

	function _buildQuery($terrain=null) {
		$mainframe = JFactory::getApplication();

		$orderby	= $this->_buildContentOrderBy();

		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__jtg_terrains"
		. $orderby;
		if ( $terrain !== null )
		$query .= " WHERE id=" . $terrain
		;

		return $query;
	}

	/**
	 *
	 * @global object $mainframe
	 * @global string $option
	 * @return string
	 */
	function _buildContentOrderBy()
	{
		return;
		$mainframe = JFactory::getApplication(); // global _ $option;

		$filter_order		= $mainframe->getUserStateFromRequest( $this->option.'filter_order',		'filter_order',		'title',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $this->option.'filter_order_Dir',	'filter_order_Dir',	'',		'word' );

		$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.' , title ';
		// Problems if sorted in "Files"-Menu and switched to "Terrain"
		// return $orderby;
	}

	/**
	 *
	 * @param   string  $id
	 */
	function setId($id)
	{
		// Set weblink id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}

	function save() {
		// get post data
		$row = JRequest::get('post' );
		$table = $this->getTable( 'jtg_terrain' );
		$table->bind( $row );

		if (!$table->store()) {
			JFactory::getApplication()->enqueueMessage($table->getError() , 'Warning');
			return false;
		}
		return TRUE;
	}

	/**
	 *
	 * @param   array  $cid
	 * @param int $publish
	 * @return boolean
	 */
	function publish($cid = array(), $publish = 1)
	{
		$user 	= JFactory::getUser();

		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__jtg_terrains'
			. ' SET published = '.(int) $publish
			. ' WHERE id IN ( '.$cids.' )'
			. ' AND ( checked_out = 0 OR ( checked_out = '.(int) $user->get('id').' ) )'
			;
			$this->_db->setQuery( $query );
			if (!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

	/**
	 *
	 * @param   array  $cid
	 * @return boolean
	 */
	function delete($cid = array())
	{

		$result = false;

		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );

			//delete from DB
			$query = 'DELETE FROM #__jtg_terrains'
			. ' WHERE id IN ( '.$cids.' )';
			$this->_db->setQuery( $query );
			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

}
