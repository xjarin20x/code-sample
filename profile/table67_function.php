<?php
	require_once("../includes/headeralt.php");
?>
<div id="tableData">
<?php
	$provinces = disinfect_var($_POST['provinces']);
	$seasons = disinfect_var($_POST['seasons']);

	$content=count($provinces);
	
	$stmt = $total_stmt = "";
	$total = $codename = $percent = 0;
	
	$lop = implode(',', $provinces);
	$los = implode(',', $seasons);
	$region = 0;
	
	foreach($provinces as $province){
	$season_names =  $headings = $headings2 = $footnotes = array();
	$counter = 0;
	$total_farmers = $costr = $n_stat = $total_n = array();
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
	
	for($i=0; $i < 3; $i++){
		array_push($headings2, "Mean");	
		array_push($headings2, "SD<sup>a</sup>");
	}
	
	if($province==999){
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput, matrix_creturns WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_rectype1.region = matrix_creturns.region AND matrix_rectype1.province = matrix_creturns.province AND matrix_rectype1.municipality = matrix_creturns.municipality AND matrix_rectype1.barangay = matrix_creturns.barangay AND matrix_rectype1.hh_number = matrix_creturns.hh_number AND matrix_rectype1.season = matrix_creturns.season AND matrix_rectype1.season = ? AND matrix_ioutput.yield != 0) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput, matrix_creturns, matrix_irrigation WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_rectype1.region = matrix_creturns.region AND matrix_rectype1.province = matrix_creturns.province AND matrix_rectype1.municipality = matrix_creturns.municipality AND matrix_rectype1.barangay = matrix_creturns.barangay AND matrix_rectype1.hh_number = matrix_creturns.hh_number AND matrix_rectype1.season = matrix_creturns.season AND matrix_irrigation.region = matrix_ioutput.region AND matrix_irrigation.province = matrix_ioutput.province AND matrix_irrigation.municipality = matrix_ioutput.municipality AND matrix_irrigation.barangay = matrix_ioutput.barangay AND matrix_irrigation.hh_number = matrix_ioutput.hh_number AND matrix_irrigation.season = matrix_ioutput.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_rectype1.season = ? AND matrix_ioutput.yield != 0) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput, matrix_creturns, matrix_irrigation WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_rectype1.region = matrix_creturns.region AND matrix_rectype1.province = matrix_creturns.province AND matrix_rectype1.municipality = matrix_creturns.municipality AND matrix_rectype1.barangay = matrix_creturns.barangay AND matrix_rectype1.hh_number = matrix_creturns.hh_number AND matrix_rectype1.season = matrix_creturns.season AND matrix_irrigation.region = matrix_ioutput.region AND matrix_irrigation.province = matrix_ioutput.province AND matrix_irrigation.municipality = matrix_ioutput.municipality AND matrix_irrigation.barangay = matrix_ioutput.barangay AND matrix_irrigation.hh_number = matrix_ioutput.hh_number AND matrix_irrigation.season = matrix_ioutput.season AND matrix_irrigation.irrigation_source = 0 AND matrix_rectype1.season = ? AND matrix_ioutput.yield != 0) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput, matrix_creturns WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_rectype1.region = matrix_creturns.region AND matrix_rectype1.province = matrix_creturns.province AND matrix_rectype1.municipality = matrix_creturns.municipality AND matrix_rectype1.barangay = matrix_creturns.barangay AND matrix_rectype1.hh_number = matrix_creturns.hh_number AND matrix_rectype1.season = matrix_creturns.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_ioutput.yield != 0) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput, matrix_creturns, matrix_irrigation WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_rectype1.region = matrix_creturns.region AND matrix_rectype1.province = matrix_creturns.province AND matrix_rectype1.municipality = matrix_creturns.municipality AND matrix_rectype1.barangay = matrix_creturns.barangay AND matrix_rectype1.hh_number = matrix_creturns.hh_number AND matrix_rectype1.season = matrix_creturns.season AND matrix_irrigation.region = matrix_ioutput.region AND matrix_irrigation.province = matrix_ioutput.province AND matrix_irrigation.municipality = matrix_ioutput.municipality AND matrix_irrigation.barangay = matrix_ioutput.barangay AND matrix_irrigation.hh_number = matrix_ioutput.hh_number AND matrix_irrigation.season = matrix_ioutput.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_ioutput.yield != 0) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_ioutput, matrix_creturns, matrix_irrigation WHERE matrix_rectype1.region = matrix_ioutput.region AND matrix_rectype1.province = matrix_ioutput.province AND matrix_rectype1.municipality = matrix_ioutput.municipality AND matrix_rectype1.barangay = matrix_ioutput.barangay AND matrix_rectype1.hh_number = matrix_ioutput.hh_number AND matrix_rectype1.season = matrix_ioutput.season AND matrix_rectype1.region = matrix_creturns.region AND matrix_rectype1.province = matrix_creturns.province AND matrix_rectype1.municipality = matrix_creturns.municipality AND matrix_rectype1.barangay = matrix_creturns.barangay AND matrix_rectype1.hh_number = matrix_creturns.hh_number AND matrix_rectype1.season = matrix_creturns.season AND matrix_irrigation.region = matrix_ioutput.region AND matrix_irrigation.province = matrix_ioutput.province AND matrix_irrigation.municipality = matrix_ioutput.municipality AND matrix_irrigation.barangay = matrix_ioutput.barangay AND matrix_irrigation.hh_number = matrix_ioutput.hh_number AND matrix_irrigation.season = matrix_ioutput.season AND matrix_irrigation.irrigation_source = 0 AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_ioutput.yield != 0) a");
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
		$all_stmt = $conn->prepare('SELECT 
		AVG(c.hlabseedco), STDDEV_SAMP(c.hlabseedco), 
		AVG(c.hlablandprepco), STDDEV_SAMP(c.hlablandprepco), 
		AVG(c.hlabcropestco), STDDEV_SAMP(c.hlabcropestco), 
		AVG(c.hlabcropcareco), STDDEV_SAMP(c.hlabcropcareco), 
		AVG(c.hlabharvco), STDDEV_SAMP(c.hlabharvco), 
		AVG(c.hlabpostco), STDDEV_SAMP(c.hlabpostco), 
		AVG(c.permlabco), STDDEV_SAMP(c.permlabco), 
		AVG(c.olabseedco), STDDEV_SAMP(c.olabseedco),  
		AVG(c.olablandprepco), STDDEV_SAMP(c.olablandprepco), 
		AVG(c.olabcropestco), STDDEV_SAMP(c.olabcropestco), 
		AVG(c.olabcropcareco), STDDEV_SAMP(c.olabcropcareco),  
		AVG(c.olabharvco), STDDEV_SAMP(c.olabharvco),  
		AVG(c.olabpostco), STDDEV_SAMP(c.olabpostco) 
		FROM matrix_creturns c JOIN matrix_rectype1 r, matrix_ioutput i WHERE r.region = i.region AND r.province = i.province AND r.municipality = i.municipality AND r.barangay = i.barangay AND r.hh_number = i.hh_number AND r.season = i.season AND r.region = c.region AND r.province = c.province AND r.municipality = c.municipality AND r.barangay = c.barangay AND r.hh_number = c.hh_number AND r.season = c.season AND c.season = ? AND i.yield != 0');
		$irri_stmt = $conn->prepare('SELECT 
		AVG(c.hlabseedco), STDDEV_SAMP(c.hlabseedco), 
		AVG(c.hlablandprepco), STDDEV_SAMP(c.hlablandprepco),  
		AVG(c.hlabcropestco), STDDEV_SAMP(c.hlabcropestco), 
		AVG(c.hlabcropcareco), STDDEV_SAMP(c.hlabcropcareco),  
		AVG(c.hlabharvco), STDDEV_SAMP(c.hlabharvco), 
		AVG(c.hlabpostco), STDDEV_SAMP(c.hlabpostco), 
		AVG(c.permlabco), STDDEV_SAMP(c.permlabco), 
		AVG(c.olabseedco), STDDEV_SAMP(c.olabseedco), 
		AVG(c.olablandprepco), STDDEV_SAMP(c.olablandprepco), 
		AVG(c.olabcropestco), STDDEV_SAMP(c.olabcropestco), 
		AVG(c.olabcropcareco), STDDEV_SAMP(c.olabcropcareco), 
		AVG(c.olabharvco), STDDEV_SAMP(c.olabharvco),  
		AVG(c.olabpostco), STDDEV_SAMP(c.olabpostco) 
		FROM matrix_creturns c JOIN matrix_rectype1 r, matrix_ioutput i, matrix_irrigation ir WHERE r.region = i.region AND r.province = i.province AND r.municipality = i.municipality AND r.barangay = i.barangay AND r.hh_number = i.hh_number AND r.season = i.season AND r.region = c.region AND r.province = c.province AND r.municipality = c.municipality AND r.barangay = c.barangay AND r.hh_number = c.hh_number AND r.season = c.season AND ir.region = i.region AND ir.province = i.province AND ir.municipality = i.municipality AND ir.barangay = i.barangay AND ir.hh_number = i.hh_number AND ir.season = i.season AND (ir.irrigation_source = 1 OR ir.irrigation_source = 2 OR ir.irrigation_source = 3 OR ir.irrigation_source = 4 OR ir.irrigation_source = 5) AND c.season = ? AND i.yield != 0');
		$rain_stmt = $conn->prepare('SELECT 
		AVG(c.hlabseedco), STDDEV_SAMP(c.hlabseedco), 
		AVG(c.hlablandprepco), STDDEV_SAMP(c.hlablandprepco), 
		AVG(c.hlabcropestco), STDDEV_SAMP(c.hlabcropestco), 
		AVG(c.hlabcropcareco), STDDEV_SAMP(c.hlabcropcareco), 
		AVG(c.hlabharvco), STDDEV_SAMP(c.hlabharvco), 
		AVG(c.hlabpostco), STDDEV_SAMP(c.hlabpostco), 
		AVG(c.permlabco), STDDEV_SAMP(c.permlabco), 
		AVG(c.olabseedco), STDDEV_SAMP(c.olabseedco),  
		AVG(c.olablandprepco), STDDEV_SAMP(c.olablandprepco),  
		AVG(c.olabcropestco), STDDEV_SAMP(c.olabcropestco), 
		AVG(c.olabcropcareco), STDDEV_SAMP(c.olabcropcareco),  
		AVG(c.olabharvco), STDDEV_SAMP(c.olabharvco),  
		AVG(c.olabpostco), STDDEV_SAMP(c.olabpostco) 
		FROM matrix_creturns c JOIN matrix_rectype1 r, matrix_ioutput i, matrix_irrigation ir WHERE r.region = i.region AND r.province = i.province AND r.municipality = i.municipality AND r.barangay = i.barangay AND r.hh_number = i.hh_number AND r.season = i.season AND r.region = c.region AND r.province = c.province AND r.municipality = c.municipality AND r.barangay = c.barangay AND r.hh_number = c.hh_number AND r.season = c.season AND ir.region = i.region AND ir.province = i.province AND ir.municipality = i.municipality AND ir.barangay = i.barangay AND ir.hh_number = i.hh_number AND ir.season = i.season AND ir.irrigation_source = 0 AND c.season = ? AND i.yield != 0');
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt = $conn->prepare('SELECT 
		AVG(c.hlabseedco), STDDEV_SAMP(c.hlabseedco), 
		AVG(c.hlablandprepco), STDDEV_SAMP(c.hlablandprepco), 
		AVG(c.hlabcropestco), STDDEV_SAMP(c.hlabcropestco), 
		AVG(c.hlabcropcareco), STDDEV_SAMP(c.hlabcropcareco), 
		AVG(c.hlabharvco), STDDEV_SAMP(c.hlabharvco), 
		AVG(c.hlabpostco), STDDEV_SAMP(c.hlabpostco), 
		AVG(c.permlabco), STDDEV_SAMP(c.permlabco), 
		AVG(c.olabseedco), STDDEV_SAMP(c.olabseedco),  
		AVG(c.olablandprepco), STDDEV_SAMP(c.olablandprepco),  
		AVG(c.olabcropestco), STDDEV_SAMP(c.olabcropestco), 
		AVG(c.olabcropcareco), STDDEV_SAMP(c.olabcropcareco),  
		AVG(c.olabharvco), STDDEV_SAMP(c.olabharvco),  
		AVG(c.olabpostco), STDDEV_SAMP(c.olabpostco) 
		FROM matrix_creturns c JOIN matrix_rectype1 r, matrix_ioutput i WHERE r.region = i.region AND r.province = i.province AND r.municipality = i.municipality AND r.barangay = i.barangay AND r.hh_number = i.hh_number AND r.season = i.season AND r.region = c.region AND r.province = c.province AND r.municipality = c.municipality AND r.barangay = c.barangay AND r.hh_number = c.hh_number AND r.season = c.season AND c.season = ? AND c.province = ? AND i.yield != 0');
		$irri_stmt = $conn->prepare('SELECT 
		AVG(c.hlabseedco), STDDEV_SAMP(c.hlabseedco), 
		AVG(c.hlablandprepco), STDDEV_SAMP(c.hlablandprepco), 
		AVG(c.hlabcropestco), STDDEV_SAMP(c.hlabcropestco), 
		AVG(c.hlabcropcareco), STDDEV_SAMP(c.hlabcropcareco), 
		AVG(c.hlabharvco), STDDEV_SAMP(c.hlabharvco), 
		AVG(c.hlabpostco), STDDEV_SAMP(c.hlabpostco), 
		AVG(c.permlabco), STDDEV_SAMP(c.permlabco), 
		AVG(c.olabseedco), STDDEV_SAMP(c.olabseedco),  
		AVG(c.olablandprepco), STDDEV_SAMP(c.olablandprepco),  
		AVG(c.olabcropestco), STDDEV_SAMP(c.olabcropestco), 
		AVG(c.olabcropcareco), STDDEV_SAMP(c.olabcropcareco),  
		AVG(c.olabharvco), STDDEV_SAMP(c.olabharvco),  
		AVG(c.olabpostco), STDDEV_SAMP(c.olabpostco) 
		FROM matrix_creturns c JOIN matrix_rectype1 r, matrix_ioutput i, matrix_irrigation ir WHERE r.region = i.region AND r.province = i.province AND r.municipality = i.municipality AND r.barangay = i.barangay AND r.hh_number = i.hh_number AND r.season = i.season AND r.region = c.region AND r.province = c.province AND r.municipality = c.municipality AND r.barangay = c.barangay AND r.hh_number = c.hh_number AND r.season = c.season AND ir.region = i.region AND ir.province = i.province AND ir.municipality = i.municipality AND ir.barangay = i.barangay AND ir.hh_number = i.hh_number AND ir.season = i.season AND (ir.irrigation_source = 1 OR ir.irrigation_source = 2 OR ir.irrigation_source = 3 OR ir.irrigation_source = 4 OR ir.irrigation_source = 5) AND c.season = ? AND c.province = ? AND i.yield != 0');
		$rain_stmt = $conn->prepare('SELECT 
		AVG(c.hlabseedco), STDDEV_SAMP(c.hlabseedco), 
		AVG(c.hlablandprepco), STDDEV_SAMP(c.hlablandprepco), 
		AVG(c.hlabcropestco), STDDEV_SAMP(c.hlabcropestco), 
		AVG(c.hlabcropcareco), STDDEV_SAMP(c.hlabcropcareco), 
		AVG(c.hlabharvco), STDDEV_SAMP(c.hlabharvco), 
		AVG(c.hlabpostco), STDDEV_SAMP(c.hlabpostco), 
		AVG(c.permlabco), STDDEV_SAMP(c.permlabco), 
		AVG(c.olabseedco), STDDEV_SAMP(c.olabseedco),  
		AVG(c.olablandprepco), STDDEV_SAMP(c.olablandprepco),  
		AVG(c.olabcropestco), STDDEV_SAMP(c.olabcropestco), 
		AVG(c.olabcropcareco), STDDEV_SAMP(c.olabcropcareco),  
		AVG(c.olabharvco), STDDEV_SAMP(c.olabharvco),  
		AVG(c.olabpostco), STDDEV_SAMP(c.olabpostco) 
		FROM matrix_creturns c JOIN matrix_rectype1 r, matrix_ioutput i, matrix_irrigation ir WHERE r.region = i.region AND r.province = i.province AND r.municipality = i.municipality AND r.barangay = i.barangay AND r.hh_number = i.hh_number AND r.season = i.season AND r.region = c.region AND r.province = c.province AND r.municipality = c.municipality AND r.barangay = c.barangay AND r.hh_number = c.hh_number AND r.season = c.season AND ir.region = i.region AND ir.province = i.province AND ir.municipality = i.municipality AND ir.barangay = i.barangay AND ir.hh_number = i.hh_number AND ir.season = i.season AND ir.irrigation_source = 0 AND c.season = ? AND c.province = ? AND i.yield != 0');
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
	
	$keywords = array("hlabseedco", "hlablandprepco", "hlabcropestco", "hlabcropcareco", "hlabharvco", "hlabpostco", "permlabco", "olabseedco", "olablandprepco", "olabcropestco", "olabcropcareco", "olabharvco", "olabpostco");
	$all_stmt->execute();
	$all_stmt->store_result();
	$res_arr = fetch_get_result_alt($all_stmt);
	// PHP 5.3
	// $res = $all_stmt->get_result();
	// $res_arr = $res->fetch_assoc();
	foreach ($keywords as $value){
		if(!isset($costr[$value])){
			$costr[$value] = array();
		}
		foreach ($res_arr as $key => $n){
			if($pos = strpos($key, ".". $value)){
				array_push($costr[$value], $n);
				unset($res_arr[$key]);
			}
		}
	}
	foreach ($costr as $key => $value){
		if(count($costr[$key]) < count($costr['hlabharvco'])){
			$temp = count($costr[$key]);
			for($i = 0; $i < count($costr['hlabharvco']) - $temp; $i++){
				array_push($costr[$key], "...");
			}
		}
	}
	$all_stmt->close();
	//irrigated
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$res_arr = fetch_get_result_alt($irri_stmt);
	// PHP 5.3
	// $res = $irri_stmt->get_result();
	// $res_arr = $res->fetch_assoc();
	foreach ($keywords as $value){
		if(!isset($costr[$value])){
			$costr[$value] = array();
		}
		foreach ($res_arr as $key => $n){
			if($pos = strpos($key, ".". $value)){
				array_push($costr[$value], $n);
				unset($res_arr[$key]);
			}
		}
	}
	foreach ($costr as $key => $value){
		if(count($costr[$key]) < count($costr['hlabharvco'])){
			$temp = count($costr[$key]);
			for($i = 0; $i < count($costr['hlabharvco']) - $temp; $i++){
				array_push($costr[$key], "...");
			}
		}
	}
	$irri_stmt->close();
	//rainfed
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$res_arr = fetch_get_result_alt($rain_stmt);
	// PHP 5.3
	// $res = $rain_stmt->get_result();
	// $res_arr = $res->fetch_assoc();
	foreach ($keywords as $value){
		if(!isset($costr[$value])){
			$costr[$value] = array();
		}
		foreach ($res_arr as $key => $n){
			if($pos = strpos($key, ".". $value)){
				array_push($costr[$value], $n);
				unset($res_arr[$key]);
			}
		}
	}
	foreach ($costr as $key => $value){
		if(count($costr[$key]) < count($costr['hlabharvco'])){
			$temp = count($costr[$key]);
			for($i = 0; $i < count($costr['hlabharvco']) - $temp; $i++){
				array_push($costr[$key], "...");
			}
		}
	}
	$rain_stmt->close();
	}//SEASON END BRACKET
	foreach ($costr as $key => $value){
			for($i = 0; $i < count($seasons) * 1; $i++){
			if( empty( $costr[$key][$i] ) ){
				$costr[$key][$i] = 0;
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
	create_special_rows("Items", $headings, 2);
	create_special_rows("", array_formatting($total_farmers,"(n = ",")"), 2);
	create_headings("", $headings2);
	echo "<tr>\n<td class='header4 left bold' colspan=".$count_columns.">Hired Labor (₱/ha)</td>\n</tr>\n";
	humanized_number("&nbsp;&nbsp;&nbsp;&nbsp;Seedling management", $costr['hlabseedco']);
	humanized_number("&nbsp;&nbsp;&nbsp;&nbsp;Land preparation", $costr['hlablandprepco']);
	humanized_number("&nbsp;&nbsp;&nbsp;&nbsp;Crop establishment", $costr['hlabcropestco']);
	humanized_number("&nbsp;&nbsp;&nbsp;&nbsp;Crop care and maintenance", $costr['hlabcropcareco']);
	humanized_number("&nbsp;&nbsp;&nbsp;&nbsp;Harvesting and threshing", $costr['hlabharvco']);
	humanized_number("&nbsp;&nbsp;&nbsp;&nbsp;Post-harvest labor", $costr['hlabpostco']);
	humanized_number("&nbsp;&nbsp;&nbsp;&nbsp;Permanent hired labor", $costr['permlabco']);
	echo "<tr>\n<td class='header4 left bold' colspan=".$count_columns.">Operator, Family & Exchange (OFE) Labor (₱/ha)</td>\n</tr>\n";
	humanized_number("&nbsp;&nbsp;&nbsp;&nbsp;Seedling management", $costr['olabseedco']);
	humanized_number("&nbsp;&nbsp;&nbsp;&nbsp;Land preparation", $costr['olablandprepco']);
	humanized_number("&nbsp;&nbsp;&nbsp;&nbsp;Crop establishment", $costr['olabcropestco']);
	humanized_number("&nbsp;&nbsp;&nbsp;&nbsp;Crop care and maintenance", $costr['olabcropcareco']);
	humanized_number("&nbsp;&nbsp;&nbsp;&nbsp;Harvesting and threshing", $costr['olabharvco']);
	humanized_number("&nbsp;&nbsp;&nbsp;&nbsp;Post-harvest labor", $costr['olabpostco']);
	$labor_total = create_total_arr($costr['hlabseedco'], $costr['hlablandprepco'], $costr['hlabcropestco'], $costr['hlabcropcareco'], $costr['hlabharvco'], $costr['hlabpostco'], $costr['permlabco'], $costr['olabseedco'], $costr['olablandprepco'], $costr['olabcropestco'], $costr['olabcropcareco'], $costr['olabharvco'], $costr['olabpostco']);
	for($i = 0; $i < count($labor_total); $i++){
		if($i % 2 != 0){
			$labor_total[$i] = "";
		}
	}
	humanized_number_bold("Total Labor Costs", $labor_total);
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
	$temp = count($seasons) * 3;
	$j = 0;
	for($i=0;$i<$temp;$i++){
		if($i % 3 == 0){
			if ($total_farmers[$i] < $total_n[$j]){
				echo '<div>* Excludes farmers who temporarily stopped farming/did not plant rice/experienced crop failure/with missing data during '. $footnotes[$j] .' harvest (n='. ($total_n[$j]-$total_farmers[$i]) .')</div>';
			}
			$j++;
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
echo "<br/>";
echo "<div>Data accessed at ". date('l jS \of F Y h:i:s A') . "</div>";
echo displayNoteContact();
?>
</div>
<?php
require_once("../includes/export.php");
?>