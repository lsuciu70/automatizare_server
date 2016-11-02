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
    "dl" => array(),
    "dl_r" => array(),
    "dm" => array(),
    "dm_r" => array(),
    "d3" => array(),
    "d3_r" => array(),
    "bs" => array(),
    "bs_r" => array(),
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
  sscanf($avg, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_dl, $t_dl_r, $t_dm, $t_dm_r, $t_d3, $t_d3_r, $t_bs, $t_bs_r, $sus_dt);
  if(strpos($sus_dt, $hm_start) === 0)
  {
    // ok
    $ok = TRUE;
    $vn["dl"][0] = $t_dl;
    $vn["dl_r"][0] = $t_dl_r;
    $vn["dm"][0] = $t_dm;
    $vn["dm_r"][0] = $t_dm_r;
    $vn["d3"][0] = $t_d3;
    $vn["d3_r"][0] = $t_d3_r;
    $vn["bs"][0] = $t_bs;
    $vn["bs_r"][0] = $t_bs_r;
  }
}
if($ok === FALSE)
{
    $vn["dl"][0] = 0;
    $vn["dl_r"][0] = 0;
    $vn["dm"][0] = 0;
    $vn["dm_r"][0] = 0;
    $vn["d3"][0] = 0;
    $vn["d3_r"][0] = 0;
    $vn["bs"][0] = 0;
    $vn["bs_r"][0] = 0;
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
    sscanf($line, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_dl, $t_dl_r, $t_dm, $t_dm_r, $t_d3, $t_d3_r, $t_bs, $t_bs_r, $sus_dt);
    $vn["dl"][$i] = $t_dl;
    $vn["dl_r"][$i] = $t_dl_r;
    $vn["dm"][$i] = $t_dm;
    $vn["dm_r"][$i] = $t_dm_r;
    $vn["d3"][$i] = $t_d3;
    $vn["d3_r"][$i] = $t_d3_r;
    $vn["bs"][$i] = $t_bs;
    $vn["bs_r"][$i] = $t_bs_r;
  }
  else
  {
    $vn["dl"][$i] = 0;
    $vn["dl_r"][$i] = 0;
    $vn["dm"][$i] = 0;
    $vn["dm_r"][$i] = 0;
    $vn["d3"][$i] = 0;
    $vn["d3_r"][$i] = 0;
    $vn["bs"][$i] = 0;
    $vn["bs_r"][$i] = 0;
  }
}

//// images
if(is_file("/usr/share/fonts/truetype/freefont/FreeSerif.ttf"))
  $fontfile = "/usr/share/fonts/truetype/freefont/FreeSerif.ttf";
else if(is_file("/opt/share/fonts/bitstream-vera/VeraSe.ttf"))
  $fontfile = "/opt/share/fonts/bitstream-vera/VeraSe.ttf";

$max_all = 0; $min_all = 0;
$max_dl = 0; $min_dl = 0;
$max_dm = 0; $min_dm = 0;
$max_d3 = 0; $min_d3 = 0;
$max_bs = 0; $min_bs = 0;

for($i = 0; $i <= $SIZE; ++$i)
{
  if(($dl = $vn["dl"][$i]) > 0)
  {
    if($max_dl < $dl) $max_dl = $dl;
    if($min_dl === 0 || $min_dl > $dl) $min_dl = $dl;
    if($max_all < $max_dl) $max_all = $max_dl;
    if($min_all === 0 || $min_all > $min_dl) $min_all = $min_dl;
  }
  if(($dm = $vn["dm"][$i]) > 0)
  {
    if($max_dm < $dm) $max_dm = $dm;
    if($min_dm === 0 || $min_dm > $dm) $min_dm = $dm;
    if($max_all < $max_dm) $max_all = $max_dm;
    if($min_all === 0 || $min_all > $min_dm) $min_all = $min_dm;
  }
  if(($d3 = $vn["d3"][$i]) > 0)
  {
    if($max_d3 < $d3) $max_d3 = $d3;
    if($min_d3 === 0 || $min_d3 > $d3) $min_d3 = $d3;
    if($max_all < $max_d3) $max_all = $max_d3;
    if($min_all === 0 || $min_all > $min_d3) $min_all = $min_d3;
  }
  if(($bs = $vn["bs"][$i]) > 0)
  {
    if($max_bs < $bs) $max_bs = $bs;
    if($min_bs === 0 || $min_bs > $bs) $min_bs = $bs;
    if($max_all < $max_bs) $max_all = $max_bs;
    if($min_all === 0 || $min_all > $min_bs) $min_all = $min_bs;
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
$temp_box = imagettfbbox($fsz, 0, $fontfile, "00.00");
$temp_w = $temp_box[2] - $temp_box[0];
$temp_h = $temp_box[1] - $temp_box[7];
// echo "temp_w=".$temp_w.", temp_h=".$temp_h."\n";
//// time text width & height
$time_box = imagettfbbox($fsz, 0, $fontfile, "00:00-00:00");
$time_w = $time_box[2] - $time_box[0];
$time_h = $time_box[1] - $time_box[7];
// echo "time_w=".$time_w.", time_h=".$time_h."\n";
//// Title text width & height
$dl_str = "Dormitor Luca"; $dl_box = imagettfbbox($fsz, 0, $fontfile, $dl_str); $dl_w = $dl_box[2] - $dl_box[0]; $dl_h = $dl_box[1] - $dl_box[7];
$dm_str = "Dormitor matrimonial"; $dm_box = imagettfbbox($fsz, 0, $fontfile, $dm_str); $dm_w = $dm_box[2] - $dm_box[0]; $dm_h = $dm_box[1] - $dm_box[7];
$d3_str = "Dormitor oaspeti"; $d3_box = imagettfbbox($fsz, 0, $fontfile, $d3_str); $d3_w = $d3_box[2] - $d3_box[0]; $d3_h = $d3_box[1] - $d3_box[7];
$bs_str = "Baie sus"; $bs_box = imagettfbbox($fsz, 0, $fontfile, $bs_str); $bs_w = $bs_box[2] - $bs_box[0]; $bs_h = $bs_box[1] - $bs_box[7];
$title_w = $dl_w + $dm_w + $d3_w + $bs_w;
$title_h = $d3_h;

//// lelft and right gaps
$l_gap = 2 * $rad + $temp_w;
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

$dl_color = $green_color;
$dm_color = $orange_color;
$d3_color = $blue_color;
$bs_color = $red_color;

//// title
$title_w += 4 * 10 * $w_c + 7 * $rad;
$title_x = $im_w / 2 - $title_w / 2;
$title_y = $rad + $title_h;
$hth = intval($title_h / 2);
// dl
imagettftext($image, $fsz, 0, $title_x, $title_y, $dl_color, $fontfile, $dl_str);
$title_x += $rad + $dl_w; $title_y -= $hth;
for($i = 0 ; $i < 5 ; ++$i)
{
  imageline($image, $title_x, $title_y - 2 + $i, $title_x + 10 * $w_c, $title_y - 2 + $i, $dl_color);
}
// dm
$title_x += $rad + 10 * $w_c; $title_y += $hth;
imagettftext($image, $fsz, 0, $title_x, $title_y, $dm_color, $fontfile, $dm_str);
$title_x += $rad + $dm_w; $title_y -= $hth;
for($i = 0 ; $i < 5 ; ++$i)
{    
  imageline($image, $title_x, $title_y - 2 + $i, $title_x + 10 * $w_c, $title_y - 2 + $i, $dm_color);
}
// d3
$title_x += $rad + 10 * $w_c; $title_y += $hth;
imagettftext($image, $fsz, 0, $title_x, $title_y, $d3_color, $fontfile, $d3_str);
$title_x += $rad + $d3_w; $title_y -= $hth;
for($i = 0 ; $i < 5 ; ++$i)
{    
  imageline($image, $title_x, $title_y - 2 + $i, $title_x + 10 * $w_c, $title_y - 2 + $i, $d3_color);
}
// bs
$title_x += $rad + 10 * $w_c; $title_y += $hth;
imagettftext($image, $fsz, 0, $title_x, $title_y, $bs_color, $fontfile, $bs_str);
$title_x += $rad + $bs_w; $title_y -= $hth;
for($i = 0 ; $i < 5 ; ++$i)
{    
  imageline($image, $title_x, $title_y - 2 + $i, $title_x + 10 * $w_c, $title_y - 2 + $i, $bs_color);
}

// Etaj
$dl_x_prev = 0; $dl_y_prev = 0; $dl_r_prev = 0;
$dm_x_prev = 0; $dm_y_prev = 0; $dm_r_prev = 0;
$d3_x_prev = 0; $d3_y_prev = 0; $d3_r_prev = 0;
$bs_x_prev = 0; $bs_y_prev = 0; $bs_r_prev = 0;

for($i = $SIZE; $i >= 0; --$i)
{
  // Dormitor Luca
  $dl = $vn["dl"][$i]; $dl_r = $vn["dl_r"][$i];

  $dl_x = $l_gap + 2 * $w_c * ($SIZE - $i); $dl_y = $im_h - (($dl - $min_img) * $h_c + $b_gap);
  if($dl_x_prev === 0 && $dl_y_prev === 0) { $dl_x_prev = $dl_x; $dl_y_prev = $dl_y; $dl_r_prev = $dl_r; }

  imageline($image, $dl_x_prev, $dl_y_prev - 2, $dl_x, $dl_y - 2, $dl_color);
  imageline($image, $dl_x_prev, $dl_y_prev + 2, $dl_x, $dl_y + 2, $dl_color);
  if($dl_r_prev !== 0)
  {
    imageline($image, $dl_x_prev, $dl_y_prev - 1, $dl_x, $dl_y - 1, $dl_color);
    imageline($image, $dl_x_prev, $dl_y_prev, $dl_x, $dl_y, $dl_color);
    imageline($image, $dl_x_prev, $dl_y_prev + 1, $dl_x, $dl_y + 1, $dl_color);
  }
  $dl_x_prev = $dl_x; $dl_y_prev = $dl_y; $dl_r_prev = $dl_r;

  // Dormitor matrimonial
  $dm = $vn["dm"][$i]; $dm_r = $vn["dm_r"][$i];

  $dm_x = $l_gap + 2 * $w_c * ($SIZE - $i); $dm_y = $im_h - (($dm - $min_img) * $h_c + $b_gap);
  if($dm_x_prev === 0 && $dm_y_prev === 0) { $dm_x_prev = $dm_x; $dm_y_prev = $dm_y; $dm_r_prev = $dm_r; }

  imageline($image, $dm_x_prev, $dm_y_prev - 2, $dm_x, $dm_y - 2, $dm_color);
  imageline($image, $dm_x_prev, $dm_y_prev + 2, $dm_x, $dm_y + 2, $dm_color);
  if($dm_r_prev !== 0)
  {
    imageline($image, $dm_x_prev, $dm_y_prev - 1, $dm_x, $dm_y - 1, $dm_color);
    imageline($image, $dm_x_prev, $dm_y_prev, $dm_x, $dm_y, $dm_color);
    imageline($image, $dm_x_prev, $dm_y_prev + 1, $dm_x, $dm_y + 1, $dm_color);
  }
  $dm_x_prev = $dm_x; $dm_y_prev = $dm_y; $dm_r_prev = $dm_r;

  // Dormitor oaspeti
  $d3 = $vn["d3"][$i]; $d3_r = $vn["d3_r"][$i];

  $d3_x = $l_gap + 2 * $w_c * ($SIZE - $i); $d3_y = $im_h - (($d3 - $min_img) * $h_c + $b_gap);
  if($d3_x_prev === 0 && $d3_y_prev === 0) { $d3_x_prev = $d3_x; $d3_y_prev = $d3_y; $d3_r_prev = $d3_r; }

  imageline($image, $d3_x_prev, $d3_y_prev - 2, $d3_x, $d3_y - 2, $d3_color);
  imageline($image, $d3_x_prev, $d3_y_prev + 2, $d3_x, $d3_y + 2, $d3_color);
  if($d3_r_prev !== 0)
  {
    imageline($image, $d3_x_prev, $d3_y_prev - 1, $d3_x, $d3_y - 1, $d3_color);
    imageline($image, $d3_x_prev, $d3_y_prev, $d3_x, $d3_y, $d3_color);
    imageline($image, $d3_x_prev, $d3_y_prev + 1, $d3_x, $d3_y + 1, $d3_color);
  }
  $d3_x_prev = $d3_x; $d3_y_prev = $d3_y; $d3_r_prev = $d3_r;

  // Baie sus
  $bs = $vn["bs"][$i]; $bs_r = $vn["bs_r"][$i];

  $bs_x = $l_gap + 2 * $w_c * ($SIZE - $i); $bs_y = $im_h - (($bs - $min_img) * $h_c + $b_gap);
  if($bs_x_prev === 0 && $bs_y_prev === 0) { $bs_x_prev = $bs_x; $bs_y_prev = $bs_y; $bs_r_prev = $bs_r; }

  imageline($image, $bs_x_prev, $bs_y_prev - 2, $bs_x, $bs_y - 2, $bs_color);
  imageline($image, $bs_x_prev, $bs_y_prev + 2, $bs_x, $bs_y + 2, $bs_color);
  if($bs_r_prev !== 0)
  {
    imageline($image, $bs_x_prev, $bs_y_prev - 1, $bs_x, $bs_y - 1, $bs_color);
    imageline($image, $bs_x_prev, $bs_y_prev, $bs_x, $bs_y, $bs_color);
    imageline($image, $bs_x_prev, $bs_y_prev + 1, $bs_x, $bs_y + 1, $bs_color);
  }
  $bs_x_prev = $bs_x; $bs_y_prev = $bs_y; $bs_r_prev = $bs_r;

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
$nb_temp = intval($bw_y / ($temp_h + $rad));
$temp_scale = intval(($max_img - $min_img) / $nb_temp);
$temp_scale = 2 * ($temp_scale - ($temp_scale % 10));
$inter = $min_img;
$inter_x = $l_gap - $rad;
while(($inter += $temp_scale) < $max_img)
{
  $inter_y = $im_h - (($inter - $min_img) * $h_c + $b_gap);
  if(abs($inter_y - $max_y) <= $temp_h)
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

