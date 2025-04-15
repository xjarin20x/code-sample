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
	$avg_distance = $avg_time = $avg_fare =  array(); 
	$total_n = $below_significance = $n_farmers = array();
	$counter = 0;
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	
	$cropping = array(); 
	$count = $conn->prepare("SELECT cpattern_id FROM legend_cpattern");
	$count->execute();
	$count->store_result();
	$count->bind_result($ids);
	while($count->fetch()){
		$cropping[$ids-1] = array();
	}
	$j = 0;
	foreach($seasons as $season){
		
	$findlegend = $conn->prepare("SELECT DISTINCT season FROM legend_cpattern ORDER BY season DESC");
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
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 LEFT JOIN matrix_cpattern ON matrix_rectype1.region = matrix_cpattern.region AND matrix_rectype1.province = matrix_cpattern.province AND matrix_rectype1.municipality = matrix_cpattern.municipality AND matrix_rectype1.barangay = matrix_cpattern.barangay AND matrix_rectype1.hh_number = matrix_cpattern.hh_number AND matrix_rectype1.season = matrix_cpattern.season WHERE matrix_rectype1.season = ? AND matrix_cpattern.cpattern IS NOT NULL GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_cpattern, matrix_irrigation WHERE matrix_rectype1.region = matrix_cpattern.region AND matrix_rectype1.province = matrix_cpattern.province AND matrix_rectype1.municipality = matrix_cpattern.municipality AND matrix_rectype1.barangay = matrix_cpattern.barangay AND matrix_rectype1.hh_number = matrix_cpattern.hh_number AND matrix_rectype1.season = matrix_cpattern.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_cpattern.cpattern IS NOT NULL AND matrix_irrigation.irrigation_prime = 1 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_cpattern, matrix_irrigation WHERE matrix_rectype1.region = matrix_cpattern.region AND matrix_rectype1.province = matrix_cpattern.province AND matrix_rectype1.municipality = matrix_cpattern.municipality AND matrix_rectype1.barangay = matrix_cpattern.barangay AND matrix_rectype1.hh_number = matrix_cpattern.hh_number AND matrix_rectype1.season = matrix_cpattern.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_cpattern.cpattern IS NOT NULL AND matrix_irrigation.irrigation_prime = 0 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 LEFT JOIN matrix_cpattern ON matrix_rectype1.region = matrix_cpattern.region AND matrix_rectype1.province = matrix_cpattern.province AND matrix_rectype1.municipality = matrix_cpattern.municipality AND matrix_rectype1.barangay = matrix_cpattern.barangay AND matrix_rectype1.hh_number = matrix_cpattern.hh_number AND matrix_rectype1.season = matrix_cpattern.season WHERE matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_cpattern.cpattern IS NOT NULL GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$irri_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_cpattern, matrix_irrigation WHERE matrix_rectype1.region = matrix_cpattern.region AND matrix_rectype1.province = matrix_cpattern.province AND matrix_rectype1.municipality = matrix_cpattern.municipality AND matrix_rectype1.barangay = matrix_cpattern.barangay AND matrix_rectype1.hh_number = matrix_cpattern.hh_number AND matrix_rectype1.season = matrix_cpattern.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_cpattern.cpattern IS NOT NULL AND matrix_irrigation.irrigation_prime = 1 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$rain_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_cpattern, matrix_irrigation WHERE matrix_rectype1.region = matrix_cpattern.region AND matrix_rectype1.province = matrix_cpattern.province AND matrix_rectype1.municipality = matrix_cpattern.municipality AND matrix_rectype1.barangay = matrix_cpattern.barangay AND matrix_rectype1.hh_number = matrix_cpattern.hh_number AND matrix_rectype1.season = matrix_cpattern.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_cpattern.cpattern IS NOT NULL AND matrix_irrigation.irrigation_prime = 0 GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$all_stmt->bind_param("ss", $season, $province);
		$irri_stmt->bind_param("ss", $season, $province);
		$rain_stmt->bind_param("ss", $season, $province);
	}

	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($total);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			array_push($n_farmers, $total);
		}
	}
	else{
		array_push($n_farmers, 0);
	}
	
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($total);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			array_push($n_farmers, $total);
		}
	}
	else{
		array_push($n_farmers, 0);
	}
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($total);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			array_push($n_farmers, $total);
		}
	}
	else{
		array_push($n_farmers, 0);
	}
	
	if($province==999){
		$all_stmt= $conn->prepare("SELECT c.cpattern_id, count(r.crop) AS frequency FROM (SELECT cpattern_id FROM legend_cpattern WHERE season = ? OR season IS NULL) AS c LEFT JOIN (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_cpattern.cpattern as crop FROM matrix_cpattern JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_cpattern.region AND matrix_rectype1.province = matrix_cpattern.province AND matrix_rectype1.municipality = matrix_cpattern.municipality AND matrix_rectype1.barangay = matrix_cpattern.barangay AND matrix_rectype1.hh_number = matrix_cpattern.hh_number AND matrix_rectype1.season = matrix_cpattern.season AND matrix_rectype1.season = ? AND matrix_cpattern.cpattern IS NOT NULL) AS r ON c.cpattern_id = r.crop GROUP BY c.cpattern_id");
		$irri_stmt= $conn->prepare("SELECT c.cpattern_id, count(r.crop) AS frequency FROM (SELECT cpattern_id FROM legend_cpattern WHERE season = ? OR season IS NULL) AS c LEFT JOIN (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_cpattern.cpattern as crop FROM matrix_cpattern JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_cpattern.region AND matrix_rectype1.province = matrix_cpattern.province AND matrix_rectype1.municipality = matrix_cpattern.municipality AND matrix_rectype1.barangay = matrix_cpattern.barangay AND matrix_rectype1.hh_number = matrix_cpattern.hh_number AND matrix_rectype1.season = matrix_cpattern.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 1 AND matrix_cpattern.cpattern IS NOT NULL) AS r ON c.cpattern_id = r.crop GROUP BY c.cpattern_id");
		$rain_stmt= $conn->prepare("SELECT c.cpattern_id, count(r.crop) AS frequency FROM (SELECT cpattern_id FROM legend_cpattern WHERE season = ? OR season IS NULL) AS c LEFT JOIN (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_cpattern.cpattern as crop FROM matrix_cpattern JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_cpattern.region AND matrix_rectype1.province = matrix_cpattern.province AND matrix_rectype1.municipality = matrix_cpattern.municipality AND matrix_rectype1.barangay = matrix_cpattern.barangay AND matrix_rectype1.hh_number = matrix_cpattern.hh_number AND matrix_rectype1.season = matrix_cpattern.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_irrigation.irrigation_prime = 0 AND matrix_cpattern.cpattern IS NOT NULL) AS r ON c.cpattern_id = r.crop GROUP BY c.cpattern_id");
		$all_stmt->bind_param("ss", $legend, $season);
		$irri_stmt->bind_param("ss", $legend, $season);
		$rain_stmt->bind_param("ss", $legend, $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT c.cpattern_id, count(r.crop) AS frequency FROM (SELECT cpattern_id FROM legend_cpattern WHERE season = ? OR season IS NULL) AS c LEFT JOIN (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_cpattern.cpattern as crop FROM matrix_cpattern JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_cpattern.region AND matrix_rectype1.province = matrix_cpattern.province AND matrix_rectype1.municipality = matrix_cpattern.municipality AND matrix_rectype1.barangay = matrix_cpattern.barangay AND matrix_rectype1.hh_number = matrix_cpattern.hh_number AND matrix_rectype1.season = matrix_cpattern.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_cpattern.cpattern IS NOT NULL) AS r ON c.cpattern_id = r.crop GROUP BY c.cpattern_id");
		$irri_stmt= $conn->prepare("SELECT c.cpattern_id, count(r.crop) AS frequency FROM (SELECT cpattern_id FROM legend_cpattern WHERE season = ? OR season IS NULL) AS c LEFT JOIN (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_cpattern.cpattern as crop FROM matrix_cpattern JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_cpattern.region AND matrix_rectype1.province = matrix_cpattern.province AND matrix_rectype1.municipality = matrix_cpattern.municipality AND matrix_rectype1.barangay = matrix_cpattern.barangay AND matrix_rectype1.hh_number = matrix_cpattern.hh_number AND matrix_rectype1.season = matrix_cpattern.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime = 1 AND matrix_cpattern.cpattern IS NOT NULL) AS r ON c.cpattern_id = r.crop GROUP BY c.cpattern_id");
		$rain_stmt= $conn->prepare("SELECT c.cpattern_id, count(r.crop) AS frequency FROM (SELECT cpattern_id FROM legend_cpattern WHERE season = ? OR season IS NULL) AS c LEFT JOIN (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_cpattern.cpattern as crop FROM matrix_cpattern JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_cpattern.region AND matrix_rectype1.province = matrix_cpattern.province AND matrix_rectype1.municipality = matrix_cpattern.municipality AND matrix_rectype1.barangay = matrix_cpattern.barangay AND matrix_rectype1.hh_number = matrix_cpattern.hh_number AND matrix_rectype1.season = matrix_cpattern.season AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_irrigation.irrigation_prime = 0 AND matrix_cpattern.cpattern IS NOT NULL) AS r ON c.cpattern_id = r.crop GROUP BY c.cpattern_id");
		$all_stmt->bind_param("sss", $legend, $season, $province);
		$irri_stmt->bind_param("sss", $legend, $season, $province);
		$rain_stmt->bind_param("sss", $legend, $season, $province);
	}
		
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($category, $quantity);
	
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			$temp = 0;
			if($n_farmers[$counter] != 0){
				$temp =  ($quantity / $n_farmers[$counter]) * 100;
			}
			array_push($cropping[$category-1], $temp);
		}
	}
	$all_stmt->close();
	$counter++;
	foreach ($cropping as &$sec_arr){
		if(count($sec_arr) < $counter){
			array_push($sec_arr, 0);
		}
	}
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($category, $quantity);
	
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			$temp = 0;
			if($n_farmers[$counter] != 0){
				$temp =  ($quantity / $n_farmers[$counter]) * 100;
			}
			array_push($cropping[$category-1], $temp);
		}
	}
	$irri_stmt->close();
	$counter++;
	foreach ($cropping as &$sec_arr){
		if(count($sec_arr) < $counter){
			array_push($sec_arr, 0);
		}
	}
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($category, $quantity);
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			$temp = 0;
			if($n_farmers[$counter] != 0){
				$temp =  ($quantity / $n_farmers[$counter]) * 100;
			}
			array_push($cropping[$category-1], $temp);
		}
	}
	$rain_stmt->close();
	$counter++;
	foreach ($cropping as &$sec_arr){
		if(count($sec_arr) < $counter){
			array_push($sec_arr, 0);
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
	$count_columns=(count($seasons)*3)+1;
	create_header($name, $season_names, 3);
	create_headings("Cropping Pattern", $headings);
	create_special_rows("", array_formatting($n_farmers,"(n = ",")"), 1);
	echo "<tr>\n<td class='header4 left'></td>\n<td class='header4 center' colspan=".($count_columns-1).">(percent of farmers)</td>\n</tr>\n";
	$print=$conn->prepare("SELECT cpattern_id, pattern FROM legend_cpattern WHERE season = ? OR season IS NULL");
	$print->bind_param("s", $legend);
	$print->execute();
	$print->store_result();
	$print->bind_result($index, $name);
	while($print->fetch()){
		if(array_sum($cropping[$index-1]) > 1){
			create_row($name, $cropping[$index-1], 0);
		}
		elseif(array_sum($cropping[$index-1]) < 1 AND array_sum($cropping[$index-1]) > 0){
			create_row($name, $cropping[$index-1], 2);
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
		if ($n_farmers[$i*3] < $total_n[$i]){
			echo '<div>Excludes missing response during '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$n_farmers[$i*3]) .')</div>';
		}
	}
	echo "<br/>\n";
	}

echo "<div>All rice-based parcels operated by farmers were considered.</div>";
echo "<div>Respondents provided multiple answers so the total percentage exceeded 100.</div>\n<br/>";
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
echo "<br/>" . displayNoteIrrigate();
?>
<br/>
<div>Data accessed at <?php echo date('l jS \of F Y h:i:s A');?></div>
<?php echo displayNoteContact(); ?>
</div>
<?php
require_once("../includes/export.php");
?>