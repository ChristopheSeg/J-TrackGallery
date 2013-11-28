<?php
/**
 * @component  J!Track Gallery (jtg) for Joomla! 2.5
 *
 * 
 * @author     J!Track Gallery, InJooosm and joomGPStracks teams
 * @package    com_jtg
 * @subpackage backend
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL2
 * @link       http://jtrackgallery.net/
 *
 */

// No direct access
defined('_JEXEC') or die('Restricted access');


// Import Joomla! libraries
jimport('joomla.application.component.model');



/**
 * Returns thumbnail name (including extension)  if thumbnail creation was successful
 * @param <link> $image_path
 * @param <integer> $thumb_size
 * @param <link> $thumb_path
 * @param <string> $thumb_name (without extension)
 * @return <string> 
 */


function com_jtg_create_Thumbnails ($image_dir, $image_name, $thumb_height=210)  {

	$ext = JFile::getExt($image_name);
	$image_path = $image_dir . $image_name;
	switch (strtolower($ext))
	{
		case 'jpeg':
		case 'pjpeg':
		case 'jpg':
			$src = ImageCreateFromJpeg($image_path);
			break;

		case 'png':
			$src = ImageCreateFromPng($image_path);
			break;

		case 'gif':
			$src = ImageCreateFromGif($image_path);
			break;

	}
	list($width,$height)=getimagesize($image_path);
	// set height and width an even integer 
	$thumb_height = 2 * ( (int) $thumb_height/2 );
	$thumb_width = 2 * ( (int) ($width/2/$height*$thumb_height) );

	// create first thumbnail
	$tmp=imagecreatetruecolor($thumb_width,$thumb_height);
	imagecopyresampled($tmp,$src,0,0,0,0,$thumb_width,$thumb_height,$width,$height);//resample the image
	$thumb_path = $image_dir . 'thumbs/thumb1_' . $image_name; 

	switch (strtolower($ext))
	{
		case 'jpeg':
		case 'pjpeg':
		case 'jpg':
			$statusupload1 = imagejpeg($tmp,$thumb_path,85);//upload the image
			break;

		case 'png':
			$statusupload1 = imagepng($tmp,$thumb_path,85);//upload the image
			break;

		case 'gif':
			$statusupload1 = imagegif($tmp,$thumb_path,85);//upload the image
			break;

	}
	// create second thumbnail
	$tmp=imagecreatetruecolor($thumb_width/2,$thumb_height/2);
	imagecopyresampled($tmp,$src,0,0,0,0,$thumb_width/2,$thumb_height/2,$width,$height);//resample the image
	$thumb_path = $image_dir . 'thumbs/thumb2_' . $image_name; 

	switch (strtolower($ext))
	{
		case 'jpeg':
		case 'pjpeg':
		case 'jpg':
			$statusupload2 = imagejpeg($tmp,$thumb_path,85);//upload the image
			break;

		case 'png':
			$statusupload2 = imagepng($tmp,$thumb_path,85);//upload the image
			break;

		case 'gif':
			$statusupload2 = imagegif($tmp,$thumb_path,85);//upload the image
			break;

	}
	if ( ($statusupload1) and ($statusupload2) ) {return true;}
	return false;
}