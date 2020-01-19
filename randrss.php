<?php
require_once('lib/init.php');
require_once('lib/imageprocessing.php');
	$sql = "select * from images order by random() limit 1";


$res = pg_query($sql);
$qc++;

$pic = pg_fetch_array($res);

//print_r($pic);
/* check to see if image actually exists */ 
if (pg_num_rows($res) == 0) $content = "<h1>Image not found</h1>";


/* image exists, lets display it */
else {
	$sql = "select t.id, t.name from tags as t inner join taggings as tg on (tg.tag_id = t.id) where tg.taggable_id = " . $pic['id'];
	$tres = pg_query($sql);
	$path = 'gallery/' . $pic['path'];
	if (substr($pic['path'], -3) == 'gif') {
		$content .= '<a href="randss.php"><img src="gallery/'. $pic['path'] . '" /></a><br />';
		$path = "images/snaffed.png";
	}
	else {
		$ext = substr(strrchr($pic['path'], '.'), 1);
		$content .= '<center><a href="randss.php"><img height="800" src="gallery/' . $pic['path'] . '" /></a></center><br />';
	}
	
	$sql = "update images set views = views + 1 where id = $pic[id]";
	pg_query($sql);
	$qc++;
	
	
}
header("Content-type: text/xml");
@date_default_timezone_set("GMT");

$writer = new XMLWriter();
$writer->openURI('php://output');
$writer->startDocument('1.0');

$writer->setIndent(4);

// declare it as an rss document
$writer->setIndent(4);
$writer->startElementNS(NULL,'snaffedpic','http://dieseldl.org/snaffed/schema');

$writer->writeElement('pic_id', $pic[id]);
$writer->writeElement('pic_name', $pic[path]);
$writer->writeElement('pic_url', 'http://dieseldl.org:8081/snaffed/randssimg.php?id=' . $pic[id]);
$writer->writeElement('pic_original', 'http://dieseldl.org:8081/snaffed/gallery/' . $pic['path']);
$writer->writeElement('pic_tags', $pic[tagsearch]);
$writer->writeElement('nudity', $pic[nudity]);
$writer->writeElement('md5', $pic[md5]);
$writer->writeElement('created', $pic[created_on]);
$writer->writeElement('size', $pic[size]);
$writer->endElement();
$writer->endDocument();
$writer->flush();


$content .= "<br /><font size=\"14\"><center><a href=\"gallery/" . $pic['path'] . "\">Download</a></center></font>";
$content .= "<br /><font size=\"14\"><center><a href=\"gallery/" . $pic['path'] . "\">Mark as fave</a></center></font>";
