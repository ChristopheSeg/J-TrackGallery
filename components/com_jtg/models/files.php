<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooosm and joomGPStracks teams
 * @package    com_jtg
 * @subpackage frontend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

class JtgModelFiles extends JModel
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

		if(JRequest::getVar('layout') == 'user' && !$where) {
			$userwhere = " WHERE uid='" . $user->get('id') . "'";
		} else if(JRequest::getVar('layout') == 'user' && $where) {
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

		$search =& JRequest::getVar('search');
		$cat =& JRequest::getVar('cat');
		$terrain =& JRequest::getVar('terrain');
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
		$list = JHTML::_('menu.treerecurse', 0, '', array(), $children );
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

		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();

		// get the post data
		$catid =& JRequest::getVar('catid');
		if ( $catid !== null )
		$catid = implode(",",$catid);
		else
		$catid = "";
		$level =& JRequest::getInt('level');
		$title =& JRequest::getVar('title');
		$terrain =& JRequest::getVar('terrain');
		if($terrain != NULL)
		$terrain = implode(', ', $terrain);
		else
		$terrain = "";
		$desc =& JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$file =& JRequest::getVar('file', null, 'files', 'array');
		$uid = $user->get('id');
		$date = date("Y-m-d");
		$images =& JRequest::getVar('images', null, 'files', 'array');
		$access =& JRequest::getInt('access', 0);
		$hidden =& JRequest::getInt('hidden', 0);
		$published =& JRequest::getInt('published', 0);

		// upload the file
		$upload_dir = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'uploaded_tracks' . DS;
		$filename = JFile::makeSafe($file['name']);
		echo '<pre>';print_r($file);echo'</pre>';
		echo '<br>$upload_dir.strtolower($filename = '.$upload_dir.strtolower($filename);
		echo '<br>$file[\'tmp_name\'] = '.$file['tmp_name'];
		echo "TODOPRINT";
		if ( JFile::exists($upload_dir.strtolower($filename)))
		{
		    die("<script type='text/javascript'>alert('".JText::sprintf("COM_JTG_FILE_ALREADY_EXISTS",$filename) . "');window.history.back(-1);</script>"); 
		}
		if (!JFile::upload($file['tmp_name'], $upload_dir.$filename)) {
			die("<script type='text/javascript'>alert('".JText::_('COM_JTG_UPLOAD_FAILS') . "');window.history.back(-1);</script>");
		} else {
			chmod($upload_dir.strtolower($filename), 0777);
		}

		// get the start coordinates
		$gps = new gpsClass();
		$gps->gpsFile = DS . 'images' . DS . 'jtrackgallery' . DS . 'uploaded_tracks' . DS . strtolower($filename);
		if(!$start = $gps->getStartCoordinates()) {
			echo "<script type='text/javascript'>alert('".JText::_('COM_JTG_NO_SUPPORT') . "');window.history.back(-1);</script>";
			exit;
		}

		$file = DS . 'images' . DS . 'jtrackgallery' . DS . 'uploaded_tracks' . DS . strtolower($filename);
		$start_n = $start[1];
		$start_e = $start[0];
		$coords = $gps->getCoords($file);
		$isTrack = $gps->isTrack();
		if ($isTrack !== false) $isTrack = "1"; else $isTrack = "0";
		$isWaypoint = $gps->isWaypoint();
		if ($isWaypoint !== false) $isWaypoint = "1"; else $isWaypoint = "0";
		$isRoute = 0;
		$isCache = 0;
//		$isCache = $gps->isCache();
//		if ($isCache !== false) $isCache = "1"; else $isCache = "0";
		$distance = $gps->getDistance($coords);
		// 	 Na und was ist mit Wegpunkten?
		//		 if($distance == NULL) {
		//			 echo "<script type='text/javascript'>alert('" . $distance . "');window.history.back(-1);</script>";
		//			 exit;
		//		 }

		// call the elevation function
		$ele = $gps->getElevation($coords);

		$query = "INSERT INTO #__jtg_files SET"
		. "\n uid='" . $uid . "',"
		. "\n catid='" . $catid . "',"
		. "\n title='" . $title . "',"
		. "\n file='".strtolower($filename) . "',"
		. "\n terrain='" . $terrain . "',"
		. "\n description='" . $desc . "',"
		. "\n published='" . $published . "',"
		. "\n date='" . $date . "',"
		. "\n start_n='" . $start_n . "',"
		. "\n start_e='" . $start_e . "',"
		. "\n distance='" . $distance . "',"
		. "\n ele_asc='" . $ele[0] . "',"
		. "\n ele_desc='" . $ele[1] . "',"
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
		} else {
			$query = "SELECT id FROM #__jtg_files WHERE file='".strtolower($filename) . "'";

			$db->setQuery($query);
			$rows = $db->loadObject();

			// images upload part
			$cfg = JtgHelper::getConfig();
			$types = explode(',',$cfg->type);
			if(count($images) > 0 ) {
				$img_dir = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . $rows->id;
				JFolder::create($img_dir,0777);
				foreach($images['name'] as $key => $value) {
					$ext = explode('.',$images['name'][$key]);
					if(in_array($ext[1], $types)) {
						$path = $img_dir . DS . strtolower($images['name'][$key]);
						(JtgHelper::createImage($images['tmp_name'][$key], $ext[1], $path));
					}
				}
			}

			return true;
		}
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
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__jtg_files WHERE id='" . $id . "'";
		$this->_db->setQuery($query);
		$file = $this->_db->loadObject();
		// folder and Pictures within delete
		$folder = JPATH_SITE . DS . "images" . DS . "jtrackgallery" . DS . $id;
		if (JFolder::exists($folder))
		JFolder::delete($folder);
		// File (gpx?) delete
		$filename = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'uploaded_tracks' . DS . $file->file;
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
		$img_dir = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . $id;
		if (!JFolder::exists($img_dir))
		return null;
		$images = JFolder::files($img_dir);
		return $images;

	}

	function updateFile($id) {
		$mainframe =& JFactory::getApplication();

		jimport('joomla.filesystem.file');

		$db =& JFactory::getDBO();
		$user =& JFactory::getUser();

		// get the post data
		$catid =& JRequest::getVar('catid');
		$catid = implode(",",$catid);
		$level =& JRequest::getInt('level');
		$title =& JRequest::getVar('title');
		$allimages = $this->getImages($id);
		$imgpath = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . $id.DS;
		foreach ($allimages AS $key => $image) {
			$image =& JRequest::getVar('deleteimage_'.str_replace('.',null,$image));
			if($image !== NULL)
			JFile::delete($imgpath.$image);
		}
		$terrain =& JRequest::getVar('terrain');
		if ($terrain)
		$terrain = implode(', ', $terrain);
		else
		$terrain = "";
		$desc =& mysql_real_escape_string(JRequest::getVar( 'description', '', 'post', 'string', JREQUEST_ALLOWRAW));
		$images =& JRequest::getVar('images', null, 'files', 'array');
		$access =& JRequest::getInt('access', 0);
		$hidden =& JRequest::getInt('hidden', 0);
		$published =& JRequest::getInt('published', 0);
		//		if($images["tmp_name"][0] == "") return "no tempname";

		// images upload part
		$cfg = JtgHelper::getConfig();
		$types = explode(',',$cfg->type);
		if($images) {
			$img_dir = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . $id;
			if(!JFolder::exists($img_dir)) {
				JFolder::create($img_dir,0777);
			}
			foreach($images['name'] as $key => $value) {
				if ($value) {
					$ext = explode('.',$images['name'][$key]);
					if(in_array(strtolower($ext[1]), $types)) {
						$path = $img_dir . DS . strtolower($images['name'][$key]);
						(JtgHelper::createImage($images['tmp_name'][$key], $ext[1], $path));
					}
				}
			}
		}

		$query = "UPDATE #__jtg_files SET"
		. "\n catid='" . $catid . "',"
		. "\n title='" . $title . "',"
		. "\n terrain='" . $terrain . "',"
		. "\n description='" . $desc . "',"
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
	 * @param string $order
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
		JHTML::_('behavior.formvalidation');
		$editor =& JFactory::getEditor('tinymce');
		$editor_params = array('theme' => 'simple');
		$user =& JFactory::getUser();
		$id = JRequest::getInt('id');
		?>
<script language="javascript">
		Joomla.myValidate = function(f) {
				if (document.formvalidator.isValid(f)) {
						f.check.value='<?php echo JUtility::getToken(); ?>';//send token
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
		<?php echo JHTML::_( 'form.token' ) . "\n"; ?> <input type='hidden'
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

		$name =& JRequest::getVar('name');
		$email =& JRequest::getVar('email');
		$homepage =& JRequest::getVar('homepage');
		$title =& JRequest::getVar('title');
		$text =& JRequest::getVar( 'text', '', 'post', 'string', JREQUEST_ALLOWRAW);
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
			jimport('joomla.mail.helper');
			$jcfg = JFactory::getConfig();
			$autor = $this->getAutorData($id);
			$jcfg = $jcfg->_registry['config']['data'];
			$email = $autor->email;
			$from = $jcfg->mailfrom;
			$sender = $jcfg->fromname;
			$link = JURI::base() . "index.php?option=com_jtg&view=files&layout=file&id=" . $id;
			$msg = JText::_('COM_JTG_CMAIL_MSG');
			$body = sprintf($msg, $link);
			$subject = JText::_('COM_JTG_CMAIL_SUBJECT');

			// clean the email data
			$subject = JMailHelper::cleanSubject($subject);
			$body = JMailHelper::cleanBody($body);
			$sender = JMailHelper::cleanAddress($sender);

			JUtility::sendMail($from, $sender,$email,$subject,$body);
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
	 * @param string Absolute Link to ORS
	 * @param string lat
	 * @param string lon
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
	 * @param string Absolute Link to ORS
	 * @param string lat
	 * @param string lon
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
		JURI :: base() .
		"components/com_jtg/assets/template/" .
		$cfg->template .
		"/images/weblink.png\" /></a>";
		return $return;
	}

	function parseEMailIcon($mail) {
		$cfg = JtgHelper::getConfig();
		$return = JHTML::_('email.cloak', $mail, true, "<img src=\"" .
		JURI :: base() .
		"components/com_jtg/assets/template/" .
		$cfg->template .
		"/images/emailButton.png\" />",0);
		return $return;
	}

}
