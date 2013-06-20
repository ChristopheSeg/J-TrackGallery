<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooOSM and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

defined('_JEXEC') or die('Restricted access');

// toolbar
JToolBarHelper::title(JText::_('COM_JTG_UPLOAD_CATIMAGE'), 'categories.png');
JToolBarHelper::back($alt= 'COM_JTG_BACK', $href= 'javascript:history.back();');
JToolBarHelper::spacer();
JToolBarHelper::help( 'cats/managecatpicsform',true );
JHTML::script('jquery.js', 'components/com_jtg/assets/js/', false);
JHTML::script('multifile.js', 'components/com_jtg/assets/js/', false);
JHTML::script('mootools.js', '/media/system/js/', false);
echo JText::sprintf('COM_JTG_ALLOWED_FILETYPES',$this->types);
?>
<form action="" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm">
	<input type="file" name="files" accept="image/*" /><br />
	<input type='submit' value='<?php echo JText::_('COM_JTG_UPLOAD') ?>' class='submit' onclick="javascript: submitbutton('uploadcatimages')"  />
	<input type="hidden" name="option" value="com_jtg" />
	<input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="cats" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
