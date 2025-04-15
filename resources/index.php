<?php
require_once("../includes/header.php");
$prepath = checksubdomain(dirname(__FILE__), $conn, $purifier);
?>
<div class="page resources col-lg-8 mx-auto">
    <div class="row">
        <div class="col-lg-3">
            <h1>Resources</h1>
            <nav class="resource-nav nav flex-column">
                <?php
                    if ($ctgrs = $conn->prepare("SELECT DISTINCT itemSubCategory FROM cms_items WHERE itemCategory = 'Resources' ORDER BY itemSubCategory ASC")) {
                        $ctgrs->execute();
                        $ctgrs->store_result();
                        $count = $ctgrs->num_rows;
                        $ctgrs->bind_result($category);
                        while($ctgrs->fetch()){
                            echo '<a class="nav-link" href="#'. strtolower($category) .'" publication="'. $category .'">';
                            echo $category;
                            echo '</a>';
                        }
                    }
                ?>
                <a class="nav-link active" publication="" href="#">All</a>
            </nav>
        </div>
        <div id="resources-list" class="col-lg-9">
            <?php
            //if ($res = $conn->prepare("SELECT i.itemID, i.itemTitle, i.itemSubCategory, i.itemURL, it.itemPreviewURL FROM cms_items i LEFT JOIN cms_itemdetails it ON i.itemID = it.itemID WHERE i.itemCategory = 'Publications' ORDER BY i.itemTitle ASC")) {
            if ($res = $conn->prepare("SELECT i.itemID, i.itemTitle, i.itemSubCategory, i.itemURL, it.itemPreviewURL, i.itemDescription FROM cms_items i LEFT JOIN cms_itemdetails it ON i.itemID = it.itemID WHERE i.itemCategory = 'Resources' ORDER BY i.itemTitle ASC")) {  
                $res->execute();
                $res->store_result();
                $count = $res->num_rows;
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
            ?>
        </div>
    </div>
</div>
<?php
require_once("../includes/footer.php");
?>