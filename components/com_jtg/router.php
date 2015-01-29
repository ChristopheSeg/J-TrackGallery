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
 * Function to convert a system URL to a SEF URL
*/
function jtgBuildRoute(&$query) {
	$segments = array();
	$app = JFactory::getApplication();
	$menu = $app->getMenu();

	if (empty($query['Itemid'])) {
		$menuItem = $menu->getActive();
	}
	else
	{
		$menuItem = $menu->getItem($query['Itemid']);
	}
	//	$menuid = $menuItem->id;

	if (isset($query['view'])){
		$segments[] = $query['view'];
		unset($query['view']);
	}
	if (isset($query['layout'])){
		$segments[] = $query['layout'];
		unset($query['layout']);
	}
	if (isset($query['controller'])) {
		$segments[] = $query['controller'];
		unset($query['controller']);
	}
	if (isset($query['task'])) {
		$segments[] = $query['task'];
		unset($query['task']);
	}
	if (isset($query['id'])){
		$segments[] = $query['id'];
		unset($query['id']);
	}

	return $segments;
}

function _jtgParseRouteFile($segments) {
	switch ($segments[1]) {
		case 'file':
			$vars['view'] = 'files';
			$vars['layout'] = 'file';
			$vars['id'] = $segments[2];
			break;

		case 'form':
			$vars['view'] = 'files';
			$vars['layout'] = 'form';
			$vars['id'] = $segments[2];
			break;

		case 'delete':
			$vars['controller'] = 'files';
			$vars['task'] = 'delete';
			$vars['id'] = $segments[2];
			break;

		case 'vote':
			$vars['controller'] = 'files';
			$vars['task'] = 'vote';
			$vars['id'] = $segments[2];
			break;
	}
	if (!isset($vars)) return false;
	return $vars;
}

function _jtgParseRouteCategory($segments) {
	switch ($segments[0]) {
		case 'files':
			$vars['view'] = 'files';
			$vars['layout'] = 'list';
			break;
		case 'cats':
			$vars['view'] = 'cats';
			$vars['layout'] = 'default';
			break;
	}
	if (!isset($vars)) return false;
	return $vars;
}

function _jtgParseRouteSubCategory($segments) {
	switch ($segments[0]) {
		case 'files':
			switch ($segments[1]) {
				case 'form':
					$vars['view'] = 'files';
					$vars['layout'] = 'form';
					break;

				case 'user':
					$vars['view'] = 'files';
					$vars['layout'] = 'user';
					break;
			}
			break;
		case 'jtg':
			switch ($segments[1]) {
				case 'geo':
					$vars['view'] = 'jtg';
					$vars['layout'] = 'geo';
					break;
			}
	}
	if (!isset($vars)) return false;
	return $vars;
}

/*
 * Function to convert a SEF URL back to a system URL
*/
function jtgParseRoute($segments) {

	$vars = array();

	//Get the active menu item
	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	$item = $menu->getActive();

	// Count route segments
	$count = count($segments);

	if ( $count == 1 ) {
		$vars = _jtgParseRouteCategory($segments);
	}
	elseif ( $count == 2 ) {
		if ( isset( $segments[1] ) AND
				( $segments[1] == "default" ) // Cats/default
				OR
				( ( $segments[0] == "files" ) AND ( $segments[1] == "list" ) )
		) {
			$vars = _jtgParseRouteCategory($segments);
		}
		else
		{
			$vars = _jtgParseRouteSubCategory($segments);
		}
	}
	else
	{
		switch ($segments[0]) {
			case 'files':
				$vars = _jtgParseRouteFile($segments);
				break;
		}
	}

	if ( ( $vars === false ) OR ( count($vars) == 0 ) )
	{
		//		$errmsg = implode("/",$segments);
		//		$errmsg = "Route " . $errmsg . " does not exists!";
		//		JFactory::getApplication()->enqueueMessage($errmsg, 'Warning');
		$vars['view'] = 'files';
		$vars['layout'] = 'list';
		return $vars;
	}
	return $vars;
}
