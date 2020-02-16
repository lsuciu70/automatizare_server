<?php
$debug = TRUE;

$curr_h = strftime("%H");
$first_half = intval($curr_h) < 12;

// var_dump($first_half);

$room_string = "00:00-00:40, 04:00-05:30, 07:10-11:00, 17:10-21:00, 23:20";
$idx = 0;
$last_idx = 0;
$intervals = array();
while(($idx = strpos ($room_string, ", ", $last_idx)) !== FALSE)
{
  $temp = substr($room_string, $last_idx, $idx - $last_idx);
  $intervals[] = trim($temp);
  $last_idx = $idx + strlen(",");
}
if($last_idx < strlen($room_string))
{
  $temp = substr($room_string, $last_idx);
  $temp = trim($temp);
  if (strlen($temp) < 6)
    $temp = $temp."-".$temp;
    $intervals[] = $temp;
}
foreach ($intervals as $interval)
{
  if($debug)
    var_dump($interval);
  sscanf($interval, "%d:%d-%d:%d", $i_f_h, $i_f_m, $i_l_h, $i_l_m);
  $f_meas = $i_f_h * 6;
  if ($i_f_m % 10 === 0)
    $f_meas += $i_f_m / 10;
  else
    $f_meas += ($i_f_m - ($i_f_m % 10)) / 10 + 1;
  $l_meas = $i_l_h * 6;
  if ($i_l_m % 10 === 0)
    $l_meas += $i_l_m / 10;
  else
    $l_meas += ($i_l_m - ($i_l_m % 10)) / 10 + 1;
  if ($l_meas === $f_meas)
    $l_meas += 1;
  if($debug)
    echo "$f_meas - $l_meas".PHP_EOL;
}
?>
