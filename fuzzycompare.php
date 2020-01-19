<?
require_once('../lib/init.php');
/*if (checkLogin() == 0) {
	header("Location: index.php");
}*/
require_once(BASEDIR . 'lib/imageprocessing.php');

if (isset($_GET['a'])) {
  $action = $_GET['a'];
  if ($action == 'nmd') {
    $fmid = $_GET['i'];
    $sql = "update fuzzy_duplicates set checked=true where id = '$fmid'";
    pg_query($sql);
    $content .= $fmid . ' deleted<br />';
  }
  if ($action == 'md') {
    $id = $_GET['i'];
    $content .= deleteImg($id) . '<br />';
  }
}



$sql = "select count(id) from fuzzy_duplicates where checked=false";
$res = pg_query($sql);
$row = pg_fetch_row($res);
$remain = $row[0];
$content .= $remain . ' fuzzy matches remaining<br />';
pg_free_result($res);

$sql = "select * from fuzzy_duplicates where checked=false order by cvec_diff asc limit 1";
$res = pg_query($sql);
if (pg_num_rows($res) != 0) {
$row = pg_fetch_array($res);
$diff = $row['cvec_diff'];
$orig = $row['img_id'];
$comp = $row['match_img_id'];
$compid = $row['id'];

$content .= '<table border="0" cellpadding="5" cellspacing="5"><tr><td>';

/* get left image */
$sql = "select * from images where id=$orig";
$res = pg_query($sql);
$row = pg_fetch_array($res);
$res = getimagesize(BASEDIR . 'gallery/' . $pic['path']);
	$res = $res[0] . 'x' . $res[1];
$content .= '<img src="/wm/' . $row['id'] . '.jpg" />';
$content .= "<br />img id: " . $row['id'];
$content .= "<br />size: " . $row['size'] . " - " . $res;
$content .= "<br />shit: " . $row['hit'];
$content .= '</td><td>';

$sql = "select * from images where id=$comp";
$res = pg_query($sql);
$row = pg_fetch_array($res);
$res = getimagesize(BASEDIR . 'gallery/' . $pic['path']);
	$res = $res[0] . 'x' . $res[1];
$content .= '<img src="/wm/' . $row['id'] . '.jpg" />';
$content .= "<br />img id: " . $row['id'];
$content .= "<br />size: " . $row['size'] . " - " . $res;
$content .= "<br />shit: " . $row['hit'];
$content .= '</td>';

$content .= '</td></tr></table>';
$sidebar_content .= '<h1>Fuzzy Matching</h1>';
$sidebar_content .= '<b>Difference: ' . $diff . '</b><br /><br />';
$sidebar_content .= '<a href="' . $PHP_SELF . '?a=nmd&i=' . $compid . '">No match - delete</a><br />';
$sidebar_content .= '<a href="' . $PHP_SELF . '?a=md&i=' . $orig . '">Delete left image</a><br />';
$sidebar_content .= '<a href="' . $PHP_SELF . '?a=md&i=' . $comp . '">Delete right image</a>';
}
else {
$content .= 'No fuzzy matches';
}
include(BASEDIR . 'layouts/default.php');

