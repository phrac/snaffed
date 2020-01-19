<?

function process_upload($uldata,$response_id=0,$tags=NULL) {
global $category;
include_once('init.php');
require_once('imageprocessing.php');

if (isset($uldata['uploadedfile']) && $uldata['uploadedfile']['error'] == 0) {
	
	/* gather new file name information */
	$upload = $uldata['uploadedfile']['name'];
	$filename = $uldata['uploadedfile']['tmp_name'];
	$sql = "select nextval('images_id_seq')";
	$res = pg_query($sql);
	$row = pg_fetch_row($res);
	$imgid = $row[0];
	$ext = substr(strrchr($upload, '.'), 1);
	if ($ext == 'jpeg') $ext = 'jpg';
	$newabsfilename = $imgid . '.' . $ext;
	$newfilename = "gallery/" . $newabsfilename;
	//$newthumbname = BASEDIR . "gallery/thumbs/" . $newabsfilename; //always save the thumbs as a jpg
	$newthumbname = "gallery/thumbs/" . $imgid . '.jpg';
	/* move the file */	
	move_uploaded_file($filename, $newfilename);
	$type = exif_imagetype($newfilename);	
	
	/* check if file is valid. if valid, process */
	if ($type == 1 || $type == 2 || $type == 3) {
		$ip = $_SERVER['REMOTE_ADDR'];
	/*	$image = NewMagickWand();
		MagickReadImage($image, $newfilename);
		$w = NULL;
		$w = MagickGetImageWidth($image);
		/*if ($w > 1280) {
			$image->ResizeImage(1280,1280,NULL,1,TRUE);
			$image->WriteImage($newfilename);
			$image->Destroy;
			$image = New($newfilename);
			//unlink($newfilename);
			$resized = 1;
		} 
		MagickSetImageGravity($image, NorthGravity);
		MagickTransformImage($image,"125x125+0+0", "");
		$image->WriteImage($newthumbname);
		$image->Destroy;
		*/
		exec("convert $newfilename -size 300x300 -thumbnail 125x125^ -gravity center -extent 125x125 $newthumbname");
		$md5 = md5_file($newfilename);
		$sql = "select id from images where md5='$md5'";
		$res = pg_query($sql);
		if (pg_num_rows($res) == 0) {
			/* fuzzy image detection */
			$hist_orig = getHistogram($newfilename);
			
			$size = filesize($newfilename);
			$sql = "insert into images (id, path, size, md5, created_on, ip, views,signature,response_id,category) values ($imgid, '$newabsfilename', $size, '$md5', now(), '$ip', 0, '$hist_orig','$response_id', '$category')";
      pg_query($sql) or die ("could not insert new image");
			
			fuzzyMatch($imgid);
			$sql = "update images set fuzzychecked=true where id=$imgid";
      pg_query($sql);
			if ($tags != '') {
				
				process_tags($imgid, $tags, $_SERVER['REMOTE_ADDR']);
				$tags = NULL;
			}
			/* redirect to newly uploaded image */
			return($imgid);
		}
		else {
			unlink($newfilename);
			unlink($newthumbname);
			$content .= '<h1>Error: file already exists in database</h1>';
			include(BASEDIR . 'layouts/default.php');
			die();
		}
	}
	
	/* file is not valid, cleanup */
	else {
		$content .= '<h1>Error, invalid file type detected.  Only .jpg, .gif, & .png files are allowed at this time</h1>';
		unlink($newfilename);
		include(BASEDIR . 'layouts/default.php');
		die();
		
	}
}
// something went wrong with the upload
if (isset($uldata['uploadedfile']) && $uldata['uploadedfile']['error'] != 0) {
	$content .= '<h1>There was an error with your upload (error code ' . $uldata['uploadedfile']['error'] . ')</h1>';
	include(BASEDIR . 'layouts/default.php');
	die();
}
}
