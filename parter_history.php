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
    "bu" => array(),
    "bu_r" => array(),
    "li" => array(),
    "li_r" => array(),
    "bi" => array(),
    "bi_r" => array(),
    "bj" => array(),
    "bj_r" => array(),
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

// get jos average file
$ok = FALSE;
$file_name = "data/jos_avg.txt";
if(is_file($file_name) && ($avg = file_get_contents($file_name)) !== FALSE)
{
  sscanf($avg, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_bu, $t_bu_r, $t_li, $t_li_r, $t_bi, $t_bi_r, $t_bj, $t_bj_r, $jos_dt);
  if(strpos($jos_dt, $hm_start) === 0)
  {
    // ok
    $ok = TRUE;
    $vn["bu"][0] = $t_bu;
    $vn["bu_r"][0] = $t_bu_r;
    $vn["li"][0] = $t_li;
    $vn["li_r"][0] = $t_li_r;
    $vn["bi"][0] = $t_bi;
    $vn["bi_r"][0] = $t_bi_r;
    $vn["bj"][0] = $t_bj;
    $vn["bj_r"][0] = $t_bj_r;
  }
}
if($ok === FALSE)
{
  $vn["bu"][0] = 0;
  $vn["bu_r"][0] = 0;
  $vn["li"][0] = 0;
  $vn["li_r"][0] = 0;
  $vn["bi"][0] = 0;
  $vn["bi_r"][0] = 0;
  $vn["bj"][0] = 0;
  $vn["bj_r"][0] = 0;
}

$lines = array();
$jos_file_name = "data/jos_" . $ts_Y . "." . $ts_M . "." . $ts_D . ".txt";
$count = $SIZE + 1;
if(is_file($jos_file_name))
{
  $file = new SplFileObject($jos_file_name);
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
  $jos_file_name = "data/jos_" . $ts_yY . "." . $ts_yM . "." . $ts_yD . ".txt";
  if(is_file($jos_file_name))
  {
    $file = new SplFileObject($jos_file_name);
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
    sscanf($line, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_bu, $t_bu_r, $t_li, $t_li_r, $t_bi, $t_bi_r, $t_bj, $t_bj_r, $jos_dt);
    $vn["bu"][$i] = $t_bu;
    $vn["bu_r"][$i] = $t_bu_r;
    $vn["li"][$i] = $t_li;
    $vn["li_r"][$i] = $t_li_r;
    $vn["bi"][$i] = $t_bi;
    $vn["bi_r"][$i] = $t_bi_r;
    $vn["bj"][$i] = $t_bj;
    $vn["bj_r"][$i] = $t_bj_r;
  }
  else
  {
    $vn["bu"][$i] = 0;
    $vn["bu_r"][$i] = 0;
    $vn["li"][$i] = 0;
    $vn["li_r"][$i] = 0;
    $vn["bi"][$i] = 0;
    $vn["bi_r"][$i] = 0;
    $vn["bj"][$i] = 0;
    $vn["bj_r"][$i] = 0;
  }
}

//// images
if(is_file("/usr/share/fonts/truetype/freefont/FreeSerif.ttf"))
  $fontfile = "/usr/share/fonts/truetype/freefont/FreeSerif.ttf";
else if(is_file("/opt/share/fonts/bitstream-vera/VeraSe.ttf"))
  $fontfile = "/opt/share/fonts/bitstream-vera/VeraSe.ttf";

$max_all = 0; $min_all = 0;
$max_bu = 0; $min_bu = 0;
$max_li = 0; $min_li = 0;
$max_bi = 0; $min_bi = 0;
$max_bj = 0; $min_bj = 0;

for($i = 0; $i <= $SIZE; ++$i)
{
  if(($bu = $vn["bu"][$i]) > 0)
  {
    if($max_bu < $bu) $max_bu = $bu;
    if($min_bu === 0 || $min_bu > $bu) $min_bu = $bu;
    if($max_all < $max_bu) $max_all = $max_bu;
    if($min_all === 0 || $min_all > $min_bu) $min_all = $min_bu;
  }
  if(($li = $vn["li"][$i]) > 0)
  {
    if($max_li < $li) $max_li = $li;
    if($min_li === 0 || $min_li > $li) $min_li = $li;
    if($max_all < $max_li) $max_all = $max_li;
    if($min_all === 0 || $min_all > $min_li) $min_all = $min_li;
  }
  if(($bi = $vn["bi"][$i]) > 0)
  {
    if($max_bi < $bi) $max_bi = $bi;
    if($min_bi === 0 || $min_bi > $bi) $min_bi = $bi;
    if($max_all < $max_bi) $max_all = $max_bi;
    if($min_all === 0 || $min_all > $min_bi) $min_all = $min_bi;
  }
  if(($bj = $vn["bj"][$i]) > 0)
  {
    if($max_bj < $bj) $max_bj = $bj;
    if($min_bj === 0 || $min_bj > $bj) $min_bj = $bj;
    if($max_all < $max_bj) $max_all = $max_bj;
    if($min_all === 0 || $min_all > $min_bj) $min_all = $min_bj;
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
$bu_str = "Bucatarie"; $bu_box = imagettfbbox($fsz, 0, $fontfile, $bu_str); $bu_w = $bu_box[2] - $bu_box[0]; $bu_h = $bu_box[1] - $bu_box[7];
$li_str = "Living"; $li_box = imagettfbbox($fsz, 0, $fontfile, $li_str); $li_w = $li_box[2] - $li_box[0]; $li_h = $li_box[1] - $li_box[7];
$bi_str = "Birou"; $bi_box = imagettfbbox($fsz, 0, $fontfile, $bi_str); $bi_w = $bi_box[2] - $bi_box[0]; $bi_h = $bi_box[1] - $bi_box[7];
$bj_str = "Baie jos"; $bj_box = imagettfbbox($fsz, 0, $fontfile, $bj_str); $bj_w = $bj_box[2] - $bj_box[0]; $bj_h = $bj_box[1] - $bj_box[7];
$title_w = $bu_w + $li_w + $bi_w + $bj_w;
$title_h = $bi_h;

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

$bu_color = $green_color;
$li_color = $orange_color;
$bi_color = $blue_color;
$bj_color = $red_color;

//// title
$title_w += 4 * 10 * $w_c + 7 * $rad;
$title_x = $im_w / 2 - $title_w / 2;
$title_y = $rad + $title_h;
$hth = intval($title_h / 2);
// bu
imagettftext($image, $fsz, 0, $title_x, $title_y, $bu_color, $fontfile, $bu_str);
$title_x += $rad + $bu_w; $title_y -= $hth;
for($i = 0 ; $i < 5 ; ++$i)
{
  imageline($image, $title_x, $title_y - 2 + $i, $title_x + 10 * $w_c, $title_y - 2 + $i, $bu_color);
}
// li
$title_x += $rad + 10 * $w_c; $title_y += $hth;
imagettftext($image, $fsz, 0, $title_x, $title_y, $li_color, $fontfile, $li_str);
$title_x += $rad + $li_w; $title_y -= $hth;
for($i = 0 ; $i < 5 ; ++$i)
{    
  imageline($image, $title_x, $title_y - 2 + $i, $title_x + 10 * $w_c, $title_y - 2 + $i, $li_color);
}
// bi
$title_x += $rad + 10 * $w_c; $title_y += $hth;
imagettftext($image, $fsz, 0, $title_x, $title_y, $bi_color, $fontfile, $bi_str);
$title_x += $rad + $bi_w; $title_y -= $hth;
for($i = 0 ; $i < 5 ; ++$i)
{    
  imageline($image, $title_x, $title_y - 2 + $i, $title_x + 10 * $w_c, $title_y - 2 + $i, $bi_color);
}
// bj
$title_x += $rad + 10 * $w_c; $title_y += $hth;
imagettftext($image, $fsz, 0, $title_x, $title_y, $bj_color, $fontfile, $bj_str);
$title_x += $rad + $bj_w; $title_y -= $hth;
for($i = 0 ; $i < 5 ; ++$i)
{    
  imageline($image, $title_x, $title_y - 2 + $i, $title_x + 10 * $w_c, $title_y - 2 + $i, $bj_color);
}

// Etaj
$bu_x_prev = 0; $bu_y_prev = 0; $bu_r_prev = 0;
$li_x_prev = 0; $li_y_prev = 0; $li_r_prev = 0;
$bi_x_prev = 0; $bi_y_prev = 0; $bi_r_prev = 0;
$bj_x_prev = 0; $bj_y_prev = 0; $bj_r_prev = 0;

for($i = $SIZE; $i >= 0; --$i)
{
  // Dormitor Luca
  $bu = $vn["bu"][$i]; $bu_r = $vn["bu_r"][$i];

  $bu_x = $l_gap + 2 * $w_c * ($SIZE - $i); $bu_y = $im_h - (($bu - $min_img) * $h_c + $b_gap);
  if($bu_x_prev === 0 && $bu_y_prev === 0) { $bu_x_prev = $bu_x; $bu_y_prev = $bu_y; $bu_r_prev = $bu_r; }

  imageline($image, $bu_x_prev, $bu_y_prev - 2, $bu_x, $bu_y - 2, $bu_color);
  imageline($image, $bu_x_prev, $bu_y_prev + 2, $bu_x, $bu_y + 2, $bu_color);
  if($bu_r_prev !== 0)
  {
    imageline($image, $bu_x_prev, $bu_y_prev - 1, $bu_x, $bu_y - 1, $bu_color);
    imageline($image, $bu_x_prev, $bu_y_prev, $bu_x, $bu_y, $bu_color);
    imageline($image, $bu_x_prev, $bu_y_prev + 1, $bu_x, $bu_y + 1, $bu_color);
  }
  $bu_x_prev = $bu_x; $bu_y_prev = $bu_y; $bu_r_prev = $bu_r;

  // Dormitor matrimonial
  $li = $vn["li"][$i]; $li_r = $vn["li_r"][$i];

  $li_x = $l_gap + 2 * $w_c * ($SIZE - $i); $li_y = $im_h - (($li - $min_img) * $h_c + $b_gap);
  if($li_x_prev === 0 && $li_y_prev === 0) { $li_x_prev = $li_x; $li_y_prev = $li_y; $li_r_prev = $li_r; }

  imageline($image, $li_x_prev, $li_y_prev - 2, $li_x, $li_y - 2, $li_color);
  imageline($image, $li_x_prev, $li_y_prev + 2, $li_x, $li_y + 2, $li_color);
  if($li_r_prev !== 0)
  {
    imageline($image, $li_x_prev, $li_y_prev - 1, $li_x, $li_y - 1, $li_color);
    imageline($image, $li_x_prev, $li_y_prev, $li_x, $li_y, $li_color);
    imageline($image, $li_x_prev, $li_y_prev + 1, $li_x, $li_y + 1, $li_color);
  }
  $li_x_prev = $li_x; $li_y_prev = $li_y; $li_r_prev = $li_r;

  // Dormitor oaspeti
  $bi = $vn["bi"][$i]; $bi_r = $vn["bi_r"][$i];

  $bi_x = $l_gap + 2 * $w_c * ($SIZE - $i); $bi_y = $im_h - (($bi - $min_img) * $h_c + $b_gap);
  if($bi_x_prev === 0 && $bi_y_prev === 0) { $bi_x_prev = $bi_x; $bi_y_prev = $bi_y; $bi_r_prev = $bi_r; }

  imageline($image, $bi_x_prev, $bi_y_prev - 2, $bi_x, $bi_y - 2, $bi_color);
  imageline($image, $bi_x_prev, $bi_y_prev + 2, $bi_x, $bi_y + 2, $bi_color);
  if($bi_r_prev !== 0)
  {
    imageline($image, $bi_x_prev, $bi_y_prev - 1, $bi_x, $bi_y - 1, $bi_color);
    imageline($image, $bi_x_prev, $bi_y_prev, $bi_x, $bi_y, $bi_color);
    imageline($image, $bi_x_prev, $bi_y_prev + 1, $bi_x, $bi_y + 1, $bi_color);
  }
  $bi_x_prev = $bi_x; $bi_y_prev = $bi_y; $bi_r_prev = $bi_r;

  // Baie jos
  $bj = $vn["bj"][$i]; $bj_r = $vn["bj_r"][$i];

  $bj_x = $l_gap + 2 * $w_c * ($SIZE - $i); $bj_y = $im_h - (($bj - $min_img) * $h_c + $b_gap);
  if($bj_x_prev === 0 && $bj_y_prev === 0) { $bj_x_prev = $bj_x; $bj_y_prev = $bj_y; $bj_r_prev = $bj_r; }

  imageline($image, $bj_x_prev, $bj_y_prev - 2, $bj_x, $bj_y - 2, $bj_color);
  imageline($image, $bj_x_prev, $bj_y_prev + 2, $bj_x, $bj_y + 2, $bj_color);
  if($bj_r_prev !== 0)
  {
    imageline($image, $bj_x_prev, $bj_y_prev - 1, $bj_x, $bj_y - 1, $bj_color);
    imageline($image, $bj_x_prev, $bj_y_prev, $bj_x, $bj_y, $bj_color);
    imageline($image, $bj_x_prev, $bj_y_prev + 1, $bj_x, $bj_y + 1, $bj_color);
  }
  $bj_x_prev = $bj_x; $bj_y_prev = $bj_y; $bj_r_prev = $bj_r;

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
imagejpeg($image, 'data/jos_history.jpg');
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
echo "   <tr><td align='center'><img src='data/jos_history.jpg'></td></tr>\n";
echo "   <tr><td></td></tr>\n";
echo "   <tr><td align='center'><form action='info.php' method='get'><input type='submit' value='Inapoi'></form></td></tr>\n";
echo "  </table>\n";
echo " </body>\n";
echo "</html>\n";
echo "\n";
?>

