<?
include('../lib/init.php');
require_once(BASEDIR . 'lib/imageprocessing.php');


if (isset($_POST['img1']) && isset($_POST['img2'])) {
	$img1 = $_POST['img1'];
	$img2 = $_POST['img2'];
	$tags = $_POST['tags'];
   $i = $img1;
   for ($i=$img1;$i<$img2+1;$i++) {
	   $sql = "select id from images where id=$i";
	   $res = pg_query($sql);
	   if (pg_num_rows($res) != 0) {
		   process_tags($i,$tags,$_SERVER['REMOTE_ADDR']);
		}
	}
	
}

	$content .= '<form action="' . $PHP_SELF . '" method="post">';
	$content .= 'Start id: <input type="text" name="img1" /><br />';
	$content .= 'End id:<input type="text" name="img2" /><br />';
	$content .= 'Tags: <input type="text" name="tags" /><br />';
	$content .= '<input type="submit" value="tag" />';
	$content .= '</form>';
	
	
	include(BASEDIR . 'layouts/default.php');
