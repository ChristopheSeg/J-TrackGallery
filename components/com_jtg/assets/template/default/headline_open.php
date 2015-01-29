<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @author       J!Track Gallery, InJooosm and joomGPStracks teams
 * @package      com_jtg
 * @subpackage  frontend
 * @license      http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link          http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

define ('_parseTemplate_headline_open', true);
function parseTemplate_headline_open($linkname) {
	$link = JFactory::getURI()->toString() . "#" . $linkname;
	$link = str_replace("&","&amp;",$link);
	return "<div class=\"gps-headline\"><a class=\"anchor\" name=\"" . $linkname . "\" href=\"" . $link . "\">";
}