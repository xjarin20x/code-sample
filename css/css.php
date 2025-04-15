<?php
header('Content-type: text/css');
header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: must-revalidate");
$offset = 60 * 60 ;
$ExpStr = "Expires: " .
gmdate("D, d M Y H:i:s",
time() + $offset) . " GMT";
header($ExpStr);
ob_start("compress");
 
function compress($buffer) {
	$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
	$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
	return $buffer;
}

include('bootstrap-reboot.min.css');
include('bootstrap.min.css');
include('bootstrap.custom.min.css');
include('bootstrap-icons.css');
include('bootstrap-multiselect.min.css');
include('bootstrap-reboot.min.css');
include('styles.css');

ob_end_flush();
?>