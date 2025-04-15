<?php
require_once("../includes/conn.php");
require_once("../includes/global_functions.php");
require_once("../includes/HTMLPurifier.standalone.php");

$base = "palaystat";
$GLOBALS['hname'] = $_SERVER['HTTP_HOST'];
$GLOBALS['hname'] = $GLOBALS['hname']."/".$base;
?>
<div class="btn-group">
  <button id="download" type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <span class="glyphicon glyphicon-download-alt" aria-hidden="true"></span> Download <span class="caret"></span>
  </button>
  <ul class="dropdown-menu">
    <li><a id="xlsx" class="dropdown-item" href="#">Excel</a></li>
    <li><a id="ods" class="dropdown-item" href="#">OpenDocument</a></li>
    <li><a id="csv" class="dropdown-item" href="#">CSV</a></li>
    <div class="dropdown-divider"></div>
    <li><a id="pdf" class="dropdown-item" href="#">PDF</a></li>
    <li><a id="html" class="dropdown-item" href="#">HTML</a></li>
  </ul>
</div>
<button id="print" type="button" class="btn btn-primary" aria-expanded="false">
	<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Print
</button>