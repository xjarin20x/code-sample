<?php
	require_once("../includes/headeralt.php");
?>	
<div id="tableData">
<?php
$regions = $provinces = $cities = $categories = $years = $causes = array();
$region_count = $province_count = $city_count = $category_count = $year_count = $cause_count = 0;

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
	$ha_colspan = in_array(6,$categories) + in_array(7,$categories);
}
if(isset($_POST['causes'])){
	$causes = disinfect_var($_POST['causes']);
	sort($causes);
	$cause_count = count($causes);
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
		foreach ($causes as $cause){
			switch($cause){
				case 1: $label = "All Causes"; break;
				case 2: $label = "Flashfloods and Typhoons"; break;
				case 3: $label = "Drought"; break;
				case 4: $label = "Pests and Diseases"; break;
				case 5: $label = "Other Causes"; break;
			}
			echo "<h4>". $label ."</h4>";
			echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
			echo "<thead>\n";
			echo "<tr>\n";
			echo '<th class="center" rowspan="2">YEAR</th>'; echo "\n";
			foreach ($categories as $category){
				$label = "";
				switch($category){
					case 6: $label = "Area Affected"; break;
					case 7: $label = "Totally Damaged"; break;
					case 8: $label = "Production Losses"; break;
					case 9: $label = "Value of production"; break;
				}
			echo '<th class="center"	>' . $label . '</th>'; echo "\n";
			}
			echo "</tr>";
			echo "<tr>";
			if(in_array(6,$categories) || in_array(7,$categories)){
				echo '<th class="center" colspan="' . $ha_colspan. '">(Hectare)'; echo "\n";
			}
			if(in_array(8,$categories)){
				echo '<th class="center">(Metric Ton)'; echo "\n";	
			}
			if(in_array(9,$categories)){
				echo '<th class="center">(PhP\'000)'; echo "\n";	
			}
			echo "</thead>\n";
			echo "<tbody>\n";
			foreach ($years as $year) {
				$annual = array();
				echo "<tr>";
				if ($stmt = $conn->prepare("SELECT * FROM stat_damages WHERE year = ? AND location_code = ? AND cause = ? AND location_type = 1")) {
					$stmt->bind_param("sss", $year, $region, $cause);
					$stmt->execute();
					$stmt->store_result();
					while($row = fetch_get_result_alt($stmt)){
						$annual = array_values($row);
					}
				}

				if($stmt->num_rows < 1){
					for($i = 0; $i < 9; $i++){
						array_push($annual, 0);
					}
				}

				$total_damages = 0;
				for ($j=5; $j < count($annual); $j++) { 
					$total_damages = $total_damages + $annual[$j];
				}

				if($annual[3] == 0){
					echo "<td>" . $year . "</td>";
				}
				else {
					echo "<td>" . $year . " <sup>r/</sup></td>";
				}

				if($total_damages > 0){
					for($i = 5; $i <= max($categories); $i++){
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
				else {
					echo "<td colspan=".$category_count.">(not applicable within this region)</td>";
				}
			}
			echo "</tr>";
			echo "</tbody>\n";
			echo "</table>\n";
			}
			echo "</br>";
		}
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
		foreach ($causes as $cause){
			switch($cause){
				case 1: $label = "All Causes"; break;
				case 2: $label = "Flashfloods and Typhoons"; break;
				case 3: $label = "Drought"; break;
				case 4: $label = "Pests and Diseases"; break;
				case 5: $label = "Other Causes"; break;
			}
			echo "<h4>". $label ."</h4>";
			echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
			echo "<thead>\n";
			echo "<tr>\n";
			echo '<th class="center" rowspan="2">YEAR</th>'; echo "\n";
			foreach ($categories as $category){
				$label = "";
				switch($category){
					case 6: $label = "Area Affected"; break;
					case 7: $label = "Totally Damaged"; break;
					case 8: $label = "Production Losses"; break;
					case 9: $label = "Value of production"; break;
				}
			echo '<th class="center"	>' . $label . '</th>'; echo "\n";
			}
			echo "</tr>";
			echo "<tr>";
			if(in_array(6,$categories) || in_array(7,$categories)){
				echo '<th class="center" colspan="' . $ha_colspan. '">(Hectare)'; echo "\n";
			}
			if(in_array(8,$categories)){
				echo '<th class="center">(Metric Ton)'; echo "\n";	
			}
			if(in_array(9,$categories)){
				echo '<th class="center">(PhP\'000)'; echo "\n";	
			}
			echo "</thead>\n";
			echo "<tbody>\n";
			foreach ($years as $year) {
				$annual = array();
				echo "<tr>";
				if ($stmt = $conn->prepare("SELECT * FROM stat_damages WHERE year = ? AND location_code = ? AND cause = ? AND location_type = 2")) {
					$stmt->bind_param("sss", $year, $province, $cause);
					$stmt->execute();
					$stmt->store_result();
					while($row = fetch_get_result_alt($stmt)){
						$annual = array_values($row);
					}
				}
				if($stmt->num_rows < 1){
					for($i = 0; $i < 9; $i++){
						array_push($annual, 0);
					}
				}

				$total_damages = 0;
				for ($j=5; $j < count($annual); $j++) { 
					$total_damages = $total_damages + $annual[$j];
				}

				if($annual[3] == 0){
					echo "<td>" . $year . "</td>";
				}
				else {
					echo "<td>" . $year . " <sup>r/</sup></td>";
				}

				if($total_damages > 0){
					for($i = 5; $i <= max($categories); $i++){
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
				else {
					echo "<td colspan=".$category_count.">(not applicable within this province)</td>";
				}

			}
			echo "</tr>";
			echo "</tbody>\n";
			echo "</table>\n";
			}
			echo "</br>";
		}
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
		foreach ($causes as $cause){
			switch($cause){
				case 1: $label = "All Causes"; break;
				case 2: $label = "Flashfloods and Typhoons"; break;
				case 3: $label = "Drought"; break;
				case 4: $label = "Pests and Diseases"; break;
				case 5: $label = "Other Causes"; break;
			}
			echo "<h4>". $label ."</h4>";
			echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
			echo "<thead>\n";
			echo "<tr>\n";
			echo '<th class="center" rowspan="2">YEAR</th>'; echo "\n";
			foreach ($categories as $category){
				$label = "";
				switch($category){
					case 6: $label = "Area Affected"; break;
					case 7: $label = "Totally Damaged"; break;
					case 8: $label = "Production Losses"; break;
					case 9: $label = "Value of production"; break;
				}
			echo '<th class="center"	>' . $label . '</th>'; echo "\n";
			}
			echo "</tr>";
			echo "<tr>";
			if(in_array(6,$categories) || in_array(7,$categories)){
				echo '<th class="center" colspan="' . $ha_colspan. '">(Hectare)'; echo "\n";
			}
			if(in_array(8,$categories)){
				echo '<th class="center">(Metric Ton)'; echo "\n";	
			}
			if(in_array(9,$categories)){
				echo '<th class="center">(PhP\'000)'; echo "\n";	
			}
			echo "</thead>\n";
			echo "<tbody>\n";
			foreach ($years as $year) {
				$annual = array();
				echo "<tr>";
				if ($stmt = $conn->prepare("SELECT * FROM stat_damages WHERE year = ? AND location_code = ? AND cause = ? AND location_type = 3")) {
					$stmt->bind_param("sss", $year, $city, $cause);
					$stmt->execute();
					$stmt->store_result();
					while($row = fetch_get_result_alt($stmt)){
						$annual = array_values($row);
					}
				}
				if($stmt->num_rows < 1){
					for($i = 0; $i < 9; $i++){
						array_push($annual, 0);
					}
				}

				$total_damages = 0;
				for ($j=5; $j < count($annual); $j++) { 
					$total_damages = $total_damages + $annual[$j];
				}

				if($annual[3] == 0){
					echo "<td>" . $year . "</td>";
				}
				else {
					echo "<td>" . $year . " <sup>r/</sup></td>";
				}

				if($total_damages > 0){
					for($i = 5; $i <= max($categories); $i++){
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
				else {
					echo "<td colspan=".$category_count.">(not applicable within this city)</td>";
				}

			}
			echo "</tr>";
			echo "</tbody>\n";
			echo "</table>\n";
			}
			echo "</br>";
		}
}

echo '<p class="footnote">r/ = Revised</p>
<p class="footnote">- = Data not available</p>
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