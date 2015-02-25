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

$tcustom_enable = $params->get('tcustom_enable');

// load english language file for 'com_jtg' component then override with current language file
JFactory::getLanguage()->load('com_jtg_common', JPATH_SITE . '/components/com_jtg',    null, true);
// com_jtg_additional language files are in /images/jtrackgallery/language folder
JFactory::getLanguage()->load('com_jtg_additional', JPATH_SITE . '/images/jtrackgallery',    null, true);


foreach($tracks as $track)
{
    $distance_km = (string) round($track->distance,1);
    $distance_mi_or_km = (string) round($unit*$track->distance,1);
    $distance_mi = (string) round($track->distance/1.609344,1);
    $decimalseparator = $params->get('decimalseparator');
    if ($decimalseparator != '.')
    {
	$distance_km = str_replace ('.', $decimalseparator, $distance_km);
	$distance_mi_or_km = str_replace ('.', $decimalseparator, $distance_mi_or_km);
	$distance_mi = str_replace ('.', $decimalseparator, $distance_mi);
    }
    if ($tcustom_enable)
    {
	$tcustom = $params->get('tcustom');
	$tcustom = str_replace('$cats', ($track->cat? JText::_($track->cat): '?'), $tcustom);
	$tcustom = str_replace('$distance_km', $distance_km, $tcustom);
	$tcustom = str_replace('$distance_mi', $distance_mi, $tcustom);
    }

    if($params->get('style') == 0)
	{

		$link = JRoute::_('index.php?option=com_jtg&view=files&layout=file&id='.$track->id);
		echo '<div align="center" style="margin-bottom:20px;">';
		echo '<img src="http://maps.googleapis.com/maps/api/staticmap?center='.$track->start_n.','.$track->start_e.'&zoom='.$zoom.'&size='.$width.'x'.$heigth.'&maptype='.$map.'&markers=color:'.$color.'|'.$track->start_n.','.$track->start_e.($apikey? '&key='.$apikey: '').'&sensor=false" >';
		echo '<div align="center"><a href="'.$link.'">'.$track->title.'</a></div>';
		if ($tcustom_enable)
		{
		    echo $tcustom;
		}
		else
		{
		    if ( ($params->get('cats') != 0 ) and ($track->cat) )
		    {
			echo '<div align="center">'.sprintf($params->get('tcats'),$track->cat).'</div>';
		    }
		    if($params->get('distance') != 0)
		    {
			echo '<div align="center">'.sprintf($params->get('tdistance'),$distance_mi_or_km).'</div>';
		    }
		}

		echo '</div>';
	}
    elseif($params->get('style') == 1)
	{
	    echo '<table><tr>';
	    $link = JRoute::_('index.php?option=com_jtg&view=files&layout=file&id='.$track->id);
	    echo '<td align="center" style="padding: 10px" valign="top">';
	    echo '<img src="http://maps.googleapis.com/maps/api/staticmap?center='.$track->start_n.','.$track->start_e.'&zoom='.$zoom.'&size='.$width.'x'.$heigth.'&maptype='.$map.'&markers=color:'.$color.'|'.$track->start_n.','.$track->start_e.($apikey? '&key='.$apikey: '').'&sensor=false" >';
	    echo '<div align="center"><a href="'.$link.'">'.$track->title.'</a></div>';
	    if ($tcustom_enable)
	    {
		echo $tcustom;
	    }
	    else
	    {
		if ( ($params->get('cats') != 0 ) and ($track->cat) )
		{
		    echo '<div align="center">'.sprintf($params->get('tcats'),$track->cat).'</div>';
		}
		if($params->get('distance') != 0)
		{
		    echo '<div align="center">'.sprintf($params->get('tdistance'),$distance_mi_or_km).'</div>';
		}
	    }

	    echo '</td>';
	    echo '</tr></table>';
	}
    }
?>
