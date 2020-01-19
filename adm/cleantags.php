<?php
require_once('../lib/init.php');
$sql = "select id,name from tags order by name";
$res = pg_query($sql);
while ($row=pg_fetch_row($res)) {
  	$tid = $row[0];
	$tname = $row[1];
	$sql = "select count(id) from taggings where tag_id='$tid'";
	$res2 = pg_query($sql);
  	while ($tagrow = pg_fetch_row($res2)) {
		$numtagged = $tagrow[0];
  		echo "[$tid] - <a href=\"http://192.168.2.6:8081/snaffed/browse.php?t=$tid\">$tname</a>: " . $tagrow[0] . " images tagged ";
		if ($numtagged == 0) {
			$sql = "delete from tags where id='$tid'";
			pg_query($sql);
			echo "- <b>DELETED</b>";
		}
		echo "<br />";
	}
}


?>
