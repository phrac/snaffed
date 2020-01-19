<?php

require_once('lib/init.php');

$watermarkimage = "snaffed_wm.png";

//if (isset($_GET['e'])) {
	$id = $_GET['i'];
	$sql = "select path from images where id='$id'";
	$res = pg_query($sql);
	$row = pg_fetch_row($res);
	$file = basename($row[0]);
//}

//else {
//	$file = basename($_GET['i']);
//}

$image = BASEDIR . "/gallery/" . $file;

$watermark = BASEDIR . "/images/" . $watermarkimage;
if (!file_exists($image)) { echo "Image not found"; die(); }
$im = imagecreatefrompng($watermark);
$ext = substr($image, -3);

if (strtolower($ext) == "gif") {
    if (!$im2 = imagecreatefromgif($image)) {
        echo "Error opening $image!"; exit;
    }
} else if(strtolower($ext) == "jpg" || strtolower(substr($image,-4)) == "jpeg") {
    if (!$im2 = imagecreatefromjpeg($image)) {
        echo "Error opening $image!"; exit;
    }
} else if(strtolower($ext) == "png") {
    if (!$im2 = imagecreatefrompng($image)) {
        echo "Error opening $image!"; exit;
    }
} else {
    die;
}

imagecopy($im2, $im, (imagesx($im2)-10)-(imagesx($im)-10), (imagesy($im2)-10)-(imagesy($im)-10), 0, 0, imagesx($im), imagesy($im));

header("Content-Type: image/jpeg");
imagejpeg($im2, NULL, 95);
imagedestroy($im);
imagedestroy($im2);

?> 