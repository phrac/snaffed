<?php
include('lib/init.php');
$content .= '<center>';
$sql = "select * from tags order by name";
$res = pg_query($sql);
while ($tag = pg_fetch_row($res)) {
	$content .= '<a href="browse.php?t=' . $tag[0] . '">' . $tag[1] . '</a>&nbsp;&nbsp;';
}
$content .= '</center>';
include('layouts/default.php');
