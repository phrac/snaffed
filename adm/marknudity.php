<?php
require_once('../lib/init.php');
/* default view */ 


if (isset($_POST['nude'])) {
  $nude = $_POST['nude'];
  $id = $_POST['id'];
  if ($nude == 'No') {
    include(BASEDIR . 'lib/imageprocessing.php');
    $del = deleteImg($id);
    $content .= "$id deleted<br />";
  }
  else {
    $sql = "update images set nudity=false where id = $id";
    $content = "$id saved<br />";
  }
  pg_query($sql);
}

$sql = "select * from images where nudity is NULL order by id limit 1";
$res = pg_query($sql);
$pic = pg_fetch_array($res);
$content .= '<h2>Save image?</h2>';
$content .= '<form action="'. $PHP_SELF . '" method="post">';
$content .= '<input type="hidden" name="id" value="' . $pic['id'] . '" />';
$content .= '<input type="submit" name="nude" value="Yes" accesskey="," />
<input type="submit" name="nude" value="No" accesskey="." /></form>';

$content .= '<img id="pic" onload="scaleImg(\'pic\')" border="0" src="/snaffed/gallery/' . $pic['path'] . '" /></a>';





/* load the template and process */
include(BASEDIR . "layouts/default.php");
