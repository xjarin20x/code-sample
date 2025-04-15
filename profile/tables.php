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
			case 'social-profile':
				$content .= '<div class="media col-lg-10 mx-auto">
                                <div class="media-left">
                                    <img class="circular-image-small media-object" src="'.  $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/images/social.png">
                                </div>
                                <div class="media-body">
                                    <h1>Social Profile</h1>
                                    <p>Social, economic and financial characteristics of farmers and farm households</p>
                                </div>
                             </div>
                             </div>
                             <div class="page-body text-center">
							 <div class="item-list">
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/1">Socioeconomic characteristics of farmer-respondents</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/68">Distribution of farm households (%) by annual per capita poverty threshold</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/9">Distribution of farmers (%) by tenurial status</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/6">Estimated monthly per capita income and percentage distribution of income, by source</a></p>
								</div>';
				break;

			case 'farm-profile':
				$content .= '<div class="media col-lg-10 mx-auto">
                                <div class="media-left">
                                    <img class="circular-image-small media-object" src="'.  $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/images/farm.png">
                                </div>
                                <div class="media-body">
                                    <h1>Farm profile</h1>
                                    <p>Information on rice-based farms of farmers about location, cropping schedules, source of water and other related details</p>
                                </div>
                             </div>
                             </div>
                             <div class="page-body text-center">
							 <div class="item-list">
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/10">Accessibility of rice parcel to wholesale markets</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/14">Distribution of farmers (%) by cropping pattern</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/13a">Distribution of farmers (%) by planting schedule</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/13b">Distribution of farmers (%) by harvesting schedule</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/11">Distribution of farmers (%) by source of water</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/8">Farm size, number of rice parcels and farm location</a></p>
							</div>';
				break;

			case 'crop-establishment':
				$content .= '<div class="media col-lg-10 mx-auto">
                                <div class="media-left">
                                    <img class="circular-image-small media-object" src="'.  $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/images/crop.png">
                                </div>
                                <div class="media-body">
                                    <h1>Crop establishment</h1>
                                    <p>Data on crop establishment and crop seedbed preparation method used on rice farming</p>
                                </div>
                             </div>
                             </div>
                             <div class="page-body text-center">
							 <div class="item-list">
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/15">Distribution of farmers (%) by method of crop establishment</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/17">Distribution of farmers (%) by method of seedbed preparation</a></p>
							 </div>';
				break;

			case 'seeds-varieties':
                $content .= '<div class="media col-lg-10 mx-auto">
                                <div class="media-left">
                                    <img class="circular-image-small media-object" src="'.  $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/images/seeds.png">
                                </div>
                                <div class="media-body">
                                    <h1>Seeds and Varieties</h1>
                                    <p>About seeds and varieties planted</p>
                                </div>
                             </div>
                             </div>
                             <div class="page-body text-center">
							 <div class="item-list">
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/21">Distribution of farmers (%) by seed classification</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/22">Distribution of farmers (%) by seed source</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/19">Distribution of farmers (%) by ten (10) most varieties planted</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/62">Seeding rate by cropping season</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/70">Seeding rate by seed classification</a></p>
							 </div>';
				break;

			case 'pest-management':
				$content .= '<div class="media col-lg-10 mx-auto">
                                <div class="media-left">
                                    <img class="circular-image-small media-object" src="'.  $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/images/pest.png">
                                </div>
                                <div class="media-body">
                                    <h1>Pest management</h1>
                                    <p>Frequency of pesticide application and average active ingredients used</p>
                                </div>
                             </div>
                             </div>
                             <div class="page-body text-center">
							 <div class="item-list">
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/64">Average active ingredients (kg/ha) of pesticides used per chemical type, by cropping season</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/28">Distribution of farmers (%) by active ingredients of pesticide applied</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/33f">Distribution of farmers (%) by frequency of all pesticide application</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/33b">Distribution of farmers (%) by frequency of fungicide application</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/30">Distribution of farmers (%) by frequency of herbicide application</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/33a">Distribution of farmers (%) by frequency of insecticide application</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/33c">Distribution of farmers (%) by frequency of molluscicide application</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/33d">Distribution of farmers (%) by frequency of rodenticide application</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/33e">Distribution of farmers (%) by frequency of application of other chemicals</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/24">Number of times of fertilizer and pesticide application</a></p>
							 </div>';
				break;

			case 'nutrient-management':
				$content .= '<div class="media col-lg-10 mx-auto">
                                <div class="media-left">
                                    <img class="circular-image-small media-object" src="'.  $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/images/nutrient.png">
                                </div>
                                <div class="media-body">
                                    <h1>Nutrient management</h1>
                                    <p>Fertilizers applied, nutrient content, and nutrient management</p>
                                </div>
                             </div>
                             </div>
                             <div class="page-body text-center">
							 <div class="item-list">
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/65">Average N-P-K component per fertilizer use, by cropping season</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/27">Distribution of farmers (%) by frequency of fertilizer application</p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/25">Distribution of farmers (%) by type of fertilizer</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/63">Number of times of fertilizer application</a></p>
							 </div>';
				break;

			case 'farm-inputs-costs-returns':
				$content .= '<div class="media col-lg-10 mx-auto">
                                <div class="media-left">
                                    <img class="circular-image-small media-object" src="'.  $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/images/cost.png">
                                </div>
                                <div class="media-body">
                                    <h1>Farm input use, costs and returns</h1>
                                    <p>Information on yield, quantity of inputs, costs and returns of rice production</p>
                                </div>
                             </div>
                             </div>
                             <div class="page-body text-center">
							 <div class="item-list">
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/37">Costs and returns of rice production</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/23">Yield and quantity of inputs used</a></p>
							 </div>';
				break;

			case 'yield':
				$content .= '<div class="media col-lg-10 mx-auto">
                                <div class="media-left">
                                    <img class="circular-image-small media-object" src="'.  $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/images/yield.png">
                                </div>
                                <div class="media-body">
                                    <h1>Yield</h1>
                                    <p>All about yield and yield-related information</p>
                                </div>
                             </div>
                             </div>
                             <div class="page-body text-center">
							 <div class="item-list">
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/61">Yield per ecosystem, by cropping season</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/60">Yield per seed classification, by cropping season</a></p>
							 </div>';
				break;

			case 'credit':
				$content .= '<div class="media col-lg-10 mx-auto">
                                <div class="media-left">
                                    <img class="circular-image-small media-object" src="'.  $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/images/credit.png">
                                </div>
                                <div class="media-body">
                                    <h1>Credit</h1>
                                    <p>Financing and credit information involved in rice farming</p>
                                </div>
                             </div>
                             </div>
                             <div class="page-body text-center">
							 <div class="item-list">
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/41">Distribution of farmers (%) by source of capital for farming</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/42">Information on borrowed capital</a></p>
							 </div>';
				break;

			case 'marketing-practices':
				$content .= '<div class="media col-lg-10 mx-auto">
                                <div class="media-left">
                                    <img class="circular-image-small media-object" src="'.  $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/images/market.png">
                                </div>
                                <div class="media-body">
                                    <h1>Marketing practices</h1>
                                    <p>Information about how rice farmers market and allot harvest for sale</p>
                                </div>
                             </div>
                             </div>
                             <div class="page-body text-center">
							 <div class="item-list">
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/39b">Distribution of farmers (%) by marketing practices</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/38">Distribution of production and disposition of harvest (%) by cropping season</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/39a">Percent of farmers with harvest allotted for sale</a></p>
							 </div>';
				break;

			case 'labor-mechanization':
				$content .= '<div class="media col-lg-10 mx-auto">
                                <div class="media-left">
                                    <img class="circular-image-small media-object" src="'.  $GLOBALS['htp'] . '://' . $GLOBALS['hname'] . '/images/labor.png">
                                </div>
                                <div class="media-body">
                                    <h1>Labor and mechanization</h1>
                                    <p>Labor needs and labor costs</p>
                                </div>
                             </div>
                             </div>
                             <div class="page-body text-center">
							 <div class="item-list">
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/67">Labor costs of rice production by cropping season</a></p>
								<p><a href="'. $GLOBALS['htp'] .'://' . $GLOBALS['hname'] . '/profile/retrieve/table/66">Labor requirements (person-days/ha) by cropping season</a></p>
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