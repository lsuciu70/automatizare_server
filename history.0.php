<?php                         
// make client to refresh every 10 seconds
$url1=$_SERVER['REQUEST_URI'];  
header("Refresh: 10; URL=$url1");

$HOURS = 3;
$SIZE = $HOURS * 6;

// // check if client is a mobile device
// if (preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]))
//   $is_mobile = 1;
// else
//   $is_mobile = 0;

// current date and time
$now_ts = strftime("%Y.%m.%d %H:%M:%S");
sscanf($now_ts, "%d.%d.%d %d:%d:%d", $ts_Y, $ts_M, $ts_D, $ts_h, $ts_m, $ts_s);

// read last temperatures
// read last temperatures
$jos_temp_file_name = "data/jos_temp.txt";
$sus_temp_file_name = "data/sus_temp.txt";
$is_jos = is_file($jos_temp_file_name);
$is_sus = is_file($sus_temp_file_name);
if($is_jos)
{
  $jos_temp = file_get_contents($jos_temp_file_name);
  sscanf($jos_temp, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_bu, $t_bu_r, $t_li, $t_li_r, $t_bi, $t_bi_r, $t_bj, $t_bj_r, $jos_ts);
  // 2016.10.20_11:08:01
  $start = strpos($jos_ts, "_");
  $stop = strrpos($jos_ts, ":");
  if($start !== FALSE && $stop !== FALSE)
  {
    $start += 1;
    $length = $stop - $start;
    $jos_ts = substr($jos_ts, $start, $length);
  }
$a_bu = array();
$a_li = array();
$a_bi = array();
$a_bj = array();
$a_bu_r = array();
$a_li_r = array();
$a_bi_r = array();
$a_bj_r = array();
$jos_time = array();
  for ( $i = 0 ; $i < $SIZE ; ++$i )
{
  $a_bu[$i] = 0;
  $a_li[$i] = 0;
  $a_bi[$i] = 0;
  $a_bj[$i] = 0;
  $a_bu_r[$i] = 0;
  $a_li_r[$i] = 0;
  $a_bi_r[$i] = 0;
  $a_bj_r[$i] = 0;
  $jos_time[$i] = "";
}
}
if($is_sus)
{
  $sus_temp = file_get_contents($sus_temp_file_name);
  sscanf($sus_temp, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_dl, $t_dl_r, $t_dm, $t_dm_r, $t_d3, $t_d3_r, $t_bs, $t_bs_r, $sus_ts);
  $start = strpos($sus_ts, "_");
  $stop = strrpos($sus_ts, ":");
  if($start !== FALSE && $stop !== FALSE)
  {
    $start += 1;
    $length = $stop - $start;
    $sus_ts = substr($sus_ts, $start, $length);
  }
$a_dl = array();
$a_dm = array();
$a_d3 = array();
$a_bs = array();
$a_dl_r = array();
$a_dm_r = array();
$a_d3_r = array();
$a_bs_r = array();
$sus_time = array();
for ( $i = 0 ; $i < $SIZE ; ++$i )
{
  $a_dl[$i] = 0;
  $a_dm[$i] = 0;
  $a_d3[$i] = 0;
  $a_bs[$i] = 0;
  $a_dl_r[$i] = 0;
  $a_dm_r[$i] = 0;
  $a_d3_r[$i] = 0;
  $a_bs_r[$i] = 0;
  $sus_time[$i] = "";
}
}

// read day temperatures

// parter
$day_file_jos_name = "/opt/www/data/jos_" . $ts_Y . "." . $ts_M . "." . $ts_D . ".txt";
$is_day_file_jos = is_file($day_file_jos_name);
if($is_day_file_jos)
{
  $day_file_jos = fopen($day_file_jos_name, "r");
  while (($line = fgets($day_file_jos)) !== FALSE)
  {
    for($i = ($SIZE - 1); $i > 0; --$i)
    {
      $a_bu[$i] = $a_bu[($i-1)]; $a_li[$i] = $a_li[($i-1)]; $a_bi[$i] = $a_bi[($i-1)]; $a_bj[$i] = $a_bj[($i-1)];
      $a_bu_r[$i] = $a_bu_r[$i-1]; $a_li_r[$i] = $a_li_r[$i-1]; $a_bi_r[$i] = $a_bi_r[$i-1]; $a_bj_r[$i] = $a_bj_r[$i-1];
      $jos_time[$i] = $jos_time[$i-1];
    }
    sscanf($line, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $a_bu[0], $a_bu_r[0], $a_li[0], $a_li_r[0], $a_bi[0], $a_bi_r[0], $a_bj[0], $a_bj_r[0], $jos_time[0]);
  }
  fclose($day_file_jos);
}

// etaj
$day_file_sus_name = "/opt/www/data/sus_" . $ts_Y . "." . $ts_M . "." . $ts_D . ".txt";
$is_day_file_sus = is_file($day_file_sus_name);
if($is_day_file_sus)                                                                                                                                                                     
{
  $day_file_sus = fopen($day_file_sus_name, "r");
  while (($line = fgets($day_file_sus)) !== FALSE)
  {
    for($i = ($SIZE - 1); $i > 0; --$i)
    {
      $a_dl[$i] = $a_dl[$i-1]; $a_dm[$i] = $a_dm[$i-1]; $a_d3[$i] = $a_d3[$i-1]; $a_bs[$i] = $a_bs[$i-1];
      $a_dl_r[$i] = $a_dl_r[$i-1]; $a_dm_r[$i] = $a_dm_r[$i-1]; $a_d3_r[$i] = $a_d3_r[$i-1]; $a_bs_r[$i] = $a_bs_r[$i-1];
      $sus_time[$i] = $sus_time[$i-1];
    }
    sscanf($line, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $a_dl[0], $a_dl_r[0], $a_dm[0], $a_dm_r[0], $a_d3[0], $a_d3_r[0], $a_bs[0], $a_bs_r[0], $sus_time[0]);
  }
  fclose($day_file_sus);
}

echo "<html>\n";
echo " <head>\n";
echo "  <title>CLLS - Istoric</title>\n";
echo "  <style>\n";
echo "   html { height:100%; min-height:100%; width:100%; min-width:100%; }\n";
echo "   body { font-size:large; }\n";
echo "   input { font-size:large; }\n";
echo "   table { border-collapse:collapse; border-style:solid; }\n";
echo "   th { padding:5px; border-style:solid; border-width: thin; }\n";
echo "   td { padding:5px; border-style:solid; border-width: thin; }\n";
echo "  </style>\n";
echo " </head>\n";
echo " <body>\n";
echo "  <table>\n";
echo "   <tr>\n";
echo "    <th align='left'>Data</th>\n";
echo "    <td align='center' colspan='".($SIZE + 1)."'>".$now_ts."</td>\n";
echo "   </tr>\n";
echo "   <tr>\n";
echo "    <td></td>\n";
echo "    <td><b>Temperatura</b></td>\n";
echo "    <td align='center' colspan='".$SIZE."'><b>Temperatura ultimele ".$HOURS." ore</b><br>mediile pe fiecare 10 minute</td>\n";
echo "   </tr>\n";
echo "\n";

if($is_jos)
{
  echo "   <tr><td colspan='".($SIZE + 2)."'></td></tr>\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Parter</th>\n";
  echo "    <td align='center'>".$jos_ts."</td>\n";
  for($i = 0; $i < $SIZE; ++$i)
  {
  echo "    <td align='center'>" . $jos_time[$i] . "</td>\n";
  }
  echo "   </tr>\n";

  echo "\n";
  echo "   <!-- Bucatarie -->\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Bucatarie</th>\n";
  $p_zec = $t_bu % 100; $p_int = ($t_bu - $p_zec) / 100;
  $color = ($t_bu_r > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . " &deg;C</font></td>\n"; 
  for($i = 0; $i < $SIZE; ++$i)
  {
    $p_zec = $a_bu[$i] % 100; $p_int = ($a_bu[$i] - $p_zec) / 100;
    $color = ($a_bu_r[$i] > 0) ? "red" : "blue";
    echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . " &deg;C</font></td>\n";
  }
  echo "   </tr>\n";

  echo "\n";
  echo "   <!-- Living -->\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Living</th>\n";
  $p_zec = $t_li % 100; $p_int = ($t_li - $p_zec) / 100;
  $color = ($t_li_r > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . " &deg;C</font></td>\n"; 
  for($i = 0; $i < $SIZE; ++$i)
  {
    $p_zec = $a_li[$i] % 100; $p_int = ($a_li[$i] - $p_zec) / 100;
    $color = ($a_li_r[$i] > 0) ? "red" : "blue";
    echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . " &deg;C</font></td>\n";
  }
  echo "   </tr>\n";

  echo "\n";
  echo "   <!-- Birou -->\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Birou</th>\n";
  $p_zec = $t_bi % 100; $p_int = ($t_bi - $p_zec) / 100;
  $color = ($t_bi_r > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . " &deg;C</font></td>\n"; 
  for($i = 0; $i < $SIZE; ++$i)
  {
    $p_zec = $a_bi[$i] % 100; $p_int = ($a_bi[$i] - $p_zec) / 100;
    $color = ($a_bi_r[$i] > 0) ? "red" : "blue";
    echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . " &deg;C</font></td>\n";
  }
  echo "   </tr>\n";

  echo "\n";
  echo "   <!-- Baie parter -->\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Baie parter</th>\n";
  $p_zec = $t_bj % 100; $p_int = ($t_bj - $p_zec) / 100;
  $color = ($t_bj_r > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . " &deg;C</font></td>\n"; 
  for($i = 0; $i < $SIZE; ++$i)
  {
    $p_zec = $a_bj[$i] % 100; $p_int = ($a_bj[$i] - $p_zec) / 100;
    $color = ($a_bj_r[$i] > 0) ? "red" : "blue";
    echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . " &deg;C</font></td>\n";
  }
  echo "   </tr>\n";
}
echo "\n";

  echo "   <tr><td colspan='".($SIZE + 2)."'></td></tr>\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Etaj</th>\n";
  echo "    <td align='center'>".$sus_ts."</td>\n";
  for($i = 0; $i < $SIZE; ++$i)
  {
  echo "    <td align='center'>" . $sus_time[$i] . "</td>\n";
  }
  echo "   </tr>\n";

  echo "\n";
  echo "   <!-- Dormitor Luca -->\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Dormitor Luca</th>\n";
  $p_zec = $t_dl % 100; $p_int = ($t_dl - $p_zec) / 100;
  $color = ($t_dl_r > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . " &deg;C</font></td>\n"; 
  for($i = 0; $i < $SIZE; ++$i)
  {
    $p_zec = $a_dl[$i] % 100; $p_int = ($a_dl[$i] - $p_zec) / 100;
    $color = ($a_dl_r[$i] > 0) ? "red" : "blue";
    echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . " &deg;C</font></td>\n";
  }
  echo "   </tr>\n";

  echo "\n";
  echo "   <!-- Dormitor matrimonial -->\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Dormitor matrimonial</th>\n";
  $p_zec = $t_dm % 100; $p_int = ($t_dm - $p_zec) / 100;
  $color = ($t_dm_r > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . " &deg;C</font></td>\n"; 
  for($i = 0; $i < $SIZE; ++$i)
  {
    $p_zec = $a_dm[$i] % 100; $p_int = ($a_dm[$i] - $p_zec) / 100;
    $color = ($a_dm_r[$i] > 0) ? "red" : "blue";
    echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . " &deg;C</font></td>\n";
  }
  echo "   </tr>\n";

  echo "\n";
  echo "   <!-- Dormitor oaspeti -->\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Dormitor oaspeti</th>\n";
  $p_zec = $t_d3 % 100; $p_int = ($t_d3 - $p_zec) / 100;
  $color = ($t_d3_r > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . " &deg;C</font></td>\n"; 
  for($i = 0; $i < $SIZE; ++$i)
  {
    $p_zec = $a_d3[$i] % 100; $p_int = ($a_d3[$i] - $p_zec) / 100;
    $color = ($a_d3_r[$i] > 0) ? "red" : "blue";
    echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . " &deg;C</font></td>\n";
  }
  echo "   </tr>\n";

  echo "\n";
  echo "   <!-- Baie etaj -->\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Baie etaj</th>\n";
  $p_zec = $t_bs % 100; $p_int = ($t_bs - $p_zec) / 100;
  $color = ($t_bs_r > 0) ? "red" : "blue";
  echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . " &deg;C</font></td>\n"; 
  for($i = 0; $i < $SIZE; ++$i)
  {
    $p_zec = $a_bs[$i] % 100; $p_int = ($a_bs[$i] - $p_zec) / 100;
    $color = ($a_bs_r[$i] > 0) ? "red" : "blue";
    echo "    <td align='center'><font color='" . $color . "'>" . $p_int . "."; if($p_zec < 10) echo "0"; echo $p_zec . " &deg;C</font></td>\n";
  }
  echo "   </tr>\n";

echo "\n";
echo "   <tr><td colspan='".($SIZE + 2)."'></td></tr>\n";
echo "   <tr><td colspan='".($SIZE + 2)."' align='center'><form action='info.php' method='get'><input type='submit' value='Inapoi'></form></td></tr>\n";
echo "  </table>\n";
echo " </body>\n";
echo "</html>\n";
echo "\n";
?>