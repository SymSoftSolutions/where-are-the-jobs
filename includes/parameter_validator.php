<?php

/* function to validate if the parameter passed is numeric or not.
 * Most parameters like the occupation group code, occupation code, state code and metro code are numeric in nature.
 */
function parameter_validator($param) {
  $param = trim($param);
  if($param == "") $param = 0;
  if(is_numeric($param)) {
    return true;
  } else {
    return false;
  }
}

?>
