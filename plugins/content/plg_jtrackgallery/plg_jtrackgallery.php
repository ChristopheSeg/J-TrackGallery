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
defined ( '_JEXEC' ) or die ( 'Restricted access' );

jimport ( 'joomla.plugin.plugin' );
if (version_compare ( JVERSION, '1.6.0', 'ge' )) {
	jimport ( 'joomla.html.parameter' );
}
class plgContentPlg_jtrackgallery extends JPlugin {

	function onContentPrepare($context, &$row, &$params, $page = 0) {
		$this->renderJtrackGalleryPlugin ( $row, $params, $page = 0 );
	}

	// The main function
	private function renderJtrackGalleryPlugin(&$row, &$params, $page = 0) {
		// API
		jimport ( 'joomla.filesystem.file' );
		$mainframe = JFactory::getApplication ();
		$document = JFactory::getDocument ();

		// Assign paths
		$plg_name = "plg_jtrackgallery";
		$plg_tag = "JTRACKGALLERY";
		$plg_copyrights_start = "\n\n<!-- J!TrackGallery \"plg_jtrackgallery\" Plugin (v0.9) starts here -->\n";
		$plg_copyrights_end = "\n<!-- J!TrackGallery \"plg_jtrackgallery\" Plugin (v0.9) ends here -->\n\n";

		$sitePath = JPATH_SITE;
		$siteUrl = JURI::root ( true );
		$pluginLivePath = $siteUrl . '/plugins/content/' . $plg_name;

		// Check if plugin is enabled
		if (JPluginHelper::isEnabled ( 'content', $plg_name ) == false)
			return;

		// Check
		if (!JComponentHelper::isEnabled('com_jtg', true)) {
			return JError::raiseError(JText::_('PLG_JTG_COM_JTG_NOT_INSTALLED'));
		}

		// Bail out if the page format is not what we want
		$allowedFormats = array (
				'',
				'html'
		);
		if (! in_array ( JRequest::getCmd ( 'format' ), $allowedFormats ))
			return;

			// Simple performance check to determine whether plugin should process further
		if (JString::strpos ( $row->text, $plg_tag ) === false)
			return;

			// expression to search for
		$regex = "#{" . $plg_tag . "}(.*?){/" . $plg_tag . "}#is";

		// Find all instances of the plugin and put them in $matches
		preg_match_all ( $regex, $row->text, $matches );

		// Number of plugins
		$count = count ( $matches [0] );

		// Plugin only processes if there are any instances of the plugin in the text
		if (! $count)
			return;

			// Load the plugin language file the proper way
		JPlugin::loadLanguage ( 'plg_content_' . $plg_name, JPATH_ADMINISTRATOR );

		// Check for basic requirements
		$db = JFactory::getDBO ();

		// ----------------------------------- Get plugin parameters -----------------------------------

		// Get plugin info
		$plugin = JPluginHelper::getPlugin ( 'content', $plg_name );

		// ----------------------------------- Prepare the output -----------------------------------

		// Process plugin tags
		if (preg_match_all ( $regex, $row->text, $matches, PREG_PATTERN_ORDER ) > 0) {

			// Start the replace loop
			$map_count = 0;
			foreach ( $matches [0] as $key => $match ) {

				$plg_params = array (
						"id" => 0,
						"gpxfilename" => ''
				);
				$tagcontent = preg_replace ( "/{.+?}/", "", $match );
				$tagparams = explode ( ',', strip_tags ( $tagcontent ) );

				foreach ( $tagparams as $tagparam ) {
					$temp = explode ( '=', $tagparam );
					$plg_params [trim ( $temp [0] )] = trim ( $temp [1] );
				}
				$plg_params ['id'] = ( int ) $plg_params ['id'];
				$warningtext = ' id=' . ($plg_params ['id'] ? $plg_params ['id'] : 'null') . ' gpxfilename=' . ($plg_params ['gpxfilename'] ? $plg_params ['gpxfilename'] : '') ;

				if ((! $plg_params ['id'] > 0) and (! $plg_params ['gpxfilename'])) {
					JError::raiseNotice ( '', JText::_ ( 'PLG_JTG_TRACK_NOT_SPECIFIED' ) . "()" );
				}
				// Test if given id or filename correspond to one track in database
				if ($plg_params ['gpxfilename']) {
					// Determine the id of the filename
					$query = "SELECT id FROM `#__jtg_files` WHERE file='" . $plg_params ['gpxfilename'] . "'";
					if ($plg_params ['id'] > 0) {
						$query .= " or id=" . $plg_params ['id'];
					}
					$db->setQuery ( $query );
					$db->execute ();
					$ids = $db->loadObjectList ();

					if (count ( $ids ) > 0 and ( int ) $ids [0]->id > 0)
					{
						$plg_params ['id'] = ( int ) $ids [0]->id;
					}
					else
					{
						JError::raiseNotice ( '', JText::_ ( 'PLG_JTG_TRACK_NOT_FOUND' ) . " ($warningtext)" );
						$plg_params ['id'] = 0;
					}
				}

				$plg_html = $plg_copyrights_start;

				if ($plg_params ['id'] > 0)
				{
					// Generate the html code for the map
					$map_count += 1;
					if ($map_count <2)
					{
						$plg_html .= $this->rendermap($plg_params);
					}
					else
					{
						JError::raiseNotice ( '', JText::_ ( 'PLG_JTG_CANT_RENDER_TRACKS' ));
						$plg_html .= JText::_ ( 'PLG_JTG_CANT_RENDER_TRACKS' );
					}

				} else {
					$plg_html .= JText::_ ( 'PLG_JTG_TRACK_NOT_FOUND' ) . " ($warningtext)" ;
				}
				$plg_html .= $plg_copyrights_end;
				// Do the replace
				$row->text = str_replace ( $match, $plg_html, $row->text );

			}

		}
	}

	private function rendermap($plg_params)
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::base(true) . '/components/com_jtg/template.css');
		$document->addStyleSheet('http://dev.openlayers.org/theme/default/style.css');

		// Add jtg_map stylesheet
		require_once JPATH_SITE . '/components/com_jtg/helpers/helper.php';
		$cfg = JtgHelper::getConfig();
		$tmpl = ($cfg->template <> "") ? $cfg->template : 'default';
		$document->addStyleSheet(JUri::root(true) . '/components/com_jtg/assets/template/' . $tmpl . '/jtg_map_style.css');
		$map = "";

		// Load english language file for 'com_jtg' component then override with current language file
		JFactory::getLanguage()->load('com_jtg_common', JPATH_SITE . '/components/com_jtg',	null, true);

		// Com_jtg_additional language files are in /images/jtrackgallery/language folder
		JFactory::getLanguage()->load('com_jtg_additional', JPATH_SITE . '/images/jtrackgallery',	null, true);


		// Edit file
		$cache = JFactory::getCache('plg_jtrackgallery');
		$params = JComponentHelper::getParams('com_jtg');

		require_once JPATH_SITE . '/components/com_jtg/models/files.php';
		$model = JModelLegacy::getInstance( 'Files', 'JtgModel' );
		$track = $cache->get(array($model, 'getFile'), array($plg_params['id']));
		$document = JFactory::getDocument();
		require_once JPATH_SITE . '/components/com_jtg/helpers/gpsClass.php';
		$document->addScript('http://www.openlayers.org/api/OpenLayers.js');
		$document->addScript( JUri::root(true) . '/components/com_jtg/assets/js/fullscreen.js');
		$document->addScript('http://www.openstreetmap.org/openlayers/OpenStreetMap.js');
		$document->addScript( JUri::root(true) . '/components/com_jtg/assets/js/jtg.js');
		$file = JPATH_SITE . '/images/jtrackgallery/uploaded_tracks/' . $track->file;
		$gpsData = new GpsDataClass($cfg->unit);
		$gpsData = $cache->get(array ( $gpsData, 'loadFileAndData' ), array ($file, $track->file ), $cfg->unit);

		if ($gpsData->displayErrors())
		{
			$map = "";
		}
		else
		{
			$map = $cache->get(array ( $gpsData, 'writeTrackOL' ), array ( $track, $params ));
			$map.= '<style type="text/css">

.olButton::before{
	display: none;
}
#jtg_map img{
	max-width: none; /* joomla3 max-width=100% breaks popups*/
}

/* Fix Bootstrap-Openlayers issue */
.olMap img { max-width: none !important;
}

img.olTileImage {
	max-width: none !important;
}
</style>';
		$map_width = isset ($plg_params ['map_width'])? $plg_params ['map_width']: $cfg->map_width;
		$map_height = isset ($plg_params ['map_height'])? $plg_params ['map_height']: $cfg->map_height;

		$map .= ("\n<div id=\"jtg_map\"  align=\"center\" class=\"olMap\" ");
		$map .= ("style=\"width: $map_width; height: $map_height; background-color:#EEE; vertical-align:middle;\" >");
		$map .= ("\n<script>slippymap_init();</script>");
		$map .= ("\n</div>");
	}

	return $map;
}

} // END CLASS
