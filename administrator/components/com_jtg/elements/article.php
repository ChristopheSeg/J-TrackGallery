<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @package    Comjtg
 * @author     Christophe Seguinot <christophe@jtrackgallery.net>
 * @copyright  2013 J!Track Gallery, InJooosm and joomGPStracks teams
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link       http://jtrackgallery.net/
 *
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class JElementArticle extends JFormField
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'Article';

	protected function getInput($name, $value, &$node, $control_name)
	// TODO You still need to replace the references to $control_name
	{
		$mainframe = JFactory::getApplication();

		//		$db			= JFactory::getDBO();
		$doc 		= JFactory::getDocument();
		//		$template 	= $mainframe->getTemplate();
		$fieldName	= $control_name.'['.$name.']';
		$path = JPATH_SITE . "/administrator/components/com_jtg/tables/";
		JTable::addIncludePath($path);
		$article = JTable::getInstance('jtg_files','Table');
		if ($value) {
			$article->load($value);
		}
		else
		{
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

		JHtml::_('behavior.modal', 'a.modal');
		$html = "\n".'<div style="float: left;"><input style="background: #ffffff;" type="text" id="'.$name.'_name" value="'.htmlspecialchars($article->title, ENT_QUOTES, 'UTF-8').'" disabled="disabled" /></div>';
		//		$html .= "\n &nbsp; <input class=\"inputbox modal-button\" type=\"button\" value=\"".JText::_('COM_JTG_SELECT') . "\" />";
		$html .= '<div class="button2-left"><div class="blank"><a class="modal" title="'.JText::_('COM_JTG_SELECT_A_FILE').'"  href="'.$link.'" rel="{handler: \'iframe\', size: {x: 650, y: 375}}">'.JText::_('COM_JTG_SELECT').'</a></div></div>' . "\n";
		$html .= "\n".'<input type="hidden" id="'.$name.'_id" name="'.$fieldName.'" value="'.(int) $value.'" />';

		return $html;
	}
}
