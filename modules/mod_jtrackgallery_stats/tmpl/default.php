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
?>
<div style="padding-left:10px">
<?php

if ($tcustom_enable) 
{
	// Use custom display, replace each data when needed
	$tcustom = str_replace('$cats', $cats, $tcustom);
	$tcustom = str_replace('$tracks', $tracks, $tcustom);
	$tcustom = str_replace('$distance_km', $distance_km, $tcustom);
	$tcustom = str_replace('$distance_mi', $distance_mi, $tcustom);
	$tcustom = str_replace('$ascent', $ascent, $tcustom);
	$tcustom = str_replace('$descent', $descent, $tcustom);
	$tcustom = str_replace('$views', $views, $tcustom);
	$tcustom = str_replace('$votes', $votes, $tcustom);
	echo $tcustom; 
}    
else
{
	if ($theado == "1")
	{
	    echo "<div>"; echo JText::_($thead); echo "</div>"; 
	}
	echo '<ul>'; 
	if ($tcato == "1") 
	{
	    echo "<li>"; echo sprintf($tcat, $cats); echo "</li>"; 
	}
	if ($ttracko == "1") 
	{
	    echo "<li>"; echo sprintf($ttrack, $tracks); echo "</li>"; 
	}
	if ($tdiso == "1") 
	{
	    echo "<li>"; echo sprintf($tdis, $distance); echo "</li>"; 
	}
	if ($tasco == "1") 
	{
	    echo "<li>"; echo sprintf($tasc, $ascent); echo "</li>";
	}
	if ($tdeco == "1") 
	{
	    echo "<li>"; echo sprintf($tdec, $descent); echo "</li>";
	}
	if ($tviewo == "1") 
	{
	    echo "<li>"; echo sprintf($tview, $views); echo "</li>";
	}
	if ($tvoteo == "1") 
	{
	    echo "<li>"; echo sprintf($tvote, $votes); echo "</li>"; 
	}
	echo '</ul>'; 
}
?>
</div>