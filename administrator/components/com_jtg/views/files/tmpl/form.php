<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 * 
 * @package    Comjtg
 * @author     Christophe Seguinot <christophe@jtrackgallery.net>
 * @copyright  2013 J!Track Gallery, InJooosm and joomGPStracks teams
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

defined('_JEXEC') or die('Restricted access');

// toolbar
if($this->id < 1)
$title = JText::_('COM_JTG_ADD_FILE');
else
$title = JText::_('COM_JTG_EDIT_FILE');
JToolBarHelper::title($title, 'categories.png');
JToolBarHelper::back();
JToolBarHelper::spacer();
if($this->id < 1):
JToolBarHelper::save('savefile',$alt= 'COM_JTG_SAVE', 'save.png' );
else:
JToolBarHelper::save('updatefile', $alt= 'COM_JTG_SAVE');
JToolBarHelper::custom('updateGeneratedValues', 'apply', 'apply', 'COM_JTG_REFRESH_DATAS', false );
endif;
JToolBarHelper::help( 'files/form',true );
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::base().'components/com_jtg/template.css');
// add jtg_map stylesheet
$tmpl = ($cfg->template = "") ? $cfg->template : 'default';
$document->addStyleSheet(JUri::root().'components/com_jtg/assets/template/'.$tmpl.'/jtg_map_style.css');
$map = "";
if($this->id >= 1) 
{
	//	edit file
	$cache = JFactory::getCache('com_jtg');
	$cfg = JtgHelper::getConfig();
	$params = JComponentHelper::getParams( 'com_jtg' );
	$model = $this->getModel();
	$track = $cache->get(array($model, 'getFile'), array($this->id));
	$document = JFactory::getDocument();
	require_once(".." . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_jtg" . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR . "gpsClass.php");
	$document->addScript('http://www.openlayers.org/api/OpenLayers.js');
	$document->addScript('../components' . DIRECTORY_SEPARATOR . 'com_jtg' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'js' . DIRECTORY_SEPARATOR . 'fullscreen.js');
	$document->addScript('http://www.openstreetmap.org/openlayers/OpenStreetMap.js');
	$document->addScript("../components/com_jtg/assets/js/jtg.js");
	$file = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'uploaded_tracks' . DIRECTORY_SEPARATOR . $this->track->file;
	$gpsData = new gpsDataClass($cfg->unit);
	$gpsData = $cache->get(array ( $gpsData, 'loadFileAndData' ), array ($file, $track->file ), $cfg->unit);
	if ($gpsData->displayErrors())
	{
	    	$map = ""; 
	}
	else
	{
		$map = $cache->get(array ( $gpsData, 'writeTrackOL' ), array ( $track, $params ));
		$map .= ("\n<div id=\"jtg_map\"  align=\"center\" class=\"olMap\" ");
		$map .= ("style=\"width: 400px; height: 500px; background-color:#EEE; vertical-align:middle;\" >");
		$map .= ("\n<script>slippymap_init();</script>");
		$map .= ("\n</div>");	    
	}

}

?>
<form action="" method="post" name="adminForm" id="adminForm"
	class="adminForm" enctype="multipart/form-data">
<table class="adminlist" cellpadding="1">
	<thead>
		<tr>
			<th colspan="3" align="center"><?php echo $title; ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo JText::_('COM_JTG_GPS_FILE') . ":"; if($this->id < 1) echo "*";?></td>
			<td><?php if($this->id < 1) { ?><input type="file" name="file"
				value="" size="30" /><?php } else echo $this->track->file; ?></td>
			<td rowspan="12" width="1" valign="top"><?php echo $map; ?></td>
		</tr>
		<?php if($this->id >= 1) { ?>
		<tr>
			<td>Id:</td>
			<td><?php echo $this->id; ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td><?php echo JText::_('COM_JTG_PUBLISHED'); ?>:*</td>
			<td><?php echo $this->lists['published']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_TITLE'); ?>:*</td>
			<td><input id="title" type="text" name="title"
				value="<?php if( isset($this->id) AND ($this->id != 0) ) echo $this->track->title; ?>"
				size="30" /></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_DATE'); ?>:*</td>
			<td><input id="date" type="text" name="date"
				value="<?php if( isset($this->id) AND ($this->id != 0) ) echo $this->track->date; ?>"
				size="10" /></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_INFO_AUTHOR'); ?>:*<br />
			</td>
			<td><?php echo $this->lists['uid']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_LEVEL'); ?>* <?php echo JHtml::tooltip(JText::_('COM_JTG_TT_LEVEL')); ?>:
			<td><?php echo $this->level; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_CAT'); ?>:</td>
			<td><?php echo $this->lists['cats']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_ACCESS_LEVEL'); ?>:</td>
			<td><?php echo $this->lists['access']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_HIDDEN'); ?>:</td>
			<td><?php echo $this->lists['hidden']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_TERRAIN'); ?>:</td>
			<td><?php echo $this->lists['terrain']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_CALCULATED_VALUES'); ?>:</td>
			<td><?php echo $this->lists['values']; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_DESCRIPTION'); ?>:*</td>
			<td colspan="2"><?php
			if( isset($this->track->description) )
			$trackdescription = $this->track->description;
			else
			$trackdescription = null;
			echo $this->editor->display( 'description', $trackdescription, '100%', '200', '15', '25', false ) ;
			?></td>
		</tr>
		<?php
		/*
		 echo ("	<tr>
		 <td>".JText::_('COM_JTG_WPS') . ":</td>
		 <td colspan=\"2\"></td>
		 </tr>
		 <tr>
		 <td>".JText::_('COM_JTG_TRACKS') . ":</td>
		 <td colspan=\"2\"></td>
		 </tr>
		 ");
		 */
		?>
		<tr>
			<td valign="top"><?php echo JText::_('COM_JTG_IMAGES'); ?> (max. 10):</td>
			<td colspan="2"><input type="file" name="images[]" class="multi"
				maxlength="10"><br clear="all" />
			<br />
			<?php if( isset($this->images) )
			echo $this->images; ?></td>
		</tr>
		<?php
		if($cfg->terms == 1):
		 ?>
		 <tr>
		 <td><?php echo JText::_('COM_JTG_TERMS'); ?></td>
		 <td><input id="terms" type="checkbox" name="terms" value="" /><?php echo JText::_('COM_JTG_AGREE'); ?> <a class="modal" href="<?php echo JUri::base() . "../?option=com_content&view=article&id=" . $cfg->terms_id; ?>" target="_blank"><?php echo JText::_('COM_JTG_TERMS'); ?></a></td>
		 </tr>
		 <?php
		 endif;
		 ?>
	</tbody>
</table>
<?php echo JHtml::_( 'form.token' ); ?>
<input type="hidden" name="option" value="com_jtg" />
<input type="hidden" name="controller" value="files" />
<input type="hidden" name="task" value="" />
<?php
if ($this->id) {
	echo ("<input type=\"hidden\" name=\"id\" value=\"" . $this->id . "\" />");
	echo ("<input type=\"hidden\" name=\"file\" value=\"" . $this->track->file . "\" />");
}
?>
</form>
