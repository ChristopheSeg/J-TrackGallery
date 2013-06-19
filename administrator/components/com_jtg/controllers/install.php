<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: install.php,v 1.2 2011/04/09 09:33:39 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_petcare'.DS.'tables');
/**
 * Controller Class Configuration
 */
class jtgControllerInstall extends jtgController
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
		echo "<br><br>";print_r($model);die();
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
