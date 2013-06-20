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
JToolBarHelper::title(JText::_('COM_JTG_COMMENTS'), 'categories.png');
JToolBarHelper::back($alt= 'COM_JTG_BACK', $href= 'javascript:history.back();');
JToolBarHelper::spacer();
JToolBarHelper::editList('editComment');
JToolBarHelper::publish();
JToolBarHelper::unpublish();
JToolBarHelper::deleteList();
JToolBarHelper::help( 'comments',true );

JHTML::_('behavior.tooltip');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<table class="adminlist" cellpadding="1">
	<thead>
		<tr>
			<th width="5%" class="title"><?php echo JText::_( 'COM_JTG_NUM' ); ?></th>
			<th width="5%" class="title"><input type="checkbox" name="toggle"
				value="" onclick="checkAll(<?php echo count($this->rows); ?>);" /></th>
			<th width="10%" class="title"><?php echo JText::_('COM_JTG_USER'); ?></th>
			<th width="15%" class="title"><?php echo JText::_('COM_JTG_TRACK'); ?></th>
			<th width="29%" class="title"><?php echo JText::_('COM_JTG_TITLE'); ?></th>
			<th width="10%" class="title"><?php echo JText::_('COM_JTG_DATE'); ?></th>
			<th width="10%" class="title"><?php echo JText::_( 'COM_JTG_PUBLISHED'); ?></th>
			<th width="2%" class="title" nowrap="nowrap"><?php echo JText::_( 'COM_JTG_ID'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
	{
		$row = &$this->rows[$i];

		$link 	= JRoute::_( 'index.php?option=com_jtg&task=editComment&controller=comments&id='. $row->id, false );
		// eh?? terrain??
		$row->checked_out=null;
		$checked 	= JHTML::_('grid.checkedout', $row, $i );
		$published 	= JHTML::_('grid.published', $row, $i );

		?>
		<tr class="<?php echo "row$k"; ?>">
			<td align="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
			<td align="center"><?php echo $checked; ?></td>
			<td align="center"><?php echo $row->user; ?></td>
			<td align="center"><?php echo $row->track; ?></td>
			<td align="center"><span class="hasTip" title="<?php echo JText::_('COM_JTG_COMMENT_TEXT'); ?> :: <?php echo htmlentities($row->text,ENT_QUOTES,'UTF-8'); ?>"><?php echo htmlentities($row->title,ENT_QUOTES,'UTF-8'); ?></span></td>
			<td align="center"><?php echo $row->date; ?></td>
			<td align="center"><?php echo $published;?></td>
			<td><?php echo $row->id; ?></td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
</table>
<input type="hidden" name="option" value="com_jtg" /> <input
	type="hidden" name="task" value="" /> <input type="hidden"
	name="boxchecked" value="0" /> <input type="hidden" name="controller"
	value="comments" /> <?php echo JHTML::_( 'form.token' ); ?></form>
