<?php
	require_once("../includes/headeralt.php");
?>	
<div id="tableData">
<?php
	$provinces = disinfect_var($_POST['provinces']);
	$seasons = disinfect_var($_POST['seasons']);

	$content=count($provinces);
	
	$stmt= $total_stmt = "";
	$total = $codename = $percent = 0;
	
	$lop = implode(',', $provinces);
	$los = implode(',', $seasons);
	$region = 0;	
	
	foreach($provinces as $province){
	$season_names = $headings = $footnotes = array();
	$avg_distance = $avg_time = $avg_fare =  array(); 
	$n_stat = $total_n = array();
	$counter = -1;
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
		
	foreach($seasons as $season){
	$total_farmers = 0;
        
	$getseason=$conn->prepare("SELECT season_name, season_year FROM legend_season WHERE season_id = ?");
	$getseason->bind_param("s", $season);
	$getseason->execute();
	$getseason->store_result();
	$getseason->bind_result($name, $year);
	while($getseason->fetch()){ 
		$fullname = $name." ".$year." harvest";
		$nickname = $name." ".$year;
		array_push($season_names, $fullname);
		array_push($footnotes, $nickname);
	}
	array_push($headings, "(%)");
	
	if($province==999){
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_watersource WHERE matrix_rectype1.region = matrix_watersource.region AND matrix_rectype1.province = matrix_watersource.province AND matrix_rectype1.municipality = matrix_watersource.municipality AND matrix_rectype1.barangay = matrix_watersource.barangay AND matrix_rectype1.hh_number = matrix_watersource.hh_number AND matrix_rectype1.season = matrix_watersource.season AND matrix_rectype1.season = ? AND matrix_watersource.irrigation_source IS NOT NULL) a");
		$all_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_watersource WHERE matrix_rectype1.region = matrix_watersource.region AND matrix_rectype1.province = matrix_watersource.province AND matrix_rectype1.municipality = matrix_watersource.municipality AND matrix_rectype1.barangay = matrix_watersource.barangay AND matrix_rectype1.hh_number = matrix_watersource.hh_number AND matrix_rectype1.season = matrix_watersource.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_watersource.irrigation_source IS NOT NULL) a");
		$all_stmt->bind_param("ss", $season, $province);
	}
		
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($total);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			$total_farmers = $total;
			array_push($n_stat, $total);
		}
	}
        
    $findlegend = $conn->prepare("SELECT DISTINCT season FROM legend_watersource ORDER BY season DESC");
	$findlegend->execute();
	$findlegend->store_result();
	$findlegend->bind_result($this);
	$season_pool = array();
	while($findlegend->fetch()){
		array_push($season_pool, $this);
	}
	$findlegend->close();
	$varlegend = 0;
	for($i=$season; $i > 0; $i--){
		if(in_array($i, $season_pool)) {
			$varlegend = $i;
			break;
		}
	}
        
	if($province==999){
		$all_stmt= $conn->prepare("SELECT c.source, c.watersource, count(r.irrig) AS frequency FROM (SELECT watersource_id as id, watersource_category AS source, watersource FROM legend_watersource WHERE season = ? OR season IS NULL ORDER BY watersource_id ASC) AS c LEFT JOIN (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_watersource.irrigation_source as irrig FROM matrix_watersource JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_watersource.region AND matrix_rectype1.province = matrix_watersource.province AND matrix_rectype1.municipality = matrix_watersource.municipality AND matrix_rectype1.barangay = matrix_watersource.barangay AND matrix_rectype1.hh_number = matrix_watersource.hh_number AND matrix_rectype1.season = matrix_watersource.season AND matrix_rectype1.season = ?) AS r ON c.source = r.irrig GROUP BY c.source ORDER BY c.id ASC;");
		$all_stmt->bind_param("ss", $varlegend, $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT c.source, c.watersource, count(r.irrig) AS frequency FROM (SELECT watersource_id as id, watersource_category AS source, watersource FROM legend_watersource WHERE season = ? OR season IS NULL ORDER BY watersource_id ASC) AS c LEFT JOIN (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_watersource.irrigation_source as irrig FROM matrix_watersource JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_watersource.region AND matrix_rectype1.province = matrix_watersource.province AND matrix_rectype1.municipality = matrix_watersource.municipality AND matrix_rectype1.barangay = matrix_watersource.barangay AND matrix_rectype1.hh_number = matrix_watersource.hh_number AND matrix_rectype1.season = matrix_watersource.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) AS r ON c.source = r.irrig GROUP BY c.source ORDER BY c.id ASC;");
		$all_stmt->bind_param("sss", $varlegend, $season, $province);
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($category, $category_name, $quantity);
        
    $counter++;
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers != 0){
				$temp =  ($quantity / $total_farmers) * 100;
			}
            if(!isset($matrix_watersource[$category_name])){
				$matrix_watersource[$category_name] = array();
			}
            $matrix_watersource[$category_name][$counter] = $temp;
		}
	}
     

	$all_stmt->close();
        
	}	
	$region = 0;			
	$getprovince=$conn->prepare("SELECT province_name, region FROM legend_province WHERE province_id = ?");
	$getprovince->bind_param("s", $province);
	$getprovince->execute();
	$getprovince->store_result();
	$getprovince->bind_result($name, $region);
	$getprovince->fetch();
	$count_columns=(count($seasons))+1;
	create_header($name, $season_names, 1);
	echo "<tbody>\n";
	create_special_rows("Source of water*", array_formatting($n_stat,"(n = ",")"), 1);
	echo "<tr>\n<td class='header4'></td><td class='header4 center' colspan=".($count_columns-1).">(percent of farm households)</td>\n</tr>\n";
    foreach ($matrix_watersource as $key => $value){
        for($i = 0; $i <count($seasons); $i++){
            if(!isset($value[$i])){
                $value[$i] = "-";
            }
        }
        if($key !== "Rain/Sahod ulan/Rainfed") {
            create_row($key, $value, 0);
        }
	}
    create_row("Rain/Sahod ulan/Rainfed", $matrix_watersource['Rain/Sahod ulan/Rainfed'], 0);
	echo "</tbody>\n";
	echo "</table>\n";
	for($i=0;$i<count($seasons);$i++){
		if($province==999){
			$stmt= $conn->prepare("SELECT count(region) from matrix_rectype1 where season = ?");
			$stmt->bind_param("s", $seasons[$i]);
		}
		else{
			$stmt= $conn->prepare("SELECT count(region) from matrix_rectype1 where season = ? AND province = ?");
			$stmt->bind_param("ss", $seasons[$i], $province);
		}
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($total);
			
		while($stmt->fetch()){ 
			array_push($total_n, $total);
		}
	}
	for($i=0;$i<count($seasons);$i++){
		if ($n_stat[$i] < $total_n[$i]){
			echo '<div>Excludes farmers who temporarily stopped farming/did not plant rice/experienced crop failure/with inconsistent data for '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$n_stat[$i]) .')</div>';
		}
	}
	echo "<br/>\n";
}

echo "<div>* for largest parcel only</div>\n";
echo "<div>Note: Different source of categories were used during the 2016-2017 RBFHS survey round.";
echo "<div>Spring water includes free-flowing source, spring, open well, dug well, river/streams - gravity</div>\n<br/>\n";
echo "<div><b>NIA/NIS</b> - refers to the National Irrigation Administration/National Irrigation Systems</div>\n";
echo "<div><b>CIS</b> - refers to Communal Irrigation Systems</div>\n";
echo "<div><b>SWIP</b> - refers to Small Water Impounding Project</div>\n";
echo "<div><b>SSIS</b> - refers to Small Scale Irrigation Systems</div>\n";
echo "<div><b>SFR</b> - refers to Small Farm Reservoir</div>\n";
echo "<div><b>STW</b> - refers to Shallow Tube Well</div>\n<br/>\n";
for($i=0;$i<count($seasons);$i++){
$stmt=$conn->prepare("SELECT count(region) from matrix_rectype1 where season = ?");
$stmt->bind_param("s", $seasons[$i]);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($total);
	
while($stmt->fetch()){ 
	echo "<div>".$footnotes[$i]." = ". number_format($total) ." respondents</div>\n";
}
}
	
$getseason->close();
$getprovince->close();
$stmt->close();
?>
<?php echo displayNoteRounding(); ?>
<br />
<?php echo displayNoteIrrigate(); ?>
<br />
<div>Data accessed at <?php echo date('l jS \of F Y h:i:s A');?></div>
<?php echo displayNoteContact(); ?>
</div>
<?php
require_once("../includes/export.php");
?>