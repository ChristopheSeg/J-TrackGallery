<?php

/**
 *    @component   J!Track Gallery (jtg) for Joomla! 2.5
 * 
 *    @package     Com_Jtg
 *    @subpackage  Backend
 *    @author      J!Track Gallery, InJooosm and joomGPStracks teams <christophe@jtrackgallery.net>
 *    @copyright   test 
 *    @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2 *
 *    @link        http://jtrackgallery.net/
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * maincontroller for backend
 */
class JtgController extends JController
{
	/**
	 *
	 */
	function __construct() 
	{
		parent::__construct();
		$this->registerTask('savecat', 'savecat');
	}

	/**
	 * @use get task form uri and set view and layout
	 */
	function display() 
	{
		require_once JPATH_COMPONENT . '/helpers/jtg.php';

		// JtgHelper::updateReset();

		// Load the submenu.
		JtgHelper::addSubmenu(JRequest::getCmd('view', 'jtg'));

		switch ($this->getTask())
		{
			default:
				JRequest::setVar('view',	'default');
				break;

			case 'maps':
				JRequest::setVar('view',	'maps');
				JRequest::setVar('layout',	'default');
				break;

			case 'newmap':
			case 'editmap':
				JRequest::setVar('view',	'maps');
				JRequest::setVar('layout',	'form');
				break;

			case 'config':
				JRequest::setVar('view',	'config');
				JRequest::setVar('layout',	'default');
				break;

			case 'info':
				JRequest::setVar('view',	'info');
				JRequest::setVar('layout',	'default');
				break;

			case 'cats':
				JRequest::setVar('view',	'cats');
				JRequest::setVar('layout',	'default');
				break;

			case 'newcat':
				JRequest::setVar('view',	'cats');
				JRequest::setVar('layout',	'form');
				break;

			case 'editcat':
				JRequest::setVar('view',	'cats');
				JRequest::setVar('layout',	'editform');
				break;

			case 'managecatpics':
				JRequest::setVar('view',	'cats');
				JRequest::setVar('layout',	'managecatpics');
				JRequest::setVar('task',	'default');
				break;

			case 'newcatpic':
				JRequest::setVar('view',	'cats');
				JRequest::setVar('layout',	'managecatpicsform');
				JRequest::setVar('task',	'new');
				break;

			case 'editcatpic':
				JRequest::setVar('view',	'cats');
				JRequest::setVar('layout',	'managecatpicsform');
				JRequest::setVar('task',	'edit');
				break;

			case 'files':
			case 'toshow':
			case 'tohide':
				JRequest::setVar('view',	'files');
				JRequest::setVar('layout',	'default');
				break;

			case 'upload':
				JRequest::setVar('view',	'files');
				JRequest::setVar('layout',	'upload');
				break;

			case 'newfiles':
				JRequest::setVar('view',	'files');
				JRequest::setVar('layout',	'import');
				break;

			case 'newfile':
			case 'editfile':
			case 'updateGeneratedValues':
				JRequest::setVar('view',	'files');
				JRequest::setVar('layout',	'form');
				break;

			case 'terrain':
				JRequest::setVar('view',	'terrain');
				JRequest::setVar('layout',	'default');
				break;

			case 'newterrain':
			case 'editterrain':
				JRequest::setVar('view',	'terrain');
				JRequest::setVar('layout',	'form');
				break;

			case 'comments':
				JRequest::setVar('view',	'comments');
				JRequest::setVar('layout',	'default');
				break;

			case 'editComment':
				JRequest::setVar('view',	'comments');
				JRequest::setVar('layout',	'form');
				break;

			case 'importjgt':
				JRequest::setVar('view',	'files');
				JRequest::setVar('layout',	'importjgt');
				break;
			case 'element':
				JRequest::setVar('view',	'files');
				JRequest::setVar('layout',	'element');
				break;

			case 'translations':
				JRequest::setVar('view',	'translations');
				JRequest::setVar('layout',	'default');
				break;
		}

		parent::display();
	}
}
