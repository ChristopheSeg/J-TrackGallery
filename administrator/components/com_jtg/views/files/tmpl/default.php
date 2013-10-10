<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooosm and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

defined('_JEXEC') or die('Restricted access');

// toolbar
JToolBarHelper::title(JText::_('COM_JTG_GPS_FILES'), 'categories.png');
JToolBarHelper::back($alt= 'COM_JTG_BACK', $href= 'javascript:history.back();');
// JToolBarHelper::back();
JToolBarHelper::spacer();
$bar=& JToolBar::getInstance( 'toolbar' );
$folder = JURI::base().'index.php?option=com_jtg&tmpl=component&controller=files&task=upload';
// popup:
$bar->appendButton( 'Popup', 'upload', 'COM_JTG_UPLOAD', $folder, 550, 400 );
// Normal Window:
//JToolBarHelper::custom( 'upload', 'upload.png', 'upload.png', 'Upload', false);
JToolBarHelper::addNew('newfiles',$alt='COM_JTG_NEW_FILES');
JToolBarHelper::editList('editfile');
JToolBarHelper::publish();
JToolBarHelper::unpublish();
JToolBarHelper::custom('toshow','toshow',null,$alt='COM_JTG_TOSHOW_SMALL');
JToolBarHelper::custom('tohide','tohide',null,$alt='COM_JTG_TOHIDE_SMALL');
JToolBarHelper::deleteList('COM_JTG_VALIDATE_DELETE_ITEMS');
JToolBarHelper::help( 'files/default',true );

$ordering = ($this->lists['order'] == 'ordering');
$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base().'components/com_jtg/template.css');

?>
<form action="" method="post" name="adminForm" id="adminForm">
<table>
	<tr>
		<td align="left" width="100%">
			<?php echo JText::_( 'COM_JTG_FILTER' ); ?>: <input type="text" name="search" id="search" value="<?php echo $this->lists['search'];?>" class="text_area" onchange="document.adminForm.submit();" />
			<button onclick="this.form.submit();">
				<?php echo JText::_( 'COM_JTG_GO' ); ?>
			</button>
			<button onclick="document.getElementById('search').value='';this.form.getElementById('filter_state').value='';this.form.submit();">
				<?php echo JText::_( 'COM_JTG_RESET' ); ?>
			</button>
		</td>
		<td nowrap="nowrap"></td>
	</tr>
</table>
<table class="adminlist" cellpadding="1">
	<tfoot>
		<tr>
			<td colspan="14"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	$missingcat = array();
	$missingterrain = false;
	$cfg = $this->cfg;
	$iconpath	= JURI::root() . "components/com_jtg/assets/template/" . $cfg->template . "/images/";
	for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
	{
		$row = &$this->rows[$i];
		$row->groupname = $this->buildRowGroupname($row->access); // wird für die Zugriffsebene benötigt
		if ( $row->access == 9 )
		$access 	= "<font color='orange'>".JText::_('COM_JTG_PRIVATE') . "</font>";
		else
		$access 	= JHTML::_('grid.access', $row, $i );
		$checked 	= JHTML::_('grid.checkedout', $row, $i );
		$published 	= JHTML::_('grid.published', $row, $i );
		$user		= JFactory::getUser($row->uid);
		$imagelink	= $this->buildImageFiletypes($row->istrack,$row->iswp,$row->isroute,$row->iscache);
		$title		= $this->buildEditKlicks($row->title,$i);
		$hidden		= $row->hidden;
		$hiddenlink	= $this->buildHiddenImage($iconpath,$hidden,$i);
		$catids		= explode(",",$row->catid);
		if ( ( $catids === $row->catid ) OR ( $row->catid == "0" ) OR ( $row->catid == "" ) )
		$cats = "<font class=\"emptyEntry\">".JText::_('COM_JTG_NOTHING') . "</font>";
		else
		{
			$cats = "<ul class=\"cattree\">";
			$l = 0;
			foreach ($catids AS $catid) {
				$cattree = $this->parseCatTree($this->cats,$catid);
				if ( count($cattree["missing"]) != 0 )
				{
					foreach ($cattree["missing"] as $miss) {
						if ( !isset($missingcat[$miss]) )
						$missingcat[$miss] = $miss;
					}
				}
				$cats .= "<li>" . $cattree["tree"] . "</li>";
				$l++;
			}
			$cats .= "</ul>";
			if ( $l == 1 ) { // only List if more than 1 entry
				$cats = $this->parseCatTree($this->cats,$catid);
				$cats = $cats["tree"];
			}
		}

		$terrains = $row->terrain;
		$terrains = explode(",",$terrains);
		$terrain = array();
		$model = new JtgModelFiles;
		foreach ($terrains as $v) {
			$tmp = $model->getTerrain("*",false, "WHERE id = " . $v);
			if ( isset( $tmp[0] ) AND ( $tmp[0]->title ) )
			{
				$terrain[] = JText::_($tmp[0]->title);
			}
			else
			{
				$terrain[] = "<font class=\"errorEntry\">" .
				JText::sprintf('COM_JTG_ERROR_MISSING_TERRAINID',$v) .
				"</font>";
				if ( $v != 0 )
				$missingterrain = true;
			}
		}
		if ( ( isset($terrains[0]) ) AND ( $terrains[0] == "" ) )
			$terrain = "<font class=\"emptyEntry\">" . JText::_('JNONE') . "</font>";
		else
			$terrain = implode(", ",$terrain);
		?>
		<tr class="<?php echo "row" . $k; ?>">
			<td align="center"><?php echo $this->pagination->getRowOffset( $i ); ?></td>
			<td align="center"><?php echo $row->id; ?></td>
			<td align="center"><?php echo $checked; ?></td>
			<td align="center"><span class="hasTip" title="<?php echo $row->file ?>"><?php echo $title; ?></span></td>
			<td align="center" nowrap><?php echo $imagelink; ?></td>
			<td align="left"><?php echo JText::_($cats); ?></td>
			<td align="center"><?php echo $terrain; ?></td>
			<td align="center"><?php echo $row->level; ?></td>
			<td align="center"><?php echo $row->date; ?></td>
			<td align="center"><?php echo $published;?></td>
			<td align="center"><?php echo $hiddenlink;?></td>
			<td align="center"><?php echo $access; ?></td>
			<td align="center"><?php echo $user->username;?></td>
		</tr>
		<?php
		$k = 1 - $k;
	} ?>
	</tbody>
<?php
if ( count($missingcat) == 0)
$missingcat = null;
else
{
	$missingcat = "<br /><font class=\"errorEntry\">".
	JText::sprintf('COM_JTG_ERROR_MISSING_CATS').
	"</font>";
}
if ($missingterrain === true) {
	$missingterrain = "<br /><font class=\"errorEntry\">".
	JText::sprintf('COM_JTG_ERROR_MISSING_TERRAINS').
	"</font>";
}
else
{
	$missingterrain = null;
}
?>
	<thead>
		<tr>
			<th class="title"><?php echo JText::_( 'COM_JTG_NUM' ); ?></th>
			<th class="title" nowrap="nowrap"><?php
			echo JHTML::_('grid.sort',
			JText::_('COM_JTG_ID'), 'id', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?>:</th>
			<th class="title"><input type="checkbox" name="toggle" value=""
				onclick="checkAll(<?php
			echo count($this->rows); ?>);" /></th>
			<!--			<th class="title"><?php
			// echo JHTML::_('grid.sort', JText::_('COM_JTG_GPS_FILE'),
	// 'file', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?>:</th>
-->
			<th class="title"><?php
			echo JHTML::_('grid.sort', JText::_('COM_JTG_TITLE')  . "<small> (" . JText::_('COM_JTG_GPS_FILE')  . ")</small> ",
	'title', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?>:</th>
			<th class="title"><?php
			echo JText::_('COM_JTG_GPS_FILETYPE');
			?>:</th>
			<th class="title"><?php
			echo JHTML::_('grid.sort', JText::_('COM_JTG_CAT'),
	'cat', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?>:
<?php echo $missingcat; ?>
</th>
			<th class="title"><?php
			echo JHTML::_('grid.sort', JText::_('COM_JTG_TERRAIN'),
	'terrain', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?>:
<?php echo $missingterrain; ?>
</th>
			<th class="title"><?php
			echo JHTML::_('grid.sort', JText::_('COM_JTG_LEVEL'),
	'level', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?>:</th>
			<th class="title"><?php
			echo JHTML::_('grid.sort', JText::_('COM_JTG_DATE'),
	'date', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?>:</th>
			<th class="title"><?php
			echo JHTML::_('grid.sort', JText::_('COM_JTG_PUBLISHED'),
	'published', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?>:</th>
			<th class="title"><?php
			echo JHTML::_('grid.sort', JText::_('COM_JTG_HIDDEN'),
	'hidden', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?>:</th>
			<th class="title"><?php
			echo JHTML::_('grid.sort', JText::_('COM_JTG_ACCESS_LEVEL'),
	'access', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?>:</th>
			<th class="title"><?php
			echo JHTML::_('grid.sort', JText::_('COM_JTG_INFO_AUTHOR'),
	'uid', @$this->lists['order_Dir'], @$this->lists['order'], 'files' ); ?>:</th>
		</tr>
	</thead>
</table>
<input type="hidden" name="option" value="com_jtg" /> <input
	type="hidden" name="task" value="files" /> <input type="hidden"
	name="boxchecked" value="0" /> <input type="hidden" name="controller"
	value="files" /> <input type="hidden" name="filter_order"
	value="<?php echo $this->lists['order']; ?>" /> <input type="hidden"
	name="filter_order_Dir"
	value="<?php echo $this->lists['order_Dir']; ?>" /> <?php echo JHTML::_( 'form.token' ); ?>
</form>
