<?php
require_once("../includes/header.php");
$prepath = checksubdomain(dirname(__FILE__), $conn, $purifier);
// PHP 5.3
//$prepath = checksubdomain(__DIR__, $conn, $purifier);
?>
<div id="profile" class="page col-lg-8 mx-auto">
    <div class="page-header">
        <h1>RBFHS Datasets</h1>
        <p>Results from our nationwide survey of rice farm households conducted every 5 years since 1996</p>
    </div>
    <div class="page-body">
        <div class="card-deck">
            <div class="card rounded">
                <div class="card-header">
                    <img class="card-img-top" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/social.png" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><a category="social-profile" href="#">Social Profile</a></h5>
                    <p class="card-text">Social, economic and financial characteristics of farmers and farm households</p>
                </div>
            </div>
            <div class="card rounded">
                <div class="card-header">
                    <img class="card-img-top" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/farm.png" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><a category="farm-profile" href="#">Farm profile</a></h5>
                    <p class="card-text">Information on rice-based farms of farmers about location, cropping schedules, source of water and other related details</p>
                </div>
            </div>
            <div class="card rounded">
                <div class="card-header">
                    <img class="card-img-top" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/crop.png" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><a category="crop-establishment" href="#">Crop establishment</a></h5>
                    <p class="card-text">Data on crop establishment and crop seedbed preparation method used on rice farming</p>
                </div>
            </div>
        </div>
        <div class="card-deck">
            <div class="card rounded">
                <div class="card-header">
                    <img class="card-img-top" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/seeds.png" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><a category="seeds-varieties" href="#">Seeds and varieties</a></h5>
                    <p class="card-text">About seeds and varieties planted</p>
                </div>
            </div>
            <div class="card rounded">
                <div class="card-header">
                    <img class="card-img-top" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/pest.png" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><a category="pest-management" href="#">Pest management</a></h5>
                    <p class="card-text">Frequency of pesticide application and average active ingredients used</p>
                </div>
            </div>
            <div class="card rounded">
                <div class="card-header">
                    <img class="card-img-top" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/nutrient.png" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><a category="nutrient-management" href="#">Nutrient management</a></h5>
                    <p class="card-text">Fertilizers applied, nutrient content, and nutrient management</p>
                </div>
            </div>
        </div>
        <div class="card-deck">
            <div class="card rounded">
                <div class="card-header">
                    <img class="card-img-top" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/cost.png" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><a category="farm-inputs-costs-returns" href="#">Farm inputs, costs and returns</a></h5>
                    <p class="card-text">Information on yield, quantity of inputs, costs and returns of rice production</p>
                </div>
            </div>
            <div class="card rounded">
                <div class="card-header">
                    <img class="card-img-top" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/yield.png" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><a category="yield" href="#">Yield</a></h5>
                    <p class="card-text">All about yield and yield-related information</p>
                </div>
            </div>
            <div class="card rounded">
                <div class="card-header">
                    <img class="card-img-top" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/credit.png" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><a category="credit" href="#">Credit</a></h5>
                    <p class="card-text">Financing and credit information involved in rice farming</p>
                </div>
            </div>
        </div>
        <div class="card-deck col-lg-9 mx-auto">
            <div class="card rounded">
                <div class="card-header">
                    <img class="card-img-top" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/market.png" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><a category="marketing-practices" href="#">Marketing practices</a></h5>
                    <p class="card-text">Information about how rice farmers market and allot harvest for sale</p>
                </div>
            </div>
            <div class="card rounded">
                <div class="card-header">
                    <img class="card-img-top" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/mech.png" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><a category="labor-mechanization" href="#">Labor and mechanization</a></h5>
                    <p class="card-text">Labor needs and labor costs</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once("../includes/footer.php");
?>
