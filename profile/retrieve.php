<?php
require_once("../includes/header.php");
$prepath = checksubdomain(dirname(__FILE__), $conn, $purifier);

$content = "";
$table_list = array(
			1 => array("Socioeconomic characteristics of farmer-respondents", "matrix_rectype1"),
			6 => array("Estimated monthly per capita income and percentage distribution of income", "matrix_rectype2"),
			8 => array("Farm size, number of parcels and farm location", "matrix_rectype5"),
			9 => array("Distribution of farmers (%) by tenurial status", "matrix_rectype5"),
			10 => array("Accessibility of rice parcel to wholesale markets", "matrix_transpo"),
			11 => array("Distribution of farmers (%) by source of water", "matrix_irrigation"),
			"13a" => array("Distribution of farmers (%) by planting schedule", "matrix_pharvest"),
			"13b" => array("Distribution of farmers (%) by harvesting schedule", "matrix_pharvest"),
			14 => array("Distribution of farmers (%) by cropping pattern", "matrix_cpattern"),
			15 => array("Distribution of farmers (%) by method of crop establishment", "matrix_rectype6"),
			17 => array("Distribution of farmers (%) by method of seedbed preparation", "matrix_rectype6"),
			19 => array("Distribution of farmers (%) by ten (10) most varieties planted", "matrix_mvplanted"),
			21 => array("Distribution of farmers (%) by seed classification", "matrix_rectype6"),
			22 => array("Distribution of farmers (%) by seed source", "matrix_sseed"),
			23 => array("Yield and quantity of inputs used", "matrix_ioutput"),
			24 => array("Number of times of fertilizer and pesticide application", "matrix_application"),
			25 => array("Distribution of farmers (%) by type of fertilizer", "matrix_rectype7"),
			27 => array("Distribution of farmers (%) by frequency of fertilizer application", "matrix_application"),
			28 => array("Distribution of farmers (%) by active ingredients of pesticide applied", "matrix_rectype9"),
			30 => array("Distribution of farmers (%) by frequency of herbicide application", "matrix_application"),
			"33a" => array("Distribution of farmers (%) by frequency of insecticide application", "matrix_application"),
			"33b" => array("Distribution of farmers (%) by frequency of fungicide application", "matrix_application"),
			"33c" => array("Distribution of farmers (%) by frequency of molluscicide application", "matrix_application"),
			"33d" => array("Distribution of farmers (%) by frequency of rodenticide application ", "matrix_application"),
			"33e" => array("Distribution of farmers (%) by frequency of application of other chemicals", "matrix_application"),
			"33f" => array("Distribution of farmers (%) by frequency of all pesticide application", "matrix_application"),
			37 => array("Costs and returns of rice production", "matrix_creturns"),
			38 => array("Distribution of production and disposition of harvest (%) by cropping season", "matrix_mprac"),
			"39a" => array("Percent of farmers with harvest allotted for sale", "matrix_mprac"),
			"39b" => array("Distribution of farmers by marketing practices", "matrix_mdist"),
			41 => array("Distribution of farmers (%) by source of capital for farming", "matrix_rectype15"),
			42 => array("Information on borrowed capital", "matrix_rectype15"),
			60 => array("Yield (kg/ha) per seed classification, by cropping season", "matrix_ioutput"),
			61 => array("Yield (kg/ha) per ecosystem, by cropping season", "matrix_ioutput"),
			62 => array("Seeding rate by cropping season", "matrix_ioutput"),
			63 => array("Number of times of fertilizer application", "matrix_application"),
			64 => array("Average active ingredients (kg/ha) of pesticides used per chemical type, by cropping season", "matrix_ioutput"),
			65 => array("Average N-P-K component per fertilizer use, by cropping season", "matrix_ioutput"),
			66 => array("Labor requirements (person-days/ha) by cropping season", "matrix_ioutput"),
			67 => array("Labor costs of rice production by cropping season", "matrix_creturns"),
			68 => array("Distribution of farm households (%) by annual per capita poverty threshold", "matrix_rectype2"),
			70 => array("Seeding rate by seed classification",  "matrix_ioutput")
		);

if(isset($_GET['table'])) {
	$table = disinfect_var($_GET['table']);
	if (array_key_exists($table, $table_list)) {
?>
<div id="table-view" class="page col-lg-10 mx-auto">
    <div class="page-header col-lg-8 mx-auto">
        <h2 id="table-title"><?php echo $table_list[$table][0]; ?></h2>
        <p><?php echo $GLOBALS['tag']['description']; ?></p>
    </div>
    <div class="page-body col-lg-8 mx-auto">
        <div id="table-input">
            <div class="alert alert-info" role="alert">Asterisk (*) indicates required field.</div>
            <form id="retrieve" method="post" action="<?php echo $GLOBALS['htp'];?>://<?php echo $GLOBALS['hname'];?>/profile/table<?php echo $table; ?>_function.php">
                <div class="form-group">
                    <label for="provinces">Provinces*</label>
                    <select id="provinces" class="form-control multi-select" name="provinces[]" multiple="multiple" tabindex="1" title="Not specified" data-live-search="true" data-size="10" data-selected-text-format="count > 3">
                        <option value="999">Philippines</option>
                        <?php
                        $tname = disinfect_var($table_list[$table][1]);

                        if ($stmt = $conn->prepare("SELECT r.region_id, r.region_name FROM legend_region r JOIN (SELECT DISTINCT region FROM " . $tname . ")l WHERE r.region_id = l.region ORDER BY (r.region_sort) ASC")) {
                            $stmt->execute();
                            $stmt->store_result();
                            $stmt->bind_result($code, $regname);
                            while($stmt->fetch()){
                                $content .=  "<optgroup label='". $regname ."'>\n";
                                if ($pstmt = $conn->prepare("SELECT p.province_id, p.province_name FROM legend_province p JOIN (SELECT DISTINCT province FROM " . $tname . ")l WHERE p.province_id = l.province AND p.region = ? AND p.type = '0' ORDER BY (p.province_name) ASC")) {
                                    $pstmt->bind_param("i", $code);
                                    $pstmt->execute();
                                    $pstmt->bind_result($id, $name);
                                    while($pstmt->fetch()){
                                        $content .=  "<option value='" . $id . "'>". $name ."</option>\n";
                                    }
                                    $pstmt->close();			
                                }
                                $content .=  "</optgroup>\n";
                            }
                            $stmt->close();
                            echo $content;
                        }
                        ?>
                    </select>
                </div>
                <input id="provinces-options" class="btn btn-light options" type="button" value="OK"><br />
                <div class="form-group">
                    <label for="seasons">Seasons*</label>
                    <select id="seasons" class="form-control multi-select" name="seasons[]" multiple="multiple" tabindex="1" title="Not specified" data-live-search="true" data-size="10">
                        <?php
				$content = "";
				if ($stmt = $conn->prepare("SELECT s.season_id, s.season_name, s.season_year FROM legend_season s JOIN (SELECT DISTINCT season FROM " . $tname . ")l WHERE s.season_id = l.season ORDER BY (s.season_year) ASC")) {
					$stmt->execute();
					$stmt->store_result();
					$stmt->bind_result($number, $name, $year);
					while($stmt->fetch()){
						$content .= "<option value='" . $number . "' title='" . $year . "'>" . $name." ".$year. "</option>\n";
					}
					$stmt->close();			
					echo $content;
				}
				?>
                    </select>
                </div>
                <input id="seasons-options" class="btn btn-light options" type="button" value="OK"><br />
                <input id="submit" class="btn btn-primary" type="submit" value="Continue" disabled>
            </form>
        </div>
        <div id="table-output">
            <div class="loading d-flex justify-content-center">
              <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Building custom data tables...</span>
              </div>
              <p class="text-muted"> Building custom data tables...</p>
            </div>
        </div>
    </div>
</div>
<?php
	}
    else {
    echo '	<div class="page col-lg-8 mx-auto">
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
	header("HTTP/1.1 401 Unauthorized");
    header("Location: index.php");
}
?>
<?php
require_once("../includes/footer.php");
?>
