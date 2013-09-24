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

// No direct access
defined('_JEXEC') or die('Restricted access');

function jtgdebug($val, $die=false) {
	$r = "<pre>";
	if ( is_array($val) )
	{
		foreach ($val AS $k => $v)
		{
			$r .= $k . " = " . print_r($v,true) . "<br />\n";
		}
	}
	else
	$r .= print_r($val) . "<br />\n";
	$r .= "</pre>";
	if ( $die !== false ) die($r);
	echo $r;
	// if ( JDEBUG )
}

class JtgHelper
{
	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public static function addSubmenu($vName)
	{
		// TODO move addSubmenu and GetConfig function to backend code
		JSubMenuHelper::addEntry(   
			JText::_('COM_JTG_CONFIGURATION'),
			'index.php?option=com_jtg&task=config&controller=config',
			$vName == 'config'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JTG_FILES'),
			'index.php?option=com_jtg&task=files&controller=files',
			$vName == 'config'
		);		
		JSubMenuHelper::addEntry(
			JText::_('COM_JTG_MAPS'),
			'index.php?option=com_jtg&task=maps&controller=maps',
			$vName == 'config'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JTG_CATEGORIES'),
			'index.php?option=com_jtg&task=cats&controller=cats',
			$vName == 'config'
		);		
		JSubMenuHelper::addEntry(
			JText::_('COM_JTG_TERRAIN'),
			'index.php?option=com_jtg&task=terrain&controller=terrain',
			$vName == 'config'
		);		
		JSubMenuHelper::addEntry(
			JText::_('COM_JTG_COMMENTS'),
			'index.php?option=com_jtg&task=comments&controller=comments',
			$vName == 'config'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_JTG_INFO'),
			'index.php?option=com_jtg&task=info&controller=info',
			$vName == 'config'
		);

		JSubMenuHelper::addEntry(
			JText::_('COM_JTG_TRANSLATE'),
			'index.php?option=com_jtg&task=translations&controller=translations',
			$vName == 'config'
		);

		// Groups and Levels are restricted to core.admin
		// $canDo = self::getActions(); ... 

	}
	public function howMuchVote($tid) {
		$db =& JFactory::getDBO();
		$query = "SELECT COUNT(id) FROM #__jtg_votes"
		. "\n WHERE trackid='" .$tid. "'";
		$db->setQuery($query);
		return (int)$db->loadResult();
	}

	public function giveGeneratedValues($surface,$filetypes,$track) {
		switch ($surface) {
			case 'backend':
				break;

			case 'frontend':
				break;

			default:
				return "No frontend|backend given!";
				break;
		}
		if ( ( $track->start_n == null ) OR ( $track->start_e == null ) )
		{
			$error = "<font color=red> (" . JText::_('Error') . ")</font> ";
			$north = 0;
			$east = 0;
			$osm = null;
		}
		else
		{
			$error = null;
			$north = $track->start_n;
			$east = $track->start_e;
			$osm = " <a href='http://www.openstreetmap.org/?mlat=" . $north . "&mlon=" . $east . "&zoom=18' target='_blank' >OpenStreetMap</a>";
		}
		$values = JText::_('COM_JTG_COORDS') . $error . ": " . $north . ", " . $east . $osm;
		$distance = (float)$track->distance;
		if ( $distance != 0 )
		{
			$km = JtgHelper::getLocatedFloat($distance);
			$miles = JtgHelper::getMiles($distance);
			$miles = JtgHelper::getLocatedFloat($miles);
			$distance =  $km . " Km (" . $miles . " Miles)";
		}
		else
		{
			$distance = 0;
		}
		$distance = JText::_('COM_JTG_DISTANCE') . ": " . $distance;
		$ele_asc = JText::_('COM_JTG_ELEVATION_UP') . ": " . (float)$track->ele_asc;
		$ele_desc = JText::_('COM_JTG_ELEVATION_DOWN') . ": " . (float)$track->ele_desc;
		$voted = JtgHelper::howMuchVote($track->id);
		if ( ( $voted != 0 ) AND ( (float)$track->vote == 0 ) )
		{
			// Wenn gevoted wurde aber Voting gleich 0
			// If voted but voting = 0
			$error = "<font color=red> (" . JText::_('Error') . "?)</font> ";
		}
		else
		{
			$error = null;
		}
		$voted = JText::sprintf('COM_JTG_MENU_LIMIT_CONSTRUCT_VOTED', $voted) . $error;
		$vote = (float)$track->vote;
		$vote = JtgHelper::getLocatedFloat($vote);
		$vote = JText::sprintf('COM_JTG_MENU_LIMIT_CONSTRUCT_VOTE', $vote) . $error;
		$button = "<button class=\"button\" type=\"button\" onclick=\"submitbutton('updateGeneratedValues')\">" . JText::_('COM_JTG_REFRESH_DATAS') . "</button>";
		return "<ul><li>"
			. $filetypes
			. "</li><li>"
			. $values
			. "</li><li>"
			. $distance
			. "</li><li>"
			. $ele_asc
			. "</li><li>"
			. $ele_desc
			. "</li><li>"
			. $vote
			. "</li><li>"
			. $voted
			. "</li></ul>"
			. $button;
	}

	/**
	 *
	 * @param int
	 * @return random string with given (int) lengh
	 * http://www.php.net/manual/de/function.mt-rand.php#92711
	 */
	function alphanumericPass($length) {
		$p ="";
		for ($i=0;$i<$length;$i++)
		{
			$c = mt_rand(1,4);
			switch ($c)
			{
				case ($c<=2):
					// Add a number
					$p .= mt_rand(0,9);
					break;
				case ($c<=4):
					// Add a lowercase letter
					$p .= chr(mt_rand(97,122));
					break;
			}
		}
		return $p;
	}

	function uploadfile($file, $dest) {
		if (
		( $file["error"] != 0 ) OR
		( $file["size"] == 0 )
		)
		return false;
		$filename = JFile::makeSafe($file['name']);
		$randnumber = (50-strlen($filename));
		$fncount = 0;
		while (true) {
			if (!JFile::exists($dest.$filename))
			{
				if (!JFile::upload($file['tmp_name'], $dest.$filename)) {
					return false;
				} else {
					chmod($dest.strtolower($filename), 0664);
				}
				break;
			}
			else
			{
				$randname = JtgHelper::alphanumericPass($randnumber);// $this->alphanumericPass($randnumber);
				$filename = $randname.JFile::makeSafe($file['name']);
				// Man weiß ja nie ;)
				if ( $fncount > 10000 )
				JError::raiseError(501, "<p class=\"type\">".JText::_( 'COM_JTG_ERROR_NO_FREE_FILENAMES' ) . "</p>".
				"<i>".JFile::makeSafe($file['name']) . "</i></p>");
				$fncount++;
			}
		}
		return true;
	}

	function parseMoreCats($allcats,$catid,$format="array",$link=false) {
		$baseurl = "index.php?option=com_jtg&view=files&layout=list&cat=";
		$image = JURI::base() . 'images/jtrackgallery/cats/';
		$catids = explode(",",$catid);
		$return = array();
		switch ($format) {
			case "box":
				if ( ( $link === false ) OR ( $catid == "0" ) )
				{
					foreach ($catids as $catid) {
						if ( isset($allcats[$catid]->title) )
						$return[] = JText::_($allcats[$catid]->title);
					}
				}
				else
				{
					foreach ($catids as $catid) {
						if ( isset($allcats[$catid]->id) ) {
							$url = JRoute::_($baseurl . $allcats[$catid]->id, true);
							$return[] = "<a href=\"" . $url  . "\">".
							JText::_($allcats[$catid]->title).
							"</a>";
						}
					}
				}
				$return = implode(", ",$return);
				break;
					
			case "TrackDetails":
				if ( ( $link === false ) OR ( $catid == "0" ) )
				{
					foreach ($catids as $catid) {
						if ( isset($allcats[$catid]->title) )
						$return[] = JText::_($allcats[$catid]->title);
					}
				}
				else
				{
					foreach ($catids as $catid) {
						if ( isset($allcats[$catid]->id) ) {
							$url = JRoute::_($baseurl  . $allcats[$catid]->id, true);
							if ( $allcats[$catid]->image != "")
							{
								$return[] = "<a href=\"" . $url  . "\">".
								"<img title=\"".JText::_($allcats[$catid]->title) . "\" alt=\"".JText::_($allcats[$catid]->title) . "\" src=\"" . $image.$allcats[$catid]->image . "\" />&nbsp;".
//								JText::_($allcats[$catid]->title).
								"</a>";
							}
							else
							{
								$return[] = "<a href=\"" . $url  . "\">".
								JText::_($allcats[$catid]->title).
								"</a>";
							}
						}
					}
				}
				$return = implode(", ",$return);
				break;
					
			case "Images":
				if ( ( $link === false ) OR ( $catid == "0" ) )
				{
					foreach ($catids as $catid) {
						if ( isset($allcats[$catid]->title) )
						$return[] = JText::_($allcats[$catid]->title);
					}
				}
				else
				{
					foreach ($catids as $catid) {
						if ( isset($allcats[$catid]->id) ) {
							$url = JRoute::_($baseurl . $allcats[$catid]->id, true);
							if ( $allcats[$catid]->image == "" )
							$return[] = "<a href=\"" . $url . "\">".JText::_($allcats[$catid]->title) . "</a>";
							else
							$return[] = "<a href=\"" . $url . "\"><img title=\"".JText::_($allcats[$catid]->title) . "\" alt=\"".JText::_($allcats[$catid]->title) . "\" src=\"" . $image.$allcats[$catid]->image . "\" /></a>";
						}
					}
				}
				$return = implode(", ",$return);
				break;
					
			case "list":
				if ( ( $link === false ) OR ( $catid == "0" ) )
				{
					foreach ($catids as $catid) {
						if ( isset($allcats[$catid]->title) )
						$return[] = JText::_($allcats[$catid]->title);
					}
				}
				else
				{
					foreach ($catids as $catid) {
						if ( isset($allcats[$catid])) {
							$url = JRoute::_($baseurl . $allcats[$catid]->id, true);
							if ( $allcats[$catid]->image == "" )
							$return[] = "<a href=\"" . $url . "\">".JText::_($allcats[$catid]->title) . "</a>";
							else
							$return[] = "<a href=\"" . $url . "\"><img title=\"".JText::_($allcats[$catid]->title) . "\" alt=\"".JText::_($allcats[$catid]->title) . "\" src=\"" . $image.$allcats[$catid]->image . "\" /></a>";
						}
					}
				}
				$return = implode(" ",$return);
				break;
					
			case "array":
			default:
				foreach ($catids as $catid) {
					if ( isset($allcats[$catid])) {
						$return[] = JText::_($allcats[$catid]->title);
					}
				}
				break;
		}
		return $return;
	}

	function parseMoreTerrains($allterrains,$terrainid,$format="array",$link=false) {
		$baseurl = "index.php?option=com_jtg&view=files&layout=list&terrain=";
		$image = JURI::base() . 'images' . DS . 'jtrackgallery' . DS . 'terrain' . DS;
		$terrainids = explode(",",$terrainid);
		$return = array();
		switch ($format) {
			case "list":
				if ( ( $link === false ) OR ( $terrainid == "0" ) )
				{
					foreach ($terrainids as $terrainid) {
						$return[] = JText::_($allterrains[$terrainid]->title);
					}
				}
				else
				{
					foreach ($terrainids as $terrainid) {
						if ( isset($allterrains[$terrainid]) )
						{
							$url = JRoute::_($baseurl . $allterrains[$terrainid]->id, false);
							$return[] = "<a href=\"" .$url  . "\">".
							JText::_($allterrains[$terrainid]->title).
							"</a>";
						}
					}
				}
				$return = implode(", ",$return);
				break;
			case "array":
			default:
				foreach ($terrainids as $terrainid) {
					if ( isset($allterrains[$terrainid]) )
					$return[] = JText::_($allterrains[$terrainid]->title);
				}
				break;
		}
		if ( $return == "" )
		$return = "<label title=\"".JText::_('COM_JTG_TERRAIN_NONE') . "\">-</label>";
		return $return;
	}
	
	function userHasFrontendRights() {
	    $user_groups = JFactory :: getUser()->get('groups');
	    if ( JFactory :: getUser()->get('isRoot') ) { return true;};
	    if (!$user_groups) {return false;} // seems this is never empty !!
	    $cfg_id = unserialize(JtgHelper :: getConfig()->gid);
	    foreach ($cfg_id as $group) {
		if (array_key_exists ($group , $user_groups ) ) {
		    return true;
		}
	    }
	    return  false;    
	}	    

	function getAccessList($accesslevel) {
		$access = array (
		array (
				'id' => 9,
				'text' => JText::_( 'COM_JTG_PRIVATE')
		),
		array (
				'id' => 0,
				'text' => JText::_( 'COM_JTG_PUBLIC')
		),
		array (
				'id' => 1,
				'text' => JText::_( 'COM_JTG_REGISTERED')
		),
		array (
				'id' => 2,
				'text' => JText::_( 'COM_JTG_ADMINISTRATORS')
		)
		);
		return JHTML :: _('select.genericlist', $access, 'access', 'size="4"', 'id', 'text', $accesslevel);
	}

	function giveAccessLevel() {
		$user =& JFactory::getUser();
		// Admin		24
		// Registered	18
		// Guest		0
		if (!$user->id)
		return 0;
		elseif ( $user->get('isRoot') )
		return 2;
		else // registered ($id>0)
		return 1;

	}
	/**
	 *
	 * @global object $mainframe
	 * @return object
	 */
	function getConfig()  {
		$mainframe =& JFactory::getApplication();

		$db =& JFactory::getDBO();

		$query = "SELECT * FROM #__jtg_config WHERE id='1'";

		$db->setQuery($query);
		$result = $db->loadObject();

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}

		return $result;
	}

	function checkCaptcha()  {
		$mainframe =& JFactory::getApplication();

		$db = JFactory::getDBO();

		$query ="SELECT extension_id FROM #__extensions WHERE element='captcha'";
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Fetchs lat/lon from users given ID, otherwise from all users
	 */
	function getLatLon($uid=false,$exclude=false) {
		$mainframe =& JFactory::getApplication();
		$db =& JFactory::getDBO();
		$query = "SELECT u.id,u.name,u.username,u2.jtglat,u2.jtglon,u2.jtgvisible FROM #__users as u left join #__jtg_users as u2 ON u.id=u2.user_id";
		if ($uid !== false)
		$query .= " WHERE u.id='" . $uid . "'";
		elseif ($exclude !== false)
		$query .= " WHERE u.id<>'" . $exclude . "'";
		$db->setQuery($query);
		$object = $db->loadObjectList();
		return $object;
	}

	/**
	 *
	 * @param string $distance
	 * @return string
	 */
	function getMiles($distance) {
		$miles = round($distance * 0.621, 2);
		return $miles;
	}

	/**
	 * creates the images
	 *
	 * @param string $file_tmp_name
	 * @param string $ext
	 * @param string $filepath
	 */
	function createImage($file_tmp_name, $ext, $filepath) {
		switch (strtolower($ext)) {
			case 'jpeg':
			case 'pjpeg':
			case 'jpg':
				$src = ImageCreateFromJpeg($file_tmp_name);
				break;

			case 'png':
				$src = ImageCreateFromPng($file_tmp_name);
				break;

			case 'gif':
				$src = ImageCreateFromGif($file_tmp_name);
				break;

		}
		list($width,$height)=getimagesize($file_tmp_name);
		//		$newwidth=460;//set file width to 460
		//		$newheight=($height/$width)*460;//the height are set according to ratio
		$cfg =& JtgHelper::getConfig();
		$maxfilesize = $cfg->max_size; // filesize in kb
		$maxfilesize = (int)$maxfilesize;
		$filesize = filesize($file_tmp_name)/1024; // Byte -> kb
		$maxsize = 500; // pixsize in pixel
		$resized = false;
		if (
		( $height > $maxsize ) OR
		( $width > $maxsize ) OR
		( $filesize > $maxfilesize )
		)
		// @ToDo http://www.php.net/manual/de/function.getimagesize.php#97564
		{
			if ( $height == $width ) // square
			{
				$newheight = $maxsize;
				$newwidth = $maxsize;
			}
			elseif ( $height < $width ) // landscape
			{
				$newheight = $maxsize / $width * $height;
				$newwidth = $maxsize;
			}
			else // portrait
			{
				$newheight = $maxsize;
				$newwidth = $width / $height * $newheight;
			}
			$resized = true;
			$newwidth = (int)$newwidth;
			$newheight = (int)$newheight;
		}
		else
		{
			$newwidth = (int)$width;
			$newheight = (int)$height;
		}

		$tmp=imagecreatetruecolor($newwidth,$newheight);
		imagecopyresampled($tmp,$src,0,0,0,0,$newwidth,$newheight,$width,$height);//resample the image
		switch (strtolower($ext)) {
			case 'jpeg':
			case 'pjpeg':
			case 'jpg':
				if($resized)
				{
					// upload the image and convert
					$statusupload = imagejpeg($tmp,$filepath,100);
				}
				else
				{
					// copy the image and convert NOT (for exif-data)
					$statusupload = JFile::copy($file_tmp_name,$filepath);
				}
				break;

			case 'png':
				$filename = explode('.',$filepath);
				$extension = $filename[(count($filename)-1)];
				$filename = str_replace('.'.$extension,'.jpg',$filepath);

				$statusupload = imagejpeg($tmp,$filename,100);//upload the image
				// 				$statusupload = imagepng($tmp,$filepath,100);//upload the image
				break;

			case 'gif':
				$filename = explode('.',$filepath);
				$extension = $filename[(count($filename)-1)];
				$filename = str_replace('.'.$extension,'.jpg',$filepath);

				$statusupload = imagejpeg($tmp,$filename,100);//upload the image
				// 				$statusupload = imagegif($tmp,$filepath,100);//upload the image
				break;

		}
		imagedestroy($tmp);
		if($statusupload) return true;
		return false;
	}

	/**
	 *
	 * @param string $uid
	 * @param string $username
	 * @return string
	 */
	function getProfileLink($uid, $username) {

		$cfg = JtgHelper::getConfig();

		switch($cfg->profile) {

			case "cb":
				$link = "<a href=".JRoute::_('index.php?option=com_comprofiler&task=userProfile&user='.$uid) . " >" . $username . "</a>";
				return $link;
				break;

			case "js":
				$jspath = JPATH_BASE . DS . 'components' . DS . 'com_community';
				include_once($jspath . DS . 'libraries' . DS . 'core.php');

				$link = "<a href=".CRoute::_('index.php?option=com_community&view=profile&userid='.$uid) . " >" . $username . "</a>";
				return $link;
				break;

			case "ku":
				$link = "<a href=".JRoute::_('index.php?option=com_kunena&func=fbprofile&userid='.$uid) . " >" . $username . "</a>";
				return $link;
				break;

			case "0":
				$link = $username;
				return $link;
				break;
		}
	}

	function MayIsee($where,$access,$otherfiles) {
		$otherfiles = (int)$otherfiles;
		if ( $where != "" ) $where = $where . " AND ";
		switch ($otherfiles) {
			case 0: // No
				return $where . "a.access <= " . $access;
				break;
			case 1: // Registered
				if ( ( $access == 0 ) OR ( $access == 1 ) )
				return $where . "( a.access = 0 OR a.access = 1 )";
				else
				return;
				break;
			case 2: // Special, Yes
				return;
				break;
		}
	}

	function getLocatedFloat($float,$default=0,$unit=null) {
		if ( $float == 0 ) return $default;
		if ( strtolower($unit) == "kilometer" )
		{
			$unit = JText::_('COM_JTG_KILOMETER');
		}
		if ( strtolower($unit) == "miles" )
		{
			$float = JtgHelper::getMiles($float);
			$unit = JText::_('COM_JTG_MILES');
		}
		$float = (float) $float;
		if ( ( $unit !== null ) AND ( $unit == JText::_('COM_JTG_KILOMETER') ) AND ( $float < 1 ) ) {
			$float = $float * 1000;
			$unit = "&nbsp;".JText::_('COM_JTG_METERS');
		}

		if ( $unit !== null )
		$unit = "&nbsp;" . $unit;
			
		if ( preg_match('/\./',$float) ) { // has decimal place
			$digit = explode('.',$float);
			$digits = strlen($digit[1]); // count of digits after decimal place
		}
		else $digits = 0;

		return number_format(
		$float,
		$digits,
		JText::_('COM_JTG_SEPARATOR_DEC'),
		JText::_('COM_JTG_SEPARATOR_THS')
		).$unit;
	}

	function _getLocatedFloat_old($float) {
		//		preg_match('/([0-9.]+)/', $float, $newfloat);
		//		$float = (float)$newfloat[0];
		$float = (float) $float;
		if ( preg_match('/\./',$float) ) { // has decimal place
			$digit = explode('.',$float);
			$digits = strlen($digit[1]); // count of digits after decimal place
		}
		else $digits = 0;
		jimport('joomla.language.language');
		$lang =& JFactory::getLanguage();
		$locale = $lang->getLocale();
		setlocale (LC_ALL, $locale);
		$locale = localeconv();
		$return = number_format(
		$float,
		$digits,
		$locale['decimal_point'],
		$locale['thousands_sep']
		);
		return $return;
	}
}
