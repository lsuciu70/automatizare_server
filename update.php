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
//   $post_data = "";
//   foreach ($_POST as $key => $value)
//   {
//     $post_data .= "&".$key."=".$value;
//   }
//   $post_data = substr($post_data, 1);
//   error_log($post_data);

$LOC_SUS = "etaj";
$LOC_JOS = "parter";

$is_jos = FALSE;
$is_sus = FALSE;
$is_dbox = FALSE;

$sum_file = "";
$avg_file = "";
$temp_file = "";
$temp_day_file = "";
$dbox_file = "";

$t_loc=htmlspecialchars($_POST["t_loc"]);
if (strpos($t_loc, $LOC_JOS) !== FALSE)
{
  $sum_file = "data/jos_sum.txt";
  $avg_file = "data/jos_avg.txt";
  $temp_file = "data/jos_temp.txt";
  $is_jos = TRUE;
}
else if (strpos($t_loc, $LOC_SUS) !== FALSE)
{
  $sum_file = "data/sus_sum.txt";
  $avg_file = "data/sus_avg.txt";
  $temp_file = "data/sus_temp.txt";
  $is_sus = TRUE;
}
else
{
  error_log("ERROR - Unknown t_loc: ".$t_loc);
  exit("ERROR - Unknown t_loc: ".$t_loc);
}

// get current date and time
$ts_full = strftime("%Y.%m.%d_%H:%M:%S");
sscanf($ts_full, "%d.%d.%d_%d:%d:%d", $ts_Y, $ts_M, $ts_D, $ts_h, $ts_m, $ts_s);

// read last sums
$s_t0 = 0; $s_t1 = 0; $s_t2 = 0; $s_t3 = 0; $s_jos = 0;
$s_t0_r = 0; $s_t1_r = 0; $s_t2_r = 0; $s_t3_r = 0;
if (is_file($sum_file))
{
  $jos_sum = file_get_contents($sum_file);
  sscanf($jos_sum, "%d,%d,%d,%d,%d,%d,%d,%d,%d", $s_t0, $s_t0_r, $s_t1, $s_t1_r, $s_t2, $s_t2_r, $s_t3, $s_t3_r, $s_jos);
}

// read previous temperatures
if (is_file($temp_file))
{
  $jos_temp = file_get_contents($temp_file);
  sscanf($jos_temp, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_t0, $t_t0_r, $t_t1, $t_t1_r, $t_t2, $t_t2_r, $t_t3, $t_t3_r, $ts_jos);
  sscanf($ts_jos, "%d.%d.%d_%d:%d:%d", $ts_pY, $ts_pM, $ts_pD, $ts_ph, $ts_pm, $ts_ps);

  // check next 10'th minutes
  if (($ts_m - ($ts_m % 10)) != ($ts_pm - ($ts_pm % 10)))
  {
    // minute changed
    //  -> calculate average and save to day file
    if ($s_jos > 0)
    {
      $s_t0 = intval($s_t0 / $s_jos); $s_t1 = intval($s_t1 / $s_jos); $s_t2 = intval($s_t2 / $s_jos); $s_t3 = intval($s_t3 / $s_jos);
    }
    $s_t0_r = ($s_t0_r > 0) ? 1 : 0; $s_t1_r = ($s_t1_r > 0) ? 1 : 0; $s_t2_r = ($s_t2_r > 0) ? 1 : 0; $s_t3_r = ($s_t3_r > 0) ? 1 : 0;

    if($is_jos)
      $dbox_file = "jos_" . $ts_pY . "." . $ts_pM . "." . $ts_pD . ".txt";
    else if($is_sus)
      $dbox_file = "sus_" . $ts_pY . "." . $ts_pM . "." . $ts_pD . ".txt";
    else
      exit("ERROR - Unknown t_loc: ".$t_loc);
    $temp_day_file = "data/". $dbox_file;
    $is_dbox = TRUE;

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
      
    $line = "" . $s_t0 . ","  . $s_t0_r . "," . $s_t1 . "," . $s_t1_r . "," . $s_t2 . "," . $s_t2_r . "," . $s_t3 . "," . $s_t3_r . "," . $day_time . PHP_EOL;
    file_put_contents($temp_day_file, $line, FILE_APPEND | LOCK_EX);

    //  -> remove sum file
    if (is_file($sum_file))
      unlink($sum_file);
    // reset
     $s_t0 = 0; $s_t1 = 0; $s_t2 = 0; $s_t3 = 0; $s_jos = 0;
     $s_t0_r = 0; $s_t1_r = 0; $s_t2_r = 0; $s_t3_r = 0;
  }
}
// get POST values
$t_t0=htmlspecialchars($_POST["t_0"]);
$t_t0_r=htmlspecialchars($_POST["t_0_r"]);
$t_t1=htmlspecialchars($_POST["t_1"]);
$t_t1_r=htmlspecialchars($_POST["t_1_r"]);
$t_t2=htmlspecialchars($_POST["t_2"]);
$t_t2_r=htmlspecialchars($_POST["t_2_r"]);
$t_t3=htmlspecialchars($_POST["t_3"]);
$t_t3_r=htmlspecialchars($_POST["t_3_r"]);

// write to temperature file
$line = "" . $t_t0 . "," . $t_t0_r . "," . $t_t1 . "," . $t_t1_r . "," . $t_t2 . "," . $t_t2_r . "," . $t_t3 . "," . $t_t3_r . "," . $ts_full . PHP_EOL;
file_put_contents($temp_file, $line, LOCK_EX);

// write to sum file
$s_t0 += $t_t0; $s_t1 += $t_t1; $s_t2 += $t_t2; $s_t3 += $t_t3; $s_jos += 1;
$s_t0_r += $t_t0_r; $s_t1_r += $t_t1_r; $s_t2_r += $t_t2_r; $s_t3_r += $t_t3_r;
$line = "" . $s_t0 . "," . $s_t0_r . "," . $s_t1 . "," . $s_t1_r . "," . $s_t2 . "," . $s_t2_r . "," . $s_t3 . "," . $s_t3_r . "," . $s_jos . PHP_EOL;
file_put_contents($sum_file, $line, LOCK_EX);

// write to average file
if ($s_jos > 0)
{
  $s_t0 = intval($s_t0 / $s_jos); $s_t1 = intval($s_t1 / $s_jos); $s_t2 = intval($s_t2 / $s_jos); $s_t3 = intval($s_t3 / $s_jos);
}
$s_t0_r = ($s_t0_r > 0) ? 1 : 0; $s_t1_r = ($s_t1_r > 0) ? 1 : 0; $s_t2_r = ($s_t2_r > 0) ? 1 : 0; $s_t3_r = ($s_t3_r > 0) ? 1 : 0;
  
$m_minus = $ts_m % 10;

$m_start = $ts_m - $m_minus;
$h_start = $ts_h;
$m_stop = $ts_m;
$h_stop = $ts_h;
if($m_start === $m_stop)
  $day_time = sprintf("%02d:%02d", $h_start, $m_start);
else
  $day_time = sprintf("%02d:%02d-%02d:%02d", $h_start, $m_start, $h_stop, $m_stop);

$line = "" . $s_t0 . ","  . $s_t0_r . "," . $s_t1 . "," . $s_t1_r . "," . $s_t2 . "," . $s_t2_r . "," . $s_t3 . "," . $s_t3_r . "," . $day_time;
file_put_contents($avg_file, $line, LOCK_EX);

// save to Dropbox
if($is_dbox)
{
  include_once "dbox.php";

  $curl_cmd = 'curl -k -X POST https://content.dropboxapi.com/2/files/upload '.
      '--header "Authorization: Bearer '.$dbox_k.'" '.
      '--header "Dropbox-API-Arg: {\"path\": \"/'.$dbox_file.'\", \"mode\": \"overwrite\"}" '.
      '--header "Content-Type: application/octet-stream" '.
      '--data-binary @'.$temp_day_file;
  
  if(system($curl_cmd." &", $retval) === FALSE)
    error_log($retval);
}
?>
