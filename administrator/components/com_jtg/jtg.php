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

// No direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Define constants for all pages
 */

define( 'COM_JTG_DIR', 'images' . DIRECTORY_SEPARATOR . 'jtrackgallery'. DIRECTORY_SEPARATOR );
define( 'COM_JTG_BASE', JPATH_ROOT . DIRECTORY_SEPARATOR . COM_JTG_DIR );
define( 'COM_JTG_BASEURL', JUri::root().str_replace( DIRECTORY_SEPARATOR, '/', COM_JTG_DIR ));

jimport('joomla.filesystem.file');
// load english language file for 'com_jtg' component then override with current language file
JFactory::getLanguage()->load('com_jtg',   JPATH_ADMINISTRATOR . '/components/com_jtg', 'en-GB', true);
JFactory::getLanguage()->load('com_jtg',   JPATH_ADMINISTRATOR . '/components/com_jtg',    null, true);
JFactory::getLanguage()->load('com_jtg_common', JPATH_SITE . '/components/com_jtg', 'en-GB', true);
JFactory::getLanguage()->load('com_jtg_common', JPATH_SITE . '/components/com_jtg',    null, true);
// com_jtg_additional language files are in /images/jtrackgallery/language folder
JFactory::getLanguage()->load('com_jtg_additional', JPATH_SITE . '/images/jtrackgallery', 'en-GB', true);
JFactory::getLanguage()->load('com_jtg_additional', JPATH_SITE . '/images/jtrackgallery',    null, true);

$contr = JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'install.php';
$model = JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . 'install.php';
// Require the base controller
require_once JPATH_COMPONENT_SITE . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php';
require_once JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'controller.php';

// load the gpsDataClass
JLoader::import('components.com_jtg.helpers.gpsClass', JPATH_SITE, 'gpsClass');

// Initialize the controller
if ($controller = JRequest::getWord('controller'))
{
	$path = JPATH_COMPONENT . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . $controller.'.php';
	$getCmdTask = JFactory::getApplication()->input->get('task' );

	if (file_exists($path))
	{
		require_once $path;
	}
	else
	{
		$controller = '';
	}
}
else
{
	    $getCmdTask = "info";
}

$classname = 'JtgController'.$controller;
$controller = new $classname( );
$controller->execute($getCmdTask );

// Access check: is this user allowed to access the backend of J!TrackGallery?
if (!JFactory::getUser()->authorise('core.manage', 'com_jtg'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Redirect if set by the controller
$controller->redirect();
