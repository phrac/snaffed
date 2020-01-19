<?
include('../lib/init.php');
if (isset($_POST['img1']) && isset($_POST['img2'])) {
	$img1 = $_POST['img1'];
	$img2 = $_POST['img2'];
	
	$sql = "select * from images where id=$img1 or id=$img2";
	$res = pg_query($sql);
	$content .= '<table border="0" cellpadding="5" cellspacing="5"><tr><td>';
	$row = pg_fetch_array($res);
	$sig1 = $row['signature'];
	$sig1 = pg_unescape_bytea($sig1);
  	$sig1 = puzzle_uncompress_cvec($sig1);
	$content .= '<tr><td><img src="/gallery/' . $row['path'] . '" /></td>';
	
	$row = pg_fetch_array($res);
	$sig2 = $row['signature'];
	$sig2 = pg_unescape_bytea($sig2);
  	$sig2 = puzzle_uncompress_cvec($sig2);
	$content .= '<td><img src="/gallery/' . $row['path'] . '" /></td></tr>';
	
	
	$d = puzzle_vector_normalized_distance($sig1, $sig2);		
	$content .= '</table>';
	
	$sidebar_content .= '<b>Difference:</b> ' . $d;

}

	$content .= '<form action="' . $PHP_SELF . '" method="post">';
	$content .= '<input type="text" name="img1" />';
	$content .= '<input type="text" name="img2" />';
	$content .= '<input type="submit" value="compare" />';
	$content .= '</form>';
	
	
	include(BASEDIR . 'layouts/default.php');