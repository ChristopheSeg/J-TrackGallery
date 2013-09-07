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
		
		$jversion = new JVersion();

		// Installing component manifest file version
		$this->release = $parent->get( "manifest" )->version;
		
		// File version of existing manifest file
		$this->release_existing = $this->getParam('version');
		
		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get( "manifest" )->attributes()->version;   

		// Show the essential information at the install/update back-end
		echo '<p> -- ' . JText::sprintf('COM_JTG_PREFLIGHT',$this->release_existing) .'</p>';

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
			$rel = $oldRelease . ' to ' . $this->release;
			if ( version_compare( $this->release, $oldRelease, 'le' ) ) {
				Jerror::raiseWarning(null, JText::sprintf('COM_JTG_PREFLIGHT_JTG_WRONG_VERSION', $rel) );
				return false;
				// TODO this aborts the install process but generates and error in Joomla !!!
			}
		}
		else { $rel = $this->release; }
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

	    // load english language file for 'com_jtg' component then override with current language file
	    JFactory::getLanguage()->load('com_jtg', JPATH_ADMINISTRATOR, 'en-GB', true);
	    JFactory::getLanguage()->load('com_jtg', JPATH_ADMINISTRATOR, null, true);

	    ?>

	    <img src="<?php echo $imgdir . "logo_JTG.png"; ?>"  alt="J!Track Gallery" />
	    <br />
	    <table class="adminlist" border="1" width="100%">
		    <tbody>
			    <tr><td><?php JText::_('COM_JTG_INSTALL_LICENCE') ?></td></tr>
			    <tr><td><?php JText::_('COM_JTG_CREATE_FOLDERS') ?></td></tr>

	    <?php
	    $folders_to_create = array (
	    "images" . DS . "jtg",
	    "images" . DS . "jtg" . DS . "cats",
	    );

	    $folders_to_chmod = array (
	    "components" . DS . "com_jtg" . DS . "uploads",
	    "components" . DS . "com_jtg" . DS . "uploads" . DS . "import",
	    "components" . DS . "com_jtg" . DS . "assets" . DS . "images" . DS . "symbols",
	    );
	    $filetodelete = JPATH_SITE . DS . "components" . DS . "com_jtg" . DS . "uploads" . DS . "import" . DS . "Miele.gpx~";
	    if (JFile::exists($filetodelete))
		    JFile::delete($filetodelete);
	    foreach ( $folders_to_create AS $folder ) {
		    if(JFolder::exists(JPATH_SITE . DS . $folder))
		    echo "<tr><td>
			    <font color='green'>" . JText::_('COM_JTG_SKIPPING') . "</font>" . 
			    JText::_('COM_JTG_FOLDER'). $folder. JText::_('COM_JTG_ALREADY_EXISTS') . ".</td></tr>";
		    elseif(JFolder::create(JPATH_SITE . DS . $folder)) {
			    echo "<tr><td>
			    <font color='green'>" . JText::_('COM_JTG_DELETING') . "</font>" . 
			    JText::_('COM_JTG_FOLDER'). $folder. JText::_('COM_JTG_NOT_CREATED'). ".</td></tr>";
		    } else {
			    echo "<tr><td>
			    <font color='red'>" . JText::_('COM_JTG_ERROR') . "</font>" . 
			    JText::_('COM_JTG_FOLDER'). $folder. JText::_('COM_JTG_NOT_CREATED'). ".</td></tr>";
		    }
	    }

	    foreach ( $folders_to_chmod AS $folder ) {
		    ;
		    if ( JPath::canChmod(JPATH_SITE . DS . $folder) AND (chmod(JPATH_SITE . DS . $folder, 0777))) {
			    echo "</tr><td><font color='green'>Finished:</font>" . JText::_('COM_JTG_FOLDER'). 
				    $folder. JText::_('COM_JTG_CHMODDED'). ".</td></tr>";
		    } else {
			    echo "</tr><td><font color='red'>Error:</font>" . JText::_('COM_JTG_FOLDER'). 
				    $folder. JText::_('COM_JTG_NOT_CHMODDED'). ".</td></tr>";
		    }
	    }
	    ?>
		    </tbody>
	    </table>
	    <table class="adminlist" border="1" width="100%">
		    <tbody>
			    <tr><td>
				    <a href="index.php?option=com_jtg&task=postinstall&controller=postinstall">

					<?php echo(JText::_('COM_JTG_POST_INSTALL')); ?></a>
			    </td></tr>
			    <tr><td>
				    <a href="index.php?option=com_jtg">
					<?php echo(JText::_('COM_JTG_GOTO_ADMIN')); ?></a>
			    </td></tr>
		    </tbody>
	    </table>
	    <?php    
               		
	    echo '<p>' . JText::sprintf('COM_JTG_INSTALLED',$this->release) . '</p>';
	    // You can have the backend jump directly to the newly installed component configuration page
	    // $parent->getParent()->setRedirectURL('index.php?option=com_jtg');
	    return true;
	}
 
	/*
	 * $parent is the class calling this method.
	 * update runs after the database scripts are executed.
	 * If the extension exists, then the update method is run.
	 * If this returns false, Joomla will abort the update and undo everything already done.
	 */
	function update( $parent ) {
		
	    // nothing more to do here !!
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
 
		// echo '<p>' . JText::sprintf('COM_JTG_POSTFLIGHT ' . $type . ' to ' . $this->release) . '</p>';
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
	    echo '<p>' . JText::sprintf('COM_JTG_UNINSTALLING') . '</p>';
	    $folders_to_delete = array (
	    "images" . DS . "jtg"
	    );

	    // JPATH_SITE . DS . "components" . DS . "com_jtg" . DS . "uploads" . DS . "import" . DS . "noexistantfile"
	    $files_to_delete = array (
		 JPATH_SITE . DS . "components" . DS . "com_jtg" . DS . "uploads" . DS . "import" . DS . "noexistantfile"
		);
	    echo '<b>'.JText::_('COM_JTG_UNINSTALLING_RESUME').'</b><br/>';
	    echo '<table><tr><td colspan="3"><b>'.JText::_('COM_JTG_FILES_FOLDERS_TO_DELETE').'</td></tr>';
	    echo '<table><tr><td>'.JText::_('COM_JTG_FILE').'/'.JText::_('COM_JTG_FOLDER').'</td><td>'.JText::_('COM_JTG_NAME').'</td><td>'.JText::_('COM_JTG_STATUS').'</td></tr>';
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
			    echo '<tr><td>'.JText::_('COM_JTG_FOLDER').'</td><td>' . $folder . '</td><font color="green">' .JText::_('COM_JTG_DELETED') . '"</font>" </td></tr>';
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
