<?php

$t_loc = "etaj";

$LOC_SUS = "etaj";
$LOC_JOS = "parter";

$t_loc_file = "";
$c1_str = "";
$c2_str = "";
$c3_str = "";
$c4_str = "";

if (strpos($t_loc, $LOC_JOS) !== FALSE)
{
  $t_loc_file = "jos";
  $c1_str = "Bucatarie";
  $c2_str = "Living";
  $c3_str = "Birou";
  $c4_str = "Baie parter";
}
else if (strpos($t_loc, $LOC_SUS) !== FALSE)
{
  $t_loc_file = "sus";
  $c1_str = "Dormitor Luca";
  $c2_str = "Dormitor matrimonial";
  $c3_str = "Dormitor oaspeti";
  $c4_str = "Baie etaj";
}
else
{
  error_log("ERROR - Unknown t_loc: ".$t_loc);
  exit("ERROR - Unknown t_loc: ".$t_loc);
}

//// current date and time
$format = "%Y.%m.%d %H:%M:%S";
$now = strftime($format);
sscanf($now, "%d.%d.%d %d:%d:%d", $now_Y, $now_M, $now_D, $now_h, $now_m, $now_s);

$HOURS = 24;
$MEAS_PER_HOUR = 6;
$SIZE = $HOURS * $MEAS_PER_HOUR;

$is_today = TRUE;
$ts_Y = 0; $ts_M = 0; $ts_D = 0; $ts_h = 0; $ts_m = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST')
{
  if(isset($_POST["t_date"]) !== FALSE)
  {
    $date_time = htmlspecialchars($_POST["t_date"]);
    sscanf($date_time, "%d.%d.%d", $ts_Y, $ts_M, $ts_D);
    $is_today = FALSE;
  }
}
if($is_today)
{
  //// current date and time
  $date_time = $now;
  sscanf($date_time, "%d.%d.%d %d:%d:%d", $ts_Y, $ts_M, $ts_D, $ts_h, $ts_m, $ts_s);
}
else
{
  $is_today = $now_Y === $ts_Y && $now_M === $ts_M && $now_D === $ts_D;
}

if($is_today)
{
  $date_time = $now;
  sscanf($date_time, "%d.%d.%d %d:%d:%d", $ts_Y, $ts_M, $ts_D, $ts_h, $ts_m, $ts_s);
  // make client to refresh every 60 seconds
  $url1=$_SERVER['REQUEST_URI'];
  header("Refresh: 60; URL=$url1");
}

$t_day = sprintf("%d.%d.%d", $ts_Y, $ts_M, $ts_D);
$t_day_s = sprintf("%d/%d/%d", $ts_Y, $ts_M, $ts_D);

$y_day = date('Y.m.d', strtotime('-1 day', strtotime($t_day_s)));
sscanf($y_day, "%d.%d.%d", $ts_yY, $ts_yM, $ts_yD);
$y_day_s = sprintf("%d.%d.%d", $ts_yY, $ts_yM, $ts_yD);

$t_mrow = date('Y.m.d', strtotime('+1 day', strtotime($t_day_s)));
sscanf($t_mrow, "%d.%d.%d", $ts_tY, $ts_tM, $ts_tD);
$t_mrow_s = sprintf("%d.%d.%d", $ts_tY, $ts_tM, $ts_tD);

$image_file_name = "data/".$t_loc_file."_history.jpg";
$image_exist = FALSE;
$image_day_file_name = "data/".$t_loc_file."_history_".$t_day.".jpg";

//echo $y_day." ".$t_day." ".$t_mrow."\n";

$avg_file_name = "data/".$t_loc_file."_avg.txt";
$t_day_file_name = "data/".$t_loc_file."_" . $t_day . ".txt";
$y_day_file_name = "data/".$t_loc_file."_" . $y_day_s . ".txt";
$t_mrow_file_name = "data/".$t_loc_file."_" . $t_mrow_s . ".txt";
$log_file_name = "data/log_" . $t_day . ".txt";

$has_y_day = is_file($y_day_file_name);
$has_t_mrow = is_file($t_mrow_file_name);

$vn = array (
    "hm" => array(),
    "c1" => array(),
    "c1_r" => array(),
    "c2" => array(),
    "c2_r" => array(),
    "c3" => array(),
    "c3_r" => array(),
    "c4" => array(),
    "c4_r" => array(),
);

$c1_color_str = "green";
$c2_color_str = "orange";
$c3_color_str = "blue";
$c4_color_str = "red";

if($is_today)
{
  $SIZE += 1;

  $m_start = $ts_m - ($ts_m % 10);
  $h_start = $ts_h;
  $m_stop = $ts_m;
  $h_stop = $ts_h;
  $hm_start = sprintf("%02d:%02d", $h_start, $m_start);
  if($m_start === $ts_m)
    $hm_start_stop = $hm_start;
  else
    $hm_start_stop = sprintf("%02d:%02d-%02d:%02d", $h_start, $m_start, $h_stop, $m_stop);

  $vn["hm"][0] = $hm_start_stop;
  for($i = 1; $i < $SIZE; ++$i)
  {
    $m_stop = $m_start;
    $h_stop = $h_start;
    $m_start -= 10;
    if($m_start < 0)
    {
      $m_start = 50;
      $h_start -= 1;
    }
    if($h_start < 0)
    {
      $h_start = 23;
    }
    $hm_start_stop = sprintf("%02d:%02d-%02d:%02d", $h_start, $m_start, $h_stop, $m_stop);
    $vn["hm"][$i] = $hm_start_stop;
  }

  $ok = FALSE;
  if(is_file($avg_file_name) && ($avg = file_get_contents($avg_file_name)) !== FALSE)
  {
    sscanf($avg, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_c1, $t_c1_r, $t_c2, $t_c2_r, $t_c3, $t_c3_r, $t_c4, $t_c4_r, $avg_dt);
    if(strpos($avg_dt, $hm_start) === 0)
    {
      // ok
      $ok = TRUE;
      $vn["c1"][0] = $t_c1;
      $vn["c1_r"][0] = $t_c1_r;
      $vn["c2"][0] = $t_c2;
      $vn["c2_r"][0] = $t_c2_r;
      $vn["c3"][0] = $t_c3;
      $vn["c3_r"][0] = $t_c3_r;
      $vn["c4"][0] = $t_c4;
      $vn["c4_r"][0] = $t_c4_r;
    }
  }
  if($ok === FALSE)
  {
    $vn["c1"][0] = 0;
    $vn["c1_r"][0] = 0;
    $vn["c2"][0] = 0;
    $vn["c2_r"][0] = 0;
    $vn["c3"][0] = 0;
    $vn["c3_r"][0] = 0;
    $vn["c4"][0] = 0;
    $vn["c4_r"][0] = 0;
  }

  $lines = array();
  $count = $SIZE;
  if(is_file($t_day_file_name))
  {
    $file = new SplFileObject($t_day_file_name);
    if($file->isReadable())
    {
      $file->seek($file->getSize());
      $curr_line = $file->key();
      $count = 0;
      while ((--$curr_line) >= 0 && (++$count) <= $SIZE)
      {
        $file->seek($curr_line);
        $line = $file->current();
        if(($idx = strrpos($line, ",")) !== FALSE)
        {
          $key = substr($line, $idx + 1, 11);
        }
        $lines[$key] = $line;
      }
    }
  }
  $SIZE = $count + 1;

  for($i = 1; $i < $SIZE; ++$i)
  {
    $key = $vn["hm"][$i];
    if(array_key_exists($key, $lines))
    {
      $line = $lines[$key];
      sscanf($line, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_c1, $t_c1_r, $t_c2, $t_c2_r, $t_c3, $t_c3_r, $t_c4, $t_c4_r, $ts_dt);
      $vn["c1"][$i] = $t_c1;
      $vn["c1_r"][$i] = $t_c1_r;
      $vn["c2"][$i] = $t_c2;
      $vn["c2_r"][$i] = $t_c2_r;
      $vn["c3"][$i] = $t_c3;
      $vn["c3_r"][$i] = $t_c3_r;
      $vn["c4"][$i] = $t_c4;
      $vn["c4_r"][$i] = $t_c4_r;
    }
    else
    {
      $vn["c1"][$i] = 0;
      $vn["c1_r"][$i] = 0;
      $vn["c2"][$i] = 0;
      $vn["c2_r"][$i] = 0;
      $vn["c3"][$i] = 0;
      $vn["c3_r"][$i] = 0;
      $vn["c4"][$i] = 0;
      $vn["c4_r"][$i] = 0;
    }
  }
} // is_today
else
{
  $image_file_name = $image_day_file_name;
  $image_exist = is_file($image_file_name);
  $m_start = $ts_m;
  $h_start = $ts_h;
  for($i = 0; $i < $SIZE; ++$i)
  {
    $m_stop = $m_start;
    $h_stop = $h_start;
    $m_start -= 10;
    if($m_start < 0)
    {
      $m_start = 50;
      $h_start -= 1;
    }
    if($h_start < 0)
    {
      $h_start = 23;
    }
    $hm_start_stop = sprintf("%02d:%02d-%02d:%02d", $h_start, $m_start, $h_stop, $m_stop);
    $vn["hm"][$i] = $hm_start_stop;
  }

  if(is_file($t_day_file_name))
  {
    $file = new SplFileObject($t_day_file_name);
    if($file->isReadable())
    {
      $file->seek($file->getSize());
      $curr_line = $file->key();
      $count = 0;
      while ((--$curr_line) >= 0 && (++$count) <= $SIZE)
      {
        $file->seek($curr_line);
        $line = $file->current();
        if(($idx = strrpos($line, ",")) !== FALSE)
        {
          $key = substr($line, $idx + 1, 11);
        }
        $lines[$key] = $line;
      }
    }
  }

  for($i = 0; $i < $SIZE; ++$i)
  {
    $key = $vn["hm"][$i];
    if(array_key_exists($key, $lines))
    {
      $line = $lines[$key];
      sscanf($line, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_c1, $t_c1_r, $t_c2, $t_c2_r, $t_c3, $t_c3_r, $t_c4, $t_c4_r, $ts_dt);
      $vn["c1"][$i] = $t_c1;
      $vn["c1_r"][$i] = $t_c1_r;
      $vn["c2"][$i] = $t_c2;
      $vn["c2_r"][$i] = $t_c2_r;
      $vn["c3"][$i] = $t_c3;
      $vn["c3_r"][$i] = $t_c3_r;
      $vn["c4"][$i] = $t_c4;
      $vn["c4_r"][$i] = $t_c4_r;
    }
    else
    {
      $vn["c1"][$i] = 0;
      $vn["c1_r"][$i] = 0;
      $vn["c2"][$i] = 0;
      $vn["c2_r"][$i] = 0;
      $vn["c3"][$i] = 0;
      $vn["c3_r"][$i] = 0;
      $vn["c4"][$i] = 0;
      $vn["c4_r"][$i] = 0;
    }
  }
}

//// images
if(is_file("/usr/share/fonts/truetype/freefont/FreeSerif.ttf"))
  $fontfile = "/usr/share/fonts/truetype/freefont/FreeSerif.ttf";
else if(is_file("/opt/share/fonts/bitstream-vera/VeraSe.ttf"))
  $fontfile = "/opt/share/fonts/bitstream-vera/VeraSe.ttf";

$max_all = 0; $min_all = 0;
$max_c1 = 0; $min_c1 = 0;
$max_c2 = 0; $min_c2 = 0;
$max_c3 = 0; $min_c3 = 0;
$max_c4 = 0; $min_c4 = 0;

for($i = 0; $i < $SIZE; ++$i)
{
  if(($c1 = $vn["c1"][$i]) > 0)
  {
    if($max_c1 < $c1) $max_c1 = $c1;
    if($min_c1 === 0 || $min_c1 > $c1) $min_c1 = $c1;
    if($max_all < $max_c1) $max_all = $max_c1;
    if($min_all === 0 || $min_all > $min_c1) $min_all = $min_c1;
  }
  if(($c2 = $vn["c2"][$i]) > 0)
  {
    if($max_c2 < $c2) $max_c2 = $c2;
    if($min_c2 === 0 || $min_c2 > $c2) $min_c2 = $c2;
    if($max_all < $max_c2) $max_all = $max_c2;
    if($min_all === 0 || $min_all > $min_c2) $min_all = $min_c2;
  }
  if(($c3 = $vn["c3"][$i]) > 0)
  {
    if($max_c3 < $c3) $max_c3 = $c3;
    if($min_c3 === 0 || $min_c3 > $c3) $min_c3 = $c3;
    if($max_all < $max_c3) $max_all = $max_c3;
    if($min_all === 0 || $min_all > $min_c3) $min_all = $min_c3;
  }
  if(($c4 = $vn["c4"][$i]) > 0)
  {
    if($max_c4 < $c4) $max_c4 = $c4;
    if($min_c4 === 0 || $min_c4 > $c4) $min_c4 = $c4;
    if($max_all < $max_c4) $max_all = $max_c4;
    if($min_all === 0 || $min_all > $min_c4) $min_all = $min_c4;
  }
}

$max_img = $max_all - ($max_all % 10) + 20;
$min_img = $min_all - ($min_all % 10) - 10;

// make graphic aprox 300 pixels high
$h_c = intval(3000 / ($max_img - $min_img)) / 10;

//// dot radius
$rad = 8;
//// font size
$fsz = 12;
//// height & width temperature coeficient
//$h_c = 1;
$w_c = 4;

//// temperature text width & height
$temp_box = imagettfbbox($fsz, 0, $fontfile, "00.00");
$temp_w = $temp_box[2] - $temp_box[0];
$temp_h = $temp_box[1] - $temp_box[7];
// echo "temp_w=".$temp_w.", temp_h=".$temp_h."\n";
//// time text width & height
$time_box = imagettfbbox($fsz, 0, $fontfile, "00:00-00:00");
$time_w = $time_box[2] - $time_box[0];
$time_h = $time_box[1] - $time_box[7];
// echo "time_w=".$time_w.", time_h=".$time_h."\n";

//// lelft and right gaps
$l_gap = 2 * $rad + $temp_w;
$r_gap = $l_gap;
//// bottom and top gaps
$b_gap = 3 * $rad + $time_h;
$t_gap = 2 * $rad;

//// image width
$im_w = (2 * $SIZE - 1) * $w_c + $l_gap + $r_gap;
$im_h = intval(($max_img - $min_img) * $h_c) + $b_gap + $t_gap;

//// max image temperature
$p_zec = $max_img % 100; $p_int = ($max_img - $p_zec) / 100;
$max_img_str = sprintf("%2d.%02d", $p_int, $p_zec);
$max_img_x = $l_gap - $rad;
$max_img_y = $t_gap;
//// min image temperature
$p_zec = $min_img % 100; $p_int = ($min_img - $p_zec) / 100;
$min_img_str = sprintf("%2d.%02d", $p_int, $p_zec);
$min_img_x = $l_gap - $rad;
$min_img_y = $im_h - $b_gap;

$imgmap = "  <map name='historymap'>\n";

for($i = $SIZE - 1; $i >= 0; --$i)
{
  $c_x = $l_gap + 2 * $w_c * ($SIZE - $i - 1);
  $hm = $vn["hm"][$i];
  
  
  $left_top_x = $c_x - ($rad + $w_c) / 2;
  $left_top_y = $max_img_y;
  $right_bott_x = $c_x + ($rad + $w_c) / 2;
  $right_bott_y = $min_img_y;
  $imgmap .= "   <area shape='rect' coords='".$left_top_x.", ".$left_top_y.", ".$right_bott_x.", ".$right_bott_y."' alt='Sun' title='".$hm;
  // Dormitor Luca / Bucatarie
  $c1 = $vn["c1"][$i];
  $c1_r = $vn["c1_r"][$i];
if($c1 !== 0)
{
  $p_zec = $c1 % 100; $p_int = ($c1 - $p_zec) / 100;
  $c1_temp_str = "" . (($p_int < 10) ? " " : "") . $p_int . "." . (($p_zec < 10) ? "0" : "") . $p_zec;
  $imgmap .= "&#13;".$c1_temp_str." - ".$c1_str;
  if($c1_r !== 0)
    $imgmap .= " (merge)";
}
  // Dormitor matrimonial / Living
  $c2 = $vn["c2"][$i]; $c2_r = $vn["c2_r"][$i];
if($c2 !== 0)
{
  $p_zec = $c2 % 100; $p_int = ($c2 - $p_zec) / 100;
  $c2_temp_str = "" . (($p_int < 10) ? " " : "") . $p_int . "." . (($p_zec < 10) ? "0" : "") . $p_zec;
  $imgmap .= "&#13;".$c2_temp_str." - ".$c2_str;
  if($c2_r !== 0)
    $imgmap .= " (merge)";
}
  // Dormitor oaspeti / Birou
  $c3 = $vn["c3"][$i]; $c3_r = $vn["c3_r"][$i];
if($c3 !== 0)
{
  $p_zec = $c3 % 100; $p_int = ($c3 - $p_zec) / 100;
  $c3_temp_str = "" . (($p_int < 10) ? " " : "") . $p_int . "." . (($p_zec < 10) ? "0" : "") . $p_zec;
  $imgmap .= "&#13;".$c3_temp_str." - ".$c3_str;
  if($c3_r !== 0)
    $imgmap .= " (merge)";
}
  // Baie 
  $c4 = $vn["c4"][$i]; $c4_r = $vn["c4_r"][$i];
if($c4 !== 0)
{
  $p_zec = $c4 % 100; $p_int = ($c4 - $p_zec) / 100;
  $c4_temp_str = "" . (($p_int < 10) ? " " : "") . $p_int . "." . (($p_zec < 10) ? "0" : "") . $p_zec;
  $imgmap .= "&#13;".$c4_temp_str." - ".$c4_str;
  if($c4_r !== 0)
    $imgmap .= " (merge)";
}
$imgmap .= "' href=''>\n";
}
$imgmap .= "  </map>\n";


if($image_exist === FALSE)
{
//// the image object
$image = imagecreatetruecolor($im_w, $im_h);
//// make white background
$white_color = imagecolorallocate($image, 255, 255, 255);
imagefill($image, 0, 0, $white_color);

$black_color = imagecolorallocate($image, 0, 0, 0);
$blue_color = imagecolorallocate($image, 0, 0, 255);
$red_color = imagecolorallocate($image, 255, 0, 0);
$yellow_color = imagecolorallocate($image, 255, 255, 0);
$green_color = imagecolorallocate($image, 0, 255, 0);
$orange_color = imagecolorallocate($image, 255, 128, 0);

$c1_color = $green_color;
$c2_color = $orange_color;
$c3_color = $blue_color;
$c4_color = $red_color;

//// max temperature
$p_zec = $max_all % 100; $p_int = ($max_all - $p_zec) / 100;
$max_str = sprintf("%2d.%02d", $p_int, $p_zec);
$max_x = $l_gap - $rad;
$max_y = $im_h - intval((($max_all - $min_img) * $h_c) + $b_gap);
imagettftext($image, $fsz, 0, $im_w - $r_gap + $rad, ($max_y + ($fsz / 2)), $black_color, $fontfile, $max_str);
imageline($image, $max_x, $max_y, ($im_w - $r_gap), $max_y, $black_color);
//// min temperature
$p_zec = $min_all % 100; $p_int = ($min_all - $p_zec) / 100;
$min_str = sprintf("%2d.%02d", $p_int, $p_zec);
$min_x = $l_gap - $rad;
$min_y = $im_h - intval((($min_all - $min_img) * $h_c) + $b_gap);
imagettftext($image, $fsz, 0, $im_w - $r_gap + $rad, ($min_y + ($fsz / 2)), $black_color, $fontfile, $min_str);
imageline($image, $min_x, $min_y, ($im_w - $r_gap), $min_y, $black_color);

//// max image temperature
imagettftext($image, $fsz, 0, $rad, ($max_img_y + ($fsz / 2)), $black_color, $fontfile, $max_img_str);
imageline($image, $max_img_x, $max_img_y, ($im_w - $r_gap), $max_img_y, $black_color);
//// min image temperature
imagettftext($image, $fsz, 0, $rad, ($min_img_y + ($fsz / 2)), $black_color, $fontfile, $min_img_str);
imageline($image, $min_img_x, $min_img_y, ($im_w - $r_gap), $min_img_y, $black_color);

for($i = $SIZE - 1; $i >= 0; --$i)
{
  $c_x = $l_gap + 2 * $w_c * ($SIZE - $i - 1);
  $hm = $vn["hm"][$i];
  
  
  // Dormitor Luca / Bucatarie
  $c1 = $vn["c1"][$i];
  $c1_r = $vn["c1_r"][$i];
if($c1 !== 0)
{
  $c1_y = $im_h - intval((($c1 - $min_img) * $h_c) + $b_gap);
  if($c1_r !== 0)
    imagefilledellipse($image, $c_x, $c1_y, $rad, $rad, $c1_color);
  else
    imageellipse($image, $c_x, $c1_y, $rad, $rad, $c1_color);
}
  // Dormitor matrimonial / Living
  $c2 = $vn["c2"][$i]; $c2_r = $vn["c2_r"][$i];
if($c2 !== 0)
{
  $c2_y = $im_h - intval((($c2 - $min_img) * $h_c) + $b_gap);
  if($c2_r !== 0)
    imagefilledellipse($image, $c_x, $c2_y, $rad, $rad, $c2_color);
  else
    imageellipse($image, $c_x, $c2_y, $rad, $rad, $c2_color);
}
  // Dormitor oaspeti / Birou
  $c3 = $vn["c3"][$i]; $c3_r = $vn["c3_r"][$i];
if($c3 !== 0)
{
  $c3_y = $im_h - intval((($c3 - $min_img) * $h_c) + $b_gap);
  if($c3_r !== 0)
    imagefilledellipse($image, $c_x, $c3_y, $rad, $rad, $c3_color);
  else
    imageellipse($image, $c_x, $c3_y, $rad, $rad, $c3_color);
}
  // Baie 
  $c4 = $vn["c4"][$i]; $c4_r = $vn["c4_r"][$i];
if($c4 !== 0)
{
  $c4_y = $im_h - intval((($c4 - $min_img) * $h_c) + $b_gap);
  if($c4_r !== 0)
    imagefilledellipse($image, $c_x, $c4_y, $rad, $rad, $c4_color);
  else
    imageellipse($image, $c_x, $c4_y, $rad, $rad, $c4_color);
}
}
//// time name='historymap'
$hm_y = $im_h - $b_gap + $rad;
$hm_x = 0;
for($i = $SIZE - 1; $i >= 0; --$i)
{
  if($i === $SIZE - 1 || $i === 0 || ($i % $MEAS_PER_HOUR) === 0)
  {
    $hm = $vn["hm"][$i];
    $hm_x_new = $l_gap + 2 * $w_c * ($SIZE - $i - 1);
    if($hm_x > 0 && $hm_x_new - $hm_x < $time_w + $w_c)
      continue;
    $hm_x = $hm_x_new;
    imagettftext($image, $fsz, 0, ($hm_x - $time_w / 2), $im_h - $rad, $black_color, $fontfile, $hm);
    imageline($image, $hm_x, $hm_y, $hm_x, $t_gap - $rad, $black_color);
  }
}

//// temperatures in between
$bw_y = $min_img_y - $max_img_y;
$nb_temp = intval($bw_y / ($temp_h + $rad));
$temp_scale = intval(($max_img - $min_img) / $nb_temp);
$temp_scale = max($temp_scale, ($temp_h + $rad));
$temp_scale = 2 * ($temp_scale - ($temp_scale % 10));
$inter = $min_img;
$inter_x = $l_gap - $rad;
while(($inter += $temp_scale) < $max_img)
{
  $inter_y = $im_h - intval((($inter - $min_img) * $h_c) + $b_gap);
  if(abs($inter_y - $max_img_y) <= $temp_h)
  {
    // skip, too close to max_img
    continue;
  }
  $p_zec = $inter % 100; $p_int = ($inter - $p_zec) / 100;
  $inter_str = sprintf("%2d.%02d", $p_int, $p_zec);

  imagettftext($image, $fsz, 0, $rad, ($inter_y + ($fsz / 2)), $black_color, $fontfile, $inter_str);
  imageline($image, $inter_x, $inter_y, ($im_w - $r_gap), $inter_y, $black_color);
}

//// save as jpeg
imagejpeg($image, $image_file_name);
// Free up memory
imagedestroy($image);
//exit;
}

// logs
$log = array();
$log_count = 0;
$is_log_file = is_file($log_file_name);
if($is_log_file)
{
  $log_file = fopen($log_file_name, "r");
  while (($line = fgets($log_file)) !== FALSE)
  {
    if (strpos($line, $t_loc) !== FALSE)
    {
      $log[$log_count] = $line;
      ++$log_count;
    }
  }
}

echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo " <head>\n";
echo "  <title>CLLS - Istoric</title>\n";
echo "  <style>\n";
echo "   html { height:100%; min-height:100%; width:100%; min-width:100%; }\n";
echo "   body { font-size:large; }\n";
echo "   input { font-size:large; }\n";
echo "   table { border-collapse:collapse; border-style:solid; }\n";
echo "   th { padding:5px; border-style:solid; border-width:thin; width:10em; }\n";
echo "   td { padding:5px; border-style:solid; border-width:thin; width:10em; }\n";
echo "  </style>\n";
echo " </head>\n";
echo " <body>\n";

echo $imgmap;

echo "  <table style='border-style:hidden;'>\n";
echo "   <tr><th align='center' style='border-style:hidden;'>".$date_time."</th></tr>\n";
echo "   <tr>\n";
echo "    <td align='center' style='border-style:hidden;'>\n";
echo "     <img src='".$image_file_name."' usemap='#historymap'>\n";
echo "    </td>\n";
echo "   </tr>\n";
echo "  </table>\n";
echo "  <table>\n";
echo "   <tr>\n";
echo "    <td align='center' colspan='2'>\n";
echo "     <form action='".$t_loc."_day_history.php' method='post'>\n";
echo "      <input type='hidden' name='t_date' value='".$y_day."'>\n";
echo "      <input type='submit' value='Istoric ziua precedenta'".(($has_y_day === FALSE) ? " disabled": "").">\n";
echo "     </form>\n";
echo "    </td>\n";
echo "    <td align='center'>\n";
echo "     <form action='info.php' method='get'><input type='submit' value='Inapoi'></form>\n";
echo "    </td>\n";
echo "    <td align='center' colspan='2'>\n";
echo "     <form action='".$t_loc."_day_history.php' method='post'>\n";
echo "      <input type='hidden' name='t_date' value='".$t_mrow."'>\n";
echo "      <input type='submit' value='Istoric ziua urmatoare'".(($has_t_mrow === FALSE) ? " disabled": "").">\n";
echo "     </form>\n";
echo "    </td>\n";
echo "   </tr>\n";
//echo "  </table>\n";
//echo "  <table>\n";
echo "   <tr>\n";
echo "    <td></td>\n";
echo "    <th align='center'><font color='" . $c1_color_str . "'>".$c1_str."</font></th>\n";
echo "    <th align='center'><font color='" . $c2_color_str . "'>".$c2_str."</font></th>\n";
echo "    <th align='center'><font color='" . $c3_color_str . "'>".$c3_str."</font></th>\n";
echo "    <th align='center'><font color='" . $c4_color_str . "'>".$c4_str."</font></th>\n";
echo "   </tr>\n";
for($i = 0; $i < $SIZE; ++$i)
{
  $c1_r = $vn["c1_r"][$i] > 0;
  $c2_r = $vn["c2_r"][$i] > 0;
  $c3_r = $vn["c3_r"][$i] > 0;
  $c4_r = $vn["c4_r"][$i] > 0;
  $c_r = $c1_r || $c2_r || $c3_r || $c4_r;

  $c1 = $vn["c1"][$i];
  $c2 = $vn["c2"][$i];
  $c3 = $vn["c3"][$i];
  $c4 = $vn["c4"][$i];

  echo "   <tr>\n";
  echo "    <td align='center'>".($c_r ? "<b>" : "").$vn["hm"][$i].($c_r ? "</b>" : "")."</td>\n";

  // Dormitor Luca / Bucatarie
  $p_zec = $c1 % 100; $p_int = ($c1 - $p_zec) / 100;
  echo "    <td align='center'>".($c1_r ? "<b>" : "")."<font color='" . $c1_color_str . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font>".($c1_r ? "</b>" : "")."</td>\n";

  $p_zec = $c2 % 100; $p_int = ($c2 - $p_zec) / 100;
  echo "    <td align='center'>".($c2_r ? "<b>" : "")."<font color='" . $c2_color_str . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font>".($c2_r ? "</b>" : "")."</td>\n";

  $p_zec = $c3 % 100; $p_int = ($c3 - $p_zec) / 100;
  echo "    <td align='center'>".($c3_r ? "<b>" : "")."<font color='" . $c3_color_str . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font>".($c2_r ? "</b>" : "")."</td>\n";

  $p_zec = $c4 % 100; $p_int = ($c4 - $p_zec) / 100;
  echo "    <td align='center'>".($c4_r ? "<b>" : "")."<font color='" . $c4_color_str . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font>".($c4_r ? "</b>" : "")."</td>\n";

  echo "   </tr>\n";
}
echo "  </table>\n";

// show logs
if($log_count > 0)
{
echo "  <p>Log:</p>\n";
echo "  <p>\n";
for($i = ($log_count - 1); $i >= 0; --$i)
{
echo "   ".$log[$i]."<br>\n";
}
echo "  </p>\n";
}
echo " </body>\n";
echo "</html>\n";
echo "\n";
?>
