<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5 and 3.x
 *
 *
 * @package     Comjtg
 * @subpackage  Frontend
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
 * LayoutHelper class for the jtg component
 *
 * @package     Comjtg
 * @subpackage  Frontend
 * @since       0.8
 */
class LayoutHelper
{
	/**
	 * function_description
	 *
	 * @param   unknown_type  $float       param_description
	 * @param   unknown_type  $expressive  param_description
	 *
	 * @return return_description
	 */
	static public function parseVoteFloat($float, $expressive = false)
	{
		if ( ( $float === null ) OR ( $float == 0 ) )
		{
			if ( $expressive )
			{
				return "<font class=\"emptyEntry\">" . JText::_('COM_JTG_NOT_VOTED') . "</font>";
			}
			else
			{
				return 0;
			}
		}

		$int = (int) round($float, 0);
		$stars = JText::_('COM_JTG_STAR' . $int);
		$return = "<font title=\"" . JtgHelper::getLocatedFloat($float) . "\">";
		$return .= $int;
		$return .= " ";

		if ( $expressive )
		{
			$return .= $stars;
		}

		$return .= "</font>";

		return $return;
	}

	/**
	 * function_description
	 *
	 * @return return_description
	 */
	static public function navigation()
	{
		$user = JFactory::getUser();
		$juser = new JUser($user->id);
		$uri = JFactory::getApplication()->input->get('layout');
		$navi = '';
		$navi .= '<div class="gps-navi">';
		$navi .= '<div class="navi-part"><a href="' .
				JRoute::_("index.php?option=com_jtg") .
				'">' . JText::_('COM_JTG_OVERVIEW') . '</a></div>';
		$navi .= '<div class="navi-part"><a href="' .
				JRoute::_("index.php?option=com_jtg&view=cats&layout=default") . '">' . JText::_('COM_JTG_CATS') . '</a></div>';
		$navi .= '<div class="navi-part"><a href="' .
				JRoute::_("index.php?option=com_jtg&view=files&layout=list") . '">' . JText::_('COM_JTG_TRACKS') . '</a></div>';
		$cfg = JtgHelper::getConfig();

		if ($user->get('id'))
		{
			// Erscheint nur, wenn User kein Gast
			if ( JtgHelper::userHasFrontendRights() )
			{
				$navi .= '<div class="navi-part"><a href="' .
						JRoute::_("index.php?option=com_jtg&view=files&layout=form") . '">' .
						JText::_('COM_JTG_ADD_FILE') . '</a></div>';
			}
			// Erscheint bei jedem Registrierten
			$navi .= '<div class="navi-part"><a href="' .
					JRoute::_("index.php?option=com_jtg&view=files&layout=user") . '">' .
					JText::_('COM_JTG_MY_FILES') . '</a></div>';

			if ( ($uri != null) AND ($uri == 'file') )
			{
				$gpsfile = new JtgModelFiles;
				$track = JFactory::getApplication()->input->get('id');
				$track = $gpsfile->getFile($track);

				if ( ($track !== null)
					AND (( $user->get('id') == $track->uid ) ) )
				{
					// User can delete or, update its own tracks
					$navi .= '<div class="navi-part"><a href="' .
							JRoute::_("index.php?option=com_jtg&view=files&layout=form&id=" .
									JFactory::getApplication()->input->get('id')
									) . '">' . JText::_('COM_JTG_UPDATE_GPS_FILE') . '</a></div>';
					$navi .= '<div class="navi-part"><a href="' .
							JRoute::_("index.php?option=com_jtg&controller=files&task=delete&id=" .
									JFactory::getApplication()->input->get('id')
									) . '">' . JText::_('COM_JTG_DELETE_FILE') . '</a></div>';
				}
			}
		}

		$navi .= '<div class="no-float"></div>';
		$navi .= '</div>';

		return $navi;
	}

	/**
	 * function_description
	 *
	 * @return return JTrackGallery footer
	 */
	static public function footer()
	{
		$params = JComponentHelper::getParams('com_jtg');

		if ($params->get('jtg_param_display_jtg_credits') == 1)
		{
			$footer = '<div class="gps-footer">' . JText::_('COM_JTG_POWERED_BY');
			$footer .= ' <a href="http://jtrackgallery.net"';
			$footer .= ' target="_blank">J!Track Gallery</a>';
			$footer .= "</div>\n";
		}
		else
		{
			// Add a comment with a link to jtrackgallery.net
			$footer = '<!--' . JText::_('COM_JTG_POWERED_BY');
			$footer .= ' <a href="http://jtrackgallery.net"';
			$footer .= ' target="_blank">J!Track Gallery</a>';
			$footer .= "-->\n";
		}

		return $footer;
	}

	/**
	 * function_description
	 *
	 * @return return_description
	 */
	static public function disclaimericons()
	{
		$disclaimericons = '<div class="gps-footer">' . JText::_('COM_JTG_DISCLAIMER_ICONS');
		$disclaimericons .= ' ' . JText::_('COM_JTG_SUBMITTER') . ': <a href="" target="_blank"></a></div>';

		return $disclaimericons;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $document  param_description
	 *
	 * @return return_description
	 */
	static public function parseMap($document)
	{
		$document->addScript('http://www.openlayers.org/api/OpenLayers.js');
		$document->addScript('components/com_jtg/assets/js/fullscreen.js');
		$document->addScript('http://www.openstreetmap.org/openlayers/OpenStreetMap.js');
		$document->addScript('components/com_jtg/assets/js/jtg.js');
	}

	/**
	 * For CSS-Declaration
	 *
	 * @param   unknown_type  $params  param_description
	 *
	 * @return string toptrack + id
	 */
	static public function parseToptracks($params)
	{
		$i = 0;

		if ($params->get('jtg_param_newest') != 0)
		{
			$i++;
		}

		if ($params->get('jtg_param_mostklicks') != 0)
		{
			$i++;
		}

		if ($params->get('jtg_param_best') != 0)
		{
			$i++;
		}

		if ($params->get('jtg_param_rand') != 0)
		{
			$i++;
		}

		return "toptracks_" . $i;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $where   param_description
	 * @param   unknown_type  $access  param_description
	 * @param   unknown_type  $model   param_description
	 * @param   unknown_type  $newest  param_description
	 *
	 * @return return_description
	 */
	static public function parseTopNewest($where, $access, $model, $newest)
	{
		if ($access === null)
		{
			$access = $where;
		}

		$limit = "LIMIT 0," . $newest;

		return $model->getTracksData("ORDER BY a.id DESC", $limit, $access);
	}

	/**
	 * function_description
	 *
	 * @param   string        $where   input where statement
	 * @param   string        $access  File access level
	 * @param   unknown_type  $model   param_description
	 * @param   unknown_type  $hits    param_description
	 *
	 * @return return_description
	 */
	static public function parseTopHits($where, $access, $model, $hits)
	{
		if ($access === null)
		{
			$access = $where;
		}

		$limit = "LIMIT 0," . $hits;

		return $model->getTracksData("ORDER BY a.hits DESC", $limit, $access);
	}

	/**
	 * function_description
	 *
	 * @param   string        $where   input where statement
	 * @param   string        $access  File access level
	 * @param   unknown_type  $model   param_description
	 * @param   unknown_type  $limit   param_description
	 *
	 * @return return_description
	 */
	static public function parseTopRand($where, $access, $model, $limit)
	{
		if ($access === null)
		{
			$access = $where;
		}

		$limit = "LIMIT 0," . $limit;

		return $model->getTracksData("ORDER BY RAND()", $limit, $access);
	}

	/**
	 * function_description
	 *
	 * @param   string        $where      input where statement
	 * @param   string        $access     File access level
	 * @param   unknown_type  $model      param_description
	 * @param   unknown_type  $best       param_description
	 * @param   unknown_type  $showstars  param_description
	 *
	 * @return return_description
	 */
	static public function parseTopBest($where, $access, $model, $best, $showstars)
	{
		if ($access === null)
		{
			$access = $where;
		}

		$limit = "LIMIT 0," . $best;
		$translate = array(
				0 => "nostar",
				1 => "onestar",
				2 => "twostar",
				3 => "threestar",
				4 => "fourstar",
				5 => "fivestar",
				6 => "sixstar",
				7 => "sevenstar",
				8 => "eightstar",
				9 => "ninestar",
				10 => "tenstar"
		);

		return array(
				$showstars,
				$model->getTracksData("ORDER BY a.vote DESC", $limit, $access),
				$translate);
	}

	/*
	 static private function giveBest($model,$best,$bad=false) {
	echo "function giveBest: deprecated";
	$votes = $model->getVotesData();
	$calc = 0;
	$i=0;
	$return = array();
	$translate = array( 0 => "nostar", 1 => "onestar", 2 => "twostar", 3 => "threestar", 4 => "fourstar", 5 => "fivestar", 6 => "sixstar", 7 => "sevenstar", 8 => "eightstar", 9 => "ninestar", 10 => "tenstar" );
	for ($j = 0; $j <= count($votes); $j++)
	{
	if (isset($votes[$j])) // Clean for last flow (necessary to calc last file)
	{
	$vote = $votes[$j];
	$newid = $vote->id;
	$rt = $vote->rating;
	}
	else $newid = 0; // Save for first flow

	if ( isset($oldid) AND ( $oldid != $newid ) )
	{ // Calculate the voting-average for one file if all votings found
	$stars = (int) round($calc / $i,0);
	$voting = (float) round($calc / $i,2);
	$index = (int) (round($calc / $i,4)*10000);
	while (true) { // If index already exist
	if (isset($return[$index]))
		$index++;
	else break;
	}
	$obj = array();
	$obj['id'] = $oldid;
	$obj['rate'] = $stars;
	$obj['voting'] = $voting;
	$obj['count'] = $i;
	$obj['class'] = $translate[$stars];
	$return[$index] = $obj; // Index is bestvote - better to sort
	$calc = 0;
	$i = 0;
	$oldid = 0;
	}
	if ( $calc == 0 )
	{ // Init first flow per file
	$oldid = $newid;
	$calc = $rt;
	$i++;
	}
	else
	{ // Summate all votings and store incident (to calc average later)
	$calc += $rt;
	$i++;
	}
	}
	if ($bad === false) // Sort to best or bad
	krsort($return);
	else
		ksort($return);

	$limitreturn = array(); // Limitation in new array
	foreach ( $return AS $key => $voting )
	{
	if (!isset($voting)) break;
	$limitreturn[] = array_shift($return);
	}
	return $limitreturn;
	}
	*/

	/**
	 *  parse Param of a user(s)
	 *
	 * @param   array  $val  array of user(s) ID
	 *
	 * @return sql where statement to select user(s)
	 */
	static private function parseParam_User($val)
	{
		$where = null;

		if (is_array($val))
		{
			if ($val[0] != 0)
			{
				$subwhere = array();

				foreach ($val as $user)
				{
					$subwhere[] = "a.uid = " . $user;
				}

				$where .= "( " . implode(' OR ', $subwhere) . " )";
			}
		}
		elseif ($val != 0)
		{
			$where .= "a.uid = " . $val;
		}

		return $where;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $val  param_description
	 *
	 * @return return_description
	 */
	static private function parseParam_Cats($val)
	{
		$catswhere = null;

		if (is_array($val))
		{
			$subwhere = array();

			foreach ($val as $cat)
			{
				if ($cat == -1)
				{
					return null;
				}

				$subwhere[] = "a.catid LIKE '%" . $cat . "%'";
			}

			$catswhere .= "( " . implode(' OR ', $subwhere) . " )";
		}
		elseif ($val != -1)
		{
			$catswhere .= "a.catid LIKE '%" . $val . "%'";
		}

		return $catswhere;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $val   param_description
	 * @param   unknown_type  $cats  param_description
	 *
	 * @return return_description
	 */
	static private function parseParam_Subcats($val, $cats)
	{
		$catswhere = null;

		if (is_array($val))
		{
			$subwhere = array();

			foreach ($val as $cat)
			{
				if ($cat == -1)
				{
					return null;
				}

				$subwhere[] = "( " . self::getParentcats($cat, $cats, true) . " )";
			}

			$catswhere .= "( " . implode(' OR ', $subwhere) . " )";
		}
		elseif ($val != -1)
		{
			$catswhere .= "( " . self::getParentcats($val, $cats, true) . " )";
		}

		return $catswhere;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $val  param_description
	 *
	 * @return return_description
	 */
	static private function parseParam_Usergroup($val)
	{
		$where = null;

		if (is_array($val))
		{
			$subwhere = array();

			foreach ($val as $grp)
			{
				if ($grp == -1)
				{
					return null;
				}

				$subwhere[] = "a.access = " . $grp;
			}

			$where .= "( " . implode(' OR ', $subwhere) . " )";
		}
		elseif ( ($val != -1) AND (!is_null($val) ) )
		{
			$where .= "a.access = " . $val;
		}

		return $where;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $val  param_description
	 *
	 * @return NULL|Ambigous <NULL, string>
	 */
	static private function parseParam_Terrain($val)
	{
		$where = null;

		if (is_array($val))
		{
			$subwhere = array();

			foreach ($val as $terrain)
			{
				if ($terrain == -1)
				{
					return null;
				}

				$subwhere[] = "a.terrain LIKE '%" . $terrain . "%'";
			}

			$where .= "( " . implode(' ) OR ( ', $subwhere) . " )";
		}
		elseif ($val != -1)
		{
			$where = "a.terrain LIKE '%" . $val . "%'";
		}

		return $where;
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $val  param_description
	 *
	 * @return return_description
	 */
	static private function parseParam_LevelFrom($val)
	{
		if ( ($val != 0) AND (!is_null($val) ) )
		{
			return "a.level >= " . $val;
		}
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $val  param_description
	 *
	 * @return return_description
	 */
	static private function parseParam_LevelTo($val)
	{
		if ( ($val != 5) AND (!is_null($val) ) )
		{
			return "a.level <= " . $val;
		}
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $val  param_description
	 *
	 * @return return_description
	 */
	static private function parseParam_VotingFrom($val)
	{
		if ( ($val != 0) AND (!is_null($val) ) )
		{
			return "a.vote >= " . $val;
		}
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $val  param_description
	 *
	 * @return return_description
	 */
	static private function parseParam_VotingTo($val)
	{
		if ( ($val != 10) AND (!is_null($val) ) )
		{
			return "a.vote <= " . $val;
		}
	}

	/**
	 * function_description
	 *
	 * @param   unknown_type  $cats  param_description
	 *
	 * @return string
	 */
	static public function filterTracks($cats)
	{
		$params = JComponentHelper::getParams('com_jtg');

		$access = $params->get('jtg_param_otherfiles');
		$where = array();
		$catswhere = array();
		$layout = self::parseParam_User($params->get('jtg_param_user'));

		if ($layout !== null)
		{
			$where[] = $layout;
		}

		$layout = self::parseParam_Cats($params->get('jtg_param_cats'));

		if ($layout !== null)
		{
			$catswhere[] = $layout;
		}

		$layout = self::parseParam_Usergroup($params->get('jtg_param_usergroup'));

		if ($layout !== null)
		{
			$where[] = $layout;
		}

		$layout = self::parseParam_Terrain($params->get('jtg_param_terrain'));

		if ($layout !== null)
		{
			$where[] = $layout;
		}

		$layout = self::parseParam_LevelFrom($params->get('jtg_param_level_from'));

		if ($layout !== null)
		{
			$where[] = $layout;
		}

		$layout = self::parseParam_LevelTo($params->get('jtg_param_level_to'));

		if ($layout !== null)
		{
			$where[] = $layout;
		}

		$layout = self::parseParam_VotingFrom($params->get('jtg_param_vote_from'));

		if ($layout !== null)
		{
			$where[] = $layout;
		}

		$layout = self::parseParam_VotingTo($params->get('jtg_param_vote_to'));

		if ($layout !== null)
		{
			$where[] = $layout;
		}

		if (count($where) == 0)
		{
			$where = "";
		}
		else
		{
			$where = "( " . implode(" AND \n", $where) . " )";
		}

		if (count($catswhere) == 0)
		{
			$catswhere = "";
		}
		else
		{
			$catswhere = "( " . implode(" OR \n", $catswhere) . " )";
		}

		if ( ( $catswhere != "") AND ( $where != "" ) )
		{
			$operand = " AND \n";
		}
		else
		{
			$operand = "";
		}

		$return = $where . $operand . $catswhere;

		return $return;
	}

	/**
	 * build the SQL where statement to filter one category subcategories
	 *
	 * @param   integer  $catid    category ID
	 * @param   array    $cats     param_description
	 * @param   boolean  $lockage  if false only return sub categories
	 *
	 * @return string SQL where statement to select a category ($lockage=true) and its subcategories
	 */
	static private function getParentcats($catid, $cats, $lockage = false)
	{
		$returncats = array();

		if ( $lockage !== false )
		{
			$returncats[] = "a.catid LIKE '%" . $catid . "%'";
		}

		foreach ($cats AS $cat)
		{
			if ($cat->parent_id == $catid)
			{
				$returncats[] = "a.catid LIKE '%" . $cat->id . "%'";
			}
		}

		$returncats = implode(" OR ", $returncats);

		return $returncats;
	}
}
