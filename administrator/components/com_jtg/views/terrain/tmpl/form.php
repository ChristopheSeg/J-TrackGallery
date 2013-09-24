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

if($this->id < 1)
{
	$title = JText::_('COM_JTG_ADD_TERRAIN');
	$save["func"] = 'save';
	$save["name"] = 'COM_JTG_SAVE';
	$terrain = new stdClass;
	$terrain->title = "";
	$terrain->published = 1;
	$terrain->checked_out = 0;
	$terrain->ordering = 0;
}
else
{
	$title = JText::_('COM_JTG_UPDATE_TERRAIN');
	$save["func"] = 'update';
	$save["name"] = 'COM_JTG_EDIT';
	$id = $this->id;
	$terrain = $this->terrain;
}
JToolBarHelper::title($title, 'categories.png');
JToolBarHelper::back($alt= 'COM_JTG_BACK', $href= 'javascript:history.back();');
JToolBarHelper::spacer();
JToolBarHelper::save($save["func"], $save["name"], 'save.png' );
JToolBarHelper::help( 'terrain/form',true );
?>
<form action="" method="post" name="adminForm" id="adminForm" class="adminForm">
    <table class="adminlist" cellpadding="1">
        <thead>
            <tr>
                <th colspan="2" align="center"><?php echo $title; ?></th>
            </tr>
        </thead>
        <tbody>
<?php if ( $this->id ) { ?>
            <tr>
                <td><?php echo JText::_('COM_JTG_ID'); ?></td>
                <td><?php echo $this->id ?></td>
            </tr>
<?php } ?>
            <tr>
                <td width="150px"><?php echo JText::_('COM_JTG_TITLE'); ?></td>
                <td><input type="text" name="title" value="<?php echo $terrain->title ?>" /> (<?php echo JText::_($terrain->title); ?>)</td>
            </tr>
            <tr>
                <td><?php echo JText::_('COM_JTG_PUBLISHED'); ?></td>
                <td><?php echo $this->lists['block']; ?></td>
            </tr>
        </tbody>
    </table>
    <?php echo JHTML::_( 'form.token' ); ?>
    <input type="hidden" name="option" value="com_jtg" />
    <input type="hidden" name="controller" value="terrain" />
    <input type="hidden" name="task" value="" />
<?php
if ($this->id)
	echo ("<input type=\"hidden\" name=\"id\" value=\"" . $this->id . "\" />")
?>
</form>
