<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: view.html.php,v 1.1 2011/04/03 08:41:47 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage frontend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');

/**
 * HTML View class for the jtg component
 */
class jtgViewCats extends JView {

function display($tpl = null)
{
	$mainframe =& JFactory::getApplication();
	$this->lh = layoutHelper::navigation();
	$this->footer = layoutHelper::footer();
	$pathway =& $mainframe->getPathway();
	$pathway->addItem(JText::_('COM_JTG_CATS'), '');
	$sitename = $mainframe->getCfg('sitename');
	$document = JFactory::getDocument();
	$document->setTitle(JText::_('COM_JTG_CATS')." - ".$sitename);
	$model = $this->getModel();
	$this->cats = $model->getCats();


	parent::display($tpl);
}
}
