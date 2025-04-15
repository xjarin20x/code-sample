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
	$total_farmers = $total_n = $below_significance = $other_seed = array();
	$counter = -1;
	$c = 0;
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	
	$seedsource = $others = array();

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
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_sseed WHERE matrix_rectype1.region = matrix_sseed.region AND matrix_rectype1.province = matrix_sseed.province AND matrix_rectype1.municipality = matrix_sseed.municipality AND matrix_rectype1.barangay = matrix_sseed.barangay AND matrix_rectype1.hh_number = matrix_sseed.hh_number AND matrix_rectype1.season = matrix_sseed.season AND matrix_rectype1.season = ?) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_sseed, matrix_irrigation WHERE matrix_rectype1.region = matrix_sseed.region AND matrix_rectype1.province = matrix_sseed.province AND matrix_rectype1.municipality = matrix_sseed.municipality AND matrix_rectype1.barangay = matrix_sseed.barangay AND matrix_rectype1.hh_number = matrix_sseed.hh_number AND matrix_rectype1.season = matrix_sseed.season AND matrix_rectype1.season = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_sseed, matrix_irrigation WHERE matrix_rectype1.region = matrix_sseed.region AND matrix_rectype1.province = matrix_sseed.province AND matrix_rectype1.municipality = matrix_sseed.municipality AND matrix_rectype1.barangay = matrix_sseed.barangay AND matrix_rectype1.hh_number = matrix_sseed.hh_number AND matrix_rectype1.season = matrix_sseed.season AND matrix_rectype1.season = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime=  0) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_sseed WHERE matrix_rectype1.region = matrix_sseed.region AND matrix_rectype1.province = matrix_sseed.province AND matrix_rectype1.municipality = matrix_sseed.municipality AND matrix_rectype1.barangay = matrix_sseed.barangay AND matrix_rectype1.hh_number = matrix_sseed.hh_number AND matrix_rectype1.season = matrix_sseed.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_sseed, matrix_irrigation WHERE matrix_rectype1.region = matrix_sseed.region AND matrix_rectype1.province = matrix_sseed.province AND matrix_rectype1.municipality = matrix_sseed.municipality AND matrix_rectype1.barangay = matrix_sseed.barangay AND matrix_rectype1.hh_number = matrix_sseed.hh_number AND matrix_rectype1.season = matrix_sseed.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_sseed, matrix_irrigation WHERE matrix_rectype1.region = matrix_sseed.region AND matrix_rectype1.province = matrix_sseed.province AND matrix_rectype1.municipality = matrix_sseed.municipality AND matrix_rectype1.barangay = matrix_sseed.barangay AND matrix_rectype1.hh_number = matrix_sseed.hh_number AND matrix_rectype1.season = matrix_sseed.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0) a");
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
	
	
	$findlegend = $conn->prepare("SELECT DISTINCT season FROM legend_training ORDER BY season DESC");
	$findlegend->execute();
	$findlegend->store_result();
	$findlegend->bind_result($this);
	$season_pool = array();
	while($findlegend->fetch()){
		array_push($season_pool, $this);
	}
	$findlegend->close();
	$legend = 0;
	for($i=$season; $i > 0; $i--){
		if(in_array($i, $season_pool)) {
			$legend = $i;
			break;
		}
	}
	
	for($i=0; $i < count($total_farmers); $i++){
		array_push($below_significance, $total_farmers[$i] * 0.01);
	}

	if($province==999){
		$all_stmt= $conn->prepare("SELECT s.seedsource, IFNULL(ss.sseed_name, s.seedsource) as topic, COUNT(s.seedsource) AS frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_sseed.sseed as seedsource FROM matrix_sseed JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_sseed.region AND matrix_rectype1.province = matrix_sseed.province AND matrix_rectype1.municipality = matrix_sseed.municipality AND matrix_rectype1.barangay = matrix_sseed.barangay AND matrix_rectype1.hh_number = matrix_sseed.hh_number AND matrix_rectype1.season = matrix_sseed.season AND matrix_rectype1.season = ?) AS s LEFT JOIN (SELECT sseed_id, sseed_name FROM legend_sseed WHERE season = ?) AS ss ON ss.sseed_id = s.seedsource GROUP BY topic");
		$irri_stmt= $conn->prepare("SELECT s.seedsource, IFNULL(ss.sseed_name, s.seedsource) as topic, COUNT(s.seedsource) AS frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_sseed.sseed as seedsource FROM matrix_sseed JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_sseed.region AND matrix_rectype1.province = matrix_sseed.province AND matrix_rectype1.municipality = matrix_sseed.municipality AND matrix_rectype1.barangay = matrix_sseed.barangay AND matrix_rectype1.hh_number = matrix_sseed.hh_number AND matrix_rectype1.season = matrix_sseed.season AND matrix_rectype1.season = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1) AS s LEFT JOIN (SELECT sseed_id, sseed_name FROM legend_sseed WHERE season = ?) AS ss ON ss.sseed_id = s.seedsource GROUP BY topic");
		$rain_stmt= $conn->prepare("SELECT s.seedsource, IFNULL(ss.sseed_name, s.seedsource) as topic, COUNT(s.seedsource) AS frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_sseed.sseed as seedsource FROM matrix_sseed JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_sseed.region AND matrix_rectype1.province = matrix_sseed.province AND matrix_rectype1.municipality = matrix_sseed.municipality AND matrix_rectype1.barangay = matrix_sseed.barangay AND matrix_rectype1.hh_number = matrix_sseed.hh_number AND matrix_rectype1.season = matrix_sseed.season AND matrix_rectype1.season = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0) AS s LEFT JOIN (SELECT sseed_id, sseed_name FROM legend_sseed WHERE season = ?) AS ss ON ss.sseed_id = s.seedsource GROUP BY topic");
		$all_stmt->bind_param("ss", $season, $legend);
		$irri_stmt->bind_param("ss", $season, $legend);
		$rain_stmt->bind_param("ss", $season, $legend);
	}
	else{
		$all_stmt= $conn->prepare("SELECT s.seedsource, IFNULL(ss.sseed_name, s.seedsource) as topic, COUNT(s.seedsource) AS frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_sseed.sseed as seedsource FROM matrix_sseed JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_sseed.region AND matrix_rectype1.province = matrix_sseed.province AND matrix_rectype1.municipality = matrix_sseed.municipality AND matrix_rectype1.barangay = matrix_sseed.barangay AND matrix_rectype1.hh_number = matrix_sseed.hh_number AND matrix_rectype1.season = matrix_sseed.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) AS s LEFT JOIN (SELECT sseed_id, sseed_name FROM legend_sseed WHERE season = ?) AS ss ON ss.sseed_id = s.seedsource GROUP BY topic");
		$irri_stmt= $conn->prepare("SELECT s.seedsource, IFNULL(ss.sseed_name, s.seedsource) as topic, COUNT(s.seedsource) AS frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_sseed.sseed as seedsource FROM matrix_sseed JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_sseed.region AND matrix_rectype1.province = matrix_sseed.province AND matrix_rectype1.municipality = matrix_sseed.municipality AND matrix_rectype1.barangay = matrix_sseed.barangay AND matrix_rectype1.hh_number = matrix_sseed.hh_number AND matrix_rectype1.season = matrix_sseed.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1) AS s LEFT JOIN (SELECT sseed_id, sseed_name FROM legend_sseed WHERE season = ?) AS ss ON ss.sseed_id = s.seedsource GROUP BY topic");
		$rain_stmt= $conn->prepare("SELECT s.seedsource, IFNULL(ss.sseed_name, s.seedsource) as topic, COUNT(s.seedsource) AS frequency FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_sseed.sseed as seedsource FROM matrix_sseed JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_sseed.region AND matrix_rectype1.province = matrix_sseed.province AND matrix_rectype1.municipality = matrix_sseed.municipality AND matrix_rectype1.barangay = matrix_sseed.barangay AND matrix_rectype1.hh_number = matrix_sseed.hh_number AND matrix_rectype1.season = matrix_sseed.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0) AS s LEFT JOIN (SELECT sseed_id, sseed_name FROM legend_sseed WHERE season = ?) AS ss ON ss.sseed_id = s.seedsource GROUP BY topic");
		$all_stmt->bind_param("sss", $season, $province, $legend);
		$irri_stmt->bind_param("sss", $season, $province, $legend);
		$rain_stmt->bind_param("sss", $season, $province, $legend);
	}
		
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($id, $name, $quantity);
	
	$counter++;
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){
			if(!isset($seedsource[$name])){
				$seedsource[$name] = array();
			}
			$seedsource[$name][$counter] = $quantity;
		}
	}

	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($id, $name, $quantity);
	
	$counter++;
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){
			if(!isset($seedsource[$name])){
				$seedsource[$name] = array();
			}
			$seedsource[$name][$counter] = $quantity;
		}
	}
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($id, $name, $quantity);
	
	$counter++;
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){
			if(!isset($seedsource[$name])){
				$seedsource[$name] = array();
			}
			$seedsource[$name][$counter] = $quantity;
		}
	}
	$c++;
	
	if($province==999){
		$all_stmt= $conn->prepare("SELECT matrix_sseed.sseed from matrix_sseed WHERE matrix_sseed.season = ? AND CONCAT('', sseed * 1 ) != sseed ORDER BY sseed ASC");
		$all_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT matrix_sseed.sseed from matrix_sseed WHERE matrix_sseed.province = ? AND matrix_sseed.season = ? AND CONCAT('', sseed * 1 ) != sseed ORDER BY sseed ASC");
		$all_stmt->bind_param("ss", $province, $season);
	}
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($seed);
	
	while($all_stmt->fetch()){ 
		array_push($other_seed, $seed);
	}
	
	$all_stmt->close();
	$irri_stmt->close();
	$rain_stmt->close();
	}
	uasort($seedsource, "compareArray");
	$forprint = array();
	$region = 0;
	$getprovince=$conn->prepare("SELECT province_name, region FROM legend_province WHERE province_id = ?");
	$getprovince->bind_param("s", $province);
	$getprovince->execute();
	$getprovince->store_result();
	$getprovince->bind_result($name, $region);
	$getprovince->fetch();
	$count_columns=(count($seasons)*3)+1;
	create_header($name, $season_names, 3);
	create_headings("Seed source", $headings);
	create_special_rows("", array_formatting($total_farmers,"(n = ",")"), 1);
	echo "<tr>\n<td class='header4 left'></td>\n<td class='header4 center' colspan=".($count_columns-1).">(percent of farmers)</td>\n</tr>\n";
	foreach ($seedsource as $key => &$sec_arr){
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

	foreach ($forprint as $key => $value){
		if($forprint[$key] == "TRUE"){
			create_average($seedsource[$key], $total_farmers, ucfirst($key), 0);
		}
		else{
			for($j=0; $j < count($seedsource[$key]); $j++){
				if(!isset($others[$j])){
					$others[$j] = 0;
				}
				$others[$j] = $others[$j] + $seedsource[$key][$j];
			}
		}
	}
	if(count($others) > 0){
		create_average($others, $total_farmers, "Others*", 0);
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
			echo '<div>Excludes farmers who temporarily stopped farming during and missing responses for '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$total_farmers[$i*3]) .')</div>';
		}
	}
	if(count($other_seed) != 0){
		echo "<br/><div>* Others include " . concatenate(array_unique($other_seed)) . ".</div>";
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
echo "<div>Respondents provided multiple answers so the percentage exceeded 100.</div>";
	
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