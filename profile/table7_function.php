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
<h3>Distribution of farmers (%) by farm assets</h3>
<h3></h3>
<br />
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
	$farm_assets = array();
	$total_farmers = $total_n = array();
	$counter = -1;
	echo "<table>\n";
	
	$limit_farmers = $frm_assets = $other_assets = array();
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
	
	$season = "IN (7, 8)";
	
	if($province==999){
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation, matrix_fassets WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_fassets.region AND matrix_rectype1.province = matrix_fassets.province AND matrix_rectype1.municipality = matrix_fassets.municipality AND matrix_rectype1.barangay = matrix_fassets.barangay AND matrix_rectype1.hh_number = matrix_fassets.hh_number AND matrix_rectype1.season = matrix_fassets.season AND matrix_rectype1.season ? AND matrix_irrigation.irrigation_source IS NOT NULL) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation, matrix_fassets WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_fassets.region AND matrix_rectype1.province = matrix_fassets.province AND matrix_rectype1.municipality = matrix_fassets.municipality AND matrix_rectype1.barangay = matrix_fassets.barangay AND matrix_rectype1.hh_number = matrix_fassets.hh_number AND matrix_rectype1.season = matrix_fassets.season AND matrix_rectype1.season ? AND matrix_irrigation.irrigation_source IS NOT NULL AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5)) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation, matrix_fassets WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_fassets.region AND matrix_rectype1.province = matrix_fassets.province AND matrix_rectype1.municipality = matrix_fassets.municipality AND matrix_rectype1.barangay = matrix_fassets.barangay AND matrix_rectype1.hh_number = matrix_fassets.hh_number AND matrix_rectype1.season = matrix_fassets.season AND matrix_rectype1.season ? AND matrix_irrigation.irrigation_source IS NOT NULL AND matrix_irrigation.irrigation_source = 0) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation, matrix_fassets WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_fassets.region AND matrix_rectype1.province = matrix_fassets.province AND matrix_rectype1.municipality = matrix_fassets.municipality AND matrix_rectype1.barangay = matrix_fassets.barangay AND matrix_rectype1.hh_number = matrix_fassets.hh_number AND matrix_rectype1.season = matrix_fassets.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_source IS NOT NULL) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation, matrix_fassets WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_fassets.region AND matrix_rectype1.province = matrix_fassets.province AND matrix_rectype1.municipality = matrix_fassets.municipality AND matrix_rectype1.barangay = matrix_fassets.barangay AND matrix_rectype1.hh_number = matrix_fassets.hh_number AND matrix_rectype1.season = matrix_fassets.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_source IS NOT NULL AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5)) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_irrigation, matrix_fassets WHERE matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.region = matrix_fassets.region AND matrix_rectype1.province = matrix_fassets.province AND matrix_rectype1.municipality = matrix_fassets.municipality AND matrix_rectype1.barangay = matrix_fassets.barangay AND matrix_rectype1.hh_number = matrix_fassets.hh_number AND matrix_rectype1.season = matrix_fassets.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_source IS NOT NULL AND matrix_irrigation.irrigation_source = 0) a");
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
	
	for($i=0; $i < count($total_farmers); $i++){
		array_push($limit_farmers, $total_farmers[$i] * (0.005 * (count($seasons) * 3)));
	}

	if($province==999){
		$all_stmt= $conn->prepare("SELECT r.farm_assets, COUNT(r.farm_assets) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_fassets.farm_assets FROM matrix_rectype1 JOIN matrix_fassets WHERE matrix_rectype1.region = matrix_fassets.region AND matrix_rectype1.province = matrix_fassets.province AND matrix_rectype1.municipality = matrix_fassets.municipality AND matrix_rectype1.barangay = matrix_fassets.barangay AND matrix_rectype1.hh_number = matrix_fassets.hh_number AND matrix_rectype1.season = matrix_fassets.season AND matrix_rectype1.season = ?) r GROUP BY r.farm_assets ORDER BY COUNT(r.farm_assets) DESC");
		$irri_stmt= $conn->prepare("SELECT r.farm_assets, COUNT(r.farm_assets) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_fassets.farm_assets FROM matrix_rectype1 JOIN matrix_fassets, matrix_irrigation WHERE matrix_rectype1.region = matrix_fassets.region AND matrix_rectype1.province = matrix_fassets.province AND matrix_rectype1.municipality = matrix_fassets.municipality AND matrix_rectype1.barangay = matrix_fassets.barangay AND matrix_rectype1.hh_number = matrix_fassets.hh_number AND matrix_rectype1.season = matrix_fassets.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5)) r GROUP BY r.farm_assets ORDER BY COUNT(r.farm_assets) DESC");
		$rain_stmt= $conn->prepare("SELECT r.farm_assets, COUNT(r.farm_assets) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_fassets.farm_assets FROM matrix_rectype1 JOIN matrix_fassets, matrix_irrigation WHERE matrix_rectype1.region = matrix_fassets.region AND matrix_rectype1.province = matrix_fassets.province AND matrix_rectype1.municipality = matrix_fassets.municipality AND matrix_rectype1.barangay = matrix_fassets.barangay AND matrix_rectype1.hh_number = matrix_fassets.hh_number AND matrix_rectype1.season = matrix_fassets.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_source = 0) r GROUP BY r.farm_assets ORDER BY COUNT(r.farm_assets) DESC");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT r.farm_assets, COUNT(r.farm_assets) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_fassets.farm_assets FROM matrix_rectype1 JOIN matrix_fassets WHERE matrix_rectype1.region = matrix_fassets.region AND matrix_rectype1.province = matrix_fassets.province AND matrix_rectype1.municipality = matrix_fassets.municipality AND matrix_rectype1.barangay = matrix_fassets.barangay AND matrix_rectype1.hh_number = matrix_fassets.hh_number AND matrix_rectype1.season = matrix_fassets.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) r GROUP BY r.farm_assets ORDER BY COUNT(r.farm_assets) DESC");
		$irri_stmt= $conn->prepare("SELECT r.farm_assets, COUNT(r.farm_assets) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_fassets.farm_assets FROM matrix_rectype1 JOIN matrix_fassets, matrix_irrigation WHERE matrix_rectype1.region = matrix_fassets.region AND matrix_rectype1.province = matrix_fassets.province AND matrix_rectype1.municipality = matrix_fassets.municipality AND matrix_rectype1.barangay = matrix_fassets.barangay AND matrix_rectype1.hh_number = matrix_fassets.hh_number AND matrix_rectype1.season = matrix_fassets.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND (matrix_irrigation.irrigation_source = 1 OR matrix_irrigation.irrigation_source = 2 OR matrix_irrigation.irrigation_source = 3 OR matrix_irrigation.irrigation_source = 4 OR matrix_irrigation.irrigation_source = 5)) r GROUP BY r.farm_assets ORDER BY COUNT(r.farm_assets) DESC");
		$rain_stmt= $conn->prepare("SELECT r.farm_assets, COUNT(r.farm_assets) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_fassets.farm_assets FROM matrix_rectype1 JOIN matrix_fassets, matrix_irrigation WHERE matrix_rectype1.region = matrix_fassets.region AND matrix_rectype1.province = matrix_fassets.province AND matrix_rectype1.municipality = matrix_fassets.municipality AND matrix_rectype1.barangay = matrix_fassets.barangay AND matrix_rectype1.hh_number = matrix_fassets.hh_number AND matrix_rectype1.season = matrix_fassets.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_source = 0) r GROUP BY r.farm_assets ORDER BY COUNT(r.farm_assets) DESC");
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}
		
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($category, $quantity);
	
	$counter++;
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){
			if(!isset($frm_assets[$category])){
				$frm_assets[$category] = array();
			}
			$frm_assets[$category][$counter] = $quantity;
		}
	}
	
	$all_stmt->close();
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($category, $quantity);
	
	$counter++;
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){
			if(!isset($frm_assets[$category])){
				$frm_assets[$category] = array();
			}
			$frm_assets[$category][$counter] = $quantity;
		}
	}
	
	$irri_stmt->close();
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($category, $quantity);
	
	$counter++;
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){
			if(!isset($frm_assets[$category])){
				$frm_assets[$category] = array();
			}
			$frm_assets[$category][$counter] = $quantity;
		}
	}
	
	$rain_stmt->close();
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
	if( !empty($frm_assets) ){
	create_header($name, $season_names, 3);
	create_headings("Farm assets", $headings);
	create_special_rows("", array_formatting($total_farmers,"(n = ",")"), 1);
	foreach ($frm_assets as $key => &$sec_arr){
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
			create_average($frm_assets[$key], $total_farmers, $key, 0);
		}
		else{
			for($j=0; $j < count($frm_assets[$key]); $j++){
				if(!isset($other_assets[$j])){
					$other_assets[$j] = 0;
				}
				$other_assets[$j] = $other_assets[$j] + $frm_assets[$key][$j];
			}
		}
	}
	create_average($other_assets, $total_farmers, "<i>Other assets</i>", 0);
	echo "<tbody>\n";
	echo "</tbody>\n";
	echo "</table>\n";
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
	echo '<div>Excludes farmers who temporarily stopped farming, experienced crop failure and with inconsistent data for '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$total_farmers[$i*3]) .')</div>';
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
$getprovince->close();
$stmt->close();
echo "<br/>\n";
echo "<div>Respondents provided multiple answers so the percentage exceeded 100.</div>";
echo displayNoteIrrigate();
echo "<br/>\n";
?>
<div>* <b>Pesticide</b> - only refers to insecticides, fungicides, molluscicides, rodenticides, and other chemicals</div>
<br/>
<div>Data accessed at <?php echo date('l jS \of F Y h:i:s A');?></div>
<?php echo displayNoteContact(); ?>
</div>
<?php
require_once("../includes/export.php");
require_once("../includes/footer.php");
?>