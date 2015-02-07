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

// Include library dependencies
jimport('joomla.filter.input');

/**
 * Table class
 *
 * @since  0.1
 */
class TableJTG_Cats extends JTable
{
	var $id = null;

	var $parent = null;

	var $title = null;

	var $description = null;

	var $image = null;

	var $ordering = null;

	var $published = null;

	var $checked_out = null;

	/**
	 * function_description
	 *
	 * @param   object  &$db  the  database
	 *
	 */
	function __construct (& $db)
	{
		parent::__construct('#__jtg_cats', 'id', $db);
	}

	/**
	 * bind function
	 *
	 * @param   array   $array
	 * @param   string  $ignore
	 *
	 * @return object
	 */
	function bind ($array, $ignore = '')
	{
		if (key_exists('params', $array) && is_array($array['params']))
		{
			$registry = new JRegistry;
			$registry->loadArray($array['params']);
			$array['params'] = $registry->toString();
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity
	 *
	 * @access public
	 * @return boolean True on success
	 */
	function check ()
	{
		return true;
	}

	/**
	 *
	 *
	 * @return return_description
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;

		return 'com_jtg.cat.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return      string
	 *
	 * @since       2.5
	 */
	protected function _getAssetTitle()
	{
		return 'J!TrackGallery';
	}

	/**
	 * Method to get the asset-parent-id of the item
	 *
	 * @return      int
	 */
	protected function _getAssetParentId()
	{
		// We will retrieve the parent-asset from the Asset-table
		$assetParent = JTable::getInstance('Asset');

		// Default: if no asset-parent can be found we take the global asset
		$assetParentId = $assetParent->getRootId();

		// Find the parent-asset
		if (($this->catid)&& !empty($this->catid))
		{
			// The item has a category as asset-parent
			$assetParent->loadByName('com_jtg.category.' . (int) $this->catid);
		}
		else
		{
			// The item has the component as asset-parent
			$assetParent->loadByName('com_jtg');
		}
		// Return the found asset-parent-id
		if ($assetParent->id)
		{
			$assetParentId = $assetParent->id;
		}

		return $assetParentId;
	}
}
