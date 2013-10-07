<?php


/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooosm and joomGPStracks teams
 * @package    com_jtg
 * @subpackage frontend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

/**
 * HTML View class for the jtg component
 */

class JtgViewFiles extends JView
{
	/**
	 * Returns true|false if it is allowed to see the file
	 * @param <obj> $this
	 * @return <bool>
	 */
	function maySeeSingleFile($param) {
		if(!isset($param->track))
		return JText::_('COM_JTG_NO_RESSOURCE');
		$published = (bool) $param->track->published;
		$access = (int) $param->track->access;
		/* $access:
		 0 = public
		 1 = registered
		 2 = special // ie admin
		 9 = private
		 */
		$uid = JFactory::getUser()->id;


		if(JFactory::getUser()->get('isRoot'))
		    {
		    $admin = true;
		    }
		    else
		    {
		    $admin = false;
		    }
		if($uid)
		    {
		    $registred = true;
		    }
		    else
		    {
		    $registred = false;
		    }
 
		$owner = (int) $param->track->uid;

		if ( ( $access == 9 ) AND ( $uid != $owner ) ) return false; // private only
		if (($registred) AND ($uid == $owner)) {
			$myfile = true;
		} else {
			$myfile = false;
		}

		if ($registred) {
			if ($myfile)
			return true;
			elseif (!$published) return false;
			elseif ($access != 2) return true;
			elseif (($admin) AND ($access == 2)) return true;
			else
			return false;
		} else {
			if (!$published)
			return false;
			elseif ($access == 0) return true;
			else
			return false;
		}
	}

	/**
	 *
	 * @param <type> $tpl
	 * @return <type>$gps
	 */
	function display($tpl = null) {
		$file = JPATH_SITE . DS . "components" . DS . "com_jtg" . DS . "models" . DS . "jtg.php";
		require_once $file;

		if ($this->getLayout() == 'form')
		: $this->_displayForm($tpl);
		return;
		endif;

		if ($this->getLayout() == 'file')
		: $this->_displayFile($tpl);
		return;
		endif;

		if ($this->getLayout() == 'list')
		: $this->_displayList($tpl);
		return;
		endif;

		if ($this->getLayout() == 'user')
		: $this->_displayUserTracks($tpl);
		return;
		endif;

		parent :: display($tpl);
	}

	/**
	 *
	 * @global <type> $mainframe
	 * @param <type> $tpl
	 * @return <type>
	 */
	function _displayForm($tpl = null) {
		$mainframe =& JFactory::getApplication();

		JHTML :: script('jquery.js', 'components/com_jtg/assets/js/', false);
		JHTML :: script('multifile.js', 'components/com_jtg/assets/js/', false);
		JHTML :: _('behavior.modal');
		JHTML :: _('behavior.tooltip');
		$cache = & JFactory :: getCache('com_jtg');
		$yesnolist = array(
		array('id' => 0, 'title' => JText::_('JNO')),
		array('id' => 1, 'title' => JText::_('JYES'))
		);

		$cfg = JtgHelper :: getConfig();

		// Make sure you are logged in and have the necessary access rights

		// TODO check this if ($user->get('gid') < $cfg->gid) {
		if (! JtgHelper :: userHasFrontendRights() ) {
		    JResponse :: setHeader('HTTP/1.0 403', true);
			JError :: raiseWarning(403, JText :: _('ALERTNOTAUTH'));
			return;
		}

		$editor = & JFactory :: getEditor();
		$pathway = & $mainframe->getPathway();
		$lh = layoutHelper :: navigation();
		$footer = layoutHelper :: footer();
		$model = $this->getModel();
		//		$uri = & JFactory :: getURI();
		//		$uri = $uri->get('_uri');
		$uri = "index.php?option=com_jtg&view=files&layout=form";
		$uri = JRoute :: _($uri,false);
		// Add pathway item
		$pathway->addItem(JText :: _('New'), '');
		$sitename = $mainframe->getCfg('sitename');
		$row = $model->getCats();
		$terrain = $model->getTerrain(" WHERE published=1 ");
		//		$terrain = $cache->get(array (
		//		$model, 'getTerrain' ), array ());
		$terms = JRoute :: _('index.php?option=com_content&view=article&tmpl=component&id=' . $cfg->terms_id,false);
		$id = (JRequest :: getInt('id', NULL));
		$catid = "";
			$document = JFactory::getDocument();
		if (isset ($id)) {
			// update part
			// $track = $model->getTrack($id);
			// $track = $cache->get(array($model, 'getTrack'), array($id));
			$track = $cache->get(array ( $model, 'getFile' ), array ( $id ));
			$this->track = $track;
			$this->id = $id;
			$catid = $track->catid;
			$catid = explode(",",$catid);
			$pathway->addItem(JText::_( 'COM_JTG_UPDATE'), '');
			$document = JFactory::getDocument();
			$document->setTitle(JText::_( 'COM_JTG_UPDATE') . " (" . $track->title . ") - " . $sitename);
			$selterrain = explode(',', $track->terrain);
			$value_published = $track->published;
			$value_hidden = $track->hidden;
			$sellevel = $track->level;
		}
		else
		{
			//new file
			$document->setTitle(JText::_( 'COM_JTG_NEW_TRACK') . " - " . $sitename);
			$track = array("access"=>"0");
			$track = JArrayHelper::toObject($track);
			$catid = null;
			$selterrain = null;
			$value_published = 1;
			$value_hidden = 0;
			$sellevel = 0;
		}
		$level = $model->getLevelSelect($sellevel);
		$img_dir = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . $id.DS;
		$imgpath = JURI::root().'images/jtrackgallery'.$id . "/";
		$images = null;
		$imgcount = 0;
		if(JFolder::exists($img_dir)) {
			$imgs = JFolder::files($img_dir);
			if($imgs)
			{
				foreach($imgs AS $image)
				{
					$imgcount++;
					$images .= "<input type=\"checkbox\" name=\"deleteimage_".str_replace('.',null,$image) . "\" value=\"" . $image . "\">".JText::_( 'COM_JTG_DELETE_IMAGE' ) . " (" . $image . ")<br />".
					"<img src=\"" . $imgpath.$image . "\" alt=\"" . $image . "\" title=\"" . $image . "\" /><br /><br />\n";
				}
			}
		}

		$size = count($row);
		if ( $size > 6 ) $size = 6;
		$lists['content'] = JHTML :: _('select.genericlist', $row, 'catid[]', 'multiple="multiple" size="'.$size.'"', 'id', 'title', $catid);
		$size = count($terrain);
		if ( $size > 6 ) $size = 6;
		$lists['terrain'] = JHTML :: _('select.genericlist', $terrain, 'terrain[]', 'multiple="multiple" size="'.$size.'"', 'id', 'title', $selterrain);
		//			$lists['access'] = JHTML :: _('select.genericlist', $access, 'access', 'size="4"', 'id', 'text', $track->access);
		$lists['access'] = JtgHelper::getAccessList($track->access);
		$lists['hidden']	= JHTML::_('select.genericlist', $yesnolist, 'hidden', 'class="inputbox" size="1"', 'id', 'title',$value_hidden);
		$lists['published']	= JHTML::_('select.genericlist', $yesnolist, 'published', 'class="inputbox" size="1"', 'id', 'title',$value_published);
		// $gps = new gpsClass();
		$this->imgcount = $imgcount;
		$this->images = $images;
		$this->kml = NULL; // TODO $this->kml = $start;
		$this->lh = $lh;
		$this->footer = $footer;
		$this->track = $track;
		$this->editor = $editor;
		$this->lists = $lists;
		$this->action = $uri;
		$this->cfg = $cfg;
		$this->terms = $terms;
		$this->level = $level;
		//		$this->comments = comments;
		//	$this->gps = $gps;

		parent :: display($tpl);
	}

	function _displayFile($tpl) {
		$mainframe =& JFactory::getApplication();
		jimport('joomla.filesystem.file');
		$mapsxml = JPATH_COMPONENT_ADMINISTRATOR . DS . 'views' . DS . 'maps' . DS . 'maps.xml';
		$params_maps = new JRegistry( 'com_jtg', $mapsxml );
		$this->params = $params_maps;
		$params = &JComponentHelper::getParams( 'com_jtg' );
		$sitename = $mainframe->getCfg('sitename');
		// JHTML::_('behavior.modal');	// with this option IE doesn't work
		JHTML :: _('behavior.combobox');
		$cache = & JFactory :: getCache('com_jtg');

		if ( $params->get("jtg_param_lh") == 1 )
		$lh = layoutHelper :: navigation();
		else
		$lh = null;
		$footer = layoutHelper :: footer();
		$cfg = JtgHelper :: getConfig();

		$model = $this->getModel();
		$pathway = & $mainframe->getPathway();
		$id = & JRequest :: getInt('id');
		// $track = $model->getTrack($id);
		// $track = $cache->get(array($model, 'getTrack'), array($id));
		// if (!$id) die ("Schau mal in datei view.html.php Zeile 152 :-P");
		// if (!$id) $id = 1;
		//		$file = JPATH_SITE . DS . "components" . DS . "com_jtg" . DS . "models" . DS . "jtg.php";
		//		require_once $file;
		$sortedcats = JtgModeljtg::getCatsData(true);
		$track = $cache->get(array ( $model, 'getFile' ), array ( $id ));
		if ( ( !$track ) OR ( $track->id === null ) )
		{
			//		$this->model = $model;
			$this->lh = $lh;
			$this->footer = $footer;
			parent :: display($tpl);
			return false;
		}
		// $vote = $model->getVotes($id);
		//			$vote = $cache->get(array (
		//			$model,
		//			'getVotes'
		//			), array (
		//			$id
		//			));
		$vote = $model->getVotes($id);
		$pathway->addItem($track->title, '');
		$document = JFactory::getDocument();
		$document->setTitle($track->title . " - " . $sitename);
		$date = JHTML :: _('date', $track->date, JText :: _('COM_JTG_DATE_FORMAT_LC4'));
		$profile = JtgHelper::getProfileLink($track->uid, $track->user);
		$comments = $model->getComments($id, $cfg->ordering);
		//		$comments = $cache->get(array (
		//			$model,
		//			'getComments'
		//		), array (
		//			$id,
		//			$cfg->ordering
		//		));
		$user = & JFactory :: getUser();
		$document = & JFactory :: getDocument();
		// load Openlayers stylesheet first (for overridding)
		$document->addStyleSheet('http://openlayers.org/dev/theme/default/style.css');
		// then load jtg_map stylesheet
		$tmpl = ($cfg->template = "") ? $cfg->template : 'default';
		$document->addStyleSheet(JURI::base().'components/com_jtg/assets/template/'.$tmpl.'/jtg_map_style.css');
		// then override style with user templates

		$template = $mainframe->getTemplate();
		$template_jtg_map_style='templates/' . $template . '/css/jtg_map_style.css';
		if ( JFile::exists($template_jtg_map_style) )
		{
		    $document->addStyleSheet( 'templates/' . $template . '/css/jtg_map_style.css' );
		} 
		    
		// Kartenauswahl BEGIN
		JHTML :: script('jtg.js', 'components/com_jtg/assets/js/', false);

		//			$document->addScript('http://maps.google.com/maps?file=api&amp;v=2&amp;key='.$cfg->apikey);
		//			$document->addScript('http://api.maps.yahoo.com/ajaxymap?v=3.0&appid=');
		$document->addScript('http://www.openlayers.org/api/OpenLayers.js');
		$document->addScript('components' . DS . 'com_jtg' . DS . 'assets' . DS . 'js' . DS . 'fullscreen.js');
		//		JHTML::script('OpenLayers.js', 'components' . DS . 'com_jtg' . DS . 'assets' . DS . 'js' . DS . 'OpenLayers'., false); // IE-Fehler
		$document->addScript('http://www.openstreetmap.org/openlayers/OpenStreetMap.js');
		$document->addScript('http://www.openlayers.org/api/Ajax.js');
		// 		$document->addScript('components/com_jtg/assets/js/GPX.js');
		//		$document->addScript('components' . DS . 'com_jtg' . DS . 'assets' . DS . 'js' . DS . 'jtg.js');
		// 		$document->addScript('');

//	TODO remove script from file.php and use method addscript
//			if ( ($this->params->get("jtg_param_show_heightchart"))  AND $track ) {
//		    $document->addScript('http://code.highcharts.com/highcharts.js');
//		    $document->addScript('http://code.highcharts.com/modules/exporting.js');
//		}	
		
		$action = "index.php?option=com_jtg&amp;controller=download&amp;task=download";

		$gps = new gpsClass();
		// Kartenauswahl BEGIN
		$map = $cache->get(array ( $gps, 'writeTrackOL' ), array ( $track, $params ));
		// Kartenauswahl END
		$unit = $cfg->unit;
		$distance_float = (float) $track->distance;
		//		$distance_float = (int)
		$distance = JtgHelper::getLocatedFloat($distance_float,0,$unit);
		// charts
		$file = '.' . DS . 'images' . DS . 'jtrackgallery' . DS . 'uploaded_tracks' . DS . strtolower($track->file);
		/*
		 // ToDo: mehrere Profile in einem
		 $coords = array();
		 $i = 0;
		 while (true) {
		 $coords_tmp = $cache->get(array($gps, 'getCoords'), array($file,$i));
		 if ($coords_tmp)
		 $coords = array_merge($coords, $coords_tmp);
		 else
		 break;
		 $i++;
		 }
		 */

		$coords = $cache->get(array ( $gps, 'getCoords' ), array ( $file ));
		$distances = $cache->get(array ( $gps, 'getDistances' ), array ( $coords ));

		if (isset ($coords[0][3])) {
			// 		Speedprofile
			$speeddata = $cache->get(array ( $gps, 'createSpeedData' ), array ( $coords, $distances, $unit ));
		} else $speeddata = false;
		if ((!$speeddata) OR (preg_match('/0,0,0,0,0,0,0,0,0,0/',$speeddata))) // change this test according to new createSpeeddata method
		//	give scatter-plot a chance
		$speeddata = false;
		if ($coords[0][2] != 0) {
			// Heightprofile ($heighdata)
			$heighdata = $cache->get(array ( $gps, 'createElevationData' ), array ( $coords, $distances ));
		}

		// heartbeat
		if (isset ($coords[0][4]) && $coords[0][4] > 0) {
			// $beatdata = $gps->createBeatsData($coords);
			$beatdata = $cache->get(array ( $gps, 'createBeatsData' ), array ( $coords, $distances ));
			$this->beatdata = $beatdata;
		}

		// Klicklinks for every track in one file (at the moment not active)
		// function giveClickLinks is not performant!
		//			$clicklist = $cache->get(array (
		//			$gps,
		//			'giveClickLinks'
		//			), array (
		//			$file
		//			));
		//			if ((count($clicklist)) < 2) {
		//				$clicklist = false;
		//			}

		// load images if exists
		$img_dir = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery' . DS . $id;
		if (JFolder :: exists($img_dir)) {
			$exclude = array ( '.db', '.txt' );
			$images = JFolder :: files($img_dir, '', true, false, $exclude);
		}

		$jscript = "<script language=\"javascript\" type=\"text/javascript\">
					Joomla.submitbutton = function(pressbutton)  {
						var form = document.adminForm;
				 // do field validation
						if (document.getElementById('format').value == \"\"){
							alert( \"" . JText::_( 'COM_JTG_NEED_FORMAT', true) . "\" );
						} else {
							submitform( pressbutton);
						}
					}
				</script>\n";

		$galscript = "<script language=\"javascript\" type=\"text/javascript\">
	Joomla.startGallery = function()  {
		var myGallery = new gallery($('myGallery'), {
			timed: true,
			showArrows: true,
			embedLinks: false,
			showCarousel: false
		});
	}
	window.addEvent('domready',startGallery);
</script>\n";

		$imageBlock = null;
		if((isset($images) AND (count($images) > 0))) {
			$this->images = $images;
			switch ($cfg->gallery) {
				case 'jd2' :
					$document->addScript('components/com_jtg/assets/js/jd.gallery.js');
					//	case 'jd21' :
					$imageBlock .= "<div id=\"myGallery\">";
					foreach($images as $image)
					{
						$ext = JFile::getExt($image);
						$imgtypes = explode(',',$cfg->type);
						if ( in_array(strtolower($ext),$imgtypes) )
						{
							$imageBlock .= "	<div class=\"imageElement\">
			<h3>" . $track->title . " <small>(" . $image . ")</small></h3>
			<p></p>
			<img src=\"".JURI::base() . "images/jtrackgallery" . $id . "/" . $image . "\" class=\"full\" height=\"0px\" />
		</div>\n";
						}
					}
					$imageBlock .= "</div>\n";
					break;
				case 'jd21' :
					$document->addScript('components/com_jtg/assets/js/jd.gallery.js');
					$imageBlock = "<p></p>"; // dummy
					break;
				case 'straight' :
					$i=0;
					foreach($images as $image)
					{
						$i++;
						$ext = JFile::getExt($image);
						$imgtypes = explode(',',$cfg->type);
						if ( in_array(strtolower($ext),$imgtypes) ) {
							if ($i != 0)
							$imageBlock .= "<br /><br />";
							$imageBlock .= "<img src=\"".JURI::base() . "images/jtrackgallery" . $id . "/" . $image . "\" alt=\"" . $track->title . " (" . $image . ")" . "\" title=\"" . $track->title . " (" . $image . ")" . "\" />\n";
						}
					}
					break;
			}
		}
		else {
		    $this->images = false;
		}
		$stars = array(
		1 => "one",
		2 => "two",
		3 => "three",
		4 => "four",
		5 => "five",
		6 => "six",
		7 => "seven",
		8 => "eight",
		9 => "nine",
		10 => "ten");
		$stars =& JArrayHelper::toObject($stars);

		$level = $model->getLevel($track->level);
		$this->lh = $lh;
		$this->params = $params;
		$this->sortedcats = $sortedcats;
		$this->stars = $stars;
		$this->jscript =  $jscript;
		$this->galscript = $galscript;
		$this->footer = $footer;
		$this->map = $map;
		$this->cfg = $cfg;
		$this->track = $track;
		$this->vote = $vote;
		$this->distance = $distance;
		$this->distance_float = $distance_float;
		$this->action = $action;
		 // $this->images = $images;
		$this->date = $date;
		$this->profile = $profile;
		$this->heighdata = $heighdata;
		$this->speeddata = $speeddata;
		$this->comments = $comments;
		$this->user = $user;
		$this->model = $model;
		$this->level = $level;
		$this->imageBlock = $imageBlock;

		parent :: display($tpl);
	}

	function _displayList($tpl) {
		$mainframe =& JFactory::getApplication(); $option = JRequest::getCmd('option');

		$model = & $this->getModel();
		$cache = & JFactory :: getCache('com_jtg');
		$sortedcats = JtgModeljtg::getCatsData(true);
		$sortedter = JtgModeljtg::getTerrainData(true);
		$user = & JFactory :: getUser();
		$uid = $user->get('id');
		// TODO upgrade ACL
		$gid = $user->get('gid');
		$lh = layoutHelper :: navigation();
		$footer = layoutHelper :: footer();
		$cfg = JtgHelper :: getConfig();
		$pathway = & $mainframe->getPathway();
		$pathway->addItem(JText::_( 'COM_JTG_FILES'), '');
		$sitename = $mainframe->getCfg('sitename');
		$document = JFactory::getDocument();
		$document->setTitle(JText::_( 'COM_JTG_FILES') . " - " . $sitename);
		$params = & $mainframe->getParams();

		$order = JRequest :: getVar('order', 'order', 'post', 'string');

		$filter_order = $mainframe->getUserStateFromRequest("$option.filter_order", 'filter_order', 'ordering', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest("$option.filter_order_Dir", 'filter_order_Dir', '', 'word');
		$search = $mainframe->getUserStateFromRequest("$option.search", 'search', '', 'string');
		$search = JString :: strtolower($search);
		$limit = $mainframe->getUserStateFromRequest($option . '.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option . '.limitstart', 'limitstart', 0, 'int');
		$limitstart = JRequest :: getVar('limitstart', 0, '', 'int');
		$action = JRoute :: _('index.php?option=com_jtg&view=files&layout=list',false);

		$lists['order'] = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['search'] = $search;

		//	$rows =& $model->getData($limit, $limitstart );
		$rows = $cache->get(array ( $model, 'getData' ), array ( $limit, $limitstart ));
		$total = & $this->get('Total');
		$pagination = new JPagination($total, $limitstart, $limit);

		$this->sortedcats = $sortedcats;
		$this->sortedter = $sortedter;
		$this->lists = $lists;
		$this->rows = $rows;
		$this->uid = $uid;
		$this->gid = $gid; //TODO check this ??
		$this->pagination = $pagination;
		$this->lh = $lh;
		$this->footer = $footer;
		$this->action = $action;
		$this->cfg = $cfg;
		$this->params = $params;

		parent :: display($tpl);
	}

	function _displayUserTracks($tpl) {
		$mainframe =& JFactory::getApplication(); $option = JRequest::getCmd('option');

		$cache = & JFactory :: getCache('com_jtg');
		$lh = layoutHelper :: navigation();
		$footer = layoutHelper :: footer();
		$model = & $this->getModel();
		$cfg = JtgHelper :: getConfig();
		$pathway = & $mainframe->getPathway();
		$pathway->addItem(JText::_( 'COM_JTG_MY_FILES'), '');
		$sitename = $mainframe->getCfg('sitename');
		$document = JFactory::getDocument();
		$document->setTitle(JText::_( 'COM_JTG_MY_FILES') . " - " . $sitename);

		$order = JRequest :: getVar('order', 'order', 'post', 'string');

		$filter_order = $mainframe->getUserStateFromRequest("$option.filter_order", 'filter_order', 'ordering', 'cmd');
		$filter_order_Dir = $mainframe->getUserStateFromRequest("$option.filter_order_Dir", 'filter_order_Dir', '', 'word');
		$search = $mainframe->getUserStateFromRequest("$option.search", 'search', '', 'string');
		$search = JString :: strtolower($search);
		$limit = $mainframe->getUserStateFromRequest($option . '.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest($option . '.limitstart', 'limitstart', 0, 'int');
		$limitstart = JRequest :: getVar('limitstart', 0, '', 'int');
		$action = JRoute :: _('index.php?option=com_jtg&view=files&layout=user',false);

		$lists['order'] = $filter_order;
		$lists['order_Dir'] = $filter_order_Dir;
		$lists['search'] = $search;

		//		$rows = & $model->getData($limit, $limitstart);
		$rows = $cache->get(array ( $model, 'getData' ), array ( $limit, $limitstart ));
		$total = & $this->get('Total');
		$pagination = new JPagination($total, $limitstart, $limit);
		$cats = JtgModeljtg::getCatsData(true);
		//		$cats = $model->getCats();
		$sortedter = JtgModeljtg::getTerrainData(true);
		$params = & $mainframe->getParams();
		$this->params = $params;
		$this->sortedter = $sortedter;
		$this->lh = $lh;
		$this->cats = $cats;
		$this->footer = $footer;
		$this->action = $action;
		$this->cfg = $cfg;
		$this->lists = $lists;
		$this->rows = $rows;
		$this->pagination = $pagination;

		parent :: display($tpl);
	}

	function approach($service) {
		//		$userparams = explode("\n", $this->user->params);
		$user = & JFactory :: getUser();

		if ($user->id == 0) // user is public
		{
			$config = & JFactory :: getConfig();
			$lang = $config->getValue('language');
		} else {
			$lang = JFactory :: getLanguage();
			$lang = $user->_params->_registry["_default"]["data"]->language;
		}
		$lang = explode("-", $lang);
		$userlang = $lang[0];
		$availablelang = array ( 'de', 'en', 'it', 'fr', 'es' ); // allowed from ORS
		if (in_array($userlang, $availablelang))
		$lang = $userlang;
		else
		$lang = "en";
		$imgdir = JURI :: base() . "components/com_jtg/assets/images/approach/" . $this->cfg->routingiconset . "/";
		$routservices = array();
		$return = "";
		switch ($service) {
			case 'ors' :
				//	OpenRouteService:
				$link = $this->model->approachors($this->track->start_n, $this->track->start_e, $lang);
				$routservices = array (
				array (
						"img" => $imgdir . "car.png",
						"name" => JText::_( 'COM_JTG_CAR'),
				array (
				array (
								"Fastest",
				JText::_( 'COM_JTG_FASTEST')
				),
				array (
								"Shortest",
				JText::_( 'COM_JTG_SHORTEST')
				)
				)
				),
				array (
						"img" => $imgdir . "bike.png",
						"name" => JText::_( 'COM_JTG_BICYCLE'),
				array (
				array (
								"BicycleSafety",
				JText::_( 'COM_JTG_SAVETEST')
				),
				array (
								"Bicycle",
				JText::_( 'COM_JTG_SHORTEST')
				),
				array (
								"BicycleMTB",
				JText::_( 'COM_JTG_MTB')
				),
				array (
								"BicycleRacer",
				JText::_( 'COM_JTG_RACERBIKE')
				)
				)
				),
				array (
						"img" => $imgdir . "foot.png",
						"name" => JText::_( 'COM_JTG_PEDESTRIAN'),
				array (
				array (
								"Pedestrian",
				JText::_( 'COM_JTG_SHORTEST')
				)
				)
				)
				);
				break;
			case 'cm' :
				//	CloudMade:
				$link = $this->model->approachcm($this->track->start_n, $this->track->start_e, $lang);
				$routservices = array (
				array (
						"img" => $imgdir . "car.png",
						"name" => JText::_( 'COM_JTG_CAR'),
				array (
				array (
								"car",
				JText::_( 'COM_JTG_FASTEST')
				),
				array (
								"car/shortest",
				JText::_( 'COM_JTG_SHORTEST')
				)
				)
				),
				array (
						"img" => $imgdir . "bike.png",
						"name" => JText::_( 'COM_JTG_BICYCLE'),
				array (
				array (
								"bicycle",
				JText::_( 'COM_JTG_SHORTEST')
				)
				)
				),
				array (
						"img" => $imgdir . "foot.png",
						"name" => JText::_( 'COM_JTG_PEDESTRIAN'),
				array (
				array (
								"foot",
				JText::_( 'COM_JTG_SHORTEST')
				)
				)
				)
				);
				break;
			case 'cmkey' :
				//	CloudMade with API-Key:
				$link = $this->model->approachcmkey($this->track->start_n, $this->track->start_e, $lang);
				$routservices = array (
				array (
						"img" => $imgdir . "car.png",
						"name" => JText::_( 'COM_JTG_CAR'),
				array (
				array (
								"car",
				JText::_( 'COM_JTG_FASTEST')
				),
				array (
								"car/shortest",
				JText::_( 'COM_JTG_SHORTEST')
				)
				)
				),
				array (
						"img" => $imgdir . "bike.png",
						"name" => JText::_( 'COM_JTG_BICYCLE'),
				array (
				array (
								"bicycle",
				JText::_( 'COM_JTG_SHORTEST')
				)
				)
				),
				array (
						"img" => $imgdir . "foot.png",
						"name" => JText::_( 'COM_JTG_PEDESTRIAN'),
				array (
				array (
								"foot",
				JText::_( 'COM_JTG_SHORTEST')
				)
				)
				)
				);
				break;
			case 'easy' :
				$cfg = JtgHelper :: getConfig();
				$link = $this->model->approacheasy($this->track->start_n, $this->track->start_e, $lang);
				break;
		}
		foreach ($routservices AS $shifting) {
			$return .= "			<td>
										<center>
											<img src=\"" . $shifting['img'] . "\" alt=\"" . $shifting['name'] . "\" title=\"" . $shifting['name'] . "\" />
										</center>
										<ul>\n";
			foreach ($shifting[0] AS $service)
			$return .= "					<li>
														<a href=\"" . $link . $service[0] . "\" target=\"_blank\">" . $service[1] . "</a>
													</li>\n";
			$return .= "				</ul>\n			</td>\n";
		}
		return $return;
	}

	function parseTemplate($template, $content = null, $linkname = null, $only = null) {
		$tmpl = ($this->cfg->template = "") ? $this->cfg->template : 'default';;
		$templatepath = JPATH_BASE . DS . "components" . DS . "com_jtg" . DS . "assets" . DS . "template" . DS . $tmpl . DS;
		if ((!$content)AND($content != "")) {
			include_once ($templatepath . $template . "_" . $only . ".php");
			return;
		}
		$TLopen = $template . "_open";
		$TLclose = $template . "_close";
		$function = "parseTemplate_" . $TLopen;
		defined('_parseTemplate_' . $template . '_open') or include_once ($templatepath . $TLopen . ".php");
		$return = $function ($linkname);
		$return .= $content;
		$function = "parseTemplate_" . $TLclose;
		defined('parseTemplate_' . $template . '_close') or include_once ($templatepath . $TLclose . ".php");
		$return .= $function ($linkname);
		return $return;
	}
}