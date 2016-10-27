<?php 
// in case of a POST request
if ($_SERVER ['REQUEST_METHOD'] === 'POST')
{
  $ts_full = strftime ( "%Y.%m.%d_%H:%M:%S" );
  sscanf ( $ts_full, "%d.%d.%d_%d:%d:%d", $ts_Y, $ts_M, $ts_D, $ts_h, $ts_m, $ts_s );

  $log_file_name = "data/log_" . $ts_Y . "." . $ts_M . "." . $ts_D . ".txt";;
  if(isset($_POST["t_log"]))
    file_put_contents($log_file_name, (htmlspecialchars($_POST["t_log"]) . PHP_EOL), FILE_APPEND | LOCK_EX);
}
?>
