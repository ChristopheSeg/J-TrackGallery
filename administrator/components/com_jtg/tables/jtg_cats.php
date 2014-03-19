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

// Include library dependencies
jimport('joomla.filter.input');

/**
 * Table class
 *
 * @since  0.1
 */
class TableJTG_Cats extends JTable
{
	var $id = null;

	var $parent = null;

	var $title = null;

	var $description = null;

	var $image = null;

	var $ordering = null;

	var $published = null;

	var $checked_out = null;

	/**
	 *
	 * @param   object  $db  the  database
	 *
	 */
	function __construct (& $db)
	{
		parent::__construct('#__jtg_cats', 'id', $db);
	}

	/**
	 * bind function
	 *
	 * @param   array   $array
	 * @param   string  $ignore
	 *
	 * @return object
	 */
	function bind ($array, $ignore = '')
	{
		if (key_exists('params', $array) && is_array($array['params']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 */
	function check ()
	{
		return true;
	}
}
