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
 */


// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_petcare' . DS . 'tables');
/**
 * Controller Class Configuration
 */
class JtgControllerInstall extends JtgController
{
	/**
	 *
	 */
	function display()
	{
		parent::display();
	}

	/**
	 *
	 * @global object $mainframe
	 * @return boolean
	 */
	function install_install()
	{
		//$mainframe =& JFactory::getApplication();
		$model = $this->getModel('install');
		echo "<br><br>admin/com_JTG// controllers/install.php:";print_r($model);die();
		if(!$model->installCleanDB("install")) return false;
		return true;
	}

	/*  TODO  Obsolete 
	function install_update047() {
		$model = $this->getModel('install');
		if(!$model->installCleanDB("update047")) return false;
		return true;
	}

*/
	function install_maps() {
		$model = $this->getModel('install');
		if(!$model->installCleanDB("maps")) return false;
		return true;
	}

	function install_cats() {
		$model = $this->getModel('install');
		if(!$model->installCleanDB("cats")) return false;
		return true;
	}

	function install_terrains() {
		$model = $this->getModel('install');
		if(!$model->installCleanDB("terrains")) return false;
		return true;
	}
}
