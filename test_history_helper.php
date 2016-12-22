<?php
include 'file_helper.php';

$t_day = sprintf("%d.%d.%d", 2016, 12, 20);

$t_day_jos_history_file_name = "data/jos_" . $t_day . ".txt";
$jos_history = file_to_double_array($t_day_jos_history_file_name);
$t_day_sus_history_file_name = "data/sus_" . $t_day . ".txt";
$sus_history = file_to_double_array($t_day_sus_history_file_name);

$bu_str = ""; $bu_r_prev = FALSE; $bu_r_first = TRUE; $bu_r_sum = 0;
$li_str = ""; $li_r_prev = FALSE; $li_r_first = TRUE; $li_r_sum = 0;
$bi_str = ""; $bi_r_prev = FALSE; $bi_r_first = TRUE; $bi_r_sum = 0;
$bj_str = ""; $bj_r_prev = FALSE; $bj_r_first = TRUE; $bj_r_sum = 0;
$dl_str = ""; $dl_r_prev = FALSE; $dl_r_first = TRUE; $dl_r_sum = 0;
$dm_str = ""; $dm_r_prev = FALSE; $dm_r_first = TRUE; $dm_r_sum = 0;
$d3_str = ""; $d3_r_prev = FALSE; $d3_r_first = TRUE; $d3_r_sum = 0;
$bs_str = ""; $bs_r_prev = FALSE; $bs_r_first = TRUE; $bs_r_sum = 0;
$jos_str = ""; $jos_r_prev = FALSE; $jos_r_first = TRUE; $jos_r_sum = 0;
$sus_str = ""; $sus_r_prev = FALSE; $sus_r_first = TRUE; $sus_r_sum = 0;
$all_str = ""; $all_r_prev = FALSE; $all_r_first = TRUE; $all_r_sum = 0;
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
    if($c1_r > 0) { $bu_r_sum += 1; }
    if($bu_r_prev === FALSE && $c1_r > 0) { if($bu_r_first === FALSE) { $bu_str .= "; "; } $bu_str .= "start ".$start; $bu_r_prev = TRUE; $bu_r_first = FALSE; }
    if ($bu_r_prev === TRUE && $c1_r <= 0) { $bu_str .= " stop ".$stop; $bu_r_prev = FALSE; }

    $c2_r = $jos_history["c2_r"][$i];
    if($c2_r > 0) { $li_r_sum += 1; }
    if($li_r_prev === FALSE && $c2_r > 0) { if($li_r_first === FALSE) { $li_str .= "; "; } $li_str .= "start ".$start; $li_r_prev = TRUE; $li_r_first = FALSE; }
    if ($li_r_prev === TRUE && $c2_r <= 0) { $li_str .= " stop ".$stop; $li_r_prev = FALSE; }
    
    $c3_r = $jos_history["c3_r"][$i];
    if($c3_r > 0) { $bi_r_sum += 1; }
    if($bi_r_prev === FALSE && $c3_r > 0) { if($bi_r_first === FALSE) { $bi_str .= "; "; } $bi_str .= "start ".$start; $bi_r_prev = TRUE; $bi_r_first = FALSE; }
    if ($bi_r_prev === TRUE && $c3_r <= 0) { $bi_str .= " stop ".$stop; $bi_r_prev = FALSE; }
    
    $c4_r = $jos_history["c4_r"][$i];
    if($c4_r > 0) { $bj_r_sum += 1; }
    if($bj_r_prev === FALSE && $c4_r > 0) { if($bj_r_first === FALSE) { $bj_str .= "; "; } $bj_str .= "start ".$start; $bj_r_prev = TRUE; $bj_r_first = FALSE; }
    if ($bj_r_prev === TRUE && $c4_r <= 0) { $bj_str .= " stop ".$stop; $bj_r_prev = FALSE; }
    
    $jos_r = $c1_r + $c2_r + $c3_r + $c4_r;
    $jos_r = ($jos_r <= 0) ? 0 : 1;
    if($jos_r > 0) { $jos_r_sum += 1; }
    if($jos_r_prev === FALSE && $jos_r > 0) { if($jos_r_first === FALSE) { $jos_str .= "; "; } $jos_str .= "start ".$start; $jos_r_prev = TRUE; $jos_r_first = FALSE; }
    if ($jos_r_prev === TRUE && $jos_r <= 0) { $jos_str .= " stop ".$stop; $jos_r_prev = FALSE; }
    $sum_history_r[$time][0] = $jos_r;
  }
}
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
    if($c1_r > 0) { $dl_r_sum += 1; }
    if($dl_r_prev === FALSE && $c1_r > 0) { if($dl_r_first === FALSE) { $dl_str .= "; "; } $dl_str .= "start ".$start; $dl_r_prev = TRUE; $dl_r_first = FALSE; }
    if ($dl_r_prev === TRUE && $c1_r <= 0) { $dl_str .= " stop ".$stop; $dl_r_prev = FALSE; }
    
    $c2_r = $sus_history["c2_r"][$i];
    if($c2_r > 0) { $dm_r_sum += 1; }
    if($dm_r_prev === FALSE && $c2_r > 0) { if($dm_r_first === FALSE) { $dm_str .= "; "; } $dm_str .= "start ".$start; $dm_r_prev = TRUE; $dm_r_first = FALSE; }
    if ($dm_r_prev === TRUE && $c2_r <= 0) { $dm_str .= " stop ".$stop; $dm_r_prev = FALSE; }
    
    $c3_r = $sus_history["c3_r"][$i];
    if($c3_r > 0) { $d3_r_sum += 1; }
    if($d3_r_prev === FALSE && $c3_r > 0) { if($d3_r_first === FALSE) { $d3_str .= "; "; } $d3_str .= "start ".$start; $d3_r_prev = TRUE; $d3_r_first = FALSE; }
    if ($d3_r_prev === TRUE && $c3_r <= 0) { $d3_str .= " stop ".$stop; $d3_r_prev = FALSE; }
    
    $c4_r = $sus_history["c4_r"][$i];
    if($c4_r > 0) { $bs_r_sum += 1; }
    if($bs_r_prev === FALSE && $c4_r > 0) { if($bs_r_first === FALSE) { $bs_str .= "; "; } $bs_str .= "start ".$start; $bs_r_prev = TRUE; $bs_r_first = FALSE; }
    if ($bs_r_prev === TRUE && $c4_r <= 0) { $bs_str .= " stop ".$stop; $bs_r_prev = FALSE; }
    
    $sus_r = $c1_r + $c2_r + $c3_r + $c4_r;
    $sus_r = ($sus_r <= 0) ? 0 : 1;
    if($sus_r > 0) { $sus_r_sum += 1; }
    if($sus_r_prev === FALSE && $sus_r > 0) { if($sus_r_first === FALSE) { $sus_str .= "; "; } $sus_str .= "start ".$start; $sus_r_prev = TRUE; $sus_r_first = FALSE; }
    if ($sus_r_prev === TRUE && $sus_r <= 0) { $sus_str .= " stop ".$stop; $sus_r_prev = FALSE; }
    $sum_history_r[$time][1] = $sus_r;
  }
}
foreach ($sum_history_r as $time => $run)
{
  sscanf($time, "%d:%d-%s", $start_h, $start_m, $stop);
  $start = sprintf("%02d:%02d", $start_h, $start_m);
  
  $all_r = ((isset($sum_history_r[$time][0]) && $sum_history_r[$time][0] > 0) || (isset($sum_history_r[$time][1]) && $sum_history_r[$time][1] > 0)) ? 1 : 0;
  if($all_r > 0) { $all_r_sum += 1; }
  if($all_r_prev === FALSE && $all_r > 0) { if($all_r_first === FALSE) { $all_str .= "; "; } $all_str .= "start ".$start; $all_r_prev = TRUE; $all_r_first = FALSE; }
  if ($all_r_prev === TRUE && $all_r <= 0) { $all_str .= " stop ".$stop; $all_r_prev = FALSE; }
}

if ($bu_r_sum <= 0) { $bu_str .= "nu a mers"; }
else { $mers = $bu_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; if($h === 0) { $bu_str .= "; a mers ".$m." minute"; } else { $bu_str .= "; a mers ".$h." ore si ".$m." minute"; } $bu_str .= " (".$mers." minute)"; }

if ($li_r_sum <= 0) { $li_str .= "nu a mers"; }
else { $mers = $li_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; if($h === 0) { $li_str .= "; a mers ".$m." minute"; } else { $li_str .= "; a mers ".$h." ore si ".$m." minute"; } $li_str .= " (".$mers." minute)"; }

if ($bi_r_sum <= 0) { $bi_str .= "nu a mers"; }
else { $mers = $bi_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; if($h === 0) { $bi_str .= "; a mers ".$m." minute"; } else { $bi_str .= "; a mers ".$h." ore si ".$m." minute"; } $bi_str .= " (".$mers." minute)"; }

if ($bj_r_sum <= 0) { $bj_str .= "nu a mers"; }
else { $mers = $bj_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; if($h === 0) { $bj_str .= "; a mers ".$m." minute"; } else { $bj_str .= "; a mers ".$h." ore si ".$m." minute"; } $bj_str .= " (".$mers." minute)"; }

if ($jos_r_sum <= 0) { $jos_str .= "nu a mers"; }
else { $mers = $jos_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; if($h === 0) { $jos_str .= "; a mers ".$m." minute"; } else { $jos_str .= "; a mers ".$h." ore si ".$m." minute"; } $jos_str .= " (".$mers." minute)"; }

if ($dl_r_sum <= 0) { $dl_str .= "nu a mers"; }
else { $mers = $dl_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; if($h === 0) { $dl_str .= "; a mers ".$m." minute"; } else { $dl_str .= "; a mers ".$h." ore si ".$m." minute"; } $dl_str .= " (".$mers." minute)"; }

if ($dm_r_sum <= 0) { $dm_str .= "nu a mers"; }
else { $mers = $dm_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; if($h === 0) { $dm_str .= "; a mers ".$m." minute"; } else { $dm_str .= "; a mers ".$h." ore si ".$m." minute"; } $dm_str .= " (".$mers." minute)"; }

if ($d3_r_sum <= 0) { $d3_str .= "nu a mers"; }
else { $mers = $d3_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; if($h === 0) { $d3_str .= "; a mers ".$m." minute"; } else { $d3_str .= "; a mers ".$h." ore si ".$m." minute"; } $d3_str .= " (".$mers." minute)"; }

if ($bs_r_sum <= 0) { $bs_str .= "nu a mers"; }
else { $mers = $bs_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; if($h === 0) { $bs_str .= "; a mers ".$m." minute"; } else { $bs_str .= "; a mers ".$h." ore si ".$m." minute"; } $bs_str .= " (".$mers." minute)"; }

if ($sus_r_sum <= 0) { $sus_str .= "nu a mers"; }
else { $mers = $sus_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; if($h === 0) { $sus_str .= "; a mers ".$m." minute"; } else { $sus_str .= "; a mers ".$h." ore si ".$m." minute"; } $sus_str .= " (".$mers." minute)"; }

if ($all_r_sum <= 0) { $all_str .= "nu a mers"; }
else { $mers = $all_r_sum * 10; $m = $mers % 60; $h = ($mers - $m) / 60; if($h === 0) { $all_str .= "; a mers ".$m." minute"; } else { $all_str .= "; a mers ".$h." ore si ".$m." minute"; } $all_str .= " (".$mers." minute)"; }

echo "bu ".$bu_str.PHP_EOL;
echo "li ".$li_str.PHP_EOL;
echo "bi ".$bi_str.PHP_EOL;
echo "bj ".$bj_str.PHP_EOL;
echo "dl ".$dl_str.PHP_EOL;
echo "dm ".$dm_str.PHP_EOL;
echo "d3 ".$d3_str.PHP_EOL;
echo "bs ".$bs_str.PHP_EOL;
echo "jos ".$jos_str.PHP_EOL;
echo "sus ".$sus_str.PHP_EOL;
echo "total ".$all_str.PHP_EOL;
?>
