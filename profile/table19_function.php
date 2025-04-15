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
	$season_names = $headings = $footnotes = $labels = array();
	$avg_distance = $avg_time = $avg_fare =  array(); 
	$total_farmers = $n_stat = $total_n = array();
	$counter = $c = 0;
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	
	$variety_freq = array();
	
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
	array_push($headings, "ALL ECOSYSTEMS");
	array_push($headings, "IRRIGATED");
	array_push($headings, "NON-IRRIGATED");
	for($i=0;$i<3;$i++){
		array_push($labels, "Name of Variety");		
		array_push($labels, "(%)");
	}

	if($province==999){
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mvplanted WHERE matrix_rectype1.region = matrix_mvplanted.region AND matrix_rectype1.province = matrix_mvplanted.province AND matrix_rectype1.municipality = matrix_mvplanted.municipality AND matrix_rectype1.barangay = matrix_mvplanted.barangay AND matrix_rectype1.hh_number = matrix_mvplanted.hh_number AND matrix_rectype1.season = matrix_mvplanted.season AND matrix_rectype1.season = ? AND matrix_mvplanted.vplanted IS NOT NULL GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mvplanted, matrix_irrigation WHERE matrix_rectype1.region = matrix_mvplanted.region AND matrix_rectype1.province = matrix_mvplanted.province AND matrix_rectype1.municipality = matrix_mvplanted.municipality AND matrix_rectype1.barangay = matrix_mvplanted.barangay AND matrix_rectype1.hh_number = matrix_mvplanted.hh_number AND matrix_rectype1.season = matrix_mvplanted.season AND matrix_rectype1.season = ? AND matrix_mvplanted.vplanted IS NOT NULL AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mvplanted, matrix_irrigation WHERE matrix_rectype1.region = matrix_mvplanted.region AND matrix_rectype1.province = matrix_mvplanted.province AND matrix_rectype1.municipality = matrix_mvplanted.municipality AND matrix_rectype1.barangay = matrix_mvplanted.barangay AND matrix_rectype1.hh_number = matrix_mvplanted.hh_number AND matrix_rectype1.season = matrix_mvplanted.season AND matrix_rectype1.season = ? AND matrix_mvplanted.vplanted IS NOT NULL AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mvplanted WHERE matrix_rectype1.region = matrix_mvplanted.region AND matrix_rectype1.province = matrix_mvplanted.province AND matrix_rectype1.municipality = matrix_mvplanted.municipality AND matrix_rectype1.barangay = matrix_mvplanted.barangay AND matrix_rectype1.hh_number = matrix_mvplanted.hh_number AND matrix_rectype1.season = matrix_mvplanted.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_mvplanted.vplanted IS NOT NULL GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mvplanted, matrix_irrigation WHERE matrix_rectype1.region = matrix_mvplanted.region AND matrix_rectype1.province = matrix_mvplanted.province AND matrix_rectype1.municipality = matrix_mvplanted.municipality AND matrix_rectype1.barangay = matrix_mvplanted.barangay AND matrix_rectype1.hh_number = matrix_mvplanted.hh_number AND matrix_rectype1.season = matrix_mvplanted.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_mvplanted.vplanted IS NOT NULL AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_mvplanted, matrix_irrigation WHERE matrix_rectype1.region = matrix_mvplanted.region AND matrix_rectype1.province = matrix_mvplanted.province AND matrix_rectype1.municipality = matrix_mvplanted.municipality AND matrix_rectype1.barangay = matrix_mvplanted.barangay AND matrix_rectype1.hh_number = matrix_mvplanted.hh_number AND matrix_rectype1.season = matrix_mvplanted.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_mvplanted.vplanted IS NOT NULL AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
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
	
	$findlegend = $conn->prepare("SELECT DISTINCT season FROM legend_variety ORDER BY season DESC");
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
		$all_stmt= $conn->prepare("SELECT r.vplanted, IFNULL(v.variety_name, r.vplanted) as varname, COUNT(r.vplanted) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mvplanted.vplanted FROM matrix_rectype1 JOIN matrix_mvplanted WHERE matrix_rectype1.region = matrix_mvplanted.region AND matrix_rectype1.province = matrix_mvplanted.province AND matrix_rectype1.municipality = matrix_mvplanted.municipality AND matrix_rectype1.barangay = matrix_mvplanted.barangay AND matrix_rectype1.hh_number = matrix_mvplanted.hh_number AND matrix_rectype1.season = matrix_mvplanted.season AND matrix_rectype1.season = ?) r LEFT JOIN (SELECT variety_id, variety_name FROM legend_variety WHERE season = ?) v ON r.vplanted = v.variety_id GROUP BY varname ORDER BY COUNT(r.vplanted) DESC LIMIT 0,10");
		$irri_stmt= $conn->prepare("SELECT r.vplanted, IFNULL(v.variety_name, r.vplanted) as varname, COUNT(r.vplanted) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mvplanted.vplanted FROM matrix_rectype1 JOIN matrix_mvplanted, matrix_irrigation WHERE matrix_rectype1.region = matrix_mvplanted.region AND matrix_rectype1.province = matrix_mvplanted.province AND matrix_rectype1.municipality = matrix_mvplanted.municipality AND matrix_rectype1.barangay = matrix_mvplanted.barangay AND matrix_rectype1.hh_number = matrix_mvplanted.hh_number AND matrix_rectype1.season = matrix_mvplanted.season AND matrix_rectype1.season = ?  AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1) r LEFT JOIN (SELECT variety_id, variety_name FROM legend_variety WHERE season = ?) v ON r.vplanted = v.variety_id GROUP BY varname ORDER BY COUNT(r.vplanted) DESC LIMIT 0,10");
		$rain_stmt= $conn->prepare("SELECT r.vplanted, IFNULL(v.variety_name, r.vplanted) as varname, COUNT(r.vplanted) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mvplanted.vplanted FROM matrix_rectype1 JOIN matrix_mvplanted, matrix_irrigation WHERE matrix_rectype1.region = matrix_mvplanted.region AND matrix_rectype1.province = matrix_mvplanted.province AND matrix_rectype1.municipality = matrix_mvplanted.municipality AND matrix_rectype1.barangay = matrix_mvplanted.barangay AND matrix_rectype1.hh_number = matrix_mvplanted.hh_number AND matrix_rectype1.season = matrix_mvplanted.season AND matrix_rectype1.season = ?  AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0) r LEFT JOIN (SELECT variety_id, variety_name FROM legend_variety WHERE season = ?) v ON r.vplanted = v.variety_id GROUP BY varname ORDER BY COUNT(r.vplanted) DESC LIMIT 0,10");
		$all_stmt->bind_param("ss", $season, $varlegend);
		$irri_stmt->bind_param("ss", $season, $varlegend);
		$rain_stmt->bind_param("ss", $season, $varlegend);
	}
	else{
		$all_stmt= $conn->prepare("SELECT r.vplanted, IFNULL(v.variety_name, r.vplanted) as varname, COUNT(r.vplanted) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mvplanted.vplanted FROM matrix_rectype1 JOIN matrix_mvplanted WHERE matrix_rectype1.region = matrix_mvplanted.region AND matrix_rectype1.province = matrix_mvplanted.province AND matrix_rectype1.municipality = matrix_mvplanted.municipality AND matrix_rectype1.barangay = matrix_mvplanted.barangay AND matrix_rectype1.hh_number = matrix_mvplanted.hh_number AND matrix_rectype1.season = matrix_mvplanted.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) r LEFT JOIN (SELECT variety_id, variety_name FROM legend_variety WHERE season = ?) v ON r.vplanted = v.variety_id GROUP BY varname ORDER BY COUNT(r.vplanted) DESC LIMIT 0,10");
		$irri_stmt= $conn->prepare("SELECT r.vplanted, IFNULL(v.variety_name, r.vplanted) as varname, COUNT(r.vplanted) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mvplanted.vplanted FROM matrix_rectype1 JOIN matrix_mvplanted, matrix_irrigation WHERE matrix_rectype1.region = matrix_mvplanted.region AND matrix_rectype1.province = matrix_mvplanted.province AND matrix_rectype1.municipality = matrix_mvplanted.municipality AND matrix_rectype1.barangay = matrix_mvplanted.barangay AND matrix_rectype1.hh_number = matrix_mvplanted.hh_number AND matrix_rectype1.season = matrix_mvplanted.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?  AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1) r LEFT JOIN (SELECT variety_id, variety_name FROM legend_variety WHERE season = ?) v ON r.vplanted = v.variety_id GROUP BY varname ORDER BY COUNT(r.vplanted) DESC LIMIT 0,10");
		$rain_stmt= $conn->prepare("SELECT r.vplanted, IFNULL(v.variety_name, r.vplanted) as varname, COUNT(r.vplanted) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_mvplanted.vplanted FROM matrix_rectype1 JOIN matrix_mvplanted, matrix_irrigation WHERE matrix_rectype1.region = matrix_mvplanted.region AND matrix_rectype1.province = matrix_mvplanted.province AND matrix_rectype1.municipality = matrix_mvplanted.municipality AND matrix_rectype1.barangay = matrix_mvplanted.barangay AND matrix_rectype1.hh_number = matrix_mvplanted.hh_number AND matrix_rectype1.season = matrix_mvplanted.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?  AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0) r LEFT JOIN (SELECT variety_id, variety_name FROM legend_variety WHERE season = ?) v ON r.vplanted = v.variety_id GROUP BY varname ORDER BY COUNT(r.vplanted) DESC LIMIT 0,10");
		$all_stmt->bind_param("sss", $season, $province, $varlegend);
		$irri_stmt->bind_param("sss", $season, $province, $varlegend);
		$rain_stmt->bind_param("sss", $season, $province, $varlegend);
	}
	
	//all
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($var_id, $var_name, $quantity);
			
	if($all_stmt->num_rows != 0){
		$i = 0;
		while($all_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$c] != 0){
				$temp =  ($quantity / $total_farmers[$c]) * 100;
			}
			if(!isset($variety_freq[$i])){
				$variety_freq[$i] = array();
			}
			$variety_freq[$i][$counter] = $var_name;
			$variety_freq[$i][$counter+1] = $temp;
			$i++;
		}
	}
	$counter = $counter + 2;
	$c++;
	$all_stmt->close();
	
	//irri
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($var_id, $var_name, $quantity);
			
	if($irri_stmt->num_rows != 0){
		$i = 0;
		while($irri_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$c] != 0){
				$temp =  ($quantity / $total_farmers[$c]) * 100;
			}
			if(!isset($variety_freq[$i])){
				$variety_freq[$i] = array();
			}
			$variety_freq[$i][$counter] = $var_name;
			$variety_freq[$i][$counter+1] = $temp;
			$i++;
		}
	}
	$counter = $counter + 2;
	$c++;
	$irri_stmt->close();
	
	//rain
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($var_id, $var_name, $quantity);
			
	if($rain_stmt->num_rows != 0){
		$i = 0;
		while($rain_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$c] != 0){
				$temp =  ($quantity / $total_farmers[$c]) * 100;
			}
			if(!isset($variety_freq[$i])){
				$variety_freq[$i] = array();
			}
			$variety_freq[$i][$counter] = $var_name;
			$variety_freq[$i][$counter+1] = $temp;
			$i++;
		}
	}
	$counter = $counter + 2;
	$c++;
	$rain_stmt->close();
	
	}
 
	$n_stat= array_formatting($total_farmers,"(n = ",")");
	$region = 0;			
	$getprovince=$conn->prepare("SELECT province_name, region FROM legend_province WHERE province_id = ?");
	$getprovince->bind_param("s", $province);
	$getprovince->execute();
	$getprovince->store_result();
	$getprovince->bind_result($name, $region);
	$getprovince->fetch();
	$count_columns=(count($seasons)*6)+1;
	create_span($name, count($seasons)*6);
	create_special_header(array_shift($season_names), $season_names, 6);
	create_spanned_headings(array_shift($headings), $headings, 2);
	create_spanned_headings(array_shift($n_stat), $n_stat, 2);
	create_row_bold(array_shift($labels), $labels, 0);
	for($i=0; $i<count($variety_freq); $i++){
		if(!isset($variety_freq[$i])){
			$variety_freq[$i] = array();
		}
		create_mixed($variety_freq[$i], 2, $counter);
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
			echo '<div>Excludes farmers who temporarily stopped farming during '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$total_farmers[$i*3]) .')</div>';
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