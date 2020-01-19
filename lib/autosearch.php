<?

include('init.php');
$q = strtolower($_POST['q']);
echo '<ul>';

$sql = "select DISTINCT name from tags where name LIKE '%". $q . "%' order by name limit 5";
$res = pg_query($sql);

while ($row = pg_fetch_row($res)) {
  $tag = $row[0];
  $sql = "select path,id from images where tagsearch LIKE '%$tag%' order by tagsearch limit 1";
  $res2 = pg_query($sql);
  $row2 = pg_fetch_row($res2);
  if (pg_num_rows($res2) != 0) {
    $img = $row2[0];
    $thumb = getthumb($row2['0'], $row2['1']);
    $imgs = '<img class="searchres" width="30" height="30" src="gallery/thumbs/' . $thumb . '" />';
  }
  else {
    $imgs = '';
  }
  echo '<li>' .$imgs.'<br />' . $row[0] . '</li>';
}

echo '</ul>';
?>
