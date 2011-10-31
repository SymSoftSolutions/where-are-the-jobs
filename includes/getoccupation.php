<?php
require_once("../php_mysql_class/config.inc.php");
require_once("../php_mysql_class/Database.class.php");
require_once("parameter_validator.php");

$db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->connect();

$_GET['occupation_partial_string'] = $db->escape($_GET['occupation_partial_string']);
$_GET['og'] = $db->escape($_GET['og']);

if(isset($_GET['occupation_partial_string']) && !empty($_GET['occupation_partial_string'])) {
  if(isset($_GET['og']) && !empty($_GET['og']) && parameter_validator($_GET['og'])) { 
    $sql = "select occupation_code, occupation_name from occupation, occugroup where left(occugroup_code, 2) = left(occupation_code, 2) and occugroup_code = ".$_GET['og']." and occupation_name LIKE '%".$_GET['occupation_partial_string']."%' and occupation_code != 000000 order by occupation_name";
  } else {
    $sql = "select occupation_code, occupation_name from occupation where occupation_name LIKE '%".$_GET['occupation_partial_string']."%' and occupation_code != 000000 order by occupation_name";
  }
} else {
  if(isset($_GET['og']) && !empty($_GET['og']) && parameter_validator($_GET['og'])) {
    $sql = "select occupation_code, occupation_name from occupation, occugroup where left(occugroup_code, 2) = left(occupation_code, 2) and occugroup_code = ".$_GET['og']." and occupation_code != 000000 order by occupation_name";
  } else {
    $sql = "select occupation_code, occupation_name from occupation where occupation_code != 000000 order by occupation_name";
  }
}

$occupations = "";
$occupations_list = array();
$rows = $db->query($sql);
while ($record = $db->fetch_array($rows)) {
  if(isset($_GET['occupation']) && !empty($_GET['occupation'])) {
    if($record['occupation_code'] == $_GET['occupation']) {
      $occupations .= "<li class=\"selected\" value='".$record['occupation_code']."' ><span class=\"close\">[x]</span>".$record['occupation_name']."</li>";
    } else {
      $occupations .= "<li value='".$record['occupation_code']."' >".$record['occupation_name']."</li>";
    }
  } else {
    $occupations .= "<li value='".$record['occupation_code']."' >".$record['occupation_name']."</li>";
  }
  $occupation_code = $record['occupation_code'];
  $occupations_list[$occupation_code] = $record['occupation_name'];
}

$db->close();

echo $occupations;
?>
