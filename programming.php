<?php
// variables
$room = "unknown";
$room_name = array("Bucatarie", "Living", "Birou", "Baie parter", "Camera Luca", "Dormitor matrimonial", "Dormitor oaspeti", "Baie etaj");

// get post data
$LOC_SUS = "etaj";
$LOC_JOS = "parter";
$location_offset = 0;
if(isset($_POST["t_loc"]))
{
  $t_loc=htmlspecialchars($_POST["t_loc"]);
  if (strpos($t_loc, $LOC_JOS) !== FALSE)
    $location_offset = 0;
  else if (strpos($t_loc, $LOC_SUS) !== FALSE)
    $location_offset = 4;
  else
  {
    error_log("ERROR - Unknown t_loc: ".$t_loc);
    exit("ERROR - Unknown t_loc: ".$t_loc);
  }
}
else
{
  error_log("ERROR - Empty t_loc");
  exit("ERROR - Empty t_loc");
}

$room_idx = "";
if(isset($_POST["t_room"]))
  $room_idx = intval(htmlspecialchars($_POST["t_room"]));
else
{
  error_log("ERROR - Empty t_room");
  exit("ERROR - Empty t_room");
}

$index = $room_idx + $location_offset;
$room = $room_name[$index];

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
  "start_hour_p3",
  "start_minute_p3",
  "next_programm_p3",
  "next_programm_p4",
);
$vn_val = array (
  $vn[0] => array(1, 1, 1, 1, 1, 1, 1, 1),
  $vn[1] => array(15, 15, 15, 15, 15, 15, 15, 15),
  $vn[2] => array(0, 0, 0, 0, 0, 0, 0, 0),
  $vn[3] => array(7, 7, 7, 7, 7, 7, 7, 7),
  $vn[4] => array(0, 0, 0, 0, 0, 0, 0, 0),
  $vn[5] => array(2000, 2000, 1500, 2000, 2200, 2200, 2000, 2100),
  $vn[6] => array(4, 4, 4, 4, 4, 4, 4, 4),
  $vn[7] => array(30, 30, 30, 30, 30, 30, 30, 30),
  $vn[8] => array(3, 3, 3, 3, 3, 3, 3, 3),
  $vn[9] => array(14, 14, 14, 14, 14, 14, 14, 14),
  $vn[10] => array(30, 30, 30, 30, 30, 30, 30, 30),
  $vn[11] => array(2, 2, 2, 2, 2, 2, 2, 2),
  $vn[12] => array(0, 0, 0, 0, 0, 0, 0, 0)
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
$p = $vn_val["programming"][$index];
$shp1 = $vn_val["start_hour_p1"][$index];
$smp1 = $vn_val["start_minute_p1"][$index];
$ehp1 = $vn_val["stop_hour_p1"][$index];
$emp1 = $vn_val["stop_minute_p1"][$index];
$tp1 = $vn_val["target_temperature_p1"][$index];
$tp1_zec = $tp1 % 100;
$tp1_int = intval(($tp1 - $tp1_zec) / 100);
$shp2 = $vn_val["start_hour_p2"][$index];
$smp2 = $vn_val["start_minute_p2"][$index];
$npp2 = $vn_val["next_programm_p2"][$index];
$shp3 = $vn_val["start_hour_p3"][$index];
$smp3 = $vn_val["start_minute_p3"][$index];
$npp3 = $vn_val["next_programm_p3"][$index];
$npp4 = $vn_val["next_programm_p4"][$index];

$prog_str_0 = "P0 - oprit";

$prog_str_1 = "P1 - merge intre ".$shp1.":";
if($smp1 < 10) $prog_str_1 .= "0";
$prog_str_1 .= $smp1." si ".$ehp1.":";
if($emp1 < 10) $prog_str_1 .= "0";
$prog_str_1 .= $emp1;
if($shp1 > $ehp1) $prog_str_1 .= " (ziua urmatoare)";
$prog_str_1 .= " si face ".$tp1_int.".";
if($tp1_zec < 10) $prog_str_1 .= "0";
$prog_str_1 .= $tp1_zec." &deg;C";

$prog_str_2 ="P2 - porneste la ".$shp2.":";
if($smp2 < 10) $prog_str_2 .= "0";
$prog_str_2 .= $smp2." si face temperatura de la pornire + 0.5 &deg;C";

$prog_str_3 ="P3 - porneste la ".$shp3.":";
if($smp3 < 10) $prog_str_3 .= "0";
$prog_str_3 .= $smp3." si face temperatura de la pornire + 0.5 &deg;C";

$prog_str_4 = "P4 - porneste acum si face temperatura de la pornire + 0.5 &deg;C";
?>
<!DOCTYPE html>
<html>
 <head>
  <title>CLLS - Programare</title>
  <style>
   html { height:100%; min-height:100%; width:100%; min-width:100%; }
   body { font-size:large; }
   input { font-size:large; }
   table { border-collapse:collapse; border-style:solid; }
   td { padding:5px; border-style:solid; border-width: thin; }
  </style>
 </head>
 <body>
  <table>

   <form action='/programming_save.php' method='post'>
   <input type='hidden' name='t_loc' value='<?php echo $t_loc; ?>'>
   <!-- room: <?php echo $room; ?> -->
   <tr>
    <td rowspan=5><?php echo $room; ?></td>

   <!-- programm: 0 -->
    <td align='left'>
     <label style='padding:5px;'><input type='radio' name='programming_<?php echo $room_idx; ?>' value='0'<?php if($p === 0) echo " checked"; ?>><?php echo $prog_str_0; ?></label>
    </td>
   </tr>

   <!-- programm: 1 -->
   <tr>
    <td align='left'>
     <label style='padding:5px;'><input type='radio' name='programming_<?php echo $room_idx; ?>' value='1'<?php if($p === 1) echo " checked"; ?>><?php echo $prog_str_1; ?></label><br>
     Start
     <input type='text' style='text-align:center;' name='start_hour_p1_<?php echo $room_idx; ?>' maxlength='2' size='1' value='<?php echo $shp1; ?>'>:
     <input type='text' style='text-align:center;' name='start_minute_p1_<?php echo $room_idx; ?>' maxlength='2' size='1' value='<?php if($smp1 < 10) echo "0"; echo $smp1; ?>'>
     Stop
     <input type='text' style='text-align:center;' name='stop_hour_p1_<?php echo $room_idx; ?>' maxlength='2' size='1' value='<?php echo $ehp1; ?>'>:
     <input type='text' style='text-align:center;' name='stop_minute_p1_<?php echo $room_idx; ?>' maxlength='2' size='1' value='<?php if($emp1 < 10) echo "0"; echo $emp1; ?>'>
     &deg;C
     <input type='text' style='text-align:center;' name='target_temperature_p1_<?php echo $room_idx; ?>' maxlength='5' size='1' value='<?php echo $tp1_int."."; if($tp1_zec < 10) echo "0"; echo $tp1_zec; ?>'>
    </td>
   </tr>

   <!-- programm: 2 -->
   <tr>
    <td align='left'>
     <label style='padding:5px;'><input type='radio' name='programming_<?php echo $room_idx; ?>' value='2'<?php if($p === 2) echo " checked"; ?>><?php echo $prog_str_2; ?></label><br>
     Start
     <input type='text' style='text-align:center;' name='start_hour_p2_<?php echo $room_idx; ?>' maxlength='2' size='1' value='<?php echo $shp2; ?>'>:
     <input type='text' style='text-align:center;' name='start_minute_p2_<?php echo $room_idx; ?>' maxlength='2' size='1' value='<?php if($smp2 < 10) echo "0"; echo $smp2; ?>'>
     Urmatorul program: 
     <label><input type='radio' name='next_programm_p2_<?php echo $room_idx; ?>' value='0'<?php if($npp2 === 0) echo " checked"; ?>>P0</label>
     <label><input type='radio' name='next_programm_p2_<?php echo $room_idx; ?>' value='1'<?php if($npp2 === 1) echo " checked"; ?>>P1</label>
     <label><input type='radio' name='next_programm_p2_<?php echo $room_idx; ?>' value='2'<?php if($npp2 === 2) echo " checked"; ?>>P2</label>
     <label><input type='radio' name='next_programm_p2_<?php echo $room_idx; ?>' value='3'<?php if($npp2 === 3) echo " checked"; ?>>P3</label>
    </td>
   </tr>

   <!-- programm: 3 -->
   <tr>
    <td align='left'>
     <label style='padding:5px;'><input type='radio' name='programming_<?php echo $room_idx; ?>' value='3'<?php if($p === 3) echo " checked"; ?>><?php echo $prog_str_3; ?></label><br>
     Start   <input type='text' style='text-align:center;' name='start_hour_p3_<?php echo $room_idx; ?>' maxlength='2' size='1' value='<?php echo $shp3; ?>'>:
     <input type='text' style='text-align:center;' name='start_minute_p3_<?php echo $room_idx; ?>' maxlength='2' size='1' value='<?php if($smp3 < 10) echo "0"; echo $smp3; ?>'>
     Urmatorul program:
     <label><input type='radio' name='next_programm_p3_<?php echo $room_idx; ?>' value='0'<?php if($npp3 === 0) echo " checked"; ?>>P0</label>
     <label><input type='radio' name='next_programm_p3_<?php echo $room_idx; ?>' value='1'<?php if($npp3 === 1) echo " checked"; ?>>P1</label>
     <label><input type='radio' name='next_programm_p3_<?php echo $room_idx; ?>' value='2'<?php if($npp3 === 2) echo " checked"; ?>>P2</label>
     <label><input type='radio' name='next_programm_p3_<?php echo $room_idx; ?>' value='3'<?php if($npp3 === 3) echo " checked"; ?>>P3</label>
    </td>
   </tr>

   <!-- programm: 4 -->
   <tr>
    <td>
     <label style='padding:5px;'><input type='radio' name='programming_<?php echo $room_idx; ?>' value='4'<?php if($p === 4) echo " checked"; ?>><?php echo $prog_str_4; ?></label><br>
     Urmatorul program:
     <label><input type='radio' name='next_programm_p4_<?php echo $room_idx; ?>' value='0'<?php if($npp4 === 0) echo " checked"; ?>>P0</label>
     <label><input type='radio' name='next_programm_p4_<?php echo $room_idx; ?>' value='1'<?php if($npp4 === 1) echo " checked"; ?>>P1</label>
     <label><input type='radio' name='next_programm_p4_<?php echo $room_idx; ?>' value='2'<?php if($npp4 === 2) echo " checked"; ?>>P2</label>
     <label><input type='radio' name='next_programm_p4_<?php echo $room_idx; ?>' value='3'<?php if($npp4 === 3) echo " checked"; ?>>P3</label>
    </td>
   </tr>

   <tr>
    <td colspan=4 align='center'>
     <input type='submit' value='Salvare'>
    </td>
   </tr>
   </form>
   <tr>
    <td colspan=4 align='center'>
     <form action='.' method='get'><input type='submit' value='Inapoi'></form>
    </td>
   </tr>
  </table>
 </body>
</html>

