<?php
include_once 'file_helper.php';

$t_day_sus_history_file_name = "data/sus_" . $t_day . ".txt";
$sus_history = file_to_double_array($t_day_sus_history_file_name);
$t_day_jos_history_file_name = "data/jos_" . $t_day . ".txt";
$jos_history = file_to_double_array($t_day_jos_history_file_name);

$room_str = ""; $room_was_running = FALSE; $room_first_running = TRUE; $room_sum_minutes = 0; $room_prev_stop = "";
$dm_str = ""; $dm_r_prev = FALSE; $dm_r_first = TRUE; $dm_r_sum = 0; $dm_r_prev_stop = "";
$d3_str = ""; $d3_r_prev = FALSE; $d3_r_first = TRUE; $d3_r_sum = 0; $d3_r_prev_stop = "";
$bs_str = ""; $bs_r_prev = FALSE; $bs_r_first = TRUE; $bs_r_sum = 0; $bs_r_prev_stop = "";
$bu_str = ""; $bu_r_prev = FALSE; $bu_r_first = TRUE; $bu_r_sum = 0; $bu_r_prev_stop = "";
$li_str = ""; $li_r_prev = FALSE; $li_r_first = TRUE; $li_r_sum = 0; $li_r_prev_stop = "";
$bi_str = ""; $bi_r_prev = FALSE; $bi_r_first = TRUE; $bi_r_sum = 0; $bi_r_prev_stop = "";
$bj_str = ""; $bj_r_prev = FALSE; $bj_r_first = TRUE; $bj_r_sum = 0; $bj_r_prev_stop = "";
$sus_str = ""; $sus_r_prev = FALSE; $sus_r_first = TRUE; $sus_r_sum = 0; $sus_r_prev_stop = "";
$jos_str = ""; $jos_r_prev = FALSE; $jos_r_first = TRUE; $jos_r_sum = 0; $jos_r_prev_stop = "";
$all_str = ""; $all_r_prev = FALSE; $all_r_first = TRUE; $all_r_sum = 0; $all_r_prev_stop = "";
if ($sus_history != NULL)
{
  $hm = $sus_history["hm"];
  $sz = count($hm);
  for ($i = 0 ; $i < $sz ; ++$i)
  {
    $time = $hm[$i];
    sscanf($time, "%d:%d-%s", $start_h, $start_m, $stop);
    $start = sprintf("%02d:%02d", $start_h, $start_m);
    
    $c1_r = $sus_history["c1_r"][$i];
    if($c1_r > 0) { $room_sum_minutes += 1; $room_prev_stop = $stop; }
    if($room_was_running === FALSE && $c1_r > 0) { if($room_first_running === FALSE) { $room_str .= ", "; } $room_str .= $start; $room_was_running = TRUE; $room_first_running = FALSE; }
    if ($room_was_running === TRUE && $c1_r <= 0) { $room_str .= "-".$room_prev_stop; $room_was_running = FALSE; }
//     if($c1_r > 0) { $dl_r_sum += 1; $dl_r_prev_stop = $stop; }
//     if($dl_r_prev === FALSE && $c1_r > 0) { if($dl_r_first === FALSE) { $dl_str .= "; "; } $dl_str .= "start ".$start; $dl_r_prev = TRUE; $dl_r_first = FALSE; }
//     if ($dl_r_prev === TRUE && $c1_r <= 0) { $dl_str .= " stop ".$dl_r_prev_stop; $dl_r_prev = FALSE; }
    
    $c2_r = $sus_history["c2_r"][$i];
    if($c2_r > 0) { $dm_r_sum += 1; $dm_r_prev_stop = $stop; }
    if($dm_r_prev === FALSE && $c2_r > 0) { if($dm_r_first === FALSE) { $dm_str .= ", "; } $dm_str .= $start; $dm_r_prev = TRUE; $dm_r_first = FALSE; }
    if ($dm_r_prev === TRUE && $c2_r <= 0) { $dm_str .= "-".$dm_r_prev_stop; $dm_r_prev = FALSE; }
    
    $c3_r = $sus_history["c3_r"][$i];
    if($c3_r > 0) { $d3_r_sum += 1; $d3_r_prev_stop = $stop; }
    if($d3_r_prev === FALSE && $c3_r > 0) { if($d3_r_first === FALSE) { $d3_str .= ", "; } $d3_str .= $start; $d3_r_prev = TRUE; $d3_r_first = FALSE; }
    if ($d3_r_prev === TRUE && $c3_r <= 0) { $d3_str .= "-".$d3_r_prev_stop; $d3_r_prev = FALSE; }
    
    $c4_r = $sus_history["c4_r"][$i];
    if($c4_r > 0) { $bs_r_sum += 1; $bs_r_prev_stop = $stop; }
    if($bs_r_prev === FALSE && $c4_r > 0) { if($bs_r_first === FALSE) { $bs_str .= ", "; } $bs_str .= $start; $bs_r_prev = TRUE; $bs_r_first = FALSE; }
    if ($bs_r_prev === TRUE && $c4_r <= 0) { $bs_str .= "-".$bs_r_prev_stop; $bs_r_prev = FALSE; }
    
    $sus_r = $c1_r + $c2_r + $c3_r + $c4_r;
    $sus_r = ($sus_r <= 0) ? 0 : 1;
    if($sus_r > 0) { $sus_r_sum += 1; $sus_r_prev_stop = $stop; }
    if($sus_r_prev === FALSE && $sus_r > 0) { if($sus_r_first === FALSE) { $sus_str .= ", "; } $sus_str .= $start; $sus_r_prev = TRUE; $sus_r_first = FALSE; }
    if ($sus_r_prev === TRUE && $sus_r <= 0) { $sus_str .= "-".$sus_r_prev_stop; $sus_r_prev = FALSE; }
    $sum_history_r[$time][1] = $sus_r;
  }
}
if ($jos_history != NULL)
{
  $hm = $jos_history["hm"];
  $sz = count($hm);
  for ($i = 0 ; $i < $sz ; ++$i)
  {
    $time = $hm[$i];
    sscanf($time, "%d:%d-%s", $start_h, $start_m, $stop);
    $start = sprintf("%02d:%02d", $start_h, $start_m);

    $c1_r = $jos_history["c1_r"][$i];
    if($c1_r > 0) { $bu_r_sum += 1; $bu_r_prev_stop = $stop; }
    if($bu_r_prev === FALSE && $c1_r > 0) { if($bu_r_first === FALSE) { $bu_str .= ", "; } $bu_str .= $start; $bu_r_prev = TRUE; $bu_r_first = FALSE; }
    if ($bu_r_prev === TRUE && $c1_r <= 0) { $bu_str .= "-".$bu_r_prev_stop; $bu_r_prev = FALSE; }

    $c2_r = $jos_history["c2_r"][$i];
    if($c2_r > 0) { $li_r_sum += 1; $li_r_prev_stop = $stop; }
    if($li_r_prev === FALSE && $c2_r > 0) { if($li_r_first === FALSE) { $li_str .= ", "; } $li_str .= $start; $li_r_prev = TRUE; $li_r_first = FALSE; }
    if ($li_r_prev === TRUE && $c2_r <= 0) { $li_str .= "-".$li_r_prev_stop; $li_r_prev = FALSE; }
    
    $c3_r = $jos_history["c3_r"][$i];
    if($c3_r > 0) { $bi_r_sum += 1; $bi_r_prev_stop = $stop; }
    if($bi_r_prev === FALSE && $c3_r > 0) { if($bi_r_first === FALSE) { $bi_str .= ", "; } $bi_str .= $start; $bi_r_prev = TRUE; $bi_r_first = FALSE; }
    if ($bi_r_prev === TRUE && $c3_r <= 0) { $bi_str .= "-".$bi_r_prev_stop; $bi_r_prev = FALSE; }
    
    $c4_r = $jos_history["c4_r"][$i];
    if($c4_r > 0) { $bj_r_sum += 1; $bj_r_prev_stop = $stop; }
    if($bj_r_prev === FALSE && $c4_r > 0) { if($bj_r_first === FALSE) { $bj_str .= ", "; } $bj_str .= $start; $bj_r_prev = TRUE; $bj_r_first = FALSE; }
    if ($bj_r_prev === TRUE && $c4_r <= 0) { $bj_str .= "-".$bj_r_prev_stop; $bj_r_prev = FALSE; }
    
    $jos_r = $c1_r + $c2_r + $c3_r + $c4_r;
    $jos_r = ($jos_r <= 0) ? 0 : 1;
    if($jos_r > 0) { $jos_r_sum += 1; $jos_r_prev_stop = $stop; }
    if($jos_r_prev === FALSE && $jos_r > 0) { if($jos_r_first === FALSE) { $jos_str .= ", "; } $jos_str .= $start; $jos_r_prev = TRUE; $jos_r_first = FALSE; }
    if ($jos_r_prev === TRUE && $jos_r <= 0) { $jos_str .= "-".$jos_r_prev_stop; $jos_r_prev = FALSE; }
    $sum_history_r[$time][0] = $jos_r;
  }
}
foreach ($sum_history_r as $time => $run)
{
  sscanf($time, "%d:%d-%s", $start_h, $start_m, $stop);
  $start = sprintf("%02d:%02d", $start_h, $start_m);
  
  $all_r = ((isset($sum_history_r[$time][0]) && $sum_history_r[$time][0] > 0) || (isset($sum_history_r[$time][1]) && $sum_history_r[$time][1] > 0)) ? 1 : 0;
  if($all_r > 0) { $all_r_sum += 1; $all_r_prev_stop = $stop; }
  if($all_r_prev === FALSE && $all_r > 0) { if($all_r_first === FALSE) { $all_str .= ", "; } $all_str .= $start; $all_r_prev = TRUE; $all_r_first = FALSE; }
  if ($all_r_prev === TRUE && $all_r <= 0) { $all_str .= "-".$all_r_prev_stop; $all_r_prev = FALSE; }
}

$dl_sum_str = "";
$dm_sum_str = "";
$d3_sum_str = "";
$bs_sum_str = "";
$bu_sum_str = "";
$li_sum_str = "";
$bi_sum_str = "";
$bj_sum_str = "";
$sus_sum_str = "";
$jos_sum_str = "";
$all_sum_str = "";

if ($room_sum_minutes <= 0) { $room_str .= "nu a mers"; }
else { $mers = $room_sum_minutes * 10; $m = $mers % 60; $h = ($mers - $m) / 60; $dl_sum_str = sprintf("%d:%02d", $h, $m); $room_str .= " (".$mers." minute)"; }

if ($dm_r_sum <= 0) { $dm_str .= "nu a mers"; }
else { $mers = $dm_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; $dm_sum_str = sprintf("%d:%02d", $h, $m); $dm_str .= " (".$mers." minute)"; }

if ($d3_r_sum <= 0) { $d3_str .= "nu a mers"; }
else { $mers = $d3_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; $d3_sum_str = sprintf("%d:%02d", $h, $m); $d3_str .= " (".$mers." minute)"; }

if ($bs_r_sum <= 0) { $bs_str .= "nu a mers"; }
else { $mers = $bs_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; $bs_sum_str = sprintf("%d:%02d", $h, $m); $bs_str .= " (".$mers." minute)"; }

if ($bu_r_sum <= 0) { $bu_str .= "nu a mers"; }
else { $mers = $bu_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; $bu_sum_str = sprintf("%d:%02d", $h, $m); $bu_str .= " (".$mers." minute)"; }

if ($li_r_sum <= 0) { $li_str .= "nu a mers"; }
else { $mers = $li_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; $li_sum_str = sprintf("%d:%02d", $h, $m); $li_str .= " (".$mers." minute)"; }

if ($bi_r_sum <= 0) { $bi_str .= "nu a mers"; }
else { $mers = $bi_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; $bi_sum_str = sprintf("%d:%02d", $h, $m); $bi_str .= " (".$mers." minute)"; }

if ($bj_r_sum <= 0) { $bj_str .= "nu a mers"; }
else { $mers = $bj_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; $bj_sum_str = sprintf("%d:%02d", $h, $m); $bj_str .= " (".$mers." minute)"; }

if ($sus_r_sum <= 0) { $sus_str .= "nu a mers"; }
else { $mers = $sus_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; $sus_sum_str = sprintf("%d:%02d", $h, $m); $sus_str .= " (".$mers." minute)"; }

if ($jos_r_sum <= 0) { $jos_str .= "nu a mers"; }
else { $mers = $jos_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; $jos_sum_str = sprintf("%d:%02d", $h, $m); $jos_str .= " (".$mers." minute)"; }

if ($all_r_sum <= 0) { $all_str .= "nu a mers"; }
else { $mers = $all_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; $all_sum_str = sprintf("%d:%02d", $h, $m); $all_str .= " (".$mers." minute)"; }

?>
