<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooosm and joomGPStracks teams
 * @package    com_jtg
 * @subpackage frontend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

echo $this->lh;
?>
<script language="javascript" type="text/javascript">

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
<table width="100%">
	<tr>
		<td><?php echo JText::_( 'COM_JTG_FILTER' ); ?>: <input type="text"
			name="search" id="searchfield"
			value="<?php echo html_entity_decode($this->lists['search']);?>"
			class="text_area" onchange="document.adminForm.submit();" />
		<button class="button" onclick="this.form.submit();"><?php echo JText::_( 'COM_JTG_APPLY' ); ?></button>
		<button class="button"
			onclick="document.getElementById('searchfield').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'COM_JTG_RESET' ); ?></button>
			<?php echo JText::_('Display Num') .'&nbsp;' . $this->pagination->getLimitBox(); ?>
		</td>
		<td style="text-align: right"><?php echo $this->pagination->getResultsCounter( ); ?>
		</td>
	</tr>
</table>
<table class="tracktable">
	<thead>
		<tr class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
			<th>#</th>
			<th><?php echo JHTML::_('grid.sort', JText::_( 'COM_JTG_TITLE' ), 'title', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?></th>
			<th width="80px"><?php echo JHTML::_('grid.sort', JText::_( 'COM_JTG_CAT' ), 'cat', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?></th>
			<th width="80px"><?php echo JHTML::_('grid.sort', JText::_( 'COM_JTG_TERRAIN' ), 'terrain', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?></th>
			<th width="80px"><?php echo JHTML::_('grid.sort', JText::_( 'COM_JTG_USER' ), 'user', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?></th>
			<th width="20px"><?php echo JHTML::_('grid.sort', JText::_( 'COM_JTG_HITS' ), 'hits', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?></th>
			<th width="20px"><?php echo JHTML::_('grid.sort', JText::_( 'COM_JTG_VOTING' ), 'vote', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?></th>
			<th width="80px"><?php echo JHTML::_('grid.sort', JText::_( 'COM_JTG_DISTANCE' ), 'distance', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr class="sectiontablefooter">
			<td colspan="8" align="center"><?php echo $this->pagination->getPagesLinks( $this->rows ); ?>
			<?php echo $this->pagination->getResultsCounter( ); ?></td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
	{
		$row = &$this->rows[$i];
		if (!$row->title) $row->title = "<font class=\"emptyEntry\">".JText::_('COM_JTG_NO_TITLE') . "</font>";
		$link = JRoute::_('index.php?option=com_jtg&view=files&layout=file&id='.$row->id,false);
		$profile = JtgHelper::getProfileLink($row->uid, $row->user);
		$cat = JtgHelper::parseMoreCats($this->sortedcats,$row->catid,"list",true);
		$terrain = JtgHelper::parseMoreTerrains($this->sortedter,$row->terrain,"list",true);
		$hits = JtgHelper::getLocatedFloat($row->hits);
		$votes = layoutHelper::parseVoteFloat($row->vote,true);
		$links = null;
		if(!$row->distance) $row->distance = 0;
		if($this->cfg->unit == "Miles") {
			$distance = JtgHelper::getLocatedFloat(JtgHelper::getMiles($row->distance,"-","Miles"));
		} else {
			$distance = JtgHelper::getLocatedFloat($row->distance,"-","km");
		}
		if ($profile != "")
			$profile .= "<br />";
		else
			$profile .= "<font class=\"emptyEntry\">" . JText::_('COM_JTG_NO_USER')  . "</font><br />";
		if ( 
		( ( $this->uid != 0 ) AND ( $this->uid == $row->uid ) ) // My File
		OR
		( JFactory::getUser()->get('isRoot') ) ) // I am Admin 
		{
			// I can edit and delete
			$editlink = JRoute::_('index.php?option=com_jtg&view=files&layout=form&id='.$row->id,false);
			$deletelink = JRoute::_('index.php?option=com_jtg&controller=files&task=delete&id='.$row->id,false);
			$links =
			" <a href=\"" . $editlink . "\">".
				"<img title=\"".JText::_('Edit') . "\" alt=\"".JText::_('Edit') . "\" src=\"/components/com_jtg/assets/images/edit_f2.png\" width=\"16px\" />".
			"</a> ".
        	"<a href=\"" . $deletelink . "\">".
        		"<img title=\"".JText::_('Delete') . "\" alt=\"".JText::_('Delete') . "\" src=\"/components/com_jtg/assets/images/cancel_f2.png\" width=\"16px\" />".
        	"</a>";
		}
		?>
		<tr class="sectiontableentry<?php echo $k; ?>">
			<td><?php echo $this->pagination->getRowOffset( $i ).$links; ?></td>
			<td><a href="<?php echo $link; ?>"><?php echo $row->title; ?></a></td>
			<td><?php echo $cat; ?></td>
			<td><?php echo $terrain; ?></td>
			<td><?php echo $profile; ?></td>
			<td><?php echo $hits; ?></td>
			<td><?php echo $votes; ?></td>
			<td><?php echo $distance; ?></td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
</table>
<input type="hidden" name="option" value="com_jtg" /> <input
	type="hidden" name="task" value="" /> <input type="hidden"
	name="filter_order" value="<?php echo $this->lists['order']; ?>" /> <input
	type="hidden" name="filter_order_Dir"
	value="<?php echo $this->lists['order_Dir']; ?>" /></form>
	<?php
	echo $this->footer;
