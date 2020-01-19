<?php
require_once('lib/init.php');
$content .= "<h1>Newest Images:</h1>";

$sql = "select * from images where $filter order by created_on desc limit " . ROWS_PER_PAGE;

if ($res = pg_query($sql)) $qc++;

while ($pic = pg_fetch_array($res)) {
	$thumb = getthumb($pic['path'],$pic['id']); 
	$content .= '<a href="view.php?img=' . $pic['id'] . '"><img border="0" src="gallery/thumbs/' . $thumb . '" /></a>';
}

$content .= '<h1><a href="browse.php">See more images</a></h1>';
$sidebar_content = find_random(8);
/* load the template and process */
include("layouts/default.php");
