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
echo $this->lh;

$maySeeSingleFile = $this->maySeeSingleFile($this);

if ($maySeeSingleFile === true)
{
	$durationbox = (bool) $this->params->get("jtg_param_show_durationcalc");
	echo $this->map;

	if ( ( $this->cfg->gallery == "jd2" ) OR ( $this->cfg->gallery == "jd21" )  OR ( $this->cfg->gallery == "highslide" ) )
	{
		echo $this->galscript;
	}

	if ( $this->params->get("jtg_param_show_heightchart") AND $this->elevationDataExists)
	{
		$heightchart = true;
	}
	else
	{
		$heightchart = false;
	}

	if ( $this->params->get("jtg_param_show_speedchart") AND $this->speedDataExists )
	{
		$speedchart = true;
	}
	else
	{
		$speedchart = false;
	}

	if ($this->params->get("jtg_param_show_speedchart") AND $this->beatDataExists )
	{
		$beatchart = true;
	}
	else
	{
		$beatchart = false;
	}

	$havechart = ($heightchart or $speedchart) or $beatchart;

	if ($havechart)
	{
		$axisnumber = 0;

		if ($heightchart)
		{
			// Heightchart is always on left (first) axis
			$heightchartaxis = 1;
			$axisnumber ++;
		}
		else
		{
			$heightchartaxis = 0;
		}

		if ($speedchart)
		{
			// Speedchart is on left (first) axis or on right axis when there is a heighchart
			$speedchartaxis = $heightchartaxis + 1;
			$axisnumber++;
		}
		else
		{
			$speedchartaxis = 0;
		}

		if ($beatchart)
		{
			// Beatchart is on left (first) axis or on right axis when there is a heighchart or a speed chart
			if ($axisnumber = 1)
			{
				$beatchartaxis = 2;
				$axisnumber = 2;
			}
			else
			{
				$beatchartaxis = 1;
				$axisnumber = 1;
			}
		}
		else
		{
			$beatchartaxis = 0;
		}
		// Code support for joomla version greater than 3.0
		if (JVERSION >= 3.0)
		{
			JHtml::_('jquery.framework');
		}
		else
		{
			JHtml::script('jquery.js', 'components/com_jtg/assets/js/', false);
		}
		?>
<!-- begin Charts -->

<script type="text/javascript">
		jQuery.noConflict();
		</script>
<script
	src="http://code.highcharts.com/highcharts.js"></script>
<script
	src="http://code.highcharts.com/modules/exporting.js"></script>

<script type="text/javascript">
		var isIE=0;
		if (navigator.appName == 'Microsoft Internet Explorer')
			isIE=1;
		(function($){ // encapsulate jQuery
		 $(function () {
			$('#elevation').highcharts({
			chart: {
				type: 'line',
				zoomType: 'xy'
			},
			 credits: {
			enabled: 'false'
			},
				plotOptions: {
				area: {
				stacking: 'normal',
				lineColor: '#666666',
				lineWidth: 1,
				marker: {
					lineWidth: 1,
					lineColor: '#666666'
				}
				},
				series: {
				fillOpacity: 0.1
				}
			},

			title: {
				text: null
			},
			xAxis: [{
				labels: {
				formatter: function() {
					return this.value + '<?php echo JText::_('COM_JTG_DISTANCE_UNIT_' . strtoupper($this->cfg->unit)); ?>';
				}
			},
				tooltip: {
				valueDecimals: 2,
				valueSuffix: '<?php echo JText::_('COM_JTG_DISTANCE_UNIT_' . strtoupper($this->cfg->unit)); ?>'

				}
			}],
			yAxis: [
			<?php if ($heightchart)
			{
			?>
			{ // Elevation data
				labels: {
				formatter: function() {
					return this.value +'<?php echo JText::_('COM_JTG_ELEVATION_UNIT'); ?>';
				},
				style: {
					color: '#89A54E'
				}
				},
				title: {
				text: '<?php echo JText::_('COM_JTG_ELEVATION') . '(' . JText::_('COM_JTG_ELEVATION_UNIT'); ?>)',
				style: {
					color: '#89A54E'
				}
				}
			}
<?php
}

if ($speedchart)
{
?>
			, { // Speed data
				gridLineWidth: 0,
				title: {
				text: '<?php echo JText::_('COM_JTG_SPEED') . '(' . JText::_('COM_JTG_SPEED_UNIT_' . strtoupper($this->cfg->unit)); ?>)',
				style: {
					color: '#4572A7'
				}
				},
				labels: {
				formatter: function() {
					return this.value +'<?php echo JText::_('COM_JTG_SPEED_UNIT_' . strtoupper($this->cfg->unit)); ?>';
				},
				style: {
					color: '#4572A7'
				}
				}
<?php
if ($speedchartaxis == 2)
{
?>
					,opposite: true // Suppress this if only one axis
<?php
}
?>
			}
<?php
}

if ($beatchart)
{
?>
				,{ // Heart beat data
				gridLineWidth: 0,
				title: {
				text: '<?php echo JText::_('COM_JTG_HEARTFREQU') . '(' . JText::_('COM_JTG_HEARTFREQU_UNIT'); ?>)',
				style: {
					color: '#AA4643'
				}
				},
				labels: {
				formatter: function() {
					return this.value +'<?php echo JText::_('COM_JTG_HEARTFREQU_UNIT'); ?>'
				},
				style: {
					color: '#AA4643'
				}
				}
	<?php
	if ($beatchartaxis == 2)
	{
		// Suppress this if only one axis
		?>
					,opposite: true
	<?php
	}
	?>
			}
<?php
}
?>
			],
<?php
// TODO if (AnimatedCursorLayer)
if (true)
{
?>
			plotOptions: {
				series: {
					point: {
						events: {
							mouseOver: function () {
								var index = this.series.processedXData.indexOf(this.x);
								hover_profil_graph(longitudeData[index],latitudeData[index], index);
							}
						}
					},
					events: {
						mouseOut: function () {
						out_profil_graph();
						}
					}
				}
			},
<?php
}
?>
			tooltip: {
			valueDecimals: 2,
			formatter: function() {
			var s = '<b><?php echo JText::_('COM_JTG_DISTANCE'); ?>: '
				+ this.x
				+' <?php echo JText::_('COM_JTG_DISTANCE_UNIT_' . strtoupper($this->cfg->unit)); ?></b>';
			$.each(this.points, function(i, point) {
				s += '<br/>'+ point.series.name +': '+
				point.y + ' ' + point.series.options.unit;
			});
			return s;
			},
			shared: true
			},
			legend: {
				layout: 'vertical',
				align: 'left',
				x: 120,
				verticalAlign: 'top',
				y: 0,
				floating: true,
				backgroundColor: '#FFFFFF',
				labelFormatter: function() {
				return this.name <?php echo $axisnumber > 1? "+ ' (" . JText::_('COM_JTG_CLICK_TO_HIDE') . ")'": ''; ?>;
				}
			},
			series: [
<?php
if ($heightchart)
{
?>
				{
				name: '<?php echo JText::_('COM_JTG_ELEVATION'); ?>',
				unit: 'm',
				color: '#4572A7',
				yAxis: <?php echo $heightchartaxis - 1; ?>,
				data: <?php echo $this->heighdata; ?>,
				marker: {
				enabled: false
				},
				tooltip: {
				valueSuffix: ' m'
				}

			}
<?php
}

if ($speedchart)
{
?>
				, {
				name: '<?php echo JText::_('COM_JTG_SPEED'); ?>',
				unit:'<?php echo JText::_('COM_JTG_SPEED_UNIT_' . strtoupper($this->cfg->unit)); ?>',
				color: '#AA4643',
				yAxis: <?php echo $speedchartaxis - 1; ?>,
				data: <?php echo $this->speeddata; ?>,
				marker: {
				enabled: false
				},
				dashStyle: 'shortdot',
				tooltip: {
				valueSuffix: ' <?php echo JText::_('COM_JTG_SPEED_UNIT_' . strtoupper($this->cfg->unit)); ?>'
				}

			}
<?php
}

if ($beatchart)
{
?>
				, {
				name: '<?php echo JText::_('COM_JTG_HEARTFREQU'); ?>',
				unit: '<?php echo JText::_('COM_JTG_HEARTFREQU_UNIT'); ?>',
				color: '#89A54E',
				data: <?php echo $this->beatdata; ?>,
				tooltip: {
				valueSuffix: '<?php echo JText::_('COM_JTG_HEARTFREQU_UNIT'); ?>'
				}
			}
<?php
}
?>
			]
			});
		});
		})(jQuery);
		</script>
<!-- end Charts -->

	<?php
	}

	echo $this->parseTemplate("headline", $this->track->title, "jtg_param_header_map");
	?>

<style type="text/css">
#jtg_map.olMap {
	height: <?php echo$this->cfg->map_height; ?>;
	width: <?php echo$this->cfg->map_width; ?>;
	z-index: 0;
}
.olButton::before {
	display: none;
}

#jtg_map.fullscreen {
	height: 800px;
	width: 100%;
	z-index: 20;
}

/* Fix Bootstrap-Openlayers issue */
img.olTileImage {
	max-width: none !important;
}
.olPopup img { max-width: none !important;
}

</style>
<?php
if ($this->map)
{
	echo "<center><div id=\"jtg_map\" class=\"olMap\"></div><br /></center>\n";
}
?>
<!--    <div>
<?php
/*
ToDo: inspect GET['subid'] to give the ability single tracks are shown
if ( $this->clicklist !== false ) {
	$return = ("<div><ul>\n");
	foreach ($this->clicklist AS $value => $key ) {
		$return .= ("<li><a href=\"" . $key['link'] . "\">" . JText::_('COM_JTG_TRACK').$value . ": " . $key['name'] . "</a></li>\n");
	}
	$return .= ("</ul></div>\n");
	echo $return;
}
*/
?>
    </div>
-->
<div id="profile" style="width:<?php echo (int) $this->cfg->charts_width; ?>" >
	<?php
	if ($havechart)
	{
		?>
	<div class="profile-img" id="elevation" style="width:<?php echo $this->cfg->charts_width; ?>
		; height: <?php echo $this->cfg->charts_height; ?>;"></div>
	<?php
	}
	?>
	<div class="gps-info">
		<table border=0 style="width:98%;" align="center">
			<thead>
				<tr>
					<th colspan="3"><?php echo JText::_('COM_JTG_DETAILS'); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo JText::_('COM_JTG_UPLOADER'); ?>:</td>
					<td><?php echo $this->profile; ?></td>
					<td rowspan="6" valign="top"><?php
					if ( ($durationbox) AND ($this->track->distance != "") AND ((float) $this->track->distance != 0))
					{
						?>
						<div id="count-box">
							<div align="center">
								<span><?php echo JText::_('COM_JTG_TIMECOUNT'); ?> </span>
							</div>
							<div align="center">
								<span> <label for="speed"> <?php echo JText::_('COM_JTG_AVG_SPEED'); ?>
								</label> <input type="text" name="speed" id="speed" value=""
									size="4" />
								</span> <input type="button" name="button" class="button"
									value="<?php echo JText::_('JSUBMIT'); ?>"
									onclick="getAvgTime(document.getElementById('speed').value,<?php echo $this->distance_float; ?>,
				<?php echo '\'' . JText::_('COM_JTG_SEPARATOR_DEC') . '\''; ?>);" />
							</div>
							<div align="center">
								<span> <label for="time"> <?php echo JText::_('COM_JTG_ESTIMATED_TIME'); ?>
								</label> <input type="text" name="time" id="time" value=""
									size="9" readonly="readonly" />
								</span>
							</div>
						</div> <?php
					}
				?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JTG_DATE'); ?>:</td>
					<td><?php echo $this->date; ?></td>
				</tr>
				<?php if ( $this->track->level != "0" )
		{
		?>
				<tr>
					<td><?php echo JText::_('COM_JTG_LEVEL'); ?>:</td>
					<td><?php echo $this->level; ?></td>
				</tr>
<?php
}

if ( ($this->track->distance != "") AND ((float) $this->track->distance != 0) )
{
?>
				<tr>
					<td><?php echo JText::_('COM_JTG_DISTANCE'); ?>:</td>
					<td><?php echo $this->distance; ?></td>
				</tr>
<?php
}

if ($this->track->ele_asc)
{
?>
				<tr>
					<td><?php echo JText::_('COM_JTG_ELEVATION_UP'); ?>:</td>
					<td><?php
						echo $this->track->ele_asc;
						echo ' ' . JText::_('COM_JTG_METERS');
						?>
					</td>
				</tr>
<?php
}

if ($this->track->ele_desc)
{
?>
				<tr>
					<td><?php echo JText::_('COM_JTG_ELEVATION_DOWN'); ?>:</td>
					<td><?php echo $this->track->ele_desc; ?>
						<?php echo ' ' . JText::_('COM_JTG_METERS'); ?>
					</td>
				</tr>
<?php
}
?>
				<tr>
					<td><?php echo JText::_('COM_JTG_HITS'); ?>:</td>
					<td><?php echo $this->track->hits; ?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JTG_CATS'); ?>:</td>
					<td colspan="2"><?php
					$JtgHelper = new JtgHelper;
					echo $JtgHelper->parseMoreCats($this->sortedcats, $this->track->catid, "TrackDetails", true);
					?>
					</td>
				</tr>
				<tr>
					<td><?php echo JText::_('COM_JTG_BIG_MAP') ?>:</td>
					<td colspan="2"><a rel="width[1000];height[700];" class="jcebox"
						href="http://maps.google.com/maps?q=<?php echo $this->track->start_n . "," . $this->track->start_e; ?>"
						target="_blank">Google</a>, <a rel="width[1000];height[700];"
						class="jcebox"
						href="http://openstreetmap.org/?mlat=<?php echo $this->track->start_n . "&amp;mlon=" . $this->track->start_e; ?>"
						target="_blank">OpenStreetMap</a>, <a
						rel="width[1000];height[700];" class="jcebox"
						href="http://www.geocaching.com/map/default.aspx?lat=<?php echo $this->track->start_n . "&amp;lng=" . $this->track->start_e; ?>"
						target="_blank">Geocaching.com</a>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="no-float"></div>
</div>
<?php
if ($this->cfg->usevote == 1)
{
	echo $this->parseTemplate("headline", JText::_('COM_JTG_VOTING'), "jtg_param_header_rating");
	$template = "<div id=\"ratingbox\">
	<ul id=\"1001\" class=\"rating " . $this->vote['class'] . "\">\n";

	for ($i = 1; $i <= 10; $i++)
	{
		$link = "index.php?option=com_jtg&controller=files&id=" . $this->track->id . "&task=vote&rate=" . $i . "#jtg_param_header_rating";
		$link = JRoute::_($link, false);
		$template .= "		<li id=\"" . $i . "\" class=\"rate " . $this->stars->$i . "\">\n"
		. "			<a href=\"" . $link . "\" title=\"" . JText::_('COM_JTG_STARS_' . $i) . "\">" . $i . "</a>\n"
		. "		</li>\n";
	}

	$template .= "	</ul>\n";

	if ( $this->vote['count'] == 0 )
	{
		$template .= JText::_('COM_JTG_NOT_VOTED') . "\n";
	}
	else
	{
		$template .= JText::sprintf(
				'COM_JTG_TRACK_RATING',
				$JtgHelper->getLocatedFloat($this->vote['rate']),
				$this->vote['count']
				) . "\n";
	}

	echo $this->parseTemplate("description", $template);
	?>
<div class="no-float"></div>
</div>
<?php
}
else
{
	echo "<a name=\"jtg_param_header_rating\"></a>";
}

if ($this->cfg->download >= 1)
{
	echo $this->parseTemplate("headline", JText::_('COM_JTG_DOWNLOAD'), "jtg_param_header_download");
	echo $this->parseTemplate("description", null, null, "open");

	if (($this->cfg->download == 2) AND ($this->user->get('id') == 0))
	{
		// Registered users only
		echo $this->parseTemplate("description", JText::_('COM_JTG_NOT_DOWNLOAD'));
	}
	else
	{
		$download_option = "";

		if ( (bool) $this->params->get("jtg_param_offer_download_gpx") )
		{
			$download_option .= "	<option value=\"gpx\">GPX</option>\n";
		}

		if ( (bool) $this->params->get("jtg_param_offer_download_kml") )
		{
			$download_option .= "	<option value=\"kml\">KML</option>\n";
		}

		if ( (bool) $this->params->get("jtg_param_offer_download_tcx") )
		{
			$download_option .= "	<option value=\"tcx\">Garmin (tcx)</option>\n";
		}

		echo $this->jscript;
		?>
<form name="adminForm" id="adminForm" method="post"
	action="<?php echo $this->action; ?>">

	<span><label for="format"><?php echo JText::_('COM_JTG_FORMAT'); ?>&nbsp;</label>
		<select name="format" id="format">
			<option value="">
				<?php echo JText::_('COM_JTG_SELECT'); ?>
			</option>
			<?php echo $download_option; ?>
	</select>
		<button class="button" type="button"
			onclick="submitbutton('download')">
			<?php echo JText::_('COM_JTG_DOWNLOAD_THIS_TRACK') ?>
		</button> </span>
	<?php
	echo JHtml::_('form.token') . "\n"; ?>
	<input type="hidden" name="option" value="com_jtg" /> <input
		type="hidden" name="id" value="<?php echo $this->track->id; ?>" /> <input
		type="hidden" name="task" value="" />
</form>
<?php echo $this->parseTemplate("description", null, null, "close"); ?>
<div class="no-float"></div>
<!--</div>-->
<?php
	}
}
else
{
	echo "<a name=\"jtg_param_header_download\"></a>";
}

if (! $this->params->get("jtg_param_disable_terrains"))
{
	// Terrain description is enabled
	if ($this->track->terrain)
	{
		$terrain = $this->track->terrain;
		$terrain = explode(',', $terrain);
		$newterrain = array();

		foreach ($terrain as $t)
		{
			$t = $this->model->getTerrain(' WHERE id=' . $t);

			if ( ( isset($t[0])) AND ( $t[0]->published == 1 ) )
			{
				$newterrain[] = $t[0]->title;
			}
		}

		$terrain = implode(', ', $newterrain);
		echo $this->parseTemplate('headline', JText::_('COM_JTG_TERRAIN'), "jtg_param_header_terrain");
		echo $this->parseTemplate('description', $terrain);
	}
	else
	{
		echo "<a name=\"jtg_param_header_terrain\"></a>";
	}
}

if ($this->track->description)
{
	echo $this->parseTemplate("headline", JText::_('COM_JTG_DESCRIPTION'), "jtg_param_header_description");

	echo $this->parseTemplate("description", JHTML::_('content.prepare', $this->track->description));
}
else
{
	echo '<a name="jtg_param_header_description"></a>';
}

if (($this->images) AND ( $this->cfg->gallery != "none" ))
{
	echo $this->parseTemplate('headline', JText::_('COM_JTG_GALLERY'), 'jtg_param_header_gallery');
	echo $this->parseTemplate('description', $this->imageBlock);
}
else
{
	echo '<a name="jtg_param_header_gallery"></a>';
}

if ( $this->cfg->approach != 'no' )
{
	echo $this->parseTemplate("headline", JText::_('COM_JTG_APPROACH_SERVICE'), "jtg_param_header_approach");
	$description = "	<table id=\"approach\">
	<tr valign=\"top\">";

	switch ($this->cfg->approach)
	{
		case 'ors':
			$description .= $this->approach('ors');
			break;
		case 'cm':
			$description .= $this->approach('cm');
			break;
		case 'cmkey':
			$description .= $this->approach('cmkey');
			break;
		case 'easy':
			$description .= $this->approach('easy');
			break;
	}

	$description .= "		</tr>
	</table>\n";
	echo $this->parseTemplate("description", $description);
}
else
{
	echo "<a name=\"jtg_param_header_approach\"></a>";
}
// Approach END

// Adding the comments
if ($this->cfg->comments == 1)
{
	echo $this->parseTemplate("headline", JText::_('COM_JTG_COMMENTS'), "jtg_param_header_comment");

	if (!$this->comments)
	{
		echo "<div>" . JText::_('COM_JTG_NO_COMMENTS_DESC') . "</div>";
	}
	else
	{
		for ($i = 0, $n = count($this->comments); $i < $n; $i++)
		{
			$comment = $this->comments[$i];
			?>
<div class='comment'>
	<div class="comment-header">
		<div class="comment-title">
			<?php echo $i + 1 . ": " . $comment->title; ?>
		</div>
		<div class="date">
			<?php echo JHtml::_('date', $comment->date, JText::_('COM_JTG_DATE_FORMAT_LC4')); ?>
		</div>
		<div class="no-float"></div>
	</div>
	<div class="comment-autor">
		<?php echo $comment->user; ?>
		<br />
		<?php
		echo $this->model->parseEMailIcon($comment->email);

		if ($comment->homepage)
		{
			echo ' ' . $this->model->parseHomepageIcon($comment->homepage);
		}
		?>
	</div>
	<div class="comment-text">
		<?php echo $comment->text; ?>
	</div>
	<div class="no-float"></div>
</div>
<?php
		}
	}
	//

	if ($JtgHelper->userHasCommentsRights() )
	{
		echo $this->model->addcomment($this->cfg);
	}
	else
	{
		echo JText::_('COM_JTG_ADD_COMMENT_NOT_AUTH');
	}
}
elseif ($this->cfg->comments == 3)
{
	$jcommentsfile = 'components/com_jcomments/jcomments.php';

	if ((JFile::exists(JPATH_SITE . '/' . $jcommentsfile)))
	{
		// 	global $mosConfig_absolute_path;
		require_once 'components/com_jcomments/jcomments.php';
		echo JComments::showComments($this->track->id, "com_jtg");
	}
	else
	{
		JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_ERROR_ACTIVATE_JCOMMENTS'), 'Error');
	}
}
else
{
	echo "<a name=\"jtg_param_header_comment\"></a>";
}
?>
<div style="display: none">
	<!-- load necessary pics in background -->
	<img src="http://www.openlayers.org/api/img/cloud-popup-relative.png"
		alt="display:none" /> <img
		src="http://www.openlayers.org/api/img/marker.png" alt="display:none" />
	<img src="http://www.openlayers.org/api/theme/default/img/close.gif"
		alt="display:none" />
</div>
<?php
}
elseif ($maySeeSingleFile === false)
{
	echo '<p class="error">' . JText::_('COM_JTG_NOT_AUTH') . '</p>';
}
else
{
	echo '<p class="error">' . $maySeeSingleFile . '</p>';
}

echo $this->footer;

if ( isset($this->cfg) )
{
	echo "\n<script type=\"text/javascript\">\n
	var olmap={ title: 'com_jtg_map_object' } \n
	slippymap_init();</script>\n";
}
