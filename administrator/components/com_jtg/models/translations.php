<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 * 
 * @package    Comjtg
 * @author     Christophe Seguinot <christophe@jtrackgallery.net>
 * @copyright  2013 J!Track Gallery, InJooosm and joomGPStracks teams
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport('joomla.application.component.model');
/**
 * Model Class Terrain
 */
class JtgModelTranslations extends JModelLegacy
{
	function __construct() {
		parent::__construct();
	}

	function saveLanguage() {
		jimport('joomla.filesystem.file');
		JSession::checkToken() or die( 'Invalid Token' );
		$languages = $this->getRawLanguages();
		foreach ($languages as $lang) {
			$file = JPATH_SITE . '/images/jtrackgallery/language/' . $lang['tag'] . DIRECTORY_SEPARATOR . $lang['tag'] . DIRECTORY_SEPARATOR ."com_jtg_additional.ini";
			$inhalt = JFactory::getApplication()->input->get( $lang['tag'] );
			if(!JFile::write( $file, $inhalt ))
			return false;
		}
		return true;
	}

	function getRawLanguages() {
		$language = JFactory::getLanguage();
		$languages = $language->getKnownLanguages();
		return $languages;
	}

	function getLanguages() {
		jimport('joomla.filesystem.file');
		$languages = $this->getRawLanguages();
		$newlanguages = array();
		foreach ($languages as $lang) {
			$rows = 5;
			$cols = 10;
			$newlanguages[$lang['tag']] = array();
			$newlanguages[$lang['tag']]['name'] = $lang['name'];
			$newlanguages[$lang['tag']]['tag'] = $lang['tag'];
			$path = JPATH_SITE . '/images/jtrackgallery/language/' . $lang['tag'] . DIRECTORY_SEPARATOR;
			$file = $path . $lang['tag'] . DIRECTORY_SEPARATOR ."com_jtg_additional.ini";
			$newlanguages[$lang['tag']]['file'] = $file;
			if (!JFile::exists($file))
			{
				$buffer="; These are additional translation strings added by users
; They may be used in Front-end AND Back-end
";
				$iswritable = JPath::getPermissions($path);
				$iswritable = $iswritable[1];
				if ( ( JPath::canChmod($path) ) AND  ( $iswritable == "w" ) )
				{
					JFile::write( $file, $buffer );
				}
			}
			if (JFile::exists($file))
			{
				$content = file_get_contents($file);
				$text = explode("\n",$content);
				foreach ($text as $val) { // find out max line lengh
					if ( strlen($val) > $cols)
					$cols = strlen($val);
				}
				if (1 == 1)// ( JPath::canChmod($file) )
				{
					$header_color = "green";
					$header_desc = JText::_('COM_JTG_WRITABLE');
				} else {
					$header_color = "red";
					$header_desc = JText::_('COM_JTG_UNWRITABLE');
				}
			} else {
				if ( ( JPath::canChmod($path) ) AND  ( $iswritable == "w" ) )
				{
					$header_color = "green";
					$header_desc = JText::_('COM_JTG_WRITABLE');
				} else {
					$header_color = "red";
					$header_desc = JText::_('COM_JTG_UNWRITABLE');
				}
				$content = JText::_('COM_JTG_UNWRITABLE');
			}
			$newlanguages[$lang['tag']]['header'] = $lang['name'] . "<br /><font color=\"" . $header_color . "\"><small>(" . $header_desc . ")</small></font>";
			$size = substr_count($content,"\n");
			$rows = $size + $rows;
			$newlanguages[$lang['tag']]['rows'] = $rows;
			$newlanguages[$lang['tag']]['cols'] = $cols + 2;
			$newlanguages[$lang['tag']]['value'] = $content;
		}
		return $newlanguages;
	}
}
