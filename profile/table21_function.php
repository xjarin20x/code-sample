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
	$total_n = $total_farmers = $n_stat = array();
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
	array_push($headings, "ALL ECOSYSTEMS");	
	array_push($headings, "IRRIGATED");	
	array_push($headings, "NON-IRRIGATED");	
        
    $findlegend = $conn->prepare("SELECT DISTINCT season FROM legend_sclass ORDER BY season DESC");
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
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6 WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype6.sclass IS NOT NULL) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype6.sclass IS NOT NULL AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime=1) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype6.sclass IS NOT NULL AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0) a");
		$all_stmt->bind_param("s", $season);
		$irri_stmt->bind_param("s", $season);
		$rain_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6 WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype6.sclass IS NOT NULL) a");
		$irri_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype6.sclass IS NOT NULL AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime=1) a");
		$rain_stmt= $conn->prepare("SELECT COUNT(*) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_rectype6, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype6.sclass IS NOT NULL AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0) a");
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
		$all_stmt= $conn->prepare("SELECT c.category, c.seedclass, count(r.seedclass) AS frequency FROM (SELECT seedclass_id as id, seedclass_category AS category, seedclass FROM legend_sclass WHERE season = ? OR season IS NULL ORDER BY seedclass_id ASC) AS c LEFT JOIN (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype6.sclass as seedclass FROM matrix_rectype6 JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ?) AS r ON c.category = r.seedclass GROUP BY c.category");
		$irri_stmt= $conn->prepare("SELECT c.category, c.seedclass, count(r.seedclass) AS frequency FROM (SELECT seedclass_id as id, seedclass_category AS category, seedclass FROM legend_sclass WHERE season = ? OR season IS NULL ORDER BY seedclass_id ASC) AS c LEFT JOIN (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype6.sclass as seedclass FROM matrix_rectype6 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1) AS r ON c.category = r.seedclass GROUP BY c.category");
		$rain_stmt= $conn->prepare("SELECT c.category, c.seedclass, count(r.seedclass) AS frequency FROM (SELECT seedclass_id as id, seedclass_category AS category, seedclass FROM legend_sclass WHERE season = ? OR season IS NULL ORDER BY seedclass_id ASC) AS c LEFT JOIN (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype6.sclass as seedclass FROM matrix_rectype6 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0) AS r ON c.category = r.seedclass GROUP BY c.category");
		$all_stmt->bind_param("ss", $varlegend, $season);
		$irri_stmt->bind_param("ss", $varlegend, $season);
		$rain_stmt->bind_param("ss", $varlegend, $season);
	}
	else{
		$all_stmt= $conn->prepare("SELECT c.category, c.seedclass, count(r.seedclass) AS frequency FROM (SELECT seedclass_id as id, seedclass_category AS category, seedclass FROM legend_sclass WHERE season = ? OR season IS NULL ORDER BY seedclass_id ASC) AS c LEFT JOIN (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype6.sclass as seedclass FROM matrix_rectype6 JOIN matrix_rectype1 WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ?) AS r ON c.category = r.seedclass GROUP BY c.category");
		$irri_stmt= $conn->prepare("SELECT c.category, c.seedclass, count(r.seedclass) AS frequency FROM (SELECT seedclass_id as id, seedclass_category AS category, seedclass FROM legend_sclass WHERE season = ? OR season IS NULL ORDER BY seedclass_id ASC) AS c LEFT JOIN (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype6.sclass as seedclass FROM matrix_rectype6 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND  matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 1) AS r ON c.category = r.seedclass GROUP BY c.category");
		$rain_stmt= $conn->prepare("SELECT c.category, c.seedclass, count(r.seedclass) AS frequency FROM (SELECT seedclass_id as id, seedclass_category AS category, seedclass FROM legend_sclass WHERE season = ? OR season IS NULL ORDER BY seedclass_id ASC) AS c LEFT JOIN (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_rectype6.sclass as seedclass FROM matrix_rectype6 JOIN matrix_rectype1, matrix_irrigation WHERE matrix_rectype1.region = matrix_rectype6.region AND matrix_rectype1.province = matrix_rectype6.province AND matrix_rectype1.municipality = matrix_rectype6.municipality AND matrix_rectype1.barangay = matrix_rectype6.barangay AND matrix_rectype1.hh_number = matrix_rectype6.hh_number AND matrix_rectype1.season = matrix_rectype6.season AND  matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_rectype1.region = matrix_irrigation.region AND matrix_rectype1.province = matrix_irrigation.province AND matrix_rectype1.municipality = matrix_irrigation.municipality AND matrix_rectype1.barangay = matrix_irrigation.barangay AND matrix_rectype1.hh_number = matrix_irrigation.hh_number AND matrix_rectype1.season = matrix_irrigation.season AND matrix_irrigation.irrigation_prime= 0) AS r ON c.category = r.seedclass GROUP BY c.category");
		$all_stmt->bind_param("sss", $varlegend, $season, $province);
		$irri_stmt->bind_param("sss", $varlegend, $season, $province);
		$rain_stmt->bind_param("sss", $varlegend, $season, $province);
	}
	
	//all
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($category, $category_name, $quantity);
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$counter] != 0){
				$temp =  ($quantity / $total_farmers[$counter]) * 100;
			}
            if(!isset($matrix_sclass[$category_name])){
				$matrix_sclass[$category_name] = array();
			}
            $matrix_sclass[$category_name][$counter] = $temp;
		}
	}
        
	$counter++;
        
	$all_stmt->close();	
	
	//irri
	$irri_stmt->execute();
	$irri_stmt->store_result();
	$irri_stmt->bind_result($category, $category_name, $quantity);
			
	if($irri_stmt->num_rows != 0){
		while($irri_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$counter] != 0){
				$temp =  ($quantity / $total_farmers[$counter]) * 100;
			}
            if(!isset($matrix_sclass[$category_name])){
				$matrix_sclass[$category_name] = array();
			}
            $matrix_sclass[$category_name][$counter] = $temp;
		}
	}

	$counter++;
	$irri_stmt->close();	
	
	$rain_stmt->execute();
	$rain_stmt->store_result();
	$rain_stmt->bind_result($category, $category_name, $quantity);
			
	if($rain_stmt->num_rows != 0){
		while($rain_stmt->fetch()){ 
			$temp = 0;
			if($total_farmers[$counter] != 0){
				$temp =  ($quantity / $total_farmers[$counter]) * 100;
			}
            if(!isset($matrix_sclass[$category_name])){
				$matrix_sclass[$category_name] = array();
			}
            $matrix_sclass[$category_name][$counter] = $temp;
		}
	}
        
	$counter++;
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
	create_headings("Seed Classification", $headings);
	create_special_rows("", array_formatting($total_farmers,"(n = ",")"), 1);
	echo "<tr>\n<td class='header4 left'></td>\n<td class='header4 center' colspan=".($count_columns-1).">(percent of farmers)</td>\n</tr>\n";
    foreach ($matrix_sclass as $key => $value){
        for($i = 0; $i <count($seasons)*3 ; $i++){
            if(!isset($value[$i])){
                $value[$i] = "-";
            }
        }
        create_row($key, $value, 0);
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
    echo "<div>Good seeds defined as seeds produced from the planting of registered and certified seeds.</div>\n";
    echo "<div>Note: Different source of categories were used during the 2016-2017 RBFHS survey round.";
	for($i=0;$i<count($seasons);$i++){
		if ($total_farmers[$i*3] < $total_n[$i]){
			echo '<div>Excludes farmers who temporarily stopped farming during '. $footnotes[$i] .' harvest (n='. ($total_n[$i]-$total_farmers[$i*3]) .')</div>';
		}
	}
	echo "\n<br/>\n";
	}
//echo "<div>*During the early seasons (1996-2002), hybrid rice in the Philippines is at its early stage and is not widely used.</div>";
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
echo displayNoteRounding();
echo displayNoteIrrigate();
?>
<br/>
<div>Data accessed at <?php echo date('l jS \of F Y h:i:s A');?></div>
<?php echo displayNoteContact(); ?>
</div>
<?php
require_once("../includes/export.php");
?>