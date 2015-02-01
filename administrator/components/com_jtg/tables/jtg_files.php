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
class TableJTG_files extends JTable
{
	var $id				= null;

	var $uid			= null;

	var $catid			= null;

	var $title			= null;

	var $file			= null;

	var $terrain		= null;

	var $description	= null;

	var $published		= null;

	var $date			= null;

	var $hits			= null;

	var $checked_out	= null;

	var $start_n		= null;

	var $start_e		= null;

	var $distance		= null;

	var $ele_asc		= null;

	var $ele_desc		= null;

	var $level			= null;

	var $access			= null;

	var $istrack		= null;

	var $iswp			= null;

	var $isroute		= null;

	var $vote			= null;

	var $hidden			= null;


	/**
	 *
	 * @param object $db
	 */
	function __construct(& $db)
	{
		parent::__construct('#__jtg_files', 'id', $db);
	}

	/**
	 *
	 * @param   array  $array
	 * @param string<type> $ignore
	 * @return object
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

	/**
	 *
	 * @return boolean
	 */
	function check()
	{
		jimport('joomla.filter.output');

		if (empty($this->alias))
		{
			$this->alias = $this->title;
		}

		$this->alias = JFilterOutput::stringURLSafe($this->alias);

		return true;
	}
}
