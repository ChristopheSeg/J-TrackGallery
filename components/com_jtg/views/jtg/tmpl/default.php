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

defined('_JEXEC') or die('Restricted access');
$this->get('State'); // have to get state before we can set state variables
$this->getModel()->setState('list.limit','0'); // show all tracks
$this->items = $this->get('Items');
$this->filterForm = $this->get('FilterForm');
$this->activeFilters = $this->get('ActiveFilters');
echo $this->lh;

echo "\n<script>\n";
echo "  DPCalLocs = ".$this->gpsData->parseDPCalLocations($this->dpcallocs);
echo "\n  DPCalMarkerStyle = new ol.style.Style( { image: new ol.style.Icon( { src: '/components/com_jtg/assets/images/orange-dot.png'}) });\n";
echo "</script>\n";

echo $this->gpsData->writeOLMap(null, $this->showtracks, $this->params, $this->items, JFactory::getApplication()->input->get('geoloc'));
?>

<style type="text/css">
#jtg_map.olMap {
	height: <?php echo$this->cfg->map_height; ?>;
	width: <?php echo$this->cfg->map_width; ?>;
	z-index: 0;
}

.olButton::before{
	display: none;
}
#jtg_map.fullscreen {
	height: 800px;
	width: 100%;
	z-index: 10000;
}
/* Fix Joomla3 max-width=100% breaks popups
   Fix Yooytheme theme.css also breaks popups */
#jtg_map canvas,img{
	max-width: none;
}

/* Fix Bootstrap-Openlayers issue */
.olMap img { max-width: none !important;
}

.olPopup img { max-width: none ! important; }

img.olTileImage {
	max-width: none !important;
}
</style>
<center>
      <div id="jtg_map" class="olMap"></div>

      <div id="popup" class="ol-popup">
          <a href="#" id="popup-closer" class="ol-popup-closer"></a>
          <div id="popup-content"></div>
      </div>
	<div>
	<br>
<div id="geo-msg"></div>
   <form action="<?php echo JURI::getInstance(); ?>" method="post"
   name="adminForm" id="adminForm">
   <div class="row-fluid">
            <div class="span12">
                <?php
                    echo JLayoutHelper::render(
                        'joomla.searchtools.default',
                        array('view' => $this)
                    );
                ?>
            </div>
        </div>
	</div>
</form>
</center>
<?php

// Karten-Auswahl END
if ($this->newest != 0)
{
	?>

<div class="<?php echo $this->toptracks; ?>">
	<div class="<?php echo $this->toptracks; ?>_title">
		<span class="headline"> <a href="#TT_newest"><?php
		echo JText::_('COM_JTG_NEWEST');
		?> </a>
		</span>
		<ul class="title">
			<li>
				<div class="list-left">
					<b><?php echo JText::_('COM_JTG_TITLE'); ?> </b>
				</div>
				<div class="list-right">
					<b><?php echo JText::_('COM_JTG_CAT'); ?> </b>
				</div>
				<div class="no-float"></div>
			</li>
		</ul>
	</div>
	<div class="<?php echo $this->toptracks; ?>_entry">
		<ul class="entry">
			<?php
			if ( count($this->newest) == 0 )
			{
				echo JText::_('COM_JTG_NOENTRY');
			}
			else
			{
				foreach ($this->newest as $new)
				{
					$link = JRoute::_('index.php?option=com_jtg&view=files&layout=file&id=' . $new->id);
					?>
			<li>
				<div class="list-left">
					<a title="<?php echo $this->boxlinktext[$new->access]; ?>"
						class="access_<?php echo $new->access; ?>"
						href="<?php echo $link; ?>"><?php

						if ($new->title != "")
						{
							echo htmlentities($new->title, ENT_QUOTES, "UTF-8");
						}
						else
						{
							echo '<i>' . JText::_('COM_JTG_NO_TITLE') . '</i>';
						}
						?> </a>
				</div>
				<div class="list-right">
					<?php echo JtgHelper::parseMoreCats($this->sortedcats, $new->catid, "box", true); ?>
				</div>
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

if ($this->hits != 0)
{
	?>
<div class="<?php echo $this->toptracks; ?>">
	<div class="<?php echo $this->toptracks; ?>_title">
		<span class="headline"> <a href="#TT_hits"><?php
		echo JText::_('COM_JTG_MOSTHITS');
		?> </a>
		</span>
		<ul class="title">
			<li>
				<div class="list-left">
					<b><?php echo JText::_('COM_JTG_TITLE'); ?> </b>
				</div>
				<div class="list-right">
					<b><?php echo JText::_('COM_JTG_HITS'); ?> </b>
				</div>
				<div class="no-float"></div>
			</li>
		</ul>
	</div>
	<div class="<?php echo $this->toptracks; ?>_entry">
		<ul class="entry">
			<?php

			if ( count($this->hits) == 0 )
			{
				echo JText::_('COM_JTG_NOENTRY');
			}
			else
			{
				foreach ($this->hits as $hits)
				{
					$link = JRoute::_('index.php?option=com_jtg&view=files&layout=file&id=' . $hits->id);
					?>
			<li>
				<div class="list-left">
					<a title="<?php echo $this->boxlinktext[$hits->access]; ?>"
						class="access_<?php echo $hits->access; ?>"
						href="<?php echo $link; ?>"><?php

						if ($hits->title != "")
						{
							echo htmlentities($hits->title, ENT_QUOTES, 'UTF-8');
						}
						else
						{
							echo '<i>' . JText::_('COM_JTG_NO_TITLE') . '</i>';
						}
						?> </a>
				</div>
				<div class="list-right">
					<?php
					echo JtgHelper::getLocatedFloat($hits->hits);
					?>
				</div>
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

if ($this->best != 0)
{
	?>
<div class="<?php echo $this->toptracks; ?>">
	<div class="<?php echo $this->toptracks; ?>_title">
		<span class="headline"> <a href="#TT_best"><?php
		echo JText::_('COM_JTG_MOSTVOTES');
		?> </a>
		</span>
		<ul class="title">
			<li>
				<div class="list-left">
					<b><?php echo JText::_('COM_JTG_TITLE'); ?> </b>
				</div>
				<div class="list-right">
					<b><?php echo JText::_('COM_JTG_STARS'); ?> </b>
				</div>
				<div class="no-float"></div>
			</li>
		</ul>
	</div>
	<div class="<?php echo $this->toptracks; ?>_entry">
		<ul class="entry">
			<?php

			if ( count($this->best[1]) == 0 )
			{
				echo JText::_('COM_JTG_NOENTRY');
			}
			else
			{
				foreach ($this->best[1] as $best)
				{
					$link = JRoute::_('index.php?option=com_jtg&view=files&layout=file&id=' . $best->id);
					?>
			<li>
				<div class="list-left">
					<a title="<?php echo $this->boxlinktext[$best->access]; ?>"
						class="access_<?php echo $best->access; ?>"
						href="<?php echo $link; ?>"><?php

						if ($best->title != "")
						{
							echo htmlentities($best->title, ENT_QUOTES, "UTF-8");
						}
						else
						{
							echo '<i>' . JText::_('COM_JTG_NO_TITLE') . '</i>';
						}
						?> </a>
				</div>
				<div class="list-right">
					<?php
					$stars_int = JtgHelper::getLocatedFloat((int) round($best->vote, 0));
					$stars_float = JtgHelper::getLocatedFloat((float) $best->vote);
					$stars_float2 = JtgHelper::getLocatedFloat((float) round($best->vote, 2));

					if ( $stars_float == 0 )
					{
						$title = JText::_('COM_JTG_NOT_VOTED');
					}
					elseif ( $best->vote == 1 )
					{
						$title = "1 " . JText::_('COM_JTG_STAR');
					}
					else
					{
						$title = $stars_float2 . " " . JText::_('COM_JTG_STARS');
					}

					if ($this->best[0])
					{
						// Picture
						echo "<div title='" . $title . "'><ul class=\"rating " . $this->best[2][$stars_int] . "\"><li></li></ul></div>";
					}
					else
					{
						// Float
						echo "<a title='" . $title . "'>" . $stars_int . "</a>";
					}
					?>
				</div>
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

if ($this->rand != 0)
{
	?>
<div class="<?php echo $this->toptracks; ?>">
	<div class="<?php echo $this->toptracks; ?>_title">
		<span class="headline"> <a href="#TT_rand"><?php
		echo JText::_('COM_JTG_RANDOM_TRACKS');
		?> </a>
		</span>
		<ul class="title">
			<li>
				<div class="list-left">
					<b><?php echo JText::_('COM_JTG_TITLE'); ?> </b>
				</div>
				<div class="list-right">
					<b><?php echo JText::_('COM_JTG_CAT'); ?> </b>
				</div>
				<div class="no-float"></div>
			</li>
		</ul>
	</div>
	<div class="<?php echo $this->toptracks; ?>_entry">
		<ul class="entry">
			<?php

			if ( count($this->rand) == 0 )
			{
				echo JText::_('COM_JTG_NOENTRY');
			}
			else
			{
				foreach ($this->rand as $rand)
				{
					$link = JRoute::_('index.php?option=com_jtg&view=files&layout=file&id=' . $rand->id);
					?>
			<li>
				<div class="list-left">
					<a title="<?php echo $this->boxlinktext[$rand->access]; ?>"
						class="access_<?php echo $rand->access; ?>"
						href="<?php echo $link; ?>"><?php

						if ($rand->title != "")
						{
							echo htmlentities($rand->title, ENT_QUOTES, "UTF-8");
						}
						else
						{
							echo '<i>' . JText::_('COM_JTG_NO_TITLE') . '</i>';
						}
						?> </a>
				</div>
				<div class="list-right">
					<?php
					// Echo $rand->cat;
					echo JtgHelper::parseMoreCats($this->sortedcats, $rand->catid, "box", true);
					?>
				</div>
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

?>
<div class="no-float">
	<?php
/*
	// Needed Pics preload
	?>
	<div style="display: none">
		<img alt="cloud-popup-relative.png"
			src="///www.openlayers.org/api/img/cloud-popup-relative.png" /> <img
			alt="marker.png" src="///www.openlayers.org/api/img/marker.png" />
		<img alt="close.png"
			src="///www.openlayers.org/api/theme/default/img/close.gif" />
	</div>
	<?php
*/
	echo $this->footer;
	?>
	<script type="text/javascript">
	    var olmap={ title: 'com_jtg_map_object' } 
		 var jtgMapZoomLevel = <?php echo $this->default_zoom; ?>;
	    slippymap_init("map");
	</script>
</div>
