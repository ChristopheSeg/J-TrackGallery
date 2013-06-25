<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJO3SM and joomGPStracks teams
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
class TableJTG_cats extends JTable
{
        var $id                   = NULL;

        var $parent               = NULL;

        var $title                = NULL;

        var $description          = NULL;

        var $image                = NULL;

        var $ordering             = NULL;

        var $published            = NULL;

        var $checked_out          = NULL;

        /**
         *
         * @param object $db
         */
        function __construct(& $db) {
            parent::__construct('#__jtg_cats', 'id', $db);
        }

        /**
         *
         * @param array $array
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

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 */
	function check() {
		return true;
	}

}
