<?php

/**
 * Project:     Securimage: A PHP class for creating and managing form CAPTCHA images<br />
 * File:        form.php<br /><br />
 *
 * This is a very simple form sending a username and password.<br />
 * It demonstrates how you can integrate the image script into your code.<br />
 * By creating a new instance of the class and passing the user entered code as the only parameter, you can then immediately call $obj->checkCode() which will return true if the code is correct, or false otherwise.<br />
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or any later version.<br /><br />
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.<br /><br />
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA<br /><br />
 * 
 * Any modifications to the library should be indicated clearly in the source code 
 * to inform users that the changes are not a part of the original software.<br /><br />
 *
 * If you found this script useful, please take a quick moment to rate it.<br />
 * http://www.hotscripts.com/rate/49400.html  Thanks.
 *
 * @link http://www.phpcaptcha.org Securimage PHP CAPTCHA
 * @link http://www.phpcaptcha.org/latest.zip Download Latest Version
 * @link http://www.phpcaptcha.org/Securimage_Docs/ Online Documentation
 * @copyright 2007 Drew Phillips
 * @author drew010 <drew@drew-phillips.com>
 * @version 1.0.3.1 (March 23, 2008)
 * @package Securimage
 *
 */

  session_start();
require_once('lib/init.php');
if (!isset($_GET['id']) || $_GET['id'] == '') {
	$content .= '<h1>No image specified</h1>';
	include('layouts/default.php');
	die();
}

$id = $_GET['id'];
$sql = "select * from images where id=$id";
$res = pg_query($sql);
if (pg_num_rows($res) == 0) {
	$content .= '<h1>Image not found</h1>';
	include('layouts/default.php');
	die();
}
$pic = pg_fetch_array($res);



if (empty($_POST)) {
$content .= '<table border="0" cellpadding="5" cellspacing="5">'; 
$content .= '<form method="POST">';
$content .= '<input type="hidden" name="imgid" value="' . $id . '" />';
$content .= '<tr><td>Reason for reporting:</td>';
$content .= '<td><select name="reason">';
$content .= '<option value="cp">Suspected Child Pornography (CP)</option>';
$content .= '<option value="hc">Hardcore</option>';
$content .= '<option value="cr">Copyright Infringement</option>';
$content .= '</select></td></tr>';
$content .= '<tr><td>Security Image:</td><td><img src="securimage_show.php?sid=' . md5(uniqid(time())) . '"></td></tr>';
$content .= '<tr><td>Type Security Code:</td><td><input type="text" name="code" /></td></tr>';

$content .= '<tr><td><input type="submit" value="Report" /></td></tr>';
$content .= '</form>';
$content .= '</table>';

$content .= '<h1>Picture you are reporting:</h1>';
if (substr($pic['path'], -3) == 'gif') {
	$content .= '<img src="/gallery/'. $pic['path'] . '" /><br />';
}
else {
	$content .= '<img src="/wm.php?i=' . $pic['path'] . '" /><br />';
}





} else { //form is posted
  include("securimage.php");
  $img = new Securimage();
  $valid = $img->check($_POST['code']);

  if($valid == true) {
    $id = $_POST['imgid'];
    $ip = $_SERVER['REMOTE_ADDR'];
    $reason = $_POST['reason'];
    $sql = "insert into reported_images (img_id,reason,ip,date) values ($id, '$reason', '$ip', now())";
    pg_query($sql);
    $sql = "update images set reported=true where id=$id";
    pg_query($sql);
    $content .= '<h1>Image Reported</h1>';
    $content .= '<p>The image will be reviewed and will either be removed from the database or the report will be cleared.</p>';
    
  } 
  else {
    $content .= "<center>Sorry, the code you entered was invalid.  <a href=\"javascript:history.go(-1)\">Go back</a> to try again.</center>";
  }
}

include('layouts/default.php');
?>

