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

// No direct access
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('COM_JTG_EDIT_CAT'), 'categories.png');
JToolBarHelper::back();
JToolBarHelper::spacer();
JToolBarHelper::save('updatecat', $alt = 'COM_JTG_SAVE', 'save.png');
JToolBarHelper::help('cats/form', true);

$image = $this->data->image;
?>
<form action="" method="post" name="adminForm" id="adminForm"
	class="adminForm" enctype="multipart/form-data">
	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="2" align="center"><?php echo JText::_('COM_JTG_ADD_CAT'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td width="150px"><?php echo JText::_('COM_JTG_TITLE'); ?></td>
				<td><input type="text" name="title"
					value="<?php echo $this->data->title; ?>" /> (
					<?php echo JText::_($this->data->title);?>)
				</td>
			</tr>
			<tr>
				<td width="150px"><?php echo JText::_('COM_JTG_PARENT'); ?></td>
				<td><?php echo $this->lists['parent']; ?></td>
			</tr>
			<tr>
				<td width="150px"><?php echo JText::_('COM_JTG_PUBLISHED'); ?></td>
				<td><?php echo $this->lists['block']; ?></td>
			</tr>
			<tr>
				<td width="150px"><?php echo JText::_('COM_JTG_CATS_DEFAULT_MAP'); ?></td>
				<td><?php echo $this->lists['default_map']; ?></td>
			</tr>
			<tr>
				<td width="150px"><?php echo JText::_('COM_JTG_CATS_DEFAULT_OVERLAYS'); ?></td>
				<td><?php echo $this->lists['default_overlays']; ?></td>
			</tr>
			<tr>
				<td width="150px"><?php echo JText::_('COM_JTG_USEPACE'); ?></td>
				<td><?php echo $this->lists['usepace']; ?></td>
			</tr>
			<tr>
				<td width="150px"><?php echo JText::_('COM_JTG_IMAGE'); ?></td>
				<td><input type="radio" name="catpic" value=""
					title="<?php echo JText::_('COM_JTG_NONE'); ?>"
					<?php echo !$image? ' checked="checked"': ''; ?>>
					<?php echo JText::_('COM_JTG_NONE'); ?>
					&nbsp; <?php

					foreach ($this->images as $img)
					{
						$imageurl = JUri::root() . 'images/jtrackgallery/cats/';
						$pic = "";
						$pic .= "<input type=\"radio\" name=\"catpic\" value=\"" . $img . "\" title=\"" . $img . "\"";

						if ( $image == $img )
						{
							$pic .= " checked=\"checked\"";
						}

							$pic .= ">\n<img src=\"" .
								$imageurl . $img .
								"\" title=\" . $img . \" />\n";
					echo $pic;
					}
					?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_JTG_DESC_JTEXT_ALLOWED'); ?></td>
				<td><?php echo $this->editor->display('desc', $this->data->description, '500', '200', '75', '10', false, null); ?>
				</td>
			</tr>
		</tbody>
	</table>
	<?php echo JHtml::_('form.token'); ?>
	<input type="hidden" name="id" value="<?php echo $this->data->id; ?>">
	<input type="hidden" name="option" value="com_jtg" /> <input
		type="hidden" name="controller" value="cats" /> <input type="hidden"
		name="task" value="" />
</form>
