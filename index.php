<?php
require_once("includes/header.php");
$prepath = checksubdomain(dirname(__FILE__), $conn, $purifier);

if(empty($_GET) || empty($_GET["slug"])){
    $stmt = $conn->prepare("SELECT content FROM cms_contents WHERE cms_contents.contentID = 0 LIMIT 1");
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($content);
    $stmt->store_result();
    $stmt->fetch();
    $home = $purifier->purify($content);
    $temp = '';
    $temp = $temp . $home;
}
else{
	$slug = disinfect_var($_GET["slug"]);
	$slug = $purifier->purify($slug);
	$temp = "";
	if(!empty($slug)){
		$stmt = $conn->prepare("SELECT content FROM cms_contents WHERE cms_contents.navSlug = ? LIMIT 1");
		$stmt->bind_param("s", $slug);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($content);
		$stmt->store_result();
		$stmt->fetch();
		$temp = $purifier->purify($content);
        if ($stmt->num_rows == 0){
			$temp = '	<div class="page col-lg-8 mx-auto">
	                       <div class="page-header">
                              <h1>Page Not Found</h1>
		                      <p>Try using search to find what you are looking for.</p>
                            </div>
                            <div class="page-body">
                                <p class="extend">The page you requested cannot be found.</p><br/>
                            </div>
                        </div>';
			}
		}
		else {
			$temp = '	<div class="page col-lg-8 mx-auto">
	                       <div class="page-header">
                              <h1>Page Not Found</h1>
		                      <p>Try using search to find what you are looking for.</p>
                            </div>
                            <div class="page-body">
                                <p class="extend">The page you requested cannot be found.</p><br/>
                            </div>
                        </div>';
		}
	}
?>
<?php echo $temp;

require_once("includes/footer.php");
?>