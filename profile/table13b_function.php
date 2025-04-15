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
	$total_farmers = $n_stat = $total_n = $months = array();
	$psched = array();

	for($i=0;$i<12;$i++){
		$psched[$i] = array();
	}
	$i = 0;
	$ti = 0;
	$divisor = 0;
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
		
		for($j=0; $j<3; $j++){
			array_push($headings2, "First crop");	
			array_push($headings2, "Second crop");	
			array_push($headings2, "Third crop");
		}
				
			if($province==999){
				$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_pharvest, matrix_irrigation  WHERE matrix_rectype1.region = matrix_pharvest.region AND matrix_rectype1.province = matrix_pharvest.province AND matrix_rectype1.municipality = matrix_pharvest.municipality AND matrix_rectype1.barangay = matrix_pharvest.barangay AND matrix_rectype1.hh_number = matrix_pharvest.hh_number AND matrix_rectype1.season = matrix_pharvest.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime IS NOT NULL GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
				$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_pharvest, matrix_irrigation  WHERE matrix_rectype1.region = matrix_pharvest.region AND matrix_rectype1.province = matrix_pharvest.province AND matrix_rectype1.municipality = matrix_pharvest.municipality AND matrix_rectype1.barangay = matrix_pharvest.barangay AND matrix_rectype1.hh_number = matrix_pharvest.hh_number AND matrix_rectype1.season = matrix_pharvest.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 1 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
				$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_pharvest, matrix_irrigation  WHERE matrix_rectype1.region = matrix_pharvest.region AND matrix_rectype1.province = matrix_pharvest.province AND matrix_rectype1.municipality = matrix_pharvest.municipality AND matrix_rectype1.barangay = matrix_pharvest.barangay AND matrix_rectype1.hh_number = matrix_pharvest.hh_number AND matrix_rectype1.season = matrix_pharvest.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 0 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
				$all_stmt->bind_param("s", $season);
				$irri_stmt->bind_param("s", $season);
				$rain_stmt->bind_param("s", $season);
			}
			else{
				$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_pharvest, matrix_irrigation  WHERE matrix_rectype1.region = matrix_pharvest.region AND matrix_rectype1.province = matrix_pharvest.province AND matrix_rectype1.municipality = matrix_pharvest.municipality AND matrix_rectype1.barangay = matrix_pharvest.barangay AND matrix_rectype1.hh_number = matrix_pharvest.hh_number AND matrix_rectype1.season = matrix_pharvest.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime IS NOT NULL GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
				$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_pharvest, matrix_irrigation  WHERE matrix_rectype1.region = matrix_pharvest.region AND matrix_rectype1.province = matrix_pharvest.province AND matrix_rectype1.municipality = matrix_pharvest.municipality AND matrix_rectype1.barangay = matrix_pharvest.barangay AND matrix_rectype1.hh_number = matrix_pharvest.hh_number AND matrix_rectype1.season = matrix_pharvest.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime = 1 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
				$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_pharvest, matrix_irrigation  WHERE matrix_rectype1.region = matrix_pharvest.region AND matrix_rectype1.province = matrix_pharvest.province AND matrix_rectype1.municipality = matrix_pharvest.municipality AND matrix_rectype1.barangay = matrix_pharvest.barangay AND matrix_rectype1.hh_number = matrix_pharvest.hh_number AND matrix_rectype1.season = matrix_pharvest.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime = 0 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
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
		
		$n_stat = $total_farmers;
		
		if($province==999){
			$all_stmt=$conn->prepare("
				SELECT firstcrop.month, firstcrop.fcount, secondcrop.scount, thirdcrop.tcount
				FROM (

				SELECT m.month, c.fcount
				FROM (

				SELECT 1 AS 
				MONTH UNION SELECT 2 
				UNION SELECT 3 
				UNION SELECT 4 
				UNION SELECT 5 
				UNION SELECT 6 
				UNION SELECT 7 
				UNION SELECT 8 
				UNION SELECT 9 
				UNION SELECT 10 
				UNION SELECT 11 
				UNION SELECT 12

				)m
				LEFT JOIN (

				SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS fcount
				FROM matrix_rectype1
				JOIN matrix_pharvest
				WHERE matrix_rectype1.region = matrix_pharvest.region
				AND matrix_rectype1.province = matrix_pharvest.province
				AND matrix_rectype1.municipality = matrix_pharvest.municipality
				AND matrix_rectype1.barangay = matrix_pharvest.barangay
				AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
				AND matrix_rectype1.season = matrix_pharvest.season
				AND matrix_rectype1.season = ?
				AND matrix_pharvest.crop_indicator =1
				AND matrix_pharvest.cropharv IS NOT NULL
				GROUP BY (
				matrix_pharvest.cropharv
				)
				ORDER BY matrix_pharvest.cropharv ASC

				)c ON m.month = c.cm

				) firstcrop

				LEFT JOIN

				(

				SELECT m.month, c.scount
				FROM (

				SELECT 1 AS 
				MONTH UNION SELECT 2 
				UNION SELECT 3 
				UNION SELECT 4 
				UNION SELECT 5 
				UNION SELECT 6 
				UNION SELECT 7 
				UNION SELECT 8 
				UNION SELECT 9 
				UNION SELECT 10 
				UNION SELECT 11 
				UNION SELECT 12

				)m
				LEFT JOIN (

				SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS scount
				FROM matrix_rectype1
				JOIN matrix_pharvest
				WHERE matrix_rectype1.region = matrix_pharvest.region
				AND matrix_rectype1.province = matrix_pharvest.province
				AND matrix_rectype1.municipality = matrix_pharvest.municipality
				AND matrix_rectype1.barangay = matrix_pharvest.barangay
				AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
				AND matrix_rectype1.season = matrix_pharvest.season
				AND matrix_rectype1.season = ?
				AND matrix_pharvest.crop_indicator =2
				AND matrix_pharvest.cropharv IS NOT NULL 
				GROUP BY (
				matrix_pharvest.cropharv
				)
				ORDER BY matrix_pharvest.cropharv ASC

				)c ON m.month = c.cm

				) secondcrop ON firstcrop.month = secondcrop.month

				LEFT JOIN

				(

				SELECT m.month, c.tcount
				FROM (

				SELECT 1 AS 
				MONTH UNION SELECT 2 
				UNION SELECT 3 
				UNION SELECT 4 
				UNION SELECT 5 
				UNION SELECT 6 
				UNION SELECT 7 
				UNION SELECT 8 
				UNION SELECT 9 
				UNION SELECT 10 
				UNION SELECT 11 
				UNION SELECT 12

				)m
				LEFT JOIN (

				SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS tcount
				FROM matrix_rectype1
				JOIN matrix_pharvest
				WHERE matrix_rectype1.region = matrix_pharvest.region
				AND matrix_rectype1.province = matrix_pharvest.province
				AND matrix_rectype1.municipality = matrix_pharvest.municipality
				AND matrix_rectype1.barangay = matrix_pharvest.barangay
				AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
				AND matrix_rectype1.season = matrix_pharvest.season
				AND matrix_rectype1.season = ?
				AND matrix_pharvest.crop_indicator =3
				AND matrix_pharvest.cropharv IS NOT NULL 
				GROUP BY (
				matrix_pharvest.cropharv
				)
				ORDER BY matrix_pharvest.cropharv ASC

				)c ON m.month = c.cm

				) thirdcrop ON firstcrop.month = thirdcrop.month		
			");
			$irri_stmt=$conn->prepare("
				SELECT firstcrop.month, firstcrop.fcount, secondcrop.scount, thirdcrop.tcount
				FROM (

				SELECT m.month, c.fcount
				FROM (

				SELECT 1 AS 
				MONTH UNION SELECT 2 
				UNION SELECT 3 
				UNION SELECT 4 
				UNION SELECT 5 
				UNION SELECT 6 
				UNION SELECT 7 
				UNION SELECT 8 
				UNION SELECT 9 
				UNION SELECT 10 
				UNION SELECT 11 
				UNION SELECT 12

				)m
				LEFT JOIN (

				SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS fcount
				FROM matrix_rectype1
				JOIN matrix_pharvest, matrix_irrigation
				WHERE matrix_rectype1.region = matrix_pharvest.region
				AND matrix_rectype1.province = matrix_pharvest.province
				AND matrix_rectype1.municipality = matrix_pharvest.municipality
				AND matrix_rectype1.barangay = matrix_pharvest.barangay
				AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
				AND matrix_rectype1.season = matrix_pharvest.season
				AND matrix_rectype1.region = matrix_irrigation.region
				AND matrix_rectype1.province = matrix_irrigation.province
				AND matrix_rectype1.municipality = matrix_irrigation.municipality
				AND matrix_rectype1.barangay = matrix_irrigation.barangay
				AND matrix_rectype1.hh_number = matrix_irrigation.hh_number
				AND matrix_rectype1.season = matrix_irrigation.season
				AND matrix_rectype1.season = ?
				AND matrix_pharvest.crop_indicator =1
				AND matrix_pharvest.cropharv IS NOT NULL
				AND matrix_irrigation.irrigation_prime = 1
				GROUP BY (
				matrix_pharvest.cropharv
				)
				ORDER BY matrix_pharvest.cropharv ASC

				)c ON m.month = c.cm

				) firstcrop

				LEFT JOIN

				(

				SELECT m.month, c.scount
				FROM (

				SELECT 1 AS 
				MONTH UNION SELECT 2 
				UNION SELECT 3 
				UNION SELECT 4 
				UNION SELECT 5 
				UNION SELECT 6 
				UNION SELECT 7 
				UNION SELECT 8 
				UNION SELECT 9 
				UNION SELECT 10 
				UNION SELECT 11 
				UNION SELECT 12

				)m
				LEFT JOIN (

				SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS scount
				FROM matrix_rectype1
				JOIN matrix_pharvest, matrix_irrigation
				WHERE matrix_rectype1.region = matrix_pharvest.region
				AND matrix_rectype1.province = matrix_pharvest.province
				AND matrix_rectype1.municipality = matrix_pharvest.municipality
				AND matrix_rectype1.barangay = matrix_pharvest.barangay
				AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
				AND matrix_rectype1.season = matrix_pharvest.season
				AND matrix_rectype1.region = matrix_irrigation.region
				AND matrix_rectype1.province = matrix_irrigation.province
				AND matrix_rectype1.municipality = matrix_irrigation.municipality
				AND matrix_rectype1.barangay = matrix_irrigation.barangay
				AND matrix_rectype1.hh_number = matrix_irrigation.hh_number
				AND matrix_rectype1.season = matrix_irrigation.season
				AND matrix_rectype1.season = ?
				AND matrix_pharvest.crop_indicator =2
				AND matrix_pharvest.cropharv IS NOT NULL
				AND matrix_irrigation.irrigation_prime = 1
				GROUP BY (
				matrix_pharvest.cropharv
				)
				ORDER BY matrix_pharvest.cropharv ASC

				)c ON m.month = c.cm

				) secondcrop ON firstcrop.month = secondcrop.month

				LEFT JOIN

				(

				SELECT m.month, c.tcount
				FROM (

				SELECT 1 AS 
				MONTH UNION SELECT 2 
				UNION SELECT 3 
				UNION SELECT 4 
				UNION SELECT 5 
				UNION SELECT 6 
				UNION SELECT 7 
				UNION SELECT 8 
				UNION SELECT 9 
				UNION SELECT 10 
				UNION SELECT 11 
				UNION SELECT 12

				)m
				LEFT JOIN (

				SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS tcount
				FROM matrix_rectype1
				JOIN matrix_pharvest, matrix_irrigation
				WHERE matrix_rectype1.region = matrix_pharvest.region
				AND matrix_rectype1.province = matrix_pharvest.province
				AND matrix_rectype1.municipality = matrix_pharvest.municipality
				AND matrix_rectype1.barangay = matrix_pharvest.barangay
				AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
				AND matrix_rectype1.season = matrix_pharvest.season
				AND matrix_rectype1.region = matrix_irrigation.region
				AND matrix_rectype1.province = matrix_irrigation.province
				AND matrix_rectype1.municipality = matrix_irrigation.municipality
				AND matrix_rectype1.barangay = matrix_irrigation.barangay
				AND matrix_rectype1.hh_number = matrix_irrigation.hh_number
				AND matrix_rectype1.season = matrix_irrigation.season
				AND matrix_rectype1.season = ?
				AND matrix_pharvest.crop_indicator =3
				AND matrix_pharvest.cropharv IS NOT NULL
				AND matrix_irrigation.irrigation_prime = 1
				GROUP BY (
				matrix_pharvest.cropharv
				)
				ORDER BY matrix_pharvest.cropharv ASC

				)c ON m.month = c.cm

				) thirdcrop ON firstcrop.month = thirdcrop.month
			");
			$rain_stmt=$conn->prepare("
				SELECT firstcrop.month, firstcrop.fcount, secondcrop.scount, thirdcrop.tcount
				FROM (

				SELECT m.month, c.fcount
				FROM (

				SELECT 1 AS 
				MONTH UNION SELECT 2 
				UNION SELECT 3 
				UNION SELECT 4 
				UNION SELECT 5 
				UNION SELECT 6 
				UNION SELECT 7 
				UNION SELECT 8 
				UNION SELECT 9 
				UNION SELECT 10 
				UNION SELECT 11 
				UNION SELECT 12

				)m
				LEFT JOIN (

				SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS fcount
				FROM matrix_rectype1
				JOIN matrix_pharvest, matrix_irrigation
				WHERE matrix_rectype1.region = matrix_pharvest.region
				AND matrix_rectype1.province = matrix_pharvest.province
				AND matrix_rectype1.municipality = matrix_pharvest.municipality
				AND matrix_rectype1.barangay = matrix_pharvest.barangay
				AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
				AND matrix_rectype1.season = matrix_pharvest.season
				AND matrix_rectype1.region = matrix_irrigation.region
				AND matrix_rectype1.province = matrix_irrigation.province
				AND matrix_rectype1.municipality = matrix_irrigation.municipality
				AND matrix_rectype1.barangay = matrix_irrigation.barangay
				AND matrix_rectype1.hh_number = matrix_irrigation.hh_number
				AND matrix_rectype1.season = matrix_irrigation.season
				AND matrix_rectype1.season = ?
				AND matrix_pharvest.crop_indicator =1
				AND matrix_pharvest.cropharv IS NOT NULL
				AND matrix_irrigation.irrigation_prime = 0
				GROUP BY (
				matrix_pharvest.cropharv
				)
				ORDER BY matrix_pharvest.cropharv ASC

				)c ON m.month = c.cm

				) firstcrop

				LEFT JOIN

				(

				SELECT m.month, c.scount
				FROM (

				SELECT 1 AS 
				MONTH UNION SELECT 2 
				UNION SELECT 3 
				UNION SELECT 4 
				UNION SELECT 5 
				UNION SELECT 6 
				UNION SELECT 7 
				UNION SELECT 8 
				UNION SELECT 9 
				UNION SELECT 10 
				UNION SELECT 11 
				UNION SELECT 12

				)m
				LEFT JOIN (

				SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS scount
				FROM matrix_rectype1
				JOIN matrix_pharvest, matrix_irrigation
				WHERE matrix_rectype1.region = matrix_pharvest.region
				AND matrix_rectype1.province = matrix_pharvest.province
				AND matrix_rectype1.municipality = matrix_pharvest.municipality
				AND matrix_rectype1.barangay = matrix_pharvest.barangay
				AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
				AND matrix_rectype1.season = matrix_pharvest.season
				AND matrix_rectype1.region = matrix_irrigation.region
				AND matrix_rectype1.province = matrix_irrigation.province
				AND matrix_rectype1.municipality = matrix_irrigation.municipality
				AND matrix_rectype1.barangay = matrix_irrigation.barangay
				AND matrix_rectype1.hh_number = matrix_irrigation.hh_number
				AND matrix_rectype1.season = matrix_irrigation.season
				AND matrix_rectype1.season = ?
				AND matrix_pharvest.crop_indicator =2
				AND matrix_pharvest.cropharv IS NOT NULL
				AND matrix_irrigation.irrigation_prime = 0
				GROUP BY (
				matrix_pharvest.cropharv
				)
				ORDER BY matrix_pharvest.cropharv ASC

				)c ON m.month = c.cm

				) secondcrop ON firstcrop.month = secondcrop.month

				LEFT JOIN

				(

				SELECT m.month, c.tcount
				FROM (

				SELECT 1 AS 
				MONTH UNION SELECT 2 
				UNION SELECT 3 
				UNION SELECT 4 
				UNION SELECT 5 
				UNION SELECT 6 
				UNION SELECT 7 
				UNION SELECT 8 
				UNION SELECT 9 
				UNION SELECT 10 
				UNION SELECT 11 
				UNION SELECT 12

				)m
				LEFT JOIN (

				SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS tcount
				FROM matrix_rectype1
				JOIN matrix_pharvest, matrix_irrigation
				WHERE matrix_rectype1.region = matrix_pharvest.region
				AND matrix_rectype1.province = matrix_pharvest.province
				AND matrix_rectype1.municipality = matrix_pharvest.municipality
				AND matrix_rectype1.barangay = matrix_pharvest.barangay
				AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
				AND matrix_rectype1.season = matrix_pharvest.season
				AND matrix_rectype1.region = matrix_irrigation.region
				AND matrix_rectype1.province = matrix_irrigation.province
				AND matrix_rectype1.municipality = matrix_irrigation.municipality
				AND matrix_rectype1.barangay = matrix_irrigation.barangay
				AND matrix_rectype1.hh_number = matrix_irrigation.hh_number
				AND matrix_rectype1.season = matrix_irrigation.season
				AND matrix_rectype1.season = ?
				AND matrix_pharvest.crop_indicator =3
				AND matrix_pharvest.cropharv IS NOT NULL
				AND matrix_irrigation.irrigation_prime = 0
				GROUP BY (
				matrix_pharvest.cropharv
				)
				ORDER BY matrix_pharvest.cropharv ASC

				)c ON m.month = c.cm

				) thirdcrop ON firstcrop.month = thirdcrop.month
			");
			$all_stmt->bind_param("sss", $season, $season, $season);
			$irri_stmt->bind_param("sss", $season, $season, $season);
			$rain_stmt->bind_param("sss", $season, $season, $season);
			}
			else {
			$all_stmt=$conn->prepare("
			SELECT firstcrop.month, firstcrop.fcount, secondcrop.scount, thirdcrop.tcount
			FROM (

			SELECT m.month, c.fcount
			FROM (

			SELECT 1 AS 
			MONTH UNION SELECT 2 
			UNION SELECT 3 
			UNION SELECT 4 
			UNION SELECT 5 
			UNION SELECT 6 
			UNION SELECT 7 
			UNION SELECT 8 
			UNION SELECT 9 
			UNION SELECT 10 
			UNION SELECT 11 
			UNION SELECT 12

			)m
			LEFT JOIN (

			SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS fcount
			FROM matrix_rectype1
			JOIN matrix_pharvest
			WHERE matrix_rectype1.region = matrix_pharvest.region
			AND matrix_rectype1.province = matrix_pharvest.province
			AND matrix_rectype1.municipality = matrix_pharvest.municipality
			AND matrix_rectype1.barangay = matrix_pharvest.barangay
			AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
			AND matrix_rectype1.season = matrix_pharvest.season
			AND matrix_rectype1.season = ?
			AND matrix_rectype1.province = ?
			AND matrix_pharvest.crop_indicator =1
			AND matrix_pharvest.cropharv IS NOT NULL
			GROUP BY (
			matrix_pharvest.cropharv
			)
			ORDER BY matrix_pharvest.cropharv ASC

			)c ON m.month = c.cm

			) firstcrop

			LEFT JOIN

			(

			SELECT m.month, c.scount
			FROM (

			SELECT 1 AS 
			MONTH UNION SELECT 2 
			UNION SELECT 3 
			UNION SELECT 4 
			UNION SELECT 5 
			UNION SELECT 6 
			UNION SELECT 7 
			UNION SELECT 8 
			UNION SELECT 9 
			UNION SELECT 10 
			UNION SELECT 11 
			UNION SELECT 12

			)m
			LEFT JOIN (

			SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS scount
			FROM matrix_rectype1
			JOIN matrix_pharvest
			WHERE matrix_rectype1.region = matrix_pharvest.region
			AND matrix_rectype1.province = matrix_pharvest.province
			AND matrix_rectype1.municipality = matrix_pharvest.municipality
			AND matrix_rectype1.barangay = matrix_pharvest.barangay
			AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
			AND matrix_rectype1.season = matrix_pharvest.season
			AND matrix_rectype1.season = ?
			AND matrix_rectype1.province = ?
			AND matrix_pharvest.crop_indicator =2
			AND matrix_pharvest.cropharv IS NOT NULL 
			GROUP BY (
			matrix_pharvest.cropharv
			)
			ORDER BY matrix_pharvest.cropharv ASC

			)c ON m.month = c.cm

			) secondcrop ON firstcrop.month = secondcrop.month

			LEFT JOIN

			(

			SELECT m.month, c.tcount
			FROM (

			SELECT 1 AS 
			MONTH UNION SELECT 2 
			UNION SELECT 3 
			UNION SELECT 4 
			UNION SELECT 5 
			UNION SELECT 6 
			UNION SELECT 7 
			UNION SELECT 8 
			UNION SELECT 9 
			UNION SELECT 10 
			UNION SELECT 11 
			UNION SELECT 12

			)m
			LEFT JOIN (

			SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS tcount
			FROM matrix_rectype1
			JOIN matrix_pharvest
			WHERE matrix_rectype1.region = matrix_pharvest.region
			AND matrix_rectype1.province = matrix_pharvest.province
			AND matrix_rectype1.municipality = matrix_pharvest.municipality
			AND matrix_rectype1.barangay = matrix_pharvest.barangay
			AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
			AND matrix_rectype1.season = matrix_pharvest.season
			AND matrix_rectype1.season = ?
			AND matrix_rectype1.province = ?
			AND matrix_pharvest.crop_indicator =3
			AND matrix_pharvest.cropharv IS NOT NULL 
			GROUP BY (
			matrix_pharvest.cropharv
			)
			ORDER BY matrix_pharvest.cropharv ASC

			)c ON m.month = c.cm

			) thirdcrop ON firstcrop.month = thirdcrop.month
		");
		$irri_stmt=$conn->prepare("
			SELECT firstcrop.month, firstcrop.fcount, secondcrop.scount, thirdcrop.tcount
			FROM (

			SELECT m.month, c.fcount
			FROM (

			SELECT 1 AS 
			MONTH UNION SELECT 2 
			UNION SELECT 3 
			UNION SELECT 4 
			UNION SELECT 5 
			UNION SELECT 6 
			UNION SELECT 7 
			UNION SELECT 8 
			UNION SELECT 9 
			UNION SELECT 10 
			UNION SELECT 11 
			UNION SELECT 12

			)m
			LEFT JOIN (

			SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS fcount
			FROM matrix_rectype1
			JOIN matrix_pharvest, matrix_irrigation
			WHERE matrix_rectype1.region = matrix_pharvest.region
			AND matrix_rectype1.province = matrix_pharvest.province
			AND matrix_rectype1.municipality = matrix_pharvest.municipality
			AND matrix_rectype1.barangay = matrix_pharvest.barangay
			AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
			AND matrix_rectype1.season = matrix_pharvest.season
			AND matrix_rectype1.region = matrix_irrigation.region
			AND matrix_rectype1.province = matrix_irrigation.province
			AND matrix_rectype1.municipality = matrix_irrigation.municipality
			AND matrix_rectype1.barangay = matrix_irrigation.barangay
			AND matrix_rectype1.hh_number = matrix_irrigation.hh_number
			AND matrix_rectype1.season = matrix_irrigation.season
			AND matrix_rectype1.season = ?
			AND matrix_rectype1.province = ?
			AND matrix_pharvest.crop_indicator =1
			AND matrix_pharvest.cropharv IS NOT NULL
			AND matrix_irrigation.irrigation_prime = 1
			GROUP BY (
			matrix_pharvest.cropharv
			)
			ORDER BY matrix_pharvest.cropharv ASC

			)c ON m.month = c.cm

			) firstcrop

			LEFT JOIN

			(

			SELECT m.month, c.scount
			FROM (

			SELECT 1 AS 
			MONTH UNION SELECT 2 
			UNION SELECT 3 
			UNION SELECT 4 
			UNION SELECT 5 
			UNION SELECT 6 
			UNION SELECT 7 
			UNION SELECT 8 
			UNION SELECT 9 
			UNION SELECT 10 
			UNION SELECT 11 
			UNION SELECT 12

			)m
			LEFT JOIN (

			SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS scount
			FROM matrix_rectype1
			JOIN matrix_pharvest, matrix_irrigation
			WHERE matrix_rectype1.region = matrix_pharvest.region
			AND matrix_rectype1.province = matrix_pharvest.province
			AND matrix_rectype1.municipality = matrix_pharvest.municipality
			AND matrix_rectype1.barangay = matrix_pharvest.barangay
			AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
			AND matrix_rectype1.season = matrix_pharvest.season
			AND matrix_rectype1.region = matrix_irrigation.region
			AND matrix_rectype1.province = matrix_irrigation.province
			AND matrix_rectype1.municipality = matrix_irrigation.municipality
			AND matrix_rectype1.barangay = matrix_irrigation.barangay
			AND matrix_rectype1.hh_number = matrix_irrigation.hh_number
			AND matrix_rectype1.season = matrix_irrigation.season
			AND matrix_rectype1.season = ?
			AND matrix_rectype1.province = ?
			AND matrix_pharvest.crop_indicator =2
			AND matrix_pharvest.cropharv IS NOT NULL
			AND matrix_irrigation.irrigation_prime = 1
			GROUP BY (
			matrix_pharvest.cropharv
			)
			ORDER BY matrix_pharvest.cropharv ASC

			)c ON m.month = c.cm

			) secondcrop ON firstcrop.month = secondcrop.month

			LEFT JOIN

			(

			SELECT m.month, c.tcount
			FROM (

			SELECT 1 AS 
			MONTH UNION SELECT 2 
			UNION SELECT 3 
			UNION SELECT 4 
			UNION SELECT 5 
			UNION SELECT 6 
			UNION SELECT 7 
			UNION SELECT 8 
			UNION SELECT 9 
			UNION SELECT 10 
			UNION SELECT 11 
			UNION SELECT 12

			)m
			LEFT JOIN (

			SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS tcount
			FROM matrix_rectype1
			JOIN matrix_pharvest, matrix_irrigation
			WHERE matrix_rectype1.region = matrix_pharvest.region
			AND matrix_rectype1.province = matrix_pharvest.province
			AND matrix_rectype1.municipality = matrix_pharvest.municipality
			AND matrix_rectype1.barangay = matrix_pharvest.barangay
			AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
			AND matrix_rectype1.season = matrix_pharvest.season
			AND matrix_rectype1.region = matrix_irrigation.region
			AND matrix_rectype1.province = matrix_irrigation.province
			AND matrix_rectype1.municipality = matrix_irrigation.municipality
			AND matrix_rectype1.barangay = matrix_irrigation.barangay
			AND matrix_rectype1.hh_number = matrix_irrigation.hh_number
			AND matrix_rectype1.season = matrix_irrigation.season
			AND matrix_rectype1.season = ?
			AND matrix_rectype1.province = ?
			AND matrix_pharvest.crop_indicator =3
			AND matrix_pharvest.cropharv IS NOT NULL
			AND matrix_irrigation.irrigation_prime = 1
			GROUP BY (
			matrix_pharvest.cropharv
			)
			ORDER BY matrix_pharvest.cropharv ASC

			)c ON m.month = c.cm

			) thirdcrop ON firstcrop.month = thirdcrop.month
		");
		$rain_stmt=$conn->prepare("
			SELECT firstcrop.month, firstcrop.fcount, secondcrop.scount, thirdcrop.tcount
			FROM (

			SELECT m.month, c.fcount
			FROM (

			SELECT 1 AS 
			MONTH UNION SELECT 2 
			UNION SELECT 3 
			UNION SELECT 4 
			UNION SELECT 5 
			UNION SELECT 6 
			UNION SELECT 7 
			UNION SELECT 8 
			UNION SELECT 9 
			UNION SELECT 10 
			UNION SELECT 11 
			UNION SELECT 12

			)m
			LEFT JOIN (

			SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS fcount
			FROM matrix_rectype1
			JOIN matrix_pharvest, matrix_irrigation
			WHERE matrix_rectype1.region = matrix_pharvest.region
			AND matrix_rectype1.province = matrix_pharvest.province
			AND matrix_rectype1.municipality = matrix_pharvest.municipality
			AND matrix_rectype1.barangay = matrix_pharvest.barangay
			AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
			AND matrix_rectype1.season = matrix_pharvest.season
			AND matrix_rectype1.region = matrix_irrigation.region
			AND matrix_rectype1.province = matrix_irrigation.province
			AND matrix_rectype1.municipality = matrix_irrigation.municipality
			AND matrix_rectype1.barangay = matrix_irrigation.barangay
			AND matrix_rectype1.hh_number = matrix_irrigation.hh_number
			AND matrix_rectype1.season = matrix_irrigation.season
			AND matrix_rectype1.season = ?
			AND matrix_rectype1.province = ?
			AND matrix_pharvest.crop_indicator =1
			AND matrix_pharvest.cropharv IS NOT NULL
			AND matrix_irrigation.irrigation_prime = 0
			GROUP BY (
			matrix_pharvest.cropharv
			)
			ORDER BY matrix_pharvest.cropharv ASC

			)c ON m.month = c.cm

			) firstcrop

			LEFT JOIN

			(

			SELECT m.month, c.scount
			FROM (

			SELECT 1 AS 
			MONTH UNION SELECT 2 
			UNION SELECT 3 
			UNION SELECT 4 
			UNION SELECT 5 
			UNION SELECT 6 
			UNION SELECT 7 
			UNION SELECT 8 
			UNION SELECT 9 
			UNION SELECT 10 
			UNION SELECT 11 
			UNION SELECT 12

			)m
			LEFT JOIN (

			SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS scount
			FROM matrix_rectype1
			JOIN matrix_pharvest, matrix_irrigation
			WHERE matrix_rectype1.region = matrix_pharvest.region
			AND matrix_rectype1.province = matrix_pharvest.province
			AND matrix_rectype1.municipality = matrix_pharvest.municipality
			AND matrix_rectype1.barangay = matrix_pharvest.barangay
			AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
			AND matrix_rectype1.season = matrix_pharvest.season
			AND matrix_rectype1.region = matrix_irrigation.region
			AND matrix_rectype1.province = matrix_irrigation.province
			AND matrix_rectype1.municipality = matrix_irrigation.municipality
			AND matrix_rectype1.barangay = matrix_irrigation.barangay
			AND matrix_rectype1.hh_number = matrix_irrigation.hh_number
			AND matrix_rectype1.season = matrix_irrigation.season
			AND matrix_rectype1.season = ?
			AND matrix_rectype1.province = ?
			AND matrix_pharvest.crop_indicator =2
			AND matrix_pharvest.cropharv IS NOT NULL
			AND matrix_irrigation.irrigation_prime = 0
			GROUP BY (
			matrix_pharvest.cropharv
			)
			ORDER BY matrix_pharvest.cropharv ASC

			)c ON m.month = c.cm

			) secondcrop ON firstcrop.month = secondcrop.month

			LEFT JOIN

			(

			SELECT m.month, c.tcount
			FROM (

			SELECT 1 AS 
			MONTH UNION SELECT 2 
			UNION SELECT 3 
			UNION SELECT 4 
			UNION SELECT 5 
			UNION SELECT 6 
			UNION SELECT 7 
			UNION SELECT 8 
			UNION SELECT 9 
			UNION SELECT 10 
			UNION SELECT 11 
			UNION SELECT 12

			)m
			LEFT JOIN (

			SELECT matrix_pharvest.cropharv AS cm, COUNT( * ) AS tcount
			FROM matrix_rectype1
			JOIN matrix_pharvest, matrix_irrigation
			WHERE matrix_rectype1.region = matrix_pharvest.region
			AND matrix_rectype1.province = matrix_pharvest.province
			AND matrix_rectype1.municipality = matrix_pharvest.municipality
			AND matrix_rectype1.barangay = matrix_pharvest.barangay
			AND matrix_rectype1.hh_number = matrix_pharvest.hh_number
			AND matrix_rectype1.season = matrix_pharvest.season
			AND matrix_rectype1.region = matrix_irrigation.region
			AND matrix_rectype1.province = matrix_irrigation.province
			AND matrix_rectype1.municipality = matrix_irrigation.municipality
			AND matrix_rectype1.barangay = matrix_irrigation.barangay
			AND matrix_rectype1.hh_number = matrix_irrigation.hh_number
			AND matrix_rectype1.season = matrix_irrigation.season
			AND matrix_rectype1.season = ?
			AND matrix_rectype1.province = ?
			AND matrix_pharvest.crop_indicator =3
			AND matrix_pharvest.cropharv IS NOT NULL
			AND matrix_irrigation.irrigation_prime = 0
			GROUP BY (
			matrix_pharvest.cropharv
			)
			ORDER BY matrix_pharvest.cropharv ASC

			)c ON m.month = c.cm

			) thirdcrop ON firstcrop.month = thirdcrop.month
		");
		$all_stmt->bind_param("ssssss", $season, $province, $season, $province, $season, $province);
		$irri_stmt->bind_param("ssssss", $season, $province, $season, $province, $season, $province);
		$rain_stmt->bind_param("ssssss", $season, $province, $season, $province, $season, $province);
		}
		
		$all_stmt->execute();
		$all_stmt->store_result();
		$all_stmt->bind_result($month, $first, $second, $third);
			
			while($all_stmt->fetch()){
				if(is_null($first)){
					$first = "0";
				}
				if(is_null($second)){
					$second = "0";
				}
				if(is_null($third)){
					$third = "0";
				}
				
				if($total_farmers[$ti] == 0){
					$divisor = 1;
				}
				else{
					$divisor = $total_farmers[$ti];
				}
				$psched[$month][$i] = round(($first / $divisor) * 100);
				$psched[$month][$i+1] = round(($second / $divisor) * 100);
				$psched[$month][$i+2] = round(($third / $divisor) * 100);
			}
			
			$i += 3;
			$ti++;
			
			$irri_stmt->execute();
			$irri_stmt->store_result();
			$irri_stmt->bind_result($month, $first, $second, $third);
			
			while($irri_stmt->fetch()){
				if(is_null($first)){
					$first = "0";
				}
				if(is_null($second)){
					$second = "0";
				}
				if(is_null($third)){
					$third = "0";
				}
				
				if($total_farmers[$ti] == 0){
					$divisor = 1;
				}
				else{
					$divisor = $total_farmers[$ti];
				}
				$psched[$month][$i] = round(($first / $divisor) * 100);
				$psched[$month][$i+1] = round(($second / $divisor) * 100);
				$psched[$month][$i+2] = round(($third / $divisor) * 100);
			}
			
			$i += 3;
			$ti++;
			
			$rain_stmt->execute();
			$rain_stmt->store_result();
			$rain_stmt->bind_result($month, $first, $second, $third);
			
			while($rain_stmt->fetch()){
				if(is_null($first)){
					$first = "0";
				}
				if(is_null($second)){
					$second = "0";
				}
				if(is_null($third)){
					$third = "0";
				}
				
				if($total_farmers[$ti] == 0){
					$divisor = 1;
				}
				else{
					$divisor = $total_farmers[$ti];
				}
				$psched[$month][$i] = round(($first / $divisor) * 100);
				$psched[$month][$i+1] = round(($second / $divisor) * 100);
				$psched[$month][$i+2] = round(($third / $divisor) * 100);
			}
			
			$i += 3;
			$ti++;
			
			for($j=1; $j<13; $j++){
				for($k=0; $k<$i; $k++){
					if(!isset($psched[$j][$k])){
						$psched[$j][$k] = "0";
					}
				}
			}
	}
	$region = 0;		
	$forprint = $names = array();	
	$getprovince=$conn->prepare("SELECT province_name, region FROM legend_province WHERE province_id = ?");
	$getprovince->bind_param("s", $province);
	$getprovince->execute();
	$getprovince->store_result();
	$getprovince->bind_result($name, $region);
	$getprovince->fetch();
	$count_columns=(count($seasons)*9)+1;
	create_header($name, $season_names, 9);
	create_special_rows("Harvest Month*", $headings, 3);
	create_special_rows("", array_formatting($n_stat,"(n = ",")"), 3);
	create_special_rows("", $headings2, 1);
	echo "<tr>\n<td class='header4 left'></td>\n<td class='header4 center' colspan=".($count_columns-1).">(Percent of farmers)</td>\n</tr>\n";
	create_row("January", $psched[1], 0);
	create_row("February", $psched[2], 0);
	create_row("March", $psched[3], 0);
	create_row("April", $psched[4], 0);
	create_row("May", $psched[5], 0);
	create_row("June", $psched[6], 0);
	create_row("July", $psched[7], 0);
	create_row("August", $psched[8], 0);
	create_row("September", $psched[9], 0);
	create_row("October", $psched[10], 0);
	create_row("November", $psched[11], 0);
	create_row("December", $psched[12], 0);
	$total = create_total_arr($psched[1], $psched[2], $psched[3], $psched[4], $psched[5], $psched[6], $psched[7], $psched[8], $psched[9], $psched[10], $psched[11], $psched[12]);
	foreach ($total as &$value){
		if($value < 100){
			$value = 100 - $value;
		}
		else{
			$value = 0;
		}
	}
	create_row("<i>Not applicable</i>**", $total, 0);
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
			echo '<div>Missing response: '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$n_stat[$i*3]) .')</div>';
		}
	}
	echo "<br/>\n";
	}

echo "<div>*for the largest parcel only</div>\n";
echo "<div>**did not plant rice</div>\n";
echo "<br/>\n";
// if (min($seasons) < 5){
// echo "<div><strong>Notice</strong>: 1996, 1997, 2001, 2002 harvest data are <strong>under development</strong>. Thus, it will only show the <b>ALL ECOSYSTEM</b> data.</div>\n<br/>\n";
// }	
echo displayNoteRounding();
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
?>
<br/>
<div>Data accessed at <?php echo date('l jS \of F Y h:i:s A');?></div>
<?php echo displayNoteContact(); ?>
</div>
<?php
require_once("../includes/export.php");
?>