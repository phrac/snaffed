<?
require_once('lib/init.php');
$return = $_POST['respond_id'];


if (isset($_FILES['uploadedfile']) && $_FILES['uploadedfile']['error'] == 0) {
  require_once('lib/process_upload.php');
	$newid = process_upload($_FILES, $return, $_POST['tags']);
}
else {
  $newid = 0;
}
if (isset($_POST['response'])) {
  $response = $_POST['response'];
  $response = pg_escape_string($response);
  $ip = $_SERVER['REMOTE_ADDR'];
  $sql = "insert into comments (img_id,comment,attached_id, date,address) values ($return, '$response', $newid, now(),'$ip')";
  //echo $sql;
  pg_query($sql) or die("Database error");
  
}
if (!isset($_POST['response']) || !isset($_FILES)) { $content .= '<h1>Error, no image or text</h1>'; }
else {
  $content .= "Thanks for the response.  Reloading page...";
  $content .= '<meta http-equiv="refresh" content="3;URL=/img/' . $return . '.html" />';
}
include('layouts/default.php');

