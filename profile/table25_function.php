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
	$season_names = $headings = $footnotes = $total_n = $total_farmers = $fert_freq = array();
	$counter = 0;
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
	
	if($province==999){
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_source IS NOT NULL) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_source IS NOT NULL AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5)) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_source IS NOT NULL AND matrix_irrigation.irrigation_source = 0) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_source IS NOT NULL) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_source IS NOT NULL AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5)) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_source IS NOT NULL AND matrix_irrigation.irrigation_source = 0) a");
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

	$findlegend = $conn->prepare("SELECT DISTINCT season FROM legend_fertilizer ORDER BY fert_id DESC");
	$findlegend->execute();
	$findlegend->store_result();
	$findlegend->bind_result($this);
	$season_pool = array();
	while($findlegend->fetch()){
		array_push($season_pool, $this);
	}
	$findlegend->close();
	$fertlegend = 0;
	for($i=$season; $i > 0; $i--){
		if(in_array($i, $season_pool)) {
			$fertlegend = $i;
			break;
		}
	}
	if($province==999){
		$all_stmt= $conn->prepare("
		SELECT 
		fert.fertid, 
		IFNULL(legend.fert_grade, fert.fertid) as grade, 
		COUNT(fert.fertid) 
		FROM 
		(SELECT 
			r.fertilizer, 
			IFNULL(f.fert_id, r.fertilizer) as fertid 
			FROM 
			(SELECT DISTINCT 
				matrix_rectype1.region, 
				matrix_rectype1.province, 
				matrix_rectype1.municipality, 
				matrix_rectype1.barangay, 
				matrix_rectype1.hh_number, 
				matrix_rectype1.season, matrix_rectype7.fertilizer 
				FROM 
				matrix_rectype1 
				JOIN 
				matrix_rectype7 WHERE 
				matrix_rectype1.region = matrix_rectype7.region AND 
				matrix_rectype1.province = matrix_rectype7.province AND 
				matrix_rectype1.municipality = matrix_rectype7.municipality 
				AND matrix_rectype1.barangay = matrix_rectype7.barangay AND 
				matrix_rectype1.hh_number = matrix_rectype7.hh_number AND 
				matrix_rectype1.season = matrix_rectype7.season AND 
				matrix_rectype1.season = ?) r 
			LEFT JOIN 
			(SELECT 
				fert_id, 
				fert_grade 
				FROM 
				legend_fertilizer 
				WHERE season = ?) f 
			ON r.fertilizer = f.fert_grade) fert 
		LEFT JOIN 
		(SELECT 
			fert_id, 
			fert_grade 
			FROM 
			legend_fertilizer 
			WHERE 
			season = ? 
			AND fert_id IS NOT NULL GROUP BY fert_id) legend 
		ON fert.fertid = legend.fert_id 
		GROUP BY grade 
		ORDER BY COUNT(fert.fertid) DESC
		");
		
		$irri_stmt= $conn->prepare("
		SELECT 
		fert.fertid, 
		IFNULL(legend.fert_grade, fert.fertid) as grade, 
		COUNT(fert.fertid) 
		FROM 
		(SELECT 
			r.fertilizer, 
			IFNULL(f.fert_id, r.fertilizer) as fertid 
			FROM 
			(SELECT DISTINCT 
				matrix_rectype1.region, 
				matrix_rectype1.province, 
				matrix_rectype1.municipality, 
				matrix_rectype1.barangay, 
				matrix_rectype1.hh_number, 
				matrix_rectype1.season, matrix_rectype7.fertilizer 
				FROM 
				matrix_rectype1 
				JOIN 
				matrix_rectype7, 
				matrix_irrigation WHERE 
				matrix_rectype1.region = matrix_rectype7.region AND 
				matrix_rectype1.province = matrix_rectype7.province AND 
				matrix_rectype1.municipality = matrix_rectype7.municipality 
				AND matrix_rectype1.barangay = matrix_rectype7.barangay AND 
				matrix_rectype1.hh_number = matrix_rectype7.hh_number AND 
				matrix_rectype1.season = matrix_rectype7.season AND 
				(matrix_irrigation.irrigation_source = 1 OR 
					matrix_irrigation.irrigation_source = 2 OR 
					matrix_irrigation.irrigation_source = 3 OR 
					matrix_irrigation.irrigation_source = 4 OR 
					matrix_irrigation.irrigation_source = 5) AND 
				matrix_rectype1.region = matrix_irrigation.region AND 
				matrix_rectype1.province = matrix_irrigation.province AND 
				matrix_rectype1.municipality = matrix_irrigation.municipality AND 
				matrix_rectype1.barangay = matrix_irrigation.barangay AND 
				matrix_rectype1.hh_number = matrix_irrigation.hh_number AND 
				matrix_rectype1.season = matrix_irrigation.season AND 
				matrix_rectype1.season = ?) r 
			LEFT JOIN 
			(SELECT 
				fert_id, 
				fert_grade 
				FROM 
				legend_fertilizer 
				WHERE season = ?) f 
			ON r.fertilizer = f.fert_grade) fert 
		LEFT JOIN 
		(SELECT 
			fert_id, 
			fert_grade 
			FROM 
			legend_fertilizer 
			WHERE 
			season = ? 
			AND fert_id IS NOT NULL GROUP BY fert_id) legend 
		ON fert.fertid = legend.fert_id 
		GROUP BY grade 
		ORDER BY COUNT(fert.fertid) DESC
		");

		$rain_stmt= $conn->prepare("
		SELECT 
		fert.fertid, 
		IFNULL(legend.fert_grade, fert.fertid) as grade, 
		COUNT(fert.fertid) 
		FROM 
		(SELECT 
			r.fertilizer, 
			IFNULL(f.fert_id, r.fertilizer) as fertid 
			FROM 
			(SELECT DISTINCT 
				matrix_rectype1.region, 
				matrix_rectype1.province, 
				matrix_rectype1.municipality, 
				matrix_rectype1.barangay, 
				matrix_rectype1.hh_number, 
				matrix_rectype1.season, matrix_rectype7.fertilizer 
				FROM 
				matrix_rectype1 
				JOIN 
				matrix_rectype7, 
				matrix_irrigation 
				WHERE 
				matrix_rectype1.region = matrix_rectype7.region AND 
				matrix_rectype1.province = matrix_rectype7.province AND 
				matrix_rectype1.municipality = matrix_rectype7.municipality 
				AND matrix_rectype1.barangay = matrix_rectype7.barangay AND 
				matrix_rectype1.hh_number = matrix_rectype7.hh_number AND 
				matrix_rectype1.season = matrix_rectype7.season AND 
				matrix_irrigation.irrigation_source = 0 AND 
				matrix_rectype1.region = matrix_irrigation.region AND 
				matrix_rectype1.province = matrix_irrigation.province AND 
				matrix_rectype1.municipality = matrix_irrigation.municipality AND 
				matrix_rectype1.barangay = matrix_irrigation.barangay AND 
				matrix_rectype1.hh_number = matrix_irrigation.hh_number AND 
				matrix_rectype1.season = matrix_irrigation.season AND 
				matrix_rectype1.season = ?) r 
			LEFT JOIN 
			(SELECT 
				fert_id, 
				fert_grade 
				FROM 
				legend_fertilizer 
				WHERE season = ?) f 
			ON r.fertilizer = f.fert_grade) fert 
		LEFT JOIN 
		(SELECT 
			fert_id, 
			fert_grade 
			FROM 
			legend_fertilizer 
			WHERE 
			season = ? 
			AND fert_id IS NOT NULL GROUP BY fert_id) legend 
		ON fert.fertid = legend.fert_id 
		GROUP BY grade 
		ORDER BY COUNT(fert.fertid) DESC
		");
		$all_stmt->bind_param("sss", $season, $fertlegend, $fertlegend);
		$irri_stmt->bind_param("sss", $season, $fertlegend, $fertlegend);
		$rain_stmt->bind_param("sss", $season, $fertlegend, $fertlegend);
	}
	else{
		$all_stmt= $conn->prepare("
		SELECT 
		fert.fertid, 
		IFNULL(legend.fert_grade, fert.fertid) as grade, 
		COUNT(fert.fertid) 
		FROM 
		(SELECT 
			r.fertilizer, 
			IFNULL(f.fert_id, r.fertilizer) as fertid 
			FROM 
			(SELECT DISTINCT 
				matrix_rectype1.region, 
				matrix_rectype1.province, 
				matrix_rectype1.municipality, 
				matrix_rectype1.barangay, 
				matrix_rectype1.hh_number, 
				matrix_rectype1.season, matrix_rectype7.fertilizer 
				FROM 
				matrix_rectype1 
				JOIN 
				matrix_rectype7 WHERE 
				matrix_rectype1.region = matrix_rectype7.region AND 
				matrix_rectype1.province = matrix_rectype7.province AND 
				matrix_rectype1.municipality = matrix_rectype7.municipality 
				AND matrix_rectype1.barangay = matrix_rectype7.barangay AND 
				matrix_rectype1.hh_number = matrix_rectype7.hh_number AND 
				matrix_rectype1.season = matrix_rectype7.season AND 
				matrix_rectype1.season = ? AND 
				matrix_rectype1.province = ?) r 
			LEFT JOIN 
			(SELECT 
				fert_id, 
				fert_grade 
				FROM 
				legend_fertilizer 
				WHERE season = ?) f 
			ON r.fertilizer = f.fert_grade) fert 
		LEFT JOIN 
		(SELECT 
			fert_id, 
			fert_grade 
			FROM 
			legend_fertilizer 
			WHERE 
			season = ? 
			AND fert_id IS NOT NULL GROUP BY fert_id) legend 
		ON fert.fertid = legend.fert_id 
		GROUP BY grade 
		ORDER BY COUNT(fert.fertid) DESC
		");
		
		$irri_stmt= $conn->prepare("
		SELECT 
		fert.fertid, 
		IFNULL(legend.fert_grade, fert.fertid) as grade, 
		COUNT(fert.fertid) 
		FROM 
		(SELECT 
			r.fertilizer, 
			IFNULL(f.fert_id, r.fertilizer) as fertid 
			FROM 
			(SELECT DISTINCT 
				matrix_rectype1.region, 
				matrix_rectype1.province, 
				matrix_rectype1.municipality, 
				matrix_rectype1.barangay, 
				matrix_rectype1.hh_number, 
				matrix_rectype1.season, matrix_rectype7.fertilizer 
				FROM 
				matrix_rectype1 
				JOIN 
				matrix_rectype7, 
				matrix_irrigation WHERE 
				matrix_rectype1.region = matrix_rectype7.region AND 
				matrix_rectype1.province = matrix_rectype7.province AND 
				matrix_rectype1.municipality = matrix_rectype7.municipality 
				AND matrix_rectype1.barangay = matrix_rectype7.barangay AND 
				matrix_rectype1.hh_number = matrix_rectype7.hh_number AND 
				matrix_rectype1.season = matrix_rectype7.season AND 
				(matrix_irrigation.irrigation_source = 1 OR 
					matrix_irrigation.irrigation_source = 2 OR 
					matrix_irrigation.irrigation_source = 3 OR 
					matrix_irrigation.irrigation_source = 4 OR 
					matrix_irrigation.irrigation_source = 5) AND 
				matrix_rectype1.region = matrix_irrigation.region AND 
				matrix_rectype1.province = matrix_irrigation.province AND 
				matrix_rectype1.municipality = matrix_irrigation.municipality AND 
				matrix_rectype1.barangay = matrix_irrigation.barangay AND 
				matrix_rectype1.hh_number = matrix_irrigation.hh_number AND 
				matrix_rectype1.season = matrix_irrigation.season AND 
				matrix_rectype1.season = ? AND 
				matrix_rectype1.province = ?) r 
			LEFT JOIN 
			(SELECT 
				fert_id, 
				fert_grade 
				FROM 
				legend_fertilizer 
				WHERE season = ?) f 
			ON r.fertilizer = f.fert_grade) fert 
		LEFT JOIN 
		(SELECT 
			fert_id, 
			fert_grade 
			FROM 
			legend_fertilizer 
			WHERE 
			season = ? 
			AND fert_id IS NOT NULL GROUP BY fert_id) legend 
		ON fert.fertid = legend.fert_id 
		GROUP BY grade 
		ORDER BY COUNT(fert.fertid) DESC
		");

		$rain_stmt= $conn->prepare("
		SELECT 
		fert.fertid, 
		IFNULL(legend.fert_grade, fert.fertid) as grade, 
		COUNT(fert.fertid) 
		FROM 
		(SELECT 
			r.fertilizer, 
			IFNULL(f.fert_id, r.fertilizer) as fertid 
			FROM 
			(SELECT DISTINCT 
				matrix_rectype1.region, 
				matrix_rectype1.province, 
				matrix_rectype1.municipality, 
				matrix_rectype1.barangay, 
				matrix_rectype1.hh_number, 
				matrix_rectype1.season, matrix_rectype7.fertilizer 
				FROM 
				matrix_rectype1 
				JOIN 
				matrix_rectype7, 
				matrix_irrigation 
				WHERE 
				matrix_rectype1.region = matrix_rectype7.region AND 
				matrix_rectype1.province = matrix_rectype7.province AND 
				matrix_rectype1.municipality = matrix_rectype7.municipality 
				AND matrix_rectype1.barangay = matrix_rectype7.barangay AND 
				matrix_rectype1.hh_number = matrix_rectype7.hh_number AND 
				matrix_rectype1.season = matrix_rectype7.season AND 
				matrix_irrigation.irrigation_source = 0 AND 
				matrix_rectype1.region = matrix_irrigation.region AND 
				matrix_rectype1.province = matrix_irrigation.province AND 
				matrix_rectype1.municipality = matrix_irrigation.municipality AND 
				matrix_rectype1.barangay = matrix_irrigation.barangay AND 
				matrix_rectype1.hh_number = matrix_irrigation.hh_number AND 
				matrix_rectype1.season = matrix_irrigation.season AND 
				matrix_rectype1.season = ? AND 
				matrix_rectype1.province = ?) r 
			LEFT JOIN 
			(SELECT 
				fert_id, 
				fert_grade 
				FROM 
				legend_fertilizer 
				WHERE season = ?) f 
			ON r.fertilizer = f.fert_grade) fert 
		LEFT JOIN 
		(SELECT 
			fert_id, 
			fert_grade 
			FROM 
			legend_fertilizer 
			WHERE 
			season = ? 
			AND fert_id IS NOT NULL GROUP BY fert_id) legend 
		ON fert.fertid = legend.fert_id 
		GROUP BY grade 
		ORDER BY COUNT(fert.fertid) DESC
		");
		
		$all_stmt->bind_param("ssss", $season, $province, $fertlegend, $fertlegend);
		$irri_stmt->bind_param("ssss", $season, $province, $fertlegend, $fertlegend);
		$rain_stmt->bind_param("ssss", $season, $province, $fertlegend, $fertlegend);
	}
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($fert_id, $fert_grade, $quantity);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$counter] != 0){
				$temp =  ($quantity / $total_farmers[$counter]) * 100;
			}
			$temp_name = strtolower($fert_grade);
			if(!isset($fert_freq[$temp_name])){
				$fert_freq[$temp_name] = array();
			}
			$fert_freq[$temp_name][$counter] = $temp;
		}
	}
	$counter++;
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($fert_id, $fert_grade, $quantity);
	
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$counter] != 0){
				$temp =  ($quantity / $total_farmers[$counter]) * 100;
			}
			$temp_name = strtolower($fert_grade);
			if(!isset($fert_freq[$temp_name])){
				$fert_freq[$temp_name] = array();
			}
			$fert_freq[$temp_name][$counter] = $temp;
		}
	}
	$counter++;
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($fert_id, $fert_grade, $quantity);
	
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$counter] != 0){
				$temp =  ($quantity / $total_farmers[$counter]) * 100;
			}
			$temp_name = strtolower($fert_grade);
			if(!isset($fert_freq[$temp_name])){
				$fert_freq[$temp_name] = array();
			}
			$fert_freq[$temp_name][$counter] = $temp;
		}
	}
	$counter++;
	
	}
	
	$region = 0;
	$forprint = $names = array();
	$getprovince=$conn->prepare("SELECT province_name, region FROM legend_province WHERE province_id = ?");
	$getprovince->bind_param("s", $province);
	$getprovince->execute();
	$getprovince->store_result();
	$getprovince->bind_result($name, $region);
	$getprovince->fetch();
	$count_columns=(count($seasons)*3)+1;
	create_header($name, $season_names, 3);
	create_headings("Fertilizer applied", $headings);
	create_special_rows("", array_formatting($total_farmers,"(n = ",")"), 1);
	echo "<tr>\n<td class='header4'></td><td class='header4 center' colspan=".($count_columns-1).">(Percent of farmers)</td>\n</tr>\n";
	uasort($fert_freq, "compareArray");
	foreach($fert_freq as $key => &$inner) {
		for($i=0; $i<$counter; $i++){
			if(!isset($inner[$i])){
				$inner[$i] = 0;
			}
		}
		ksort($inner);
		if(array_sum($inner) >= (0.5 * (count($seasons) * 3))){
			create_row(ucfirst($key), $inner, 0);
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
	echo '<div>*  Excludes farmers who temporarily stopped farming for '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$total_farmers[$i*3]) .')</div>';
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
echo "<div>Respondents provided multiple answers so the percentage exceeded 100.</div>\n";
$getseason->close();
$getprovince->close();
$stmt->close();
?>
<br/>
<div>Data accessed at <?php echo date('l jS \of F Y h:i:s A');?></div>
<?php echo displayNoteContact(); ?>
</div>
<?php
require_once("../includes/export.php");
?>