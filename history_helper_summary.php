<?php
include_once 'file_helper.php';
include_once 'time_helper.php';
include_once 'temperature_helper.php';

if ( ! isset($debug))
  $debug = TRUE;
if ( ! isset($day) || $day === NULL )
  $day = strftime("%Y.%m.%d");

// if ($debug)
//   $day = "2016.12.26";
  
if ( ! isset($day_history_data))
{
  $day_history_data = array();
  day_history(strip_prepend_zero($day), $day_history_data);
  if(is_today($day))
  {
    save_day_history("data/sus_avg.txt", $day_history_data, 0);
    save_day_history("data/jos_avg.txt", $day_history_data, 8);
    save_day_history("data/sus_temp.txt", $day_history_data, 0);
    save_day_history("data/jos_temp.txt", $day_history_data, 8);
  }
}
if ($debug)
  var_dump($day_history_data);

if ( ! isset($room_count))
{
  if ( isset($room))
    $room_count = count($room);
  else
    $room_count = 11;
}

$up_index = $room_count - 3;
$down_index = $room_count - 2;
$all_index = $room_count - 1;


$max_all = 0; $min_all = 10000;

// running -> array keys: (0 - 15 - rooms), (16 - up), (17 -down), (18 -  total)
$room_sum_minutes = array();
$room_sum_str = array();
$room_str = array();
$room_was_running = array();
$room_first_running = array();
$room_prev_stop = array();

for ($i = 0 ; $i < $room_count ; ++ $i)
{
  $room_sum_minutes[$i] = 0;
  $room_sum_str = "";
  $room_str[$i] = "";
  $room_was_running[$i] = FALSE;
  $room_first_running[$i] = TRUE;
  $room_prev_stop[$i] = "";
}

foreach ($day_history_data as $time => $values)
{
  sscanf($time, "%d:%d-%s", $start_h, $start_m, $stop);
  $start = sprintf("%02d:%02d", $start_h, $start_m);
  if($stop === NULL)
  {
    $stop = $start;
  }
  sscanf($start, "%d:%d", $start_h, $start_m);
  sscanf($stop, "%d:%d", $stop_h, $stop_m);
  if($stop_h === 0 && $stop_m === 0)
    $stop_h = 24;
  $minutes = ($stop_m + $stop_h * 60) - ($start_m + $start_h * 60);
  if ($minutes === 0)
    $minutes += 1;
  for ($i = 0 ; $i < $up_index ; ++ $i)
  {
    // running index inside $day_history_data
    $j = $i * 2 + 1;
    if ( ! isset($values[$j]))
      continue;
    $room_is_running = $values[$j];
    if($room_is_running > 0)
    {
      $room_sum_minutes[$i] += $minutes;
      $room_prev_stop[$i] = $stop;
    }
    if($room_was_running[$i] === FALSE && $room_is_running > 0)
    {
      // started
      if($room_first_running[$i] === FALSE)
      {
        $room_str[$i] .= ", ";
      }
      $room_str[$i] .= $start;
      $room_was_running[$i] = TRUE;
      $room_first_running[$i] = FALSE;
    }
    if ($room_was_running[$i] === TRUE && $room_is_running <= 0)
    {
      // stopped
      $room_str[$i] .= "-".$room_prev_stop[$i];
      $room_was_running[$i] = FALSE;
    }
  }

  $up_is_running = 0;
  for ($i = 0 ; $i < $up_index / 2 ; ++ $i)
  {
    $j = $i * 2 + 1;
    if ( ! isset($values[$j]))
      continue;
    $up_is_running += $values[$j];
  }
  if($up_is_running > 0)
  {
    $room_sum_minutes[$up_index] += $minutes;
    $room_prev_stop[$up_index] = $stop;
  }
  if ($debug === TRUE)
    echo "$start up_is_running = $up_is_running, sum_minutes = $room_sum_minutes[$up_index]".PHP_EOL;
  if($room_was_running[$up_index] === FALSE && $up_is_running > 0)
  {
    // starting
    if($room_first_running[$up_index] === FALSE)
    {
      $room_str[$up_index] .= ", ";
    }
    $room_str[$up_index] .= $start;
    $room_was_running[$up_index] = TRUE;
    $room_first_running[$up_index] = FALSE;
  }
  if ($room_was_running[$up_index] === TRUE && $up_is_running <= 0)
  {
    $room_str[$up_index] .= "-".$room_prev_stop[$up_index];
    $room_was_running[$up_index] = FALSE;
  }

  $down_is_running = 0;
  for ($i = $up_index / 2 ; $i <  $up_index; ++ $i)
  {
    $j = $i * 2 + 1;
    if ( ! isset($values[$j]))
      continue;
    $down_is_running += $values[$j];
  }
//   if(isset($values[9])) $down_is_running += $values[9];
//   if(isset($values[11])) $down_is_running += $values[11];
//   if(isset($values[13])) $down_is_running += $values[13];
//   if(isset($values[15])) $down_is_running += $values[15];
  if($down_is_running > 0)
  {
    $room_sum_minutes[$down_index] += $minutes;
    $room_prev_stop[$down_index] = $stop;
  }
  if($room_was_running[$down_index] === FALSE && $down_is_running > 0)
  {
    // starting
    if($room_first_running[$down_index] === FALSE)
    {
      $room_str[$down_index] .= ", ";
    }
    $room_str[$down_index] .= $start;
    $room_was_running[$down_index] = TRUE;
    $room_first_running[$down_index] = FALSE;
  }
  if ($room_was_running[$down_index] === TRUE && $down_is_running <= 0)
  {
    $room_str[$down_index] .= "-".$room_prev_stop[$down_index];
    $room_was_running[$down_index] = FALSE;
  }

  $all_is_running = $up_is_running + $down_is_running;
  if($all_is_running > 0)
  {
    $room_sum_minutes[$all_index] += $minutes;
    $room_prev_stop[$all_index] = $stop;
  }
  if($room_was_running[$all_index] === FALSE && $all_is_running > 0)
  {
    // starting
    if($room_first_running[$all_index] === FALSE)
    {
      $room_str[$all_index] .= ", ";
    }
    $room_str[$all_index] .= $start;
    $room_was_running[$all_index] = TRUE;
    $room_first_running[$all_index] = FALSE;
  }
  if ($room_was_running[$all_index] === TRUE && $all_is_running <= 0)
  {
    $room_str[$all_index] .= "-".$room_prev_stop[$all_index];
    $room_was_running[$all_index] = FALSE;
  }
}
for ($i = 0 ; $i < $room_count ; ++$i)
{
  if ($room_sum_minutes[$i] <= 0)
  {
    $room_str[$i] .= "Nu a mers";
  }
  else
  {
    $mers = $room_sum_minutes[$i];
    $m = $mers % 60;
    $h = ($mers - $m) / 60;
    $room_sum_str[$i] = sprintf("%d:%02d", $h, $m);
//     $room_str[$i] .= " (".$room_sum_minutes[$i]." minute)";
  }
}
if ($debug === TRUE)
{
  echo $day.PHP_EOL;
  for ($i = 0 ; $i < $room_count ; ++ $i)
  {
    if ( isset($room))
      echo $room[$i].": ";
    if ($room_sum_minutes[$i] > 0)
      echo "A mers $room_sum_str[$i]; ";
    echo $room_str[$i]." (".$room_sum_minutes[$i]." minute)".PHP_EOL;
  }
}
?>
