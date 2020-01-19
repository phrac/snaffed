<?php
require_once('lib/init.php');
require_once('lib/imageprocessing.php');
$id = $_GET['id'];
	$sql = "select * from images where id=$id limit 1";


$res = pg_query($sql);
$qc++;

$pic = pg_fetch_array($res);


/* check to see if image actually exists */ 
if (pg_num_rows($res) == 0) $content = "<h1>Image not found</h1>";


/* image exists, lets display it */
else {
	$sql = "select t.id, t.name from tags as t inner join taggings as tg on (tg.tag_id = t.id) where tg.taggable_id = " . $pic['id'];
	$tres = pg_query($sql);
	$path = 'gallery/' . $pic['path'];
	if (substr($pic['path'], -3) == 'gif') {
		$content .= '<a href="randss.php"><img src="gallery/'. $pic['path'] . '" /></a><br />';
		$path = "images/snaffed.png";
	}
	else {
		$ext = substr(strrchr($pic['path'], '.'), 1);
		$content .= '<center><a href="randss.php"><img height="800" src="gallery/' . $pic['path'] . '" /></a></center><br />';
	}
	
	$sql = "update images set views = views + 1 where id = $pic[id]";
	pg_query($sql);
	$qc++;
	
	
}
$resource = NewMagickWand();
MagickReadImage($resource, $path);

MagickSetFormat($resource, 'JPG');
MagickSetImageCompression($resource, MW_JPEGCompression);
MagickSetImageCompressionQuality($resource, 35.0);
header('Content-Type: image/jpeg');
MagickEchoImageBlob($resource);
