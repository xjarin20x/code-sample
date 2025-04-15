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
	$season_names =  $headings = $headings2 = $footnotes = array();
	$counter = 0;
	$iout = $n_stat = $total_n = array();
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	
	foreach($seasons as $season){
	$total_farmers = array();
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
	
	$n_stat= array_merge($n_stat, $total_farmers);
	
	if($province==999){
		$all_stmt = $conn->prepare('SELECT AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha) FROM matrix_ioutput i JOIN matrix_rectype1 WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.yield != 0;');
		$irri_stmt = $conn->prepare('SELECT AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha) FROM matrix_ioutput i JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.yield != 0 AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.municipality = i.municipality AND matrix_irrigation.barangay = i.barangay AND matrix_irrigation.hh_number = i.hh_number AND matrix_irrigation.season = i.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5);');
		$rain_stmt = $conn->prepare('SELECT AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha) FROM matrix_ioutput i JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.yield != 0 AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.municipality = i.municipality AND matrix_irrigation.barangay = i.barangay AND matrix_irrigation.hh_number = i.hh_number AND matrix_irrigation.season = i.season AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.municipality = i.municipality AND matrix_irrigation.barangay = i.barangay AND matrix_irrigation.hh_number = i.hh_number AND matrix_irrigation.season = i.season AND matrix_irrigation.irrigation_source = 0;');
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt = $conn->prepare('SELECT AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha) FROM matrix_ioutput i JOIN matrix_rectype1 WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.province = ? AND i.yield != 0');
		$irri_stmt = $conn->prepare('SELECT AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha) FROM matrix_ioutput i JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.province = ? AND i.yield != 0 AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.municipality = i.municipality AND matrix_irrigation.barangay = i.barangay AND matrix_irrigation.hh_number = i.hh_number AND matrix_irrigation.season = i.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5);');
		$rain_stmt = $conn->prepare('SELECT AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha) FROM matrix_ioutput i JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.province = ? AND i.yield != 0 AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.municipality = i.municipality AND matrix_irrigation.barangay = i.barangay AND matrix_irrigation.hh_number = i.hh_number AND matrix_irrigation.season = i.season AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.municipality = i.municipality AND matrix_irrigation.barangay = i.barangay AND matrix_irrigation.hh_number = i.hh_number AND matrix_irrigation.season = i.season AND matrix_irrigation.irrigation_source = 0;');
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
	$keywords = array("nha", "pha", "kha");
	$all_stmt->execute();
	$all_stmt->store_result();
	$res_arr = fetch_get_result_alt($all_stmt);
	// PHP 5.3
	// $res = $all_stmt->get_result();
	// $res_arr = $res->fetch_assoc();
	
	foreach ($keywords as $value){
		if(!isset($iout[$value])){
			$iout[$value] = array();
		}
		foreach ($res_arr as $key => $n){
			if($pos = strpos($key, $value)){
				array_push($iout[$value], $n);
				unset($res_arr[$key]);
			}
		}
	}
	//irrigated
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$res_arr = fetch_get_result_alt($irri_stmt);
	// PHP 5.3
	// $res = $irri_stmt->get_result();
	// $res_arr = $res->fetch_assoc();
	
	foreach ($keywords as $value){
		if(!isset($iout[$value])){
			$iout[$value] = array();
		}
		foreach ($res_arr as $key => $n){
			if($pos = strpos($key, $value)){
				array_push($iout[$value], $n);
				unset($res_arr[$key]);
			}
		}
	}
	//rainfed
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$res_arr = fetch_get_result_alt($rain_stmt);
	// PHP 5.3
	// $res = $rain_stmt->get_result();
	// $res_arr = $res->fetch_assoc();
	
	foreach ($keywords as $value){
		if(!isset($iout[$value])){
			$iout[$value] = array();
		}
		foreach ($res_arr as $key => $n){
			if($pos = strpos($key, $value)){
				array_push($iout[$value], $n);
				unset($res_arr[$key]);
			}
		}
	}
	
	$all_stmt->close();
	$irri_stmt->close();
	$rain_stmt->close();
	}//SEASON END BRACKET
	foreach ($iout as $key => $value){
		for($i = 0; $i < count($seasons) * 6; $i++){
			if( !isset( $iout[$key][$i] ) ){
				$iout[$key][$i] = 0;
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
	$count_columns=(count($seasons)*6)+1;
	create_header($name, $season_names, 6);
	echo "<tbody>\n";
	create_special_rows("Items", $headings, 2);
	create_special_rows("", array_formatting($n_stat,"(n = ",")"), 2);
	create_headings("", $headings2);
	create_row("Nitrogen (N) (kg/ha)", $iout['nha'], 2);
	create_row("Phosphorus (P) (kg/ha)", $iout['pha'], 2);
	create_row("Potassium (K) (kg/ha)", $iout['kha'], 2);
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
			echo '<div> Excludes farmers who temporarily stopped farming/did not plant rice/experienced crop failure/with missing data for'. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$n_stat[$i*3]) .')</div>';
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
echo "<br/>\n<div><sup>a </sup><b>SD</b> - refers to standard deviation</div>\n";
echo displayNoteIrrigate();
?>
<br/>
<div>Data accessed at <?php echo date('l jS \of F Y h:i:s A');?></div>
<?php echo displayNoteContact(); ?>
</div>
<?php
require_once("../includes/export.php");
?>