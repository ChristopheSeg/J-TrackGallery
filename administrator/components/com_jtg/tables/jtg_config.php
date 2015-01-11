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
/**
 * Table class
 *
 */
class TableJTG_config extends JTable
{
	var $id				= NULL;
	var $gid			= NULL;
	var $unit			= NULL;
	var $type			= NULL;
	var $max_size		= NULL;
	var $max_thumb_height		= NULL;
	var $max_geoim_height		= NULL; 
	var $terms			= NULL;
	var $terms_id		= NULL;
	var $sort			= NULL;
	var $map_height		= NULL;
	var $map_width		= NULL;
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
	var $approach		= NULL;
	var $routingiconset	= NULL;
	var $usevote		= NULL;
	var $download		= NULL;
	var $gallery		= NULL;
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
	 * @param   string  $ignore
	 * @return object
	 */
	function bind($array, $ignore = '')
	{
		if (key_exists( 'params', $array ) && is_array( $array['params'] ))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}
}
