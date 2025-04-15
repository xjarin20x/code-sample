<?php
	require_once("../includes/headeralt.php");
?>	
<div id="tableData">
<?php
	$provinces = disinfect_var($_POST['provinces']);
	$seasons = disinfect_var($_POST['seasons']);

	$content = count($provinces);
	
	$stmt = $total_stmt = "";
	$total = 0;
	
	foreach($provinces as $province){
	$season_names = $headings = $headings2 = $footnotes = array();
	$fert = $herb = $insect = $fungi = $rodent = $mollusk = $otherchem = array();
	$total_n = $total_farmers = array();
	$count = 0;
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	
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

	for($i=0;$i<3;$i++){
		array_push($headings2, "Mean");	
		array_push($headings2, "SD<sup>a</sup>");
	}

	if($province==999){
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_source IS NOT NULL) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5)) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_source = 0) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_source IS NOT NULL) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5)) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_source = 0) a");
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
		$all_stmt= $conn->prepare("SELECT matrix_application.type, AVG(IFNULL(matrix_application.seedbed_appl, 0) + IFNULL(matrix_application.preestab_appl, 0) + IFNULL(matrix_application.standing_appl, 0)) AS avg_appl, STDDEV_SAMP(IFNULL(matrix_application.seedbed_appl, 0) + IFNULL(matrix_application.preestab_appl, 0) + IFNULL(matrix_application.standing_appl, 0)) AS stddev_appl FROM matrix_application JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_application.season = ? GROUP BY matrix_application.type");
		$irri_stmt= $conn->prepare("SELECT matrix_application.type, AVG(IFNULL(matrix_application.seedbed_appl, 0) + IFNULL(matrix_application.preestab_appl, 0) + IFNULL(matrix_application.standing_appl, 0)) AS avg_appl, STDDEV_SAMP(IFNULL(matrix_application.seedbed_appl, 0) + IFNULL(matrix_application.preestab_appl, 0) + IFNULL(matrix_application.standing_appl, 0)) AS stddev_appl FROM matrix_application JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_application.season = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) GROUP BY matrix_application.type");
		$rain_stmt= $conn->prepare("SELECT matrix_application.type, AVG(IFNULL(matrix_application.seedbed_appl, 0) + IFNULL(matrix_application.preestab_appl, 0) + IFNULL(matrix_application.standing_appl, 0)) AS avg_appl, STDDEV_SAMP(IFNULL(matrix_application.seedbed_appl, 0) + IFNULL(matrix_application.preestab_appl, 0) + IFNULL(matrix_application.standing_appl, 0)) AS stddev_appl FROM matrix_application JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_application.season = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_source = 0 GROUP BY matrix_application.type");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT matrix_application.type, AVG(IFNULL(matrix_application.seedbed_appl, 0) + IFNULL(matrix_application.preestab_appl, 0) + IFNULL(matrix_application.standing_appl, 0)) AS avg_appl, STDDEV_SAMP(IFNULL(matrix_application.seedbed_appl, 0) + IFNULL(matrix_application.preestab_appl, 0) + IFNULL(matrix_application.standing_appl, 0)) AS stddev_appl FROM matrix_application JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_application.season = ? AND matrix_application.province = ? GROUP BY matrix_application.type");
		$irri_stmt= $conn->prepare("SELECT matrix_application.type, AVG(IFNULL(matrix_application.seedbed_appl, 0) + IFNULL(matrix_application.preestab_appl, 0) + IFNULL(matrix_application.standing_appl, 0)) AS avg_appl, STDDEV_SAMP(IFNULL(matrix_application.seedbed_appl, 0) + IFNULL(matrix_application.preestab_appl, 0) + IFNULL(matrix_application.standing_appl, 0)) AS stddev_appl FROM matrix_application JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_application.season = ? AND matrix_application.province = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) GROUP BY matrix_application.type");
		$rain_stmt= $conn->prepare("SELECT matrix_application.type, AVG(IFNULL(matrix_application.seedbed_appl, 0) + IFNULL(matrix_application.preestab_appl, 0) + IFNULL(matrix_application.standing_appl, 0)) AS avg_appl, STDDEV_SAMP(IFNULL(matrix_application.seedbed_appl, 0) + IFNULL(matrix_application.preestab_appl, 0) + IFNULL(matrix_application.standing_appl, 0)) AS stddev_appl FROM matrix_application JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_application.season = ? AND matrix_application.province = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_source = 0 GROUP BY matrix_application.type");
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($type, $avg, $sd);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){
			switch ($type) {
				case "F":
					$fert[$count] = $avg;
					$fert[$count+1] = $sd;
					break;
				case "H":
					$herb[$count] = $avg;
					$herb[$count+1] = $sd;
					break;
				case "I":
					$insect[$count] = $avg;
					$insect[$count+1] = $sd;
					break;
				case "M":
					$mollusk[$count] = $avg;
					$mollusk[$count+1] = $sd;
					break;
				case "O":
					$otherchem[$count] = $avg;
					$otherchem[$count+1] = $sd;
					break;
				case "R":
					$rodent[$count] = $avg;
					$rodent[$count+1] = $sd;
					break;
				case "U":
					$fungi[$count] = $avg;
					$fungi[$count+1] = $sd;
					break;
			}
		}
	}
	$ctemp = $count;
	$count += 2;
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($fert[$i])){
			$fert[$i] = "0";
		}
	}
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($herb[$i])){
			$herb[$i] = "0";
		}
	}
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($insect[$i])){
			$insect[$i] = "0";
		}
	}
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($mollusk[$i])){
			$mollusk[$i] = "0";
		}
	}	
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($otherchem[$i])){
			$otherchem[$i] = "0";
		}
	}
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($rodent[$i])){
			$rodent[$i] = "0";
		}
	}
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($fungi[$i])){
			$fungi[$i] = "0";
		}
	}
	$all_stmt->close();	
	
	//irri
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($type, $avg, $sd);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){
			switch ($type) {
				case "F":
					$fert[$count] = $avg;
					$fert[$count+1] = $sd;
					break;
				case "H":
					$herb[$count] = $avg;
					$herb[$count+1] = $sd;
					break;
				case "I":
					$insect[$count] = $avg;
					$insect[$count+1] = $sd;
					break;
				case "M":
					$mollusk[$count] = $avg;
					$mollusk[$count+1] = $sd;
					break;
				case "O":
					$otherchem[$count] = $avg;
					$otherchem[$count+1] = $sd;
					break;
				case "R":
					$rodent[$count] = $avg;
					$rodent[$count+1] = $sd;
					break;
				case "U":
					$fungi[$count] = $avg;
					$fungi[$count+1] = $sd;
					break;
			}
		}
	}
	$ctemp = $count;
	$count += 2;
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($fert[$i])){
			$fert[$i] = "0";
		}
	}
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($herb[$i])){
			$herb[$i] = "0";
		}
	}
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($insect[$i])){
			$insect[$i] = "0";
		}
	}
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($mollusk[$i])){
			$mollusk[$i] = "0";
		}
	}	
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($otherchem[$i])){
			$otherchem[$i] = "0";
		}
	}
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($rodent[$i])){
			$rodent[$i] = "0";
		}
	}
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($fungi[$i])){
			$fungi[$i] = "0";
		}
	}
	$irri_stmt->close();
	
	//rain
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($type, $avg, $sd);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){
			switch ($type) {
				case "F":
					$fert[$count] = $avg;
					$fert[$count+1] = $sd;
					break;
				case "H":
					$herb[$count] = $avg;
					$herb[$count+1] = $sd;
					break;
				case "I":
					$insect[$count] = $avg;
					$insect[$count+1] = $sd;
					break;
				case "M":
					$mollusk[$count] = $avg;
					$mollusk[$count+1] = $sd;
					break;
				case "O":
					$otherchem[$count] = $avg;
					$otherchem[$count+1] = $sd;
					break;
				case "R":
					$rodent[$count] = $avg;
					$rodent[$count+1] = $sd;
					break;
				case "U":
					$fungi[$count] = $avg;
					$fungi[$count+1] = $sd;
					break;
			}
		}
	}
	$ctemp = $count;
	$count += 2;
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($fert[$i])){
			$fert[$i] = "0";
		}
	}
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($herb[$i])){
			$herb[$i] = "0";
		}
	}
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($insect[$i])){
			$insect[$i] = "0";
		}
	}
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($mollusk[$i])){
			$mollusk[$i] = "0";
		}
	}	
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($otherchem[$i])){
			$otherchem[$i] = "0";
		}
	}
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($rodent[$i])){
			$rodent[$i] = "0";
		}
	}
	for($i=$ctemp; $i < $count; $i++){
		if(!isset($fungi[$i])){
			$fungi[$i] = "0";
		}
	}
	$rain_stmt->close();	
}
$region = 0;			
$getprovince=$conn->prepare("SELECT province_name, region FROM legend_province WHERE province_id = ?");
$getprovince->bind_param("s", $province);
$getprovince->execute();
$getprovince->store_result();
$getprovince->bind_result($name, $region);
$getprovince->fetch();
$count_columns=(count($seasons)*6)+1;
create_header($name, $season_names, 6);
echo "<tbody>\n";
create_special_rows("Items", $headings, 2);
create_special_rows("", array_formatting($total_farmers,"(n = ",")"), 2);
echo "<tr>\n<td class='header4'></td><td class='header4 center' colspan=".($count_columns-1).">(average)</td>\n</tr>\n";
create_headings("", $headings2);
create_row("Fertilizer", $fert, 1);
create_row("Herbicide", $herb, 1);
create_row("Insecticide", $insect, 1);
create_row("Fungicide", $fungi, 1);
create_row("Rodenticide", $rodent, 1);
create_row("Molluscicide", $mollusk, 1);
create_row("Other pesticide", $otherchem, 1);
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
echo '<div>Excludes farmers who temporarily stopped farming for '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$total_farmers[$i*3]) .')</div>';
}
}
echo "<br/>\n";
}
for($i=0;$i<count($seasons);$i++){
$stmt= $conn->prepare("SELECT count(region) from matrix_rectype1 where season = ?");
$stmt->bind_param("s", $seasons[$i]);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($total);
while($stmt->fetch()){ 
echo "<div>".$footnotes[$i]." = ". number_format($total) ." respondents</div>\n";
}
}
echo "<br/>\n<div><sup>a </sup><b>SD</b> - refers to standard deviation</div>\n<br/>\n";
$getseason->close();
$getprovince->close();
$stmt->close();
echo displayNoteIrrigate();
echo "<br/>";
echo "<div>Data accessed at ". date('l jS \of F Y h:i:s A') . "</div>";
echo displayNoteContact();
echo "</div>";
require_once("../includes/export.php");
?>