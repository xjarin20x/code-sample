<?php
require_once("../includes/header.php");
$prepath = checksubdomain(dirname(__FILE__), $conn, $purifier);
// PHP 5.3
//$prepath = checksubdomain(__DIR__, $conn, $purifier);
?>
<div class="prov-overview">
    <?php
    if(isset($_GET['province'])) {
        $provnum = disinfect_var($_GET['province']);
        if ($stmt = $conn->prepare("SELECT province_name, province_desc, seal_url FROM cms_province WHERE province_id = ?")) {
            $stmt->bind_param("s", $provnum);
            $stmt->execute();
            $stmt->execute();
            $result = $stmt->get_result(); 
            $row = mysqli_fetch_assoc($result);
        }
    }
    if($result->num_rows > 0) {
        $province = disinfect_var($row["province_name"]);
        $description = disinfect_var($row["province_desc"]);
        $provimage = disinfect_var($row["seal_url"]);
    ?>
    <div class="page-header col-lg-8 mx-auto">
        <div class="media">
            <div class="media-left">
                <img src="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/images/<?php echo $provimage;?>" />
            </div>
            <div class="media-body">
                <h1><?php echo $province;?></h1>
                <p><?php echo $description;?></p>
            </div>
        </div>
    </div>
    <div class="page-body graph-gallery">
        <div class="row col-lg-10 mx-auto">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h5 class="my-0 p-2"><a href="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/statistics/retrieve/table/1">Total palay produced in <?php echo $province;?> (2000-2023)</a></h5>
                        <p class="my-0 font-weight-normal text-muted">in dry palay, by metric tons, yearly</p>
                    </div>
                    <div class="card-body">
                        <div id="pprod" class="quick-graph"></div>
                    </div>
                    <div class="card-footer text-muted">
                        <p class="font-italic">Source: Estimated production, area harvested and yield per hectare (PAY), 2000-2023, PalayStat System</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h5 class="my-0 p-2"><a href="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/statistics/retrieve/table/1">Total rice area harvested in <?php echo $province;?> (2000-2023)</a></h5>
                        <p class="my-0 font-weight-normal text-muted">in hectares, yearly</p>
                    </div>
                    <div class="card-body">
                        <div id="ahar" class="quick-graph"></div>
                    </div>
                    <div class="card-footer text-muted">
                        <p class="font-italic">Source: Estimated production, area harvested and yield per hectare (PAY), 2000-2023, PalayStat System</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row col-lg-10 mx-auto">
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h5 class="my-0 p-2"><a href="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/statistics/retrieve/table/1">Average yield of palay per hectare in <?php echo $province;?> (2000-2023)</a></h5>
                        <p class="my-0 font-weight-normal text-muted">in dry palay, in metric tons per hectare, yearly</p>
                    </div>
                    <div class="card-body">
                        <div id="ryield" class="quick-graph"></div>
                    </div>
                    <div class="card-footer text-muted">
                        <p class="font-italic">Source: Estimated production, area harvested and yield per hectare (PAY), 2000-2023, PalayStat System</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="card">
                    <div class="card-header text-center">
                        <h5 class="my-0 p-2"><a href="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/statistics/retrieve/table/5">Local production and utilization in <?php echo $province;?> (1996-2016)</a></h5>
                        <p class="my-0 font-weight-normal text-muted">in milled rice, by metric tons, yearly</p>
                    </div>
                    <div class="card-body">
                        <div id="use_est" class="quick-graph"></div>
                    </div>
                    <div class="card-footer text-muted">
                        <p class="font-italic">Source: Production and use estimates, 1996-2016, PalayStat System</p>
                    </div>
                </div>
            </div>
        </div>
        <!--
        <div class="row col-lg-10 mx-auto">
            <div class="col-sm-8 mx-auto">
                <div class="card">
                    <div class="card-header text-center">
                        <h5 class="my-0 p-2"><a href="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/statistics/retrieve/table/15">Palay and rice prices in <?php echo $province;?> (2000-2019)</a></h5>
                        <p class="my-0 font-weight-normal text-muted">in Philippine Peso (PhP), yearly</p>
                    </div>
                    <div class="card-body">
                        <div id="ordrice" class="quick-graph"></div>
                    </div>
                    <div class="card-footer text-muted">
                        <p class="font-italic">Source: Annual and semestral average farmgate, wholesale and retail prices, ordinary palay/rice, 2000-2020, PalayStat System</p>
                    </div>
                </div>
            </div>
        </div>
        -->
    </div>
    <?php 
    $eco = 2;
    $pprod = $ahar = $ryield = $usest = $oprice = array();
    if ($stmt = $conn->prepare("SELECT year AS period, SUM(production) AS produce FROM stat_production WHERE ecosystem = ? AND location_code = ? AND location_type = 2  AND year >= 2000 GROUP BY year")) {
        $stmt->bind_param("ss", $eco, $provnum);
        $stmt->execute();
        $stmt->store_result();
        while($row = fetch_get_result_alt($stmt)){
            $pprod[] = $row;
        }
    }
    if ($stmt = $conn->prepare("SELECT year AS period, SUM(area) AS avgha FROM stat_production WHERE ecosystem = ? AND location_code = ? AND location_type = 2 AND year >= 2000 GROUP BY year")) {
        $stmt->bind_param("ss", $eco, $provnum);
        $stmt->execute();
        $stmt->store_result();
        while($row = fetch_get_result_alt($stmt)){
            $ahar[] = $row;
        }
    }
    if ($stmt = $conn->prepare("SELECT year AS period, AVG(yield) AS yieldha FROM stat_production WHERE ecosystem = ? AND location_code = ? AND location_type = 2 AND year >= 2000  GROUP BY year")) {
        $stmt->bind_param("ss", $eco, $provnum);
        $stmt->execute();
        $stmt->store_result();
        while($row = fetch_get_result_alt($stmt)){
            $ryield[] = $row;
        }
    }
    if ($stmt = $conn->prepare("SELECT year AS period , food_use AS food, seeds AS seed, feed_wastes AS feeds, production AS produce FROM stat_estimates WHERE year >= 1996 AND location_code = ? AND location_type = 2 GROUP BY year")) {
        $stmt->bind_param("s", $provnum);
        $stmt->execute();
        $stmt->store_result();
        while($row = fetch_get_result_alt($stmt)){
            $usest[] = $row;
        }
    }
    if ($stmt = $conn->prepare("SELECT year AS period, farmgate AS farmprice, wholesale AS saleprice, retail AS consumerprice FROM stat_ordprice WHERE location_code =? AND location_type=2 AND semester=3 AND year >= 2000 ORDER BY (year) ASC")) {
        $stmt->bind_param("s", $provnum);
        $stmt->execute();
        $stmt->store_result();
        while($row = fetch_get_result_alt($stmt)){
            $oprice[] = $row;
        }
    }
    $pprod_data = $purifier->purify(json_encode($pprod));
    $ahar_data = $purifier->purify(json_encode($ahar));
    $ryield_data = $purifier->purify(json_encode($ryield));
    $usest_data = $purifier->purify(json_encode($usest));
    $oprice_data = $purifier->purify(json_encode($oprice));
    ?>
    <script>
        <?php 
        echo 'var pproddata = ' . $pprod_data . ';';
        echo 'var ahardata = ' . $ahar_data . ';';
        echo 'var ryielddata = ' . $ryield_data . ';';
        echo 'var usestdata = ' . $usest_data . ';';
        echo 'var opricedata = ' . $oprice_data . ';';?>
        let prod = {
            x: [],
            y: [],
            mode: "lines+markers",
            marker: {
                color: '#2138B7',
                size: 5
            },
            line: {
                color: '#2138B7',
                width: 2
            }
        };
        let ahar = {
            x: [],
            y: [],
            mode: "lines+markers",
            marker: {
                color: '#1A8437',
                size: 5
            },
            line: {
                color: '#1A8437',
                width: 2
            }
        };
        let ryield = {
            x: [],
            y: [],
            mode: "lines+markers",
            marker: {
                color: '#7570B3',
                size: 5
            },
            line: {
                color: '#7570B3',
                width: 2
            }
        };
        let fooduse = {
            x: [],
            y: [],
            type: 'bar',
            name: 'Food',
            marker: {
                color: '#FFC000'
            }
        };
        let seeds = {
            x: [],
            y: [],
            type: 'bar',
            name: 'Seeds',
            marker: {
                color: '#5B9BD5'
            }
        };
        let feeds = {
            x: [],
            y: [],
            type: 'bar',
            name: 'Feeds+Waste',
            marker: {
                color: '#ED7D31'
            }
        };
        let prodest = {
            x: [],
            y: [],
            mode: "lines+markers",
            marker: {
                color: '#344E65',
                size: 5
            },
            line: {
                color: '#344E65',
                width: 2
            },
            name: 'Production'
        };
        let farmp = {
            x: [],
            y: [],
            mode: "lines+markers",
            marker: {
                color: '#FAB231',
                size: 5
            },
            line: {
                color: '#FAB231',
                width: 2
            },
            name: 'Dry palay price',
            connectgaps: false
        };
        let wholep = {
            x: [],
            y: [],
            mode: "lines+markers",
            marker: {
                color: '#1A8437',
                size: 5
            },
            line: {
                color: '#1A8437',
                width: 2
            },
            name: 'Wholesale, reg. milled',
            connectgaps: false
        };
        let retp = {
            x: [],
            y: [],
            mode: "lines+markers",
            marker: {
                color: '#9D4C3B',
                size: 5
            },
            line: {
                color: '#9D4C3B',
                width: 2
            },
            name: 'Retail, reg. milled',
            connectgaps: false
        };
        var data = {
            xaxis: {
                type: "category",
                showgrid: false,
                zeroline: false,
                showline: false,
                mirror: 'ticks',
                nticks: 7
            },
            yaxis: {
                showgrid: true,
                zeroline: true,
                showline: false,
                mirror: 'ticks',
                gridcolor: "#DDDDDD",
                gridwidth: 0.5,
                zerolinecolor: '#969696',
                zerolinewidth: 1,
                tickformat: ',',
                rangemode: 'tozero'
            },
            font: {
                family: 'Barlow, sans-serif',
                size: 14,
                color: '#242525'
            },
            margin: {
                r: 10,
                b: 60,
                t: 80,
                pad: 12
            },
            legend: {
                orientation: "h",
                x: 1,
                y: 1.12
            },
            barmode: 'stack'
        };
        var layout = {};
        pproddata.forEach(function(val) {
            prod.x.push(val["period"]);
            prod.y.push(val["produce"])
        });
        ahardata.forEach(function(val) {
            ahar.x.push(val["period"]);
            ahar.y.push(val["avgha"])
        });
        ryielddata.forEach(function(val) {
            ryield.x.push(val["period"]);
            ryield.y.push(val["yieldha"])
        });
        usestdata.forEach(function(val) {
            fooduse.x.push(val["period"]);
            fooduse.y.push(val["food"]);
            seeds.x.push(val["period"]);
            seeds.y.push(val["seed"]);
            feeds.x.push(val["period"]);
            feeds.y.push(val["feeds"]);
            prodest.x.push(val["period"]);
            prodest.y.push(val["produce"])
        });
        opricedata.forEach(function(val) {
            farmp.x.push(val["period"]);
            farmp.y.push(val["farmprice"]);
            wholep.x.push(val["period"]);
            wholep.y.push(val["saleprice"]);
            retp.x.push(val["period"]);
            retp.y.push(val["consumerprice"])
        });
        Plotly.newPlot('pprod', [prod], data, {
            displayModeBar: true,
            responsive: true
        });
        Plotly.newPlot('ahar', [ahar], data, {
            displayModeBar: true,
            responsive: true
        });
        Plotly.newPlot('ryield', [ryield], data, {
            displayModeBar: true,
            responsive: true
        });
        Plotly.newPlot('use_est', [prodest, fooduse, seeds, feeds], data, {
            displayModeBar: true,
            responsive: true
        });
        Plotly.newPlot('ordrice', [farmp, wholep, retp], data, {
            displayModeBar: true,
            responsive: true
        });
    </script>
    <?php
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
        echo $temp;
    }
require_once("../includes/footer.php");
?>
