<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooosm and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.appliterrainion.component.controller');
JTable::addIncludePath(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jtg' . DIRECTORY_SEPARATOR . 'tables');

/**
 * Controller Class terrainegories
 */
class JtgControllerTerrain extends JtgController
{
    function save()  {
        $mainframe =& JFactory::getApplication();
        
        // Check for request forgeries
        JSession::checkToken() or jexit( 'Invalid Token' );

        $model = $this->getModel('terrain');
        $model->save();

        // redirect to terrains overview
        $link = JRoute::_( "index.php?option=com_jtg&task=terrain&controller=terrain",false);
        $this->setRedirect($link, JText::_('COM_JTG_TERRAIN_SAVED'));
    }

    function update()  {
        $mainframe =& JFactory::getApplication();
        
        // Check for request forgeries
        JSession::checkToken() or jexit( 'Invalid Token' );

        $model = $this->getModel('terrain');
        $model->save();

        // redirect to terrains overview
        $link = JRoute::_( "index.php?option=com_jtg&task=terrain&controller=terrain",false);
        $this->setRedirect($link, JText::_('COM_JTG_TERRAIN_UPDATED'));
    }

    /**
         * @uses JtgModelterrain::publish
         * @return redirect
         */
	function publish()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );

		$cid = JFactory::getApplication()->input->get( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) 
		{
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_JTG_SELECT_AN_ITEM_TO_PUBLISH'),'Error' );
		}

		$model = $this->getModel('terrain');
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=terrain&controller=terrain', false ));
	}

        /**
         * @uses JtgModelterrain::publish
         * @return redirect
         */
	function unpublish()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );

		$cid = JFactory::getApplication()->input->get( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) 
		{
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_JTG_SELECT_AN_ITEM_TO_UNPUBLISH'),'Error' );
		}

		$model = $this->getModel('terrain');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=terrain&controller=terrain', false ));
	}

        /**
         * @uses JtgModelterrain::delete
         * @return redirect
         */
	function remove()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );

		$cid = JFactory::getApplication()->input->get( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1)
		{
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_JTG_SELECT_AN_ITEM_TO_DELETE'),'Error' );
		}

		$model = $this->getModel('terrain');
		
		if (!$model->delete($cid))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=terrain&controller=terrain', false ));
	}

}
