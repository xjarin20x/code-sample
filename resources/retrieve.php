<?php
require_once("../includes/conn.php");
require_once("../includes/global_functions.php");
require_once("../includes/HTMLPurifier.standalone.php");

// $GLOBALS['hname'] = $_SERVER['HTTP_HOST'];
$GLOBALS['hname'] = $_SERVER['HTTP_HOST']."/"."palaystat";

if(isset($_POST) && !empty($_POST)){
	$content = "";
	if(!empty($_POST['pubcategory'])){
		$pubcategory = disinfect_var($_POST['pubcategory']);
		//if ($res = $conn->prepare("SELECT i.itemID, i.itemTitle, i.itemSubCategory, i.itemURL, it.itemPreviewURL FROM cms_items i LEFT JOIN cms_itemdetails it ON i.itemID = it.itemID WHERE i.itemCategory = 'Publications' AND i.itemSubCategory = ? ORDER BY i.itemTitle ASC")) {
        if ($res = $conn->prepare("SELECT i.itemID, i.itemTitle, i.itemSubCategory, i.itemURL, it.itemPreviewURL, i.itemDescription FROM cms_items i LEFT JOIN cms_itemdetails it ON i.itemID = it.itemID WHERE i.itemCategory = 'Resources' AND i.itemSubCategory = ? ORDER BY i.itemTitle ASC")) { 
			$res->bind_param("s", $pubcategory);
			$res->execute();
			$res->store_result();
			$count = $res->num_rows;
			$res->bind_result($ID, $title, $category, $url, $thumbnail, $description);
			if($count > 0) {
				while($res->fetch()){
                    $link = $GLOBALS['htp'] . "://" . $GLOBALS['hname'] . "/" . disinfect_var($url);
                    $image = $GLOBALS['htp'] . "://" . $GLOBALS['hname'] . "/" . disinfect_var($thumbnail);
                    echo '<div class="media">';
                    echo '<div class="media-left">';
                    echo '<a href="'. disinfect_var($link) .'">';
                    echo '<img class="media-object" src="'. disinfect_var($image) .'" alt="'. disinfect_var($title) .' preview"></a>';
                    echo '</div>';
                    echo '<div class="media-body">';
                    echo '<h3><a href="'. disinfect_var($link) .'">'. disinfect_var($title) .'</a></h3>';
                    echo '<span class="badge badge-secondary">'. disinfect_var($category) .'</span><br/>';
                    $desc_trimmed = strlen($description) > 250 ? substr($description,0,250)."..." : $description;
                    echo '<p>'. disinfect_var($desc_trimmed) .'</p>';
                    echo '<a class="btn rounded-pill bg-primary text-white" href="'. disinfect_var($link) .'" role="button"><i class="bi bi-book"></i> View Resource</a>';
                    echo '</div></div>';
				}
			}
			else {
				$content = "<h2>We're sorry!</h2>";
				$content .= "<p>Oops, something went wrong. Try to refresh this page.</p><br/>";
				$content .= "<p>Or try again by starting in these pages: </p>";
				$content .= '<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile">Summary Tables</a></p>';
				$content .= '<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/statistics">Rice Statistics</a></p>';
				$content .= '<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/publications">Publications</a></p>';
				$content .= '<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/maps">Maps</a></p>';
				$content .= '<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/related-links">Related Links</a></p>';
			}
		}
	}
	else {
		//if ($res = $conn->prepare("SELECT i.itemID, i.itemTitle, i.itemSubCategory, i.itemURL, it.itemPreviewURL FROM cms_items i LEFT JOIN cms_itemdetails it ON i.itemID = it.itemID WHERE i.itemCategory = 'Publications' ORDER BY i.itemTitle ASC")) {
        if ($res = $conn->prepare("SELECT i.itemID, i.itemTitle, i.itemSubCategory, i.itemURL, it.itemPreviewURL, i.itemDescription FROM cms_items i LEFT JOIN cms_itemdetails it ON i.itemID = it.itemID WHERE i.itemCategory = 'Resources' ORDER BY i.itemTitle ASC")) {  
			$res->execute();
			$res->store_result();
			$count = $res->num_rows;
			//$res->bind_result($ID, $title, $category, $url, $thumbnail);
            $res->bind_result($ID, $title, $category, $url, $thumbnail, $description);
			while($res->fetch()){
				$link = $GLOBALS['htp'] . "://" . $GLOBALS['hname'] . "/" . disinfect_var($url);
                $image = $GLOBALS['htp'] . "://" . $GLOBALS['hname'] . "/" . disinfect_var($thumbnail);
                echo '<div class="media">';
                echo '<div class="media-left">';
                echo '<a href="'. disinfect_var($link) .'">';
                echo '<img class="media-object" src="'. disinfect_var($image) .'" alt="'. disinfect_var($title) .' preview"></a>';
                echo '</div>';
                echo '<div class="media-body">';
                echo '<h3><a href="'. disinfect_var($link) .'">'. disinfect_var($title) .'</a></h3>';
                echo '<span class="badge badge-secondary">'. disinfect_var($category) .'</span><br/>';
                $desc_trimmed = strlen($description) > 250 ? substr($description,0,250)."..." : $description;
                echo '<p>'. disinfect_var($desc_trimmed) .'</p>';
                echo '<a class="btn rounded-pill bg-primary text-white" href="'. disinfect_var($link) .'" role="button"><i class="bi bi-book"></i> View Resource</a>';
                echo '</div></div>';
			}
		}
	}
echo $content;
}
else {
	header("HTTP/1.1 401 Unauthorized");
    header("Location: index.php");
}