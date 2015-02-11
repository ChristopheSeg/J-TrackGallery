<?php

/**
 * @component   J!Track Gallery (jtg) for Joomla! 2.5 and Joomla 3.x
 *
 * @package     Com_Jtg
 * @subpackage  Backend
 * @author      Christophe Seguinot <christophe@jtrackgallery.net>
 * @author      Pfister Michael, JoomGPStracks <info@mp-development.de>
 * @author      Christian Knorr, InJooOSM  <christianknorr@users.sourceforge.net>
 * @copyright   2015 J!TrackGallery, InJooosm and joomGPStracks teams
 *
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 *
 * @link        http://jtrackgallery.net/
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * class JtgControllerfor the jtg component
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @since       0.8
 */
class JtgController extends JControllerLegacy
{
	/**
	 * function_description
	 */
	public function __construct()
	{
		parent::__construct();
		$this->registerTask('savecat', 'savecat');
	}

	/**
	 * View method for JTG
	 *
	 * This function override joomla.application.component.controller
	 * View Cache not yet implemented in JTrackGallery
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   array    $urlparams  An array of safe url parameters and their variable types
	 *
	 * @return void
	 */
	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT . '/helpers/jtg.php';

		// JtgHelper::updateReset();

		// Load the submenu.
		JtgHelper::addSubmenu(JFactory::getApplication()->input->get('view', 'jtg'));

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
