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
<h3>Distribution of farmers by type of rice farming seminar/training attended</h3>
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
	$total_farmers = $below_significance = array();
	$counter = -1;
	echo "<table>\n";
	
	$training_array = $others = array();
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
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_training WHERE matrix_rectype1.region = matrix_training.region AND matrix_rectype1.province = matrix_training.province AND matrix_rectype1.municipality = matrix_training.municipality AND matrix_rectype1.barangay = matrix_training.barangay AND matrix_rectype1.hh_number = matrix_training.hh_number AND matrix_rectype1.season = matrix_training.season AND matrix_rectype1.season = ? GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
		$all_stmt->bind_param("s", $season);
	}
	else{
		$all_stmt=$conn->prepare("SELECT COUNT(*) FROM (SELECT DISTINCT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season FROM matrix_rectype1 JOIN matrix_training WHERE matrix_rectype1.region = matrix_training.region AND matrix_rectype1.province = matrix_training.province AND matrix_rectype1.municipality = matrix_training.municipality AND matrix_rectype1.barangay = matrix_training.barangay AND matrix_rectype1.hh_number = matrix_training.hh_number AND matrix_rectype1.season = matrix_training.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? GROUP BY matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season) a");
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
		$all_stmt= $conn->prepare("SELECT t.training, IFNULL(l.training_topic, t.training) as topic, COUNT(t.training) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_training.training FROM matrix_rectype1 JOIN matrix_training WHERE matrix_rectype1.region = matrix_training.region AND matrix_rectype1.province = matrix_training.province AND matrix_rectype1.municipality = matrix_training.municipality AND matrix_rectype1.barangay = matrix_training.barangay AND matrix_rectype1.hh_number = matrix_training.hh_number AND matrix_rectype1.season = matrix_training.season AND matrix_rectype1.season = ? AND matrix_training.training IS NOT NULL) t LEFT JOIN (SELECT training_id, training_topic FROM legend_training WHERE season = ?) l ON t.training = l.training_id GROUP BY topic ORDER BY COUNT(t.training) DESC");
		$all_stmt->bind_param("ss", $season, $legend);
	}
	else{
		$all_stmt= $conn->prepare("SELECT t.training, IFNULL(l.training_topic, t.training) as topic, COUNT(t.training) FROM (SELECT matrix_rectype1.region, matrix_rectype1.province, matrix_rectype1.municipality, matrix_rectype1.barangay, matrix_rectype1.hh_number, matrix_rectype1.season, matrix_training.training FROM matrix_rectype1 JOIN matrix_training WHERE matrix_rectype1.region = matrix_training.region AND matrix_rectype1.province = matrix_training.province AND matrix_rectype1.municipality = matrix_training.municipality AND matrix_rectype1.barangay = matrix_training.barangay AND matrix_rectype1.hh_number = matrix_training.hh_number AND matrix_rectype1.season = matrix_training.season AND matrix_rectype1.season = ? AND matrix_rectype1.province = ? AND matrix_training.training IS NOT NULL) t LEFT JOIN (SELECT training_id, training_topic FROM legend_training WHERE season = ?) l ON t.training = l.training_id GROUP BY topic ORDER BY COUNT(t.training) DESC");
		$all_stmt->bind_param("sss", $season, $province, $legend);
	}
		
	$all_stmt->execute();
	$all_stmt->store_result();
	$all_stmt->bind_result($id, $name, $quantity);
	
	$counter++;
			
	if($all_stmt->num_rows != 0){
		while($all_stmt->fetch()){
			if(!isset($training_array[$name])){
				$training_array[$name] = array();
			}
			$training_array[$name][$counter] = $quantity;
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
	if( !empty($training_array) ){
	create_header($name, $season_names, 1);
	echo "<tbody>\n";
	create_headings("Type of Trainings/Seminars Attended", $headings);
	create_special_rows("", array_formatting($total_farmers,"n = (",")*"), 1);
	foreach ($training_array as $key => &$sec_arr){
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
			create_average($training_array[$key], $total_farmers, ucfirst($key), 0);
		}
		else{
			for($j=0; $j < count($training_array[$key]); $j++){
				if(!isset($others[$j])){
					$others[$j] = 0;
				}
				$others[$j] = $others[$j] + $training_array[$key][$j];
			}
		}
	}
	create_average($others, $total_farmers, "Others", 0);
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
echo "<div><b>Note:</b> Respondents provided multiple answers so the percentage may exceed 100.</div>";
echo "<div>*This excludes farmers who did not attend rice farming seminars/trainings within five years.</div>\n<br/>";
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
echo displayNoteContact();
</div>
<?php
require_once("../includes/export.php");
require_once("../includes/footer.php");
?>