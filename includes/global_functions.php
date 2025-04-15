<?php
function disinfect_var($var) {
	if(is_array($var)){
		$var = array_map('stripslashes', $var);
		$var = array_map('strip_tags', $var);
		return $var;
	}
	else{
		$var = stripslashes($var);
		$var = strip_tags($var);
		return $var;
	}
}

function notMSIE() {
	$userAgent = strtolower($_SERVER["HTTP_USER_AGENT"]);
	if (preg_match("/msie/", $userAgent)) {
		return false;
	}
	return true;
}

function isMobile($useragent) {
   return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4));
}

function check_array($arr, $basis, $multiplier, $filler) {
$j = count($basis) * $multiplier;
for($i=0;$i<$j;$i++){
	if(!isset( $arr[$i] )){
		$arr[$i] = $filler;
	}
}
return $arr;
}

function extractString($string, $start, $end) {
    $string = " ".$string;
    $ini = strpos($string, $start);
    if ($ini == 0) return "";
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function concatenate($elements, $delimiter = ', ', $finalDelimiter = ' and ') {
    $lastElement = array_pop($elements);
	if(count($elements) == 0){
		return $lastElement;
	}
	else{
		return join($delimiter, $elements) . $finalDelimiter . $lastElement;
	}
}

// functions for PHP 2.8 and below
function fetch_get_result_alt($statement){
    if($statement->num_rows > 0){
        $result = array();
        $metadata = $statement->result_metadata();
		
        $paramaters = array();
		
        while($field = $metadata->fetch_field()) {
            $paramaters[] = &$result[$field->name];
        }
        call_user_func_array(array($statement, 'bind_result'), $paramaters);
		
        if($statement->fetch()){
            return $result;
		}
    }
	return null;
}

function strstr_alt($haystack, $needle, $beforeNeedle = false) {
	$needlePosition = strpos($haystack, $needle);
	if ($needlePosition === false) {
		return false;
	}
	if ($beforeNeedle) {
		return substr($haystack, 0, $needlePosition);
	}
	else {
		return substr($haystack, $needlePosition);
	}
}
	
// end functions

function create_header($title, $col_title, $span) {
echo "<thead>\n";
echo "<tr>\n";
echo "<th class='header'>".$title."</th>\n";
for($i=0;$i<count($col_title);$i++){
	echo "<th class='header_span' colspan='".$span."'>".$col_title[$i]."</th>\n";
}
echo "</tr>\n";
echo "</thead>\n";
}

function create_span($text, $span) {
echo "<thead>\n";
echo "<tr>\n";
echo "<th class='header_span' colspan='".$span."'>".$text."</th>\n";
echo "</tr>\n";
echo "</thead>\n";
}

function create_special_header($title, $col_title, $span) {
echo "<thead>\n";
echo "<tr>\n";
echo "<th class='header_span' colspan='".$span."'>".$title."</th>\n";
for($i=0;$i<count($col_title);$i++){
	echo "<th class='header_span' colspan='".$span."'>".$col_title[$i]."</th>\n";
}
echo "</tr>\n";
echo "</thead>\n";
}

function create_special_rows($title, $col_title, $span) {
echo "<tr>\n";
echo "<td class='left bold'>".$title."</td>\n";
for($i=0;$i<count($col_title);$i++){
	echo "<td class='center bold' colspan='".$span."'>".$col_title[$i]."</td>\n";
}
echo "</tr>\n";
}

function create_special_numbers($title, $col_data, $span, $decimal) {
echo "<tr>\n";
echo "<td class='left'>".$title."</td>\n";
for($i=0;$i<count($col_data);$i++){
	if (is_numeric($col_data[$i])) {
        echo "<td class='center' colspan='".$span."'>".number_format((float)$col_data[$i], $decimal, '.', '')."</td>\n";
    } 
	else{
		echo "<td class='center' colspan='".$span."'>".$col_data[$i]."</td>\n";
	}
}
echo "</tr>\n";
}

function create_humanized_special_numbers($title, $col_data, $span, $decimal) {
echo "<tr>\n";
echo "<td class='left'>".$title."</td>\n";
for($i=0;$i<count($col_data);$i++){
	if (is_numeric($col_data[$i])) {
        echo "<td class='center' colspan='".$span."'>".number_format((float)$col_data[$i], $decimal, '.', ',')."</td>\n";
    } 
	else{
		echo "<td class='center' colspan='".$span."'>".$col_data[$i]."</td>\n";
	}
}
echo "</tr>\n";
}
	
function create_headings($title, $col_title) {
echo "<tr>\n";
echo "<td class='left bold'>".$title."</td>\n";
for($i=0;$i<count($col_title);$i++){
	echo "<td class='center bold'>".$col_title[$i]."</td>\n";
}
echo "</tr>\n";
}

function create_spanned_headings($title, $col_title, $span) {
echo "<tr>\n";
echo "<td class='bold center' colspan='".$span."'>".$title."</td>\n";
for($i=0;$i<count($col_title);$i++){
	echo "<td class='bold center' colspan='".$span."'>".$col_title[$i]."</td>\n";
}
echo "</tr>\n";
}
	
function create_row($title, $col_data, $decimal) {
echo "<tr>\n";
echo "<td class='left'>".$title."</td>\n";
for($i=0;$i<count($col_data);$i++){
	if (is_numeric($col_data[$i])) {
        echo "<td class='right'>".round($col_data[$i], $decimal)."</td>\n";
    } 
	else{
		echo "<td class='right'>".$col_data[$i]."</td>\n";
	}
}
echo "</tr>\n";
}

function create_row_left($title, $col_data, $decimal) {
echo "<tr>\n";
echo "<td class='left'>".$title."</td>\n";
for($i=0;$i<count($col_data);$i++){
	if (is_numeric($col_data[$i])) {
        echo "<td class='left'>".number_format((float)$col_data[$i], 2, '.', '')."</td>\n";
    } 
	else{
		echo "<td class='left'>".$col_data[$i]."</td>\n";
	}
}
echo "</tr>\n";
}

function create_row_bold($title, $col_data, $decimal) {
echo "<tr>\n";
echo "<td class='left bold'>".$title."</td>\n";
for($i=0;$i<count($col_data);$i++){
	if (is_numeric($col_data[$i])) {
        echo "<td class='bold'>".round($col_data[$i], $decimal)."</td>\n";
    } 
	else{
		echo "<td class='bold left'>".$col_data[$i]."</td>\n";
	}
}
echo "</tr>\n";
}

function create_row_custom($title, $col_data, $decimal, $class) {
echo "<tr>\n";
echo "<td class='left ". $class ."'>".$title."</td>\n";
for($i=0;$i<count($col_data);$i++){
	if (is_numeric($col_data[$i])) {
        echo "<td class='". $class ."'>".round($col_data[$i], $decimal)."</td>\n";
    } 
	else{
		echo "<td class='". $class ."'>".$col_data[$i]."</td>\n";
	}
}
echo "</tr>\n";
}

function create_numbers($title, $col_data, $decimal) {
echo "<tr>\n";
echo "<td class='left'>".$title."</td>\n";
for($i=0;$i<count($col_data);$i++){
	if (is_numeric($col_data[$i])) {
		echo "<td class='right'>".number_format((float)$col_data[$i], $decimal, '.', '')."</td>\n";
    } 
	else{
		echo "<td class='right'>".$col_data[$i]."</td>\n";
	}
}
echo "</tr>\n";
}

function humanized_number($title, $col_data) {
echo "<tr>\n";
echo "<td class='left'>".$title."</td>\n";
for($i=0;$i<count($col_data);$i++){
	if (is_numeric($col_data[$i])) {
		echo "<td>".number_format($col_data[$i])."</td>\n";
	}
	else{
		echo "<td>".$col_data[$i]."</td>\n";
	}
}
echo "</tr>\n";
}

function humanized_number_bold($title, $col_data) {
echo "<tr>\n";
echo "<td class='left bold'>".$title."</td>\n";
for($i=0;$i<count($col_data);$i++){
	if (is_numeric($col_data[$i])) {
		echo "<td class='bold'>".number_format($col_data[$i])."</td>\n";
	}
	else{
		echo "<td class='bold'>".$col_data[$i]."</td>\n";
	}
}
echo "</tr>\n";
}

function humanized_number_custom($title, $col_data, $class) {
echo "<tr>\n";
echo "<td class='left ". $class ."'>".$title."</td>\n";
for($i=0;$i<count($col_data);$i++){
	if (is_numeric($col_data[$i])) {
		echo "<td class='". $class ."'>".number_format($col_data[$i])."</td>\n";
	}
	else{
		echo "<td class='". $class ."'>".$col_data[$i]."</td>\n";
	}
}
echo "</tr>\n";
}

function create_subheading($title, $col_data, $decimal) {
echo "<tr>\n";
echo "<td class='left bold'>".$title."</td>\n";
for($i=0;$i<count($col_data);$i++){
	echo "<td class='bold'>".round($col_data[$i], $decimal)."</td>\n";
}
echo "</tr>\n";
}

function create_soi($categories, $subcategories, $contents, $basis, $decimal) {
$counter = count($basis) * 3;
foreach ($categories as $key => $value) {
	if( count($contents[$value])  == $counter ){
		//create_subheading($value, $contents[$value], $decimal);
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;".$value, $contents[$value], $decimal);
	}
	else{
		$sumArr = array();
		for($i=0;$i<$counter;$i++){
			if(!isset($contents[$value][$i])){
				$sum = 0;
				foreach ($subcategories[$key] as $pointer){
					if (isset( $contents[$pointer][$i] ) ){
						if (is_numeric( $contents[$pointer][$i] )) {
							$sum = $contents[$pointer][$i] + $sum;
						} 
					}
				}
				array_push($sumArr, $sum);
			}
			else{
				array_push($sumArr, $contents[$value][$i]);
			}
		}
		//create_subheading($value, $sumArr, $decimal);
		create_row("&nbsp;&nbsp;&nbsp;&nbsp;".$value, $sumArr, $decimal);
	}
	/*if(isset($subcategories[$key])){
		foreach ($subcategories[$key] as $pointer){
			create_row("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$pointer, $contents[$pointer], $decimal);
		}
	}*/
}
} 

function create_average($dividend, $divisor, $text, $decimal) {
$tmp = 0;
$tmpArr = array();
for($i=0;$i<count($divisor);$i++){
	if($divisor[$i] == 0 || empty($dividend)){
		array_push($tmpArr, 0);
	}
	else{
		$tmp = ($dividend[$i] / $divisor[$i]) * 100;
		array_push($tmpArr, $tmp);
	}
}
create_row($text, $tmpArr, $decimal);
}

function create_average_span($dividend, $divisor, $text, $decimal, $span) {
$tmp = 0;
$tmpArr = array();
for($i=0;$i<count($divisor);$i++){
	if($divisor[$i] == 0 || empty($dividend)){
		array_push($tmpArr, 0);
	}
	else{
		$tmp = ($dividend[$i] / $divisor[$i]) * 100;
		array_push($tmpArr, $tmp);
	}
}
create_special_numbers($text, $tmpArr, $span, $decimal);
}

function create_average_dash($dividend, $divisor, $text, $decimal) {
$tmp = 0;
$tmpArr = array();
for($i=0;$i<count($divisor);$i++){
	if($divisor[$i] == 0 || empty($dividend)){
		array_push($tmpArr, "-");
	}
	else{
		$tmp = ($dividend[$i] / $divisor[$i]) * 100;
		array_push($tmpArr, $tmp);
	}
}
create_row($text, $tmpArr, $decimal);
}

function create_mixed($rows, $decimal, $content) {
for($i=0;$i<$content;$i++){
	if(!isset($rows[$i])){
		$rows[$i] = "...";
	}
}
ksort($rows);
$text = array_shift($rows);
create_row_left($text, $rows, $decimal);
}

function get_n($arr){
	$temp = "n = (" . number_format(array_sum($arr)) . ")";
	return $temp;
}

function array_formatting($arr, $front, $back){
	for($i=0;$i<count($arr);$i++){
		if (is_numeric($arr[$i])) {
			$arr[$i] = $front . number_format($arr[$i]) . $back;
		}
		else{
			$arr[$i] = $front . $arr[$i] . $back;
		}
	}
	return $arr;
}

function create_total_arr(){
	$numargs = func_num_args();
	$args = func_get_args();
	$total = array();
	for ($i = 0; $i < $numargs; $i++) {
		foreach($args[$i] as $key => $n){
			if(!isset($total[$key])){
				$total[$key] = 0;
			}
			if(is_numeric($n)){
				$temp = $n;
			}
			else{
				$temp = 0;
			}
			$total[$key] = $total[$key] + round($temp, 2);
		}
    }
	return $total;
}

function create_compute_arr(){
	$numargs = func_num_args();
	$args = func_get_args();
	$operator = $args[0];
	$answer = array();
	for ($i = 1; $i < $numargs; $i++) {
		foreach($args[$i] as $key => $n){
			if(is_numeric($n)){
				$temp = $n;
			}
			else{
				$temp = 0;
			}
			switch($operator){
				case "add":
				{
					if(empty($answer[$key])){
						$answer[$key] = 0;
					}
					$answer[$key] = $answer[$key] + $temp;
					break;
				}
				case "subtract":
				{
					if(empty($answer[$key])){
						$answer[$key] = round($temp, 1);
					}
					else{
						$answer[$key] = $answer[$key] - $temp;
					}
					break;
				}
				case "multiply":
				{
					if(empty($answer[$key])){
						$answer[$key] = 1;
					}
					$answer[$key] = $answer[$key] * $temp;
					break;
				}
				case "divide":
				{
					if(empty($answer[$key])){
						$answer[$key] = round($temp, 1);
					}
					else{
						$answer[$key] = $answer[$key] / $temp;
					}
					break;
				}
			}
		}
	}
	return $answer;
}

function compareArray($a, $b) {
	return array_sum($a) < array_sum($b);
}

function displayNoteContact(){
	return "<br/><div><b>Contact Details:</b><br/>\nPalayStat System<br/>\nSocioeconomics Division<br/>\nPhilRice Central Experiment Station<br/>Science City of Muñoz, 3119<br/>\nNueva Ecija, Philippines<br/>\n<b>TRUNKLINES</b>: (044) 456-0285, 0258 local 300, 301</div>\n";
}

function displayNoteRounding(){
	return "<div>Percentages may not total 100% due to rounding.</div>\n";
}

function displayNoteIrrigate(){
	return "<div><b>IRRIGATED</b> - has source of water except rain; <b>NON-IRRIGATED</b> - source of water is rain only.</div>\n";
}

function cacArray($a, $f){
	$temp = 0;
	$counts = array_map('count', $a);
	switch ($f) {
	case "min":
		$temp = min($counts);
		break;
	case "max":
		$temp = max($counts);
		break;	
	}
	return $temp;
}

function text_limit($text, $limit, $append = '&hellip;') {
	if (strlen($text) <= $limit) {
		return $text;
	}
	$return = substr($text, 0, $limit);
	if (strpos($text, ' ') === false) {
		return $return . $append;
	}
	return preg_replace('/\w+$/', '', $return) . $append;
}

function periodArray($period){
	$listing = array();
	switch($period){
		case 1: array_push($listing,"Jan-Dec");
				break;
		case 2: array_push($listing,"Jan-Jun");
				array_push($listing,"Jul-Dec");
				break;
		case 3: array_push($listing,"Jan-Mar");
				array_push($listing,"Apr-Jun");
				array_push($listing,"Jul-Sep");
				array_push($listing,"Oct-Dec");
				break;
		case 4: array_push($listing,"Jan");
				array_push($listing,"Feb");
				array_push($listing,"Mar");
				array_push($listing,"Apr");
				array_push($listing,"May");
				array_push($listing,"Jun");
				array_push($listing,"Jul");
				array_push($listing,"Aug");
				array_push($listing,"Sep");
				array_push($listing,"Oct");
				array_push($listing,"Nov");
				array_push($listing,"Dec");
				break;
	}
	return $listing;
}

function isNavBarActive($filename, $link) {
	if ($link === "downloads" || $link === "about-us"){
		switch ($filename) {
			case 'references/':
			case 'maps/': $filename = "downloads"; break;
			case 'rationale':
			case 'history':
			case 'sampling-method':
			case 'related-links': $filename = "about-us"; break;
			default: break;
		}
	}
	
	if($filename === $link) {
		echo "active";
	}
	else {
		$length = strlen($link);
		if(substr($filename, 0, $length) === $link) {
			echo "active";
		}
	}
}

function XML1_replace($string) {
	$replace = 	array(
		'"'=> "&quot;",
        "&" => "&amp;",
        "'"=> "&apos;",
        "<" => "&lt;",
        ">"=> "&gt;"
    );
	$unclean = strtr($string, $replace);
	return strtr($unclean, $replace);
}
?>