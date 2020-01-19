<?
require_once('init.php');
require_once('imageprocessing.php');

process_tags($_GET['imgid'], $_GET['tags'], $_SERVER['REMOTE_ADDR']);
$tags = displayTags($_GET['imgid']);
echo $tags[0];
?>