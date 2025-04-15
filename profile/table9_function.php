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
	$season_names = $headings = $footnotes = array();
	$total_farmers = $total_n = $below_significance = array();
	$counter = -1;
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	
	$tenure_array = $others = array();
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
	
	if($province==999){
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5 WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 1 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 0 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5 WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime = 1 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime = 0 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
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
	
	$findlegend = $conn->prepare("SELECT DISTINCT season FROM legend_tenure ORDER BY season DESC");
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
		$all_stmt= $conn->prepare("SELECT t.tenurial_status, IFNULL(l.tenure, t.tenurial_status) as tenurial, COUNT(t.tenurial_status) as quantity FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype5.tenurial_status FROM matrix_rectype1 JOIN matrix_rectype5 WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.season = ? AND matrix_rectype5.tenurial_status IS NOT NULL) t LEFT JOIN (SELECT tenure_id, tenure FROM legend_tenure WHERE season = ?) l ON t.tenurial_status = l.tenure_id GROUP BY tenurial ORDER BY (t.tenurial_status+0) ASC");
		$irri_stmt= $conn->prepare("SELECT t.tenurial_status, IFNULL(l.tenure, t.tenurial_status) as tenurial, COUNT(t.tenurial_status) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype5.tenurial_status FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype5.tenurial_status IS NOT NULL) t LEFT JOIN (SELECT tenure_id, tenure FROM legend_tenure WHERE season = ?) l ON t.tenurial_status = l.tenure_id GROUP BY tenurial ORDER BY (t.tenurial_status+0) ASC");
		$rain_stmt= $conn->prepare("SELECT t.tenurial_status, IFNULL(l.tenure, t.tenurial_status) as tenurial, COUNT(t.tenurial_status) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype5.tenurial_status FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype5.tenurial_status IS NOT NULL) t LEFT JOIN (SELECT tenure_id, tenure FROM legend_tenure WHERE season = ?) l ON t.tenurial_status = l.tenure_id GROUP BY tenurial ORDER BY (t.tenurial_status+0) ASC");
		$all_stmt->bind_param("ss", $season, $legend);
		$irri_stmt->bind_param("ss", $season, $legend);
		$rain_stmt->bind_param("ss", $season, $legend);
	}
	else{
		$all_stmt= $conn->prepare("SELECT t.tenurial_status, IFNULL(l.tenure, t.tenurial_status) as tenurial, COUNT(t.tenurial_status) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype5.tenurial_status FROM matrix_rectype1 JOIN matrix_rectype5 WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype5.tenurial_status IS NOT NULL) t LEFT JOIN (SELECT tenure_id, tenure FROM legend_tenure WHERE season = ?) l ON t.tenurial_status = l.tenure_id GROUP BY tenurial ORDER BY (t.tenurial_status+0) ASC");
		$irri_stmt= $conn->prepare("SELECT t.tenurial_status, IFNULL(l.tenure, t.tenurial_status) as tenurial, COUNT(t.tenurial_status) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype5.tenurial_status FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime = 1 AND matrix_rectype5.tenurial_status IS NOT NULL) t LEFT JOIN (SELECT tenure_id, tenure FROM legend_tenure WHERE season = ?) l ON t.tenurial_status = l.tenure_id GROUP BY tenurial ORDER BY (t.tenurial_status+0) ASC");
		$rain_stmt= $conn->prepare("SELECT t.tenurial_status, IFNULL(l.tenure, t.tenurial_status) as tenurial, COUNT(t.tenurial_status) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype5.tenurial_status FROM matrix_rectype1 JOIN matrix_rectype5, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype5.region AND matrix_rectype1.province = matrix_rectype5.province AND matrix_rectype1.municipality = matrix_rectype5.municipality AND matrix_rectype1.barangay = matrix_rectype5.barangay AND matrix_rectype1.hh_number = matrix_rectype5.hh_number AND matrix_rectype1.season = matrix_rectype5.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime = 0 AND matrix_rectype5.tenurial_status IS NOT NULL) t LEFT JOIN (SELECT tenure_id, tenure FROM legend_tenure WHERE season = ?) l ON t.tenurial_status = l.tenure_id GROUP BY tenurial ORDER BY (t.tenurial_status+0) ASC");
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
			if(!isset($tenure_array[$name])){
				$tenure_array[$name] = array();
			}
			$tenure_array[$name][$counter] = $quantity;
		}
	}
	
	$counter++;
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($id, $name, $quantity);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){
			if(!isset($tenure_array[$name])){
				$tenure_array[$name] = array();
			}
			$tenure_array[$name][$counter] = $quantity;
		}
	}
	
	$counter++;
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($id, $name, $quantity);
	
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){
			if(!isset($tenure_array[$name])){
				$tenure_array[$name] = array();
			}
			$tenure_array[$name][$counter] = $quantity;
		}
	}
	$all_stmt->close();
	$irri_stmt->close();
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
	$count_columns=(count($seasons)*3)+1;
	if( !empty($tenure_array) ){
	create_header($name, $season_names, 3);
	echo "<tbody>\n";
	create_headings("Tenurial Status*", $headings);
	create_special_rows("", array_formatting($total_farmers,"(n = ",")"), 1);
	echo "<tr>\n<td class='header4'></td><td class='header4 center' colspan=".($count_columns-1).">(Percent of farmers)</td>\n</tr>\n";
	foreach ($tenure_array as $key => &$sec_arr){
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
	uasort($tenure_array, "compareArray");
	foreach ($tenure_array as $key => $value){
		if($forprint[$key] == "TRUE"){
			create_average($tenure_array[$key], $total_farmers, ucfirst(strtolower($key)), 0);
		}
		else{
			create_average($tenure_array[$key], $total_farmers, ucfirst(strtolower($key)), 1);
		}
	}
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
			echo '<div>Excludes missing response during '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$total_farmers[$i*3]) .')</div>';
		}
	}
	echo "<br/>\n";
	}
echo "<div>* All rice-based farm parcels reported by farmers are considered.</div>\n<br/>\n";
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
echo "<div>Respondents provided multiple answers so the percentage may exceed 100.</div>";
echo displayNoteIrrigate();
?>
<br/>
<div>Data accessed at <?php echo date('l jS \of F Y h:i:s A');?></div>
<?php echo displayNoteContact(); ?>
</div>
<?php
require_once("../includes/export.php");
?>