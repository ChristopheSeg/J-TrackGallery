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

require_once dirname(__FILE__).'/helper.php';
$latest = new modjtrackgalleryLatestHelper;

$width = $params->get('width');
$heigth = $params->get('heigth');
$zoom = $params->get('zoom');
$map = $params->get('map');
$color = $params->get('color');
$count = $params->get('count');
$tracks = $latest->getTracks($count);
$apikey = $params->get('apikey');
if ($params->get('unit') == "Kilometer" ) 
{
    $unit = 1;
}
else
{
    $unit = 1/1.609344;
}


require JModuleHelper::getLayoutPath('mod_jtrackgallery_latest', $params->get('layout', 'default'));

?>
