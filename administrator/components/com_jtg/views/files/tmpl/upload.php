<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @package     Comjtg
 * @subpackage  Backend
 * @author      Christophe Seguinot <christophe@jtrackgallery.net>
 * @author      Pfister Michael, JoomGPStracks <info@mp-development.de>
 * @author      Christian Knorr, InJooOSM  <christianknorr@users.sourceforge.net>
 * @copyright   2015 J!TrackGallery, InJooosm and joomGPStracks teams
 *
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link        http://jtrackgallery.net/
 *
 */

defined('_JEXEC') or die('Restricted access');

// Toolbar
JToolBarHelper::title($alt = 'COM_JTG_UPLOAD', 'categories.png');
JToolBarHelper::back();

// JToolBarHelper::back();
JToolBarHelper::spacer();
JToolBarHelper::save('uploadfiles', 'Upload');
$accept = array("gpx","tcx", "kml");
$max = 100;
?>

<table class="adminlist" cellpadding="1">
	<tbody>
		<tr>
			<td>
				<form action="" method="post" name="adminForm" id="adminForm"
					class="adminForm" enctype="multipart/form-data" target="_parent">
					<?php echo JText::sprintf('COM_JTG_ALLOWED_FILETYPES', implode(", ", $accept)); ?>
					<br />
					<?php echo JText::_('COM_JTG_MAXIMAL') . " " . $max; ?>
					<br /> <br /> <input type="file" name="files[]" class="multi"
						maxlength="<?php echo $max; ?>"
						accept="<?php echo implode("|", $accept) ?>" /> <br /> <br /> <input
						type="radio" name="toimport" value="1" checked="checked">
					<?php echo JText::_('COM_JTG_REDIRECT_TO_IMPORT') ?>
					<br /> <input type="radio" name="toimport" value="0">
					<?php echo JText::_('COM_JTG_REDIRECT_TO_IMPORT_NO') ?>
					<input type="hidden" name="option" value="com_jtg" /> <input
						type="hidden" name="controller" value="files" /> <input
						type="hidden" name="task" value="" />
					<?php echo JHtml::_('form.token'); ?>
				</form> <br /> <br /> <input type='submit'
				value='<?php echo JText::_('COM_JTG_UPLOAD') ?>' class='submit'
				onclick="javascript:Joomla.submitbutton('uploadfiles')" />
			</td>
		</tr>
	</tbody>
</table>
