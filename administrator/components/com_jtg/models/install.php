<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJO3SM and joomGPStracks teams
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
class JtgModelInstall extends JModel
{
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
		$source = JPATH_SITE . DS . "components" . DS . "com_jtg" . DS . "assets" . DS . "images" . DS . "source" . DS . "samplecaticons.zip";
		$destpath = JPATH_SITE . DS . "images" . DS . "jtrackgallery" . DS . "cats";
		jimport('joomla.filesystem.archive');
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

		$path = JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jtg' . DS . 'sql'.DS;
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
