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

define ('_parseTemplate_headline_close', true);
function parseTemplate_headline_close($linkname) {
	return "</a></div>";
}