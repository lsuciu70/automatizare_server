<?php
include_once "time_helper.php";
function day_history($day, &$result = NULL, $read_sus = TRUE, $read_jos = TRUE)
{
  if (! $read_sus && ! $read_jos)
  {
    echo "sus $ jos === FALSE".PHP_EOL;
    return $result;
  }
  if ($result === NULL)
  {
    $result = array();
  }
  if (! is_array ( $result ))
  {
    echo "result is not array".PHP_EOL;
    return $result;
  }
  
  if (! isset($day) || $day === NULL)
    $day = strftime("%Y.%m.%d");
  $day = strip_prepend_zero($day);
  if ($read_sus)
  {
    $file_name = "data/sus_" . $day . ".txt";
    save_day_history ( $file_name, $result, 0 );
  }
  if ($read_jos)
  {
    $file_name = "data/jos_" . $day . ".txt";
    save_day_history ( $file_name, $result, 8 );
  }
  return $result;
}
function file_to_double_array(&$file_name, $reverse = FALSE)
{
  $file = NULL;
  if (! is_file ( $file_name ))
  {
    echo "file not exist: " . $file_name . "\n";
    return NULL;
  }
  $file = new SplFileObject ( $file_name );
  if (! $file->isReadable ())
  {
    echo "file not exist: " . $file_name . "\n";
    return NULL;
  }
  
  $vn = array (
      "hm" => array (),
      "c1" => array (),
      "c1_r" => array (),
      "c2" => array (),
      "c2_r" => array (),
      "c3" => array (),
      "c3_r" => array (),
      "c4" => array (),
      "c4_r" => array () 
  );
  
  // file exist
  $lines = array ();
  if ($reverse)
  {
    $file->seek ( $file->getSize () );
    $curr_line = $file->key ();
    while ( $curr_line >= 0 )
    {
      $file->seek ( $curr_line );
      $line = $file->current ();
      save_line_to_double_array ( $vn, $line );
      -- $curr_line;
    }
  }
  else
  {
    while ( ! $file->eof () )
    {
      // Echo one line from the file.
      $line = $file->fgets ();
      save_line_to_double_array ( $vn, $line );
    }
  }
  return $vn;
}
function file_to_array(&$file_name, $reverse = FALSE)
{
  if (! is_file ( $file_name ))
  {
    echo "file not exist: " . $file_name . "\n";
    return NULL;
  }
  $file = new SplFileObject ( $file_name );
  if (! $file->isReadable ())
  {
    echo "file not exist: " . $file_name . "\n";
    return NULL;
  }
  
  $vn = array ();
  
  // file exist
  $lines = array ();
  if ($reverse)
  {
    $file->seek ( $file->getSize () );
    $curr_line = $file->key ();
    while ( $curr_line >= 0 )
    {
      $file->seek ( $curr_line );
      $line = $file->current ();
      if (save_line_to_array_v0 ( $vn, $line ) === TRUE)
        break;
      -- $curr_line;
    }
  }
  else
  {
    while ( ! $file->eof () )
    {
      // Echo one line from the file.
      $line = $file->fgets ();
      if (save_line_to_array_v0 ( $vn, $line ) === TRUE)
        break;
    }
  }
  return $vn;
}
function save_line_to_double_array(&$vn, &$line)
{
  if ($line == NULL || strlen ( trim ( $line ) ) <= 0)
    return FALSE;
  sscanf ( $line, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_c1, $t_c1_r, $t_c2, $t_c2_r, $t_c3, $t_c3_r, $t_c4, $t_c4_r, $ts_dt );
  $vn ["hm"] [] = $ts_dt;
  $vn ["c1"] [] = $t_c1;
  $vn ["c1_r"] [] = $t_c1_r;
  $vn ["c2"] [] = $t_c2;
  $vn ["c2_r"] [] = $t_c2_r;
  $vn ["c3"] [] = $t_c3;
  $vn ["c3_r"] [] = $t_c3_r;
  $vn ["c4"] [] = $t_c4;
  $vn ["c4_r"] [] = $t_c4_r;
  return TRUE;
}
function save_line_to_array_v0(&$vn, &$line)
{
  if ($line == NULL || strlen ( trim ( $line ) ) <= 0)
    return FALSE;
  sscanf ( $line, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_c1, $t_c1_r, $t_c2, $t_c2_r, $t_c3, $t_c3_r, $t_c4, $t_c4_r, $ts_dt );
  if (strlen ( trim ( $ts_dt ) ) <= 0)
    return FALSE;
  $vn [8] = $ts_dt;
  $vn [0] = $t_c1;
  $vn [1] = $t_c1_r;
  $vn [2] = $t_c2;
  $vn [3] = $t_c2_r;
  $vn [4] = $t_c3;
  $vn [5] = $t_c3_r;
  $vn [6] = $t_c4;
  $vn [7] = $t_c4_r;
  return TRUE;
}
function save_day_history($file_name, &$result, $index)
{
  if (! is_file ( $file_name ))
  {
//     echo "file not exist: " . $file_name . "\n";
    return $result;
  }
  $file = new SplFileObject ( $file_name );
  if (! $file->isReadable ())
  {
//     echo "file not readable: " . $file_name . "\n";
    return $result;
  }
  $lines = array ();
  while ( ! $file->eof () )
  {
    // Echo one line from the file.
    $line = $file->fgets ();
    save_line_to_array ( $result, $line, $index );
  }
  return $result;
}
function save_line_to_array(&$result, &$line, $index)
{
  if ($line == NULL || strlen ( trim ( $line ) ) <= 0)
    return FALSE;
  sscanf ( $line, "%d,%d,%d,%d,%d,%d,%d,%d,%s", $t_c1, $t_c1_r, $t_c2, $t_c2_r, $t_c3, $t_c3_r, $t_c4, $t_c4_r, $ts_dt );
  if (strlen ( trim ( $ts_dt ) ) <= 0)
    return FALSE;
  if (strlen ( trim ( $ts_dt ) ) > 11)
  {
    sscanf($ts_dt, "%d.%d.%d_%d:%d:%d", $blah, $blah, $blah, $start_h, $start_m, $blah);
    $ts_dt = sprintf("%02d:%02d", $start_h, $start_m);
  }
  if (! isset ( $result [$ts_dt] ))
    $result [$ts_dt] = array ();
  $result [$ts_dt] [$index ++] = $t_c1;
  $result [$ts_dt] [$index ++] = $t_c1_r;
  $result [$ts_dt] [$index ++] = $t_c2;
  $result [$ts_dt] [$index ++] = $t_c2_r;
  $result [$ts_dt] [$index ++] = $t_c3;
  $result [$ts_dt] [$index ++] = $t_c3_r;
  $result [$ts_dt] [$index ++] = $t_c4;
  $result [$ts_dt] [$index ++] = $t_c4_r;
}
?>
