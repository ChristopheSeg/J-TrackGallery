<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 * 
 * @package    Comjtg
 * @author     Christophe Seguinot <christophe@jtrackgallery.net>
 * @copyright  2013 J!Track Gallery, InJooosm and joomGPStracks teams/model
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');
/**
 * Model Class Categories
 */
class JtgModelCats extends JModelLegacy
{
	/**
	 * Category Images array
	 *
	 * @var array
	 */
	var $_pics = null;

	/**
	 * Category Data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * Category total
	 *
	 * @var integer
	 */
	var $_total = null;

	/**
	 * Pagination object
	 *
	 * @var object
	 */
	var $_pagination = null;
	/**
	 *
	 * @global object $mainframe
	 * @global string $option
	 */
	function __construct() {
		parent::__construct();
		$mainframe =& JFactory::getApplication(); // global _ $option;

		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $this->option.'.limitstart', 'limitstart', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		//		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$limitstart = JFactory::getApplication()->input->get('limitstart',0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 *
	 * @return object
	 */
	function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_data;
	}

	/**
	 *
	 * @return object
	 */
	function getPics()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_pics))
		{
			$folder = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'cats' . DIRECTORY_SEPARATOR;
			jimport('joomla.filesystem.folder');
			$files = JFolder::files($folder);
			$this->_pics = $files;
		}
		return $this->_pics;
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
	function getTotal()  {

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
	function _buildQuery()  {
		$mainframe =& JFactory::getApplication();
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__jtg_cats ORDER BY ordering";
		return $query;
	}

	/**
	 *
	 * @global object $mainframe
	 * @param int $id
	 * @return object
	 */
	function getCat($id)  {
		$mainframe =& JFactory::getApplication();

		$db =& JFactory::getDBO();

		$query = "SELECT * FROM #__jtg_cats"
		. "\n WHERE id='" . $id . "'";

		$db->setQuery($query);
		$result = $db->loadObject();

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		return $result;
	}

	function getParent($exclusion=null)  {
		$mainframe =& JFactory::getApplication();
		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__jtg_cats"
		. "\n WHERE published=1";
		if ( $exclusion !== null )
		$query .= " AND id != " . $exclusion;
		$query .= "\n ORDER BY title ASC";

		$db->setQuery($query);
		$result = $db->loadObjectList();
		$newresult = array();
		foreach ($result as $k => $v) {
			$newresult[$k] = $v;
			$newresult[$k]->name = JText::_($newresult[$k]->title);
		}
		return $result;
	}
}
