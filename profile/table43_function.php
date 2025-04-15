<?php
	require_once("../includes/header.php");
	$prepath = checksubdomain(dirname(__FILE__), $conn, $purifier);
	$file = strstr_alt(basename(__FILE__, ".php"), '_', true);
	if(empty($_POST)){
		header('Location:retrieve.php?table='. str_replace('table', '', $file) .'');
		exit; 
	}
?>	
<div id="table">
<h2>SOCIOECONOMICS AND TECHNOLOGY PROFILE</h2>
<div id="tableData">
<h3>Distribution of farmers by source of information</h3>
<br />
<?php
	$provinces = disinfect_var($_POST['provinces']);
	$seasons = disinfect_var($_POST['seasons']);

	$content=count($provinces);
	
	$stmt = "";
	$total = 0;
	$region = 0;	
	
	foreach($provinces as $province){
	$season_names = $headings = $footnotes = array();
	$total_farmers = $n_stat = array();
	$counter = -1;
	echo "<table>\n";
	
	$contain_array = $others_arr = $others = $limit_farmers = array();
	foreach($seasons as $season){
	$n_farmers = array();
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
	
	if($province==999){
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_sinfo WHERE matrix_rectype1.region = matrix_sinfo.region AND matrix_rectype1.province = matrix_sinfo.province AND matrix_rectype1.municipality = matrix_sinfo.municipality AND matrix_rectype1.barangay = matrix_sinfo.barangay AND matrix_rectype1.hh_number = matrix_sinfo.hh_number AND matrix_rectype1.season = matrix_sinfo.season AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$all_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_sinfo WHERE matrix_rectype1.region = matrix_sinfo.region AND matrix_rectype1.province = matrix_sinfo.province AND matrix_rectype1.municipality = matrix_sinfo.municipality AND matrix_rectype1.barangay = matrix_sinfo.barangay AND matrix_rectype1.hh_number = matrix_sinfo.hh_number AND matrix_rectype1.season = matrix_sinfo.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$all_stmt->bind_param("ss", $season, $province);
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
	
	for($i=0; $i < count($total_farmers); $i++){
		array_push($limit_farmers, $total_farmers[$i] * 0.01);
	}
	
	$n_stat = $total_farmers;
	
	if($province==999){
		$all_stmt= $conn->prepare("SELECT si.sinfo, COUNT(si.sinfo) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_sinfo.sinfo FROM matrix_rectype1 JOIN matrix_sinfo WHERE matrix_rectype1.region = matrix_sinfo.region AND matrix_rectype1.province = matrix_sinfo.province AND matrix_rectype1.municipality = matrix_sinfo.municipality AND matrix_rectype1.barangay = matrix_sinfo.barangay AND matrix_rectype1.hh_number = matrix_sinfo.hh_number AND matrix_rectype1.season = matrix_sinfo.season AND matrix_rectype1.season = ? AND matrix_sinfo.sinfo IS NOT NULL) si GROUP BY si.sinfo ORDER BY COUNT(si.sinfo) DESC");
		$all_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT si.sinfo, COUNT(si.sinfo) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_sinfo.sinfo FROM matrix_rectype1 JOIN matrix_sinfo WHERE matrix_rectype1.region = matrix_sinfo.region AND matrix_rectype1.province = matrix_sinfo.province AND matrix_rectype1.municipality = matrix_sinfo.municipality AND matrix_rectype1.barangay = matrix_sinfo.barangay AND matrix_rectype1.hh_number = matrix_sinfo.hh_number AND matrix_rectype1.season = matrix_sinfo.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_sinfo.sinfo IS NOT NULL) si GROUP BY si.sinfo ORDER BY COUNT(si.sinfo) DESC");
		$all_stmt->bind_param("ss", $season, $province);
	}
		
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($category, $quantity);
	
	$counter++;
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){
			if(!isset($contain_array[$category])){
				$contain_array[$category] = array();
			}
			$contain_array[$category][$counter] = $quantity;
		}
	}
	
	$all_stmt->close();
	}
	$region = 0;		
	$forprint = $names = array();	
	$getprovince=$conn->prepare("SELECT province_name, region FROM legend_province WHERE province_id = ?");
	$getprovince->bind_param("s", $province);
	$getprovince->execute();
	$getprovince->store_result();
	$getprovince->bind_result($name, $region);
	$getprovince->fetch();
	$count_columns=(count($seasons)*2)+1;
	if( !empty($contain_array) ){
	create_header($name, $season_names, 1);
	echo "<tbody>\n";
	create_headings("Source of Information", $headings);
	create_special_rows("", array_formatting($n_stat,"n = (",")"), 1);
	foreach ($contain_array as $key => &$sec_arr){
		$mark = FALSE;
		for($i = 0; $i < $counter+1; $i++){
			if(!isset($sec_arr[$i])){
				$sec_arr[$i] = 0;
			}
			if($sec_arr[$i] > $limit_farmers[$i]){
				$mark = TRUE;
			}
		}
		$forprint[$key]=$mark;
		ksort($sec_arr);
	}
	
	foreach ($forprint as $key => $value){
		if($forprint[$key] == "TRUE"){
			create_average($contain_array[$key], $total_farmers, strtoupper($key), 0);
		}
		else{
			for($j=0; $j < count($contain_array[$key]); $j++){
				if(!isset($others_arr[$j])){
					$others_arr[$j] = 0;
				}
				$others_arr[$j] = $others_arr[$j] + $contain_array[$key][$j];
			}
		}
	}
	create_average($others_arr, $total_farmers, "OTHERS", 0);
	echo "</tbody>\n";
	echo "</table>\n";
	echo "<br/>\n";
	}
	else{
	echo "</table>\n";
		 if(count($season_names)==1) {
			$seas = $season_names[0];
		}
		else {
			$seas = concatenate($season_names);
		}
		echo "<div class='bold'>Parameters for your requested query are not applicable for ". $name ." during the ". $seas .".</div><br/>";
	}
	}

echo "<div>Percentages may not total 100% due to rounding.</div>";
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
require_once("../includes/footer.php");
?>