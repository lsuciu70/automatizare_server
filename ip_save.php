<?php 
// in case of a POST request
if ($_SERVER ['REQUEST_METHOD'] !== 'POST')
{
  exit;
}

if(isset($_POST["t_loc"]) === FALSE || isset($_POST["t_ip"]) === FALSE)
{
  error_log("ERROR - Empty data");
  exit("ERROR - Empty data");
}

$t_loc = htmlspecialchars($_POST["t_loc"]);
$t_ip = htmlspecialchars($_POST["t_ip"]);
$ip_file_name = "data/" . $t_loc . "_ip.txt";
// DO NOT write an end line
file_put_contents($ip_file_name, $t_ip, LOCK_EX);
?>
