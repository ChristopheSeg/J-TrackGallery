<?php
/**
 * Joomla! 2.5 component J!Track Gallery (jtg)
 *
 * @version $Id: download.php,v 1.3 2011/04/07 20:18:05 christianknorr Exp $
 * @author Christophe Seguinot
 * @package jtg
 * @subpackage frontend
 * @license GNU/GPL
 * @filesource
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.controller' );

class jtgControllerDownload extends jtgController {

	function download() {

		JRequest::checkToken() or jexit( 'Invalid Token' );

//		if( headers_sent() )
//			die('Headers Sent');
		$format =& JRequest::getVar('format');
		$model = $this->getModel('download');
		$id =& JRequest::getInt('id');
		$track = $model->getFile($id);
		$trackname = str_replace(' ', '_', $track->title);
		if($format == "kml") {
			$mime = "application/vnd.google-earth.kml+xml";
		} else {
			$mime = "application/x-octet-stream";
		}

		header("Pragma: public"); // required
		header("Content-Type: " . $mime . "; charset=UTF-8");
		header("Content-Disposition: attachment; filename=\"".$trackname.".".strtolower($format)."\"");
		header("Content Transfer-Encoding:binary");
		header("Cache-Control: post-check=0, pre-check=0");

		echo $model->download($id, $format, $track);
	}

}
