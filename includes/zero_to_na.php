<?php
function zero_to_na($data, $type){
  if($data == '0' || $data == DB_DASH ){
    $data = "N/A";
  } else {
    $data =  number_format($data);
  }
  if($type == "salary"){
    if($data == "N/A") return $data;
    return '$'.$data;
  }
  if($type == ""){
    return $data;
  }
}

?>
