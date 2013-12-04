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
 * Returns true  if thumbnail creation was successful for all thumbnails
 * @param <integer> $max_thumb_height
 * @param <integer> $max_geoim_height
 * @return <boolean> 
 */
function com_jtg_refresh_Thumbnails()  
{
	$cfg = JtgHelper::getConfig();
	require_once(JPATH_SITE . DS . "administrator" . DS . "components" . DS . "com_jtg" . DS . "models" . DS . "thumb_creation.php");
	$base_dir = JPATH_SITE . DS . 'images' . DS . 'jtrackgallery';
	$success=true; 
	$regex_folder = "^track_";
	$regex_images="([^\s]+(\.(?i)(jpg|png|gif|bmp))$)";
	$folders= JFolder::folders($base_dir, $regex_folder, $recurse=false, $full=false);

	foreach ($folders as $folder)
	{
	    $imgs = JFolder::files($base_dir . DS . $folder);
	    $thumb_dir = $base_dir . DS . $folder . DS. 'thumbs';
	    if($imgs)
	    {
		if(JFolder::exists($thumb_dir)) 
		{
		    // remove old thumbnails 
		    $filesToDelete = JFolder::files($thumb_dir, $regex_images);
		    foreach($filesToDelete AS $fileToDelete)
		    {
			JFile::delete($thumb_dir . DS . $fileToDelete); 
		    }
		}
		else 
		{
		    JFolder::create($thumb_dir);
		}
		foreach($imgs AS $image)
		{
			$thumb = com_jtg_create_Thumbnails ($base_dir . DS . $folder .DS, $image, $cfg->max_thumb_height, $cfg->max_geoim_height); 
			if (! $thumb) 
			{
			    $success=false; 
			}					    
		}
	    }
	}
	return $success; 		
}


/**
 * Returns thumbnail name (including extension)  if thumbnail creation was successful
 * @param <link> $image_path
 * @param <integer> $thumb_size
 * @param <link> $thumb_path
 * @param <string> $thumb_name (without extension)
 * @return <string> 
 */


function com_jtg_create_Thumbnails ($image_dir, $image_name, $max_thumb_height=210, $max_geoim_height=300)  
{

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
	// set height and width an integer 
	if ($height > $max_geoim_height) 
	{
	    $thumb_height = (int) $max_geoim_height;
	    $thumb_width = (int) ($width/2/$height*$max_geoim_height;
	}
	else 
	{
	    $thumb_height = $height;
	    $thumb_width = $width;	    
	}
	// create geotaged image thumbnail
	$tmp=imagecreatetruecolor($thumb_width,$thumb_height);
	imagecopyresampled($tmp,$src,0,0,0,0,$thumb_width,$thumb_height,$width,$height);//resample the image
	$thumb_path = $image_dir . 'thumbs/thumb0_' . $image_name; 
	switch (strtolower($ext))
	{
		case 'jpeg':
		case 'pjpeg':
		case 'jpg':
			$statusupload0 = imagejpeg($tmp,$thumb_path,85);//upload the image
			break;

		case 'png':
			$statusupload0 = imagepng($tmp,$thumb_path,85);//upload the image
			break;

		case 'gif':
			$statusupload0 = imagegif($tmp,$thumb_path,85);//upload the image
			break;

	}
	
	// create first thumbnail
	// set height and width an even integer 
	if ($height > $max_thumb_height) 
	{
	    $thumb_height = 2 * ( (int) $max_thumb_height/2 );
	    $thumb_width = 2 * ( (int) ($width/2/$height*$max_thumb_height) );	
	}
	else 
	{
	    $thumb_height = $height;
	    $thumb_width = $width;	    
	}
	$thumb_height = 2 * ( (int) $max_thumb_height/2 );
	$thumb_width = 2 * ( (int) ($width/2/$height*$max_thumb_height) );
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
	if ( ($statusupload0) and ($statusupload1) and ($statusupload2) ) {return true;}
	return false;
}
