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

// No direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');
/**
 * Model Class Configuration
 */
class JtgModelConfig extends JModelLegacy
{
	/**
	 *
	 */
	function __construct() {
		parent::__construct();
	}


	/**
	 *
	 * @return boolean
	 */
	function saveConfig()
	{
		//	get post data
		$row =& JRequest::get( 'post' );
		//	Store tables if they not exists		
		$cfg = JtgHelper::getConfig();
		$createColumns = $this->createColumns($row, "config");
		if ($createColumns !== true)
		return $createColumns;
		//	Bereinige $row um OSM-Available Map
		//	$row = $this->cleanJTGconfig($row);
		$table = $this->getTable( 'jtg_config' );

		// for gid multiple select Normally done in bind  (/models/config.php but does not work!)
		$row['gid'] = serialize($row['gid']) ;		
		$table->bind( $row );
		if (!$table->store()) 
		{
		    return $table->getError();
		}
		// Config saved, 
		if ( ($row['max_geoim_height']<>$cfg->max_geoim_height) 
			OR ($row['max_thumb_height']<>$cfg->max_thumb_height) )
		{
		    // recreate thumbnails if max_height changed
		    require_once(JPATH_SITE . DS . "administrator" . DS . "components" . DS . "com_jtg" . DS . "models" . DS . "thumb_creation.php");
		    com_jtg_refresh_Thumbnails();
		}
		return true;
	}

	/**
	 * Create Columns if they not exists, after upgrade for example
	 * @return true or errormessage
	 */
	function createColumns($row, $tablekey)
	{
		echo "deprecated: JtgModelConfig::createColumns";
		//	find out exists columns
		$db = &JFactory::getDBO();
		$sql='Select * from #__jtg_'.$tablekey;
		$db->setQuery($sql);
		$existobj = $db->loadObject();
		$existarr = array();

		//	object to array conversion
		foreach($existobj AS $table => $value)
		$existarr[$table] = $value;
		//	exclude unnecessary columns
		$ignore = array();
		if ($tablekey == "config")
		{
			$ignore = array('map','option','task','jtg_param_default_map');
			for($i=0;$i<10;$i++)
			$ignore[] = 'jtg_param_allow_map_'.$i;
		}
		//	find out missing columns
		$missingcolumns = array();
		foreach($row AS $key => $value)
		{
			//		$istoken = ((strlen($key) == 32) AND ($value == "1")); // quick'n'dirty to find token -> ugly and unsafe
			$istoken = JSession::checkToken();
			if ((!in_array($key, $ignore)) AND (!array_key_exists($key, $existarr)) AND (!$istoken))
			{
				$missingcolumns[] = $key;
			}
		}
		if ( count($missingcolumns) == 0 )
		//		all necessary colums exists
		return true;

		//	load install.sql
		$file = JPath::clean(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jtg' . DS . 'sql' . DS . 'install.sql');
		if (!is_file($file))
		return ('File "'.$file.'" not found');
		if (jimport('joomla.filesystem.file'))
		$sqlcontent = file_get_contents($file);
		$sqlcontent = explode("\n", $sqlcontent);
		$content = null;
		$content_switch = false;
		$comma = "";
		//	convert sql-statement from CREATE to ALTER ADD
		foreach ($sqlcontent AS $zeile)
		{
			if ($content_switch)
			{
				$tempcontent = explode('`', $zeile);
				if (count($tempcontent) == 3)
				{
					$tempcontent = $tempcontent[1];
					$zeile = str_replace(",",null, $zeile);
					if (in_array($tempcontent, $missingcolumns)) {
						$content .= $comma . "ADD COLUMN " . $zeile;
						if($comma == "")
						$comma = ",\n";
					}
				}
			}
			if ((preg_match("/#__jtg_" . $tablekey . "/", $zeile)) AND (preg_match("/CREATE/",$zeile)) AND (!$content_switch))
			{
				$zeile = str_replace("(", "", $zeile);
				$zeile = str_replace("CREATE", "ALTER IGNORE", $zeile);
				$zeile = str_replace("IF NOT EXISTS", null, $zeile);
				$content .= $zeile . "\n";
				$content_switch = true;
			}
			if ((preg_match("/\;/", $zeile)) AND ($content_switch))
			{
				$content .= ";";
				break;
			}
		}
		$db = &JFactory::getDBO();
		$db->setQuery($content);
		$db->query();

		if ($db->getErrorNum())
		return( ($db->stderr()));
		return true;
	}

	/**
	 * @return Object
	 */
	function getContent() {
		$db = &JFactory::getDBO();
		$sql='Select id from #__categories where title=\'term\'';
		$db->setQuery($sql);
		$catid = $db->loadResult();
		$sql='Select id from #__categories where title=\'jtg\'';
		$db->setQuery($sql);
		$secid = $db->loadResult();
		if (($catid==null)OR($secid==null))
		return false;
		$mainframe =& JFactory::getApplication();
		$query = "SELECT id, title FROM #__content WHERE"
		. "\n sectionid='" . $secid . "'"
		. "\n AND catid='" . $catid . "'"
		. "\n AND state='1'";

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}
		return $result;
	}


	function getTemplates()  {
		jimport('joomla.filesystem.file');

		$templates = JFolder::listFolderTree('..' . DS . 'components' . DS . 'com_jtg' . DS . 'assets' . DS . 'template','',1);
		return $templates;
	}
}
