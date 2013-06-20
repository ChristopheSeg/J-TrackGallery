<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooOSM and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Model Class Categorie
 */
class jtgModelCat extends JModel {

	function __construct()
	{
		parent::__construct();

		$array = JRequest::getVar('cid', array(0), '', 'array');
		$edit	= JRequest::getVar('edit',true);
		if($edit)
		$this->setId((int)$array[0]);
	}

	function saveCatImage() {
		JRequest::checkToken() or die( 'Invalid Token' );
		jimport('joomla.filesystem.file');
		$files =& JRequest::getVar('files', null, 'files', 'array');
		return $this->uploadCatImage($files);
	}

	/**
	 *
	 * @param int $id
	 */
	function setId($id)
	{
		// Set weblink id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 *
	 * @param string $direction
	 * @return boolean
	 */
	function move($direction)
	{
		$row =& $this->getTable('osm_cats');
		if (!$row->load($this->_id)) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		if (!$row->move( $direction )) {
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 *
	 * @param array $cid
	 * @param string $order
	 * @return boolean
	 */
	function saveorder($cid = array(), $order)
	{
		$row =& $this->getTable('osm_cats');
		$groupings = array();

		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );
			// track categories
			$groupings[] = $row->catid;

			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('id = '.(int) $group);
		}

		return true;
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

			$query = 'UPDATE #__jtg_cats'
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

	function deleteCatImage($files) {
		jimport('joomla.filesystem.file');
		$path = JPATH_SITE.DS."images".DS."jtg".DS."cats".DS;
		foreach($files as $file) {
			if (!JFile::delete($path.$file)) return false;
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
		jimport('joomla.filesystem.file');

		$result = false;

		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );
			// delete the images
			$query = "SELECT * FROM #__jtg_cats"
			. "\n WHERE id IN ( '.$cids.' )";

			$this->_db->setQuery($query);
			$rows = $this->_db->loadObjectList();

			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}

			foreach($rows as $row) {
				JFile::delete(JPATH_SITE.DS."images".DS."jtg".DS."cats".DS.$row->image);
			}

			//delete from DB
			$query = 'DELETE FROM #__jtg_cats'
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
	 * @global obejct $mainframe
	 * @return boolean
	 */
	function saveCat() {
		$mainframe =& JFactory::getApplication();

		// check the token
		JRequest::checkToken() or die( 'Invalid Token' );
		jimport('joomla.filesystem.file');

		$db =& JFactory::getDBO();

		$title =& JRequest::getVar( 'title' );
		if ( $title == "" )
		{
			JError::raiseWarning( 1, JText::_('COM_JTG_NO_TITLE'));
			return false;
		}
		$published =& JRequest::getInt( 'publish' );
		$desc =& JRequest::getVar( 'desc', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$parent =& JRequest::getInt('parent');
		$image =& JRequest::getVar( 'catpic' );
		
		$query = "INSERT INTO #__jtg_cats SET"
		. "\n parent_id='".$parent."',"
		. "\n title='" . $title . "',"
		. "\n image='" . $image . "',"
		. "\n description='" . $desc . "',"
		. "\n published='" . $published . "'"
		;

		$db->setQuery($query);
		$db->query();
		
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		return true;
	}

	function uploadCatImage($file) {
		if ($file['name'] != "") {
			$file['ext'] = JFile::getext($file['name']);
			$config = jtgHelper::getConfig();
			$allowedimages = $config->type;
			$allowedimages = explode(',',$allowedimages);
			if ( !in_array($file['ext'],$allowedimages) )
			{
				JError::raiseWarning( 1, JText::sprintf('COM_JTG_NOTALLOWED_FILETYPE',$file['ext']));
				return false;
			}
			$upload_dir = JPATH_SITE.DS."images".DS."jtg".DS."cats".DS;
			$filename = JFile::makeSafe(strtolower($file['name']));
				
			if (JFile::exists($upload_dir.$filename)) {
				JError::raiseWarning( 1, JText::_('COM_JTG_CATPIC_ALLREADYEXIST'));
				return false;
			} else {
				$upload = JFile::upload($file['tmp_name'], $upload_dir.$filename);
				if (!$upload)
				{
					return false;
				} else {
					return true;
				}
			}
		} else return true;
	}

	/**
	 *
	 * @global object $mainframe
	 * @return boolean
	 */
	function updateCat() {
		$mainframe =& JFactory::getApplication();

		// check the token
		JRequest::checkToken() or die( 'Invalid Token' );
		jimport('joomla.filesystem.file');

		$db =& JFactory::getDBO();

		$id =& JRequest::getInt('id');
		$file =& JRequest::getVar('image', null, 'files', 'array');
		$title =& JRequest::getVar( 'title' );
		$image =& JRequest::getVar( 'catpic' );
		if ( $title == "" )
		{
			JError::raiseWarning( 1, JText::_('COM_JTG_NO_TITLE'));
			return false;
		}
		$published =& JRequest::getInt( 'publish' );
		$desc =& JRequest::getVar( 'desc', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$parent =& JRequest::getInt('parent');

		$query = "UPDATE #__jtg_cats SET"
		. "\n parent_id='".$parent."',"
		. "\n title='" . $title . "',"
		. "\n image='" . $image . "',"
		. "\n description='" . $desc . "',"
		. "\n published='" . $published . "'"
		. "\n WHERE id='" . $id . "'";

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		return true;
	}

}
