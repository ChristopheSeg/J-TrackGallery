<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooOSM and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

// Import Joomla! libraries
jimport( 'joomla.application.component.view');

/**
 *
 */
class JtgViewPostinstall extends JView
{
    /**
     *
     * @param object $tpl
     */
    function display($tpl = null) {

        parent::display($tpl);
    }
    function parsejs_old($doc,$method,$href_id,$target,$returntext) {
	$js = "	window.addEvent('domready', function() {
		$('" . $href_id . "').addEvent('click', function(e) {
			e = new Event(e).stop();
			/**
			* The simple way for an Ajax request, use onRequest/onComplete/onFailure
			* to do add your own Ajax depended code.
			*/
			var jtgrequest = new Request.HTML({
				url: 'index.php?option=com_jtg&task=install_" . $method . "&controller=install',
				onComplete: document.getElementById('" . $target . "').innerHTML = '" . $returntext . "'
			}).request();
		});
	});
	";
	$doc->addScriptDeclaration($js);
    }

    function parsejs_old2($doc,$method,$href_id,$target,$returntext) {
	$js = "
	$('" . $href_id . "').addEvent('domready', function() {
		//e = new Event(e).stop();
		var url = \"index.php?option=com_jtg&task=install_" . $method . "&controller=install\";
		if(MooTools.version>=\"1.2.4\") {
			new Request.HTML({
				onComplete: document.getElementById('" . $target . "').innerHTML = '" . $returntext . "'
			}).send();
		}else{
			var ajax = new Ajax(url, {
				onComplete: document.getElementById('" . $target . "').innerHTML = '" . $returntext . "'
			});
			ajax.request.delay(500, ajax);
		}
	});
	";
	$doc->addScriptDeclaration($js);
    }

    function parsejs($doc,$method,$href_id,$target,$returntext) {
	$js = "
	window.addEvent('domready', function(){

	var result = $('" . $href_id . "');
	var url = \"index.php?option=com_jtg&task=install_" . $method . "&controller=install\";

	//We can use one Request object many times.
	var req = new Request({

	    url: url,

	    onRequest: function(){
	    result.set('text', 'Loading...');
	    },

	    onSuccess: function(txt){
	    result.set('html', '" . $returntext . "');
	    },

	    onFailure: function(){
	    result.set('text', 'The request failed.');
	    }

	});

	$('" . $target . "').addEvent('click', function(event){
	    event.stop();
	    req.send();
	});

	});
	";
	$doc->addScriptDeclaration($js);
    }
	
	function getVersion() {
		$return = array();
		$latest = file ("https://github.com/ChristopheSeg/J-TrackGallery/raw/master/version.php");
		$latest = $latest[0];
		$xml = simplexml_load_file(JPATH_SITE . DS . "administrator" . DS . "components" . DS . "com_jtg" . DS . "jtg.xml");
		$thisversion = (string)$xml->version;

		if (! version_compare( strtolower($latest),strtolower($thisversion) ) ) {
		        // $latest  > $thisversion (current).. must upgrade
			$return["this"] = $thisversion;
			$return["latest"] = "<font color=\"green\">$latest: ".JText::_('COM_JTG_STATE_LATEST') . "</font>\n";
		} else {
			$return["this"] = "<font color=\"orange\">" . $thisversion . "</font>";
			$return["latest"] = "<font color=\"red\">" . $latest . ": ".JText::_('COM_JTG_UPDATE') . "</font> ".
				JText::_('COM_JTG_UPDATE_LINK_A') . " ".
				"<a href=\"http://jtrackgallery.net/download\" target=\"_blank\">".
				JText::_('COM_JTG_UPDATE_LINK_B').
				"</a> ".JText::_('COM_JTG_UPDATE_LINK_C') . " <font color=\"orange\"></font>".
				JText::_('COM_JTG_UPDATE_LINK_D');
		}
		return $return;
	}
}
