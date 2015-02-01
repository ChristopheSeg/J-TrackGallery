<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @package    Comjtg
 * @author     Christophe Seguinot <christophe@jtrackgallery.net>
 * @copyright  2013 J!Track Gallery, InJooosm and joomGPStracks teams
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Include library dependencies
jimport('joomla.filter.input');

/**
 * Table class
 *
 */
class TableJTG_terrain extends JTable
{
	var $id             = null;

	var $title          = null;

	var $published      = null;

	var $checked_out    = null;

	var $ordering       = null;

	/**
	 *
	 * @param object $db
	 */
	function __construct(& $db)
	{
		parent::__construct('#__jtg_terrains', 'id', $db);
	}

	/**
	 *
	 * @param   array  $array
	 * @param   string  $ignore
	 * @return string
	 */
	function bind($array, $ignore = '')
	{
		if (key_exists('params', $array) && is_array($array['params']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}
}
