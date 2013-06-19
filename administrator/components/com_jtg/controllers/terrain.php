<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: terrain.php,v 1.1 2011/04/03 08:41:46 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.appliterrainion.component.controller' );
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jtg'.DS.'tables');

/**
 * Controller Class terrainegories
 */
class jtgControllerTerrain extends jtgController  {

    function save()  {
        $mainframe =& JFactory::getApplication();
        
        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $model = $this->getModel('terrain');
        $model->save();

        // redirect to terrains overview
        $link = JRoute::_( "index.php?option=com_jtg&task=terrain&controller=terrain",false);
        $this->setRedirect($link, JText::_('COM_JTG_TERRAIN_SAVED'));
    }

    function update()  {
        $mainframe =& JFactory::getApplication();
        
        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );

        $model = $this->getModel('terrain');
        $model->save();

        // redirect to terrains overview
        $link = JRoute::_( "index.php?option=com_jtg&task=terrain&controller=terrain",false);
        $this->setRedirect($link, JText::_('COM_JTG_TERRAIN_UPDATED'));
    }

    /**
         * @uses jtgModelterrain::publish
         * @return redirect
         */
	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_JTG_SELECT_AN_ITEM_TO_PUBLISH' ) );
		}

		$model = $this->getModel('terrain');
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=terrain&controller=terrain', false ));
	}

        /**
         * @uses jtgModelterrain::publish
         * @return redirect
         */
	function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_JTG_SELECT_AN_ITEM_TO_UNPUBLISH' ) );
		}

		$model = $this->getModel('terrain');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=terrain&controller=terrain', false ));
	}

        /**
         * @uses jtgModelterrain::delete
         * @return redirect
         */
	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_JTG_SELECT_AN_ITEM_TO_DELETE' ) );
		}

		$model = $this->getModel('terrain');
		if(!$model->delete($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=terrain&controller=terrain', false ));
	}

}
