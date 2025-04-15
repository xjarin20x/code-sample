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
	$avg_income = $std_income = $min_income = $max_income = array();
	$p_capita = $t_income = $rf_income = $rn_income = $of_income = $on_income = $nr_income = array();
	$rf_income = $rn_income = $of_income = $on_income = $nr_income = array();
	$n_stat = $total_n = array();
	$count = 0;
	
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
			$all_stmt->bind_param("ss", $season,$province);
			$irri_stmt->bind_param("ss", $season,$province);
			$rain_stmt->bind_param("ss", $season,$province);
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
		
		if($province==999){
			$all_stmt= $conn->prepare("SELECT COUNT(matrix_rectype2.hh_number), AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as avg_total_income, STDDEV_SAMP(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  std_total_income, MIN(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  min_total_income, MAX(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  max_total_income, (AVG(matrix_rectype2.rice_off_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as rice_off, (AVG(matrix_rectype2.rice_on_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as rice_on, (AVG(matrix_rectype2.other_off_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as other_off, (AVG(matrix_rectype2.other_on_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as other_on, (AVG(matrix_rectype2.nonrice_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as nonrice FROM `matrix_rectype1` JOIN matrix_rectype2 WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND matrix_rectype1.season = ? AND matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income > 1 GROUP BY matrix_rectype1.season");
			$irri_stmt= $conn->prepare("SELECT COUNT(matrix_rectype2.hh_number), AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as avg_total_income, STDDEV_SAMP(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  std_total_income, MIN(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  min_total_income, MAX(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  max_total_income, (AVG(matrix_rectype2.rice_off_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as rice_off, (AVG(matrix_rectype2.rice_on_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as rice_on, (AVG(matrix_rectype2.other_off_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as other_off, (AVG(matrix_rectype2.other_on_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as other_on, (AVG(matrix_rectype2.nonrice_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as nonrice FROM `matrix_rectype1` JOIN matrix_rectype2, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income > 1 AND matrix_irrigation.irrigation_prime = 1 GROUP BY matrix_rectype1.season");
			$rain_stmt= $conn->prepare("SELECT COUNT(matrix_rectype2.hh_number), AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as avg_total_income, STDDEV_SAMP(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  std_total_income, MIN(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  min_total_income, MAX(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  max_total_income, (AVG(matrix_rectype2.rice_off_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as rice_off, (AVG(matrix_rectype2.rice_on_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as rice_on, (AVG(matrix_rectype2.other_off_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as other_off, (AVG(matrix_rectype2.other_on_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as other_on, (AVG(matrix_rectype2.nonrice_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as nonrice FROM `matrix_rectype1` JOIN matrix_rectype2, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income > 1 AND matrix_irrigation.irrigation_prime = 0 GROUP BY matrix_rectype1.season");
			$all_stmt->bind_param("s", $season);
			$irri_stmt->bind_param("s", $season);
			$rain_stmt->bind_param("s", $season);
		}
		else{
			$all_stmt= $conn->prepare("SELECT COUNT(matrix_rectype2.hh_number), AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as avg_total_income, STDDEV_SAMP(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  std_total_income, MIN(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  min_total_income, MAX(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  max_total_income, (AVG(matrix_rectype2.rice_off_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as rice_off, (AVG(matrix_rectype2.rice_on_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as rice_on, (AVG(matrix_rectype2.other_off_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as other_off, (AVG(matrix_rectype2.other_on_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as other_on, (AVG(matrix_rectype2.nonrice_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as nonrice FROM `matrix_rectype1` JOIN matrix_rectype2 WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income > 1 GROUP BY matrix_rectype1.season");
			$irri_stmt= $conn->prepare("SELECT COUNT(matrix_rectype2.hh_number), AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as avg_total_income, STDDEV_SAMP(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  std_total_income, MIN(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  min_total_income, MAX(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  max_total_income, (AVG(matrix_rectype2.rice_off_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as rice_off, (AVG(matrix_rectype2.rice_on_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as rice_on, (AVG(matrix_rectype2.other_off_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as other_off, (AVG(matrix_rectype2.other_on_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as other_on, (AVG(matrix_rectype2.nonrice_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as nonrice FROM `matrix_rectype1` JOIN matrix_rectype2, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income > 1 AND matrix_irrigation.irrigation_prime = 1 GROUP BY matrix_rectype1.season");
			$rain_stmt= $conn->prepare("SELECT COUNT(matrix_rectype2.hh_number), AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as avg_total_income, STDDEV_SAMP(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  std_total_income, MIN(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  min_total_income, MAX(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as  max_total_income, (AVG(matrix_rectype2.rice_off_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as rice_off, (AVG(matrix_rectype2.rice_on_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as rice_on, (AVG(matrix_rectype2.other_off_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as other_off, (AVG(matrix_rectype2.other_on_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as other_on, (AVG(matrix_rectype2.nonrice_income)*100) / AVG(matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income) as nonrice FROM `matrix_rectype1` JOIN matrix_rectype2, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype2.region AND matrix_rectype1.province = matrix_rectype2.province AND matrix_rectype1.municipality = matrix_rectype2.municipality AND matrix_rectype1.barangay = matrix_rectype2.barangay AND matrix_rectype1.hh_number = matrix_rectype2.hh_number AND matrix_rectype1.season = matrix_rectype2.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype2.rice_off_income + matrix_rectype2.rice_on_income + matrix_rectype2.other_off_income + matrix_rectype2.other_on_income + matrix_rectype2.nonrice_income > 1 AND matrix_irrigation.irrigation_prime = 0 GROUP BY matrix_rectype1.season");
			$all_stmt->bind_param("ss", $season, $province);
			$irri_stmt->bind_param("ss", $season, $province);
			$rain_stmt->bind_param("ss", $season, $province);
		}
		
		if ($season < 5){
			$divisor = 12;
		}
		else {
			$divisor = 6;
		}
		
		$all_stmt->execute();
		$all_stmt->store_result();
		$all_stmt->bind_result($concat, $avg_inc, $std_inc, $min_inc, $max_inc, $rice_off, $rice_on, $other_off, $other_on, $nonrice);
				
		if($all_stmt->num_rows != 0){
			while($all_stmt->fetch()){ 
			array_push($total_farmers, $concat);
			array_push($avg_income, $avg_inc);
			array_push($std_income, $std_inc);
			array_push($min_income, $min_inc);
			array_push($max_income, $max_inc);
			array_push($p_capita, ($avg_inc / $mean_hh[$count]) / $divisor);
			array_push($rf_income, $rice_off);
			array_push($rn_income, $rice_on);
			array_push($of_income, $other_off);
			array_push($on_income, $other_on);
			array_push($nr_income, $nonrice);
			}
		}
		else{
			array_push($total_farmers, 0);
			array_push($avg_income, 0);
			array_push($std_income, 0);
			array_push($min_income, 0);
			array_push($max_income, 0);
			array_push($p_capita, 0);
			array_push($rf_income, 0);
			array_push($rn_income, 0);
			array_push($of_income, 0);
			array_push($on_income, 0);
			array_push($nr_income, 0);
		}
		
		$count++;
		$irri_stmt->execute();
		$irri_stmt->store_result();
		$irri_stmt->bind_result($concat, $avg_inc, $std_inc, $min_inc, $max_inc, $rice_off, $rice_on, $other_off, $other_on, $nonrice);
				
		if($irri_stmt->num_rows != 0){
			while($irri_stmt->fetch()){ 
			array_push($total_farmers, $concat);
			array_push($avg_income, $avg_inc);
			array_push($std_income, $std_inc);
			array_push($min_income, $min_inc);
			array_push($max_income, $max_inc);
			array_push($p_capita, ($avg_inc / $mean_hh[$count]) / $divisor);
			array_push($rf_income, $rice_off);
			array_push($rn_income, $rice_on);
			array_push($of_income, $other_off);
			array_push($on_income, $other_on);
			array_push($nr_income, $nonrice);
			}
		}
		else{
			array_push($total_farmers, 0);
			array_push($avg_income, 0);
			array_push($std_income, 0);
			array_push($min_income, 0);
			array_push($max_income, 0);
			array_push($p_capita, 0);
			array_push($rf_income, 0);
			array_push($rn_income, 0);
			array_push($of_income, 0);
			array_push($on_income, 0);
			array_push($nr_income, 0);
		}
		
		$count++;
		$rain_stmt->execute();
		$rain_stmt->store_result();
		$rain_stmt->bind_result($concat, $avg_inc, $std_inc, $min_inc, $max_inc, $rice_off, $rice_on, $other_off, $other_on, $nonrice);
				
		if($rain_stmt->num_rows != 0){
			while($rain_stmt->fetch()){ 
			array_push($total_farmers, $concat);
			array_push($avg_income, $avg_inc);
			array_push($std_income, $std_inc);
			array_push($min_income, $min_inc);
			array_push($max_income, $max_inc);
			array_push($p_capita, ($avg_inc / $mean_hh[$count]) / $divisor);
			array_push($rf_income, $rice_off);
			array_push($rn_income, $rice_on);
			array_push($of_income, $other_off);
			array_push($on_income, $other_on);
			array_push($nr_income, $nonrice);
			}
		}
		else{
			array_push($total_farmers, 0);
			array_push($avg_income, 0);
			array_push($std_income, 0);
			array_push($min_income, 0);
			array_push($max_income, 0);
			array_push($p_capita, 0);
			array_push($rf_income, 0);
			array_push($rn_income, 0);
			array_push($of_income, 0);
			array_push($on_income, 0);
			array_push($nr_income, 0);
		}
		$count++;
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
	
	create_header($name, $season_names, 3);
	echo "<tbody>\n";
	create_headings("ITEM", $headings);
	create_special_rows("", array_formatting($n_stat,"(n = ",")"), 1);
	humanized_number("Monthly per capita income (&#8369;)", $p_capita, 0);
	echo "<tr>\n<td class='header4 left' colspan=".$count_columns.">Distribution of income by source (percent)</td>\n</tr>\n";
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rice Farming - On Farm", $rn_income, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rice Farming - Off Farm", $rf_income, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other Farming/Fishing - On Farm", $on_income, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Other Farming/Fishing - Off Farm", $of_income, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Non Farming", $nr_income, 0);
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
<div>Data accessed at <?php echo date('l jS \of F Y h:i:s A');?></div><br/>
<div><b>Rice Farming - On Farm</b> refers to the gross income derived from all rice parcels operated by the household regardless of tenure and location.</div>
<div><b>Rice Farming - Off Farm</b> includes gross income derived from machine and animal rental, and from serving as a hired worker for a rice farm operated by another household.</div>
<div><b>Other Farming/Fishing - On Farm</b> includes income derived from cultivating non-rice crops in all land parcels operated by the household as well as fishery, livestock, and other agricultural activity.</div>
<div><b>Other Farming/Fishing - Off Farm</b> includes income derived from machine and animal rental or serve as hired laborer/worker in a non-rice farm or other agricultural activity operated by another household.</div>
<div><b>Non Farming</b> includes regular income of household members from non-agricultural sources such as government/non-government employment, pensions, 4Ps, etc.</div>
<?php
echo displayNoteContact();
echo "</div>";
require_once("../includes/export.php");
?>