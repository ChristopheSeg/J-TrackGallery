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

// no direct access
defined('_JEXEC') or die('Restricted access');
/**
 * Table class
 * 
 */
class TableOSM_maps extends JTable  {
	var $id				= NULL;
	var $name			= NULL;
	var $order			= NULL;
	var $param			= NULL;
	var $published		= NULL;
	var $script			= NULL;
	var $code			= NULL;
	var $checked_out	= NULL;
	//	var $			= NULL;

	/**
	 *
	 * @param object $db
	 */
	function __construct(& $db)
	{
		parent::__construct('#__jtg_maps', 'id', $db);
	}

	/**
	 *
	 * @param arrray $array
	 * @param string $ignore
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
}
