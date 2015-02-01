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
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JtgControllerComments extends JtgController
{
	/**
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

		$model = $this->getModel('comments');

		if (! $model->publish($cid, 1))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=comments&controller=comments', false));
	}

	/**
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

		$model = $this->getModel('comments');

		if (! $model->publish($cid, 0))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=comments&controller=comments', false));
	}

	/**
	 * remove comments
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

		$model = $this->getModel('comments');

		if (! $model->delete($cid))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=comments&controller=comments', false));
	}

	/**
	 * update a comment
	 */
	function saveComment ()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit('Invalid Token');

		$model = $this->getModel('comments');

		if (! $model->saveComment())
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect(JRoute::_('index.php?option=com_jtg&task=comments&controller=comments', false));
	}
}
