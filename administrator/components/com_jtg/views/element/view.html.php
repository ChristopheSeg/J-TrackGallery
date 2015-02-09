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
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML Article Element View class for the Content component
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @since       0.8
 */
class ContentViewElement extends JViewLegacy
{
	/**
	 * function_description
	 *
	 * @param   unknown_type  $cachable  param_description
	 * @param   unknown_type  $urlparams  param_description
	 *
	 * @return return_description
	 */
	public function display($cachable = false, $urlparams = false)
	{
		return false;
		$mainframe = JFactory::getApplication();

		// Initialize variables
		$db			= JFactory::getDBO();
		$nullDate	= $db->getNullDate();

		$document	= JFactory::getDocument();
		$document->setTitle(JText::_('COM_JTG_ARTICLE_SELECTION'));

		JHtml::_('behavior.modal');

		$template = $mainframe->getTemplate();
		$document->addStyleSheet("templates/$template/css/general.css");

		$limitstart = JFactory::getApplication()->input->get('limitstart', '0', '', 'int');

		$lists = $this->_getLists();

		// Ordering allowed ?
		$ordering = ($lists['order'] == 'section_name' && $lists['order_Dir'] == 'ASC');

		$rows = $this->get('List');
		$page = $this->get('Pagination');
		JHtml::_('behavior.tooltip');
		?>
<form
	action="index.php?option=com_content&amp;task=element&amp;tmpl=component&amp;object=id"
	method="post" name="adminForm" id="adminForm">

	<table>
		<tr>
			<td style="width:100%;"><?php echo JText::_('COM_JTG_FILTER'); ?>: <input
				type="text" name="search" id="search"
				value="<?php echo htmlspecialchars($lists['search']);?>"
				class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();">
					<?php echo JText::_('COM_JTG_APPLY'); ?>
				</button>
				<button
					onclick="document.getElementById('search').value='';this.form.submit();">
					<?php echo JText::_('COM_JTG_RESET'); ?>
				</button>
			</td>
			<td nowrap="nowrap"><?php
			echo $lists['sectionid'];
			echo $lists['catid'];
			?>
			</td>
		</tr>
	</table>

	<table class="adminlist" cellspacing="1">
		<thead>
			<tr>
				<th width="5"><?php echo JText::_('COM_JTG_NUM'); ?>
				</th>
				<th class="title"><?php echo JHtml::_('grid.sort',   'Title', 'c.title', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th width="7%"><?php echo JHtml::_('grid.sort',   'Access', 'groupname', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th width="2%" class="title"><?php echo JHtml::_('grid.sort',   'ID', 'c.id', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th class="title" width="15%" nowrap="nowrap"><?php echo JHtml::_('grid.sort',   'Section', 'section_name', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th class="title" width="15%" nowrap="nowrap"><?php echo JHtml::_('grid.sort',   'Category', 'cc.title', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
				<th align="center" width="10"><?php echo JHtml::_('grid.sort',   'Date', 'c.created', @$lists['order_Dir'], @$lists['order']); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15"><?php echo $page->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$k = 0;

			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$row = $rows[$i];
				$link 	= '';
				$date	= JHtml::_('date',  $row->created, JText::_('COM_JTG_DATE_FORMAT_LC4'));
				$access	= JHtml::_('grid.access',   $row, $i, $row->state);
				?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $page->getRowOffset($i); ?>
				</td>
				<td><a style="cursor: pointer;"
					onclick="window.parent.jSelectArticle('<?php echo $row->id; ?>', '<?php echo str_replace(array("'", "\""), array("\\'", ""), $row->title); ?>', '<?php echo JFactory::getApplication()->input->get('object'); ?>');">
				<?php echo htmlspecialchars($row->title, ENT_QUOTES, 'UTF-8'); ?>
				</a>
				</td>
				<td align="center"><?php echo $row->groupname;?>
				</td>
				<td><?php echo $row->id; ?>
				</td>
				<td><?php echo $row->section_name; ?>
				</td>
				<td><?php echo $row->cctitle; ?>
				</td>
				<td nowrap="nowrap"><?php echo $date; ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
			}
			?>
		</tbody>
	</table>

	<input type="hidden" name="boxchecked" value="0" /> <input
		type="hidden" name="filter_order"
		value="<?php echo $lists['order']; ?>" /> <input type="hidden"
		name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
</form>
<?php
	}

	/**
	 * function_description
	 *
	 * @return return_description
	 */
	function _getLists()
	{
		$mainframe = JFactory::getApplication();

		// Initialize variables
		$db		= JFactory::getDBO();

		// Get some variables from the request
		$sectionid			= JFactory::getApplication()->input->get('sectionid', -1, '', 'int');
		$redirect			= $sectionid;
		$option = JFactory::getApplication()->input->get('option');
		$filter_order		= $mainframe->getUserStateFromRequest('articleelement.filter_order',		'filter_order',		'',	'cmd');
		$filter_order_Dir	= $mainframe->getUserStateFromRequest('articleelement.filter_order_Dir',	'filter_order_Dir',	'',	'word');
		$filter_state		= $mainframe->getUserStateFromRequest('articleelement.filter_state',		'filter_state',		'',	'word');
		$catid				= $mainframe->getUserStateFromRequest('articleelement.catid',				'catid',			0,	'int');
		$filter_authorid	= $mainframe->getUserStateFromRequest('articleelement.filter_authorid',		'filter_authorid',	0,	'int');
		$filter_sectionid	= $mainframe->getUserStateFromRequest('articleelement.filter_sectionid',	'filter_sectionid',	-1,	'int');
		$limit				= $mainframe->getUserStateFromRequest('global.list.limit',					'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart			= $mainframe->getUserStateFromRequest('articleelement.limitstart',			'limitstart',		0,	'int');
		$search				= $mainframe->getUserStateFromRequest('articleelement.search',				'search',			'',	'string');

		if (strpos($search, '"') !== false)
		{
			$search = str_replace(array('=', '<'), '', $search);
		}

		$search = JString::strtolower($search);

		// Get list of categories for dropdown filter
		$filter = ($filter_sectionid >= 0) ? ' WHERE cc.section = ' . $db->Quote($filter_sectionid) : '';

		$query = 'SELECT cc.id AS value, cc.title AS text, section' .
				' FROM #__categories AS cc' .
				' INNER JOIN #__sections AS s ON s.id = cc.section' .
				$filter .
				' ORDER BY s.ordering, cc.ordering';
		$lists['catid'] = ContentHelper::filterCategory($query, $catid);

		// Get list of sections for dropdown filter
		$javascript = 'onchange="document.adminForm.submit();"';
		$lists['sectionid'] = JHtml::_('list.section', 'filter_sectionid', $filter_sectionid, $javascript);

		// Table ordering
		$lists['order_Dir']	= $filter_order_Dir;
		$lists['order']		= $filter_order;

		// Search filter
		$lists['search'] = $search;

		return $lists;
	}
}
