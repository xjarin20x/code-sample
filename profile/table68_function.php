<?php
	require_once("../includes/headeralt.php");
?>		
<div id="tableData">
<?php	
	$provinces = disinfect_var($_POST['provinces']);
	$seasons = disinfect_var($_POST['seasons']);

	$content=count($provinces);
	
	$stmt= $total_stmt = "";
	$total = $codename = $percent = $divisor = 0;
	
	$lop = implode(',', $provinces);
	$los = implode(',', $seasons);
	$region = 0;	
	
	foreach($provinces as $province){
	$season_names = $headings = $footnotes = array();
	$mean_hh = array();
	$belowThreshold = $aboveThreshold = $povertyLine = array();
	$n_stat = $total_n = array();
	$mean_count = 0;
	
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	foreach($seasons as $season){
		
		$total_farmers = array();
		$count = $t_constant = $total_temp = 0;
		
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
			$all_stmt= $conn->prepare("SELECT AVG(matrix_rectype1.hh_male), AVG(matrix_rectype1.hh_female) FROM matrix_rectype1 JOIN matrix_rectype2 WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND matrix_rectype1.season = ?");
			$irri_stmt= $conn->prepare("SELECT AVG(matrix_rectype1.hh_male), AVG(matrix_rectype1.hh_female) FROM matrix_rectype1 JOIN matrix_rectype2, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 1");
			$rain_stmt= $conn->prepare("SELECT AVG(matrix_rectype1.hh_male), AVG(matrix_rectype1.hh_female) FROM matrix_rectype1 JOIN matrix_rectype2, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 0");
			$all_stmt->bind_param("s", $season);
			$irri_stmt->bind_param("s", $season);
			$rain_stmt->bind_param("s", $season);
		}
		else{
			$all_stmt= $conn->prepare("SELECT AVG(matrix_rectype1.hh_male), AVG(matrix_rectype1.hh_female) FROM matrix_rectype1 JOIN matrix_rectype2 WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?");
			$irri_stmt= $conn->prepare("SELECT AVG(matrix_rectype1.hh_male), AVG(matrix_rectype1.hh_female) FROM matrix_rectype1 JOIN matrix_rectype2, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime = 1");
			$rain_stmt= $conn->prepare("SELECT AVG(matrix_rectype1.hh_male), AVG(matrix_rectype1.hh_female) FROM matrix_rectype1 JOIN matrix_rectype2, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime = 0");
			$all_stmt->bind_param("ss", $season, $province);
			$irri_stmt->bind_param("ss", $season, $province);
			$rain_stmt->bind_param("ss", $season, $province);
		}
		
		$all_stmt->execute();
		$all_stmt->store_result();
		$all_stmt->bind_result($male, $female);
		
		while($all_stmt->fetch()){ 
			array_push($mean_hh, round($male) + round($female));
		}
		
		$irri_stmt->execute();
		$irri_stmt->store_result();
		$irri_stmt->bind_result($male, $female);
		
		while($irri_stmt->fetch()){ 
			array_push($mean_hh, round($male) + round($female));
		}
		
		$rain_stmt->execute();
		$rain_stmt->store_result();
		$rain_stmt->bind_result($male, $female);
		
		while($rain_stmt->fetch()){ 
			array_push($mean_hh, round($male) + round($female));
		}
		
		if ($season < 5){
			$divisor = 12;
		}
		else {
			$divisor = 6;
		}
		
		$findlegend = $conn->prepare("SELECT DISTINCT season FROM legend_pthreshold ORDER BY season DESC");
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
		
		if($province==999){
			$all_stmt= $conn->prepare("
			SELECT mpth.pthreshold, 
			IF((
				(
					(
					matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income
					) / ? / ?
				) * 12
			) < mpth.pthreshold, 1, 0) AS bthreshold,
			COUNT(*) AS freq
			FROM matrix_rectype1 
			JOIN matrix_rectype2, (SELECT * FROM legend_pthreshold WHERE season = ?) mpth
			WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND 
			mpth.province = 999 AND matrix_rectype1.season = ? 
			AND matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income > 1 GROUP BY bthreshold
			");
			$irri_stmt= $conn->prepare("
			SELECT mpth.pthreshold, 
			IF((
				(
					(
					matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income
					) / ? / ?
				) * 12
			) < mpth.pthreshold, 1, 0) AS bthreshold,
			COUNT(*) AS freq
			FROM matrix_rectype1 
			JOIN matrix_rectype2, (SELECT * FROM legend_pthreshold WHERE season = ?) mpth, matrix_irrigation
			WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND 
			mpth.province = 999 AND matrix_rectype1.season = ? AND 
			matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND 
			matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income > 1 GROUP BY bthreshold
			");
			$rain_stmt= $conn->prepare("
			SELECT mpth.pthreshold, 
			IF((
				(
					(
					matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income
					) / ? / ?
				) * 12
			) < mpth.pthreshold, 1, 0) AS bthreshold,
			COUNT(*) AS freq
			FROM matrix_rectype1 
			JOIN matrix_rectype2, (SELECT * FROM legend_pthreshold WHERE season = ?) mpth, matrix_irrigation
			WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND 
			mpth.province = 999 AND matrix_rectype1.season = ? AND 
			matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND 
			matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income > 1 GROUP BY bthreshold
			");
			$all_stmt->bind_param("ssss", $mean_hh[$mean_count], $divisor, $legend, $season); $mean_count++;
			$irri_stmt->bind_param("ssss", $mean_hh[$mean_count], $divisor, $legend, $season); $mean_count++;
			$rain_stmt->bind_param("ssss", $mean_hh[$mean_count], $divisor, $legend, $season); $mean_count++;
		}
		else{
			$all_stmt= $conn->prepare("
			SELECT mpth.pthreshold, 
			IF((
				(
					(
					matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income
					) / ? / ?
				) * 12
			) < mpth.pthreshold, 1, 0) AS bthreshold,
			COUNT(*) AS freq
			FROM matrix_rectype1 
			JOIN matrix_rectype2, (SELECT * FROM legend_pthreshold WHERE season = ?) mpth
			WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND 
			matrix_rectype2.province = mpth.province AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND 
			matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income > 1 GROUP BY bthreshold
			");
			$irri_stmt= $conn->prepare("
			SELECT mpth.pthreshold, 
			IF((
				(
					(
					matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income
					) / ? / ?
				) * 12
			) < mpth.pthreshold, 1, 0) AS bthreshold,
			COUNT(*) AS freq
			FROM matrix_rectype1 
			JOIN matrix_rectype2, (SELECT * FROM legend_pthreshold WHERE season = ?) mpth, matrix_irrigation
			WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND 
			matrix_rectype2.province = mpth.province AND matrix_rectype1.season = ? AND mpth.province = ? AND 
			matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND 
			matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income > 1 GROUP BY bthreshold
			");
			$rain_stmt= $conn->prepare("
			SELECT mpth.pthreshold, 
			IF((
				(
					(
					matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income
					) / ? / ?
				) * 12
			) < mpth.pthreshold, 1, 0) AS bthreshold,
			COUNT(*) AS freq
			FROM matrix_rectype1 
			JOIN matrix_rectype2, (SELECT * FROM legend_pthreshold WHERE season = ?) mpth, matrix_irrigation
			WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND 
			matrix_rectype2.province = mpth.province AND matrix_rectype1.season = ? AND mpth.province = ? AND 
			matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND 
			matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income > 1 GROUP BY bthreshold
			");
			$all_stmt->bind_param("sssss", $mean_hh[$mean_count], $divisor, $legend, $season, $province); $mean_count++;
			$irri_stmt->bind_param("sssss", $mean_hh[$mean_count], $divisor, $legend, $season, $province); $mean_count++;
			$rain_stmt->bind_param("sssss", $mean_hh[$mean_count], $divisor, $legend, $season, $province); $mean_count++;
		}
		
		$res_arr = array();
		$all_stmt->execute();
		$all_stmt->store_result();
		while($row = fetch_get_result_alt($all_stmt)) {
			$row = array_values($row);
			array_push($res_arr, $row);
		}
		// PHP 5.3
		// $res = $all_stmt->get_result();
		// while($row = $res->fetch_array(MYSQLI_NUM)) {
			// $res_arr[] = $row;
		// }

		foreach ($res_arr as $key => $value){
			$t_constant = $value[0];
			if($value[1] == 1){
				array_push($belowThreshold, $value[2]);
			}
			else if($value[1] == 0){
				array_push($aboveThreshold, $value[2]);
			}
			$total_temp = $total_temp + $value[2];
		}
		
		if(count($res_arr) == 0){
			array_push($belowThreshold, 0);
			array_push($aboveThreshold, 0);
		}
		elseif (count($res_arr) == 1){
			if($res_arr[0][1] == 0){
				array_push($belowThreshold, 0);
			}
			else{
				array_push($aboveThreshold, 0);
			}
		}
		
		array_push($total_farmers, $total_temp);
		$total_temp = 0;
		$count++;
		
		$res_arr = array();
		$irri_stmt->execute();
		$irri_stmt->store_result();
		while($row = fetch_get_result_alt($irri_stmt)) {
			$row = array_values($row);
			array_push($res_arr, $row);
		}
		// PHP 5.3
		// $res = $irri_stmt->get_result();
		// while($row = $res->fetch_array(MYSQLI_NUM)) {
			// $res_arr[] = $row;
		// }
		
		foreach ($res_arr as $key => $value){
			$t_constant = $value[0];
			if($value[1] == 1){
				array_push($belowThreshold, $value[2]);
			}
			else if($value[1] == 0){
				array_push($aboveThreshold, $value[2]);
			}
			$total_temp = $total_temp + $value[2];
		}
		
		if(count($res_arr) == 0){
			array_push($belowThreshold, 0);
			array_push($aboveThreshold, 0);
		}
		elseif (count($res_arr) == 1){
			if($res_arr[0][1] == 0){
				array_push($belowThreshold, 0);
			}
			else{
				array_push($aboveThreshold, 0);
			}
		}
		
		array_push($total_farmers, $total_temp);
		$total_temp = 0;
		$count++;

		$res_arr = array();
		$rain_stmt->execute();
		$rain_stmt->store_result();
		while($row = fetch_get_result_alt($rain_stmt)) {
			$row = array_values($row);
			array_push($res_arr, $row);
		}
		// PHP 5.3
		// $res = $rain_stmt->get_result();
		// while($row = $res->fetch_array(MYSQLI_NUM)) {
			// $res_arr[] = $row;
		// }
		
		foreach ($res_arr as $key => $value){
			$t_constant = $value[0];
			if($value[1] == 1){
				array_push($belowThreshold, $value[2]);
			}
			else if($value[1] == 0){
				array_push($aboveThreshold, $value[2]);
			}
			$total_temp = $total_temp + $value[2];
		}
		
		if(count($res_arr) == 0){
			array_push($belowThreshold, 0);
			array_push($aboveThreshold, 0);
		}
		elseif (count($res_arr) == 1){
			if($res_arr[0][1] == 0){
				array_push($belowThreshold, 0);
			}
			else{
				array_push($aboveThreshold, 0);
			}
		}
		
		array_push($total_farmers, $total_temp);
		$total_temp = 0;
		$count++;
		$res_arr = array();

		array_push($povertyLine, $t_constant);
		$n_stat= array_merge($n_stat, $total_farmers);
		
	}
	$region = 0;			
	$getprovince=$conn->prepare("SELECT province_name, region FROM legend_province WHERE province_id = ?");
	$getprovince->bind_param("s", $province);
	$getprovince->execute();
	$getprovince->store_result();
	$getprovince->bind_result($name, $region);
	$getprovince->fetch();
	$count_columns=(count($seasons)*3)+1;
	
	$title = "<b>Annual per capita provincial poverty threshold (₱)*</b>";
	
	if($province == "999"){
		$title = "<b>Annual per capita poverty threshold (₱)*</b>";
	}
	
	create_header($name, $season_names, 3);
	echo "<tbody>\n";
	create_headings("Items", $headings);
	create_special_rows("", array_formatting($n_stat,"(n = ",")"), 1);
	create_humanized_special_numbers($title, $povertyLine, 3, 2);
	echo "<tr>\n<td class='header4'></td><td class='header4 center' colspan=".($count_columns-1).">(Percent of farm households)</td>\n</tr>\n";
	create_average($aboveThreshold, $n_stat, "Above threshold", 0);
	create_average($belowThreshold, $n_stat, "Below threshold", 0);
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
		if ($n_stat[$i*3] < $total_n[$i]){
			echo '<div>Excludes missing response during '. $footnotes[$i] .' harvest  (n='. ($total_n[$i]-$n_stat[$i*3]) .').</div>';
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
$all_stmt->close();
$getprovince->close();
$stmt->close();

echo "<br/>" . displayNoteRounding();
echo displayNoteIrrigate();
?>
<br/>
<div>*<b>Source</b>: Philippine Statistics Authority (2016). 2015 Full Year Official Poverty Statistics of the Philippines. Quezon City, Philippines</div><br/>
<div>Data accessed at <?php echo date('l jS \of F Y h:i:s A');?></div>
<?php
echo displayNoteContact();
echo "</div>";
require_once("../includes/export.php");
?>