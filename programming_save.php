<?php
header('Location: info.php');
// in case of a POST request
if ($_SERVER ['REQUEST_METHOD'] !== 'POST')
{
  exit;
}

$post_topic = "";
$post_data = "";
foreach ($_POST as $key => $value)
{
  if ($key == "room") {
    $post_topic = "heating/prog/".$value;
    continue;
  }
  if ($key == "tt1") {
    $value = $value * 100;
  }
  if ($key == "p") {
    $post_data = ',"'.$key.'":'.$value.$post_data;
  } else {
    $post_data .= ',"'.$key.'":'.$value;
  }
}
if(strlen($post_data) > 0) {
  $post_data = substr($post_data, 1);
}

$post_data = '{'.$post_data.'}';
// echo ($post_topic.'<br>'.$post_data);
// die();

require("php/phpMQTT.php");

$server = "192.168.100.60";       // change if necessary
$port = 1883;                     // change if necessary
$username = "";                   // set your username
$password = "";                   // set your password
$client_id = "programming_save";  // make sure this is unique for connecting to sever - you could use uniqid()
$mqtt = new phpMQTT($server, $port, $client_id);
if ($mqtt->connect(true, NULL, $username, $password)) {
    $mqtt->publish($post_topic, $post_data, 0);
    $mqtt->close();
}
?>
