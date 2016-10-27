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
  "start_hour_p3",
  "start_minute_p3",
  "next_programm_p3",
  "next_programm_p4" 
);
$vn_cnt = count ( $vn );
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

for($x = 0; $x < $vn_cnt; $x ++)
{
  $name = $vn [$x];
  $name_val = $vn_val [$name];
  $name_file_name = "data/" . $name . ".txt";
  $line = "";
  if (is_file ( $name_file_name ))
  {
    $line = file_get_contents ( $name_file_name );
    sscanf ( $line, "%d,%d,%d,%d,%d,%d,%d,%d", $name_val [0], $name_val [1], $name_val [2], $name_val [3], $name_val [4], $name_val [5], $name_val [6], $name_val [7] );
  }
  $name_val_changed = FALSE;
  $name_val_post = array ($NOT_SET, $NOT_SET, $NOT_SET, $NOT_SET, $NOT_SET, $NOT_SET, $NOT_SET, $NOT_SET);

  for($i = 0; $i < 4; $i ++)
  {
    $j = $i + $location_offset;
    if (isset ( $_POST [($name . "_" . $i)] ))
    {
      $name_val_post [$j] = htmlspecialchars ( $_POST [($name . "_" . $i)] );
      if (strpos ( $name, "target_temperature_p1" ) !== FALSE)
        $name_val_post [$j] = intval ( floatval ( $name_val_post [$j] ) * 100 );
      $name_val_changed = $name_val_changed || intval ( $name_val_post [$j] ) != $name_val [$j];
      $name_val [$j] = intval ( $name_val_post [$j] );
    }
  }

  if ($name_val_changed)
  {
    $name_file_name_date = "/opt/www/data/" . $name . "_" . $ts_Y . "." . $ts_M . "." . $ts_D . ".txt";
    $line = $name_val [0] . "," . $name_val [1] . "," . $name_val [2] . "," . $name_val [3] . "," . $name_val [4] . "," . $name_val [5] . "," . $name_val [6] . "," . $name_val [7] . "," . $ts_full . PHP_EOL;
    file_put_contents ( $name_file_name, $line, LOCK_EX );
    file_put_contents ( $name_file_name_date, $line, FILE_APPEND | LOCK_EX );
  }
}
?>
