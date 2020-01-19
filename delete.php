<?
require_once('lib/init.php');
/*if (checkLogin() == 0) {
	header("Location: index.php");
} */
require_once(BASEDIR . 'lib/imageprocessing.php');

if (isset($_GET[img])) {
  $img = $_GET[img];
  $content .= deleteImg($img);
}

include(BASEDIR . 'layouts/default.php');