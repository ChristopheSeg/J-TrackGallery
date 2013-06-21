<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooOSM and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
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
 */
class TableOSM_files extends JTable{
	var $id				= NULL;
	var $uid			= NULL;
	var $catid			= NULL;
	var $title			= NULL;
	var $file			= NULL;
	var $terrain		= NULL;
	var $description	= NULL;
	var $published		= NULL;
	var $date			= NULL;
	var $hits			= NULL;
	var $checked_out	= NULL;
	var $start_n		= NULL;
	var $start_e		= NULL;
	var $distance		= NULL;
	var $ele_asc		= NULL;
	var $ele_desc		= NULL;
	var $level			= NULL;
	var $access			= NULL;
	var $istrack		= NULL;
	var $iswp			= NULL;
	var $isroute		= NULL;
	var $vote			= NULL;
	var $hidden			= NULL;


	/**
	 *
	 * @param object $db
	 */
	function __construct(& $db) {
		parent::__construct('#__jtg_files', 'id', $db);
	}

	/**
	 *
	 * @param array $array
	 * @param string<type> $ignore
	 * @return object
	 */
	function bind($array, $ignore = '')
	{
		if (key_exists( 'params', $array ) && is_array( $array['params'] ))
		{
			$registry = new JRegistry();
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
		if(empty($this->alias)) {
			$this->alias = $this->title;
		}
		$this->alias = JFilterOutput::stringURLSafe($this->alias);

		/* All your other checks */
		return true;
	}

}
