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
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Model Class Categorie
 */
class JtgModelMAp extends JModelLegacy
{
	/**
	 *
	 *
	 * @return return_description
	 */
	function __construct()
	{
		parent::__construct();

		$array = JFactory::getApplication()->input->get('cid', array(0), 'array');
		$edit	= JFactory::getApplication()->input->get('edit', true);

		if ($edit)
		{
			$this->setId((int) $array[0]);
		}
	}

	/**
	 * function_description
	 *
	 * @param   int $id
	 *
	 * @return return_description
	 */
	function setId($id)
	{
		// Set weblink id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 * function_description
	 *
	 * @param   string  $order
	 * @param   array  $cid

	 * @return boolean
	 */
	function saveorder($order, $cid = array())
	{
		$row = $this->getTable('jtg_maps');
		$groupings = array();

		// Update ordering values
		for ( $i = 0; $i < count($cid); $i++ )
		{
			$row->load((int) $cid[$i]);

			// Track categories
			$groupings[] = $row->mapid;

			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];

				if (!$row->store())
				{
					$this->setError($this->_db->getErrorMsg());

					return false;
				}
			}
		}

		// Execute updateOrder for each parent group
		$groupings = array_unique($groupings);

		foreach ($groupings as $group)
		{
			$row->reorder('id = ' . (int) $group);
		}

		return true;
	}
}
