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
	$counter = 0;
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	
	$matrix_irrigation = array(); 
	for($i=0; $i <= 5; $i++){
		array_push($matrix_irrigation, array());
	}
	
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
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_source IS NOT NULL) a");
		$all_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_source IS NOT NULL) a");
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

	if($province==999){
		$all_stmt= $conn->prepare("SELECT c.source, count(r.irrig) AS frequency FROM (SELECT 0 source UNION ALL SELECT 1 source UNION ALL SELECT 2 source UNION ALL SELECT 3 source UNION ALL SELECT 4 source UNION ALL SELECT 5 source) AS c LEFT JOIN (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_irrigation.irrigation_source as irrig FROM matrix_irrigation JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ?) AS r ON c.source = r.irrig GROUP BY c.source");
		$all_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT c.source, count(r.irrig) AS frequency FROM (SELECT 0 source UNION ALL SELECT 1 source UNION ALL SELECT 2 source UNION ALL SELECT 3 source UNION ALL SELECT 4 source UNION ALL SELECT 5 source) AS c LEFT JOIN (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_irrigation.irrigation_source as irrig FROM matrix_irrigation JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) AS r ON c.source = r.irrig GROUP BY c.source");
		$all_stmt->bind_param("ss", $season, $province);
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($category, $quantity);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers != 0){
				$temp =  ($quantity / $total_farmers) * 100;
			}
			array_push($matrix_irrigation[$category], $temp);
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
	create_row("NIA", $matrix_irrigation[1], 0);
	create_row("SWIP", $matrix_irrigation[5], 0);
	create_row("Communal Irrigation System", $matrix_irrigation[2], 0);
	create_row("Individual/Gas/Private Pump", $matrix_irrigation[3], 0);
	create_row("Rain/<i>Sahod ulan</i>/Rainfed", $matrix_irrigation[0], 0);
	create_row("Spring Water<sup>a</sup>", $matrix_irrigation[4], 0);
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
echo "<div><sup>a</sup> Spring water includes free-flowing source, spring, open well, dug well, river/streams - gravity</div>\n<br/>\n";
echo "<div><b>NIA</b> - refers to the National Irrigation Administration</div>\n";
echo "<div><b>SWIP</b> - refers to Small Water Impounding Project</div>\n<br/>\n";
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