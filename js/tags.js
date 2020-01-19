var xmlHttp

function showTags(str,imgid)
{ 
xmlHttp=GetXmlHttpObject()
if (xmlHttp==null)
 {
 alert ("Browser does not support HTTP Request")
 return
 }
var url="lib/tags.php"
url=url+"?tags="+str
url=url+"&imgid="+imgid
url=url+"&sid="+Math.random()
xmlHttp.onreadystatechange=stateChanged 
xmlHttp.open("GET",url,true)
xmlHttp.send(null)

}

function updateSimilar(imgid){
xmlHttp2=GetXmlHttpObject()
if (xmlHttp2==null)
 {
 alert ("Browser does not support HTTP Request")
 return
 }
var url="/lib/updatesim.php"
url=url+"?id="+imgid

xmlHttp2.onreadystatechange=stateChangeded
xmlHttp2.open("GET",url,true)
xmlHttp2.send(null)
}

function stateChangeded() 
{ 
if (xmlHttp2.readyState==4 || xmlHttp2.readyState=="complete")
 { 
 document.getElementById("similar").innerHTML=xmlHttp2.responseText
  
 } 
}

function stateChanged() 
{ 
if (xmlHttp.readyState==4 || xmlHttp.readyState=="complete")
 { 
 document.getElementById("tagsDiv").innerHTML=xmlHttp.responseText 
 document.tagsform.tags.value= "";
 
 
 } 
}

function GetXmlHttpObject()
{
var xmlHttp=null;
try
 {
 // Firefox, Opera 8.0+, Safari
 xmlHttp=new XMLHttpRequest();
 }
catch (e)
 {
 //Internet Explorer
 try
  {
  xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
  }
 catch (e)
  {
  xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
 }
return xmlHttp;
}