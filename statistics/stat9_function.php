<?php
	require_once("../includes/headeralt.php");
?>	
<div id="tableData">
<?php
$regions = $provinces = $cities = $categories = $years = $seed_class = array();
$region_count = $province_count = $city_count = $category_count = $seed_class_count = $year_count = 0;

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
if(isset($_POST['seed_classification'])){
	$seed_class = disinfect_var($_POST['seed_classification']);
	ksort($seed_class);
	$seed_class_count = count($seed_class);
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
		echo '<th class="center" rowspan="2">YEAR</th>'; echo "\n";
		foreach ($seed_class as $sc){
			$label = "";
			switch($sc){
				case 5: $label = "Hybrid"; break;
				case 6: $label = "Inbred"; break;
				case 7: $label = "Foundation"; break;
				case 8: $label = "Registered"; break;
				case 9: $label = "Certified"; break;
				case 10: $label = "Good"; break;
				case 11: $label = "Farmers"; break;
				case 12: $label = "Native"; break;
				case 13: $label = "All Seed Classes"; break;
			}
			echo '<th class="center" colspan="' . $category_count . '">' . $label . '</th>'; echo "\n";
		}
		echo "</tr>";
		echo "<tr>\n";
		for($i = 0; $i < $seed_class_count; $i++){
			foreach ($categories as $c){
				$label = "";
				switch($c){
					case 1: $label = "Direct Seeding"; break;
					case 2: $label = "Transplanting"; break;
					case 3: $label = "Both Methods"; break;
				}
			echo '<th class="center"	>' . $label . '</th>'; echo "\n";
			}
		}
		echo "</tr>";
		echo "</thead>\n";
		echo "<tbody>\n";
		foreach ($years as $year) {
		$dseeding = $tplanting = $bmethods = array();
		echo "<tr>";
		echo "<td>" . $year . "</td>";
		if ($stmt = $conn->prepare("SELECT * FROM stat_seedcrop WHERE year = ? AND location_code = ? AND location_type = 1 AND crop_establishment = 2")) {
			$stmt->bind_param("ss", $year, $region);
			$stmt->execute();
			$stmt->store_result();
			while($row = fetch_get_result_alt($stmt)){
				$dseeding = array_values($row);
			}
		}
		if($stmt->num_rows < 1){
			for($i = 0; $i < 13; $i++){
				array_push($dseeding, 0);
			}
		}
		$stmt->close();
		
		if ($stmt = $conn->prepare("SELECT * FROM stat_seedcrop WHERE year = ? AND location_code = ? AND location_type = 1 AND crop_establishment = 1")) {
			$stmt->bind_param("ss", $year, $region);
			$stmt->execute();
			$stmt->store_result();
			while($row = fetch_get_result_alt($stmt)){
				$tplanting = array_values($row);
			}
		}
		if($stmt->num_rows < 1){
			for($i = 0; $i < 13; $i++){
				array_push($tplanting, 0);
			}
		}
		$stmt->close();
		
		if ($stmt = $conn->prepare("SELECT * FROM stat_seedcrop WHERE year = ? AND location_code = ? AND location_type = 1 AND crop_establishment = 3")) {
			$stmt->bind_param("ss", $year, $region);
			$stmt->execute();
			$stmt->store_result();
			while($row = fetch_get_result_alt($stmt)){
				$bmethods = array_values($row);
			}
		}
		if($stmt->num_rows < 1){
			for($i = 0; $i < 13; $i++){
				array_push($bmethods, 0);
			}
		}
		$stmt->close();

		for($i = 4; $i < max($seed_class); $i++){
			$flag = $i + 1;
			if(in_array($flag, $seed_class)){
				if(in_array(2,$categories)){
					if($dseeding[$i] > 0 && is_numeric ($dseeding[$i])){
						echo "<td>" . number_format($dseeding[$i], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
				if(in_array(1,$categories)){
					if($tplanting[$i] > 0 && is_numeric ($tplanting[$i])){
						echo "<td>" . number_format($tplanting[$i], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
				if(in_array(3,$categories)){
					if($bmethods[$i] > 0 && is_numeric ($bmethods[$i])){
						echo "<td>" . number_format($bmethods[$i], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
		}
		echo "</tr>";
		}
		echo "</tbody>\n";
		echo "</table>\n";
	}
	echo "</br>";
}

if(isset($_POST['provinces'])){
	foreach ($provinces as $province) {
		if ($pstmt = $conn->prepare("SELECT province_name FROM legend_province WHERE province_id = ?")) {
			$pstmt->bind_param("i", $province);
			$pstmt->execute();
			$pstmt->bind_result($province_name);
			while($pstmt->fetch()){
				echo "<h3>". $province_name ."</h3>";
			}
			$pstmt->close();				
		}
		echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
		echo "<thead>\n";
		echo "<tr>\n";
		echo '<th class="center" rowspan="2">YEAR</th>'; echo "\n";
		foreach ($seed_class as $sc){
			$label = "";
			switch($sc){
				case 5: $label = "Hybrid"; break;
				case 6: $label = "Inbred"; break;
				case 7: $label = "Foundation"; break;
				case 8: $label = "Registered"; break;
				case 9: $label = "Certified"; break;
				case 10: $label = "Good"; break;
				case 11: $label = "Farmers"; break;
				case 12: $label = "Native"; break;
				case 13: $label = "All Seed Classes"; break;
			}
			echo '<th class="center" colspan="' . $category_count . '">' . $label . '</th>'; echo "\n";
		}
		echo "</tr>";
		echo "<tr>\n";
		for($i = 0; $i < $seed_class_count; $i++){
			foreach ($categories as $c){
				$label = "";
				switch($c){
					case 1: $label = "Direct Seeding"; break;
					case 2: $label = "Transplanting"; break;
					case 3: $label = "Both Methods"; break;
				}
			echo '<th class="center">' . $label . '</th>'; echo "\n";
			}
		}
		echo "</tr>";
		echo "</thead>\n";
		echo "<tbody>\n";
		foreach ($years as $year) {
		$dseeding = $tplanting = $bmethods = array();
		echo "<tr>";
		echo "<td>" . $year . "</td>";
		if ($stmt = $conn->prepare("SELECT * FROM stat_seedcrop WHERE year = ? AND location_code = ? AND location_type = 2 AND crop_establishment = 2")) {
			$stmt->bind_param("ss", $year, $province);
			$stmt->execute();
			$stmt->store_result();
			while($row = fetch_get_result_alt($stmt)){
				$dseeding = array_values($row);
			}
		}
		if($stmt->num_rows < 1){
			for($i = 0; $i < 13; $i++){
				array_push($dseeding, 0);
			}
		}
		$stmt->close();
		
		if ($stmt = $conn->prepare("SELECT * FROM stat_seedcrop WHERE year = ? AND location_code = ? AND location_type = 2 AND crop_establishment = 1")) {
			$stmt->bind_param("ss", $year, $province);
			$stmt->execute();
			$stmt->store_result();
			while($row = fetch_get_result_alt($stmt)){
				$tplanting = array_values($row);
			}
		}
		if($stmt->num_rows < 1){
			for($i = 0; $i < 13; $i++){
				array_push($tplanting, 0);
			}
		}
		$stmt->close();
		
		if ($stmt = $conn->prepare("SELECT * FROM stat_seedcrop WHERE year = ? AND location_code = ? AND location_type = 2 AND crop_establishment = 3")) {
			$stmt->bind_param("ss", $year, $province);
			$stmt->execute();
			$stmt->store_result();
			while($row = fetch_get_result_alt($stmt)){
				$bmethods = array_values($row);
			}
		}
		if($stmt->num_rows < 1){
			for($i = 0; $i < 13; $i++){
				array_push($bmethods, 0);
			}
		}
		$stmt->close();

		for($i = 4; $i < max($seed_class); $i++){
			$flag = $i + 1;
			if(in_array($flag, $seed_class)){
				if(in_array(2,$categories)){
					if($dseeding[$i] > 0 && is_numeric ($dseeding[$i])){
						echo "<td>" . number_format($dseeding[$i], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
				if(in_array(1,$categories)){
					if($tplanting[$i] > 0 && is_numeric ($tplanting[$i])){
						echo "<td>" . number_format($tplanting[$i], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
				if(in_array(3,$categories)){
					if($bmethods[$i] > 0 && is_numeric ($bmethods[$i])){
						echo "<td>" . number_format($bmethods[$i], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
		}
		echo "</tr>";
		}
		echo "</tbody>\n";
		echo "</table>\n";
	}
	echo "</br>";
}

if(isset($_POST['cities'])){
	foreach ($cities as $city) {
		if ($cstmt = $conn->prepare("SELECT city_name FROM legend_city WHERE city_id = ?")) {
			$cstmt->bind_param("i", $city);
			$cstmt->execute();
			$cstmt->bind_result($city_name);
			while($cstmt->fetch()){
				echo "<h3>". $city_name ."</h3>";
			}
			$cstmt->close();				
		}
		echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
		echo "<thead>\n";
		echo "<tr>\n";
		echo '<th class="center" rowspan="2">YEAR</th>'; echo "\n";
		foreach ($seed_class as $sc){
			$label = "";
			switch($sc){
				case 5: $label = "Hybrid"; break;
				case 6: $label = "Inbred"; break;
				case 7: $label = "Foundation"; break;
				case 8: $label = "Registered"; break;
				case 9: $label = "Certified"; break;
				case 10: $label = "Good"; break;
				case 11: $label = "Farmers"; break;
				case 12: $label = "Native"; break;
				case 13: $label = "All Seed Classes"; break;
			}
			echo '<th class="center" colspan="' . $category_count . '">' . $label . '</th>'; echo "\n";
		}
		echo "</tr>";
		echo "<tr>\n";
		for($i = 0; $i < $seed_class_count; $i++){
			foreach ($categories as $c){
				$label = "";
				switch($c){
					case 1: $label = "Direct Seeding"; break;
					case 2: $label = "Transplanting"; break;
					case 3: $label = "Both Methods"; break;
				}
			echo '<th class="center">' . $label . '</th>'; echo "\n";
			}
		}
		echo "</tr>";
		echo "</thead>\n";
		echo "<tbody>\n";
		foreach ($years as $year) {
		$dseeding = $tplanting = $bmethods = array();
		echo "<tr>";
		echo "<td>" . $year . "</td>";
		if ($stmt = $conn->prepare("SELECT * FROM stat_seedcrop WHERE year = ? AND location_code = ? AND location_type = 2 AND crop_establishment = 2")) {
			$stmt->bind_param("ss", $year, $city);
			$stmt->execute();
			$stmt->store_result();
			while($row = fetch_get_result_alt($stmt)){
				$dseeding = array_values($row);
			}
		}
		if($stmt->num_rows < 1){
			for($i = 0; $i < 13; $i++){
				array_push($dseeding, 0);
			}
		}
		$stmt->close();
		
		if ($stmt = $conn->prepare("SELECT * FROM stat_seedcrop WHERE year = ? AND location_code = ? AND location_type = 2 AND crop_establishment = 1")) {
			$stmt->bind_param("ss", $year, $city);
			$stmt->execute();
			$stmt->store_result();
			while($row = fetch_get_result_alt($stmt)){
				$tplanting = array_values($row);
			}
		}
		if($stmt->num_rows < 1){
			for($i = 0; $i < 13; $i++){
				array_push($tplanting, 0);
			}
		}
		$stmt->close();
		
		if ($stmt = $conn->prepare("SELECT * FROM stat_seedcrop WHERE year = ? AND location_code = ? AND location_type = 2 AND crop_establishment = 3")) {
			$stmt->bind_param("ss", $year, $city);
			$stmt->execute();
			$stmt->store_result();
			while($row = fetch_get_result_alt($stmt)){
				$bmethods = array_values($row);
			}
		}
		if($stmt->num_rows < 1){
			for($i = 0; $i < 13; $i++){
				array_push($bmethods, 0);
			}
		}
		$stmt->close();

		for($i = 4; $i < max($seed_class); $i++){
			$flag = $i + 1;
			if(in_array($flag, $seed_class)){
				if(in_array(2,$categories)){
					if($dseeding[$i] > 0 && is_numeric ($dseeding[$i])){
						echo "<td>" . number_format($dseeding[$i], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
				if(in_array(1,$categories)){
					if($tplanting[$i] > 0 && is_numeric ($tplanting[$i])){
						echo "<td>" . number_format($tplanting[$i], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
				if(in_array(3,$categories)){
					if($bmethods[$i] > 0 && is_numeric ($bmethods[$i])){
						echo "<td>" . number_format($bmethods[$i], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
		}
		echo "</tr>";
		}
		echo "</tbody>\n";
		echo "</table>\n";
	}
	echo "</br>";
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