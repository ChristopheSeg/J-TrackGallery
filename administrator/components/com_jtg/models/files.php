<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooosm and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
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
 * Model Class Files
 */
class JtgModelFiles extends JModel
{
	/**
	 * Category data array
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

	function updateGeneratedValues() {
		//$mainframe =& JFactory::getApplication();

		// get the post data
		$id =& JRequest::getVar('id');
		$file =& JRequest::getVar('file');
		$cfg = JtgHelper::getConfig();
		jimport('joomla.filesystem.file');
		require_once(".." . DS . "components" . DS . "com_jtg" . DS . "helpers" . DS . "gpsClass.php");
		$gps = new gpsClass();
		$file = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'uploaded_tracks' . DS . $file;
		$gps->gpsFile = $file;

		$isTrack = $gps->isTrack();
		if ( $isTrack === false )
		$isTrack = 0;
		else
		$isTrack = 1;

		$isWaypoint = (int)$gps->isWaypoint();

		//$isRoute = $gps->isRoute();
		$isRoute = (int)0;

		if ( $isWaypoint == 1 )
		$isCache = (int)$gps->isCache();
		else
		$isCache = 0;

		if ( $isTrack == 1 )
		{
			$coords = $gps->getAllTracksCoords($file);
			$distance = $gps->getDistance($coords);
			$ele = $gps->getElevation($coords);
		}
		else
		{
			$distance = 0;
			$ele = array(null,null);
		}

		$start = $gps->getStartCoordinates();
		if ( $start === false )
		{
			return false;
		}

		$db =& JFactory::getDBO();
		// count votings
		$query = "SELECT COUNT(*) FROM #__jtg_votes"
		. "\n WHERE trackid='" .$id. "'";

		$db->setQuery($query);
		$count = (int)$db->loadResult();

		// sum rating
		$query = "SELECT SUM(rating) FROM #__jtg_votes"
		. "\n WHERE trackid='" .$id. "'";
		$db->setQuery($query);
		$givenvotes = (int)$db->loadResult();

		if ( $count == 0 )
		$vote = 0;
		else
		$vote = (float) ( round( ( $givenvotes / $count ), 3 ) );

		$query = "UPDATE #__jtg_files SET"
		. "\n istrack='" . $isTrack . "',"
		. "\n iswp='" . $isWaypoint . "',"
		. "\n isroute='" . $isRoute . "',"
		. "\n iscache='" . $isCache . "',"
		. "\n start_n='" . $start[1] . "',"
		. "\n start_e='" . $start[0] . "',"
		. "\n distance='" . $distance . "',"
		. "\n ele_asc='" . $ele[0] . "',"
		. "\n ele_desc='" . $ele[1] . "',"
		. "\n vote='" . $vote . "'"
		. "\n WHERE id='" . $id . "'"
		;

		$db =& JFactory::getDBO();
		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return "database not saved";
		} else {
			return true;
		}
	}

	function uploadfiles($files, $dest, $types=true) {
		jimport('joomla.filesystem.file');
		if(count($files['name']) > 1)
		{
			foreach($files['name'] as $key => $value)
			{
				if($value != "")
				{
					$filename = JFile::makesafe($value);
					$ext = JFile::getExt($files['name'][$key]);
					if ( ( $types === true ) OR (in_array(strtolower($ext),$types)) )
					{
						if ( JtgHelper::uploadfile(array(
							"tmp_name" => $files['tmp_name'][$key],
							"name" => $files['name'][$key],
							"type" => $files['type'][$key],
							"error" => $files['error'][$key],
							"size" => $files['size'][$key],
						), $dest) === false ) return false;
					}
				}
			}
		}
		return true;
	}
	function TODOREMOVEDEPRECATEDimageupload($images,$id,$path) {
		$cfg = JtgHelper::getConfig();
		$types = explode(',',$cfg->type); // jpg,png,gif f.e.
		if(count($images) > 0 )  {
			$img_dir = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'track_' . $id;
			JFolder::create($img_dir,0777);
			foreach($images['name'] as $key => $value)  {
				$ext = explode('.',$images['name'][$key]);
				$ext = count($ext)-1;
				$filename = str_replace("." . $ext,"",$images['name'][$key]);
				if(in_array(strtolower($ext), $types)) {
					JtgHelper::createimageandthumbs($images['tmp_name'][$key], $ext, $img_dir,$images['name'][$key]);
				}
			}
		}
	}

	/**
	 *
	 * @global array $mainframe
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
		$limitstart = JRequest::getVar('limitstart',0);

		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);

		$array = JRequest::getVar('cid', array(0), '', 'array');
		$edit	= JRequest::getVar('edit',true);
		if($edit)
		$this->setId((int)$array[0]);
	}

	/**
	 *
	 * @return array
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
	 * @return string
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
	 * @global array $mainframe
	 * @return string
	 */
	function _fetchJPTfiles()  {
		$mainframe =& JFactory::getApplication();
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__gps_tracks";
		if($db->setQuery($query)) return false;
		$rows = $db->loadAssocList();
		return $rows;
	}

	/**
	 *
	 * @global array $mainframe
	 * @return string
	 */
	function _buildQuery()  {
		$mainframe =& JFactory::getApplication();

		$orderby	= $this->_buildContentOrderBy();
		$where      = $this->_buildContentWhere();

		//        $db =& JFactory::getDBO();

		$query = "SELECT a.*, b.title AS cat FROM"
		. "\n #__jtg_files AS a"
		. "\n LEFT JOIN #__jtg_cats AS b"
		. "\n ON a.catid=b.id"
		. $where
		. $orderby
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
		$mainframe =& JFactory::getApplication(); // global _ $option;

		$filter_order		= $mainframe->getUserStateFromRequest
		( $this->option.'filter_order','filter_order','ordering','cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest
		( $this->option.'filter_order_Dir','filter_order_Dir','','word' );

		if ($filter_order == 'ordering'){
			$orderby 	= ' ORDER BY ordering '.$filter_order_Dir;
		} else {
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.' , id ';
		}

		return $orderby;
	}

	/**
	 *
	 * @global object $mainframe
	 * @global string $option
	 * @return string
	 */
	function _buildContentWhere()  {

		$mainframe =& JFactory::getApplication(); // global _ $option;

		$search =& JRequest::getVar('search');
		$where = array();
		$db =& JFactory::getDBO();

		if($search)  {
			$where[] = 'LOWER(a.title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = 'LOWER(b.title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = 'LOWER(a.date) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
		}
		$where = ( count( $where ) ? ' WHERE ' . implode( ' OR ', $where ) : '' );

		return $where;
	}

	/**
	 *
	 * @global object $mainframe
	 * @param string $id
	 * @return object
	 */
	function getFile($id)  {
		$mainframe =& JFactory::getApplication();
		$db =& JFactory::getDBO();
		$query = "SELECT * FROM #__jtg_files"
		. "\n WHERE id='" . $id . "'";
		$db->setQuery($query);
		$result = $db->loadObject();
		//	if(!$result) return false;
		if ($db->getErrorNum()) 
		{
			echo $db->stderr();
			return false;
		}
		if (!$result)
		{
		    return JTable::getInstance('jtg_files', 'table');
		}
		return $result;
	}

	/**
	 *
	 * @param string $id
	 */
	function setId($id)
	{
		// Set weblink id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 *
	 * @param array $cid
	 * @param string $publish
	 * @return bool
	 */
	function publish($cid = array(), $publish = 1)
	{
		$user 	=& JFactory::getUser();

		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__jtg_files'
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
	 * @param array $cid
	 * @param string $hide
	 * @return bool
	 */
	function showhide($cid = array(), $hide = 0)
	{
		$user 	=& JFactory::getUser();

		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__jtg_files'
			. ' SET hidden = '.(int) $hide
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
	 * @param array $cid
	 * @param string $access
	 * @return bool
	 */
	function access($cid = array(), $access = 1)
	{
		$user 	=& JFactory::getUser();

		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );

			$query = 'UPDATE #__jtg_files'
			. ' SET access = '.(int) $access
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
			$query = 'SELECT * FROM #__jtg_files WHERE id IN ( '.$cids.' )';
			$this->_db->setQuery($query);
			$rows = $this->_db->loadObjectList();

			if(!$this->_db->query()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			foreach($rows as $row)  {
				// folder and Pictures within delete
				$folder = JPATH_SITE . DS . "images" . DS . "jtrackgallery" . DS . $row->id;
				if (JFolder::exists($folder))
				JFolder::delete($folder);
				// File (gpx?) delete
				$filename = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'uploaded_tracks' . DS . $row->file;
				if (JFile::exists($filename))
				JFile::delete($filename);
			}
			// delete from DB
			$query = 'DELETE FROM #__jtg_files WHERE id IN ( '.$cids.' )';
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
	 * @param array $cid
	 * @return boolean
	 */
	function deleteFromImport($found)
	{
		$cid = JRequest::getVar( 'import_0' );
		jimport('joomla.filesystem.file');
		$result = false;
		for ($i = 0; $i <= $found; $i++)
		{
			$file = JRequest::getVar( 'import_'.$i );
			if ( $file !== null )
			{
				if (JFile::exists($file))
				{
					if (!JFile::delete($file))
					{
						JError::raiseError(500, "<p class=\"type\">".JText::_( 'COM_JTG_ERROR_FILE_NOT_ERASEABLE' ) . "</p><p>" . $file . "</p>" );
						return false;
					}
				}
			}
		}
		return true;
	}

	/**
	 *
	 * @global object $mainframe
	 * @return array
	 */
	function getLevel($selected=0)  {
		$return = "\n";
		$cfg = JtgHelper::getConfig();
		$levels = explode("\n",$cfg->level);
		array_unshift($levels,'dummy');
		$i = 0;
		foreach($levels AS $level){
			if ( trim($level) != "" ) {
				$return .= ("<input type=\"radio\" name=\"level\" id=\"level" . $i . "\" value=\"" . $i . "\"");
				if ( $i == $selected )
				$return .= (" checked=\"checked\"");
				$return .= (" /><label for=\"level" . $i . "\">");
				if ( $i == 0 )
				$return .= JText::_('COM_JTG_SELECT');
				else
				$return .= $i . " - " . JText::_(trim($level));
				$return .= ("</label><br />\n");
				$i++;
			}
		}
		return $return;
	}
	/**
	 *
	 * @global object $mainframe
	 * @return array
	 */
	function getCats($nosubcats=false,$stdtext='COM_JTG_SELECT',$stdid=0) {
		$mainframe =& JFactory::getApplication();
		$db =& JFactory::getDBO();

		// 	$query = "SELECT * FROM #__jtg_cats ORDER BY title ASC";
		$query = "SELECT * FROM #__jtg_cats WHERE published=1 ORDER BY ordering,id ASC";
		// 	$query = "SELECT * FROM #__jtg_cats";
		// 	$query = "SELECT * FROM #__jtg_cats ORDER BY parent,id ASC";

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$children = array();
		foreach ($rows as $v ) {
			if ( ( ($nosubcats) AND ($v->parent_id == 0) ) OR (!$nosubcats) )
			{
				$v->name = JText::_($v->title);
				$pt	= $v->parent_id;
				$list	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
		$levellimit = 50;

		$rows = JHTML::_('menu.treerecurse', 0, '', array(), $children, max( 0, $levellimit-1 ) );
		$nullcat = array(
		"id"=>$stdid,
		"parent"=>"0",
		"title"=>JText::_($stdtext),
		"description"=>"",
		"image"=>"",
		"ordering"=>"0",
		"published"=>"0",
		"checked_out"=>"0",
		"name"=>JText::_($stdtext),
		"treename"=>JText::_($stdtext),
		"children"=>""
		);
		$nullcat = JArrayHelper::toObject($nullcat);
		array_unshift($rows,$nullcat);
		return $rows;
		foreach($rows AS $value){
			$return[] = $value;
		}
		return $return;
	}

	/**
	 *
	 * @global object $mainframe
	 * @return array
	 */
	function getTerrain($select="*", $nullter = false, $where = null )  {
		//		$mainframe =& JFactory::getApplication();

		$db =& JFactory::getDBO();
		$rows=null;
		if ($where!=="WHERE id = ")
		    {
		    $query = "SELECT " . $select . " FROM #__jtg_terrains " . $where . " ORDER BY ordering,title ASC";

		    $db->setQuery($query);
		    $rows = $db->loadObjectList();		    
		}



		$terrain = array();
		if($rows)
		{
			foreach ($rows as $v) {
				$v->title = JText::_($v->title);
				$terrain[] = $v;
			}
		}
		if ($nullter !== false)
		{
			$nullter = new stdClass();
			$nullter->title = JText::_('COM_JTG_SELECT');
			$nullter->id = null;
			array_unshift($terrain,$nullter);
		}
		return $terrain;
	}

	/**
	 *
	 * @global object $mainframe
	 * @return array
	 */
	function getAccess($id)  {
		$mainframe =& JFactory::getApplication();

		$db =& JFactory::getDBO();

		$query = "SELECT access FROM #__jtg_files WHERE id='" . $id . "'";

		$db->setQuery($query);
		$row = $db->loadResult();
		return $row;
	}

	function saveFiles() {
		$mainframe =& JFactory::getApplication();
		jimport('joomla.filesystem.file');
		require_once(".." . DS . "components" . DS . "com_jtg" . DS . "helpers" . DS . "gpsClass.php");
		$fileokay = true;
		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();
		$targetdir = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'uploaded_tracks'.DS;
		$found =& JRequest::getInt('found');
		for($i=0;$i<$found;$i++) {
			$existingfiles = JFolder::files($targetdir);
			$import =& JRequest::getVar('import_'.$i);
			if ( $import !== null ) {
				$catid =& JRequest::getVar('catid_'.$i);
				if ( $catid )

				$catid = implode(",",$catid);
				$level =& JRequest::getInt('level_'.$i);
				$title =& JRequest::getVar('title_'.$i);
				$terrain =& JRequest::getVar('terrain_'.$i);
				if ($terrain)
				$terrain = implode(',', $terrain);
				else
				$terrain = "";
				$desc =& JRequest::getVar( 'desc_'.$i, '', 'post', 'string', JREQUEST_ALLOWRAW);
				$desc = $this->parsedesc($desc);
				$file =& JRequest::getVar('file_'.$i);
				$hidden =& JRequest::getVar('hidden_'.$i);
				$file_tmp = explode(DS,$file);
				$file_tmp = str_replace(' ','_',strtolower($file_tmp[(count($file_tmp)-1)]));
				$file_tmp = explode('.',$file_tmp);
				$extension = $file_tmp[(count($file_tmp)-1)];
				unset($file_tmp[(count($file_tmp)-1)]);
				$file_tmp = trim(implode('.',$file_tmp));
				$file_tmp = str_replace('#','',$file_tmp);
				$file_tmp = str_replace('\&amp;','',$file_tmp);
				$file_tmp = str_replace('\&','',$file_tmp);
				$target = $file_tmp . "." . $extension;
				$target = JFile::makeSafe($target);
				if ( in_array($target,$existingfiles) ) {
					$randnumber = (50-strlen($target));
					$fncount = 0;
					while (true) {
						$randname = JtgHelper::alphanumericPass($randnumber);
						$target = $file_tmp.$randname . "." . $extension;
						if (!in_array($target,$existingfiles) )
						break;
						// Man weiß ja nie ;)
						if ( $fncount > 100 ) {
							$randname = JtgHelper::alphanumericPass(45);
							$target = $randname . "." . $extension;
						}
						if ( $fncount > 10000 )
						die("<html>Booah! No free Filename available!<br>\"<i>" . $file . "</i>\"</html>");
						$fncount++;
					}
				} elseif (strlen($target) > 50) { // Wenn Dateiname über 50 Zeichen hat...
					for($j=0;$j<100;$j++) { // ... unternehme 100 Versuche...
						$file_tmp = JtgHelper::alphanumericPass(45); // $this->alphanumericPass(45);
						if ( !in_array($file_tmp . "." . $extension,$existingfiles) ) {
							// ... einen neuen Namen zu finden, ...
							$target = $file_tmp . "." . $extension;
							$j=105; // ... und beende, andernfalls ...
						}
						if ( $j == 99 ) // ... breche ab.
						die("<html>Filename<br>\"<i>" . $file . "</i>\"<br>too long to proceed, please short manually</html>");
					}
				}

				$uid =& JRequest::getVar('uid_'.$i);
				$date =& JRequest::getVar('date_'.$i);
				//			$images =& JRequest::getVar('images_'.$i, null, 'files', 'array');
				$access =& JRequest::getInt('access_'.$i);
				// 	get the start coordinates $target
				$gps = new gpsClass();
				$gps->gpsFile = $file;
				$isTrack = $gps->isTrack();
				if ($isTrack !== false) $isTrack = "1"; else $isTrack = "0";
				$isWaypoint = $gps->isWaypoint();
				if ($isWaypoint !== false) $isWaypoint = "1"; else $isWaypoint = "0";
				$isRoute = "0";
				if($start = $gps->getStartCoordinates())  {
					$fileokay = true;
				} else {
					echo "<script type='text/javascript'>alert('".JText::_('COM_JTG_NO_SUPPORT') . ": " . $target . "');window.history.back(-1);</script>";
					// 				exit;
				}
				if ($fileokay == true) {

					// upload the file
					// 				$upload_dir = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'uploaded_tracks'.DS;
					// 				$filename = explode(DS,$file);
					// 				$filename = $filename[(count($filename)-1)];
					// 				$filename = JFile::makeSafe($filename);
					if (!JFile::copy($file, $targetdir.$target)) {
						echo "Upload failed (file: \"" . $file . "\") !\n";
					} else {
						chmod($targetdir.$target, 0664);
					}
					if (!JFile::delete($file))
					echo "Erasing failed (file: \"" . $file . "\") !\n";

					$start_n = $start[1];
					$start_e = $start[0];
					$coords = $gps->getCoords($targetdir.$target);
					$distance = $gps->getDistance($coords);
					// call the elevation function
					$ele = $gps->getElevation($coords);
					// images upload part

					$query = "INSERT INTO #__jtg_files SET"
					. "\n uid='" . $uid . "',"
					. "\n catid='" . $catid . "',"
					. "\n title='" . $title . "',"
					. "\n file='" . $target . "',"
					. "\n terrain='" . $terrain . "',"
					. "\n description='" . $desc . "',"
					. "\n date='" . $date . "',"
					. "\n start_n='" . $start_n . "',"
					. "\n start_e='" . $start_e . "',"
					. "\n distance='" . $distance . "',"
					. "\n ele_asc='" . $ele[0] . "',"
					. "\n ele_desc='" . $ele[1] . "',"
					. "\n level='" . $level . "',"
					. "\n access='" . $access . "',"
					. "\n istrack='" . $isTrack . "',"
					. "\n iswp='" . $isWaypoint . "',"
					. "\n isroute='" . $isRoute . "',"
					. "\n hidden='" . $hidden . "'";

					$db->setQuery($query);
					$db->query();
					if ($db->getErrorNum()) {
						echo $db->stderr();
						return false;
					}
				}
			}
		}
		return true;
	}

	function importFromJPT($track) {
		$mainframe =& JFactory::getApplication();
		jimport('joomla.filesystem.file');
		require_once(".." . DS . "components" . DS . "com_jtg" . DS . "helpers" . DS . "gpsClass.php");
		$db =& JFactory::getDBO();
		$fileokay = false;
		$targetdir = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'uploaded_tracks'.DS;
		// TODO What was this import for? Joogpstracks to Injooosm??
		$sourcedir = JPATH_SITE . DS . "components" . DS . "com_joomgpstracks" . DS . "uploaded_tracks".DS;
		$existingfiles = JFolder::files($targetdir);
		$file = $sourcedir.$track['file'];
		$file_tmp = explode(DS,$file);
		$file_tmp = str_replace(' ','_',strtolower($file_tmp[(count($file_tmp)-1)]));
		$file_tmp = explode('.',$file_tmp);
		$extension = $file_tmp[(count($file_tmp)-1)];
		unset($file_tmp[(count($file_tmp)-1)]);
		$file_tmp = trim(implode('.',$file_tmp));
		$file_tmp = str_replace('#','',$file_tmp);
		$file_tmp = str_replace('\&amp;','',$file_tmp);
		$file_tmp = str_replace('\&','',$file_tmp);
		$target = $file_tmp . "." . $extension;
		$target = JFile::makeSafe($target);
		if ( in_array($target,$existingfiles) ) {
			$randnumber = (50-strlen($target));
			$fncount = 0;
			while (true) {
				$randname = $this->alphanumericPass($randnumber);
				$target = $file_tmp.$randname . "." . $extension;
				if (!in_array($target,$existingfiles) )
				break;
				// Man weiß ja nie ;)
				if ( $fncount > 100 ) {
					$randname = $this->alphanumericPass(45);
					$target = $randname . "." . $extension;
				}
				if ( $fncount > 10000 )
				die("<html>Booah! No free Filename available!<br>\"<i>" . $file . "</i>\"</html>");
				$fncount++;
			}
		} elseif (strlen($target) > 50) { // Wenn Dateiname über 50 Zeichen hat...
			for($j=0;$j<100;$j++) { // ... unternehme 100 Versuche...
				$file_tmp = $this->alphanumericPass(45);
				if ( !in_array($file_tmp . "." . $extension,$existingfiles) ) {
					// ... einen neuen Namen zu finden, ...
					$target = $file_tmp . "." . $extension;
					$j=105; // ... und beende, andernfalls ...
				}
				if ( $j == 99 ) // ... breche ab.
				die("<html>Filename<br>\"<i>" . $file . "</i>\"<br>too long to proceed, please short manually</html>");
			}
		}
		// 	get the start coordinates $target
		$gps = new gpsClass();
		$gps->gpsFile = $file;
		$isTrack = $gps->isTrack();
		if ($isTrack !== false) $isTrack = "1"; else $isTrack = "0";
		$isWaypoint = $gps->isWaypoint();
		if ($isWaypoint !== false) $isWaypoint = "1"; else $isWaypoint = "0";
		$isRoute = "0";
		if($start = $gps->getStartCoordinates())  {
			$fileokay = true;
		} else {
			echo "<script type='text/javascript'>alert('".JText::_('COM_JTG_NO_SUPPORT') . ": " . $target . "');window.history.back(-1);</script>";
			// 				exit;
		}
		if ($fileokay == true) {
			if (!JFile::copy($file, $targetdir.$target)) {
				echo "Upload failed (file: \"" . $file . "\") !\n";
			} else {
				chmod($targetdir.$target, 0664);
			}
			$cfg = JtgHelper::getConfig();
			$types = explode(',',$cfg->type);
			$query = "INSERT INTO #__jtg_files SET"
			. "\n uid='" . $track['uid'] . "',"
			. "\n catid='0',"
			. "\n title='" . $track['title'] . "',"
			//			. "\n file='" . $track['file'] . "',"
			. "\n file='" . $target . "',"
			. "\n description='" . $track['description'] . "',"
			. "\n date='" . $track['date'] . "',"
			. "\n start_n='" . $track['start_n'] . "',"
			. "\n start_e='" . $track['start_e'] . "',"
			. "\n distance='" . $track['distance'] . "',"
			. "\n ele_asc='" . $track['ele_asc'] . "',"
			. "\n ele_desc='" . $track['ele_desc'] . "',"
			. "\n level='" . $track['level'] . "',"
			. "\n access='" . $track['access'] . "',"
			. "\n istrack='" . $isTrack . "',"
			. "\n iswp='" . $isWaypoint . "',"
			. "\n isroute='" . $isRoute . "'";
			$db->setQuery($query);
			$db->query();
			if ($db->getErrorNum()) {
				echo $db->stderr();
				return false;
			} else {
				// start picture import
				$query = "SELECT id FROM #__jtg_files WHERE file='" . $target . "'";
				$db->setQuery($query);
				$result = $db->loadObject();
				if ($db->getErrorNum()) {
					echo $db->stderr();
					return false;
				}
				$imagedirsource = JPATH_SITE . DS . "images" . DS . "joomgpstracks" . DS . md5($track['title']).DS;
				$imagedirsourcedir = JFolder::files($imagedirsource);
				$imagedirdestination = JPATH_SITE . DS . "images" . DS . "jtrackgallery" . DS . $result->id.DS;
				if((!JFolder::exists($imagedirdestination)) AND (count($imagedirsourcedir) > 0) )
				JFolder::create($imagedirdestination,0777);
				foreach ( $imagedirsourcedir AS $imagetocopy ) {
					if (!JFile::copy($imagedirsource.$imagetocopy, $imagedirdestination.$imagetocopy)) {
						echo "Upload failed:<pre>\"" . $imagedirsource.$imagetocopy . "\"</pre> to <pre>\"" . $imagedirdestination.$imagetocopy . "\"</pre>\n";
						return false;
					}
				}
				// end picture import
			}
		}
		return true;
	}

	function saveFile() {
		$mainframe =& JFactory::getApplication();
		jimport('joomla.filesystem.file');
		require_once(".." . DS . "components" . DS . "com_jtg" . DS . "helpers" . DS . "gpsClass.php");

		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();
		// get the post data
		$catid =& JRequest::getVar('catid');
		$catid = implode(",",$catid);
		$level =& JRequest::getInt('level');
		$title =& JRequest::getVar('title');
		$terrain =& JRequest::getVar('terrain');
		if ($terrain)
		$terrain = implode(',', $terrain);
		else
		$terrain = "";
		$desc =& JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$desc = $this->parsedesc($desc);
		$file =& JRequest::getVar('file', null, 'files', 'array');
		$uid =& JRequest::getVar('uid');
		$date = date("Y-m-d");
		$images =& JRequest::getVar('images', null, 'files', 'array');
		$access =& JRequest::getInt('access');
		$hidden =& JRequest::getVar('hidden');

		// @ToDo: => JtgHelper::uploadfile
		// upload the file
		$upload_dir = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'uploaded_tracks' . DS;
		$filename = JFile::makeSafe($file['name']);
		$randnumber = (50-strlen($filename));
		$fncount = 0;
		while (true) {
			if (!JFile::exists($upload_dir.$filename))
			{
				if (!JFile::upload($file['tmp_name'], $upload_dir.$filename)) {
					echo "Upload failed!";
				} else {
					chmod($upload_dir.strtolower($filename), 0664);
				}
				break;
			}
			else
			{
				$randname = $this->alphanumericPass($randnumber);
				$filename = $randname.JFile::makeSafe($file['name']);
				// Man weiß ja nie ;)
				if ( $fncount > 10000 )
				die("<html>Booah! No free Filename available!<br>\"<i>".JFile::makeSafe($file['name']) . "</i>\"</html>");
				$fncount++;
			}
		}

		// get the start coordinates
		$gps = new gpsClass();
		$isTrack = $gps->isTrack(simplexml_load_file($upload_dir.strtolower($filename)));
		if ($isTrack !== false) $isTrack = "1"; else $isTrack = "0";
		$isWaypoint = $gps->isWaypoint(simplexml_load_file($upload_dir.strtolower($filename)));
		if ($isWaypoint !== false) $isWaypoint = "1"; else $isWaypoint = "0";
		$isRoute = "0";
		$isCache = 0;
		//		$isCache = $gps->isCache();
		//		if ($isCache !== false) $isCache = "1"; else $isCache = "0";
		$gps->gpsFile = ".." . DS . "images" . DS . "jtrackgallery" . DS . "uploaded_tracks" . DS . strtolower($filename);
		if($gps->getStartCoordinates())  {
			$start = $gps->getStartCoordinates();
		} else {
			echo "<script type='text/javascript'>alert('".JText::_('COM_JTG_NO_SUPPORT') . "');window.history.back(-1);</script>";
			//                 exit;
		}

		$file = ".." . DS . "images" . DS . "jtrackgallery" . DS . "uploaded_tracks" . DS . strtolower($filename);
		$start_n = $start[1];
		$start_e = $start[0];
		$coords = $gps->getCoords($file);
		$distance = $gps->getDistance($coords);
		//             if($distance == NULL)  {
		//                 echo "<script type='text/javascript'>alert('".JText::_('COM_JTG_DISTANCE') . " = 0');window.history.back(-1);</script>";
		//                 exit; // Warum stop?
		//             }

		// call the elevation function
		$ele = $gps->getElevation($coords);

		$query = "INSERT INTO #__jtg_files SET"
		. "\n uid='" . $uid . "',"
		. "\n catid='" . $catid . "',"
		. "\n title='" . $title . "',"
		. "\n file='".strtolower($filename) . "',"
		. "\n terrain='" . $terrain . "',"
		. "\n description='" . $desc . "',"
		. "\n date='" . $date . "',"
		. "\n start_n='" . $start_n . "',"
		. "\n start_e='" . $start_e . "',"
		. "\n distance='" . $distance . "',"
		. "\n ele_asc='" . $ele[0] . "',"
		. "\n ele_desc='" . $ele[1] . "',"
		. "\n level='" . $level . "',"
		. "\n access='" . $access . "',"
		. "\n istrack='" . $isTrack . "',"
		. "\n iswp='" . $isWaypoint . "',"
		. "\n isroute='" . $isRoute . "',"
		. "\n iscache='" . $isCache . "',"
		. "\n hidden='" . $hidden . "'"
		;
		$db->setQuery($query);
		$db->query();
		if ($db->getErrorNum()) {
			JFile::delete($file);
			return false;
		}

		$query = "SELECT * FROM #__jtg_files"
		. "\n WHERE file='" . strtolower($filename) . "'";
		$db->setQuery($query);
		$result = $db->loadObject();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		$id = $result->id;
		//		images upload part
		$imgpath = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'track_' . $id . DS;
		$images =& JRequest::getVar('images', null, 'files', 'array');
		if(count($images['name']) > 1)
		{
			$cfg = JtgHelper::getConfig();
			$types = explode(',',$cfg->type);
			JFolder::create($imgpath,0777);
			foreach($images['name'] as $key => $value)
			{
				if($value != "")
				{
					$imgfilename = JFile::makesafe($value);
					$ext = explode('.',$images['name'][$key]);
					if(in_array(strtolower($ext[1]),$types))
					{
						JtgHelper::createimageandthumbs($images['tmp_name'][$key], $ext[1],$imgpath, $imgfilename);
					}
				}
			}
		}
		return true;
	}

	/*
	 * description: Import tracks from JoomGPSTracks
	 *
	 * @param string $id
	 */
	function importJPTtracks()  {
		/* under construction */
		$importfiles = $this->_fetchJPTfiles;
		$mainframe =& JFactory::getApplication();
		jimport('joomla.filesystem.file');
		require_once(".." . DS . "components" . DS . "com_jtg" . DS . "helpers" . DS . "gpsClass.php");
		$fileokay = true;
		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();
		$targetdir = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'uploaded_tracks'.DS;
		//	$found =& JRequest::getInt('found');
		for($i=0;$i<count($importfiles);$i++) {
			$importfile = $importfiles[$i];
			$existingfiles = JFolder::files($targetdir);
			//		$import =& JRequest::getVar('import_'.$i);
			//		if ( $import == "on" ) {
			$catid = $importfile['catid'];
			$level = $importfile['level'];
			$title = $importfile['title'];
			$terrain = $importfile['terrain'];
			$desc = $importfile['desc'];
			$file = $importfile['file'];
			$source = $file;
			$file_tmp = explode(DS,$file);
			//				$file_tmp = str_replace(' ','_',strtolower($file_tmp[(count($file_tmp)-1)]));
			$file_tmp = explode('.',$file_tmp);
			$extension = $file_tmp[(count($file_tmp)-1)];
			unset($file_tmp[(count($file_tmp)-1)]);
			$file_tmp = trim(implode('.',$file_tmp));
			$file_tmp = str_replace('#','',$file_tmp);
			$file_tmp = str_replace('\&amp;','',$file_tmp);
			$file_tmp = str_replace('\&','',$file_tmp);
			$target = $file_tmp . "." . $extension;
			$target = JFile::makeSafe($target);
			if ( in_array($target,$existingfiles) ) {
				$randnumber = (50-strlen($target));
				$fncount = 0;
				while (true) {
					$randname = $this->alphanumericPass($randnumber);
					$target = $file_tmp.$randname . "." . $extension;
					if (!in_array($target,$existingfiles) )
					break;
					// Man weiß ja nie ;)
					if ( $fncount > 100 ) {
						$randname = $this->alphanumericPass(45);
						$target = $randname . "." . $extension;
					}
					if ( $fncount > 10000 )
					die("<html>Booah! No free Filename available!<br>\"<i>" . $file . "</i>\"</html>");
					$fncount++;
				}
			} elseif (strlen($target) > 50) { // Wenn Dateiname über 50 Zeichen hat...
				for($j=0;$j<100;$j++) { // ... unternehme 100 Versuche...
					$file_tmp = $this->alphanumericPass(45);
					if ( !in_array($file_tmp . "." . $extension,$existingfiles) ) {
						// ... einen neuen Namen zu finden, ...
						$target = $file_tmp . "." . $extension;
						$j=105; // ... und beende, andernfalls ...
					}
					if ( $j == 99 ) // ... breche ab.
					die("<html>Filename<br>\"<i>" . $file . "</i>\"<br>too long to proceed, please short manually</html>");
				}
			}

			$uid = $importfile['uid'];
			$date = $importfile['date'];
			$access = $importfile['access'];
			// 	get the start coordinates $target
			$gps = new gpsClass();
			$gps->gpsFile = $file;
			$isTrack = $gps->isTrack();
			if ($isTrack !== false) $isTrack = "1"; else $isTrack = "0";
			$isWaypoint = $gps->isWaypoint();
			if ($isWaypoint !== false) $isWaypoint = "1"; else $isWaypoint = "0";
			$isRoute = "0";
			$start_n = $importfile['start_n'];
			$start_e = $importfile['start_e'];
			/*			if($start = $gps->getStartCoordinates())  {
			 $fileokay = true;
			 } else {
			 echo "<script type='text/javascript'>alert('".JText::_('COM_JTG_NO_SUPPORT') . ": " . $target . "');window.history.back(-1);</script>";
			 // 				exit;
			 }
			 */
			//			if ($fileokay == true) {

			// upload the file
			// 				$upload_dir = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'uploaded_tracks'.DS;
			// 				$filename = explode(DS,$file);
			// 				$filename = $filename[(count($filename)-1)];
			// 				$filename = JFile::makeSafe($filename);
			if (!JFile::copy($file, $targetdir.$target)) {
				echo "Upload failed (file: \"" . $file . "\") !\n";
			} else {
				chmod($targetdir.$target, 0664);
			}
			//				if (!JFile::delete($file))
			//					echo "Erasing failed (file: \"" . $file . "\") !\n";

			//				$start_n = $start[1];
			//				$start_e = $start[0];
			//				$coords = $gps->getCoords($targetdir.$target);
			//				$distance = $gps->getDistance($coords);
			$distance = $importfile['distance'];

			// call the elevation function
			//				$ele = $gps->getElevation($coords);
			$ele[0] = $importfile['ele_asc'];
			$ele[1] = $importfile['ele_desc'];

			// images upload part
			$cfg = JtgHelper::getConfig();
			$types = explode(',',$cfg->type);

			$query = "INSERT INTO #__jtg_files SET"
			. "\n uid='" . $uid . "',"
			. "\n catid='" . $catid . "',"
			. "\n title='" . $title . "',"
			. "\n file='" . $target . "',"
			. "\n terrain='" . $terrain . "',"
			. "\n description='" . $desc . "',"
			. "\n date='" . $date . "',"
			. "\n start_n='" . $start_n . "',"
			. "\n start_e='" . $start_e . "',"
			. "\n distance='" . $distance . "',"
			. "\n ele_asc='" . $ele[0] . "',"
			. "\n ele_desc='" . $ele[1] . "',"
			. "\n level='" . $level . "',"
			. "\n access='" . $access . "',"
			. "\n istrack='" . $isTrack . "',"
			. "\n iswp='" . $isWaypoint . "',"
			. "\n isroute='" . $isRoute . "'";

			$db->setQuery($query);
			$db->query();
			if ($db->getErrorNum()) {
				echo $db->stderr();
				return false;
			} else {

				// Fehlt noch ...
				$id = 0;
				$images = 0;
				// Fehlt noch ...

				$sourcePath = JPATH_SITE . DS . 'images' . DS . 'joomgpstracks' . DS . md5($title);
				$destPath = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'track_' . $id;
				if(count($images) > 0 )  {
					JFolder::create($destPath,0777);
					//						$img_dir = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . md5($title);
					foreach($images['name'] as $key => $value)  {
						$ext = explode('.',$images['name'][$key]);
						if(in_array($ext[1], $types)) {

							JtgHelper::createimageandthumbs($images['tmp_name'][$key], $ext[1], $destPath,  strtolower($images['name'][$key]));
						}
					}
				}
			}
		}

		return false;
	}

	function getImages($id) {
		$img_dir = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'track_' . $id;
		if (!JFolder::exists($img_dir))
		return null;
		$images = JFolder::files($img_dir);
		return $images;
	}

	function updateFile()  {
		$mainframe =& JFactory::getApplication();
		jimport('joomla.filesystem.file');
		require_once('..' . DS . 'components' . DS . 'com_jtg' . DS . 'helpers' . DS . 'gpsClass.php');

		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();

		// get the post data
		$id =& JRequest::getInt('id');
		$catid =& JRequest::getVar('catid');
		$catid = implode(",",$catid);
		$level =& JRequest::getInt('level');
		$title =& JRequest::getVar('title');
		$hidden =& JRequest::getVar('hidden');
		$published =& JRequest::getVar('published');

		$allimages = $this->getImages($id);
		$imgpath = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'track_' . $id. DS;
		if($allimages){
			foreach ($allimages AS $key => $image) {
				$image =& JRequest::getVar('deleteimage_'.str_replace('.',null,$image));
				if($image !== NULL) 
				{
				    JFile::delete($imgpath.$image);
				    // delete thumbnails too
				    JFile::delete($imgpath. 'thumbs' . DS. 'thumb0_' . $image);
				    JFile::delete($imgpath. 'thumbs' . DS. 'thumb1_' . $image);
				    JFile::delete($imgpath. 'thumbs' . DS. 'thumb2_' . $image);
				}
			}
		}
		$date =& JRequest::getVar('date');
		$terrain =& JRequest::getVar('terrain');
		// ToDo: empty Terrain = bad
		if ($terrain != "")
		$terrain = implode(',', $terrain);
		$desc =& JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$desc = $this->parsedesc($desc);
		//         $uid = $user->get('id');
		$uid =& JRequest::getVar('uid');
		if ( $date == "" )
		$date = date("Y-m-d");
		$access =& JRequest::getInt('access');


		//		images upload part
		$images =& JRequest::getVar('images', null, 'files', 'array');
		if(count($images['name']) > 1)
		{
			$cfg = JtgHelper::getConfig();
			$types = explode(',',$cfg->type);
			JFolder::create($imgpath,0777);
			foreach($images['name'] as $key => $value)
			{
				$filename = JFile::makesafe($value);
				$ext = explode('.',$images['name'][$key]);
				if(in_array(strtolower($ext[1]), $types))
				{
					JtgHelper::createimageandthumbs($images['tmp_name'][$key], $ext[1], $imgpath, $filename);
				}
			}
		}



		$query = "UPDATE #__jtg_files SET"
		. "\n uid='" . $uid . "',"
		. "\n catid='" . $catid . "',"
		. "\n title='" . $title . "',"
		. "\n terrain='" . $terrain . "',"
		. "\n date='" . $date . "',"
		. "\n description='" . $desc . "',"
		. "\n level='" . $level . "',"
		. "\n access='" . $access . "',"
		. "\n published='" . $published . "',"
		. "\n hidden='" . $hidden . "'"
		. "\n WHERE id='" . $id . "'"
		;

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		} else {
			return true;
		}

	}
	function parsedesc($desc) {
		$replace = array('"',"'");
		$with = array('&#34;','&#39;');
		return str_replace($replace,$with,$desc);
	}
}
