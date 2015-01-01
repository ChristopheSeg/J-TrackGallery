<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 * 
 * @package    Comjtg
 * @author     Christophe Seguinot <christophe@jtrackgallery.net>
 * @copyright  2013 J!Track Gallery, InJooosm and joomGPStracks teams
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

defined('_JEXEC') or die('Restricted access');

// toolbar
JToolBarHelper::title(JText::_('COM_JTG_COMMENTS'), 'categories.png');
JToolBarHelper::back();
JToolBarHelper::spacer();
JToolBarHelper::editList('editComment');
JToolBarHelper::publish();
JToolBarHelper::unpublish();
JToolBarHelper::deleteList();
JToolBarHelper::help( 'comments',true );

if(JVERSION>=3.0) //Code support for joomla version greater than 3.0
{
	JHtml::_('bootstrap.tooltip');
}
else
{
	JHtml::_('behavior.tooltip');
}

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
<table class="adminlist" cellpadding="1">
	<thead>
		<tr>
			<th class="title"><?php echo JText::_( 'COM_JTG_NUM' ); ?></th>
			<th class="title"><input type="checkbox" onclick="Joomla.checkAll(this)" title="<?php echo JText::_( 'JGLOBAL_CHECK_ALL' ); ?>" value="" name="checkall-toggle"></th>
			<th class="title"><?php echo JText::_('COM_JTG_USER'); ?></th>
			<th class="title"><?php echo JText::_('COM_JTG_TRACK'); ?></th>
			<th class="title"><?php echo JText::_('COM_JTG_COMMENT_TITLE'); ?></th>
			<th class="title"><?php echo JText::_('COM_JTG_DATE'); ?></th>
			<th class="title"><?php echo JText::_( 'COM_JTG_PUBLISHED'); ?></th>
			<th class="title" nowrap="nowrap"><?php echo JText::_( 'COM_JTG_ID'); ?></th>
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
		$row = $this->rows[$i];

		$link 	= JRoute::_( 'index.php?option=com_jtg&task=editComment&controller=comments&id='. $row->id, false );
		// eh?? terrain??
		$row->checked_out=null;
		$checked 	= JHtml::_('grid.checkedout', $row, $i );
		$published 	= JHtml::_('grid.published', $row, $i );

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
<?php 
if ($n==0) 
{
	echo '<br><br><b>' . JText::_('COM_JTG_NO_COMMENTS') . '</b>';
}
?>

<input type="hidden" name="option" value="com_jtg" /> <input
	type="hidden" name="task" value="" /> <input type="hidden"
	name="boxchecked" value="0" /> <input type="hidden" name="controller"
	value="comments" /> <?php echo JHtml::_( 'form.token' ); ?>
	&nbsp;
	</form>
