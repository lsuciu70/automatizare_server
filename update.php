<?php
//// setlocale(LC_NUMERIC, 'C');
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
// error_log($_POST);

// get current date and time
$ts_full = strftime("%Y.%m.%d_%H:%M:%S");
sscanf($ts_full, "%d.%d.%d_%d:%d:%d", $ts_Y, $ts_M, $ts_D, $ts_h, $ts_m, $ts_s);

$LOC_SUS = "etaj";
$LOC_JOS = "parter";
$t_loc=htmlspecialchars($_POST["t_loc"]);
if (strpos($t_loc, $LOC_JOS) !== FALSE)
{

//   $post_data = "";
//   foreach ($_POST as $key => $value)
//   {
//     $post_data .= "&".$key."=".$value;
//   }
//   $post_data = substr($post_data, 1);
//   error_log($post_data);

  $sum_file = "data/jos_sum.txt";
  $avg_file = "data/jos_avg.txt";
  $temp_file = "data/jos_temp.txt";
  // read last sums
  if (is_file($sum_file))
  {
    $jos_sum = file_get_contents($sum_file);
    sscanf($jos_sum, "%d,%d,%d,%d,%d,%d,%d,%d,%d", $s_bu, $s_bu_r, $s_li, $s_li_r, $s_bi, $s_bi_r, $s_bj, $s_bj_r, $s_jos);
  }
  else
  {
     $s_bu = 0; $s_li = 0; $s_bi = 0; $s_bj = 0; $s_jos = 0;
     $s_bu_r = 0; $s_li_r = 0; $s_bi_r = 0; $s_bj_r = 0;
  }
  // read previous temperatures
  if (is_file($temp_file))
  {
    $jos_temp = file_get_contents($temp_file);
    sscanf($jos_temp, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_bu, $t_bu_r, $t_li, $t_li_r, $t_bi, $t_bi_r, $t_bj, $t_bj_r, $ts_jos);
    sscanf($ts_jos, "%d.%d.%d_%d:%d:%d", $ts_pY, $ts_pM, $ts_pD, $ts_ph, $ts_pm, $ts_ps);

    // check next 10'th minutes
    if (($ts_m - ($ts_m % 10)) != ($ts_pm - ($ts_pm % 10)))
    {
      if ($ts_D != $ts_pD)
      {
        // day changed -> save to Dropbox
      }
      // minute changed
      //  -> calculate average and save to day file
      if ($s_jos > 0)
      {
        $s_bu = intval($s_bu / $s_jos); $s_li = intval($s_li / $s_jos); $s_bi = intval($s_bi / $s_jos); $s_bj = intval($s_bj / $s_jos);
      }
      $s_bu_r = ($s_bu_r > 0) ? 1 : 0; $s_li_r = ($s_li_r > 0) ? 1 : 0; $s_bi_r = ($s_bi_r > 0) ? 1 : 0; $s_bj_r = ($s_bj_r > 0) ? 1 : 0;
      $temp_day_file = "data/jos_" . $ts_pY . "." . $ts_pM . "." . $ts_pD . ".txt";
      
      $m_minus = $ts_pm % 10;
      $m_plus = 10 - $m_minus;
      
      $m_start = $ts_pm - $m_minus;
      $h_start = $ts_ph;
      $m_stop = $ts_pm + $m_plus;
      $h_stop = $ts_ph;
      if($m_stop >= 60)
      {
        $m_stop = $m_stop % 60;
        $h_stop = ($h_stop + 1) % 24;
      }
      $day_time = sprintf("%02d:%02d-%02d:%02d", $h_start, $m_start, $h_stop, $m_stop);
      
      $line = "" . $s_bu . ","  . $s_bu_r . "," . $s_li . "," . $s_li_r . "," . $s_bi . "," . $s_bi_r . "," . $s_bj . "," . $s_bj_r . "," . $day_time . PHP_EOL;
      file_put_contents($temp_day_file, $line, FILE_APPEND | LOCK_EX);
      //  -> remove sum file
      if (is_file($sum_file))
        unlink($sum_file);
      // reset
       $s_bu = 0; $s_li = 0; $s_bi = 0; $s_bj = 0; $s_jos = 0;
       $s_bu_r = 0; $s_li_r = 0; $s_bi_r = 0; $s_bj_r = 0;
    }
  }
  // get POST values
  $t_bu=htmlspecialchars($_POST["t_0"]);
  $t_bu_r=htmlspecialchars($_POST["t_0_r"]);
  $t_li=htmlspecialchars($_POST["t_1"]);
  $t_li_r=htmlspecialchars($_POST["t_1_r"]);
  $t_bi=htmlspecialchars($_POST["t_2"]);
  $t_bi_r=htmlspecialchars($_POST["t_2_r"]);
  $t_bj=htmlspecialchars($_POST["t_3"]);
  $t_bj_r=htmlspecialchars($_POST["t_3_r"]);

  // write to temperature file
  $line = "" . $t_bu . "," . $t_bu_r . "," . $t_li . "," . $t_li_r . "," . $t_bi . "," . $t_bi_r . "," . $t_bj . "," . $t_bj_r . "," . $ts_full . PHP_EOL;
  file_put_contents($temp_file, $line, LOCK_EX);

  // write to sum file
  $s_bu += $t_bu; $s_li += $t_li; $s_bi += $t_bi; $s_bj += $t_bj; $s_jos += 1;
  $s_bu_r += $t_bu_r; $s_li_r += $t_li_r; $s_bi_r += $t_bi_r; $s_bj_r += $t_bj_r;

  $line = "" . $s_bu . "," . $s_bu_r . "," . $s_li . "," . $s_li_r . "," . $s_bi . "," . $s_bi_r . "," . $s_bj . "," . $s_bj_r . "," . $s_jos . PHP_EOL;
  file_put_contents($sum_file, $line, LOCK_EX);

  // write to average file
  if ($s_jos > 0)
  {
    $s_bu = intval($s_bu / $s_jos); $s_li = intval($s_li / $s_jos); $s_bi = intval($s_bi / $s_jos); $s_bj = intval($s_bj / $s_jos);
  }
  $s_bu_r = ($s_bu_r > 0) ? 1 : 0; $s_li_r = ($s_li_r > 0) ? 1 : 0; $s_bi_r = ($s_bi_r > 0) ? 1 : 0; $s_bj_r = ($s_bj_r > 0) ? 1 : 0;
  
  $m_minus = $ts_m % 10;

  $m_start = $ts_m - $m_minus;
  $h_start = $ts_h;
  $m_stop = $ts_m;
  $h_stop = $ts_h;
  if($m_start === $m_stop)
    $day_time = sprintf("%02d:%02d", $h_start, $m_start);
  else
    $day_time = sprintf("%02d:%02d-%02d:%02d", $h_start, $m_start, $h_stop, $m_stop);

  $line = "" . $s_bu . ","  . $s_bu_r . "," . $s_li . "," . $s_li_r . "," . $s_bi . "," . $s_bi_r . "," . $s_bj . "," . $s_bj_r . "," . $day_time;
  file_put_contents($avg_file, $line, LOCK_EX);
}
else if (strpos($t_loc, $LOC_SUS) !== FALSE)
{
  $sum_file = "data/sus_sum.txt";
  $avg_file = "data/sus_avg.txt";
  $temp_file = "data/sus_temp.txt";
  if (is_file($sum_file))
  {
    $sus_sum = file_get_contents($sum_file);
    sscanf($sus_sum, "%d,%d,%d,%d,%d,%d,%d,%d,%d", $s_dl, $s_dl_r, $s_dm, $s_dm_r, $s_d3, $s_d3_r, $s_bs, $s_bs_r, $s_sus);
  }
  else
  {
    $s_dl = 0; $s_dm = 0; $s_d3 = 0; $s_bs = 0; $s_sus = 0;
    $s_dl_r = 0; $s_dm_r = 0; $s_d3_r = 0; $s_bs_r = 0;
  }
  // read previous temperatures
  if (is_file($temp_file))
  {
    $sus_temp = file_get_contents($temp_file);
    sscanf($sus_temp, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_dl, $t_dl_r, $t_dm, $t_dm_r, $t_d3, $t_d3_r, $t_bs, $t_bs_r, $ts_sus);
    sscanf($ts_sus, "%d.%d.%d_%d:%d:%d", $ts_pY, $ts_pM, $ts_pD, $ts_ph, $ts_pm, $ts_ps);
    // check next 10'th minute
    if (($ts_m - ($ts_m % 10)) != ($ts_pm - ($ts_pm % 10)))
    {
      // check day changed
      if ($ts_D != $ts_pD)
      {
        // day changed -> save to Dropbox
      }
      // minute changed
      //  -> calculate average and save to day file
      if ($s_sus > 0)
      {
        $s_dl = intval($s_dl / $s_sus); $s_dm = intval($s_dm / $s_sus); $s_d3 = intval($s_d3 / $s_sus); $s_bs = intval($s_bs / $s_sus);
      }
      $s_dl_r = ($s_dl_r > 0) ? 1 : 0; $s_dm_r = ($s_dm_r > 0) ? 1 : 0; $s_d3_r = ($s_d3_r > 0) ? 1 : 0; $s_bs_r = ($s_bs_r > 0) ? 1 : 0;
      $temp_day_file = "data/sus_" . $ts_pY . "." . $ts_pM . "." . $ts_pD . ".txt";

      $m_minus = $ts_pm % 10;
      $m_plus = 10 - $m_minus;
      
      $m_start = $ts_pm - $m_minus;
      $h_start = $ts_ph;
      $m_stop = $ts_pm + $m_plus;
      $h_stop = $ts_ph;
      if($m_stop >= 60)
      {
        $m_stop = $m_stop % 60;
        $h_stop = ($h_stop + 1) % 24;
      }
      $day_time = sprintf("%02d:%02d-%02d:%02d", $h_start, $m_start, $h_stop, $m_stop);

      $line = "" . $s_dl . "," . $s_dl_r . "," . $s_dm . "," . $s_dm_r . "," . $s_d3 . "," . $s_d3_r . "," . $s_bs . "," . $s_bs_r . "," . $day_time . PHP_EOL;
      file_put_contents($temp_day_file, $line, FILE_APPEND | LOCK_EX);
      //  -> remove sum file
      if (is_file($sum_file))
        unlink($sum_file);
      // reset
      $s_dl = 0; $s_dm = 0; $s_d3 = 0; $s_bs = 0; $s_sus = 0;
      $s_dl_r = 0; $s_dm_r = 0; $s_d3_r = 0; $s_bs_r = 0;
    }
  }
  // get POST values
  $t_dl=htmlspecialchars($_POST["t_0"]);
  $t_dl_r=htmlspecialchars($_POST["t_0_r"]);
  $t_dm=htmlspecialchars($_POST["t_1"]);
  $t_dm_r=htmlspecialchars($_POST["t_1_r"]);
  $t_d3=htmlspecialchars($_POST["t_2"]);
  $t_d3_r=htmlspecialchars($_POST["t_2_r"]);
  $t_bs=htmlspecialchars($_POST["t_3"]);
  $t_bs_r=htmlspecialchars($_POST["t_3_r"]);

  // write to file
  $line = "" . $t_dl . "," . $t_dl_r . "," . $t_dm . "," . $t_dm_r . "," . $t_d3 . "," . $t_d3_r . "," . $t_bs . "," . $t_bs_r . "," . $ts_full . PHP_EOL;
  file_put_contents($temp_file, $line, LOCK_EX);

  // write to sum file
  $s_dl += $t_dl; $s_dm += $t_dm; $s_d3 += $t_d3; $s_bs += $t_bs; $s_sus += 1;
  $s_dl_r += $t_dl_r; $s_dm_r += $t_dm_r; $s_d3_r += $t_d3_r; $s_bs_r += $t_bs_r;
  $line = "" . $s_dl . "," . $s_dl_r . "," . $s_dm . "," . $s_dm_r . "," . $s_d3 . "," . $s_d3_r . "," . $s_bs . "," . $s_bs_r . "," . $s_sus . PHP_EOL;
  file_put_contents($sum_file, $line, LOCK_EX);

  // write to average file
  if ($s_sus > 0)
  {
    $s_dl = intval($s_dl / $s_sus); $s_dm = intval($s_dm / $s_sus); $s_d3 = intval($s_d3 / $s_sus); $s_bs = intval($s_bs / $s_sus);
  }
  $s_dl_r = ($s_dl_r > 0) ? 1 : 0; $s_dm_r = ($s_dm_r > 0) ? 1 : 0; $s_d3_r = ($s_d3_r > 0) ? 1 : 0; $s_bs_r = ($s_bs_r > 0) ? 1 : 0;

  $m_minus = $ts_m % 10;

  $m_start = $ts_m - $m_minus;
  $h_start = $ts_h;
  $m_stop = $ts_m;
  $h_stop = $ts_h;
  if($m_start === $m_stop)
    $day_time = sprintf("%02d:%02d", $h_start, $m_start);
  else
    $day_time = sprintf("%02d:%02d-%02d:%02d", $h_start, $m_start, $h_stop, $m_stop);

  $line = "" . $s_dl . "," . $s_dl_r . "," . $s_dm . "," . $s_dm_r . "," . $s_d3 . "," . $s_d3_r . "," . $s_bs . "," . $s_bs_r . "," . $day_time;
  file_put_contents($avg_file, $line, LOCK_EX);
}
else
{
  error_log("ERROR - Unknown t_loc: ".$t_loc);
  exit("ERROR - Unknown t_loc: ".$t_loc);
}
?>
