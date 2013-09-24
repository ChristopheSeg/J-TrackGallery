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
	    "images" . DS . "jtrackgallery" . DS . "uploads",
	    "images" . DS . "jtrackgallery" . DS . "uploads" . DS . "import"
		);

	    $folders_to_chmod = array (
	    "images" . DS . "jtrackgallery" . DS . "uploads",
	    "images" . DS . "jtrackgallery" . DS . "uploads" . DS . "import",
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
	    
	    echo '<p>' . JText::_('COM_JTG_INSTALLED',$this->release) . '</p>';
	    // You can have the backend jump directly to the newly installed component configuration page
	    // $parent->getParent()->setRedirectURL('index.php?option=com_jtg');

	    echo('<font color="red" size="+1">' . JText::_('COM_JTG_HINTS') . '</font>');
	    echo(JText::_('COM_JTG_HINTS_DETAILS'));  
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
		// installed version is lower then 0.7.0
		// do some stuff
	    }    
	    echo '<p>' . JText::_('COM_JTG_UPDATED', $this->release) . '</p>';
		
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
 
		// echo '<p>' . JText::_('COM_JTG_POSTFLIGHT ' . $type . ' to ' . $this->release) . '</p>';
		return true;
	}

	/*
	 * $parent is the class calling this method
	 * uninstall runs before any other action is taken (file removal or database processing).
	 */
	function uninstall( $parent ) {
	    // Set a simple message

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
