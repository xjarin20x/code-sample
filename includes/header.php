<?php
require_once("conn.php");
require_once("global_functions.php");
require_once("HTMLPurifier.standalone.php");
header('Content-Type:text/html; charset=UTF-8');
$config = HTMLPurifier_Config::createDefault();
$config->set('HTML.SafeIframe', true);
$config->set('URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/)%'); //allow YouTube and Vimeo
$def = $config->getHTMLDefinition(true);
$def->addAttribute('a', 'data-toggle', 'CDATA');
$def->addAttribute('a', 'data-target', 'CDATA');
$def->addAttribute('a', 'aria-expanded', 'CDATA');
$def->addAttribute('a', 'aria-controls', 'CDATA');
$def->addAttribute('div', 'aria-labelledby', 'CDATA');
$def->addAttribute('div', 'data-bs-parent', 'CDATA');
$def->addAttribute('div', 'id', 'CDATA');
$purifier = new HTMLPurifier($config);
function checksubdomain($filepath, $conn, $puri){
//	Check domain names
$GLOBALS['hname'] = $_SERVER['HTTP_HOST'];
//$GLOBALS['hname'] = $_SERVER['HTTP_HOST']."/"."palaystat";
$GLOBALS['hname'] = disinfect_var($GLOBALS['hname']); 
$GLOBALS['hname'] = $puri->purify($GLOBALS['hname']);
$filepath = str_replace('\\', '/',$filepath);
$prepath = "";
$script = substr($_SERVER['SCRIPT_NAME'], 1);
$path = explode('/', $script);
$basename = substr(strtolower(basename($_SERVER['PHP_SELF'])),0,strlen(basename($_SERVER['PHP_SELF']))-4);
//	Set timezone
date_default_timezone_set('Asia/Manila');
//	Metatags
//$itemFilename = substr($_SERVER['REQUEST_URI'], strlen($path[0])+2);
$itemFilename = substr($_SERVER['REQUEST_URI'], 1);	
//$itemFilename = strtok($itemFilename, '?'); // Remove strtok in live
$itemFilename = disinfect_var($itemFilename);    
$itemFilename = $puri->purify($itemFilename);
$GLOBALS['base_URL'] = $itemFilename;
if ($meta = $conn->prepare("SELECT itemDescription, metaTitle, metaRobots FROM cms_items WHERE itemURL = ?")) {
	$meta->bind_param("s", $itemFilename);
	$meta->execute();
	$meta->store_result();
	$meta->bind_result($a, $b, $c);
}
$GLOBALS['tag'] = array();
$GLOBALS['tag']['title'] = "PalayStat System | Socioeconomics Division";
$GLOBALS['tag']['description'] = "PalayStat system is an interactive web-based information system that caters to rice researchers and policy makers in need of accessible rice-related information in the Philippines.";
$GLOBALS['tag']['robots'] = "index, follow";
while ($meta->fetch()) {
	$GLOBALS['tag'] = array('description' => $puri->purify($a),
	'title' => $puri->purify($b),
	'robots' => $puri->purify($c));
}
if(empty($GLOBALS['tag']['image'])){
    $GLOBALS['tag']['image'] = $GLOBALS['htp']."://". $GLOBALS['hname'] ."/images/palaystat_social_media.jpg";
}
?>
<!DOCTYPE html>
<html xmlns="https://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-23Z5YCWD6T"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-23Z5YCWD6T');
    </script>
    <title><?php echo $GLOBALS['tag']['title']; ?></title>
    <?php
echo '<meta charset="utf-8">' . "\n";
if(!empty($GLOBALS['tag']['title'])){
echo '<meta name="title" content="' . $GLOBALS['tag']['title'] . '">' . "\n";
}
if(!empty($GLOBALS['tag']['description'])){
echo '<meta name="description" content="' . $GLOBALS['tag']['description'] . '">' . "\n";
}
if(!empty($GLOBALS['tag']['robots'])){
echo '<meta name="robots" content="' . $GLOBALS['tag']['robots'] . '">' . "\n";
}
?>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <?php
if(!empty($GLOBALS['tag']['image'])){
echo '<meta name="image" content="' . $GLOBALS['tag']['image'] . '">' . "\n";
}
if(!empty($GLOBALS['tag']['title'])){
echo '<meta itemprop="name" content="' . $GLOBALS['tag']['title'] . '">' . "\n";
}
if(!empty($GLOBALS['tag']['description'])){
echo '<meta itemprop="description" content="' . $GLOBALS['tag']['description'] . '">' . "\n";
}
?>
    <!-- Open Graph protocol -->
    <?php
if(!empty($GLOBALS['tag']['title'])){
echo '<meta property="og:type" content="website">' . "\n";
echo '<meta property="og:title" content="' . $GLOBALS['tag']['title'] . '">' . "\n";
}
if(!empty($GLOBALS['tag']['description'])){
echo '<meta property="og:description" content="' . $GLOBALS['tag']['description'] . '">' . "\n";
}
if(!empty($GLOBALS['tag']['image'])){
echo '<meta property="og:image" content="' . $GLOBALS['tag']['image'] . '">' . "\n";
}
?>
    <!-- Twitter card -->
    <meta property="twitter:card" content="summary_large_image">
    <?php
if(!empty($GLOBALS['tag']['title'])){
echo '<meta property="twitter:title" content="' . $GLOBALS['tag']['title'] . '">' . "\n";
}
if(!empty($GLOBALS['tag']['description'])){
echo '<meta property="twitter:description" content="' . $GLOBALS['tag']['description'] . '">' . "\n";
}
if(!empty($GLOBALS['tag']['image'])){
echo '<meta property="twitter:image" content="' . $GLOBALS['tag']['image'] . '">' . "\n";
}
?>
    <?php
?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@graph": [{
                "@type": "Organization",
                "@id": "<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/contact",
                "name": "PhilRice - Socioeconomics Division",
                "url": "<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>"
            }, {
                "@type": "WebSite",
                "@id": "<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>",
                "url": "<?php echo $GLOBALS['htp'];?>:/<?php echo $GLOBALS['hname'];?>",
                "name": "PalayStat System",
                "publisher": {
                    "@id": "<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/contact"
                },
                "potentialAction": {
                    "@type": "SearchAction",
                    "target": "<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/search.php?s={search_term_string}",
                    "query-input": "required name=search_term_string"
                }
            }, {
                "@type": "WebPage",
                "@id": "<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'] . "/" . $itemFilename;?>",
                "url": "<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'] . "/" . $itemFilename;?>",
                "inLanguage": "en-US",
                "name": "<?php echo $GLOBALS['tag']['title']; ?>",
                "isPartOf": {
                    "@id": "<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname']?>"
                },
                "description": "<?php echo substr($GLOBALS['tag']['description'], 0, 320); ?>"
            }]
        }

    </script>
    <link rel="stylesheet" type="text/css" href="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/css/css.php?<?php echo time(); ?>" media="all" />
    <link href="https://fonts.googleapis.com/css2?family=Barlow:wght@300;400;500;700&family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/palaystat-logo.png?v=1" />
    <script src="https://cdn.jsdelivr.net/npm/plotly.js-dist@2.5.1/plotly.min.js"></script>
</head>

<body>
<?php 
//if ($itemFilename == "palaystat/") {
if (empty($itemFilename)) {
?>
    <nav id="primary" class="navbar navbar-expand-lg fixed-top navbar-light bg-white">
        <div class="mx-auto d-sm-flex d-block flex-sm-nowrap">
            <a class="navbar-brand" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>"><img src="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/images/palaystat-logo.png" alt="" height="30" class="d-inline-block align-text-top"> PalayStat</a>
            <button class="navbar-toggler p-0 border-0" type="button" id="palayStatMainMenu" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="navbar-collapse offcanvas-collapse bg-white" id="palayStatMainMenu-nav">
                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-uppercase" href="#" id="aboutus" data-toggle="dropdown">About Us</a>
                        <ul class="dropdown-menu" aria-labelledby="aboutus">
                            <li><a class="dropdown-item" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/rationale">Rationale</a></li>
                            <li><a class="dropdown-item" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/history">History</a></li>
                            <li><a class="dropdown-item" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/sampling-method">Sampling Method</a></li>
                            <li><a class="dropdown-item" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/related-links">Related Links</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-uppercase" href="#" id="quick-facts" data-toggle="dropdown">Provincial Overview</a>
                        <ul class="dropdown-menu" aria-labelledby="provincial-overview">
                            <li>
                                <p class="description">Provincial overview on its rice farming and rice industry situation</p>
                            </li>
                            <li><a class="dropdown-item text-primary" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/quick-facts">Go to graphs</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-uppercase" href="#" id="survey-results" data-toggle="dropdown">RBFHS Datasets</a>
                        <ul class="dropdown-menu" aria-labelledby="provincial-overview">
                            <li>
                                <p class="description">Results from our nationwide survey of rice farm households conducted every 5 years</p>
                            </li>
                            <li><a class="dropdown-item text-primary" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/profile">Go to tables</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-uppercase" href="#" id="statistics" data-toggle="dropdown">PSA Datasets</a>
                        <ul class="dropdown-menu" aria-labelledby="provincial-overview">
                            <li>
                                <p class="description">Philippine Statistics Authority's yearly statistics on rice farming and rice industry</p>
                            </li>
                            <li><a class="dropdown-item text-primary" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/statistics">Go to tables</a></li>
                        </ul>
                    </li>
                    <li class="nav-item mr-1">
                        <a class="nav-link text-uppercase" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/resources">Resources</a>
                    </li>
                    <li class="nav-item mr-1">
                        <a class="nav-link text-uppercase" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/contact">Contact Us</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div id="main">
        <div id="header-search" class="text-center">
            <div class="container">
                <h1>One website for your rice data needs</h1>
                <p class="text-white">
                    <a href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/quick-facts" class="link-light">Provincial Overview</a><i class="bi bi-dot"></i>
                    <a href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/profile" class="link-light">RBFHS Datasets</a><i class="bi bi-dot"></i>
                    <a href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/statistics" class="link-light">PSA Datasets</a><i class="bi bi-dot"></i>
                    <a href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/resources" class="link-light">Resources</a>
                </p>
                <form id="search" method="get" action="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/search.php">
                    <div class="search-group input-group text-light col-8">
                        <input type="text" class="form-control" placeholder="Search data" id="s" name="s" type="search">
                        <div class="input-group-append">
                            <button class="btn btn-warning text-white"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                </form>
                <h6 class="text-uppercase">Popular Topics</h6>
                <a class="btn tag-topics rounded-pill" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/statistics/retrieve/table/1" role="button">Production</a>
                <a class="btn tag-topics rounded-pill" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/statistics/retrieve/table/14" role="button">Special Rice Prices</a>
                <a class="btn tag-topics rounded-pill" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/statistics/retrieve/table/15" role="button">Ordinary Rice Prices</a>
                <a class="btn tag-topics rounded-pill" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/statistics/retrieve/table/16" role="button">Imports</a>
                <a class="btn tag-topics rounded-pill" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/profile/retrieve/table/37" role="button">Cost and Returns</a>
                <a class="btn tag-topics rounded-pill" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/profile/retrieve/table/1" role="button">Farmer Profile</a>
            </div>
        </div>
        <?php
}
else { ?>
    <nav id="primary-alt" class="navbar navbar-expand-lg navbar-light bg-white">
        <div class="mx-auto col-9 d-sm-flex flex-sm-nowrap justify-content-between">
            <a class="navbar-brand" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>"><img src="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/images/palaystat-logo.png" alt="" height="30" class="d-inline-block align-text-top"> PalayStat</a>
            <form id="search" class="form-inline" method="get" action="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/search.php">
                <div class="search-group input-group text-light">
                    <input type="text" class="form-control" placeholder="Search data" id="s" name="s" type="search">
                    <div class="input-group-append">
                        <button class="btn btn-warning text-white"><i class="bi bi-search"></i></button>
                    </div>
                </div>
            </form>    
        </div>
    </nav>
    <nav id="secondary" class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="mx-auto col-9 d-sm-flex flex-sm-nowrap justify-content-between">
            <button class="navbar-toggler p-0 border-0" type="button" id="palayStatSecMenu" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
             <div class="collapse navbar-collapse" id="palayStatSecMenu-nav">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item">
                        <a class="nav-link text-uppercase" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-uppercase" href="#" id="aboutus" data-toggle="dropdown">About Us</a>
                        <ul class="dropdown-menu" aria-labelledby="aboutus">
                            <li><a class="dropdown-item" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/rationale">Rationale</a></li>
                            <li><a class="dropdown-item" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/history">History</a></li>
                            <li><a class="dropdown-item" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/sampling-method">Sampling Method</a></li>
                            <li><a class="dropdown-item" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/related-links">Related Links</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-uppercase" href="#" id="quick-facts" data-toggle="dropdown">Provincial Overview</a>
                        <ul class="dropdown-menu" aria-labelledby="provincial-overview">
                            <li>
                                <p class="description">Provincial overview on its rice farming and rice industry situation</p>
                            </li>
                            <li><a class="dropdown-item text-primary" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/quick-facts">Go to graphs</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-uppercase" href="#" id="survey-results" data-toggle="dropdown">RBFHS Datasets</a>
                        <ul class="dropdown-menu" aria-labelledby="provincial-overview">
                            <li>
                                <p class="description">Results from our nationwide survey of rice farm households conducted every 5 years</p>
                            </li>
                            <li><a class="dropdown-item text-primary" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/profile">Go to tables</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-uppercase" href="#" id="statistics" data-toggle="dropdown">PSA Datasets</a>
                        <ul class="dropdown-menu" aria-labelledby="provincial-overview">
                            <li>
                                <p class="description">Philippine Statistics Authority's yearly statistics on rice farming and rice industry</p>
                            </li>
                            <li><a class="dropdown-item text-primary" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/statistics">Go to tables</a></li>
                        </ul>
                    </li>
                    <li class="nav-item mr-1">
                        <a class="nav-link text-uppercase" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/resources">Resources</a>
                    </li>
                    <li class="nav-item mr-1">
                        <a class="nav-link text-uppercase" href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/contact">Contact Us</a>
                    </li>
                </ul>
            </div>
        </div>    
    </nav>
    <nav id="directory" class="mx-auto col-9" aria-label="breadcrumb">
      <?php 
        $breadcrumb = "";
        //echo $itemFilename;
        switch ($itemFilename) {
            case 'rationale': $breadcrumb = "Rationale"; break;
            case 'history': $breadcrumb = "History"; break;
            case 'sampling-method': $breadcrumb = "Sampling Method"; break;
            case 'related-links': $breadcrumb = "Related Links"; break;
            case 'quick-facts/': $breadcrumb = "Provincial Overview"; break;
            case 'profile/': $breadcrumb = "RBFHS Datasets"; break;
            case 'statistics/': $breadcrumb = "PSA Datasets"; break;
            case 'resources/': $breadcrumb = "Resources"; break;
            case 'contact': $breadcrumb = "Contact Us"; break;
            default: break;
        }  
        if(!empty($breadcrumb)) {
      ?>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page"><a href="<?php echo $GLOBALS['htp'] . "://". $GLOBALS['hname']; ?>/<?php echo $itemFilename;?>"><?php echo $breadcrumb?></a></li>
      </ol>
  <?php } ?>
    </nav>
<?php }
return $prepath;
}
