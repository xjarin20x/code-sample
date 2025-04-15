<?php
	require_once("../includes/headeralt.php");
?>	
<div id="tableData">
<?php
$regions = $provinces = $cities = $categories = $period = $ecosystems = $years = array();
$region_count = $province_count = $city_count = $category_count = $period_count = $ecosystems_count = $year_count = 0;

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
	$prod_count = in_array(6,$categories) + in_array(7,$categories);
	$area_count = in_array(8,$categories) + in_array(9,$categories);
	$yield_count = in_array(10,$categories) + in_array(11,$categories) + in_array(12,$categories);
}
if(isset($_POST['period'])){
	$period = disinfect_var($_POST['period']);
	$period_count = count($period);
}
if(isset($_POST['ecosystems'])){
	$ecosystems = disinfect_var($_POST['ecosystems']);
	$ecosystems_count = count($ecosystems);
}
if(isset($_POST['years'])){
	$years = disinfect_var($_POST['years']);
	$year_count = count($years);
}
## REGIONS
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

	foreach ($ecosystems as $ecosystem) {
		$ecoLabel = "";
		switch ($ecosystem){
			case 0: $ecoLabel = "RAINFED"; break;
			case 1: $ecoLabel = "IRRIGATED"; break;
			case 2: $ecoLabel = "ALL ECOSYSTEMS"; break;
			case 3: $ecoLabel = "UPLAND"; break;
		}
		echo "<h4>". $ecoLabel ."</h4>";
		echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
		echo "<thead>\n";
		echo "<tr>\n";
		echo '<th class="center" rowspan="3">YEAR</th>'; echo "\n";
		if(in_array(6,$categories) || in_array(7,$categories)){
			$colspan = $prod_count * array_sum($period);
			echo '<th class="center" colspan="' . $colspan. '">PRODUCTION (MT)</th>'; echo "\n";
		}
		if(in_array(8,$categories) || in_array(9,$categories)){
			$colspan = $area_count * array_sum($period);
			echo '<th class="center" colspan="' . $colspan. '">AREA HARVESTED (HA)</th>'; echo "\n";
		}
		if(in_array(10,$categories) || in_array(11,$categories) || in_array(12,$categories)){
			$colspan = $yield_count * array_sum($period);
			echo '<th class="center" colspan="' . $colspan. '">YIELD PER HECTARE (MT)</th>'; echo "\n";
		}
		echo "</tr>\n";
		echo "<tr>\n";
		if ($prod_count != 0){
			if(in_array(2,$period)){
				echo '<th class="center" colspan="'. $prod_count . '">JAN-JUN</th>'; echo "\n";
				echo '<th class="center" colspan="'. $prod_count . '">JUL-DEC</th>'; echo "\n";
			}
			if(in_array(1,$period)){
				echo '<th class="center" colspan="'. $prod_count . '">JAN-DEC<sup>1</sup></th>'; echo "\n";
			}
		}	
		if ($area_count != 0){
			if(in_array(2,$period)){
				echo '<th class="center" colspan="'. $area_count . '">JAN-JUN</th>'; echo "\n";
				echo '<th class="center" colspan="'. $area_count . '">JUL-DEC</th>'; echo "\n";
			}
			if(in_array(1,$period)){
				echo '<th class="center" colspan="'. $area_count . '">JAN-DEC<sup>1</sup></th>'; echo "\n";
			}
		}	
		if ($yield_count != 0){
			if(in_array(2,$period)){
				echo '<th class="center" colspan="'. $yield_count . '">JAN-JUN</th>'; echo "\n";
				echo '<th class="center" colspan="'. $yield_count . '">JUL-DEC</th>'; echo "\n";
			}
			if(in_array(1,$period)){
				echo '<th class="center" colspan="'. $yield_count . '">JAN-DEC<sup>1</sup></th>'; echo "\n";
			}
		}	
		echo "</tr>\n";
		echo "<tr>\n";
		for($i = 0; $i < array_sum($period) ; $i++){
			if(in_array(6,$categories)){
				echo '<th class="center">TOTAL</th>'; echo "\n";
			}
			if(in_array(7,$categories)){
				echo '<th class="center">% MV</th>'; echo "\n";
			}
		}
		for($i = 0; $i < array_sum($period) ; $i++){
			if(in_array(8,$categories)){
				echo '<th class="center">TOTAL</th>'; echo "\n";
			}
			if(in_array(9,$categories)){
				echo '<th class="center">% MV</th>'; echo "\n";
			}
		}
		for($i = 0; $i < array_sum($period) ; $i++){
			if(in_array(10,$categories)){
				echo '<th class="center">TOTAL</th>'; echo "\n";
			}
			if(in_array(11,$categories)){
				echo '<th class="center">MV</th>'; echo "\n";
			}
			if(in_array(12,$categories)){
				echo '<th class="center">TV</th>'; echo "\n";
			}
		}
		echo "</tr>";
		echo "</thead>\n";
		echo "<tbody>\n";
		foreach ($years as $year) {
			$semester1 = $semester2 = $annual = array();
			echo "<tr>";
			echo "<td>" . $year . "</td>";
			if ($stmt = $conn->prepare("SELECT * FROM stat_production WHERE ecosystem = ? AND year = ? AND location_code = ? AND semester = 1 AND location_type = 1")) {
				$stmt->bind_param("sss", $ecosystem, $year, $region);
				$stmt->execute();
				$stmt->store_result();
				while($row = fetch_get_result_alt($stmt)){
					$semester1 = array_values($row);
				}
			}
			if($stmt->num_rows < 1){
				for($i = 0; $i < 12; $i++){
					array_push($semester1, 0);
				}
			}
			$stmt->close();
			
			if ($stmt = $conn->prepare("SELECT * FROM stat_production WHERE ecosystem = ? AND year = ? AND location_code = ? AND semester = 2 AND location_type = 1")) {
				$stmt->bind_param("sss", $ecosystem, $year, $region);
				$stmt->execute();
				$stmt->store_result();
				while($row = fetch_get_result_alt($stmt)){
					$semester2 = array_values($row);
				}
			}
			if($stmt->num_rows < 1){
				for($i = 0; $i < 12; $i++){
					array_push($semester2, 0);
				}
			}
			$stmt->close();
            	
			if(in_array(1,$period)){
				foreach($categories as $category){
                    if($category == 10 || $category == 11 || $category == 12){
						$divisor = !empty($semester1[$category-1]) + !empty($semester2[$category-1]);
						if($divisor == 0) { $divisor == 1; }
                        $annual[$category-1] = (($semester1[$category-1] + $semester2[$category-1]) / $divisor);
                    }
                    else {
					   $annual[$category-1] = (($semester1[$category-1] + $semester2[$category-1]));
                    }
				}
			}
			
			$prod_array = $area_array = $yield_array = array();
			
			if(in_array(6,$categories)){
				array_push($prod_array, 5);
			}
			if(in_array(7,$categories)){
				array_push($prod_array, 6);
			}
			
			if(in_array(2,$period)){
				for($i = 0; $i < count($prod_array); $i++){
					if($semester1[$prod_array[$i]] > 0 && is_numeric ($semester1[$prod_array[$i]])){
						echo "<td>" . number_format($semester1[$prod_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
				for($i = 0; $i < count($prod_array); $i++){
					if($semester2[$prod_array[$i]] > 0 && is_numeric ($semester2[$prod_array[$i]])){
						echo "<td>" . number_format($semester2[$prod_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			
			if(in_array(1,$period)){
				for($i = 0; $i < count($prod_array); $i++){
					if($annual[$prod_array[$i]] > 0 && is_numeric ($annual[$prod_array[$i]])){
						echo "<td>" . number_format($annual[$prod_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			
			if(in_array(8,$categories)){
				array_push($area_array, 7);
			}
			if(in_array(9,$categories)){
				array_push($area_array, 8);
			}
			
			if(in_array(2,$period)){
				for($i = 0; $i < count($area_array); $i++){
					if($semester1[$area_array[$i]] > 0 && is_numeric ($semester1[$area_array[$i]])){
						echo "<td>" . number_format($semester1[$area_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
				for($i = 0; $i < count($area_array); $i++){
					if($semester2[$area_array[$i]] > 0 && is_numeric ($semester2[$area_array[$i]])){
						echo "<td>" . number_format($semester2[$area_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			
			if(in_array(1,$period)){
				for($i = 0; $i < count($area_array); $i++){
					if($annual[$area_array[$i]] > 0 && is_numeric ($annual[$area_array[$i]])){
						echo "<td>" . number_format($annual[$area_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			
			if(in_array(10,$categories)){
				array_push($yield_array, 9);
			}
			if(in_array(11,$categories)){
				array_push($yield_array, 10);
			}
			if(in_array(12,$categories)){
				array_push($yield_array, 11);
			}
			
			if(in_array(2,$period)){
				for($i = 0; $i < count($yield_array); $i++){
					if($semester1[$yield_array[$i]] > 0 && is_numeric($semester1[$yield_array[$i]])){
						echo "<td>" . number_format($semester1[$yield_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
				for($i = 0; $i < count($yield_array); $i++){
					if($semester2[$yield_array[$i]] > 0 && is_numeric ($semester2[$yield_array[$i]])){
						echo "<td>" . number_format($semester2[$yield_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			
			if(in_array(1,$period)){
				for($i = 0; $i < count($yield_array); $i++){
					if($annual[$yield_array[$i]] > 0 && is_numeric ($annual[$yield_array[$i]])){
						echo "<td>" . number_format($annual[$yield_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
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
}
## PROVINCES
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

	foreach ($ecosystems as $ecosystem) {
		$ecoLabel = "";
		switch ($ecosystem){
			case 0: $ecoLabel = "RAINFED"; break;
			case 1: $ecoLabel = "IRRIGATED"; break;
			case 2: $ecoLabel = "ALL ECOSYSTEMS"; break;
			case 3: $ecoLabel = "UPLAND"; break;
		}
		echo "<h4>". $ecoLabel ."</h4>";
		echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
		echo "<thead>\n";
		echo "<tr>\n";
		echo '<th class="center" rowspan="3">YEAR</th>'; echo "\n";
		if(in_array(6,$categories) || in_array(7,$categories)){
			$colspan = $prod_count * array_sum($period);
			echo '<th class="center" colspan="' . $colspan. '">PRODUCTION (MT)</th>'; echo "\n";
		}
		if(in_array(8,$categories) || in_array(9,$categories)){
			$colspan = $area_count * array_sum($period);
			echo '<th class="center" colspan="' . $colspan. '">AREA HARVESTED (HA)</th>'; echo "\n";
		}
		if(in_array(10,$categories) || in_array(11,$categories) || in_array(12,$categories)){
			$colspan = $yield_count * array_sum($period);
			echo '<th class="center" colspan="'. $colspan. '">YIELD PER HECTARE (MT)</th>'; echo "\n";
		}
		echo "</tr>\n";
		echo "<tr>\n";
		if ($prod_count != 0){
			if(in_array(2,$period)){
				echo '<th class="center" colspan="'. $prod_count . '">JAN-JUN</th>'; echo "\n";
				echo '<th class="center" colspan="'. $prod_count . '">JUL-DEC</th>'; echo "\n";
			}
			if(in_array(1,$period)){
				echo '<th class="center" colspan="'. $prod_count . '">JAN-DEC<sup>1</sup></th>'; echo "\n";
			}
		}	
		if ($area_count != 0){
			if(in_array(2,$period)){
				echo '<th class="center" colspan="'. $area_count . '">JAN-JUN</th>'; echo "\n";
				echo '<th class="center" colspan="'. $area_count . '">JUL-DEC</th>'; echo "\n";
			}
			if(in_array(1,$period)){
				echo '<th class="center" colspan="'. $area_count . '">JAN-DEC<sup>1</sup></th>'; echo "\n";
			}
		}	
		if ($yield_count != 0){
			if(in_array(2,$period)){
				echo '<th class="center" colspan="'. $yield_count . '">JAN-JUN</th>'; echo "\n";
				echo '<th class="center" colspan="'. $yield_count . '">JUL-DEC</th>'; echo "\n";
			}
			if(in_array(1,$period)){
				echo '<th class="center" colspan="'. $yield_count . '">JAN-DEC<sup>1</sup></th>'; echo "\n";
			}
		}	
		echo "</tr>\n";
		echo "<tr>\n";
		for($i = 0; $i < array_sum($period) ; $i++){
			if(in_array(6,$categories)){
				echo '<th class="center">TOTAL</th>'; echo "\n";
			}
			if(in_array(7,$categories)){
				echo '<th class="center">% MV</th>'; echo "\n";
			}
		}
		for($i = 0; $i < array_sum($period) ; $i++){
			if(in_array(8,$categories)){
				echo '<th class="center">TOTAL</th>'; echo "\n";
			}
			if(in_array(9,$categories)){
				echo '<th class="center">% MV</th>'; echo "\n";
			}
		}
		for($i = 0; $i < array_sum($period) ; $i++){
			if(in_array(10,$categories)){
				echo '<th class="center">TOTAL</th>'; echo "\n";
			}
			if(in_array(11,$categories)){
				echo '<th class="center">MV</th>'; echo "\n";
			}
			if(in_array(12,$categories)){
				echo '<th class="center">TV</th>'; echo "\n";
			}
		}
		echo "</tr>";
		echo "</thead>\n";
		echo "<tbody>\n";
		foreach ($years as $year) {
			$semester1 = $semester2 = $annual = array();
			echo "<tr>";
			echo "<td>" . $year . "</td>";
			if ($stmt = $conn->prepare("SELECT * FROM stat_production WHERE ecosystem = ? AND year = ? AND location_code = ? AND semester = 1 AND location_type = 2")) {
				$stmt->bind_param("sss", $ecosystem, $year, $province);
				$stmt->execute();
				$stmt->store_result();
				while($row = fetch_get_result_alt($stmt)){
					$semester1 = array_values($row);
				}
			}
			if($stmt->num_rows < 1){
				for($i = 0; $i < 12; $i++){
					array_push($semester1, 0);
				}
			}
			$stmt->close();
			
			if ($stmt = $conn->prepare("SELECT * FROM stat_production WHERE ecosystem = ? AND year = ? AND location_code = ? AND semester = 2 AND location_type = 2")) {
				$stmt->bind_param("sss", $ecosystem, $year, $province);
				$stmt->execute();
				$stmt->store_result();
				while($row = fetch_get_result_alt($stmt)){
					$semester2 = array_values($row);
				}
			}
			if($stmt->num_rows < 1){
				for($i = 0; $i < 12; $i++){
					array_push($semester2, 0);
				}
			}
			$stmt->close();
			
			if(in_array(1,$period)){
				foreach($categories as $category){
                    if($category == 10 || $category == 11 || $category == 12){
						$divisor = !empty($semester1[$category-1]) + !empty($semester2[$category-1]);
						if($divisor == 0) { $divisor == 1; }
                        $annual[$category-1] = (($semester1[$category-1] + $semester2[$category-1]) / $divisor);
                    }
                    else {
					   $annual[$category-1] = (($semester1[$category-1] + $semester2[$category-1]));
                    }
				}
			}
			
			$prod_array = $area_array = $yield_array = array();
			
			if(in_array(6,$categories)){
				array_push($prod_array, 5);
			}
			if(in_array(7,$categories)){
				array_push($prod_array, 6);
			}
			
			if(in_array(2,$period)){
				for($i = 0; $i < count($prod_array); $i++){
					if($semester1[$prod_array[$i]] > 0 && is_numeric ($semester1[$prod_array[$i]])){
						echo "<td>" . number_format($semester1[$prod_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
				for($i = 0; $i < count($prod_array); $i++){
					if($semester2[$prod_array[$i]] > 0 && is_numeric ($semester2[$prod_array[$i]])){
						echo "<td>" . number_format($semester2[$prod_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			
			if(in_array(1,$period)){
				for($i = 0; $i < count($prod_array); $i++){
					if($annual[$prod_array[$i]] > 0 && is_numeric ($annual[$prod_array[$i]])){
						echo "<td>" . number_format($annual[$prod_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			
			if(in_array(8,$categories)){
				array_push($area_array, 7);
			}
			if(in_array(9,$categories)){
				array_push($area_array, 8);
			}
			
			if(in_array(2,$period)){
				for($i = 0; $i < count($area_array); $i++){
					if($semester1[$area_array[$i]] > 0 && is_numeric ($semester1[$area_array[$i]])){
						echo "<td>" . number_format($semester1[$area_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
				for($i = 0; $i < count($area_array); $i++){
					if($semester2[$area_array[$i]] > 0 && is_numeric ($semester2[$area_array[$i]])){
						echo "<td>" . number_format($semester2[$area_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			
			if(in_array(1,$period)){
				for($i = 0; $i < count($area_array); $i++){
					if($annual[$area_array[$i]] > 0 && is_numeric ($annual[$area_array[$i]])){
						echo "<td>" . number_format($annual[$area_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			
			if(in_array(10,$categories)){
				array_push($yield_array, 9);
			}
			if(in_array(11,$categories)){
				array_push($yield_array, 10);
			}
			if(in_array(12,$categories)){
				array_push($yield_array, 11);
			}
			
			if(in_array(2,$period)){
				for($i = 0; $i < count($yield_array); $i++){
					if($semester1[$yield_array[$i]] > 0 && is_numeric($semester1[$yield_array[$i]])){
						echo "<td>" . number_format($semester1[$yield_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
				for($i = 0; $i < count($yield_array); $i++){
					if($semester2[$yield_array[$i]] > 0 && is_numeric ($semester2[$yield_array[$i]])){
						echo "<td>" . number_format($semester2[$yield_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			
			if(in_array(1,$period)){
				for($i = 0; $i < count($yield_array); $i++){
					if($annual[$yield_array[$i]] > 0 && is_numeric ($annual[$yield_array[$i]])){
						echo "<td>" . number_format($annual[$yield_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
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
}
## CITIES
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

	foreach ($ecosystems as $ecosystem) {
		$ecoLabel = "";
		switch ($ecosystem){
			case 0: $ecoLabel = "RAINFED"; break;
			case 1: $ecoLabel = "IRRIGATED"; break;
			case 2: $ecoLabel = "ALL ECOSYSTEMS"; break;
			case 3: $ecoLabel = "UPLAND"; break;
		}
		echo "<h4>". $ecoLabel ."</h4>";
		echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
		echo "<thead>\n";
		echo "<tr>\n";
		echo '<th class="center" rowspan="3">YEAR</th>'; echo "\n";
		if(in_array(6,$categories) || in_array(7,$categories)){
			$colspan = $prod_count * array_sum($period);
			echo '<th class="center" colspan="' . $colspan. '">PRODUCTION (MT)</th>'; echo "\n";
		}
		if(in_array(8,$categories) || in_array(9,$categories)){
			$colspan = $area_count * array_sum($period);
			echo '<th class="center" colspan="' . $colspan. '">AREA HARVESTED (HA)</th>'; echo "\n";
		}
		if(in_array(10,$categories) || in_array(11,$categories) || in_array(12,$categories)){
			$colspan = $yield_count * array_sum($period);
			echo '<th class="center" colspan="'. $colspan. '">YIELD PER HECTARE (MT)</th>'; echo "\n";
		}
		echo "</tr>\n";
		echo "<tr>\n";
		if ($prod_count != 0){
			if(in_array(2,$period)){
				echo '<th class="center" colspan="'. $prod_count . '">JAN-JUN</th>'; echo "\n";
				echo '<th class="center" colspan="'. $prod_count . '">JUL-DEC</th>'; echo "\n";
			}
			if(in_array(1,$period)){
				echo '<th class="center" colspan="'. $prod_count . '">JAN-DEC<sup>1</sup></th>'; echo "\n";
			}
		}	
		if ($area_count != 0){
			if(in_array(2,$period)){
				echo '<th class="center" colspan="'. $area_count . '">JAN-JUN</th>'; echo "\n";
				echo '<th class="center" colspan="'. $area_count . '">JUL-DEC</th>'; echo "\n";
			}
			if(in_array(1,$period)){
				echo '<th class="center" colspan="'. $area_count . '">JAN-DEC<sup>1</sup></th>'; echo "\n";
			}
		}	
		if ($yield_count != 0){
			if(in_array(2,$period)){
				echo '<th class="center" colspan="'. $yield_count . '">JAN-JUN</th>'; echo "\n";
				echo '<th class="center" colspan="'. $yield_count . '">JUL-DEC</th>'; echo "\n";
			}
			if(in_array(1,$period)){
				echo '<th class="center" colspan="'. $yield_count . '">JAN-DEC<sup>1</sup></th>'; echo "\n";
			}
		}	
		echo "</tr>\n";
		echo "<tr>\n";
		for($i = 0; $i < array_sum($period) ; $i++){
			if(in_array(6,$categories)){
				echo '<th class="center">TOTAL</th>'; echo "\n";
			}
			if(in_array(7,$categories)){
				echo '<th class="center">% MV</th>'; echo "\n";
			}
		}
		for($i = 0; $i < array_sum($period) ; $i++){
			if(in_array(8,$categories)){
				echo '<th class="center">TOTAL</th>'; echo "\n";
			}
			if(in_array(9,$categories)){
				echo '<th class="center">% MV</th>'; echo "\n";
			}
		}
		for($i = 0; $i < array_sum($period) ; $i++){
			if(in_array(10,$categories)){
				echo '<th class="center">TOTAL</th>'; echo "\n";
			}
			if(in_array(11,$categories)){
				echo '<th class="center">MV</th>'; echo "\n";
			}
			if(in_array(12,$categories)){
				echo '<th class="center">TV</th>'; echo "\n";
			}
		}
		echo "</tr>";
		echo "</thead>\n";
		echo "<tbody>\n";
		foreach ($years as $year) {
			$semester1 = $semester2 = $annual = array();
			echo "<tr>";
			echo "<td>" . $year . "</td>";
			if ($stmt = $conn->prepare("SELECT * FROM stat_production WHERE ecosystem = ? AND year = ? AND location_code = ? AND semester = 1 AND location_type = 3")) {
				$stmt->bind_param("sss", $ecosystem, $year, $city);
				$stmt->execute();
				$stmt->store_result();
				while($row = fetch_get_result_alt($stmt)){
					$semester1 = array_values($row);
				}
			}
			if($stmt->num_rows < 1){
				for($i = 0; $i < 12; $i++){
					array_push($semester1, 0);
				}
			}
			$stmt->close();
			
			if ($stmt = $conn->prepare("SELECT * FROM stat_production WHERE ecosystem = ? AND year = ? AND location_code = ? AND semester = 2 AND location_type = 3")) {
				$stmt->bind_param("sss", $ecosystem, $year, $city);
				$stmt->execute();
				$stmt->store_result();
				while($row = fetch_get_result_alt($stmt)){
					$semester2 = array_values($row);
				}
			}
			if($stmt->num_rows < 1){
				for($i = 0; $i < 12; $i++){
					array_push($semester2, 0);
				}
			}
			$stmt->close();
			
			if(in_array(1,$period)){
				foreach($categories as $category){
                    if($category == 10 || $category == 11 || $category == 12){
						$divisor = !empty($semester1[$category-1]) + !empty($semester2[$category-1]);
						if($divisor == 0) { $divisor == 1; }
                        $annual[$category-1] = (($semester1[$category-1] + $semester2[$category-1]) / $divisor);
                    }
                    else {
					   $annual[$category-1] = (($semester1[$category-1] + $semester2[$category-1]));
                    }
				}
			}
			
			$prod_array = $area_array = $yield_array = array();
			
			if(in_array(6,$categories)){
				array_push($prod_array, 5);
			}
			if(in_array(7,$categories)){
				array_push($prod_array, 6);
			}
			
			if(in_array(2,$period)){
				for($i = 0; $i < count($prod_array); $i++){
					if($semester1[$prod_array[$i]] > 0 && is_numeric ($semester1[$prod_array[$i]])){
						echo "<td>" . number_format($semester1[$prod_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
				for($i = 0; $i < count($prod_array); $i++){
					if($semester2[$prod_array[$i]] > 0 && is_numeric ($semester2[$prod_array[$i]])){
						echo "<td>" . number_format($semester2[$prod_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			
			if(in_array(1,$period)){
				for($i = 0; $i < count($prod_array); $i++){
					if($annual[$prod_array[$i]] > 0 && is_numeric ($annual[$prod_array[$i]])){
						echo "<td>" . number_format($annual[$prod_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			
			if(in_array(8,$categories)){
				array_push($area_array, 7);
			}
			if(in_array(9,$categories)){
				array_push($area_array, 8);
			}
			
			if(in_array(2,$period)){
				for($i = 0; $i < count($area_array); $i++){
					if($semester1[$area_array[$i]] > 0 && is_numeric ($semester1[$area_array[$i]])){
						echo "<td>" . number_format($semester1[$area_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
				for($i = 0; $i < count($area_array); $i++){
					if($semester2[$area_array[$i]] > 0 && is_numeric ($semester2[$area_array[$i]])){
						echo "<td>" . number_format($semester2[$area_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			
			if(in_array(1,$period)){
				for($i = 0; $i < count($area_array); $i++){
					if($annual[$area_array[$i]] > 0 && is_numeric ($annual[$area_array[$i]])){
						echo "<td>" . number_format($annual[$area_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			
			if(in_array(10,$categories)){
				array_push($yield_array, 9);
			}
			if(in_array(11,$categories)){
				array_push($yield_array, 10);
			}
			if(in_array(12,$categories)){
				array_push($yield_array, 11);
			}
			
			if(in_array(2,$period)){
				for($i = 0; $i < count($yield_array); $i++){
					if($semester1[$yield_array[$i]] > 0 && is_numeric($semester1[$yield_array[$i]])){
						echo "<td>" . number_format($semester1[$yield_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
				for($i = 0; $i < count($yield_array); $i++){
					if($semester2[$yield_array[$i]] > 0 && is_numeric ($semester2[$yield_array[$i]])){
						echo "<td>" . number_format($semester2[$yield_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
					}
				}
			}
			
			if(in_array(1,$period)){
				for($i = 0; $i < count($yield_array); $i++){
					if($annual[$yield_array[$i]] > 0 && is_numeric ($annual[$yield_array[$i]])){
						echo "<td>" . number_format($annual[$yield_array[$i]], 2). "</td>";
					}
					else{
						echo "<td>-</td>";
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