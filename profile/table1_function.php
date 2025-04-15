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
	
	$region = 0;
	
	foreach($provinces as $province){
	$season_names = $headings = $footnotes = array();
	$n_stat = array();
	
	$age_mean = $age_std = $age_range = array();
	$male_p = $female_p = array();
	$single_p = $married_p = $widow_p = $separated_p = array();
	$educ_years = $educ_std = $educ_range = array();
	$exp = $exp_std = $exp_range = array();
	$h_size = $h_size_std = $h_size_range = $h_male = $h_female = array();
	$inc_source = $inc_others = $other_source = $temp_arr = array();
	$attend = $not_to_attend = $org = $non_org = array();
	$categories = $subcategs = array();
	$locate = 0;
	
	$getCategory = $conn->prepare("SELECT sincome_id, sincome_description FROM legend_sincome where sincome_category = 1 OR season = 1 ORDER BY sincome_id ASC");
	$getCategory->execute();
	$getCategory->store_result();
	$getCategory->bind_result($sincome_id, $sincome_description);
	$number_categ = $getCategory->num_rows;
	while($getCategory->fetch()){
		array_push($categories, $sincome_description);
		$inc_source[$sincome_description] = array();
	}
	
	if (in_array(5, $seasons) || in_array(6,$seasons)) {
		$getSubCat = $conn->prepare("SELECT sincome_category, sincome_description FROM legend_sincome where season = 5 ORDER BY sincome_id ASC");
	}
	else{
		if(max($seasons) < 5){
			$getSubCat = $conn->prepare("SELECT sincome_category, sincome_description FROM legend_sincome where 1 = 2");	
		}
		else{
			$getSubCat = $conn->prepare("SELECT sincome_category, sincome_description FROM legend_sincome where sincome_category != 1 AND season IS NULL ORDER BY sincome_id ASC");
		}
	}

	$getSubCat->execute();
	$getSubCat->store_result();
	$getSubCat->bind_result($sincome_category, $sincome_description);
	while($getSubCat->fetch()){
		$k = $sincome_category - 1;
		if(!isset($subcategs[$k])){
			$subcategs[$k] = array();
		}
		array_push($subcategs[$k], $sincome_description);
	}
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
		foreach($seasons as $season){
			$total_farmers =  $total_gender = $total_status = $total_earners = $total_trng = $total_org = $total_income = array();
			$j = 0;
			
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
				$all_stmt= $conn->prepare("SELECT count(matrix_rectype1.region), AVG(matrix_rectype1.age), STDDEV_SAMP(matrix_rectype1.age), MIN(matrix_rectype1.age), MAX(matrix_rectype1.age), AVG(matrix_rectype1.educ_years),  STDDEV_SAMP(matrix_rectype1.educ_years), MIN(matrix_rectype1.educ_years), MAX(matrix_rectype1.educ_years), AVG(matrix_rectype1.farm_exp), STDDEV_SAMP(matrix_rectype1.farm_exp), MIN(matrix_rectype1.farm_exp), MAX(matrix_rectype1.farm_exp), STDDEV_SAMP(matrix_rectype1.hh_male + matrix_rectype1.hh_female), MIN(matrix_rectype1.hh_male + matrix_rectype1.hh_female), MAX(matrix_rectype1.hh_male + matrix_rectype1.hh_female), AVG(matrix_rectype1.hh_male), AVG(matrix_rectype1.hh_female) FROM matrix_rectype1 WHERE matrix_rectype1.season = ?");
				$irri_stmt= $conn->prepare("SELECT count(matrix_rectype1.region), AVG(matrix_rectype1.age), STDDEV_SAMP(matrix_rectype1.age), MIN(matrix_rectype1.age), MAX(matrix_rectype1.age), AVG(matrix_rectype1.educ_years),  STDDEV_SAMP(matrix_rectype1.educ_years), MIN(matrix_rectype1.educ_years), MAX(matrix_rectype1.educ_years), AVG(matrix_rectype1.farm_exp), STDDEV_SAMP(matrix_rectype1.farm_exp), MIN(matrix_rectype1.farm_exp), MAX(matrix_rectype1.farm_exp), STDDEV_SAMP(matrix_rectype1.hh_male + matrix_rectype1.hh_female), MIN(matrix_rectype1.hh_male + matrix_rectype1.hh_female), MAX(matrix_rectype1.hh_male + matrix_rectype1.hh_female), AVG(matrix_rectype1.hh_male), AVG(matrix_rectype1.hh_female) FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 1");
				$rain_stmt= $conn->prepare("SELECT count(matrix_rectype1.region), AVG(matrix_rectype1.age), STDDEV_SAMP(matrix_rectype1.age), MIN(matrix_rectype1.age), MAX(matrix_rectype1.age), AVG(matrix_rectype1.educ_years),  STDDEV_SAMP(matrix_rectype1.educ_years), MIN(matrix_rectype1.educ_years), MAX(matrix_rectype1.educ_years), AVG(matrix_rectype1.farm_exp), STDDEV_SAMP(matrix_rectype1.farm_exp), MIN(matrix_rectype1.farm_exp), MAX(matrix_rectype1.farm_exp), STDDEV_SAMP(matrix_rectype1.hh_male + matrix_rectype1.hh_female), MIN(matrix_rectype1.hh_male + matrix_rectype1.hh_female), MAX(matrix_rectype1.hh_male + matrix_rectype1.hh_female), AVG(matrix_rectype1.hh_male), AVG(matrix_rectype1.hh_female) FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 0");
				$all_stmt->bind_param("s", $season);
				$irri_stmt->bind_param("s", $season);
				$rain_stmt->bind_param("s", $season);
			}
			else{
				$all_stmt= $conn->prepare("SELECT count(matrix_rectype1.region), AVG(matrix_rectype1.age), STDDEV_SAMP(matrix_rectype1.age), MIN(matrix_rectype1.age), MAX(matrix_rectype1.age), AVG(matrix_rectype1.educ_years),  STDDEV_SAMP(matrix_rectype1.educ_years), MIN(matrix_rectype1.educ_years), MAX(matrix_rectype1.educ_years), AVG(matrix_rectype1.farm_exp), STDDEV_SAMP(matrix_rectype1.farm_exp), MIN(matrix_rectype1.farm_exp), MAX(matrix_rectype1.farm_exp), STDDEV_SAMP(matrix_rectype1.hh_male + matrix_rectype1.hh_female), MIN(matrix_rectype1.hh_male + matrix_rectype1.hh_female), MAX(matrix_rectype1.hh_male + matrix_rectype1.hh_female), AVG(matrix_rectype1.hh_male), AVG(matrix_rectype1.hh_female) FROM matrix_rectype1 WHERE matrix_rectype1.province = ? AND matrix_rectype1.season = ?");
				$irri_stmt= $conn->prepare("SELECT count(matrix_rectype1.region), AVG(matrix_rectype1.age), STDDEV_SAMP(matrix_rectype1.age), MIN(matrix_rectype1.age), MAX(matrix_rectype1.age), AVG(matrix_rectype1.educ_years),  STDDEV_SAMP(matrix_rectype1.educ_years), MIN(matrix_rectype1.educ_years), MAX(matrix_rectype1.educ_years), AVG(matrix_rectype1.farm_exp), STDDEV_SAMP(matrix_rectype1.farm_exp), MIN(matrix_rectype1.farm_exp), MAX(matrix_rectype1.farm_exp), STDDEV_SAMP(matrix_rectype1.hh_male + matrix_rectype1.hh_female), MIN(matrix_rectype1.hh_male + matrix_rectype1.hh_female), MAX(matrix_rectype1.hh_male + matrix_rectype1.hh_female), AVG(matrix_rectype1.hh_male), AVG(matrix_rectype1.hh_female) FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.province = ? AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 1");
				$rain_stmt= $conn->prepare("SELECT count(matrix_rectype1.region), AVG(matrix_rectype1.age), STDDEV_SAMP(matrix_rectype1.age), MIN(matrix_rectype1.age), MAX(matrix_rectype1.age), AVG(matrix_rectype1.educ_years),  STDDEV_SAMP(matrix_rectype1.educ_years), MIN(matrix_rectype1.educ_years), MAX(matrix_rectype1.educ_years), AVG(matrix_rectype1.farm_exp), STDDEV_SAMP(matrix_rectype1.farm_exp), MIN(matrix_rectype1.farm_exp), MAX(matrix_rectype1.farm_exp), STDDEV_SAMP(matrix_rectype1.hh_male + matrix_rectype1.hh_female), MIN(matrix_rectype1.hh_male + matrix_rectype1.hh_female), MAX(matrix_rectype1.hh_male + matrix_rectype1.hh_female), AVG(matrix_rectype1.hh_male), AVG(matrix_rectype1.hh_female) FROM matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.province = ? AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 0");
				$all_stmt->bind_param("ss", $province, $season);
				$irri_stmt->bind_param("ss", $province, $season);
				$rain_stmt->bind_param("ss", $province, $season);
			}
			
			if($province==999){
				$sex_stmt1= $conn->prepare("SELECT count(matrix_rectype1.sex) AS sex from matrix_rectype1 WHERE matrix_rectype1.sex != 0 AND matrix_rectype1.season = ?");
				$status_stmt1= $conn->prepare("SELECT count(matrix_rectype1.civil_status) AS civil_status from matrix_rectype1 WHERE matrix_rectype1.civil_status != 0 AND matrix_rectype1.season = ?");
				$inc_stmt1= $conn->prepare("SELECT count(matrix_mincome.major_income) AS major_income from matrix_mincome WHERE matrix_mincome.major_income IS NOT NULL AND matrix_mincome.season = ?");
				$trng_stmt1= $conn->prepare("SELECT count(matrix_rectype1.training) AS training from matrix_rectype1 WHERE matrix_rectype1.training != 0 AND matrix_rectype1.season = ?");
				$org_stmt1= $conn->prepare("SELECT count(matrix_rectype1.organization) AS organization from matrix_rectype1 WHERE matrix_rectype1.organization != 0 AND matrix_rectype1.season = ?");
				$sex_stmt2= $conn->prepare("SELECT count(matrix_rectype1.sex) AS sex from matrix_rectype1 WHERE matrix_rectype1.sex != 0 AND matrix_rectype1.season = ?");
				//irrigated
				$sex_stmt2= $conn->prepare("SELECT count(matrix_rectype1.sex) AS sex from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.sex != 0 AND matrix_rectype1.season = ?");
				$status_stmt2= $conn->prepare("SELECT count(matrix_rectype1.civil_status) AS civil_status from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.civil_status != 0 AND matrix_rectype1.season = ?");
				$inc_stmt2= $conn->prepare("SELECT count(matrix_mincome.major_income) AS major_income from matrix_mincome JOIN matrix_irrigation WHERE matrix_mincome.region = matrix_irrigation.region AND matrix_mincome.province = matrix_irrigation.province AND matrix_mincome.municipality = matrix_irrigation.municipality AND matrix_mincome.barangay = matrix_irrigation.barangay AND matrix_mincome.hh_number = matrix_irrigation.hh_number AND matrix_mincome.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_mincome.major_income IS NOT NULL AND matrix_mincome.season = ?");
				$trng_stmt2= $conn->prepare("SELECT count(matrix_rectype1.training) AS training from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.training != 0 AND matrix_rectype1.season = ?");
				$org_stmt2= $conn->prepare("SELECT count(matrix_rectype1.organization) AS organization from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.organization != 0 AND matrix_rectype1.season = ?");
				//rainfed
				$sex_stmt3= $conn->prepare("SELECT count(matrix_rectype1.sex) AS sex from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.sex != 0 AND matrix_rectype1.season = ?");
				$status_stmt3= $conn->prepare("SELECT count(matrix_rectype1.civil_status) AS civil_status from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.civil_status != 0 AND matrix_rectype1.season = ?");
				$inc_stmt3= $conn->prepare("SELECT count(matrix_mincome.major_income) AS major_income from matrix_mincome JOIN matrix_irrigation WHERE matrix_mincome.region = matrix_irrigation.region AND matrix_mincome.province = matrix_irrigation.province AND matrix_mincome.municipality = matrix_irrigation.municipality AND matrix_mincome.barangay = matrix_irrigation.barangay AND matrix_mincome.hh_number = matrix_irrigation.hh_number AND matrix_mincome.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_mincome.major_income IS NOT NULL AND matrix_mincome.season = ?");
				$trng_stmt3= $conn->prepare("SELECT count(matrix_rectype1.training) AS training from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.training != 0 AND matrix_rectype1.season = ?");
				$org_stmt3= $conn->prepare("SELECT count(matrix_rectype1.organization) AS organization from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.organization != 0 AND matrix_rectype1.season = ?");
				$sex_stmt1->bind_param("s", $season);
				$status_stmt1->bind_param("s", $season);
				$inc_stmt1->bind_param("s", $season);
				$trng_stmt1->bind_param("s", $season);
				$org_stmt1->bind_param("s", $season);
				
				$sex_stmt2->bind_param("s", $season);
				$status_stmt2->bind_param("s", $season);
				$inc_stmt2->bind_param("s", $season);
				$trng_stmt2->bind_param("s", $season);
				$org_stmt2->bind_param("s", $season);
				
				$sex_stmt3->bind_param("s", $season);
				$status_stmt3->bind_param("s", $season);
				$inc_stmt3->bind_param("s", $season);
				$trng_stmt3->bind_param("s", $season);
				$org_stmt3->bind_param("s", $season);
			}
			else{
				$sex_stmt1= $conn->prepare("SELECT count(matrix_rectype1.sex) AS sex from matrix_rectype1 WHERE matrix_rectype1.province = ? AND matrix_rectype1.season = ?");
				$status_stmt1= $conn->prepare("SELECT count(matrix_rectype1.civil_status) AS civil_status from matrix_rectype1 WHERE matrix_rectype1.civil_status != 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ?");
				$inc_stmt1= $conn->prepare("SELECT count(matrix_mincome.major_income) AS major_income from matrix_mincome WHERE matrix_mincome.major_income IS NOT NULL AND matrix_mincome.province = ? AND matrix_mincome.season = ?");
				$trng_stmt1= $conn->prepare("SELECT count(matrix_rectype1.training) AS training from matrix_rectype1 WHERE matrix_rectype1.training != 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ?");
				$org_stmt1= $conn->prepare("SELECT count(matrix_rectype1.organization) AS organization from matrix_rectype1 WHERE matrix_rectype1.organization != 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ?");
				//irrigated
				$sex_stmt2= $conn->prepare("SELECT count(matrix_rectype1.sex) AS sex from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ?");
				$status_stmt2= $conn->prepare("SELECT count(matrix_rectype1.civil_status) AS civil_status from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.civil_status != 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ?");
				$inc_stmt2= $conn->prepare("SELECT count(matrix_mincome.major_income) AS major_income from matrix_mincome JOIN matrix_irrigation WHERE matrix_mincome.region = matrix_irrigation.region AND matrix_mincome.province = matrix_irrigation.province AND matrix_mincome.municipality = matrix_irrigation.municipality AND matrix_mincome.barangay = matrix_irrigation.barangay AND matrix_mincome.hh_number = matrix_irrigation.hh_number AND matrix_mincome.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_mincome.major_income IS NOT NULL AND matrix_mincome.province = ? AND matrix_mincome.season = ?");
				$trng_stmt2= $conn->prepare("SELECT count(matrix_rectype1.training) AS training from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.training != 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ?");
				$org_stmt2= $conn->prepare("SELECT count(matrix_rectype1.organization) AS organization from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.organization != 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ?");
				//rainfed
				$sex_stmt3= $conn->prepare("SELECT count(matrix_rectype1.sex) AS sex from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ?");
				$status_stmt3= $conn->prepare("SELECT count(matrix_rectype1.civil_status) AS civil_status from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.civil_status != 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ?");
				$inc_stmt3= $conn->prepare("SELECT count(matrix_mincome.major_income) AS major_income from matrix_mincome JOIN matrix_irrigation WHERE matrix_mincome.region = matrix_irrigation.region AND matrix_mincome.province = matrix_irrigation.province AND matrix_mincome.municipality = matrix_irrigation.municipality AND matrix_mincome.barangay = matrix_irrigation.barangay AND matrix_mincome.hh_number = matrix_irrigation.hh_number AND matrix_mincome.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_mincome.major_income IS NOT NULL AND matrix_mincome.province = ? AND matrix_mincome.season = ?");
				$trng_stmt3= $conn->prepare("SELECT count(matrix_rectype1.training) AS training from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.training != 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ?");
				$org_stmt3= $conn->prepare("SELECT count(matrix_rectype1.organization) AS organization from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.organization != 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ?");
				$sex_stmt1->bind_param("ss", $province, $season);
				$status_stmt1->bind_param("ss", $province, $season);
				$inc_stmt1->bind_param("ss", $province, $season);
				$trng_stmt1->bind_param("ss", $province, $season);
				$org_stmt1->bind_param("ss", $province, $season);
				
				$sex_stmt2->bind_param("ss", $province, $season);
				$status_stmt2->bind_param("ss", $province, $season);
				$inc_stmt2->bind_param("ss", $province, $season);
				$trng_stmt2->bind_param("ss", $province, $season);
				$org_stmt2->bind_param("ss", $province, $season);
				
				$sex_stmt3->bind_param("ss", $province, $season);
				$status_stmt3->bind_param("ss", $province, $season);
				$inc_stmt3->bind_param("ss", $province, $season);
				$trng_stmt3->bind_param("ss", $province, $season);
				$org_stmt3->bind_param("ss", $province, $season);
			}
			
			$sex_stmt1->execute();
			$sex_stmt1->store_result();
			$value = fetch_get_result_alt($sex_stmt1);
			// PHP 5.3
			// $total= $sex_stmt1->get_result();
			// $value = $total->fetch_assoc();
			array_push($total_gender, $value['sex']);
			
			$status_stmt1->execute();
			$status_stmt1->store_result();
			$value = fetch_get_result_alt($status_stmt1);
			// PHP 5.3
			// $total= $status_stmt1->get_result();
			// $value = $total->fetch_assoc();
			array_push($total_status, $value['civil_status']);
			
			$inc_stmt1->execute();
			$inc_stmt1->store_result();
			$value = fetch_get_result_alt($inc_stmt1);
			// PHP 5.3
			// $total= $inc_stmt1->get_result();
			// $value = $total->fetch_assoc();
			array_push($total_earners, $value['major_income']);
			
			$trng_stmt1->execute();
			$trng_stmt1->store_result();
			$value = fetch_get_result_alt($trng_stmt1);
			// PHP 5.3
			// $total= $trng_stmt1->get_result();
			// $value = $total->fetch_assoc();
			array_push($total_trng, $value['training']);
			
			$org_stmt1->execute();
			$org_stmt1->store_result();
			$value = fetch_get_result_alt($org_stmt1);
			// PHP 5.3
			// $total= $org_stmt1->get_result();
			// $value = $total->fetch_assoc();
			array_push($total_org, $value['organization']);
			
			//irrigated
			$sex_stmt2->execute();
			$sex_stmt2->store_result();
			$value = fetch_get_result_alt($sex_stmt2);
			// PHP 5.3
			// $total= $sex_stmt2->get_result();
			// $value = $total->fetch_assoc();
			array_push($total_gender, $value['sex']);
			
			$status_stmt2->execute();
			$status_stmt2->store_result();
			$value = fetch_get_result_alt($status_stmt2);
			// PHP 5.3
			// $total= $status_stmt2->get_result();
			// $value = $total->fetch_assoc();
			array_push($total_status, $value['civil_status']);
			
			$inc_stmt2->execute();
			$inc_stmt2->store_result();
			$value = fetch_get_result_alt($inc_stmt2);
			// PHP 5.3
			// $total= $inc_stmt2->get_result();
			// $value = $total->fetch_assoc();
			array_push($total_earners, $value['major_income']);
			
			$trng_stmt2->execute();
			$trng_stmt2->store_result();
			$value = fetch_get_result_alt($trng_stmt2);
			// PHP 5.3
			// $total= $trng_stmt2->get_result();
			// $value = $total->fetch_assoc();
			array_push($total_trng, $value['training']);
			
			$org_stmt2->execute();
			$org_stmt2->store_result();
			$value = fetch_get_result_alt($org_stmt2);
			// PHP 5.3
			// $total= $org_stmt2->get_result();
			// $value = $total->fetch_assoc();
			array_push($total_org, $value['organization']);
			
			//rainfed
			$sex_stmt3->execute();
			$sex_stmt3->store_result();
			$value = fetch_get_result_alt($sex_stmt3);
			// PHP 5.3
			// $total= $sex_stmt3->get_result();
			// $value = $total->fetch_assoc();
			array_push($total_gender, $value['sex']);
			
			$status_stmt3->execute();
			$status_stmt3->store_result();
			$value = fetch_get_result_alt($status_stmt3);
			// PHP 5.3
			// $total= $status_stmt3->get_result();
			// $value = $total->fetch_assoc();
			array_push($total_status, $value['civil_status']);
			
			$inc_stmt3->execute();
			$inc_stmt3->store_result();
			$value = fetch_get_result_alt($inc_stmt3);
			// PHP 5.3
			// $total= $inc_stmt3->get_result();
			// $value = $total->fetch_assoc();
			array_push($total_earners, $value['major_income']);
			
			$trng_stmt3->execute();
			$trng_stmt3->store_result();
			$value = fetch_get_result_alt($trng_stmt3);
			// PHP 5.3
			// $total= $trng_stmt3->get_result();
			// $value = $total->fetch_assoc();
			array_push($total_trng, $value['training']);
			
			$org_stmt3->execute();
			$org_stmt3->store_result();
			$value = fetch_get_result_alt($org_stmt3);
			// PHP 5.3
			// $total= $org_stmt3->get_result();
			// $value = $total->fetch_assoc();
			array_push($total_org, $value['organization']);
					
			$all_stmt->execute();
			$all_stmt->store_result();
			$all_stmt->bind_result($count, $age, $age_s, $age_min, $age_max, $educ, $educ_s, $educ_min, $educ_max, $farmexp, $farmexp_std, $farmexp_min, $farmexp_max, $size_std, $size_min, $size_max, $hmale, $hfemale);
			
			while($all_stmt->fetch()){ 
			array_push($total_farmers, $count);
			array_push($age_mean, $age);
			array_push($age_std, $age_s);
			array_push($age_range, $age_min. " - " . $age_max);
			array_push($educ_years, $educ);
			array_push($educ_std, $educ_s);
			array_push($educ_range, $educ_min. " - " . $educ_max);
			array_push($exp, $farmexp);
			array_push($exp_std, $farmexp_std);
			array_push($exp_range, $farmexp_min. " - " . $farmexp_max);
			array_push($h_size, round($hmale) + round($hfemale));
			array_push($h_size_std, $size_std);
			array_push($h_size_range, $size_min . " - " . $size_max);
			array_push($h_male, $hmale);
			array_push($h_female, $hfemale);
			}
			
			$irri_stmt->execute();
			$irri_stmt->store_result();
			$irri_stmt->bind_result($count, $age, $age_s, $age_min, $age_max, $educ, $educ_s, $educ_min, $educ_max, $farmexp, $farmexp_std, $farmexp_min, $farmexp_max, $size_std, $size_min, $size_max, $hmale, $hfemale);
			
			while($irri_stmt->fetch()){ 
			array_push($total_farmers, $count);
			array_push($age_mean, $age);
			array_push($age_std, $age_s);
			array_push($age_range, $age_min. " - " . $age_max);
			array_push($educ_years, $educ);
			array_push($educ_std, $educ_s);
			array_push($educ_range, $educ_min. " - " . $educ_max);
			array_push($exp, $farmexp);
			array_push($exp_std, $farmexp_std);
			array_push($exp_range, $farmexp_min. " - " . $farmexp_max);
			array_push($h_size, round($hmale) + round($hfemale));
			array_push($h_size_std, $size_std);
			array_push($h_size_range, $size_min . " - " . $size_max);
			array_push($h_male, $hmale);
			array_push($h_female, $hfemale);
			}
			
			$rain_stmt->execute();
			$rain_stmt->store_result();
			$rain_stmt->bind_result($count, $age, $age_s, $age_min, $age_max, $educ, $educ_s, $educ_min, $educ_max, $farmexp, $farmexp_std, $farmexp_min, $farmexp_max, $size_std, $size_min, $size_max, $hmale, $hfemale);
			
			while($rain_stmt->fetch()){ 
			array_push($total_farmers, $count);
			array_push($age_mean, $age);
			array_push($age_std, $age_s);
			array_push($age_range, $age_min. " - " . $age_max);
			array_push($educ_years, $educ);
			array_push($educ_std, $educ_s);
			array_push($educ_range, $educ_min. " - " . $educ_max);
			array_push($exp, $farmexp);
			array_push($exp_std, $farmexp_std);
			array_push($exp_range, $farmexp_min. " - " . $farmexp_max);
			array_push($h_size, round($hmale) + round($hfemale));
			array_push($h_size_std, $size_std);
			array_push($h_size_range, $size_min . " - " . $size_max);
			array_push($h_male, $hmale);
			array_push($h_female, $hfemale);
			}
			
			
			$n_stat= array_merge($n_stat, $total_farmers);
			
			if($province==999){
				$all_stmt= $conn->prepare("SELECT matrix_rectype1.sex, count(matrix_rectype1.sex) from matrix_rectype1 WHERE matrix_rectype1.season = ? GROUP BY matrix_rectype1.sex");
				$all_stmt->bind_param("s", $season);
			}
			else{
				$all_stmt= $conn->prepare("SELECT matrix_rectype1.sex, count(matrix_rectype1.sex) from matrix_rectype1 WHERE matrix_rectype1.province = ? AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.sex");
				$all_stmt->bind_param("ss", $province, $season);
			}
			
			$all_stmt->execute();
			$all_stmt->store_result();
			$all_stmt->bind_result($sincome_category, $sex);
			
			while($all_stmt->fetch()){ 
				if($sincome_category==1){
				$temp =  ($sex / $total_gender[$j]) * 100;
				array_push($male_p, $temp);
				}
				elseif($sincome_category==2){
				$temp =  ($sex / $total_gender[$j]) * 100;
				array_push($female_p, $temp);
				}
			array_push($temp_arr, $sincome_category);
			}
			
			for($i=1; $i <= 2 ; $i++){
				if(!in_array($i, $temp_arr)){
					switch ($i){
					case 1:
					array_push($male_p, 0);
					break;
					case 2:
					array_push($female_p, 0);
					break;
					}
				}
			}
			
			$temp_arr = array();
			
			//irrigated
			$j++;
			if($province==999){
				$irri_stmt= $conn->prepare("SELECT matrix_rectype1.sex, count(matrix_rectype1.sex) from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.sex != 0 AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.sex");
				$irri_stmt->bind_param("s", $season);
			}
			else{
				$irri_stmt= $conn->prepare("SELECT matrix_rectype1.sex, count(matrix_rectype1.sex) from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.sex != 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.sex");
				$irri_stmt->bind_param("ss", $province, $season);
			}
			
			$irri_stmt->execute();
			$irri_stmt->store_result();
			$irri_stmt->bind_result($sincome_category, $sex);
			
			while($irri_stmt->fetch()){ 
				if($sincome_category==1){
				$temp =  ($sex / $total_gender[$j]) * 100;
				array_push($male_p, $temp);
				}
				elseif($sincome_category==2){
				$temp =  ($sex / $total_gender[$j]) * 100;
				array_push($female_p, $temp);
				}
			array_push($temp_arr, $sincome_category);
			}
			
			for($i=1; $i <= 2 ; $i++){
				if(!in_array($i, $temp_arr)){
					switch ($i){
					case 1:
					array_push($male_p, 0);
					break;
					case 2:
					array_push($female_p, 0);
					break;
					}
				}
			}
			
			$temp_arr = array();
			
			//rainfed
			$j++;
			if($province==999){
				$rain_stmt= $conn->prepare("SELECT matrix_rectype1.sex, count(matrix_rectype1.sex) from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.sex !=0 AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.sex");
				$rain_stmt->bind_param("s", $season);
			}
			else{
				$rain_stmt= $conn->prepare("SELECT matrix_rectype1.sex, count(matrix_rectype1.sex) from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.sex !=0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.sex");
				$rain_stmt->bind_param("ss", $province, $season);
			}
			
			$rain_stmt->execute();
			$rain_stmt->store_result();
			$rain_stmt->bind_result($sincome_category, $sex);
			
			while($rain_stmt->fetch()){ 
				if($sincome_category==1){
				$temp =  ($sex / $total_gender[$j]) * 100;
				array_push($male_p, $temp);
				}
				elseif($sincome_category==2){
				$temp =  ($sex / $total_gender[$j]) * 100;
				array_push($female_p, $temp);
				}
			array_push($temp_arr, $sincome_category);
			}
			
			for($i=1; $i <= 2 ; $i++){
				if(!in_array($i, $temp_arr)){
					switch ($i){
					case 1:
					array_push($male_p, 0);
					break;
					case 2:
					array_push($female_p, 0);
					break;
					}
				}
			}
			
			if($province==999){
				$all_stmt= $conn->prepare("SELECT matrix_rectype1.civil_status, count(matrix_rectype1.civil_status) from matrix_rectype1 WHERE matrix_rectype1.civil_status != 0 AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.civil_status");
				$all_stmt->bind_param("s", $season);
			}
			else{
				$all_stmt= $conn->prepare("SELECT matrix_rectype1.civil_status, count(matrix_rectype1.civil_status) from matrix_rectype1 WHERE matrix_rectype1.civil_status != 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.civil_status");
				$all_stmt->bind_param("ss", $province, 	$season);
			}
			
			$all_stmt->execute();
			$all_stmt->store_result();
			$all_stmt->bind_result($sincome_category, $status);
			
			$j = 0;
			
			while($all_stmt->fetch()){ 
				$temp =  ($status / $total_status[$j]) * 100;
				switch ($sincome_category) {
				case 1:
				array_push($single_p, $temp);
				break;
				case 2:
				array_push($married_p, $temp);
				break;
				case 3:
				array_push($widow_p, $temp);
				break;
				case 4:
				array_push($separated_p, $temp);
				break;
				}
				array_push($temp_arr, $sincome_category);
			}
			
			for($i=1; $i <= 4 ; $i++){
				if(!in_array($i, $temp_arr)){
					switch ($i){
					case 1:
					array_push($single_p, 0);
					break;
					case 2:
					array_push($married_p, 0);
					break;
					case 3:
					array_push($widow_p, 0);
					break;
					case 4:
					array_push($separated_p, 0);
					break;
					}
				}
			}
			
			$temp_arr = array();
			$j++;
			
			if($province==999){
				$irri_stmt= $conn->prepare("SELECT matrix_rectype1.civil_status, count(matrix_rectype1.civil_status) from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.civil_status != 0 AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.civil_status");
				$irri_stmt->bind_param("s", $season);
			}
			else{
				$irri_stmt= $conn->prepare("SELECT matrix_rectype1.civil_status, count(matrix_rectype1.civil_status) from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.civil_status != 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.civil_status");
				$irri_stmt->bind_param("ss", $province, 	$season);
			}
			
			$irri_stmt->execute();
			$irri_stmt->store_result();
			$irri_stmt->bind_result($sincome_category, $status);
			
			while($irri_stmt->fetch()){ 
				$temp =  ($status / $total_status[$j]) * 100;
				switch ($sincome_category) {
				case 1:
				array_push($single_p, $temp);
				break;
				case 2:
				array_push($married_p, $temp);
				break;
				case 3:
				array_push($widow_p, $temp);
				break;
				case 4:
				array_push($separated_p, $temp);
				break;
				}
				array_push($temp_arr, $sincome_category);
			}
			
			for($i=1; $i <= 4 ; $i++){
				if(!in_array($i, $temp_arr)){
					switch ($i){
					case 1:
					array_push($single_p, 0);
					break;
					case 2:
					array_push($married_p, 0);
					break;
					case 3:
					array_push($widow_p, 0);
					break;
					case 4:
					array_push($separated_p, 0);
					break;
					}
				}
			}
			
			$temp_arr = array();
			$j++;
			
			if($province==999){
				$rain_stmt= $conn->prepare("SELECT matrix_rectype1.civil_status, count(matrix_rectype1.civil_status) from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.civil_status != 0 AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.civil_status");
				$rain_stmt->bind_param("s", $season);
			}
			else{
				$rain_stmt= $conn->prepare("SELECT matrix_rectype1.civil_status, count(matrix_rectype1.civil_status) from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.civil_status != 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.civil_status");
				$rain_stmt->bind_param("ss", $province, $season);
			}
			
			$rain_stmt->execute();
			$rain_stmt->store_result();
			$rain_stmt->bind_result($sincome_category, $status);
			
			while($rain_stmt->fetch()){ 
				$temp =  ($status / $total_status[$j]) * 100;
				switch ($sincome_category) {
				case 1:
				array_push($single_p, $temp);
				break;
				case 2:
				array_push($married_p, $temp);
				break;
				case 3:
				array_push($widow_p, $temp);
				break;
				case 4:
				array_push($separated_p, $temp);
				break;
				}
				array_push($temp_arr, $sincome_category);
			}
			
			for($i=1; $i <= 4 ; $i++){
				if(!in_array($i, $temp_arr)){
					switch ($i){
					case 1:
					array_push($single_p, 0);
					break;
					case 2:
					array_push($married_p, 0);
					break;
					case 3:
					array_push($widow_p, 0);
					break;
					case 4:
					array_push($separated_p, 0);
					break;
					}
				}
			}
			
			$temp_arr = array();
			
//--------- Source of income
			$j = 0;
			if ($season < 5){
				$getLegend = $conn->prepare("SELECT sincome_id, sincome_description FROM legend_sincome where sincome_category = 1 OR season = 1 ORDER BY sincome_id ASC");
			}
			elseif ($season == 5 || $season == 6){
				$getLegend = $conn->prepare("SELECT sincome_id, sincome_description FROM legend_sincome where sincome_category = 1 OR season = 5 ORDER BY sincome_id ASC");
			}
			else{
				$getLegend = $conn->prepare("SELECT sincome_id, sincome_description FROM legend_sincome where sincome_category = 1 OR season IS NULL ORDER BY sincome_id ASC");
			}

			$getLegend->execute();
			$getLegend->store_result();
			$getLegend->bind_result($sincome_id, $sincome_description);
			while($getLegend->fetch()){
			//all ecosystem
				if($province==999){
					$legend_all_stmt= $conn->prepare("SELECT matrix_mincome.major_income, count(matrix_mincome.major_income) from matrix_mincome WHERE matrix_mincome.season = ? AND matrix_mincome.major_income = ? GROUP BY matrix_mincome.major_income");
					$legend_irri_stmt= $conn->prepare("SELECT matrix_mincome.major_income, count(matrix_mincome.major_income) from matrix_mincome JOIN matrix_irrigation WHERE matrix_mincome.season = ? AND matrix_mincome.major_income = ? AND matrix_mincome.region = matrix_irrigation.region AND matrix_mincome.province = matrix_irrigation.province AND matrix_mincome.municipality = matrix_irrigation.municipality AND matrix_mincome.barangay = matrix_irrigation.barangay AND matrix_mincome.hh_number = matrix_irrigation.hh_number AND matrix_mincome.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_mincome.major_income IS NOT NULL GROUP BY matrix_mincome.major_income");
					$legend_rain_stmt= $conn->prepare("SELECT matrix_mincome.major_income, count(matrix_mincome.major_income) from matrix_mincome JOIN matrix_irrigation WHERE matrix_mincome.season = ? AND matrix_mincome.major_income = ? AND matrix_mincome.region = matrix_irrigation.region AND matrix_mincome.province = matrix_irrigation.province AND matrix_mincome.municipality = matrix_irrigation.municipality AND matrix_mincome.barangay = matrix_irrigation.barangay AND matrix_mincome.hh_number = matrix_irrigation.hh_number AND matrix_mincome.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_mincome.major_income IS NOT NULL GROUP BY matrix_mincome.major_income");
					$legend_all_stmt->bind_param("ss", $season, $sincome_id);
					$legend_irri_stmt->bind_param("ss", $season, $sincome_id);
					$legend_rain_stmt->bind_param("ss", $season, $sincome_id);
				}
				else{
					$legend_all_stmt= $conn->prepare("SELECT matrix_mincome.major_income, count(matrix_mincome.major_income) from matrix_mincome WHERE matrix_mincome.province = ? AND matrix_mincome.season = ? AND matrix_mincome.major_income = ? GROUP BY matrix_mincome.major_income");
					$legend_irri_stmt= $conn->prepare("SELECT matrix_mincome.major_income, count(matrix_mincome.major_income) from matrix_mincome JOIN matrix_irrigation WHERE matrix_mincome.province =? AND matrix_mincome.season = ? AND matrix_mincome.major_income = ? AND matrix_mincome.region = matrix_irrigation.region AND matrix_mincome.province = matrix_irrigation.province AND matrix_mincome.municipality = matrix_irrigation.municipality AND matrix_mincome.barangay = matrix_irrigation.barangay AND matrix_mincome.hh_number = matrix_irrigation.hh_number AND matrix_mincome.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_mincome.major_income IS NOT NULL GROUP BY matrix_mincome.major_income");
					$legend_rain_stmt= $conn->prepare("SELECT matrix_mincome.major_income, count(matrix_mincome.major_income) from matrix_mincome JOIN matrix_irrigation WHERE matrix_mincome.province =? AND matrix_mincome.season = ? AND matrix_mincome.major_income = ? AND matrix_mincome.region = matrix_irrigation.region AND matrix_mincome.province = matrix_irrigation.province AND matrix_mincome.municipality = matrix_irrigation.municipality AND matrix_mincome.barangay = matrix_irrigation.barangay AND matrix_mincome.hh_number = matrix_irrigation.hh_number AND matrix_mincome.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_mincome.major_income IS NOT NULL GROUP BY matrix_mincome.major_income");
					$legend_all_stmt->bind_param("sss", $province, $season, $sincome_id);
					$legend_irri_stmt->bind_param("sss", $province, $season, $sincome_id);
					$legend_rain_stmt->bind_param("sss", $province, $season, $sincome_id);
				}
				if(!isset($inc_source[$sincome_description])){
					$inc_source[$sincome_description] = array();
				}
				$j = 0;
				$legend_all_stmt->execute();
				$legend_all_stmt->store_result();
				$legend_all_stmt->bind_result($sincome_category, $source);
				$number_rows = $legend_all_stmt->num_rows;
				if($number_rows == 0){
					array_push($inc_source[$sincome_description], 0);
				}
				else{
					while($legend_all_stmt->fetch()){ 
						array_push($inc_source[$sincome_description], ($source / $total_farmers[$j]) * 100);
						array_push($total_income, $source);
					}
				}
				$j++;
				$legend_irri_stmt->execute();
				$legend_irri_stmt->store_result();
				$legend_irri_stmt->bind_result($sincome_category, $source);
				$number_rows = $legend_irri_stmt->num_rows;
				if($number_rows == 0){
					array_push($inc_source[$sincome_description], 0);
				}
				else{
					while($legend_irri_stmt->fetch()){ 
						array_push($inc_source[$sincome_description], ($source / $total_farmers[$j]) * 100);
						array_push($total_income, $source);
					}
				}
				$j++;
				$legend_rain_stmt->execute();
				$legend_rain_stmt->store_result();
				$legend_rain_stmt->bind_result($sincome_category, $source);
				$number_rows = $legend_rain_stmt->num_rows;
				if($number_rows == 0){
					array_push($inc_source[$sincome_description], 0);
				}
				else{
					while($legend_rain_stmt->fetch()){ 
						array_push($inc_source[$sincome_description], ($source / $total_farmers[$j]) * 100);
						array_push($total_income, $source);
					}
				}
			
			foreach($subcategs as $key => $value){
				foreach($value as $pointer){
					for($i=0; $i < $locate ; $i++){
						if(isset($inc_source[$pointer])){
							if(!isset($inc_source[$pointer][$i])){
								$inc_source[$pointer][$i] = "...";
							}
						}
						else{
							$inc_source[$pointer] = array();
							$inc_source[$pointer][$i] = "...";
						}
					}
				}
			}
			//end of loop
			}
			$locate = $locate + 3;

			$j = 0;
			if($province==999){
				$all_stmt= $conn->prepare("SELECT COUNT(matrix_mincome.major_income) from matrix_mincome WHERE matrix_mincome.season = ? AND CONCAT('', major_income * 1 ) != major_income ORDER BY major_income ASC");
				$irri_stmt= $conn->prepare("SELECT COUNT(matrix_mincome.major_income) from matrix_mincome JOIN matrix_irrigation WHERE matrix_mincome.season = ? AND CONCAT('', major_income * 1 ) != major_income  AND matrix_mincome.region = matrix_irrigation.region AND matrix_mincome.province = matrix_irrigation.province AND matrix_mincome.municipality = matrix_irrigation.municipality AND matrix_mincome.barangay = matrix_irrigation.barangay AND matrix_mincome.hh_number = matrix_irrigation.hh_number AND matrix_mincome.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_mincome.major_income IS NOT NULL ORDER BY major_income ASC");
				$rain_stmt= $conn->prepare("SELECT COUNT(matrix_mincome.major_income) from matrix_mincome JOIN matrix_irrigation WHERE matrix_mincome.season = ? AND CONCAT('', major_income * 1 ) != major_income  AND matrix_mincome.region = matrix_irrigation.region AND matrix_mincome.province = matrix_irrigation.province AND matrix_mincome.municipality = matrix_irrigation.municipality AND matrix_mincome.barangay = matrix_irrigation.barangay AND matrix_mincome.hh_number = matrix_irrigation.hh_number AND matrix_mincome.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_mincome.major_income IS NOT NULL ORDER BY major_income ASC");
				$all_stmt->bind_param("s", $season);
				$irri_stmt->bind_param("s", $season);
				$rain_stmt->bind_param("s", $season);
			}
			else{
				$all_stmt= $conn->prepare("SELECT COUNT(matrix_mincome.major_income) from matrix_mincome WHERE matrix_mincome.season = ? AND matrix_mincome.province = ? AND CONCAT('', major_income * 1 ) != major_income ORDER BY major_income ASC");
				$irri_stmt= $conn->prepare("SELECT COUNT(matrix_mincome.major_income) from matrix_mincome JOIN matrix_irrigation WHERE matrix_mincome.season = ? AND matrix_mincome.province = ? AND CONCAT('', major_income * 1 ) != major_income  AND matrix_mincome.region = matrix_irrigation.region AND matrix_mincome.province = matrix_irrigation.province AND matrix_mincome.municipality = matrix_irrigation.municipality AND matrix_mincome.barangay = matrix_irrigation.barangay AND matrix_mincome.hh_number = matrix_irrigation.hh_number AND matrix_mincome.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_mincome.major_income IS NOT NULL ORDER BY major_income ASC");
				$rain_stmt= $conn->prepare("SELECT COUNT(matrix_mincome.major_income) from matrix_mincome JOIN matrix_irrigation WHERE matrix_mincome.season = ? AND matrix_mincome.province = ? AND CONCAT('', major_income * 1 ) != major_income  AND matrix_mincome.region = matrix_irrigation.region AND matrix_mincome.province = matrix_irrigation.province AND matrix_mincome.municipality = matrix_irrigation.municipality AND matrix_mincome.barangay = matrix_irrigation.barangay AND matrix_mincome.hh_number = matrix_irrigation.hh_number AND matrix_mincome.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_mincome.major_income IS NOT NULL ORDER BY major_income ASC");
				$all_stmt->bind_param("ss", $province, $season);
				$irri_stmt->bind_param("ss", $province, $season);
				$rain_stmt->bind_param("ss", $province, $season);
			}
			$j=0;
			$all_stmt->execute();
			$all_stmt->store_result();
			$all_stmt->bind_result($count);
			while($all_stmt->fetch()){ 
				if($total_farmers[$j] != 0){
					array_push($inc_others,($count / $total_farmers[$j]) * 100);
				}
				else{
					array_push($inc_others, 0);
				}
			}
			$j++;
			$irri_stmt->execute();
			$irri_stmt->store_result();
			$irri_stmt->bind_result($count);
			while($irri_stmt->fetch()){ 
				if($total_farmers[$j] != 0){
					array_push($inc_others,($count / $total_farmers[$j]) * 100);
				}
				else{
					array_push($inc_others, 0);
				}
			}
			$j++;
			$rain_stmt->execute();
			$rain_stmt->store_result();
			$rain_stmt->bind_result($count);
			while($rain_stmt->fetch()){ 
				if($total_farmers[$j] != 0){
					array_push($inc_others,($count / $total_farmers[$j]) * 100);
				}
				else{
					array_push($inc_others, 0);
				}
			}
			
			if($province==999){
				$all_stmt= $conn->prepare("SELECT matrix_mincome.major_income from matrix_mincome WHERE matrix_mincome.season = ? AND CONCAT('', major_income * 1 ) != major_income ORDER BY major_income ASC");
				$all_stmt->bind_param("s", $season);
			}
			else{
				$all_stmt= $conn->prepare("SELECT matrix_mincome.major_income from matrix_mincome WHERE matrix_mincome.province = ? AND matrix_mincome.season = ? AND CONCAT('', major_income * 1 ) != major_income ORDER BY major_income ASC");
				$all_stmt->bind_param("ss", $province, $season);
			}
			$all_stmt->execute();
			$all_stmt->store_result();
			$all_stmt->bind_result($source);
			while($all_stmt->fetch()){ 
				array_push($other_source, $source);
			}
//--------- END OF source of income
			$j=0;
			if($province==999){
				$all_stmt= $conn->prepare("SELECT matrix_rectype1.training, count(matrix_rectype1.training) from matrix_rectype1 WHERE matrix_rectype1.training != 0 AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.training");
				$all_stmt->bind_param("s", $season);
			}
			else{
				$all_stmt= $conn->prepare("SELECT matrix_rectype1.training, count(matrix_rectype1.training) from matrix_rectype1 WHERE matrix_rectype1.training != 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.training");
				$all_stmt->bind_param("ss", $province, $season);
			}
			
			$all_stmt->execute();
			$all_stmt->store_result();
			$all_stmt->bind_result($sincome_category, $training);
			
			$count = count($attend);
			
			while($all_stmt->fetch()){ 
				$j = 0;
				$temp =  ($training / $total_trng[$j]) * 100;
				switch ($sincome_category) {
				case 1:
				$attend[$count] = $temp;
				break;
				case 2:
				$not_to_attend[$count] = $temp;
				break;
				}
			}
			
			if( $count  == count($attend) ){
				$attend[$count] = 0;
			}
			if( $count == count($not_to_attend) ){
				$not_to_attend[$count] = 0;
			}
			//irrigated
			$j++;
			if($province==999){
				$irri_stmt= $conn->prepare("SELECT matrix_rectype1.training, count(matrix_rectype1.training) from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.training != 0 AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.training");
				$irri_stmt->bind_param("s", $season);
			}
			else{
				$irri_stmt= $conn->prepare("SELECT matrix_rectype1.training, count(matrix_rectype1.training) from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.training != 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.training");
				$irri_stmt->bind_param("ss", $province, $season);
			}
			
			$irri_stmt->execute();
			$irri_stmt->store_result();
			$irri_stmt->bind_result($sincome_category, $training);
			
			$count = count($attend);
			
			while($irri_stmt->fetch()){ 
				$temp =  ($training / $total_trng[$j]) * 100;
				switch ($sincome_category) {
				case 1:
				$attend[$count] = $temp;
				break;
				case 2:
				$not_to_attend[$count] = $temp;
				break;
				}
			}
			
			if( $count  == count($attend) ){
				$attend[$count] = 0;
			}
			if( $count == count($not_to_attend) ){
				$not_to_attend[$count] = 0;
			}
			//rainfed
			$j++;
			if($province==999){
				$rain_stmt= $conn->prepare("SELECT matrix_rectype1.training, count(matrix_rectype1.training) from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.training != 0 AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.training");
				$rain_stmt->bind_param("s", $season);
			}
			else{
				$rain_stmt= $conn->prepare("SELECT matrix_rectype1.training, count(matrix_rectype1.training) from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.training != 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.training");
				$rain_stmt->bind_param("ss", $province, $season);
			}
			
			$rain_stmt->execute();
			$rain_stmt->store_result();
			$rain_stmt->bind_result($sincome_category, $training);
			
			$count = count($attend);
			
			while($rain_stmt->fetch()){ 
				$temp =  ($training / $total_trng[$j]) * 100;
				switch ($sincome_category) {
				case 1:
				$attend[$count] = $temp;
				break;
				case 2:
				$not_to_attend[$count] = $temp;
				break;
				}
			}
			if( $count  == count($attend) ){
				$attend[$count] = 0;
			}
			if( $count == count($not_to_attend) ){
				$not_to_attend[$count] = 0;
			}
			$j=0;
			if($province==999){
				$all_stmt= $conn->prepare("SELECT matrix_rectype1.organization, count(matrix_rectype1.organization) from matrix_rectype1 WHERE matrix_rectype1.season = ? GROUP BY matrix_rectype1.organization");
				$all_stmt->bind_param("s", $season);
			}
			else{
				$all_stmt= $conn->prepare("SELECT matrix_rectype1.organization, count(matrix_rectype1.organization) from matrix_rectype1 WHERE matrix_rectype1.province = ? AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.organization");
				$all_stmt->bind_param("ss", $province, $season);
			}
			
			$all_stmt->execute();
			$all_stmt->store_result();
			$all_stmt->bind_result($sincome_category, $member);
			
			$count = count($org);
			
			while($all_stmt->fetch()){ 
				if ($total_org[$j] != 0){
				$temp =  ($member / $total_org[$j]) * 100;
				}
				else{
				$temp = 0;
				}
				switch ($sincome_category) {
				case 1:
				$org[$count] = $temp;
				break;
				case 2:
				$non_org[$count] = $temp;
				break;
				}
			}
			
			if( $count  == count($org) ){
				$org[$count] = 0;
			}
			if( $count == count($non_org) ){
				$non_org[$count] = 0;
			}
			//irrigated
			$j++;
			if($province==999){
				$irri_stmt= $conn->prepare("SELECT matrix_rectype1.organization, count(matrix_rectype1.organization) from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.organization");
				$irri_stmt->bind_param("s", $season);
			}
			else{
				$irri_stmt= $conn->prepare("SELECT matrix_rectype1.organization, count(matrix_rectype1.organization) from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.organization");
				$irri_stmt->bind_param("ss", $province, $season);
			}
			
			$irri_stmt->execute();
			$irri_stmt->store_result();
			$irri_stmt->bind_result($sincome_category, $member);
			
			$count = count($org);
			
			while($irri_stmt->fetch()){ 
				if ($total_org[$j] != 0){
				$temp =  ($member / $total_org[$j]) * 100;
				}
				else{
				$temp = 0;
				}
				switch ($sincome_category) {
				case 1:
				$org[$count] = $temp;
				break;
				case 2:
				$non_org[$count] = $temp;
				break;
				}
			}
			
			if( $count  == count($org) ){
				$org[$count] = 0;
			}
			if( $count == count($non_org) ){
				$non_org[$count] = 0;
			}
			//rainfed
			$j++;
			if($province==999){
				$irri_stmt= $conn->prepare("SELECT matrix_rectype1.organization, count(matrix_rectype1.organization) from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.organization");
				$irri_stmt->bind_param("s", $season);
			}
			else{
				$irri_stmt= $conn->prepare("SELECT matrix_rectype1.organization, count(matrix_rectype1.organization) from matrix_rectype1 JOIN matrix_irrigation WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype1.province = ? AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.organization");
				$irri_stmt->bind_param("ss", $province, $season);
			}
			
			$irri_stmt->execute();
			$irri_stmt->store_result();
			$irri_stmt->bind_result($sincome_category, $member);
			
			$count = count($org);
			
			while($irri_stmt->fetch()){ 
				if ($total_org[$j] != 0){
				$temp =  ($member / $total_org[$j]) * 100;
				}
				else{
				$temp = 0;
				}
				switch ($sincome_category) {
				case 1:
				$org[$count] = $temp;
				break;
				case 2:
				$non_org[$count] = $temp;
				break;
				}
			}
			
			if( $count  == count($org) ){
				$org[$count] = 0;
			}
			if( $count == count($non_org) ){
				$non_org[$count] = 0;
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
	
	$age_mean = check_array($age_mean, $seasons, 3, 0);
	$educ_years = check_array($educ_years, $seasons, 3, 0);
	$exp = check_array($exp, $seasons, 3, 0);
	$h_size = check_array($h_size, $seasons, 3, 0);
    $h_male = check_array($h_male, $seasons, 3, 0);
	$h_female = check_array($h_female, $seasons, 3, 0);
	$attend = check_array($attend, $seasons, 3, 0);
	$not_to_attend = check_array($not_to_attend, $seasons, 3, 0);
	$org = check_array($org, $seasons, 3, 0);
	$non_org = check_array($non_org, $seasons, 3, 0);
	create_header($name, $season_names, 3);
	echo "<tbody>\n";
	create_special_rows("Items", $headings, 1);
	create_special_rows("", array_formatting($n_stat,"(n = ",")"), 1);
	echo "<tr>\n<td class='header4 left' colspan=".$count_columns.">Age</td>\n</tr>\n";
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;<i>mean</i>", $age_mean, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;<i>standard deviation</i>", $age_std, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;<i>range</i>", $age_range, 0);
	echo "<tr>\n<td class='header4 left' colspan=".$count_columns.">Gender (percent of farmers)</td>\n</tr>\n";
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Male", $male_p, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Female", $female_p, 0);
	echo "<tr>\n<td class='header4 left' colspan=".$count_columns.">Civil status (percent of farmers)</td>\n</tr>\n";
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Single", $single_p, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Married", $married_p, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Widow/widower", $widow_p, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Separated", $separated_p, 0);
	echo "<tr>\n<td class='header4 left' colspan=".$count_columns.">Years of schooling</td>\n</tr>\n";
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;<i>mean</i>", $educ_years, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;<i>standard deviation</i>", $educ_std, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;<i>range</i>", $educ_range, 0);
	echo "<tr>\n<td class='header4 left' colspan=".$count_columns.">Years of farming experience</td>\n</tr>\n";
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;<i>mean</i>", $exp, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;<i>standard deviation</i>", $exp_std, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;<i>range</i>", $exp_range, 0);
	echo "<tr>\n<td class='header4 left' colspan=".$count_columns.">Household size (number of members)</td>\n</tr>\n";
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;<i>mean</i>", $h_size, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;<i>standard deviation</i>", $h_size_std, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;<i>range</i>", $h_size_range, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Male household member [<i>mean</i>]", $h_male, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Female household member [<i>mean</i>]", $h_female, 0);
	echo "<tr>\n<td class='header4 left' colspan=".$count_columns.">Major source of income (percent of farmers)</td>\n</tr>\n";
	create_soi($categories, $subcategs, $inc_source, $seasons, 0);
	//if(array_sum($inc_others) > 0){
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;Others<sup>a</sup>", $inc_others, 0);
	//}
	echo "<tr>\n<td class='header4 left' colspan=".$count_columns.">Attendance in trainings/seminars (percent of farmers)</td>\n</tr>\n";
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Attended", $attend, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Not-Attended", $not_to_attend, 0);
	echo "<tr>\n<td class='header4 left' colspan=".$count_columns.">Membership in farmer organizations (percent of farmers)</td>\n</tr>\n";
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Member", $org, 0);
	create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Non-member", $non_org, 0);
	echo "</tbody>\n";
	echo "</table>\n";
	echo "<br/>\n";
	if(count($other_source) != 0){
		echo "<div><sup>a</sup> Others include " . concatenate(array_unique($other_source)) . ".</div><br/>";
	}
	}
	for($i=0;$i<count($seasons);$i++){
		$stmt= $conn->prepare("SELECT COUNT(region) from matrix_rectype1 where season = ?");
	    $stmt->bind_param("s", $seasons[$i]);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($total);
		
		while($stmt->fetch()){ 
			echo "<div>".$footnotes[$i]." = ". number_format($total) ." respondents</div>\n";
		}
	}
$getseason->close();
$getSubCat->close();
$getCategory->close();
$getLegend->close();
$all_stmt->close();
$getprovince->close();
$stmt->close();
$sex_stmt1->close();
$status_stmt1->close();
$inc_stmt1->close();
$trng_stmt1->close();
$org_stmt1->close();
$legend_all_stmt->close();
echo displayNoteRounding();
echo displayNoteIrrigate();
?>
<br/>
<div>Data accessed at <?php echo date('l jS \of F Y h:i:s A');?></div>
<?php
echo displayNoteContact();
echo "</div>";
require_once("../includes/export.php");
?>