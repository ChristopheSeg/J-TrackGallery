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


// Import Joomla! libraries
jimport('joomla.application.component.model');

/**
 *
 * @param <integer> $max_thumb_height
 * @param <integer> $max_geoim_height
 * @return <boolean> true  if thumbnail creation was successful for all thumbnails
 */
function com_jtg_refresh_Thumbnails()
{
	jimport('joomla.filesystem.folder');
	jimport('joomla.filesystem.file');
	$cfg = JtgHelper::getConfig();
	require_once JPATH_SITE . '/administrator/components/com_jtg/models/thumb_creation.php';
	$base_dir = JPATH_SITE . '/images/jtrackgallery';
	$success=true;
	$regex_folder = '^track_';
	$regex_images='([^\s]+(\.(?i)(jpg|png|gif|bmp))$)';
	$folders= JFolder::folders($base_dir, $regex_folder, $recurse=false, $full=false);

	foreach ($folders as $folder)
	{
		$imgs = JFolder::files($base_dir . '/' . $folder);
		$thumb_dir = $base_dir . '/' . $folder . '/thumbs';
		if ($imgs)
		{
			if (JFolder::exists($thumb_dir))
			{
				// Remove old thumbnails
				$filesToDelete = JFolder::files($thumb_dir, $regex_images);
				foreach ($filesToDelete AS $fileToDelete)
				{
					JFile::delete($thumb_dir . '/' . $fileToDelete);
				}
			}
			foreach ($imgs AS $image)
			{
				$thumb = com_jtg_create_Thumbnails ($base_dir . '/' . $folder . '/', $image, $cfg->max_thumb_height, $cfg->max_geoim_height);
				if (! $thumb)
				{
					$success=false;
				}
			}
		}
		else
		{
			// no imgs so delete possibly existing folder
			if (JFolder::exists($thumb_dir))
			{
				JFolder::delete($thumb_dir);
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

	jimport('joomla.filesystem.folder');
	jimport('joomla.filesystem.file');
	$ext = JFile::getExt($image_name);
	$image_path = $image_dir . $image_name;
	$thumb_dir = $image_dir . 'thumbs/' ;
	if (! JFolder::exists($thumb_dir))
	{
		JFolder::create($thumb_dir);
	}
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
			$src = ImageCreateFromGif ($image_path);
			break;

	}
	list($width,$height)=getimagesize($image_path);
	// Set height and width an integer
	if ($height > $max_geoim_height)
	{
		$thumb_height = (int) $max_geoim_height;
		$thumb_width = (int) $width/2/$height*$max_geoim_height;
	}
	else
	{
		$thumb_height = $height;
		$thumb_width = $width;
	}
	// Create geotaged image thumbnail (use Geotagged image size)
	$tmp=imagecreatetruecolor($thumb_width,$thumb_height);
	imagecopyresampled($tmp,$src,0,0,0,0,$thumb_width,$thumb_height,$width,$height);//resample the image
	$thumb_path = $thumb_dir . 'thumb0_' . $image_name;
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

	// Create first thumbnail
	// Set height and width an even integer
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
	$thumb_path = $thumb_dir . 'thumb1_' . $image_name;

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
	// Create second thumbnail
	$tmp=imagecreatetruecolor($thumb_width/2,$thumb_height/2);
	imagecopyresampled($tmp,$src,0,0,0,0,$thumb_width/2,$thumb_height/2,$width,$height);//resample the image
	$thumb_path = $thumb_dir . 'thumb2_' . $image_name;

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
	if ( ($statusupload0) and ($statusupload1) and ($statusupload2) ) {
		return true;
	}
	return false;
}
