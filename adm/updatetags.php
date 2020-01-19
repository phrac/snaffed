<?php
require_once('lib/init.php');
$sql = "select id from images";
$res = pg_query($sql);
while ($row=pg_fetch_row($res)) {
	$tagstring = NULL;
	$img = $row[0];
	$sql = "select t.name from tags as t inner join taggings as ta on (ta.tag_id = t.id) where ta.taggable_id = $img";
  	$res2 = pg_query($sql);
  	while ($tagrow = pg_fetch_row($res2)) {
  		//$optimized_tags = str_replace(' ','&',$tagrow[0]);
  		$tagstring .= $tagrow[0] . ' ';
  	}
  	$sql = "update images set tagsearch='$tagstring' where id=$img";
  	pg_query($sql);
  	echo "Updating $img - setting tags to '$imgstring'<br />";
}


?>