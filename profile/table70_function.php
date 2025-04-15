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
	
	$seedclass = array(); 

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

	$findlegend = $conn->prepare("SELECT DISTINCT season FROM legend_sclass ORDER BY season DESC");
	$findlegend->execute();
	$findlegend->store_result();
	$findlegend->bind_result($this);
	$season_pool = array();
	while($findlegend->fetch()){
		array_push($season_pool, $this);
	}
	$findlegend->close();
	$varlegend = 0;
	for($i=$season; $i > 0; $i--){
		if(in_array($i, $season_pool)) {
			$varlegend = $i;
			break;
		}
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
		$all_stmt= $conn->prepare("
		SELECT COUNT(*) FROM
		(
			SELECT ri.region, ri.province, ri.municipality, ri.barangay, ri.hh_number, ri.season, s.sclass, ri.srate FROM 
			(
				SELECT r.region, r.province, r.municipality, r.barangay, r.hh_number, r.season, i.seedha AS srate FROM 
				matrix_rectype1 r LEFT JOIN
				matrix_ioutput i ON
				r.region = i.region AND 
				r.province = i.province AND 
				r.municipality = i.municipality AND 
				r.barangay = i.barangay AND 
				r.hh_number = i.hh_number AND 
				r.season = i.season WHERE
				r.season = ? AND i.seedha IS NOT NULL AND i.yield != 0
			) ri 
			LEFT JOIN
			matrix_rectype6 s ON 
			ri.region = s.region AND 
			ri.province = s.province AND 
			ri.municipality = s.municipality AND 
			ri.barangay = s.barangay AND 
			ri.hh_number = s.hh_number AND 
			ri.season = s.season WHERE
			ri.season = ? AND s.sclass IS NOT NULL
		) ris
		");
		$irri_stmt= $conn->prepare("
		SELECT COUNT(*) FROM
		(
			SELECT ri.region, ri.province, ri.municipality, ri.barangay, ri.hh_number, ri.season, s.sclass, ri.srate FROM 
			(
				SELECT r.region, r.province, r.municipality, r.barangay, r.hh_number, r.season, i.seedha AS srate FROM 
				matrix_rectype1 r LEFT JOIN
				matrix_ioutput i ON
				r.region = i.region AND 
				r.province = i.province AND 
				r.municipality = i.municipality AND 
				r.barangay = i.barangay AND 
				r.hh_number = i.hh_number AND 
				r.season = i.season WHERE
				r.season = ? AND i.seedha IS NOT NULL AND i.yield != 0
			) ri 
			LEFT JOIN
			matrix_rectype6 s ON 
			ri.region = s.region AND 
			ri.province = s.province AND 
			ri.municipality = s.municipality AND 
			ri.barangay = s.barangay AND 
			ri.hh_number = s.hh_number AND 
			ri.season = s.season WHERE
			ri.season = ? AND s.sclass IS NOT NULL
		) ris JOIN 
		matrix_irrigation ir WHERE 
		ris.region = ir.region AND 
		ris.province = ir.province AND 
		ris.municipality = ir.municipality AND 
		ris.barangay = ir.barangay AND 
		ris.hh_number = ir.hh_number AND 
		ris.season = ir.season AND 
		(ir.irrigation_source = 1 OR ir.irrigation_source = 2 OR ir.irrigation_source = 3 OR ir.irrigation_source = 4 OR ir.irrigation_source = 5)
		");
		$rain_stmt= $conn->prepare("
		SELECT COUNT(*) FROM
		(
			SELECT ri.region, ri.province, ri.municipality, ri.barangay, ri.hh_number, ri.season, s.sclass, ri.srate FROM 
			(
				SELECT r.region, r.province, r.municipality, r.barangay, r.hh_number, r.season, i.seedha AS srate FROM 
				matrix_rectype1 r LEFT JOIN
				matrix_ioutput i ON
				r.region = i.region AND 
				r.province = i.province AND 
				r.municipality = i.municipality AND 
				r.barangay = i.barangay AND 
				r.hh_number = i.hh_number AND 
				r.season = i.season WHERE
				r.season = ? AND i.seedha IS NOT NULL AND i.yield != 0
			) ri 
			LEFT JOIN
			matrix_rectype6 s ON 
			ri.region = s.region AND 
			ri.province = s.province AND 
			ri.municipality = s.municipality AND 
			ri.barangay = s.barangay AND 
			ri.hh_number = s.hh_number AND 
			ri.season = s.season WHERE
			ri.season = ? AND s.sclass IS NOT NULL
		) ris JOIN 
		matrix_irrigation ir WHERE 
		ris.region = ir.region AND 
		ris.province = ir.province AND 
		ris.municipality = ir.municipality AND 
		ris.barangay = ir.barangay AND 
		ris.hh_number = ir.hh_number AND 
		ris.season = ir.season AND 
		ir.irrigation_source = 0	
		");
		$all_stmt->bind_param("ss", $season, $season);
		$irri_stmt->bind_param("ss", $season, $season);
		$rain_stmt->bind_param("ss", $season, $season);
	}
	else{
		$all_stmt= $conn->prepare("
		SELECT COUNT(*) FROM
		(
			SELECT ri.region, ri.province, ri.municipality, ri.barangay, ri.hh_number, ri.season, s.sclass, ri.srate FROM 
			(
				SELECT r.region, r.province, r.municipality, r.barangay, r.hh_number, r.season, i.seedha AS srate FROM 
				matrix_rectype1 r LEFT JOIN
				matrix_ioutput i ON
				r.region = i.region AND 
				r.province = i.province AND 
				r.municipality = i.municipality AND 
				r.barangay = i.barangay AND 
				r.hh_number = i.hh_number AND 
				r.season = i.season WHERE
				r.season = ? AND r.province = ? AND i.seedha IS NOT NULL AND i.yield != 0
			) ri 
			LEFT JOIN
			matrix_rectype6 s ON 
			ri.region = s.region AND 
			ri.province = s.province AND 
			ri.municipality = s.municipality AND 
			ri.barangay = s.barangay AND 
			ri.hh_number = s.hh_number AND 
			ri.season = s.season WHERE
			ri.season = ? AND ri.province = ? AND s.sclass IS NOT NULL
		) ris
		");
		$irri_stmt= $conn->prepare("
		SELECT COUNT(*) FROM
		(
			SELECT ri.region, ri.province, ri.municipality, ri.barangay, ri.hh_number, ri.season, s.sclass, ri.srate FROM 
			(
				SELECT r.region, r.province, r.municipality, r.barangay, r.hh_number, r.season, i.seedha AS srate FROM 
				matrix_rectype1 r LEFT JOIN
				matrix_ioutput i ON
				r.region = i.region AND 
				r.province = i.province AND 
				r.municipality = i.municipality AND 
				r.barangay = i.barangay AND 
				r.hh_number = i.hh_number AND 
				r.season = i.season WHERE
				r.season = ? AND r.province = ? AND i.seedha IS NOT NULL AND i.yield != 0
			) ri 
			LEFT JOIN
			matrix_rectype6 s ON 
			ri.region = s.region AND 
			ri.province = s.province AND 
			ri.municipality = s.municipality AND 
			ri.barangay = s.barangay AND 
			ri.hh_number = s.hh_number AND 
			ri.season = s.season WHERE
			ri.season = ? AND ri.province = ? AND s.sclass IS NOT NULL
		) ris JOIN 
		matrix_irrigation ir WHERE 
		ris.region = ir.region AND 
		ris.province = ir.province AND 
		ris.municipality = ir.municipality AND 
		ris.barangay = ir.barangay AND 
		ris.hh_number = ir.hh_number AND 
		ris.season = ir.season AND 
		(ir.irrigation_source = 1 OR ir.irrigation_source = 2 OR ir.irrigation_source = 3 OR ir.irrigation_source = 4 OR ir.irrigation_source = 5)
		");
		$rain_stmt= $conn->prepare("
		SELECT COUNT(*) FROM
		(
			SELECT ri.region, ri.province, ri.municipality, ri.barangay, ri.hh_number, ri.season, s.sclass, ri.srate FROM 
			(
				SELECT r.region, r.province, r.municipality, r.barangay, r.hh_number, r.season, i.seedha AS srate FROM 
				matrix_rectype1 r LEFT JOIN
				matrix_ioutput i ON
				r.region = i.region AND 
				r.province = i.province AND 
				r.municipality = i.municipality AND 
				r.barangay = i.barangay AND 
				r.hh_number = i.hh_number AND 
				r.season = i.season WHERE
				r.season = ? AND r.province = ? AND i.seedha IS NOT NULL AND i.yield != 0
			) ri 
			LEFT JOIN
			matrix_rectype6 s ON 
			ri.region = s.region AND 
			ri.province = s.province AND 
			ri.municipality = s.municipality AND 
			ri.barangay = s.barangay AND 
			ri.hh_number = s.hh_number AND 
			ri.season = s.season WHERE
			ri.season = ? AND ri.province = ? AND s.sclass IS NOT NULL
		) ris JOIN 
		matrix_irrigation ir WHERE 
		ris.region = ir.region AND 
		ris.province = ir.province AND 
		ris.municipality = ir.municipality AND 
		ris.barangay = ir.barangay AND 
		ris.hh_number = ir.hh_number AND 
		ris.season = ir.season AND 
		ir.irrigation_source = 0	
		");
		$all_stmt->bind_param("ssss", $season, $province, $season, $province);
		$irri_stmt->bind_param("ssss", $season, $province, $season, $province);
		$rain_stmt->bind_param("ssss", $season, $province, $season, $province);
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
		$all_stmt = $conn->prepare("
		SELECT ss.seedclass, AVG(ris.srate), STDDEV_SAMP(ris.srate) FROM
		(
			SELECT ri.region, ri.province, ri.municipality, ri.barangay, ri.hh_number, ri.season, s.sclass, ri.srate FROM 
			(
				SELECT r.region, r.province, r.municipality, r.barangay, r.hh_number, r.season, i.seedha AS srate FROM 
				matrix_rectype1 r LEFT JOIN
				matrix_ioutput i ON
				r.region = i.region AND 
				r.province = i.province AND 
				r.municipality = i.municipality AND 
				r.barangay = i.barangay AND 
				r.hh_number = i.hh_number AND 
				r.season = i.season WHERE
				r.season = ? AND i.seedha IS NOT NULL AND i.yield != 0
			) ri 
			LEFT JOIN
			matrix_rectype6 s ON 
			ri.region = s.region AND 
			ri.province = s.province AND 
			ri.municipality = s.municipality AND 
			ri.barangay = s.barangay AND 
			ri.hh_number = s.hh_number AND 
			ri.season = s.season WHERE
			ri.season = ? AND s.sclass IS NOT NULL
		) ris LEFT JOIN legend_sclass ss ON ss.seedclass_category = ris.sclass AND ss.season = ? GROUP BY ris.sclass
		");
		$irri_stmt = $conn->prepare("
		SELECT ss.seedclass, AVG(ris.srate), STDDEV_SAMP(ris.srate) FROM
		(
			SELECT ri.region, ri.province, ri.municipality, ri.barangay, ri.hh_number, ri.season, s.sclass, ri.srate FROM 
			(
				SELECT r.region, r.province, r.municipality, r.barangay, r.hh_number, r.season, i.seedha AS srate FROM 
				matrix_rectype1 r LEFT JOIN
				matrix_ioutput i ON
				r.region = i.region AND 
				r.province = i.province AND 
				r.municipality = i.municipality AND 
				r.barangay = i.barangay AND 
				r.hh_number = i.hh_number AND 
				r.season = i.season WHERE
				r.season = ? AND i.seedha IS NOT NULL AND i.yield != 0
			) ri 
			LEFT JOIN
			matrix_rectype6 s ON 
			ri.region = s.region AND 
			ri.province = s.province AND 
			ri.municipality = s.municipality AND 
			ri.barangay = s.barangay AND 
			ri.hh_number = s.hh_number AND 
			ri.season = s.season WHERE
			ri.season = ? AND s.sclass IS NOT NULL
		) ris JOIN 
		matrix_irrigation ir,
		legend_sclass ss WHERE 
		ss.seedclass_category = ris.sclass AND 
		ss.season = ? AND 
		ris.region = ir.region AND 
		ris.province = ir.province AND 
		ris.municipality = ir.municipality AND 
		ris.barangay = ir.barangay AND 
		ris.hh_number = ir.hh_number AND 
		ris.season = ir.season AND 
		(ir.irrigation_source = 1 OR ir.irrigation_source = 2 OR ir.irrigation_source = 3 OR ir.irrigation_source = 4 OR ir.irrigation_source = 5) 
		GROUP BY ris.sclass
		");
		$rain_stmt = $conn->prepare("
		SELECT ss.seedclass, AVG(ris.srate), STDDEV_SAMP(ris.srate) FROM
		(
			SELECT ri.region, ri.province, ri.municipality, ri.barangay, ri.hh_number, ri.season, s.sclass, ri.srate FROM 
			(
				SELECT r.region, r.province, r.municipality, r.barangay, r.hh_number, r.season, i.seedha AS srate FROM 
				matrix_rectype1 r LEFT JOIN
				matrix_ioutput i ON
				r.region = i.region AND 
				r.province = i.province AND 
				r.municipality = i.municipality AND 
				r.barangay = i.barangay AND 
				r.hh_number = i.hh_number AND 
				r.season = i.season WHERE
				r.season = ? AND i.seedha IS NOT NULL AND i.yield != 0
			) ri 
			LEFT JOIN
			matrix_rectype6 s ON 
			ri.region = s.region AND 
			ri.province = s.province AND 
			ri.municipality = s.municipality AND 
			ri.barangay = s.barangay AND 
			ri.hh_number = s.hh_number AND 
			ri.season = s.season WHERE
			ri.season = ? AND s.sclass IS NOT NULL
		) ris JOIN 
		matrix_irrigation ir,
		legend_sclass ss WHERE 
		ss.seedclass_category = ris.sclass AND 
		ss.season = ? AND 
		ris.region = ir.region AND 
		ris.province = ir.province AND 
		ris.municipality = ir.municipality AND 
		ris.barangay = ir.barangay AND 
		ris.hh_number = ir.hh_number AND 
		ris.season = ir.season AND 
		ir.irrigation_source = 0 
		GROUP BY ris.sclass
		");
		$all_stmt->bind_param("sss", $season, $season, $varlegend);
		$irri_stmt->bind_param("sss", $season, $season, $varlegend);
		$rain_stmt->bind_param("sss", $season, $season, $varlegend);
	}
	else{
		$all_stmt = $conn->prepare("
		SELECT ss.seedclass, AVG(ris.srate), STDDEV_SAMP(ris.srate) FROM
		(
			SELECT ri.region, ri.province, ri.municipality, ri.barangay, ri.hh_number, ri.season, s.sclass, ri.srate FROM 
			(
				SELECT r.region, r.province, r.municipality, r.barangay, r.hh_number, r.season, i.seedha AS srate FROM 
				matrix_rectype1 r LEFT JOIN
				matrix_ioutput i ON
				r.region = i.region AND 
				r.province = i.province AND 
				r.municipality = i.municipality AND 
				r.barangay = i.barangay AND 
				r.hh_number = i.hh_number AND 
				r.season = i.season WHERE
				r.season = ? AND r.province = ? AND i.seedha IS NOT NULL AND i.yield != 0
			) ri 
			LEFT JOIN
			matrix_rectype6 s ON 
			ri.region = s.region AND 
			ri.province = s.province AND 
			ri.municipality = s.municipality AND 
			ri.barangay = s.barangay AND 
			ri.hh_number = s.hh_number AND 
			ri.season = s.season WHERE
			ri.season = ? AND ri.province = ? AND s.sclass IS NOT NULL
		) ris LEFT JOIN legend_sclass ss ON ss.seedclass_category = ris.sclass AND ss.season = ? GROUP BY ris.sclass
		");
		$irri_stmt = $conn->prepare("
		SELECT ss.seedclass, AVG(ris.srate), STDDEV_SAMP(ris.srate) FROM
		(
			SELECT ri.region, ri.province, ri.municipality, ri.barangay, ri.hh_number, ri.season, s.sclass, ri.srate FROM 
			(
				SELECT r.region, r.province, r.municipality, r.barangay, r.hh_number, r.season, i.seedha AS srate FROM 
				matrix_rectype1 r LEFT JOIN
				matrix_ioutput i ON
				r.region = i.region AND 
				r.province = i.province AND 
				r.municipality = i.municipality AND 
				r.barangay = i.barangay AND 
				r.hh_number = i.hh_number AND 
				r.season = i.season WHERE
				r.season = ? AND r.province = ? AND i.seedha IS NOT NULL AND i.yield != 0
			) ri 
			LEFT JOIN
			matrix_rectype6 s ON 
			ri.region = s.region AND 
			ri.province = s.province AND 
			ri.municipality = s.municipality AND 
			ri.barangay = s.barangay AND 
			ri.hh_number = s.hh_number AND 
			ri.season = s.season WHERE
			ri.season = ? AND ri.province = ? AND s.sclass IS NOT NULL
		) ris JOIN 
		matrix_irrigation ir,
		legend_sclass ss WHERE 
		ss.seedclass_category = ris.sclass AND 
		ss.season = ? AND 
		ris.region = ir.region AND 
		ris.province = ir.province AND 
		ris.municipality = ir.municipality AND 
		ris.barangay = ir.barangay AND 
		ris.hh_number = ir.hh_number AND 
		ris.season = ir.season AND 
		(ir.irrigation_source = 1 OR ir.irrigation_source = 2 OR ir.irrigation_source = 3 OR ir.irrigation_source = 4 OR ir.irrigation_source = 5) 
		GROUP BY ris.sclass
		");
		$rain_stmt = $conn->prepare("
		SELECT ss.seedclass, AVG(ris.srate), STDDEV_SAMP(ris.srate) FROM
		(
			SELECT ri.region, ri.province, ri.municipality, ri.barangay, ri.hh_number, ri.season, s.sclass, ri.srate FROM 
			(
				SELECT r.region, r.province, r.municipality, r.barangay, r.hh_number, r.season, i.seedha AS srate FROM 
				matrix_rectype1 r LEFT JOIN
				matrix_ioutput i ON
				r.region = i.region AND 
				r.province = i.province AND 
				r.municipality = i.municipality AND 
				r.barangay = i.barangay AND 
				r.hh_number = i.hh_number AND 
				r.season = i.season WHERE
				r.season = ? AND r.province = ? AND i.seedha IS NOT NULL AND i.yield != 0
			) ri 
			LEFT JOIN
			matrix_rectype6 s ON 
			ri.region = s.region AND 
			ri.province = s.province AND 
			ri.municipality = s.municipality AND 
			ri.barangay = s.barangay AND 
			ri.hh_number = s.hh_number AND 
			ri.season = s.season WHERE
			ri.season = ? AND ri.province = ? AND s.sclass IS NOT NULL
		) ris JOIN 
		matrix_irrigation ir,
		legend_sclass ss WHERE 
		ss.seedclass_category = ris.sclass AND 
		ss.season = ? AND 
		ris.region = ir.region AND 
		ris.province = ir.province AND 
		ris.municipality = ir.municipality AND 
		ris.barangay = ir.barangay AND 
		ris.hh_number = ir.hh_number AND 
		ris.season = ir.season AND 
		ir.irrigation_source = 0 
		GROUP BY ris.sclass
		");
		$all_stmt->bind_param("sssss", $season, $province, $season, $province, $varlegend);
		$irri_stmt->bind_param("sssss", $season, $province, $season, $province, $varlegend);
		$rain_stmt->bind_param("sssss", $season, $province, $season, $province, $varlegend);
	}
	
	//all
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($category, $avg, $stddev);

	// From Table 21
	//$all_stmt->bind_result($category, $category_name, $quantity);
	/*
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$counter] != 0){
				$temp =  ($quantity / $total_farmers[$counter]) * 100;
			}
            if(!isset($matrix_sclass[$category_name])){
				$matrix_sclass[$category_name] = array();
			}
            $matrix_sclass[$category_name][$counter] = $temp;
		}
	}
        
	$counter++;
        
	*/

	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			if(empty($stddev)){
				$stddev = 0;
			}
            if(!isset($seedclass[$category])){
				$seedclass[$category] = array();
			}
			$seedclass[$category][$counter] = $avg;
			$seedclass[$category][$counter+1] = $stddev;
		}
	}

	$counter+=2;

	/*	
	Old code
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){
			if(empty($stddev)){
				$stddev = 0;
			}
			if($season >= 5 && $category == 1){
				array_push($seedclass[5], $avg);
				array_push($seedclass[5], $stddev);
			}
			elseif($season < 5 && $category == 1){
				array_push($seedclass[0], $avg);
				array_push($seedclass[0], $stddev);
			}
			else{
				array_push($seedclass[$category-1], $avg);
				array_push($seedclass[$category-1], $stddev);
			}
		}
	}
	
	$counter+=2;

	for($i=0;$i<6;$i++){
		if(count($seedclass[$i]) < $counter){
			array_push($seedclass[$i], 0);
			array_push($seedclass[$i], 0);
		}
	}
	*/
	$all_stmt->close();
	//irri
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($category, $avg, $stddev);
	
	/*
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){
			if(empty($stddev)){
				$stddev = 0;
			}
			if($season >= 5 && $category == 1){
				array_push($seedclass[5], $avg);
				array_push($seedclass[5], $stddev);
			}
			elseif($season < 5 && $category == 1){
				array_push($seedclass[0], $avg);
				array_push($seedclass[0], $stddev);
			}
			else{
				array_push($seedclass[$category-1], $avg);
				array_push($seedclass[$category-1], $stddev);
			}		
		}
	}
	*/
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			if(empty($stddev)){
				$stddev = 0;
			}
            if(!isset($seedclass[$category])){
				$seedclass[$category] = array();
			}
			$seedclass[$category][$counter] = $avg;
			$seedclass[$category][$counter+1] = $stddev;
		}
	}
	
	$counter+=2;
	/*
	for($i=0;$i<6;$i++){
		if(count($seedclass[$i]) < $counter){
			array_push($seedclass[$i], 0);
			array_push($seedclass[$i], 0);
		}
	}
	*/
	$irri_stmt->close();
	
	//rain
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($category, $avg, $stddev);

	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			if(empty($stddev)){
				$stddev = 0;
			}
            if(!isset($seedclass[$category])){
				$seedclass[$category] = array();
			}
			$seedclass[$category][$counter] = $avg;
			$seedclass[$category][$counter+1] = $stddev;
		}
	}

	$counter+=2;

	/*
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){
			if(empty($stddev)){
				$stddev = 0;
			}
			if($season >= 5 && $category == 1){
				array_push($seedclass[5], $avg);
				array_push($seedclass[5], $stddev);
			}
			elseif($season < 5 && $category == 1){
				array_push($seedclass[0], $avg);
				array_push($seedclass[0], $stddev);
			}
			else{
				array_push($seedclass[$category-1], $avg);
				array_push($seedclass[$category-1], $stddev);
			}		
		}
	}
	
	for($i=0;$i<6;$i++){
		if(count($seedclass[$i]) < $counter){
			array_push($seedclass[$i], 0);
			array_push($seedclass[$i], 0);
		}
	}
	*/
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
	echo "<tbody>\n";
	create_special_rows("Seed classification", $headings, 2);
	create_special_rows("", array_formatting($n_stat,"(n = ",")"), 2);
	create_headings("", $headings2);
	echo "<tr>\n<td class='header4 left'></td>\n<td class='header4 center' colspan=".($count_columns-1).">Seeds (kg/ha)</td>\n</tr>\n";
	//echo "<pre>"; print_r($seedclass); echo "<pre>";
	/*
	if(min($seasons) < 5){
		create_row("Foundation", $seedclass[0], 2);	
	}
	if(max($seasons) >= 5){
		create_row("Hybrid", $seedclass[5], 2);
	}
	create_row("Registered", $seedclass[1], 2);
	create_row("Certified", $seedclass[2], 2);
	create_row("Good seeds", $seedclass[3], 2);
	create_row("Farmer's seed", $seedclass[4], 2);
	*/
	foreach ($seedclass as $key => $value){
        for($i = 0; $i < count($seasons) * 6 ; $i++){
            if(!isset($value[$i])){
                $value[$i] = "-";
            }
        }
        create_row($key, $value, 2);
	}
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
	echo "<div>Good seeds defined as seeds produced from the planting of registered and certified seeds.</div>\n";
    echo "<div>Note: Different source of categories were used during the 2016-2017 RBFHS survey round.";
	for($i=0;$i<count($seasons);$i++){
		if ($n_stat[$i*3] < $total_n[$i]){
			echo '<div>Excludes farmers who temporarily stopped farming during '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$n_stat[$i*3]) .')</div>';
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