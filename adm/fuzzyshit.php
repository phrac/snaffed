<?

require_once('../lib/init.php');
if (checkLogin() == 0) {
	header("Location: index.php");
}
$sql = "select id from images where fuzzychecked=false or fuzzychecked is null order by id asc";
$res = pg_query($sql);
while ($row = pg_fetch_array($res)) {
$id = $row[id];

$content .= $id . ' matched ' . fuzzyMatch($id) . ' images<br />';
$sql = "update images set fuzzychecked=true where id=$id";
pg_query($sql);
}
include(BASEDIR . 'layouts/default.php');

