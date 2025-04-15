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
	$avg_distance = $avg_time = $avg_fare =  array(); 
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
	
	for($i=0; $i<3; $i++){
		array_push($headings2, "Mean");	
		array_push($headings2, "SD<sup>a</sup>");	
	}

	if($province==999){
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput, matrix_irrigation WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_irrigation.region = matrix_ioutput.region AND matrix_irrigation.province = matrix_ioutput.province AND matrix_irrigation.municipality = matrix_ioutput.municipality AND matrix_irrigation.barangay = matrix_ioutput.barangay AND matrix_irrigation.hh_number = matrix_ioutput.hh_number AND matrix_irrigation.season = matrix_ioutput.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_source IS NOT NULL AND matrix_ioutput.yield != 0) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput, matrix_irrigation WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_irrigation.region = matrix_ioutput.region AND matrix_irrigation.province = matrix_ioutput.province AND matrix_irrigation.municipality = matrix_ioutput.municipality AND matrix_irrigation.barangay = matrix_ioutput.barangay AND matrix_irrigation.hh_number = matrix_ioutput.hh_number AND matrix_irrigation.season = matrix_ioutput.season AND matrix_rectype1.season = ? AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_ioutput.yield != 0) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput, matrix_irrigation WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_irrigation.region = matrix_ioutput.region AND matrix_irrigation.province = matrix_ioutput.province AND matrix_irrigation.municipality = matrix_ioutput.municipality AND matrix_irrigation.barangay = matrix_ioutput.barangay AND matrix_irrigation.hh_number = matrix_ioutput.hh_number AND matrix_irrigation.season = matrix_ioutput.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_source = 0 AND matrix_ioutput.yield != 0) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput, matrix_irrigation WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_irrigation.region = matrix_ioutput.region AND matrix_irrigation.province = matrix_ioutput.province AND matrix_irrigation.municipality = matrix_ioutput.municipality AND matrix_irrigation.barangay = matrix_ioutput.barangay AND matrix_irrigation.hh_number = matrix_ioutput.hh_number AND matrix_irrigation.season = matrix_ioutput.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_source IS NOT NULL AND matrix_ioutput.yield != 0) a");
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
		if($season < 5){
			$all_stmt = $conn->prepare('SELECT AVG(i.aplant), STDDEV_SAMP(i.aplant), AVG(i.yield), STDDEV_SAMP(i.yield), AVG(i.seedha), STDDEV_SAMP(i.seedha), AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha), AVG(i.herbaiha), STDDEV_SAMP(i.herbaiha), AVG(i.insectai), STDDEV_SAMP(i.insectai), AVG(i.fungikgai), STDDEV_SAMP(i.fungikgai), AVG(i.othpkgai), STDDEV_SAMP(i.othpkgai), AVG(i.hlabpre), STDDEV_SAMP(i.hlabpre), AVG(i.hlabht), STDDEV_SAMP(i.hlabht), AVG(i.hlabpost),  STDDEV_SAMP(i.hlabpost), AVG(i.prelabha), STDDEV_SAMP(i.prelabha), AVG(i.ilabht), STDDEV_SAMP(i.ilabht), AVG(i.ilabpost), STDDEV_SAMP(i.ilabpost) FROM matrix_ioutput i JOIN matrix_rectype1 WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.yield != 0;');
			$irri_stmt = $conn->prepare('SELECT AVG(i.aplant), STDDEV_SAMP(i.aplant), AVG(i.yield), STDDEV_SAMP(i.yield), AVG(i.seedha), STDDEV_SAMP(i.seedha), AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha), AVG(i.herbaiha), STDDEV_SAMP(i.herbaiha), AVG(i.insectai), STDDEV_SAMP(i.insectai), AVG(i.fungikgai), STDDEV_SAMP(i.fungikgai), AVG(i.othpkgai), STDDEV_SAMP(i.othpkgai), AVG(i.hlabpre), STDDEV_SAMP(i.hlabpre), AVG(i.hlabht), STDDEV_SAMP(i.hlabht), AVG(i.hlabpost),  STDDEV_SAMP(i.hlabpost), AVG(i.prelabha), STDDEV_SAMP(i.prelabha), AVG(i.ilabht), STDDEV_SAMP(i.ilabht), AVG(i.ilabpost), STDDEV_SAMP(i.ilabpost) FROM matrix_ioutput i JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.yield != 0 AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.municipality = i.municipality AND matrix_irrigation.barangay = i.barangay AND matrix_irrigation.hh_number = i.hh_number AND matrix_irrigation.season = i.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5);');
			$rain_stmt = $conn->prepare('SELECT AVG(i.aplant), STDDEV_SAMP(i.aplant), AVG(i.yield), STDDEV_SAMP(i.yield), AVG(i.seedha), STDDEV_SAMP(i.seedha), AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha), AVG(i.herbaiha), STDDEV_SAMP(i.herbaiha), AVG(i.insectai), STDDEV_SAMP(i.insectai), AVG(i.fungikgai), STDDEV_SAMP(i.fungikgai), AVG(i.othpkgai), STDDEV_SAMP(i.othpkgai), AVG(i.hlabpre), STDDEV_SAMP(i.hlabpre), AVG(i.hlabht), STDDEV_SAMP(i.hlabht), AVG(i.hlabpost),  STDDEV_SAMP(i.hlabpost), AVG(i.prelabha), STDDEV_SAMP(i.prelabha), AVG(i.ilabht), STDDEV_SAMP(i.ilabht), AVG(i.ilabpost), STDDEV_SAMP(i.ilabpost) FROM matrix_ioutput i JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.yield != 0 AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.municipality = i.municipality AND matrix_irrigation.barangay = i.barangay AND matrix_irrigation.hh_number = i.hh_number AND matrix_irrigation.season = i.season AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.municipality = i.municipality AND matrix_irrigation.barangay = i.barangay AND matrix_irrigation.hh_number = i.hh_number AND matrix_irrigation.season = i.season AND matrix_irrigation.irrigation_source = 0;');
			$all_stmt->bind_param("s", $season);
			$irri_stmt->bind_param("s", $season);
			$rain_stmt->bind_param("s", $season);
		}
		else{
			$all_stmt = $conn->prepare('SELECT AVG(i.aplant), STDDEV_SAMP(i.aplant), AVG(i.yield), STDDEV_SAMP(i.yield), AVG(i.seedha), STDDEV_SAMP(i.seedha), AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha), AVG(i.herbaiha), STDDEV_SAMP(i.herbaiha), AVG(i.insectai), STDDEV_SAMP(i.insectai), AVG(i.fungikgai), STDDEV_SAMP(i.fungikgai), AVG(i.othpkgai), STDDEV_SAMP(i.othpkgai), AVG(i.hlabseed), STDDEV_SAMP(i.hlabseed), AVG(i.hlablandprep), STDDEV_SAMP(i.hlablandprep), AVG(i.hlabcropest), STDDEV_SAMP(i.hlabcropest), AVG(i.hlabcropcare), STDDEV_SAMP(i.hlabcropcare), AVG(i.hlabht), STDDEV_SAMP(i.hlabht), AVG(i.hlabpost), STDDEV_SAMP(i.hlabpost), AVG(i.permlabor), STDDEV_SAMP(i.permlabor), AVG(i.preseed), STDDEV_SAMP(i.preseed), AVG(i.prelandprep), STDDEV_SAMP(i.prelandprep), AVG(i.precropest), STDDEV_SAMP(i.precropest), AVG(i.precropcare), STDDEV_SAMP(i.precropcare), AVG(i.ilabht), STDDEV_SAMP(i.ilabht), AVG(i.ilabpost), STDDEV_SAMP(i.ilabpost) FROM matrix_ioutput i JOIN matrix_rectype1 WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.yield != 0');
			$irri_stmt = $conn->prepare('SELECT AVG(i.aplant), STDDEV_SAMP(i.aplant), AVG(i.yield), STDDEV_SAMP(i.yield), AVG(i.seedha), STDDEV_SAMP(i.seedha), AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha), AVG(i.herbaiha), STDDEV_SAMP(i.herbaiha), AVG(i.insectai), STDDEV_SAMP(i.insectai), AVG(i.fungikgai), STDDEV_SAMP(i.fungikgai), AVG(i.othpkgai), STDDEV_SAMP(i.othpkgai), AVG(i.hlabseed), STDDEV_SAMP(i.hlabseed), AVG(i.hlablandprep), STDDEV_SAMP(i.hlablandprep), AVG(i.hlabcropest), STDDEV_SAMP(i.hlabcropest), AVG(i.hlabcropcare), STDDEV_SAMP(i.hlabcropcare), AVG(i.hlabht), STDDEV_SAMP(i.hlabht), AVG(i.hlabpost), STDDEV_SAMP(i.hlabpost), AVG(i.permlabor), STDDEV_SAMP(i.permlabor), AVG(i.preseed), STDDEV_SAMP(i.preseed), AVG(i.prelandprep), STDDEV_SAMP(i.prelandprep), AVG(i.precropest), STDDEV_SAMP(i.precropest), AVG(i.precropcare), STDDEV_SAMP(i.precropcare), AVG(i.ilabht), STDDEV_SAMP(i.ilabht), AVG(i.ilabpost), STDDEV_SAMP(i.ilabpost) FROM matrix_ioutput i JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.yield != 0 AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.municipality = i.municipality AND matrix_irrigation.barangay = i.barangay AND matrix_irrigation.hh_number = i.hh_number AND matrix_irrigation.season = i.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5)');
			$rain_stmt = $conn->prepare('SELECT AVG(i.aplant), STDDEV_SAMP(i.aplant), AVG(i.yield), STDDEV_SAMP(i.yield), AVG(i.seedha), STDDEV_SAMP(i.seedha), AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha), AVG(i.herbaiha), STDDEV_SAMP(i.herbaiha), AVG(i.insectai), STDDEV_SAMP(i.insectai), AVG(i.fungikgai), STDDEV_SAMP(i.fungikgai), AVG(i.othpkgai), STDDEV_SAMP(i.othpkgai), AVG(i.hlabseed), STDDEV_SAMP(i.hlabseed), AVG(i.hlablandprep), STDDEV_SAMP(i.hlablandprep), AVG(i.hlabcropest), STDDEV_SAMP(i.hlabcropest), AVG(i.hlabcropcare), STDDEV_SAMP(i.hlabcropcare), AVG(i.hlabht), STDDEV_SAMP(i.hlabht), AVG(i.hlabpost), STDDEV_SAMP(i.hlabpost), AVG(i.permlabor), STDDEV_SAMP(i.permlabor), AVG(i.preseed), STDDEV_SAMP(i.preseed), AVG(i.prelandprep), STDDEV_SAMP(i.prelandprep), AVG(i.precropest), STDDEV_SAMP(i.precropest), AVG(i.precropcare), STDDEV_SAMP(i.precropcare), AVG(i.ilabht), STDDEV_SAMP(i.ilabht), AVG(i.ilabpost), STDDEV_SAMP(i.ilabpost) FROM matrix_ioutput i JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.yield != 0 AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.municipality = i.municipality AND matrix_irrigation.barangay = i.barangay AND matrix_irrigation.hh_number = i.hh_number AND matrix_irrigation.season = i.season AND matrix_irrigation.irrigation_source = 0');
			$all_stmt->bind_param("s", $season);
			$irri_stmt->bind_param("s", $season);
			$rain_stmt->bind_param("s", $season);
		}
	}
	else{
		if($season < 5){
			$all_stmt = $conn->prepare('SELECT AVG(i.aplant), STDDEV_SAMP(i.aplant), AVG(i.yield), STDDEV_SAMP(i.yield), AVG(i.seedha), STDDEV_SAMP(i.seedha), AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha), AVG(i.herbaiha), STDDEV_SAMP(i.herbaiha), AVG(i.insectai), STDDEV_SAMP(i.insectai), AVG(i.fungikgai), STDDEV_SAMP(i.fungikgai), AVG(i.othpkgai), STDDEV_SAMP(i.othpkgai), AVG(i.hlabpre), STDDEV_SAMP(i.hlabpre), AVG(i.hlabht), STDDEV_SAMP(i.hlabht), AVG(i.hlabpost),  STDDEV_SAMP(i.hlabpost), AVG(i.prelabha), STDDEV_SAMP(i.prelabha), AVG(i.ilabht), STDDEV_SAMP(i.ilabht), AVG(i.ilabpost), STDDEV_SAMP(i.ilabpost) FROM matrix_ioutput i JOIN matrix_rectype1 WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.province = ? AND i.yield != 0');
			$irri_stmt = $conn->prepare('SELECT AVG(i.aplant), STDDEV_SAMP(i.aplant), AVG(i.yield), STDDEV_SAMP(i.yield), AVG(i.seedha), STDDEV_SAMP(i.seedha), AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha), AVG(i.herbaiha), STDDEV_SAMP(i.herbaiha), AVG(i.insectai), STDDEV_SAMP(i.insectai), AVG(i.fungikgai), STDDEV_SAMP(i.fungikgai), AVG(i.othpkgai), STDDEV_SAMP(i.othpkgai), AVG(i.hlabpre), STDDEV_SAMP(i.hlabpre), AVG(i.hlabht), STDDEV_SAMP(i.hlabht), AVG(i.hlabpost),  STDDEV_SAMP(i.hlabpost), AVG(i.prelabha), STDDEV_SAMP(i.prelabha), AVG(i.ilabht), STDDEV_SAMP(i.ilabht), AVG(i.ilabpost), STDDEV_SAMP(i.ilabpost) FROM matrix_ioutput i JOIN matrix_rectype1, matrix_irrigation WHERE i.season = ? AND i.province = ? AND i.yield != 0 AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.municipality = i.municipality AND matrix_irrigation.barangay = i.barangay AND matrix_irrigation.hh_number = i.hh_number AND matrix_irrigation.season = i.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5)');
			$rain_stmt = $conn->prepare('SELECT AVG(i.aplant), STDDEV_SAMP(i.aplant), AVG(i.yield), STDDEV_SAMP(i.yield), AVG(i.seedha), STDDEV_SAMP(i.seedha), AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha), AVG(i.herbaiha), STDDEV_SAMP(i.herbaiha), AVG(i.insectai), STDDEV_SAMP(i.insectai), AVG(i.fungikgai), STDDEV_SAMP(i.fungikgai), AVG(i.othpkgai), STDDEV_SAMP(i.othpkgai), AVG(i.hlabpre), STDDEV_SAMP(i.hlabpre), AVG(i.hlabht), STDDEV_SAMP(i.hlabht), AVG(i.hlabpost),  STDDEV_SAMP(i.hlabpost), AVG(i.prelabha), STDDEV_SAMP(i.prelabha), AVG(i.ilabht), STDDEV_SAMP(i.ilabht), AVG(i.ilabpost), STDDEV_SAMP(i.ilabpost) FROM matrix_ioutput i JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.province = ? AND i.yield != 0 AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.municipality = i.municipality AND matrix_irrigation.barangay = i.barangay AND matrix_irrigation.hh_number = i.hh_number AND matrix_irrigation.season = i.season AND matrix_irrigation.irrigation_source = 0');
			$all_stmt->bind_param("ss", $season, $province);
			$irri_stmt->bind_param("ss", $season, $province);
			$rain_stmt->bind_param("ss", $season, $province);
		}
		else{
			$all_stmt = $conn->prepare('SELECT AVG(i.aplant), STDDEV_SAMP(i.aplant), AVG(i.yield), STDDEV_SAMP(i.yield), AVG(i.seedha), STDDEV_SAMP(i.seedha), AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha), AVG(i.herbaiha), STDDEV_SAMP(i.herbaiha), AVG(i.insectai), STDDEV_SAMP(i.insectai), AVG(i.fungikgai), STDDEV_SAMP(i.fungikgai), AVG(i.othpkgai), STDDEV_SAMP(i.othpkgai), AVG(i.hlabseed), STDDEV_SAMP(i.hlabseed), AVG(i.hlablandprep), STDDEV_SAMP(i.hlablandprep), AVG(i.hlabcropest), STDDEV_SAMP(i.hlabcropest), AVG(i.hlabcropcare), STDDEV_SAMP(i.hlabcropcare), AVG(i.hlabht), STDDEV_SAMP(i.hlabht), AVG(i.hlabpost), STDDEV_SAMP(i.hlabpost), AVG(i.permlabor), STDDEV_SAMP(i.permlabor), AVG(i.preseed), STDDEV_SAMP(i.preseed), AVG(i.prelandprep), STDDEV_SAMP(i.prelandprep), AVG(i.precropest), STDDEV_SAMP(i.precropest), AVG(i.precropcare), STDDEV_SAMP(i.precropcare), AVG(i.ilabht), STDDEV_SAMP(i.ilabht), AVG(i.ilabpost), STDDEV_SAMP(i.ilabpost) FROM matrix_ioutput i JOIN matrix_rectype1 WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.province = ? AND i.yield != 0');
			$irri_stmt = $conn->prepare('SELECT AVG(i.aplant), STDDEV_SAMP(i.aplant), AVG(i.yield), STDDEV_SAMP(i.yield), AVG(i.seedha), STDDEV_SAMP(i.seedha), AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha), AVG(i.herbaiha), STDDEV_SAMP(i.herbaiha), AVG(i.insectai), STDDEV_SAMP(i.insectai), AVG(i.fungikgai), STDDEV_SAMP(i.fungikgai), AVG(i.othpkgai), STDDEV_SAMP(i.othpkgai), AVG(i.hlabseed), STDDEV_SAMP(i.hlabseed), AVG(i.hlablandprep), STDDEV_SAMP(i.hlablandprep), AVG(i.hlabcropest), STDDEV_SAMP(i.hlabcropest), AVG(i.hlabcropcare), STDDEV_SAMP(i.hlabcropcare), AVG(i.hlabht), STDDEV_SAMP(i.hlabht), AVG(i.hlabpost), STDDEV_SAMP(i.hlabpost), AVG(i.permlabor), STDDEV_SAMP(i.permlabor), AVG(i.preseed), STDDEV_SAMP(i.preseed), AVG(i.prelandprep), STDDEV_SAMP(i.prelandprep), AVG(i.precropest), STDDEV_SAMP(i.precropest), AVG(i.precropcare), STDDEV_SAMP(i.precropcare), AVG(i.ilabht), STDDEV_SAMP(i.ilabht), AVG(i.ilabpost), STDDEV_SAMP(i.ilabpost) FROM matrix_ioutput i JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.province = ? AND i.yield != 0 AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.municipality = i.municipality AND matrix_irrigation.barangay = i.barangay AND matrix_irrigation.hh_number = i.hh_number AND matrix_irrigation.season = i.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5)');
			$rain_stmt = $conn->prepare('SELECT AVG(i.aplant), STDDEV_SAMP(i.aplant), AVG(i.yield), STDDEV_SAMP(i.yield), AVG(i.seedha), STDDEV_SAMP(i.seedha), AVG(i.nha), STDDEV_SAMP(i.nha), AVG(0.4364 * i.pha), STDDEV_SAMP(0.4364 * i.pha), AVG(0.8302 * i.kha), STDDEV_SAMP(0.8302 * i.kha), AVG(i.herbaiha), STDDEV_SAMP(i.herbaiha), AVG(i.insectai), STDDEV_SAMP(i.insectai), AVG(i.fungikgai), STDDEV_SAMP(i.fungikgai), AVG(i.othpkgai), STDDEV_SAMP(i.othpkgai), AVG(i.hlabseed), STDDEV_SAMP(i.hlabseed), AVG(i.hlablandprep), STDDEV_SAMP(i.hlablandprep), AVG(i.hlabcropest), STDDEV_SAMP(i.hlabcropest), AVG(i.hlabcropcare), STDDEV_SAMP(i.hlabcropcare), AVG(i.hlabht), STDDEV_SAMP(i.hlabht), AVG(i.hlabpost), STDDEV_SAMP(i.hlabpost), AVG(i.permlabor), STDDEV_SAMP(i.permlabor), AVG(i.preseed), STDDEV_SAMP(i.preseed), AVG(i.prelandprep), STDDEV_SAMP(i.prelandprep), AVG(i.precropest), STDDEV_SAMP(i.precropest), AVG(i.precropcare), STDDEV_SAMP(i.precropcare), AVG(i.ilabht), STDDEV_SAMP(i.ilabht), AVG(i.ilabpost), STDDEV_SAMP(i.ilabpost) FROM matrix_ioutput i JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = i.region AND matrix_rectype1.province = i.province AND matrix_rectype1.municipality = i.municipality AND matrix_rectype1.barangay = i.barangay AND matrix_rectype1.hh_number = i.hh_number AND matrix_rectype1.season = i.season AND i.season = ? AND i.province = ? AND i.yield != 0 AND matrix_irrigation.region = i.region AND matrix_irrigation.province = i.province AND matrix_irrigation.municipality = i.municipality AND matrix_irrigation.barangay = i.barangay AND matrix_irrigation.hh_number = i.hh_number AND matrix_irrigation.season = i.season AND matrix_irrigation.irrigation_source =  0');
			$all_stmt->bind_param("ss", $season, $province);
			$irri_stmt->bind_param("ss", $season, $province);
			$rain_stmt->bind_param("ss", $season, $province);
		}
	}
	$keywords = array("aplant", "yield", "seedha", "nha", "pha", "kha", "herbaiha", "insectai", "fungikgai", "othpkgai", "hlabpre", "hlabseed", "hlablandprep", "hlabcropest", "hlabcropcare", "hlabht", "hlabpost", "permlabor", "prelabha", "preseed", "prelandprep", "precropest", "precropcare", "ilabht", "ilabpost");
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
	
	foreach ($iout as $key => $value){
		if(count($iout[$key]) < count($iout['aplant'])){
			$temp = count($iout[$key]);
			for($i = 0; $i < count($iout['aplant']) - $temp; $i++){
				array_push($iout[$key], "...");
			}
		}
	}
	
	$all_stmt->close();
	}//SEASON END BRACKET
	foreach ($iout as $key => $value){
		for($i = 0; $i < count($seasons) * 1; $i++){
			if( empty( $iout[$key][$i] ) ){
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
	create_special_rows("Item", $headings, 2);
	create_special_rows("", array_formatting($n_stat,"(n = ",")"), 2);
	create_headings("", $headings2);
	create_row_bold("Area planted (ha)", $iout['aplant'], 2);
	humanized_number("Yield (kg/ha)", $iout['yield']);
	echo "<tr>\n<td class='header4 left bold' colspan=".$count_columns.">Material Inputs</td>\n</tr>\n";
	create_row("&nbsp;&nbsp;&nbsp;Seed (kg/ha)", $iout['seedha'], 2);
	echo "<tr>\n<td class='left' colspan=".$count_columns.">&nbsp;&nbsp;&nbsp;Fertilizer (kg/ha)</td>\n</tr>\n";
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;N", $iout['nha'], 2);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;P", $iout['pha'], 2);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;K", $iout['kha'], 2);
	create_row("&nbsp;&nbsp;&nbsp;Herbicide (kg or L AI<sup>b</sup>/ha)", $iout['herbaiha'], 2);
	create_row("&nbsp;&nbsp;&nbsp;Insecticide (kg or L AI/ha)", $iout['insectai'], 2);
	create_row("&nbsp;&nbsp;&nbsp;Fungicide (kg or L AI/ha)", $iout['fungikgai'], 2);
	create_row("&nbsp;&nbsp;&nbsp;Other pesticide (kg or L AI/ha)", $iout['othpkgai'], 2);
	echo "<tr>\n<td class='header4 left bold' colspan=".$count_columns.">Labor Requirements (person-days/ha)</td>\n</tr>\n";
	echo "<tr>\n<td class='left bold' colspan=".$count_columns.">&nbsp;&nbsp;&nbsp;Hired labor</td>\n</tr>\n";
	if(max($seasons) < 5) {
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pre Harvest Labor", $iout['hlabpre'], 2);
	}
	else{
		$prehired = create_total_arr($iout['hlabseed'], $iout['hlablandprep'], $iout['hlabcropest'], $iout['hlabcropcare']);
		foreach($prehired as $key => &$value){
			if($value == 0 && $iout['hlabpre'][$key] != '...'){
				$value = $iout['hlabpre'][$key];
			}
		}
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pre Harvest Labor", $prehired, 2);
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Seedling management", $iout['hlabseed'], 2);
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Land preparation", $iout['hlablandprep'], 2);
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Crop establishment", $iout['hlabcropest'], 2);
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Crop care and maintenance", $iout['hlabcropcare'], 2);
	}
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Harvesting and threshing", $iout['hlabht'], 2);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Post-harvest labor", $iout['hlabpost'], 2);
	if(max($seasons) >= 5) {
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Permanent hired labor", $iout['permlabor'], 2);
	}
	echo "<tr>\n<td class='left bold' colspan=".$count_columns.">&nbsp;&nbsp;&nbsp;Operator, Family & Exchange (OFE) Labor</td>\n</tr>\n";
	if(max($seasons) < 5) {
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pre Harvest Labor", $iout['prelabha'], 2);
	}
	else{
		$preofe = create_total_arr($iout['preseed'], $iout['prelandprep'], $iout['precropest'], $iout['precropcare']);
		foreach($preofe as $key => &$value){
			if($value == 0 && $iout['prelabha'][$key] != '...'){
				$value = $iout['prelabha'][$key];
			}
		}
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pre Harvest Labor", $preofe, 2);
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Seedling management", $iout['preseed'], 2);
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Land preparation", $iout['prelandprep'], 2);
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Crop establishment", $iout['precropest'], 2);
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Crop care and maintenance", $iout['precropcare'], 2);
	}
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Harvesting and threshing", $iout['ilabht'], 2);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Post-harvest labor", $iout['ilabpost'], 2);
	if(max($seasons) < 5) {
		$totlab = create_total_arr($iout['hlabpre'], $iout['hlabht'], $iout['hlabpost'], $iout['prelabha'], $iout['ilabht'], $iout['ilabpost']);
	}
	else{
		$totlab = create_total_arr($prehired, $iout['hlabht'], $iout['hlabpost'], $iout['permlabor'], $preofe, $iout['ilabht'], $iout['ilabpost']);
	}
	for($i = 0; $i < count($totlab); $i++){
		if($i % 2 != 0){
			$totlab[$i] = "";
		}
	}
	create_row_bold("&nbsp;&nbsp;&nbsp; Total Labor", $totlab, 0);
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
			echo '<div>* Excludes farmers who temporarily stopped farming/did not plant rice/experienced crop failure/with missing data for '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$n_stat[$i*3]) .')</div>';
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
echo "<br/>\n<div><sup>a </sup><b>SD</b> - refers to standard deviation</div>\n";
echo "<div><sup>b </sup><b>AI</b> - refers to active ingredient</div>\n<br/>\n";
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