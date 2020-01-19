<?php
require_once('lib/init.php');

/* find images tagged with a certain tag */
if ($_GET['t']) {
  $tagid = $_GET['t'];
  $pages = paginate("select count(*) from taggings where $filter and tag_id = " . $tagid, $_GET['p'], 't=' . $tagid);
  $sql = "select img.* from images as img inner join taggings as t on (t.taggable_id = img.id) where $filter and t.tag_id=" . $tagid . ' ' . $pages['limit'];
}

/* a search has been performed, process */
else if ($_GET['q']) {
$keywords_single = explode(' ', $_GET['q']);
//Get the number of words they are searching by

/*
$sizeof_keywords_single = sizeof($keywords_single);
$pspell_config = pspell_config_create("en");
$pspell_link = pspell_new_config($pspell_config);
$keywords_single_replacement = array(); //Create the replacement array
for($i = 0; $i < $sizeof_keywords_single; $i++) { //Loop through the words
	//Check if the word is correctly spelt
	if (!pspell_check($pspell_link, $keywords_single[$i])) {
		$keywords_misspell = true;
		//Get the suggestions from Pspell
		$suggestions = pspell_suggest($pspell_link, $keywords_single[$i]);
		//Take the first result (Pspell sorts the result)
		$keywords_single_replacement[$i] = $suggestions[0];
	} else {
		//Set the replacement word back to the original word
		$keywords_single_replacement[$i] = $keywords_single[$i];
	}
}*/
/*
 * The following code replaces the words with the corrected words above.
 */
if (!isset($keywords_phrase) && isset($keywords_misspell)) {
	$keywords_replacement = stripslashes($_GET['q']);
	for ($i = 0; $i < $sizeof_keywords_single; $i++) {
		$keywords_replacement = preg_replace(
					sprintf('#(?!<.*?)(%s)(?![^<>]*? )#i',
					preg_quote($keywords_single[$i])),
					$keywords_single_replacement[$i],
					$keywords_replacement);
	}
	$content .= '<h1>Did you mean "<i><a href="browse.php?q=' . $keywords_replacement . '">' . $keywords_replacement . '</a></i>"?</h1>';
}
  
  $q = $_GET['q'];
  $origq = $q;

  $q = ltrim($q);
  $q = str_ireplace('-', '!', $q);
  $q = str_ireplace(' or ', '|', $q);
  $q = str_replace(' & ', ' ', $q);
  $q = str_replace(' ', ' & ', $q);
  
  	$content .= "<h1>Search results for \"<i>$origq</i>\"</h1>";
  	$sql = "select count(*) from images where $filter and to_tsvector('english',tagsearch) @@ to_tsquery('$q')";
  	$res = pg_query($sql);
  	$results = pg_fetch_row($res);
  	if ($results[0] != 0) {
  		$pages = paginate($sql, $_GET['p'], 'q=' . $origq);
  	
  		$sql = "select *,ts_rank(ts_index_col,to_tsquery('$q')) as rank from images where $filter and to_tsvector('english',tagsearch) @@ to_tsquery('$q') order by rank desc " . $pages['limit'];
  	
    }
  	else { $content .= "<b>No Results</b>"; include('layouts/default.php'); echo $sql; die(); }
  	
  
}



/* default view */ 

else { 
	$content .= "Order by: <a href=\"$PHP_SELF?order=created_on\">Date Added</a> . <a href=\"$PHP_SELF?order=views\">Views</a><br />";  
  
  if (isset($_GET['order'])) $order = $_GET['order'];
	else $order = 'created_on';
  $pages = paginate("SELECT count(*) FROM images where $filter", $_GET['p'], "order=$order");
  $sql = "select * from images where $filter order by $order desc " . $pages['limit'];
}

$res = pg_query($sql);


/* load the template and process */
if (isset($_GET['rss'])) {
	header("Content-type: text/xml");
	@date_default_timezone_set("GMT");

	$writer = new XMLWriter();
	$writer->openURI('php://output');
	$writer->startDocument('1.0');

	$writer->setIndent(4);

	// declare it as an rss document
	$writer->setIndent(4);
	$writer->startElementNS(NULL,'snaffedgallery','http://dieseldl.org/snaffed/schema');
    while ($pic = pg_fetch_array($res)) {
		$thumb = getthumb($pic['path'], $pic['id']);
		$writer->startElement('pic');
		$writer->writeElement('pic_id', $pic[id]);
		$writer->writeElement('pic_url', 'http://dieseldl.org:8081/snaffed/randssimg.php?id=' . $pic[id]);
		$writer->writeElement('pic_thumb', "http://dieseldl.org:8081/snaffed/gallery/thumbs/$thumb");
		$writer->endElement();
	}
	$writer->endElement();
	$writer->endDocument();
	$writer->flush();
}
else {
	while ($pic = pg_fetch_array($res)) {
		$thumb = getthumb($pic['path'], $pic['id']);
		$content .= '<a href="view.php?img=' . $pic['id'] . '"><img border="0" src="gallery/thumbs/' . $thumb . '" /></a>';
	}
    $content .= "<br /><h1>";
	$content .= $pages['content'];
	$content .= "</h1></center>";
	$sidebar_content = find_random(8);
	include("layouts/default.php");
}
