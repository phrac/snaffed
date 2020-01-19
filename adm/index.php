<?
include('../lib/init.php');
/*
if (isset($_POST['user']) && isset($_POST['pass'])) {
	
	$user = $_POST['user'];
	$pass = md5($_POST['pass']);
	if ($pass == md5('RLq34978') && $user == 'phrac'){
		setcookie('snfadm', md5('RLq34978'));
		header("Location: index.php");
	}
	else {
		showLogin('wrong username or password');
	}
}

if (checkLogin() == 0) {
	showLogin();
}
*/
function showLogin($error=NULL) {	
	$content .= '<center>';
	$content .= '<b>' . $error . '</b>';
	$content .= '<form action="' . $PHP_SELF . '" method="post">';
	$content .= 'user: <input type="text" name="user" /><br />';
	$content .= 'pass: <input type="password" name="pass" /><br />';
	$content .= '<input type="submit" value="login" />';
	$content .= '</form>';
	$content .= '</center>';
	include(BASEDIR . 'layouts/default.php');
	die();
}
$content .= 'Working on ' . $category . '<br />';
$content .= '<h1>Daily Tasks:</h1>';
$content .= '<a href="import.php">Import New Images</a><br />';
$content .= '<a href="multitag.php">Tag multiple images</a><br />';
$content .= '<a href="fuzzycompare.php">Compare fuzzy matches</a><br />';
$content .= '<a href="delete.php">Delete an image</a><br />';
$content .= '<a href="viewreported.php">View reported images</a><br />';
$content .= '<a href="marknudity.php">Mark images as nude/non-nude</a><br />';
$content .= '<a href="compareimg.php">Compare two images</a><br />';
$content .= '<br /><h1>Monthly Tasks:</h1>';
$content .= '<a href="fuzzyshit.php">Fuzzy compare all images</a><br />';
$content .= '<a href="cleantags.php">Delete unused tags</a>';
include(BASEDIR . 'layouts/default.php');

?>
