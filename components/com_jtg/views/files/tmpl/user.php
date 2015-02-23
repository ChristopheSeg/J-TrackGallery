<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @package     Comjtg
 * @subpackage  Frontend
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
$user = JFactory::getUser();

if ($user->id != 0)
{
	echo $this->lh;
	?>
<script type="text/javascript">

	 Joomla.tableOrdering = function( order, dir, task )
	{
		var form = document.adminForm;

		form.filter_order.value 	= order;
		form.filter_order_Dir.value	= dir;
		document.adminForm.submit( task );
	}
</script>
<form action="<?php echo $this->action; ?>" method="post"
	name="adminForm" id="adminForm">
	<table style="width:100%;">
		<tr>
			<td><?php echo JText::_('JGLOBAL_DISPLAY_NUM') . '&nbsp;' . $this->pagination->getLimitBox(); ?>
			</td>
			<td style="text-align: right"><?php echo $this->pagination->getResultsCounter(); ?>
			</td>
		</tr>
	</table>
	<table class="tracktable">
		<thead>
			<tr
				class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
				<th width="60px">#</th>
				<th><?php echo JHtml::_('grid.sort', JText::_('COM_JTG_TITLE'), 'title', @$this->lists['order_Dir'], @$this->lists['order'], 'files'); ?>
				</th>
				<th width="80px"><?php echo JHtml::_('grid.sort', JText::_('COM_JTG_CAT'), 'cat', @$this->lists['order_Dir'], @$this->lists['order'], 'files'); ?>
				</th>
				<th width="50px"><?php echo JHtml::_('grid.sort', JText::_('COM_JTG_HITS'), 'hits', @$this->lists['order_Dir'], @$this->lists['order'], 'files'); ?>
				</th>
				<th width="80px"><?php echo JHtml::_('grid.sort', JText::_('COM_JTG_TERRAIN'), 'terrain', @$this->lists['order_Dir'], @$this->lists['order'], 'files'); ?>
				</th>
				<th width="20px"><?php echo JHtml::_('grid.sort', JText::_('COM_JTG_VOTING'), 'vote', @$this->lists['order_Dir'], @$this->lists['order'], 'files'); ?>
				</th>
				<th width="20px"><?php echo JHtml::_('grid.sort', JText::_('COM_JTG_DISTANCE'), 'distance', @$this->lists['order_Dir'], @$this->lists['order'], 'files'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php
			$edit = JTExt::_('edit');
			$delete = JTExt::_('delete');
			$edit = "title=\"" . $edit . "\" alt=\"" . $edit . "\"";
			$delete = "title=\"" . $delete . "\" alt=\"" . $delete . "\"";
			$k = 0;

			for ($i = 0, $n = count($this->rows); $i < $n; $i++)
			{
				$row = $this->rows[$i];
				$terrain = JtgHelper::parseMoreTerrains($this->sortedter, $row->terrain, "array");
				$terrain = implode(", ", $terrain);

				if ($this->cfg->unit == "Miles")
				{
					$distance = JtgHelper::getLocatedFloat(JtgHelper::getMiles($row->distance, "-", "Miles"));
				}
				else
				{
					$distance = JtgHelper::getLocatedFloat($row->distance, "-", "km");
				}

				$votes = LayoutHelper::parseVoteFloat($row->vote);
				$link = JRoute::_('index.php?option=com_jtg&view=files&layout=file&id=' . $row->id, false);
				$cats = JtgHelper::parseMoreCats($this->cats, $row->catid, "array");
				$cats = implode(", ", $cats);
				?>
			<tr class="sectiontableentry<?php echo $k; ?>">
				<td align="center"><?php echo $this->pagination->getRowOffset($i); ?>
					<a
					href="index.php?option=com_jtg&view=files&layout=form&id=<?php echo $row->id; ?>">
						<img <?php echo $edit ?>
						src="/components/com_jtg/assets/images/edit_f2.png" width="16px" />
				</a> <a
					href="index.php?option=com_jtg&controller=files&task=delete&id=<?php echo $row->id; ?>">
						<img <?php echo $delete ?>
						src="/components/com_jtg/assets/images/cancel_f2.png" width="16px" />
				</a>
				</td>
				<td><a href="<?php echo $link; ?>">
					<?php echo $row->title; ?> </a></td>
				<td><?php echo $cats; ?></td>
				<td><?php echo $row->hits; ?></td>
				<td><?php echo $terrain; ?></td>
				<td><?php echo $votes; ?></td>
				<td><?php echo $distance; ?></td>
			</tr>
			<?php
			$k = 1 - $k;
			}
			?>
		</tbody>
		<tfoot>
			<tr class="sectiontablefooter">
				<td colspan="7" align="center"><?php echo $this->pagination->getPagesLinks($this->rows); ?>
					<?php echo $this->pagination->getResultsCounter(); ?></td>
			</tr>
		</tfoot>
	</table>
	<input type="hidden" name="option" value="com_jtg" /> <input
		type="hidden" name="filter_order"
		value="<?php echo $this->lists['order']; ?>" /> <input type="hidden"
		name="filter_order_Dir"
		value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
<?php
}
else
{
	JResponse::setHeader('HTTP/1.0 403', true);
	JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_ALERT_NOT_AUTHORISED'), 'Error');
}

echo $this->footer;
