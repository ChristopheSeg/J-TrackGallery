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

// No direct access
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('COM_JTG_ADD_CAT'), 'categories.png');
JToolBarHelper::back($alt= 'COM_JTG_BACK', $href= 'javascript:history.back();');
JToolBarHelper::spacer();
JToolBarHelper::save('savecat', $alt='COM_JTG_SAVE', 'save.png' );
JToolBarHelper::help( 'cats/form',true );

?>
<form action="" method="post" name="adminForm" id="adminForm" class="adminForm" enctype="multipart/form-data">
    <table class="adminlist" cellpadding="1">
        <thead>
            <tr>
                <th colspan="5" align="center"><?php echo JText::_('COM_JTG_ADD_CAT'); ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td width="150px"><?php echo JText::_('COM_JTG_TITLE'); ?></td>
                <td><input type="text" name="title" value="" /></td>
            </tr>
            <tr>
                <td width="150px"><?php echo JText::_('COM_JTG_PARENT'); ?></td>
                <td><?php echo $this->lists['parent']; ?></td>
            </tr>
            <tr>
                <td width="150px"><?php echo JText::_('COM_JTG_PUBLISHED'); ?></td>
                <td><?php echo $this->lists['block']; ?></td>
            </tr>
            <tr>
                <td width="150px"><?php echo JText::_('COM_JTG_IMAGE'); ?></td>
				<td><input type="radio" name="catpic" value="" title="<?php echo JText::_('COM_JTG_NONE'); ?>" checked="checked"><?php echo JText::_('COM_JTG_NONE'); ?><br />
<?php
foreach ($this->images as $img) {
	$imageurl = JURI::root().'images/jtg/cats/';
	$pic = "";
	$pic .= "<input type=\"radio\" name=\"catpic\" value=\"" . $img . "\" title=\"" . $img . "\"";
	$pic .= ">".
	"<img src=\"".
	$imageurl.$img.
	"\" title=\" . $img.\" />".
	" \n";
	echo $pic;
}
?>
</td>
            </tr>
            <tr>
                <td><?php echo JText::_('COM_JTG_DESC'); ?></td>
                <td><?php echo $this->editor->display( 'desc', '' , '500', '200', '75', '20', false ) ; ?></td>
            </tr>
        </tbody>
    </table>
    <?php
    echo JHTML::_( 'form.token' ); ?>
    <input type="hidden" name="option" value="com_jtg" />
    <input type="hidden" name="controller" value="cats" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="MAX_FILES_SIZE" value="<?php echo $this->maxsize; ?>">
</form>
