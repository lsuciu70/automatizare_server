<?php
// make client to refresh every 10 seconds
// $url1=$_SERVER['REQUEST_URI'];
// header("Refresh: 10; URL=$url1");

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
$jos_file_name = "data/jos_" . $ts_Y . "." . $ts_M . "." . $ts_D . ".txt";
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

$lines = array();
$count = 0;
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
$lines = array();
// var_dump($vn);

// images
if(is_file("/usr/share/fonts/truetype/freefont/FreeSerif.ttf"))
  $fontfile = "/usr/share/fonts/truetype/freefont/FreeSerif.ttf";
else if(is_file("/opt/share/fonts/bitstream-vera/VeraSe.ttf"))
  $fontfile = "/opt/share/fonts/bitstream-vera/VeraSe.ttf";

$max_all = 0; $min_all = 0;
$max_dl = 0; $min_dl = 0;
for($i = 0; $i <= $SIZE; ++$i)
{
  if(($dl = $vn["dl"][$i]) > 0)
  {
    if($max_dl < $dl)
      $max_dl = $dl;
    if($min_dl === 0 || $min_dl > $dl)
      $min_dl = $dl;
    if($max_all < $max_dl)
      $max_all = $max_dl;
    if($min_all === 0 || $min_all > $min_dl)
      $min_all = $min_dl;
  }
}
//var_dump($max_all);
$max_all = $max_all - ($max_all % 10) + 20;
//var_dump($max_all);

//var_dump($min_all);
$min_all  = $min_all - ($min_all % 10) - 10;
//var_dump($min_all);

//// dot radius
$rad = 8;
//// font size
$fsz = 12;
//// height & width temperature coeficient
$h_c = 1;
$w_c = 6;

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
//// "Dormitor Luca" text width & height
$dl_str = "Dormitor Luca";
$dl_box = imagettfbbox($fsz, 0, $fontfile, $dl_str);
$dl_w = $dl_box[2] - $dl_box[0];
$dl_h = $dl_box[1] - $dl_box[7];

//// lelft and right gaps
$l_gap = 2 * $rad + $temp_w;
$r_gap = $l_gap;
//// bottom and top gaps
$b_gap = 3 * $rad + $time_h;
$t_gap = 2 * $rad + $dl_h;

//// image width
$im_w = (2 * $SIZE + 1) * $w_c + $l_gap + $r_gap;
$im_h = ($max_all - $min_all) * $h_c + $b_gap + $t_gap;

//// the image object
$dl_im = imagecreatetruecolor($im_w, $im_h);
//// make white background
$white_color = imagecolorallocate($dl_im, 255, 255, 255);
imagefill($dl_im, 0, 0, $white_color);
//// color black
$black_color = imagecolorallocate($dl_im, 0, 0, 0);
//// color blue
$blue_color = imagecolorallocate($dl_im, 0, 0, 255);
//// color red
$red_color = imagecolorallocate($dl_im, 255, 0, 0);

//// Dormitor Luca
imagettftext($dl_im, $fsz, 0, ($im_w / 2 - $dl_w / 2), ($rad + $dl_h), $black_color, $fontfile, $dl_str);

$dl_max_done = FALSE; $dl_min_done = FALSE;
for($i = $SIZE; $i >= 0; --$i)
{
  $dl = $vn["dl"][$i];

  $dl_x = $l_gap + 2 * $w_c * ($SIZE - $i);
  $dl_y = $im_h - (($dl - $min_all) * $h_c + $b_gap);

  if($vn["dl_r"][$i] === 0)
    $fg_color = $blue_color;
  else
    $fg_color = $red_color;
  imagefilledellipse($dl_im, $dl_x, $dl_y, $rad, $rad, $fg_color);
  if(($dl_max_done === FALSE && ($dl_max_done = ($dl === $max_dl)) === TRUE) ||
     ($dl_min_done === FALSE && ($dl_min_done = ($dl === $min_dl)) === TRUE)  ||
     ($i === 0 && ($max_dl - $dl) * $h_c > $temp_h + $rad && ($dl - $min_dl) * $h_c > $temp_h + $rad) ||
     ($i === $SIZE && ($max_dl - $dl) * $h_c > $temp_h + $rad && ($dl - $min_dl) * $h_c > $temp_h + $rad))
  {
    $p_zec = $dl % 100; $p_int = ($dl - $p_zec) / 100;
    $dl_temp = sprintf("%2d.%02d", $p_int, $p_zec);
    // write temperature (left text)
    imagettftext($dl_im, $fsz, 0, ($im_w - $rad - $temp_w), ($dl_y + ($fsz / 2)), $black_color, $fontfile, $dl_temp);
    // imageline ( resource $image , int $x1 , int $y1 , int $x2 , int $y2 , int $color )
    imageline($dl_im, $dl_x, $dl_y, ($im_w - $r_gap), $dl_y, $black_color);
  }
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
    imagettftext($dl_im, $fsz, 0, ($hm_x + $rad - $time_w / 2), $im_h - $rad, $black_color, $fontfile, $hm);
    imageline($dl_im, $hm_x, $hm_y, $hm_x, $t_gap, $black_color);
  }
}

//// max temperature
$p_zec = $max_all % 100; $p_int = ($max_all - $p_zec) / 100;
$max_str = sprintf("%2d.%02d", $p_int, $p_zec);
$max_x = $l_gap - $rad; $max_y = $im_h - (($max_all - $min_all) * $h_c + $b_gap);
imagettftext($dl_im, $fsz, 0, $rad, ($max_y + ($fsz / 2)), $black_color, $fontfile, $max_str);
imageline($dl_im, $max_x, $max_y, ($im_w - $r_gap), $max_y, $black_color);
//// min temperature
$p_zec = $min_all % 100; $p_int = ($min_all - $p_zec) / 100;
$min_str = sprintf("%2d.%02d", $p_int, $p_zec);
$min_x = $l_gap - $rad; $min_y = $im_h - $b_gap;
imagettftext($dl_im, $fsz, 0, $rad, ($min_y + ($fsz / 2)), $black_color, $fontfile, $min_str);
imageline($dl_im, $min_x, $min_y, ($im_w - $r_gap), $min_y, $black_color);

//// save as jpeg
imagejpeg($dl_im, 'data/dl.jpg');
// Free up memory
imagedestroy($dl_im);
exit;

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
echo "   <tr>\n";
echo "    <th align='center'>Data</th>\n";
echo "    <td align='center' colspan='8'>".$now_ts."</td>\n";
echo "   </tr>\n";
echo "   <tr>\n";
echo "    <td rowspan='2'></td>\n";
echo "    <td align='center' colspan='8'><b>Temperatura ultimele ".$HOURS." ore</b><br>mediile pe fiecare 10 minute</td>\n";
echo "   </tr>\n";
echo "\n";
echo "   <tr>\n";
echo "    <th align='center'>Dormitor Luca</th>\n";
echo "    <th align='center'>Dormitor matrimonial</th>\n";
echo "    <th align='center'>Dormitor oaspeti</th>\n";
echo "    <th align='center'>Baie etaj</th>\n";
echo "    <th align='center'>Bucatarie</th>\n";
echo "    <th align='center'>Living</th>\n";
echo "    <th align='center'>Birou</th>\n";
echo "    <th align='center'>Baie parter</th>\n";
echo "   </tr>\n";
for($i = 0; $i <= $SIZE; ++$i)
{
  echo "   <tr>\n";  
  echo "    <th align='center'>".$vn["hm"][$i]."</th>\n";

  $p_zec = $vn["dl"][$i] % 100; $p_int = ($vn["dl"][$i] - $p_zec) / 100;
  $color = ($vn["dl_r"][$i] > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font></td>\n";

  $p_zec = $vn["dm"][$i] % 100; $p_int = ($vn["dm"][$i] - $p_zec) / 100;
  $color = ($vn["dm_r"][$i] > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font></td>\n";

  $p_zec = $vn["d3"][$i] % 100; $p_int = ($vn["d3"][$i] - $p_zec) / 100;
  $color = ($vn["d3_r"][$i] > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font></td>\n";

  $p_zec = $vn["bs"][$i] % 100; $p_int = ($vn["bs"][$i] - $p_zec) / 100;
  $color = ($vn["bs_r"][$i] > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font></td>\n";

  $p_zec = $vn["bu"][$i] % 100; $p_int = ($vn["bu"][$i] - $p_zec) / 100;
  $color = ($vn["bu_r"][$i] > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font></td>\n";

  $p_zec = $vn["li"][$i] % 100; $p_int = ($vn["li"][$i] - $p_zec) / 100;
  $color = ($vn["li_r"][$i] > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font></td>\n";

  $p_zec = $vn["bi"][$i] % 100; $p_int = ($vn["bi"][$i] - $p_zec) / 100;
  $color = ($vn["bi_r"][$i] > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font></td>\n";

  $p_zec = $vn["bj"][$i] % 100; $p_int = ($vn["bj"][$i] - $p_zec) / 100;
  $color = ($vn["bj_r"][$i] > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font></td>\n";

  echo "   </tr>\n";
echo "\n";
}
echo "   <tr><td colspan='9'></td></tr>\n";
echo "   <tr><td colspan='9' align='center'><form action='info.php' method='get'><input type='submit' value='Inapoi'></form></td></tr>\n";
echo "  </table>\n";
echo " </body>\n";
echo "</html>\n";
echo "\n";
?>
