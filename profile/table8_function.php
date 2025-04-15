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
	$season_names = $headings = $headings2 = $footnotes = array();
	$avg_all = $avg_largest = $avg_num = array(); 
	$n_stat = $total_n = array();
	$count = 0;
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';;
	
	$location = array(); 
	for($i=0; $i < 3; $i++){
		array_push($location, array());
	}
	
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
	
	for($i=0; $i < 3; $i++){
		array_push($headings2, "Mean");	
		array_push($headings2, "SD<sup>a</sup>");
	}
	
	if($province==999){
		$all_stmt= $conn->prepare("SELECT AVG(t.AREA), STDDEV_SAMP(t.AREA) FROM (SELECT SUM(matrix_rectype5.area) as AREA FROM matrix_rectype5 JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype5.season = ? AND matrix_rectype5.area > 0 GROUP BY matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season) t");
		$irri_stmt= $conn->prepare("SELECT AVG(t.AREA), STDDEV_SAMP(t.AREA) FROM (SELECT SUM(matrix_rectype5.area) as AREA FROM matrix_rectype5 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype5.season = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1 AND matrix_rectype5.area > 0 GROUP BY matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season) t");
		$rain_stmt= $conn->prepare("SELECT AVG(t.AREA), STDDEV_SAMP(t.AREA) FROM (SELECT SUM(matrix_rectype5.area) as AREA FROM matrix_rectype5 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype5.season = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0 AND matrix_rectype5.area > 0 GROUP BY matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season) t");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT AVG(t.AREA), STDDEV_SAMP(t.AREA) FROM (SELECT SUM(matrix_rectype5.area) as AREA FROM matrix_rectype5 JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype5.season = ? AND matrix_rectype5.province = ? AND matrix_rectype5.area > 0 GROUP BY matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season) t");
		$irri_stmt= $conn->prepare("SELECT AVG(t.AREA), STDDEV_SAMP(t.AREA) FROM (SELECT SUM(matrix_rectype5.area) as AREA FROM matrix_rectype5 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype5.season = ? AND matrix_rectype5.province = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1 AND matrix_rectype5.area > 0 GROUP BY matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season) t");
		$rain_stmt= $conn->prepare("SELECT AVG(t.AREA), STDDEV_SAMP(t.AREA) FROM (SELECT SUM(matrix_rectype5.area) as AREA FROM matrix_rectype5 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype5.season = ? AND matrix_rectype5.province = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0 AND matrix_rectype5.area > 0 GROUP BY matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season) t");
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($mean, $sd);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			if($mean == NULL){
				array_push($avg_all, 0);
			}
			else{
				array_push($avg_all, $mean);
			}
			if($sd == NULL){
				array_push($avg_all, 0);
			}
			else{
				array_push($avg_all, $sd);
			}
		}
	}
	else{
		for($i=0; $i<2; $i++){
			array_push($avg_all, 0);
		}
	}
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($mean, $sd);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			if($mean == NULL){
				array_push($avg_all, 0);
			}
			else{
				array_push($avg_all, $mean);
			}
			if($sd == NULL){
				array_push($avg_all, 0);
			}
			else{
				array_push($avg_all, $sd);
			}
		}
	}
	else{
		for($i=0; $i<2; $i++){
			array_push($avg_all, 0);
		}
	}
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($mean, $sd);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			if($mean == NULL){
				array_push($avg_all, 0);
			}
			else{
				array_push($avg_all, $mean);
			}
			if($sd == NULL){
				array_push($avg_all, 0);
			}
			else{
				array_push($avg_all, $sd);
			}
		}
	}
	else{
		for($i=0; $i<2; $i++){
			array_push($avg_all, 0);
		}
	}
	
	if($province==999){
		if($season == 4){
			$all_stmt = $conn->prepare("SELECT AVG(a), STDDEV_SAMP(a) FROM ((SELECT matrix_rectype5.area as a FROM matrix_rectype1 JOIN matrix_rectype5 USING (region, province, municipality, barangay, hh_number, season) WHERE matrix_rectype1.season = 3 AND matrix_rectype5.largest_parcel = 1) UNION (SELECT r1.area FROM matrix_rectype5 r1 JOIN matrix_rectype1 WHERE NOT EXISTS (select 1 from matrix_rectype5 r2 where r1.region = r2.region AND r1.province = r2.province AND r1.municipality = r2.municipality AND r1.barangay = r2.barangay AND r1.hh_number = r2.hh_number AND r1.season = r2.season and r2.area > r1.area) AND matrix_rectype1.region = r1.region AND matrix_rectype1.province = r1.province AND matrix_rectype1.municipality = r1.municipality AND matrix_rectype1.barangay = r1.barangay AND matrix_rectype1.hh_number = r1.hh_number AND matrix_rectype1.season = r1.season AND r1.largest_parcel IS NULL AND matrix_rectype1.season = 3)) a");
			//add irrigated and rainfed later
		}
		elseif($season < 5 && $season != 4){
			$all_stmt = $conn->prepare("SELECT AVG(a), STDDEV_SAMP(a) FROM ((SELECT matrix_rectype5.area as a FROM matrix_rectype1 JOIN matrix_rectype5 USING (region, province, municipality, barangay, hh_number, season) WHERE matrix_rectype1.season = ? AND matrix_rectype5.largest_parcel = 1) UNION (SELECT r1.area FROM matrix_rectype5 r1 JOIN matrix_rectype1 WHERE NOT EXISTS (select 1 from matrix_rectype5 r2 where r1.region = r2.region AND r1.province = r2.province AND r1.municipality = r2.municipality AND r1.barangay = r2.barangay AND r1.hh_number = r2.hh_number AND r1.season = r2.season and r2.area > r1.area) AND matrix_rectype1.region = r1.region AND matrix_rectype1.province = r1.province AND matrix_rectype1.municipality = r1.municipality AND matrix_rectype1.barangay = r1.barangay AND matrix_rectype1.hh_number = r1.hh_number AND matrix_rectype1.season = r1.season AND r1.largest_parcel IS NULL AND matrix_rectype1.season = ?)) a");
			$all_stmt->bind_param("ss", $season, $season);
			//add irrigated and rainfed later
		}
		else{
			$all_stmt = $conn->prepare("SELECT AVG(matrix_rectype5.area), STDDEV_SAMP(matrix_rectype5.area) FROM matrix_rectype1 JOIN matrix_rectype5 USING (region, province, municipality, barangay, hh_number, season) WHERE matrix_rectype1.season = ? AND matrix_rectype5.largest_parcel = 1 AND matrix_rectype5.area > 0");
			$irri_stmt = $conn->prepare("SELECT AVG(matrix_rectype5.area), STDDEV_SAMP(matrix_rectype5.area) FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype5.largest_parcel = 1 AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype5.area > 0");
			$rain_stmt = $conn->prepare("SELECT AVG(matrix_rectype5.area), STDDEV_SAMP(matrix_rectype5.area) FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype5.largest_parcel = 1 AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype5.area > 0");
			$all_stmt->bind_param("s", $season);
			$irri_stmt->bind_param("s", $season);
			$rain_stmt->bind_param("s", $season);
		}
	}
	else{
		if($season == 4){
			$all_stmt = $conn->prepare("SELECT AVG(a), STDDEV_SAMP(a) FROM ((SELECT matrix_rectype5.area as a FROM matrix_rectype1 JOIN matrix_rectype5 USING (region, province, municipality, barangay, hh_number, season) WHERE matrix_rectype1.season = 3 AND matrix_rectype1.province = ? AND matrix_rectype5.largest_parcel = 1) UNION (SELECT r1.area FROM matrix_rectype5 r1 JOIN matrix_rectype1 WHERE NOT EXISTS (select 1 from matrix_rectype5 r2 where r1.region = r2.region AND r1.province = r2.province AND r1.municipality = r2.municipality AND r1.barangay = r2.barangay AND r1.hh_number = r2.hh_number AND r1.season = r2.season and r2.area > r1.area) AND matrix_rectype1.region = r1.region AND matrix_rectype1.province = r1.province AND matrix_rectype1.municipality = r1.municipality AND matrix_rectype1.barangay = r1.barangay AND matrix_rectype1.hh_number = r1.hh_number AND matrix_rectype1.season = r1.season AND r1.largest_parcel IS NULL AND matrix_rectype1.season = 3 AND matrix_rectype1.province = ?)) a");
			$all_stmt->bind_param("ss", $province, $province);
		}
		elseif($season < 5 && $season != 4){
			$all_stmt = $conn->prepare("SELECT AVG(a), STDDEV_SAMP(a) FROM ((SELECT matrix_rectype5.area as a FROM matrix_rectype1 JOIN matrix_rectype5 USING (region, province, municipality, barangay, hh_number, season) WHERE matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype5.largest_parcel = 1) UNION (SELECT r1.area FROM matrix_rectype5 r1 JOIN matrix_rectype1 WHERE NOT EXISTS (select 1 from matrix_rectype5 r2 where r1.region = r2.region AND r1.province = r2.province AND r1.municipality = r2.municipality AND r1.barangay = r2.barangay AND r1.hh_number = r2.hh_number AND r1.season = r2.season and r2.area > r1.area) AND matrix_rectype1.region = r1.region AND matrix_rectype1.province = r1.province AND matrix_rectype1.municipality = r1.municipality AND matrix_rectype1.barangay = r1.barangay AND matrix_rectype1.hh_number = r1.hh_number AND matrix_rectype1.season = r1.season AND r1.largest_parcel IS NULL AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?)) a");
			$all_stmt->bind_param("ssss", $season, $province, $season, $province);
		}
		else{
			$all_stmt = $conn->prepare("SELECT AVG(matrix_rectype5.area), STDDEV_SAMP(matrix_rectype5.area) FROM matrix_rectype1 JOIN matrix_rectype5 USING (region, province, municipality, barangay, hh_number, season) WHERE matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype5.largest_parcel = 1 ");
			$irri_stmt = $conn->prepare("SELECT AVG(matrix_rectype5.area), STDDEV_SAMP(matrix_rectype5.area) FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype5.largest_parcel = 1 AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype5.area > 0");
			$rain_stmt = $conn->prepare("SELECT AVG(matrix_rectype5.area), STDDEV_SAMP(matrix_rectype5.area) FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype5.largest_parcel = 1 AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype5.area > 0");
			$all_stmt->bind_param("ss", $season, $province);
			$irri_stmt->bind_param("ss", $season, $province);
			$rain_stmt->bind_param("ss", $season, $province);
		}
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($mean, $sd);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			if($mean == NULL){
				array_push($avg_largest, 0);
			}
			else{
				array_push($avg_largest, $mean);
			}
			if($sd == NULL){
				array_push($avg_largest, 0);
			}
			else{
				array_push($avg_largest, $sd);
			}
		}
	}
	else{
		for($i=0; $i<2; $i++){
			array_push($avg_largest, 0);
		}
	}	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($mean, $sd);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			if($mean == NULL){
				array_push($avg_largest, 0);
			}
			else{
				array_push($avg_largest, $mean);
			}
			if($sd == NULL){
				array_push($avg_largest, 0);
			}
			else{
				array_push($avg_largest, $sd);
			}
		}
	}
	else{
		for($i=0; $i<2; $i++){
			array_push($avg_largest, 0);
		}
	}
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($mean, $sd);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			if($mean == NULL){
				array_push($avg_largest, 0);
			}
			else{
				array_push($avg_largest, $mean);
			}
			if($sd == NULL){
				array_push($avg_largest, 0);
			}
			else{
				array_push($avg_largest, $sd);
			}
		}
	}
	else{
		for($i=0; $i<2; $i++){
			array_push($avg_largest, 0);
		}
	}
	
	if($province==999){
		$all_stmt= $conn->prepare("SELECT AVG(n), STDDEV_SAMP(n) FROM (SELECT matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season, COUNT(matrix_rectype5.area) as n FROM matrix_rectype1 JOIN matrix_rectype5 USING (region, province, municipality, barangay, hh_number, season) WHERE matrix_rectype1.season = ? GROUP BY matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season) np");
		$irri_stmt= $conn->prepare("SELECT AVG(n), STDDEV_SAMP(n) FROM (SELECT matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season, COUNT(matrix_rectype5.area) as n FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 1 GROUP BY matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season) np");
		$rain_stmt= $conn->prepare("SELECT AVG(n), STDDEV_SAMP(n) FROM (SELECT matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season, COUNT(matrix_rectype5.area) as n FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 0 GROUP BY matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season) np");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT AVG(n), STDDEV_SAMP(n) FROM (SELECT matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season, COUNT(matrix_rectype5.area) as n FROM matrix_rectype1 JOIN matrix_rectype5 USING (region, province, municipality, barangay, hh_number, season) WHERE matrix_rectype1.season = ? AND matrix_rectype1.province = ? GROUP BY matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season) np");
		$irri_stmt= $conn->prepare("SELECT AVG(n), STDDEV_SAMP(n) FROM (SELECT matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season, COUNT(matrix_rectype5.area) as n FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime = 1 GROUP BY matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season) np");
		$rain_stmt= $conn->prepare("SELECT AVG(n), STDDEV_SAMP(n) FROM (SELECT matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season, COUNT(matrix_rectype5.area) as n FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime = 0 GROUP BY matrix_rectype5.region, matrix_rectype5.province, matrix_rectype5.municipality, matrix_rectype5.barangay, matrix_rectype5.hh_number, matrix_rectype5.season) np");
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($mean, $sd);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			if($mean == NULL){
				array_push($avg_num, 0);
			}
			else{
				array_push($avg_num, $mean);
			}
			if($sd == NULL){
				array_push($avg_num, 0);
			}
			else{
				array_push($avg_num, $sd);
			}
		}
	}
	else{
		for($i=0; $i<2; $i++){
			array_push($avg_num, 0);
		}
	}
	//irri
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($mean, $sd);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			if($mean == NULL){
				array_push($avg_num, 0);
			}
			else{
				array_push($avg_num, $mean);
			}
			if($sd == NULL){
				array_push($avg_num, 0);
			}
			else{
				array_push($avg_num, $sd);
			}
		}
	}
	else{
		for($i=0; $i<2; $i++){
			array_push($avg_num, 0);
		}
	}
	//rain
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($mean, $sd);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			if($mean == NULL){
				array_push($avg_num, 0);
			}
			else{
				array_push($avg_num, $mean);
			}
			if($sd == NULL){
				array_push($avg_num, 0);
			}
			else{
				array_push($avg_num, $sd);
			}
		}
	}
	else{
		for($i=0; $i<2; $i++){
			array_push($avg_num, 0);
		}
	}
	
	if($province==999){
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5 WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.season = ? AND matrix_rectype5.location IS NOT NULL) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.season = ? AND matrix_rectype5.location IS NOT NULL AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype5.location IS NOT NULL) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.season = ? AND matrix_rectype5.location IS NOT NULL AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype5.location IS NOT NULL) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5 WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype5.location IS NOT NULL) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype5.location IS NOT NULL AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype5.location IS NOT NULL) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype5.location IS NOT NULL AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype5.location IS NOT NULL) a");
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
		$all_stmt= $conn->prepare("SELECT l.location, count(r.curloc) AS frequency FROM (SELECT 1 location UNION ALL SELECT 2 location UNION ALL SELECT 3 category) AS l LEFT JOIN (SELECT matrix_rectype5.region, matrix_rectype5.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype5.location AS curloc FROM matrix_rectype5 JOIN matrix_rectype1 WHERE  matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.season = ?) AS r ON l.location  = r.curloc GROUP BY l.location");
		$irri_stmt= $conn->prepare("SELECT l.location, count(r.curloc) AS frequency FROM (SELECT 1 location UNION ALL SELECT 2 location UNION ALL SELECT 3 category) AS l LEFT JOIN (SELECT matrix_rectype5.region, matrix_rectype5.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype5.location AS curloc FROM matrix_rectype5 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.season = ?) AS r ON l.location  = r.curloc GROUP BY l.location");
		$rain_stmt= $conn->prepare("SELECT l.location, count(r.curloc) AS frequency FROM (SELECT 1 location UNION ALL SELECT 2 location UNION ALL SELECT 3 category) AS l LEFT JOIN (SELECT matrix_rectype5.region, matrix_rectype5.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype5.location AS curloc FROM matrix_rectype5 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.season = ?) AS r ON l.location  = r.curloc GROUP BY l.location");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT l.location, count(r.curloc) AS frequency FROM (SELECT 1 location UNION ALL SELECT 2 location UNION ALL SELECT 3 category) AS l LEFT JOIN (SELECT matrix_rectype5.region, matrix_rectype5.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype5.location AS curloc FROM matrix_rectype5 JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype5.season = ? AND matrix_rectype1.province = ?) AS r ON l.location  = r.curloc GROUP BY l.location");
		$irri_stmt= $conn->prepare("SELECT l.location, count(r.curloc) AS frequency FROM (SELECT 1 location UNION ALL SELECT 2 location UNION ALL SELECT 3 category) AS l LEFT JOIN (SELECT matrix_rectype5.region, matrix_rectype5.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype5.location AS curloc FROM matrix_rectype5 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype5.season = ? AND matrix_rectype1.province = ?) AS r ON l.location  = r.curloc GROUP BY l.location");
		$rain_stmt= $conn->prepare("SELECT l.location, count(r.curloc) AS frequency FROM (SELECT 1 location UNION ALL SELECT 2 location UNION ALL SELECT 3 category) AS l LEFT JOIN (SELECT matrix_rectype5.region, matrix_rectype5.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype5.location AS curloc FROM matrix_rectype5 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype5.season = ? AND matrix_rectype1.province = ?) AS r ON l.location  = r.curloc GROUP BY l.location");
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
			if($n_stat[$count] != 0){
				$temp =  ($quantity / $n_stat[$count]) * 100;
			}
			array_push($location[$category-1], $temp);
		}
	}
	$all_stmt->close();
	$count++;
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($category, $quantity);		
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			$temp = 0;
			if($n_stat[$count] != 0){
				$temp =  ($quantity / $n_stat[$count]) * 100;
			}
			array_push($location[$category-1], $temp);
		}
	}
	$irri_stmt->close();
	$count++;
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($category, $quantity);		
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			$temp = 0;
			if($n_stat[$count] != 0){
				$temp =  ($quantity / $n_stat[$count]) * 100;
			}
			array_push($location[$category-1], $temp);
		}
	}
	$rain_stmt->close();
	$count++;
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
echo "<tr>\n<td class='header4'></td><td class='header4 center' colspan=".($count_columns-1).">(averages)</td>\n</tr>\n";
create_special_numbers("Total farm size (ha)", $avg_all, 1, 2);
create_special_numbers("Size of largest parcel (ha)", $avg_largest, 1, 2);
create_special_numbers("Number of rice-based farm parcels", $avg_num, 1, 0);
echo "<tr>\n<td class='header4 left' colspan=".$count_columns.">Location of the largest parcel (%)</td>\n</tr>\n";
create_special_numbers("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Within the barangay", $location[0], 2, 0);
create_special_numbers("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Outside the barangay, within the municipality", $location[1], 2, 0);
create_special_numbers("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Outside the municipality", $location[2], 2, 0);
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
echo '<div>Excludes missing response during '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$n_stat[$i*3]) .').</div>';
}
}
echo "<br/>";
}
echo "<div>Note: All rice-based parcels, regardless of tenurial status, were considered in generating average farm size.</div>\n<br/>\n";
// if (min($seasons) < 5){
// echo "<div><strong>Notice</strong>: 1996, 1997, 2001, 2002 harvest data are <strong>under construction</strong>.</div><br/>\n";
// }	
for($i=0;$i<count($seasons);$i++){
$stmt= $conn->prepare("SELECT count(region) from matrix_rectype1 where season = ?");
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
echo displayNoteRounding();
echo "<br/>";
echo "<div>Data accessed at ". date('l jS \of F Y h:i:s A') . "</div>";
echo displayNoteContact();
echo "</div>";
require_once("../includes/export.php");
?>