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

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a multiple item select element
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @since       0.8
 */
/**
 * JElementUsergroupList class for the jtg component
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @since       0.8
 */

class JElementUsergroupList extends JFormField
{
	/**
	 * Element name
	 *
	 * @access       protected
	 * @var          string
	 */
	var $_name = 'MultiList';

	/**
	 * function_description
	 *
	 * @param   unknown_type  $name          param_description
	 * @param   unknown_type  $value         param_description
	 * @param   unknown_type  &$node         param_description
	 * @param   unknown_type  $control_name  param_description
	 *
	 * @return return_description
	 */
	protected function getInput($name, $value, &$node, $control_name)
	{
		// TODO You still need to replace the references to $control_name
		// Base name of the HTML control.
		$ctrl  = $control_name . '[' . $name . ']';

		// Construct an array of the HTML OPTION statements.
		$options = array ();

		foreach ($node->children() as $option)
		{
			$val   = $option->attributes('value');
			$text  = $option->data();
			$options[] = JHtml::_('select.option', $val, JText::_($text));
		}

		// Construct the various argument calls that are supported.
		$attribs       = ' ';

		if ($v = $node->attributes('size'))
		{
			$attribs       .= 'size="' . $v . '"';
		}

		if ($v = $node->attributes('class'))
		{
			$attribs       .= 'class="' . $v . '"';
		}
		else
		{
			$attribs       .= 'class="inputbox"';
		}

		if ($m = $node->attributes('multiple'))
		{
			$attribs       .= ' multiple="multiple"';
			$ctrl          .= '[]';
		}

		// Render the HTML SELECT list.
		return JHtml::_('select.genericlist', $options, $ctrl, $attribs, 'value', 'text', $value, $control_name . $name);
	}
}
