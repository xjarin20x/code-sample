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
	$total_farmers = $total_n = array();
	$counter = 0;
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	
	$source_cap = array(); 
	for($i=0; $i < 3; $i++){
		array_push($source_cap, array());
	}
	
	foreach($seasons as $season){

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
	array_push($headings, "ALL ECOSYSTEMS");
	array_push($headings, "IRRIGATED");
	array_push($headings, "NON-IRRIGATED");
	
	if($province==999){
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype15, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype15.region AND matrix_rectype1.province = matrix_rectype15.province AND matrix_rectype1.municipality = matrix_rectype15.municipality AND matrix_rectype1.barangay = matrix_rectype15.barangay AND matrix_rectype1.hh_number = matrix_rectype15.hh_number AND matrix_rectype1.season = matrix_rectype15.season AND matrix_rectype1.season = ? AND matrix_irrigation.region = matrix_rectype1.region AND matrix_irrigation.province = matrix_rectype1.province AND matrix_irrigation.municipality = matrix_rectype1.municipality AND matrix_irrigation.barangay = matrix_rectype1.barangay AND matrix_irrigation.hh_number = matrix_rectype1.hh_number AND matrix_irrigation.season = matrix_rectype1.season) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype15, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype15.region AND matrix_rectype1.province = matrix_rectype15.province AND matrix_rectype1.municipality = matrix_rectype15.municipality AND matrix_rectype1.barangay = matrix_rectype15.barangay AND matrix_rectype1.hh_number = matrix_rectype15.hh_number AND matrix_rectype1.season = matrix_rectype15.season AND matrix_rectype1.season = ? AND matrix_irrigation.region = matrix_rectype1.region AND matrix_irrigation.province = matrix_rectype1.province AND matrix_irrigation.municipality = matrix_rectype1.municipality AND matrix_irrigation.barangay = matrix_rectype1.barangay AND matrix_irrigation.hh_number = matrix_rectype1.hh_number AND matrix_irrigation.season = matrix_rectype1.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5)) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype15, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype15.region AND matrix_rectype1.province = matrix_rectype15.province AND matrix_rectype1.municipality = matrix_rectype15.municipality AND matrix_rectype1.barangay = matrix_rectype15.barangay AND matrix_rectype1.hh_number = matrix_rectype15.hh_number AND matrix_rectype1.season = matrix_rectype15.season AND matrix_rectype1.season = ? AND matrix_irrigation.region = matrix_rectype1.region AND matrix_irrigation.province = matrix_rectype1.province AND matrix_irrigation.municipality = matrix_rectype1.municipality AND matrix_irrigation.barangay = matrix_rectype1.barangay AND matrix_irrigation.hh_number = matrix_rectype1.hh_number AND matrix_irrigation.season = matrix_rectype1.season AND matrix_irrigation.irrigation_source = 0) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype15, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype15.region AND matrix_rectype1.province = matrix_rectype15.province AND matrix_rectype1.municipality = matrix_rectype15.municipality AND matrix_rectype1.barangay = matrix_rectype15.barangay AND matrix_rectype1.hh_number = matrix_rectype15.hh_number AND matrix_rectype1.season = matrix_rectype15.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.region = matrix_rectype1.region AND matrix_irrigation.province = matrix_rectype1.province AND matrix_irrigation.municipality = matrix_rectype1.municipality AND matrix_irrigation.barangay = matrix_rectype1.barangay AND matrix_irrigation.hh_number = matrix_rectype1.hh_number AND matrix_irrigation.season = matrix_rectype1.season AND matrix_irrigation.irrigation_source IS NOT NULL) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype15, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype15.region AND matrix_rectype1.province = matrix_rectype15.province AND matrix_rectype1.municipality = matrix_rectype15.municipality AND matrix_rectype1.barangay = matrix_rectype15.barangay AND matrix_rectype1.hh_number = matrix_rectype15.hh_number AND matrix_rectype1.season = matrix_rectype15.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.region = matrix_rectype1.region AND matrix_irrigation.province = matrix_rectype1.province AND matrix_irrigation.municipality = matrix_rectype1.municipality AND matrix_irrigation.barangay = matrix_rectype1.barangay AND matrix_irrigation.hh_number = matrix_rectype1.hh_number AND matrix_irrigation.season = matrix_rectype1.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5)) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype15, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype15.region AND matrix_rectype1.province = matrix_rectype15.province AND matrix_rectype1.municipality = matrix_rectype15.municipality AND matrix_rectype1.barangay = matrix_rectype15.barangay AND matrix_rectype1.hh_number = matrix_rectype15.hh_number AND matrix_rectype1.season = matrix_rectype15.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.region = matrix_rectype1.region AND matrix_irrigation.province = matrix_rectype1.province AND matrix_irrigation.municipality = matrix_rectype1.municipality AND matrix_irrigation.barangay = matrix_rectype1.barangay AND matrix_irrigation.hh_number = matrix_rectype1.hh_number AND matrix_irrigation.season = matrix_rectype1.season AND matrix_irrigation.irrigation_source = 0) a");
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($total);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			array_push($total_farmers, $total);
		}
	}
	else{
		array_push($total_farmers, 0);
	}
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($total);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			array_push($total_farmers, $total);
		}
	}
	else{
		array_push($total_farmers, 0);
	}
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($total);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			array_push($total_farmers, $total);
		}
	}
	else{
		array_push($total_farmers, 0);
	}
	
	if($province==999){
		$all_stmt= $conn->prepare("SELECT c.category, COUNT(a.source) AS frequency FROM (SELECT 1 category UNION ALL SELECT 2 category UNION ALL SELECT 3 category) AS c LEFT JOIN (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype15.cap_type as source FROM matrix_rectype15 JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_rectype15.region AND matrix_rectype1.province = matrix_rectype15.province AND matrix_rectype1.municipality = matrix_rectype15.municipality AND matrix_rectype1.barangay = matrix_rectype15.barangay AND matrix_rectype1.hh_number = matrix_rectype15.hh_number AND matrix_rectype1.season = matrix_rectype15.season AND matrix_rectype1.season = ?) AS a ON c.category = a.source GROUP BY c.category");
		$irri_stmt= $conn->prepare("SELECT c.category, COUNT(a.source) AS frequency FROM (SELECT 1 category UNION ALL SELECT 2 category UNION ALL SELECT 3 category) AS c LEFT JOIN (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype15.cap_type as source FROM matrix_rectype15 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype15.region AND matrix_rectype1.province = matrix_rectype15.province AND matrix_rectype1.municipality = matrix_rectype15.municipality AND matrix_rectype1.barangay = matrix_rectype15.barangay AND matrix_rectype1.hh_number = matrix_rectype15.hh_number AND matrix_rectype1.season = matrix_rectype15.season AND matrix_rectype1.season = ? AND matrix_irrigation.region = matrix_rectype1.region AND matrix_irrigation.province = matrix_rectype1.province AND matrix_irrigation.municipality = matrix_rectype1.municipality AND matrix_irrigation.barangay = matrix_rectype1.barangay AND matrix_irrigation.hh_number = matrix_rectype1.hh_number AND matrix_irrigation.season = matrix_rectype1.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5)) AS a ON c.category = a.source GROUP BY c.category");
		$rain_stmt= $conn->prepare("SELECT c.category, COUNT(a.source) AS frequency FROM (SELECT 1 category UNION ALL SELECT 2 category UNION ALL SELECT 3 category) AS c LEFT JOIN (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype15.cap_type as source FROM matrix_rectype15 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype15.region AND matrix_rectype1.province = matrix_rectype15.province AND matrix_rectype1.municipality = matrix_rectype15.municipality AND matrix_rectype1.barangay = matrix_rectype15.barangay AND matrix_rectype1.hh_number = matrix_rectype15.hh_number AND matrix_rectype1.season = matrix_rectype15.season AND matrix_rectype1.season = ? AND matrix_irrigation.region = matrix_rectype1.region AND matrix_irrigation.province = matrix_rectype1.province AND matrix_irrigation.municipality = matrix_rectype1.municipality AND matrix_irrigation.barangay = matrix_rectype1.barangay AND matrix_irrigation.hh_number = matrix_rectype1.hh_number AND matrix_irrigation.season = matrix_rectype1.season AND matrix_irrigation.irrigation_source = 0) AS a ON c.category = a.source GROUP BY c.category");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT c.category, COUNT(a.source) AS frequency FROM (SELECT 1 category UNION ALL SELECT 2 category UNION ALL SELECT 3 category) AS c LEFT JOIN (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype15.cap_type as source FROM matrix_rectype15 JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_rectype15.region AND matrix_rectype1.province = matrix_rectype15.province AND matrix_rectype1.municipality = matrix_rectype15.municipality AND matrix_rectype1.barangay = matrix_rectype15.barangay AND matrix_rectype1.hh_number = matrix_rectype15.hh_number AND matrix_rectype1.season = matrix_rectype15.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) AS a ON c.category = a.source GROUP BY c.category");
		$irri_stmt= $conn->prepare("SELECT c.category, COUNT(a.source) AS frequency FROM (SELECT 1 category UNION ALL SELECT 2 category UNION ALL SELECT 3 category) AS c LEFT JOIN (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype15.cap_type as source FROM matrix_rectype15 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype15.region AND matrix_rectype1.province = matrix_rectype15.province AND matrix_rectype1.municipality = matrix_rectype15.municipality AND matrix_rectype1.barangay = matrix_rectype15.barangay AND matrix_rectype1.hh_number = matrix_rectype15.hh_number AND matrix_rectype1.season = matrix_rectype15.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.region = matrix_rectype1.region AND matrix_irrigation.province = matrix_rectype1.province AND matrix_irrigation.municipality = matrix_rectype1.municipality AND matrix_irrigation.barangay = matrix_rectype1.barangay AND matrix_irrigation.hh_number = matrix_rectype1.hh_number AND matrix_irrigation.season = matrix_rectype1.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5)) AS a ON c.category = a.source GROUP BY c.category");
		$rain_stmt= $conn->prepare("SELECT c.category, COUNT(a.source) AS frequency FROM (SELECT 1 category UNION ALL SELECT 2 category UNION ALL SELECT 3 category) AS c LEFT JOIN (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype15.cap_type as source FROM matrix_rectype15 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype15.region AND matrix_rectype1.province = matrix_rectype15.province AND matrix_rectype1.municipality = matrix_rectype15.municipality AND matrix_rectype1.barangay = matrix_rectype15.barangay AND matrix_rectype1.hh_number = matrix_rectype15.hh_number AND matrix_rectype1.season = matrix_rectype15.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.region = matrix_rectype1.region AND matrix_irrigation.province = matrix_rectype1.province AND matrix_irrigation.municipality = matrix_rectype1.municipality AND matrix_irrigation.barangay = matrix_rectype1.barangay AND matrix_irrigation.hh_number = matrix_rectype1.hh_number AND matrix_irrigation.season = matrix_rectype1.season AND matrix_irrigation.irrigation_source = 0) AS a ON c.category = a.source GROUP BY c.category");
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($category, $quantity);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$counter] != 0){
				$temp = ($quantity / $total_farmers[$counter]) * 100;
			}
			array_push($source_cap[$category-1], $temp);
		}
	}
	$counter++;
	$all_stmt->close();
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($category, $quantity);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$counter] != 0){
				$temp = ($quantity / $total_farmers[$counter]) * 100;
			}
			array_push($source_cap[$category-1], $temp);
		}
	}
	$counter++;
	$irri_stmt->close();
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($category, $quantity);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$counter] != 0){
				$temp = ($quantity / $total_farmers[$counter]) * 100;
			}
			array_push($source_cap[$category-1], $temp);
		}
	}
	$counter++;
	$rain_stmt->close();

	}	
	$region = 0;			
	$getprovince=$conn->prepare("SELECT province_name, region FROM legend_province WHERE province_id = ?");
	$getprovince->bind_param("s", $province);
	$getprovince->execute();
	$getprovince->store_result();
	$getprovince->bind_result($name, $region);
	$getprovince->fetch();
	$count_columns=(count($seasons)*3)+1;
	create_header($name, $season_names, 3);
	create_headings("Source of capital", $headings);
	create_special_rows("", array_formatting($total_farmers,"(n = ",")"), 1);
	echo "<tr>\n<td class='header4'></td><td class='header4 center' colspan=".($count_columns-1).">(Percent of farmers)</td>\n</tr>\n";
	create_row("Own", $source_cap[0], 0);
	create_row("Borrowed", $source_cap[1], 0);
	create_row("Both", $source_cap[2], 0);
	echo "<tbody>\n";
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
		if ($total_farmers[$i*3] < $total_n[$i]){
			echo '<div>Excludes farmers who temporarily stopped farming/did not plant rice/with missing data (n=3) for '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$total_farmers[$i*3]) .')</div>';
		}
	}
	echo "<br/>\n";
	}

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
echo "<br/>\n" . displayNoteRounding();
echo displayNoteIrrigate();
?>
<br/>
<div>Data accessed at <?php echo date('l jS \of F Y h:i:s A');?></div>
<?php echo displayNoteContact(); ?>
</div>
<?php
require_once("../includes/export.php");
?>