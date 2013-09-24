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
JTable::addIncludePath(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_jtg' . DS . 'tables');
/**
 * Controller Class Files
 */
class JtgControllerFiles extends JtgController
{
	function updateGeneratedValues() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$model = $this->getModel('files');
		if(!$model->updateGeneratedValues()) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=files&controller=files', false ));
	}

	function uploadfiles() {

		JRequest::checkToken() or jexit( 'Invalid Token' );
		$files =& JRequest::getVar('files', null, 'files', 'array');

		if (count( $files["name"] ) <= 1) {
			JError::raiseError(500, "<p class=\"type\">".JText::_( 'COM_JTG_ERROR_NO_FILES_CHOOSEN' ) . "</p>" );
		}

		$model = $this->getModel('files');
		$dest = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . 'uploads' . DS . 'import'.DS;
		if(!$model->uploadfiles( $files, $dest )) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}
		if ( JRequest::getVar('toimport') )
		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=newfiles&controller=files', false ));
		else
		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=files&controller=files', false ));
	}

	/**
	 * @uses JtgModelFiles::publish
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

		$model = $this->getModel('files');
		if(!$model->publish($cid, 1)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}
		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=files&controller=files', false ));
	}

	/**
	 * @uses JtgModelFiles::tohide
	 * @return redirect
	 */
	function tohide() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_JTG_SELECT_AN_ITEM_TO_UNPUBLISH' ) );
		}

		$model = $this->getModel('files');
		if(!$model->showhide($cid, 1)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=files&controller=files', false ));
	}

	/**
	 * @uses JtgModelFiles::toshow
	 * @return redirect
	 */
	function toshow() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_JTG_SELECT_AN_ITEM_TO_UNPUBLISH' ) );
		}

		$model = $this->getModel('files');
		if(!$model->showhide($cid, 0)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=files&controller=files', false ));
	}

	/**
	 * @uses JtgModelFiles::publish
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

		$model = $this->getModel('files');
		if(!$model->publish($cid, 0)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=files&controller=files', false ));
	}


	/**
	 * @uses JtgModelFiles::accessregistered
	 * @return redirect
	 */
	function accessregistered()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_JTG_SELECT_AN_ITEM_TO_PUBLISH' ) );
		}

		$model = $this->getModel('files');
		if(!$model->access($cid, 1)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=files&controller=files', false ));
	}

	/**
	 * @uses JtgModelFiles::accessspecial
	 * @return redirect
	 */
	function accessspecial()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_JTG_SELECT_AN_ITEM_TO_UNPUBLISH' ) );
		}

		$model = $this->getModel('files');
		if(!$model->access($cid, 2)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=files&controller=files', false ));
	}

	/**
	 * @uses JtgModelFiles::accesspublic
	 * @return redirect
	 */
	function accesspublic()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$cid = JRequest::getVar( 'cid', array(), 'post', 'array' );
		JArrayHelper::toInteger($cid);

		if (count( $cid ) < 1) {
			JError::raiseError(500, JText::_( 'COM_JTG_SELECT_AN_ITEM_TO_UNPUBLISH' ) );
		}

		$model = $this->getModel('files');
		if(!$model->access($cid, 0)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=files&controller=files', false ));
	}

	/**
	 * @uses JtgModelFiles::delete
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

		$model = $this->getModel('files');
		if(!$model->delete($cid)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=files&controller=files', false ));
	}

	/**
	 * @uses JtgModelFiles::delete
	 * @return redirect
	 */
	function removeFromImport()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$found = JRequest::getVar( 'found' );

		$model = $this->getModel('files');
		if(!$model->deleteFromImport($found)) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=newfiles&controller=files', false ));
	}

	/**
	 *
	 */
	function savefile() {

		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$model = $this->getModel('files');
		if(!$model->saveFile()) {
			// 			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
			echo "<script> alert('Error');</script>";
			// 			$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=files&controller=files', false ));
		} else
		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=files&controller=files', false ));
	}

	function savefiles() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$model = $this->getModel('files');
		if(!$model->saveFiles()) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=files&controller=files', false ));
	}

	function updatefile() {
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$model = $this->getModel('files');
		if(!$model->updateFile()) {
			echo "<script> alert('" . $model->getError(true) . "'); window.history.go(-1); </script>\n";
		}

		$this->setRedirect( JRoute::_('index.php?option=com_jtg&task=files&controller=files', false ));
	}

	function fetchJPTfiles() {
		JRequest::checkToken() or jexit( 'Invalid Token' );
		$model = $this->getModel('files');
		echo($model->_fetchJPTfiles());
	}
}
