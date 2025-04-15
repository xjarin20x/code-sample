<?php
	require_once("../includes/headeralt.php");
?>	
<div id="tableData">
<?php
$regions = $provinces = $cities = $categories = $period = $years = array();
$region_count = $province_count = $city_count = $category_count = $period_count = $year_count = 0;

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
if(isset($_POST['period'])){
	$period = disinfect_var($_POST['period']);
	$period_count = count($period);
	$period_columns = array_sum($period);
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
		if(in_array(2,$period)){
			echo '<th class="center" colspan="'. $category_count . '">JAN-JUN</th>'; echo "\n";
			echo '<th class="center" colspan="'. $category_count . '">JUL-DEC</th>'; echo "\n";
		}
		if(in_array(1,$period)){
			echo '<th class="center" colspan="'. $category_count . '">JAN-DEC<sup>1</sup></th>'; echo "\n";
		}
		echo "</tr>";
		echo "<tr>\n";
		for($i = 0; $i < $period_columns; $i++){
			if(in_array(1,$categories)){
				echo '<th class="center">TRANSPLANTING</th>'; echo "\n";
			}
			if(in_array(2,$categories)){
				echo '<th class="center">DIRECT SEEDING</th>'; echo "\n";
			}
		}
		echo "</tr>";
		echo "</thead>\n";
		echo "<tbody>\n";
		foreach ($years as $year) {
			$ccounter = 0;
			$semester1 = $semester2 = $annual = array();
			echo "<tr>";
			echo "<td>" . $year . "</td>";
			if ($stmt = $conn->prepare("SELECT farms_reporting FROM stat_reporting WHERE year = ? AND location_code = ? AND semester = 1 AND location_type = 1 ORDER BY crop_establishment ASC")) {
				$stmt->bind_param("ss", $year, $region);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($freport);
				while($stmt->fetch()){
					array_push($semester1, $freport);
				}
			}
			if($stmt->num_rows < 1){
				for($i = 0; $i < 6; $i++){
					array_push($semester1, 0);
				}
			}
			$stmt->close();
				
			if ($stmt = $conn->prepare("SELECT farms_reporting FROM stat_reporting WHERE year = ? AND location_code = ? AND semester = 2 AND location_type = 1 ORDER BY crop_establishment ASC")) {
				$stmt->bind_param("ss", $year, $region);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($freport);
				while($stmt->fetch()){
					array_push($semester2, $freport);
				}
			}
			if($stmt->num_rows < 1){
				for($i = 0; $i < 6; $i++){
					array_push($semester2, 0);
				}
			}
			$stmt->close();
				
			if(in_array(1,$period)){
				for($i = 0; $i < max($categories); $i++){
					$annual[$i] = (($semester1[$i] + $semester2[$i]) / 2);
				}
			}
		
			if(in_array(2,$period)){
				for($i = 0; $i < max($categories); $i++){
					$flag = $i + 1;
					if(in_array($flag,$categories)){
						if($semester1[$i] > 0 && is_numeric ($semester1[$i])){
							echo "<td>" . number_format($semester1[$i], 2). "</td>";
						}
						else{
							echo "<td>-</td>";
						}
					}
				}
				for($i = 0; $i < max($categories); $i++){
					$flag = $i + 1;
					if(in_array($flag,$categories)){
						if($semester2[$i] > 0 && is_numeric ($semester2[$i])){
							echo "<td>" . number_format($semester2[$i], 2). "</td>";
						}
						else{
							echo "<td>-</td>";
						}
					}
				}
			}
			
			if(in_array(1,$period)){
				for($i = 0; $i < max($categories); $i++){
					$flag = $i + 1;
					if(in_array($flag,$categories)){
						if($annual[$i] > 0 && is_numeric ($annual[$i])){
							echo "<td>" . number_format($annual[$i], 2). "</td>";
						}
						else{
							echo "<td>-</td>";
						}
					}
				}
			}
			echo "</tr>\n";
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
	if(in_array(2,$period)){
		echo '<th class="center" colspan="'. $category_count . '">JAN-JUN</th>'; echo "\n";
		echo '<th class="center" colspan="'. $category_count . '">JUL-DEC</th>'; echo "\n";
	}
	if(in_array(1,$period)){
		echo '<th class="center" colspan="'. $category_count . '">JAN-DEC<sup>1</sup></th>'; echo "\n";
	}
	echo "</tr>";
	echo "<tr>\n";
	for($i = 0; $i < $period_columns; $i++){
		if(in_array(1,$categories)){
			echo '<th class="center">TRANSPLANTING</th>'; echo "\n";
		}
		if(in_array(2,$categories)){
			echo '<th class="center">DIRECT SEEDING</th>'; echo "\n";
		}
	}
	echo "</tr>";
	echo "</thead>\n";
	echo "<tbody>\n";
	foreach ($years as $year) {
		$ccounter = 0;
		$semester1 = $semester2 = $annual = array();
		echo "<tr>";
		echo "<td>" . $year . "</td>";
		if ($stmt = $conn->prepare("SELECT farms_reporting FROM stat_reporting WHERE year = ? AND location_code = ? AND semester = 1 AND location_type = 2 ORDER BY crop_establishment ASC")) {
			$stmt->bind_param("ss", $year, $province);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($freport);
			while($stmt->fetch()){
				array_push($semester1, $freport);
			}
		}
		if($stmt->num_rows < 1){
			for($i = 0; $i < 6; $i++){
				array_push($semester1, 0);
			}
		}
		$stmt->close();
			
		if ($stmt = $conn->prepare("SELECT farms_reporting FROM stat_reporting WHERE year = ? AND location_code = ? AND semester = 2 AND location_type = 2 ORDER BY crop_establishment ASC")) {
			$stmt->bind_param("ss", $year, $province);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($freport);
			while($stmt->fetch()){
				array_push($semester2, $freport);
			}
		}
		if($stmt->num_rows < 1){
			for($i = 0; $i < 6; $i++){
				array_push($semester2, 0);
			}
		}
		$stmt->close();
			
		if(in_array(1,$period)){
			for($i = 0; $i < max($categories); $i++){
				$annual[$i] = (($semester1[$i] + $semester2[$i]) / 2);
			}
		}
	
		if(in_array(2,$period)){
			for($i = 0; $i < max($categories); $i++){
				$flag = $i + 1;
				if(in_array($flag,$categories)){
					if($semester1[$i] > 0 && is_numeric ($semester1[$i])){
						echo "<td>" . number_format($semester1[$i], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			for($i = 0; $i < max($categories); $i++){
				$flag = $i + 1;
				if(in_array($flag,$categories)){
					if($semester2[$i] > 0 && is_numeric ($semester2[$i])){
						echo "<td>" . number_format($semester2[$i], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
		}
		
		if(in_array(1,$period)){
			for($i = 0; $i < max($categories); $i++){
				$flag = $i + 1;
				if(in_array($flag,$categories)){
					if($annual[$i] > 0 && is_numeric ($annual[$i])){
						echo "<td>" . number_format($annual[$i], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
		}
		echo "</tr>\n";
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
	if(in_array(2,$period)){
		echo '<th class="center" colspan="'. $category_count . '">JAN-JUN</th>'; echo "\n";
		echo '<th class="center" colspan="'. $category_count . '">JUL-DEC</th>'; echo "\n";
	}
	if(in_array(1,$period)){
		echo '<th class="center" colspan="'. $category_count . '">JAN-DEC<sup>1</sup></th>'; echo "\n";
	}
	echo "</tr>";
	echo "<tr>\n";
	for($i = 0; $i < $period_columns; $i++){
		if(in_array(1,$categories)){
			echo '<th class="center">TRANSPLANTING</th>'; echo "\n";
		}
		if(in_array(2,$categories)){
			echo '<th class="center">DIRECT SEEDING</th>'; echo "\n";
		}
	}
	echo "</tr>";
	echo "</thead>\n";
	echo "<tbody>\n";
	foreach ($years as $year) {
		$ccounter = 0;
		$semester1 = $semester2 = $annual = array();
		echo "<tr>";
		echo "<td>" . $year . "</td>";
		if ($stmt = $conn->prepare("SELECT farms_reporting FROM stat_reporting WHERE year = ? AND location_code = ? AND semester = 1 AND location_type = 3 ORDER BY crop_establishment ASC")) {
			$stmt->bind_param("ss", $year, $city);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($freport);
			while($stmt->fetch()){
				array_push($semester1, $freport);
			}
		}
		if($stmt->num_rows < 1){
			for($i = 0; $i < 6; $i++){
				array_push($semester1, 0);
			}
		}
		$stmt->close();
			
		if ($stmt = $conn->prepare("SELECT farms_reporting FROM stat_reporting WHERE year = ? AND location_code = ? AND semester = 2 AND location_type = 3 ORDER BY crop_establishment ASC")) {
			$stmt->bind_param("ss", $year, $city);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($freport);
			while($stmt->fetch()){
				array_push($semester2, $freport);
			}
		}
		if($stmt->num_rows < 1){
			for($i = 0; $i < 6; $i++){
				array_push($semester2, 0);
			}
		}
		$stmt->close();
			
		if(in_array(1,$period)){
			for($i = 0; $i < max($categories); $i++){
				$annual[$i] = (($semester1[$i] + $semester2[$i]) / 2);
			}
		}
	
		if(in_array(2,$period)){
			for($i = 0; $i < max($categories); $i++){
				$flag = $i + 1;
				if(in_array($flag,$categories)){
					if($semester1[$i] > 0 && is_numeric ($semester1[$i])){
						echo "<td>" . number_format($semester1[$i], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			for($i = 0; $i < max($categories); $i++){
				$flag = $i + 1;
				if(in_array($flag,$categories)){
					if($semester2[$i] > 0 && is_numeric ($semester2[$i])){
						echo "<td>" . number_format($semester2[$i], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
		}
		
		if(in_array(1,$period)){
			for($i = 0; $i < max($categories); $i++){
				$flag = $i + 1;
				if(in_array($flag,$categories)){
					if($annual[$i] > 0 && is_numeric ($annual[$i])){
						echo "<td>" . number_format($annual[$i], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
		}
		echo "</tr>\n";
	}
	echo "</tbody>\n";
	echo "</table>\n";
	}
	echo "</br>";
}

echo '<p class="footnote"><sup>1</sup> - Values were dynamically computed from the official PSA statistics</p>
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