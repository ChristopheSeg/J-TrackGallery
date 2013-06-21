<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooOSM and joomGPStracks teams
 * @package    com_jtg
 * @subpackage frontend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

defined('_JEXEC') or die('Restricted access');
echo $this->lh;
// Karten-Auswahl BEGIN
//if($this->map == "disabled") {
//	echo JText::_('COM_JTG_NOSERVICE');
//}
if($this->map == "osm") {
	echo $this->gps->writeOSMMap($this->where,$this->tracks,$this->params);
	?>
<center>
<div id="map" style="width: <?php echo $this->cfg->map_width; ?>px; height: <?php echo $this->cfg->map_height; ?>px;" ><script
	type="text/javascript">slippymap_init("map");</script></div>
</center>
	<?php
}
if($this->map == "google") {
	echo $this->gps->writeGoogleMap();
	?>
<script
	src="http://www.google.com/uds/solutions/localsearch/gmlocalsearch.js"
	type="text/javascript"></script>

<style type="text/css">
@import url("http://www.google.com/uds/css/gsearch.css");

@import
	url("http://www.google.com/uds/solutions/localsearch/gmlocalsearch.css")
	;
</style>
<center>
<div id="map" style="width: <?php echo $this->cfg->map_width; ?>px; height: <?php echo $this->cfg->map_height; ?>px;" ></div>
</center>
	<?php
}
// Karten-Auswahl END
if ($this->newest != 0) {
	?>

<div class="<?php echo $this->toptracks; ?>">
<div class="<?php echo $this->toptracks; ?>_title"><span
	class="headline"> <a href="#TT_newest" name="TT_newest"><?php
	echo JText::_('COM_JTG_NEWEST');
	?></a> </span>
<ul class="title">
	<li>
	<div class="list-left"><b><?php echo JText::_('COM_JTG_TITLE'); ?></b></div>
	<div class="list-right"><b><?php echo JText::_('COM_JTG_CAT'); ?></b></div>
	<div class="no-float"></div>
	</li>
</ul>
</div>
<div class="<?php echo $this->toptracks; ?>_entry">
<ul class="entry">
<?php
if ( count ($this->newest) == 0 ) echo JText::_('COM_JTG_NOENTRY');
else {
	foreach($this->newest as $new)  {
		$link = JRoute::_('index.php?option=com_jtg&view=files&layout=file&id=' . $new->id);
		?>
	<li>
	<div class="list-left"><a
		title="<?php echo $this->boxlinktext[$new->access]; ?>"
		class="access_<?php echo $new->access; ?>" href="<?php echo $link; ?>"><?php
		if($new->title != "")
		echo htmlentities($new->title,ENT_QUOTES,"UTF-8");
		else
		echo ("<i>".JText::_('COM_JTG_NO_TITLE') . "</i>");
		?></a></div>
	<div class="list-right"><?php echo JtgHelper::parseMoreCats($this->sortedcats,$new->catid,"box",true); ?></div>
	<div class="no-float"></div>
	</li>
	<?php
	}
}
?>
</ul>
</div>
</div>
<?php
}
if ($this->hits != 0) {
	?>
<div class="<?php echo $this->toptracks; ?>">
<div class="<?php echo $this->toptracks; ?>_title"><span
	class="headline"> <a href="#TT_hits" name="TT_hits"><?php
	echo JText::_('COM_JTG_MOSTHITS');
	?></a> </span>
<ul class="title">
	<li>
	<div class="list-left"><b><?php echo JText::_('COM_JTG_TITLE'); ?></b></div>
	<div class="list-right"><b><?php echo JText::_('COM_JTG_HITS'); ?></b></div>
	<div class="no-float"></div>
	</li>
</ul>
</div>
<div class="<?php echo $this->toptracks; ?>_entry">
<ul class="entry">
<?php
if ( count ($this->hits) == 0 ) echo JText::_('COM_JTG_NOENTRY');
else {
	foreach($this->hits as $hits)  {
		$link = JRoute::_('index.php?option=com_jtg&view=files&layout=file&id=' . $hits->id);
		?>
	<li>
	<div class="list-left"><a
		title="<?php echo $this->boxlinktext[$hits->access]; ?>"
		class="access_<?php echo $hits->access; ?>"
		href="<?php echo $link; ?>"><?php
		if($hits->title != "")
		echo htmlentities($hits->title,ENT_QUOTES,"UTF-8");
		else
		echo ("<i>".JText::_('COM_JTG_NO_TITLE') . "</i>");
		?></a></div>
	<div class="list-right"><?php
	echo JtgHelper::getLocatedFloat($hits->hits);
	?></div>
	<div class="no-float"></div>
	</li>
	<?php
	}
}
?>
</ul>
</div>
</div>
<?php
}
if ($this->best != 0) {
	?>
<div class="<?php echo $this->toptracks; ?>">
<div class="<?php echo $this->toptracks; ?>_title"><span
	class="headline"> <a href="#TT_best" name="TT_best"><?php
	echo JText::_('COM_JTG_MOSTVOTES');
	?></a> </span>
<ul class="title">
	<li>
	<div class="list-left"><b><?php echo JText::_('COM_JTG_TITLE'); ?></b></div>
	<div class="list-right"><b><?php echo JText::_('COM_JTG_STARS'); ?></b></div>
	<div class="no-float"></div>
	</li>
</ul>
</div>
<div class="<?php echo $this->toptracks; ?>_entry">
<ul class="entry">
<?php
if ( count ($this->best[1]) == 0 ) echo JText::_('COM_JTG_NOENTRY');
else {
	foreach($this->best[1] as $best) {
		$link = JRoute::_('index.php?option=com_jtg&view=files&layout=file&id=' . $best->id);
		?>
	<li>
	<div class="list-left"><a
		title="<?php echo $this->boxlinktext[$best->access]; ?>"
		class="access_<?php echo $best->access; ?>"
		href="<?php echo $link; ?>"><?php
		if($best->title != "")
		echo htmlentities($best->title,ENT_QUOTES,"UTF-8");
		else
		echo ("<i>".JText::_('COM_JTG_NO_TITLE') . "</i>");
		?></a></div>
	<div class="list-right"><?php
	$stars_int = JtgHelper::getLocatedFloat((int)round($best->vote,0));
	$stars_float = JtgHelper::getLocatedFloat((float)$best->vote);
	$stars_float2 = JtgHelper::getLocatedFloat((float)round($best->vote,2));
	if ( $stars_float == 0 )
	$title = JText::_('COM_JTG_NOT_VOTED');
	elseif ( $best->vote == 1 )
	$title = "1 ".JText::_('COM_JTG_STAR');
	else
	$title = $stars_float2 . " ".JText::_('COM_JTG_STARS');
	if($this->best[0])
	{
		// Picture
		echo "<div title='" . $title . "'><ul class=\"rating " . $this->best[2][$stars_int] . "\"><li></li></ul></div>";
	}
	else
	{
		// Float
		echo "<a title='" . $title . "'>" . $stars_int . "</a>";
	}
	?></div>
	<div class="no-float"></div>
	</li>
	<?php
	}
}
?>
</ul>
</div>
</div>
<?php
}
if ($this->rand != 0) {
	?>
<div class="<?php echo $this->toptracks; ?>">
<div class="<?php echo $this->toptracks; ?>_title"><span
	class="headline"> <a href="#TT_rand" name="TT_rand"><?php
	echo JText::_('COM_JTG_RANDOM_TRACKS');
	?></a> </span>
<ul class="title">
	<li>
	<div class="list-left"><b><?php echo JText::_('COM_JTG_TITLE'); ?></b></div>
	<div class="list-right"><b><?php echo JText::_('COM_JTG_CAT'); ?></b></div>
	<div class="no-float"></div>
	</li>
</ul>
</div>
<div class="<?php echo $this->toptracks; ?>_entry">
<ul class="entry">
<?php
if ( count ($this->rand) == 0 ) echo JText::_('COM_JTG_NOENTRY');
else {
	foreach($this->rand as $rand) {
		$link = JRoute::_('index.php?option=com_jtg&view=files&layout=file&id=' . $rand->id);
		?>
	<li>
	<div class="list-left"><a
		title="<?php echo $this->boxlinktext[$rand->access]; ?>"
		class="access_<?php echo $rand->access; ?>"
		href="<?php echo $link; ?>"><?php
		if($rand->title != "")
		echo htmlentities($rand->title,ENT_QUOTES,"UTF-8");
		else
		echo ("<i>".JText::_('COM_JTG_NO_TITLE') . "</i>");
		?></a></div>
	<div class="list-right"><?php
//	echo $rand->cat;
	echo JtgHelper::parseMoreCats($this->sortedcats,$rand->catid,"box",true);
	?></div>
	<div class="no-float"></div>
	</li>
	<?php
	}
}
?>
</ul>
</div>
</div>
<?php
}
//width="0" height="0"
?>
<div class="no-float"><?php
if($this->map == "osm") {
	// Needed Pics preload
	?>
<div style="display: none">
	<img alt="cloud-popup-relative.png" src="http://www.openlayers.org/api/img/cloud-popup-relative.png" />
	<img alt="marker.png" src="http://www.openlayers.org/api/img/marker.png" />
	<img alt="close.png" src="http://www.openlayers.org/api/theme/default/img/close.gif" />
</div>
	<?php
}
?> <?php
echo $this->footer;
?></div>
