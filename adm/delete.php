<?
require_once('../lib/init.php');
/*if (checkLogin() == 0) {
	header("Location: index.php");
} */
require_once(BASEDIR . 'lib/imageprocessing.php');

$content .= '<form action="delete.php" method="post">';
$content .= 'ID to delete: <input type="text" name="id" size="6" />';
$content .= '<input type="submit" value="delete" />';

if (isset($_POST[id])) {
  $img = $_POST['id'];
  $content .= deleteImg($img);
}

include(BASEDIR . 'layouts/default.php');