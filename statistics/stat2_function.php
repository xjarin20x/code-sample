<?php
	require_once("../includes/headeralt.php");
?>	
<div id="tableData">
<?php
$regions = $provinces = $cities = $period = $years = array();
$region_count = $province_count = $city_count = $period_count = $year_count = 0;

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

if(isset($_POST['period'])){
	$period = disinfect_var($_POST['period']);
	$period_count = count($period);
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
		foreach ($period as $p){
			switch($p){
				case 2: $list = periodArray(2); break;
				case 3: $list = periodArray(3); break;
				case 4: $list = periodArray(4); break;
			}
			foreach ($list as $timerange){
				echo '<th class="center">'. strtoupper($timerange) . '</th>'; echo "\n";	
			}
		}
		echo "</tr>\n";
		echo "</thead>\n";
		echo "<tbody>\n";
		foreach ($years as $year) {
			echo "<tr>";
			echo "<td>" . $year . "</td>";
			$monthly = array();
			if ($stmt = $conn->prepare("SELECT percent_production FROM stat_distribution WHERE year = ? AND location_code = ? AND location_type = 1 ORDER BY (month) ASC")) {
				$stmt->bind_param("ss", $year, $region);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($month);
				while($stmt->fetch()){
					array_push($monthly, $month);
				}
			}
			if($stmt->num_rows < 1){
				for($i = 0; $i < 12; $i++){
					array_push($monthly, 0);
				}
			}
			$stmt->close();

			foreach ($period as $p){
				switch($p){
					case 2: {
						$halfyear = array_chunk($monthly, 6);
						for($i = 0; $i < count($halfyear); $i++){
							$halfyear[$i] = array_sum($halfyear[$i]);
							if($halfyear[$i] > 0){
								echo "<td>" . number_format($halfyear[$i], 2). "</td>";
							}
							else{
								echo "<td>-</td>";
							}
						}
						break;
					}
					case 3: {
						$quarterly = array_chunk($monthly, 3);
						for($i = 0; $i < count($quarterly); $i++){
							$quarterly[$i] = array_sum($quarterly[$i]);
							if($quarterly[$i] > 0){
								echo "<td>" . number_format($quarterly[$i], 2). "</td>";
							}
							else{
								echo "<td>-</td>";
							}
						}
						break;
					}
					case 4: {
						foreach($monthly as $month){
							if($month > 0){
								echo "<td>" . number_format($month, 2). "</td>";
							}
							else{
								echo "<td>-</td>";
							}
						}
						break;
					}
				}
			}
			echo "</tr>";
		}
		echo "</tbody>\n";
		echo "</table>\n";
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

		echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
		echo "<thead>\n";
		echo "<tr>\n";
		echo '<th class="center">YEAR</th>'; echo "\n";
		foreach ($period as $p){
			switch($p){
				case 1: $list = periodArray(1); break;
				case 2: $list = periodArray(2); break;
				case 3: $list = periodArray(3); break;
				case 4: $list = periodArray(4); break;
			}
			foreach ($list as $timerange){
				echo '<th class="center">'. strtoupper($timerange) . '</th>'; echo "\n";	
			}
		}
		echo "</tr>\n";
		echo "</thead>\n";
		echo "<tbody>\n";
		foreach ($years as $year) {
			echo "<tr>";
			echo "<td>" . $year . "</td>";
			$monthly = array();
			if ($stmt = $conn->prepare("SELECT percent_production FROM stat_distribution WHERE year = ? AND location_code = ? AND location_type = 2 ORDER BY (month) ASC")) {
				$stmt->bind_param("ss", $year, $province);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($month);
				while($stmt->fetch()){
					array_push($monthly, $month);
				}
			}
			if($stmt->num_rows < 1){
				for($i = 0; $i < 12; $i++){
					array_push($monthly, 0);
				}
			}
			$stmt->close();

			foreach ($period as $p){
				switch($p){
					case 2: {
						$halfyear = array_chunk($monthly, 6);
						for($i = 0; $i < count($halfyear); $i++){
							$halfyear[$i] = array_sum($halfyear[$i]);
							if($halfyear[$i] > 0){
								echo "<td>" . number_format($halfyear[$i], 2). "</td>";
							}
							else{
								echo "<td>-</td>";
							}
						}
						break;
					}
					case 3: {
						$quarterly = array_chunk($monthly, 3);
						for($i = 0; $i < count($quarterly); $i++){
							$quarterly[$i] = array_sum($quarterly[$i]);
							if($quarterly[$i] > 0){
								echo "<td>" . number_format($quarterly[$i], 2). "</td>";
							}
							else{
								echo "<td>-</td>";
							}
						}
						break;
					}
					case 4: {
						foreach($monthly as $month){
							if($month > 0){
								echo "<td>" . number_format($month, 2). "</td>";
							}
							else{
								echo "<td>-</td>";
							}
						}
						break;
					}
				}
			}
			echo "</tr>";
		}
		echo "</tbody>\n";
		echo "</table>\n";
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

		echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
		echo "<thead>\n";
		echo "<tr>\n";
		echo '<th class="center">YEAR</th>'; echo "\n";
		foreach ($period as $p){
			switch($p){
				case 2: $list = periodArray(2); break;
				case 3: $list = periodArray(3); break;
				case 4: $list = periodArray(4); break;
			}
			foreach ($list as $timerange){
				echo '<th class="center">'. strtoupper($timerange) . '</th>'; echo "\n";	
			}
		}
		echo "</tr>\n";
		echo "</thead>\n";
		echo "<tbody>\n";
		foreach ($years as $year) {
			echo "<tr>";
			echo "<td>" . $year . "</td>";
			$monthly = array();
			if ($stmt = $conn->prepare("SELECT percent_production FROM stat_distribution WHERE year = ? AND location_code = ? AND location_type = 3 ORDER BY (month) ASC")) {
				$stmt->bind_param("ss", $year, $city);
				$stmt->execute();
				$stmt->store_result();
				$stmt->bind_result($month);
				while($stmt->fetch()){
					array_push($monthly, $month);
				}
			}
			if($stmt->num_rows < 1){
				for($i = 0; $i < 12; $i++){
					array_push($monthly, 0);
				}
			}
			$stmt->close();

			foreach ($period as $p){
				switch($p){
					case 2: {
						$halfyear = array_chunk($monthly, 6);
						for($i = 0; $i < count($halfyear); $i++){
							$halfyear[$i] = array_sum($halfyear[$i]);
							if($halfyear[$i] > 0){
								echo "<td>" . number_format($halfyear[$i], 2). "</td>";
							}
							else{
								echo "<td>-</td>";
							}
						}
						break;
					}
					case 3: {
						$quarterly = array_chunk($monthly, 3);
						for($i = 0; $i < count($quarterly); $i++){
							$quarterly[$i] = array_sum($quarterly[$i]);
							if($quarterly[$i] > 0){
								echo "<td>" . number_format($quarterly[$i], 2). "</td>";
							}
							else{
								echo "<td>-</td>";
							}
						}
						break;
					}
					case 4: {
						foreach($monthly as $month){
							if($month > 0){
								echo "<td>" . number_format($month, 2). "</td>";
							}
							else{
								echo "<td>-</td>";
							}
						}
						break;
					}
				}
			}
			echo "</tr>";
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