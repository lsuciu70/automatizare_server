<?php 
// in case of a POST request
if ($_SERVER ['REQUEST_METHOD'] === 'POST')
{
  $ts_full = strftime ( "%Y.%m.%d_%H:%M:%S" );
  sscanf ( $ts_full, "%d.%d.%d_%d:%d:%d", $ts_Y, $ts_M, $ts_D, $ts_h, $ts_m, $ts_s );

  $dbox_file = "log_" . $ts_Y . "." . $ts_M . "." . $ts_D . ".txt";
  $log_file = "data/" . $dbox_file;
  if(isset($_POST["t_log"]))
  {
    file_put_contents($log_file, (htmlspecialchars($_POST["t_log"]) . PHP_EOL), FILE_APPEND | LOCK_EX);
    // save to Dropbox
    include "dbox.php";
  
    $curl_cmd = 'curl -k -X POST https://content.dropboxapi.com/2/files/upload '.
        '--header "Authorization: Bearer '.$dbox_k.'" '.
        '--header "Dropbox-API-Arg: {\"path\": \"/'.$dbox_file.'\", \"mode\": \"overwrite\"}" '.
        '--header "Content-Type: application/octet-stream" '.
        '--data-binary @'.$log_file;
    
    if(system($curl_cmd." &", $retval) === FALSE)
      error_log($retval);
  }
}
?>
