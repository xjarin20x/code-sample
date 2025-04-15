<?php
	require_once("../includes/headeralt.php");
?>	
<div id="tableData">
<?php
$countries = $years = $categories = $annual = array();
$country_count = $year_count = $category_count = 0;

if(isset($_POST['countries'])){
	$countries = disinfect_var($_POST['countries']);
	$country_count = count($countries);
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
if(isset($_POST['countries'])){
	echo '<table class="table table-hover table-condensed table-bordered table-striped text-center table-responsive">';
	echo "<thead>\n";
	echo "<tr>\n";
	echo '<th class="center" rowspan="2">YEAR</th>'; echo "\n";
	foreach ($countries as $country) {
		if ($cstmt = $conn->prepare("SELECT country_name FROM legend_country WHERE iso_code = ?")) {
			$cstmt->bind_param("i", $country);
			$cstmt->execute();
			$cstmt->bind_result($country_name);
			while($cstmt->fetch()){
				echo '<th class="center" colspan="' . $category_count . '">'. strtoupper($country_name) .'</th>'; echo "\n";
			}
			$cstmt->close();				
		}
	}
	echo "</tr>\n"; echo "<tr>\n";
	foreach ($countries as $country) {
		if(in_array(3,$categories)){
			echo '<th class="center">QUANTITY</th>'; echo "\n";
		}
		if(in_array(4,$categories)){
			echo '<th class="center">VALUE</th>'; echo "\n";
		}
	}
	echo "<tr>\n";
	echo "</thead>\n";
	echo "<tbody>\n";
	foreach ($years as $year) {
		echo "<tr>\n";
		echo '<td class="center">' . $year . '</td>'; echo "\n";
		foreach ($countries as $country) {
			if ($stmt = $conn->prepare("SELECT * FROM stat_imports WHERE iso_code = ? AND year = ? LIMIT 1")) {
				$stmt->bind_param("ss", $country, $year);
				$stmt->execute();
				$stmt->store_result();
				while($row = fetch_get_result_alt($stmt)){
					$annual = array_values($row);
				}
			}
			if($stmt->num_rows < 1){
				for($i = 0; $i < 6; $i++){
					array_push($annual, 0);
				}
			}
			if(in_array(3,$categories)){
				if(!empty($annual[2])){
					echo '<td class="right">' . number_format($annual[2], 0) . '</td>'; echo "\n";
				}
				elseif(!empty($annual[4])){
					echo '<td class="right">' . $annual[4] . '</td>'; echo "\n";
				}
				else {
					echo '<td class="center"> - </td>'; echo "\n";
				}
			}
			if(in_array(4,$categories)){
				if(!empty($annual[3])){
					echo '<td class="right">' . number_format($annual[3], 0) . '</td>'; echo "\n";
				}
				elseif(!empty($annual[5])){
					echo '<td class="right">' . $annual[5] . '</td>'; echo "\n";
				}
				else {
					echo '<td class="center"> - </td>'; echo "\n";
				}
			}
		}
		echo "</tr>\n";
	}
	echo "</tbody>\n";
	echo "</table>\n";
}

echo '<p class="footnote">Quantity: \'000 MT; Value: FOB \'000 $</p>
<p class="footnote">- = Data not available</p>
<p class="footnote">Other countries includes Pakistan, Sabah (North Borneo), Italy, Taiwan, France, Hongkong, Netherlands (Hollands), Spain, Canada, Korea, Malaysia and Portugal, Colombia</p>
<p class="footnote">1/ = Donation.</p>
<p class="footnote">2/ = Less than 1,000 MT.</p>
<p class="footnote">3/ = Less than $1,000.</p>';
echo "<br/>";
echo "</div>";
echo "</div>";
?>