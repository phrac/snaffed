<?
if ($category==0) {
  include('launch.php');
  die();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head>
<link rel="stylesheet" href="/snaffed/css/default.css" />

<meta http-equiv="content-type" content="text/html; charset=iso-8859-1"/>
<meta name="description" content="<? echo $sitemeta ?>"/>
<meta name="keywords" content="<? echo $sitemeta ?>"/> 
<meta name="author" content="disflux, inc."/> 

<title>snaffed<? if (isset($sitetitle)) echo $sitetitle; ?><? if (isset($title)) echo ' - ' . $title;  else echo ' - ' . $extratitle; ?></title>
<script src="/snaffed/js/tags.js"></script>
<script src="/snaffed/js/prototype.js" type="text/javascript"></script>
<script src="/snaffed/js/scriptaculous.js" type="text/javascript"></script>



<script type="text/javascript">
var saveWidth = 0;
var saveHeight = 0;
function scaleImg(item) 
{
	what = document.getElementById(item);

	if (navigator.appName=="Netscape") {
		winW = window.innerWidth;
	}
	
	if (navigator.appName.indexOf("Microsoft")!=-1) {
		winW = document.body.offsetWidth;
	}

	if (what.width>(765) || saveWidth>(765)) {
		if (what.width==(765)) {
			what.width=saveWidth;
		} else {
			saveWidth = what.width;
			what.style.cursor = "pointer";
			what.width=(765);
		}
	}
	
}
</script>
</head>

<body OnLoad="document.tagsform.tags.focus();">              




	<div class="header">
		
		<div class="title">
			
		</div>

		<div class="navigation">
		    <a href="/snaffed">Home</a>
		    
			<a href="browse.php">Browse Images</a>
			<a href="view.php?spree">Tagging Spree</a>
			
			<a href="tags.php">Tags</a>
			
			
			<div class="clearer"><span></span></div>
		</div>

	</div>

	<div class="main">
		<div class="sidenav">
           
			<h2>Search</h2>
	   <form action="browse.php" method="get">
	   <input type="text" id="search" name="q" />
	
	   <? /*<input type="text" name="q" size="17" />*/ ?>
	   <input type="submit" value="find" />
			</form>
		<div class="autocomplete" id="updater"></div>
			<script type="text/javascript">
        new Ajax.Autocompleter('search','updater','lib/autosearch.php', {});
      </script>
			
               <?php
			 echo $sidebar_content;
			
			 
		?>
            </div>
		</div>
		<div class="content">
         
               
			<? 
			
			if ($category == 0) {
				//include('/usr/local/www/data/snaffed/launch.php');
			}
			else {
				echo $content;
			}	
				 ?>

		</div>

		
	
		<div class="clearer"><span></span></div>

	</div>


<?
$stop_exec = microtime();
$exectime = $stop_exec - $start_exec;
?>

<div class="footer">
</div>
<? echo $tracker ?>
</body>

</html>


