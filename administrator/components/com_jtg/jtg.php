<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: jtg.php,v 1.2 2011/04/21 21:37:42 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Define constants for all pages
 */
// @ToDo check and work with it
define( 'COM_JTG_DIR', 'images'.DS.'jtg'.DS );
define( 'COM_JTG_BASE', JPATH_ROOT.DS.COM_JTG_DIR );
define( 'COM_JTG_BASEURL', JURI::root().str_replace( DS, '/', COM_JTG_DIR ));

jimport('joomla.filesystem.file');
// load english language file for 'com_jtg' component then override with current language file
JFactory::getLanguage()->load('com_jtg',   JPATH_ADMINISTRATOR, 'en-GB', true);
JFactory::getLanguage()->load('com_jtg',   JPATH_ADMINISTRATOR,    null, true);
JFactory::getLanguage()->load('com_jtg_additional', JPATH_SITE, 'en-GB', true);
JFactory::getLanguage()->load('com_jtg_additional', JPATH_SITE,    null, true);

$contr = JPATH_COMPONENT.DS.'controllers'.DS.'install.php';
$model = JPATH_COMPONENT.DS.'models'.DS.'install.php';
// Require the base controller
require_once JPATH_COMPONENT_SITE.DS.'helpers'.DS.'helper.php';
require_once JPATH_COMPONENT.DS.'controller.php';


// Initialize the controller
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	$getCmdTask = JRequest::getCmd( 'task' );
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
} else $getCmdTask = "info";

$classname = 'jtgController'.$controller;
$controller = new $classname( );
$controller->execute( $getCmdTask );

// Redirect if set by the controller
$controller->redirect();
