<?php
// in case of a POST request
if ($_SERVER ['REQUEST_METHOD'] !== 'POST')
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
    
// Constants
$NOT_SET = 255;

// get current date and time
$ts_full = strftime ( "%Y.%m.%d_%H:%M:%S" );
sscanf ( $ts_full, "%d.%d.%d_%d:%d:%d", $ts_Y, $ts_M, $ts_D, $ts_h, $ts_m, $ts_s );

$vn = array (
  "programming",
  "start_hour_p1",
  "start_minute_p1",
  "stop_hour_p1",
  "stop_minute_p1",
  "target_temperature_p1",
  "start_hour_p2",
  "start_minute_p2",
  "next_programm_p2",
  "target_temperature_p2",
  "start_hour_p3",
  "start_minute_p3",
  "next_programm_p3",
  "target_temperature_p3",
  "next_programm_p4",
  "target_temperature_p4",
    "start_hour_p5",
    "start_minute_p5",
    "stop_hour_p5",
    "stop_minute_p5",
    "next_programm_p5",
    "start_hour_p6",
    "start_minute_p6",
    "stop_hour_p6",
    "stop_minute_p6",
    "next_programm_p6",
);
$vn_cnt = count ( $vn );
$vn_val = array (
  $vn[0] => array(1, 1, 1, 1, 1, 1, 1, 1), // programming
  $vn[1] => array(15, 15, 15, 15, 15, 15, 15, 15), // start_hour_p1
  $vn[2] => array(0, 0, 0, 0, 0, 0, 0, 0), // start_minute_p1
  $vn[3] => array(7, 7, 7, 7, 7, 7, 7, 7), // stop_hour_p1
  $vn[4] => array(0, 0, 0, 0, 0, 0, 0, 0), // stop_minute_p1
  $vn[5] => array(2100, 2100, 1800, 2100, 2300, 2300, 2300, 2100), // target_temperature_p1
  $vn[6] => array(4, 4, 4, 4, 4, 4, 4, 4), // start_hour_p2
  $vn[7] => array(30, 30, 30, 30, 30, 30, 30, 30), // start_minute_p2
  $vn[8] => array(3, 3, 3, 3, 3, 3, 3, 3), // next_programm_p2
  $vn[9] => array(0, 0, 0, 0, 0, 0, 0, 0), // target_temperature_p2
  $vn[10] => array(14, 14, 14, 14, 14, 14, 14, 14), // start_hour_p3
  $vn[11] => array(30, 30, 30, 30, 30, 30, 30, 30), // start_minute_p3
  $vn[12] => array(2, 2, 2, 2, 2, 2, 2, 2), // next_programm_p3
  $vn[13] => array(0, 0, 0, 0, 0, 0, 0, 0), // target_temperature_p3
  $vn[14] => array(0, 0, 0, 0, 0, 0, 0, 0), // next_programm_p4
  $vn[15] => array(0, 0, 0, 0, 0, 0, 0, 0), // target_temperature_p4
    $vn[16] => array(5, 5, 5, 5, 5, 5, 5, 5), // start_hour_p5
    $vn[17] => array(0, 0, 0, 0, 0, 0, 0, 0), // start_minute_p5
    $vn[18] => array(5, 5, 5, 5, 5, 5, 5, 5), // stop_hour_p5
    $vn[19] => array(59, 59, 59, 59, 59, 59, 59, 59), // stop_minute_p5
    $vn[20] => array(6, 6, 6, 6, 6, 6, 6, 6), // next_programm_p5
    $vn[21] => array(19, 19, 19, 19, 19, 19, 19, 19), // start_hour_p6
    $vn[22] => array(0, 0, 0, 0, 0, 0, 0, 0), // start_minute_p6
    $vn[23] => array(19, 19, 19, 19, 19, 19, 19, 19), // stop_hour_p6
    $vn[24] => array(59, 59, 59, 59, 59, 59, 59, 59), // stop_minute_p6
    $vn[25] => array(5, 5, 5, 5, 5, 5, 5, 5), // next_programm_p6
);

for($x = 0; $x < $vn_cnt; $x ++)
{
  $name = $vn [$x];
  $name_val = $vn_val [$name];
  $name_val_post = $vn_val [$name];
  $name_file = "data/" . $name . ".txt";
  $line = "";
  if (is_file ( $name_file ))
  {
    $line = file_get_contents ( $name_file );
    sscanf ( $line, "%d,%d,%d,%d,%d,%d,%d,%d", $name_val [0], $name_val [1], $name_val [2], $name_val [3], $name_val [4], $name_val [5], $name_val [6], $name_val [7] );
  }
  $name_val_changed = FALSE;

  for($i = 0; $i < 4; $i ++)
  {
    $j = $i + $location_offset;
    if (isset ( $_POST [($name . "_" . $i)] ))
    {
      $name_val_post [$j] = htmlspecialchars ( $_POST [($name . "_" . $i)] );
      if (strpos ( $name, "target_temperature_p1" ) !== FALSE || 
          strpos ( $name, "target_temperature_p2" ) !== FALSE ||
          strpos ( $name, "target_temperature_p3" ) !== FALSE ||
          strpos ( $name, "target_temperature_p4" ) !== FALSE)
        $name_val_post [$j] = intval ( floatval ( $name_val_post [$j] ) * 100 );
      $name_val_changed = $name_val_changed || intval ( $name_val_post [$j] ) != $name_val [$j];
      $name_val [$j] = intval ( $name_val_post [$j] );
    }
  }

  if ($name_val_changed)
  {
    $line = $name_val [0] . "," . $name_val [1] . "," . $name_val [2] . "," . $name_val [3] . "," . $name_val [4] . "," . $name_val [5] . "," . $name_val [6] . "," . $name_val [7] . "," . $ts_full . PHP_EOL;
    file_put_contents ( $name_file, $line, LOCK_EX );
    // do not continue for target tempereatures P2 - P4
    if (strpos ( $name, "target_temperature_p2" ) === FALSE &&
        strpos ( $name, "target_temperature_p3" ) === FALSE &&
        strpos ( $name, "target_temperature_p4" ) === FALSE)
    {
      $dbox_file = $name . "_" . $ts_Y . "." . $ts_M . "." . $ts_D . ".txt";
      $name_file_date = "data/" . $dbox_file;
      file_put_contents ( $name_file_date, $line, FILE_APPEND | LOCK_EX );
      // save to Dropbox
      include_once "dbox.php";
    
      $curl_cmd = 'curl -k -X POST https://content.dropboxapi.com/2/files/upload '.
          '--header "Authorization: Bearer '.$dbox_k.'" '.
          '--header "Dropbox-API-Arg: {\"path\": \"/'.$dbox_file.'\", \"mode\": \"overwrite\"}" '.
          '--header "Content-Type: application/octet-stream" '.
          '--data-binary @'.$name_file_date;
      
      if(system($curl_cmd." &", $retval) === FALSE)
        error_log($retval);
    }
  }
}
?>
