<?php
require_once('lib/init.php');

$sql = "select id from images where $filter order by created_on desc";

if ($res = pg_query($sql)) $qc++;

while ($pic = pg_fetch_array($res)) {
	echo '<a href="http://www.snaffed.com/img/' . $pic['id'] . '.html">http://www.snaffed.com/img/' . $pic['id'] . '.html</a><br />' . "\n";
}

