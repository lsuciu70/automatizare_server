<?php
include_once 'file_helper.php';
include_once 'time_helper.php';
include_once 'temperature_helper.php';

$debug = FALSE;

$day = "";

if (isset($_SERVER['REQUEST_METHOD']) !== FALSE && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["t_date"]) !== FALSE)
  $day = htmlspecialchars($_POST["t_date"]);
else
  $day = strftime("%Y.%m.%d");

// if ($debug)
//   $day = "2016.12.26";

if(isset($_SERVER['REQUEST_URI']) !== FALSE && is_today($day))
{
  $req_uri=$_SERVER['REQUEST_URI'];
  header("Refresh: 150; URL=$req_uri");
}

$day_history_data = day_history(strip_prepend_zero($day));
if(is_today($day))
{
  save_day_history("data/sus_avg.txt", $day_history_data, 0);
  save_day_history("data/jos_avg.txt", $day_history_data, 8);
  save_day_history("data/sus_temp.txt", $day_history_data, 0);
  save_day_history("data/jos_temp.txt", $day_history_data, 8);
}

$y_day = the_day_before($day);
$t_mrow = the_day_after($day);

$sus_yday_file_name = "data/sus_" . $y_day . ".txt";
$sus_tmrow_file_name = "data/sus_" . $t_mrow . ".txt";
$jos_yday_file_name = "data/sus_" . $y_day . ".txt";
$sos_tmrow_file_name = "data/sus_" . $t_mrow . ".txt";
$has_y_day = is_file($sus_yday_file_name) || is_file($sus_yday_file_name);
$has_t_mrow = is_file($sus_tmrow_file_name) || is_file($sus_tmrow_file_name);

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
$room_count = count($room);

include_once 'history_helper_summary.php';
include_once 'history_helper_image.php';

// logs
$log = array();
$log_count = 0;
$log_file_name = "data/log_" . strip_prepend_zero ( $day ) . ".txt";
$is_log_file = is_file($log_file_name);
if($is_log_file)
{
  $log_file = fopen($log_file_name, "r");
  while (($line = fgets($log_file)) !== FALSE)
  {
//     if (strpos($line, $t_loc) !== FALSE)
    {
      $log[$log_count] = $line;
      ++$log_count;
    }
  }
}

echo "<!DOCTYPE html>\n";
echo "<html>\n";
echo " <head>\n";
echo "  <title>CLLS - Istoric</title>\n";
echo "  <style>\n";
echo "   html { height:100%; min-height:100%; width:100%; min-width:100%; }\n";
echo "   body { font-size:large; }\n";
echo "   input { font-size:large; }\n";
echo "   table { border-collapse:collapse; border-style:hidden; }\n";
echo "   th { padding:5px; width:10em; }\n";
echo "   td { padding:5px; width:10em; }\n";
// echo "   th { padding:5px; border-style:solid; border-width:thin; width:10em; }\n";
// echo "   td { padding:5px; border-style:solid; border-width:thin; width:10em; }\n";
echo "  </style>\n";
echo " </head>\n";
echo " <body>\n";
echo $imgmap;
echo "  <table>\n";
echo "   <tr><th align='center' colspan='3'>".$day."</th></tr>\n";
// echo "   <tr><th align='center' style='border-style:hidden;'>".$day."</th></tr>\n";
echo "   <tr>\n";
echo "    <td align='center' colspan='3'>\n";
echo "     <img src='".$image_file_name."' usemap='#historymap'>\n";
echo "    </td>\n";
echo "   </tr>\n";
// echo "  </table>\n";
// echo "  <table>\n";
echo "   <tr>\n";
echo "    <td align='center'>\n";
echo "     <form action='history.php' method='post'>\n";
echo "      <input type='hidden' name='t_date' value='".$y_day."'>\n";
echo "      <input type='submit' value='Istoric ziua precedenta'".(($has_y_day === FALSE) ? " disabled": "").">\n";
echo "     </form>\n";
echo "    </td>\n";
echo "    <td align='center'>\n";
echo "     <form action='info.php' method='get'><input type='submit' value='Inapoi'></form>\n";
echo "    </td>\n";
echo "    <td align='center'>\n";
echo "     <form action='history.php' method='post'>\n";
echo "      <input type='hidden' name='t_date' value='".$t_mrow."'>\n";
echo "      <input type='submit' value='Istoric ziua urmatoare'".(($has_t_mrow === FALSE) ? " disabled": "").">\n";
echo "     </form>\n";
echo "    </td>\n";
echo "   </tr>\n";
// echo "  </table>\n";
echo "   <tr>\n";
echo "    <td align='left' colspan='3'></td>\n";
echo "   </tr>\n";
$i = $all_index;
echo "   <tr>\n";
echo "    <td align='left' colspan='3'>\n";
echo "     ".$room[$i].": ".(($room_sum_minutes[$i] > 0) ? "A mers <b>$room_sum_str[$i]</b>; ":"").$room_str[$i]." (".$room_sum_minutes[$i]." minute)\n";
echo "    </td>\n";
echo "   </tr>\n";
$i = $up_index;
echo "   <tr>\n";
echo "    <td align='left' colspan='3'>\n";
echo "     ".$room[$i].": ".(($room_sum_minutes[$i] > 0) ? "A mers <b>$room_sum_str[$i]</b>; ":"").$room_str[$i]." (".$room_sum_minutes[$i]." minute)\n";
echo "    </td>\n";
echo "   </tr>\n";
$i = $down_index;
echo "   <tr>\n";
echo "    <td align='left' colspan='3'>\n";
echo "     ".$room[$i].": ".(($room_sum_minutes[$i] > 0) ? "A mers <b>$room_sum_str[$i]</b>; ":"").$room_str[$i]." (".$room_sum_minutes[$i]." minute)\n";
echo "    </td>\n";
echo "   </tr>\n";
echo "   <tr>\n";
echo "    <td align='left' colspan='3'></td>\n";
echo "   </tr>\n";
for ($i = 0 ; $i < $up_index ; ++ $i)
{
  if ($i === $up_index / 2)
  {
    echo "   <tr>\n";
    echo "    <td align='left' colspan='3'></td>\n";
    echo "   </tr>\n";
  }
  echo "   <tr>\n";
  echo "    <td align='left' colspan='3'>\n";
  echo "     ".$room[$i].": ".(($room_sum_minutes[$i] > 0) ? "A mers <b>$room_sum_str[$i]</b>; ":"").$room_str[$i]." (".$room_sum_minutes[$i]." minute)\n";
  echo "    </td>\n";
  echo "   </tr>\n";
}
echo "  </table>\n";
// echo "  <br>\n";
// echo "  <p>".$room[$i].": ".(($room_sum_minutes[$i] > 0) ? "A mers <b>$room_sum_str[$i]</b>; ":"").$room_str[$i]."</p>\n";
// $i = $up_index;
// echo "  <p>".$room[$i].": ".(($room_sum_minutes[$i] > 0) ? "A mers <b>$room_sum_str[$i]</b>; ":"").$room_str[$i]."</p>\n";
// $i = $down_index;
// echo "  <p>".$room[$i].": ".(($room_sum_minutes[$i] > 0) ? "A mers <b>$room_sum_str[$i]</b>; ":"").$room_str[$i]."</p>\n";
// echo "  <br>\n";
// for ($i = 0 ; $i < $up_index ; ++ $i)
// {
//   if ($i === $up_index / 2)
//     echo "  <br>\n";
//   echo "  <p>".$room[$i].": ".(($room_sum_minutes[$i] > 0) ? "A mers <b>$room_sum_str[$i]</b>; ":"").$room_str[$i]."</p>\n";
// }
// echo "  <br>\n";

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
echo "\n";
?>
