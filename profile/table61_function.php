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
	$ecosystem = array(); 
	for($i=0; $i < 3; $i++){
		array_push($ecosystem, array());
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

	array_push($headings, "Mean");	
	array_push($headings, "SD<sup>a</sup>");

	if($province==999){
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_rectype1.season = ? AND matrix_ioutput.yield != 0) a");
		$all_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_ioutput.yield != 0) a");
		$all_stmt->bind_param("ss", $season, $province);
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
	
	if($province==999){
		$all_stmt= $conn->prepare("
		SELECT 
		AVG(v.yield),
		STDDEV_SAMP(v.yield) 
		FROM 
		(
			SELECT 
			io.yield 
			FROM 
			matrix_ioutput io 
			JOIN 
			matrix_rectype1 re
			WHERE
			re.region = io.region AND 
			re.province = io.province AND 
			re.municipality = io.municipality AND 
			re.barangay = io.barangay AND 
			re.hh_number = io.hh_number AND 
			re.season = io.season AND 
			io.yield != 0 AND
			re.season = ?
		) v;
		");
		$irri_stmt= $conn->prepare("
		SELECT 
		AVG(v.yield),
		STDDEV_SAMP(v.yield) 
		FROM 
		(
			SELECT 
			io.yield 
			FROM 
			matrix_ioutput io 
			JOIN 
			matrix_rectype1 re, 
			matrix_irrigation ir 
			WHERE
			re.region = io.region AND 
			re.province = io.province AND 
			re.municipality = io.municipality AND 
			re.barangay = io.barangay AND 
			re.hh_number = io.hh_number AND 
			re.season = io.season AND 
			re.region = ir.region AND 
			re.province = ir.province AND 
			re.municipality = ir.municipality AND 
			re.barangay = ir.barangay AND 
			re.hh_number = ir.hh_number AND 
			re.season = ir.season AND 
			io.yield != 0 AND
			(ir.irrigation_source = 1 OR 
			 ir.irrigation_source = 2 OR 
			 ir.irrigation_source = 3 OR 
			 ir.irrigation_source = 4 OR 
			 ir.irrigation_source = 5) AND
			re.season = ?
		) v;
		");
		$rain_stmt= $conn->prepare("
		SELECT 
		AVG(v.yield),
		STDDEV_SAMP(v.yield) 
		FROM 
		(
			SELECT 
			io.yield 
			FROM 
			matrix_ioutput io 
			JOIN 
			matrix_rectype1 re, 
			matrix_irrigation ir 
			WHERE
			re.region = io.region AND 
			re.province = io.province AND 
			re.municipality = io.municipality AND 
			re.barangay = io.barangay AND 
			re.hh_number = io.hh_number AND 
			re.season = io.season AND 
			re.region = ir.region AND 
			re.province = ir.province AND 
			re.municipality = ir.municipality AND 
			re.barangay = ir.barangay AND 
			re.hh_number = ir.hh_number AND 
			re.season = ir.season AND 
			io.yield != 0 AND
			ir.irrigation_source = 0 AND
			re.season = ?
		) v;
		");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("
		SELECT 
		AVG(v.yield),
		STDDEV_SAMP(v.yield) 
		FROM 
		(
			SELECT 
			io.yield 
			FROM 
			matrix_ioutput io 
			JOIN 
			matrix_rectype1 re
			WHERE
			re.region = io.region AND 
			re.province = io.province AND 
			re.municipality = io.municipality AND 
			re.barangay = io.barangay AND 
			re.hh_number = io.hh_number AND 
			re.season = io.season AND 
			io.yield != 0 AND
			re.season = ? AND re.province = ?
		) v;
		");
		$irri_stmt= $conn->prepare("
		SELECT 
		AVG(v.yield),
		STDDEV_SAMP(v.yield) 
		FROM 
		(
			SELECT 
			io.yield 
			FROM 
			matrix_ioutput io 
			JOIN 
			matrix_rectype1 re, 
			matrix_irrigation ir 
			WHERE
			re.region = io.region AND 
			re.province = io.province AND 
			re.municipality = io.municipality AND 
			re.barangay = io.barangay AND 
			re.hh_number = io.hh_number AND 
			re.season = io.season AND 
			re.region = ir.region AND 
			re.province = ir.province AND 
			re.municipality = ir.municipality AND 
			re.barangay = ir.barangay AND 
			re.hh_number = ir.hh_number AND 
			re.season = ir.season AND 
			(ir.irrigation_source = 1 OR 
			 ir.irrigation_source = 2 OR 
			 ir.irrigation_source = 3 OR 
			 ir.irrigation_source = 4 OR 
			 ir.irrigation_source = 5) AND
			 io.yield != 0 AND
			re.season = ? AND re.province = ?
		) v;
		");
		$rain_stmt= $conn->prepare("
		SELECT 
		AVG(v.yield),
		STDDEV_SAMP(v.yield) 
		FROM 
		(
			SELECT 
			io.yield 
			FROM 
			matrix_ioutput io 
			JOIN 
			matrix_rectype1 re, 
			matrix_irrigation ir 
			WHERE
			re.region = io.region AND 
			re.province = io.province AND 
			re.municipality = io.municipality AND 
			re.barangay = io.barangay AND 
			re.hh_number = io.hh_number AND 
			re.season = io.season AND 
			re.region = ir.region AND 
			re.province = ir.province AND 
			re.municipality = ir.municipality AND 
			re.barangay = ir.barangay AND 
			re.hh_number = ir.hh_number AND 
			re.season = ir.season AND 
			ir.irrigation_source = 0 AND
			io.yield != 0 AND
			re.season = ? AND re.province = ?
		) v;
		");
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($avg_yield, $sd_yield);
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){
			$ecosystem[0][$counter] =  $avg_yield;
			$ecosystem[0][$counter+1] =  $sd_yield;
		}
	}
	else {
		$ecosystem[0][$counter] = 0;
		$ecosystem[0][$counter+1] = 0;
	}	

	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($avg_yield, $sd_yield);
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){
			$ecosystem[1][$counter] =  $avg_yield;
			$ecosystem[1][$counter+1] =  $sd_yield;
		}
	}
	else {
		$ecosystem[1][$counter] = 0;
		$ecosystem[1][$counter+1] = 0;
	}
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($avg_yield, $sd_yield);
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){
			$ecosystem[2][$counter] =  $avg_yield;
			$ecosystem[2][$counter+1] =  $sd_yield;
		}
	}
	else {
		$ecosystem[2][$counter] = 0;
		$ecosystem[2][$counter+1] = 0;
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
	$count_columns=(count($seasons)*2)+1;
	create_header($name, $season_names, 2);
	create_special_rows("", array_formatting($total_farmers,"(n = ",")"), 2);
	//echo "<tr>\n<td class='header4'></td><td class='header4 center' colspan=".($count_columns-1).">(average)</td>\n</tr>\n";
	create_headings("", $headings);
	humanized_number("All ecosystems", $ecosystem[0]);
	humanized_number("Irrigated ecosystem", $ecosystem[1]);
	humanized_number("Non-irrigated ecosystem", $ecosystem[2]);
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
		if ($total_farmers[$i] < $total_n[$i]){
			echo '<div>* Excludes farmers who temporarily stopped farming/did not plant rice/experienced crop failure/with missing data for '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$total_farmers[$i]) .')</div>';
		}
	}
	echo "<br/>";
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