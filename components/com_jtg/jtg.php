<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooosm and joomGPStracks teams
 * @package    com_jtg
 * @subpackage frontend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

global $jtg_microtime;
$jtg_microtime = microtime(true);
// Require the base controller
require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'controller.php';

// Require the base helper
require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'layout.php';
require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php';
JLoader::register('gpsCLass', '.' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jtg' . DIRECTORY_SEPARATOR . 'helpers'. DIRECTORY_SEPARATOR);
JLoader::import('components.com_jtg.helpers.gpsClass', JPATH_SITE, 'gpsClass');

JFactory::getLanguage()->load('com_jtg', JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jtg', 'en-GB', true);
JFactory::getLanguage()->load('com_jtg', JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jtg',    null, true);
JFactory::getLanguage()->load('com_jtg_common', JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jtg', 'en-GB', true);
JFactory::getLanguage()->load('com_jtg_common', JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jtg',    null, true);
// com_jtg_additional language files are in /images/jtrackgallery/language folder
JFactory::getLanguage()->load('com_jtg_additional' . DIRECTORY_SEPARATOR . JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery', 'en-GB', true);
JFactory::getLanguage()->load('com_jtg_additional' . DIRECTORY_SEPARATOR . JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery',    null, true);
$cfg = JtgHelper::getConfig();
// set the template
$tmpl = ($cfg->template = "") ? $cfg->template : 'default';

$document =& JFactory::getDocument();
$document->addStyleSheet(JUri::base().'components' . DIRECTORY_SEPARATOR . 'com_jtg' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $tmpl. DIRECTORY_SEPARATOR . 'jtg_style.css');
// override style with user templates
$mainframe =& JFactory::getApplication();
jimport('joomla.filesystem.file');
$template_css = 'templates' . DIRECTORY_SEPARATOR .  $mainframe->getTemplate() . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . 'jtg_style.css';
if (JFile::exists($template_css) ) 
{
   // override with site template
   $document->addStyleSheet( $template_css);
 
}
// Initialize the controller
if($controller = JRequest::getWord('controller')) 
{
	$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controller.'.php';
	if (file_exists($path)) {
		require_once $path;
	} else 
	{
		$controller = '';
	}
}

$classname    = 'JtgController'.ucfirst($controller);
$controller   = new $classname( );

// Register Extra tasks
$controller->registerTask( 'save', 'save' );

$controller->execute( JRequest::getCmd( 'task' ) );
// Redirect if set by the controller

$controller->redirect();
// en-/disable execution time
if ( ( JDEBUG ) AND (isset($jtg_microtime) ) ) {
	$seconds = ( microtime(true) - $jtg_microtime );
	$milliseconds = ( $seconds * 1000 );
	$microseconds = ( $seconds * 1000000 );
	echo
	(int) round($microseconds,0) . " μs<br />".
	(int) round($milliseconds,0) . " ms<br />".
	(int) round($seconds,0) . " s";
}
