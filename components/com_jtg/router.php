<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @author      Christophe Seguinot <christophe@jtrackgallery.net>
 * @author      Pfister Michael, JoomGPStracks <info@mp-development.de>
 * @author      Christian Knorr, InJooOSM  <christianknorr@users.sourceforge.net>
 * @copyright   2015 J!TrackGallery, InJooosm and joomGPStracks teams
 *
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link        http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/*
 * Function to convert a system URL to a SEF URL
*/
	/**
	 * Function to convert a system URL to a SEF URL
	 *
	 * @param   array  &$query  segmented URL
	 *
	 * @return segmented URL (array)
	 */
function jtgBuildRoute(&$query)
{
	$segments = array();
	$app = JFactory::getApplication();
	$menu = $app->getMenu();

	if (empty($query['Itemid']))
	{
		$menuItem = $menu->getActive();
	}
	else
	{
		$menuItem = $menu->getItem($query['Itemid']);
	}
	// $menuid = $menuItem->id;

	if (isset($query['view']))
	{
		$segments[] = $query['view'];
		unset($query['view']);
	}

	if (isset($query['layout']))
	{
		$segments[] = $query['layout'];
		unset($query['layout']);
	}

	if (isset($query['controller']))
	{
		$segments[] = $query['controller'];
		unset($query['controller']);
	}

	if (isset($query['task']))
	{
		$segments[] = $query['task'];
		unset($query['task']);
	}

	if (isset($query['id']))
	{
		$segments[] = $query['id'];
		unset($query['id']);
	}

	return $segments;
}

/**
 * function_description
 *
 * @param   array  $segments  segmented URL
 *
 * @return return_description
 */
function _jtgParseRouteFile($segments)
{
	switch ($segments[1])
	{
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

	if (!isset($vars))
	{
		return false;
	}

	return $vars;
}

/**
 * function_description
 *
 * @param   unknown_type  $segments  param_description
 *
 * @return return_description
 */
function _jtgParseRouteCategory($segments)
{
	switch ($segments[0])
	{
		case 'files':
			$vars['view'] = 'files';
			$vars['layout'] = 'list';
			break;
		case 'cats':
			$vars['view'] = 'cats';
			$vars['layout'] = 'default';
			break;
		case 'jtg':
			$vars['view'] = 'jtg';
			break;
	}

	if (!isset($vars))
	{
		return false;
	}

	return $vars;
}

/**
 * function_description
 *
 * @param   unknown_type  $segments  param_description
 *
 * @return return_description
 */
function _jtgParseRouteSubCategory($segments)
{
	$vars['view'] = $segments[0];
	$vars['layout'] = $segments[1];
	// TODO: could remove the case statements? keep files/map?
	switch ($segments[0])
	{
		case 'files':
			switch ($segments[1])
			{
				case 'form':
					$vars['view'] = 'files';
					$vars['layout'] = 'form';
					break;

				case 'user':
					$vars['view'] = 'files';
					$vars['layout'] = 'user';
					break;

				case 'map':
					$vars['view'] = 'jtg';
					$vars['layout'] = 'map';
					break;

			}
			break;
		case 'jtg':
			switch ($segments[1])
			{
				case 'geo':
					$vars['view'] = 'jtg';
					$vars['layout'] = 'geo';
					break;
			}
	}

	if (!isset($vars))
	{
		return false;
	}

	return $vars;
}

/**
 * Function to convert a SEF URL back to a system URL
 *
 * @param   array  $segments  segmented URL
 *
 * @return return_description
 */
function jtgParseRoute($segments)
{
	$vars = array();

	// Get the active menu item
	$app = JFactory::getApplication();
	$menu = $app->getMenu();
	$item = $menu->getActive();

	// Count route segments
	$count = count($segments);

	if ( $count == 1 )
	{
		$vars = _jtgParseRouteCategory($segments);
	}
	elseif ( $count == 2 )
	{
		if ( isset( $segments[1] )
			AND ( $segments[1] == "default" )
			OR  ( ( $segments[0] == "files" ) AND ( $segments[1] == "list" ) ))
		{
			$vars = _jtgParseRouteCategory($segments);
		}
		else
		{
			$vars = _jtgParseRouteSubCategory($segments);
		}
	}
	else
	{
		switch ($segments[0])
		{
			case 'files':
				$vars = _jtgParseRouteFile($segments);
				break;
		}
	}

	if ( ( $vars === false ) OR ( count($vars) == 0 ) )
	{
		$vars['view'] = 'files';
		$vars['layout'] = 'list';

		return $vars;
	}

	return $vars;
}
