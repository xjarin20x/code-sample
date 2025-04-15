<?php
	require_once("../includes/headeralt.php");
?>
<div id="tableData">
<?php
	$provinces = disinfect_var($_POST['provinces']);
	$seasons = disinfect_var($_POST['seasons']);

	$content=count($provinces);
	
	$stmt= $total_stmt = $placeholder = "";
	$total = $codename = $percent = $ccount = $empcount = 0;
	
	$lop = implode(',', $provinces);
	$los = implode(',', $seasons);
	$region = 0;
	
	foreach($provinces as $province){
	$season_names = $headings = $footnotes = array();
	$seedbed =  $pre_estab = $stand = $n_stat = $total_n = array(); 
	$seed_farmers = $pre_farmers = $stand_farmers = array();
	$counter = $j = 0;
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
	
	$n_stat = array_merge($n_stat, $total_farmers);
	
	if($province==999){
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype6.crop_est = 1 AND matrix_application.seedbed_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype6.crop_est = 1 AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_application.seedbed_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype6.crop_est = 1 AND matrix_irrigation.irrigation_source = 0 AND matrix_application.seedbed_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype6.crop_est = 1 AND matrix_application.seedbed_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype6.crop_est = 1 AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_application.seedbed_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype6.crop_est = 1 AND matrix_irrigation.irrigation_source = 0 AND matrix_application.seedbed_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($total);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			array_push($seed_farmers, $total);
		}
	}
	else{
		array_push($seed_farmers, 0);
	}
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($total);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			array_push($seed_farmers, $total);
		}
	}
	else{
		array_push($seed_farmers, 0);
	}
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($total);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			array_push($seed_farmers, $total);
		}
	}
	else{
		array_push($seed_farmers, 0);
	}
	
	if($province==999){
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_application.preestab_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_application.preestab_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_source = 0 AND matrix_application.preestab_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_application.preestab_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_application.preestab_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_source = 0 AND matrix_application.preestab_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($total);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			array_push($pre_farmers, $total);
		}
	}
	else{
		array_push($pre_farmers, 0);
	}
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($total);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			array_push($pre_farmers, $total);
		}
	}
	else{
		array_push($pre_farmers, 0);
	}
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($total);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			array_push($pre_farmers, $total);
		}
	}
	else{
		array_push($pre_farmers, 0);
	}

	if($province==999){
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_application.standing_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_application.standing_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_source = 0 AND matrix_application.standing_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_application.standing_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_application.standing_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_application, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_source = 0 AND matrix_application.standing_appl IS NOT NULL AND matrix_application.type= 'R') a");
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($total);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			array_push($stand_farmers, $total);
		}
	}
	else{
		array_push($stand_farmers, 0);
	}
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($total);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			array_push($stand_farmers, $total);
		}
	}
	else{
		array_push($stand_farmers, 0);
	}
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($total);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			array_push($stand_farmers, $total);
		}
	}
	else{
		array_push($stand_farmers, 0);
	}

	if($province==999){
		$all_stmt= $conn->prepare("SELECT matrix_application.seedbed_appl, COUNT(matrix_application.seedbed_appl) as seedbed FROM matrix_application JOIN matrix_rectype1, matrix_rectype6 WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_application.seedbed_appl IS NOT NULL AND matrix_rectype6.crop_est = 1 AND matrix_application.type= 'R' GROUP BY matrix_application.seedbed_appl");
		$irri_stmt= $conn->prepare("SELECT matrix_application.seedbed_appl, COUNT(matrix_application.seedbed_appl) as seedbed FROM matrix_application JOIN matrix_rectype1, matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_application.seedbed_appl IS NOT NULL AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_rectype6.crop_est = 1 AND matrix_application.type= 'R' GROUP BY matrix_application.seedbed_appl");
		$rain_stmt= $conn->prepare("SELECT matrix_application.seedbed_appl, COUNT(matrix_application.seedbed_appl) as seedbed FROM matrix_application JOIN matrix_rectype1, matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_application.seedbed_appl IS NOT NULL AND matrix_irrigation.irrigation_source = 0 AND matrix_rectype6.crop_est = 1 AND matrix_application.type= 'R' GROUP BY matrix_application.seedbed_appl");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT matrix_application.seedbed_appl, COUNT(matrix_application.seedbed_appl) as seedbed FROM matrix_application JOIN matrix_rectype1, matrix_rectype6 WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_application.seedbed_appl IS NOT NULL AND matrix_rectype6.crop_est = 1 AND matrix_application.type= 'R' GROUP BY matrix_application.seedbed_appl");
		$irri_stmt= $conn->prepare("SELECT matrix_application.seedbed_appl, COUNT(matrix_application.seedbed_appl) as seedbed FROM matrix_application JOIN matrix_rectype1, matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_application.seedbed_appl IS NOT NULL AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_rectype6.crop_est = 1 AND matrix_application.type= 'R' GROUP BY matrix_application.seedbed_appl");
		$rain_stmt= $conn->prepare("SELECT matrix_application.seedbed_appl, COUNT(matrix_application.seedbed_appl) as seedbed FROM matrix_application JOIN matrix_rectype1, matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_application.seedbed_appl IS NOT NULL AND matrix_irrigation.irrigation_source = 0 AND matrix_rectype6.crop_est = 1 AND matrix_application.type= 'R' GROUP BY matrix_application.seedbed_appl");
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
	
	$c = $counter;
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($category, $quantity);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			$temp = 0;
			if($seed_farmers[$counter] != 0){
				$temp =  ($quantity / $seed_farmers[$counter]) * 100;
			}
			if(!isset( $seedbed[$category] )){
				$seedbed[$category] = array();
			}
			$seedbed[$category][$counter] = $temp;
		}
	}
	
	$counter++;
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($category, $quantity);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			$temp = 0;
			if($seed_farmers[$counter] != 0){
				$temp =  ($quantity / $seed_farmers[$counter]) * 100;
			}
			if(!isset( $seedbed[$category] )){
				$seedbed[$category] = array();
			}
			$seedbed[$category][$counter] = $temp;
		}

	}
	$counter++;
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($category, $quantity);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			$temp = 0;
			if($seed_farmers[$counter] != 0){
				$temp =  ($quantity / $seed_farmers[$counter]) * 100;
			}
			if(!isset( $seedbed[$category] )){
				$seedbed[$category] = array();
			}
			$seedbed[$category][$counter] = $temp;
		}
	}
	$counter++;
	
	if(empty($seedbed)) {
		for($i=0; $i<$counter; $i++){
			for($j=0; $j < $counter; $j++){
				if(!isset($seedbed[$i][$j])){
					$seedbed[$i][$j] = "..";
				}
			}
		}
	}
	else{
		foreach ($seedbed as $i => $a) {
			for($j=0; $j < $counter; $j++){
				if(!isset($seedbed[$i][$j])){
					$seedbed[$i][$j] = 0;
				}
			}
		}
	}
	
	$counter = $c;

	if($province==999){
		$all_stmt= $conn->prepare("SELECT matrix_application.preestab_appl, COUNT(matrix_application.preestab_appl) as preestab FROM matrix_application JOIN matrix_rectype1, matrix_rectype6 WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_application.preestab_appl IS NOT NULL AND matrix_application.type= 'R' GROUP BY matrix_application.preestab_appl");
		$irri_stmt= $conn->prepare("SELECT matrix_application.preestab_appl, COUNT(matrix_application.preestab_appl) as preestab FROM matrix_application JOIN matrix_rectype1, matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_application.preestab_appl IS NOT NULL AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_application.type= 'R' GROUP BY matrix_application.preestab_appl");
		$rain_stmt= $conn->prepare("SELECT matrix_application.preestab_appl, COUNT(matrix_application.preestab_appl) as preestab FROM matrix_application JOIN matrix_rectype1, matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_application.preestab_appl IS NOT NULL AND matrix_irrigation.irrigation_source = 0 AND matrix_application.type= 'R' GROUP BY matrix_application.preestab_appl");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT matrix_application.preestab_appl, COUNT(matrix_application.preestab_appl) as preestab FROM matrix_application JOIN matrix_rectype1, matrix_rectype6 WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_application.preestab_appl IS NOT NULL AND matrix_application.type= 'R' GROUP BY matrix_application.preestab_appl");
		$irri_stmt= $conn->prepare("SELECT matrix_application.preestab_appl, COUNT(matrix_application.preestab_appl) as preestab FROM matrix_application JOIN matrix_rectype1, matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_application.preestab_appl IS NOT NULL AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_application.type= 'R' GROUP BY matrix_application.preestab_appl");
		$rain_stmt= $conn->prepare("SELECT matrix_application.preestab_appl, COUNT(matrix_application.preestab_appl) as preestab FROM matrix_application JOIN matrix_rectype1, matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_application.preestab_appl IS NOT NULL AND matrix_irrigation.irrigation_source = 0 AND matrix_application.type= 'R' GROUP BY matrix_application.preestab_appl");
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
	
	$c = $counter;
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($category, $quantity);
			
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($category, $quantity);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			$temp = 0;
			if($pre_farmers[$counter] != 0){
				$temp =  ($quantity / $pre_farmers[$counter]) * 100;
			}
			if(!isset( $pre_estab[$category] )){
				$pre_estab[$category] = array();
			}
			$pre_estab[$category][$counter] = $temp;
		}
	}
	$counter++;
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($category, $quantity);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			$temp = 0;
			if($pre_farmers[$counter] != 0){
				$temp =  ($quantity / $pre_farmers[$counter]) * 100;
			}
			if(!isset( $pre_estab[$category] )){
				$pre_estab[$category] = array();
			}
			$pre_estab[$category][$counter] = $temp;
		}
	}
	$counter++;
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($category, $quantity);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			$temp = 0;
			if($pre_farmers[$counter] != 0){
				$temp =  ($quantity / $pre_farmers[$counter]) * 100;
			}
			if(!isset( $pre_estab[$category] )){
				$pre_estab[$category] = array();
			}
			$pre_estab[$category][$counter] = $temp;
		}
	}
	$counter++;
	if(empty($pre_estab)) {
		for($i=0; $i<$counter; $i++){
			for($j=0; $j < $counter; $j++){
				if(!isset($pre_estab[$i][$j])){
					$pre_estab[$i][$j] = "..";
				}
			}
		}
	}
	else{
		foreach ($pre_estab as $i => $a) {
			for($j=0; $j < $counter; $j++){
				if(!isset($pre_estab[$i][$j])){
					$pre_estab[$i][$j] = 0;
				}
			}
		}
	}

	$counter = $c;
	
	if($province==999){
		$all_stmt= $conn->prepare("SELECT matrix_application.standing_appl, COUNT(matrix_application.standing_appl) as preestab FROM matrix_application JOIN matrix_rectype1, matrix_rectype6 WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_application.standing_appl IS NOT NULL AND matrix_application.type= 'R' GROUP BY matrix_application.standing_appl");
		$irri_stmt= $conn->prepare("SELECT matrix_application.standing_appl, COUNT(matrix_application.standing_appl) as preestab FROM matrix_application JOIN matrix_rectype1, matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_application.standing_appl IS NOT NULL AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_application.type= 'R' GROUP BY matrix_application.standing_appl");
		$rain_stmt= $conn->prepare("SELECT matrix_application.standing_appl, COUNT(matrix_application.standing_appl) as preestab FROM matrix_application JOIN matrix_rectype1, matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_application.standing_appl IS NOT NULL AND matrix_irrigation.irrigation_source = 0 AND matrix_application.type= 'R' GROUP BY matrix_application.standing_appl");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT matrix_application.standing_appl, COUNT(matrix_application.standing_appl) as preestab FROM matrix_application JOIN matrix_rectype1, matrix_rectype6 WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_application.standing_appl IS NOT NULL AND matrix_application.type= 'R' GROUP BY matrix_application.standing_appl");
		$irri_stmt= $conn->prepare("SELECT matrix_application.standing_appl, COUNT(matrix_application.standing_appl) as preestab FROM matrix_application JOIN matrix_rectype1, matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_application.standing_appl IS NOT NULL AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_application.type= 'R' GROUP BY matrix_application.standing_appl");
		$rain_stmt= $conn->prepare("SELECT matrix_application.standing_appl, COUNT(matrix_application.standing_appl) as preestab FROM matrix_application JOIN matrix_rectype1, matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_application.region AND matrix_rectype1.province = matrix_application.province AND matrix_rectype1.municipality = matrix_application.municipality AND matrix_rectype1.barangay = matrix_application.barangay AND matrix_rectype1.hh_number = matrix_application.hh_number AND matrix_rectype1.season = matrix_application.season AND matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_application.standing_appl IS NOT NULL AND matrix_irrigation.irrigation_source = 0 AND matrix_application.type= 'R' GROUP BY matrix_application.standing_appl");
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
			if($stand_farmers[$counter] != 0){
				$temp =  ($quantity / $stand_farmers[$counter]) * 100;
			}
			if(!isset( $stand[$category] )){
				$stand[$category] = array();
			}
			$stand[$category][$counter] = $temp;
		}
	}
	$counter++;
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($category, $quantity);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			$temp = 0;
			if($stand_farmers[$counter] != 0){
				$temp =  ($quantity / $stand_farmers[$counter]) * 100;
			}
			if(!isset( $stand[$category] )){
				$stand[$category] = array();
			}
			$stand[$category][$counter] = $temp;
		}
	}
	$counter++;
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($category, $quantity);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			$temp = 0;
			if($stand_farmers[$counter] != 0){
				$temp =  ($quantity / $stand_farmers[$counter]) * 100;
			}
			if(!isset( $stand[$category] )){
				$stand[$category] = array();
			}
			$stand[$category][$counter] = $temp;
		}
	}
	$counter++;
	
	if(empty($stand)) {
		for($i=0; $i<$counter; $i++){
			for($j=0; $j < $counter; $j++){
				if(!isset($stand[$i][$j])){
					$stand[$i][$j] = "..";
				}
			}
		}
	}
	else{
		foreach ($stand as $i => $a) {
			for($j=0; $j < $counter; $j++){
				if(!isset($stand[$i][$j])){
					$stand[$i][$j] = 0;
				}
			}
		}
	}


	
	$all_stmt->close();
	$irri_stmt->close();
	$rain_stmt->close();
	}
	ksort($seedbed);
	ksort($pre_estab);
	ksort($stand);
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
	create_headings("Frequency of rodenticide application", $headings);
	create_special_rows("", array_formatting($n_stat,"(n = ",")"), 1);
	echo "<tr>\n<td class='header4'></td><td class='header4 center' colspan=".($count_columns-1).">(Percent of farmers)</td>\n</tr>\n";
	echo "<tr>\n<td class='header4 left' colspan=".$count_columns.">Seedbed*</td>\n</tr>\n";
	foreach($seedbed as $key => $value){
		if(array_sum($value) > count($seasons)){
			switch($key){
				case 0:  break;
				case 1:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;once", $value, 0); break;
				case 2:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;twice", $value, 0); break;
				case 3:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;thrice", $value, 0); break;
				default:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$key." times", $value, 0); 
			}
		}
		elseif(array_sum($value) > 0.1){
			switch($key){
				case 0:  break;
				case 1:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;once", $value, 1); break;
				case 2:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;twice", $value, 1); break;
				case 3:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;thrice", $value, 1); break;
				default:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$key." times", $value, 1); 
			}
		}
	}
	if(isset($seedbed[0])){
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>did not apply</i>", $seedbed[0], 0);
	}
	if(max($seasons) > 5){
		echo "<tr>\n<td class='header4 left' colspan=".$count_columns.">Pre-establishment</td>\n</tr>\n";
		foreach($pre_estab as $key => $value){
			if(array_sum($value) > count($seasons)){
				switch($key){
					case 0:  break;
					case 1:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;once", $value, 0); break;
					case 2:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;twice", $value, 0); break;
					case 3:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;thrice", $value, 0); break;
					default:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$key." times", $value, 0); 
				}
			}
			elseif(array_sum($value) > 0.1){
				switch($key){
					case 0:  break;
					case 1:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;once", $value, 1); break;
					case 2:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;twice", $value, 1); break;
					case 3:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;thrice", $value, 1); break;
					default:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$key." times", $value, 1); 
				}
			}
		}
		if(isset($pre_estab[0])){
			create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>did not apply</i>", $pre_estab[0], 0);
		}
	}
	echo "<tr>\n<td class='header4 left' colspan=".$count_columns.">Standing</td>\n</tr>\n";
	foreach($stand as $key => $value){
		if(array_sum($value) > count($seasons)){
			switch($key){
				case 0:  break;
				case 1:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;once", $value, 0); break;
				case 2:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;twice", $value, 0); break;
				case 3:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;thrice", $value, 0); break;
				default:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$key." times", $value, 0); 
			}
		}
		elseif(array_sum($value) > 0.1){
			switch($key){
				case 0:  break;
				case 1:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;once", $value, 1); break;
				case 2:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;twice", $value, 1); break;
				case 3:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;thrice", $value, 1); break;
				default:  create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$key." times", $value, 1); 
			}
		}
	}
	if(isset($stand[0])){
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i>did not apply</i>", $stand[0], 0);
	}
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
			echo '<div>Excludes farmers who temporarily stopped farming and missing responses for '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$n_stat[$i*3]) .')</div>';
		}
	}
	echo "<br/>";
	}

echo "<div>* Includes farmers who used transplanting method only</div>";
echo "<div>.. - Season data is not available</div>";
echo "<br/>";
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