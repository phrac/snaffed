<?php

$startexec = microtime();
global $qc;
$qc = 0;
$debug=0;


require_once('functions.php');

if (isset($_GET['rss'])) {
	define("ROWS_PER_PAGE", 12);
}
else {
	define("ROWS_PER_PAGE", 35);
}
define("BASEDIR", '/home/www/snaffed/');
define("FUZZY_MATCHING", 3.5);
//$db = pg_pconnect("dbname=snaffed user=snaffed");
$db = new Mongo();

global $filter;
$site = $_SERVER['HTTP_HOST'];
switch($site){
	case 'girls.snaffed.com':
		include(BASEDIR . 'sites/girls/config.php');
		break;
	case 'fun.snaffed.com':
		include(BASEDIR . 'sites/fun/config.php');
		break;
	case 'mc.snaffed.com':
		include(BASEDIR . 'sites/mc/config.php');
		break;
	case 'atv.snaffed.com':
		include(BASEDIR . 'sites/atv/config.php');
		break;
	case 'bike.snaffed.com':
		include(BASEDIR . 'sites/bike/config.php');
		break;
	default:
		$category = 2;
}

$filter = ' 1 = 1'; 

/* check if the user is banned */
$ip = $_SERVER['REMOTE_ADDR'];
$sql = "select * from bans where address='$ip' and (expires > now() or expires is null)";
$res = pg_query($sql);
if (pg_num_rows($res) > 0) {
	$row = pg_fetch_array($res);
	if ($row['expires'] == NULL || $row['expires'] == '') { $expires = "<b>NEVER</b>"; }
	else { $expires = $row['expires']; }
	$hash = $row['hash'];
	setcookie('vd', $hash, $row['timestamp'], '/');
	showBan($row['address'], $row['reason'], $expires);
}

function showBan($ip,$reason,$expires) {
	$content .= '<h1>You have been BANNED!<br /> (IP ' . $ip . ')</h1>';
	$content .= '<br /><br /><b>You have been banned for the following reason:</b><br />';
	$content .= nl2br($reason);
	$content .= '<br /><br />Your ban expires on ';
	$content .= $expires;
	include(BASEDIR . 'banned.php');
	die();
	//header("Location: http://www.snaffed.com/banned.php");

}
?>
