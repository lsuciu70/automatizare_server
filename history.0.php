<?php                         
// make client to refresh every 10 seconds
$url1=$_SERVER['REQUEST_URI'];
header("Refresh: 10; URL=$url1");

$HOURS = 5;
$MEAS_PER_HOUR = 6;
$SIZE = $HOURS * $MEAS_PER_HOUR;

// current date and time
$format = "%Y.%m.%d %H:%M:%S";
$now_ts = strftime($format);
sscanf($now_ts, "%d.%d.%d %d:%d:%d", $ts_Y, $ts_M, $ts_D, $ts_h, $ts_m, $ts_s);

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
// var_dump($lines);
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
// var_dump($lines);
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
echo "    <td align='center' colspan='".($SIZE+1)."'>".$now_ts."</td>\n";
echo "   </tr>\n";
echo "   <tr>\n";
echo "    <td rowspan='2'></td>\n";
echo "    <td align='center' colspan='".($SIZE+1)."'><b>Temperatura ultimele ".$HOURS." ore</b><br>mediile pe fiecare 10 minute</td>\n";
echo "   </tr>\n";
echo "   <tr>\n";
for($i = 0; $i <= $SIZE; ++$i)
{
  echo "    <th align='center'>".$vn["hm"][$i]."</th>\n";
}
echo "\n";

echo "   <tr>\n";
echo "    <th align='center'>Dormitor Luca</th>\n";
for($i = 0; $i <= $SIZE; ++$i)
{
  $p_zec = $vn["dl"][$i] % 100; $p_int = ($vn["dl"][$i] - $p_zec) / 100;
  $color = ($vn["dl_r"][$i] > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font></td>\n";
}
echo "   </tr>\n";

echo "   <tr>\n"; 
echo "    <th align='center'>Dormitor matrimonial</th>\n";
for($i = 0; $i <= $SIZE; ++$i)
{
  $p_zec = $vn["dm"][$i] % 100; $p_int = ($vn["dm"][$i] - $p_zec) / 100;
  $color = ($vn["dm_r"][$i] > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font></td>\n";
}
echo "   </tr>\n";

echo "   <tr>\n"; 
echo "    <th align='center'>Dormitor oaspeti</th>\n";
for($i = 0; $i <= $SIZE; ++$i)
{
  $p_zec = $vn["d3"][$i] % 100; $p_int = ($vn["d3"][$i] - $p_zec) / 100;
  $color = ($vn["d3_r"][$i] > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font></td>\n";
}
echo "   </tr>\n";

echo "   <tr>\n"; 
echo "    <th align='center'>Baie etaj</th>\n";
for($i = 0; $i <= $SIZE; ++$i)
{
  $p_zec = $vn["bs"][$i] % 100; $p_int = ($vn["bs"][$i] - $p_zec) / 100;
  $color = ($vn["bs_r"][$i] > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font></td>\n";
}
echo "   </tr>\n";

echo "   <tr>\n"; 
echo "    <th align='center'>Bucatarie</th>\n";
for($i = 0; $i <= $SIZE; ++$i)
{
  $p_zec = $vn["bu"][$i] % 100; $p_int = ($vn["bu"][$i] - $p_zec) / 100;
  $color = ($vn["bu_r"][$i] > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font></td>\n";
}
echo "   </tr>\n";

echo "   <tr>\n"; 
echo "    <th align='center'>Living</th>\n";
for($i = 0; $i <= $SIZE; ++$i)
{
  $p_zec = $vn["li"][$i] % 100; $p_int = ($vn["li"][$i] - $p_zec) / 100;
  $color = ($vn["li_r"][$i] > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font></td>\n";
}
echo "   </tr>\n";

echo "   <tr>\n"; 
echo "    <th align='center'>Birou</th>\n";
for($i = 0; $i <= $SIZE; ++$i)
{
  $p_zec = $vn["bi"][$i] % 100; $p_int = ($vn["bi"][$i] - $p_zec) / 100;
  $color = ($vn["bi_r"][$i] > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font></td>\n";
}
echo "   </tr>\n";

echo "   <tr>\n"; 
echo "    <th align='center'>Baie parter</th>\n";
for($i = 0; $i <= $SIZE; ++$i)
{
  $p_zec = $vn["bj"][$i] % 100; $p_int = ($vn["bj"][$i] - $p_zec) / 100;
  $color = ($vn["bj_r"][$i] > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . "</font></td>\n";
}
echo "   </tr>\n";
echo "\n";
echo "   <tr><td colspan='".($SIZE+2)."'></td></tr>\n";
echo "   <tr><td colspan='".($SIZE+2)."' align='center'><form action='info.php' method='get'><input type='submit' value='Inapoi'></form></td></tr>\n";
echo "  </table>\n";
echo " </body>\n";
echo "</html>\n";
echo "\n";
?>
