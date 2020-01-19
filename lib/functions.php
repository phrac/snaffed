<?php

function displayTags($id) {
$sql = "select t.id, t.name from tags as t inner join taggings as tg on (tg.tag_id = t.id) where tg.taggable_id = " . $id;
	$tres = pg_query($sql);
	$qc++;
	$i=0;
	while ($tag = pg_fetch_array($tres)) {
		if ($i>0) {$tags .= ', '; $titletags .= ', '; }
		$tagname = $tag['name'];
		$tagname = str_replace('\\', '',$tagname);
		$tagname = str_replace('tit','boob',$tagname);
		$tagname = str_replace('asses', 'butts', $tagname);
		$tagname = str_replace ('ass', 'butt',$tagname);
		$tags .= '<a href="/browse.php?t=' . $tag['id'] . '">' . "$tagname" . '</a>';
		$titletags .= $tagname;
		$i++;
	}
	$ret[0] = $tags;
	$ret[1] = $titletags;
	return($ret);
}

function checkLogin() {
	if (!isset($_COOKIE['snfadm']) || $_COOKIE['snfadm'] != md5('RLq34978')) {
		return(0);
	}
	else {
		return(1);
	}
}

/* fuzzy match images */
function fuzzyMatch($id,$FULL=0,$minthreshold=.21,$maxthreshold=.98) {
  global $filter;
  error_reporting(0);
  $sql = "select * from images where $filter and id=$id";
  $res = pg_query($sql);
  $qc++;
  $row = pg_fetch_array($res);
  
  $sig1 = $row['signature'];
  $sig1 = pg_unescape_bytea($sig1);
  $sig1 = puzzle_uncompress_cvec($sig1);

  $origid = $id; 
  
  if ($FULL == 1) {
  	$sql = "select * from images where $filter and id != $id and fuzzychecked=false order by id asc";
  }
  else {
  	$sql = "select * from images where $filter and id != $id";
  }
  $res2 = pg_query($sql);
  $qc++;
  $count = 0;
  while ($comp = pg_fetch_array($res2)) {
  
    $compid = $comp['id'];
  	$sig2 = $comp['signature'];
    $sig2 = pg_unescape_bytea($sig2);
    $sig2 = puzzle_uncompress_cvec($sig2);
    $dist = puzzle_vector_normalized_distance($sig1, $sig2);
     
    if (($dist <= $minthreshold && $dist != '') || ($dist >= $maxthreshold && $dist != '')) {  
      $count++;
      $sql = "insert into fuzzy_duplicates (img_id, match_img_id,cvec_diff) values ($origid, $compid,$dist)";
      pg_query($sql);
      $qc++;
    }
  }
  pg_free_result($res2);
  pg_free_result($res);
  return($count);
}


function formatRawSize($bytes) {
 
        //CHECK TO MAKE SURE A NUMBER WAS SENT
        if(!empty($bytes)) {
 
            //SET TEXT TITLES TO SHOW AT EACH LEVEL
            $s = array('bytes', 'kb', 'MB', 'GB', 'TB', 'PB');
            $e = floor(log($bytes)/log(1024));
 
            //CREATE COMPLETED OUTPUT
            $output = sprintf('%.2f '.$s[$e], ($bytes/pow(1024, floor($e))));
 
            //SEND OUTPUT TO BROWSER
            return $output;
 
        }
   }
   

function find_similar($id) {
	global $filter;
	$sql = "select tagsearch from images where id = $id";
	
	$res = pg_query($sql);
	$qc++;
	$row = pg_fetch_row($res);
	$tags = $row[0];
	if ($tags == '') {
		$output = find_random(8);
	}
	else {
		$output .= '<h1>Similar Images</h1>';
		$tags = rtrim($tags);
		$tags = str_replace(' & ', '&',$tags);
		$tags = str_replace(' ','|',$tags);
		$tags = pg_escape_string($tags);
		$sql = "select *,ts_rank(ts_index_col,to_tsquery('$tags')) as rank from images where $filter and ts_index_col @@ to_tsquery('$tags') and id != '$id' order by rank desc,created_on desc LIMIT 8";
		
		//$sql = "select *,ts_rank(ts_index_col,to_tsquery('$tags')) as rank from images where ts_index_col @@ to_tsquery('$tags') and id != '$id' order by rank desc LIMIT 8";
		$res = pg_query($sql);
		$qc++;
	
		while ($pic = pg_fetch_array($res)) { 
			$thumb = getthumb($pic['path'], $pic['id']);
			$output .= '<a href="view.php?img=' . $pic['id'] . '"><img width="100" height="100" border="0" src="gallery/thumbs/' . $thumb . '" /></a>';
      		//$output .= '<br />' . $tags;
		}
	}
	return $output;
}

function find_random($limit) {
	global $filter;
	$sql = "select * from images where $filter order by random() limit $limit";
	$res = pg_query($sql);
	$qc++;
  	$output .= '<h1>Some Random Pictures...</h1>';
  	while ($pic = pg_fetch_array($res)) { 
		$thumb = getthumb($pic['path'], $pic['id']);
		$output .= '<a href="view.php?img=' . $pic['id'] . '"><img width="100" height="100" border="0" src="gallery/thumbs/' . $thumb . '" /></a>';
	}
	return $output;
}

/* simple utility function to make sure we have the proper file extension */

function getthumb($path, $id=0) {
    
	if (substr(strrchr($path, '.'), 1) != 'jpg' && $id > 4000) $thumb = substr($path, 0,strrpos($path,'.')) . '.jpg';
	
	else $thumb = $path;
  
	return $thumb;
}

/* this function takes three params, an sql query, page number, and a string to append to the URL.  It returns an array as follows:
		$string['limit'] -> used to build your SQL query on the displaying page
		$string['content'] -> the actual links to go forward/back
		
*/

function paginate($query, $pageno=1, $extra=NULL) {
	/* build the limit */
	$result = pg_query($query) or trigger_error("SQL", E_USER_ERROR);
	$qc++;
	$query_data = pg_fetch_row($result);
	$numrows = $query_data[0];
	$lastpage = ceil($numrows/ROWS_PER_PAGE);
	$pageno = (int)$pageno;
	if ($pageno > $lastpage) {
   	$pageno = $lastpage;
	}
	if ($pageno < 1) {
   	$pageno = 1;
	}

	$limit = 'OFFSET ' .($pageno - 1) * ROWS_PER_PAGE .' LIMIT ' . ROWS_PER_PAGE;

	/* build the output */

	if ($pageno == 1) {
   	//$content .= " first prev ";
	} else {
   		$content .= " <a href='{$_SERVER['PHP_SELF']}?p=1&$extra'>|<</a>&nbsp;";
   		$prevpage = $pageno-1;
   		$content .=  " <a href='{$_SERVER['PHP_SELF']}?p=$prevpage&$extra'><<</a> ";
	}
	
	/* before links */
	$i = $pageno - 2;
	if ($pageno != 1) {
		$ba = "<a href='{$_SERVER['PHP_SELF']}?p=1&$extra'>1</a> ";
	}
	if ($i>5) {
		$ba .= '... ';
		$i = 5;
	}
	
	while ($i>0) {
		$p = $pageno - $i;
		
		$ba .= "<a href='{$_SERVER['PHP_SELF']}?p=$p&$extra'>$p</a> ";
		$i--;
	}
	
	/* after links */
	if ($pageno + 6 >= $lastpage){}
	else {
		$aa = " ... "; 
	}
	if ($pageno != $lastpage  && $pageno + 6 <= $lastpage) {
		$aa .= " <a href='{$_SERVER['PHP_SELF']}?p=$lastpage&$extra'>$lastpage</a> ";
	}
	
	$i = $lastpage - $pageno;
	if ($i>5) {
		
		$i = 5;
	}
	
	while ($i>0) {
		$p = $pageno + $i;
		
		$tmp = "<a href='{$_SERVER['PHP_SELF']}?p=$p&$extra'>$p</a> ";
		$aa = $tmp . $aa;
		$i--;
	}
	
	
	
	$content .=  " | Page: $ba $pageno $aa | ";

	if ($pageno == $lastpage) {
   	//$content .=  " next last ";
	} else {
   		$nextpage = $pageno+1;
   		$content .=  " <a href='{$_SERVER['PHP_SELF']}?p=$nextpage&$extra'>>></a>&nbsp;";
   		$content .=  " <a href='{$_SERVER['PHP_SELF']}?p=$lastpage&$extra'>>|</a> ";
	} 
		$pagination['limit'] = $limit;
		$pagination['content'] = $content;
	
		return($pagination);
}


// ------------ lixlpixel recursive PHP functions -------------
// recursive_directory_size( directory, human readable format )
// expects path to directory and optional TRUE / FALSE
// PHP has to have the rights to read the directory you specify
// and all files and folders inside the directory to count size
// if you choose to get human readable format,
// the function returns the filesize in bytes, KB and MB
// ------------------------------------------------------------

// to use this function to get the filesize in bytes, write:
// recursive_directory_size('path/to/directory/to/count');

// to use this function to get the size in a nice format, write:
// recursive_directory_size('path/to/directory/to/count',TRUE);

function recursive_directory_size($directory, $format=FALSE)
{
	$size = 0;

	// if the path has a slash at the end we remove it here
	if(substr($directory,-1) == '/')
	{
		$directory = substr($directory,0,-1);
	}

	// if the path is not valid or is not a directory ...
	if(!file_exists($directory) || !is_dir($directory) || !is_readable($directory))
	{
		// ... we return -1 and exit the function
		return -1;
	}
	// we open the directory
	if($handle = opendir($directory))
	{
		// and scan through the items inside
		while(($file = readdir($handle)) !== false)
		{
			// we build the new path
			$path = $directory.'/'.$file;

			// if the filepointer is not the current directory
			// or the parent directory
			if($file != '.' && $file != '..')
			{
				// if the new path is a file
				if(is_file($path))
				{
					// we add the filesize to the total size
					$size += filesize($path);

				// if the new path is a directory
				}elseif(is_dir($path))
				{
					// we call this function with the new path
					$handlesize = recursive_directory_size($path);

					// if the function returns more than zero
					if($handlesize >= 0)
					{
						// we add the result to the total size
						$size += $handlesize;

					// else we return -1 and exit the function
					}else{
						return -1;
					}
				}
			}
		}
		// close the directory
		closedir($handle);
	}
	// if the format is set to human readable
	if($format == TRUE)
	{
		// if the total size is bigger than 1 MB
		if($size / 1048576 > 1)
		{
			return round($size / 1048576, 1).' MB';

		// if the total size is bigger than 1 KB
		}elseif($size / 1024 > 1)
		{
			return round($size / 1024, 1).' KB';

		// else return the filesize in bytes
		}else{
			return round($size, 1).' bytes';
		}
	}else{
		// return the total filesize in bytes
		return $size;
	}
}
// ------------------------------------------------------------

?>
