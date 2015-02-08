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

jimport('joomla.application.component.controller');

class JtgControllerDownload extends JtgController
{
	/**
	 * function_description
	 *
	 * @return return_description
	 */
	function download()
	{
		JSession::checkToken() or jexit('Invalid Token');

		$format = JFactory::getApplication()->input->get('format');
		$model = $this->getModel('download');
		$id = JRequest::getInt('id');
		$track = $model->getFile($id);
		$trackname = str_replace(' ', '_', $track->title);

		if ($format == "kml")
		{
			$mime = "application/vnd.google-earth.kml+xml";
		}
		else
		{
			$mime = "application/x-octet-stream";
		}

		// Required
		header("Pragma: public");
		header("Content-Type: " . $mime . "; charset=UTF-8");
		header("Content-Disposition: attachment; filename=\"" . $trackname . "." . strtolower($format) . "\"");
		header("Content Transfer-Encoding:binary");
		header("Cache-Control: post-check=0, pre-check=0");

		echo $model->download($id, $format, $track);
	}
}
