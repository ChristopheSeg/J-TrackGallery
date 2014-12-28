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
JToolBarHelper::title(JText::_('COM_JTG_MAPS'), 'generic.png');
JToolBarHelper::back();
JToolBarHelper::addNew('newmap', 'COM_JTG_NEW_MAP');
JToolBarHelper::editList('editmap');
//$mapsxml = 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jtg' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'maps' . DIRECTORY_SEPARATOR . 'maps.xml';
//JToolBarHelper::preferences( 'com_jtg', 400, 500, 'Preferences', $mapsxml );
JToolBarHelper::spacer();
JToolBarHelper::publish();
JToolBarHelper::unpublish();
JToolBarHelper::deleteList();
JToolBarHelper::spacer();
JToolBarHelper::help("maps", true);

jimport('joomla.html.pane');
if(JVERSION>=3.0) //Code support for joomla version greater than 3.0
{
	JHtml::_('bootstrap.tooltip');
}
else
{
	JHtml::_('behavior.tooltip');
}

$ordering = ($this->lists['order'] == 'ordering' );

$link = JRoute::_('index.php?option=com_jtg&task=maps&controller=maps&layout=default');
?>
<form action="<?php echo $link ?>" method="post" name="adminForm" id="adminForm" class="adminForm">
<!-- <table>
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_( 'COM_JTG_FILTER' ); ?>:
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'COM_JTG_APPLY' ); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'COM_JTG_RESET' ); ?></button>
		</td>
		<td nowrap="nowrap">
			<?php
				echo $this->state;
			?>
		</td>
	</tr>
</table>-->			
	<table class="adminlist" cellpadding="1">
		<thead>
			<tr>
				<th class="title" nowrap="nowrap"><?php
//					echo JHtml::_('grid.sort', JText::_('COM_JTG_ID'), 'id', @$this->lists['order_Dir'], @$this->lists['order'], 'maps' );
					echo JText::_('COM_JTG_ID');
					?>:</th>
				<th class="title"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(<?php echo $this->total; ?>);" /></th>
				<th class="title"><?php
//					echo JHtml::_('grid.sort', JText::_('COM_JTG_NAME'), 'title', @$this->lists['order_Dir'], @$this->lists['order'], 'maps' );
					echo JText::_('COM_JTG_NAME');
					?>:</th>
<?php if ($ordering) { ?>
				<th class="order"> <?php echo JText::_('COM_JTG_ORDER'); ?>:

					<?php
//					echo JHtml::_('grid.sort', JText::_('COM_JTG_ORDER'), 'order', @$this->lists['order_Dir'], @$this->lists['order'], 'maps' ); ? >:</th>
					?></th>
				<th class="order"><?php echo JHtml::_('grid.order',  $this->maps ); ?></th>
<?php } ?>
				<th class="title"><?php
//					echo JHtml::_('grid.sort', JText::_('COM_JTG_PUBLISHED'), 'published', @$this->lists['order_Dir'], @$this->lists['order'], 'maps' ); ? >:</th>
					echo JText::_('COM_JTG_PUBLISHED'); ?>:</th>
				<th class="title"><?php
					echo JText::_('COM_JTG_OL_PARAMETERS');
					?>:</th>
				<th class="title"><?php
					echo JText::_('COM_JTG_NEEDSCRIPT');
					?>:</th>
				<th class="title"><?php
					echo JText::_('COM_JTG_CODE');
					?>:</th>
			</tr>
		</thead>
		<tbody>
<?php
$k = 0;
$user = JFactory::getUser();
for ($i=0, $n=count( $this->maps ); $i < $n; $i++) {
	//	$map->published
	$map = $this->maps[$i];
	$published 	= JHtml::_('grid.published', $map, $i );
	$checked 	= JHtml::_('grid.checkedout', $map, $i );
	$name		= $this->buildEditKlicks(JText::_($map->name),$i);
	$map_parameters = JHtml::tooltip($map->param, JText::_('COM_JTG_OL_PARAMETERS'), 'tooltip.png', JText::_('COM_JTG_OL_PARAMETERS'));
	$map_script = ($map->script? JHtml::tooltip($map->script, JText::_('COM_JTG_NEEDSCRIPT'), 'tooltip.png', JText::_('COM_JTG_NEEDSCRIPT')) : '<i>' . JText::_('JNONE') . '</i>' );
	$map_code = ($map->code? JHtml::tooltip($map->code, JText::_('COM_JTG_CODE'), 'tooltip.png', JText::_('COM_JTG_CODE'))  : '<i>' . JText::_('JNONE') . '</i>' );
	
?>			<tr class="<?php echo "row$k "; echo($k? "row-odd":"row-even"); $k=1-$k; ?>">
				<td align="center"><?php echo $map->id;									?></td>
                <td align="center"><?php echo $checked;									?></td>
				<td align="center"><?php echo $name;	?></td>
<?php if ($ordering) { ?>
                <td colspan="2" class="order">
                <span><?php echo $this->pagination->orderUpIcon( $i, true,'orderup', 'Move Up', $map->ordering );
                ?></span>
				<span><?php echo $this->pagination->orderDownIcon( $i, $n, true, 'orderdown', 'Move Down', $map->ordering );
				?></span>
				<input type="text" name="order[]" size="2" maxlength="2" value="<?php echo $map->ordering;
				?>" class="text_area" style="text-align: center; width: 3em; display: inline"/>
                </td>
<?php } ?>
				<td align="center"><?php echo $published; ?></td>
				<td align="center"><?php echo $map_parameters; ?></td>
				<td align="center"><?php echo $map_script;?></td>
				<td align="center"><?php echo $map_code;?></td>
			</tr>
<?php } ?>		</tbody>
<!--         <tfoot>
            <tr>
                <td colspan="9">
                    <?php // echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
-->
	</table>
	

	<input type="hidden" name="option" value="com_jtg" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="maps" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	    <?php echo JHtml::_( 'form.token' ); ?>
	
</form>
