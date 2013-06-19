<?php
/**
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: maps.php,v 1.1 2011/04/03 08:41:46 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

class jtgControllerMaps extends jtgController  {

	function bak__construct() {
		parent::__construct();
		$mainframe =& JFactory::getApplication(); // global _ $option;
		$where = array();
//
//		// Get the pagination request variables
//		$limit		= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
//		$limitstart	= $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
//
//		// In case limit has been changed, adjust limitstart accordingly
//		//		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
//		$limitstart = JRequest::getVar('limitstart',0);
//
//		$this->setState('limit', $limit);
//		$this->setState('limitstart', $limitstart);
		$filter_state = $mainframe->getUserStateFromRequest( $this->option.'filter_state', 'filter_state', '', 'word' );
		if ( $filter_state ) {
			if ( $filter_state == 'P' ) {
				$where[] = 'a.published = 1';
			} else if ($filter_state == 'U' ) {
				$where[] = 'a.published = 0';
			}
		}
		$where = ' WHERE ' . implode( ' AND ', $where );
		$search = JRequest::getVar('search',true);
		$layout = JRequest::getVar('layout',true);
		$task = JRequest::getVar('task',true);
		$controller = JRequest::getVar('controller',true);
	}

    /**
     * @uses jtgModelMaps::move
     * @return redirect
     */
    function orderup()  {

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$model = $this->getModel('maps');
		$model->move(-1);

        $this->setRedirect( JRoute::_('index.php?option=com_jtg&task=maps&controller=maps', false));
    }

    /**
     * @uses jtgModelMaps::move
     *@return redirect
     */
    function orderdown()  {

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$model = $this->getModel('maps');
		$model->move(1);

        $this->setRedirect( JRoute::_('index.php?option=com_jtg&task=maps&controller=maps', false));
		    }
	/**
         * @uses jtgModelMaps::publish
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

		$model = $this->getModel('maps');
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}
		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=maps&controller=maps', false ));
	}

        /**
         * @uses jtgModelMaps::publish
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

		$model = $this->getModel('maps');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=maps&controller=maps', false ));
	}

        /**
         * @uses jtgModelMaps::remove
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

		$model = $this->getModel('maps');
		if(!$model->delete($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=maps&controller=maps', false ));
	}

        /**
         *
         */
        function savemap()  {

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$model = $this->getModel('maps');
		$savemap = $model->saveMap();
		if(!$savemap)
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=maps&controller=maps', false ));
        }

        function savemaps() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $model = $this->getModel('maps');
		if(!$model->saveMaps()) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=maps&controller=maps', false ));
        }

        function updatemap() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $model = $this->getModel('maps');
		if(!$model->updateMap())
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=maps&controller=maps', false ));
        }

}
