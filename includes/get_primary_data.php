<?php
function get_primary_data($sql) {
  require_once("php_mysql_class/config.inc.php");
  require_once("php_mysql_class/Database.class.php");
  require_once("zero_to_na.php");

  $db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
  $db->connect();

  $rows = $db->query($sql);
  $occupation_data = array();
  $i = 0;
  while($record = $db->fetch_array($rows)){
    $occupation_data[$i]['name'] = $record['occupation_name'];
    $occupation_data[$i]['industry'] = $record['industry_name'];
    $occupation_data[$i]['annual_mean'] = $record['annual_mean'];
    $occupation_data[$i]['employment'] = $record['employment'];
    $i++;
  }
  $i = 1;

  if(count($occupation_data) == 0){
    $output['primary']['content'] .= ERROR_MESSAGE;
  } else {
    foreach($occupation_data as $value ) {
      if($i <= DATA_MAX_TO_SHOW ):
        $occupation_name = $value['name'];
        $employment_value = zero_to_na($value['employment'], "");
        $annual_mean = zero_to_na((double)$value['annual_mean'], "salary");
        $output['primary']['content'] .= "<tr><td class=\"first\"><span class=\"heading_style\">{$i}.</span> {$occupation_name}</td><td class=\"second\">{$annual_mean}</td><td class =\"third\">{$employment_value}</td></tr>";
      endif;
      $i++;

    }
  }

  return $output;

}

?>
