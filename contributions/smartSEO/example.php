<?php
if(!isset($_POST['url']))
{
echo '<form method="post" action="">
URL: <input type="text" name="url"/>
<input type="submit" value="Go"/>';
die();
}
include 'smartSEO.php';

$seo=new smartSEO($_POST['url']);

$seo->getreport();

