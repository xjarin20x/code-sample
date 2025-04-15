<?php
require_once("includes/conn.php");
require_once("includes/global_functions.php");
require_once("includes/HTMLPurifier.standalone.php");
$config = HTMLPurifier_Config::createDefault();
$purifier = new HTMLPurifier($config);
// $prepath = checksubdomain(dirname(__FILE__), $conn, $purifier);
// PHP 5.3
//$prepath = checksubdomain(__DIR__, $conn, $purifier);
date_default_timezone_set('Asia/Manila');
//captcha stuff
$captcha = "";
$iamhuman = false;

if(!empty($_POST['g-recaptcha-response'])) {
    $captcha = disinfect_var($_POST['g-recaptcha-response']);
    $captcha = $purifier->purify($captcha);
    
    // google phone home using file_get_contents
    $secretKey = "6Lc65vUZAAAAAALVsl0_S6TuOQuVs520QNlpzfbg";
    $ip = $_SERVER['REMOTE_ADDR'];
    
    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array('secret' => $secretKey, 'response' => $captcha);
    
    $options = array(
            'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded",
            'method'  => 'POST',
            'content' => http_build_query($data),
            'verify_peer' => false,         // for HTTPS
            'verify_peer_name' => false     // for HTTPS
        )
    );
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    $gkeys = json_decode($response, true);
    
    //end
    
    // google phone home
    /**
    $data = array('secret' => '6Lc65vUZAAAAAALVsl0_S6TuOQuVs520QNlpzfbg', 'response' => $captcha);
    $curlConfig = array(
        CURLOPT_URL => 'https://www.google.com/recaptcha/api/siteverify',
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_REFERER => 'http://palaystat.philrice.gov.ph/',
        CURLOPT_USERAGENT => 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)'
    );
    $ch = curl_init();
    curl_setopt_array($ch, $curlConfig);
    $response = curl_exec($ch);
    curl_close($ch);
    */
    // end curl
    $gkeys = json_decode($response, true); 
    if ($gkeys['success'] === true && $gkeys['action'] == "submit_feedback" && $gkeys['score'] >= 0.5 && $gkeys['hostname'] == $_SERVER['SERVER_NAME']) {
        $iamhuman = true; 
    }
}

if(count($_POST) === 6 && $iamhuman){
    $a = $b = $c = $d = $e = "";
    $f = false;
    $e = date("m-d-Y | H:i:s"); 
    if(!empty($_POST['purpose'])) {
        $reason = disinfect_var($_POST['purpose']);
        $reason = $purifier->purify($reason);
        $b = intval($reason);
    }
    if(!empty($_POST['rate-use'])) {
        $rating = disinfect_var($_POST['rate-use']);
        $rating = $purifier->purify($rating);
        $c = intval($rating);
    }
    if(!empty($_POST['userID'])) {
        $user = disinfect_var($_POST['userID']);
        $a = $purifier->purify($user);
    }
    if(!empty($_POST['source'])) {
        $url = disinfect_var($_POST['source']);
        $d = $purifier->purify($url);
    }
    if(empty($_POST['accuse'])) {
        $f = true;
    }
    unset($_POST);
    if((!empty($a)) && (!empty($b)) && (!empty($c)) && (!empty($d)) && (!empty($e)) && $f) {
        $istmt = $conn->prepare("INSERT INTO sys_feedback (`subID`,`usage`,`usefulness`,`itemURL`,`date_received`) VALUES (?, ?, ?, ?, ?)");
        $istmt->bind_param("siiss", $a, $b, $c, $d, $e);
        $istmt->execute();
        $istmt->close();
    }
}
else {
    header("HTTP/1.1 401 Unauthorized");
    header("Location: index.php");
}
?>