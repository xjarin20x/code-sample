<?php
require_once("includes/header.php");
$prepath = checksubdomain(dirname(__FILE__), $conn, $purifier);
// PHP 5.3
//$prepath = checksubdomain(__DIR__, $conn, $purifier);
?>
<div class="page col-lg-8 mx-auto">
	<div class="page-header">
		<h1>Search Results</h1>
	</div>
    <div class="page-body search-results">
<?php
if(count($_GET) === 1){
    $mquery = $rquery = "";

    reset($_GET);
	$fkey = key($_GET);
    switch ($fkey) {
        case 's':
            $s = disinfect_var($_GET["s"]);
			$mquery = "SELECT `itemID`, `itemTitle`, `itemCategory`, `itemSubCategory`, `itemURL`, MATCH(`itemSearchKeywords`) AGAINST(?) as score FROM cms_items WHERE MATCH(`itemSearchKeywords`) AGAINST(?) > 0 ORDER BY itemSubCategory ASC, SCORE DESC";
			$rquery = "SELECT `itemID`, `itemTitle`, `itemCategory`, `itemSubCategory`, `itemURL`, MATCH(`itemSearchKeywords`) AGAINST(? WITH QUERY EXPANSION) as score FROM cms_items WHERE MATCH(`itemSearchKeywords`) AGAINST(? WITH QUERY EXPANSION) > 0 ORDER BY itemSubCategory ASC, SCORE DESC";
			break;     
        default:
			unset($fkey);
			break;
    }
    
    if (($matched = $conn->prepare($mquery)) && isset($fkey)) {
        $s = $purifier->purify($s);
        $matched->bind_param("ss", $s, $s);
        $matched->execute();
        $matched->store_result();
        $count = $matched->num_rows;
        $matched->bind_result($ID, $title, $category, $subcategory, $url, $score);
        if($count == 0){
            echo "<p>We can't seem to find any content for: " . $s . ". You can try a different keyword.</p><br/>";
            echo "<h2>Can't find what you are looking for? Try our request service.</h2>";
            echo '<p><a class="btn btn-primary" href="https://docs.google.com/forms/d/1cSYpUDRME-ieWNk1ZtrE7YsCNlMTu1jJpO_EHHhL5oU/viewform">Request data</a><br/><br/>';
            echo "<h2>Or try finding it by starting in these pages: </h2>";
            echo '<p><a href="' . $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/profile">Survey Results</a></p>';
            echo '<p><a href="' . $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/statistics">Rice Statistics</a></p>';
            echo '<p><a href="' . $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/resources">Resources</a></p>';
            echo '<p><a href="' . $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/related-links">Related Links</a></p>';
        }
        else{
            if($count == 1){
                $tempstr = '<h2>Search results for: "' . $s . '" <small>' . $count . ' result</small></h2>';
            }
            else{
                $tempstr = '<h2>Search results for: "' . $s . '" <small>' . $count . ' results</small></h2>';
            }
            $tempstr = $purifier->purify($tempstr);
            echo $tempstr;
            $tempstr = "";
            $majorResultsID = array();
            while($matched->fetch()){
                array_push($majorResultsID, $ID);
                $link = $GLOBALS['htp'] . "://" . $GLOBALS['hname'] . "/" . $url;
                if(pathinfo($url, PATHINFO_EXTENSION)) {
                    $title .= " [" . strtoupper(pathinfo($url, PATHINFO_EXTENSION)) . " file]";
                }
                $tempstr = '<div class="media">';
                $tempstr .= '<div class="media-body">';
                $tempstr .= '<h3><a href="'. $link .'">' . $title . '</a></h3>';
                $tempstr .= '<p class="search-link">' . $link . "</p>";
                if(empty($subcategory)){
                    $tempstr .= '<span class="badge badge-secondary">' . $category . "</span></div></div>";
                }
                else {
                    $tempstr .= '<span class="badge badge-secondary">' . $subcategory . " under " . $category . "</span></div></div>";
                }
                $tempstr = $purifier->purify($tempstr);
                echo $tempstr;
            }
            if ($count <= 10) {
                if ($related = $conn->prepare($rquery)) {
                    $related->bind_param("ss", $s, $s);
                    $related->execute();
                    $related->store_result();
                    $rcount = $related->num_rows;
                    $related->bind_result($ID, $title, $category, $subcategory, $url, $score);
                    if($rcount > 5) {
                        $successCount = 0;
                        $rstr = '<br/ ><h2>You may also check these related items...</h2>';
                        while($related->fetch() && $successCount < 12){
                            if(!in_array($ID, $majorResultsID)) {
                                $rlink = $GLOBALS['htp'] . "://" . $GLOBALS['hname'] . "/" . $url;

                                if(pathinfo($url, PATHINFO_EXTENSION)) {
                                    $title .= " [" . strtoupper(pathinfo($url, PATHINFO_EXTENSION)) . " file]";
                                }
                                $rstr .= '<div class="media">';
                                $rstr .= '<div class="media-body">';
                                $rstr .= '<h3><a href="'. $rlink .'">' . $title . '</a></h3>';
                                $rstr .= '<p class="search-link">' . $rlink . "</p>";
                                if(empty($subcategory)){
                                    $rstr .= '<span class="badge badge-secondary">' . $category . "</span></div></div>";
                                }
                                else {
                                    $rstr .= '<span class="badge badge-secondary">' . $subcategory . " under " . $category . "</span></div></div>";
                                }
                                $rstr = $purifier->purify($rstr);
                                echo $rstr;
                                $rstr = "";
                                $successCount++;
                            }
                        }	
                    }
                }
            }
        }
    }
    else {
			echo "<p>We can't seem to find any content of your search query. You can try a different keyword.</p><br/>";
			echo "<h2>Can't find what you are looking for? Try our request service.</h2>";
			echo '<p><a class="btn btn-primary" href="https://docs.google.com/forms/d/1cSYpUDRME-ieWNk1ZtrE7YsCNlMTu1jJpO_EHHhL5oU/viewform">Request data</a><br/><br/>';
			echo "<h2>Or look again by starting in these pages: </h2><br/>";
            echo '<p><a href="' . $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/profile">Survey Results</a></p>';
            echo '<p><a href="' . $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/statistics">Rice Statistics</a></p>';
            echo '<p><a href="' . $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/resources">Resources</a></p>';
            echo '<p><a href="' . $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/related-links">Related Links</a></p>';
    }
}
else {
		echo "<p>We can't seem to find any content of your search query. You can try a different keyword.</p><br/>";
		echo "<h2>Can't find what you are looking for? Try our request service.</h2>";
		echo '<p><a class="btn btn-primary" href="https://docs.google.com/forms/d/1cSYpUDRME-ieWNk1ZtrE7YsCNlMTu1jJpO_EHHhL5oU/viewform">Request data</a><br/><br/>';
		echo "<h2>Or look again by starting in these pages: </h2><br/>";
        echo '<p><a href="' . $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/profile">Survey Results</a></p>';
        echo '<p><a href="' . $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/statistics">Rice Statistics</a></p>';
        echo '<p><a href="' . $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/resources">Resources</a></p>';
        echo '<p><a href="' . $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/related-links">Related Links</a></p>';
}
?>
    </div>
</div>
<?php
require_once("includes/footer.php");
?>