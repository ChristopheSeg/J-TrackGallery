<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @author      Christophe Seguinot <christophe@jtrackgallery.net>
 * @author      Pfister Michael, JoomGPStracks <info@mp-development.de>
 * @author      Christian Knorr, InJooOSM  <christianknorr@users.sourceforge.net>
 * @copyright   2015 J!TrackGallery, InJooosm and joomGPStracks teams
 *
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link        http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

define('_parseTemplate_headline_open', true);

/**
 * function_description
 *
 * @param   unknown_type  $linkname  param_description
 *
 * @return string
 */
function parseTemplate_headline_open($linkname)
{
	$link = JFactory::getURI()->toString() . "#" . $linkname;
	$link = str_replace("&", "&amp;", $link);

	return "<div class=\"gps-headline\"><a class=\"anchor\" name=\"" . $linkname . "\" href=\"" . $link . "\">";
}
