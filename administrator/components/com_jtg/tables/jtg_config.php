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
/**
 * Table class
 *
 */
class TableJTG_config extends JTable
{
	var $id				= null;

	var $gid			= null;

	var $unit			= null;

	var $type			= null;

	var $max_size		= null;

	var $max_thumb_height		= null;

	var $max_geoim_height		= null;

	var $terms			= null;

	var $terms_id		= null;

	var $sort			= null;

	var $map_height		= null;

	var $map_width		= null;

	var $charts_width	= null;

	var $charts_height	= null;

	var $charts_linec	= null;

	var $charts_bg		= null;

	var $profile		= null;

	var $template		= null;

	var $comment_who	= null;

	var $inform_autor	= null;

	var $captcha		= null;

	var $ordering		= null;

	var $comments		= null;

	var $access			= null;

	var $approach		= null;

	var $routingiconset	= null;

	var $usevote		= null;

	var $download		= null;

	var $gallery		= null;

	var $level			= null;

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
		if (key_exists('params', $array) && is_array($array['params']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}
}
