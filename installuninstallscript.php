<?php

/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 * @package     Com_Jtg
 * @subpackage  Backend
 * @author      Christophe Seguinot <christophe@jtrackgallery.net>
 * @author      Pfister Michael, JoomGPStracks <info@mp-development.de>
 * @author      Christian Knorr, InJooOSM  <christianknorr@users.sourceforge.net>
 * @copyright   2015 J!TrackGallery, InJooosm and joomGPStracks teams
 *
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link        http://jtrackgallery.net/
 *
 */

// This file is based on Joomla script.php and corresponding com_flexicontent install script

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// The name of the class must be the name of your component + InstallerScript
class Com_JtgInstallerScript
{
	/**
	 * $parent is the class calling this method.
	 * $type is the type of change (install, update or discover_install, not uninstall).
	 * preflight runs before anything else and while the extracted files are in the uploaded temp folder.
	 * If preflight returns false, Joomla will abort the update and undo everything already done.
	 *
* @param   unknown_type  $type
* @param   unknown_type  $parent
	 *
	 * @return return_description
	 */
	function preflight($type, $parent)
	{
		// Try to increment some limits
		// Execution time 5 minutes
		@set_time_limit(240);

		// Continue execution if client disconnects
		ignore_user_abort(true);

		// Load english language file for 'com_jtg' component then override with current language file
		JFactory::getLanguage()->load('com_jtg', JPATH_ADMINISTRATOR . '/components/com_jtg/language', 'en-GB', true);
		JFactory::getLanguage()->load('com_jtg', JPATH_ADMINISTRATOR . '/components/com_jtg/language', null, true);
		$jversion = new JVersion;

		// Installing component manifest file version
		$this->release = $parent->get('manifest')->version;

		// File version of existing manifest file
		$this->release_existing = $this->getParam('version');

		// Manifest file minimum Joomla version
		$this->minimum_joomla_release = $parent->get('manifest')->attributes()->version;
		echo '<p> -- ' . JText::sprintf('COM_JTG_PREFLIGHT', $this->release) . '</p>';

		if ($this->release_existing)
		{
			echo '<br /> &nbsp; ' . JText::sprintf('COM_JTG_PREFLIGHT_UPDATING', $this->release_existing, $this->release);
		}
		else
		{
			echo '<br /> &nbsp; ' . JText::sprintf('COM_JTG_PREFLIGHT_INSTALLING', $this->release);
		}

		echo '<br /> &nbsp; ' . JText::sprintf('COM_JTG_PREFLIGHT_MIN_JOOMLA', $this->minimum_joomla_release, $jversion->getShortVersion);

		// Abort if the current Joomla release is older
		if (version_compare($jversion->getShortVersion, $this->minimum_joomla_release, 'lt'))
		{
			Factory::getApplication()->enqueueMessage(JText::sprintf('COM_JTG_PREFLIGHT_MIN_JOOMLA_ABORT', $this->minimum_joomla_release), 'Warning');

			return false;
		}

		// Abort if the component being installed is not newer than the currently installed version
		if ( $type == 'update' )
		{
			$oldRelease = $this->getParam('version');

			if ( version_compare($this->release, $oldRelease, 'lt') )
			{
				JFactory::getApplication()->enqueueMessage(JText::sprintf('COM_JTG_PREFLIGHT_JTG_WRONG_VERSION', $oldRelease, $this->release), 'Warning');

				return false;

				// TODO this aborts the install process but generates and error in Joomla !!!
			}
		}

		return true;
	}

	/**
	 * install runs after the database scripts are executed.
	 *
	 * If the extension is new, the install method is run.
	 * If install returns false, Joomla will abort the install and undo everything already done.
	 *
	 * @param   object  $parent  the class calling this method.
	 *
	 * @return <type>
	 */
	function install( $parent )
	{
		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$doc = JFactory::getDocument();

		?>
<br />
<img
	src="<?php echo '/components/com_jtg/assets/images/logo_JTG.png'; ?>"
	alt="J!Track Gallery" />
<br />
<table class="adminlist" border="1" style="width:100%;">
	<tbody>
		<tr>
			<td><?php JText::_('COM_JTG_INSTALL_LICENCE') ?></td>
		</tr>
		<tr>
			<td><?php JText::_('COM_JTG_CREATE_FOLDERS') ?></td>
		</tr>
	</tbody>
</table>
<?php
$folders_to_create = array (
		"images/jtrackgallery",
		"images/jtrackgallery/cats",
		"images/jtrackgallery/terrain",
		"images/jtrackgallery/uploaded_tracks",
		"images/jtrackgallery/uploaded_tracks/import"
);

$folders_to_chmod = array (
		"images/jtrackgallery/uploaded_tracks",
		"images/jtrackgallery/uploaded_tracks/import",
		"components/com_jtg/assets/images/symbols",
);

echo '<table><tr><td colspan="3"><b>' . JText::_('COM_JTG_FILES_FOLDERS_TO_CREATE') . '</td></tr>';
echo '<tr><td>' . JText::_('COM_JTG_FILE') . '/' . JText::_('COM_JTG_FOLDER') . '</td><td>' . JText::_('COM_JTG_NAME') . '</td><td>' . JText::_('COM_JTG_STATE') . '</td></tr>';

foreach ( $folders_to_create AS $folder )
{
	if (JFolder::exists(JPATH_SITE . '/' . $folder))
	{
		echo '<tr><td>' . JText::_('COM_JTG_FOLDER') . '</td><td>' .
				$folder . '</td><td><font color="green">' .
				JText::_('COM_JTG_ALREADY_EXISTS') . '</font></td></tr>';
	}
	elseif (JFolder::create(JPATH_SITE . '/' . $folder))
	{
		echo '<tr><td>' . JText::_('COM_JTG_FOLDER') . '</td><td>' .
				$folder . '</td><td><font color="green">' .
				JText::_('COM_JTG_CREATED') . '</font></td></tr>';
	}
	else
	{
		echo '<tr><td>' . JText::_('COM_JTG_FOLDER') . '</td><td>' .
				$folder . '</td><td><font color="red">' .
				JText::_('COM_JTG_NOT_CREATED') . '</font></td></tr>';
	}
}

foreach ( $folders_to_chmod AS $folder )
{
	if ( JPath::canChmod(JPATH_SITE . '/' . $folder) AND (chmod(JPATH_SITE . '/' . $folder, 0777)))
	{
		echo '<tr><td>' . JText::_('COM_JTG_FOLDER') . '</td><td>' .
				$folder . '</td><td><font color="green">' .
				JText::_('COM_JTG_CHMODDED') . '</font></td></tr>';
	}
	else
	{
		echo '<tr><td>' . JText::_('COM_JTG_FOLDER') . '</td><td>' .
				$folder . '</td><td><font color="red">' .
				JText::_('COM_JTG_NOT_CHMODDED') . '</font></td></tr>';
	}
}

// Copy Cats image
$src_folder_to_copy = JPATH_SITE . '/components/com_jtg/assets/images/cats';
$dest_folder_to_copy = JPATH_SITE . '/images/jtrackgallery/cats';
$files = JFolder::files($src_folder_to_copy);

// Copy file by file without erasing existing files
foreach ($files as $file)
{
	if (!JFile::exists($dest_folder_to_copy . '/' . $file) )
	{
		JFile::copy($src_folder_to_copy . '/' . $file, $dest_folder_to_copy . '/' . $file);
	}
}

// Copy additional.ini language files without erasing existing files
$src_folder_to_copy = JPATH_SITE . '/components/com_jtg/assets/language';
$dest_folder_to_copy = JPATH_SITE . '/images/jtrackgallery/language';
JFolder::copy($src_folder_to_copy, $dest_folder_to_copy, $force = false);

// Copy example tracks
$src_folder_to_copy = JPATH_SITE . '/components/com_jtg/assets/sample_tracks';
$dest_folder_to_copy = JPATH_SITE . '/images/jtrackgallery/uploaded_tracks';
$files = JFolder::files($src_folder_to_copy);

// Copy file by file without erasing existing files
foreach ($files as $file)
{
	if (!JFile::exists($dest_folder_to_copy . '/' . $file) )
	{
		JFile::copy($src_folder_to_copy . '/' . $file, $dest_folder_to_copy . '/' . $file);
	}
}

echo '<tr><td colspan="3">' . JText::sprintf('COM_JTG_INSTALLED_VERSION', $this->release) . '</td></tr>';

// You can have the backend jump directly to the newly installed component configuration page
// $parent->getParent()->setRedirectURL('index.php?option=com_jtg');

echo '<tr><td colspan="3">';
echo '<font color="red" size="+1">' . JText::_('COM_JTG_HINTS') . '</font>';
echo JText::_('COM_JTG_HINTS_DETAILS');
echo '</td></tr>';
echo '</table>';

return true;
	}

	/**
	 * update runs after the database scripts are executed.
	 *
	 * If the extension exists, then the update method is run.
	 * If this returns false, Joomla will abort the update and undo everything already done.
	 *
	 * @param   object  $parent  the class calling this method.
	 *
	 * @return <type>
	 */
	function update( $parent )
	{

		// This is executed after upgrade.sql
		// Upgrading from $oldRelease to $this->release
		$oldRelease = $this->getParam('version');

		if ( version_compare($oldRelease, '0.7.0', '<'))
		{
			// Installed version is lower then 0.7.0 ==> do some stuff
		}

		echo '<p>' . JText::sprintf('COM_JTG_UPDATED', $this->release) . '</p>';

		// You can have the backend jump directly to the newly updated component configuration page
		// $parent->getParent()->setRedirectURL('index.php?option=com_jtg');

		return true;
	}

	/**
	 * postflight is run after the extension is registered in the database.
	 *
	 * @param   string  $type    the type of change (install, update or discover_install, not uninstall).
	 * @param   object  $parent  the class calling this method.
	 *
	 * @return <type>
	 */
	function postflight( $type, $parent )
	{
		$db = JFactory::getDBO();
		$application = JFactory::getApplication();

		// Check if com_jtg is installed,
		$query = 'SELECT COUNT(*) FROM #__extensions WHERE name = "com_jtg"';
		$db->setQuery($query);
		$db->execute();
		$componentJtgIsInstalled = $db->loadResult();

		if (( $type == 'install' ) and (! $componentJtgIsInstalled) )
		{
			// This a NON successfull install:
			// TODO truncate all com_jtg tables
			$application->enqueueMessage('SHOULD TRUNCATE ALL TABLES');

			// DROP TABLE `crl05_jtg_cats`, `crl05_jtg_cats2`, `crl05_jtg_comments`, `crl05_jtg_config`, `crl05_jtg_files`, `crl05_jtg_files2`, `crl05_jtg_maps`, `crl05_jtg_terrains`, `crl05_jtg_users`, `crl05_jtg_votes`;
		}

		if (( $type == 'install' ) and ($componentJtgIsInstalled !== null) )
		{
			// This is a successful install (not an upgrade):
			// affect sample tracks to this admin user
			$user = JFactory::getUser();
			$uid = $user->get('id');

			if ($uid !== 430)
			{
				$query = 'UPDATE #__jtg_files SET uid =' . $uid . ' WHERE uid =430';
				$db->setQuery($query);
				$db->execute();
			}

			// Save default params
			$query = 'UPDATE #__extensions SET params = ';
			$query .= '\' {"jtg_param_newest":"10","jtg_param_mostklicks":"10",
			"jtg_param_best":"0","jtg_param_rand":"0",
			"jtg_param_otherfiles":"0",
			"jtg_param_lh":"1",
			"jtg_param_disable_terrains":"0",
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
			"jtg_param_vote_to":"10"}\'';
			$query .= ' WHERE name = "com_jtg" AND type = "Component"';
			$db->setQuery($query);
			$db->execute();
		}

		return true;
	}

	/*
	 *
	 * $parent is the class calling this method
	* uninstall runs before any other action is taken (file removal or database processing).
	*/
	function uninstall( $parent )
	{
		// Set a simple message

		jimport('joomla.filesystem.folder');
		jimport('joomla.filesystem.file');
		$application = JFactory::getApplication();
		$application->enqueueMessage(JText::_('COM_JTG_THANK_YOU_FOR_USING'));
		echo '<p>' . JText::_('COM_JTG_UNINSTALLING') . '</p>';
		$folders_to_delete = array ('images/jtrackgallery');

		$files_to_delete = array ();

		echo '<table><tr><td colspan="3"><b>' . JText::_('COM_JTG_FILES_FOLDERS_TO_DELETE') . '</td></tr>';
		echo '<tr><td>' . JText::_('COM_JTG_FILE') . '/' . JText::_('COM_JTG_FOLDER') . '</td><td>' . JText::_('COM_JTG_NAME') . '</td><td>' . JText::_('COM_JTG_STATE') . '</td></tr>';

		foreach ( $files_to_delete AS $file )
		{
			if (!JFile::exists($file))
			{
				echo '<tr><td>' . JText::_('COM_JTG_FILE') . '</td><td>' . $file . '</td><td><font color="green">' . JText::_('COM_JTG_NOT_EXISTS') . ' </font>  </td></tr>';
			}
			elseif (JFile::delete($file))
			{
				echo '<tr><td>' . JText::_('COM_JTG_FILE') . '</td><td>' . $file . '</td><td><font color="green">' . JText::_('COM_JTG_DELETED') . ' </font> </td></tr>';
			}
			else
			{
				echo '<tr><td>' . JText::_('COM_JTG_FILE') . '</td><td>' . $file . '</td><td><font color="red">' . JText::_('COM_JTG_ERROR') . '/' . JText::_('COM_JTG_NOT_DELETED') . '</font> </td></tr>';
			}
		}

		foreach ( $folders_to_delete AS $folder )
		{
			if (!JFolder::exists(JPATH_SITE . '/' . $folder))
			{
				echo '<tr><td>' . JText::_('COM_JTG_FOLDER') . '</td><td>' . $folder . '</td><td><font color="green">' . JText::_('COM_JTG_NOT_EXISTS') . '</font> </td></tr>';
			}
			elseif (JFolder::delete(JPATH_SITE . '/' . $folder))
			{
				echo '<tr><td>' . JText::_('COM_JTG_FOLDER') . '</td><td>' . $folder . '</td><td><font color="green">' . JText::_('COM_JTG_DELETED') . '</font> </td></tr>';
			}
			else
			{
				echo '<tr><td>' . JText::_('COM_JTG_FOLDER') . '</td><td>' . $folder . '</td><font color="red">' . JText::_('COM_JTG_ERROR') . JText::_('COM_JTG_NOT_DELETED') . '</font> </td></tr>';
			}
		}

		echo '</table>';

		return true;
	}

	/**
	 * get a variable from the manifest file (actually, from the manifest cache).
	 *
	 * @param   string  $name  parameter name
	 *
	 * @return parameter value
	 */
	function getParam( $name )
	{
		$db = JFactory::getDbo();
		$db->setQuery('SELECT manifest_cache FROM #__extensions WHERE name = "com_jtg"');
		$manifest = json_decode($db->loadResult(), true);

		return $manifest[ $name ];
	}

	/*
	 * sets parameter values in the component's row of the extension table
	*/
	function setParams($param_array)
	{
		if ( count($param_array) > 0 )
		{
			// Read the existing component value(s)
			$db = JFactory::getDbo();
			$db->setQuery('SELECT params FROM #__extensions WHERE name = "com_jtg"');
			$params = json_decode($db->loadResult(), true);

			// Add the new variable(s) to the existing one(s)
			foreach ( $param_array as $name => $value )
			{
				$params[ (string) $name ] = (string) $value;
			}
			// Store the combined new and existing values back as a JSON string
			$paramsString = json_encode($params);
			$db->setQuery('UPDATE #__extensions SET params = ' .
					$db->quote($paramsString) .
					' WHERE name = "com_jtg"' );
			$db->execute();
		}
	}
}
