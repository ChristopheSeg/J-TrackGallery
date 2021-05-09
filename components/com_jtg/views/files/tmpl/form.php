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

//
// This form has three states or modes:
//   1) Update mode: update existing record
//   2a) New mode (no id set)
//   2b) New mode, but a file has been uploaded, also $id is set
// The disctinction between 2b and 1 is now made based on whether 
// the title is already set; this logic can be improved.
//
$tracktitle='';
if (isset($this->id) && !empty($this->track->title))
{
	$description = $this->track->description;
	$buttonaction = "submitbutton('update')";
	$buttontext = JText::_('COM_JTG_SAVE_TO_FILEVIEW');
	$title = JText::_('COM_JTG_UPDATE_GPS_FILE');
	$tracktitle = $this->track->title;
}
else
{
	$description = '';
	if (isset($this->track->description)) $description = $this->track->description;
	$buttontext = JText::_('COM_JTG_SAVE');
	$title = JText::_('COM_JTG_NEW_TRACK');
	if (isset($this->id)) {
		$buttonaction = "submitbutton('update')";
		$tracktitle = $this->track->file;
	}
	else {
		// TODO: This should normally not happen, since the file needs to be uploaded first?
		$buttonaction = "submitbutton('save')";
	}
}

$cfg = JtgHelper::getConfig();
$user = JFactory::getUser();
$juser = new JUser($user->id);
$k = 0;

echo $this->map;
?>
<script type="text/javascript">

Joomla.submitbutton = function(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel')
		{
		submitform( pressbutton );
		return;
	}
	if (pressbutton == 'reset') {
		submitform( pressbutton );
		return;
	}
	// Do field validation
	if (document.getElementById('title').value == ""){
		alert( "<?php echo JText::_('COM_JTG_NEED_TITLE', true); ?>");
	}
	if (document.getElementById('catid').value == "") {
		alert( "<?php echo JText::_('COM_JTG_NEED_CATEGORY', true); ?>");
   }
<?php
if ($this->cfg->terms == 1)
{
?>
		else if (document.getElementById('terms').checked == false) {
			alert( "<?php echo JText::_('COM_JTG_NEED_TERMS', true); ?>");
		}
		else
		{
			submitform( pressbutton );
		}
<?php
}
else
{
?>
		 else {
			submitform( pressbutton);
		}
<?php
}
?>

}
</script>

<?php
if (isset($this->map)) 
{
?>
<style type="text/css">
#jtg_map.olMap {
   height: <?php echo $this->cfg->map_height; ?>;
   width: <?php echo $this->cfg->map_width; ?>;
   z-index: 0;
}
.olButton::before {
   display: none;
}

#jtg_map.fullscreen {
   height: 800px;
   width: 100%;
   z-index: 10000;
}

/* Fix Bootstrap-Openlayers issue */
img.olTileImage {
   max-width: none !important;
}
.olPopup img { max-width: none !important;
}

</style>
<?php
}
echo $this->lh;

//  if ( ($user->get('id')) AND ($juser->get('gid') >= $this->cfg->gid ) OR (isset($this->id)) ){
if ( (JtgHelper::userHasFrontendRights() ) OR (isset($this->id)) )
{
?>
<div class="componentheading">
	<h1><?php echo $title; ?></h1>
</div>
<div>
   <center><div id="jtg_map" class="olMap"></div><br /></center>
        <div id="popup" class="ol-popup">
          <a href="#" id="popup-closer" class="ol-popup-closer"></a>
          <div id="popup-content"></div>
</div>
<div>
	<form name="adminForm" id="adminForm" method="post"
		enctype="multipart/form-data" action="<?php echo $this->action; ?>">
		<table style="width:100%;">
			<tbody>
<?php
if (!isset($this->id))
{
?>
				<tr class="sectiontableentry<?php
					echo $k;
					$k = 1 - $k;
					?>">
					<td><?php echo JText::_('COM_JTG_GPS_FILE'); ?>*
					<?php echo JHtml::tooltip(JText::_('COM_JTG_TT_FILES'), JText::_('COM_JTG_TT_HEADER'), 'tooltip.png');
					?>
					</td>
					<td><input type="file" name="file" value="" size="30" onchange="submitform('uploadGPX')"></td>
				</tr>
<?php
}
else
{
?>
				<tr class="sectiontableentry<?php
					echo $k;
					$k = 1 - $k;
					?>">
					<td><?php echo JText::_('COM_JTG_ID'); ?>:</td>
					<td><font color="grey"><?php echo $this->id; ?> </font></td>
				</tr>
				<tr class="sectiontableentry<?php
					echo $k;
					$k = 1 - $k;
					?>">
					<td><?php echo JText::_('COM_JTG_FILE'); ?>:</td>
					<td><font color="grey"><?php echo $this->track->file; ?> </font></td>
				</tr>
<?php
}
?>
				<tr class="sectiontableentry<?php
					echo $k;
					$k = 1 - $k;
					?>">
					<td><?php echo JText::_('COM_JTG_HIDDEN'); ?>*</td>
					<td><?php echo $this->lists['hidden']; ?></td>
				</tr>
				<tr class="sectiontableentry<?php
					echo $k;
					$k = 1 - $k
					?>">
					<td><?php echo JText::_('COM_JTG_PUBLISHED'); ?>*</td>
					<td><?php echo $this->lists['published']; ?></td>
				</tr>
				<tr class="sectiontableentry<?php
				echo $k;
				$k = 1 - $k;
				?>">
					<td><?php echo JText::_('COM_JTG_TITLE'); ?>*</td>
					<td><input id="title" type="text" name="title"
						value="<?php echo $tracktitle; ?>"
						size="30" /></td>
				</tr>
				<tr class="sectiontableentry<?php
				echo $k;
				$k = 1 - $k;
				?>">
					<td><?php echo JText::_('COM_JTG_LEVEL'); ?>*
					<?php echo JHtml::tooltip(JText::_('COM_JTG_TT_LEVEL'), JText::_('COM_JTG_TT_HEADER'), 'tooltip.png'); ?>
					</td>
					<td><?php echo $this->level; ?>
					</td>
				</tr>
				<tr class="sectiontableentry<?php
					echo $k;
					$k = 1 - $k;
					?>">
					<td><?php echo JText::_('COM_JTG_CAT'); ?></td>
					<td><?php echo $this->lists['content']; ?></td>
				</tr>
<?php
if ($this->cfg->access == 1)
{
?>
				<tr class="sectiontableentry<?php
					echo $k;
					$k = 1 - $k;
					?>">
					<td><?php echo JText::_('COM_JTG_ACCESS_LEVEL'); ?>&nbsp;
					<?php echo JHtml::tooltip(JText::_('COM_JTG_TT_ACCESS'), JText::_('COM_JTG_TT_HEADER'), 'tooltip.png');?>
					</td>
					<td><?php echo $this->lists['access']; ?></td>
				</tr>
<?php
}
?>
				<tr class="sectiontableentry<?php
					echo $k;
					$k = 1 - $k;
					?>">
					<td><?php echo JText::_('COM_JTG_FILE_DEFAULT_MAP'); ?></td>
					<td><?php echo $this->lists['default_map']; ?></td>
				</tr>
				<tr class="sectiontableentry<?php
					echo $k;
					$k = 1 - $k;
					?>">
					<td><?php echo JText::_('COM_JTG_FILE_DEFAULT_OVERLAYS'); ?></td>
					<td><?php echo $this->lists['default_overlays']; ?></td>
				</tr>
				<tr class="sectiontableentry<?php
					echo $k;
					$k = 1 - $k;
					?>">
					<td><?php echo JText::_('COM_JTG_TERRAIN'); ?>
					<?php echo JHtml::tooltip(JText::_('COM_JTG_TT_TERRAIN'), JText::_('COM_JTG_TT_HEADER'), 'tooltip.png'); ?>
					</td>
					<td><?php echo $this->lists['terrain']; ?></td>
				</tr>
				<tr class="sectiontableentry<?php
					echo $k;
					$k = 1 - $k;
					?>">
					<td colspan="2"><p><?php echo JText::_('COM_JTG_DESCRIPTION'); ?>*:
						<?php echo JHtml::tooltip(JText::_('COM_JTG_TT_DESC'), JText::_('COM_JTG_TT_HEADER'), 'tooltip.png'); ?>
					</p>
					<?php echo $this->editor->display('description', $description, '100%', '200', '15', '25', false, null); ?>
					</td>
				</tr>
				<input id="mappreview" type="hidden" name="mappreview">
				<tr class="sectiontableentry<?php
					echo $k;
					$k = 1 - $k;
					?>">
					<?php
					$max_images = $cfg->max_images;

					if (isset($this->id))
					{
						$max_images = ( $max_images - $this->imgcount );

						if ($max_images <= 0)
						{
							$max_images = 0;
						}
					}

					// Accept  jpg,png,gif
					$accept = $this->cfg->type;
					$accept = explode(",", $accept);
					$tt = JText::sprintf('COM_JTG_ALLOWED_FILETYPES', implode(", ", $accept)) . '  ' . JText::_('COM_JTG_MAXIMAL') . ' ' . $max_images;
					?>
					<td colspan="2"><?php echo JText::_('COM_JTG_IMAGES'); ?> :
					<?php
					echo JHtml::tooltip($tt, JText::_('COM_JTG_TT_HEADER'), 'tooltip.png');
					?>
					<input
					<?php
					echo $max_images <= 0 ? 'disabled="disabled" ': ''; ?>
						type="file" name="images[]" class="multi"
						maxlength="<?php echo $max_images; ?>"
						accept="<?php echo implode("|", $accept) ?>"><br clear="all" /> <?php echo $this->images; ?>

				</tr>
				<?php
				if ($this->cfg->terms == 1)
				{
				?>
				<tr class="sectiontableentry<?php
					echo $k;
					$k = 1 - $k;
					?>">
					<td><?php echo JText::_('COM_JTG_TERMS'); ?></td>
					<td><input id="terms" type="checkbox" name="terms" value="" /> <?php echo JText::_('COM_JTG_AGREE'); ?>
						<a class="modal" href="<?php echo $this->terms; ?>"
						target="_blank"><?php echo JText::_('COM_JTG_TERMS'); ?> </a></td>
				</tr>
				<?php
				}
				?>
			</tbody>
		</table>
		<?php
		echo JHtml::_('form.token') . "\n"; ?>
		<input type="hidden" name="option" value="com_jtg" /> <input
			type="hidden" name="controller" value="files" />
		<?php
		echo isset($this->id)? '<input type="hidden" name="id" value=" ' . $this->id . '" />': '';
		?>
		<input type="hidden" name="task" value="" />
		<div>
			<br />
			<button class="button" type="button" onclick="<?php echo $buttonaction; ?>">
				<?php echo $buttontext; ?>
			</button>
			<button class="button" type="button" onclick="submitbutton('reset')">
				<?php echo JText::_('COM_JTG_RESET') ?>
			</button>
			<?php
			if (isset($this->id) && !empty($this->track->title))
			{
				//$reject = "index.php?option=com_jtg&amp;view=files&amp;layout=file&amp;id=" . $this->id;
				$canceltext = JText::_('COM_JTG_CANCEL_TO_FILEVIEW');
				$cancelaction = "submitbutton('cancel')";
			}
			else
			{
				//$reject = "index.php?option=com_jtg";
				$canceltext = JText::_('JCANCEL');
				if (isset($this->id)) {
				    $cancelaction = "submitform('deletenew')";
				}
				else $cancelaction = "submitform('cancel')";	
			}
			?>
			<button class="button" type="button"
				onclick="<?php echo $cancelaction;?>">
				<?php echo $canceltext; ?>
			</button>
		</div>
	</form>
</div>
<?php
}
else
{
	echo JText::_('COM_JTG_NOT_AUTH');
}

echo $this->footer;

if (isset($this->map)) {
	echo "\n<script type=\"text/javascript\">\n
   var olmap={ title: 'com_jtg_map_object' } \n
   slippymap_init();</script>\n";
}
