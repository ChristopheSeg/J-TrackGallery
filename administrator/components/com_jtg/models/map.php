<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 * 
 * @package    Comjtg
 * @author     Christophe Seguinot <christophe@jtrackgallery.net>
 * @copyright  2013 J!Track Gallery, InJooosm and joomGPStracks teams
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Model Class Categorie
 */
class JtgModelMAp extends JModelLegacy
{
	function __construct()
	{
		parent::__construct();

		$array = JFactory::getApplication()->input->get('cid', array(), 'array');
		$edit	= JFactory::getApplication()->input->get('edit',true);
		if($edit)
		$this->setId((int)$array[0]);
	}

	/**
	 *
	 * @param int $id
	 */
	function setId($id)
	{
		// Set weblink id and wipe data
		$this->_id		= $id;
		$this->_data	= null;
	}

	/**
	 *
	 * @param   array  $cid
	 * @param   string  $order
	 * @return boolean
	 */
	function saveorder($cid = array(), $order)
	{
		$row =& $this->getTable('jtg_maps');
		$groupings = array();

		// update ordering values
		for( $i=0; $i < count($cid); $i++ )
		{
			$row->load( (int) $cid[$i] );
			// track categories
			$groupings[] = $row->mapid;

			if ($row->ordering != $order[$i])
			{
				$row->ordering = $order[$i];
				if (!$row->store()) {
					$this->setError($this->_db->getErrorMsg());
					return false;
				}
			}
		}

		// execute updateOrder for each parent group
		$groupings = array_unique( $groupings );
		foreach ($groupings as $group){
			$row->reorder('id = '.(int) $group);
		}

		return true;
	}
}
