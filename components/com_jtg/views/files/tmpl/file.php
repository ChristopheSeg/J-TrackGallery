<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJO3SM and joomGPStracks teams
 * @package    com_jtg
 * @subpackage frontend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
echo $this->lh;

$maySeeSingleFile = $this->maySeeSingleFile($this);
if ($maySeeSingleFile === true) {
	$speedchart = (bool)$this->params->get("jtg_param_show_speedchart");
	$heightchart = (bool)$this->params->get("jtg_param_show_heightchart");
	$heightchart = true; //TODO parameter jtg_param_show_heightchart not used !!

	$durationbox = (bool)$this->params->get("jtg_param_show_durationcalc");
	$durationbox = true; //TODO parameter jtg_param_show_heightchart not used !!
	echo $this->map;
	if ( ( $this->cfg->gallery == "jd2" ) OR ( $this->cfg->gallery == "jd21" ) ) {
		echo $this->galscript;
	} 
	if($heightchart) {
if ( ($this->track->ele_asc != 0) AND ($this->track->ele_desc != 0) AND ($this->chart != null) ) {
   
?>
<script type="text/javascript">
    dojo.require("dojox.charting.Chart2D");
    dojo.require("dojox.charting.widget.Legend");
    makeCharts = function(){
        var hb = '<?php echo JText::_('COM_JTG_HEARTFREQU'); ?>';
        var ele = '<?php echo JText::_('COM_JTG_ELEVATION'); ?>';
        var chart1 = new dojox.charting.Chart2D("elevation");
        chart1.addPlot("default", {type: "Lines", hAxis: "x", vAxis: "y", markers: false, tension:4, shadows: {dx: 2, dy: 2, dw: 2}});
        chart1.addAxis("x", {leftBottom: true, majorLabels: false, minorLabels: true, majorTickStep: 100});
        chart1.addAxis("y", {vertical: true, fixUpper: "major", includeZero: false, majorTickStep: 200, fontColor:"#<?php echo $this->cfg->charts_linec; ?>"});
        chart1.addPlot("other", {type: "Lines", hAxis: "x", vAxis: "other y"});
        chart1.addAxis("other y", {vertical: true, leftBottom: false, fontColor:"#<?php echo $this->cfg->charts_bg; ?>"});
        chart1.addSeries(ele, [<?php echo $this->chart; ?>],
            {plot: "default", stroke: {color:"#<?php echo $this->cfg->charts_linec; ?>"}}
        );
<?php if (isset($this->beat)) : ?>
        chart1.addSeries(hb, [<?php echo $this->beat; ?>],
            {plot:"other", stroke: {color:"#<?php echo $this->cfg->charts_bg; ?>"}}
        );
<?php endif; ?>
        chart1.render();
        var legend1 = new dojox.charting.widget.Legend({chart: chart1, horizontal: true}, "legend1");
        legend1.chart=chart1;
    };

    dojo.addOnLoad(makeCharts);
    </script>
<?php
$nohight = false;
} else $nohight = true;
}
if($speedchart) {
	if ( ( isset($this->track->istrack) ) AND ( $this->track->istrack == "1" ) ) {
		if ( $this->speed !== false ) {
?>
<script type="text/javascript">
dojo.require("dojox.charting.Chart2D");
dojo.require("dojox.charting.widget.Legend");
    makeSpeedChart = function(){
        <?php echo ("var speedchart = '".JText::_('COM_JTG_SPEED_'.strtoupper($this->cfg->unit)) . "';\n"); ?>
        var chart2 = new dojox.charting.Chart2D("speedchart");
        chart2.addPlot("default", {type: "Lines", hAxis: "x", vAxis: "y", markers: false, tension:4, shadows: {dx: 2, dy: 2, dw: 2}});
        chart2.addAxis("x", {leftBottom: true, majorLabels: false, minorLabels: true, majorTickStep: 100});
        chart2.addAxis("y", {vertical: true, fixUpper: "major", includeZero: false, majorTickStep: 10, fontColor:"#<?php echo $this->cfg->charts_linec; ?>"});
        chart2.addPlot("other", {type: "Lines", hAxis: "x", vAxis: "other y"});
        chart2.addAxis("other y", {vertical: true, leftBottom: false, fontColor:"#<?php echo $this->cfg->charts_bg; ?>"});
        chart2.addSeries(speedchart, [<?php echo $this->speed; ?>],
            {plot: "default", stroke: {color:"#<?php echo $this->cfg->charts_linec; ?>"}}
        );
        chart2.render();
        var legend2 = new dojox.charting.widget.Legend({chart: chart2, horizontal: true}, "legend2");
        legend2.chart=chart2;
    };

    dojo.addOnLoad(makeSpeedChart);
    </script>

<!-- TODO END TEST highcharts
<script type="text/javascript">
    window.addEvent('domready', function() {
		wpgpxmaps({ targetId    : "142_3954690",
					mapType     : "SATELLITE",
					mapData     : [[45.8914800,11.8218090],[45.8910720,11.8205380],[45.8903160,11.8194700],[45.8899140,11.8185260],[45.8896780,11.8186870],[45.8886800,11.8183810],[45.8880900,11.8180430],[45.8874510,11.8178710],[45.8867750,11.8175870],[45.8861480,11.8172060],[45.8856760,11.8166480],[45.8851070,11.8160850],[45.8845010,11.8155490],[45.8838030,11.8153880],[45.8831490,11.8150390],[45.8828540,11.8148940],[45.8825110,11.8139340],[45.8824410,11.8130170],[45.8818130,11.8129630],[45.8814270,11.8127430],[45.8819420,11.8120460],[45.8825750,11.8115360],[45.8831110,11.8109140],[45.8834920,11.8106350],[45.8833470,11.8096530],[45.8830630,11.8086450],[45.8826770,11.8089610],[45.8825800,11.8080600],[45.8819850,11.8077760],[45.8813570,11.8072230],[45.8808640,11.8066280],[45.8802310,11.8060480],[45.8796460,11.8053880],[45.8791040,11.8058660],[45.8783690,11.8059620],[45.8777420,11.8054690],[45.8772110,11.8060540],[45.8768460,11.8068480],[45.8762830,11.8065580],[45.8756710,11.8060000],[45.8752100,11.8061720],[45.8747590,11.8068530],[45.8746570,11.8060590],[45.8739600,11.8068800],[45.8737240,11.8077760],[45.8735680,11.8073840],[45.8733110,11.8065200],[45.8728980,11.8057320],[45.8726030,11.8051260],[45.8727630,11.8045840],[45.8728390,11.8039990],[45.8727850,11.8031190],[45.8729890,11.8024920],[45.8735040,11.8029210],[45.8735630,11.8019770],[45.8738420,11.8012360],[45.8742600,11.8004370],[45.8749410,11.8003830],[45.8754670,11.8005070],[45.8760890,11.8010750],[45.8766690,11.8016390],[45.8774090,11.8015960],[45.8779880,11.8021210],[45.8786590,11.8021700],[45.8792380,11.8015420],[45.8797370,11.8020680],[45.8803330,11.8027600],[45.8809980,11.8032100],[45.8816310,11.8036240],[45.8823180,11.8033450],[45.8829180,11.8036340],[45.8837340,11.8038760],[45.8843510,11.8040370],[45.8847150,11.8047450],[45.8851980,11.8055870],[45.8855900,11.8064770],[45.8860990,11.8072230],[45.8864590,11.8080600],[45.8866900,11.8089770],[45.8872100,11.8093150],[45.8877730,11.8096210],[45.8882020,11.8106510],[45.8885830,11.8116060],[45.8893230,11.8118200],[45.8901010,11.8119010],[45.8907770,11.8117240],[45.8913190,11.8123570],[45.8919470,11.8129840],[45.8924240,11.8137520],[45.8929390,11.8142450],[45.8935180,11.8144700],[45.8941300,11.8151570],[45.8946400,11.8158110],[45.8952990,11.8156930],[45.8959490,11.8159080],[45.8965600,11.8163910],[45.8971980,11.8167560],[45.8977890,11.8172650],[45.8985450,11.8171790],[45.8992800,11.8174580],[45.8999400,11.8178450],[45.9004170,11.8186870],[45.9007760,11.8195500],[45.9012650,11.8202320],[45.9017050,11.8206930],[45.9023910,11.8208810],[45.9030510,11.8213420],[45.9036890,11.8218730],[45.9042690,11.8225440],[45.9049390,11.8229250],[45.9055780,11.8233480],[45.9062320,11.8236110],[45.9068170,11.8240890],[45.9073960,11.8246950],[45.9080830,11.8251130],[45.9087690,11.8251030],[45.9095150,11.8252260],[45.9101530,11.8256820],[45.9108560,11.8260520],[45.9114950,11.8264220],[45.9121760,11.8267550],[45.9128460,11.8271300],[45.9134260,11.8277800],[45.9138120,11.8283540],[45.9140750,11.8293670],[45.9143650,11.8303920],[45.9146650,11.8313410],[45.9151050,11.8320870],[45.9155930,11.8329240],[45.9158340,11.8338250],[45.9160110,11.8347530],[45.9159690,11.8356970],[45.9162960,11.8364110],[45.9166020,11.8373550],[45.9167200,11.8383530],[45.9167730,11.8393830],[45.9166660,11.8403430],[45.9166980,11.8413250],[45.9168210,11.8422420],[45.9164190,11.8422800],[45.9158770,11.8415230],[45.9155230,11.8406330],[45.9150940,11.8397050],[45.9146270,11.8388730],[45.9139140,11.8385350],[45.9131900,11.8385030],[45.9127390,11.8378000],[45.9125510,11.8368670],[45.9120580,11.8361910],[45.9124920,11.8354610],[45.9125780,11.8345600],[45.9121010,11.8338140],[45.9115860,11.8332730],[45.9109690,11.8328010],[45.9112320,11.8318990],[45.9112690,11.8309390],[45.9107970,11.8308480],[45.9100680,11.8309120],[45.9094190,11.8307080],[45.9088450,11.8312720],[45.9083780,11.8321350],[45.9078250,11.8328010],[45.9073910,11.8335520],[45.9070260,11.8342700],[45.9064730,11.8350110],[45.9057870,11.8347590],[45.9051110,11.8345490],[45.9043650,11.8343240],[45.9040000,11.8335140],[45.9031800,11.8335840],[45.9024820,11.8334820],[45.9021930,11.8325430],[45.9016990,11.8317920],[45.9011250,11.8312450],[45.9006640,11.8304940],[45.9002020,11.8301080],[45.8998000,11.8294960],[45.8992210,11.8288740],[45.8986410,11.8282780],[45.8980840,11.8276350],[45.8973970,11.8273130],[45.8968120,11.8266210],[45.8963080,11.8259820],[45.8957500,11.8253760],[45.8951600,11.8248020],[45.8945380,11.8242600],[45.8939150,11.8235950],[45.8934380,11.8228340],[45.8927940,11.8222650],[45.8920650,11.8220290]],
					graphDist   : [0.00,114.39,241.98,351.98,456.54,573.47,656.88,740.96,826.51,909.10,990.53,1081.21,1165.03,1251.91,1335.60,1425.61,1512.53,1590.90,1676.86,1768.53,1849.74,1932.60,2012.77,2096.13,2178.78,2267.63,2355.31,2436.20,2520.66,2615.66,2696.15,2784.45,2885.59,2968.36,3056.02,3137.74,3231.20,3311.10,3402.08,3485.44,3567.82,3648.31,3736.08,3845.84,3928.59,4020.32,4099.80,4180.20,4257.82,4344.33,4430.87,4527.11,4612.69,4705.50,4789.20,4870.92,4955.57,5037.98,5118.96,5205.12,5291.41,5375.56,5458.39,5552.52,5634.46,5719.58,5806.98,5891.13,5974.63,6057.21,6137.47,6238.15,6323.87,6405.76,6493.57,6577.61,6660.48,6741.71,6819.91,6902.00,6987.87,7089.70,7176.67,7263.73,7355.27,7441.32,7525.64,7614.15,7704.24,7793.45,7881.17,7969.19,8050.23,8138.81,8216.76,8295.95,8375.58,8457.29,8552.55,8641.15,8724.90,8813.44,8895.38,8976.60,9055.39,9138.48,9222.30,9306.34,9390.85,9472.51,9552.08,9631.45,9715.25,9796.00,9884.77,9969.06,10056.57,10137.49,10222.28,10302.07,10382.90,10464.90,10548.74,10642.83,10729.44,10816.90,10899.52,10983.64,11069.20,11148.00,11225.38,11309.57,11393.72,11483.02,11565.08,11648.67,11730.99,11816.52,11904.80,11994.75,12080.09,12162.25,12249.72,12337.95,12424.73,12507.38,12592.42,12671.31,12755.49,12837.69,12919.78,13000.99,13082.29,13164.50,13247.47,13332.10,13412.42,13497.51,13583.13,13666.67,13753.02,13836.60,13924.17,14006.75,14101.57,14184.90,14269.04,14360.06,14440.27,14550.57,14645.57,14730.21,14818.64,14906.75,14986.77,15087.17,15177.52,15259.67,15340.42,15421.77,15503.89,15591.26,15673.16,15754.93,15838.77,15921.13,16008.36,16089.17,16175.50,16259.50],
					graphEle    : [1305.00,1306.00,1307.00,1311.00,1362.00,1358.00,1360.00,1368.00,1380.00,1390.00,1392.00,1389.00,1395.00,1399.00,1412.00,1417.00,1423.00,1425.00,1421.00,1428.00,1438.00,1446.00,1456.00,1463.00,1475.00,1494.00,1504.00,1516.00,1523.00,1531.00,1545.00,1561.00,1571.00,1591.00,1593.00,1600.00,1611.00,1606.00,1596.00,1605.00,1607.00,1610.00,1632.00,1646.00,1658.00,1668.00,1688.00,1697.00,1701.00,1716.00,1737.00,1750.00,1762.00,1774.00,1786.00,1787.00,1783.00,1785.00,1780.00,1781.00,1771.00,1770.00,1766.00,1749.00,1745.00,1732.00,1721.00,1714.00,1710.00,1703.00,1689.00,1685.00,1662.00,1638.00,1622.00,1616.00,1605.00,1596.00,1594.00,1589.00,1580.00,1592.00,1589.00,1592.00,1599.00,1581.00,1576.00,1576.00,1567.00,1561.00,1556.00,1558.00,1570.00,1573.00,1572.00,1575.00,1578.00,1577.00,1592.00,1590.00,1598.00,1618.00,1631.00,1642.00,1649.00,1642.00,1642.00,1648.00,1657.00,1662.00,1661.00,1658.00,1677.00,1685.00,1704.00,1712.00,1722.00,1712.00,1702.00,1693.00,1695.00,1702.00,1702.00,1723.00,1717.00,1715.00,1714.00,1716.00,1721.00,1716.00,1716.00,1725.00,1712.00,1690.00,1672.00,1654.00,1633.00,1607.00,1586.00,1578.00,1570.00,1561.00,1555.00,1544.00,1538.00,1529.00,1523.00,1520.00,1520.00,1517.00,1514.00,1514.00,1513.00,1511.00,1515.00,1511.00,1504.00,1490.00,1481.00,1469.00,1456.00,1442.00,1431.00,1419.00,1404.00,1399.00,1392.00,1382.00,1368.00,1355.00,1339.00,1325.00,1322.00,1319.00,1317.00,1325.00,1323.00,1321.00,1323.00,1318.00,1327.00,1333.00,1321.00,1323.00,1322.00,1329.00,1334.00,1338.00,1339.00,1344.00],
					graphSpeed  : [],
					graphHr     : [],
					graphCad    : [],
					graphGrade  : [],
					waypoints   : [],
					unit        : "0",
					unitspeed   : "0",
					color1      : ["#3366cc","#ff0667","#ff0667"],
					color2      : "#3366cc",
					color3      : "#ff0000",
					color4      : "#000000",
					color5      : "#000000",
					color6      : "#ccff14",
					chartFrom1  : "",
					chartTo1    : "",
					chartFrom2  : "",
					chartTo2    : "",
					startIcon   : "",
					endIcon     : "",
					currentIcon : "",
					waypointIcon : "http://www.geocodezip.com/mapIcons/marker_yellow.png",
					zoomOnScrollWheel : "", 
					pluginUrl : "http://www.pedemontanadelgrappa.it/wp-content/plugins",
					langs : { altitude              : "Altitudine",
							    currentPosition       : "Posizione Corrente",
							    speed                 : "Velocità", 
							    grade                 : "Grade", 
							    heartRate             : "Battito Cardiaco", 
							    cadence               : "Cadenza",
							    goFullScreen          : "Schermo intero",
							    exitFullFcreen        : "Torna a dimensioni originali",
							    hideImages            : "Nascondi immagini",
							    showImages            : "Mostra immagini",
							    backToCenter		    : "Ritorna al centro della mappa"
							}
				    });
	});
</script>
TODO END TEST highcharts
    -->	
<?php
		}
	} else $this->speed = false;
}
?>
<?php
echo $this->parseTemplate("headline",$this->track->title,"jtg_param_header_map");
?>

    <style type="text/css">
        #jtg_map.olMap {
		height: <?php echo $this->cfg->map_height; ?>px;
		width: <?php echo $this->cfg->map_width; ?>px;
		z-index: 0;
}
        #jtg_map.fullscreen {
          	height: 800px;
		width: 100%;
		z-index: 20;
}

    </style>    
<center>
<div id="jtg_map" class="olMap"></div>
<br />
</center>
<!--    <div>
<?php
/*
// ToDo: inspect GET['subid'] to give the ability single tracks are shown
if ( $this->clicklist !== false ) {
	$return = ("<div><ul>\n");
	foreach ($this->clicklist AS $value => $key ) {
		$return .= ("<li><a href=\"" . $key['link'] . "\">".JText::_('COM_JTG_TRACK').$value . ": " . $key['name'] . "</a></li>\n");
	}
	$return .= ("</ul></div>\n");
	echo $return;
}
*/
?>
    </div>
-->
<div id="profile" style="width:<?php echo ((int)$this->cfg->charts_width+10); ?>px" >
<?php
//TODO TEST highchart
//if ( ($heightchart) AND ($nohight == false) ) {
//		
//<div class="profile-img" id="elevation2" style="width://620px; height: 180px;"></div>
//<div id='rect_click' style="position:absolute; height:145px; width:100%; left:0px; 
//     top:0px; z-index:8;background-color: #D1D6BE;opacity:0;
//     -ms-filter: 'progid:DXImageTransform.Microsoft.Alpha(Opacity=0)';" onmousemove = "hover_profil_graph(event);" onmouseout = "out_profil_graph();"></div>
//<script type="text/javascript">
//		$(function(){
//			$(window).load( loadOL() );
//		});
//    </script>
// <div id="legend1" style="width: 200px; height: 50px;">Elevation2 </div>
//   
//}
//TODO fin TEST highchart

if ( ($heightchart) AND ($nohight == false) ) {
		?>
<!-- TODO test highchart 
<div id="wpgpxmaps_142_3954690" class="wpgpxmaps">
	<div id="map_142_3954690" style="width:650px; height:500px"></div>
	<div id="hchart_142_3954690" class="plot" style="width:650px; height:200px"></div>

</div>
-->
<div class="profile-img" id="elevation" style="width:<?php echo $this->cfg->charts_width; ?>px; height: <?php echo $this->cfg->charts_height; ?>px;"></div>
<div id="legend1" style="width: 200px; height: 50px;"></div>
<?php
}
if ( ($speedchart) AND ($heightchart) ) {
?><br />
<br /><?php
}
if ( ($speedchart) AND ( $this->speed !== false ) ) {
?>
<div class="profile-img" id="speedchart" style="width:<?php echo $this->cfg->charts_width; ?>px; height: <?php echo $this->cfg->charts_height; ?>px;"></div>
<div id="legend2" style="width: 200px; height: 50px;"></div>
<?php
}
?>
<div class="gps-info">
<table border="0" width="98%" align="center">
	<thead>
		<tr>
			<th colspan="3"><?php echo JText::_('COM_JTG_DETAILS'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php echo JText::_('COM_JTG_FROM'); ?>:</td>
			<td><?php echo $this->profile; ?></td>
			<td rowspan="6" valign="top"><?php
			if ( ($durationbox) AND ($this->track->distance != "") AND ((float)$this->track->distance != 0)
			)
			{
				?>
			<div id="count-box">
			<div align="center"><span><?php echo JText::_('COM_JTG_TIMECOUNT'); ?></span>
			</div>
			<div align="center"><span> <label for="speed"> <?php echo JText::_('COM_JTG_AVG_SPEED'); ?>
			</label> <input type="text" name="speed" id="speed" value="" size="4" />
			</span> <input type="button" name="button" class="button"
				value="Submit"
				onclick="getAvgTime(document.getElementById('speed').value,<?php echo $this->distance_float; ?>);" />
			</div>
			<div align="center"><span> <label for="time"> <?php echo JText::_('COM_JTG_ESTIMATED_TIME'); ?>
			</label> <input type="text" name="time" id="time" value="" size="9"
				readonly="readonly" /> </span></div>
			</div>
			<?php } ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_DATE'); ?>:</td>
			<td><?php echo $this->date; ?></td>
		</tr><?php if ( $this->track->level != "0" )
		{ ?>
		<tr>
			<td><?php echo JText::_('COM_JTG_LEVEL'); ?>:</td>
			<td><?php echo $this->level; ?></td>
		</tr><?php } ?>
		<?php if ( ($this->track->distance != "") AND ((float)$this->track->distance != 0) )

		{ ?>
		<tr>
			<td><?php echo JText::_('COM_JTG_DISTANCE'); ?>:</td>
			<td><?php echo $this->distance; ?></td>
		</tr>
		<?php }

		if ($this->track->ele_asc)
		{ ?>
		<tr>
			<td><?php echo JText::_('COM_JTG_ELEVATION_UP'); ?>:</td>
			<td><?php echo $this->track->ele_asc; ?> <?php echo JText::_('COM_JTG_METERS'); ?></td>
		</tr>
		<?php
		}
		if ($this->track->ele_desc)
		{ ?>
		<tr>
			<td><?php echo JText::_('COM_JTG_ELEVATION_DOWN'); ?>:</td>
			<td><?php echo $this->track->ele_desc; ?> <?php echo JText::_('COM_JTG_METERS'); ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td><?php echo JText::_('COM_JTG_HITS'); ?>:</td>
			<td><?php echo $this->track->hits; ?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_CATS'); ?>:</td>
			<td colspan="2"><?php
			echo JtgHelper::parseMoreCats($this->sortedcats,$this->track->catid,"TrackDetails",true);
			?></td>
		</tr>
		<tr>
			<td><?php echo JText::_('COM_JTG_BIG_MAP') ?>:</td>
			<td colspan="2">
				<a rel="width[1000];height[700];" class="jcebox" href="http://maps.google.com/maps?q=<?php echo $this->track->start_n  . "," . $this->track->start_e; ?>" target="_blank">Google</a>,
				<a rel="width[1000];height[700];" class="jcebox" href="http://openstreetmap.org/?mlat=<?php echo $this->track->start_n . "&amp;mlon=" . $this->track->start_e; ?>" target="_blank">OpenStreetMap</a>,
				<a rel="width[1000];height[700];" class="jcebox" href="http://www.geocaching.com/map/default.aspx?lat=<?php echo $this->track->start_n . "&amp;lng=" . $this->track->start_e; ?>" target="_blank">Geocaching.com</a>
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
				echo $this->parseTemplate("headline",JText::_('COM_JTG_VOTING'),"jtg_param_header_rating");
				$template = "<div id=\"ratingbox\">
	<ul id=\"1001\" class=\"rating " . $this->vote['class'] . "\">\n";
				for ($i = 1; $i <= 10; $i++) {
					$link = "index.php?option=com_jtg&controller=files&id=" . $this->track->id . "&task=vote&rate=" . $i . "#jtg_param_header_rating";
					$link = JRoute::_($link,false);
					$template .= "		<li id=\"" . $i . "\" class=\"rate " . $this->stars->$i . "\">\n"
						. "			<a href=\"" . $link . "\" title=\"" . JText::_('COM_JTG_STARS_'.$i) . "\">" . $i . "</a>\n"
						. "		</li>\n";
//if ( $i == 1 )
				}
				$template .= "	</ul>\n";
				if ( $this->vote['count'] == 0 ) {
					$template .= JText::_('COM_JTG_NOT_VOTED') . "\n";
				} else {
					$template .= JText::_('COM_JTG_ARITHMETICAL_MIDDLE') . ": ".
					JtgHelper::getLocatedFloat($this->vote['rate'])
					 . " ".JText::_('COM_JTG_FROM') . " ".($i-1) . "<br />\n";
					$template .= $this->vote['count'] . "&nbsp;".JText::_('COM_JTG_RATINGS') . "\n";
				}
				echo $this->parseTemplate("description",$template);
				?>
<div class="no-float"></div>
</div>
				<?php
			} else echo "<a name=\"jtg_param_header_rating\"></a>";

			if ($this->cfg->download >= 1)
			{
				echo $this->parseTemplate("headline",JText::_('COM_JTG_DOWNLOAD'),"jtg_param_header_download");
				echo $this->parseTemplate("description",null,null,"open");
				if (($this->cfg->download == 2) AND ($this->user->get('id') == 0))
				// registered users only
				echo $this->parseTemplate("description",JText::_('COM_JTG_NOT_DOWNLOAD'));
				else {
					$download_option = "";
					if ( (bool)$this->params->get("jtg_param_offer_download_gpx") )
					$download_option .= "	<option value=\"gpx\">GPX</option>\n";
					if ( (bool)$this->params->get("jtg_param_offer_download_kml") )
					$download_option .= "	<option value=\"kml\">KML</option>\n";
					if ( (bool)$this->params->get("jtg_param_offer_download_tcx") )
					$download_option .= "	<option value=\"tcx\">Garmin (tcx)</option>\n";
					echo $this->jscript;
					?>
<form name="adminForm" id="adminForm" method="post" action="<?php echo $this->action; ?>">
<div><b><?php echo JText::_('COM_JTG_DOWNLOAD_FILE'); ?></b></div>
<span><label for="format"><?php echo JText::_('COM_JTG_FORMAT'); ?>&nbsp;</label>
<select name="format" id="format">
	<option value=""><?php echo JText::_('COM_JTG_SELECT'); ?></option>
<?php echo $download_option; ?>
</select>
<button class="button" type="button" onclick="submitbutton('download')">
					<?php echo JText::_('COM_JTG_DOWNLOAD') ?></button>
</span> <?php
echo JHTML::_( 'form.token' ) . "\n"; ?> <input type="hidden"
	name="option" value="com_jtg" /> <input type="hidden" name="id"
	value="<?php echo $this->track->id; ?>" /> <input type="hidden"
	name="task" value="" /></form>
<?php echo $this->parseTemplate("description",null,null,"close"); ?>
<div class="no-float"></div>
<!--</div>-->
<?php
				}
			} else echo "<a name=\"jtg_param_header_download\"></a>";

			if ($this->track->terrain)
			{
				$terrain = $this->track->terrain;
				$terrain = explode(",",$terrain);
				$newterrain = array();
				foreach ($terrain as $t) {
					$t = $this->model->getTerrain(" WHERE id=" . $t);
					if ( ( isset($t[0])) AND ( $t[0]->published == 1 ) ) {
						$newterrain[] = $t[0]->title;
					}
				}
				$terrain = implode(", ",$newterrain);
				echo $this->parseTemplate("headline",JText::_('COM_JTG_TERRAIN'),"jtg_param_header_terrain");
				echo $this->parseTemplate("description",$terrain);
			} else echo "<a name=\"jtg_param_header_terrain\"></a>";

			if ($this->track->description)
			{
				echo $this->parseTemplate("headline",JText::_('COM_JTG_DESCRIPTION'),"jtg_param_header_description");
				echo $this->parseTemplate("description",$this->track->description);
			} else echo "<a name=\"jtg_param_header_description\"></a>";

			if (($this->images) AND ( $this->cfg->gallery != "none" ))
			{
				echo $this->parseTemplate("headline",JText::_('COM_JTG_GALLERY'),"jtg_param_header_gallery");
				echo $this->parseTemplate("description",$this->imageBlock);
			} else echo "<a name=\"jtg_param_header_gallery\"></a>";

			if ( $this->cfg->approach != 'no' ) {
				echo $this->parseTemplate("headline",JText::_('COM_JTG_APPROACH'),"jtg_param_header_approach");
				$description = "	<table id=\"approach\">
		<tr valign=\"top\">";

				switch($this->cfg->approach) {
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
				//	$description .= "<a rel=\"width[1000];height[700];\" class=\"jcebox\" href=\"http://maps.google.com/maps?daddr=".JText::_($this->track->start_n) . ",".JText::_($this->track->start_e) . "\" target=\"_blank\" >Google</a>";
				echo $this->parseTemplate("description",$description);
			} else echo "<a name=\"jtg_param_header_approach\"></a>";
			// Approach END

			// adding the comments
			if($this->cfg->comments == 1) {
				echo $this->parseTemplate("headline",JText::_('COM_JTG_COMMENTS'),"jtg_param_header_comment");
				if(!$this->comments)  {
					echo "<div>".JText::_('COM_JTG_NO_COMMENTS') . "</div>";
				} else {
					for($i=0, $n=count($this->comments); $i<$n; $i++) {
						$comment = $this->comments[$i];
						?>
<div class='comment'>
<div class="comment-header">
<div class="comment-title"><?php echo ($i+1) . ": " . $comment->title; ?></div>
<div class="date"><?php echo JHTML::_('date', $comment->date, JText::_('COM_JTG_DATE_FORMAT_LC4'));; ?>
</div>
<div class="no-float"></div>
</div>
<div class="comment-autor"><?php echo $comment->user; ?><br />
						<?php
						echo $this->model->parseEMailIcon($comment->email);
						if ($comment->homepage)
						echo " " . $this->model->parseHomepageIcon($comment->homepage);
						?></div>
<div class="comment-text"><?php echo $comment->text; ?></div>
<div class="no-float"></div>
</div>
						<?php
					}
				}
				//
				if($this->cfg->comment_who == 0) {
					echo $this->model->addcomment($this->cfg);
				} elseif ($this->cfg->comment_who == 1 && $this->user->get('id')) {
					echo $this->model->addcomment($this->cfg);
				} else {
					echo JText::_('COM_JTG_ADD_COMMENT_NOT_AUTH');
				}
			} elseif ($this->cfg->comments == 2) {

				// 		Changes from jason-oxley https://sourceforge.net/projects/jtg/forums/forum/1042962/topic/3856273
				require_once( JPATH_PLUGINS . DS . 'content' . DS . 'jom_comment_bot.php' );
				echo jomcomment($this->track->id, "com_jtg");
			} elseif ($this->cfg->comments == 3) {
				//		global $mosConfig_absolute_path;
				require_once( 'components' . DS . 'com_jcomments' . DS . 'jcomments.php' );
				echo JComments::showComments($this->track->id, "com_jtg");
			} else echo "<a name=\"jtg_param_header_comment\"></a>";
			?>
<div style="display: none"><!-- load necessary pics in background --> <img
	src="http://www.openlayers.org/api/img/cloud-popup-relative.png"
	alt="display:none" /> <img
	src="http://www.openlayers.org/api/img/marker.png" alt="display:none" />
<img src="http://www.openlayers.org/api/theme/default/img/close.gif"
	alt="display:none" /></div>
			<?php
	} elseif ($maySeeSingleFile === false) {
		echo '<p class="error">'.JText::_('COM_JTG_NOT_AUTH').'</p>';
	} else echo '<p class="error">'.$maySeeSingleFile.'</p>';
	echo $this->footer;
	if ( isset($this->cfg) AND ( $this->cfg->map == "osm" ) )
	{
	    echo ("<script language=\"javascript\" type=\"text/javascript\">\n
		var olmap={ title: 'com_jtg_map_object' } \n 
		slippymap_init();</script>");
	}
