<?php
	require_once("../includes/headeralt.php");
?>	
<div id="tableData">
<?php
$regions = $provinces = $cities = $categories = $years = array();
$region_count = $province_count = $city_count = $category_count = $year_count = 0;

if(isset($_POST['regions'])){
	$regions = disinfect_var($_POST['regions']);
	$region_count = count($regions);
}
if(isset($_POST['provinces'])){
	$provinces = disinfect_var($_POST['provinces']);
	$province_count = count($provinces);
}
if(isset($_POST['cities'])){
	$cities = disinfect_var($_POST['cities']);
	$city_count = count($cities);
}
if(isset($_POST['categories'])){
	$categories = disinfect_var($_POST['categories']);
	ksort($categories);
	$category_count = count($categories);
}
if(isset($_POST['years'])){
	$years = disinfect_var($_POST['years']);
	$year_count = count($years);
}
if(isset($_POST['regions'])){
foreach ($regions as $region) {
	if ($rstmt = $conn->prepare("SELECT region_name FROM legend_region WHERE region_id = ?")) {
		$rstmt->bind_param("i", $region);
		$rstmt->execute();
		$rstmt->bind_result($region_name);
		while($rstmt->fetch()){
			echo "<h3>". $region_name ."</h3>";
		}
		$rstmt->close();				
	}
	
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	echo "<thead>\n";
	echo "<tr>\n";
	echo '<th class="center">YEAR</th>'; echo "\n";
	foreach ($categories as $c){
		$label = "";
		switch($c){
			case 4: $label = "Landlord's share"; break;
			case 5: $label = "Sold"; break;
			case 6: $label = "Food"; break;
			case 7: $label = "Others"; break;
			case 8: $label = "Total"; break;
		}
		echo '<th class="center">'. strtoupper($label) . '</th>'; echo "\n";	
	}
	echo "</tr>\n";
	echo "</thead>\n";
	echo "<tbody>\n";
	foreach ($years as $year) {
		$annual = array();
		echo "<tr>";
		echo "<td>" . $year . "</td>";
		if ($stmt = $conn->prepare("SELECT * FROM stat_pdisposition WHERE year = ? AND location_code = ? AND location_type = 1")) {
			$stmt->bind_param("ss", $year, $region);
			$stmt->execute();
			$stmt->store_result();
			while($row = fetch_get_result_alt($stmt)){
				$annual = array_values($row);
			}
		}
		if($stmt->num_rows < 1){
			for($i = 0; $i < 8; $i++){
				array_push($annual, 0);
			}
		}
		// echo "<pre>";
		// echo  $year . ": ";
		// print_r($annual);
		// echo "<br/>";
		// echo "</pre>";
		for($i = 4; $i <= max($categories); $i++){
			if(in_array($i,$categories)){
				if($annual[$i-1] > 0){
					echo "<td>" . number_format($annual[$i-1], 2). "</td>";
				}
				else{
					echo "<td>-</td>";
				}
			}
		}
	}
	echo "</tbody>\n";
	echo "</table>\n";
	echo "</br>";
}
}
if(isset($_POST['provinces'])){
foreach ($provinces as $province) {
	if ($rstmt = $conn->prepare("SELECT province_name FROM legend_province WHERE province_id = ?")) {
		$rstmt->bind_param("i", $province);
		$rstmt->execute();
		$rstmt->bind_result($province_name);
		while($rstmt->fetch()){
			echo "<h3>". $province_name ."</h3>";
		}
		$rstmt->close();				
	}
	
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	echo "<thead>\n";
	echo "<tr>\n";
	echo '<th class="center">YEAR</th>'; echo "\n";
	foreach ($categories as $c){
		$label = "";
		switch($c){
			case 4: $label = "Landlord's share"; break;
			case 5: $label = "Sold"; break;
			case 6: $label = "Food"; break;
			case 7: $label = "Others"; break;
			case 8: $label = "Total"; break;
		}
		echo '<th class="center">'. strtoupper($label) . '</th>'; echo "\n";	
	}
	echo "</tr>\n";
	echo "</thead>\n";
	echo "<tbody>\n";
	foreach ($years as $year) {
		$annual = array();
		echo "<tr>";
		echo "<td>" . $year . "</td>";
		if ($stmt = $conn->prepare("SELECT * FROM stat_pdisposition WHERE year = ? AND location_code = ? AND location_type = 2")) {
			$stmt->bind_param("ss", $year, $province);
			$stmt->execute();
			$stmt->store_result();
			while($row = fetch_get_result_alt($stmt)){
				$annual = array_values($row);
			}
		}
		if($stmt->num_rows < 1){
			for($i = 0; $i < 8; $i++){
				array_push($annual, 0);
			}
		}
		for($i = 4; $i <= max($categories); $i++){
			if(in_array($i,$categories)){
				if($annual[$i-1] > 0){
					echo "<td>" . number_format($annual[$i-1], 2). "</td>";
				}
				else{
					echo "<td>-</td>";
				}
			}
		}
	}
	echo "</tbody>\n";
	echo "</table>\n";
	echo "</br>";
}
}
if(isset($_POST['cities'])){
foreach ($cities as $city) {
	if ($rstmt = $conn->prepare("SELECT city_name FROM legend_city WHERE city_id = ?")) {
		$rstmt->bind_param("i", $city);
		$rstmt->execute();
		$rstmt->bind_result($city_name);
		while($rstmt->fetch()){
			echo "<h3>". $city_name ."</h3>";
		}
		$rstmt->close();				
	}
	
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	echo "<thead>\n";
	echo "<tr>\n";
	echo '<th class="center">YEAR</th>'; echo "\n";
	foreach ($categories as $c){
		$label = "";
		switch($c){
			case 4: $label = "Landlord's share"; break;
			case 5: $label = "Sold"; break;
			case 6: $label = "Food"; break;
			case 7: $label = "Others"; break;
			case 8: $label = "Total"; break;
		}
		echo '<th class="center">'. strtoupper($label) . '</th>'; echo "\n";	
	}
	echo "</tr>\n";
	echo "</thead>\n";
	echo "<tbody>\n";
	foreach ($years as $year) {
		$annual = array();
		echo "<tr>";
		echo "<td>" . $year . "</td>";
		if ($stmt = $conn->prepare("SELECT * FROM stat_pdisposition WHERE year = ? AND location_code = ? AND location_type = 3")) {
			$stmt->bind_param("ss", $year, $city);
			$stmt->execute();
			$stmt->store_result();
			while($row = fetch_get_result_alt($stmt)){
				$annual = array_values($row);
			}
		}
		if($stmt->num_rows < 1){
			for($i = 0; $i < 8; $i++){
				array_push($annual, 0);
			}
		}
		for($i = 4; $i <= max($categories); $i++){
			if(in_array($i,$categories)){
				if($annual[$i-1] > 0){
					echo "<td>" . number_format($annual[$i-1], 2). "</td>";
				}
				else{
					echo "<td>-</td>";
				}
			}
		}
	}
	echo "</tbody>\n";
	echo "</table>\n";
	echo "</br>";
}
}
echo '<p class="footnote">- = Data not available</p>
<p class="footnote">Apayao was part of Kalinga before 1994</p>
<p class="footnote">Batanes data is accessible beginning 2012</p>
<p class="footnote">Biliran was part pf Leyte before 1994</p>
<p class="footnote">Compostela Valley was part of Davao del Norte before 2002</p>
<p class="footnote">Dinagat Islands was part of Surigao Norte up to third quarter of 2009</p>
<p class="footnote">Guimaras was part of Iloilo before 1994</p>
<p class="footnote">Negros Occidental and Negros Oriental are part of the Negros Island Region (NIR) beginning 2016</p>
<p class="footnote">Sarangani was part of South Cotabato before 1994</p>
<p class="footnote">Zamboanga Sibugay was part of Zamboanga del Sur before 2002</p>
<p class="footnote">Revised data for 2012-2013 because of inclusion of Batanes statistics</p>
<p class="footnote">Revised data for Apr-Jun 2013 statistics</p>';
echo "<br/>";
echo "</div>";
echo "</div>";
?>