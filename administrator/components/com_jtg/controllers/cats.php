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

jimport('joomla.application.component.controller');
JTable::addIncludePath(JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_jtg' . DIRECTORY_SEPARATOR . 'tables');
/**
 * Controller Class Categories
 */
class JtgControllerCats extends JtgController
{
    /**
     *
     */
    function display()
    {
	    parent::display();
    }

	function uploadcatimages() {
        // Check for request forgeries
        JSession::checkToken() or jexit( 'Invalid Token' );
        
        $model = $this->getModel('cat');
        $success = $model->saveCatImage();
        
        // redirect to cats overview
        $link = JRoute::_( "index.php?option=com_jtg&task=cats&controller=cats&task=managecatpics",false);
        if ($success)
        	$this->setRedirect($link, JText::_('COM_JTG_CATPIC_SAVED'));
        else {
        	JFactory::getApplication()->enqueueMessage(JText::_('COM_JTG_CATPIC_NOTSAVED'), 'Warning');
        	$this->setRedirect($link);
        }
	}

	/**
     *
     * @global object $mainframe
     * @uses JtgModelCat::saveCat
     * @return redirect
     *
     */
    function savecat()  {

        // Check for request forgeries
        JSession::checkToken() or jexit( 'Invalid Token' );
        
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
     * @uses JtgModelCat::move
     * @return redirect
     */
    function orderup()  {

		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );

		$model = $this->getModel('cat');
		$model->move(-1);

        $this->setRedirect( JRoute::_('index.php?option=com_jtg&task=cats&controller=cats', false));
    }

    /**
     * @uses JtgModelCat::move
     *@return redirect
     */
    function orderdown()  {

		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );

		$model = $this->getModel('cat');
		$model->move(1);

        $this->setRedirect( JRoute::_('index.php?option=com_jtg&task=cats&controller=cats', false));
    }

        /**
         * @uses JtgModelCat::saveorder
         * @return redirect
         */
	function saveorder()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );

		$cid 	= JFactory::getApplication()->input->get( 'cid', array(), 'post', 'array' );
		$order 	= JFactory::getApplication()->input->get( 'order', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);
		JArrayHelper::toInteger($order);

		$model = $this->getModel('cat');
		$model->saveorder($cid, $order);

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=cats&controller=cats', false ));
	}


        /**
         * @uses JtgModelCat::publish
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
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_JTG_SELECT_AN_ITEM_TO_PUBLISH' ),'Error' );
		    
		}

		$model = $this->getModel('cat');

		if (!$model->publish($cid, 1))
		{
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=cats&controller=cats', false ));
	}

        /**
         * @uses JtgModelCat::publish
         * @return redirect
         */
	function unpublish()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );

		$cid = JFactory::getApplication()->input->get( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_JTG_SELECT_AN_ITEM_TO_UNPUBLISH' ),'Error' );
		}

		$model = $this->getModel('cat');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=cats&controller=cats', false ));
	}

		/**
         * @uses JtgModelCat::deleteCatImage
         * @return redirect
         */
	function removepic()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit( 'Invalid Token' );

		$cid = JFactory::getApplication()->input->get( 'cid', array(), 'post', 'array' );
		
		if (count( $cid ) < 1)
		{
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_JTG_SELECT_AN_ITEM_TO_DELETE' ),'Error' );
		}
		$model = $this->getModel('cat');
		if(!$model->deleteCatImage($cid)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=cats&controller=cats&task=managecatpics', false ));
	}

        /**
         * @uses JtgModelCat::delete
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
			JFactory::getApplication()->enqueueMessage(JText::_( 'COM_JTG_SELECT_AN_ITEM_TO_DELETE' ),'Error' );
		}

		$model = $this->getModel('cat');
		if(!$model->delete($cid)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=cats&controller=cats', false ));
	}
    /**
     * @uses JtgModelCat::updateCat
     * @return redirect
     */
    function updatecat()  {
        // check the token
        JSession::checkToken() or die( 'Invalid Token' );

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
