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

class modjtrackgalleryLatestHelper  {

    function getTracks($count) {
        $mainframe = JFactory::getApplication();;

        $db = JFactory::getDBO();

        $query = "SELECT a.*, b.title as cat FROM #__jtg_files AS a"
                . "\n LEFT JOIN #__jtg_cats AS b ON b.id=a.catid"
                . "\n ORDER BY id DESC"
                . "\n LIMIT ".$count
                ;
        $db->setQuery($query);
        $result = $db->loadObjectList();

        return $result;
    }
}
?>
