<?php
echo 'wm.php';
#####################################################
# originally gleaned from
# http://www.fedeblog.com.ar/files/view.php?i=Watermark/watermark.php
# Modified by Liem Bahneman (liem@bahneman.com) with some 
# path enhancements when called to handled subdirectories


#####################################################
# Okay to edit these

# what is the root of your files?
$basedir="/usr/local/www/data/snaffed.com";
$watermarkimage="/images/snaffed_wm.png";

#####################################################
# end user modifiable stuff...

//include('lib/init.php');
$id = $_GET['i'];
//$sql = "select path from images where id=$id";
//echo $sql;


$file=basename($id);

$image = $basedir."/gallery/".$file;
$watermark = $basedir."/".$watermarkimage;

//$im = imagecreatefrompng($watermark);

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

//header("Content-Type: image/jpeg");
imagejpeg($im2,NULL,95);
imagedestroy($im);
imagedestroy($im2);

?> 