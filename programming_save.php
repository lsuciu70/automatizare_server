<?php
header('Location: info.php');
// in case of a POST request
if ($_SERVER ['REQUEST_METHOD'] !== 'POST')
{
  exit;
}
if (isset ( $_POST ["t_loc"] ) === FALSE)
{
  error_log("ERROR - Empty t_loc");
  exit("ERROR - Empty t_loc");
}

// Constants
$LOC_SUS = "etaj";
$LOC_JOS = "parter";
  
$t_loc = "";
$location_offset = 0;
$t_loc = htmlspecialchars ( $_POST ["t_loc"] );
if (strpos ( $t_loc, $LOC_JOS ) !== FALSE)
  $location_offset = 0;
else if (strpos ( $t_loc, $LOC_SUS ) !== FALSE)
  $location_offset = 4;
else
{
  error_log( "ERROR - Unknown t_loc: " . $t_loc );
  exit ( "ERROR - Unknown t_loc: " . $t_loc );
}

$ip_file_name = "data/" . $t_loc . "_ip.txt";
if (is_file($ip_file_name))
{
  $t_ip = file_get_contents($ip_file_name);
}
else if ($location_offset === 0)
  $t_ip = "192.168.100.20";
else if ($location_offset === 4)
  $t_ip = "192.168.100.48";

$post_data = "";
foreach ($_POST as $key => $value)
{
  $post_data .= "&".$key."=".$value;
}
if(strlen($post_data) > 0)
{
  // remove first '&'
  $post_data = substr($post_data, 1);
  $url = "http://" . $t_ip;
  $header = array(
      "POST / HTTP/1.1",
      "Host: " . $t_ip,
      "User-Agent: curl/7.24.0",
      "Accept: */*",
      "Content-Type: application/x-www-form-urlencoded",
      "Content-Length: " . strlen($post_data)
  );
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
  $data = curl_exec($ch);
  if (curl_errno($ch))
    error_log("programming_save.php - ERROR: " . curl_error($ch));
  curl_close($ch);
}
// error_log("programming_save.php -> ".$t_ip." (".strlen($post_data).") - ".$post_data);
?>
