<?php

function get_secondary_data($sql){
  require_once("php_mysql_class/config.inc.php");
  require_once("php_mysql_class/Database.class.php");
  require_once("zero_to_na.php");

  $db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
  $db->connect();
  $rows = $db->query($sql);
  $industry_data = array();
  $output = array();
  $i = 0;
  while($record = $db->fetch_array($rows)){
    $industry_data[$i]['name'] = $record['industry_name'];
    $industry_data[$i]['annual_mean'] = round($record['annual_mean']);
    $industry_data[$i]['employment'] = round($record['employment']);
    $i++;
  }
  $i = 1;
  $output['secondary']['heading'] .= "<h3>Top industries across the nation </h3>";
  foreach($industry_data as $value ) {
    if($i <= DATA_MAX_TO_SHOW ):
      $industry_name = $value['name'];
      $employment_value = zero_to_na($value['employment'], "");
      $annual_mean = zero_to_na((double)$value['annual_mean'], "salary");
      $output['secondary']['content'] .= "<tr><td class=\"first\"><span class=\"heading_style\">{$i}.</span> {$industry_name}</td><td class=\"second\">{$annual_mean}</td><td class=\"third\">{$employment_value}</td></tr>";
    endif;
    $i++;
  }
  
  return $output;
}

?>
