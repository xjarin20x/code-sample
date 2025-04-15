<?php
header('Content-type: application/xml; charset=utf-8');
require_once("includes/conn.php");
require_once("includes/global_functions.php");
require_once("includes/HTMLPurifier.standalone.php");
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
if ($meta = $conn->prepare("SELECT itemURL FROM cms_items ORDER BY itemID ASC")) {
	$meta->execute();
	$meta->store_result();
	$meta->bind_result($db_url);
}
$date_today = date("Y-m-d"); 
$freq = "daily";
$prior = 1;
// $GLOBALS['hname'] = $_SERVER['HTTP_HOST'];
$GLOBALS['hname'] = $_SERVER['HTTP_HOST']."/"."palaystat";

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
';
echo '<url>
<loc>https://'.$GLOBALS['hname']. '/</loc>
<lastmod>'.$date_today.'</lastmod>
<changefreq>'.$freq.'</changefreq>
<priority>'.$prior.'</priority>
</url>
';
while ($meta->fetch()) {
echo '<url>
';
$url_temp = XML1_replace($db_url);
$url_temp = $purifier->purify($url_temp);
$url = 'https://'.$GLOBALS['hname']. '/'. $url_temp;
echo '<loc>'.$url.'</loc>
';
echo '<lastmod>'.$date_today.'</lastmod>
';
echo '<changefreq>'.$freq.'</changefreq>
';
echo '<priority>'.$prior.'</priority>
';
echo '</url>
';
}echo '</urlset>
';
?>