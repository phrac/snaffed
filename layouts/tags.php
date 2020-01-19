<?php
include('lib/init.php');

$sql = "select * from tags order by id";
$res = pg_query($sql);
while ($tag = pg_fetch_row($res)) {
	$content .= '<a href="browse.php?t=' . $tag[0]' . '">' . $row[1] . '</a>';
}

include('layouts/default.php');
