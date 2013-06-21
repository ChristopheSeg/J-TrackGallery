<?php

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
define ('_parseTemplate_headline_open', true);
function parseTemplate_headline_open($linkname) {
	$link = JFactory::getURI()->toString() . "#" . $linkname;
	$link = str_replace("&","&amp;",$link);
	return "<div class=\"gps-headline\"><a class=\"anchor\" name=\"" . $linkname . "\" href=\"" . $link . "\">";
}