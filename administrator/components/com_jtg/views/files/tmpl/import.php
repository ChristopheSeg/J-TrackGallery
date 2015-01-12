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
JToolBarHelper::title(JText::_('COM_JTG_ADD_FILES'), 'categories.png');
JToolBarHelper::back();
JToolBarHelper::spacer();
$bar= JToolBar::getInstance( 'toolbar' );
$folder = JUri::base().'index.php?option=com_jtg&tmpl=component&controller=files&task=upload';
jimport('joomla.filesystem.folder');
// popup:
JToolBarHelper::addNew('newfiles', JText::_('COM_JTG_RELOAD'));
//JToolBarHelper::cancel('jtg');
JToolBarHelper::save('savefiles', JText::_('COM_JTG_SAVE_NEW_FILE'), 'save.png' );
JToolBarHelper::deleteList('COM_JTG_VALIDATE_DELETE_ITEMS', 'removeFromImport');
JToolBarHelper::help( 'files/import',true );
$document = JFactory::getDocument();
$style = "   .row00 {background-color: #FFFF99;}\n";
if(JVERSION>=3.0) //Code support for joomla version greater than 3.0
{
	$style.= "	select, textarea, input{
	width: auto !important;\n}";
}	
$document->addStyleDeclaration( $style );

?>
<form action="" method="post" name="adminForm" id="adminForm" class="adminForm" enctype="multipart/form-data">
<?php
$yesnolist = array(
	array('id' => 0, 'title' => JText::_('JNO')),
	array('id' => 1, 'title' => JText::_('JYES'))
);
$tracks = $this->rows;
$trackfilename = array();
// Vorhandene Dateinamen in array speichern
for($i=0;$i<count($tracks);$i++)
{
	$trackfilename[$i] = $tracks[$i]->file;
}
$level = array("access" => 0);
$level = JArrayHelper::toObject($level);
$row=0;
$count = 0;
$errorposted = false;
$importdir = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'uploaded_tracks' . DIRECTORY_SEPARATOR . "import";
$filesdir = JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'uploaded_tracks'. DIRECTORY_SEPARATOR;
$filesdir = JFolder::files($filesdir);
//	*.gpx, *.trk, *.kml (not case sensitive)
$regex="(.[gG][pP][xX]$|.[tT][rR][kK]$|.[kK][mM][lL]$)";
$me = JFactory::getUser();
//$access = new StdClass();
//$access->access=0;
$files = JFolder::files($importdir,$regex,true,true);
$model = $this->getModel();
$terrain = $model->getTerrain("*",true," WHERE published=1 ");
$terrainsize = count($terrain);
if ( $terrainsize > 6 )
{
	$terrainsize = 6;
}
$cats = $model->getCats(0,'COM_JTG_SELECT',0,0);
$catssize = count($cats);
if ( $catssize > 6 )
{
	$catssize = 6;
}
$userslist = $model->getUsers(false, $where = "WHERE block = 0" );
$userslistsize = count($userslist);
if ( $userslistsize > 6 )
{
	$userslistsize = 6;
}
$cfg = JtgHelper::getConfig();
$levels = explode("\n",$cfg->level);
$toggle['level'] = "<select name=\"level_all\" size=\"6\" onclick=\"setSelect('level');\">
						<option value=\"0\">".JText::_('COM_JTG_SELECT') . "</option>";
$i = 1;
foreach($levels AS $level){
	if ( trim($level) != "" )
	{
		$toggle['level'] .= "						<option value=\"$i\">$i - " . JText::_(trim($level)) . "</option>";
		$i++;
	}
}
$toggle['level'] .= "					</select>\n";

$table = ("		<tbody>\n
			<tr class=\"row00\">
				<td ><input type=\"checkbox\" onclick=\"Joomla.checkAll(this)\" title=\"" . JText::_( 'JGLOBAL_CHECK_ALL' ) . "\" value=\"\" checked=\"checked\" name=\"checkall-toggle\"></td>
				<td colspan=\"2\" align=\"right\">	<b>" . JText::_('COM_JTG_PRESELECTION') . "==></b><br><br>"
				. JText::_('COM_JTG_PRESELECTION_DESCRIPTION') . "</td>
				<td>" . $toggle['level'] . "</td>
				<td>" . JHtml::_('select.genericlist', $cats, 'catid_all[]', 'size="'.$catssize.'" multiple="multiple" onclick="setSelectMultiple(\'catid\')"', 'id', 'treename')
					. "<br /><small>".JText::_('COM_JTG_MULTIPLE_CHOICE_POSSIBLE') . "</small>". "</td>
				<td>" . JHtml::_('select.genericlist', $terrain, 'terrain_all[]', 'size="'.$terrainsize.'"  multiple="multiple" onclick="setSelectMultiple(\'terrain\')"', 'id', 'title')
					. "<br /><small>".JText::_('COM_JTG_MULTIPLE_CHOICE_POSSIBLE') . "</small>". "</td>
				<td>" . JHtml::_('select.genericlist', $userslist, 'uid_all', 'class="inputbox" size="'.$userslistsize.'" onclick="setSelect(\'uid\')"', 'id','title', $me->id) . "</td>
				<td>" . $this->accesslevelForImport("access_all","onclick=\"setSelect('access')\"",false) . "</td>
				<td>" . JHtml::_('select.genericlist', $yesnolist, 'hidden_all', 'class="inputbox" size="1" onclick="setSelect(\'hidden\')"', 'id', 'title') . "</td>
				</tr>
");
if ( $files !== false )
foreach($files AS $file) {
		$row = (1 - $row);
	// Formatierungen

//	$lists['cats'] = JHtml::_('select.genericlist', $model->getCats(), 'catid_'.$count, 'size="'.$size.'"', 'id', 'title');
	$lists['cats'] = JHtml::_('select.genericlist',
		$cats,
		'catid_'.$count.'[]',
		'multiple="multiple" size="'.$catssize.'"',
		'id', 'treename' );
	$editor = JFactory::getEditor();
	$buttons = array(
	"pagebreak",
	"readmore");
	$lists['description'] = $editor->display( 'desc_'.$count, '', '100%', '200', '20', '20', $buttons, NULL, NULL );
	$lists['access'] = $this->accesslevelForImport("access_" . $count);
	$lists['uid'] = JHtml::_('select.genericlist', $userslist, 'uid_'.$count, ' size="'.$userslistsize.'"','id', 'title', $me->id);
	// $me->id, 1, 'onclick="setSelect(\'uid\')"', 'name', 0 );
	// 				genericlist($arr, $name, $attribs=null, $key= 'value', $text= 'text', $selected=NULL, $idtag=false, $translate=false)
	$lists['hidden'] = JHtml::_('select.genericlist', $yesnolist, 'hidden_'.$count, 'class="inputbox" size="2"', 'id', 'title',0);
	$lists['terrain'] = JHtml::_('select.genericlist',
		$terrain,
		'terrain_'.$count.'[]',
		'multiple="multiple" size="'.$terrainsize.'"',
		'id', 'title');

	jimport('joomla.filesystem.file');
	$extension = JFile::getExt($file);
	$file_tmp = explode('.',$file);
	unset($file_tmp[(count($file_tmp)-1)]);
	$filename = implode('.',$file_tmp);
	// TODO Verify these lines !!
	$filename = $filename . "." . $extension;
	$filename = str_replace($importdir. DIRECTORY_SEPARATOR,'',$filename);
	$filename_wof = explode(DIRECTORY_SEPARATOR,$filename);
	$filename_wof = $filename_wof[(count($filename_wof)-1)];

	// $filename = strtolower(JFile::getName($file));


	if (in_array(strtolower($filename_wof),$filesdir) ) {
		$check = 1; //track already existing, not reloaded 
		// TODO if file is reloaded after upgrade (new gps data, but same filename), 
		// should delete cache, then load data with gpsDataClass  
		$filename_exists = "<input type=\"hidden\" name=\"filenameexists_" . $count . "\" value=\"true\">\n";
	}
	else
	{
		$gpsData = new gpsDataClass("Kilometer"); // default unit
		$cache = JFactory::getCache('com_jtg');
		// New gps Data are cached
		// TODOTODO
		$gpsData = $cache->get(
				array($gpsData, 'loadFileAndData'), 
				array($file, strtolower($filename_wof)), // TODO strtolower or not??
				"Kilometer");
		$check = 3;// $gpsData->checkFile;
		$filename_exists = "<input type=\"hidden\" name=\"filenameexists_" . $count . "\" value=\"false\">\n";
	}
	 
	$date = $gpsData->Date;
	$title = $gpsData->Title;
	//if ( ( $errorposted == false ) AND ( $check !== true ) )
	{
		if ( ( $check != 8 ) AND ( $errorposted == false ) )
		{
			$errorposted = true;
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_ERROR_FOUND'),'Notice' );
		}
		/*
		 1 = JText::_('COM_JTG_TT_ERR_FILEEXIST');		green
		2 = JText::_('COM_JTG_TT_ERR_NODELETE');		red
		3 = JText::_('COM_JTG_TT_ERR_MUCHLEN');		brown
		4 = JText::_('COM_JTG_TT_ERR_BADFILENAME') . " (&)";	red
		5 = JText::_('COM_JTG_TT_ERR_BADFILENAME') . " (#)";	red
		6 = JText::_('COM_JTG_TT_ERR_NOTRACK');		grey
		7 = JText::_('COM_JTG_TT_ERR_NOPOINTINTRACK');	grey
		8 = JText::_('COM_JTG_TT_ERR_MORETRACKS');		blue
		*/
		// if ( ( $check === true ) OR ( $check == 8 ) )
		
		if ( $check !== true )
		{
			if ( $check == 1 ) {
				$tt = JText::_('COM_JTG_TT_ERR_FILEEXIST');
				$color = "green";
			} elseif ( $check == 2 ) {
				$tt = JText::_('COM_JTG_TT_ERR_NODELETE');
				$color = "red";
			} elseif ( $check == 3 ) {
				$tt = JText::_('COM_JTG_TT_ERR_MUCHLEN');
				$color = "brown";
			} elseif ( $check == 4 ) {
				$tt = JText::_('COM_JTG_TT_ERR_BADFILENAME') . " (&)";
				$color = "red";
			} elseif ( $check == 5 ) {
				$tt = JText::_('COM_JTG_TT_ERR_BADFILENAME') . " (#)";
				$color = "red";
			} elseif ( $check == 6 ) {
				$tt = JText::_('COM_JTG_TT_ERR_NOTRACK');
				$color = "lightgrey";
			} elseif ( $check == 7 ) {
				$tt = JText::_('COM_JTG_TT_ERR_NOPOINTINTRACK');
				$color = "lightgrey";
			} elseif ( $check == 8 ) {
				$tt = JText::_('COM_JTG_TT_ERR_MORETRACKS');
				$color = "blue";
			}
			$table .= "			<tr class=\"row$row " . ($row? "row-odd":"row-even"). "\">\n<td colspan=\"9\">" . JText::_('COM_JTG_GPS_FILE') . ': ' .  $filename . ": <b><font color=\"red\">" . $tt . "</font></b><br></tr>\n";
		}
		else
		{
			$table .= "			<tr class=\"row$row " . ($row? "row-odd":"row-even"). "\">\n<td colspan=\"9\">" . JText::_('COM_JTG_GPS_FILE') . ': ' .  $filename . ': '. JText::_('COM_JTG_TT_FILEOKAY') . "</tr>\n";
		}
		
		
		$table .= ("		<tr class=\"row$row " . ($row? "row-odd":"row-even"). "\">\n");

		// Row: Selector + Date
		{
			$table .= '<td nowrap>'.$filename_exists;

			if (
				( $check === true ) OR
				// eine Spur mit Punkten an erster Stelle vorhanden
				( $check == 1 ) OR
				// Dateiname existiert
				//			( $check == 2 ) OR
				// Kein Löschrecht
				( $check == 3 ) OR
				// Dateinamenslänge überschritten
				//			( $check == 4 ) OR
				// Wenn "&" im Dateinamen
				( $check == 5 ) OR
				// Wenn "#" im Dateinamen
				( $check == 6 ) OR
				// Keine Spur vorhanden
				( $check == 7 ) OR
				// Spur vorhanden, aber kein Punkt
				( $check == 8 )
				// Spur vorhanden, aber nicht an erster Stelle. Evtl. mehrere Spuren
			)
			{
				$table .= ("<input type=\"checkbox\" checked=\"checked\" id=\"cb" . $count . "\" value=\"" . $file . "\" name=\"import_" . $count . "\" onclick=\"Joomla.isChecked(this.checked);\" /></td>\n");
			}
			$table .= ("				<td><input id=\"date_" . $count . "\" type=\"text\" name=\"date_" . $count . "\" size=\"10\" value=\"");
			if ($date === false)
			$table .= (date('Y-m-d',time()) . "\" /><font color=\"orange\">&nbsp;</font></td>");
			else
			$table .= ($date . "\" /></td>");
		}

		//Row: GPS filename / Title
		{
			$table .= ("				<td>");
			$table .= ("<input type=\"hidden\" name=\"file_" . $count . "\" value=\"" . $file . "\" />\n");
			$table .= ("\n				<input id=\"title\" type=\"text\" name=\"title_" . $count . "\" value=\"" . $title . "\" size=\"30\" /></td>\n");
		}

		//Row: Schwierigkeitsgrad
		{
			$table .= "				<td>\n";
			$table .=  "					<select id=\"level_" . $count . "\" name=\"level_" . $count . "\" size=\"6\"\n>
						<option>".JText::_('COM_JTG_SELECT') . "</option>\n";
			$i = 1;
			foreach($levels AS $level){
				if ( trim($level) != "" )
				{
					$table .=  "<option value=\"$i\">$i - " . JText::_(trim($level)) . "</option>\n";
					$i++;
				}
			}
			$table .="					</select>
				</td>\n";
		}

		//Row: Kategorien
		{
			$table .= ("				<td>" . $lists['cats'] . "</td>\n");
		}

		//Row: Terrain
		{
			$table .= ("				<td>" . $lists['terrain'] . "</td>\n");
		}

		//Row: Autor
		{
			$table .= ("				<td>" . $lists['uid'] . "</td>\n");
		}

		//Row: Zugriffsebene
		{
			$table .= ("				<td>" . $lists['access'] . "</td>\n");
		}

		//Row: Hidden
		{
			$table .= ("				<td>" . $lists['hidden'] . "</td>\n");
		}

		//Row: NULL
		{
			$table .= ("				\n");
		}

		$table .= ("			</tr>\n<tr class=\"row$row " . ($row? "row-odd":"row-even"). "\">\n");
		//Row: Beschreibung
		{
			$table .= ("				<td>&nbsp;&nbsp;</td><td colspan='8'>".JText::_('COM_JTG_DESCRIPTION') . ":<br />\n" . $lists['description'] . "</td>\n");
		}
		$table .= ("				\n");
		$table .= ("			</tr>\n");

		$count++;
		if ( $count > 50 ) {
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_TOO_MUCH_TRACKS_TO_IMPORT'),'Warning' );
			break;
		}
	}
}
/*
 echo JText::_('COM_JTG_INFO_AUTHOR');
 echo JText::_('COM_JTG_LEVEL');
 echo JText::_('COM_JTG_SELECT');
 echo JText::_('COM_JTG_CAT');
 echo JText::_('COM_JTG_ACCESS_LEVEL');
 echo JText::_('COM_JTG_GPS_FILE');
 echo JText::_('COM_JTG_TERRAIN');
 echo JText::_('COM_JTG_DESCRIPTION');
 echo JText::_('COM_JTG_IMAGES');
 echo JText::_('COM_JTG_TERMS');*/

$table_header = ("	<table class=\"adminlist\" cellpadding=\"1\">
		<thead>
			<tr>
				<th class=\"title\" width=\"1\">&nbsp;</th>
				<th class=\"title\" width=\"1\">"
					.JText::_('COM_JTG_DATE') . "</th>
				<th class=\"title\" width=\"1\">".JText::_('COM_JTG_TITLE') . "</th>
				<th class=\"title\" width=\"1\">".JText::_('COM_JTG_LEVEL') . "</th>
				<th class=\"title\" width=\"1\">".JText::_('COM_JTG_CAT') . "</th>
				<th class=\"title\" width=\"1\">".JText::_('COM_JTG_TERRAIN') . "</th>
				<th class=\"title\" width=\"1\">".JText::_('COM_JTG_INFO_AUTHOR') . "</th>
				<th class=\"title\" width=\"1\">".JText::_('COM_JTG_ACCESS_LEVEL') . "</th>
				<th class=\"title\" width=\"1\">".JText::_('COM_JTG_HIDDEN') . "</th>
			</tr>
		</thead>\n");

$table_footer = ("		</tbody>\n	</table>\n");

if ( $count == 0 )
{
	$model = $this->getModel();
	$rows = $model->_fetchJPTfiles();
	if ( (JFolder::exists(JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_joomgpstracks')) AND (count($rows) != 0 ) ) {
		// DEPRECATED by default, import from joomgpstracks if no tracks uploaded in JTrackGallery folder
		// Datenimport von joomgpstracks BEGIN
		JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_FOUND_H'));
		echo (JText::_('COM_JTG_FOUND_T') . "<br /><br />");
		echo (JText::_('COM_JTG_FOUND_L'));
		//TODO folder/images don't exist !!!
		echo (" <a href=\"index.php?option=com_jtg&task=importjgt&controller=files\"><img src=\"templates" . DIRECTORY_SEPARATOR . "khepri" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "notice-download.png\" /></a>");
		// Datenimport von joomgpstracks END
	}
	else
	{
		// Nothing in import folder
		JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_IMPORTFOLDEREMPTY') . " (" . DIRECTORY_SEPARATOR
			. 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery' . DIRECTORY_SEPARATOR . 'uploaded_tracks'
			. DIRECTORY_SEPARATOR . "import)",'Warning' );
	}
}
else
{
echo $table_header.$table.$table_footer;
}
echo JHtml::_( 'form.token' );
$js = "

function setSelectMultiple(select) {
	var srcListName = select + '_all';
	var form = document['adminForm'];
	var srcList = form[srcListName];
  	var values =[];
	var i;
  	for (i=0; i<srcList.options.length; i++) {
      	values[i] = (srcList.options[i].selected==true);
    }
	for (i=0; i < " . $count . "; i++) {
		setSelectedMultipleValues('adminForm', select + '_' + i , values);
	}
}
function setSelectedMultipleValues( frmName, srcListName, values ) {
	var form = eval( 'document.' + frmName );
	var srcList = eval( 'form.' + srcListName );
  	var i;
  	for (i=0; i<srcList.options.length; i++) {
		srcList.options[i].selected = values[i];
	}
}


function setSelect(select) {

	var value = getSelectedValue('adminForm', select + '_all');
	for (i=0; i < " . $count . "; i++) {
		setSelectedValue('adminForm', select + '_' + i , value);
	}
}

function getSelectedValue(frmName, srcListName) {
	var form = eval( 'document.' + frmName );
	var srcList = eval( 'form.' + srcListName );

	i = srcList.selectedIndex;
	if (i != null && i > -1) {
		return srcList.options[i].value;
	} else {
		return null;
	}
}


function setSelectedValue( frmName, srcListName, value ) {
	var form = eval( 'document.' + frmName );
	var srcList = eval( 'form.' + srcListName );

	var srcLen = srcList.length;

	for (var i=0; i < srcLen; i++) {
		srcList.options[i].selected = false;
		if (srcList.options[i].value == value) {
			srcList.options[i].selected = true;
		}
	}
}
";
$document = JFactory::getDocument();
$document->addScriptDeclaration($js);
echo ("	<input type=\"hidden\" name=\"option\" value=\"com_jtg\" />
	<input type=\"hidden\" name=\"controller\" value=\"files\" />
	<input type=\"hidden\" name=\"task\" value=\"\" />
	<input type=\"hidden\" name=\"found\" value=\"" . $count . "\" />
	<input type=\"hidden\" name=\"boxchecked\" value=\"0\" />\n");
//echo ("	<input type=\"hidden\" name=\"id\" value=\"" . $this->id . "\" />\n");
echo ("</form>\n");
