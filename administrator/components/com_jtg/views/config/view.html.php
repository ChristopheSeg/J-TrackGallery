<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: view.html.php,v 1.4 2011/04/09 09:33:39 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');

/**
 *
 */
class jtgViewConfig extends JView {
	/**
	 *
	 * @param object $tpl
	 */
	function display($tpl = null) {

		jimport('joomla.filesystem.file');
		$config =& jtgHelper::getConfig();

		$captcha = jtgHelper::checkCaptcha();
		$cactiv = ($captcha > 0) ? '<font color="green">'.JText::_('COM_JTG_INSTALLED').'</font>' : '<font color="red">'.JText::_('COM_JTG_NOT_INSTALLED').'</font>';
		$model = $this->getModel();
		$row = $model->getContent();
		$tmpl = $model->getTemplates();
		// unit array for lists
		$unit = array();
		array_push($unit, array("unit"  =>  "Kilometer"));
		array_push($unit, array("unit"  =>  "Miles"));
		// users array for lists
		$users = array(
		array('id' => 0, 'text' => JText::_('COM_JTG_PUBLIC')),
		array('id' => 1, 'text' => JText::_('COM_JTG_REGISTERED'))
		);
		// yes/no array for lists
		$inform = array(
		array('id' => 1, 'text' => JText::_('JYES')),
		array('id' => 0, 'text' => JText::_('JNO'))
		);
		// yes/no/registered users only array for download list
		$download = array(
		array('id' => 1, 'text' => JText::_('JYES')),
		array('id' => 0, 'text' => JText::_('JNO')),
		array('id' => 2, 'text' => JText::_('COM_JTG_REGISTERED'))
		);
		// comments order aray for lists
		$order =array(
		array('order' => 'DESC', 'text' => JText::_('COM_JTG_NEWEST')),
		array('order' => 'ASC', 'text' => JText::_('COM_JTG_OLDEST'))
		);
		$comments = array(
		array('id' => 0, 'text' => JText::_('COM_JTG_NO_COMMENTS')),
		array('id' => 1, 'text' => JText::_('COM_JTG_INTERN_COMMENTS')),
		array('id' => 2, 'text' => JText::_('COM_JTG_JOMCOMMENTS')),
		array('id' => 3, 'text' => JText::_('COM_JTG_JCOMMENTS'))
		);
		$approach = array(
		array('id' => 'no' , 'text' => JText::_('JNO')),
		array('id' => 'ors' , 'text' => JText::_('COM_JTG_APPR_ORS')),
		array('id' => 'cm' , 'text' => JText::_('COM_JTG_APPR_CM'))
		//		array('id' => 'cmkey' , 'text' => JText::_('COM_JTG_APPR_CMK'))
		// Key muss noch gespeichert werden
		//		array('id' => 'easy' , 'text' => JText::_('COM_JTG_APPR_EASY'))
		// Problem mit Datenbankspeicherung
		);
		$gallery = array(
		array('id' => 'none', 'text' => JText::_('JNONE')),
		array('id' => 'straight', 'text' => JText::_('COM_JTG_GAL_STRAIGHT')),
		array('id' => 'jd2', 'text' => JText::_('COM_JTG_GAL_JDGALLERY2')),
		//		array('id' => 'jd21', 'text' => JText::_('COM_JTG_GAL_JDGALLERY21'))
		);
		$serviceprovider = array(
		array('id' => 'not available', 'text' => JText::_('COM_JTG_NOT_AVAILABLE')),
		array('id' => 'google', 'text' => JText::_('COM_JTG_GOOGLE')),
		array('id' => 'osm', 'text' => JText::_('COM_JTG_OSM')),
		);
		$routingiconset = array();
		$imgdir = "..".DS."components".DS."com_jtg".DS."assets".DS."images".DS."approach".DS;
		$importdir = JPATH_SITE.DS."components".DS."com_jtg".DS."assets".DS."images".DS."approach".DS;
		$files = JFolder::folders($importdir);
		for($i=0;$i<count($files);$i++) {
			$nopic = "<font color=\"red\"><font size=\"+2\">X</font> (Icon missing) </font>";
			$string = $files[$i]."<br />".JText::_( 'COM_JTG_PREVIEW' ).":&nbsp;&nbsp;";
			if(is_file($imgdir.$files[$i].DS."car.png"))
			$string .= "<img src=\"".$imgdir.$files[$i].DS."car.png\" alt=\"car.png\" title=\"car.png\" /> ";
			else
			$string .= $nopic;
			if(is_file($imgdir.$files[$i].DS."bike.png"))
			$string .= "<img src=\"".$imgdir.$files[$i].DS."bike.png\" alt=\"bike.png\" title=\"bike.png\" /> ";
			else
			$string .= $nopic;
			if(is_file($imgdir.$files[$i].DS."foot.png"))
			$string .= "<img src=\"".$imgdir.$files[$i].DS."foot.png\" alt=\"foot.png\" title=\"foot.png\" />";
			else
			$string .= $nopic;
			if($i < count($files)-1)
			$string .= "<br /><br /><br />";
			$routingiconset[] = JHTML::_('select.option', $files[$i], $string);
		}

		// build the html select list
		// first build the groups tree
		$query = 'SELECT CONCAT( REPEAT(\'..\', COUNT(parent.id) - 1), node.title) as text, node.id as value'
			. ' FROM #__usergroups AS node, #__usergroups AS parent'
			. ' WHERE node.lft BETWEEN parent.lft AND parent.rgt'
			. ' GROUP BY node.id'
			. ' ORDER BY node.lft';

		$db = JFactory::getDbo();
		$db->setQuery($query);
		$gtree = $db->loadObjectList();
		$lists['gid']				= JHTML::_('select.genericlist', $gtree,'gid[]', 'class="inputbox" multiple="true" size="8"' , 'value', 'text', unserialize($config->gid) );
		if ($row) // if article(s) found in section jtg and category term
		$lists['content']		= JHTML::_('select.genericlist', $row, 'terms_id', 'size="1"', 'id', 'title', $config->terms_id );
		else
		$lists['content']		= "<font color=red>".JText::_('COM_JTG_TT_TERMS_NOTFOUND')."</font>";
		$lists['unit']				= JHTML::_('select.genericlist', $unit, 'unit', 'size="1"', 'unit', 'unit', $config->unit );
		$lists['tmpl']				= JHTML::_('select.genericlist', $tmpl, 'template', 'size="1"', 'name', 'name', $config->template );
		$lists['who']				= JHTML::_('select.genericlist', $users, 'comment_who', 'size="1"', 'id', 'text', $config->comment_who );
		$lists['inform']			= JHTML::_('select.genericlist', $inform, 'inform_autor', 'size="1"', 'id', 'text', $config->inform_autor );
		$lists['captcha']			= JHTML::_('select.genericlist', $inform, 'captcha', 'size="1"', 'id', 'text', $config->captcha );
		$lists['usevote']			= JHTML::_('select.genericlist', $inform, 'usevote', 'size="1"', 'id', 'text', $config->usevote );
		$lists['download']			= JHTML::_('select.genericlist', $download, 'download', 'size="1"', 'id', 'text', $config->download );
		$lists['order']				= JHTML::_('select.genericlist', $order, 'ordering', 'size="1"', 'order', 'text', $config->ordering );
		$lists['comments']			= JHTML::_('select.genericlist', $comments, 'comments', 'size="1"', 'id', 'text', $config->comments );
		$lists['access']			= JHTML::_('select.genericlist', $inform, 'access', 'size="1"', 'id', 'text', $config->access );
		$lists['approach']			= JHTML::_('select.genericlist', $approach, 'approach', 'size="1"', 'id', 'text', $config->approach );
		$lists['routingiconset']	= JHTML::_('select.radiolist', $routingiconset, 'routingiconset', null, 'value', 'text', $config->routingiconset );
		$lists['gallery']			= JHTML::_('select.genericlist', $gallery, 'gallery', 'size="1"', 'id', 'text', $config->gallery );
		$lists['serviceprovider']	= $this->createCheckbox("serviceprovider_google","google","Google")."<br />\n".
		$this->createCheckbox("serviceprovider_osm","osm","OpenStreetMap",true);
		if ( $config->level == "" )
		{
			$rows = 6;
		}
		else
		{
			$rows = explode("\n",$config->level);
			$rows = (int)count($rows) + 2;
		}
		$translevel = array();
		$levels = $config->level;
		$levels = explode("\n",$levels);
		$i = 1;
		foreach ($levels as $level) {
			if ( trim($level) != "" )
			{
				$translevel[] = $i . " - " . JText::_(trim($level));
				$i++;
			}
		}
		$lists['translevel']		= "<textarea disabled=\"disabled\" cols=\"50\" rows=\"" . $rows . "\" >" . implode("\n", $translevel) . "</textarea>";
		$lists['level']				= "<textarea name=\"level\" cols=\"50\" rows=\"" . $rows . "\" >" . $config->level . "</textarea>";
		
		$this->config = $config;
		$this->lists = $lists;
		$this->captcha = $cactiv;
		parent::display($tpl);
	}

	function createCheckbox($name,$value,$label,$checked=null){
		if($checked===true) $checked = " checked=\"checked\"";
		return "<input type=\"checkbox\" name=\"".$name."\" value=\"".$value."\"".$checked." />&nbsp;".$label;
	}

	
}
