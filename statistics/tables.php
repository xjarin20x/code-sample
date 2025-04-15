<?php
require_once("../includes/conn.php");
require_once("../includes/global_functions.php");
require_once("../includes/HTMLPurifier.standalone.php");

//$GLOBALS['hname'] = $_SERVER['HTTP_HOST'];
$GLOBALS['hname'] = $_SERVER['HTTP_HOST']."/"."palaystat";

if( (isset($_POST) && !empty($_POST)) || (isset($_GET) && !empty($_GET))){
	$content = "";
	if(isset($_POST['category'])){
		$category = disinfect_var($_POST['category']);
		$content .= '<div class="page-header">';
		switch ($category) {
			case 'supply-demand':
				$content .= '<div class="media col-lg-10 mx-auto">
                                <div class="media-left">
                                    <img class="circular-image-small media-object" src="'.  $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/images/yield.png">
                                </div>
                                <div class="media-body">
                                    <h1>Palay/Rice Supply and Demand</h1>
                                    <p class="card-text">Explores the trends of rice supply and utilization in the Philippines, its regions and provinces</p>
                                </div>
                             </div>
                             </div>
                             <div class="page-body text-center">
							 <div class="item-list">
                                <p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/statistics/retrieve/table/1">Estimated production, area harvested and yield per hectare (PAY)</a></p>
                                <p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/statistics/retrieve/table/2">Monthly relative distribution (%) of palay production</a></p>
                                <p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/statistics/retrieve/table/3">Monthly relative distribution (%) of area harvested</a></p>
                                <p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/statistics/retrieve/table/4">Relative distribution (%) of palay production utilization and disposition of farm households</a></p>
                                <p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/statistics/retrieve/table/5">Production and use estimates</a></p>
                                <p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/statistics/retrieve/table/16">Quantity and value of imports, by country of origin</a></p>
                                <p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/statistics/retrieve/table/17">Quantity and value of exports, by country of destination</a></p>';
				break;

			case 'input-use':
				$content .= '<div class="media col-lg-10 mx-auto">
                                <div class="media-left">
                                    <img class="circular-image-small media-object" src="'.  $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/images/cost.png">
                                </div>
                                <div class="media-body">
                                    <h1>Input-use and Production Costs, Returns and Losses</h1>
                                    <p class="card-text">Highlights average use of selected inputs, and average production costs and returns in rice production</p>
                                </div>
                             </div>
                             </div>
                             <div class="page-body text-center">
							 <div class="item-list">
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/statistics/retrieve/table/6">Estimated physical area, effective area and cropping intensity</a></p>
                                <p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/statistics/retrieve/table/7">Relative distribution (%) of farms reporting - by type of crop establishment, by semester</a></p>
                                <p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/statistics/retrieve/table/8">Relative distribution (%) of farms reporting - by seed class usage, by semester</a></p>
                                <p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/statistics/retrieve/table/9">Estimated per hectare seed use - by seed class and type of crop establishment</a></p>
                                <p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/statistics/retrieve/table/13">Estimated production losses, value, area affected and damaged - by cause</a></p>
							</div>';
				break;

			case 'rice-marketing':
				$content .= '<div class="media col-lg-10 mx-auto">
                                <div class="media-left">
                                    <img class="circular-image-small media-object" src="'.  $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/images/market.png">
                                </div>
                                <div class="media-body">
                                    <h1>Palay/Rice Marketing</h1>
                                    <p class="card-text">Provides information on farmgate, wholesale and retail prices of ordinary and special rice</p>
                                </div>
                             </div>
                             </div>
                             <div class="page-body text-center">
							 <div class="item-list">
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/statistics/retrieve/table/14">Annual and semestral average farmgate, wholesale and retail prices, special palay/rice</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/statistics/retrieve/table/15">Annual and semestral average farmgate, wholesale and retail prices, ordinary palay/rice</a></p>
							 </div>';
				break;

			default:
				break;
		}
		$content .= '</div></div>';
	}
	else {
			header("HTTP/1.1 401 Unauthorized");
		    header("Location: index.php");
	}
	echo $content;
}
else {
	header("HTTP/1.1 401 Unauthorized");
    header("Location: index.php");
}
?>