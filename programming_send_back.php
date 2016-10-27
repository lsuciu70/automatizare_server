<?php
// in case of a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST')
{
  exit;
}
if(isset($_POST["t_loc"]) === FALSE)
{
  error_log("ERROR - Empty t_loc");
  exit("ERROR - Empty t_loc");
}

// Constants
$LOC_SUS = "etaj";
$LOC_JOS = "parter";

$t_loc="";
$location_offset = 0;
$t_loc=htmlspecialchars($_POST["t_loc"]);
if (strpos($t_loc, $LOC_JOS) !== FALSE)
  $location_offset = 0;
else if (strpos($t_loc, $LOC_SUS) !== FALSE)
  $location_offset = 4;
else
{
  error_log("ERROR - Unknown t_loc: ".$t_loc);
  exit("ERROR - Unknown t_loc: ".$t_loc);
}

$ip_file_name = "data/" . $t_loc . "_ip.txt";
if (is_file($ip_file_name))
  $t_ip = file_get_contents($ip_file_name);
else if ($location_offset === 0)
  $t_ip = "192.168.100.20";
else if ($location_offset === 4)
  $t_ip = "192.168.100.48";

$vn = array(
  "programming",
  "start_hour_p1",
  "start_minute_p1",
  "stop_hour_p1",
  "stop_minute_p1",
  "target_temperature_p1",
  "start_hour_p2",
  "start_minute_p2",
  "next_programm_p2",
  "start_hour_p3",
  "start_minute_p3",
  "next_programm_p3",
  "next_programm_p4",
);

$vn_cnt = count($vn);

$vn_val = array (
  $vn[0] => array(1, 1, 1, 1, 1, 1, 1, 1),
  $vn[1] => array(15, 15, 15, 15, 15, 15, 15, 15),
  $vn[2] => array(0, 0, 0, 0, 0, 0, 0, 0),
  $vn[3] => array(7, 7, 7, 7, 7, 7, 7, 7),
  $vn[4] => array(0, 0, 0, 0, 0, 0, 0, 0),
  $vn[5] => array(2000, 2000, 1500, 2000, 2200, 2200, 2000, 2100),
  $vn[6] => array(4, 4, 4, 4, 4, 4, 4, 4),
  $vn[7] => array(30, 30, 30, 30, 30, 30, 30, 30),
  $vn[8] => array(3, 3, 3, 3, 3, 3, 3, 3),
  $vn[9] => array(14, 14, 14, 14, 14, 14, 14, 14),
  $vn[10] => array(30, 30, 30, 30, 30, 30, 30, 30),
  $vn[11] => array(2, 2, 2, 2, 2, 2, 2, 2),
  $vn[12] => array(0, 0, 0, 0, 0, 0, 0, 0)
);

  $post_head = "t_loc=" . $t_loc;
  $post_data = array($post_head, $post_head, $post_head, $post_head);

for ($x = 0 ; $x < $vn_cnt ; $x++ )
{
  $name = $vn[$x];
  $name_val = $vn_val[$name];
  $name_file_name = "data/" . $name . ".txt";
  if (is_file($name_file_name))
  {
    $line = file_get_contents($name_file_name);
    sscanf($line, "%d,%d,%d,%d,%d,%d,%d,%d", $name_val[0], $name_val[1], $name_val[2], $name_val[3], $name_val[4], $name_val[5], $name_val[6], $name_val[7]);
  }
  if (strpos($name, "target_temperature_p1") !== FALSE)
  {
    for ($y = 0 ; $y < 8 ; $y++ )
    {
      $name_val[$y] = floatval($name_val[$y]) / 100;
    }
  }
  for ($i = 0;$i < 4;$i++)
  {
    $j = $i + $location_offset;
    $post_data[$i] = $post_data[$i] . "&" . $name . "_" . $i . "=" . $name_val[$j];
  }
}

// send back the post requests
$url = "http://" . $t_ip;
for ($i = 0;$i < 4;$i++)
{
  $header = array(
    "POST / HTTP/1.1",
    "Host: " . $t_ip,
    "User-Agent: curl/7.24.0",
    "Accept: */*",
    "Content-Type: application/x-www-form-urlencoded",
    "Content-Length: " . strlen($post_data[$i])
  );
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data[$i]);
  $data = curl_exec($ch);
  if (curl_errno($ch))
    error_log("programming_send_back.php - ERROR: " . curl_error($ch));
  curl_close($ch);
// error_log("programming_send_back.php -> ".$t_ip." (".strlen($post_data[$i]).") - ".$post_data[$i]);
}
?>
