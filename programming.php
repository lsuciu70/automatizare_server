<?php
// authentication
if (!isset($_SERVER['PHP_AUTH_USER']))
{
  header('WWW-Authenticate: Basic realm="CLLS Programming"');
  header('HTTP/1.0 401 Unauthorized');
  header( "refresh:5;url=info.php" );
  die ("401 - Not authorized; authentication required!");
}

$valid_passwords = array();
$users_file = fopen("data/.users", "r");
while (($line = fgets($users_file)) !== FALSE)
{
  sscanf($line, "%s = %s", $user, $passwd);
  $valid_passwords[$user] = $passwd;
}
$valid_users = array_keys($valid_passwords);

$user = $_SERVER['PHP_AUTH_USER'];
$pass = crypt($_SERVER['PHP_AUTH_PW'], "tra-la-la");

$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

if (!$validated)
{
  header('WWW-Authenticate: Basic realm="CLLS Programming"');
  header('HTTP/1.0 401 Unauthorized');
  header( "refresh:5;url=info.php" );
  die ("401 - Not authorized; wrong user or password!");
}

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

// variables
$room = "unknown";
$room_name = array("Bucatarie", "Living", "Birou", "Baie parter", "Camera Luca", "Dormitor matrimonial", "Dormitor oaspeti", "Baie etaj");
$room_short = array("bu", "li", "bi", "bp", "dl", "dm", "do", "be");

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
  "target_temperature_p2",
  "start_hour_p3",
  "start_minute_p3",
  "next_programm_p3",
  "target_temperature_p3",
  "next_programm_p4",
  "target_temperature_p4",
    "start_hour_p5",
    "start_minute_p5",
    "stop_hour_p5",
    "stop_minute_p5",
    "next_programm_p5",
    "start_hour_p6",
    "start_minute_p6",
    "stop_hour_p6",
    "stop_minute_p6",
    "next_programm_p6",
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
    $vn[16] => array(5, 5, 5, 5, 5, 5, 5, 5), // start_hour_p5
    $vn[17] => array(0, 0, 0, 0, 0, 0, 0, 0), // start_minute_p5
    $vn[18] => array(5, 5, 5, 5, 5, 5, 5, 5), // stop_hour_p5
    $vn[19] => array(59, 59, 59, 59, 59, 59, 59, 59), // stop_minute_p5
    $vn[20] => array(6, 6, 6, 6, 6, 6, 6, 6), // next_programm_p5
    $vn[21] => array(19, 19, 19, 19, 19, 19, 19, 19), // start_hour_p6
    $vn[22] => array(0, 0, 0, 0, 0, 0, 0, 0), // start_minute_p6
    $vn[23] => array(19, 19, 19, 19, 19, 19, 19, 19), // stop_hour_p6
    $vn[24] => array(59, 59, 59, 59, 59, 59, 59, 59), // stop_minute_p6
    $vn[25] => array(5, 5, 5, 5, 5, 5, 5, 5), // next_programm_p6
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
$tp2 = $vn_val["target_temperature_p2"][$index];
$tp2_zec = $tp2 % 100;
$tp2_int = intval(($tp2 - $tp2_zec) / 100);
$shp3 = $vn_val["start_hour_p3"][$index];
$smp3 = $vn_val["start_minute_p3"][$index];
$npp3 = $vn_val["next_programm_p3"][$index];
$tp3 = $vn_val["target_temperature_p3"][$index];
$tp3_zec = $tp3 % 100;
$tp3_int = intval(($tp3 - $tp3_zec) / 100);
$npp4 = $vn_val["next_programm_p4"][$index];
$tp4 = $vn_val["target_temperature_p4"][$index];
$tp4_zec = $tp4 % 100;
$tp4_int = intval(($tp4 - $tp4_zec) / 100);

$shp5 = $vn_val["start_hour_p5"][$index];
$smp5 = $vn_val["start_minute_p5"][$index];
$ehp5 = $vn_val["stop_hour_p5"][$index];
$emp5 = $vn_val["stop_minute_p5"][$index];
$npp5 = $vn_val["next_programm_p5"][$index];

$shp6 = $vn_val["start_hour_p6"][$index];
$smp6 = $vn_val["start_minute_p6"][$index];
$ehp6 = $vn_val["stop_hour_p6"][$index];
$emp6 = $vn_val["stop_minute_p6"][$index];
$npp6 = $vn_val["next_programm_p6"][$index];

$prog_str_0 = "P0 - oprit";

$prog_str_1 = "P1 - merge intre ".$shp1.":";
if($smp1 < 10) $prog_str_1 .= "0";
$prog_str_1 .= $smp1." si ".$ehp1.":";
if($emp1 < 10) $prog_str_1 .= "0";
$prog_str_1 .= $emp1;
if($shp1 > $ehp1 || ($shp1 === $ehp1 && $smp1 >= $emp1)) $prog_str_1 .= " (ziua urmatoare)";
$prog_str_1 .= " si face ".$tp1_int.".";
if($tp1_zec < 10) $prog_str_1 .= "0";
$prog_str_1 .= $tp1_zec." &deg;C";

$prog_str_2 ="P2 - porneste la ".$shp2.":";
if($smp2 < 10) $prog_str_2 .= "0";
$prog_str_2 .= $smp2;
$prog_str_2 .= " si face ";
if($tp2 !== 0)
{
  $prog_str_2 .= $tp2_int.".";
  if($tp2_zec < 10) $prog_str_2 .= "0";
  $prog_str_2 .= $tp2_zec." &deg;C";
}
else $prog_str_2 .= "temperatura de la pornire + 0.3 &deg;C";

$prog_str_3 ="P3 - porneste la ".$shp3.":";
if($smp3 < 10) $prog_str_3 .= "0";
$prog_str_3 .= $smp3;
$prog_str_3 .= " si face ";
if($tp3 !== 0)
{
  $prog_str_3 .= $tp3_int.".";
  if($tp3_zec < 10) $prog_str_3 .= "0";
  $prog_str_3 .= $tp3_zec." &deg;C";
}
else $prog_str_3 .= "temperatura de la pornire + 0.3 &deg;C";

$prog_str_4 = "P4 - porneste acum";
$prog_str_4 .= " si face ";
if($tp4 !== 0)
{
  $prog_str_4 .= $tp4_int.".";
  if($tp4_zec < 10) $prog_str_4 .= "0";
  $prog_str_4 .= $tp4_zec." &deg;C";
}
else $prog_str_4 .= "temperatura de la pornire + 0.3 &deg;C";

$prog_str_5 = "P5 - merge intre ".$shp5.":";
if($smp5 < 10) $prog_str_5 .= "0";
$prog_str_5 .= $smp5." si ".$ehp5.":";
if($emp5 < 10) $prog_str_5 .= "0";
$prog_str_5 .= $emp5;
if($shp5 > $ehp5 || ($shp5 === $ehp5 && $smp5 >= $emp5)) $prog_str_5 .= " (ziua urmatoare)";

$prog_str_6 = "P6 - merge intre ".$shp6.":";
if($smp6 < 10) $prog_str_6 .= "0";
$prog_str_6 .= $smp6." si ".$ehp6.":";
if($emp6 < 10) $prog_str_6 .= "0";
$prog_str_6 .= $emp6;
if($shp6 > $ehp6 || ($shp6 === $ehp6 && $smp6 >= $emp6)) $prog_str_6 .= " (ziua urmatoare)";
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
   <input type='hidden' name='room' value='<?php echo $room_short[$index]; ?>'>
   <!-- room: <?php echo $room; ?> -->
   <tr>
    <td rowspan=7><?php echo $room; ?></td>
<?php 
// {"p":4,"ih1":17,"im1":00,"sh1":7,"sm1":00,"tt1":2200,"ih2":3,"im2":30,"np2":3,"ih3":18,"im3":00,"np3":2,"np4":3}
// {"p":2,"ih1":17,"im1":00,"sh1":7,"sm1":01,"tt1":2200,"ih2":3,"im2":30,"np2":3,"ih3":18,"im3":00,"np3":2,"np4":3}
// {"p":2,"ih1":17,"im1":00,"sh1":7,"sm1":01,"tt1":2200,"ih2":3,"im2":30,"np2":3,"ih3":18,"im3":00,"np3":2,"np4":3}
?>
   <!-- programm: 0 -->
    <td align='left'>
     <label style='padding:5px;'><input type='radio' name='p' value='0'<?php if($p === 0) echo " checked"; ?>><?php echo $prog_str_0; ?></label>
    </td>
   </tr>

   <!-- programm: 1 -->
   <tr>
    <td align='left'>
     <label style='padding:5px;'><input type='radio' name='p' value='1'<?php if($p === 1) echo " checked"; ?>><?php echo $prog_str_1; ?></label>
     <br>Start
     <input type='text' style='text-align:center;' name='ih1' maxlength='2' size='1' value='<?php echo $shp1; ?>'>:
     <input type='text' style='text-align:center;' name='im1' maxlength='2' size='1' value='<?php if($smp1 < 10) echo "0"; echo $smp1; ?>'>
     Stop
     <input type='text' style='text-align:center;' name='sh1' maxlength='2' size='1' value='<?php echo $ehp1; ?>'>:
     <input type='text' style='text-align:center;' name='sm1' maxlength='2' size='1' value='<?php if($emp1 < 10) echo "0"; echo $emp1; ?>'>
     &deg;C
     <input type='text' style='text-align:center;' name='tt1' maxlength='5' size='1' value='<?php echo $tp1_int."."; if($tp1_zec < 10) echo "0"; echo $tp1_zec; ?>'>
    </td>
   </tr>

   <!-- programm: 2 -->
   <tr>
    <td align='left'>
     <label style='padding:5px;'><input type='radio' name='p' value='2'<?php if($p === 2) echo " checked"; ?>><?php echo $prog_str_2; ?></label>
     <br>Start
     <input type='text' style='text-align:center;' name='ih2' maxlength='2' size='1' value='<?php echo $shp2; ?>'>:
     <input type='text' style='text-align:center;' name='im2' maxlength='2' size='1' value='<?php if($smp2 < 10) echo "0"; echo $smp2; ?>'>
     <br>Urmatorul program: 
     <label><input type='radio' name='np2' value='0'<?php if($npp2 === 0) echo " checked"; ?>>P0</label>
     <label><input type='radio' name='np2' value='1'<?php if($npp2 === 1) echo " checked"; ?>>P1</label>
     <label><input type='radio' name='np2' value='2'<?php if($npp2 === 2) echo " checked"; ?>>P2</label>
     <label><input type='radio' name='np2' value='3'<?php if($npp2 === 3) echo " checked"; ?>>P3</label>
     <label><input type='radio' name='np2' value='5'<?php if($npp2 === 5) echo " checked"; ?>>P5</label>
     <label><input type='radio' name='np2' value='6'<?php if($npp2 === 6) echo " checked"; ?>>P6</label>
    </td>
   </tr>

   <!-- programm: 3 -->
   <tr>
    <td align='left'>
     <label style='padding:5px;'><input type='radio' name='p' value='3'<?php if($p === 3) echo " checked"; ?>><?php echo $prog_str_3; ?></label>
     <br>Start   <input type='text' style='text-align:center;' name='ih3' maxlength='2' size='1' value='<?php echo $shp3; ?>'>:
     <input type='text' style='text-align:center;' name='im3' maxlength='2' size='1' value='<?php if($smp3 < 10) echo "0"; echo $smp3; ?>'>
     <br>Urmatorul program:
     <label><input type='radio' name='np3' value='0'<?php if($npp3 === 0) echo " checked"; ?>>P0</label>
     <label><input type='radio' name='np3' value='1'<?php if($npp3 === 1) echo " checked"; ?>>P1</label>
     <label><input type='radio' name='np3' value='2'<?php if($npp3 === 2) echo " checked"; ?>>P2</label>
     <label><input type='radio' name='np3' value='3'<?php if($npp3 === 3) echo " checked"; ?>>P3</label>
     <label><input type='radio' name='np3' value='5'<?php if($npp3 === 5) echo " checked"; ?>>P5</label>
     <label><input type='radio' name='np3' value='6'<?php if($npp3 === 6) echo " checked"; ?>>P6</label>
    </td>
   </tr>

   <!-- programm: 4 -->
   <tr>
    <td>
     <label style='padding:5px;'><input type='radio' name='p' value='4'<?php if($p === 4) echo " checked"; ?>><?php echo $prog_str_4; ?></label>
     <br>Urmatorul program:
     <label><input type='radio' name='np4' value='0'<?php if($npp4 === 0) echo " checked"; ?>>P0</label>
     <label><input type='radio' name='np4' value='1'<?php if($npp4 === 1) echo " checked"; ?>>P1</label>
     <label><input type='radio' name='np4' value='2'<?php if($npp4 === 2) echo " checked"; ?>>P2</label>
     <label><input type='radio' name='np4' value='3'<?php if($npp4 === 3) echo " checked"; ?>>P3</label>
     <label><input type='radio' name='np4' value='5'<?php if($npp4 === 5) echo " checked"; ?>>P5</label>
     <label><input type='radio' name='np4' value='6'<?php if($npp4 === 6) echo " checked"; ?>>P6</label>
    </td>
   </tr>

   <!-- programm: 5 -->
   <tr>
    <td align='left'>
     <label style='padding:5px;'><input type='radio' name='p' value='5'<?php if($p === 5) echo " checked"; ?>><?php echo $prog_str_5; ?></label>
     <br>Start
     <input type='text' style='text-align:center;' name='ih5' maxlength='2' size='1' value='<?php echo $shp5; ?>'>:
     <input type='text' style='text-align:center;' name='im5' maxlength='2' size='1' value='<?php if($smp5 < 10) echo "0"; echo $smp5; ?>'>
     Stop
     <input type='text' style='text-align:center;' name='sh5' maxlength='2' size='1' value='<?php echo $ehp5; ?>'>:
     <input type='text' style='text-align:center;' name='sm5' maxlength='2' size='1' value='<?php if($emp5 < 10) echo "0"; echo $emp5; ?>'>
     <br>Urmatorul program:
     <label><input type='radio' name='np5' value='0'<?php if($npp5 === 0) echo " checked"; ?>>P0</label>
     <label><input type='radio' name='np5' value='1'<?php if($npp5 === 1) echo " checked"; ?>>P1</label>
     <label><input type='radio' name='np5' value='2'<?php if($npp5 === 2) echo " checked"; ?>>P2</label>
     <label><input type='radio' name='np5' value='3'<?php if($npp5 === 3) echo " checked"; ?>>P3</label>
     <label><input type='radio' name='np5' value='5'<?php if($npp5 === 5) echo " checked"; ?>>P5</label>
     <label><input type='radio' name='np5' value='6'<?php if($npp5 === 6) echo " checked"; ?>>P6</label>
    </td>
   </tr>

   <!-- programm: 6 -->
   <tr>
    <td align='left'>
     <label style='padding:5px;'><input type='radio' name='p' value='6'<?php if($p === 6) echo " checked"; ?>><?php echo $prog_str_6; ?></label>
     <br>Start
     <input type='text' style='text-align:center;' name='ih6' maxlength='2' size='1' value='<?php echo $shp6; ?>'>:
     <input type='text' style='text-align:center;' name='im6' maxlength='2' size='1' value='<?php if($smp6 < 10) echo "0"; echo $smp6; ?>'>
     Stop
     <input type='text' style='text-align:center;' name='sh6' maxlength='2' size='1' value='<?php echo $ehp6; ?>'>:
     <input type='text' style='text-align:center;' name='sm6' maxlength='2' size='1' value='<?php if($emp6 < 10) echo "0"; echo $emp6; ?>'>
     <br>Urmatorul program:
     <label><input type='radio' name='np6' value='0'<?php if($npp6 === 0) echo " checked"; ?>>P0</label>
     <label><input type='radio' name='np6' value='1'<?php if($npp6 === 1) echo " checked"; ?>>P1</label>
     <label><input type='radio' name='np6' value='2'<?php if($npp6 === 2) echo " checked"; ?>>P2</label>
     <label><input type='radio' name='np6' value='3'<?php if($npp6 === 3) echo " checked"; ?>>P3</label>
     <label><input type='radio' name='np6' value='5'<?php if($npp6 === 5) echo " checked"; ?>>P5</label>
     <label><input type='radio' name='np6' value='6'<?php if($npp6 === 6) echo " checked"; ?>>P6</label>
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

