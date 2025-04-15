<?php
require_once("../includes/header.php");
$prepath = checksubdomain(dirname(__FILE__), $conn, $purifier);
// PHP 5.3
//$prepath = checksubdomain(__DIR__, $conn, $purifier);
?>
<div id="profile" class="page col-lg-8 mx-auto">
    <div class="page-header">
        <h1>PSA Datasets</h1>
        <p>Philippine Statistics Authority's yearly statistics on rice farming and rice industry</p>
    </div>
    <div class="page-body">
        <div class="card-deck">
            <div class="card rounded">
                <div class="card-header">
                    <img class="card-img-top" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/yield.png" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><a category="supply-demand" href="#">Palay/Rice Supply and Demand</a></h5>
                    <p class="card-text">Explores the trends of rice supply and utilization in the Philippines, its regions and provinces</p>
                </div>
            </div>
            <div class="card rounded">
                <div class="card-header">
                    <img class="card-img-top" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/cost.png" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><a category="input-use" href="#">Input-use and Production Costs, Returns and Losses</a></h5>
                    <p class="card-text">Highlights average use of selected inputs, and average production costs and returns in rice production</p>
                </div>
            </div>
            <div class="card rounded">
                <div class="card-header">
                    <img class="card-img-top" src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/market.png" alt="Card image cap">
                </div>
                <div class="card-body">
                    <h5 class="card-title"><a category="rice-marketing" href="#">Palay/Rice Marketing</a></h5>
                    <p class="card-text">Provides information on farmgate, wholesale and retail prices of ordinary and special rice</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
require_once("../includes/footer.php");
?>