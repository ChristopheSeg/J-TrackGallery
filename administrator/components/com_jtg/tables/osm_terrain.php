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
class TableOSM_terrain extends JTable
{
        var $id             = NULL;
        var $title          = NULL;
        var $published      = NULL;
        var $checked_out    = NULL;
        var $ordering       = NULL;

        /**
         *
         * @param object $db
         */
        function __construct(& $db) {
            parent::__construct('#__jtg_terrains', 'id', $db);
        }

        /**
         *
         * @param array $array
         * @param string $ignore
         * @return string
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
