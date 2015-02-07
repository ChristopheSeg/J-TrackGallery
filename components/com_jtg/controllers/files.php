<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @author      Christophe Seguinot <christophe@jtrackgallery.net>
 * @author      Pfister Michael, JoomGPStracks <info@mp-development.de>
 * @author      Christian Knorr, InJooOSM  <christianknorr@users.sourceforge.net>
 * @copyright   2015 J!TrackGallery, InJooosm and joomGPStracks teams
 *
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link        http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.controller');
/**
 *
 *
 *
 */
class JtgControllerFiles extends JtgController
{
	/**
	 *
	 *
	 * @return return_description
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 *
	 *
	 * @return return_description
	 */
	function save()
	{
		jimport('joomla.filesystem.file');

		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');
		$file = JFactory::getApplication()->input->files->get('file');

		if (!$file['name'])
		{
			echo "<script> alert('" . JText::_('COM_JTG_FILE_UPLOAD_NO_FILE') . "'); window.history.go(-1); </script>\n";
			exit;
		}

		$images =& $_FILES['images'];
		$model = $this->getModel('files');

		$ext = JFile::getExt($file['name']);

		if ($ext == 'kml' || $ext == 'gpx' || $ext == 'tcx')
		{
			if (!$model->saveFile())
			{
				echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
			}

			$this->setRedirect(JRoute::_('index.php', false), false);
		}
		else
		{
			echo "<script> alert('" . $file['name'] . JText::_('COM_JTG_GPS_FILE_ERROR') . "'); window.history.go(-1); </script>\n";
			exit;
		}
	}

	function vote()
	{
		$id = JRequest::getInt('id');
		$rate = JRequest::getInt('rate');
		$model = $this->getModel('files');
		$model->vote($id, $rate);

		$msg = JText::_('COM_JTG_VOTED');
		$this->setRedirect(JRoute::_('index.php?option=com_jtg&view=files&layout=file&id=' . $id, false), false);
	}

	/**
	 *
	 *
	 * @return return_description
	 */
	function delete()
	{
		$user = JFactory::getUser();

		if (!$user->get('id'))
		{
			$this->setRedirect(JRoute::_('index.php?option=com_jtg', false), false);
		}

		$id = JRequest::getInt('id');
		$model = $this->getModel('files');

		if (!$model->deleteFile($id))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}
		else
		{
			$this->setRedirect(JRoute::_('index.php?option=com_jtg&view=files&layout=user', false), false);
		}
	}

	/**
	 *
	 *
	 * @return return_description
	 */
	function update()
	{
		jimport('joomla.filesystem.file');
		$user		= JFactory::getUser();

		if (!$user->get('id'))
		{
			$this->setRedirect(JRoute::_('index.php?option=com_jtg', false), false);
		}

		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');
		$id = JRequest::getInt('id');
		$model = $this->getModel('files');
		$errormsg = $model->updateFile($id);

		if ($errormsg !== true)
		{
			echo "<script> alert('Error: \"" . $errormsg . "\"'); window.history.go(-1); </script>\n";
		}
		else
		{
			$this->setRedirect(JRoute::_('index.php?option=com_jtg&view=files&layout=file&id=' . $id, false), false);
		}
	}

	function addcomment()
	{
		$model = $this->getModel('files');
		$model->addcomment();
	}

	/**
	 *
	 *
	 * @return return_description
	 */
	function savecomment()
	{
		$mainframe = JFactory::getApplication();

		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');
		$cfg = JtgHelper::getConfig();
		$id = JRequest::getInt('id');

		if ($cfg->captcha == 1)
		{
			$return = false;
			$word = JFactory::getApplication()->input->get('word', false, '', 'CMD');
			$mainframe->triggerEvent('onCaptcha_confirm', array($word, &$return));

			if (!$return)
			{
				echo "<script> alert('" . JText::_('COM_JTG_CAPTCHA_WRONG') . "'); window.history.go(-1); </script>\n";
			}
			else
			{
				$model = $this->getModel('files');

				if (!$model->savecomment($id, $cfg))
				{
					$msg = JText::_('COM_JTG_COMMENT_NOT_SAVED');
				}
				else
				{
					$msg = JText::_('COM_JTG_COMMENT_SAVED');
				}

				$this->setRedirect(JRoute::_('index.php?option=com_jtg&view=files&layout=file&id=' . $id . '#jtg_param_header_comment', false), $msg);
			}
		}
		else
		{
			$model = $this->getModel('files');

			if (!$model->savecomment($id, $cfg))
			{
				$msg = JText::_('COM_JTG_COMMENT_NOT_SAVED');
			}
			else
			{
				$msg = JText::_('COM_JTG_COMMENT_SAVED');
			}

			$this->setRedirect(JRoute::_('index.php?option=com_jtg&view=files&layout=file&id=' . $id . '#jtg_param_header_comment', false), $msg);
		}
	}
}
