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
	$season_names = $headings = $footnotes = array();
	$meth = $mode = $outl = array(); 
	$total_farmers = $total_n = $other_outlets = array();
	$counter = 0;
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	
	for($i=0; $i < 3; $i++){
		array_push($meth, array());
		array_push($mode, array());
	}
	
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
	
	if($province==999){
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mdist WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.season = ?) a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mdist, matrix_irrigation WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_rectype1.season = ?) a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mdist, matrix_irrigation WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_source = 0 AND matrix_rectype1.season = ?) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mdist WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mdist, matrix_irrigation WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mdist, matrix_irrigation WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_source = 0 AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) a");
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

	if($province==999){
		$all_stmt=$conn->prepare("SELECT a.method, COUNT(*) as frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mdist.method_sale as method FROM matrix_rectype1 JOIN matrix_mdist WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.season = ?) a GROUP BY a.method");
		$irri_stmt=$conn->prepare("SELECT a.method, COUNT(*) as frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mdist.method_sale as method FROM matrix_rectype1 JOIN matrix_mdist, matrix_irrigation WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_rectype1.season = ?) a GROUP BY a.method");
		$rain_stmt=$conn->prepare("SELECT a.method, COUNT(*) as frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mdist.method_sale as method FROM matrix_rectype1 JOIN matrix_mdist, matrix_irrigation WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_source = 0 AND matrix_rectype1.season = ?) a GROUP BY a.method");
		$all_stmt->bind_param("s", $season);	
		$irri_stmt->bind_param("s", $season);	
		$rain_stmt->bind_param("s", $season);	
	}
	else{
		$all_stmt=$conn->prepare("SELECT a.method, COUNT(*) as frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mdist.method_sale as method FROM matrix_rectype1 JOIN matrix_mdist WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) a GROUP BY a.method");
		$irri_stmt=$conn->prepare("SELECT a.method, COUNT(*) as frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mdist.method_sale as method FROM matrix_rectype1 JOIN matrix_mdist, matrix_irrigation WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) a GROUP BY a.method");
		$rain_stmt=$conn->prepare("SELECT a.method, COUNT(*) as frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mdist.method_sale as method FROM matrix_rectype1 JOIN matrix_mdist, matrix_irrigation WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_source = 0 AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) a GROUP BY a.method");
		$all_stmt->bind_param("ss", $season, $province);	
		$irri_stmt->bind_param("ss", $season, $province);	
		$rain_stmt->bind_param("ss", $season, $province);	
	}
	
	$c = $counter;
	$rescheck = 0;
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($method, $quantity);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			if($method == '-1' OR $method === NULL){
				if($total_farmers[$counter] != 0){
					$meth[0][$counter]  =  ($quantity / $total_farmers[$counter]) * 100;
					$rescheck = 1;
				}		
			}
			else{
				if($total_farmers[$counter] != 0){
					$meth[$method][$counter]  =  ($quantity / $total_farmers[$counter]) * 100;
				}
				else{
					$meth[$method][$counter]  =  0;
				}
			}
		}
	}
	
	if($rescheck == 0){
		$meth[0][$counter] = 0;
	}
	else{
		$rescheck = 0;
	}
	$counter++;
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($method, $quantity);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			if($method == '-1' OR $method === NULL){
				if($total_farmers[$counter] != 0){
					$meth[0][$counter]  =  ($quantity / $total_farmers[$counter]) * 100;
					$rescheck = 1;
				}		
			}
			else{
				if($total_farmers[$counter] != 0){
					$meth[$method][$counter]  =  ($quantity / $total_farmers[$counter]) * 100;
				}
				else{
					$meth[$method][$counter]  =  0;
				}
			}
		}
	}
	
	if($rescheck == 0){
		$meth[0][$counter] = 0;
	}
	else{
		$rescheck = 0;
	}
	
	$counter++;
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($method, $quantity);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			if($method == '-1' OR $method === NULL){
				if($total_farmers[$counter] != 0){
					$meth[0][$counter]  =  ($quantity / $total_farmers[$counter]) * 100;
					$rescheck = 1;
				}		
			}
			else{
				if($total_farmers[$counter] != 0){
					$meth[$method][$counter]  =  ($quantity / $total_farmers[$counter]) * 100;
				}
				else{
					$meth[$method][$counter]  =  0;
				}
			}
		}
	}
	
	if($rescheck == 0){
		$meth[0][$counter] = 0;
	}
	else{
		$rescheck = 0;
	}
	
	$counter++;
	$counter = $c;
	
	if($province==999){
		$all_stmt=$conn->prepare("SELECT a.mode, COUNT(a.mode) as frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mdist.payment as mode FROM matrix_rectype1 JOIN matrix_mdist WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.season = ?) a GROUP BY a.mode");
		$irri_stmt=$conn->prepare("SELECT a.mode, COUNT(a.mode) as frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mdist.payment as mode FROM matrix_rectype1 JOIN matrix_mdist, matrix_irrigation WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_rectype1.season = ?) a GROUP BY a.mode");
		$rain_stmt=$conn->prepare("SELECT a.mode, COUNT(a.mode) as frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mdist.payment as mode FROM matrix_rectype1 JOIN matrix_mdist, matrix_irrigation WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_source = 0 AND matrix_rectype1.season = ?) a GROUP BY a.mode");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt=$conn->prepare("SELECT a.mode, COUNT(a.mode) as frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mdist.payment as mode FROM matrix_rectype1 JOIN matrix_mdist WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) a GROUP BY a.mode");
		$irri_stmt=$conn->prepare("SELECT a.mode, COUNT(a.mode) as frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mdist.payment as mode FROM matrix_rectype1 JOIN matrix_mdist, matrix_irrigation WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) a GROUP BY a.mode");
		$rain_stmt=$conn->prepare("SELECT a.mode, COUNT(a.mode) as frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mdist.payment as mode FROM matrix_rectype1 JOIN matrix_mdist, matrix_irrigation WHERE matrix_rectype1.region = matrix_mdist.region AND matrix_rectype1.province = matrix_mdist.province AND matrix_rectype1.municipality = matrix_mdist.municipality AND matrix_rectype1.barangay = matrix_mdist.barangay AND matrix_rectype1.hh_number = matrix_mdist.hh_number AND matrix_rectype1.season = matrix_mdist.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_source = 0 AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) a GROUP BY a.mode");
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($pay, $quantity);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			if($pay == '-1' OR $pay === NULL){
				if($total_farmers[$counter] != 0){
					$mode[0][$counter]  =  ($quantity / $total_farmers[$counter]) * 100;
					$rescheck = 1;
				}		
			}
			else{
				if($total_farmers[$counter] != 0){
					$mode[$pay][$counter]  =  ($quantity / $total_farmers[$counter]) * 100;
				}
				else{
					$mode[$pay][$counter]  =  0;
				}
			}
		}
	}
	
	if($rescheck == 0){
		$mode[0][$counter] = 0;
	}
	else{
		$rescheck = 0;
	}
	
	$counter++;
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($pay, $quantity);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			if($pay == '-1' OR $pay === NULL){
				if($total_farmers[$counter] != 0){
					$mode[0][$counter]  =  ($quantity / $total_farmers[$counter]) * 100;
					$rescheck = 1;
				}		
			}
			else{
				if($total_farmers[$counter] != 0){
					$mode[$pay][$counter]  =  ($quantity / $total_farmers[$counter]) * 100;
				}
				else{
					$mode[$pay][$counter]  =  0;
				}
			}
		}
	}
	
	if($rescheck == 0){
		$mode[0][$counter] = 0;
	}
	else{
		$rescheck = 0;
	}
	
	$counter++;
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($pay, $quantity);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			if($pay == '-1' OR $pay === NULL){
				if($total_farmers[$counter] != 0){
					$mode[0][$counter]  =  ($quantity / $total_farmers[$counter]) * 100;
					$rescheck = 1;
				}		
			}
			else{
				if($total_farmers[$counter] != 0){
					$mode[$pay][$counter]  =  ($quantity / $total_farmers[$counter]) * 100;
				}
				else{
					$mode[$pay][$counter]  =  0;
				}
			}
		}
	}
	
	if($rescheck == 0){
		$mode[0][$counter] = 0;
	}
	else{
		$rescheck = 0;
	}
	
	$counter++;
	$counter = $c;
	
	$findlegend = $conn->prepare("SELECT DISTINCT season FROM legend_outlet ORDER BY season DESC");
	$findlegend->execute();
	$findlegend->store_result();
	$findlegend->bind_result($this);
	$season_pool = array();
	while($findlegend->fetch()){
		array_push($season_pool, $this);
	}
	$findlegend->close();
	$outlegend = 0;
	for($i=$season; $i > 0; $i--){
		if(in_array($i, $season_pool)) {
			$outlegend = $i;
			break;
		}
	}
	
	if($province==999){
		$all_stmt=$conn->prepare("SELECT r.outlet, IFNULL(o.outlet_name, r.outlet) as outlname, COUNT(r.outlet) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_outlet.outlet FROM matrix_outlet JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_outlet.region AND matrix_rectype1.province = matrix_outlet.province AND matrix_rectype1.municipality = matrix_outlet.municipality AND matrix_rectype1.barangay = matrix_outlet.barangay AND matrix_rectype1.hh_number = matrix_outlet.hh_number AND matrix_rectype1.season = matrix_outlet.season AND matrix_outlet.season = ? AND matrix_outlet.outlet IS NOT NULL) r LEFT JOIN (SELECT outlet_id, outlet_name FROM legend_outlet WHERE season = ?) o ON r.outlet = o.outlet_id GROUP BY outlname ORDER BY COUNT(r.outlet) DESC");
		$irri_stmt=$conn->prepare("SELECT r.outlet, IFNULL(o.outlet_name, r.outlet) as outlname, COUNT(r.outlet) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_outlet.outlet FROM matrix_outlet JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_outlet.region AND matrix_rectype1.province = matrix_outlet.province AND matrix_rectype1.municipality = matrix_outlet.municipality AND matrix_rectype1.barangay = matrix_outlet.barangay AND matrix_rectype1.hh_number = matrix_outlet.hh_number AND matrix_rectype1.season = matrix_outlet.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_outlet.season = ? AND matrix_outlet.outlet IS NOT NULL) r LEFT JOIN (SELECT outlet_id, outlet_name FROM legend_outlet WHERE season = ?) o ON r.outlet = o.outlet_id GROUP BY outlname ORDER BY COUNT(r.outlet) DESC");
		$rain_stmt=$conn->prepare("SELECT r.outlet, IFNULL(o.outlet_name, r.outlet) as outlname, COUNT(r.outlet) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_outlet.outlet FROM matrix_outlet JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_outlet.region AND matrix_rectype1.province = matrix_outlet.province AND matrix_rectype1.municipality = matrix_outlet.municipality AND matrix_rectype1.barangay = matrix_outlet.barangay AND matrix_rectype1.hh_number = matrix_outlet.hh_number AND matrix_rectype1.season = matrix_outlet.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_source = 0 AND matrix_outlet.season = ? AND matrix_outlet.outlet IS NOT NULL) r LEFT JOIN (SELECT outlet_id, outlet_name FROM legend_outlet WHERE season = ?) o ON r.outlet = o.outlet_id GROUP BY outlname ORDER BY COUNT(r.outlet) DESC");
		$all_stmt->bind_param("ss", $season, $outlegend);	
		$irri_stmt->bind_param("ss", $season, $outlegend);	
		$rain_stmt->bind_param("ss", $season, $outlegend);	
	}
	else{
		$all_stmt=$conn->prepare("SELECT r.outlet, IFNULL(o.outlet_name, r.outlet) as outlname, COUNT(r.outlet) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_outlet.outlet FROM matrix_outlet JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_outlet.region AND matrix_rectype1.province = matrix_outlet.province AND matrix_rectype1.municipality = matrix_outlet.municipality AND matrix_rectype1.barangay = matrix_outlet.barangay AND matrix_rectype1.hh_number = matrix_outlet.hh_number AND matrix_rectype1.season = matrix_outlet.season AND matrix_outlet.season = ? AND matrix_outlet.province = ? AND matrix_outlet.outlet IS NOT NULL) r LEFT JOIN (SELECT outlet_id, outlet_name FROM legend_outlet WHERE season = ?) o ON r.outlet = o.outlet_id GROUP BY outlname ORDER BY COUNT(r.outlet) DESC");
		$irri_stmt=$conn->prepare("SELECT r.outlet, IFNULL(o.outlet_name, r.outlet) as outlname, COUNT(r.outlet) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_outlet.outlet FROM matrix_outlet JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_outlet.region AND matrix_rectype1.province = matrix_outlet.province AND matrix_rectype1.municipality = matrix_outlet.municipality AND matrix_rectype1.barangay = matrix_outlet.barangay AND matrix_rectype1.hh_number = matrix_outlet.hh_number AND matrix_rectype1.season = matrix_outlet.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5) AND matrix_outlet.season = ? AND matrix_outlet.province = ? AND matrix_outlet.outlet IS NOT NULL) r LEFT JOIN (SELECT outlet_id, outlet_name FROM legend_outlet WHERE season = ?) o ON r.outlet = o.outlet_id GROUP BY outlname ORDER BY COUNT(r.outlet) DESC");
		$rain_stmt=$conn->prepare("SELECT r.outlet, IFNULL(o.outlet_name, r.outlet) as outlname, COUNT(r.outlet) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_outlet.outlet FROM matrix_outlet JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_outlet.region AND matrix_rectype1.province = matrix_outlet.province AND matrix_rectype1.municipality = matrix_outlet.municipality AND matrix_rectype1.barangay = matrix_outlet.barangay AND matrix_rectype1.hh_number = matrix_outlet.hh_number AND matrix_rectype1.season = matrix_outlet.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_source = 0 AND matrix_outlet.season = ? AND matrix_outlet.province = ? AND matrix_outlet.outlet IS NOT NULL) r LEFT JOIN (SELECT outlet_id, outlet_name FROM legend_outlet WHERE season = ?) o ON r.outlet = o.outlet_id GROUP BY outlname ORDER BY COUNT(r.outlet) DESC");
		$all_stmt->bind_param("sss", $season, $province, $outlegend);	
		$irri_stmt->bind_param("sss", $season, $province, $outlegend);	
		$rain_stmt->bind_param("sss", $season, $province, $outlegend);	
	}
	
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($outlet_id, $outname, $quantity);
	
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			if(!isset($outl[$outname])){
				$outl[$outname] = array();
			}
			if($total_farmers[$counter] != 0){
				$outl[$outname][$counter] = ($quantity / $total_farmers[$counter]) * 100;
			}
		}
	}
	$counter++;
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($outlet_id, $outname, $quantity);
	
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			if(!isset($outl[$outname])){
				$outl[$outname] = array();
			}
			if($total_farmers[$counter] != 0){
				$outl[$outname][$counter] = ($quantity / $total_farmers[$counter]) * 100;
			}
		}
	}
	$counter++;
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($outlet_id, $outname, $quantity);
	
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			if(!isset($outl[$outname])){
				$outl[$outname] = array();
			}
			if($total_farmers[$counter] != 0){
				$outl[$outname][$counter] = ($quantity / $total_farmers[$counter]) * 100;
			}
		}
	}
	$counter++;
	$all_stmt->close();	
	$irri_stmt->close();	
	$rain_stmt->close();	
	
	if($province==999){
		$all_stmt= $conn->prepare("SELECT matrix_outlet.outlet from matrix_outlet WHERE matrix_outlet.season = ? AND CONCAT('', outlet * 1 ) != outlet ORDER BY outlet ASC");
		$all_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT matrix_outlet.outlet from matrix_outlet WHERE matrix_outlet.province = ? AND matrix_outlet.season = ? AND CONCAT('', outlet * 1 ) != outlet ORDER BY outlet ASC");
		$all_stmt->bind_param("ss", $province, $season);
	}
		$all_stmt->execute();
		$all_stmt->store_result();
		$all_stmt->bind_result($source);
		while($all_stmt->fetch()){ 
			array_push($other_outlets, $source);
		}
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
	create_headings("Items", $headings);
	create_special_rows("", array_formatting($total_farmers,"(n = ",")"), 1);
	echo "<tr>\n<td class='header4'></td><td class='header4 center' colspan=".($count_columns-1).">(Percent of farmers)</td>\n</tr>\n";
	echo "<tr>\n<td class='header4 left bold' colspan=".$count_columns.">Method of Sale</td>\n</tr>\n";
	foreach($meth as $key => $value){
		for($i=0; $i < $counter; $i++){
			if(!isset($meth[$key][$i])){
				$meth[$key][$i] = '0';
			}
		}
		ksort($meth[$key]);
	}
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Delivered", $meth[1], 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Picked-up", $meth[2], 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No response", $meth[0], 0);
	echo "<tr>\n<td class='header4 left bold' colspan=".$count_columns.">Mode of Payment</td>\n</tr>\n";
	foreach($mode as $key => $value){
		for($i=0; $i < $counter; $i++){
			if(!isset($mode[$key][$i])){
				$mode[$key][$i] = '0';
			}
		}
		ksort($mode[$key]);
	}
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Cash", $mode[1], 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Credit", $mode[2], 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No response", $mode[0], 0);
	echo "<tr>\n<td class='header4 left bold' colspan=".$count_columns.">Types of Outlet**</td>\n</tr>\n";
	foreach($outl as $key => $value){
		for($i=0; $i < $counter; $i++){
			if(!isset($outl[$key][$i])){
				$outl[$key][$i] = '0';
			}
		}
		ksort($outl[$key]);
	}
	$others = array();
	foreach($outl as $key => $value){
		if(array_sum($value) >= 1 && $key != "Others"){
			create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".ucfirst($key), $value, 0);
		}
		else {
			$others = create_total_arr($others, $value);
		}
	}
	if(!empty($others)){
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Others<sup>a</sup>", $others, 0);
	}
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
	if(count($other_outlets) != 0){
		echo "<div><sup>a</sup> Others include " . concatenate(array_unique($other_outlets)) . ".</div><br/>";
	}
	}
echo '<div>* Includes only farmers with harvest allotted for sale</div>';
echo "<div>** Respondents provided multiple answers so the percentage exceeded 100.</div>\n<br/>\n";
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