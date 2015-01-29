<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @package    Comjtg
 * @author     Christophe Seguinot <christophe@jtrackgallery.net>
 * @copyright  2013 J!Track Gallery, InJooosm and joomGPStracks teams
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jtg/tables');

/**
 * Controller Class Files
 */
class JtgControllerFiles extends JtgController
{

	function updateGeneratedValues ()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$model = $this->getModel('files');
		if (! $model->updateGeneratedValues())
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=files&controller=files', false));
	}

	function uploadfiles ()
	{
		JSession::checkToken() or jexit('Invalid Token');
		$jInput = JFactory::getApplication()->input;
		$jFileInput = new jInput($_FILES);
		$files = $jFileInput->get('files',array(),'array');

		$model = $this->getModel('files');
		$dest = JPATH_SITE . '/images/jtrackgallery/uploaded_tracks' .
				'/import/';

		if (! $model->uploadfiles($files, $dest))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}
		if (JFactory::getApplication()->input->get('toimport'))
			$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=newfiles&controller=files', false));
		else
			$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=files&controller=files', false));
	}

	/**
	 *
	 * @uses JtgModelFiles::publish
	 * @return redirect
	 */
	function publish ()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_SELECT_AN_ITEM_TO_PUBLISH'), 'Error');
		}

		$model = $this->getModel('files');
		if (! $model->publish($cid, 1))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}
		$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=files&controller=files', false));
	}

	/**
	 *
	 * @uses JtgModelFiles::tohide
	 * @return redirect
	 */
	function tohide ()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_SELECT_AN_ITEM_TO_UNPUBLISH'), 'Error');
		}

		$model = $this->getModel('files');
		if (! $model->showhide($cid, 1))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=files&controller=files', false));
	}

	/**
	 *
	 * @uses JtgModelFiles::toshow
	 * @return redirect
	 */
	function toshow ()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_SELECT_AN_ITEM_TO_UNPUBLISH'), 'Error');
		}

		$model = $this->getModel('files');
		if (! $model->showhide($cid, 0))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=files&controller=files', false));
	}

	/**
	 *
	 * @uses JtgModelFiles::publish
	 * @return redirect
	 */
	function unpublish ()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_SELECT_AN_ITEM_TO_UNPUBLISH'), 'Error');
		}

		$model = $this->getModel('files');
		if (! $model->publish($cid, 0))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=files&controller=files', false));
	}

	/**
	 *
	 * @uses JtgModelFiles::accessregistered
	 * @return redirect
	 */
	function accessregistered ()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_SELECT_AN_ITEM_TO_PUBLISH'), 'Error');
		}

		$model = $this->getModel('files');
		if (! $model->access($cid, 1))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=files&controller=files', false));
	}

	/**
	 *
	 * @uses JtgModelFiles::accessspecial
	 * @return redirect
	 */
	function accessspecial ()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_SELECT_AN_ITEM_TO_UNPUBLISH'), 'Error');
		}

		$model = $this->getModel('files');
		if (! $model->access($cid, 2))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=files&controller=files', false));
	}

	/**
	 *
	 * @uses JtgModelFiles::accesspublic
	 * @return redirect
	 */
	function accesspublic ()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_SELECT_AN_ITEM_TO_UNPUBLISH'), 'Error');
		}

		$model = $this->getModel('files');

		if (! $model->access($cid, 0))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=files&controller=files', false));
	}

	/**
	 *
	 * @uses JtgModelFiles::delete
	 * @return redirect
	 */
	function remove ()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		JArrayHelper::toInteger($cid);

		if (count($cid) < 1)
		{
			JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_SELECT_AN_ITEM_TO_DELETE'), 'Error');
		}

		$model = $this->getModel('files');
		if (! $model->delete($cid))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=files&controller=files', false));
	}

	/**
	 *
	 * @uses JtgModelFiles::delete
	 * @return redirect
	 */
	function removeFromImport ()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$found = JFactory::getApplication()->input->get('found');

		$model = $this->getModel('files');
		if (! $model->deleteFromImport($found))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=newfiles&controller=files', false));
	}

	/**
	 */
	function savefile ()
	{

		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$model = $this->getModel('files');
		if (! $model->saveFile())
		{
			// echo "<script> alert('" . $model->getError(true) . "');
			// window.history.go(-1); </script>\n";
			echo "<script> alert('Error');</script>";
			// $this->setRedirect(
			// JRoute::_('index.php?option=com_jtg&task=files&controller=files',
			// false ));
		}
		else
			$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=files&controller=files', false));
	}

	function savefiles ()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$model = $this->getModel('files');
		if (! $model->saveFiles())
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=files&controller=files', false));
	}

	function updatefile ()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$model = $this->getModel('files');
		if (! $model->updateFile())
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=files&controller=files', false));
	}

	function fetchJPTfiles ()
	{
		JSession::checkToken() or jexit('Invalid Token');
		$model = $this->getModel('files');
		echo $model->_fetchJPTfiles();
	}
}
