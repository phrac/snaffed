<?php
require_once('../lib/init.php');
$sql = "select * from images where id=6983";
$res = pg_query($sql);


while($img = pg_fetch_array($res)) {
	echo " id " . $img['id'];
	$file = $img['path'];
	$sig = puzzle_fill_cvec_from_file(BASEDIR . 'gallery/' . $file);
	echo $sig;
	$compressed_sig = puzzle_compress_cvec($sig);
  	$id = $img['id'];
  	iconv_set_encoding("output_encoding", "UTF-8");
  	$sql = "update images set signature='" . pg_escape_bytea($compressed_sig) . "' where id='$id'";
  	pg_query($sql);
  	echo " - updated database<br />";
  
}

?>