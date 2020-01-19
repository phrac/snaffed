<?
include('../lib/init.php');
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

if (isset($_POST['img1']) && isset($_POST['img2'])) {
	$img1 = $_POST['img1'];
	$img2 = $_POST['img2'];
	
	$sql = "select * from images where id=$img1 or id=$img2";
	$res = pg_query($sql);
	$content .= '<table border="0" cellpadding="5" cellspacing="5"><tr><td>';
	$row = pg_fetch_array($res);
  	$sig1 = puzzle_fill_cvec_from_file('../gallery/' . $row['path']);
	$content .= '<tr><td><img width="400" src="../gallery/' . $row['path'] . '" /></td>';
	
	$row = pg_fetch_array($res);
  	$sig2 = puzzle_fill_cvec_from_file('../gallery/' . $row['path']);
	$content .= '<td><img width="400" src="../gallery/' . $row['path'] . '" /></td></tr>';
	
	
	$d = puzzle_vector_normalized_distance($sig1, $sig2);		
	$content .= '</table>';
	
	$sidebar_content .= '<b>Difference:</b> ' . $d . '<br /><br />';
	$sidebar_content .= '<a href="' . $PHP_SELF . '?a=md&i=' . $img1 . '">Delete left image</a><br />';
	$sidebar_content .= '<a href="' . $PHP_SELF . '?a=md&i=' . $img2 . '">Delete right image</a>';
}

	$content .= '<form action="' . $PHP_SELF . '" method="post">';
	$content .= '<input type="text" name="img1" />';
	$content .= '<input type="text" name="img2" />';
	$content .= '<input type="submit" value="compare" />';
	$content .= '</form>';
	
	
	include(BASEDIR . 'layouts/default.php');
