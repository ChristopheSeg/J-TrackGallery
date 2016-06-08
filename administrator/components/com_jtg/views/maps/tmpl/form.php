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
$id = $this->_models['maps']->_id;

// Toolbar
if ($id < 1)
{
	$title = JText::_('COM_JTG_ADD_MAP');
}
else
{
	$title = JText::_('COM_JTG_EDIT_MAP');
}

JToolBarHelper::title($title, 'categories.png');
JToolBarHelper::back();
JToolBarHelper::spacer();

if ($id < 1)
{
	JToolBarHelper::save('savemap', $alt = 'COM_JTG_SAVE', 'save.png');
}
else
{
	JToolBarHelper::save('updatemap', $alt = 'COM_JTG_SAVE', 'save.png');

	// Find correct map
	$maps = $this->maps;

	foreach ($maps AS $map)
	{
		if ($map->id == $id)
		{
			break;
		}
	}
}

JToolBarHelper::help("maps/newmap", true);
	$document = JFactory::getDocument();
if ($id)
{
	$cache = JFactory::getCache('com_jtg');
	$cfg = JtgHelper::getConfig();
	$model = $this->getModel();

}

if (JVERSION >= 3.0)
{
	$style = "	select, textarea, input{
	width: auto !important;}";
	$document->addStyleDeclaration($style);
}
?>
<form action="" method="post" name="adminForm" id="adminForm"
	class="adminForm" enctype="multipart/form-data">
	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="3" align="center"><?php echo $title; ?></th>
			</tr>
		</thead>
		<tbody>
<?php if ($id)
{
?>
			<tr>
				<td>Id:</td>
				<td><?php echo $id; ?></td>
			</tr>
<?php
}
?>
			<tr>
				<td><?php echo JText::_('COM_JTG_NAME'); ?>:*</td>
				<td><input id="name" type="text" name="name"
					value="<?php echo isset($map->name)? $map->name: (string) $id; ?>"
					size="50" maxlength="50" /> (<?php echo isset($map->name)? JText::_($map->name): (string) $id; ?>)
				</td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_JTG_PUBLISHED'); ?>:*</td>
				<td><?php echo $this->list['published']; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_JTG_OL_PARAMETERS'); ?>:*</td>
						<?php
						$replace = array("'",'"');
						$with = array("'","&quot;");

						if (($id) AND (isset($map->param)))
						{
							$param = str_replace($replace, $with, $map->param);
						}
						else
						{
							$param = '';
						}
						?>
				<td><textarea id="param" name="param"
						cols="100" maxlength="500" rows="8"><?php echo $param; ?></textarea>
				</td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_JTG_NEEDSCRIPT'); ?>:</td>
				<td><input id="script" type="text" name="script"
					value="<?php
if (($id) AND (isset($map->script)))
{
	$script = str_replace($replace, $with, $map->script);
	echo $script;
}
?>"
					size="100" maxlength="300" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_JTG_CODE'); ?>:</td>
				<td><input id="code" type="text" name="code"
					value="<?php

if (($id) AND (isset($map->code)))
{
	$code = str_replace($replace, $with, $map->code);
	echo $code;
}
?>"
					size="100" maxlength="300" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_JTG_ORDER'); ?>:*</td>
				<td><input id="order" type="text" name="order"
					value="<?php echo $id AND isset($map->ordering)? $map->ordering: '99'; ?>"
					size="2" maxlength="2">
				</td>
			</tr>
		</tbody>
	</table>
	<?php
	echo JHtml::_('form.token'); ?>
	<input type="hidden" name="option" value="com_jtg" /> <input
		type="hidden" name="controller" value="maps" /> <input type="hidden"
		name="checked_out" value="0" /> <input type="hidden" name="task"
		value="maps" />
<?php
if ($id)
{
?>
	<input type="hidden" name="id" value="<?php echo $id; ?>" />

<?php
}
?>
</form>
