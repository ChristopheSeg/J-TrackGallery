<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: osm_maps.php,v 1.1 2011/04/03 08:41:54 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
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
