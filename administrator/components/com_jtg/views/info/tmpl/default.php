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
 */

defined('_JEXEC') or die('Restricted access');

// Toolbar
JToolBarHelper::title(JText::_('COM_JTG_INFO'), 'generic.png');
JToolBarHelper::back();

$link = "../components/com_jtg/assets/images/logo_JTG.png";
$db = JFactory::getDbo();
$query = $db->getQuery(true);
$query->select('manifest_cache');
$query->from($db->quoteName('#__extensions'));
$query->where('element = "com_jtg"');
$db->setQuery($query);

$manifest = json_decode($db->loadResult(), true);
$version = (string) $manifest['version'];

?>
<div style="margin: auto">
	<div style="float: left; margin-left: 30px">
		<table border="0" align="center" cellspacing="2" cellpadding="2">
			<tbody>
				<tr>
					<td colspan="2"><img src="<?php echo $link;?>"
						alt="J!Track Gallery" /></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JTG_DESCRIPTION');?>:</td>
					<td><?php echo JText::_('COM_JTG_INFO_TXT');?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JTG_CURRENT_INSTALLED_VERSION');?>:</td>
					<td><?php echo $version?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JTG_LATEST_VERSION');?>:</td>
					<td><?php echo JText::_('COM_JTG_LATEST_VERSION_AT');?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JTG_DEVELOPPERS');?>:</td>
					<td><a href="<?php echo JText::_('COM_JTG_DEVELOPPERS_WEBSITE');?>">
							<?php echo JText::_('COM_JTG_DEVELOPPERS_LIST');?>
					</a></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JTG_DEMO_PAGE');?>:</td>
					<td><a href="http://jtrackgallery.net" target="_blank">http://jtrackgallery.net/demo</a>
					</td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JTG_PROJECT_PAGE');?>:</td>
					<td><a href="http://jtrackgallery.net" target="_blank">http://jtrackgallery.net</a>
					</td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JTG_SUPPORT');?>:</td>
					<td><a href="http://jtrackgallery.net/forum" target="_blank">http://jtrackgallery.net/forum</a>
					</td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JTG_LICENSE');?>:</td>
					<td><a href="http://www.gnu.org/licenses/gpl-2.0.html"
						target="_blank">GNU/GPL2</a></td>
				</tr>
			</tbody>
		</table>

	</div>
	<div style="clear: both"></div>
</div>
<?php
echo JHtml::_('form.token');
?>
<input type="hidden"
	name="option" value="com_jtg" />
<input type="hidden" name="id" value="1" />
<input type="hidden" name="task" value="" />
&nbsp;
</form>
