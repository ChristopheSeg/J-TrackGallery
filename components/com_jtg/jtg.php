<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: jtg.php,v 1.2 2011/04/21 21:37:43 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage frontend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

global $osmmicrotime;
$osmmicrotime = microtime(true);
// Require the base controller
require_once JPATH_COMPONENT.DS.'controller.php';

// Require the base helper
require_once JPATH_COMPONENT.DS.'helpers'.DS.'layout.php';
require_once JPATH_COMPONENT.DS.'helpers'.DS.'helper.php';
JLoader::register('gpsCLass', '.'.DS.'components'.DS.'com_jtg'.DS.'helpers'.DS);
JLoader::import('components.com_jtg.helpers.gpsClass', JPATH_SITE, 'gpsClass');

// Load additional Language-Files
$language = &JFactory::getLanguage();
$language->load('com_jtg');
$language->load('com_jtg_additional');

$cfg = jtgHelper::getConfig();
// set the template
$tmpl = ($cfg->template = "") ? $cfg->template : 'default';

$document =& JFactory::getDocument();
$document->addStyleSheet(JURI::base().'components/com_jtg/assets/template/'.$tmpl.'/style.css');

// Initialize the controller
if($controller = JRequest::getWord('controller')) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else {
		$controller = '';
	}
}

$classname    = 'jtgController'.ucfirst($controller);
$controller   = new $classname( );

// Register Extra tasks
$controller->registerTask( 'save', 'save' );

$controller->execute( JRequest::getCmd( 'task' ) );
// Redirect if set by the controller

$controller->redirect();
// en-/disable execution time
if ( ( JDEBUG ) AND (isset($osmmicrotime) ) ) {
	$seconds = ( microtime(true) - $osmmicrotime );
	$milliseconds = ( $seconds * 1000 );
	$microseconds = ( $seconds * 1000000 );
	echo
	(int) round($microseconds,0)." μs<br />".
	(int) round($milliseconds,0)." ms<br />".
	(int) round($seconds,0)." s";
}
