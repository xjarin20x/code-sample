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
	$dispose = $n_stat = $total_farmers = $total_n = array();
	$counter = 0;
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
	array_push($headings, "ALL ECOSYSTEM");		
	array_push($headings, "IRRIGATED");		
	array_push($headings, "NON-IRRIGATED");		
	
	for($i=0;$i<3;$i++){
		array_push($headings2, "Mean");	
		array_push($headings2, "SD<sup>a</sup>");
	}
	
	if($province==999){
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mprac WHERE matrix_rectype1.region = matrix_mprac.region AND matrix_rectype1.province = matrix_mprac.province AND matrix_rectype1.municipality = matrix_mprac.municipality AND matrix_rectype1.barangay = matrix_mprac.barangay AND matrix_rectype1.hh_number = matrix_mprac.hh_number AND matrix_rectype1.season = matrix_mprac.season AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mprac, matrix_irrigation WHERE matrix_rectype1.region = matrix_mprac.region AND matrix_rectype1.province = matrix_mprac.province AND matrix_rectype1.municipality = matrix_mprac.municipality AND matrix_rectype1.barangay = matrix_mprac.barangay AND matrix_rectype1.hh_number = matrix_mprac.hh_number AND matrix_rectype1.season = matrix_mprac.season AND matrix_rectype1.season = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mprac, matrix_irrigation WHERE matrix_rectype1.region = matrix_mprac.region AND matrix_rectype1.province = matrix_mprac.province AND matrix_rectype1.municipality = matrix_mprac.municipality AND matrix_rectype1.barangay = matrix_mprac.barangay AND matrix_rectype1.hh_number = matrix_mprac.hh_number AND matrix_rectype1.season = matrix_mprac.season AND matrix_rectype1.season = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_source = 0 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mprac WHERE matrix_rectype1.region = matrix_mprac.region AND matrix_rectype1.province = matrix_mprac.province AND matrix_rectype1.municipality = matrix_mprac.municipality AND matrix_rectype1.barangay = matrix_mprac.barangay AND matrix_rectype1.hh_number = matrix_mprac.hh_number AND matrix_rectype1.season = matrix_mprac.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mprac, matrix_irrigation WHERE matrix_rectype1.region = matrix_mprac.region AND matrix_rectype1.province = matrix_mprac.province AND matrix_rectype1.municipality = matrix_mprac.municipality AND matrix_rectype1.barangay = matrix_mprac.barangay AND matrix_rectype1.hh_number = matrix_mprac.hh_number AND matrix_rectype1.season = matrix_mprac.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mprac, matrix_irrigation WHERE matrix_rectype1.region = matrix_mprac.region AND matrix_rectype1.province = matrix_mprac.province AND matrix_rectype1.municipality = matrix_mprac.municipality AND matrix_rectype1.barangay = matrix_mprac.barangay AND matrix_rectype1.hh_number = matrix_mprac.hh_number AND matrix_rectype1.season = matrix_mprac.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_source = 0 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
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
	
	if($season < 6){
		if($province==999){
			$all_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant),  
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant)
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND m.season = ?
			");
			$irri_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant),  
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant)
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i, 
			matrix_irrigation ir 
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND r.region = ir.region AND r.province = ir.province AND r.municipality = ir.municipality AND r.barangay = ir.barangay AND r.hh_number = ir.hh_number AND r.season = ir.season 
			AND (ir.irrigation_source = 1 OR ir.irrigation_source = 2 OR ir.irrigation_source = 3 OR ir.irrigation_source = 4 OR ir.irrigation_source = 5)
			AND m.season = ?
			");
			$rain_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant),  
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant) 
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i, 
			matrix_irrigation ir 
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND r.region = ir.region AND r.province = ir.province AND r.municipality = ir.municipality AND r.barangay = ir.barangay AND r.hh_number = ir.hh_number AND r.season = ir.season 
			AND ir.irrigation_source = 0
			AND m.season = ?
			");
			$all_stmt->bind_param("s", $season);
			$irri_stmt->bind_param("s", $season);
			$rain_stmt->bind_param("s", $season);
		}
		else{
			$all_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant),  
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant)
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND m.season = ? AND m.province = ?
			");
			$irri_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant),  
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant)
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i, 
			matrix_irrigation ir 
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND r.region = ir.region AND r.province = ir.province AND r.municipality = ir.municipality AND r.barangay = ir.barangay AND r.hh_number = ir.hh_number AND r.season = ir.season 
			AND (ir.irrigation_source = 1 OR ir.irrigation_source = 2 OR ir.irrigation_source = 3 OR ir.irrigation_source = 4 OR ir.irrigation_source = 5)
			AND m.season = ? AND m.province = ?
			");
			$rain_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant),  
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant)
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i, 
			matrix_irrigation ir 
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND r.region = ir.region AND r.province = ir.province AND r.municipality = ir.municipality AND r.barangay = ir.barangay AND r.hh_number = ir.hh_number AND r.season = ir.season 
			AND ir.irrigation_source = 0
			AND m.season = ? AND m.province = ?
			");
			$all_stmt->bind_param("ss", $season, $province);
			$irri_stmt->bind_param("ss", $season, $province);
			$rain_stmt->bind_param("ss", $season, $province);
		}
	}
	elseif($season > 8){
		if($province==999){
			$all_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.thresher / 50) / i.aplant), 
			AVG((m.combine / 50) / i.aplant), 
			AVG((m.permlabor / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.irrigation / 50) / i.aplant), 
			AVG((m.machine / 50) / i.aplant),  
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant), 
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant)
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND m.season = ?
			");
			$irri_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.thresher / 50) / i.aplant), 
			AVG((m.combine / 50) / i.aplant), 
			AVG((m.permlabor / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.irrigation / 50) / i.aplant), 
			AVG((m.machine / 50) / i.aplant),  
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant), 
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant) 
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i, 
			matrix_irrigation ir 
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND r.region = ir.region AND r.province = ir.province AND r.municipality = ir.municipality AND r.barangay = ir.barangay AND r.hh_number = ir.hh_number AND r.season = ir.season 
			AND (ir.irrigation_source = 1 OR ir.irrigation_source = 2 OR ir.irrigation_source = 3 OR ir.irrigation_source = 4 OR ir.irrigation_source = 5)
			AND m.season = ?
			");
			$rain_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant),
			AVG((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.thresher / 50) / i.aplant), 
			AVG((m.combine / 50) / i.aplant), 
			AVG((m.permlabor / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.irrigation / 50) / i.aplant), 
			AVG((m.machine / 50) / i.aplant),  
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant), 
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant)
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i, 
			matrix_irrigation ir 
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND r.region = ir.region AND r.province = ir.province AND r.municipality = ir.municipality AND r.barangay = ir.barangay AND r.hh_number = ir.hh_number AND r.season = ir.season 
			AND ir.irrigation_source = 0
			AND m.season = ?
			");
			$all_stmt->bind_param("s", $season);
			$irri_stmt->bind_param("s", $season);
			$rain_stmt->bind_param("s", $season);
		}
		else{
			$all_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.thresher / 50) / i.aplant), 
			AVG((m.combine / 50) / i.aplant), 
			AVG((m.permlabor / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.irrigation / 50) / i.aplant), 
			AVG((m.machine / 50) / i.aplant),  
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant), 
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant)
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND m.season = ? AND m.province = ?
			");
			$irri_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.thresher / 50) / i.aplant), 
			AVG((m.combine / 50) / i.aplant), 
			AVG((m.permlabor / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.irrigation / 50) / i.aplant), 
			AVG((m.machine / 50) / i.aplant),  
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant), 
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant) 
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i, 
			matrix_irrigation ir 
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND r.region = ir.region AND r.province = ir.province AND r.municipality = ir.municipality AND r.barangay = ir.barangay AND r.hh_number = ir.hh_number AND r.season = ir.season 
			AND (ir.irrigation_source = 1 OR ir.irrigation_source = 2 OR ir.irrigation_source = 3 OR ir.irrigation_source = 4 OR ir.irrigation_source = 5)
			AND m.season = ? AND m.province = ?
			");
			$rain_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.thresher / 50) / i.aplant), 
			AVG((m.combine / 50) / i.aplant), 
			AVG((m.permlabor / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.irrigation / 50) / i.aplant), 
			AVG((m.machine / 50) / i.aplant),  
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant), 
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant) 
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i, 
			matrix_irrigation ir 
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND r.region = ir.region AND r.province = ir.province AND r.municipality = ir.municipality AND r.barangay = ir.barangay AND r.hh_number = ir.hh_number AND r.season = ir.season 
			AND ir.irrigation_source = 0
			AND m.season = ? AND m.province = ?
			");
			$all_stmt->bind_param("ss", $season, $province);
			$irri_stmt->bind_param("ss", $season, $province);
			$rain_stmt->bind_param("ss", $season, $province);
		}
	}
	else{
		if($province==999){
			$all_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.thresher / 50) / i.aplant), 
			AVG((m.permlabor / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.irrigation / 50) / i.aplant), 
			AVG((m.machine / 50) / i.aplant),  
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant), 
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant)
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND m.season = ?
			");
			$irri_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.thresher / 50) / i.aplant), 
			AVG((m.permlabor / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.irrigation / 50) / i.aplant), 
			AVG((m.machine / 50) / i.aplant),  
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant), 
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant) 
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i, 
			matrix_irrigation ir 
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND r.region = ir.region AND r.province = ir.province AND r.municipality = ir.municipality AND r.barangay = ir.barangay AND r.hh_number = ir.hh_number AND r.season = ir.season 
			AND (ir.irrigation_source = 1 OR ir.irrigation_source = 2 OR ir.irrigation_source = 3 OR ir.irrigation_source = 4 OR ir.irrigation_source = 5)
			AND m.season = ?
			");
			$rain_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant),
			AVG((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.thresher / 50) / i.aplant), 
			AVG((m.permlabor / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.irrigation / 50) / i.aplant), 
			AVG((m.machine / 50) / i.aplant),  
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant), 
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant)
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i, 
			matrix_irrigation ir 
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND r.region = ir.region AND r.province = ir.province AND r.municipality = ir.municipality AND r.barangay = ir.barangay AND r.hh_number = ir.hh_number AND r.season = ir.season 
			AND ir.irrigation_source = 0
			AND m.season = ?
			");
			$all_stmt->bind_param("s", $season);
			$irri_stmt->bind_param("s", $season);
			$rain_stmt->bind_param("s", $season);
		}
		else{
			$all_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.thresher / 50) / i.aplant), 
			AVG((m.permlabor / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.irrigation / 50) / i.aplant), 
			AVG((m.machine / 50) / i.aplant),  
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant), 
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant)
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND m.season = ? AND m.province = ?
			");
			$irri_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.thresher / 50) / i.aplant), 
			AVG((m.permlabor / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.irrigation / 50) / i.aplant), 
			AVG((m.machine / 50) / i.aplant),  
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant), 
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant) 
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i, 
			matrix_irrigation ir 
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND r.region = ir.region AND r.province = ir.province AND r.municipality = ir.municipality AND r.barangay = ir.barangay AND r.hh_number = ir.hh_number AND r.season = ir.season 
			AND (ir.irrigation_source = 1 OR ir.irrigation_source = 2 OR ir.irrigation_source = 3 OR ir.irrigation_source = 4 OR ir.irrigation_source = 5)
			AND m.season = ? AND m.province = ?
			");
			$rain_stmt = $conn->prepare("
			SELECT 
			AVG((m.harvest / 50) / i.aplant), 
			STDDEV_SAMP((m.harvest / 50) / i.aplant), 
			AVG((m.landlord / 50) / i.aplant), 
			AVG((m.harvster / 50) / i.aplant), 
			AVG((m.thresher / 50) / i.aplant), 
			AVG((m.permlabor / 50) / i.aplant), 
			AVG((m.othlabor / 50) / i.aplant), 
			AVG((m.irrigation / 50) / i.aplant), 
			AVG((m.machine / 50) / i.aplant),  
			AVG((m.home_cons / 50) / i.aplant), 
			AVG((m.seeds / 50) / i.aplant), 
			AVG((m.creditor / 50) / i.aplant), 
			AVG((m.others / 50) / i.aplant), 
			AVG((m.sold / 50) / i.aplant) 
			FROM matrix_mprac m 
			JOIN matrix_rectype1 r, 
			matrix_ioutput i, 
			matrix_irrigation ir 
			WHERE r.region = m.region AND r.province = m.province AND r.municipality = m.municipality AND r.barangay = m.barangay AND r.hh_number = m.hh_number AND r.season = m.season 
			AND i.region = m.region AND i.province = m.province AND i.municipality = m.municipality AND i.barangay = m.barangay AND i.hh_number = m.hh_number AND i.season = m.season 
			AND r.region = ir.region AND r.province = ir.province AND r.municipality = ir.municipality AND r.barangay = ir.barangay AND r.hh_number = ir.hh_number AND r.season = ir.season 
			AND ir.irrigation_source = 0
			AND m.season = ? AND m.province = ?
			");
			$all_stmt->bind_param("ss", $season, $province);
			$irri_stmt->bind_param("ss", $season, $province);
			$rain_stmt->bind_param("ss", $season, $province);
		}
	}

	$keywords = array("harvest","landlord","harvster","thresher","permlabor","othlabor","irrigation","machine","home_cons","seeds","creditor","others","sold","combine");
	$all_stmt->execute();
	$all_stmt->store_result();
	$res_arr = fetch_get_result_alt($all_stmt);
	// PHP 5.3
	// $res = $all_stmt->get_result();
	// $res_arr = $res->fetch_assoc();
	
	$hrvst = $res_arr[key($res_arr)];
	
	foreach ($keywords as $value){
		if(!isset($dispose[$value])){
			$dispose[$value] = array();
		}
		foreach ($res_arr as $key => $n){
			if ($key != "AVG((m.harvest / 50) / i.aplant)") {
				$n = ($n / $hrvst) * 100;
			}
			if($pos = strpos($key, $value)){
				array_push($dispose[$value], $n);
				unset($res_arr[$key]);
			}
		}
	}	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$res_arr = fetch_get_result_alt($irri_stmt);
	// PHP 5.3
	// $res = $irri_stmt->get_result();
	// $res_arr = $res->fetch_assoc();
	
	$hrvst = $res_arr[key($res_arr)];
	
	foreach ($keywords as $value){
		if(!isset($dispose[$value])){
			$dispose[$value] = array();
		}
		foreach ($res_arr as $key => $n){
			if ($key != "AVG((m.harvest / 50) / i.aplant)") {
				$n = ($n / $hrvst) * 100;
			}
			if($pos = strpos($key, $value)){
				array_push($dispose[$value], $n);
				unset($res_arr[$key]);
			}
		}
	}
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$res_arr = fetch_get_result_alt($rain_stmt);
	// PHP 5.3
	// $res = $rain_stmt->get_result();
	// $res_arr = $res->fetch_assoc();
	
	$hrvst = $res_arr[key($res_arr)];
	
	foreach ($keywords as $value){
		if(!isset($dispose[$value])){
			$dispose[$value] = array();
		}
		foreach ($res_arr as $key => $n){
			if ($key != "AVG((m.harvest / 50) / i.aplant)") {
				if ($hrvst != 0){
					$n = ($n / $hrvst) * 100;
				}
				else {
					$n = 0;
				}
			}
			if($pos = strpos($key, $value)){
				array_push($dispose[$value], $n);
				unset($res_arr[$key]);
			}
		}
	}
	foreach ($dispose as $key => $value){
		if(count($dispose[$key]) < (count($dispose['harvest'])/2)){
			$temp = count($dispose[$key]);
			for($i = 0; $i < (count($dispose['harvest']) / 2) - $temp; $i++){
				array_push($dispose[$key], "-");
			}
		}
	}
	
	$all_stmt->close();
	$irri_stmt->close();
	$rain_stmt->close();
	}
	foreach ($dispose as $key => $value){
		foreach ($value as $k => $v){
			if(empty($v)){
				$dispose[$key][$k] = "-";
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
	$letter = "c";
	$isArrayHasSix = $isArrayBelowSix = $isArrayHasSixAgain = $isArrayHasNine = "";
	if(min($seasons) <= 6){
		$isArrayHasSix = "<sup>". $letter . "</sup>";
		$letter++;
		$isArrayHasSixAgain = "<sup>". $letter . "</sup>";
		$letter++;
	}
	if(min($seasons) < 6){
		$isArrayBelowSix = "<sup>". $letter . "</sup>";
		$letter++;
	}
	if(in_array(9, $seasons) || in_array(10, $seasons)) {
		$isArrayHasNine = "<sup>". $letter . "</sup>";
	}
	$count_columns=(count($seasons)*6)+1;

	create_header($name, $season_names, 6);
	create_special_rows("Items", $headings, 2);
	create_special_rows("", array_formatting($total_farmers,"(n = ",")"), 2);
	create_headings("", $headings2);
	create_row_bold("Harvest (bag<sup>b</sup>/ha)", $dispose['harvest'], 1);
	echo "<tr>\n<td class='header4'></td><td class='header4 center' colspan=".($count_columns-1).">(Percentage share of harvest)</td>\n</tr>\n";
	create_special_numbers("Landlord's share", $dispose['landlord'], 2, 1);
	create_special_numbers("Harvester's share".$isArrayHasSix, $dispose['harvster'], 2, 1);
	create_special_numbers("Thresher's share", $dispose['thresher'], 2, 1);
	create_special_numbers("Combine harvester's share", $dispose['combine'], 2, 1);
	create_special_numbers("Permanent laborer's share".$isArrayBelowSix, $dispose['permlabor'], 2, 1);
	create_special_numbers("Other laborer's share", $dispose['othlabor'], 2, 1);
	create_special_numbers("Irrigation fee". $isArrayBelowSix, $dispose['irrigation'], 2, 1);
	create_special_numbers("Machine rental". $isArrayHasSixAgain . $isArrayBelowSix, $dispose['machine'],2, 1);
	create_special_numbers("Home consumption", $dispose['home_cons'], 2, 1);
	create_special_numbers("Seeds", $dispose['seeds'], 2, 1);
	create_special_numbers("Creditors" . $isArrayHasSixAgain, $dispose['creditor'], 2, 1);
	create_special_numbers("Others" . $isArrayHasSixAgain . $isArrayHasNine, $dispose['others'], 2, 1);
	create_special_numbers("Sold", $dispose['sold'], 2, 1);
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
		if ($total_farmers[$i*3] < $total_n[$i]){
			echo '<div>*  Excludes farmers who temporarily stopped farming/did not plant rice/experienced crop failure/with missing data for '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$total_farmers[$i*3]) .')</div>';
		}
	}
	echo "<br/>\n";
	}
$letter = "c";
echo "<div><sup>a </sup><b>SD</b> - refers to standard deviation</div>\n";
echo "<div><sup>b </sup>One bag is equivalent to 50 kilograms.</div>\n";
if(min($seasons) <= 6){ echo "<div><sup>" . $letter . "</sup> Harvester's share refers to both harvester's share and thresher's share; applicable only for 2006 and 2007 harvest season</div>\n"; $letter++;}
if(min($seasons) <= 6){ echo "<div><sup>" . $letter . "</sup> Machine rental, Creditors, and Others are not available in the questionnaire; applicable only for 2007 harvest season</div>\n"; $letter++;}
if(min($seasons) < 6){ echo "<div><sup>" . $letter . "</sup> Permanent laborer's share, irrigation fee, and machine rental are included in Others; applicable only for 2006 harvest season</div>"; $letter++;}
if(in_array(9, $seasons) || in_array(10, $seasons)) { echo "<div><sup>" . $letter . "</sup> Others refers to other uses not cited above; examples are as a gift or given away; applicable only for 2016 and 2017 harvest season</div>";}
echo "<br/>\n";
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
echo displayNoteRounding();
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