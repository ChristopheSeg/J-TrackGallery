<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 * 
 * @version $Id: cats.php,v 1.1 2011/04/03 08:41:46 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage backend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );
JTable::addIncludePath(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_jtg'.DS.'tables');
/**
 * Controller Class Categories
 */
class jtgControllerCats extends jtgController  {
    /**
     *
     */
    function display()
    {
	    parent::display();
    }

	function uploadcatimages() {
        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );
        
        $model = $this->getModel('cat');
        $success = $model->saveCatImage();
        
        // redirect to cats overview
        $link = JRoute::_( "index.php?option=com_jtg&task=cats&controller=cats&task=managecatpics",false);
        if ($success)
        	$this->setRedirect($link, JText::_('COM_JTG_CATPIC_SAVED'));
        else {
        	JError::raiseWarning( 1, JText::_('COM_JTG_CATPIC_NOTSAVED'));
        	$this->setRedirect($link);
        }
	}

	/**
     *
     * @global object $mainframe
     * @uses jtgModelCat::saveCat
     * @return redirect
     *
     */
    function savecat()  {

        // Check for request forgeries
        JRequest::checkToken() or jexit( 'Invalid Token' );
        
        $model = $this->getModel('cat');
        $success = $model->saveCat();

        // redirect to cats overview
        $link = JRoute::_( "index.php?option=com_jtg&task=cats&controller=cats",false);
        if ($success)
        	$this->setRedirect($link, JText::_('COM_JTG_CAT_SAVED'));
        else
        	$this->setRedirect($link, JText::_('COM_JTG_CAT_NOT_SAVED'));

    }

    /**
     * @uses jtgModelCat::move
     * @return redirect
     */
    function orderup()  {

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$model = $this->getModel('cat');
		$model->move(-1);

        $this->setRedirect( JRoute::_('index.php?option=com_jtg&task=cats&controller=cats', false));
    }

    /**
     * @uses jtgModelCat::move
     *@return redirect
     */
    function orderdown()  {

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$model = $this->getModel('cat');
		$model->move(1);

        $this->setRedirect( JRoute::_('index.php?option=com_jtg&task=cats&controller=cats', false));
    }

        /**
         * @uses jtgModelCat::saveorder
         * @return redirect
         */
	function saveorder()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid 	= JRequest::getVar( 'cid', array(), 'post', 'array' );
		$order 	= JRequest::getVar( 'order', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);

		$model = $this->getModel('cat');
		$model->saveorder($cid, $order);

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=cats&controller=cats', false ));
	}


        /**
         * @uses jtgModelCat::publish
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

		$model = $this->getModel('cat');
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=cats&controller=cats', false ));
	}

        /**
         * @uses jtgModelCat::publish
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

		$model = $this->getModel('cat');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=cats&controller=cats', false ));
	}

		/**
         * @uses jtgModelCat::deleteCatImage
         * @return redirect
         */
	function removepic()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		
		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_JTG_SELECT_AN_ITEM_TO_DELETE' ) );
		}
		$model = $this->getModel('cat');
		if(!$model->deleteCatImage($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=cats&controller=cats&task=managecatpics', false ));
	}

        /**
         * @uses jtgModelCat::delete
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

		$model = $this->getModel('cat');
		if(!$model->delete($cid)) {
			echo "<script> alert('".$model->getError(true)."'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=cats&controller=cats', false ));
	}
    /**
     * @uses jtgModelCat::updateCat
     * @return redirect
     */
    function updatecat()  {
        // check the token
        JRequest::checkToken() or die( 'Invalid Token' );

        $model = $this->getModel('cat');
        $success = $model->updateCat();
        
        // redirect to cats overview
        $link = JRoute::_( "index.php?option=com_jtg&task=cats&controller=cats",false);
        if ($success)
        	$this->setRedirect($link, JText::_('COM_JTG_CAT_SAVED'));
        else
        	$this->setRedirect($link, JText::_('COM_JTG_CAT_NOT_SAVED'));
    }


}
