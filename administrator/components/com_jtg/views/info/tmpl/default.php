<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJO3SM and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

defined('_JEXEC') or die('Restricted access');

// toolbar
JToolBarHelper::title(JText::_('COM_JTG_INFO'), 'generic.png');
JToolBarHelper::back($alt= 'COM_JTG_BACK', $href= 'javascript:history.back();');
// JToolBarHelper::save('saveconfig', $alt='COM_JTG_SAVE', 'save.png' );

// jimport('joomla.html.pane');
// JHTML::_('behavior.tooltip');

$link = ".." . DS . "components" . DS . "com_jtg" . DS . "assets" . DS . "images" . DS . "logo_JTG.png";
$version = $this->getVersion();
?>
    <div style="margin: auto">
<div style="float:left; margin-left:30px">
    <table border="0" align="center" cellspacing="2" cellpadding="2">
        <tbody>
            <tr>
                <td colspan="2"><img src="<?php echo $link;?>"  alt="J!Track Gallery"/></td>
            </tr>
            <tr>
                <td><?php echo(JText::_('COM_JTG_DESCRIPTION'));?>:</td>
                <td><?php echo(JText::_('COM_JTG_INFO_TXT'));?></td>
            </tr>
            <tr>
                <td><?php echo(JText::_('COM_JTG_INSTALLED_VERSION'));?>:</td>
                <td><?php echo ($version["this"]);?></td>
            </tr>
            <tr>
                <td><?php echo(JText::_('COM_JTG_LATEST_VERSION'));?>:</td>
                <td><?php echo($version["latest"]);?></td>
            </tr>
            <tr>
                <td><?php echo(JText::_('COM_JTG_DEVELOPPERS'));?>:</td>
                <td><a href="<?php echo(JText::_('COM_JTG_DEVELOPPERS_WEBSITE'));?>">
		    <?php echo(JText::_('COM_JTG_DEVELOPPERS_LIST'));?></a></td>
            </tr>
            <tr>
                <td><?php echo(JText::_('COM_JTG_DEMO_PAGE'));?>:</td>
                <td><a href="http://jtrackgallery.net/demo" target="_blank">http://jtrackgallery.net/demo</a></td>
            </tr>
            <tr>
                <td><?php echo(JText::_('COM_JTG_PROJECT_PAGE'));?>:</td>
                <td><a href="http://jtrackgallery.net" target="_blank">http://jtrackgallery.net</a></td>
            </tr>
            <tr>
                <td><?php echo(JText::_('COM_JTG_SUPPORT'));?>:</td>
                <td><a href="http://jtrackgallery.net/forum" target="_blank">http://jtrackgallery.net/forum</a></td>
            </tr>
            <tr>
                <td><?php echo(JText::_('COM_JTG_LICENSE'));?>:</td>
                <td><a href="http://www.gnu.org/licenses/gpl-2.0.html" target="_blank">GNU/GPL2</a></td>
            </tr>
        </tbody>
    </table>

</div>
<div style="clear:both"></div>
    </div>
<?php

    echo JHTML::_( 'form.token' );
    ?>
    <input type="hidden" name="option" value="com_jtg" />
    <input type="hidden" name="id" value="1" />
    <input type="hidden" name="task" value="" />
</form>
