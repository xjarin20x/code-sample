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
	$total_farmers = $total_n = array();
	$counter = $j = 0;
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	
	$crop_estab = array(); 
	$count = $conn->prepare("SELECT method FROM legend_cropest");
	$count->execute();
	$count->store_result();
	$count->bind_result($ids);
	while($count->fetch()){
		if(!isset($crop_estab[$ids])){
			$crop_estab[$ids] = array();
		}
	}
	
	foreach($seasons as $season){
	
	$findlegend = $conn->prepare("SELECT DISTINCT season FROM legend_cropest ORDER BY season DESC");
	$findlegend->execute();
	$findlegend->store_result();
	$findlegend->bind_result($this);
	$season_pool = array();
	while($findlegend->fetch()){
		array_push($season_pool, $this);
	}
	$findlegend->close();
	$legend = 0;
	for($i=$season; $i > 0; $i--){
		if(in_array($i, $season_pool)) {
			$legend = $i;
			break;
		}
	}

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
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6 WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype6.crop_est IS NOT NULL) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.season = ? AND matrix_rectype6.crop_est IS NOT NULL) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.season = ? AND matrix_rectype6.crop_est IS NOT NULL) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6 WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype6.crop_est IS NOT NULL) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype6.crop_est IS NOT NULL) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype6.crop_est IS NOT NULL) a");
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
		$all_stmt= $conn->prepare("SELECT c.method, count(r.crop) AS frequency FROM (SELECT cropest_id AS category, method FROM legend_cropest WHERE season = ? OR season IS NULL ORDER BY cropest_id ASC) AS c LEFT JOIN (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype6.crop_est as crop FROM matrix_rectype6 JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ?) AS r ON c.category = r.crop GROUP BY c.category");
		$irri_stmt= $conn->prepare("SELECT c.method, count(r.crop) AS frequency FROM (SELECT cropest_id AS category, method FROM legend_cropest WHERE season = ? OR season IS NULL ORDER BY cropest_id ASC) AS c LEFT JOIN (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype6.crop_est as crop FROM matrix_rectype6 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1) AS r ON c.category = r.crop GROUP BY c.category");
		$rain_stmt= $conn->prepare("SELECT c.method, count(r.crop) AS frequency FROM (SELECT cropest_id AS category, method FROM legend_cropest WHERE season = ? OR season IS NULL ORDER BY cropest_id ASC) AS c LEFT JOIN (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype6.crop_est as crop FROM matrix_rectype6 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0) AS r ON c.category = r.crop GROUP BY c.category");
		$all_stmt->bind_param("ss", $legend, $season);
		$irri_stmt->bind_param("ss", $legend, $season);
		$rain_stmt->bind_param("ss", $legend, $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT c.method, count(r.crop) AS frequency FROM (SELECT cropest_id AS category, method FROM legend_cropest WHERE season = ? OR season IS NULL ORDER BY cropest_id ASC) AS c LEFT JOIN (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype6.crop_est as crop FROM matrix_rectype6 JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) AS r ON c.category = r.crop GROUP BY c.category");
		$irri_stmt= $conn->prepare("SELECT c.method, count(r.crop) AS frequency FROM (SELECT cropest_id AS category, method FROM legend_cropest WHERE season = ? OR season IS NULL ORDER BY cropest_id ASC) AS c LEFT JOIN (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype6.crop_est as crop FROM matrix_rectype6 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1) AS r ON c.category = r.crop GROUP BY c.category");
		$rain_stmt= $conn->prepare("SELECT c.method, count(r.crop) AS frequency FROM (SELECT cropest_id AS category, method FROM legend_cropest WHERE season = ? OR season IS NULL ORDER BY cropest_id ASC) AS c LEFT JOIN (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype6.crop_est as crop FROM matrix_rectype6 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0) AS r ON c.category = r.crop GROUP BY c.category");
		$all_stmt->bind_param("sss", $legend, $season, $province);
		$irri_stmt->bind_param("sss", $legend, $season, $province);
		$rain_stmt->bind_param("sss", $legend, $season, $province);
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($method, $quantity);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$j] != 0){
				$temp =  ($quantity / $total_farmers[$j]) * 100;
			}
			array_push($crop_estab[$method], $temp);
		}
	}
	
	$all_stmt->close();
	$j++;
	foreach ($crop_estab as &$sec_arr){
		if(count($sec_arr) < $j){
			array_push($sec_arr, "-");
		}
	}
	
	//irrigated
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($method, $quantity);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$j] != 0){
				$temp =  ($quantity / $total_farmers[$j]) * 100;
			}
			array_push($crop_estab[$method], $temp);
		}
	}
	
	$irri_stmt->close();
	$j++;
	foreach ($crop_estab as &$sec_arr){
		if(count($sec_arr) < $j){
			array_push($sec_arr, "-");
		}
	}
	
	//rainfed

	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($method, $quantity);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$j] != 0){
				$temp =  ($quantity / $total_farmers[$j]) * 100;
			}
			array_push($crop_estab[$method], $temp);
		}
	}
	
	$rain_stmt->close();
	$j++;
	foreach ($crop_estab as &$sec_arr){
		if(count($sec_arr) < $j){
			array_push($sec_arr, "-");
		}
	}
	
	}

	$region = 0;			
	$getprovince=$conn->prepare("SELECT province_name, region FROM legend_province WHERE province_id = ?");
	$getprovince->bind_param("s", $province);
	$getprovince->execute();
	$getprovince->store_result();
	$getprovince->bind_result($name, $region);
	$getprovince->fetch();
	$count_columns=(count($seasons)*3)+1;
	create_header($name, $season_names,3);
	create_headings("Method of Crop Establishment", $headings);
	create_special_rows("", array_formatting($total_farmers,"(n = ",")"), 1);
	echo "<tr>\n<td class='header4 left'></td>\n<td class='header4 center' colspan=".($count_columns-1).">Percent of farmers	</td>\n</tr>\n";
	$temp = "";
	$createrows = $conn->prepare("SELECT season, cropest_id, cropest_category, method FROM legend_cropest WHERE season IS NULL OR season = 1");
	$createrows->execute();
	$createrows->store_result();
	$createrows->bind_result($season, $id, $category, $method);
	while($createrows->fetch()){ 
		if($season == NULL OR $season == 1){
			if($method == "Direct Seeding"){
				$direct = array();
				$n = count($crop_estab[$method]);
				if ($n > 0){
					$compare_arr = create_total_arr($crop_estab["Wet Direct Seeding"], $crop_estab["Dry Direct Seeding"]);
				}
				for($i=0;$i < count($crop_estab["Direct Seeding"]); $i++){
					if($crop_estab["Direct Seeding"][$i] == "-"){
						array_push($direct, $compare_arr[$i]);
					}
					else {
						array_push($direct, $crop_estab["Direct Seeding"][$i]);
					}
				}
				if(array_sum($direct) < 1){
					create_row("Direct Seeding*", $direct, 1);
				}
				else{
					create_row("Direct Seeding*", $direct, 0);
				}
				if(array_sum($crop_estab["Wet Direct Seeding"]) < 1){
					create_row("&nbsp;&nbsp;&nbsp;Wet Direct Seeding", $crop_estab["Wet Direct Seeding"], 1);
				}
				else{
					create_row("&nbsp;&nbsp;&nbsp;Wet Direct Seeding", $crop_estab["Wet Direct Seeding"], 0);
				}	
				if(array_sum($crop_estab["Dry Direct Seeding"]) < 1){
					create_row("&nbsp;&nbsp;&nbsp;Dry Direct Seeding", $crop_estab["Dry Direct Seeding"], 1);
				}
				else{
					create_row("&nbsp;&nbsp;&nbsp;Dry Direct Seeding", $crop_estab["Dry Direct Seeding"], 0);
				}			
			}
			else {
				if(array_sum($crop_estab[$method]) < 1){
					create_row($method, $crop_estab[$method], 1);
				}
				else{
					create_row($method, $crop_estab[$method], 0);
				}
			}
		}
	}
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
			echo '<div>Excludes farmers who temporarily stopped farming during '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$total_farmers[$i*3]) .')</div>';
		}
	}
	echo "<br/>\n";
	}
	
echo "<div>* During 2006-2007 season, Wet Direct Seeding and Dry Direct Seeding are subsumed in Direct Seeding.</div>\n<br/>\n";
echo "<div><b>Both</b> - refers both transplanting and wet direct seeding</div>\n";
echo "<br/>\n";
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
echo displayNoteRounding();
echo displayNoteIrrigate();
?>
<br/>
<div>Data accessed at <?php echo date('l jS \of F Y h:i:s A');?></div>
<?php echo displayNoteContact(); ?>
</div>
<?php
require_once("../includes/export.php");
?>