<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJO3SM and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
 
/**
 * Script file of jtg component
 */
class com_jtgInstallerScript
{
        /**
         * method to install the component
         *
         * @return void
         */
      function install($type) 
        {
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
			<thead>
				<tr><th><?php JText::_('COM_JTG_INSTALL_TEXT') ?></th></tr>
			</thead>
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
				
					    <?php echo(JText::_('COM_JTG_POST_INSTALL')); ?></a>					</td></tr>
				<tr><td>
					<a href="index.php?option=com_jtg">
					    <?php echo(JText::_('COM_JTG_GOTO_ADMIN')); ?></a>
				</td></tr>
			</tbody>
		</table>
		<?php
		// $parent->getParent()->setRedirectURL('index.php?option=com_jtg');
		return true;	    
               
        }
 

}
