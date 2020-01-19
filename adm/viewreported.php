<?
require_once('../lib/init.php');
/*if (checkLogin() == 0) {
	header("Location: index.php");
}*/
require_once(BASEDIR . 'lib/imageprocessing.php');

if (isset($_GET['a'])) {
  $action = $_GET['a'];
  if ($action == 'dr') {
    $fmid = $_GET['i'];
    $sql = "delete from reported_images where id = '$fmid'";
    pg_query($sql);
    $content .= $fmid . ' deleted<br />';
  }
  if ($action == 'di') {
    $id = $_GET['i'];
    $content .= deleteImg($id) . '<br />';
  }
}


$sql = "select * from reported_images order by id limit 1";
$res = pg_query($sql);
if (pg_num_rows($res) != 0) {
	$row = pg_fetch_array($res);
	$orig = $row['img_id'];
	$reason = $row['reason'];
	$byip = $row['ip'];
	$rid = $row['id'];

	/* get the image */
	$sql = "select * from images where id=$orig";
	$res = pg_query($sql);
	$row = pg_fetch_array($res);

	$content .= '<img src="/wm.php?i=' . $row['id'] . '" />';

	$sidebar_content .= '<h1>Reported Image</h1>';
	$sidebar_content .= '<b>Reported for: </b>' . $reason . '<br />';
	$sidebar_content .= '<b>By: </b>' . $byip . ' (<a href="/adm/ban.php?ip=' . $byip . '">ban ip</a>)<br />';
	$sidebar_content .= '<a href="' . $PHP_SELF . '?a=di&i=' . $orig . '">Delete Image</a><br />';
	$sidebar_content .= '<a href="' . $PHP_SELF . '?a=dr&i=' . $rid . '">Delete Report/Return to production</a>';
}

else {
	$content .= '<h1>No images reported</h1>';
}

include(BASEDIR . 'layouts/default.php');