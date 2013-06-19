<?php
/**
* @version		$Id: article.php,v 1.1 2011/04/03 08:41:50 christianknorr Exp $
* @package		Joomla
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class JElementArticle extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Article';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$mainframe =& JFactory::getApplication();

//		$db			=& JFactory::getDBO();
		$doc 		=& JFactory::getDocument();
//		$template 	= $mainframe->getTemplate();
		$fieldName	= $control_name.'['.$name.']';
		$path = JPATH_SITE.DS."administrator".DS."components".DS."com_jtg".DS."tables".DS;
		JTable::addIncludePath($path);
		$article =& JTable::getInstance('osm_files','Table');
		if ($value) {
			$article->load($value);
		} else {
			$article->title = JText::_('COM_JTG_SELECT_A_FILE');
		}

		$js = "
		function jSelectArticle(id, title, object) {
			document.getElementById(object + '_id').value = id;
			document.getElementById(object + '_name').value = title;
			window.parent.SqueezeBox.close();
		}";
		$doc->addScriptDeclaration($js);
		$link = 'index.php?option=com_jtg&amp;task=element&amp;controller=element&amp;tmpl=component&amp;object='.$name;
		
		JHTML::_('behavior.modal', 'a.modal');
		$html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
//		$html .= "\n &nbsp; <input class=\"inputbox modal-button\" type=\"button\" value=\"".JText::_('COM_JTG_SELECT')."\" />";
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('COM_JTG_SELECT_A_FILE').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.JText::_('COM_JTG_SELECT').'</a></div></div>'."\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int)$value.'" />';

		return $html;
	}
}
