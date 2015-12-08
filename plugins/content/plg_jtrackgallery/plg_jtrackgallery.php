<?php
/**
 * @version		0.9
 * @package		J!TrackGallery plugin plg_jtrackgallery
 * @author    	Christophe Seguinot - http://jtrackgallery.net
 * @copyright
 * @license		GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 *
 * This plugin in inspired from Simple Image Gallery (plugin)
 * developped by JoomlaWorks - http://www.joomlaworks.net
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
if (version_compare(JVERSION, '1.6.0', 'ge')){
	jimport('joomla.html.parameter');
}

class plgContentPlg_jtrackgallery extends JPlugin {

  // JoomlaWorks reference parameters
	var $plg_name					= "plg_jtrackgallery";
	var $plg_tag					= "JTRACKGALLERY";
	var $plg_copyrights_start		= "\n\n<!-- J!TrackGallery \"plg_jtrackgallery\" Plugin (v0.9) starts here -->\n";
	var $plg_copyrights_end			= "\n<!-- J!TrackGallery \"plg_jtrackgallery\" Plugin (v0.9) ends here -->\n\n";

	function plgContentPlg_jtrackgallery( &$subject, $params ){
		parent::__construct( $subject, $params );

		// Define the DS constant under Joomla! 3.0+
		if (!defined('DS')){
			define('DS', DIRECTORY_SEPARATOR);
		}
	}

	// Joomla! 1.5
	function onPrepareContent(&$row, &$params, $page = 0){
		$this->renderJtrackGalleryPlugin($row, $params, $page = 0);
	}

	// Joomla! 2.5+
	function onContentPrepare($context, &$row, &$params, $page = 0){
		$this->renderJtrackGalleryPlugin($row, $params, $page = 0);
	}

	// The main function
	function renderJtrackGalleryPlugin(&$row, &$params, $page = 0){
		// API
		jimport('joomla.filesystem.file');
		$mainframe = JFactory::getApplication();
		$document  = JFactory::getDocument();

		// Assign paths
		$sitePath = JPATH_SITE;
		$siteUrl  = JURI::root(true);
		if (version_compare(JVERSION, '1.6.0', 'ge')){
			$pluginLivePath = $siteUrl.'/plugins/content/'.$this->plg_name.'/'.$this->plg_name;
			$defaultImagePath = 'images';
		} else {
			$pluginLivePath = $siteUrl.'/plugins/content/'.$this->plg_name;
			$defaultImagePath = 'images/stories';
		}

		// Check if plugin is enabled
		if (JPluginHelper::isEnabled('content', $this->plg_name) == false) return;

		// Bail out if the page format is not what we want
		$allowedFormats = array('', 'html', 'feed', 'json');
		if (!in_array(JRequest::getCmd('format'), $allowedFormats)) return;

		// Simple performance check to determine whether plugin should process further
		if (JString::strpos($row->text, $this->plg_tag) === false) return;

		// expression to search for
		$regex = "#{".$this->plg_tag."}(.*?){/".$this->plg_tag."}#is";

		// Find all instances of the plugin and put them in $matches
		preg_match_all($regex, $row->text, $matches);

		// Number of plugins
		$count = count($matches[0]);

		// Plugin only processes if there are any instances of the plugin in the text
		if (!$count) return;

		// Load the plugin language file the proper way
		JPlugin::loadLanguage('plg_content_'.$this->plg_name, JPATH_ADMINISTRATOR);

		// Check for basic requirements


		// ----------------------------------- Get plugin parameters -----------------------------------

		// Get plugin info
		$plugin = JPluginHelper::getPlugin('content', $this->plg_name);

		// Control external parameters and set variable for controlling plugin layout within modules
		if (!$params) $params = class_exists('JParameter') ? new JParameter(null) : new JRegistry(null);
		$parsedInModule = $params->get('parsedInModule');

		$pluginParams = class_exists('JParameter') ? new JParameter($plugin->params) : new JRegistry($plugin->params);

		$galleries_rootfolder = ($params->get('galleries_rootfolder')) ? $params->get('galleries_rootfolder') : $pluginParams->get('galleries_rootfolder', $defaultImagePath);
		$popup_engine = 'jquery_fancybox';
		$jQueryHandling = $pluginParams->get('jQueryHandling', '1.8.3');
		$thb_template = 'Classic';
		$thb_width = (!is_null($params->get('thb_width', null))) ? $params->get('thb_width') : $pluginParams->get('thb_width', 200);
		$thb_height = (!is_null($params->get('thb_height', null))) ? $params->get('thb_height') : $pluginParams->get('thb_height', 160);
		$smartResize = 1;
		$jpg_quality = $pluginParams->get('jpg_quality', 80);
		$showcaptions = 0;
		$cache_expire_time = $pluginParams->get('cache_expire_time', 1440) * 60; // Cache expiration time in minutes
		// Advanced
		$memoryLimit = (int)$pluginParams->get('memoryLimit');
		if ($memoryLimit) ini_set("memory_limit", $memoryLimit."M");

		// Cleanups
		// Remove first and last slash if they exist
		if (substr($galleries_rootfolder, 0, 1) == '/') $galleries_rootfolder = substr($galleries_rootfolder, 1);
		if (substr($galleries_rootfolder, -1, 1) == '/') $galleries_rootfolder = substr($galleries_rootfolder, 0, -1);

		// Includes
		//require_once (dirname(__FILE__).DS.$this->plg_name.DS.'includes'.DS.'helper.php');

		// Other assignments
		$transparent = $pluginLivePath.'/includes/images/transparent.gif';

		// When used with K2 extra fields
		if (!isset($row->title)) $row->title = '';

		// Variable cleanups for K2
		if (JRequest::getCmd('format') == 'raw')
		{
			$this->plg_copyrights_start = '';
			$this->plg_copyrights_end = '';
		}

		// ----------------------------------- Prepare the output -----------------------------------

		// Process plugin tags
		if (preg_match_all($regex, $row->text, $matches, PREG_PATTERN_ORDER) > 0){

			// start the replace loop
			foreach ($matches[0] as $key => $match)
			{

				$tagcontent = preg_replace("/{.+?}/", "", $match);
				$plg_html = "<br>--PLG_JTRACKGALLERY--<br>";
				$plg_html .="<br>  $tagcontent";
				$tagparams = explode(',',$tagcontent);
				foreach ($tagparams as $tagparam)
				{
					$temp = explode('=',trim($tagparam));
					$plg_html .= "<br>  %$temp[0]==$temp[1]%";
				}

				// test id or filename correspond to one track in database

				// TEST
				$plg_html .= "<br>--PLG_JTRACKGALLERY--<br>";


				// Do the replace
				$row->text = preg_replace("#{".$this->plg_tag."}".$tagcontent."{/".$this->plg_tag."}#s", $plg_html, $row->text);

			}// end foreach

			// Global head includes
			if (JRequest::getCmd('format') == '' || JRequest::getCmd('format') == 'html'){
				$document->addScript($pluginLivePath.'/includes/js/behaviour.js');
			}

		} // end if

	} // END FUNCTION

} // END CLASS
