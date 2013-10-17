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

defined('_JEXEC') or die('Restricted access');
$document =& JFactory::getDocument();
$user = JFactory::getUser();
$userid = (int)$user->id;
$tmpl = ($this->cfg->template = "") ? $this->cfg->template : 'default';
$iconpath = JURI::root() . "components/com_jtg/assets/template/" . $tmpl . "/images/";
if ( $userid ) {
	$document->addScriptDeclaration ("var alerttext = '".JText::_('COM_JTG_SET_HOMEPOSITION') . "';");
} else {
	$document->addScriptDeclaration ("var alerttext = '".JText::_('COM_JTG_HOMEPOSITION_GUESTS') . "';");
}
$document->addScript('media/system/js/mootools.js');
$document->addScriptDeclaration('var iconpath = \''.$iconpath.'\';');


$document->addScript('components/com_jtg/assets/js/homeposition.js');
//JHTML::_('behavior.tooltip'); // with this option IE8 doesn't work
$otherusers = 0;
$scriptbody = "";
$scriptheader = ("<script type=\"text/javascript\">
	var iconpath = '" . $iconpath . "';\n");
$params = $this->params;
				$defaultvars = (
"	var jtg_param_geo_lat = ".(float)$params->get('jtg_param_geo_lat') . ";
	var jtg_param_geo_lon = ".(float)$params->get('jtg_param_geo_lon') . ";
	var jtg_param_geo_zoom = ".(int)$params->get('jtg_param_geo_zoom') . ";
	var jtg_param_geo_zoom_loggedin = ".(int)$params->get('jtg_param_geo_zoom_loggedin') . ";\n");

if ( $userid )
{
	$latlon = array_merge(
	JtgHelper::getLatLon($userid),
	JtgHelper::getLatLon(false,$userid)
	);	// sort logged in user to 1st place in array to give the option
	// to calculate the distances
	$homepos = JText::_('COM_JTG_MY_HOMEPOSITION');
} else {
	$latlon = JtgHelper::getLatLon();
	$homepos = "false";
}
$gps = new gpsClass();
for($x=0;$x<=count($latlon);$x++){
	if ((isset($latlon[$x])) AND
	( (float)$latlon[$x]->jtglat != 0) AND
	( (float)$latlon[$x]->jtglon != 0) AND
	($latlon[$x]->id == $userid)) {
		$userlat = $latlon[$x]->jtglat;
		$userlon = $latlon[$x]->jtglon;
		$uservis = $latlon[$x]->jtgvisible;
	} elseif ((isset($latlon[$x])) AND
	( (float)$latlon[$x]->jtglat != 0) AND
	( (float)$latlon[$x]->jtglon != 0) AND ($latlon[$x]->jtglat) && ($latlon[$x]->jtglon) && ($latlon[$x]->jtgvisible)) {
		if ( ( ( $userid ) && ( $latlon[$x]->jtgvisible != "non" ) ) || ( ( !$userid ) && ( $latlon[$x]->jtgvisible == "all" ) ) ) {
			if (isset($userlon))
			{
				$distance = $gps->getDistance(array(
				array($userlon,$userlat),
				array($latlon[$x]->jtglon,$latlon[$x]->jtglat)));
				$distance = JtgHelper::getLocatedFloat($distance,0,$this->cfg->unit);

				$distancetext = "<br />".JText::_('COM_JTG_DISTANCE') . ": ";
			}
			else
			{
				$distance = "";
				$distancetext = "<br />".JText::_('COM_JTG_NO_DISTANCE_GUEST');
			}
			if (empty($vars)) {
				$vars = (
"	var SizeIconOtherUser = new OpenLayers.Size(22,22);
	var OffsetIconOtherUser = new OpenLayers.Pixel(-11,-14);
	var IconOtherUser = '" . $iconpath . "user.png';
	var MarkerHomePosition = '" . $homepos . "';
	var inittext = '".JText::_('COM_JTG_HERE_LIVE') . ": ';
	var distancetext = '" . $distancetext . "';
	var distance=Array();
	var username=Array();
	var name=Array();
	var lat=Array();
	var lon=Array();
	var link=Array();
	var id=Array();\n");
			}
			$scriptbody .=
"	lat[" . $otherusers . "] = '" . $latlon[$x]->jtglat . "';
	lon[" . $otherusers . "] = '" . $latlon[$x]->jtglon . "';
	username[" . $otherusers . "] = '" . $latlon[$x]->username . "';
	distance[" . $otherusers . "] = '" . $distance . "';
	name[" . $otherusers . "] = '" . $latlon[$x]->name . "';
	id[" . $otherusers . "] = '" . $latlon[$x]->id . "';
	link[" . $otherusers . "] = '".JtgHelper::getProfileLink($latlon[$x]->id,$latlon[$x]->username) . "';\n";
			$otherusers++;
		}
	} elseif (empty($vars)) {
		$scriptbody = "	var MarkerHomePosition = '" . $homepos . "';\n";
	}
}
if (empty($vars)) $vars=""; // if no other person saved
$scriptfooter = ("</script>\n");
$scriptbody = "	var otherusers = '" . $otherusers . "';\n" . $scriptbody;
$script = $scriptheader.$defaultvars.$vars.$scriptbody.$scriptfooter;

echo $script;
echo ("<div id=\"map\" style=\"width: " . $this->cfg->map_width . "px; height: " . $this->cfg->map_height . ";\" ></div>
<div id=\"otheruser\" style=\"width: " . $this->cfg->map_width . ";\" >".JText::_('COM_JTG_HERE_LIVE_DESC') . "</div>\n");
if ( $userid ) {
	?>
<form action="<?php echo ($this->geo); ?>" method="post"
	name="adminForm" id="adminForm">
<table>
	<tr>
		<td><?php
		echo JText::_('COM_JTG_LAT');
		if (isset($userlat)) $lat = round($userlat,15); else $lat = "";
		if (isset($userlon)) $lon = round($userlon,15); else $lon = "";
		?></td>
		<td><input type="input" size="15" class="output" name="lat" id="lat"
			value="<?php
			echo $lat; ?>" onchange="handleFillLL();mapcenter();"></input><?php
			echo JText::_('COM_JTG_LAT_U');
			echo "</td>\n			<td>";
			echo JHTML::tooltip(JText::_('COM_JTG_TT_LAT'));?></td>
	</tr>
	<tr>
		<td><?php
		echo JText::_('COM_JTG_LON');
		?></td>
		<td><input type="input" size="15" class="output" name="lon" id="lon"
			value="<?php
			echo $lon; ?>" onchange="handleFillLL();mapcenter();"></input><?php
			echo JText::_('COM_JTG_LON_U');
			echo "</td>\n			<td>";
			echo JHTML::tooltip(JText::_('COM_JTG_TT_LON'));?></td>
	</tr>
	<tr>
		<td><?php echo JText::_('COM_JTG_VISIBLE'); ?></td>
		<td><select name="visible" id="visible" size="3">
		<?php
		// selected="selected"
		$snon = "";
		$sreg = "";
		$sall = "";

		if ( $uservis == "non" )
		$snon = " selected=\"selected\"";
		elseif ( $uservis == "reg" )
		$sreg = " selected=\"selected\"";
		else
		$sall = " selected=\"selected\"";
		echo "					<option value=\"all\"" . $sall . ">".JText::_('COM_JTG_VISIBLE_ALL') . "</option>
					<option value=\"reg\"" . $sreg . ">".JText::_('COM_JTG_VISIBLE_REG') . "</option>
					<option value=\"non\"" . $snon . ">".JText::_('COM_JTG_VISIBLE_NONE') . "</option>
				</select>
";
		?></td>
	</tr>
</table>
		<?php
		echo JHTML::_( 'form.token' ) . "\n"; ?> <input type="hidden"
	name="option" value="com_jtg" /> <input type="hidden"
	name="controller" value="geo" /> <input type="hidden" name="task"
	value="" /> <?php
	if(isset($this->id)) echo '	<input type="hidden" name="id" value="'.$this->id.'" />';
	?> <input type="submit" name="Submit" class="button"
	value="<?php echo JText::_('COM_JTG_SAVE') ?>" onclick="submitbutton('save')" />
</form>
	<?php } else { ?>
<input type="hidden"
	name="lat" id="lat" value=""></input>
<input type="hidden"
	name="lon" id="lon" value=""></input>
	<?php } ?>
<script type="text/javascript">init();</script>
<div class="no-float"><?php
//echo $this->disclaimericons;
echo $this->footer;
?></div>
