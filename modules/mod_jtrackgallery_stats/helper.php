<?php
/**
 * @version 0.7
 * @package JTrackGallery
 * @copyright (C) 2009 Michael Pfister, 2013 Christophe Seguinot
 * @license GNU/GPL2

 * You should have received a copy of the GNU General Public License
 * along with Idoblog; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die( 'Restricted access' );

class modjtrackgalleryHelper  {

    function countCats()  {
        $mainframe = JFactory::getApplication();;

        $db = JFactory::getDBO();

        $query = "SELECT COUNT(*) FROM #__jtg_cats WHERE published='1'";
        $db->setQuery($query);
        $result = $db->loadResult();

        return $result;
    }

    function countTracks()  {
        $mainframe = JFactory::getApplication();;

        $db = JFactory::getDBO();

        $query = "SELECT COUNT(*) FROM #__jtg_files WHERE published='1'";
        $db->setQuery($query);
        $result = $db->loadResult();

        return $result;
    }
    
    function countDistance()  {
        $mainframe = JFactory::getApplication();;

        $db = JFactory::getDBO();

        $query = "SELECT SUM(distance) FROM #__jtg_files WHERE published='1'";
        $db->setQuery($query);
        $result = (int) $db->loadResult(); // in km

        return $result;
    }

    function countAscent()  {
        $mainframe = JFactory::getApplication();;

        $db = JFactory::getDBO();

        $query = "SELECT SUM(ele_asc) FROM #__jtg_files WHERE published='1'";
        $db->setQuery($query);
        $result = ($db->loadResult()/1000); //in km

        return $result;
    }

    function countDescent()  {
        $mainframe = JFactory::getApplication();;

        $db = JFactory::getDBO();

        $query = "SELECT SUM(ele_desc) FROM #__jtg_files WHERE published='1'";
        $db->setQuery($query);
        $result =  ($db->loadResult()/1000); //in km

        return $result;
    }

    function countViews()  {
        $mainframe = JFactory::getApplication();;

        $db = JFactory::getDBO();

        $query = "SELECT SUM(hits) FROM #__jtg_files WHERE published='1'";
        $db->setQuery($query);
        $result = $db->loadResult();

        return $result;
    }

    function countVotes()  {
        $mainframe = JFactory::getApplication();;

        $db = JFactory::getDBO();

        $query = "SELECT COUNT(*) FROM #__jtg_votes";
        $db->setQuery($query);
        $result = $db->loadResult();

        return $result;
    }

    
}
?>
