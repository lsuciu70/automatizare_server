<?php
$users = array(
  "user1" => "pwd1",
  "user2" => "pwd2",
);

$users_file_name = "data/.users";
foreach ($users as $user => $password)
{
  file_put_contents($users_file_name, $user." = ".crypt($password, "tra-la-la").PHP_EOL, FILE_APPEND | LOCK_EX);;
}
?>

