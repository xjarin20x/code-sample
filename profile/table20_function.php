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
<h3>Reasons why farmers used the seed variety, by ecosystem and by cropping</h3>
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
	$season_names = $headings = $footnotes = $labels = array();
	$avg_distance = $avg_time = $avg_fare =  array(); 
	$total_farmers = $n_stat = $total_n = array();
	$counter = $c = 0;
	echo "<table>\n";
	
	$reasons = array();
	
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

	$findlegend = $conn->prepare("SELECT DISTINCT season FROM legend_rvariety ORDER BY season DESC");
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
	
	if($province==999){
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rvariety WHERE matrix_rectype1.region = matrix_rvariety.region AND matrix_rectype1.province = matrix_rvariety.province AND matrix_rectype1.municipality = matrix_rvariety.municipality AND matrix_rectype1.barangay = matrix_rvariety.barangay AND matrix_rectype1.hh_number = matrix_rvariety.hh_number AND matrix_rectype1.season = matrix_rvariety.season AND matrix_rectype1.season = ?) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rvariety, matrix_irrigation WHERE matrix_rectype1.region = matrix_rvariety.region AND matrix_rectype1.province = matrix_rvariety.province AND matrix_rectype1.municipality = matrix_rvariety.municipality AND matrix_rectype1.barangay = matrix_rvariety.barangay AND matrix_rectype1.hh_number = matrix_rvariety.hh_number AND matrix_rectype1.season = matrix_rvariety.season AND matrix_rectype1.season = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rvariety, matrix_irrigation WHERE matrix_rectype1.region = matrix_rvariety.region AND matrix_rectype1.province = matrix_rvariety.province AND matrix_rectype1.municipality = matrix_rvariety.municipality AND matrix_rectype1.barangay = matrix_rvariety.barangay AND matrix_rectype1.hh_number = matrix_rvariety.hh_number AND matrix_rectype1.season = matrix_rvariety.season AND matrix_rectype1.season = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rvariety WHERE matrix_rectype1.region = matrix_rvariety.region AND matrix_rectype1.province = matrix_rvariety.province AND matrix_rectype1.municipality = matrix_rvariety.municipality AND matrix_rectype1.barangay = matrix_rvariety.barangay AND matrix_rectype1.hh_number = matrix_rvariety.hh_number AND matrix_rectype1.season = matrix_rvariety.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rvariety, matrix_irrigation WHERE matrix_rectype1.region = matrix_rvariety.region AND matrix_rectype1.province = matrix_rvariety.province AND matrix_rectype1.municipality = matrix_rvariety.municipality AND matrix_rectype1.barangay = matrix_rvariety.barangay AND matrix_rectype1.hh_number = matrix_rvariety.hh_number AND matrix_rectype1.season = matrix_rvariety.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rvariety, matrix_irrigation WHERE matrix_rectype1.region = matrix_rvariety.region AND matrix_rectype1.province = matrix_rvariety.province AND matrix_rectype1.municipality = matrix_rvariety.municipality AND matrix_rectype1.barangay = matrix_rvariety.barangay AND matrix_rectype1.hh_number = matrix_rvariety.hh_number AND matrix_rectype1.season = matrix_rvariety.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0) a");
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
		$all_stmt= $conn->prepare("SELECT r.resvart, IFNULL(rv.rvariety, r.resvart), COUNT(r.resvart) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rvariety.resvart FROM matrix_rectype1 JOIN matrix_rvariety WHERE matrix_rectype1.region = matrix_rvariety.region AND matrix_rectype1.province = matrix_rvariety.province AND matrix_rectype1.municipality = matrix_rvariety.municipality AND matrix_rectype1.barangay = matrix_rvariety.barangay AND matrix_rectype1.hh_number = matrix_rvariety.hh_number AND matrix_rectype1.season = matrix_rvariety.season AND matrix_rectype1.season = ? AND matrix_rvariety.resvart IS NOT NULL) r LEFT JOIN (SELECT rvariety_id, rvariety FROM legend_rvariety WHERE season = ?) rv ON r.resvart = rv.rvariety_id GROUP BY r.resvart ORDER BY COUNT(r.resvart) DESC");
		$irri_stmt= $conn->prepare("SELECT r.resvart, IFNULL(rv.rvariety, r.resvart), COUNT(r.resvart) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rvariety.resvart FROM matrix_rectype1 JOIN matrix_rvariety, matrix_irrigation WHERE matrix_rectype1.region = matrix_rvariety.region AND matrix_rectype1.province = matrix_rvariety.province AND matrix_rectype1.municipality = matrix_rvariety.municipality AND matrix_rectype1.barangay = matrix_rvariety.barangay AND matrix_rectype1.hh_number = matrix_rvariety.hh_number AND matrix_rectype1.season = matrix_rvariety.season AND matrix_rectype1.season = ? AND matrix_rvariety.resvart IS NOT NULL AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1) r LEFT JOIN (SELECT rvariety_id, rvariety FROM legend_rvariety WHERE season = ?) rv ON r.resvart = rv.rvariety_id GROUP BY r.resvart ORDER BY COUNT(r.resvart) DESC");
		$rain_stmt= $conn->prepare("SELECT r.resvart, IFNULL(rv.rvariety, r.resvart), COUNT(r.resvart) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rvariety.resvart FROM matrix_rectype1 JOIN matrix_rvariety, matrix_irrigation WHERE matrix_rectype1.region = matrix_rvariety.region AND matrix_rectype1.province = matrix_rvariety.province AND matrix_rectype1.municipality = matrix_rvariety.municipality AND matrix_rectype1.barangay = matrix_rvariety.barangay AND matrix_rectype1.hh_number = matrix_rvariety.hh_number AND matrix_rectype1.season = matrix_rvariety.season AND matrix_rectype1.season = ? AND matrix_rvariety.resvart IS NOT NULL AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0) r LEFT JOIN (SELECT rvariety_id, rvariety FROM legend_rvariety WHERE season = ?) rv ON r.resvart = rv.rvariety_id GROUP BY r.resvart ORDER BY COUNT(r.resvart) DESC");
		$all_stmt->bind_param("ss", $season, $varlegend);
		$irri_stmt->bind_param("ss", $season, $varlegend);
		$rain_stmt->bind_param("ss", $season, $varlegend);
	}
	else{
		$all_stmt= $conn->prepare("SELECT r.resvart, IFNULL(rv.rvariety, r.resvart), COUNT(r.resvart) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rvariety.resvart FROM matrix_rectype1 JOIN matrix_rvariety WHERE matrix_rectype1.region = matrix_rvariety.region AND matrix_rectype1.province = matrix_rvariety.province AND matrix_rectype1.municipality = matrix_rvariety.municipality AND matrix_rectype1.barangay = matrix_rvariety.barangay AND matrix_rectype1.hh_number = matrix_rvariety.hh_number AND matrix_rectype1.season = matrix_rvariety.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rvariety.resvart IS NOT NULL) r LEFT JOIN (SELECT rvariety_id, rvariety FROM legend_rvariety WHERE season = ?) rv ON r.resvart = rv.rvariety_id GROUP BY r.resvart ORDER BY COUNT(r.resvart) DESC");
		$irri_stmt= $conn->prepare("SELECT r.resvart, IFNULL(rv.rvariety, r.resvart), COUNT(r.resvart) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rvariety.resvart FROM matrix_rectype1 JOIN matrix_rvariety, matrix_irrigation WHERE matrix_rectype1.region = matrix_rvariety.region AND matrix_rectype1.province = matrix_rvariety.province AND matrix_rectype1.municipality = matrix_rvariety.municipality AND matrix_rectype1.barangay = matrix_rvariety.barangay AND matrix_rectype1.hh_number = matrix_rvariety.hh_number AND matrix_rectype1.season = matrix_rvariety.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rvariety.resvart IS NOT NULL AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1) r LEFT JOIN (SELECT rvariety_id, rvariety FROM legend_rvariety WHERE season = ?) rv ON r.resvart = rv.rvariety_id GROUP BY r.resvart ORDER BY COUNT(r.resvart) DESC");
		$rain_stmt= $conn->prepare("SELECT r.resvart, IFNULL(rv.rvariety, r.resvart), COUNT(r.resvart) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rvariety.resvart FROM matrix_rectype1 JOIN matrix_rvariety, matrix_irrigation WHERE matrix_rectype1.region = matrix_rvariety.region AND matrix_rectype1.province = matrix_rvariety.province AND matrix_rectype1.municipality = matrix_rvariety.municipality AND matrix_rectype1.barangay = matrix_rvariety.barangay AND matrix_rectype1.hh_number = matrix_rvariety.hh_number AND matrix_rectype1.season = matrix_rvariety.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rvariety.resvart IS NOT NULL AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0) r LEFT JOIN (SELECT rvariety_id, rvariety FROM legend_rvariety WHERE season = ?) rv ON r.resvart = rv.rvariety_id GROUP BY r.resvart ORDER BY COUNT(r.resvart) DESC");
		$all_stmt->bind_param("sss", $season, $province, $varlegend);
		$irri_stmt->bind_param("sss", $season, $province, $varlegend);
		$rain_stmt->bind_param("sss", $season, $province, $varlegend);
	}
	
	//all
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($var_id, $var_name, $quantity);	
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$c] != 0){
				$temp =  ($quantity / $total_farmers[$c]) * 100;
			}
			if(!isset($reasons[ ucfirst($var_name) ])){
				$reasons[ ucfirst($var_name) ] = array();
			}
			$reasons[ ucfirst($var_name) ] [$counter] = $temp;
		}
	}
	$counter++; 
	$c++; 
	$all_stmt->close();
	
	//irri
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($var_id, $var_name, $quantity);	
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$c] != 0){
				$temp =  ($quantity / $total_farmers[$c]) * 100;
			}
			if(!isset($reasons[ ucfirst($var_name) ])){
				$reasons[ ucfirst($var_name) ] = array();
			}
			$reasons[ ucfirst($var_name) ] [$counter] = $temp;
		}
	}
	$counter++; 
	$c++; 
	$irri_stmt->close();
	
	//rain
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($var_id, $var_name, $quantity);	
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$c] != 0){
				$temp =  ($quantity / $total_farmers[$c]) * 100;
			}
			if(!isset($reasons[ ucfirst($var_name) ])){
				$reasons[ ucfirst($var_name) ] = array();
			}
			$reasons[ ucfirst($var_name) ] [$counter] = $temp;
		}
	}
	$counter++; 
	$c++; 
	$rain_stmt->close();
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
	create_headings("Reasons Cited", $headings);
	create_special_rows("", array_formatting($total_farmers,"n = (",")"), 1);
	
	foreach($reasons as $key => &$inner) {
		for($i=0; $i<$counter; $i++){
			if(!isset($inner[$i])){
				$inner[$i] = 0;
			}
		}
		ksort($inner);
		if(array_sum($inner) > 0.5){
			create_row($key, $inner, 0);
		}
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
	for($i=0;$i<count($seasons);$i++){
		if ($total_farmers[$i*3] < $total_n[$i]){
			echo '<div>Excludes farmers who temporarily stopped farming during '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$total_farmers[$i*3]) .')</div><br/>';
		}
	}
	}

echo "<div>Note: Respondents provided multiple response so the sum of percentages exceeds 100 percent.</div>\n<br/>";
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