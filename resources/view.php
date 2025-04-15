<?php
require_once("../includes/header.php");
$prepath = checksubdomain(dirname(__FILE__), $conn, $purifier);

if(!empty($_GET['file'])) {
	$file = $_GET['file'];
?>
<div class="page resource-view col-lg-8 mx-auto">
    <?php
    if ($result = $conn->prepare("SELECT i.itemID, i.itemTitle, it.itemAuthor, i.itemSubCategory, it.itemPreviewURL, i.itemDescription, it.itemPubdate, it.itemISBN, it.itemNumPages, it.itemFileURL FROM cms_items i LEFT JOIN cms_itemdetails it ON i.itemID = it.itemID WHERE i.itemCategory = 'Resources' AND i.itemID = ? ORDER BY i.itemTitle ASC")) {
        $result->bind_param("s", $file);
        $result->execute();
        $res = $result->get_result(); 
        $row = mysqli_fetch_assoc($res);
        $row_cnt = mysqli_num_rows($res);
        if($row_cnt != 0){
            if($res) {
                $link = $GLOBALS['htp'] . "://" . $GLOBALS['hname'] . "/" .  disinfect_var($row["itemFileURL"]);
                $image = $GLOBALS['htp'] . "://" . $GLOBALS['hname'] . "/" .  disinfect_var($row["itemPreviewURL"]);
                $explodeAuthors = explode(", ", disinfect_var($row["itemAuthor"]));
            }
            ?>
            <div class="page-header">
                <h1><?php echo disinfect_var($row["itemTitle"]); ?></h1>
                <p><strong>Authors: </strong><?php echo concatenate($explodeAuthors); ?></p>
                <span class="badge badge-secondary"><?php echo disinfect_var($row["itemSubCategory"]); ?></span>
                <div class="viewer-photo">
                    <img class="media-object" src="<?php echo $image; ?>" alt="preview">
                </div>
            </div>
            <div class="page-body">
                <h3>Description</h3>
                <div class="viewer-description fr-view">
                    <p><?php echo disinfect_var($row["itemDescription"]); ?></p>
                </div>	
                <div class="viewer-details">
                    <h3>Details</h3>
                    <ul>
                        <li>
                            <strong>Year Published: </strong><?php echo disinfect_var($row["itemPubdate"]); ?>

                        </li>
                        <?php
                        if(empty($row["itemISBN"])) { $row["itemISBN"] = "N/A"; }
                        ?>
                        <li>
                            <strong>ISBN: </strong><?php echo disinfect_var($row["itemISBN"]); ?>

                        </li>
                        <li>
                            <strong>Pages: </strong><?php echo disinfect_var($row["itemNumPages"]); ?>

                        </li>
                    </ul>
                </div>
                <div class="viewer-links">
                    <a id="view-ref" class="btn btn-primary rounded-pill" target="_blank" rel="noopener noreferrer" href="<?php echo $link; ?>" role="button"><i class="bi bi-book"></i> Read</a>
                    <a id="dl-ref" class="btn btn-primary rounded-pill" download="<?php echo basename($link); ?>" target="_blank" rel="noopener noreferrer" href="<?php echo $link; ?>" role="button"><i class="bi bi-download"></i> Download</i></a>
                </div>
            </div>

    <?php
        }
        else {
         echo '<div class="page-header">
                              <h1 class="text-center">Page Not Found</h1>
		                      <p class="text-center">Try using search to find what you are looking for.</p>
                            </div>
                            <div class="page-body">
                                <p class="extend">The page you requested cannot be found.</p><br/>
                            </div>';
        }
    }
    ?>
</div>
<?php
}
else {
	header("HTTP/1.1 401 Unauthorized");
    header("Location: index.php");
}
require_once("../includes/footer.php");
?>