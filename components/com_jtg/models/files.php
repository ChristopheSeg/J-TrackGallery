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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class JtgModelFiles extends JModelLegacy
{
	/**
	 * files data array
	 *
	 * @var array
	 */
	var $_data = null;

	/**
	 * files total
	 *
	 * @var integer
	 */
	var $_total = null;


	/**
	 * Constructor
	 */
	function __construct() {

		parent::__construct();
	}

	function getData($limit, $limitstart) {
		// Lets load the content if it doesn't already exist
		if (empty($this->_data))
		{
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $limitstart, $limit);
		}
		return $this->_data;
	}

	function getLevelSelect($selected) {
		$return = "<select name=\"level\">\n";
		$cfg = JtgHelper::getConfig();
		$levels = explode("\n",$cfg->level);
		array_unshift($levels,'dummy');
		$i = 0;
		foreach($levels AS $level){
			if ( trim($level) != "" ) {
				$return .= ("					<option value=\"" . $i . "\"");
				if ( $i == $selected )
				$return .= (" selected=\"selected\"");
				$return .= (">");
				if ( $i == 0 )
				$return .= JText::_('COM_JTG_SELECT');
				else
				$return .= $i . " - " . JText::_(trim($level));
				$return .= ("</option>\n");
				$i++;
			}
		}
		return $return . "				</select>\n";
	}

	/**
	 *
	 * @global object $mainframe
	 * @return array
	 */
	function getLevel($selected)  {
		$return = "\n";
		$cfg = JtgHelper::getConfig();
		$levels = explode("\n",$cfg->level);
		array_unshift($levels,'dummy');
		$i = 0;
		foreach($levels AS $level){
			if ( trim($level) != "" ) {
				if ( $i == $selected )
				{
					$selectedlevel = $i;
					$selectedtext = $level;
				}
				$i++;
			}
		}
		$return .= $selectedlevel . "/" . ( $i - 1 ) . " - " . JText::_(trim($selectedtext));
		return $return;
	}

	/**
	 *
	 * @return string
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
	 * @global array $mainframe
	 * @return string
	 */
	function _buildQuery() {
		$mainframe =& JFactory::getApplication();
		$user		=& JFactory::getUser();
		$orderby	= $this->_buildContentOrderBy();
		$where		= $this->_buildContentWhere();
		$userwhere	= "";

		if(JFactory::getApplication()->input->get('layout') == 'user' && !$where) {
			$userwhere = " WHERE uid='" . $user->get('id') . "'";
		} else if(JFactory::getApplication()->input->get('layout') == 'user' && $where) {
			$userwhere = " AND uid='" . $user->get('id') . "'";
		}

		$db =& JFactory::getDBO();

		$query = "SELECT a.*, b.title AS cat, b.image AS image, c.username AS user"
		. "\n FROM #__jtg_files AS a"
		. "\n LEFT JOIN #__jtg_cats AS b ON a.catid=b.id"
		//		. "\n LEFT JOIN #__jtg_cats AS b ON a.catid"
		//		. "\n LEFT JOIN #__users AS c ON a.uid\n"
		. "\n LEFT JOIN #__users AS c ON a.uid=c.id\n"
		. $where
		. $userwhere
		. $orderby
		;
		return $query;
	}

	/**
	 *
	 * @global array $mainframe
	 * @global string $option
	 * @return string
	 */
	function _buildContentOrderBy()
	{
		$mainframe =& JFactory::getApplication(); // global _ $option;

		$filter_order		= $mainframe->getUserStateFromRequest( $this->option.'filter_order',
			'filter_order',		'ordering',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $this->option.'filter_order_Dir',
			'filter_order_Dir',	'',		'word' );

		if ($filter_order == 'ordering'){
			$orderby 	= ' ORDER BY ordering '.$filter_order_Dir;
		} else {
			$orderby 	= ' ORDER BY '.$filter_order.' '.$filter_order_Dir.' , id ';
		}

		return $orderby;
	}

	/**
	 *
	 * @global array $mainframe
	 * @global string $option
	 * @return string
	 */
	function _buildContentWhere() {

		$mainframe =& JFactory::getApplication(); // global _ $option;

		$search =& JFactory::getApplication()->input->get('search');
		$cat =& JFactory::getApplication()->input->get('cat');
		$terrain =& JFactory::getApplication()->input->get('terrain');
		$index = "a";
		$where = array();
		$db =& JFactory::getDBO();

		if($search) {
			$where[] = 'LOWER(a.title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = 'LOWER(b.title) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$where[] = 'LOWER(c.username) LIKE '.$db->Quote( '%'.$db->getEscaped( $search, true ).'%', false );
			$index = "d";
		}
		if($cat) {
			$where[] = '('.$index.'.catid) LIKE '.$db->Quote( '%'.$db->getEscaped( $cat, true ).'%', false );
		}
		if($terrain) {
			//			$where[] = '('.$index.'.terrain) = '.$db->Quote( $db->getEscaped( $terrain, true ), false );
			$where[] = '('.$index.'.terrain) LIKE '.$db->Quote( '%'.$db->getEscaped( $terrain, true ).'%', false );
		}
		$pubhid = "( a.published = '1' AND a.hidden = '0' )";
		$where = ( count( $where ) ? ' WHERE ' . implode( ' OR ', $where ) : '' );
		if ( $where == "" )// bad :(
		$where = " WHERE " . $pubhid;
		else
		$where .= " AND " . $pubhid;
		return $where;
	}

	/**
	 *
	 * @global <type> $mainframe
	 * @return <type>
	 */
	function getCats() {
		$mainframe =& JFactory::getApplication();

		$db =& JFactory::getDBO();

		$query = "SELECT * FROM #__jtg_cats WHERE published=1 ORDER BY ordering ASC";

		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$limit = count($rows);
		$children = array();
		foreach ($rows as $v ) {
			$v->name = JText::_($v->title);
			$pt 	= $v->parent_id;
			$list 	= @$children[$pt] ? $children[$pt] : array();
			array_push( $list, $v );
			$children[$pt] = $list;
		}
		$list = JHtml::_('menu.treerecurse', 0, '', array(), $children );
		$list = array_slice($list,0,$limit);
		$cats = array();
		$nullcat = array(
				'id' => 0,
				'title' => JText::_('JNONE'),
				'name' => JText::_('JNONE'),
				'image' => "");
		$cats[0] = JArrayHelper::toObject($nullcat);
		foreach($list as $cat) {
			if($cat->treename == $cat->title)
			$title = $cat->title;
			else
			$title = $cat->treename;
			$arr = array(
				'id' => $cat->id,
				'title' => $title,
				'name' => JText::_($cat->title),
				'image' => $cat->image);
			$cats[$cat->id] = JArrayHelper::toObject($arr);
		}

		return $cats;
	}

	/**
	 *
	 * @global object $mainframe
	 * @return boolean
	 */
	function saveFile() {
		$mainframe =& JFactory::getApplication();

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();
		$cache =& JFactory::getCache('com_jtg');
		// get the post data
		$catid =& JFactory::getApplication()->input->get('catid', NULL,'array');
		if ( $catid !== null )
		$catid = implode(",",$catid);
		else
		$catid = "";
		$level =& JRequest::getInt('level');
		$title =& JFactory::getApplication()->input->get('title');
		$terrain =& JFactory::getApplication()->input->get('terrain', NULL, 'array');
		if($terrain != NULL)
		$terrain = implode(', ', $terrain);
		else
		$terrain = "";
		$desc =& $db->quote(JFactory::getApplication()->input->get('description', '', 'post', 'string', JREQUEST_ALLOWRAW));
		$file =& JFactory::getApplication()->input->files->get('file');
		$uid = $user->get('id');
		$date = date("Y-m-d");
		$images =& JFactory::getApplication()->input->get('images', null, 'files', 'array');
		$access =& JRequest::getInt('access', 0);
		$hidden =& JRequest::getInt('hidden', 0);
		$published =& JRequest::getInt('published', 0);

		// upload the file
		$upload_dir = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'uploaded_tracks' . DIRECTORY_SEPARATOR;
		$filename = strtolower(JFile::makeSafe($file['name']));
		$newfile = $upload_dir . strtolower($filename);
		if ( JFile::exists($newfile))
		{
		    die("<script type='text/javascript'>alert('".JText::sprintf("COM_JTG_FILE_ALREADY_EXISTS",$filename) . "');window.history.back(-1);</script>");
		}
		if (!JFile::upload($file['tmp_name'], $newfile)) {
			die("<script type='text/javascript'>alert('".JText::_('COM_JTG_UPLOAD_FAILS') . "');window.history.back(-1);</script>");
		} else {
			chmod($newfile, 0777);
		}

		// get the start coordinates..


		$gpsData = new gpsDataClass("Kilometer");// default unit

		$gpsData = $cache->get(array ( $gpsData, 'loadFileAndData' ), array ($newfile, strtolower($filename)), "Kilometer");
		if ($gpsData->displayErrors())
		{
		   $map = "";
		   $coords = "";
		   $distance_float = 0;
		   $distance = 0;
		   // try to delete the file
		   if ( JFile::exists($upload_dir.strtolower($filename)))
		    {
			JFile::delete($upload_dir.strtolower($filename));
		    }
		   echo "<script type='text/javascript'>alert('".JText::_('COM_JTG_NO_SUPPORT') . "');window.history.back(-1);</script>";
		   exit;
		}

		$start_n = $gpsData->start[1];
		$start_e = $gpsData->start[0];
		$coords = $gpsData->allCoords;
		$isTrack = $gpsData->isTrack;
		$isWaypoint = $gpsData->isWaypoint;
		$isRoute = 0;
		$isCache = 0;

		$distance = $gpsData->distance;
		// 	 Na und was ist mit Wegpunkten?
		//		 if($distance == NULL) {
		//			 echo "<script type='text/javascript'>alert('" . $distance . "');window.history.back(-1);</script>";
		//			 exit;
		//		 }

		$query = "INSERT INTO #__jtg_files SET"
		. "\n uid='" . $uid . "',"
		. "\n catid='" . $catid . "',"
		. "\n title='" . $title . "',"
		. "\n file='".strtolower($filename) . "',"
		. "\n terrain='" . $terrain . "',"
		. "\n description=" . $desc . ","
		. "\n published='" . $published . "',"
		. "\n date='" . $date . "',"
		. "\n start_n='" . $start_n . "',"
		. "\n start_e='" . $start_e . "',"
		. "\n distance='" . $distance . "',"
		. "\n ele_asc='" . round($gpsData->totalAscent,0) . "',"
		. "\n ele_desc='" . round($gpsData->totalDescent,0) . "',"
		. "\n level='" . $level . "',"
		. "\n access='" . $access . "',"
		. "\n hidden='" . $hidden . "',"
		. "\n istrack='" . $isTrack . "',"
		. "\n iswp='" . $isWaypoint . "',"
		. "\n isroute='" . $isRoute . "',"
		. "\n iscache='" . $isCache . "'";
		;

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		$query = "SELECT id FROM #__jtg_files WHERE file='".strtolower($filename) . "'";

		$db->setQuery($query);
		$rows = $db->loadObject();

		// images upload part
		$cfg = JtgHelper::getConfig();
		$types = explode(',',$cfg->type);
		if(count($images) > 0 ) {
			$img_dir = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'track_' .  $rows->id . DIRECTORY_SEPARATOR;
			JFolder::create($img_dir,0777);
			foreach($images['name'] as $key => $value)
			{
				if($value != "")
				{
					$imgfilename = JFile::makesafe($value);
					$ext = JFile::getExt($images['name'][$key]);
					if(in_array(strtolower($ext),$types))
					{
						JtgHelper::createimageandthumbs($images['tmp_name'][$key], $ext,$img_dir, $imgfilename);
					}
				}

			}
		}
		return true;

	}

	/**
	 *
	 * @global <type> $mainframe
	 * @return <type>
	 */
	function hit()
	{
		$mainframe =& JFactory::getApplication();

		$id =& JRequest::getInt('id');

		if ($id)
		{
			$tracks = & $this->getTable('jtg_files', 'Table');
			$tracks->hit($id);
			return true;
		}
		return false;
	}

	/**
	 *
	 * @global <type> $mainframe
	 * @param <type> $id
	 * @return <type>
	 */
	function getFile($id) {
		$mainframe =& JFactory::getApplication();

		$db =& JFactory::getDBO();

		$query = "SELECT a.*, b.title AS cat, b.image AS image, c.username AS user"
		. "\n FROM #__jtg_files AS a"
		. "\n LEFT JOIN #__jtg_cats AS b ON a.catid=b.id"
		. "\n LEFT JOIN #__users AS c ON a.uid=c.id"
		. "\n WHERE a.id='" .$id. "'";

		$db->setQuery($query);
		$result = $db->loadObject();
		if (!$result)
		return JTable::getInstance('jtg_files', 'table');
		return $result;
	}

	/**
	 *
	 * @global <type> $mainframe
	 * @param <type> $id
	 * @return <type>
	 */
	function getVotes($id) {
		$mainframe =& JFactory::getApplication();

		$class = array('nostar', 'onestar', 'twostar', 'threestar', 'fourstar', 'fivestar',
		'sixstar', 'sevenstar', 'eightstar', 'ninestar', 'tenstar');

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

		// fetch rating
		$rate = null;
		if ( $count != 0 ) {
			while ( $rate === null ) {
				$query = "SELECT vote FROM #__jtg_files"
				. "\n WHERE id='" .$id. "'";

				$db->setQuery($query);
				$rate = $db->loadResult();
				if ($rate === null) {
					$newvote = (float) ( round( ( $givenvotes / $count ), 3 ) );
					$query = "UPDATE #__jtg_files SET"
					. " vote='" .$newvote . "'"
					. " WHERE id='" .$id . "'"
					;
					$db->setQuery($query);
					if(!$db->query()) {
						echo ($db->stderr());
						return false;
					}
				}
			}
		}
		else
		{ // save voting: 0
			$query = "UPDATE #__jtg_files SET"
			. " vote='0'"
			. " WHERE id='" .$id . "'"
			;
			$db->setQuery($query);
			if(!$db->query()) {
				echo ($db->stderr());
				return false;
			}
		}

		$object = array("count" => $count,
						"rate" => (float)$rate,
						"sum" => (int)$givenvotes,
						"class" => $class[(int)round($rate,0)]);
		return $object;
	}

	/**
	 *
	 * @global <type> $mainframe
	 * @param <type> $id
	 * @param <type> $rate
	 * @return <type>
	 */
	function vote($id, $rate) {
		if($id && $rate):
		$givevotes = $this->getVotes($id);

		$mainframe =& JFactory::getApplication();

		$db =& JFactory::getDBO();

		$query = "INSERT INTO #__jtg_votes SET"
		. "\n trackid='" .$id . "',"
		. "\n rating='" .$rate . "'"
		;
		$db->setQuery($query);
		if(!$db->query()) {
			echo ($db->stderr());
			return false;
		}

		// count
		$count = (int)$givevotes['count'];
		$sum = (int)$givevotes['sum'];

		$newvote = (float)( round( ( ( $sum + $rate ) / ( $count + 1 ) ), 3 ) );

		$query = "UPDATE #__jtg_files SET"
		. " vote='" .$newvote . "'"
		. " WHERE id='" .$id . "'"
		;
		$db->setQuery($query);
		if(!$db->query()) {
			echo ($db->stderr());
			return false;
		}

		return true;
		endif;
		return false;
	}

	function deleteFile($id) {
		$mainframe =& JFactory::getApplication();
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__jtg_files WHERE id='" . $id . "'";
		$this->_db->setQuery($query);
		$file = $this->_db->loadObject();
		// folder and Pictures within delete
		$folder = JPATH_SITE . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "jtrackgallery" . DIRECTORY_SEPARATOR . 'track_' . $id;
		if (JFolder::exists($folder))
		JFolder::delete($folder);
		// File (gpx?) delete
		$filename = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'uploaded_tracks' . DIRECTORY_SEPARATOR . $file->file;
		if (JFile::exists($filename))
		JFile::delete($filename);
		// delete from DB
		$query = "DELETE FROM #__jtg_files"
		. "\n WHERE id='" . $id . "'";
		$db->setQuery($query);
		if($db->query()) {
			return true;
		} else {
			return false;
		}
	}

	function getImages($id) {
		$img_dir = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'track_' . $id;
		if (!JFolder::exists($img_dir))
		return null;
		$images = JFolder::files($img_dir);
		return $images;

	}

	function updateFile($id) {
		$mainframe =& JFactory::getApplication();

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();

		// get the post data
		$catid =& JFactory::getApplication()->input->get('catid', NULL,'array');
		if ( $catid !== null )
		$catid = implode(",",$catid);
		else
		$catid = "";
		$level =& JRequest::getInt('level');
		$title =& JFactory::getApplication()->input->get('title');
		$allimages = $this->getImages($id);
		$imgpath = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'track_' . $id. DIRECTORY_SEPARATOR;
		foreach ($allimages AS $key => $image)
		{
			$image =& JFactory::getApplication()->input->get('deleteimage_'.str_replace('.',null,$image));
			if($image !== NULL)
			{
			    JFile::delete($imgpath.$image);
			    // delete thumbnails too
			    JFile::delete($imgpath. 'thumbs' . DIRECTORY_SEPARATOR. 'thumb0_' . $image);
			    JFile::delete($imgpath. 'thumbs' . DIRECTORY_SEPARATOR. 'thumb1_' . $image);
			    JFile::delete($imgpath. 'thumbs' . DIRECTORY_SEPARATOR. 'thumb2_' . $image);
			}
		}
		$terrain =& JFactory::getApplication()->input->get('terrain', NULL, 'array');
		if ($terrain)
		$terrain = implode(', ', $terrain);
		else
		$terrain = "";
		$desc = & $db->quote(JFactory::getApplication()->input->get('description', '', 'post', 'string', JREQUEST_ALLOWRAW));
		$images =& JFactory::getApplication()->input->get('images', null, 'files', 'array');
		$access =& JRequest::getInt('access', 0);
		$hidden =& JRequest::getInt('hidden', 0);
		$published =& JRequest::getInt('published', 0);
		//		if($images["tmp_name"][0] == "") return "no tempname";

		// images upload part
		$cfg = JtgHelper::getConfig();
		$types = explode(',',$cfg->type);

		if($images)
		{
			if(!JFolder::exists($imgpath))
			{
				JFolder::create($imgpath,0777);
			}
			foreach($images['name'] as $key => $value) {
				if ($value)
				{
					$ext = JFile::getExt($images['name'][$key]);
						if(in_array(strtolower($ext), $types))
					{
						JtgHelper::createimageandthumbs($images['tmp_name'][$key], $ext, $imgpath , $images['name'][$key]);
					}
				}
			}
		}
		$query = "UPDATE #__jtg_files SET"
		. "\n catid='" . $catid . "',"
		. "\n title='" . $title . "',"
		. "\n terrain='" . $terrain . "',"
		. "\n description=" . $desc . "," // $desc is quoted
		. "\n level='" . $level . "',"
		. "\n hidden='" . $hidden . "',"
		. "\n published='" . $published . "',"
		. "\n access='" . $access . "'"
		. "\n WHERE id='" . $id . "'"
		;

		$db->setQuery($query);
		$db->query();

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return "database not saved";
		} else {
			return true;
		}
	}

	/**
	 *
	 * @global object $mainframe
	 * @return array
	 */
	function getTerrain($where = null) {
		$mainframe =& JFactory::getApplication();

		$db = JFactory::getDBO();

		//		$query = "SELECT * FROM #__jtg_terrains ORDER BY ordering,title ASC";
		$query = "SELECT * FROM #__jtg_terrains " . $where . " ORDER BY title ASC";

		$db->setQuery($query);
		$row = $db->loadObjectList();
		$terrain = array();
		if($row)
		{
			foreach ($row as $v) {
				$v->title = JText::_($v->title);
				$terrain[] = $v;
			}
		}

		return $terrain;
	}

	/**
	 *
	 * @global object $mainframe
	 * @param int $id
	 * @param   string  $order
	 * @return array
	 */
	function getComments($id, $order) {
		$mainframe =& JFactory::getApplication();

		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__jtg_comments WHERE"
		. "\n tid='" . $id . "'"
		. "\n AND published='1'"
		. "\n ORDER BY date " . $order
		;
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 *
	 * @param object $cfg
	 */
	function addcomment($cfg) {
		JHtml::_('behavior.formvalidation');
		$editor =& JFactory::getEditor('tinymce');
		$editor_params = array('theme' => 'simple');
		$user =& JFactory::getUser();
		$id = JRequest::getInt('id');
		?>
<script language="javascript">
		Joomla.myValidate = function(f) {
				if (document.formvalidator.isValid(f)) {
						f.check.value='<?php echo JSession::getFormToken(); ?>';//send token
						return true;
				}
				else {
						alert('<?php echo JText::_('COM_JTG_FILLOUT'); ?>');
				}
				return false;
		}
		</script>

<form class='form-validate' id='adminform' name='adminform'
	action='index.php?option=com_jtg' method='post'
	onSubmit='return myValidate(this);'>
<table class='comment-form'>
	<thead>
		<tr>
			<th colspan='2'><b><?php echo JText::_('COM_JTG_WRITE_COMMENT'); ?></b></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><label for='name'><?php echo JText::_('COM_JTG_NAME'); ?>*</label></td>
			<td><input type='text' name='name' id='name' size='20'
				value='<?php echo $user->get('name'); ?>' class='required'
				maxlength='50' /></td>
		</tr>
		<tr>
			<td><label for='email'><?php echo JText::_('COM_JTG_EMAIL'); ?>*</label></td>
			<td><input type='text' name='email' id='email' size='30'
				value='<?php echo $user->get('email'); ?>'
				class='required validate-email' maxlength='50' /></td>
		</tr>
		<tr>
			<td><label for='homepage'><?php echo JText::_('COM_JTG_INFO_AUTHOR_WWW'); ?></label></td>
			<td><input type='text' name='homepage' id='homepage' size='30'
				maxlength='50' /></td>
		</tr>
		<tr>
			<td><label for='title'><?php echo JText::_('COM_JTG_TITLE'); ?>*</label></td>
			<td><input type='text' name='title' id='title' size='40' value=''
				class='required' maxlength='80' /></td>
		</tr>
		<tr>
			<td><label for='text'><?php echo JText::_('COM_JTG_TEXT'); ?>*</label></td>
			<td><?php echo $editor->display('text', '', '400', '100', '10', '10', false, $editor_params); ?></td>
		</tr>
		<?php if($cfg->captcha == 1): ?>
		<tr>
			<td><img src='<?php echo JRoute::_("index.php?option=com_jtg&task=displayimg",false); ?>'></td>
			<td><input type="text" name="word" value="" size="10"
				class="required" /> <?php echo JText::_('COM_JTG_CAPTCHA_INFO'); ?></td>
		</tr>
		<?php endif; ?>
		<tr>
			<td colspan='2' align='right'><input type='submit' value='<?php echo JText::_('COM_JTG_SEND')?>'
				name='submit' class='button' /></td>
		</tr>
	</tbody>
</table>
		<?php echo JHtml::_( 'form.token' ) . "\n"; ?> <input type='hidden'
	name='controller' value='files' /> <input type='hidden' name='task'
	value='savecomment' /> <input type='hidden' name='id'
	value='<?php echo $id; ?>' /></form>
		<?php
	}

	/**
	 *
	 * @global object $mainframe
	 * @param int $id
	 * @return boolean
	 */
	function savecomment($id, $cfg) {
		$mainframe =& JFactory::getApplication();

		$name =& JFactory::getApplication()->input->get('name');
		$email =& JFactory::getApplication()->input->get('email', '', 'Raw');
		$homepage =& JFactory::getApplication()->input->get('homepage');
		$title =& JFactory::getApplication()->input->get('title');
		$text =& JFactory::getApplication()->input->get('text', '', 'post', 'string', JREQUEST_ALLOWRAW);
		if ($text=="") return false;

		$db =& JFactory::getDBO();
		$query = "INSERT INTO #__jtg_comments SET"
		. "\n tid='" . $id . "',"
		. "\n user='" . $name . "',"
		. "\n email='" . $email . "',"
		. "\n homepage='" . $homepage . "',"
		. "\n title='" . $title . "',"
		. "\n text='" . $text . "',"
		. "\n published='1'"
		;

		$db->setQuery($query);
		$db->query();

		// send autor email if set
		if($cfg->inform_autor == 1) {
			$mailer = JFactory::getMailer();
			$config = JFactory::getConfig();
			$sender = array(
			    $config->getValue( 'config.mailfrom' ),
			    $config->getValue( 'config.fromname' ) );
			$mailer->setSender($sender);
			$user = JFactory::getUser();
			$recipient = $user->email;
			$mailer->addRecipient($recipient);
			$link = JUri::base() . "index.php?option=com_jtg&view=files&layout=file&id=" . $id;
			$msg = JText::_('COM_JTG_CMAIL_MSG');
			$body   = sprintf($msg, $link);
			$mailer->setSubject(JText::_('COM_JTG_CMAIL_SUBJECT'));
			$mailer->setBody($body);
			// Optional file attached
			$mailer->addAttachment(JPATH_COMPONENT.'/assets/document.pdf');
/*
jimport('joomla.mail.helper');
			$config = JFactory::getConfig();
			$autor = $this->getAutorData($id);
			$email = $autor->email;
		    $from =$config->getValue( 'config.mailfrom' );
		    $sender =$config->getValue( 'config.fromname' );
			$link = JUri::base() . "index.php?option=com_jtg&view=files&layout=file&id=" . $id;
			$msg = JText::_('COM_JTG_CMAIL_MSG');
			$body = sprintf($msg, $link);
			$subject = JText::_('COM_JTG_CMAIL_SUBJECT');

			// clean the email data
			$subject = JMailHelper::cleanSubject($subject);
			$body = JMailHelper::cleanBody($body);
			$sender = JMailHelper::cleanAddress($sender);

			JMail::sendMail($from, $sender,$email,$subject,$body);
*/
			$send = $mailer->Send();
			if ( $send !== true ) {
				echo 'Error sending email: ' . $send->__toString();
			}
		}

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		} else {
			return true;
		}
	}

	function getAutorData($id) {
		$mainframe =& JFactory::getApplication();

		$db = JFactory::getDBO();
		$query = "SELECT a.uid, b.name, b.email FROM #__jtg_files AS a"
		. "\n LEFT JOIN #__users AS b ON a.uid=b.id"
		. "\n WHERE a.id='" . $id . "'";

		$db->setQuery($query);
		$user = $db->loadObject();

		return $user;

	}

	/**
	 * Gives back lat/lon from start (if given) and endpoint to make an approachlink
	 * Homepage: http://openrouteservice.org/
	 * WIKI: http://wiki.openstreetmap.org/wiki/OpenRouteService
	 *
	 * @param lat
	 * @param lon
	 * @return array
	 **/
	function approachors($to_lat,$to_lon,$lang) {
		$user = JFactory::getUser();
		$latlon = JtgHelper::getLatLon($user->id);
		$link = "http://openrouteservice.org/?";
		if(isset($latlon[0])) {
			$middle_lon = ((float)$to_lon + (float)$latlon[0]->jtglon) / 2;
			$middle_lat = ((float)$to_lat + (float)$latlon[0]->jtglat) / 2;
			$link .= "start=" . $latlon[0]->jtglon . "," . $latlon[0]->jtglat . "&amp;end=" . $to_lon . "," . $to_lat . "&amp;lat=" . $middle_lat . "&amp;lon=" . $middle_lon;
		} else
		$link .= "end=" . $to_lon . "," . $to_lat;
		return $link . "&amp;lang=" . $lang . "&amp;pref=";
	}

	/**
	 * Gives back lat/lon from start (if given) and endpoint to make an approachlink
	 * Homepage: http://maps.cloudmade.com/
	 * WIKI: http://wiki.openstreetmap.org/wiki/CloudMade
	 *
	 * @param   string  Absolute Link to ORS
	 * @param   string  lat
	 * @param   string  lon
	 * @return array
	 **/
	function approachcm($to_lat,$to_lon,$lang) {
		$link = "http://maps.cloudmade.com/?";
		$user = JFactory::getUser();
		$latlon = JtgHelper::getLatLon($user->id);
		if(isset($latlon[0])) {
			if ($latlon[0]->jtglat) $from_lat = $latlon[0]->jtglat;
			if ($latlon[0]->jtglon) $from_lon = $latlon[0]->jtglon;
		}
		if (isset($from_lon) && isset($from_lat)){
			$middle_lon = ((float)$to_lon + (float)$from_lon) / 2;
			$middle_lat = ((float)$to_lat + (float)$from_lat) / 2;
			$link .= "lat=" . $middle_lat . "&amp;";
			$link .= "lng=" . $middle_lon . "&amp;";
			$link .= "directions=" . $from_lat . "," . $from_lon;
			$link .= "," . $to_lat . "," . $to_lon . "&amp;zoom=16";
		} else {
			$link .= "directions=" . $to_lat . "," . $to_lon . "&amp;";
			$link .= "lat=" . $to_lat . "&amp;";
			$link .= "lng=" . $to_lon . "&amp;";
			$link .= "zoom=15";
		}
		return $link . "&amp;styleId=1&amp;opened_tab=1&amp;travel=";
	}

	/**
	 * Gives back lat/lon from start (if given) and endpoint to make an approachlink
	 * Homepage: http://maps.cloudmade.com/
	 * WIKI: http://wiki.openstreetmap.org/wiki/CloudMade
	 *
	 * @param   string  Absolute Link to ORS
	 * @param   string  lat
	 * @param   string  lon
	 * @return array
	 **/
	function approachcmkey($to_lat,$to_lon,$lang) {
		$key="651006379c18424d8b5104ed4b7dc210";
		$link = "http://navigation.cloudmade.com/" . $key . "/api/0.3/";
		$user = JFactory::getUser();
		$latlon = JtgHelper::getLatLon($user->id);
		if(isset($latlon[0])) {
			if ($latlon[0]->jtglat) $from_lat = $latlon[0]->jtglat;
			if ($latlon[0]->jtglon) $from_lon = $latlon[0]->jtglon;
		}
		if (isset($from_lon) && isset($from_lat)){
			$middle_lon = ((float)$to_lon + (float)$from_lon) / 2;
			$middle_lat = ((float)$to_lat + (float)$from_lat) / 2;
			$link .= "directions=" . $from_lat . "," . $from_lon.
				"," . $to_lat . "," . $to_lon . "&amp;".
				"lat=" . $middle_lat . "&amp;".
				"lng=" . $middle_lon;
		} else {
			$link .= "directions=" . $to_lat . "," . $to_lon.
				"lat=" . $to_lat . "&amp;".
				"lng=" . $to_lon . "&amp;zoom=15";
		}
		return $link . "&amp;zoom=15&amp;travel=";
	}

	function parseHomepageIcon($www) {
		if ((!preg_match('/http\:\/\//',$www))AND(!preg_match('/https\:\/\//',$www)))
		$www = "http://" . $www;
		$cfg = JtgHelper::getConfig();
		$return = "<a target=\"_blank\" href=\"" . $www . "\"><img src=\"" .
		JUri :: base() .
		"components/com_jtg/assets/template/" .
		$cfg->template .
		"/images/weblink.png\" /></a>";
		return $return;
	}

	function parseEMailIcon($mail) {
		$cfg = JtgHelper::getConfig();
		$link = "<img src=\"" . JUri :: base() .
		"components/com_jtg/assets/template/" . $cfg->template . "/images/emailButton.png\" />";

		$return = JHtml::_('email.cloak', $mail, true, $link, 0);

		return $return;
	}

}
