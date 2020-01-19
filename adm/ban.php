<?
require_once('../lib/init.php');
if (checkLogin() == 0) {
	header("Location: index.php");
}

if (!isset($_GET['ip'])) {
	$content .= '<h1>No IP specified</h1>';
	$content .= $_GET['ip'];
}

else if (isset($_POST['ip'])) {
	$ip = $_POST['ip'];
	$expires = $_POST['interval'];
	$reason = $_POST['reason'];
	$sql = "insert into bans (address,expires,reason) values ('$ip', now() + interval '$expires', '$reason')";
	pg_query($sql);
	$content .= '<h1>Ban added</h1>';
	$content .= '<p>' . $ip . ' is banned for ' . $reason . ' for a length of ' . $expires . '</p>';
}

else {
	$ip = $_GET['ip'];
	$content .= '<table border="0" cellpadding="5" cellspacing="5">';
	$content .= '<form action="' . $PHP_SELF . '" method="post">';
	$content .= '<input type="hidden" name="ip" value="' . $ip . '" />';
	$content .= '<tr><td>IP to ban:</td><td>' . $ip . '</td></tr>';
	$content .= '<tr><td>Reason:</td><td><input type="text" name="reason" /></td></tr>';
	$content .= '<tr><td>Expires:</td>';
	$content .= '<td><select name="interval">';
	$content .= '<option value="1 hour">1 hour</option>';
	$content .= '<option value="1 day">1 day</option>';
	$content .= '<option value="3 days">3 days</option>';
	$content .= '<option value="7 days">7 days</option>';
	$content .= '<option value="1 month">1 month</option>';
	$content .= '<option value="3 months">3 months</option>';
	$content .= '<option value="6 months">6 months</option>';
	$content .= '<option value="1 year">1 year</option>';
	$content .= '<option value="">NEVER</option>';
	$content .= '</select></td></tr>';
	$content .= '<tr><td><input type="submit" value="BAN" />';
	$content .= '</form>';
	$content .= '</table>';
}

include(BASEDIR . 'layouts/default.php');