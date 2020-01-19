<?php
require_once('../lib/init.php');
if (checkLogin() == 0) {
	header("Location: index.php");
}
/* default view */ 


if (isset($_POST['category'])) {
  $cat = $_POST['category'];
  $id = $_POST['id'];
  
    $sql = "update images set category=$cat where id = $id";
  
    $content = "$id marked as $cat<br />";
 
  pg_query($sql);
}

$sql = "select * from images where category is NULL order by id limit 1";
$res = pg_query($sql);
$pic = pg_fetch_array($res);

$content .= '<form action="'. $PHP_SELF . '" method="post">';
$content .= '<input type="hidden" name="id" value="' . $pic['id'] . '" />';
$content .= '<input type="submit" name="category" value="1" accesskey="1" />';
$content .= '<input type="submit" name="category" value="2" accesskey="2" />';
$content .= '<input type="submit" name="category" value="3" accesskey="3" />';
$content .= '<input type="submit" name="category" value="4" accesskey="4" />';


$content .= '<img border="0" src="/gallery/' . $pic['path'] . '" /></a>';





/* load the template and process */
include(BASEDIR . "layouts/default.php");
