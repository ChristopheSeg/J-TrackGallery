<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @package     Comjtg
 * @subpackage  Module JTrackGalleryLatest
 * @author      Christophe Seguinot <christophe@jtrackgallery.net>
 * @author      Pfister Michael, JoomGPStracks <info@mp-development.de>
 * @copyright   2015 J!TrackGallery, InJooosm and joomGPStracks teams
 *
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link        http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * ModjtrackgalleryLatestHelper class for Module JTrackGalleryLatest
 *
 * @package     Comjtg
 * @subpackage  Module JTrackGalleryLatest
 * @since       0.8
 */

class ModjtrackgalleryLatestHelper
{
	/**
	 * function_description
	 *
	 * @param   integer  $count  number of tracks used in stats
	 *
	 * @return return_description
	 */
	public function getTracks($count)
	{
		$mainframe = JFactory::getApplication();

		$db = JFactory::getDBO();

		$query = "SELECT a.*, b.title as cat FROM #__jtg_files AS a"
				. "\n LEFT JOIN #__jtg_cats AS b ON b.id=a.catid"
				. "\n ORDER BY id DESC"
				. "\n LIMIT " . $count;
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
}
