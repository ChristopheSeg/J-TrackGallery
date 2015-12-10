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

		// Control external parameters and set variable for controlling plugin layout within modules


		// ----------------------------------- Prepare the output -----------------------------------

		// Process plugin tags
		if (preg_match_all ( $regex, $row->text, $matches, PREG_PATTERN_ORDER ) > 0) {

			// start the replace loop
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
				$warningtext = '<br>-- id=' . ($plg_params ['id'] ? $plg_params ['id'] : 'null') . ' <br>--gpxfilename=' . ($plg_params ['gpxfilename'] ? $plg_params ['gpxfilename'] : '');

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
						JError::raiseNotice ( '', JText::_ ( 'PLG_JTG_TRACK_NOT_FOUND' ) . "($warningtext)" );
						$plg_params ['id'] = 0;
					}
				}

				if ($plg_params ['id'] > 0) {
					// Generate the html code for the map
					$plg_html = $plg_copyrights_start;
					$plg_html .= $this->rendermap($plg_params);
					$plg_html .= $plg_copyrights_end;
					// Do the replace
					$row->text = str_replace ( $match, $plg_html, $row->text );
				} else {
					// Remove tag?
					$plg_html = $plg_copyrights_start;
					$plg_html .= "<br>--ERROR PLG_JTRACKGALLERY--<br>" .
						' id=' . ($plg_params['id'] ? $plg_params['id'] : 'null') .
						' gpxfilename=' . ($plg_params ['gpxfilename'] ? $plg_params ['gpxfilename'] : '');
					$plg_html .= $plg_copyrights_end;

					// Do the replace
					$row->text = str_replace ( $match, $plg_html, $row->text );
				}

			} // end foreach

		} // end if
	} // END FUNCTION
	private function rendermap($plg_params)
	{
		$map="<br>THIS IS A MAP<br>";
		return $map;
	}

} // END CLASS
