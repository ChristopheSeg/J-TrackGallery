<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooosm and joomGPStracks teams
 * @package    com_jtg
 * @subpackage frontend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

if(isset($this->id))
{
	$description = $this->track->description;
	$button = "submitbutton('update')";
	$buttontext = JText::_('COM_JTG_SAVE_TO_FILEVIEW');
	$title = JText::_('COM_JTG_UPDATE');
} else {
	$description = "";
	$button = "submitbutton('save')";
	$buttontext = JText::_('COM_JTG_SAVE');
	$title = JText::_('COM_JTG_NEW_TRACK');
}

// $cfg = JtgHelper::getConfig();
$user =& JFactory::getUser();
$juser = new JUser($user->id);

?>
<script language="javascript" type="text/javascript">

Joomla.submitbutton = function(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	if (pressbutton == 'reset') {
		submitform( pressbutton );
		return;
	}
    // do field validation
	if (document.getElementById('title').value == ""){
		alert( "<?php echo JText::_( 'COM_JTG_NEED_TITLE', true ); ?>" );
    }  
    <?php if($this->cfg->terms == 1)  { ?>
        else if (document.getElementById('terms').checked == false) {
            alert( "<?php echo JText::_( 'COM_JTG_NEED_TERMS', true ); ?>" );
        } else {
		submitform( pressbutton );
        }
        <?php } else { ?>
         else {
            submitform( pressbutton);
        }
        <?php } ?>

}
</script>
        <?php echo $this->lh;
	// TODO IMPORTANT!  ACL in Joomla 2.5 has been revisited. Check next if()
//        if( ($user->get('id')) AND ($juser->get('gid') >= $this->cfg->gid ) OR (isset($this->id)) ){
        if( (JtgHelper :: userHasFrontendRights() ) OR (isset($this->id)) ){
        	?>
<div class="componentheading"><?php echo $title; ?></div>
<div>
<form name="adminForm" id="adminForm" method="post"
	enctype="multipart/form-data" action="<?php echo $this->action; ?>">
<table width="100%">
	<tbody>
		<?php
		if(!isset($this->id)) {
			?>
		<tr>
			<td><?php echo JText::_('COM_JTG_FILE'); ?>* <?php echo JHTML::tooltip(JText::_('COM_JTG_TT_FILES'), JText::_('COM_JTG_TT_HEADER'),'tooltip.png'); ?></td>
			<td><input type="file" name="file" value="" size="30" /></td>
		</tr>
		<?php }
		else
		{
			?>
		<tr>
			<td><?php echo JText::_('COM_JTG_ID'); ?>:</td>
			<td><font color="grey"><?php echo $this->id; ?></font></td>
		</tr>
		<?php
		} ?>
		<tr>
			<td><?php echo JText::_('COM_JTG_HIDDEN'); ?>*</td>
			<td><?php echo $this->lists['hidden']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_PUBLISHED'); ?>*</td>
			<td><?php echo $this->lists['published']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_TITLE'); ?>*</td>
			<td><input id="title" type="text" name="title"
				value="<?php if(isset($this->id)) echo $this->track->title; ?>"
				size="30" /></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_LEVEL'); ?>* <?php echo JHTML::tooltip(JText::_('COM_JTG_TT_LEVEL'), JText::_('COM_JTG_TT_HEADER'),'tooltip.png'); ?></td>
			<td>
				<?php echo $this->level; ?>
			</td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_CAT'); ?></td>
			<td><?php echo $this->lists['content']; ?></td>
		</tr>
		<?php
		if($this->cfg->access == 1) {
			?>
		<tr>
			<td><?php echo JText::_('COM_JTG_ACCESS_LEVEL'); ?>&nbsp;<?php echo JHTML::tooltip(JText::_('COM_JTG_TT_ACCESS'), JText::_('COM_JTG_TT_HEADER'),'tooltip.png'); ?></td>
			<td><?php echo $this->lists['access']; ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td><?php echo JText::_('COM_JTG_TERRAIN'); ?> <?php echo JHTML::tooltip(JText::_('COM_JTG_TT_TERRAIN'), JText::_('COM_JTG_TT_HEADER'),'tooltip.png'); ?></td>
			<td><?php echo $this->lists['terrain']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_DESCRIPTION'); ?>* <?php echo JHTML::tooltip(JText::_('COM_JTG_TT_DESC'), JText::_('COM_JTG_TT_HEADER'),'tooltip.png'); ?></td>
			<td><?php echo $this->editor->display( 'description', $description , '500', '200', '15', '25', false ) ; ?></td>
		</tr>
		<tr>
			<td valign="top"><?php
// @ToDo define max in BE
$max = 10;
if(isset($this->id))
{
	$max = ( $max - $this->imgcount );
	if ( $max <= 0 ) $max = 0;
}
$accept = $this->cfg->type; // e.g. jpg,png,gif
$accept = explode(",",$accept);
?><?php echo JText::_('COM_JTG_IMAGES'). " (".JText::sprintf('COM_JTG_ALLOWED_FILETYPES', implode(", ",$accept)); ?><br /><?php echo JText::_('COM_JTG_MAXIMAL') . " " . $max; ?>)</td>
			<td><input <?php if ( $max <= 0 ) echo "disabled=\"disabled\" ";?>type="file" name="images[]" class="multi" maxlength="<?php echo $max; ?>" accept="<?php echo implode("|",$accept) ?>"><br clear="all" />
				<?php echo $this->images; ?></td>
		</tr>
		<?php
		if($this->cfg->terms == 1):
		?>
		<tr>
			<td><?php echo JText::_('COM_JTG_TERMS'); ?></td>
			<td><input id="terms" type="checkbox" name="terms" value="" /><?php echo JText::_('COM_JTG_AGREE'); ?>
			<a class="modal" href="<?php echo $this->terms; ?>" target="_blank"><?php echo JText::_('COM_JTG_TERMS'); ?></a></td>
		</tr>
		<?php
		endif;
		?>
	</tbody>
</table>
		<?php
		echo JHTML::_( 'form.token' ) . "\n"; ?> <input type="hidden"
	name="option" value="com_jtg" /> <input type="hidden"
	name="controller" value="files" /> <?php
	if(isset($this->id)) echo '<input type="hidden" name="id" value="'.$this->id.'" />';
	?> <input type="hidden" name="task" value="" />
<div>
<button class="button" type="button" onclick="<?php echo $button; ?>"><?php echo $buttontext; ?>
</button>
<button class="button" type="button" onclick="submitbutton('reset')"><?php echo JText::_('COM_JTG_RESET') ?>
</button>
<!--<?php
/*	if(isset($this->id))
		$reject = "index.php?option=com_jtg&amp;view=files&amp;layout=file&amp;id=" . $this->id;
	else
		$reject = "index.php?option=com_jtg";
*/	?>
	<button class="button" type="button" onclick="window.location.replace('<?php echo $reject; ?>')">
		<?php echo JText::_('COM_JTG_CANCEL_TO_FILEVIEW') ?>
	</button>-->
<button class="button" type="button" onclick="history.back();"><?php echo JText::_('Cancel') ?>
</button>
</div>
</form>
</div>
		<?php
        } else echo JText::_('COM_JTG_NOT_AUTH');
        echo $this->footer;
