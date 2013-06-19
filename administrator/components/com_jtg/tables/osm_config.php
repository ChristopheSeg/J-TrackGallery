<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: osm_config.php,v 1.3 2011/04/09 09:33:39 christianknorr Exp $
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
class TableOSM_config extends JTable  {
	var $id				= NULL;
	var $gid			= NULL;
	var $apikey			= NULL;
	var $unit			= NULL;
	var $type			= NULL;
	var $max_size		= NULL;
	var $terms			= NULL;
	var $terms_id		= NULL;
	var $sort			= NULL;
	var $map_height		= NULL;
	var $map_width		= NULL;
	var $map_type		= NULL;
	var $charts_width	= NULL;
	var $charts_height	= NULL;
	var $charts_linec	= NULL;
	var $charts_bg		= NULL;
	var $profile		= NULL;
	var $template		= NULL;
	var $comment_who	= NULL;
	var $inform_autor	= NULL;
	var $captcha		= NULL;
	var $ordering		= NULL;
	var $comments		= NULL;
	var $access			= NULL;
	var $map			= NULL;
	var $approach		= NULL;
	var $routingiconset	= NULL;
	var $usevote		= NULL;
	var $download		= NULL;
	var $gpsstore		= NULL;
	var $gallery		= NULL;
	var $serviceprovider= NULL;
	var $level			= NULL;
	
	/**
	 *
	 * @param object $db
	 */
	function __construct(& $db) {
		parent::__construct('#__jtg_config', 'id', $db);
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
