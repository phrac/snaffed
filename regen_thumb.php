<?php

	include('lib/init.php');
	$imgid = $_GET[id];
	$sql = "select * from images where id = '$imgid'";
	$res = pg_query($sql);
	$row = pg_fetch_row($res);

	$filename = BASEDIR . "gallery/" . $row[1] . '[0]';
	$newthumbname = BASEDIR . "gallery/thumbs/" . $imgid . '.jpg'; //always save the thumbs as a jpg
      
exec("convert '$filename' -size 300x300 -thumbnail 125x125^ -gravity center -extent 125x125 $newthumbname");
