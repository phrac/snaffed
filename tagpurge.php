<?php
require_once('lib/init.php');
if (checkLogin() == 0) {
	header("Location: index.php");
}

	$sql = "select taggable_id from taggings order by taggable_id desc";
	$res = pg_query($sql);
	while ($row = pg_fetch_row($res)) {
		$sql = "select id from images where id='$row[0]'";
		$cres = pg_query($sql);
		if (pg_num_rows($cres) == 0) {
      echo "image $row[0] not found<br />";
      $missing++;
      $sql = "delete from taggings where taggable_id = $row[0]";
      pg_query($sql);
    }
		
	}
	
echo "Total missing taggings: $missing";
pg_free_result($res);	

