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
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
// Import Joomla! libraries
jimport('joomla.form.formfield');

class JFormFieldOwnList extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'uid';
	
        //The field class must know its own type through the variable $type.
        protected $type = 'ownlist';
 
        public function getInput() {
                // code that returns HTML that will be shown as the form field
		$name = $this->element['name'];
		$value = $this->value;

		global $parseLimitText;
		if (!is_array($parseLimitText)) $parseLimitText = array();
		switch ($name)
		{
			case "jtg_param_cats":
				return $this->parseCatsSelect($value, $name,false);
				break;
//			case "jtg_param_subcats":
//				return $this->parseCatsSelect($name,true);
//				break;
			case "jtg_param_user":
				return $this->parseUserSelect($value, $name);
				break;
			case "jtg_param_usergroup":
				return $this->parseUsergroupSelect($value, $name);
				break;
			case "jtg_param_terrain":
				return $this->parseTerrainSelect($value, $name);
				break;
			case "jtg_param_level_from":
				return $this->parseLevelSelect($value, $name,  5, 0);
				break;
			case "jtg_param_level_to":
				return $this->parseLevelSelect($value, $name,  5, 5);
				break;
			case "jtg_param_vote_from":
				return $this->parseLevelSelect($value, $name,  10, 0);
				break;
			case "jtg_param_vote_to":
				return $this->parseLevelSelect($value, $name,  10, 10);
				break;
			case "jtg_param_limittext":
				return $this->parseLimitText();
				break;
			case "jtg_param_helpbutton":
				return $this->parseHelpButton();
				break;
			case "jtg_param_helptext":
				die("jtg_param_helptext");
				return $this->parseHelpText();
				break;
		}
		return ;
	}

	private function parseHelpButton() {
		return "<table class=\"toolbar\"><tr><td class=\"button\" id=\"toolbar-help\"><a href=\"#\" onclick=\"popupWindow('components/com_jtg/help/en-GB/menu/overviewmap.html', 'Hilfe', 640, 480, 1)\" class=\"toolbar\"><span class=\"icon-32-help\" title=\"".JText::_('COM_JTG_HELP')  . "\"></span>".JText::_('COM_JTG_HELP')  . "</a></td></tr></table>";
	}

	private function parseHelpText() {
		return "<a href=\"#\" onclick=\"popupWindow('components/com_jtg/help/en-GB/menu/overviewmap.html', 'Hilfe', 640, 480, 1)\" >".JText::_('COM_JTG_HELP')  . "</a>";
	}

	private function parseLimitText() {
		global $parseLimitText;
		$p = $parseLimitText;
		$r = array();
		if ( $p['jtg_param_cats'] != -1 )		$r[] = JText::_('COM_JTG_MENU_LIMIT_CONSTRUCT_CATS') . " " . $this->getCatName($p['jtg_param_cats']);
//		if ( $p['jtg_param_subcats'] != -1 )	$r[] = JText::_('COM_JTG_MENU_LIMIT_CONSTRUCT_SUBCATS') . " " . $this->getCatName($p['jtg_param_subcats']);
		if ( $p['jtg_param_user'] != 0 )		$r[] = JText::_('COM_JTG_MENU_LIMIT_CONSTRUCT_USER') . " " . $this->giveRealname($p['jtg_param_user']);
		if ( $p['jtg_param_usergroup'] != -1 )	$r[] = JText::_('COM_JTG_MENU_LIMIT_CONSTRUCT_USERGROUP') . " " . $this->giveUsergroup($p['jtg_param_usergroup']);
		if ( $p['jtg_param_terrain'] != -1 )	$r[] = JText::_('COM_JTG_MENU_LIMIT_CONSTRUCT_TERRAIN') . " " . $this->getTerrainname($p['jtg_param_terrain']);

		if ( ( $p['jtg_param_level_from'] != 0 ) OR ( $p['jtg_param_level_to'] != 5 ) )
		{
			if ( $p['jtg_param_level_from'] == $p['jtg_param_level_to'] )
			$r[] = JText::_('COM_JTG_MENU_LIMIT_CONSTRUCT_LEVEL') . " " . $p['jtg_param_level_from'];
			elseif ( $p['jtg_param_level_from'] > $p['jtg_param_level_to'] )
			$r[] = "<font color=red>".
				JText::sprintf('COM_JTG_MENU_LIMIT_CONSTRUCT_LEVEL_FROM_TO',$p['jtg_param_level_from'],$p['jtg_param_level_to']).
				"</font>";
			else
				$r[] = JText::sprintf('COM_JTG_MENU_LIMIT_CONSTRUCT_LEVEL_FROM_TO',$p['jtg_param_level_from'],$p['jtg_param_level_to']);
		}

		if ( ( $p['jtg_param_vote_from'] != 0 ) OR ( $p['jtg_param_vote_to'] != 10 ) )
		{
			if ( $p['jtg_param_vote_from'] == $p['jtg_param_vote_to'] )
				$r[] = JText::sprintf('COM_JTG_MENU_LIMIT_CONSTRUCT_VOTE', $p['jtg_param_vote_from']);
			elseif ( $p['jtg_param_vote_from'] > $p['jtg_param_vote_to'] )
				$r[] = "<font color=red>".
				JText::sprintf('COM_JTG_MENU_LIMIT_CONSTRUCT_VOTE_FROM_TO',$p['jtg_param_vote_from'],$p['jtg_param_vote_to']) . "</font>";
			else
				$r[] = JText::sprintf('COM_JTG_MENU_LIMIT_CONSTRUCT_VOTE_FROM_TO',$p['jtg_param_vote_from'],$p['jtg_param_vote_to']);
		}

		if(count($r) == 0) return JText::_('COM_JTG_MENU_LIMIT_NO');
		$r = $this->implodeDesc($r,"and",", \n<b>" ,"</b> \n");
		return  JText::_('COM_JTG_MENU_LIMIT_HEADER') . " " . $r.JText::_('COM_JTG_MENU_LIMIT_FOOTER');
	}

	private function getTerrainname($cids) {
		if ( is_array($cids))
		{
			$return = array();
			foreach ($cids as $terrain) {
				$return[] = $this->getTerrainname($terrain);
			}
			return $this->implodeDesc($return,"or");
		}
		$db =& JFactory::getDBO();

		$query = "SELECT * FROM #__jtg_terrains WHERE id='" . $cids . "'";

		$db->setQuery($query);
		$result = $db->loadObject();

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		if(!$result) return;
		return JText::_($result->title);
	}

	private function giveUsergroup($gid) {
		if ( is_array($gid))
		{
			$return = array();
			foreach ($gid as $group) {
				$return[] = $this->giveUsergroup($group);
			}
			return $this->implodeDesc($return,"or");
		}
		switch ($gid) {
			case 0:
				return JText::_('COM_JTG_PUBLIC');
				break;
			case 1:
				return JText::_('COM_JTG_REGISTERED');
				break;
			case 2:
				return JText::_('COM_JTG_ADMINISTRATORS');
				break;
		}
	}

	private function implodeDesc($desc,$main,$header=" ",$footer=" ") {
		$desc = implode($header.JText::_($main).$footer,$desc);
		return $desc;
	}

	/**
	 *
	 * @global object $mainframe
	 * @return object
	 */
	private function getCatName($cids) {
		global $parseLimitText;
		if ( is_array($cids))
		{
			$return = array();
			foreach ($cids as $cid) {
				$return[] = $this->getCatName($cid);
			}
			$return = $this->implodeDesc($return,'or');
			$parseLimitText['jtg_param_catname'] = $return;
			return $return;
		}
		$db =& JFactory::getDBO();

		$query = "SELECT * FROM #__jtg_cats WHERE id='" . $cids . "'";

		$db->setQuery($query);
		$result = $db->loadObject();

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		if(!$result) return;
		return JText::_($result->title);
	}

	private function giveRealname($uid) {
		if ( is_array($uid))
		{
			$return = array();
			foreach ($uid as $user)
			{
				$return[] = $this->giveRealname($user);
			}
			return $this->implodeDesc($return,"or");
		}
		$user =& JFactory::getUser($uid);
		return $user->name;
	}

	private function parseLevelSelect($value, $name,  $max, $std) {
		//TODO check if default value taken into account 
		if ($value === null) $value = $std;
		global $parseLimitText;
		$parseLimitText[(string) $name] = $value;
		$level = array();
		for ($i = 0; $i <= $max; $i++) $level[] = JHtml::_('select.option', $i, $i);
		$list = JHTML::_('select.genericlist', $level, $this->name, null, 'value', 'text', $this->value);
		return $list;
	}
	
	private function parseTerrainSelect($value, $name) {
		$all = -1;
		if (!$value) $value = $all;
		global $parseLimitText;
		$parseLimitText[(string) $name] = $value;
		jimport('joomla.filesystem.file');
		require_once("components" . DS . "com_jtg" . DS . "models" . DS . "files.php");
		$terrain = new JtgModelFiles;
		$terrain = $terrain->getTerrain("title, id");
		$nullterrain = array('title'=>JText::_('COM_JTG_ALL'), 'id'=>$all);
		$nullterrain = JArrayHelper::toObject($nullterrain);
		array_unshift($terrain,$nullterrain);
		$size = $this->getSelectSize($terrain);
		$list = JHTML::_('select.genericlist', $terrain, $this->name.'[]', 'class="inputbox" multiple="multiple" size="'.$size.'"', 'id', 'title', $this->value, $this->id);
		return $list;
	}

	private function parseUsergroupSelect($value, $name) {
		if($value === null) $value = -1;
		global $parseLimitText;
		$parseLimitText[(string) $name] = $value;
		$gid = $this->giveUsergroupid($value);
		$db =& JFactory::getDBO();

		$query = 'SELECT id AS value, title AS text'
		. ' FROM #__viewlevels'
		. ' ORDER BY id'
		;
		$db->setQuery( $query );
		$groups = $db->loadObjectList();
		$nullgroup = array('text'=>JText::_('COM_JTG_ALL'), 'value'=>'-1');
		$nullgroup = JArrayHelper::toObject($nullgroup);
		array_unshift($groups,$nullgroup);
		$size = $this->getSelectSize($groups);
		$list = JHTML::_('select.genericlist', $groups, $this->name.'[]', 'class="inputbox" multiple="multiple" size="'.$size.'"', 'value', 'text', $gid, '', 1 );
		return $list;
	}

	private function parseCatsSelect($value, $name,$nosubcats) {
		if($value === null) $value = -1;
		global $parseLimitText;
		$parseLimitText[(string) $name] = $value;
		jimport('joomla.filesystem.file');
		require_once("components" . DS . "com_jtg" . DS . "models" . DS . "files.php");
		$cats = new JtgModelFiles;
		$cats = $cats->getCats($nosubcats,'COM_JTG_ALL',-1);
		$size = $this->getSelectSize($cats);
		$list = JHTML::_('select.genericlist', $cats, $this->name.'[]', 'class="inputbox" multiple="multiple" size="'.$size.'"', 'id', 'treename', $this->value);
		return $list;
	}

	private function parseUserSelect($uid,$name)
	{
		if ($uid === null) $uid = 0;
		global $parseLimitText;
		$parseLimitText[(string) $name] = $uid;
		$list = $this->JHTML_list_users($this->name.'[]', $uid, 1, 'multiple="multiple"', 'name', 0 );
		return $list;
	}

	private function giveUsergroupid($gid)
	{
		if(isset($gid))
		return $gid;
		else
		return 0;
	}

	private function fetchParams()
	{
		$params = &JComponentHelper::getParams( 'com_jtg' );
		// doesn't work
		//		if(!@$params = $this->_parent->_registry["_default"]["data"])
		//		$params = false;
		return $params;
	}

	/**
	 * Select list of active users
	 */
	private function JHTML_list_users( $name, $active, $nouser = 0, $javascript = NULL, $order = 'name', $reg = 1 )
	{
		$db =& JFactory::getDBO();

		$and = '';
		if ( $reg ) {
			// does not include registered users in the list
		    // TODO IMPORTANT ACL modified in Joomla 2.5
			$and = ' AND gid > 18';
		}

		$query = 'SELECT id AS value, name AS text'
		. ' FROM #__users'
		. ' WHERE block = 0'
		. $and
		. ' ORDER BY '. $order
		;
		$db->setQuery( $query );
		if ( $nouser ) {
			$users[] = JHTML::_('select.option',  '0', JText::_( 'COM_JTG_ALL' ) );
			$users = array_merge( $users, $db->loadObjectList() );
		} else {
			$users = $db->loadObjectList();
		}
		$size = $this->getSelectSize($users);
		$users = JHTML::_('select.genericlist', $users, $name, 'class="inputbox" size="'.$size.'" '. $javascript, 'value', 'text', $active );

		return $users;
	}

	private function getSelectSize($array=null) {
		if(!is_array($array)) return;
		$size = count($array);
		if($size > 6) $size = 6;
		return $size;
	}

    
}
