<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooosm and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

defined('_JEXEC') or die('Restricted access');

// toolbar
JToolBarHelper::title(JText::_('COM_JTG_ADD_FILES'), 'categories.png');
JToolBarHelper::back();
JToolBarHelper::spacer();
$bar=& JToolBar::getInstance( 'toolbar' );
$folder = JUri::base().'index.php?option=com_jtg&tmpl=component&controller=files&task=upload';
jimport('joomla.filesystem.folder');
// popup:
	$bar->appendButton( 'Popup', 'upload', 'Upload', $folder, 550, 400 );
JToolBarHelper::addNew('newfiles', JText::_('COM_JTG_RELOAD'));
// JToolBarHelper::media_manager('&folder=jtg/newfiles',"Upload");
// $directory = "jtg/newfiles";
// $alt = "Upload";
// 		$bar = & JToolBar::getInstance('toolbar');
// 		// Add an upload button
// 		$bar->appendButton( 'Popup', 'upload', $alt, "index.php?option=com_media&tmpl=component&task=popupUpload&folder=" . $directory, 800, 520 );

// JToolBarHelper::cancel('jtg');
JToolBarHelper::save('savefiles', JText::_('COM_JTG_SAVE_NEW_FILE'), 'save.png' );
JToolBarHelper::deleteList('COM_JTG_VALIDATE_DELETE_ITEMS', 'removeFromImport');
JToolBarHelper::help( 'files/import',true );
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
for($i=0;$i<count($tracks);$i++) {
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
$me =& JFactory::getUser();
//$access = new StdClass();
//$access->access=0;
$files = JFolder::files($importdir,$regex,true,true);
$model = $this->getModel();
$terrain = $model->getTerrain("*",true," WHERE published=1 ");
$cats = $model->getCats();
$toggle['level'] = ("<select name=\"level_all\" size=\"1\" onclick=\"setSelect('level');\">
						<option value=\"0\">".JText::_('COM_JTG_SELECT') . "</option>
						<option value=\"1\">1</option>
						<option value=\"2\">2</option>
						<option value=\"3\">3</option>
						<option value=\"4\">4</option>
						<option value=\"5\">5</option>
					</select>\n");
$table = ("		<tbody>\n
			<tr class=\"row" . $row . "\">
				<td colspan=\"4\" align=\"right\">".JText::_('COM_JTG_PRESELECTION') . ":</td>
				<td>" . $toggle['level'] . "</td>
				<td>".JHtml::_('select.genericlist', $cats, 'catid_all', 'size="1" onclick="setSelect(\'catid\')"', 'id', 'treename') . "</td>
				<td>".JHtml::_('select.genericlist', $terrain, 'terrain_all', 'size="1" onclick="setSelect(\'terrain\')"', 'id', 'title') . "</td>
				<td>".JHtml::_('list.users', 'uid_all', $me->id, 1, 'onclick="setSelect(\'uid\')"', 'name', 0 ) . "</td>
				<td>" . $this->accesslevelForImport("access_all","onclick=\"setSelect('access')\"",true) . "</td>
				<td>".JHtml::_('select.genericlist', $yesnolist, 'hidden_all', 'class="inputbox" size="1" onclick="setSelect(\'hidden\')"', 'id', 'title',0) . "</td>
				<td></td>
				</tr>
");
if ( $files !== false )
foreach($files AS $file) {
		$row = (1 - $row);
	// Formatierungen
	$size = count($cats);
	if ( $size > 6 )
	$size = 6;
//	$lists['cats'] = JHtml::_('select.genericlist', $model->getCats(), 'catid_'.$count, 'size="'.$size.'"', 'id', 'title');
	$lists['cats'] = JHtml::_('select.genericlist',
		$cats,
		'catid_'.$count.'[]',
		'size="'.$size.'" multiple="multiple"',
		'id', 'treename' );
	$editor = JFactory::getEditor();
	$buttons = array(
	"pagebreak",
	"readmore");
	$params = array(
		'smilies'=> '1',
		'style' => '1',
		'layer' => '0',
		'table' => '0',
		'clear_entities'=>'0');
	$lists['description'] = $editor->display( 'desc_'.$count, '', '100%', '200', '20', '20', $buttons, $params );
	$size = count($terrain);
	if ( $size > 6 )
	$size = 6;
	$lists['access'] = $this->accesslevelForImport("access_" . $count);
	$lists['uid'] = JHtml::_('list.users', 'uid_'.$count, $me->id, 1, NULL, 'name', 0 );
	$lists['hidden'] = JHtml::_('select.genericlist', $yesnolist, 'hidden_'.$count, 'class="inputbox" size="2"', 'id', 'title',0);
	$lists['terrain'] = JHtml::_('select.genericlist',
		$terrain,
		'terrain_'.$count.'[]',
		'multiple="multiple" size="'.$size.'"',
		'id', 'title'
		//		,$track->terrain
	) . "<br /><small>".JText::_('COM_JTG_MULTIPLE_CHOICE_POSSIBLE') . "</small>";

	jimport('joomla.filesystem.file');
	$extension = JFile::getExt($file);
	$file_tmp = explode('.',$file);
	unset($file_tmp[(count($file_tmp)-1)]);
	$filename = implode('.',$file_tmp);
	$filename = $filename . "." . $extension;
	$filename = str_replace($importdir. DIRECTORY_SEPARATOR,'',$filename);
	$filename_wof = explode(DIRECTORY_SEPARATOR,$filename);
	$filename_wof = $filename_wof[(count($filename_wof)-1)];

	// $filename = strtolower(JFile::getName($file));

	$date = $this->giveDate($file);
	$title = $this->giveTitle($file);
	if (in_array(strtolower($filename_wof),$filesdir) ) {
		$check = $this->checkFile($file,true);
		$filename_exists = "<input type=\"hidden\" name=\"filenameexists_" . $count . "\" value=\"true\">\n";
	} else {
		$check = $this->checkFile($file);
		$filename_exists = "<input type=\"hidden\" name=\"filenameexists_" . $count . "\" value=\"false\">\n";
	}

	//if ( ( $errorposted == false ) AND ( $check !== true ) )
	{
		if ( ( $check != 8 ) AND ( $errorposted == false ) ) {
			$errorposted = true;
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_ERROR_FOUND'),'Notice' );
		}

		$table .= ("			<tr><td colspan=\"11\"><hr></td></tr><tr class=\"row" . $row . "\">\n");
		// Spalte: Checkbox
		{
			$table .= ("				<td rowspan='2'>");
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
			$table .= ("<input type=\"checkbox\" checked=\"checked\" id=\"cb" . $count . "\" value=\"" . $file . "\" name=\"import_" . $count . "\" onclick=\"Joomla.isChecked(this.checked);\" />\n");
//			$table .= ("<input type=\"checkbox\" id=\"cb" . $count . "\" value=\"" . $file . "\" name=\"cid[]\" onclick=\"isChecked(this.checked);\" />\n");
			$table .= $filename_exists;
			$table .= ("				</td>\n");
		}

		// Spalte: Dateiname
		{
			$table .= ("				<td nowrap rowspan='2'>");
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
			$table .= ("<input type=\"hidden\" name=\"file_" . $count . "\" value=\"" . $file . "\" />\n");
			if ( $check !== true ) {
				$table .= ("<span class=\"hasTip\" title=\"" . $filename . "\">");
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
				$table .= ("<font color=\"" . $color . "\">" . $tt . "</font>: " . $extension . "</span>\n");
			} else $table .= ("<span class=\"hasTip\" title=\"" . $filename . "\"><font color=\"black\">".JText::_('COM_JTG_TT_FILEOKAY') . ":</font> " . $extension . "</span>\n");
			$table .= ("</td>\n");
		}

		// Spalte: Datum
		{
			$table .= ("				<td nowrap><input id=\"date_" . $count . "\" type=\"text\" name=\"date_" . $count . "\" size=\"10\" value=\"");
			if ($date === false)
			$table .= (date('Y-m-d',time()) . "\" /><font color=\"orange\">&nbsp;?</font></td>");
			else
			$table .= ($date . "\" /></td>");
		}

		// Spalte: Titel
		{
			$table .= ("\n				<td><input id=\"title\" type=\"text\" name=\"title_" . $count . "\" value=\"" . $title . "\" size=\"30\" /></td>\n");
		}

		// Spalte: Schwierigkeitsgrad
		{
			$table .= ("				<td>
					<select id=\"level_" . $count . "\" name=\"level_" . $count . "\" size=\"6\">
						<option>".JText::_('COM_JTG_SELECT') . "</option>
						<option value=\"1\">1</option>
						<option value=\"2\">2</option>
						<option value=\"3\">3</option>
						<option value=\"4\">4</option>
						<option value=\"5\">5</option>
					</select>
				</td>\n");
		}

		// Spalte: Kategorien
		{
			$table .= ("				<td>" . $lists['cats'] . "</td>\n");
		}

		// Spalte: Terrain
		{
			$table .= ("				<td>" . $lists['terrain'] . "</td>\n");
		}

		// Spalte: Autor
		{
			$table .= ("				<td>" . $lists['uid'] . "</td>\n");
		}

		// Spalte: Zugriffsebene
		{
			$table .= ("				<td>" . $lists['access'] . "</td>\n");
		}

		// Spalte: Hidden
		{
			$table .= ("				<td>" . $lists['hidden'] . "</td>\n");
		}

		// Spalte: NULL
		{
			$table .= ("				<td></td>\n");
		}

		$table .= ("			</tr>\n<tr class=\"row" . $row . "\">\n");
		// Spalte: Beschreibung
		{
			$table .= ("				<td colspan='8'>".JText::_('COM_JTG_DESCRIPTION') . ":<br />\n" . $lists['description'] . "</td>\n");
		}
		$table .= ("				<td></td>\n");
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
$toggle = array();
$toggle['level'] = ("<select name=\"level_all\" size=\"6\" onclick=\"setSelect('level');\">
						<option>".JText::_('COM_JTG_SELECT') . "</option>
						<option value=\"1\">1</option>
						<option value=\"2\">2</option>
						<option value=\"3\">3</option>
						<option value=\"4\">4</option>
						<option value=\"5\">5</option>
					</select>\n");
$table_header = ("	<table class=\"adminlist\" cellpadding=\"1\">
		<thead>
			<tr>
				<th class=\"title\" width=\"1\"><input type=\"checkbox\" name=\"toggle\" value=\"\" onclick=\"Joomla.checkAll(" . $count . ");\" /></th>
				<th class=\"title\" width=\"1\">".JText::_('COM_JTG_GPS_FILE') . "</th>
				<th class=\"title\" width=\"1\">".JText::_('COM_JTG_DATE') . "</th>
				<th class=\"title\" width=\"1\">".JText::_('COM_JTG_TITLE') . "</th>
				<th class=\"title\" width=\"1\">".JText::_('COM_JTG_LEVEL') . "</th>
				<th class=\"title\" width=\"1\">".JText::_('COM_JTG_CAT') . "</th>
				<th class=\"title\" width=\"1\">".JText::_('COM_JTG_TERRAIN') . "</th>
				<th class=\"title\" width=\"1\">".JText::_('COM_JTG_INFO_AUTHOR') . "</th>
				<th class=\"title\" width=\"1\">".JText::_('COM_JTG_ACCESS_LEVEL') . "</th>
				<th class=\"title\" width=\"1\">".JText::_('COM_JTG_HIDDEN') . "</th>
				<th class=\"title\"></th>
			</tr>
		</thead>\n");

$table_footer = ("		</tbody>\n	</table>\n");

if ( $count == 0 ){
	$model = $this->getModel();
	$rows = $model->_fetchJPTfiles();
	if ( (JFolder::exists(JPATH_BASE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_joomgpstracks')) AND (count($rows) != 0 ) ) {
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
		// Nichts zu importieren
		JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_IMPORTFOLDEREMPTY') . ": \"" . $importdir . "\"",'Warning' );
	}
} 
else
{
echo $table_header.$table.$table_footer;
}
echo JHtml::_( 'form.token' );
$js = "function setSelect(select) {
	var value = getSelectedValue('adminForm', select + '_all');
	for (i=0; i < " . $count . "; i++) {
		setSelectedValue('adminForm', select + '_' + i , value);
	}
}";
$document =& JFactory::getDocument();
$document->addScriptDeclaration($js);
echo ("	<input type=\"hidden\" name=\"option\" value=\"com_jtg\" />
	<input type=\"hidden\" name=\"controller\" value=\"files\" />
	<input type=\"hidden\" name=\"task\" value=\"\" />
	<input type=\"hidden\" name=\"found\" value=\"" . $count . "\" />
	<input type=\"hidden\" name=\"boxchecked\" value=\"0\" />\n");
//echo ("	<input type=\"hidden\" name=\"id\" value=\"" . $this->id . "\" />\n");
echo ("</form>\n");
