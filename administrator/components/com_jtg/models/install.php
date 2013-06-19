<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: install.php,v 1.2 2011/04/09 09:33:38 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');
/**
 * Model Class Configuration
 */
class jtgModelInstall extends JModel {
	/**
	 *
	 */
	function __construct() {
		parent::__construct();
	}

	/**
	 * Unpack Sample Categorie-Icons
	 * @return boolean
	 */
	function upackCatIcons() {
		$source = JPATH_SITE.DS."components".DS."com_jtg".DS."assets".DS."images".DS."source".DS."samplecaticons.zip";
		$destpath = JPATH_SITE.DS."images".DS."jtg".DS."cats";
		jimport( 'joomla.filesystem.archive' );
		if (JArchive::extract($source,$destpath)) return true;
		return false;
	}

	/**
	 *
	 * @global object $mainframe
	 * @return boolean
	 */
	function installCleanDB($table) {
		$mainframe =& JFactory::getApplication();
// TODO echo "<br><br> $table";die();
		$db =& JFactory::getDBO();

		$path = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jtg'.DS.'sql'.DS;
		switch ($table) {
			case "install":
				$file = JPath::clean($path.'install.sql');
				break;
			case "update047": // Obsolete
				$file = JPath::clean($path.'update047.sql');
				break;
			case "maps":
				$file = JPath::clean($path.'make_maps.sql');
				break;
			case "cats":
				$file = JPath::clean($path.'make_cats.sql');
				if (!$this->upackCatIcons()) return false;
				break;
			case "terrains":
				$file = JPath::clean($path.'make_terrains.sql');
				break;
		}

		$buffer = file_get_contents($file);

		if($buffer === false) {
			return false;
		} else {
			jimport('joomla.installer.helper');
			$queries = JInstallerHelper::splitSql($buffer);

			foreach($queries as $query) {
				$query = trim($query);
				if($query != '' && $query != '#') {
					$db->setQuery($query);
					$db->query();
					if ($db->getErrorNum()) {
						echo $db->stderr();
						return false;
					}
				}
			}
			return true;
		}
	}
}
