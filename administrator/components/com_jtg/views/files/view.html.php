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

// Import Joomla! libraries
jimport( 'joomla.application.component.view');
// $lang = JFactory::getLanguage();

/**
 *
 */
class JtgViewFiles extends JViewLegacy
{
	/**
	 * Build the select list for access level
	 */
	function accesslevelForImport( $name , $js=null, $oneline=false )
	{
		$db =& JFactory::getDBO();

		$query = 'SELECT id AS value, title AS text'
		. ' FROM #__viewlevels'
		. ' ORDER BY id'
		;
		$db->setQuery( $query );
		$groups = $db->loadObjectList();
		// New Entry Private BEGIN
		$private = new stdClass();
		$private->value = 9;
		$private->text = JText::_('COM_JTG_PRIVATE');
		array_unshift($groups,$private);
		// New Entry Private END
		if ( $oneline !== false )
		$size = 1;
		else
		{
			$size = count($groups);
			if ( $size > 6 ) $size = 6;
		}
		$access = JHtml::_('select.genericlist', $groups, $name, 'class="inputbox" size="'.$size.'" '.$js, 'value', 'text', 0, '', 1 );

		return $access;
	}

	/**
	 * Gibt den übersetzten Status zurück
	 * @param status (int)
	 * @param needcolor (bool)
	 * @return Status (string)
	 */
	public function buildRowGroupname($rowaccess,$needcolor=false){
		switch ($rowaccess) {
			case 0:
				$access = JText::_( 'COM_JTG_PUBLIC' );
				$color = "green";
				break;
			case 1:
				$access = JText::_( 'COM_JTG_REGISTERED' );
				$color = "red";
				break;
			case 2:
				$access = JText::_( 'COM_JTG_ADMINISTRATORS' );
				$color = "black";
				break;
			case 9:
				$access = JText::_( 'COM_JTG_PRIVATE' );
				$color = "orange";
				break;
		}
		if($needcolor===false)
		return $access;
		else
		return "<font color='" . $color . "'>" . $access . "</font>";
	}

	/**
	 * Gibt den Klicklink zurück mit dem man Dateien für das Menü auswählen kann
	 * @param id
	 * @param filename
	 * @return string
	 */
	public function buildChooseKlicks($id,$title){
		$onclick = "window.parent.jSelectArticle('" . $id . "', '" . $title . "', 'id');";
		return "<a style=\"cursor: pointer;\" href=\"javascript:void(0);\" onclick=\"" . $onclick . "\">" . $title . "</a>";
	}

	/**
	 * Gibt den Klicklink zurück mit dem man Spuren direkt editieren kann
	 * @param Filename
	 * @param Zähler
	 * @return string
	 */
	public function buildEditKlicks($file,$count){
		return "<a href=\"javascript:void(0);\" onclick=\"javascript:return listItemTask('cb" . $count.
			"','editfile')\">" . $file . "</a>";
	}

	public function buildHiddenImage($iconpath,$hidden,$count) {
		switch ($hidden) {
			case null: // not saved
				$tt = JText::_('COM_JTG_NOT_SAVED');
				$item = "<span title=\"" . $tt . "\">-- ? --</span>";
				return $item;
				break;
			case "0": // visible
				$link = "tohide";
				$icon = $iconpath . "icon_visible.png";
				$tt = JText::_('COM_JTG_TOHIDE');
				$item = "<img alt=\"" . $tt . "\" title=\"" . $tt . "\" src=\"" . $icon . "\" />";
				break;
			case "1": // hidden
				$link = "toshow";
				$icon = $iconpath . "icon_hidden.png";
				$tt = JText::_('COM_JTG_TOSHOW');
				$item = "<img alt=\"" . $tt . "\" title=\"" . $tt . "\" src=\"" . $icon . "\" />";
				break;
		}
		return "<a href=\"javascript:void(0);\" onclick=\"javascript:return listItemTask('cb" . $count.
			"','" . $link . "')\">" . $item . "</a>";
	}
	/**
	 * Gibt eine Bilderliste der Dateitypen zurück
	 * @param status (int)
	 * @return Status (string)
	 */
	public function buildImageFiletypes($track,$wp,$route,$cache){
		$imagelink = "<table class=\"fileis\"><tr>";

		if ( ( isset($track) ) AND ( $track == "1" ) ) $m = (string)1; else $m = (string)0;
		$imagelink .= "<td class=\"icon\">";
		if ( isset($track) )
		$imagelink .= "<span class=\"track" . $m . "\" title=\"".JText::_( 'COM_JTG_ISTRACK'.$m ) . "\"></span>";
		else
		$imagelink .= "<span class=\"track" . $m . "\" title=\"".JText::_( 'COM_JTG_DKTRACK' ).
				"\" style=\"text-align:center\"><font size=\"+2\">?</font>";
		$imagelink .= "</td>";

		if ( ( isset($wp) ) AND ( $wp == "1" ) ) $m = (string)1; else $m = (string)0;
		$imagelink .= "<td class=\"icon\">";
		if ( isset($wp) )
		$imagelink .= "<span class=\"wp" . $m . "\" title=\"".JText::_( 'COM_JTG_ISWP'.$m) . "\"></span>";
		else
		$imagelink .= "<span class=\"wp" . $m . "\" title=\"".JText::_( 'COM_JTG_DKWP' ).
				"\" style=\"text-align:center\"><font size=\"+2\">?</font>";
		$imagelink .= "</td>";
/*
		if ( ( isset($route) ) AND ( $route == "1" ) ) $m = (string)1; else $m = (string)0;
		$imagelink .= "<td class=\"icon\">";
		if ( isset($route) )
			$imagelink .= "<span class=\"route" . $m . "\" title=\"".JText::_( 'COM_JTG_ISROUTE'.$m ) . "\">";
		else
			$imagelink .= "<span class=\"route" . $m . "\" title=\"".JText::_( 'COM_JTG_DKROUTE' ) . "\" style=\"text-align:center\"><font size=\"+2\">?</font>";
		$imagelink .= "</span>";
		$imagelink .= "</td>";
*/
		if ( ( isset($cache) ) AND ( $cache == "1" ) ) $m = (string)1; else $m = (string)0;
		$imagelink .= "<td class=\"icon\">";
		if ( isset($cache) )
			$imagelink .= "<span class=\"cache" . $m . "\" title=\"".JText::_( 'COM_JTG_ISCACHE'.$m ) . "\">";
		else
			$imagelink .= "<span class=\"cache" . $m . "\" title=\"".JText::_( 'COM_JTG_DKCACHE' ) . "\" style=\"text-align:center\"><font size=\"+2\">?</font>";
		$imagelink .= "</span>";
		$imagelink .= "</td>";

		$imagelink .= "</tr></table>";
		return $imagelink;
	}

	/**
	 * Überprüft die GPX-Datei für den Massenimport
	 * @return true or Errorlevel | (string) errormessage
	 */
	public function checkFile($file,$exist=false) {
		if ($exist !== false )
		return 1;
		$filename = explode(DIRECTORY_SEPARATOR,$file);
		$filename = $filename[(count($filename)-1)];
		if ( !is_writable($file) )		// Kein Schreibrecht
		return 2;
		if ( strlen($filename) > 50 )		// Dateinamenslänge überschritten
		return 3;
		if ( preg_match('/\&/',$filename) )	// Wenn "&" im Dateinamen
		return 4;
		if ( preg_match('/\#/',$filename) )	// Wenn "#" im Dateinamen
		return 5;
		// TODO adapt for kml files???? 
		$xml = simplexml_load_file($file);
		if (empty($xml->trk))		// Keine Spur vorhanden
		return 6;
		$i = 0; // Zähler
		$j = 0; // gefundene Spuren
		while (true) {
			if (!empty($xml->trk[$i])) { // Spur vorhanden
				if (!empty($xml->trk[$i]->trkseg->trkpt)) // Punkt vorhanden
				$j++;
				$i++;
			} elseif ( $j == 0 )
			return 7; // Spur vorhanden, aber kein Punkt
			elseif ( ( $j == 1 ) AND ( $i == 1 ) )
			return true; // eine Spur mit Punkten an erster Stelle vorhanden
			else
			return 8; // Spur vorhanden, aber nicht an erster Stelle. Evtl. mehrere Spuren
		}
	}

	/**
	 * Extrahiert das Datum aus der GPX-Datei
	 * @return date
	 */
	public function giveDate($file) {
		if((!is_file($file)) OR (!is_readable($file)))return false;
		$file = simplexml_load_file($file);
		$date = explode('T',$file->time);
		if (count($file->time) == 0)
		return false;
		if ( count($date) != 2 )
		$date = explode('T',$file->trk->trkseg->trkpt->time);
		if ( count($date) != 2 )
		$date = explode('T',$file->metadata->time);
		if ( strlen($date[0]) == 10 )
		return $date[0];
		else
		return false;
	}

	/**
	 * Extrahiert den Titel aus der GPX-Datei
	 * @return date
	 */
	public function giveTitle($file) {
		if((!is_file($file)) OR (!is_readable($file))) return "";
		$file = simplexml_load_file($file);
		$desc = $file->metadata->desc;
		if ( ( $desc === null ) OR ( count($desc) == 0 ) ) {
			$desc = $file->wpt->name;
			if ( $desc !== null ) return (string) $desc;
			return $file->trk->name;
		}
		$desc = (string)($desc); // why doesn't work JArrayHelper::fromObject?
		return $desc;
	}

	function giveParentCat($catid) {
		$catid = (int)$catid;
		if ($catid == 0)
		return null;
		$model = $this->getModel();
		$cats = $model->getCats();
		$cats = JArrayHelper::toObject($cats);
		$i = 0;
		foreach($cats AS $cat){
			if(isset($cat->id))
			$id = (int)$cat->id;
			if(isset($cat->title))
			$title[$id] = $cat->title;
			if ((isset($cat->id))AND( $catid == $id )){
				$parentid = (int)$cat->parent_id;
				break;
			}
			$i++;
		}
		if((isset($parentid) AND ($parentid != 0) AND isset($title[$parentid])))
		return ($title[$parentid]);
		return null;
	}

	function parseCatTree($cats,$catid,$separator = "<br />") {
		$catid = (int)$catid;
		if ($catid == 0)
		return null;
//		$model = $this->getModel();
//		$cats = $model->getCats();
//		$cats = JArrayHelper::toObject($cats);
//		$i = 0;
		$newcat = array();
		$missingcat = array();
		foreach($cats AS $cat){
			$newcat[$cat->id] = $cat;
//			if(isset($cat->id))
//			$id = (int)$cat->id;
			if(isset($cat->title)) {
			    $newcat[$cat->id]->title = JText::_($cat->title);
			}
//			$title[$id] = $cat->title;
//			if ((isset($cat->id))AND( $catid == $id )){
//				$parentid = (int)$cat->parent_id;
//				break;
//			}
//			$i++;
		}
		if ( !isset($newcat[$catid]) )
		{ // missing Category
			$missingcat[$catid] = $catid;
			$newcat[$catid] = new stdClass;
			$newcat[$catid]->id = 0;
			$newcat[$catid]->title = JText::sprintf('COM_JTG_ERROR_MISSING_CATID',$catid);
			$newcat[$catid]->parent_id = 0;
			$newcat[$catid]->treename = "<font class=\"errorEntry\">".
			$newcat[$catid]->title.
			"</font>";
		}
		$return = array();
		$j = count($newcat);
		while (true) {
			$cat = $newcat[$catid];
			$catid = $cat->parent_id;
			array_unshift($return,JText::_($cat->treename) );
			if ( ( $cat->parent_id == 0 ) OR ( $j <= 0 ) )
			break;
			$j--;
		}
		$return = implode($separator,$return);
		return array("tree" => $return,"missing" => $missingcat);
		if((isset($parentid) AND ($parentid != 0) AND isset($title[$parentid])))
		return (JText::_($title[$parentid]));
		return null;
	}

	/**
	 *
	 * @global object $mainframe
	 * @global string $option
	 * @param object $tpl
	 */
	function display($tpl = null) {
		$mainframe =& JFactory::getApplication(); 
		$option = JFactory::getApplication()->input->get('option');

		if($this->getLayout() == 'form'):
		$this->_displayForm($tpl);
		return;
		endif;

		if($this->getLayout() == 'upload'):
		$this->_displayUpload($tpl);
		return;
		endif;

		$model =& $this->getModel();

		$order = JFactory::getApplication()->input->get('order', 'order', 'post', 'string' );

		$filter_order		= $mainframe->getUserStateFromRequest( $option . "filter_order",
 	'filter_order',
 	'ordering',
 	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option . "filter_order_Dir",
 	'filter_order_Dir',
 	'',
 	'word' );
		$search				= $mainframe->getUserStateFromRequest( $option . "search",
		'search',
		'',
		'string' );
		$search				= JString::strtolower( $search );

		$lists['order']		= $filter_order;
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['search']	= $search;
		$rows		= & $this->get('Data');
		$total		= & $this->get('Total');
		$pagination = & $this->get('Pagination' );
		$cfg = JtgHelper::getConfig();
		$cats = $model->getCats();

		$this->cats = $cats;
		$this->lists = $lists;
		$this->rows = $rows;
		$this->cfg = $cfg;
		$this->pagination = $pagination;

		parent::display($tpl);
	}

	function _displayUpload($tpl) {
		if(JVERSION>=3.0) //Code support for joomla version greater than 3.0
		{
		    JHtml::_('jquery.framework');
		    JHtml::script(Juri::base() . 'components/com_jtg/assets/js/multifile.js');
		    JHTML::_('behavior.framework');
		    // Quick'n'Dirty - Sonst funktioniert das Joomla-Menü nicht mehr: (!?)
		    // JHtml::script('core-uncompressed.js', 'media/system/js/', false); //Joomla 2.5??
		}
		else 
		{
		    JHtml::script('jquery.js', 'components/com_jtg/assets/js/', false); 
		    JHtml::script('multifile.js', 'components/com_jtg/assets/js/', false);
		    // Quick'n'Dirty - Sonst funktioniert das Joomla-Menü nicht mehr: (!?)
		    JHtml::script('mootools.js', '/media/system/js/', false);
		    JHtml::script('core-uncompressed.js', 'media/system/js/', false);
		}

		parent::display($tpl);
	}

	function _displayForm($tpl) {
		if(JVERSION>=3.0) //Code support for joomla version greater than 3.0
		{
		    JHtml::_('jquery.framework');
		    JHtml::script(Juri::base() . 'components/com_jtg/assets/js/multifile.js');
		    JHTML::_('behavior.framework');
		}
		else 
		{
		    JHtml::script('jquery.js', 'components/com_jtg/assets/js/', false); 
		    JHtml::script('multifile.js', 'components/com_jtg/assets/js/', false);
		    // Quick'n'Dirty - Sonst funktioniert das Joomla-Menü nicht mehr: (!?)
		    JHtml::script('mootools.js', '/media/system/js/', false);
		}

		jimport('joomla.filesystem.folder');
		// Quick'n'Dirty - Sonst funktioniert das Joomla-Menü nicht mehr: (!?)
		
		$cid =& JFactory::getApplication()->input->get('cid', array(), 'post', 'array' );
		// $id = implode(',', $cid);
		$cfg = JtgHelper::getConfig();
		$editor =& JFactory::getEditor();
		$model = $this->getModel();
		$cats = $model->getCats(true,'COM_JTG_SELECT',-1);
		$terrain = $model->getTerrain("*",true," WHERE published=1 ");
		$user 	=& JFactory::getUser();
		$uid = $user->get('id');
		$yesnolist = array(
		array('id' => 0, 'title' => JText::_('JNO')),
		array('id' => 1, 'title' => JText::_('JYES'))
		);
		if(count($cid)==0)
		{
			echo "deprecated";
			//			New File
			$id = 0;
			$track = $model->getFile($id);
			$level = $model->getLevel($id);
			$access = $model->getAccess($id);
			$size = count($cats);
			if ( $size > 6) $size = 6;
			$lists['cats']		= JHtml::_('select.genericlist', $cats, 'catid[]', 'size="'.$size.'" multiple="multiple"', 'id', 'treename', 0 );
			$size = count($terrain);
			if ( $size > 6) $size = 6;
			$lists['terrain']	= JHtml::_('select.genericlist', $terrain, 'terrain[]', 'multiple="multiple" size="'.$size.'"', 'id', 'title', 0 );
			$row->access = $access;
			$lists['access']	= JHtml::_('list.accesslevel', $row );
			$lists['uid']		= JHtml::_('list.users', 'uid', $uid, 1, NULL, 'name', 0 );
			$lists['hidden']	= JHtml::_('select.genericlist', $yesnolist, 'hidden', 'class="inputbox" size="2"', 'id', 'title',0);
			$lists['published']	= JHtml::_('select.genericlist', $yesnolist, 'published', 'class="inputbox" size="2"', 'id', 'title',1);
			$this->lists = $lists;
			$this->track = $track;
			$this->id = $id;
			$this->level = $level;

		}
		else
		{
			//			Edit File
			$id = $cid[0];
			$track = $model->getFile($id);
			$level = $model->getLevel($track->level);
			$access = $model->getAccess($id);
			// $terrain[0]->checked_out=1;
			$error = false;
			$terrainlist = ($track->terrain? explode(',',$track->terrain): 0);
//			What was this for ??
//			foreach ($terrainlist as $t) {
//				if ( !is_numeric($t) ) $error = true;
//			}
//			if ( $error === true ) $error = "<font color=\"red\">" . JText::_('Error') . ": " . $track->terrain . "</font><br />";
			$size = min( count($cats), 6);
			$trackids = explode(",",$track->catid);
			$lists['cats']		= JHtml::_('select.genericlist', $cats, 'catid[]', 'size="'.$size.'" multiple="multiple"', 'id', 'title', $trackids, '', true);
			$size = min( count($terrain), 6) ;
			$lists['terrain']	= $error.JHtml::_('select.genericlist', $terrain, 'terrain[]', 'multiple="multiple" size="'.$size.'"', 'id', 'title', $terrainlist );
			//			$row->access = $access;
			$lists['access']	= JtgHelper::getAccessList($access);
			//			$lists['access']	= JHtml::_('list.accesslevel', $row );
			$lists['hidden'] = JHtml::_('select.genericlist', $yesnolist, 'hidden', 'class="inputbox" size="2"', 'id', 'title',$track->hidden);
			$lists['uid']		= JHtml::_('list.users', 'uid', $track->uid, 1, NULL, 'name', 0 );
			$img_dir = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'track_' . $id . DIRECTORY_SEPARATOR;
			if(!JFolder::exists($img_dir)) {
				JFolder::create($img_dir,0777);
			}
			$img_path = JUri::root().'images/jtrackgallery/track_'.$id.'/';
			$thumb_dir = $img_dir . 'thumbs' . DIRECTORY_SEPARATOR;
			$thumb_dir = $img_dir . 'thumbs/';
			$images = null;
			// TODO recreate thumbnails: this must be done only when updating track, not always!!
			if(JFolder::exists($img_dir)) {
				$imgs = JFolder::files($img_dir);
				if($imgs)
				{
					if(!JFolder::exists($thumb_dir)) 
					{
					    JFolder::create($thumb_dir);
					}
					require_once(JPATH_SITE . DIRECTORY_SEPARATOR . "administrator" . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_jtg" . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR . "thumb_creation.php");

					foreach($imgs AS $image)
					{
						$thumb_name =  'thumb1_' . $image;   
						$thumb = com_jtg_create_Thumbnails ($img_dir, $image, $cfg->max_thumb_height, $cfg->max_geoim_height); 
						// 
						if (! $thumb) {	
						    $images .= "<input type=\"checkbox\" name=\"deleteimage_".str_replace('.',null,$image) . "\" value=\"" . $image . "\">".JText::_( 'COM_JTG_DELETE_IMAGE' ) . " (" . $image . ")<br />".
						    "<img src=\"" . $img_path.$image . "\" alt=\"" . $image . "\" title=\"" . $image . "\" /><br /><br />\n";					    
						    } else {
						    $images .= "<input type=\"checkbox\" name=\"deleteimage_".str_replace('.',null,$image) . "\" value=\"" . $image . "\">".JText::_( 'COM_JTG_DELETE_IMAGE' ) . " (" . $image . " {only thumbnail displayed})<br />".
						    "<img src=\"" . $img_path. 'thumbs/'. $thumb_name . "\" alt=\"" . $image . "\" title=\"" . $image . " (thumbnail)\" /><br /><br />\n";					    
						}					    
					}
				}
			}
			$lists['published'] = JHtml::_('select.genericlist', $yesnolist, 'published', 'class="inputbox" size="2"', 'id', 'title',$track->published);
			// Values BEGIN
			$lists['values'] = JtgHelper::giveGeneratedValues('backend',$this->buildImageFiletypes($track->istrack,$track->iswp,$track->isroute,$track->iscache),$track);
			// Values END
			$this->lists = $lists;
			$this->track = $track;
			$this->id = $id;
			$this->level = $level;
			$this->images = $images;

		}
		$this->editor = $editor;
		parent::display($tpl);
	}
}

