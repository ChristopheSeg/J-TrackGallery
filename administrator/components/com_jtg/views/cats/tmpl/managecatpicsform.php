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
 */

defined('_JEXEC') or die('Restricted access');

// toolbar
JToolBarHelper::title(JText::_('COM_JTG_UPLOAD_CATIMAGE'), 'categories.png');
JToolBarHelper::back();
JToolBarHelper::spacer();
JToolBarHelper::help( 'cats/managecatpicsform',true );
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
    JHtml::script('mootools.js', '/media/system/js/', false);
}

echo JText::sprintf('COM_JTG_ALLOWED_FILETYPES',$this->types);
?>
<form action="" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm">
	<input type="file" name="files" accept="image/*" /><br />
	<input type='submit' value='<?php echo JText::_('COM_JTG_UPLOAD') ?>' class='submit' onclick="javascript: submitbutton('uploadcatimages')"  />
	<input type="hidden" name="option" value="com_jtg" />
	<input type="hidden" name="task" value="<?php echo JFactory::getApplication()->input->get('task'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="cats" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
