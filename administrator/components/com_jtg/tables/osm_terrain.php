<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: osm_terrain.php,v 1.1 2011/04/03 08:41:54 christianknorr Exp $
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
class TableOSM_terrain extends JTable  {

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
