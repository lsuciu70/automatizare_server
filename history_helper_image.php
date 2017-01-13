<?php
include_once 'file_helper.php';
include_once 'time_helper.php';
include_once 'temperature_helper.php';

$MEAS_PER_HOUR = 6;

if (! isset ( $debug ))
  $debug = TRUE;
if (! isset ( $day ) || $day === NULL)
  $day = strftime ( "%Y.%m.%d" );

// if ($debug)
//   $day = "2016.12.26";

$curr_h = strftime("%H");
$first_half = is_today($day) && intval($curr_h) < 12;
$first_quarter = is_today($day) && intval($curr_h) < 6;

$img_file_exists = FALSE;
if(is_today($day))
  $image_file_name = "data/history.jpg";
else
{
  $image_file_name = "data/history_".strip_prepend_zero ( $day ).".jpg";
  $img_file_exists = is_file($image_file_name);
}

if (! isset ( $day_history_data ))
{
  $day_history_data = array ();
  day_history ( strip_prepend_zero ( $day ), $day_history_data );
  if (is_today ( $day ))
  {
    save_day_history ( "data/sus_avg.txt", $day_history_data, 0 );
    save_day_history ( "data/jos_avg.txt", $day_history_data, 8 );
    save_day_history ( "data/sus_temp.txt", $day_history_data, 0 );
    save_day_history ( "data/jos_temp.txt", $day_history_data, 8 );
  }
}
if ($debug)
  var_dump ( $day_history_data );

$SIZE = count($day_history_data);
if ($debug)
  echo "size=".$SIZE.PHP_EOL;

if (! isset ( $room ))
  $room = array (
      "Dormitor Luca",
      "Dormitor matrimonial",
      "Dormitor oaspeti",
      "Baie etaj",
      "Bucatarie",
      "Living",
      "Birou",
      "Baie parter",
      "Etaj",
      "Parter",
      "Total"
  );
$room_count = count ( $room );
$room_short = array (
    "DL",
    "Dm",
    "Do",
    "Be",
    "Bu",
    "Li",
    "Bi",
    "Bp",
    "Etaj",
    "Parter",
    "Total"
);

$max_all = 0;
$min_all = 10000;

foreach ( $day_history_data as $time => $values )
{
  for($i = 0; $i < $room_count; ++ $i)
  {
    $j = $i * 2;
    if (! isset ( $values [$j] ))
      continue;
      // temperatures
    if ($max_all < $values [$j])
      $max_all = $values [$j];
    if ($values [$j] > 0 && $min_all > $values [$j])
      $min_all = $values [$j];
  }
}
if ($debug === TRUE)
  echo "min temp: " . centi_to_string ( $min_all ) . " (" . $min_all . "), " . "max temp: " . centi_to_string ( $max_all ) . " (" . $max_all . "), " . PHP_EOL;
if ($debug === TRUE)
{
  var_dump($values);
  arsort($values);
  var_dump($values);
}

$max_img = round_to_upper_50 ( $max_all );
$min_img = round_to_lower_50 ( $min_all );
if ($debug === TRUE)
  echo "min img: " . centi_to_string ( $min_img ) . " (" . $min_img . "), " . "max img: " . centi_to_string ( $max_img ) . " (" . $max_img . "), " . PHP_EOL;

if (is_file ( "/usr/share/fonts/truetype/freefont/FreeSerif.ttf" ))
  $fontfile = "/usr/share/fonts/truetype/freefont/FreeSerif.ttf";
else if (is_file ( "/opt/share/fonts/bitstream-vera/VeraSe.ttf" ))
  $fontfile = "/opt/share/fonts/bitstream-vera/VeraSe.ttf";

// dot radius
$rad = 8;
// width coeficient (pixels between 10 minutes)
$w_c = 4;
if($first_half)
  $w_c = 6;
if($first_quarter)
  $w_c = 8;
// height coeficient (pixels per grad) => make graphic aprox 400 pixels high
$h_c = intval(4000 / ($max_img - $min_img)) / 10;
if ($debug === TRUE)
  echo "h_c: $h_c, w_c: $w_c" . PHP_EOL;
// font size
$fsz = 12;

// temperature text width & height
$text_box = imagettfbbox($fsz, 0, $fontfile, "Parter");
$text_w = $text_box[2] - $text_box[0];
$text_h = $text_box[1] - $text_box[7];

// lelft and right gaps
$l_gap = 2 * $rad + $text_w;
$r_gap = $l_gap + $w_c;
// bottom and top gaps
$b_gap = 4 * $rad + 2 * $text_h;
$t_gap = 2 * $rad;

// image width
$im_w = (2 * $SIZE - 1) * $w_c + $l_gap + $r_gap;
$im_h = intval(($max_img - $min_img) * $h_c) + $b_gap + $t_gap;

$imgmap = "  <map name='historymap'>\n";

// temperature map (tooltip)
$img_x = $l_gap + $w_c;

foreach ( $day_history_data as $time => $values )
{
  arsort($values);
  
  $left_top_x = $img_x - ($rad + $w_c) / 2;
  $left_top_y = $t_gap;
  $right_bott_x = $img_x + ($rad + $w_c) / 2;
  $right_bott_y = $im_h - $b_gap;
  $imgmap .= "   <area shape='rect' coords='".$left_top_x.", ".$left_top_y.", ".$right_bott_x.", ".$right_bott_y."' alt='temperatures' title='".$time;
  foreach ($values as $idx => $temp)
  {
    if($temp < 100)
      continue;
    $i = $idx / 2;
    $is_running = $day_history_data[$time][$idx + 1];

    $imgmap .= "&#13;".centi_to_string($temp)." - ".$room[$i];
    if($is_running !== 0)
      $imgmap .= " (merge)";
  }
  $imgmap .= "' href=''>\n";
  // next position
  $img_x += 2 * $w_c;
}
$imgmap .= "  </map>\n";
if ($debug === TRUE)
  echo $imgmap.PHP_EOL;

if($img_file_exists === FALSE || $debug === TRUE)
{
  
// the image object
// make space for summary
$summ_h =  $rad + $text_h;
$summ_h_all = $summ_h * $room_count + $rad;
$image = imagecreatetruecolor($im_w, $im_h + $summ_h_all);

// colors
$white_color = imagecolorallocate($image, 255, 255, 255);
$black_color = imagecolorallocate($image, 0, 0, 0);

$green_color = imagecolorallocate($image, 0, 255, 0);
$orange_color = imagecolorallocate($image, 255, 128, 0);
$blue_color = imagecolorallocate($image, 0, 0, 255);
$red_color = imagecolorallocate($image, 255, 0, 0);

$dark_green_color = imagecolorallocate($image, 0, 170, 0);
$dark_orange_color = imagecolorallocate($image, 216, 128, 0);
$dark_blue_color = imagecolorallocate($image, 0, 0, 170);
$dark_red_color = imagecolorallocate($image, 170, 0, 0);

$teal_color = imagecolorallocate($image, 0, 128, 128);
$dark_teal_color = imagecolorallocate($image, 0, 96, 96);

// $yellow_color = imagecolorallocate($image, 255, 255, 0);
// $fuchsia_color = imagecolorallocate($image, 255, 0, 255);
// $cyan_color = imagecolorallocate($image, 0, 255, 255);

// $purple_color = imagecolorallocate($image, 128, 0, 128);
// $olive_color = imagecolorallocate($image, 128,128,0);

// $red_color_str = "red";         // 255, 0, 0
// $green_color_str = "green";     // 0, 255, 0
// $blue_color_str = "blue";       // 0, 0, 255

// $yellow_color_str = "yellow";   // 255, 255, 0
// $fuchsia_color_str = "fuchsia"; // 255, 0, 255
// $cyan_color_str = "cyan";       // 0, 255, 255

// $orange_color_str = "orange";   // 255, 128, 0
// $purple_color_str = "purple";   // 128, 0, 128
// $teal_color_str = "teal";       // 0, 128, 128
// $olive_color_str = "olive";     // 128, 128, 0

// $colors_str = array(
//     $green_color_str, $orange_color_str, $blue_color_str, $red_color_str,
//     $teal_color_str, $olive_color_str, $cyan_color_str, $fuchsia_color_str
// );

$colors_img = array(
    $green_color, $orange_color, $blue_color, $red_color, 
    $dark_green_color, $dark_orange_color, $dark_blue_color, $dark_red_color,
//     $teal_color, $olive_color, $cyan_color, $fuchsia_color,
    $teal_color, $dark_teal_color, $black_color
);

// make white background
imagefill($image, 0, 0, $white_color);

// temperature grid: min, max and every .5 temperatures between
$temp_grid_cnt = floatval($max_img - $min_img) / 50.0;
$temp_grid_delta_y = ($im_h - $b_gap - $t_gap) / $temp_grid_cnt;
$temp_grid_x1 = $l_gap - $w_c;
$temp_grid_x2 = $im_w - $r_gap + 2 * $w_c;
if ($debug === TRUE)
  echo "temp_grid_cnt=".$temp_grid_cnt.", temp_grid_y=".$temp_grid_delta_y.PHP_EOL;

for ($t = 0; $t <= $temp_grid_cnt; ++$t)
{
  $temp_grid_temp = $max_img - ($t * 50); 
  $temp_grid_str = centi_to_string ( $temp_grid_temp );
  $temp_grid_y = $t_gap + ($t * $temp_grid_delta_y);
  imagettftext($image, $fsz, 0, $rad, ($temp_grid_y + ($fsz / 2)), $black_color, $fontfile, $temp_grid_str);
  imagettftext($image, $fsz, 0, $im_w - $text_w - $rad, ($temp_grid_y + ($fsz / 2)), $black_color, $fontfile, $temp_grid_str);
  imageline($image, $temp_grid_x1, $temp_grid_y, $temp_grid_x2, $temp_grid_y, $black_color);
}

// time grid
// start time
reset($day_history_data);
$time = key($day_history_data);
sscanf($time, "%d:%d-%s", $first_h, $first_m, $blah);
$first_time = sprintf("%2d:%02d", $first_h, $first_m);

end($day_history_data);
$time = key($day_history_data);
sscanf($time, "%d:%d-%s", $last_h, $last_m, $last_time);
if($last_time === NULL)
{
  $last_time = sprintf("%2d:%02d", $last_h, $last_m);
}
if ($debug === TRUE)
  echo "first_time=".$first_time.", last_time=".$last_time.PHP_EOL;

$hm_x = $l_gap;
$hm_y1 = $im_h - $b_gap + $rad;
$hm_y2 = $t_gap - $rad;
$hm_s_y1 = $im_h;
$hm_s_y2 = $im_h + $summ_h_all + $rad;
$delta_x = (2 * $MEAS_PER_HOUR - 0) * $w_c;

$t_h = $first_h;
$t_m = $first_m;
while ($t_h <= $last_h && $t_m <= $last_m)
{
  $time = sprintf("%2d:%02d", $t_h, $t_m);
  if ($debug === TRUE)
    echo $time.", ";

  imagettftext($image, $fsz, 0, ($hm_x - $text_w / 2) + $rad / 2, $im_h - $text_h - 2 * $rad, $black_color, $fontfile, $time);
  imageline($image, $hm_x, $hm_y1, $hm_x, $hm_y2, $black_color);
  imageline($image, $hm_x, $hm_s_y1, $hm_x, $hm_s_y2, $black_color);
  $hm_x += $delta_x;

  // next value (time)
  $t_m += 60;
  if($t_m >= 60)
  {
    $t_m = $t_m % 60;
    ++ $t_h;
  }
}
if ($debug === TRUE)
  echo PHP_EOL;

// last time line
$hm_x_prev = $hm_x - $delta_x;
$hm_x = $im_w - $r_gap + $w_c;
$width_left = $hm_x - $hm_x_prev;
if ($debug === TRUE)
  echo $width_left.">=".($text_w + $w_c).PHP_EOL;
$time_one_line = $width_left >= $text_w + $w_c;
if($time_one_line)
{
  imagettftext($image, $fsz, 0, ($hm_x - $text_w / 2) + $rad / 2, $im_h - $text_h - 2 * $rad, $black_color, $fontfile, $last_time);
}
else
{
  imagettftext($image, $fsz, 0, ($hm_x - $text_w / 2) + $rad / 2, $im_h - $rad, $black_color, $fontfile, $last_time);
}
imageline($image, $hm_x, $hm_y1, $hm_x, $hm_y2, $black_color);
imageline($image, $hm_x, $hm_s_y1, $hm_x, $hm_s_y2, $black_color);

if ($debug === TRUE)
  echo $last_time.PHP_EOL;

echo "";
// rooms temperature
$img_x = $l_gap + $w_c;

foreach ( $day_history_data as $time => $values )
{
  for($i = 0; $i < $room_count; ++ $i)
  {
    $j = $i * 2;
    if (! isset ( $values [$j] ))
      continue;
    // temperature
    $img_y = $im_h - intval((($values [$j] - $min_img) * $h_c) + $b_gap);
    ++ $j;
    if (! isset ( $values [$j] ))
      continue;
    $is_running = $values [$j];
    if($is_running !== 0)
      imagefilledellipse($image, $img_x, $img_y, $rad, $rad, $colors_img[$i]);
    else
      imageellipse($image, $img_x, $img_y, $rad, $rad, $colors_img[$i]);
  }
  // next position
  $img_x += 2 * $w_c;
}

include_once 'history_helper_summary.php';

$img_x = $l_gap + $w_c - $rad / 2;
$img_y = $im_h;
for($i = 0; $i < $room_count; ++ $i)
{
  $img_y += $summ_h;
  imagettftext($image, $fsz, 0, $rad, $img_y, $colors_img[$i], $fontfile, $room_short[$i]);

  $room_string = $room_str[$i];
  $idx = 0;
  $last_idx = 0;
  $intervals = array();
  while(($idx = strpos ($room_string, ", ", $last_idx)) !== FALSE)
  {
    $temp = substr($room_string, $last_idx, $idx - $last_idx);
    $intervals[] = trim($temp);
    $last_idx = $idx + strlen(",");
  }
  $now = strftime ( "%k:%M" );
  sscanf($now, "%d:%d", $now_h, $now_m);
  if($last_idx < strlen($room_string))
  {
    $temp = substr($room_string, $last_idx);
    $temp = trim($temp);
    if (strlen($temp) < 6)
    {
      if(is_today($day))
      {
        $temp = $temp."-".$now;
      }
      else
        $temp = $temp."-0:00";
    }
    $intervals[] = $temp;
  }

  $poli_y1 = $img_y - $text_h / 2;
  $poli_y2 = $poli_y1 - $rad / 2;
  $poli_y1 = $poli_y1 + $rad / 2;
  foreach ($intervals as $interval)
  {
    if($debug)
      echo $interval.PHP_EOL;
    sscanf($interval, "%d:%d-%d:%d", $i_f_h, $i_f_m, $i_l_h, $i_l_m);
    $f_meas = $i_f_h * 6;
    if ($i_f_m % 10 === 0)
      $f_meas += $i_f_m / 10;
    else
      $f_meas += ($i_f_m - ($i_f_m % 10)) / 10 + 1;
    if ($i_l_h === 0 && $i_l_m === 0)
      $i_l_h = 24;
    $l_meas = $i_l_h * 6;
    if ($i_l_m % 10 === 0)
      $l_meas += $i_l_m / 10;
    else
      $l_meas += ($i_l_m - ($i_l_m % 10)) / 10 + 1;
    if ($now_h === 0 && $now_m === 0)
      $now_h = 24;
    if ($now_h === $i_l_h && $now_m === $i_l_m)
      $l_meas += 1;
    if($debug)
      echo "$f_meas - $l_meas".PHP_EOL;
    $poli_x1 = $img_x + $f_meas * 2 * $w_c;
    $poli_x2 = $img_x + $l_meas * 2 * $w_c;
    if($i < $up_index || $i === $all_index)
      imagefilledrectangle($image, $poli_x1, $poli_y1, $poli_x2, $poli_y2, $colors_img[$i]);
    else
      imagerectangle($image, $poli_x1, $poli_y1, $poli_x2, $poli_y2, $colors_img[$i]);
  }
}

// save as jpeg
imagejpeg($image, $image_file_name);
// Free up memory
imagedestroy($image);
}
?>