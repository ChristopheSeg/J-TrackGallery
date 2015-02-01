<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @package    Comjtg
 * @author     Christophe Seguinot <christophe@jtrackgallery.net>
 * @copyright  2013 J!Track Gallery, InJooosm and joomGPStracks teams
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

echo $this->lh;
?>
<table width="100%" class="tracktable">
	<thead>
		<tr class="sectiontableheader">
			<th colspan="2" width="100px" align="center"><?php echo JText::_('COM_JTG_CAT'); ?>
			</th>
			<th align="center"><?php echo JText::_('COM_JTG_DESCRIPTION'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$k = 0;
		$imgdir = JUri::base() . "images/jtrackgallery/cats/";
		for ($i=0, $n=count($this->cats); $i<$n; $i++) {
			$cat = $this->cats[$i];
			$cat->img = null;
			if ($cat->image)
				$cat->img = "&nbsp;<img title=\"" . JText::_($cat->title) . "\" alt=\"" . JText::_($cat->title) . "\" src=\"" . $imgdir.$cat->image . "\" />";
			$link = JRoute::_('index.php?option=com_jtg&view=files&layout=list&cat='.$cat->id);
			?>
		<tr class="sectiontableentry<?php echo $k; $k = 1 -$k;?>">
			<td width="10%" align="center"><a href="<?php echo $link; ?>"><?php echo $cat->img; ?>
			</a></td>
			<td><b><a href="<?php echo $link; ?>"><?php echo JText::_($cat->treename); ?>
				</a> </b></td>
			<td><?php echo JText::_($cat->description); ?></td>
		</tr>
		<?php
		}
		?>
	</tbody>
</table>

<?php
echo $this->footer;
