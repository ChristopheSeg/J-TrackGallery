<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @package    Comjtg
 * @author     Christophe Seguinot <christophe@jtrackgallery.net>
 * @copyright  2013 J!Track Gallery, InJooosm and joomGPStracks teams
 * @license    http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link       http://jtrackgallery.net/
 *
 */

defined('_JEXEC') or die('Restricted access');
JToolBarHelper::title(JText::_('COM_JTG_ADD_FILES'), 'categories.png');
JToolBarHelper::back();
$model = $this->getModel();
$rows = $model->_fetchJPTfiles();

if ($rows == false)
{
	JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_ERROR_NOJGTFOUND'), 'Error');
}
else
{
	$i = 0;
	$importdone = false;

	foreach ( $rows AS $track )
	{
		if ($model->importFromJPT($track) == true)
		{
			$color = "green";
			$importdone = true;
		}
		else
		{
			$color = "red";
		}

		echo "<font color=\"" . $color . "\">" . $track["file"] . "</font><br />\n";
	}

	if ($importdone == true)
	{
		JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_IMPORT_DONE'));
	}
	else
	{
		JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_IMPORT_FAILURE'), 'Warning');
	}
}
