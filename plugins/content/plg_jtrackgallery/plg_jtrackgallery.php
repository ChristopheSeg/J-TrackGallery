<?php
// Code de securite
defined( '_JEXEC' ) or die( 'Restricted access' );

// Importation des routines du plugin
jimport( 'joomla.plugin.plugin');

// Definition de la classe du plugin
class plgContentplg_jtrackgallery extends JPlugin
{
	var $plg_name = "plg_jtrackgallery";

	var $open_tag = "|[";

	var $close_tag = "]|";

	var $separator_tag = "|@|";

	var $nb_map = 0;

	function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	// On content prepare plugin action
	public function onContentPrepare($context, &$article, &$params)
	{
		// Plugin regexp tag
		$regex_plg_jtrackgallery = "#{JTRACKGALLERY(.*?)}(.*?){/JTRACKGALLERY}#s";

		// function call to create content and replace plugin tag
		$article->text = preg_replace_callback($regex_plg_jtrackgallery, array(&$this,'plg_jtrackgallery'), $article->text);


/*		$article->text .=
		"<script type=\"text/javascript\"><!--//--><![CDATA[//><!--
		 //--><!]]></script>
		 ";
*/


		return true;
	}


	function plg_jtrackgallery(&$matches)
	{

		$html = "<br>--PLG_JTRACKGALLERY--<br>";
		$html .= "<pre>". print_r($matches) . "</pre>";
		$html .= "<br>--PLG_JTRACKGALLERY--<br>";
		return $html;

		$this->nb_map ++;

		$params = $matches[1]; // paramettres
		$overlays = $matches[2]; // overlays
		$param_default['width'] = $this->params->get('width');
		$param_default['height'] = $this->params->get('height');
		$param_default['lon'] = $this->params->get('longitude');
		$param_default['lat'] = $this->params->get('latitude');


		$overlayshtml= $this->createHTMLoverlays($overlays);



		$regex_width = "#.*width=\'(.*)\'.*#s";
		$regex_height = "#.*height=\'(.*)\'.*#s";
		$regex_lon = "#.*lon=\'(.*)\'.*#s";
		$regex_lat = "#.*lat=\'(.*)\'.*#s";

		$param['width'] = preg_replace($regex_width, "$1", $params);
		$param['height'] = preg_replace($regex_height, "$1", $params);
		$param['lon'] = preg_replace($regex_lon, "$1", $params);
		$param['lat'] = preg_replace($regex_lat, "$1", $params);


		if($param['lat'] == $params) // si le paramettre n'est pas présent
			$param['lat'] = $param_default['lat'];
		if($param['lon'] == $params) // si le paramettre n'est pas présent
			$param['lon'] = $param_default['lon'];
		if($param['height'] == $params) // si le paramettre n'est pas présent
			$param['height'] = $param_default['height'];
		if($param['width'] == $params) // si le paramettre n'est pas présent
			$param['width'] = $param_default['width'];

		$key  = $this->params->get('key');

		$html = "<div id=\"viewerDiv$this->nb_map\" style=\"width:". $param['width'] ."px; height:". $param['height']."px; \"></div>\n";

		if($this->nb_map == 1)
		{
			$html .= "<script type=\"text/javascript\" src=\"http://api.ign.fr/geoportail/api/js/latest/GeoportalExtended.js\">
					  <!-- -->
					  </script>";

		}


		//TODO : listener sur actions séparés (pour le onload)
		$html .= "<script type=\"text/javascript\"><!--//--><![CDATA[//><!--

				  var load$this->nb_map= function() {
					  var VIEWER$this->nb_map = Geoportal.load(
						  // div's ID:
						  'viewerDiv$this->nb_map',
						  // API's keys:
						  ['".$key."'],
						  {// map's center :
							  // longitude:
							  lon:".$param['lon'].",
							  // latitude:
							  lat:".$param['lat']."
						  },5
						  ".$overlayshtml."

					  );
				  };
				  //--><!]]></script>";

		return $html;

	}


	function createHTMLoverlays($overlays)
	{


		$regex_overlays = "#.*\{layer:(.*?) (.*?)/\}.*#si";

		$overlaysmodified = preg_replace_callback($regex_overlays, array(&$this,'createoverlays'), $overlays);

		$overlaysTab = explode($this->separator_tag, $overlaysmodified);

		$htmloverlays = array();

		foreach ($overlaysTab as $overlay)
		{
			if(!preg_match("#^\s*$#s",$overlay))//si on a une chaine vide ou exclusivement composée d'espaces
			{
				$type = substr($overlay,0,stripos($overlay,":"));//recuperer le type de la carte
				$htmloverlaystab[$type][] = substr($overlay,stripos($overlay,":")+1);
			}
		}

		// on a maintenant $htmloverlaytab de la forme : array( <type de couche> => array( '{ }','{}', ...),
		//													  <type de couche> => array( '{ }','{}', ...))

		$overlaystab2 = array();
		foreach ($htmloverlaystab as $type => $overlaytypetab)
		{
			$overlaytypetab = preg_replace("#\<br \/\>#","\n", $overlaytypetab);
			$overlaytypetab = preg_replace("#\<p\>|\<\/p\>#","", $overlaytypetab);
			$overlaystab2[$type] = "'".$type."':[";
			$overlaystab2[$type] .= implode(",",$overlaytypetab);
			$overlaystab2[$type] .= "]";
		}

		//maintenant, $overlaystab2 est un tableau contenant une liste de toute les couche dans la bonne syntaxe pour être affichés


		$rec_call="";
		if($this->nb_map != 1)
		{
			$rec_call = "onView:function() {load".($this->nb_map-1)."();},";
		}


		return ",\n{\nlayers:['ORTHOIMAGERY.ORTHOPHOTOS', 'GEOGRAPHICALGRIDSYSTEMS.MAPS'], overlays:{".implode(',',$overlaystab2)."},language:'fr',".$rec_call."viewerClass:Geoportal.Viewer.Default}";

	}




	function createoverlays($matches)
	{//$layers est : soit de la forme {layers:[type de la couche] <paramettres> /} soit d'une autre forme ou vide

		$overlayName = $this->nb_map+rand();

		$type = $matches[1];
		$params = $matches[2];

		$regex['url'] = "#^.*url:".preg_quote($this->open_tag)."(.*)".preg_quote($this->close_tag).".*$#isU";
		$regex['option'] = "#^.*options:".preg_quote($this->open_tag)."(.*)".preg_quote($this->close_tag).".*$#isU";

		$url = preg_replace($regex['url'], "$1", $params);
		$options = preg_replace($regex['option'], "$1", $params);

		if($url == $params) // si le paramettre n'est pas présent
			return null;
		if($options == $params) // si le paramettre n'est pas présent
			$tag_options = "";
		else
			$tag_options = ", options:$options";

		$html = $this->separator_tag . $type.":{name:\"$overlayName\", url:\"$url\"$tag_options}".$this->separator_tag;

		return $html;
	}

}?>
