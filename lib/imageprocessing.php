<?

function deleteImg($id) {
  $img = $id;
  $sql = "select * from images where id = '$img'";

  $res = pg_query($sql); 
  if (pg_num_rows($res) == 0) $content = "<h1>Image not found</h1>";
  $row = pg_fetch_row($res);

  unlink(BASEDIR . "gallery/$row[1]");
  unlink(BASEDIR . "gallery/thumbs/$img.jpg");
  $sql = "delete from images where id=$row[0]";
  pg_query($sql);
  $sql = "delete from taggings where taggable_id = $row[0]";
  pg_query($sql);

  $content .= 'ID ' . $img . ' deleted';
  return($content);
}

function getHistogram($file) {

	$filename = BASEDIR . $file;  // assumes the file is in the $BASEDIR/gallery/ directory
	/*$ext = substr($filename, -3);
	if (strtolower($ext) == "gif") {
    if (!$im = imagecreatefromgif($filename)) {
        echo "Error opening $image!"; exit;
    }
} else if(strtolower($ext) == "jpg" || strtolower(substr($filename,-4)) == "jpeg") {
    if (!$im = imagecreatefromjpeg($filename)) {
        echo "Error opening $filename!"; exit;
    }
} else if(strtolower($ext) == "png") {
    if (!$im = imagecreatefrompng($filename)) {
        echo "Error opening $filename!"; exit;
    }
} else {
    die;
}

	//$im = ImageCreateFromJpeg($filename); 

	$imgw = imagesx($im);
	$imgh = imagesy($im);

	$n = $imgw*$imgh;
  $ri = 0;
  $gi = 0;
  $bi = 0;
  $rtot = 0;
  $gtot = 0;
  $btot = 0;
  
	for ($i=0; $i<$imgw; $i++) {
        for ($j=0; $j<$imgh; $j++) {
        
                // get the rgb value for current pixel
                
                $rgb = ImageColorAt($im, $i, $j); 
                
                // extract each value for r, g, b
                
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                
                $rtot += $r;
                $gtot += $g;
                $btot += $b;
                $ri++;
                $gi++;
                $bi++;
                
                // get the Value from the RGB value
                
                $V = round(($r + $g + $b) / 3, 20);
                
                $tot += ($V);
                
                // add the point to the histogram
                
               // $histo[$V] += $V / $n;
        }
	}
//	print_r($histo);
$aspect = $imgw/$imgh;
//$avg = array_product($histo) / ($n*$n);

$ravg = $rtot / $ri;
$gavg = $gtot / $gi;
$bavg = $btot / $bi;
$oavg = ($ravg + $gavg + $bavg);
$shit = ($tot/$oavg)/ ((($oavg/$ravg) + ($oavg/$gavg) + ($oavg/$bavg))) /100;
$dbshit = "'" . '{' . "$ravg,$gavg,$bavg,$shit" . '}' . "'";
*/
  $sig = puzzle_fill_cvec_from_file($filename);
  $compressed_sig = puzzle_compress_cvec($sig);
  $dbcvec = pg_escape_bytea($compressed_sig);
  return($dbcvec);

}

function process_tags($img, $rawtags, $ip){
  $rawtags = str_replace('.', ',',$rawtags);
  $tags = explode(',', $rawtags);

  foreach ($tags as $tag) {
    /* see if the tag already exists */
    $tag = ltrim($tag);
    $tag = strtolower($tag);
    $tag = pg_escape_string($tag);
    if ($tag != '') {
      $sql = "select id from tags where name='" . $tag . "'";
      $res = pg_query($sql);
      if($tag[0] == '-') {
        $tag = substr($tag,1);
        $sql = "select id from tags where name='$tag'";
       
        $res2 = pg_query($sql);
        $idrow = pg_fetch_row($res2);
        $delid = $idrow[0];
        $sql = "delete from taggings where taggable_id='$img' and tag_id='$delid'";
        pg_query($sql);
        continue;
      }    
      else if (pg_num_rows($res) != 0) {
        /* the tag was found */
        $tag = pg_fetch_array($res);
        $tagid = $tag['id'];
        pg_free_result($res);
      }
      else {
        /* the tag was not found, insert it and get the id */
        
        pg_free_result($res);
        $sql = "select nextval('tags_id_seq')"; // get the next sequence id
        $res = pg_query($sql);
        $row = pg_fetch_row($res);
        $tagid = $row[0];
        pg_free_result($res);
        $sql = "insert into tags (id, name,creator_ip) values ($tagid, '$tag', '$ip')";
        pg_query($sql) or die ("Could not insert new tag $tag (id = $tagid)"); // new tag inserted
     }
    
      // see if the image is already tagged with this particular tag
      $sql = "select id from taggings where tag_id='$tagid' and taggable_id='$img'";
      $res = pg_query($sql);
      if (pg_num_rows($res) != 0) {
       /* this image already has this tag, skip it */
        pg_free_result($res);
      }
      else {
       /* this tag does not exist for this image */
       $sql = "insert into taggings (tag_id, taggable_id, created_at) values ($tagid, $img, now())";
      
       pg_query($sql) or die("Could not apply tags!");
      }
  }
  }
  /* get all tags for the image so we can do fulltext searches.  add them to the image table */
  $sql = "select t.name from tags as t inner join taggings as ta on (ta.tag_id = t.id) where ta.taggable_id = $img";
  $res = pg_query($sql);
  while ($row = pg_fetch_row($res)) {
  	$tagstring .= $row[0] . ' ';
  	$tagstring = pg_escape_string($tagstring);
  }
  $sql = "update images set tagsearch='$tagstring' where id=$img";
  pg_query($sql);
  
}
