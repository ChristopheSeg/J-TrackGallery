<?php
/**
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: comments.php,v 1.1 2011/04/03 08:41:46 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

class jtgControllerComments extends jtgController  {

        /**
         * 
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

		$model = $this->getModel('comments');
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=comments&controller=comments', false ));
	}

        /**
         *
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

		$model = $this->getModel('comments');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=comments&controller=comments', false ));
	}

        /**
         * remove comments
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

		$model = $this->getModel('comments');
		if(!$model->delete($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=comments&controller=comments', false ));
	}

        /**
         * update a comment
         */
        function saveComment()  {

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

                $model = $this->getModel('comments');
                if(!$model->saveComment())  {
                    echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
                }

                $this->setRedirect( JRoute::_('index.php?option=com_jtg&task=comments&controller=comments', false ));
        }
}
