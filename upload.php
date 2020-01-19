<?php
include_once('lib/init.php');


if (isset($_FILES['uploadedfile']) && $_FILES['uploadedfile']['error'] == 0) {
	require_once('lib/process_upload.php');
	$newid = process_upload($_FILES,0,$_POST['tags']);
	header("Location: view.php?img=$newid");
	die();
}
	
/* display the file upload form */

$content .= '<h1>Upload Rules:</h1>';
$content .= file_get_contents('txt/uploadrules.txt');
$content .= '<br /><br /><br />';
$content .= '<table border="0" cellpadding="3" cellspacing="3">';
$content .= '<form enctype="multipart/form-data" action="upload.php" method="POST">';
$content .= '<input type="hidden" name="ip" value="' . $_SERVER['REMOTE_ADDR'] . '" />';
$content .= '<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />';
$content .= '<tr><td>Choose a file to upload: </td><td><input name="uploadedfile" type="file" /></td></tr>';
$content .= '<tr><td>Apply These Tags: </td><td><input name="tags" type="text" size="30" maxlength="80" /></td></tr>';
$content .= '<tr><td><input type="submit" value="Upload File" /></td></tr>';
$content .= '</form>';
$content .= '</table>';


include('layouts/default.php');
