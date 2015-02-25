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
$stats = new modjtrackgalleryHelper;

$moduleclass_sfx = $params->get('moduleclass_sfx');
$tcustom_enable = $params->get('tcustom_enable');
$tcustom = $params->get('tcustom');
$thead = $params->get('thead');
$tcat = $params->get('tcat');
$ttrack = $params->get('ttrack');
$tdis = $params->get('tdis');
$tasc = $params->get('tasc');
$tdec = $params->get('tdec');
$tview = $params->get('tview');
$tvote = $params->get('tvote');
$theado = $params->get('theado');
$tcato = $params->get('tcato');
$ttracko = $params->get('ttracko');
$tdiso = $params->get('tdiso');
$tasco = $params->get('tasco');
$tdeco = $params->get('tdeco');
$tviewo = $params->get('tviewo');
$tvoteo = $params->get('tvoteo');
$cats = $stats->countCats();
$tracks = $stats->countTracks();
$distance_km = $stats->countDistance(); // in kilometer
$ascent = $stats->countAscent();
$descent = $stats->countDescent();
$views = $stats->countViews();
$votes = $stats->countVotes();
$distance_mi = $distance_km/1.609344; // in Miles
$decimalseparator = $params->get('decimalseparator');

// round value and account for decimal separator
$distance_km = (string) round($distance_km, 1); 
$distance_mi = (string) round($distance_mi, 1); 
if ($params->get('unit') == "Kilometer" ) 
{
    $distance = $distance_km;
}
else
{
    $distance = $distance_mi;
}
$ascent = (string) round($ascent, 1); 
$descent = (string) round($descent, 1); 

if ($decimalseparator != '.')
{
    $distance = str_replace ('.', $decimalseparator, $distance);
    $distance_km = str_replace ('.', $decimalseparator, $distance_km);
    $distance_mi = str_replace ('.', $decimalseparator, $distance_mi);
    $ascent = str_replace ('.', $decimalseparator, $ascent);
    $descent = str_replace ('.', $decimalseparator, $descent);
}

require JModuleHelper::getLayoutPath('mod_jtrackgallery_stats', $params->get('layout', 'default'));

?>
