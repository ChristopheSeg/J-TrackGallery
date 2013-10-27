<?php

/**
 *    @component  J!Track Gallery (jtg) for Joomla! 2.5
 * 
 *    @package    Com_Jtg
 *    @subpackage Backend
 *    @author     J!Track Gallery, InJooOSM and joomGPStracks teams <christophe@jtrackgallery.net>
 *    @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 *    @link       http://jtrackgallery.net/
 *
 */

// this file is based on Joomla script.php and corresponding com_flexicontent install script

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
//the name of the class must be the name of your component + InstallerScript
class com_jtgInstallerScript
{
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 */
	function preflight( $type, $parent ) {

		// Try to increment some limits
		@set_time_limit( 240 );    // execution time 5 minutes
		ignore_user_abort( true ); // continue execution if client disconnects
		//
		// language is not loaded at preflight time!!
		// load english language file for 'com_jtg' component then override with current language file
		JFactory::getLanguage()->load('com_jtg',   JPATH_ADMINISTRATOR, 'en-GB', true);
		JFactory::getLanguage()->load('com_jtg',   JPATH_ADMINISTRATOR,    null, true);		
		$jversion = new JVersion();

		// Installing component manifest file version
		$this->release = $parent->get( "manifest" )->version;
		
		// File version of existing manifest file
		$this->release_existing = $this->getParam('version');
		
		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;   
		echo '<p> -- ' . JText::sprintf('COM_JTG_PREFLIGHT',$this->release) .'</p>';
		if ($this->release_existing) {
		    
		    echo '<br /> &nbsp; ' . JText::sprintf('COM_JTG_PREFLIGHT_UPDATING',$this->release_existing,$this->release);
		} else {
		    echo '<br /> &nbsp; ' . JText::sprintf('COM_JTG_PREFLIGHT_INSTALLING',$this->release);
		}
		echo '<br /> &nbsp; ' . JText::sprintf('COM_JTG_PREFLIGHT_MIN_JOOMLA', $this->minimum_joomla_release ,$jversion->getShortVersion());

		// abort if the current Joomla release is older
		if( version_compare( $jversion->getShortVersion(), $this->minimum_joomla_release, 'lt' ) ) {
			Jerror::raiseWarning(null, JText::sprintf('COM_JTG_PREFLIGHT_MIN_JOOMLA_ABORT',$this->minimum_joomla_release) );
			return false;
		}
 
		// abort if the component being installed is not newer than the currently installed version
		if ( $type == 'update' ) {
			$oldRelease = $this->getParam('version');
			if ( version_compare( $this->release, $oldRelease, 'le' ) ) {
				Jerror::raiseWarning(null, JText::sprintf('COM_JTG_PREFLIGHT_JTG_WRONG_VERSION', $oldRelease, $this->release) );
				return false;
				// TODO this aborts the install process but generates and error in Joomla !!!
			}
		}
		return true;
 
	}
 
	/*
	 * $parent is the class calling this method.
	 * install runs after the database scripts are executed.
	 * If the extension is new, the install method is run.
	 * If install returns false, Joomla will abort the install and undo everything already done.
	 */
	function install( $parent ) {

	    jimport('joomla.filesystem.folder');
	    jimport('joomla.filesystem.file');
	    $doc =& JFactory::getDocument();

	    ?>
	    <br />
	    <img src="<?php echo  DS . 'components' . DS . 'com_jtg' . DS . 'assets' . DS . 'images' . DS . 'logo_JTG.png'; ?>"  alt="J!Track Gallery" />
	    <br />
	    <table class="adminlist" border="1" width="100%">
		    <tbody>
			    <tr><td><?php JText::_('COM_JTG_INSTALL_LICENCE') ?></td></tr>
			    <tr><td><?php JText::_('COM_JTG_CREATE_FOLDERS') ?></td></tr>
		    </tbody>
	    </table>
	    <?php
	    $folders_to_create = array (
	    "images" . DS . "jtrackgallery",
	    "images" . DS . "jtrackgallery" . DS . "cats",
	    "images" . DS . "jtrackgallery" . DS . "terrain",
	    "images" . DS . "jtrackgallery" . DS . "uploaded_tracks",
	    "images" . DS . "jtrackgallery" . DS . "uploaded_tracks" . DS . "import"
		);

	    $folders_to_chmod = array (
	    "images" . DS . "jtrackgallery" . DS . "uploaded_tracks",
	    "images" . DS . "jtrackgallery" . DS . "uploaded_tracks" . DS . "import",
	    "components" . DS . "com_jtg" . DS . "assets" . DS . "images" . DS . "symbols",
	    );

	    echo '<table><tr><td colspan="3"><b>'.JText::_('COM_JTG_FILES_FOLDERS_TO_CREATE').'</td></tr>';
	    echo '<tr><td>'.JText::_('COM_JTG_FILE').'/'.JText::_('COM_JTG_FOLDER').'</td><td>'.JText::_('COM_JTG_NAME').'</td><td>'.JText::_('COM_JTG_STATE').'</td></tr>';

	    foreach ( $folders_to_create AS $folder ) {
		    if(JFolder::exists(JPATH_SITE . DS . $folder))
			    echo '<tr><td>' . JText::_('COM_JTG_FOLDER') . '</td><td>' .
			    $folder . '</td><td><font color="green">' . 
			    JText::_('COM_JTG_ALREADY_EXISTS')  . '</font></td></tr>';
		    elseif(JFolder::create(JPATH_SITE . DS . $folder)) {
			    echo '<tr><td>' . JText::_('COM_JTG_FOLDER') . '</td><td>' .
			    $folder . '</td><td><font color="green">' . 
			    JText::_('COM_JTG_CREATED')  . '</font></td></tr>';
		    } else {
			    echo '<tr><td>' . JText::_('COM_JTG_FOLDER') . '</td><td>' .
			    $folder . '</td><td><font color="red">' . 
			    JText::_('COM_JTG_NOT_CREATED')  . '</font></td></tr>';
		    }
	    }
	
	    foreach ( $folders_to_chmod AS $folder ) {
		    ;
		    if ( JPath::canChmod(JPATH_SITE . DS . $folder) AND (chmod(JPATH_SITE . DS . $folder, 0777))) {
			    echo '<tr><td>' . JText::_('COM_JTG_FOLDER') . '</td><td>' .
			    $folder . '</td><td><font color="green">' . 
			    JText::_('COM_JTG_CHMODDED')  . '</font></td></tr>';
		    } else {
			    echo '<tr><td>' . JText::_('COM_JTG_FOLDER') . '</td><td>' .
			    $folder . '</td><td><font color="red">' . 
			    JText::_('COM_JTG_NOT_CHMODDED')  . '</font></td></tr>';
		    }
	    }

	    // copy Cats image
	    $src_folder_to_copy =  JPATH_SITE . DS . 'components' . DS . 'com_jtg' . DS . 'assets' . DS . 'images' . DS . 'cats';
	    $dest_folder_to_copy = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'cats';
	    $files = JFolder::files($src_folder_to_copy);

	    // copy file by file without erasing existing files
	    foreach ($files as $file) {
	       if (!JFile::exists($dest_folder_to_copy . DS.  $file) ) {
		   JFile::copy($src_folder_to_copy. DS . $file, $dest_folder_to_copy . DS.  $file);
	       }
	    }
	    
	    // copy example tracks
	    $src_folder_to_copy =  JPATH_SITE . DS . 'components' . DS . 'com_jtg' . DS . 'assets' . DS . 'sample_tracks';
	    $dest_folder_to_copy = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'uploaded_tracks';
	    $files = JFolder::files($src_folder_to_copy);

	    // copy file by file without erasing existing files
	    foreach ($files as $file) {
	       if (!JFile::exists($dest_folder_to_copy . DS.  $file) ) {
		   JFile::copy($src_folder_to_copy. DS . $file, $dest_folder_to_copy . DS.  $file);
	       }
	    }
	    
	    echo '<tr><td colspan="3">' . JText::sprintf('COM_JTG_INSTALLED_VERSION',$this->release) . '</td></tr>';
	    // You can have the backend jump directly to the newly installed component configuration page
	    // $parent->getParent()->setRedirectURL('index.php?option=com_jtg');
	    
	    //TODO check if is this remains usefull ??
	    echo '<tr><td colspan="3">';
	    echo('<font color="red" size="+1">' . JText::_('COM_JTG_HINTS') . '</font>');
	    echo(JText::_('COM_JTG_HINTS_DETAILS')); 
	    echo '</td></tr>';
	    echo '</table>';
	    return true;
	}
 
	/*
	 * $parent is the class calling this method.
	 * update runs after the database scripts are executed.
	 * If the extension exists, then the update method is run.
	 * If this returns false, Joomla will abort the update and undo everything already done.
	 */
	function update( $parent ) {
			    
	    // this is executed after upgrade.sql
	    // upgrading from $oldRelease to $this->release
	    if ( version_compare( $oldRelease, '0.7.0', '<' ) ) {
		// installed version is lower then 0.7.0 ==> do some stuff
	    }    
	    echo '<p>' . JText::sprintf('COM_JTG_UPDATED', $this->release) . '</p>';
		
	    // You can have the backend jump directly to the newly updated component configuration page
	    // $parent->getParent()->setRedirectURL('index.php?option=com_jtg');
	    return true;
	}
 
	/*
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * postflight is run after the extension is registered in the database.
	 */
	function postflight( $type, $parent ) {
 
		// uninstall postflight JTEXT string must be saved in en-GB.com_jtg.sys.ini
		// TODOTEMP TEST add a record in  #__jtg_users
		$db =& JFactory::getDBO();
		$application = JFactory::getApplication();
		$query = 'INSERT INTO #__jtg_temp (method, version) VALUES ("UPDATE function","'.
			$oldRelease.'==>'.$this->release.'") ';
		$application->enqueueMessage( $query) ; //TODOTEMP
		$db->setQuery($query);
		$db->query();
		// TODOTEMP TEST add a record in  #__jtg_users

		// check if com_jtg is installed, 
		$query = 'SELECT COUNT(*) FROM #__extensions WHERE name = "com_jtg"';
		$application->enqueueMessage( $query) ; //TODOTEMP
		$db->setQuery($query);
		$db->query();
		$componentJtgIsInstalled = $db->loadResult();		
		$application->enqueueMessage('$componentJtgIsInstalled='. ($componentJtgIsInstalled? 'YES':'NO')) ; //TODOTEMP
		if (( $type == 'install' ) and (! $componentJtgIsInstalled) ) 
		{
		    // this a NON successfull install: 
		    // TODO truncate all com_jtg tables
		    // DROP TABLE `crl05_jtg_cats`, `crl05_jtg_cats2`, `crl05_jtg_comments`, `crl05_jtg_config`, `crl05_jtg_files`, `crl05_jtg_files2`, `crl05_jtg_maps`, `crl05_jtg_terrains`, `crl05_jtg_users`, `crl05_jtg_votes`;
		    $application->enqueueMessage( 'SHOULD TRUNCATE ALL TABLES' ) ;
		    // DROP TABLE `dhwlt_jtg_cats`, `dhwlt_jtg_comments`, `dhwlt_jtg_config`, `dhwlt_jtg_files`, `dhwlt_jtg_maps`, `dhwlt_jtg_temp`, `dhwlt_jtg_terrains`, `dhwlt_jtg_users`, `dhwlt_jtg_votes`;
		}

		if (( $type == 'install' ) and ($componentJtgIsInstalled !== null) )  
		{
		    // this is a successful install (not an upgrade): 
		    // affect sample tracks to this admin user
		    $user =& JFactory::getUser();
		    $query = 'UPDATE #__jtg_files SET uid ='. $user->get('id') . ' WHERE uid =430';
		    $application->enqueueMessage( $query) ; //TODOTEMP
		    $db->setQuery($query);
		    $db->query();
		    // save default params
		    $query = 'UPDATE #__extensions SET params = '. 
		    $query.= ' {"jtg_param_newest":"10","jtg_param_mostklicks":"10",
			"jtg_param_best":"0","jtg_param_rand":"0",
			"jtg_param_otherfiles":"0",
			"jtg_param_lh":"1",
			"jtg_param_vote_show_stars":"0",
			"jtg_param_show_speedchart":"1",
			"jtg_param_show_heightchart":"1",
			"jtg_param_show_durationcalc":"1",
			"jtg_param_show_layerswitcher":"1",
			"jtg_param_show_panzoombar":"1",
			"jtg_param_show_attribution":"1",
			"jtg_param_show_mouselocation":"1",
			"jtg_param_show_scale":"1",
			"jtg_param_allow_mousemove":"1",
			"jtg_param_allow_keymove":"0",
			"jtg_param_offer_download_gpx":"1",
			"jtg_param_offer_download_kml":"0",
			"jtg_param_offer_download_tcx":"0",
			"jtg_param_tracks":"0",
			"jtg_param_cats":["-1"],
			"jtg_param_user":["0"],
			"jtg_param_usergroup":["-1"],
			"jtg_param_terrain":["-1"],
			"jtg_param_level_from":"0",
			"jtg_param_level_to":"5",
			"jtg_param_vote_from":"0",
			"jtg_param_vote_to":"10"}';
		    $query.= ' WHERE name = "com_jtg" AND type = "Component"'; 
		    $db->setQuery($query);
		    $db->query();
		 }
		    return true;
	}

	/*
	 * $parent is the class calling this method
	 * uninstall runs before any other action is taken (file removal or database processing).
	 */
	function uninstall( $parent ) {
	    // Set a simple message

	    // uninstall JTEXT strings must be saved in en-GB.com_jtg.sys.ini
	    $application = JFactory::getApplication();
	    $application->enqueueMessage( JText::_('COM_JTG_THANK_YOU_FOR_USING') ) ;
	    echo '<p>' . JText::_('COM_JTG_UNINSTALLING') . '</p>';
	    $folders_to_delete = array (
	    "images" . DS . "jtrackgallery"
	    );

	    $files_to_delete = array ();
	    
	    echo '<table><tr><td colspan="3"><b>'.JText::_('COM_JTG_FILES_FOLDERS_TO_DELETE').'</td></tr>';
	    echo '<tr><td>'.JText::_('COM_JTG_FILE').'/'.JText::_('COM_JTG_FOLDER').'</td><td>'.JText::_('COM_JTG_NAME').'</td><td>'.JText::_('COM_JTG_STATE').'</td></tr>';
	    foreach ( $files_to_delete AS $file ) {	    
		    if(!JFile::exists($file)) {
		    echo '<tr><td>'.JText::_('COM_JTG_FILE').'</td><td>' . $file . '</td><td><font color="green">' . JText::_('COM_JTG_NOT_EXISTS') .' "</font>"  </td></tr>';}
		    elseif(JFile::delete($file)) {
			    echo '<tr><td>'.JText::_('COM_JTG_FILE').'</td><td>' . $file . '</td><td><font color="green">' . JText::_('COM_JTG_DELETED') .' "</font>" </td></tr>';
		    } else {
			    echo '<tr><td>'.JText::_('COM_JTG_FILE').'</td><td>' . $file . '</td><td><font color="red">' . JText::_('COM_JTG_ERROR') .'/'. JText::_('COM_JTG_NOT_DELETED'). '"</font>" </td></tr>';
		    }
	    }
	    foreach ( $folders_to_delete AS $folder ) {
		    if(!JFolder::exists(JPATH_SITE . DS . $folder)){
			    echo '<tr><td>'.JText::_('COM_JTG_FOLDER').'</td><td>' . $folder . '</td><td><font color="green">' . JText::_('COM_JTG_NOT_EXISTS') . '"</font>" </td></tr>';}
		    elseif(JFolder::delete(JPATH_SITE . DS . $folder)) {
			    echo '<tr><td>'.JText::_('COM_JTG_FOLDER').'</td><td>' . $folder . '</td><td><font color="green">' .JText::_('COM_JTG_DELETED') . '"</font>" </td></tr>';
		    } else {
			    echo '<tr><td>'.JText::_('COM_JTG_FOLDER').'</td><td>' . $folder . '</td><font color="red">' . JText::_('COM_JTG_ERROR') . JText::_('COM_JTG_NOT_DELETED') . '"</font>" </td></tr>';
		    }
	    }
	    echo '</table>';

	    return true;
	}
 
	/*
	 * get a variable from the manifest file (actually, from the manifest cache).
	 */
	function getParam( $name ) {
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_jtg"');
		$manifest = json_decode( $db->loadResult(), true );
		return $manifest[ $name ];
	}
 
	/*
	 * sets parameter values in the component's row of the extension table
	 */
	function setParams($param_array) {
		if ( count($param_array) > 0 ) {
			// read the existing component value(s)
			$db = JFactory::getDbo();
			$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_jtg"');
			$params = json_decode( $db->loadResult(), true );
			// add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value ) {
				$params[ (string) $name ] = (string) $value;
			}
			// store the combined new and existing values back as a JSON string
			$paramsString = json_encode( $params );
			$db->setQuery('UPDATE #__extensions SET params = ' .
				$db->quote( $paramsString ) .
				' WHERE name = "com_jtg"' );
				$db->query();
		}
	}
}
?>
