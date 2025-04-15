<?php
	require_once("../includes/headeralt.php");
?>	
<div id="tableData">
<?php
	$provinces = disinfect_var($_POST['provinces']);
	$seasons = disinfect_var($_POST['seasons']);
	$table_no = 10;
	$content=count($provinces);
	
	$stmt= $total_stmt = "";
	$total = $codename = $percent = 0;
	
	$lop = implode(',', $provinces);
	$los = implode(',', $seasons);
	$region = 0;	
	
	foreach($provinces as $province){
	$season_names = $headings = $headings2 = $footnotes = array();
	$avg_distance = $avg_time = $avg_fare =  array();
	$total_n = $n_stat = array();
	$counter = -1;
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	
	$transpo_array = $below_significance = array();
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
	
	$total_farmers = array();
	
	if($province==999){
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5 WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5 WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
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
		if($season == 4){
			$all_stmt = $conn->prepare("SELECT AVG(d) as distance, AVG(t) as time, AVG(f) as fare FROM ((SELECT matrix_rectype5.distance_market as d, matrix_rectype5.travel_time as t, matrix_rectype5.fare as f FROM matrix_rectype1 JOIN matrix_rectype5 USING (region, province, municipality, barangay, hh_number, season) WHERE matrix_rectype1.season = 3 AND matrix_rectype5.largest_parcel = 1) UNION (SELECT r1.distance_market, r1.travel_time,  r1.fare FROM matrix_rectype5 r1 JOIN matrix_rectype1 WHERE NOT EXISTS (select 1 from matrix_rectype5 r2 where r1.region = r2.region AND r1.province = r2.province AND r1.municipality = r2.municipality AND r1.barangay = r2.barangay AND r1.hh_number = r2.hh_number AND r1.season = r2.season and r2.area > r1.area) AND matrix_rectype1.region = r1.region AND matrix_rectype1.province = r1.province AND matrix_rectype1.municipality = r1.municipality AND matrix_rectype1.barangay = r1.barangay AND matrix_rectype1.hh_number = r1.hh_number AND matrix_rectype1.season = r1.season AND r1.largest_parcel IS NULL AND matrix_rectype1.season = 3)) a");
			// add irri, rainfed clause and standard deviation
		}
		elseif($season < 5 && $season != 4){
			$all_stmt = $conn->prepare("SELECT AVG(d) as distance, AVG(t) as time, AVG(f) as fare FROM ((SELECT matrix_rectype5.distance_market as d, matrix_rectype5.travel_time as t, matrix_rectype5.fare as f FROM matrix_rectype1 JOIN matrix_rectype5 USING (region, province, municipality, barangay, hh_number, season) WHERE matrix_rectype1.season = ? AND matrix_rectype5.largest_parcel = 1) UNION (SELECT r1.distance_market, r1.travel_time,  r1.fare FROM matrix_rectype5 r1 JOIN matrix_rectype1 WHERE NOT EXISTS (select 1 from matrix_rectype5 r2 where r1.region = r2.region AND r1.province = r2.province AND r1.municipality = r2.municipality AND r1.barangay = r2.barangay AND r1.hh_number = r2.hh_number AND r1.season = r2.season and r2.area > r1.area) AND matrix_rectype1.region = r1.region AND matrix_rectype1.province = r1.province AND matrix_rectype1.municipality = r1.municipality AND matrix_rectype1.barangay = r1.barangay AND matrix_rectype1.hh_number = r1.hh_number AND matrix_rectype1.season = r1.season AND r1.largest_parcel IS NULL AND matrix_rectype1.season = ?)) a");
			$all_stmt->bind_param("ss", $season, $season);
			// add irri, rainfed clause and standard deviation
		}
		else{
			$all_stmt = $conn->prepare("SELECT AVG(matrix_rectype5.distance_market), STDDEV_SAMP(matrix_rectype5.distance_market), AVG(matrix_rectype5.travel_time),  STDDEV_SAMP(matrix_rectype5.travel_time), AVG(matrix_rectype5.fare), STDDEV_SAMP(matrix_rectype5.fare) FROM matrix_rectype1 JOIN matrix_rectype5 USING (region, province, municipality, barangay, hh_number, season) WHERE matrix_rectype1.season = ? AND matrix_rectype5.largest_parcel = 1");
			$irri_stmt = $conn->prepare("SELECT AVG(matrix_rectype5.distance_market), STDDEV_SAMP(matrix_rectype5.distance_market), AVG(matrix_rectype5.travel_time),  STDDEV_SAMP(matrix_rectype5.travel_time), AVG(matrix_rectype5.fare), STDDEV_SAMP(matrix_rectype5.fare) FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype5.largest_parcel = 1 AND matrix_irrigation.irrigation_prime = 1");
			$rain_stmt = $conn->prepare("SELECT AVG(matrix_rectype5.distance_market), STDDEV_SAMP(matrix_rectype5.distance_market), AVG(matrix_rectype5.travel_time),  STDDEV_SAMP(matrix_rectype5.travel_time), AVG(matrix_rectype5.fare), STDDEV_SAMP(matrix_rectype5.fare) FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype5.largest_parcel = 1 AND matrix_irrigation.irrigation_prime = 0");
			$all_stmt->bind_param("s", $season);
			$irri_stmt->bind_param("s", $season);
			$rain_stmt->bind_param("s", $season);
		}
	}
	else{
		if($season == 4){
			$all_stmt = $conn->prepare("SELECT AVG(d) as distance, AVG(t) as time, AVG(f) as fare FROM ((SELECT matrix_rectype5.distance_market as d, matrix_rectype5.travel_time as t, matrix_rectype5.fare as f FROM matrix_rectype1 JOIN matrix_rectype5 USING (region, province, municipality, barangay, hh_number, season) WHERE matrix_rectype1.season = 3 AND matrix_rectype1.province = ? AND matrix_rectype5.largest_parcel = 1) UNION (SELECT r1.distance_market, r1.travel_time,  r1.fare FROM matrix_rectype5 r1 JOIN matrix_rectype1 WHERE NOT EXISTS (select 1 from matrix_rectype5 r2 where r1.region = r2.region AND r1.province = r2.province AND r1.municipality = r2.municipality AND r1.barangay = r2.barangay AND r1.hh_number = r2.hh_number AND r1.season = r2.season and r2.area > r1.area) AND matrix_rectype1.region = r1.region AND matrix_rectype1.province = r1.province AND matrix_rectype1.municipality = r1.municipality AND matrix_rectype1.barangay = r1.barangay AND matrix_rectype1.hh_number = r1.hh_number AND matrix_rectype1.season = r1.season AND r1.largest_parcel IS NULL AND matrix_rectype1.season = 3 AND matrix_rectype1.province = ?)) a");
			$all_stmt->bind_param("ss", $province, $province);
		}
		elseif($season < 5 && $season != 4){
			$all_stmt = $conn->prepare("SELECT AVG(d) as distance, AVG(t) as time, AVG(f) as fare FROM ((SELECT matrix_rectype5.distance_market as d, matrix_rectype5.travel_time as t, matrix_rectype5.fare as f FROM matrix_rectype1 JOIN matrix_rectype5 USING (region, province, municipality, barangay, hh_number, season) WHERE matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype5.largest_parcel = 1) UNION (SELECT r1.distance_market, r1.travel_time,  r1.fare FROM matrix_rectype5 r1 JOIN matrix_rectype1 WHERE NOT EXISTS (select 1 from matrix_rectype5 r2 where r1.region = r2.region AND r1.province = r2.province AND r1.municipality = r2.municipality AND r1.barangay = r2.barangay AND r1.hh_number = r2.hh_number AND r1.season = r2.season and r2.area > r1.area) AND matrix_rectype1.region = r1.region AND matrix_rectype1.province = r1.province AND matrix_rectype1.municipality = r1.municipality AND matrix_rectype1.barangay = r1.barangay AND matrix_rectype1.hh_number = r1.hh_number AND matrix_rectype1.season = r1.season AND r1.largest_parcel IS NULL AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?)) a");
			$all_stmt->bind_param("ssss", $season, $province, $season, $province);
		}
		else{
			$all_stmt = $conn->prepare("SELECT AVG(matrix_rectype5.distance_market), STDDEV_SAMP(matrix_rectype5.distance_market), AVG(matrix_rectype5.travel_time),  STDDEV_SAMP(matrix_rectype5.travel_time), AVG(matrix_rectype5.fare), STDDEV_SAMP(matrix_rectype5.fare) FROM matrix_rectype1 JOIN matrix_rectype5 USING (region, province, municipality, barangay, hh_number, season) WHERE matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype5.largest_parcel = 1");
			$irri_stmt = $conn->prepare("SELECT AVG(matrix_rectype5.distance_market), STDDEV_SAMP(matrix_rectype5.distance_market), AVG(matrix_rectype5.travel_time),  STDDEV_SAMP(matrix_rectype5.travel_time), AVG(matrix_rectype5.fare), STDDEV_SAMP(matrix_rectype5.fare) FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype5.largest_parcel = 1 AND matrix_irrigation.irrigation_prime = 1");
			$rain_stmt = $conn->prepare("SELECT AVG(matrix_rectype5.distance_market), STDDEV_SAMP(matrix_rectype5.distance_market), AVG(matrix_rectype5.travel_time),  STDDEV_SAMP(matrix_rectype5.travel_time), AVG(matrix_rectype5.fare), STDDEV_SAMP(matrix_rectype5.fare) FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype5.largest_parcel = 1 AND matrix_irrigation.irrigation_prime = 0");
			$all_stmt->bind_param("ss", $season, $province);
			$irri_stmt->bind_param("ss", $season, $province);
			$rain_stmt->bind_param("ss", $season, $province);
		}
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($distance, $sd_distance, $time, $sd_time, $fare, $sd_fare);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			if($distance == NULL){
				array_push($avg_distance, 0);
			}
			else{
				array_push($avg_distance, $distance);
			}
			if($sd_distance == NULL){
				array_push($avg_distance, 0);
			}
			else{
				array_push($avg_distance, $sd_distance);
			}
			if($time == NULL){
				array_push($avg_time, 0);
			}
			else{
				array_push($avg_time, $time);
			}
			if($sd_time == NULL){
				array_push($avg_time, 0);
			}
			else{
				array_push($avg_time, $sd_time);
			}
			if($fare == NULL){
				array_push($avg_fare, 0);
			}
			else{
				array_push($avg_fare, $fare);
			}
			if($sd_fare == NULL){
				array_push($avg_fare, 0);
			}
			else{
				array_push($avg_fare, $sd_fare);
			}
		}
	}
	else{
		array_push($avg_distance, 0);
		array_push($avg_distance, 0);
		array_push($avg_time, 0);
		array_push($avg_time, 0);
		array_push($avg_fare, 0);
		array_push($avg_fare, 0);
	}
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($distance, $sd_distance, $time, $sd_time, $fare, $sd_fare);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			if($distance == NULL){
				array_push($avg_distance, 0);
			}
			else{
				array_push($avg_distance, $distance);
			}
			if($sd_distance == NULL){
				array_push($avg_distance, 0);
			}
			else{
				array_push($avg_distance, $sd_distance);
			}
			if($time == NULL){
				array_push($avg_time, 0);
			}
			else{
				array_push($avg_time, $time);
			}
			if($sd_time == NULL){
				array_push($avg_time, 0);
			}
			else{
				array_push($avg_time, $sd_time);
			}
			if($fare == NULL){
				array_push($avg_fare, 0);
			}
			else{
				array_push($avg_fare, $fare);
			}
			if($sd_fare == NULL){
				array_push($avg_fare, 0);
			}
			else{
				array_push($avg_fare, $sd_fare);
			}
		}
	}
	else{
		array_push($avg_distance, 0);
		array_push($avg_distance, 0);
		array_push($avg_time, 0);
		array_push($avg_time, 0);
		array_push($avg_fare, 0);
		array_push($avg_fare, 0);
	}
		
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($distance, $sd_distance, $time, $sd_time, $fare, $sd_fare);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			if($distance == NULL){
				array_push($avg_distance, 0);
			}
			else{
				array_push($avg_distance, $distance);
			}
			if($sd_distance == NULL){
				array_push($avg_distance, 0);
			}
			else{
				array_push($avg_distance, $sd_distance);
			}
			if($time == NULL){
				array_push($avg_time, 0);
			}
			else{
				array_push($avg_time, $time);
			}
			if($sd_time == NULL){
				array_push($avg_time, 0);
			}
			else{
				array_push($avg_time, $sd_time);
			}
			if($fare == NULL){
				array_push($avg_fare, 0);
			}
			else{
				array_push($avg_fare, $fare);
			}
			if($sd_fare == NULL){
				array_push($avg_fare, 0);
			}
			else{
				array_push($avg_fare, $sd_fare);
			}
		}
	}
	else{
		array_push($avg_distance, 0);
		array_push($avg_distance, 0);
		array_push($avg_time, 0);
		array_push($avg_time, 0);
		array_push($avg_fare, 0);
		array_push($avg_fare, 0);
	}
	
	$findlegend = $conn->prepare("SELECT DISTINCT season FROM legend_transpo ORDER BY season DESC");
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
	
	for($i=0; $i < count($total_farmers); $i++){
		array_push($below_significance, $total_farmers[$i] * 0.01);
	}
	
	if($province==999){
		$all_stmt= $conn->prepare("
		SELECT 
		t.transpo, 
		IFNULL(l.transpo_type, t.transpo) as transportation, 
		COUNT(t.transpo) 
		FROM
			(SELECT
			matrix_rectype1.region, 
			matrix_rectype1.province, 
			matrix_rectype1.municipality, 
			matrix_rectype1.barangay, 
			matrix_rectype1.hh_number, 
			matrix_rectype1.season, 
			matrix_transpo.transpo 
			FROM matrix_rectype1 
			JOIN matrix_transpo, matrix_irrigation 
			WHERE matrix_rectype1.region = matrix_transpo.region AND 
			matrix_rectype1.province = matrix_transpo.province AND 
			matrix_rectype1.municipality = matrix_transpo.municipality AND 
			matrix_rectype1.barangay = matrix_transpo.barangay AND 
			matrix_rectype1.hh_number = matrix_transpo.hh_number AND 
			matrix_rectype1.season = matrix_transpo.season AND 
			matrix_rectype1.region = matrix_irrigation.region AND 
			matrix_rectype1.province = matrix_irrigation.province AND 
			matrix_rectype1.municipality = matrix_irrigation.municipality AND 
			matrix_rectype1.barangay = matrix_irrigation.barangay AND 
			matrix_rectype1.hh_number = matrix_irrigation.hh_number AND 
			matrix_rectype1.season = matrix_irrigation.season AND 
			matrix_irrigation.irrigation_source IS NOT NULL AND 
			matrix_rectype1.season = ? 
			) t 
		LEFT JOIN 
			(SELECT 
			transpo_id, 
			transpo_type 
			FROM legend_transpo 
			WHERE season = ?) l 
		ON t.transpo = l.transpo_id GROUP BY transportation ORDER BY COUNT(t.transpo) DESC
		");
		$irri_stmt= $conn->prepare("
		SELECT 
		t.transpo, 
		IFNULL(l.transpo_type, t.transpo) as transportation, 
		COUNT(t.transpo) 
		FROM
			(SELECT
			matrix_rectype1.region, 
			matrix_rectype1.province, 
			matrix_rectype1.municipality, 
			matrix_rectype1.barangay, 
			matrix_rectype1.hh_number, 
			matrix_rectype1.season, 
			matrix_transpo.transpo 
			FROM matrix_rectype1 
			JOIN matrix_transpo, matrix_irrigation 
			WHERE matrix_rectype1.region = matrix_transpo.region AND 
			matrix_rectype1.province = matrix_transpo.province AND 
			matrix_rectype1.municipality = matrix_transpo.municipality AND 
			matrix_rectype1.barangay = matrix_transpo.barangay AND 
			matrix_rectype1.hh_number = matrix_transpo.hh_number AND 
			matrix_rectype1.season = matrix_transpo.season AND 
			matrix_rectype1.region = matrix_irrigation.region AND 
			matrix_rectype1.province = matrix_irrigation.province AND 
			matrix_rectype1.municipality = matrix_irrigation.municipality AND 
			matrix_rectype1.barangay = matrix_irrigation.barangay AND 
			matrix_rectype1.hh_number = matrix_irrigation.hh_number AND 
			matrix_rectype1.season = matrix_irrigation.season AND  
			(matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND
			matrix_rectype1.season = ?
			) t 
		LEFT JOIN 
			(SELECT 
			transpo_id, 
			transpo_type 
			FROM legend_transpo 
			WHERE season = ?) l 
		ON t.transpo = l.transpo_id GROUP BY transportation ORDER BY COUNT(t.transpo) DESC
		");
		$rain_stmt= $conn->prepare("
		SELECT 
		t.transpo, 
		IFNULL(l.transpo_type, t.transpo) as transportation, 
		COUNT(t.transpo) 
		FROM
			(SELECT
			matrix_rectype1.region, 
			matrix_rectype1.province, 
			matrix_rectype1.municipality, 
			matrix_rectype1.barangay, 
			matrix_rectype1.hh_number, 
			matrix_rectype1.season, 
			matrix_transpo.transpo 
			FROM matrix_rectype1 
			JOIN matrix_transpo, matrix_irrigation 
			WHERE matrix_rectype1.region = matrix_transpo.region AND 
			matrix_rectype1.province = matrix_transpo.province AND 
			matrix_rectype1.municipality = matrix_transpo.municipality AND 
			matrix_rectype1.barangay = matrix_transpo.barangay AND 
			matrix_rectype1.hh_number = matrix_transpo.hh_number AND 
			matrix_rectype1.season = matrix_transpo.season AND 
			matrix_rectype1.region = matrix_irrigation.region AND 
			matrix_rectype1.province = matrix_irrigation.province AND 
			matrix_rectype1.municipality = matrix_irrigation.municipality AND 
			matrix_rectype1.barangay = matrix_irrigation.barangay AND 
			matrix_rectype1.hh_number = matrix_irrigation.hh_number AND 
			matrix_rectype1.season = matrix_irrigation.season AND 
			matrix_irrigation.irrigation_source = 0 AND
			matrix_rectype1.season = ?
			) t 
		LEFT JOIN 
			(SELECT 
			transpo_id, 
			transpo_type 
			FROM legend_transpo 
			WHERE season = ?) l 
		ON t.transpo = l.transpo_id GROUP BY transportation ORDER BY COUNT(t.transpo) DESC
		");
		$all_stmt->bind_param("ss", $season, $varlegend);
		$irri_stmt->bind_param("ss", $season, $varlegend);
		$rain_stmt->bind_param("ss", $season, $varlegend);
	}
	else{
		$all_stmt= $conn->prepare("
		SELECT 
		t.transpo, 
		IFNULL(l.transpo_type, t.transpo) as transportation, 
		COUNT(t.transpo) 
		FROM
			(SELECT
			matrix_rectype1.region, 
			matrix_rectype1.province, 
			matrix_rectype1.municipality, 
			matrix_rectype1.barangay, 
			matrix_rectype1.hh_number, 
			matrix_rectype1.season, 
			matrix_transpo.transpo 
			FROM matrix_rectype1 
			JOIN matrix_transpo, matrix_irrigation 
			WHERE matrix_rectype1.region = matrix_transpo.region AND 
			matrix_rectype1.province = matrix_transpo.province AND 
			matrix_rectype1.municipality = matrix_transpo.municipality AND 
			matrix_rectype1.barangay = matrix_transpo.barangay AND 
			matrix_rectype1.hh_number = matrix_transpo.hh_number AND 
			matrix_rectype1.season = matrix_transpo.season AND 
			matrix_rectype1.region = matrix_irrigation.region AND 
			matrix_rectype1.province = matrix_irrigation.province AND 
			matrix_rectype1.municipality = matrix_irrigation.municipality AND 
			matrix_rectype1.barangay = matrix_irrigation.barangay AND 
			matrix_rectype1.hh_number = matrix_irrigation.hh_number AND 
			matrix_rectype1.season = matrix_irrigation.season AND 
			matrix_irrigation.irrigation_source IS NOT NULL AND 
			matrix_rectype1.season = ? AND matrix_rectype1.province = ? 
			) t 
		LEFT JOIN 
			(SELECT 
			transpo_id, 
			transpo_type 
			FROM legend_transpo 
			WHERE season = ?) l 
		ON t.transpo = l.transpo_id GROUP BY transportation ORDER BY COUNT(t.transpo) DESC
		");
		$irri_stmt= $conn->prepare("
		SELECT 
		t.transpo, 
		IFNULL(l.transpo_type, t.transpo) as transportation, 
		COUNT(t.transpo) 
		FROM
			(SELECT
			matrix_rectype1.region, 
			matrix_rectype1.province, 
			matrix_rectype1.municipality, 
			matrix_rectype1.barangay, 
			matrix_rectype1.hh_number, 
			matrix_rectype1.season, 
			matrix_transpo.transpo 
			FROM matrix_rectype1 
			JOIN matrix_transpo, matrix_irrigation 
			WHERE matrix_rectype1.region = matrix_transpo.region AND 
			matrix_rectype1.province = matrix_transpo.province AND 
			matrix_rectype1.municipality = matrix_transpo.municipality AND 
			matrix_rectype1.barangay = matrix_transpo.barangay AND 
			matrix_rectype1.hh_number = matrix_transpo.hh_number AND 
			matrix_rectype1.season = matrix_transpo.season AND 
			matrix_rectype1.region = matrix_irrigation.region AND 
			matrix_rectype1.province = matrix_irrigation.province AND 
			matrix_rectype1.municipality = matrix_irrigation.municipality AND 
			matrix_rectype1.barangay = matrix_irrigation.barangay AND 
			matrix_rectype1.hh_number = matrix_irrigation.hh_number AND 
			matrix_rectype1.season = matrix_irrigation.season AND 
			(matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND
			matrix_rectype1.season = ? AND matrix_rectype1.province = ? 
			) t 
		LEFT JOIN 
			(SELECT 
			transpo_id, 
			transpo_type 
			FROM legend_transpo 
			WHERE season = ?) l 
		ON t.transpo = l.transpo_id GROUP BY transportation ORDER BY COUNT(t.transpo) DESC
		");
		$rain_stmt= $conn->prepare("
		SELECT 
		t.transpo, 
		IFNULL(l.transpo_type, t.transpo) as transportation, 
		COUNT(t.transpo) 
		FROM
			(SELECT
			matrix_rectype1.region, 
			matrix_rectype1.province, 
			matrix_rectype1.municipality, 
			matrix_rectype1.barangay, 
			matrix_rectype1.hh_number, 
			matrix_rectype1.season, 
			matrix_transpo.transpo 
			FROM matrix_rectype1 
			JOIN matrix_transpo, matrix_irrigation 
			WHERE matrix_rectype1.region = matrix_transpo.region AND 
			matrix_rectype1.province = matrix_transpo.province AND 
			matrix_rectype1.municipality = matrix_transpo.municipality AND 
			matrix_rectype1.barangay = matrix_transpo.barangay AND 
			matrix_rectype1.hh_number = matrix_transpo.hh_number AND 
			matrix_rectype1.season = matrix_transpo.season AND 
			matrix_rectype1.region = matrix_irrigation.region AND 
			matrix_rectype1.province = matrix_irrigation.province AND 
			matrix_rectype1.municipality = matrix_irrigation.municipality AND 
			matrix_rectype1.barangay = matrix_irrigation.barangay AND 
			matrix_rectype1.hh_number = matrix_irrigation.hh_number AND 
			matrix_rectype1.season = matrix_irrigation.season AND 
			matrix_irrigation.irrigation_source = 0 AND
			matrix_rectype1.season = ? AND matrix_rectype1.province = ? 
			) t 
		LEFT JOIN 
			(SELECT 
			transpo_id, 
			transpo_type 
			FROM legend_transpo 
			WHERE season = ?) l 
		ON t.transpo = l.transpo_id GROUP BY transportation ORDER BY COUNT(t.transpo) DESC
		");
		$all_stmt->bind_param("sss", $season, $province, $varlegend);
		$irri_stmt->bind_param("sss", $season, $province, $varlegend);
		$rain_stmt->bind_param("sss", $season, $province, $varlegend);
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($id, $name, $quantity);
	
	$counter++;
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){
			if(!isset($transpo_array[$name])){
				$transpo_array[$name] = array();
			}
			$transpo_array[$name][$counter] = $quantity;
		}
	}
	
	$counter++;
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($id, $name, $quantity);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){
			if(!isset($transpo_array[$name])){
				$transpo_array[$name] = array();
			}
			$transpo_array[$name][$counter] = $quantity;
		}
	}
	
	$counter++;
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($id, $name, $quantity);
	
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){
			if(!isset($transpo_array[$name])){
				$transpo_array[$name] = array();
			}
			$transpo_array[$name][$counter] = $quantity;
		}
	}
	$all_stmt->close();
	$irri_stmt->close();
	$rain_stmt->close();
	
	}
	// echo "<pre>";
	// print_r($transpo_array);
	// echo "</pre><br/>";
	// echo "<pre>";
	// print_r($total_farmers);
	// echo "</pre>";
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
	create_special_rows("ITEM", $headings, 2);
	create_special_rows("", array_formatting($n_stat,"(n = ",")"), 2);
	echo "<tr>\n<td class='header4'></td><td class='header4 center' colspan=".($count_columns-1).">(average)</td>\n</tr>\n";
	create_headings("", $headings2);
	create_row("Average distance to the nearest wholesale market (km)", $avg_distance, 2);
	create_row("Average travel time (minutes)", $avg_time, 2);
	create_row("Average one-way fare (&#8369;)", $avg_fare, 2);
	echo "<tr>\n<td class='header4 left' colspan=".$count_columns.">Means of transportation (percent)<sup>b</sup></td>\n</tr>\n";
	foreach ($transpo_array as $key => &$sec_arr){
		$mark = FALSE;
		for($i = 0; $i < $counter+1; $i++){
			if(!isset($sec_arr[$i])){
				$sec_arr[$i] = 0;
			}
			if($sec_arr[$i] > $below_significance[$i] && $sec_arr[$i] > (count($seasons) * 1)){
				$mark = TRUE;
			}
		}
		$forprint[$key]=$mark;
		ksort($sec_arr);
	}
	uasort($transpo_array, "compareArray");
	foreach ($transpo_array as $key => $value){
		if($forprint[$key] == "TRUE"){
			create_average_span($transpo_array[$key], $n_stat, $key, 0, 2);
		}
		else{
			create_average_span($transpo_array[$key], $n_stat, $key, 1, 2);
		}
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
	for($i=0;$i<count($seasons);$i++){
		if ($n_stat[$i*3] < $total_n[$i]){
			echo '<div>Excludes missing response during '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$n_stat[$i*3]) .')</div>';
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
echo "\n<div><sup>b</sup>Respondents provided multiple answers so the percentage exceeded 100.</div>\n";
$getseason->close();
$getprovince->close();
$stmt->close();
echo "<br/>";
echo "<div>Data accessed at " . date('l jS \of F Y h:i:s A') . "</div>";
echo displayNoteIrrigate();
echo displayNoteContact();
require_once("../includes/export.php");
?>