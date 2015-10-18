<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @package     Comjtg
 * @subpackage  Backend
 * @author      Christophe Seguinot <christophe@jtrackgallery.net>
 * @author      Pfister Michael, JoomGPStracks <info@mp-development.de>
 * @author      Christian Knorr, InJooOSM  <christianknorr@users.sourceforge.net>
 * @copyright   2015 J!TrackGallery, InJooosm and joomGPStracks teams
 *
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3
 * @link        http://jtrackgallery.net/
 *
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');


// Import Joomla! libraries
jimport('joomla.application.component.view');
/**
 * JtgViewCats class for the jtg component
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @since       0.8
 */

class JtgViewCats extends JViewLegacy
{
	/**
	 * function_description
	 *
	 * @param   object  $tpl  template
	 *
	 * @return void
	 */
	public function display($tpl = null)
	{
		switch ($this->getLayout())
		{
			case 'form':
				$this->_displayForm($tpl);
				break;

			case 'editform':
				$this->_displayEditcat($tpl);
				break;

			case 'default':
				$this->_displayDefault($tpl);
				break;

			case 'managecatpics':
			case 'managecatpicsform':
				$this->_displayManageCatPics($tpl);
				break;
		}
	}

	/**
	 * function_description
	 *
	 * @param   object  $tpl  template
	 *
	 * @return return_description
	 */
	function _displayManageCatPics($tpl)
	{
		$model = $this->getModel();
		$rows = $this->get('Pics');
		$children = array();
		$imagedir = JPATH_SITE . '/images/jtrackgallery/cats/';
		$imageurl = JUri::root() . 'images/jtrackgallery/cats/';
		jimport('joomla.filesystem.file');
		$images = array();

		foreach ($rows as $k => $v )
		{
			$new = new stdClass;
			$new->id = $v;
			$new->file = $v;
			$new->pathfile = $imagedir . $v;
			$new->name = JFile::stripext($v);
			$new->ext = JFile::getext($v);
			$new->checked_out = 0;
			$new->image = " <image src='" . $imageurl . $v . "' title='" . $v . "' alt='" . $v . "' />";
			$images[$k] = $new;
		}

		$config = JtgHelper::getConfig();
		$types = $config->type;
		$this->types = $types;
		$this->rows = $images;

		parent::display($tpl);
	}

	/**
	 * function_description
	 *
	 * @param   object  $tpl  template
	 *
	 * @return return_description
	 */
	function _displayDefault($tpl)
	{
		$mainframe = JFactory::getApplication();
		$option = JFactory::getApplication()->input->get('option');
		$model = $this->getModel();

		// $order = JFactory::getApplication()->input->get('order', 'order', 'post', 'string' );

		$filter_order		= $mainframe->getUserStateFromRequest(
				$option . "filter_order",
				'filter_order',
				'ordering',
				'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest(
				$option . "filter_order_Dir",
				'filter_order_Dir',
				'',
				'word' );

		$lists['order']		= $filter_order;
		$lists['order_Dir']	= $filter_order_Dir;

		$rows = $this->get('Data');
		$children = array();
		$image = array();
		$imagedir = JUri::root() . 'images/jtrackgallery/cats/';

		foreach ($rows as $v )
		{
			$v->title = JText::_($v->title);

			// TODO  unnecessary
			$v->name = $v->title;
			$pt	= $v->parent_id;
			$list = @$children[$pt] ? $children[$pt] : array();
			array_push($list, $v);
			$children[$pt] = $list;

			if ($v->image)
			{
				$image[$v->id] = " <image src='" . $imagedir . $v->image . "' title='" . JText::_($v->title) . "' alt='" . JText::_($v->title) . "' />";
			}
		}

		$levellimit = 50;
		$list = JHtml::_('menu.treerecurse', 0, '', array(), $children, max(0, $levellimit - 1));
		$list = array_slice($list, 0, 999);

		$total		= $this->get('Total');
		$pagination = $this->get('Pagination');
		$this->lists = $lists;
		$this->pagination = $pagination;
		$this->list = $list;
		$this->catpic = $image;
		$this->rows = $rows;

		parent::display($tpl);
	}

	/**
	 * function_description
	 *
	 * @param   object  $tpl  template
	 *
	 * @return return_description
	 */
	protected function _displayForm($tpl)
	{
		// 	$mainframe = JFactory::getApplication(); $option = JFactory::getApplication()->input->get('option');

		$model = $this->getModel();
		$parent = $model->getParent();
		$nullcat = array('id' => 0, "name" => JText::_('COM_JTG_NOTHING'), "title" => JText::_('COM_JTG_NOTHING'));
		array_unshift($parent, $nullcat);
		$editor = JFactory::getEditor();
		$lists['block'] 	= JHtml::_('select.booleanlist', 'publish', 'class="inputbox" size="1"', 1);
		$lists['parent'] 	= JHtml::_('select.genericlist', $parent, 'parent', 'size="1"', 'id', 'name', '');
		$config = JtgHelper::getConfig();
		$images = $model->getPics();

		$this->images = $images;
		$this->lists = $lists;
		$this->editor = $editor;
		$this->max_images = $config->max_images;
		$this->maxsize = $config->max_size;

		parent::display($tpl);
	}

	/**
	 * function_description
	 *
	 * @param   object  $tpl  template
	 *
	 * @return return_description
	 */
	function _displayEditcat($tpl)
	{
		// 	$mainframe = JFactory::getApplication();
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$id = $cid[0];

		$editor = JFactory::getEditor();
		$model = $this->getModel();
		$parent = $model->getParent($id);
		$nullcat = array('id' => 0, "name" => JText::_('COM_JTG_NOTHING'), "title" => JText::_('COM_JTG_NOTHING'));
		array_unshift($parent, $nullcat);
		$data = $model->getCat($id);
		$lists['block'] 	= JHtml::_('select.booleanlist', 'publish', 'class="inputbox" size="1"', $data->published);
		$lists['usepace'] 	= JHtml::_('select.booleanlist', 'usepace', 'class="inputbox" size="1"', $data->usepace);
		$lists['parent'] 	= JHtml::_('select.genericlist', $parent, 'parent', 'size="1"', 'id', 'name', $data->parent_id);

		$config = JtgHelper::getConfig();
		$images = $model->getPics();
		$this->images = $images;
		$this->lists = $lists;
		$this->editor = $editor;
		$this->maxsize = $config->max_size;
		$this->data = $data;

		parent::display($tpl);
	}
}
