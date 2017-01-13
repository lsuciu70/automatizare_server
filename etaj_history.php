<?php
// make client to refresh every 60 seconds
$url1=$_SERVER['REQUEST_URI'];
header("Refresh: 60; URL=$url1");

$HOURS = 24;
$MEAS_PER_HOUR = 6;
$SIZE = $HOURS * $MEAS_PER_HOUR;

//// current date and time
$format = "%Y.%m.%d %H:%M:%S";
$now_ts = strftime($format);
sscanf($now_ts, "%d.%d.%d %d:%d:%d", $ts_Y, $ts_M, $ts_D, $ts_h, $ts_m, $ts_s);
$y_day = date('Y.m.d', strtotime('-1 day'));
sscanf($y_day, "%d.%d.%d", $ts_yY, $ts_yM, $ts_yD);

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
for($i = 1; $i <= $SIZE; ++$i)
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

// get sus average file
$ok = FALSE;
$file_name = "data/sus_avg.txt";
if(is_file($file_name) && ($avg = file_get_contents($file_name)) !== FALSE)
{
  sscanf($avg, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_c1, $t_c1_r, $t_c2, $t_c2_r, $t_c3, $t_c3_r, $t_c4, $t_c4_r, $sus_dt);
  if(strpos($sus_dt, $hm_start) === 0)
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
$count = $SIZE + 1;
$sus_file_name = "data/sus_" . $ts_Y . "." . $ts_M . "." . $ts_D . ".txt";
if(is_file($sus_file_name))
{
  $file = new SplFileObject($sus_file_name);
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
if($count <= $SIZE)
{
  $sus_file_name = "data/sus_" . $ts_yY . "." . $ts_yM . "." . $ts_yD . ".txt";
  if(is_file($sus_file_name))
  {
    $file = new SplFileObject($sus_file_name);
    if($file->isReadable())
    {
      $file->seek($file->getSize());
      $curr_line = $file->key();
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
}

for($i = 1; $i <= $SIZE; ++$i)
{
  $key = $vn["hm"][$i];
  if(array_key_exists($key, $lines))
  {
    $line = $lines[$key];
    sscanf($line, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_c1, $t_c1_r, $t_c2, $t_c2_r, $t_c3, $t_c3_r, $t_c4, $t_c4_r, $sus_dt);
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

for($i = 0; $i <= $SIZE; ++$i)
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

//// dot radius
$rad = 8;
//// font size
$fsz = 12;
//// height & width temperature coeficient
$h_c = 1;
$w_c = 4;

//// temperature text width & height
$text_box = imagettfbbox($fsz, 0, $fontfile, "00.00");
$text_w = $text_box[2] - $text_box[0];
$$text_h = $text_box[1] - $text_box[7];
// echo "temp_w=".$temp_w.", temp_h=".$temp_h."\n";
//// time text width & height
$time_box = imagettfbbox($fsz, 0, $fontfile, "00:00-00:00");
$time_w = $time_box[2] - $time_box[0];
$time_h = $time_box[1] - $time_box[7];
// echo "time_w=".$time_w.", time_h=".$time_h."\n";
//// Title text width & height
$c1_str = "Dormitor Luca"; $c1_box = imagettfbbox($fsz, 0, $fontfile, $c1_str); $c1_w = $c1_box[2] - $c1_box[0]; $c1_h = $c1_box[1] - $c1_box[7];
$c2_str = "Dormitor matrimonial"; $c2_box = imagettfbbox($fsz, 0, $fontfile, $c2_str); $c2_w = $c2_box[2] - $c2_box[0]; $c2_h = $c2_box[1] - $c2_box[7];
$c3_str = "Dormitor oaspeti"; $c3_box = imagettfbbox($fsz, 0, $fontfile, $c3_str); $c3_w = $c3_box[2] - $c3_box[0]; $c3_h = $c3_box[1] - $c3_box[7];
$c4_str = "Baie sus"; $c4_box = imagettfbbox($fsz, 0, $fontfile, $c4_str); $c4_w = $c4_box[2] - $c4_box[0]; $c4_h = $c4_box[1] - $c4_box[7];
$title_w = $c1_w + $c2_w + $c3_w + $c4_w;
$title_h = $c3_h;

//// lelft and right gaps
$l_gap = 2 * $rad + $text_w;
$r_gap = $l_gap;
//// bottom and top gaps
$b_gap = 3 * $rad + $time_h;
$t_gap = 2 * $rad + $title_h;

//// image width
$im_w = (2 * $SIZE + 1) * $w_c + $l_gap + $r_gap;
$im_h = ($max_img - $min_img) * $h_c + $b_gap + $t_gap;

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

//// title
$title_w += 4 * 10 * $w_c + 7 * $rad;
$title_x = $im_w / 2 - $title_w / 2;
$title_y = $rad + $title_h;
$hth = intval($title_h / 2);
// c1
imagettftext($image, $fsz, 0, $title_x, $title_y, $c1_color, $fontfile, $c1_str);
$title_x += $rad + $c1_w; $title_y -= $hth;
for($i = 0 ; $i < 5 ; ++$i)
{
  imageline($image, $title_x, $title_y - 2 + $i, $title_x + 10 * $w_c, $title_y - 2 + $i, $c1_color);
}
// c2
$title_x += $rad + 10 * $w_c; $title_y += $hth;
imagettftext($image, $fsz, 0, $title_x, $title_y, $c2_color, $fontfile, $c2_str);
$title_x += $rad + $c2_w; $title_y -= $hth;
for($i = 0 ; $i < 5 ; ++$i)
{    
  imageline($image, $title_x, $title_y - 2 + $i, $title_x + 10 * $w_c, $title_y - 2 + $i, $c2_color);
}
// c3
$title_x += $rad + 10 * $w_c; $title_y += $hth;
imagettftext($image, $fsz, 0, $title_x, $title_y, $c3_color, $fontfile, $c3_str);
$title_x += $rad + $c3_w; $title_y -= $hth;
for($i = 0 ; $i < 5 ; ++$i)
{    
  imageline($image, $title_x, $title_y - 2 + $i, $title_x + 10 * $w_c, $title_y - 2 + $i, $c3_color);
}
// c4
$title_x += $rad + 10 * $w_c; $title_y += $hth;
imagettftext($image, $fsz, 0, $title_x, $title_y, $c4_color, $fontfile, $c4_str);
$title_x += $rad + $c4_w; $title_y -= $hth;
for($i = 0 ; $i < 5 ; ++$i)
{    
  imageline($image, $title_x, $title_y - 2 + $i, $title_x + 10 * $w_c, $title_y - 2 + $i, $c4_color);
}

// Etaj
$c1_x_prev = 0; $c1_y_prev = 0; $c1_r_prev = 0;
$c2_x_prev = 0; $c2_y_prev = 0; $c2_r_prev = 0;
$c3_x_prev = 0; $c3_y_prev = 0; $c3_r_prev = 0;
$c4_x_prev = 0; $c4_y_prev = 0; $c4_r_prev = 0;

for($i = $SIZE; $i >= 0; --$i)
{
  // Dormitor Luca
  $c1 = $vn["c1"][$i]; $c1_r = $vn["c1_r"][$i];

  $c1_x = $l_gap + 2 * $w_c * ($SIZE - $i); $c1_y = $im_h - (($c1 - $min_img) * $h_c + $b_gap);
  if($c1_x_prev === 0 && $c1_y_prev === 0) { $c1_x_prev = $c1_x; $c1_y_prev = $c1_y; $c1_r_prev = $c1_r; }

  imageline($image, $c1_x_prev, $c1_y_prev - 2, $c1_x, $c1_y - 2, $c1_color);
  imageline($image, $c1_x_prev, $c1_y_prev + 2, $c1_x, $c1_y + 2, $c1_color);
  if($c1_r_prev !== 0)
  {
    imageline($image, $c1_x_prev, $c1_y_prev - 1, $c1_x, $c1_y - 1, $c1_color);
    imageline($image, $c1_x_prev, $c1_y_prev, $c1_x, $c1_y, $c1_color);
    imageline($image, $c1_x_prev, $c1_y_prev + 1, $c1_x, $c1_y + 1, $c1_color);
  }
  $c1_x_prev = $c1_x; $c1_y_prev = $c1_y; $c1_r_prev = $c1_r;

  // Dormitor matrimonial
  $c2 = $vn["c2"][$i]; $c2_r = $vn["c2_r"][$i];

  $c2_x = $l_gap + 2 * $w_c * ($SIZE - $i); $c2_y = $im_h - (($c2 - $min_img) * $h_c + $b_gap);
  if($c2_x_prev === 0 && $c2_y_prev === 0) { $c2_x_prev = $c2_x; $c2_y_prev = $c2_y; $c2_r_prev = $c2_r; }

  imageline($image, $c2_x_prev, $c2_y_prev - 2, $c2_x, $c2_y - 2, $c2_color);
  imageline($image, $c2_x_prev, $c2_y_prev + 2, $c2_x, $c2_y + 2, $c2_color);
  if($c2_r_prev !== 0)
  {
    imageline($image, $c2_x_prev, $c2_y_prev - 1, $c2_x, $c2_y - 1, $c2_color);
    imageline($image, $c2_x_prev, $c2_y_prev, $c2_x, $c2_y, $c2_color);
    imageline($image, $c2_x_prev, $c2_y_prev + 1, $c2_x, $c2_y + 1, $c2_color);
  }
  $c2_x_prev = $c2_x; $c2_y_prev = $c2_y; $c2_r_prev = $c2_r;

  // Dormitor oaspeti
  $c3 = $vn["c3"][$i]; $c3_r = $vn["c3_r"][$i];

  $c3_x = $l_gap + 2 * $w_c * ($SIZE - $i); $c3_y = $im_h - (($c3 - $min_img) * $h_c + $b_gap);
  if($c3_x_prev === 0 && $c3_y_prev === 0) { $c3_x_prev = $c3_x; $c3_y_prev = $c3_y; $c3_r_prev = $c3_r; }

  imageline($image, $c3_x_prev, $c3_y_prev - 2, $c3_x, $c3_y - 2, $c3_color);
  imageline($image, $c3_x_prev, $c3_y_prev + 2, $c3_x, $c3_y + 2, $c3_color);
  if($c3_r_prev !== 0)
  {
    imageline($image, $c3_x_prev, $c3_y_prev - 1, $c3_x, $c3_y - 1, $c3_color);
    imageline($image, $c3_x_prev, $c3_y_prev, $c3_x, $c3_y, $c3_color);
    imageline($image, $c3_x_prev, $c3_y_prev + 1, $c3_x, $c3_y + 1, $c3_color);
  }
  $c3_x_prev = $c3_x; $c3_y_prev = $c3_y; $c3_r_prev = $c3_r;

  // Baie sus
  $c4 = $vn["c4"][$i]; $c4_r = $vn["c4_r"][$i];

  $c4_x = $l_gap + 2 * $w_c * ($SIZE - $i); $c4_y = $im_h - (($c4 - $min_img) * $h_c + $b_gap);
  if($c4_x_prev === 0 && $c4_y_prev === 0) { $c4_x_prev = $c4_x; $c4_y_prev = $c4_y; $c4_r_prev = $c4_r; }

  imageline($image, $c4_x_prev, $c4_y_prev - 2, $c4_x, $c4_y - 2, $c4_color);
  imageline($image, $c4_x_prev, $c4_y_prev + 2, $c4_x, $c4_y + 2, $c4_color);
  if($c4_r_prev !== 0)
  {
    imageline($image, $c4_x_prev, $c4_y_prev - 1, $c4_x, $c4_y - 1, $c4_color);
    imageline($image, $c4_x_prev, $c4_y_prev, $c4_x, $c4_y, $c4_color);
    imageline($image, $c4_x_prev, $c4_y_prev + 1, $c4_x, $c4_y + 1, $c4_color);
  }
  $c4_x_prev = $c4_x; $c4_y_prev = $c4_y; $c4_r_prev = $c4_r;

}
//// time
$hm_y = $im_h - $b_gap;
$hm_x = 0;
for($i = $SIZE; $i >= 0; --$i)
{
  if($i === $SIZE || $i === 0 || (($i % $MEAS_PER_HOUR) === 0 && ($SIZE - $i) > 1))
  {
    $hm = $vn["hm"][$i];
    $hm_x_new = $l_gap + 2 * $w_c * ($SIZE - $i);
    if($hm_x > 0 && $hm_x_new - $hm_x < $time_w + $w_c)
      continue;
    $hm_x = $hm_x_new;
    imagettftext($image, $fsz, 0, ($hm_x - $time_w / 2), $im_h - $rad, $black_color, $fontfile, $hm);
    imageline($image, $hm_x, $hm_y, $hm_x, $t_gap, $black_color);
  }
}

//// max temperature
$p_zec = $max_all % 100; $p_int = ($max_all - $p_zec) / 100;
$max_str = sprintf("%2d.%02d", $p_int, $p_zec);
$max_x = $l_gap - $rad;
//$max_y = $t_gap;
$max_y = $im_h - (($max_all - $min_img) * $h_c + $b_gap);
imagettftext($image, $fsz, 0, $im_w - $r_gap + $rad, ($max_y + ($fsz / 2)), $black_color, $fontfile, $max_str);
imageline($image, $max_x, $max_y, ($im_w - $r_gap), $max_y, $black_color);
//// min temperature
$p_zec = $min_all % 100; $p_int = ($min_all - $p_zec) / 100;
$min_str = sprintf("%2d.%02d", $p_int, $p_zec);
$min_x = $l_gap - $rad;
//$min_y = $im_h - $b_gap;
$min_y = $im_h - (($min_all - $min_img) * $h_c + $b_gap);
imagettftext($image, $fsz, 0, $im_w - $r_gap + $rad, ($min_y + ($fsz / 2)), $black_color, $fontfile, $min_str);
imageline($image, $min_x, $min_y, ($im_w - $r_gap), $min_y, $black_color);

//// max image temperature
$p_zec = $max_img % 100; $p_int = ($max_img - $p_zec) / 100;
$max_str = sprintf("%2d.%02d", $p_int, $p_zec);
$max_x = $l_gap - $rad;
$max_y = $t_gap;
imagettftext($image, $fsz, 0, $rad, ($max_y + ($fsz / 2)), $black_color, $fontfile, $max_str);
imageline($image, $max_x, $max_y, ($im_w - $r_gap), $max_y, $black_color);
//// min image temperature
$p_zec = $min_img % 100; $p_int = ($min_img - $p_zec) / 100;
$min_str = sprintf("%2d.%02d", $p_int, $p_zec);
$min_x = $l_gap - $rad;
$min_y = $im_h - $b_gap;
imagettftext($image, $fsz, 0, $rad, ($min_y + ($fsz / 2)), $black_color, $fontfile, $min_str);
imageline($image, $min_x, $min_y, ($im_w - $r_gap), $min_y, $black_color);

//// temperatures in between
$bw_y = $min_y - $max_y;
$nb_temp = intval($bw_y / ($$text_h + $rad));
$temp_scale = intval(($max_img - $min_img) / $nb_temp);
$temp_scale = 2 * ($temp_scale - ($temp_scale % 10));
$inter = $min_img;
$inter_x = $l_gap - $rad;
while(($inter += $temp_scale) < $max_img)
{
  $inter_y = $im_h - (($inter - $min_img) * $h_c + $b_gap);
  if(abs($inter_y - $max_y) <= $$text_h)
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
imagejpeg($image, 'data/sus_history.jpg');
// Free up memory
imagedestroy($image);
//exit;

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
echo "  <table>\n";
echo "   <tr><th align='center'>".$now_ts."</th></tr>\n";
echo "   <tr><td></td></tr>\n";
echo "   <tr><td align='center'><img src='data/sus_history.jpg'></td></tr>\n";
echo "   <tr><td></td></tr>\n";
echo "   <tr><td align='center'><form action='info.php' method='get'><input type='submit' value='Inapoi'></form></td></tr>\n";
echo "  </table>\n";
echo " </body>\n";
echo "</html>\n";
echo "\n";
?>

