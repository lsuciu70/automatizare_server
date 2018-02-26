<?php
include_once 'temperature_helper.php';

// make client to refresh every 10 seconds
$url1=$_SERVER['REQUEST_URI'];
header("Refresh: 10; URL=$url1");

// current date and time
$now_ts = strftime("%d-%m-%Y %H:%M:%S");
$ts_full = strftime("%Y.%m.%d_%H:%M:%S");
sscanf($ts_full, "%d.%d.%d_%d:%d:%d", $ts_Y, $ts_M, $ts_D, $ts_h, $ts_m, $ts_s);
$t_day = sprintf("%d.%d.%d", $ts_Y, $ts_M, $ts_D);

// read last temperatures
$jos_temp_file_name = "data/jos_temp.txt";
$sus_temp_file_name = "data/sus_temp.txt";
$is_jos = is_file($jos_temp_file_name);
$is_sus = is_file($sus_temp_file_name);
$jos_ts = "-";
$sus_ts = "-";
$now_dm = (60 * $ts_h) + $ts_m;
$jos_dm = 0;
$sus_dm = 0;
if($is_jos)
{
  $jos_temp = file_get_contents($jos_temp_file_name);
  sscanf($jos_temp, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_bu, $t_bu_r, $t_li, $t_li_r, $t_bi, $t_bi_r, $t_bj, $t_bj_r, $jos_ts);
  sscanf($jos_ts, "%d.%d.%d_%d:%d:%d", $ts_fY, $ts_fM, $ts_fD, $ts_fh, $ts_fm, $ts_fs);
  $jos_ts = sprintf("%02d-%02d-%d %02d:%02d:%02d", $ts_fD, $ts_fM, $ts_fY, $ts_fh, $ts_fm, $ts_fs);
  $jos_dm = (60 * $ts_fh) + $ts_fm;
//   if(abs($ts_fm - $ts_m) > 1)
//   {
//     $t_bu = 0; $t_bu_r = 0; $t_li = 0; $t_li_r = 0; $t_bi = 0; $t_bi_r = 0; $t_bj = 0; $t_bj_r = 0;
//   }
}
else
{
  $t_bu = 0; $t_bu_r = 0; $t_li = 0; $t_li_r = 0; $t_bi = 0; $t_bi_r = 0; $t_bj = 0; $t_bj_r = 0;
}
if($is_sus)
{
  $sus_temp = file_get_contents($sus_temp_file_name);
  sscanf($sus_temp, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_dl, $t_dl_r, $t_dm, $t_dm_r, $t_d3, $t_d3_r, $t_bs, $t_bs_r, $sus_ts);
  sscanf($sus_ts, "%d.%d.%d_%d:%d:%d", $ts_fY, $ts_fM, $ts_fD, $ts_fh, $ts_fm, $ts_fs);
  $sus_dm = (60 * $ts_fh) + $ts_fm;
  $sus_ts = sprintf("%02d-%02d-%d %02d:%02d:%02d", $ts_fD, $ts_fM, $ts_fY, $ts_fh, $ts_fm, $ts_fs);
  //   if(abs($ts_fm - $ts_m) > 1)
//   {
//     $t_dl = 0; $t_dl_r = 0; $t_dm = 0; $t_dm_r = 0; $t_d3 = 0; $t_d3_r = 0; $t_bs = 0; $t_bs_r = 0;
//   }
}
else
{
  $t_dl = 0; $t_dl_r = 0; $t_dm = 0; $t_dm_r = 0; $t_d3 = 0; $t_d3_r = 0; $t_bs = 0; $t_bs_r = 0;
}

$vn = array(                                                             
  "programming",                                                                
  "start_hour_p1",                                                              
  "start_minute_p1",                                                            
  "stop_hour_p1",                                                               
  "stop_minute_p1",                                                             
  "target_temperature_p1",                                                      
  "start_hour_p2",                                                              
  "start_minute_p2",                                                            
  "next_programm_p2",                                                           
  "target_temperature_p2",                                                      
  "start_hour_p3",                                                              
  "start_minute_p3",                                                            
  "next_programm_p3",                                                           
  "target_temperature_p3",                                                      
  "next_programm_p4",                                                           
  "target_temperature_p4",                                                      
);                                                                              
$vn_val = array (
  $vn[0] => array(1, 1, 1, 1, 1, 1, 1, 1), // programming
  $vn[1] => array(15, 15, 15, 15, 15, 15, 15, 15), // start_hour_p1
  $vn[2] => array(0, 0, 0, 0, 0, 0, 0, 0), // start_minute_p1
  $vn[3] => array(7, 7, 7, 7, 7, 7, 7, 7), // stop_hour_p1
  $vn[4] => array(0, 0, 0, 0, 0, 0, 0, 0), // stop_minute_p1
  $vn[5] => array(2100, 2100, 1800, 2100, 2300, 2300, 2300, 2100), // target_temperature_p1
  $vn[6] => array(4, 4, 4, 4, 4, 4, 4, 4), // start_hour_p2
  $vn[7] => array(30, 30, 30, 30, 30, 30, 30, 30), // start_minute_p2
  $vn[8] => array(3, 3, 3, 3, 3, 3, 3, 3), // next_programm_p2
  $vn[9] => array(0, 0, 0, 0, 0, 0, 0, 0), // target_temperature_p2
  $vn[10] => array(14, 14, 14, 14, 14, 14, 14, 14), // start_hour_p3
  $vn[11] => array(30, 30, 30, 30, 30, 30, 30, 30), // start_minute_p3
  $vn[12] => array(2, 2, 2, 2, 2, 2, 2, 2), // next_programm_p3
  $vn[13] => array(0, 0, 0, 0, 0, 0, 0, 0), // target_temperature_p3
  $vn[14] => array(0, 0, 0, 0, 0, 0, 0, 0), // next_programm_p4
  $vn[15] => array(0, 0, 0, 0, 0, 0, 0, 0), // target_temperature_p4
);
$vn_cnt = count($vn);
for ($x = 0 ; $x < $vn_cnt ; $x++ )
{
  $n = $vn[$x];
  $fn = "data/" . $n . ".txt";
  if (is_file($fn))
  {
    $line = file_get_contents($fn);
    sscanf($line, "%d,%d,%d,%d,%d,%d,%d,%d", $vn_val[$n][0], $vn_val[$n][1], $vn_val[$n][2], $vn_val[$n][3], $vn_val[$n][4], $vn_val[$n][5], $vn_val[$n][6], $vn_val[$n][7]);
  }
}
$prog_str = array("", "", "", "", "", "", "", "");
for ($i = 0 ; $i < 8 ; $i++)
{
  $p = $vn_val["programming"][$i];
  switch ($p)
  {
  case 0:
    $prog_str[$i] = "P0 - oprit";
    break;
  case 1:
    $shp1 = $vn_val["start_hour_p1"][$i];
    $smp1 = $vn_val["start_minute_p1"][$i];
    $ehp1 = $vn_val["stop_hour_p1"][$i];
    $emp1 = $vn_val["stop_minute_p1"][$i];
    $tp1 = $vn_val["target_temperature_p1"][$i];
//     $tp1_zec = $tp1 % 100;
//     $tp1_int = intval(($tp1 - $tp1_zec) / 100);
    $prog_str[$i] = "P1 - merge intre ".sprintf ( "%d:%02d", $shp1, $smp1)." si ".sprintf ( "%d:%02d", $ehp1, $emp1);
    if($shp1 > $ehp1 || ($shp1 === $ehp1 && $smp1 >= $emp1))
      $prog_str[$i] .= " (ziua urmatoare)";
    $prog_str[$i] .= " si face ".centi_to_string($tp1)." &deg;C";
//     $prog_str[$i] = "P1 - merge intre ".$shp1.":";
//     if($smp1 < 10) $prog_str[$i] .= "0";
//     $prog_str[$i] .= $smp1." si ".$ehp1.":";
//     if($emp1 < 10) $prog_str[$i] .= "0";
//     $prog_str[$i] .= $emp1;
//     if($shp1 > $ehp1 || ($shp1 === $ehp1 && $smp1 >= $emp1)) $prog_str[$i] .= " (ziua urmatoare)";
//     $prog_str[$i] .= " si face ".$tp1_int.".";
//     if($tp1_zec < 10) $prog_str[$i] .= "0";
//     $prog_str[$i] .= $tp1_zec." &deg;C";
    break;
  case 2:
    $shp2 = $vn_val["start_hour_p2"][$i];
    $smp2 = $vn_val["start_minute_p2"][$i];
    $prog_str[$i] ="P2 - porneste la ".$shp2.":";
    if($smp2 < 10) $prog_str[$i] .= "0";
    $prog_str[$i] .= $smp2;
    $tp2 = $vn_val["target_temperature_p2"][$i];
    $tp2_zec = $tp2 % 100;
    $tp2_int = intval(($tp2 - $tp2_zec) / 100);
    if($tp2 !== 0)
    {
      $prog_str[$i] .= " si face ".$tp2_int.".";
      if($tp2_zec < 10) $prog_str[$i] .= "0";
      $prog_str[$i] .= $tp2_zec." &deg;C";
    }
    else $prog_str[$i] .= " si face temperatura de la pornire + 0.3 &deg;C";
    break;
  case 3:
    $shp3 = $vn_val["start_hour_p3"][$i];
    $smp3 = $vn_val["start_minute_p3"][$i];
    $prog_str[$i] ="P3 - porneste la ".$shp3.":";
    if($smp3 < 10) $prog_str[$i] .= "0";
    $prog_str[$i] .= $smp3;
    $tp3 = $vn_val["target_temperature_p3"][$i];
    $tp3_zec = $tp3 % 100;
    $tp3_int = intval(($tp3 - $tp3_zec) / 100);
    if($tp3 !== 0)
    {
      $prog_str[$i] .= " si face ".$tp3_int.".";
      if($tp3_zec < 10) $prog_str[$i] .= "0";
      $prog_str[$i] .= $tp3_zec." &deg;C";
    }
    else $prog_str[$i] .= " si face temperatura de la pornire + 0.3 &deg;C";
    break;
  case 4:
    $prog_str[$i] = "P4 - porneste acum";
    $tp4 = $vn_val["target_temperature_p4"][$i];
    $tp4_zec = $tp4 % 100;
    $tp4_int = intval(($tp4 - $tp4_zec) / 100);
    if($tp4 !== 0)
    {
      $prog_str[$i] .= " si face ".$tp4_int.".";
      if($tp4_zec < 10) $prog_str[$i] .= "0";
      $prog_str[$i] .= $tp4_zec." &deg;C";
    }
    else $prog_str[$i] .= " si face temperatura de la pornire + 0.3 &deg;C";
    break;
  default:
    $prog_str[$i] = "Program necunoscut: ".$p;
    break;
  }
}

// logs
$log = array();
$log_count = 0;
$log_file_name = "data/log_" . $t_day . ".txt";
$is_log_file = is_file($log_file_name);
if($is_log_file)
{
  $log_file = fopen($log_file_name, "r");
  while (($line = fgets($log_file)) !== FALSE)
  {
    $log[$log_count] = $line;
    ++$log_count;
  }
}

echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo " <head>\n";
echo "  <title>CLLS</title>\n";
echo "  <style>\n";
echo "   html { height:100%; min-height:100%; width:100%; min-width:100%; }\n";
echo "   body { font-size:large; }\n";
echo "   input { font-size:large; }\n";
echo "   table { border-collapse:collapse; border-style:solid; }\n";
echo "   th { padding:5px; border-style:solid; border-width:thin; }\n";
echo "   td { padding:5px; border-style:solid; border-width:thin; }\n";
echo "  </style>\n";
echo " </head>\n";
echo " <body>\n";
echo "  <table>\n";
echo "   <tr>\n";
echo "    <th>Data</th>\n";
echo "    <th align='center' colspan='4'>".$now_ts."</td>\n";
echo "   </tr>\n";
echo "   <tr>\n";
echo "    <td></td>\n";
echo "    <th align='center'>Temperatura</th>\n";
echo "    <th align='center'>Merge</th>\n";
echo "    <th align='center' colspan='2'>Program</th>\n";
echo "   </tr>\n";

if($is_sus)
{
  $t_loc = "etaj";
  echo "   <tr><td colspan='5'></td></tr>\n";
  echo "   <tr>\n";
  echo "    <th align='center'>Etaj</th>\n";
  echo "    <td align='center'><form action='history.php' method='post'><input type='submit' value='Istoric'></form></td>\n";
  echo "    <td align='left' colspan='3'>"; if(($now_dm - $sus_dm) >= 5) echo "<font color='red'>Ultima actualizare: ".$sus_ts."</font>"; else  echo "Ultima actualizare: ".$sus_ts;  echo "</td>\n";
  echo "   </tr>\n";
  echo "   <tr><td colspan='5'></td></tr>\n";
  echo "\n";
  echo "   <!-- room: Camera Luca -->\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Camera Luca</th>\n";
  echo "    <td align='center'>" . centi_to_string($t_dl) . " &deg;C</td>\n";
  echo "    <td align='center'>"; if(0 == $t_dl_r) echo "<font color='blue'>NU</font>"; else echo "<font color='red'>DA</font>"; echo "</td>\n";
  echo "    <td align='left'>" . $prog_str[4] . "</td>\n";
  echo "    <td align='center'>\n";
  echo "     <form action='programming.php' method='post'>\n";
  echo "      <input type='hidden' name='t_loc' value='etaj'>\n";
  echo "      <input type='hidden' name='t_room' value='0'>\n";
  echo "      <input type='submit' value='Programare'>\n";
  echo "     </form>\n";
  echo "    </td>\n";
  echo "   </tr>\n";
  echo "\n";
  echo "   <!-- room: Dormitor matrimonial -->\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Dormitor matrimonial</th>\n";
  echo "    <td align='center'>" . centi_to_string($t_dm) . " &deg;C</td>\n";
  echo "    <td align='center'>"; if(0 == $t_dm_r) echo "<font color='blue'>NU</font>"; else echo "<font color='red'>DA</font>"; echo "</td>\n";
  echo "    <td align='left'>" . $prog_str[5] . "</td>\n";
  echo "    <td align='center'>\n";
  echo "     <form action='programming.php' method='post'>\n";
  echo "      <input type='hidden' name='t_loc' value='etaj'>\n";
  echo "      <input type='hidden' name='t_room' value='1'>\n";
  echo "      <input type='submit' value='Programare'>\n";
  echo "     </form>\n";
  echo "    </td>\n";
  echo "   </tr>\n";
  echo "\n";
  echo "   <!-- room: Dormitor oaspeti -->\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Dormitor oaspeti</th>\n";
  echo "    <td align='center'>" . centi_to_string($t_d3) . " &deg;C</td>\n";
  echo "    <td align='center'>"; if(0 == $t_d3_r) echo "<font color='blue'>NU</font>"; else echo "<font color='red'>DA</font>"; echo "</td>\n";
  echo "    <td align='left'>" . $prog_str[6] . "</td>\n";
  echo "    <td align='center'>\n";
  echo "     <form action='programming.php' method='post'>\n";
  echo "      <input type='hidden' name='t_loc' value='etaj'>\n";
  echo "      <input type='hidden' name='t_room' value='2'>\n";
  echo "      <input type='submit' value='Programare'>\n";
  echo "     </form>\n";
  echo "    </td>\n";
  echo "   </tr>\n";
  echo "\n";
  echo "   <!-- Baie etaj -->\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Baie etaj</th>\n";
  echo "    <td align='center'>" . centi_to_string($t_bs) . " &deg;C</td>\n";
  echo "    <td align='center'>"; if(0 == $t_bs_r) echo "<font color='blue'>NU</font>"; else echo "<font color='red'>DA</font>"; echo "</td>\n";
  echo "    <td align='left'>" . $prog_str[7] . "</td>\n";
  echo "    <td align='center'>\n";
  echo "     <form action='programming.php' method='post'>\n";
  echo "      <input type='hidden' name='t_loc' value='etaj'>\n";
  echo "      <input type='hidden' name='t_room' value='3'>\n";
  echo "      <input type='submit' value='Programare'>\n";
  echo "     </form>\n";
  echo "    </td>\n";
  echo "   </tr>\n";
}
if($is_jos)
{
  $t_loc = "parter";
  echo "   <tr><td colspan='5'></td></tr>\n";
  echo "   <tr>\n";
  echo "    <th align='center'>Parter</th>\n";
  echo "    <td align='center'><form action='history.php' method='post'><input type='submit' value='Istoric'></form></td>\n";
  echo "    <td align='left' colspan='3'>"; if(($now_dm - $jos_dm) >= 5) echo "<font color='red'>Ultima actualizare: ".$jos_ts."</font>"; else  echo "Ultima actualizare: ".$jos_ts;  echo "</td>\n";
  echo "   </tr>\n";
  echo "   <tr><td colspan='5'></td></tr>\n";
  echo "\n";
  echo "   <!-- room: Bucatarie -->\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Bucatarie</th>\n";
  echo "    <td align='center'>" . centi_to_string($t_bu) . " &deg;C</td>\n";
  echo "    <td align='center'>"; if(0 == $t_bu_r) echo "<font color='blue'>NU</font>"; else echo "<font color='red'>DA</font>"; echo "</td>\n";
  echo "    <td align='left'>" . $prog_str[0] . "</td>\n";
  echo "    <td align='center'>\n";
  echo "     <form action='programming.php' method='post'>\n";
  echo "      <input type='hidden' name='t_loc' value='parter'>\n";
  echo "      <input type='hidden' name='t_room' value='0'>\n";
  echo "      <input type='submit' value='Programare'>\n";
  echo "     </form>\n";
  echo "    </td>\n";
  echo "   </tr>\n";
  echo "\n";
  echo "   <!-- room: Living -->\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Living</th>\n";
  echo "    <td align='center'>" . centi_to_string($t_li) . " &deg;C</td>\n";
  echo "    <td align='center'>"; if(0 == $t_li_r) echo "<font color='blue'>NU</font>"; else echo "<font color='red'>DA</font>"; echo "</td>\n";
  echo "    <td align='left'>" . $prog_str[1] . "</td>\n";
  echo "    <td align='center'>\n";
  echo "     <form action='programming.php' method='post'>\n";
  echo "      <input type='hidden' name='t_loc' value='parter'>\n";
  echo "      <input type='hidden' name='t_room' value='1'>\n";
  echo "      <input type='submit' value='Programare'>\n";
  echo "     </form>\n";
  echo "    </td>\n";
  echo "   </tr>\n";
  echo "\n";
  echo "   <!-- room: Birou -->\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Birou</th>\n";
  echo "    <td align='center'>" . centi_to_string($t_bi) . " &deg;C</td>\n";
  echo "    <td align='center'>"; if(0 == $t_bi_r) echo "<font color='blue'>NU</font>"; else echo "<font color='red'>DA</font>"; echo "</td>\n";
  echo "    <td align='left'>" . $prog_str[2] . "</td>\n";
  echo "    <td align='center'>\n";
  echo "     <form action='programming.php' method='post'>\n";
  echo "      <input type='hidden' name='t_loc' value='parter'>\n";
  echo "      <input type='hidden' name='t_room' value='2'>\n";
  echo "      <input type='submit' value='Programare'>\n";
  echo "     </form>\n";
  echo "    </td>\n";
  echo "   </tr>\n";
  echo "\n";
  echo "   <!-- room: Baie parter -->\n";
  echo "   <tr>\n";
  echo "    <th align='left'>Baie parter</th>\n";
  echo "    <td align='center'>" . centi_to_string($t_bj) . " &deg;C</td>\n";
  echo "    <td align='center'>"; if(0 == $t_bj_r) echo "<font color='blue'>NU</font>"; else echo "<font color='red'>DA</font>"; echo "</td>\n";
  echo "    <td align='left'>" . $prog_str[3] . "</td>\n";
  echo "    <td align='center'>\n";
  echo "     <form action='programming.php' method='post'>\n";
  echo "      <input type='hidden' name='t_loc' value='parter'>\n";
  echo "      <input type='hidden' name='t_room' value='3'>\n";
  echo "      <input type='submit' value='Programare'>\n";
  echo "     </form>\n";
  echo "    </td>\n";
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
?>

