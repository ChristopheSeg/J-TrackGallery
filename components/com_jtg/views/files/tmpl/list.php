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

// Load core.js to enable tableordering
JHtml::_('script', 'system/core.js', false, true);

echo $this->lh;

$iconheight = $this->params->get('jtg_param_list_icon_max_height');
$hide_icon_category = (bool) $this->params->get('jtg_param_tracks_list_hide_icon_category');
$hide_icon_istrack = (bool) $this->params->get('jtg_param_tracks_list_hide_icon_istrack');
$hide_icon_isroundtrip = (bool) $this->params->get('jtg_param_tracks_list_hide_icon_isroundtrip');
$hide_icon_is_wp = (bool) $this->params->get('jtg_param_tracks_list_hide_icon_is_wp');
$hide_icon_isgeocache = (bool) $this->params->get('jtg_param_tracks_list_hide_icon_isgeocache');
$height = ($iconheight > 0? ' style="max-height:' . $iconheight . 'px;" ' : ' ');
$levelMin = $this->params->get('jtg_param_level_from');
$levelMax = $this->params->get('jtg_param_level_to');
$catcolumnwidth = 0;
$catcolumnwidth = $catcolumnwidth + ($hide_icon_category? 0: 2 + $iconheight) ;
$catcolumnwidth = $catcolumnwidth + ($hide_icon_istrack? 0: 2 + $iconheight) ;
$catcolumnwidth = $catcolumnwidth + ($hide_icon_isroundtrip? 0: 2 + $iconheight) ;
$catcolumnwidth = $catcolumnwidth + ($hide_icon_is_wp? 0: 2 + $iconheight) ;
$catcolumnwidth = $catcolumnwidth + ($hide_icon_isgeocache? 0: 2 + $iconheight) ;
$cfg = JtgHelper::getConfig();
$iconpath = JUri::root() . "components/com_jtg/assets/template/" . $cfg->template . "/images/";

// no longer needed JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

// JTG_FILTER_TODO
//Get trackcategory options
JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');
$trackcategory = JFormHelper::loadFieldType('Trackcategory', false);
$trackcategoryOptions=$trackcategory->getOptions(); // works only if you set your field getOptions on public!!
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

<form action="<?php echo JURI::getInstance(); ?>" method="post"
	name="adminForm" id="adminForm">
	<table style="width:100%;">
		<tr>
			<td> <!--   // JTG_FILTER_TODO-->
				<!--
				<fieldset id="filter-bar">
					<div class="filter-search fltlft">
						<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->searchterms); ?>" title="<?php echo JText::_('Search in company, etc.'); ?>" />
						<button type="submit">
							<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>
						</button>
						<button type="button" onclick="document.id('filter_search').value='';this.form.submit();">
							<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
						</button>
					</div>
					<div class="filter-select fltrt">
						xxx<select name="filter_state" class="inputbox" onchange="this.form.submit()">
							<option value="">
								<?php echo JText::_('JOPTION_SELECT_PUBLISHED');?>
							</option>
							<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived'=>false)), 'value', 'text', $this->state->get('filter.state'), true);?>
						</select>

						<select name="filter_type" class="inputbox" onchange="this.form.submit()">
							<option value=""> - Select trackcategory - </option>
							<?php echo JHtml::_('select.options', $trackcategoryOptions, 'value', 'text', $this->state->get('filter.trackcategory'));?>
						</select>

					</div>
				</fieldset>
				 -->
			</td>
			<td><?php echo JText::_('COM_JTG_FILTER'); ?>: <input type="text"
				name="search" id="searchfield"
				value="<?php echo html_entity_decode($this->lists['search']);?>"
				class="text_area" onchange="document.adminForm.submit();" />
				<button class="button" onclick="this.form.submit();">
					<?php echo JText::_('COM_JTG_APPLY'); ?>
				</button>
				<button class="button"
					onclick="document.getElementById('searchfield').value='';this.form.getElementById('filter_state').value='';this.form.submit();">
					<?php echo JText::_('COM_JTG_RESET'); ?>
				</button>
			</td>
			<td style="text-align: right"><?php echo $this->pagination->getPagesCounter(); ?>
			</td>
		</tr>
	</table>
	<div style="overflow-x:auto;">
	<?php if (!count($this->rows)) {
		JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_LIST_NO_TRACK'), 'Warning');
		echo '<b>' . JText::_('COM_JTG_LIST_NO_TRACK') . '</b>';
	} else {
	?>
		<table class="tracktable">
		<thead>
			<tr
				class="sectiontableheader<?php echo $this->escape($this->params->get('pageclass_sfx')); ?>">
				<th>#</th>
				<th><?php echo JHtml::_('grid.sort', JText::_('COM_JTG_TITLE'), 'title', @$this->lists['order_Dir'], @$this->lists['order'], 'files'); ?>
				</th>
				<?php if ($catcolumnwidth > 0) {?>
				<th><?php echo JHtml::_('grid.sort', JText::_('COM_JTG_CAT'), 'cat', @$this->lists['order_Dir'], @$this->lists['order'], 'files'); ?>
				</th>
				<?php } ?>
				<th><?php echo JHtml::_('grid.sort', JText::_('COM_JTG_LEVEL'), 'level', @$this->lists['order_Dir'], @$this->lists['order'], 'files'); ?>
				</th>
				<?php
				if (! $this->params->get("jtg_param_disable_terrains"))
				{
				?>
								<th>
								<?php echo JHtml::_('grid.sort', JText::_('COM_JTG_TERRAIN'), 'terrain', @$this->lists['order_Dir'], @$this->lists['order'], 'files'); ?>
								</th>
				<?php
				}

				if (! $this->params->get("jtg_param_tracks_list_hide_users"))
				{
				?>
								<th>
								<?php echo JHtml::_('grid.sort', JText::_('COM_JTG_USER'), 'user', @$this->lists['order_Dir'], @$this->lists['order'], 'files'); ?>
								</th>
				<?php
				}

				if (! $this->params->get("jtg_param_tracks_list_hide_hits"))
				{
				?>				<th>
								<?php echo JHtml::_('grid.sort', JText::_('COM_JTG_HITS'), 'hits', @$this->lists['order_Dir'], @$this->lists['order'], 'files'); ?>
								</th>
				<?php
				}

				if ($this->cfg->usevote == 1)
				{
				?>
								<th>
								<?php echo JHtml::_('grid.sort', JText::_('COM_JTG_VOTING'), 'vote', @$this->lists['order_Dir'], @$this->lists['order'], 'files'); ?>
								</th>
				<?php
				}
				?>
				<th>
				<?php echo JHtml::_('grid.sort', JText::_('COM_JTG_DISTANCE'), 'distance', @$this->lists['order_Dir'], @$this->lists['order'], 'files'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr class="sectiontablefooter">
				<td colspan="8" align="center"><?php echo $this->pagination->getListFooter();?></td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$k = 0;

			for ($i = 0, $n = count($this->rows); $i < $n; $i++)
			{
				$row = $this->rows[$i];

				if (!$row->title)
				{
					$row->title = "<font class=\"emptyEntry\">" . JText::_('COM_JTG_NO_TITLE') . "</font>";
				}
				$link_only='';
				switch ($row->access)
				{
					case 1:
						$link_only = "&nbsp;<img alt=\"" . JText::_('COM_JTG_REGISTERED') . "\" src=\"" . $iconpath . "registered_only.png\" />";
						break;
					case 2:
						$link_only = "&nbsp;<img alt=\"" . JText::_('COM_JTG_ADMINISTRATORS') . "\" src=\"" . $iconpath . "special_only.png\" />";
						break;
					case 9:
						$link_only = "&nbsp;<img alt=\"" . JText::_('COM_JTG_PRIVATE') . "\" src=\"" . $iconpath . "private_only.png\" />";
						break;
				}
				$link = JRoute::_('index.php?option=com_jtg&view=files&layout=file&id=' . $row->id, false);
				$profile = JtgHelper::getProfileLink($row->uid, $row->user);
				$cat = JtgHelper::parseMoreCats($this->sortedcats, $row->catid, "list", true, $iconheight);
				$cat = $cat ? $cat: "<img $height src =\"/components/com_jtg/assets/images/cats/symbol_inter.png\" />\n";
				$terrain = JtgHelper::parseMoreTerrains($this->sortedter, $row->terrain, "list", true);
				$hits = JtgHelper::getLocatedFloat($row->hits);
				$layoutHelper = new LayoutHelper;
				$votes = $layoutHelper->parseVoteFloat($row->vote, true);
				$links = null;
				$imagelink = $this->buildImageFiletypes($row->istrack, $row->iswp, $row->isroute, $row->iscache, $row->isroundtrip, $iconheight,
						$hide_icon_istrack, $hide_icon_is_wp, 0, $hide_icon_isgeocache, $hide_icon_isroundtrip);

				$level = JtgHelper::getLevelIcon($row->level, $row->catid, $levelMin, $levelMax, $iconheight);

				if (!$row->distance)
				{
					$row->distance = 0;
				}

				if ($this->cfg->unit == "Miles")
				{
					$distance = JtgHelper::getLocatedFloat(JtgHelper::getMiles($row->distance, "-", "Miles"));
				}
				else
				{
					$distance = JtgHelper::getLocatedFloat($row->distance, "-", "km");
				}

				if ($profile != "")
				{
					$profile .= "&nbsp;";
				}
				else
				{
					$profile .= "<font class=\"emptyEntry\">" . JText::_('COM_JTG_NO_USER') . "</font>&nbsp;";
				}


				if (JtgHelper::userHasFrontendRights($row->uid))
				{
					$editlink = JRoute::_('index.php?option=com_jtg&view=files&layout=form&id=' . $row->id, false);
					$links = " <a href=\"" . $editlink . "\">" .
					"<img title=\"" . JText::_('JACTION_EDIT') . "\" alt=\"" .
						JText::_('JACTION_EDIT') . "\" src=\"" . JUri::root() . "components/com_jtg/assets/images/edit_f2.png\" width=\"16px\" />" .
					"</a> ";
				}
				if (jtgHelper::userHasFrontendDeleteRights($row->uid))
				{
					$deletelink = JRoute::_('index.php?option=com_jtg&controller=files&task=delete&id=' . $row->id, false);
					$links .= "<a href=\"" . $deletelink . "\" onclick=\"return confirm('". JText::_('COM_JTG_VALIDATE_DELETE_TRACK') ."')\">" .
						"<img title=\"" . JText::_('JACTION_DELETE') . "\" alt=\"" .
						JText::_('JACTION_DELETE') . "\" src=\"" . JUri::root() . "components/com_jtg/assets/images/cancel_f2.png\" width=\"16px\" />" .
					"</a>";
				}
				?>
			<tr class="sectiontableentry<?php echo $k; ?>">
				<td><?php echo $this->pagination->getRowOffset($i) . $links; ?></td>
				<td><a href="<?php echo $link; ?>">
					<?php echo $row->title; ?> </a><?php echo $link_only?></td>
				<?php if ($catcolumnwidth > 0) {?>
					<td  width="<?php echo $catcolumnwidth . 'px'; ?>">
				<?php echo '<span class="fileis">' . $cat . ' ' . $imagelink . '</span>'; ?></td>
				<?php }?>
				<td><?php echo $level; ?></td>
				<?php
				if (! $this->params->get("jtg_param_disable_terrains"))
				{
				?>
								<td><?php echo $terrain; ?></td>
				<?php
				}

				if (! $this->params->get("jtg_param_tracks_list_hide_users"))
				{
				?>

								<td><?php echo $profile; ?></td>
				<?php
				}

				if (! $this->params->get("jtg_param_tracks_list_hide_hits"))
				{
				?>
								<td><?php echo $hits; ?></td>
				<?php
				}

				if ($this->cfg->usevote == 1)
				{
				?>
								<td><?php echo $votes; ?></td>
				<?php
				}
				?>
				<td><?php echo $distance; ?></td>
			</tr>
			<?php
			$k = 1 - $k;
			}
			?>
		</tbody>
	</table>
	<?php } ?>
	</div>
	<input type="hidden" name="option" value="com_jtg" /> <input
		type="hidden" name="task" value="" /> <input type="hidden"
		name="filter_order" value="<?php echo $this->lists['order']; ?>" /> <input
		type="hidden" name="filter_order_Dir"
		value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
<?php
echo $this->footer;
