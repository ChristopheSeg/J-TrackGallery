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
 */
 
defined('_JEXEC') or die('Restricted access');
JToolBarHelper::title(JText::_('COM_JTG_ADD_FILES'), 'categories.png');
JToolBarHelper::back($alt= 'COM_JTG_BACK', $href= 'javascript:history.back();');
$model = $this->getModel();
$rows = $model->_fetchJPTfiles();
if($rows == false) {
	JError::raiseNotice(0,JText::_('COM_JTG_ERROR_NOJGTFOUND'));
} else {
$i=0;
$importdone = false;
foreach ( $rows AS $track ) {
	if($model->importFromJPT($track) == true) {
		$color = "green";
		$importdone = true;
	} else {
		$color = "red";
	}
		echo("<font color=\"" . $color . "\">" . $track["file"] . "</font><br />\n");
}
if ($importdone == true)
	JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_IMPORT_DONE'));
else
	JError::raiseNotice(0,JText::_('COM_JTG_IMPORT_FAILURE'));
}
