<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: osm_files.php,v 1.1 2011/04/03 08:41:54 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Include library dependencies
jimport('joomla.filter.input');

/**
 * Table class
 *
 */
class TableOSM_files extends JTable {

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
		jimport( 'joomla.filter.output' );
		if(empty($this->alias)) {
			$this->alias = $this->title;
		}
		$this->alias = JFilterOutput::stringURLSafe($this->alias);

		/* All your other checks */
		return true;
	}

}
