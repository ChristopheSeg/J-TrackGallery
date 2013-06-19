<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: default.php,v 1.4 2011/04/21 21:37:43 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
 *
 */
defined('_JEXEC') or die('Restricted access');

// toolbar
JToolBarHelper::title(JText::_('COM_JTG_CONFIG'), 'generic.png');
JToolBarHelper::back($alt= 'COM_JTG_BACK', $href= 'javascript:history.back();');
JToolBarHelper::save('saveconfig',$alt= 'COM_JTG_SAVE', 'save.png' );
JToolBarHelper::preferences( 'com_jtg', 600, 800, $alt='COM_JTG_MENU_DEFAULT_SETTINGS');
JToolBarHelper::help( 'config/default',true );
jimport('joomla.html.pane');
JHTML::_('behavior.tooltip');
?>
<form action="" method="post" name="adminForm" id="adminForm" class="adminForm">
	<?php
	$tabs	=& JPane::getInstance('tabs');

// Hauptkonfiguration BEGIN
	echo $tabs->startPane('configuration');
	echo $tabs->startPanel(JText::_('COM_JTG_MAINCONF'), 'mainconfig');
	?>
	<table class="admintable">
	<tbody>
		<tr>
		<td><?php echo (JText::_( 'COM_JTG_MAPSERVICE' ));?>
		</td>
		<td>
<?php

if ((!$this->config->apikey) AND ($this->config->map == "google"))
{
	echo ("	<div style=\"color: #c00; background: #E6C0C0 url(templates/khepri/images/notice-alert.png) 4px top no-repeat; border-top: 3px solid #DE7A7B; border-bottom: 3px solid #DE7A7B; padding:10px; font-size:1.2em; font-weight:bold;\">\n");
	echo ("					<div style=\"padding-left:50px\">\n");
}
echo ("<select name=\"map\" size=\"1\">
							<option value=\"disabled\" ");
if($this->config->map == "disabled")
	echo ("selected=selected");
echo (">".JText::_('COM_JTG_NOSERVICE')."</option>
							<option value=\"google\" ");
if($this->config->map == "google")
	echo ("selected=selected");
echo (">".JText::_('COM_JTG_MAPGOOGLE')." (".JText::_('COM_JTG_NOTSUPPORTED').")</option>
							<option value=\"osm\" ");
if($this->config->map == "osm")
	echo ("selected=selected");
echo (">".JText::_('COM_JTG_MAPOSM')."</option>
					</select>\n");
if((!$this->config->apikey) AND ($this->config->map == "google")) {
	echo ("</div><br /><div align=\"center\">".JText::_('COM_JTG_NO_APIKEY')."</div>\n");

	echo ("</div>");}

?>
		</td>
			</tr>
			<tr>
				<td><span class="hasTip" title="<?php echo JText::_('COM_JTG_TT_TITLE'); ?> :: <?php echo JText::_('COM_JTG_TT_GID_DESC'); ?>"><?php echo JText::_('COM_JTG_USERS'); ?></span></td>
				<td><?php echo $this->lists['gid']; ?></td>
			</tr>
			<tr>
				<td><span class="hasTip" title="<?php echo JText::_('COM_JTG_TT_TITLE'); ?> :: <?php echo JText::_('COM_JTG_TT_ACCESS'); ?>"><?php echo JText::_('COM_JTG_ACCESS'); ?></span></td>
				<td><?php echo $this->lists['access']; ?></td>
			</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_ACTIVATE_COMMENTS'); ?></td>
			<td><?php echo $this->lists['comments']; ?></td>
		</tr>
			<tr>
				<td><span class="hasTip" title="<?php echo JText::_('COM_JTG_TT_TITLE'); ?> :: <?php echo JText::_('COM_JTG_TT_TERMS_USE'); ?>"><?php echo JText::_('COM_JTG_TERMS_USE'); ?></span></td>
				<td>
					<select name="terms" size="1">
							<option value="1" <?php if($this->config->terms == "1") echo "selected=selected"; ?> ><?php echo JText::_('JYES'); ?></option>
							<option value="0" <?php if($this->config->terms == "0") echo "selected=selected"; ?> ><?php echo JText::_('JNO'); ?></option>
					</select>
				</td>
			</tr>
<?php
if($this->config->terms == "1") {
?>
			<tr>
				<td><span class="hasTip" title="<?php echo JText::_('COM_JTG_TT_TITLE'); ?> :: <?php echo JText::_('COM_JTG_TT_TERMS'); ?>"><?php echo JText::_('COM_JTG_TERMS'); ?></span></td>
				<td><?php echo $this->lists['content']; ?></td>
			</tr>
<?php
}
?>
			<tr>
				<td><span class="hasTip" title="<?php echo JText::_('COM_JTG_TT_TITLE'); ?> :: <?php echo JText::_('COM_JTG_TT_PROFILEEXT'); ?>"><?php echo JText::_('COM_JTG_PROFILEEXT'); ?></span></td>
				<td>
					<select name="profile" size="1">
							<option value="0" <?php if($this->config->profile == "0") echo "selected=selected"; ?> ><?php echo JText::_('COM_JTG_NO_PROFILE'); ?></option>
							<option value="cb" <?php if($this->config->profile == "cb") echo "selected=selected"; ?> >Community Builder</option>
							<option value="js" <?php if($this->config->profile == "js") echo "selected=selected"; ?> >JomSocial</option>
							<option value="ku" <?php if($this->config->profile == "ku") echo "selected=selected"; ?> >Kunena</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><span class="hasTip" title="<?php echo JText::_('COM_JTG_TT_TITLE'); ?> :: <?php echo JText::_('COM_JTG_TT_VOTE'); ?>"><?php echo JText::_('COM_JTG_VOTE'); ?></span></td>
				<td><?php echo $this->lists['usevote']; ?></td>
			</tr>
			<tr>
				<td><span class="hasTip" title="<?php echo JText::_('COM_JTG_TT_TITLE'); ?> :: <?php echo JText::_('COM_JTG_TT_DOWNLOAD'); ?>"><?php echo JText::_('COM_JTG_DOWNLOAD'); ?></span></td>
				<td><?php echo $this->lists['download']; ?></td>
			</tr>
			<tr>
				<td><span class="hasTip" title="<?php echo JText::_('COM_JTG_TT_TITLE'); ?> :: <?php echo JText::_('COM_JTG_TT_APPROACH'); ?>"><?php echo JText::_('COM_JTG_APPROACH'); ?></span></td>
				<td><?php echo $this->lists['approach']; ?></td>
			</tr>
		</tbody>
	</table>
	<?php
	echo $tabs->endPanel();
// Hauptkonfiguration END

// Level BEGIN
echo $tabs->startPanel(JText::_('COM_JTG_LEVEL'), 'levelconfig');
?>
	<table class="admintable">
		<tbody>
			<tr>
				<td><span class="hasTip" title="<?php echo JText::_('COM_JTG_TT_TITLE'); ?> :: <?php echo JText::_('COM_JTG_TT_LEVELCONF'); ?>"><?php echo JText::_('COM_JTG_LEVEL'); ?></span></td>
				<td valign="top"><?php echo JText::_('COM_JTG_LEVELCONF_FIRST') . "<br />" . $this->lists['level']; ?></td>
				<td valign="top"><?php echo JText::_('COM_JTG_LEVEL_TRANSLATION') . ":<br />" . $this->lists['translevel']; ?></td>
			</tr>
		</tbody>
	</table>
<?php
echo $tabs->endPanel();
// Level END

// Googlekonfiguration BEGIN
	if($this->config->map == "google") {
		echo $tabs->startPanel(JText::_('COM_JTG_GOOGLECONF'), 'mainconfig');
	?>
	<table class="admintable">
		<tbody>
			<tr<?php
if(!$this->config->apikey)
	echo (" bgcolor=\"#FFD0D0\"");
?>>
				<td><?php echo JText::_( 'COM_JTG_GOOGLE_API_CODE' ); ?></td>
				<td><input type="text" name="apikey" value="<?php echo $this->config->apikey; ?>" size="60" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_JTG_MAP_TYPE'); ?></td>
				<td><select name="map_type">
						<option value=""><?php echo JText::_('COM_JTG_SELECT'); ?></option>
						<option value="0" <?php if($this->config->map_type == 0): echo "selected='selected'"; endif; ?> ><?php echo JText::_('COM_JTG_STANDARDMAP'); ?></option>
						<option value="1" <?php if($this->config->map_type == 1): echo "selected='selected'"; endif; ?> ><?php echo JText::_('COM_JTG_SATELITEMAP'); ?></option>
						<option value="2" <?php if($this->config->map_type == 2): echo "selected='selected'"; endif; ?> ><?php echo JText::_('COM_JTG_HYBRIDMAP'); ?></option>
						<option value="3" <?php if($this->config->map_type == 3): echo "selected='selected'"; endif; ?> ><?php echo JText::_('COM_JTG_PHYSICALMAP'); ?></option>
					</select></td>
			</tr>
		</tbody>
	</table>
    
	<?php

}
	echo $tabs->endPanel();
// Googlekonfiguration END

// Viewingoptions BEGIN
echo $tabs->startPanel(JText::_('COM_JTG_DISPLAY'), 'display');
?>
<table class="admintable">
	<tbody>
			<tr>
				<td><?php echo JText::_('COM_JTG_TEMPLATE'); ?></td>
				<td><?php echo $this->lists['tmpl']; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_JTG_GALLERY'); ?></td>
				<td><?php echo $this->lists['gallery']; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_( 'COM_JTG_IMAGETYPES' ); ?></td>
				<td><input type="text" name="type" value="<?php echo $this->config->type; ?>" size="30" /></td>
			</tr>
			<tr>
				<td><span class="hasTip" title="<?php echo JText::_('COM_JTG_TT_TITLE'); ?> :: <?php echo JText::_('COM_JTG_TT_SIZE_DESC'); ?>"><?php echo JText::_( 'COM_JTG_IMAGESIZE' ); ?></span></td>
				<td><input type="text" name="max_size" value="<?php echo $this->config->max_size; ?>" size="20" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_JTG_UNIT'); ?></td>
				<td><?php echo $this->lists['unit']; ?></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_JTG_SORT'); ?></td>
				<td><input type="text" name="sort" value="<?php echo $this->config->sort; ?>" size="20" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_JTG_MAPWIDTH'); ?></td>
				<td><input type="text" name="map_width" value="<?php echo $this->config->map_width; ?>" size="20" /> px</td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_JTG_MAPHEIGHT'); ?></td>
				<td><input type="text" name="map_height" value="<?php echo $this->config->map_height; ?>" size="20" /> px</td>
			</tr>
			<tr>
				<td colspan="2"><b><?php echo JText::_('COM_JTG_CHARTS_PROFILE'); ?></b></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_JTG_CHARTWIDTH'); ?></td>
				<td><input type="text" name="charts_width" value="<?php echo $this->config->charts_width; ?>" size="20" /> px</td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_JTG_CHARTHEIGTH'); ?></td>
				<td><input type="text" name="charts_height" value="<?php echo $this->config->charts_height; ?>" size="20" /> px</td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_JTG_CHARTLINEC'); ?></td>
				<td>#<input type="text" name="charts_linec" value="<?php echo $this->config->charts_linec; ?>" size="20" /></td>
			</tr>
			<tr>
				<td><?php echo JText::_('COM_JTG_CHARTBG'); ?></td>
				<td>#<input type="text" name="charts_bg" value="<?php echo $this->config->charts_bg; ?>" size="20" /></td>
			</tr>
		</tbody>
	</table>
	<?php
	echo $tabs->endPanel();
// Viewingoptions END

// Comments BEGIN
if ($this->config->comments != 0) {
	echo $tabs->startPanel(JText::_('COM_JTG_COMMENTS'), 'display');
	?>
	<table class="admintable">
		<tr>
			<td valign="top"><?php echo JText::_('COM_JTG_COMMENT_WHO'); ?></td>
			<td><?php echo $this->lists['who']; ?></td>
		</tr>
		<tr>
			<td valign="top"><span class="hasTip" title="<?php echo JText::_('COM_JTG_TT_TITLE'); ?> :: <?php echo JText::_('COM_JTG_TT_INFORM_AUTOR'); ?>"><?php echo JText::_('COM_JTG_INFORM_AUTOR'); ?></span></td>
			<td><?php echo $this->lists['inform']; ?></td>
		</tr>
		<tr>
			<td valign="top"><span class="hasTip" title="<?php echo JText::_('COM_JTG_TT_TITLE'); ?> :: <?php echo JText::_('COM_JTG_TT_CAPTCHA'); ?>"><?php echo JText::_('COM_JTG_CAPTCHA'); ?></span></td>
			<td><?php echo $this->lists['captcha']; ?><div><?php echo JText::_('COM_JTG_OST_CAPTCHA')." ".$this->captcha; ?></div></td>	 
		</tr>
		<tr>
			<td valign="top"><?php echo JText::_('COM_JTG_ORDERING'); ?></td>
			<td><?php echo $this->lists['order']; ?></td>
		</tr>
	</table>
	<?php
	echo $tabs->endPanel();
}
// Comments END

// Vote BEGIN
// Vote END

// Approach BEGIN
if ($this->config->approach != "no") {
//	die();
echo $tabs->startPanel(JText::_('COM_JTG_APPROACH'), 'display');
?>
	<table class="admintable">
<?php
if ($this->config->approach == "easy") {
?>
			<tr>
				<td><span class="hasTip" title="<?php echo JText::_('COM_JTG_TT_TITLE'); ?> :: <?php echo JText::_('COM_JTG_TT_SERVICE_PROVIDER'); ?>"><?php echo JText::_('COM_JTG_SERVICE_PROVIDER'); ?></span></td>
				<td><?php echo $this->lists['serviceprovider']; ?></td>
			</tr>
<?php
}
if ($this->config->approach == "ors") {
?>
			<tr>
				<td><span class="hasTip" title="<?php echo JText::_('COM_JTG_TT_TITLE'); ?> :: <?php echo JText::_('COM_JTG_TT_ICONSET'); ?>"><?php echo JText::_('COM_JTG_ICONSET'); ?></span></td>
				<td><?php echo $this->lists['routingiconset']; ?></td>
			</tr>
			<tr><td colspan="2">
<?php echo JText::_('COM_JTG_POWEREDBY').": <a href=\"http://openrouteservice.org\">openrouteservice.org</a> (<a href=\"http://wiki.openstreetmap.org/wiki/OpenRouteService\">".JText::_('COM_JTG_HELP')."</a>)\n";
?>
			</td></tr><?php
}
if ( ($this->config->approach == "cm") OR ($this->config->approach == "cmkey") ) {
?>
			<tr>
				<td><span class="hasTip" title="<?php echo JText::_('COM_JTG_TT_TITLE'); ?> :: <?php echo JText::_('COM_JTG_TT_ICONSET'); ?>"><?php echo JText::_('COM_JTG_ICONSET'); ?></span></td>
				<td><?php echo $this->lists['routingiconset']; ?></td>
			</tr>
			<tr><td colspan="2">
<?php echo JText::_('COM_JTG_POWEREDBY').": <a href=\"http://cloudmade.com/\">CloudMade</a> (<a href=\"http://wiki.openstreetmap.org/wiki/Cloudmade\">".JText::_('COM_JTG_HELP')."</a>)\n";
?>
			</td></tr><?php
}
?>
	</table>
	<?php
echo $tabs->endPanel();
}
// Approach END

	echo $tabs->endPane();
	echo JHTML::_( 'form.token' );
	?>
	<input type="hidden" name="option" value="com_jtg" />
	<input type="hidden" name="id" value="1" />
	<input type="hidden" name="task" value="" />
</form>
