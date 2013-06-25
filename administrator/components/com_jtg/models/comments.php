<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJO3SM and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');

class JtgModelComments extends JModel
{
    function __construct() {
        parent::__construct();
        $mainframe =& JFactory::getApplication(); // global _ $option;

        // Get the pagination request variables
        $limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
        $limitstart	= $mainframe->getUserStateFromRequest( $this->option.'.limitstart', 'limitstart', 0, 'int' );

        // In case limit has been changed, adjust limitstart accordingly
        $limitstart = JRequest::getVar('limitstart',0);

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

        $query = "SELECT a.*, b.title AS track FROM #__jtg_comments AS a"
                . "\n LEFT JOIN #__jtg_files AS b ON b.id=a.tid"
                . "\n ORDER BY date DESC"
                ;

        return $query;
    }


    /**
     *
     * @param array $cid
     * @param int $publish
     * @return boolean
     */
	function publish($cid = array(), $publish = 1)
	{
		$user 	=& JFactory::getUser();

		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__jtg_comments'
				. ' SET published = '.(int) $publish
				. ' WHERE id IN ( '.$cids.' )'
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
     * @param array $cid
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
			$query = 'DELETE FROM #__jtg_comments'
				. ' WHERE id IN ( '.$cids.' )';
			$this->_db->setQuery( $query );
			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

        /**
         *
         * @global object $mainframe
         * @param array $cid
         * @return object
         */
        function getComment($cid)  {
            $mainframe =& JFactory::getApplication();

            $db =& JFactory::getDBO();
            $cids = implode(',', $cid);
            
            $query = "SELECT * FROM #__jtg_comments WHERE id IN( " . $cids . " )";
            $db->setQuery($query);
            $result = $db->loadObject();

            return $result;

        }

        /**
         *
         * @global object $mainframe
         * @return boolean
         */
        function saveComment()  {
            $mainframe =& JFactory::getApplication();

            $id     =& JRequest::getInt('id');
            $title  =& JRequest::getVar('title');
            $text   =& JRequest::getVar( 'text', '', 'post', 'string', JREQUEST_ALLOWRAW);

            $db =& JFactory::getDBO();

            $query = "UPDATE #__jtg_comments SET"
                    . "\n title='" . $title . "',"
                    . "\n text='" . $text . "'"
                    . "\n WHERE id='" . $id . "'"
                    ;
            $db->setQuery($query);
            $db->query();

            return true;
        }
}
