<?php
require_once('lib/init.php');
require_once('lib/imageprocessing.php');
/* process newly applied tags */
if ($_POST['pt'] == 1) {
  if ($_POST['tags'] != '' && $_POST['tags'] != ' ') {
  	require_once('lib/imageprocessing.php');
    process_tags($_GET['img'], $_POST['tags'], $_SERVER['REMOTE_ADDR']);
    $tags = NULL;
  }
}

if (isset($_GET['spree'])) {
	$sql = "select count(id) from images where tagsearch = '' or tagsearch is NULL";
	$res = pg_query($sql);
	$qc++;
	$row = pg_fetch_row($res);
	$untagged = $row[0];
	$sql = "select count(id) from images";
	$res = pg_query($sql);
	$qc++;
	$row = pg_fetch_row($res);
	$total = $row[0];
	$ptagged = round(($untagged / $total) * 100, 2);
	$content .= "$untagged untagged images remaining ($ptagged% untagged)<br />";
	pg_free_result($res);
	$sql = "select * from images where tagsearch='' or tagsearch is NULL order by random() limit 1";
}

else if (isset($_GET['randss'])) {
	$rand = (1+lcg_value()*(abs(5-1)));
//	pg_query("select setseed($rand)");
	$sql = "select * from images order by random() limit 1;";
}

/* show the image */
else {
	$img = $_GET['img'];
	$sql = "select * from images where id = '$img'";
	
}

$res = pg_query($sql);
$qc++;

$pic = pg_fetch_array($res);


/* check to see if image actually exists */ 
if (pg_num_rows($res) == 0) $content = "<h1>Image not found: $sql</h1>";

/* check to see if the image has been reported */
else if ($pic['reported'] == t) {
	$content .= '<h1>This image has been reported and is not available for viewing at this time</h1>';		
}

/* image exists, lets display it */
else {
	$sql = "select t.id, t.name from tags as t inner join taggings as tg on (tg.tag_id = t.id) where tg.taggable_id = " . $pic['id'];
	$tres = pg_query($sql);
	$qc++;
	$i=0;
	while ($tag = pg_fetch_array($tres)) {
		if ($i>0) {$tags .= ', '; $titletags .= ', '; }
		$tagname = $tag['name'];
		$tagname = str_replace('\\', '',$tagname);
		$tags .= '<a href="browse.php?t=' . $tag['id'] . '">' . "$tagname" . '</a>';
		$titletags .= $tagname;
		$i++;
	}
	
	$content .= '<h1>Tags: ' . $tags . '</h1>';
	$title = $titletags;
	$cvec = puzzle_fill_cvec_from_file('gallery/' . $pic['path']);	
	if ($img) $content .= '<form name="tagsform" action="view.php?img=' . $img . '"  method="post">';
	
	else $content .= '<form name="tagsform" action="view.php?spree&img=' . $pic['id'] . '"  method="post">';
	$content .= '<input type="hidden" name="pt" value="1" />';
	$content .= '<input type="text" name="tags" size="40" maxlength="80" />&nbsp;';
	$content .= '<input type="submit" value="Apply New Tags" />';
	$content .= '</form>';
	if (isset($_GET['randss'])) $content .= '<form name="tagsform" action="view.php?randss&img=' . $pic['id'] . '"  method="post"><input type="submit" value="next" /></form><br />';
	$content .= '<script type="text/javascript" language="JavaScript">
				document.forms[\'tagsform\'].elements[\'tags\'].focus();
				</script>';
	if (substr($pic['path'], -3) == 'gif') {
		$content .= '<img src="gallery/'. $pic['path'] . '" /><br />';
	}
	else {
		$ext = substr(strrchr($pic['path'], '.'), 1);
		$content .= '<img id="bigpic" onload="scaleImg(\'bigpic\')" onclick="scaleImg(\'bigpic\')" src="gallery/' . $pic['path'] . '" /><br />';
	}
	
	$sql = "update images set views = views + 1 where id = $pic[id]";
	pg_query($sql);
	$qc++;
	
	/* see if image is a response */
	/*if ($pic['response_id'] != 0) {
	$content .= '<div class="bottom_line">';
  $rid = $pic['response_id'];
  $sql = "select * from images where id=$rid";
  $resp_res = pg_query($sql);
  $qc++;
  $resp_row = pg_fetch_array($resp_res);
  $thumb = getthumb($resp_row['path'], $resp_row['id']);
  $content .= '<b>This image is a response to:</b><br />';
	$content .= '<a href="/img/' . $resp_row['id'] . '"><img border="0" src="gallery/thumbs/' . $thumb . '" /></a>';
	$content .= '</div>'; */

	/* post a text response 
	$content .= '<div class="bottom_line">';
	$content .= '<h1>Leave a comment/response:</h1>';
	$content .= '<form enctype="multipart/form-data" action="/response.php" method="post">';
$content .= '<table border="0" cellpadding="3" cellspacing="3">';
$content .= '<input type="hidden" name="ip" value="' . $_SERVER['REMOTE_ADDR'] . '" />';
$content .= '<input type="hidden" name="respond_id" value="' . $pic['id'] . '" />';
$content .= '<input type="hidden" name="MAX_FILE_SIZE" value="3000000" />';
$content .= '<tr><td>Text comment:</td><td><textarea rows="5" cols="30" name="response"></textarea>';
$content .= '<tr><td><b>Upload Rules:</b></td><td>' . file_get_contents('txt/uploadrules.txt') . '</td></tr>';
$content .= '<tr><td>Image comment: </td><td><input name="uploadedfile" type="file" /></td></tr>';
$content .= '<tr><td>Apply these tags to image: </td><td><input name="tags" type="text" size="30" maxlength="80" /> (separate tags with a comma (,) or period (.))</td></tr>';
$content .= '<tr><td><input type="submit" value="Leave response" /></td></tr>';
$content .= '</form>';
$content .= '</table>';
$content .= '</div>';


$content .= '<div class="bottom_line">';
$content .= '<h1>Responses and comments:</h1>';

$sql = "select * from comments where img_id = " . $pic['id'];
$res = pg_query($sql);
$qc++;
while ($response = pg_fetch_array($res)) {
  $content .= '<table class="blue" cellpadding="6" cellspacing="0"><tr><td>';
  $content .= '<tr><td colspan="2">';
  
  $content .= date("r", strtotime($response['date']));
  $content .= ' - No.' . $response['id'];
  $content .= '</td></tr><tr><td class="response">';
  $attached = $response['attached_id'];
  if ($attached != 0) {
    $sql = "select * from images where id=$attached";
    $res2 = pg_query($sql);
    $qc++;
    $row = pg_fetch_array($res2);
    $thumb = getthumb($row['path'], $row['id']);
	  $content .= '<a href="/img/' . $row['id'] . '.html"><img border="0" src="gallery/thumbs/' . $thumb . '" /></a>';
  }
  */
  /*$content .= '</td><td valign="top">';
  $content .= date("r", strtotime($response['date']));
  $content .= ' - No.' . $response['id'];
  $content .= '<br /><br />'; */ /*
  $comment = htmlentities($response['comment'],ENT_QUOTES,'UTF-8');
  $comment = nl2br($comment);
  $comment = str_replace('\\','',$comment);
  $comment = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]","<a href=\"\\0\" rel=\"nofollow\">\\0</a>", $comment);
  $content .= $comment;
  $content .= '</td></tr></table>'; */

  $content .= '</div>';

	
	/* build the sidebar */
	$ext = substr(strrchr($pic['path'], '.'), 1);
	$filelink = $pic['id'] . '.' . $ext;
	$url = "gallery/" . $pic['id'] . '.' . $ext;
	$real_url = $url;
	$max = 29;
	$url_len = strlen($url);

	if ($url_len > $max) {
		$del = $url_len - $max;
		$first = round($url_len/2-$del/2);
		$last = round($url_len/2+$del/2);
		$url = substr($url,0,$first) . "..." . substr($url,$last,$url_len);
	}
	$res = getimagesize('gallery/' . $pic['path']);
	$res = $res[0] . 'x' . $res[1];

	$sidebar_content .= "<h1>Image Information</h1>";
	$sidebar_content .= "<b>Views:</b> " . $pic['views'] . "<br />";
	$sidebar_content .= "<b>Resolution:</b> " . $res . '<br />';
	$sidebar_content .= "<b>File Size:</b> " . formatRawSize($pic['size']) . "<br />";
	$sidebar_content .= "<b>Uploaded:</b> " . date("d M, Y h:i A T America/Chicago", strtotime($pic['created_on'])) . "<br />";
	$sidebar_content .= "<b>Link:</b> <a href=\"gallery/" . $filelink . "\">$url</a><br />";
	$sidebar_content .= '<a href="delete.php?img=' . $pic['id'] . '">Delete Image</a><br /><br />';
	$sidebar_content .= '<a href="regen_thumb.php?id=' . $pic['id'] . '">Regenerate Thumb</a><br />';
	//$sidebar_content .= '<a href="report.php?id=' . $pic['id'] . '">Report image</a><br />';
	//$sidebar_content .= "<b>s.h.i.t: [<a href=\"faq.php?s=shit\">?</a>]</b> " . $pic['hit'] . '<br />';
//	$sidebar_content .= "cvec: " . puzzle_compress_cvec($cvec);
  $sidebar_content .= find_similar($pic['id']);	
}


include('layouts/default.php');
