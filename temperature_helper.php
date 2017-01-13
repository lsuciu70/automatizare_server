<?php
function centi_to_string($temp)
{
  return sprintf ( "%d.%02d", ($temp - ($temp % 100)) / 100, $temp % 100 );
}
function round_to_upper_50($temp)
{
  $temp_dec = $temp % 100;
  $temp_int = ($temp - $temp_dec) / 100;
  if ($temp_dec <= 50)
    $temp_dec = 50;
  else
  {
    $temp_dec = 0;
    $temp_int += 1;
  }
  return $temp_int * 100 + $temp_dec;
}
function round_to_lower_50($temp)
{
  $temp_dec = $temp % 100;
  $temp_int = ($temp - $temp_dec) / 100;
  if ($temp_dec >= 50)
    $temp_dec = 50;
  else
    $temp_dec = 0;
  return $temp_int * 100 + $temp_dec;
}
?>