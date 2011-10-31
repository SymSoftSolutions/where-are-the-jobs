<?php
require_once("../php_mysql_class/config.inc.php");
require_once("../php_mysql_class/Database.class.php");
require_once("parameter_validator.php");

$db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->connect();

$metro_areas = "";
$state_code = "";

$_GET['state'] = $db->escape($_GET['state']);
$_GET['metro_partial_string'] = $db->escape($_GET['metro_partial_string']);

if(isset($_GET['state']) && !empty($_GET['state']) && parameter_validator($_GET['state'])){
  if($_GET['state'] != "00000"){
    $state_code = str_pad($_GET['state'], 7, "0", STR_PAD_LEFT);
    $state_code = substr($state_code, 0, 2);
  }
}


if(isset($_GET['metro_partial_string']) && !empty($_GET['metro_partial_string'])){
  if(isset($state_code) && !empty($state_code)) {
    $sql = "select msa_code, msa_name from statemsa where msa_name LIKE '%".$_GET['metro_partial_string']."%' and state_code = ".$state_code." order by msa_name";
  } else {
    $sql = "select msa_code, msa_name from statemsa where msa_name LIKE '%".$_GET['metro_partial_string']."%' order by msa_name";
  }
} else {
  if(isset($state_code) && !empty($state_code)) {
    $sql = "select msa_code, msa_name from statemsa where state_code = ".$state_code." order by msa_name";
  } else { 
    $sql = "select msa_code, msa_name from statemsa where state_code != '66' and state_code != '72' and state_code != '78'";
  }
}
$rows = $db->query($sql);
while ($record = $db->fetch_array($rows)) {
  if(isset($_GET['metro']) && !empty($_GET['metro'])) {
    if($record['msa_code'] == $_GET['metro']) {
      $metro_areas .= "<li value='".$record['msa_code']."' class=\"selected\"><span class=\"close\">[x]</span>".$record['msa_name']."</li>";
    } else {
      $metro_areas .= "<li value='".$record['msa_code']."'>".$record['msa_name']."</li>";
    }
  } else {
    $metro_areas .= "<li value='".$record['msa_code']."'>".$record['msa_name']."</li>";
  }
}

$db->close();
echo $metro_areas;
?>
