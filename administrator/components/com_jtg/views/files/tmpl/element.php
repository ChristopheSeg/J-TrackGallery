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
$ordering = ($this->lists['order'] == 'ordering');
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::base().'components/com_jtg/template.css');

?>
    <form action="" method="post" name="adminForm" id="adminForm">
<table>
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_( 'COM_JTG_FILTER' ); ?>:
			<input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();"><?php echo JText::_( 'COM_JTG_APPLY' ); ?></button>
			<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();"><?php echo JText::_( 'COM_JTG_RESET' ); ?></button>
		</td>
		<td nowrap="nowrap">
		</td>
	</tr>
</table>
    <table class="adminlist" cellpadding="1">
        <thead>
            <tr>
		<th class="title"><?php echo JText::_( 'COM_JTG_NUM' ); ?></th>
		<th class="title" nowrap="nowrap"><?php
			echo JHtml::_('grid.sort',
			JText::_('COM_JTG_ID'), 'id', @$this->lists['order_Dir'], @$this->lists['order'], 'element' ); ?>:</th>
		<th class="title"><?php
	echo JHtml::_('grid.sort', JText::_('COM_JTG_GPS_FILE'),
	'file', @$this->lists['order_Dir'], @$this->lists['order'], 'element' ); ?>:</th>
		<th class="title"><?php
	echo JText::_('COM_JTG_GPS_FILETYPE');
	?>:</th>
		<th class="title"><?php
	echo JHtml::_('grid.sort', JText::_('COM_JTG_TITLE'),
	'title', @$this->lists['order_Dir'], @$this->lists['order'], 'element' ); ?>:</th>
		<th class="title"><?php
	echo JHtml::_('grid.sort', JText::_('COM_JTG_CAT'),
	'cat', @$this->lists['order_Dir'], @$this->lists['order'], 'element' ); ?>:</th>
		<th class="title"><?php
	echo JHtml::_('grid.sort', JText::_('COM_JTG_TERRAIN'),
	'terrain', @$this->lists['order_Dir'], @$this->lists['order'], 'element' ); ?>:</th>
		<th class="title"><?php
	echo JHtml::_('grid.sort', JText::_('COM_JTG_LEVEL'),
	'level', @$this->lists['order_Dir'], @$this->lists['order'], 'element' ); ?>:</th>
		<th class="title"><?php
	echo JHtml::_('grid.sort', JText::_('COM_JTG_DATE'),
	'date', @$this->lists['order_Dir'], @$this->lists['order'], 'element' ); ?>:</th>
		<th class="title"><?php
	echo JHtml::_('grid.sort', JText::_('COM_JTG_PUBLISHED'),
	'published', @$this->lists['order_Dir'], @$this->lists['order'], 'element' ); ?>:</th>
		<th class="title"><?php
	echo JHtml::_('grid.sort', JText::_('COM_JTG_ACCESS_LEVEL'),
	'access', @$this->lists['order_Dir'], @$this->lists['order'], 'element' ); ?>:</th>
		<th class="title"><?php
	echo JHtml::_('grid.sort', JText::_('COM_JTG_INFO_AUTHOR'),
	'uid', @$this->lists['order_Dir'], @$this->lists['order'], 'element' ); ?>:</th>
	</tr>
    </thead>
        <tfoot>
            <tr>
                <td colspan="12">
                    <?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
         <?php
	$k = 0;
	for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
	{
		$row = $this->rows[$i];
		$row->groupname = $this->buildRowGroupname($row->access); // wird für die Zugriffsebene benötigt
		$access 	= $this->buildRowGroupname($row->access,true);
$published	= $row->published;
if($published == 0)
	$published = "<font color=red>".JText::_('JNO') . "</font>";
else
	$published = "<font color=green>".JText::_('JYES') . "</font>";
$user		= JFactory::getUser($row->uid);
		$imagelink	= $this->buildImageFiletypes($row->istrack,$row->iswp,$row->isroute);
		$file		= $this->buildChooseKlicks($row->id,$row->title);
		$parent		= $this->giveParentCat($row->catid);
		if ( $parent !== null )
			$row->cat = $parent . "<br />&nbsp;&nbsp;&nbsp;|_&nbsp;&nbsp;&nbsp;" . $row->cat;
		if($row->cat === null)
			$row->cat = "<i>".JText::_('COM_JTG_NOTHING') . "</i>";

        ?>
             <tr class="<?php echo "row$k "; echo($k? "row-odd":"row-even"); ?>">
                <td align="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
                <td align="center"><?php echo $row->id; ?></td>
                <td align="center"><?php echo $file; ?></td>
                <td align="center" nowrap><?php echo $imagelink; ?></td>
                <td align="center"><?php echo $row->title; ?></td>
                <td align="left"><?php echo $row->cat; ?></td>
                <td align="center"><?php echo $row->terrain; ?></td>
                <td align="center"><?php echo $row->level; ?></td>
                <td align="center"><?php echo $row->date; ?></td>
                <td align="center"><?php echo $published;?></td>
                <td align="center"><?php echo $access; ?></td>
                <td align="center"><?php echo $user->username;?></td>
             </tr>
             <?php
             $k = 1 - $k;
    }        ?>
         </tbody>
    </table>
	<input type="hidden" name="option" value="com_jtg" />
	<input type="hidden" name="task" value="element" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="controller" value="element" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>
