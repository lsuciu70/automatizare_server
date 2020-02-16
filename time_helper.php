<?php
function is_today($day)
{
  if ( ! isset($day) || $day === NULL)
    return FALSE;
  sscanf($day, "%d.%d.%d", $day_Y, $day_M, $day_D);
  $now = strftime("%Y.%m.%d");
  sscanf($now, "%d.%d.%d", $now_Y, $now_M, $now_D);
  return $day_Y === $now_Y && $day_M === $now_M && $day_D === $now_D;
}

function strip_prepend_zero($day)
{
  if ( ! isset($day) || $day === NULL)
    return NULL;
  sscanf($day, "%d.%d.%d", $day_Y, $day_M, $day_D);
  return sprintf("%d.%d.%d", $day_Y, $day_M, $day_D);
}

function add_prepend_zero($day)
{
  if ( ! isset($day) || $day === NULL)
    return NULL;
  sscanf($day, "%d.%d.%d", $day_Y, $day_M, $day_D);
  return sprintf("%d.%02d.%02d", $day_Y, $day_M, $day_D);
}

/**
 * Returns the next day, the day after given one.
 * 
 * @param string $day as yyyy.[m]m.[d]d
 * @param TRUE|FALSE $prepend_zero, default FALSE
 * @return string the next day as yyyy.mm.dd if $prepend_zero is TRUE, yyyy.m.d otherwise.
 */
function the_day_after($day, $prepend_zero = FALSE)
{
  if ( ! isset($day) || $day === NULL)
    return NULL;
  sscanf($day, "%d.%d.%d", $day_Y, $day_M, $day_D);
  $day_std = sprintf("%d/%d/%d", $day_Y, $day_M, $day_D);
  
  $day_after = date('Y.m.d', strtotime('+1 day', strtotime($day_std)));
  if($prepend_zero)
    return $day_after;
  sscanf($day_after, "%d.%d.%d", $day_after_Y, $day_after_M, $day_after_D);
  $day_after = sprintf("%d.%d.%d", $day_after_Y, $day_after_M, $day_after_D);
  return $day_after;
}


/**
 * Returns the previous day, the day before given one.
 *
 * @param string $day as yyyy.[m]m.[d]d
 * @param TRUE|FALSE $prepend_zero, default FALSE
 * @return string the next day as yyyy.mm.dd if $prepend_zero is TRUE, yyyy.m.d otherwise.
 */
function the_day_before($day, $prepend_zero = FALSE)
{
  if ( ! isset($day) || $day === NULL)
    return NULL;
  sscanf($day, "%d.%d.%d", $day_Y, $day_M, $day_D);
  $day_std = sprintf("%d/%d/%d", $day_Y, $day_M, $day_D);

  $day_before = date('Y.m.d', strtotime('-1 day', strtotime($day_std)));
  if($prepend_zero)
    return $day_before;
    sscanf($day_before, "%d.%d.%d", $day_before_Y, $day_before_M, $day_before_D);
    $day_before = sprintf("%d.%d.%d", $day_before_Y, $day_before_M, $day_before_D);
    return $day_before;
}
?>