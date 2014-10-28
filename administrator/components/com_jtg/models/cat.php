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

/**
 * Model Class Categorie
 */
class JtgModelCat extends JModelLegacy
{
	function __construct()
	{
		parent::__construct();

		$array = JFactory::getApplication()->input->get('cid', array(), 'array');
		$edit	= JFactory::getApplication()->input->get('edit',true);
		if($edit)
		$this->setId((int)$array[0]);
	}

	function saveCatImage() {
		JSession::checkToken() or die( 'Invalid Token' );
		jimport('joomla.filesystem.file');
		$files = JFactory::getApplication()->input->files->get('files');
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
	 * @param   string  $direction
	 * @return boolean
	 */
	function move($direction)
	{
		$row =$this->getTable('jtg_cats');
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
	 * @param   array  $cid
	 * @param   string  $order
	 * @return boolean
	 */
	function saveorder($cid = array(), $order)
	{
		$row =$this->getTable('jtg_cats');
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
		$path = JPATH_SITE . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "jtrackgallery" . DIRECTORY_SEPARATOR . "cats". DIRECTORY_SEPARATOR;
		foreach($files as $file) {
			if (!JFile::delete($path.$file)) return false;
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
				JFile::delete(JPATH_SITE . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "jtrackgallery" . DIRECTORY_SEPARATOR . "cats" . DIRECTORY_SEPARATOR . $row->image);
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
		$mainframe = JFactory::getApplication();

		// check the token
		JSession::checkToken() or die( 'Invalid Token' );
		jimport('joomla.filesystem.file');

		$db = JFactory::getDBO();

		$title = JFactory::getApplication()->input->get('title' );
		if ( $title == "" )
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_NO_TITLE'), 'Warning');
			return false;
		}
		$published = JRequest::getInt( 'publish' );
		$desc = JFactory::getApplication()->input->get('desc', '', 'raw');
		// allow for JTEXT in category description
		if ( (substr($desc,0,3)=='<p>') AND (substr($desc,-4,4)=='</p>') ) {
		    //remove enclosing <p> tags,try translating text, add <p> tags
		    $desc = substr($desc,3,-4);
		}
		$parent = JRequest::getInt('parent');
		$image = JFactory::getApplication()->input->get('catpic' );

		$query = "INSERT INTO #__jtg_cats SET"
		. "\n parent_id='" . $parent . "',"
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
		jimport('joomla.filesystem.file');
		if ($file['name'] != "") {
			$file['ext'] = JFile::getext($file['name']);
			$config = JtgHelper::getConfig();
			$allowedimages = $config->type;
			$allowedimages = explode(',',$allowedimages);
			if ( !in_array($file['ext'],$allowedimages) )
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_JTG_NOTALLOWED_FILETYPE',$file['ext']), 'Warning');
				return false;
			}
			$upload_dir = JPATH_SITE . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "jtrackgallery" . DIRECTORY_SEPARATOR . "cats". DIRECTORY_SEPARATOR;
			$filename = JFile::makeSafe(strtolower($file['name']));

			if (JFile::exists($upload_dir.$filename)) {
				JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_CATPIC_ALLREADYEXIST'), 'Warning');
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
		$mainframe = JFactory::getApplication();

		// check the token
		JSession::checkToken() or die( 'Invalid Token' );
		jimport('joomla.filesystem.file');

		$db = JFactory::getDBO();

		$id = JRequest::getInt('id');
		$file = JFactory::getApplication()->input->files->get('image');
		$title = JFactory::getApplication()->input->get('title' );
		$image = JFactory::getApplication()->input->get('catpic' );
		if ( $title == "" )
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_NO_TITLE'), 'Warning');
			return false;
		}
		$published = JRequest::getInt( 'publish' );
		$desc = JFactory::getApplication()->input->get('desc', '', 'raw');
		// allow for JTEXT in category description
		if ( (substr($desc,0,3)=='<p>') AND (substr($desc,-4,4)=='</p>') ) {
		    //remove enclosing <p> tags,try translating text, add <p> tags
		    $desc = substr($desc,3,-4);
		}
		$parent = JRequest::getInt('parent');

		$query = "UPDATE #__jtg_cats SET"
		. "\n parent_id='" . $parent . "',"
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
