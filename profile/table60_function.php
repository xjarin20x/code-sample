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
	$season_names = $headings = $headings2 = $footnotes = array();
	$total_farmers = $total_n = array();
	$counter = 0;
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	$seedclass = array(); 
	for($i=0; $i < 6; $i++){
		array_push($seedclass, array());
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
	
	array_push($headings2, "Mean");	
	array_push($headings2, "SD<sup>a</sup>");
	array_push($headings2, "Mean");	
	array_push($headings2, "SD<sup>a</sup>");
	array_push($headings2, "Mean");	
	array_push($headings2, "SD<sup>a</sup>");

	if($province==999){
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_rectype1.season = ? AND matrix_ioutput.yield != 0) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput, matrix_irrigation WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_irrigation.region = matrix_ioutput.region AND matrix_irrigation.province = matrix_ioutput.province AND matrix_irrigation.municipality = matrix_ioutput.municipality AND matrix_irrigation.barangay = matrix_ioutput.barangay AND matrix_irrigation.hh_number = matrix_ioutput.hh_number AND matrix_irrigation.season = matrix_ioutput.season AND matrix_rectype1.season = ? AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_ioutput.yield != 0) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput, matrix_irrigation WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_irrigation.region = matrix_ioutput.region AND matrix_irrigation.province = matrix_ioutput.province AND matrix_irrigation.municipality = matrix_ioutput.municipality AND matrix_irrigation.barangay = matrix_ioutput.barangay AND matrix_irrigation.hh_number = matrix_ioutput.hh_number AND matrix_irrigation.season = matrix_ioutput.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_source = 0 AND matrix_ioutput.yield != 0) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_ioutput.yield != 0) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput, matrix_irrigation WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_irrigation.region = matrix_ioutput.region AND matrix_irrigation.province = matrix_ioutput.province AND matrix_irrigation.municipality = matrix_ioutput.municipality AND matrix_irrigation.barangay = matrix_ioutput.barangay AND matrix_irrigation.hh_number = matrix_ioutput.hh_number AND matrix_irrigation.season = matrix_ioutput.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND  matrix_ioutput.yield != 0) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput, matrix_irrigation WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_irrigation.region = matrix_ioutput.region AND matrix_irrigation.province = matrix_ioutput.province AND matrix_irrigation.municipality = matrix_ioutput.municipality AND matrix_irrigation.barangay = matrix_ioutput.barangay AND matrix_irrigation.hh_number = matrix_ioutput.hh_number AND matrix_irrigation.season = matrix_ioutput.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_source = 0 AND matrix_ioutput.yield != 0) a");
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
		$all_stmt= $conn->prepare("
		SELECT 
		v.sclass, 
		AVG(v.yield),
		STDDEV_SAMP(v.yield) 
		FROM 
		(
			SELECT 
			sc.sclass, 
			io.yield 
			FROM 
			matrix_rectype6 sc 
			JOIN 
			matrix_ioutput io
			WHERE
			sc.region = io.region AND 
			sc.province = io.province AND 
			sc.municipality = io.municipality AND 
			sc.barangay = io.barangay AND 
			sc.hh_number = io.hh_number AND 
			sc.season = io.season AND 
			sc.season = ?
		) v 
		GROUP BY v.sclass 
		ORDER BY v.sclass ASC
		");
		$irri_stmt= $conn->prepare("
		SELECT 
		v.sclass, 
		AVG(v.yield),
		STDDEV_SAMP(v.yield) 
		FROM 
		(
			SELECT 
			sc.sclass, 
			io.yield 
			FROM 
			matrix_rectype6 sc 
			JOIN 
			matrix_ioutput io, 
			matrix_irrigation ir 
			WHERE
			sc.region = io.region AND 
			sc.province = io.province AND 
			sc.municipality = io.municipality AND 
			sc.barangay = io.barangay AND 
			sc.hh_number = io.hh_number AND 
			sc.region = ir.region AND 
			sc.province = ir.province AND 
			sc.municipality = ir.municipality AND 
			sc.barangay = ir.barangay AND 
			sc.hh_number = ir.hh_number AND 
			sc.season = ir.season AND 
			(ir.irrigation_source = 1 OR 
			 ir.irrigation_source = 2 OR 
			 ir.irrigation_source = 3 OR 
			 ir.irrigation_source = 4 OR 
			 ir.irrigation_source = 5) AND
			sc.season = ?
		) v 
		GROUP BY v.sclass 
		ORDER BY v.sclass ASC
		");
		$rain_stmt= $conn->prepare("
		SELECT 
		v.sclass, 
		AVG(v.yield),
		STDDEV_SAMP(v.yield) 
		FROM 
		(
			SELECT 
			sc.sclass, 
			io.yield 
			FROM 
			matrix_rectype6 sc 
			JOIN 
			matrix_ioutput io, 
			matrix_irrigation ir 
			WHERE
			sc.region = io.region AND 
			sc.province = io.province AND 
			sc.municipality = io.municipality AND 
			sc.barangay = io.barangay AND 
			sc.hh_number = io.hh_number AND 
			sc.region = ir.region AND 
			sc.province = ir.province AND 
			sc.municipality = ir.municipality AND 
			sc.barangay = ir.barangay AND 
			sc.hh_number = ir.hh_number AND 
			sc.season = ir.season AND 
			ir.irrigation_source = 0 AND
			sc.season = ?
		) v 
		GROUP BY v.sclass 
		ORDER BY v.sclass ASC
		");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("
		SELECT 
		v.sclass, 
		AVG(v.yield),
		STDDEV_SAMP(v.yield) 
		FROM 
		(
			SELECT 
			sc.sclass, 
			io.yield 
			FROM 
			matrix_rectype6 sc 
			JOIN 
			matrix_ioutput io
			WHERE
			sc.region = io.region AND 
			sc.province = io.province AND 
			sc.municipality = io.municipality AND 
			sc.barangay = io.barangay AND 
			sc.hh_number = io.hh_number AND 
			sc.season = io.season AND 
			sc.season = ? AND sc.province = ?
		) v 
		GROUP BY v.sclass 
		ORDER BY v.sclass ASC
		");
		$irri_stmt= $conn->prepare("
		SELECT 
		v.sclass, 
		AVG(v.yield),
		STDDEV_SAMP(v.yield) 
		FROM 
		(
			SELECT 
			sc.sclass, 
			io.yield 
			FROM 
			matrix_rectype6 sc 
			JOIN 
			matrix_ioutput io, 
			matrix_irrigation ir 
			WHERE
			sc.region = io.region AND 
			sc.province = io.province AND 
			sc.municipality = io.municipality AND 
			sc.barangay = io.barangay AND 
			sc.hh_number = io.hh_number AND 
			sc.region = ir.region AND 
			sc.province = ir.province AND 
			sc.municipality = ir.municipality AND 
			sc.barangay = ir.barangay AND 
			sc.hh_number = ir.hh_number AND 
			sc.season = ir.season AND 
			(ir.irrigation_source = 1 OR 
			 ir.irrigation_source = 2 OR 
			 ir.irrigation_source = 3 OR 
			 ir.irrigation_source = 4 OR 
			 ir.irrigation_source = 5) AND
			sc.season = ? AND sc.province = ?
		) v 
		GROUP BY v.sclass 
		ORDER BY v.sclass ASC
		");
		$rain_stmt= $conn->prepare("
		SELECT 
		v.sclass, 
		AVG(v.yield),
		STDDEV_SAMP(v.yield) 
		FROM 
		(
			SELECT 
			sc.sclass, 
			io.yield 
			FROM 
			matrix_rectype6 sc 
			JOIN 
			matrix_ioutput io, 
			matrix_irrigation ir 
			WHERE
			sc.region = io.region AND 
			sc.province = io.province AND 
			sc.municipality = io.municipality AND 
			sc.barangay = io.barangay AND 
			sc.hh_number = io.hh_number AND 
			sc.region = ir.region AND 
			sc.province = ir.province AND 
			sc.municipality = ir.municipality AND 
			sc.barangay = ir.barangay AND 
			sc.hh_number = ir.hh_number AND 
			sc.season = ir.season AND 
			ir.irrigation_source = 0 AND
			sc.season = ? AND sc.province = ?
		) v 
		GROUP BY v.sclass 
		ORDER BY v.sclass ASC
		");
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($category, $avg_yield, $sd_yield);
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){
			if($season >= 5 && $category == 1){
				$seedclass[5][$counter] = $avg_yield;
				$seedclass[5][$counter+1] = $sd_yield;
			}
			elseif($season < 5 && $category == 1){
				$seedclass[1][$counter] =  $avg_yield;
				$seedclass[1][$counter+1] =  $sd_yield;
			}
			else{
				$seedclass[$category-1][$counter] =  $avg_yield;
				$seedclass[$category-1][$counter+1] =  $sd_yield;
			}
		}
	}
	for($i=0;$i<6;$i++){
		if(empty($seedclass[$i][$counter])){
			$seedclass[$i][$counter] = "0";
		}
		if(empty($seedclass[$i][$counter+1])){
			$seedclass[$i][$counter+1] = "0";
		}
	}
	$counter = $counter + 2;
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($category, $avg_yield, $sd_yield);
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){
			if($season >= 5 && $category == 1){
				$seedclass[5][$counter] = $avg_yield;
				$seedclass[5][$counter+1] = $sd_yield;
			}
			elseif($season < 5 && $category == 1){
				$seedclass[1][$counter] =  $avg_yield;
				$seedclass[1][$counter+1] =  $sd_yield;
			}
			else{
				$seedclass[$category-1][$counter] =  $avg_yield;
				$seedclass[$category-1][$counter+1] =  $sd_yield;
			}
		}
	}
	for($i=0;$i<6;$i++){
		if(empty($seedclass[$i][$counter])){
			$seedclass[$i][$counter] = "0";
		}
		if(empty($seedclass[$i][$counter+1])){
			$seedclass[$i][$counter+1] = "0";
		}
	}
	$counter = $counter + 2;
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($category, $avg_yield, $sd_yield);
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){
			if($season >= 5 && $category == 1){
				$seedclass[5][$counter] = $avg_yield;
				$seedclass[5][$counter+1] = $sd_yield;
			}
			elseif($season < 5 && $category == 1){
				$seedclass[1][$counter] =  $avg_yield;
				$seedclass[1][$counter+1] =  $sd_yield;
			}
			else{
				$seedclass[$category-1][$counter] =  $avg_yield;
				$seedclass[$category-1][$counter+1] =  $sd_yield;
			}
		}
	}
	for($i=0;$i<6;$i++){
		if(empty($seedclass[$i][$counter])){
			$seedclass[$i][$counter] = "0";
		}
		if(empty($seedclass[$i][$counter+1])){
			$seedclass[$i][$counter+1] = "0";
		}
	}
	$counter = $counter + 2;
	$all_stmt->close();
	$irri_stmt->close();
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
	create_special_rows("Seed Classification", $headings, 2);
	create_special_rows("", array_formatting($total_farmers,"(n = ",")"), 2);
	create_headings("", $headings2);
	humanized_number("Hybrid", $seedclass[5]);
	humanized_number("Registered", $seedclass[1]);
	humanized_number("Certified", $seedclass[2]);
	humanized_number("Good seeds", $seedclass[3]);
	humanized_number("Farmer's seed", $seedclass[4]);
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
echo "<br/>\n<div><sup>a </sup><b>SD</b> - refers to standard deviation</div>\n<br/>\n";
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