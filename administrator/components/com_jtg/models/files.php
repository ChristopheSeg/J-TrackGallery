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

// Import Joomla! libraries
jimport('joomla.application.component.model');
/**
 * Model Class Files
 */
class JtgModelFiles extends JModelLegacy
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
		//$mainframe = JFactory::getApplication();

		// get the post data
		$id = JFactory::getApplication()->input->get('id');
		$file = JFactory::getApplication()->input->get('file');
		$cfg = JtgHelper::getConfig();
		jimport('joomla.filesystem.file');
		require_once(".." . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_jtg" . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "gpsClass.php");
		$file = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'uploaded_tracks' . DIRECTORY_SEPARATOR . $file;
		$gpsData = new gpsDataClass($cfg->unit);
		$gpsData ->loadFileAndData($file, $file ); // Do not use cache here
		if ($gpsData->displayErrors())
		{
		    return false;
		}

		$isTrack = $gpsData->isTrack;
		$isWaypoint = $gpsData->isWaypoint;
		$isRoute = (int)0;

		if ( $isWaypoint == 1 )
		{
		    $isCache = $gpsData->isCache;
		}
		else
		{
		    $isCache = 0;
		}
		if ( $this->isTrack == 1 )
		{
			$distance = $gpsData->distance;
			$ele = $gpsData->getElevation($coords);
		}
		else
		{
			$distance = 0;
			$ele = array(null,null);
		}

		if ( $this->start === false )
		{
			return false;
		}

		$db = JFactory::getDBO();
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
		. "\n istrack='" . $gpsData->isTrack . "',"
		. "\n iswp='" . $gpsData->isWaypoint . "',"
		. "\n isroute='" . $gpsData->isRoute . "',"
		. "\n iscache='" . $gpsData->isCache . "',"
		. "\n start_n='" . $gpsData->start[1] . "',"
		. "\n start_e='" . $gpsData->start[0] . "',"
		. "\n distance='" . $gpsData->distance . "',"
		. "\n ele_asc='" . $gpsData->totalAscent . "',"
		. "\n ele_desc='" . $gpsData->totalDescent . "',"
		. "\n vote='" . $vote . "'"
		. "\n WHERE id='" . $id . "'"
		;

		$db = JFactory::getDBO();
		$db->setQuery($query);
		$db->execute();

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


	/**
	 *
	 * @global array $mainframe
	 * @global string $option
	 */
	function __construct() {
		parent::__construct();
		$mainframe = JFactory::getApplication(); // global _ $option;

		// Get the pagination request variables
		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart	= $mainframe->getUserStateFromRequest( $this->option.'.limitstart', 'limitstart', 0, 'int' );

		// In case limit has been changed, adjust limitstart accordingly
		//		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
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
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
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
		$mainframe = JFactory::getApplication();

		$orderby	= $this->_buildContentOrderBy();
		$where      = $this->_buildContentWhere();

		//        $db = JFactory::getDBO();

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
		$mainframe = JFactory::getApplication(); // global _ $option;

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

		$mainframe = JFactory::getApplication(); // global _ $option;

		$search = JFactory::getApplication()->input->get('search');
		$where = array();
		$db = JFactory::getDBO();

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
	 * @param   string  $id
	 * @return object
	 */
	function getFile($id)  {
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();
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
	 * @param   string  $id
	 */
	function setId($id)
	{
		// Set weblink id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 *
	 * @param   array  $cid
	 * @param   string  $publish
	 * @return bool
	 */
	function publish($cid = array(), $publish = 1)
	{
		$user 	= JFactory::getUser();

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
			if (!$this->_db->execute()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}
	/**
	 *
	 * @param   array  $cid
	 * @param   string  $hide
	 * @return bool
	 */
	function showhide($cid = array(), $hide = 0)
	{
		$user 	= JFactory::getUser();

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
			if (!$this->_db->execute()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return true;
	}

	/**
	 *
	 * @param   array  $cid
	 * @param   string  $access
	 * @return bool
	 */
	function access($cid = array(), $access = 1)
	{
		$user 	= JFactory::getUser();

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
			if (!$this->_db->execute()) {
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
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$result = false;
		if (count( $cid ))
		{
			JArrayHelper::toInteger($cid);
			$cids = implode( ',', $cid );
			$query = 'SELECT * FROM #__jtg_files WHERE id IN ( '.$cids.' )';
			$this->_db->setQuery($query);
			$rows = $this->_db->loadObjectList();

			if(!$this->_db->execute()) {
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
			foreach($rows as $row)  {
				// folder and Pictures within delete
				$folder = JPATH_SITE . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "jtrackgallery" . DIRECTORY_SEPARATOR . $row->id;
				if (JFolder::exists($folder))
				JFolder::delete($folder);
				// File (gpx?) delete
				$filename = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'uploaded_tracks' . DIRECTORY_SEPARATOR . $row->file;
				if (JFile::exists($filename))
				JFile::delete($filename);
			}
			// delete from DB
			$query = 'DELETE FROM #__jtg_files WHERE id IN ( '.$cids.' )';
			$this->_db->setQuery( $query );
			if(!$this->_db->execute()) {
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
	function deleteFromImport($found)
	{
		$cid = JFactory::getApplication()->input->get('import_0' );
		jimport('joomla.filesystem.file');
		$result = false;
		for ($i = 0; $i <= $found; $i++)
		{
			$file = JFactory::getApplication()->input->get('import_'.$i );
			if ( $file !== null )
			{
				if (JFile::exists($file))
				{
					if (!JFile::delete($file))
					{
						JFactory::getApplication()->enqueueMessage(JText::_( 'COM_JTG_ERROR_FILE_NOT_ERASEABLE' ) . "(" . $file . ")",'Error' );
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
	function getLevelList($selected=0)  {
		// 

		$return = "\n";
		$cfg = JtgHelper::getConfig();
		$levels = explode("\n",$cfg->level);
		array_unshift($levels, 'dummy');
		$i = 0;
		foreach($levels AS $level){
			if ( trim($level) != "" ) 
			{
				if ( $i == 0 )
					$levels[0] = JText::_('COM_JTG_SELECT');
				else
					$levels[$i] = $i . " - " . JText::_(trim($level));

				$i++;
			}
		}
		$size = count($levels);
		if ( $size > 6) $size = 6;
		return JHtml::_('select.genericlist', $levels, 'level', 'size="'.$size.'"', 'id', 'title', $selected);
	}
	/**
	 *
	 * @global object $mainframe
	 * @return array
	 */
	function getCats($nosubcats=false,$stdtext='COM_JTG_SELECT',$stdid=0, $type=1) {
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__jtg_cats WHERE published=1 ORDER BY ordering,id ASC";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$children = array();
		foreach ($rows as $v ) {
			if ( ( ($nosubcats) AND ($v->parent_id == 0) ) OR (!$nosubcats) )
			{
				$v->title = JText::_($v->title);
				$v->name = $v->title; // TODO  unnecessary
				$pt	= $v->parent_id;
				$list	= @$children[$pt] ? $children[$pt] : array();
				array_push( $list, $v );
				$children[$pt] = $list;
			}
		}
		$levellimit = 50;

		$rows = JHtml::_('menu.treerecurse', 0, '', array(), $children, max( 0, $levellimit-1 ),0 , $type );
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
		// print_r($rows);die();

		return $rows;
	}

	/**
	 *
	 * @global object $mainframe
	 * @return array
	 */
	function getUsers($nullter = false, $where = "WHERE block = 0" )  {
		//		Used to generate generic list of users
		// Joomla 2.5 JHtml::_('list.users'..); returns duplicate users

		$db = JFactory::getDBO();
		$rows=null;
		$query = "SELECT id, name as title FROM #__users " . $where . " ORDER BY name";
	    $db->setQuery($query);
	    $rows = $db->loadObjectList();

		$users = array();
		if($rows)
		{
			foreach ($rows as $v) {
				$users[] = $v;
			}
		}
		if ($nullter !== false)
		{
			$nullter = new stdClass();
			$nullter->title = JText::_('COM_JTG_SELECT');
			$nullter->id = null;
			array_unshift($users,$nullter);
		}
		return $users;
	}

	/**
	 *
	 * @global object $mainframe
	 * @return array
	 */
	function getTerrain($select="*", $nullter = false, $where = null )  {
		//		$mainframe = JFactory::getApplication();

		$db = JFactory::getDBO();
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
		$mainframe = JFactory::getApplication();

		$db = JFactory::getDBO();

		$query = "SELECT access FROM #__jtg_files WHERE id='" . $id . "'";

		$db->setQuery($query);
		$row = $db->loadResult();
		return $row;
	}

	function saveFiles() {
		$mainframe = JFactory::getApplication();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		require_once(".." . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_jtg" . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "gpsClass.php");
		$fileokay = true;
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$targetdir = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'uploaded_tracks'. DIRECTORY_SEPARATOR;
		$found = JRequest::getInt('found');
		for($i=0;$i<$found;$i++) {
			$existingfiles = JFolder::files($targetdir);
			$import = JFactory::getApplication()->input->get('import_'.$i);
			if ( $import !== null ) {
				$catid = JFactory::getApplication()->input->get('catid_'.$i, NULL,'array');
				$catid = $catid ? implode(',',$catid) : '';
				$level = JFactory::getApplication()->input->get('level_'.$i, 0, 'integer');
				$title = JFactory::getApplication()->input->get('title_'.$i);
				$terrain = JFactory::getApplication()->input->get('terrain_'.$i, NULL,'array');
				if ($terrain)
				$terrain = $terrain ? implode(',',$terrain) : '';
				else
				$terrain = "";
				// $desc = JFactory::getApplication()->input->get('desc_'.$i, '', 'raw');
				
				$desc = $db->getEscaped(implode(' ',JFactory::getApplication()->input->get('desc_'.$i, '', 'array') ) );
				$file = JFactory::getApplication()->input->get('file_'.$i,'','raw');
				$hidden = JFactory::getApplication()->input->get('hidden_'.$i);
				$file_tmp = explode(DIRECTORY_SEPARATOR,$file);
				$filename = strtolower($file_tmp[(count($file_tmp)-1)]);
				$file_tmp = str_replace(' ','_',$filename);
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
				}
				elseif (strlen($target) > 50)
				{ // Wenn Dateiname über 50 Zeichen hat...
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

				$uid = JFactory::getApplication()->input->get('uid_'.$i);
				$date = JFactory::getApplication()->input->get('date_'.$i);
				//			$images = JFactory::getApplication()->input->files->get('images_'.$i,);
				$access = JRequest::getInt('access_'.$i);
				// 	get the start coordinates $target
				$cache = JFactory::getCache();
				$gpsData = new gpsDataClass("Kilometer");// default unit
				$gpsData = $cache->get(array ( $gpsData, 'loadFileAndData' ), array ($file, $filename ), "Kilometer");
				$errors = $gpsData->displayErrors();
				if ($errors)
				{
				   $map = "";
				   $coords = "";
				   $distance_float = 0;
				   $distance = 0;
				   echo "<script type='text/javascript'>alert('".JText::_('COM_JTG_NO_SUPPORT') . "\n" .$errors."');window.history.back(-1);</script>";
				   //TODO before exit, remove downloaded file!!
				   exit; //TODO chekch if exit is correct here ???
				}
				$fileokay = true; // TODO remove $fileokay

				$start_n = $gpsData->start[1];
				$start_e = $gpsData->start[0];
				$coords = $gpsData->allCoords;
				$isTrack = $gpsData->isTrack;
				$isWaypoint = $gpsData->isWaypoint;
				$isRoute = 0;
				$isCache = 0;

				$distance = $gpsData->distance;

				if ($fileokay == true) {

					// upload the file
					// 				$upload_dir = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'uploaded_tracks'. DIRECTORY_SEPARATOR;
					// 				$filename = explode(DIRECTORY_SEPARATOR,$file);
					// 				$filename = $filename[(count($filename)-1)];
					// 				$filename = JFile::makeSafe($filename);
					if (!JFile::copy($file, $targetdir.$target)) {
						echo "Upload failed (file: \"" . $file . "\") !\n";
					} else {
						chmod($targetdir.$target, 0664);
					}
					if (!JFile::delete($file))
					echo "Erasing failed (file: \"" . $file . "\") !\n";

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
					$db->execute();
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
		// TODO Deprecated, can be replacd by import from injooosm
		$mainframe = JFactory::getApplication();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		require_once(".." . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_jtg" . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "gpsClass.php");
		$db = JFactory::getDBO();
		$fileokay = false;
		$targetdir = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'uploaded_tracks'. DIRECTORY_SEPARATOR;
		// TODO What was this import for? Joogpstracks to Injooosm??
		$sourcedir = JPATH_SITE . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_joomgpstracks" . DIRECTORY_SEPARATOR . "uploaded_tracks". DIRECTORY_SEPARATOR;
		$existingfiles = JFolder::files($targetdir);
		$file = $sourcedir.$track['file'];
		$file_tmp = explode(DIRECTORY_SEPARATOR,$file);
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
		// TODO GPSCLASS deprecated,
		$gps_old = new gpsClass();
		$gps_old->gpsFile = $file;
		$isTrack = $gps_old->isTrack();
		$isWaypoint = $gps_old->isWaypoint();
		$isRoute = "0";

		if($start = $gps_old->getStartCoordinates())  {
			$fileokay = true;
		} else
		{
			// TODO print an error message
			echo "<script type='text/javascript'>alert('".JText::_('COM_JTG_NO_SUPPORT') . "(2): " . $target . "');window.history.back(-1);</script>";
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
			$db->execute();
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
				$imagedirsource = JPATH_SITE . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "joomgpstracks" . DIRECTORY_SEPARATOR . md5($track['title']). DIRECTORY_SEPARATOR;
				$imagedirsourcedir = JFolder::files($imagedirsource);
				$imagedirdestination = JPATH_SITE . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "jtrackgallery" . DIRECTORY_SEPARATOR . $result->id. DIRECTORY_SEPARATOR;
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
		$mainframe = JFactory::getApplication();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		require_once(".." . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_jtg" . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "gpsClass.php");

		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		// get the post data
		$catid = JFactory::getApplication()->input->get('catid', NULL,'array');
		$catid = $catid ? implode(',',$catid) : '';
		$level = JFactory::getApplication()->input->get('level', 0, 'integer');
		$title = JFactory::getApplication()->input->get('title');
		$terrain = JFactory::getApplication()->input->get('terrain', NULL, 'array');
		if ($terrain)
		$terrain = $terrain ? implode(',',$terrain) : '';
		else
		$terrain = "";
		$desc = $db->getEscaped(implode(' ',JFactory::getApplication()->input->get('description', '', 'array') ) );
		$file = JFactory::getApplication()->input->files->get('file');
		$uid = JFactory::getApplication()->input->get('uid');
		$date = date("Y-m-d");
		$images = JFactory::getApplication()->input->files->get('images');
		$access = JRequest::getInt('access');
		$hidden = JFactory::getApplication()->input->get('hidden');

		// @ToDo: => JtgHelper::uploadfile
		// upload the file
		$upload_dir = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'uploaded_tracks' . DIRECTORY_SEPARATOR;
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
				{
					//This would never happen !!
					die("<html>Booah! No free Filename available!<br>\"<i>".JFile::makeSafe($file['name']) . "</i>\"</html>");
				}
				$fncount++;
			}
		}

		// get the start coordinates
		$gpsData = new gpsDataClass("Kilometer");// default unit
		//TODO $track is not defined  here !!!!
		$file = ".." . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "jtrackgallery" . DIRECTORY_SEPARATOR . "uploaded_tracks" . DIRECTORY_SEPARATOR . strtolower($filename);
		$cache = JFactory::getCache();
		$gpsData = $cache->get(array ( $gpsData, 'loadFileAndData' ), array ($file, strtolower($filename) ), "Kilometer");
		$errors = $gpsData->displayErrors();
		if ($errors)
		{
		   $map = "";
		   $coords = "";
		   $distance_float = 0;
		   $distance = 0;
		   echo "<script type='text/javascript'>alert('".JText::_('COM_JTG_NO_SUPPORT') . "\n" . $errors . "');window.history.back(-1);</script>";
		   //TODO before exit, remove downloaded file!!
		   exit;
		}
		$fileokay = true; // TODO remove $fileokay

		$start_n = $gpsData->start[1];
		$start_e = $gpsData->start[0];
		$coords = $gpsData->allCoords;
		$isTrack = $gpsData->isTrack;
		$isWaypoint = $gpsData->isWaypoint;
		$isRoute = 0;
		$isCache = 0;

		$distance = $gpsData->distance;

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
		$db->execute();
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
		$imgpath = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'track_' . $id . DIRECTORY_SEPARATOR;
		$jInput = JFactory::getApplication()->input;
		$jFileInput = new jInput($_FILES);
    	$images = $jFileInput->get('images',array(),'array');
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
					$ext = JFile::getExt($images['name'][$key]);
					if(in_array(strtolower($ext),$types))
					{
						JtgHelper::createimageandthumbs($images['tmp_name'][$key], $ext,$imgpath, $imgfilename);
					}
				}
			}
		}
		return true;
	}

	/*
	 * description: Import tracks from JoomGPSTracks
	 *
	 * @param   string  $id
	 */
	function importJPTtracks()  {
		/* under construction */
	    // TODO DEPRECATED
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$importfiles = $this->_fetchJPTfiles;
		$mainframe = JFactory::getApplication();
		require_once(".." . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_jtg" . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "gpsClass.php");
		$fileokay = true;
		$db = JFactory::getDBO();
		$user = JFactory::getUser();
		$targetdir = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'uploaded_tracks'. DIRECTORY_SEPARATOR;
		//	$found = JRequest::getInt('found');
		for($i=0;$i<count($importfiles);$i++) {
			$importfile = $importfiles[$i];
			$existingfiles = JFolder::files($targetdir);
			//		$import = JFactory::getApplication()->input->get('import_'.$i);
			//		if ( $import == "on" ) {
			$catid = $importfile['catid'];
			$level = $importfile['level'];
			$title = $importfile['title'];
			$terrain = $importfile['terrain'];
			$desc = $importfile['desc'];
			$file = $importfile['file'];
			$source = $file;
			$file_tmp = explode(DIRECTORY_SEPARATOR,$file);
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
			//TODO gpsclass deprecated
			$gps_old = new gpsClass();
			$gps_old->gpsFile = $file;
			$isTrack = $gps_old->isTrack();
			$isWaypoint = $gps_old->isWaypoint();
			$isRoute = "0";
			$start_n = $importfile['start_n'];
			$start_e = $importfile['start_e'];
			/*			if($start = $gps_old->getStartCoordinates())  {
			 $fileokay = true;
			 } else {
			 echo "<script type='text/javascript'>alert('".JText::_('COM_JTG_NO_SUPPORT') . ": " . $target . "');window.history.back(-1);</script>";
			 // 				exit;
			 }
			 */
			//			if ($fileokay == true) {

			// upload the file
			// 				$upload_dir = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'uploaded_tracks'. DIRECTORY_SEPARATOR;
			// 				$filename = explode(DIRECTORY_SEPARATOR,$file);
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
			//				$coords = $gps_old->getCoords($targetdir.$target);
			//				$distance = $gps_old->getDistance($coords);
			$distance = $importfile['distance'];

			// call the elevation function
			//				$ele = $gps_old->getElevation($coords);
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
			$db->execute();
			if ($db->getErrorNum()) {
				echo $db->stderr();
				return false;
			} else {

				// Fehlt noch ...
				$id = 0;
				$images = 0;
				// Fehlt noch ...

				$sourcePath = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'joomgpstracks' . DIRECTORY_SEPARATOR . md5($title);
				$destPath = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'track_' . $id;
				if(count($images) > 0 )  {
					JFolder::create($destPath,0777);
					//						$img_dir = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . md5($title);
					foreach($images['name'] as $key => $value)  {
						$ext = JFile::getExt($images['name'][$key]);
						if(in_array($ext, $types)) {

							JtgHelper::createimageandthumbs($images['tmp_name'][$key], $ext, $destPath,  $images['name'][$key]);
						}
					}
				}
			}
		}

		return false;
	}

	function getImages($id) {
		jimport('joomla.filesystem.folder');
		$img_dir = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'track_' . $id;
		if (!JFolder::exists($img_dir))
		return null;
		$images = JFolder::files($img_dir);
		return $images;
	}

	function updateFile()  {
		$mainframe = JFactory::getApplication();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		require_once('..' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jtg' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'gpsClass.php');

		$db = JFactory::getDBO();
		$user = JFactory::getUser();

		// get the post data
		$id = JRequest::getInt('id');
		$catid = JFactory::getApplication()->input->get('catid', NULL,'array');
		$catid = $catid ? implode(',',$catid) : '';
		$level = JFactory::getApplication()->input->get('level', 0, 'integer');
		$title = JFactory::getApplication()->input->get('title');
		$hidden = JFactory::getApplication()->input->get('hidden');
		$published = JFactory::getApplication()->input->get('published');

		$allimages = $this->getImages($id);
		$imgpath = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'track_' . $id. DIRECTORY_SEPARATOR;
		if($allimages){
			foreach ($allimages AS $key => $image) {
				$image = JFactory::getApplication()->input->get('deleteimage_'.str_replace('.',null,$image));
				if($image !== NULL)
				{
				    JFile::delete($imgpath.$image);
				    // delete thumbnails too
				    JFile::delete($imgpath. 'thumbs' . DIRECTORY_SEPARATOR. 'thumb0_' . $image);
				    JFile::delete($imgpath. 'thumbs' . DIRECTORY_SEPARATOR. 'thumb1_' . $image);
				    JFile::delete($imgpath. 'thumbs' . DIRECTORY_SEPARATOR. 'thumb2_' . $image);
				}
			}
		}
		$date = JFactory::getApplication()->input->get('date');
		$terrain = JFactory::getApplication()->input->get('terrain', NULL, 'array');
		// ToDo: empty Terrain = bad
		if ($terrain != "")
		$terrain = $terrain ? implode(',',$terrain) : '';
		$desc = $db->getEscaped(implode(' ',JFactory::getApplication()->input->get('description', '', 'array') ) );
		$uid = JFactory::getApplication()->input->get('uid');
		if ( $date == "" )
		$date = date("Y-m-d");
		$access = JRequest::getInt('access');


		//		images upload part
		$jInput = JFactory::getApplication()->input;
		$jFileInput = new jInput($_FILES);
    	$images = $jFileInput->get('images',array(),'array');
				if(count($images['name']) > 1)
		{
			$cfg = JtgHelper::getConfig();
			$types = explode(',',$cfg->type);
			JFolder::create($imgpath,0777);
			foreach($images['name'] as $key => $value)
			{
				$filename = JFile::makesafe($value);
				$ext = JFile::getExt($images['name'][$key]);
				if(in_array(strtolower($ext), $types))
				{
					JtgHelper::createimageandthumbs($images['tmp_name'][$key], $ext, $imgpath, $filename);
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
		$db->execute();

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		} else {
			return true;
		}

	}
}
