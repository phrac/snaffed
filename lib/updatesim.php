<?
include('init.php');

$id = $_GET['id'];
sleep(1);

$content = find_similar($id);	
echo $content;
?>