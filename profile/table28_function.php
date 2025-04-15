<?php
	require_once("../includes/headeralt.php");
?>	
<div id="tableData">
<?php
	$provinces = disinfect_var($_POST['provinces']);
	$seasons = disinfect_var($_POST['seasons']);

	$content=count($provinces);
	
	$stmt = "";
	$total = 0;
	$region = 0;	
	
	foreach($provinces as $province){
	$season_names = $headings = $footnotes = array();
	$total_farmers = $below_significance = array();
	$counter = -1;
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	
	$pest_array = array();
	foreach($seasons as $season){
	$n_farmers = array();
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
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype9 WHERE matrix_rectype1.region = matrix_rectype9.region AND matrix_rectype1.province = matrix_rectype9.province AND matrix_rectype1.municipality = matrix_rectype9.municipality AND matrix_rectype1.barangay = matrix_rectype9.barangay AND matrix_rectype1.hh_number = matrix_rectype9.hh_number AND matrix_rectype1.season = matrix_rectype9.season AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype9, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype9.region AND matrix_rectype1.province = matrix_rectype9.province AND matrix_rectype1.municipality = matrix_rectype9.municipality AND matrix_rectype1.barangay = matrix_rectype9.barangay AND matrix_rectype1.hh_number = matrix_rectype9.hh_number AND matrix_rectype1.season = matrix_rectype9.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 1 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype9, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype9.region AND matrix_rectype1.province = matrix_rectype9.province AND matrix_rectype1.municipality = matrix_rectype9.municipality AND matrix_rectype1.barangay = matrix_rectype9.barangay AND matrix_rectype1.hh_number = matrix_rectype9.hh_number AND matrix_rectype1.season = matrix_rectype9.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 0 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype9 WHERE matrix_rectype1.region = matrix_rectype9.region AND matrix_rectype1.province = matrix_rectype9.province AND matrix_rectype1.municipality = matrix_rectype9.municipality AND matrix_rectype1.barangay = matrix_rectype9.barangay AND matrix_rectype1.hh_number = matrix_rectype9.hh_number AND matrix_rectype1.season = matrix_rectype9.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype9, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype9.region AND matrix_rectype1.province = matrix_rectype9.province AND matrix_rectype1.municipality = matrix_rectype9.municipality AND matrix_rectype1.barangay = matrix_rectype9.barangay AND matrix_rectype1.hh_number = matrix_rectype9.hh_number AND matrix_rectype1.season = matrix_rectype9.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime = 1 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype9, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype9.region AND matrix_rectype1.province = matrix_rectype9.province AND matrix_rectype1.municipality = matrix_rectype9.municipality AND matrix_rectype1.barangay = matrix_rectype9.barangay AND matrix_rectype1.hh_number = matrix_rectype9.hh_number AND matrix_rectype1.season = matrix_rectype9.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime = 0 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
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
		$all_stmt= $conn->prepare("SELECT p.aingredient, COUNT(p.aingredient) AS N, p.type FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype9.aingredient, matrix_rectype9.type FROM matrix_rectype1 JOIN matrix_rectype9 WHERE matrix_rectype1.region = matrix_rectype9.region AND matrix_rectype1.province = matrix_rectype9.province AND matrix_rectype1.municipality = matrix_rectype9.municipality AND matrix_rectype1.barangay = matrix_rectype9.barangay AND matrix_rectype1.hh_number = matrix_rectype9.hh_number AND matrix_rectype1.season = matrix_rectype9.season AND matrix_rectype1.season = ?) p WHERE p.type IN ('H', 'I', 'M', 'R', 'U') GROUP BY p.aingredient, p.type ORDER BY p.type ASC;");
		$irri_stmt= $conn->prepare("SELECT p.aingredient, COUNT(p.aingredient) AS N, p.type FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype9.aingredient, matrix_rectype9.type FROM matrix_rectype1 JOIN matrix_rectype9, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype9.region AND matrix_rectype1.province = matrix_rectype9.province AND matrix_rectype1.municipality = matrix_rectype9.municipality AND matrix_rectype1.barangay = matrix_rectype9.barangay AND matrix_rectype1.hh_number = matrix_rectype9.hh_number AND matrix_rectype1.season = matrix_rectype9.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 1) p WHERE p.type IN ('H', 'I', 'M', 'R', 'U') GROUP BY p.aingredient, p.type ORDER BY p.type ASC;");
		$rain_stmt= $conn->prepare("SELECT p.aingredient, COUNT(p.aingredient) AS N, p.type FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype9.aingredient, matrix_rectype9.type FROM matrix_rectype1 JOIN matrix_rectype9, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype9.region AND matrix_rectype1.province = matrix_rectype9.province AND matrix_rectype1.municipality = matrix_rectype9.municipality AND matrix_rectype1.barangay = matrix_rectype9.barangay AND matrix_rectype1.hh_number = matrix_rectype9.hh_number AND matrix_rectype1.season = matrix_rectype9.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 0) p WHERE p.type IN ('H', 'I', 'M', 'R', 'U') GROUP BY p.aingredient, p.type ORDER BY p.type ASC;");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT p.aingredient, COUNT(p.aingredient) AS N, p.type FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype9.aingredient, matrix_rectype9.type FROM matrix_rectype1 JOIN matrix_rectype9 WHERE matrix_rectype1.region = matrix_rectype9.region AND matrix_rectype1.province = matrix_rectype9.province AND matrix_rectype1.municipality = matrix_rectype9.municipality AND matrix_rectype1.barangay = matrix_rectype9.barangay AND matrix_rectype1.hh_number = matrix_rectype9.hh_number AND matrix_rectype1.season = matrix_rectype9.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) p WHERE p.type IN ('H', 'I', 'M', 'R', 'U') GROUP BY p.aingredient, p.type ORDER BY p.type ASC;");
		$irri_stmt= $conn->prepare("SELECT p.aingredient, COUNT(p.aingredient) AS N, p.type FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype9.aingredient, matrix_rectype9.type FROM matrix_rectype1 JOIN matrix_rectype9, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype9.region AND matrix_rectype1.province = matrix_rectype9.province AND matrix_rectype1.municipality = matrix_rectype9.municipality AND matrix_rectype1.barangay = matrix_rectype9.barangay AND matrix_rectype1.hh_number = matrix_rectype9.hh_number AND matrix_rectype1.season = matrix_rectype9.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime = 1) p WHERE p.type IN ('H', 'I', 'M', 'R', 'U') GROUP BY p.aingredient, p.type ORDER BY p.type ASC;");
		$rain_stmt= $conn->prepare("SELECT p.aingredient, COUNT(p.aingredient) AS N, p.type FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype9.aingredient, matrix_rectype9.type FROM matrix_rectype1 JOIN matrix_rectype9, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype9.region AND matrix_rectype1.province = matrix_rectype9.province AND matrix_rectype1.municipality = matrix_rectype9.municipality AND matrix_rectype1.barangay = matrix_rectype9.barangay AND matrix_rectype1.hh_number = matrix_rectype9.hh_number AND matrix_rectype1.season = matrix_rectype9.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime = 0) p WHERE p.type IN ('H', 'I', 'M', 'R', 'U') GROUP BY p.aingredient, p.type ORDER BY p.type ASC;");
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
		
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($ingredient, $quantity, $pest_type);
	
	$counter++;
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){
			$ingredient = TRIM($ingredient);
			if(!isset($pest_array[$pest_type])){
				$pest_array[$pest_type] = array();
			}
			if(!isset($pest_array[$pest_type][$ingredient])){
				$pest_array[$pest_type][$ingredient] = array();
			}
			$pest_array[$pest_type][$ingredient][$counter] = ($quantity / $total_farmers[$counter]) * 100;
		}
	}
	
	$counter++;
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($ingredient, $quantity, $pest_type);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){
			$ingredient = TRIM($ingredient);
			if(!isset($pest_array[$pest_type])){
				$pest_array[$pest_type] = array();
			}
			if(!isset($pest_array[$pest_type][$ingredient])){
				$pest_array[$pest_type][$ingredient] = array();
			}
			$pest_array[$pest_type][$ingredient][$counter] = ($quantity / $total_farmers[$counter]) * 100;
		}
	}
	
	$counter++;
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($ingredient, $quantity, $pest_type);
	
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){
			$ingredient = TRIM($ingredient);
			if(!isset($pest_array[$pest_type])){
				$pest_array[$pest_type] = array();
			}
			if(!isset($pest_array[$pest_type][$ingredient])){
				$pest_array[$pest_type][$ingredient] = array();
			}
			$pest_array[$pest_type][$ingredient][$counter] = ($quantity / $total_farmers[$counter]) * 100;
		}
	}
	
	$all_stmt->close();
	$irri_stmt->close();
	$rain_stmt->close();
	}
	$region = 0;
	$forprint = $names = $non_user = array();
	$getprovince=$conn->prepare("SELECT province_name, region FROM legend_province WHERE province_id = ?");
	$getprovince->bind_param("s", $province);
	$getprovince->execute();
	$getprovince->store_result();
	$getprovince->bind_result($name, $region);
	$getprovince->fetch();
	$count_columns=(count($seasons)*3)+1;
	$pest_types = array('H', 'I', 'M', 'R', 'U');
	
	if( !empty($pest_array) ){
	create_header($name, $season_names, 3);
	echo "<tbody>\n";
	create_headings("Active Ingredients*", $headings);
	create_special_rows("", array_formatting($total_farmers,"(n = ",")"), 1);
	echo "<tr>\n<td></td><td class='center' colspan=".($count_columns-1).">(Percent of farmers)</td>\n</tr>\n";
	
	foreach (array_keys($pest_array) as $value){
		switch($value){
			case "H": echo "<tr>\n<td class='header4 left bold' colspan=".$count_columns.">Herbicides</td>\n</tr>\n"; break;
			case "I": echo "<tr>\n<td class='header4 left bold' colspan=".$count_columns.">Insecticides</td>\n</tr>\n"; break;
			case "M": echo "<tr>\n<td class='header4 left bold' colspan=".$count_columns.">Molluscicides</td>\n</tr>\n"; break;
			case "R": echo "<tr>\n<td class='header4 left bold' colspan=".$count_columns.">Rodenticides</td>\n</tr>\n"; break;
			case "U": echo "<tr>\n<td class='header4 left bold' colspan=".$count_columns.">Fungicide</td>\n</tr>\n"; break;	
			default: echo "<tr>\n<td class='header4 left bold' colspan=".$count_columns.">Fungicide</td>\n</tr>\n"; break;
		}
		foreach ($pest_array[$value] as $key => &$sec_arr){
			$mark = FALSE;
			for($i = 0; $i < $counter+1; $i++){
				if(!isset($sec_arr[$i])){
					$sec_arr[$i] = "--";
				}
			}
			if(array_sum($sec_arr) >= (0.5 * 3 * count($seasons))){
				$mark = TRUE;
			}
			$forprint[$key] = $mark;
			ksort($sec_arr);
		}
		uasort($pest_array[$value], "compareArray");
		$printed = FALSE;
		foreach ($pest_array[$value] as $key => $value){
			if($forprint[$key] == "TRUE"){
				create_numbers($key, $value, 2);
				$printed = TRUE;
			}
		}
		if(!$printed){
			echo "<tr>\n<td class='center' colspan=".$count_columns.">No significant active ingredient under this category.</td>\n</tr>\n";
		}
	}
	
	/**
	echo "<pre>";
	print_r($pest_array);
	echo "</pre>";
	
	*/
	
	echo "</tbody>\n";
	echo "</table>\n";
	}
	/**
	if( !empty($tenure_array) ){
	create_header($name, $season_names, 3);
	echo "<tbody>\n";
	create_headings("Prevalent Pests*", $headings);
	create_special_rows("", array_formatting($total_farmers,"(n = ",")"), 1);
	echo "<tr>\n<td></td><td class='center' colspan=".($count_columns-1).">(Percent of farmers)</td>\n</tr>\n";
	echo "<tr>\n<td class='header4 left bold' colspan=".$count_columns.">Weeds</td>\n</tr>\n";
	foreach ($tenure_array as $key => &$sec_arr){
		$mark = FALSE;
		for($i = 0; $i < $counter+1; $i++){
			if(!isset($sec_arr[$i])){
				$sec_arr[$i] = 0;
			}
			if($sec_arr[$i] > $below_significance[$i] && $sec_arr[$i] > (count($seasons) * 1)){
				$mark = TRUE;
			}
		}
		$forprint[$key]=$mark;
		ksort($sec_arr);
	}
	uasort($tenure_array, "compareArray");
	foreach ($tenure_array as $key => $value){
		if($forprint[$key] == "TRUE"){
			create_average($tenure_array[$key], $total_farmers, ucfirst(strtolower($key)), 0);
		}
		else{
			create_average($tenure_array[$key], $total_farmers, ucfirst(strtolower($key)), 1);
		}
	}
	echo "</tbody>\n";
	echo "</table>\n";
	}
	else{
	echo "</table>\n";
		if(count($season_names)==1) {
			$seas = $season_names[0];
		}
		else {
			$seas = concatenate($season_names);
		}
		echo "<div class='bold'>Parameters for your requested query are not applicable for ". $name ." during the ". $seas .".</div><br/>";
	}
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
			echo '<div>Excludes missing response during '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$total_farmers[$i*3]) .')</div>';
		}
	}
	echo "<br/>\n";
	*/
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
echo displayNoteIrrigate();
?>
<br/>
<div>Data accessed at <?php echo date('l jS \of F Y h:i:s A');?></div>
<?php echo displayNoteContact(); ?>
</div>
<?php
require_once("../includes/export.php");
?>