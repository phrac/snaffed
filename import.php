<?php

/* this is the main import script.  Checks a directory for files and imports them */

require_once('../lib/init.php');

/*if (checkLogin() == 0) {
	header("Location: index.php");
}
*/
require_once(BASEDIR . 'lib/imageprocessing.php');
$dir = BASEDIR . 'import';
$file = BASEDIR . 'import/*.{jpg,png,JPG,jpeg,gif}';
foreach (glob($file, GLOB_BRACE) as $filename) {
	$resized = NULL;
	$content .= "importing $filename ... ";
	$sql = "select nextval('images_id_seq')";
	$res = pg_query($sql);
	$row = pg_fetch_row($res);
	$imgid = $row[0];
	$ext = substr(strrchr($filename, '.'), 1);
	$newabsfilename = $imgid . '.' . $ext;
	$histfile = "gallery/" . $newabsfilename;
	$newfilename = BASEDIR . "gallery/" . $newabsfilename;
	$newthumbname = BASEDIR . "gallery/thumbs/" . $imgid . '.jpg'; //always save the thumbs as a jpg
	//$content .= "new: $newfilename - ";
	$image = new Imagick($filename);
	$w = NULL;
	$h = NULL;
	$w = $image->getImageWidth();
	$h = $image->getImageWidth();
	if ($w > 1280) {
		$image->resizeImage(1280,1280,NULL,1,TRUE);
		$image->writeImage($newfilename);
		$image->destroy;
		$image = new Imagick($newfilename);
		unlink($filename);
		$resized = 1;
		$content .= "resized image -";
	} 
	if ($ext == 'gif') {
		$image->flattenImages();
	}
	$image->cropThumbnailImage(125,125);
	$image->writeImage($newthumbname);
	$image->destroy;
	$content .= "wrote thumbnail, checking for dupes ";
	if ($resized != 1) rename($filename, $newfilename);
	$md5 = md5_file($newfilename);
	$sql = "select id from images where md5='$md5'";
	$res = pg_query($sql);
	if (pg_num_rows($res) == 0) {
		$cvec = getHistogram($histfile);
    $content .= "...inserting...";
		$size = filesize($newfilename);
		$sql = "insert into images (id, path, size, md5, created_on, views,signature,category) values ($imgid, '$newabsfilename', $size, '$md5', now(), 0,'$cvec', '$category')";
		pg_query($sql) or die ("could not insert new image");
		$qc++;
    $fuzzy_matches = fuzzyMatch($imgid,NULL,.32);
			$content .= "fuzzy matched " . $fuzzy_matches . " images as duplicates"; 
		  $sql = "update images set fuzzychecked=true where id=$imgid";
      pg_query($sql);
      $qc++;
		$content .= "...done<br />";
	}
	else {
		$content .= "...**DUPLICATE DETECTED! ABORTING...";
		unlink($newfilename);
		unlink($newthumbname);
		$content .= "...DONE**<br />";
	}
}

/* update the stats file */
$sql = "select count(id) from images where $filter";
$res = pg_query($sql);
$row = pg_fetch_row($res);
$data = "Number of Images: $row[0]<br />";
//$data .= "Active content: " . recursive_directory_size(BASEDIR . 'gallery', TRUE);

$contentfile = BASEDIR . "txt/" . $stats;
$fh = fopen($contentfile, 'w') or die("can't open file");
fwrite($fh, $data);
fclose($fh);
include(BASEDIR . 'layouts/default.php');
