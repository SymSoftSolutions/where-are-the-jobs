<?php
require_once("php_mysql_class/config.inc.php");
require_once("php_mysql_class/Database.class.php");
require_once("zero_to_na.php");
include_once "get_primary_data.php";
include_once "get_secondary_data.php";
include_once "map_svggraph_output.php";

function default_case($sort_by, $industry_sort_by, $primary_sort_by){

  $db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
  $db->connect();

  // Get the occupation groups statistics
  if($sort_by == "average") {
    $sql = "select occugroup_name, annual_mean, employment from employment_data, occugroup where employment_data.occugroup_code = occugroup.occugroup_code and areatype_code = 'N' and industry_code = '000000' group by employment_data.occugroup_code order by annual_mean DESC";
  }
  if($sort_by == "jobs") {
    $sql = "select occugroup_name, annual_mean, employment from employment_data, occugroup where employment_data.occugroup_code = occugroup.occugroup_code and areatype_code = 'N' and industry_code = '000000' group by employment_data.occugroup_code order by employment DESC";
  }
  $rows = $db->query($sql);
  $occugroup_data = array();
  $i = 0;
  while($record = $db->fetch_array($rows)){
    $occugroup_data[$i]['name'] = $record['occugroup_name'];
    $occugroup_data[$i]['annual_mean'] = $record['annual_mean'];
    $occugroup_data[$i]['employment'] = $record['employment'];
    $i++;
  }
  $i = 1;
  $employment = array();
  $salary = array();
  $output['graph_table']['heading'] = "Top occupation groups across the nation";
  $output['graph_table']['content'] = "<table id='graphData' border='1'><tr><th>Occupation Group</th><th>OG average</th><th>Number of jobs</th></tr>";
  foreach($occugroup_data as $value ) {
    if($i < MAX_TO_SHOW ):
      $occugroup_name = $value['name'];
      if($value['employment'] != 0 && $value['employment'] != DB_DASH) {
        array_push($employment, $value['employment']);
      }
      if($value['annual_mean'] != 0 && $value['annual_mean'] != DB_DASH) {
        array_push($salary, (double)$value['annual_mean']);
      }
      $employment_value = zero_to_na($value['employment'], "");
      $annual_mean = zero_to_na((double)$value['annual_mean'], "salary");
      $output['graph_table']['content'] .= "<tr><td>{$i}. {$occugroup_name}</td><td>{$annual_mean}</td><td>{$employment_value}</td></tr>";
    endif;
    $i++;

  }
  $output['graph_table']['content'] .= "</table>";
  if(count($employment) > 0 ) $output['graph_table']['jobs']['min'] = zero_to_na(min($employment), "");
  if(count($employment) > 0 ) $output['graph_table']['jobs']['max'] = zero_to_na(max($employment), "");
  if(count($salary) > 0 ) $output['graph_table']['salary']['min'] = zero_to_na(min($salary), "salary");
  if(count($salary) > 0 ) $output['graph_table']['salary']['max'] = zero_to_na(max($salary), "salary");

  // Get top ranked industries
  if($industry_sort_by == "average") {
    $sql = "select distinct industry_name, avg(annual_mean) as annual_mean, avg(employment) as employment from employment_data, industry where employment_data.industry_code = industry.industry_code and areatype_code = 'N' and employment_data.industry_code != 00000 group by employment_data.industry_code order by avg(annual_mean) DESC";
  }
  if($industry_sort_by == "jobs") {
    $sql = "select industry_name, avg(annual_mean) as annual_mean, avg(employment) as employment from employment_data, industry where employment_data.industry_code = industry.industry_code and areatype_code = 'N' and employment_data.industry_code != 00000 group by employment_data.industry_code order by avg(employment) DESC";
  }

  // Call function that runs the sql query and outputs the data in a table.
  $temp_output = get_secondary_data($sql);
  $output['secondary']['heading'] .= "Top industries across the nation";
  $output['secondary']['content'] = $temp_output['secondary']['content'];

  $db->close();

  $map_output = map_svggraph_output("national", "");
  $output['map'] = $map_output['map'];
  $output['svgGraph'] = $map_output['svgGraph'];

  return $output;
}

function only_occupation_group($occupation_group, $sort_by, $industry_sort_by, $primary_sort_by){

  $db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
  $db->connect();
  $occupation_group = $db->escape($occupation_group);

  // Get top ranked states for the occupation group
  if($sort_by == "average") {
    $sql = "select area_name, employment_data.area_code, annual_mean, employment from employment_data, area where employment_data.area_code = lpad(area.area_code, 7, '0') and employment_data.areatype_code = 'S' and occupation_code = ".$occupation_group." and employment_data.area_code != '6600000' and employment_data.area_code != '7800000' and employment_data.area_code !=  '7200000' order by annual_mean DESC";
  }
  if($sort_by == "jobs") {
    $sql = "select area_name, employment_data.area_code, annual_mean, employment from employment_data, area where employment_data.area_code = lpad(area.area_code, 7, '0') and employment_data.areatype_code = 'S' and occupation_code = ".$occupation_group."  and employment_data.area_code != '6600000' and employment_data.area_code != '7800000' and employment_data.area_code !=  '7200000' order by employment DESC";
  }
  $rows = $db->query($sql);
  $state_data = array();
  $i = 0;
  while($record = $db->fetch_array($rows)){
    $state_data[$i]['name'] = $record['area_name'];
    $state_data[$i]['area_code'] = $record['area_code'];
    $state_data[$i]['annual_mean'] = round($record['annual_mean']);
    $state_data[$i]['employment'] = round($record['employment']);
    $i++;
  }
  $i = 1;
  $employment = array();
  $salary = array();
  if(count($state_data) == 0){
    $output['graph_table']['heading'] = "Top states for ".$GLOBALS['og_name'];
    $output['graph_table']['content'] = ERROR_MESSAGE;
  } else {
    $output['graph_table']['heading'] = "Top states for ".$GLOBALS['og_name'];
    $output['graph_table']['content'] .= "<table id='graphData' border='1'><tr><th>State</th><th>State average</th><th>Number of jobs</th><th>State Code</th></tr>";
    foreach($state_data as $value ) {
      if($i < MAX_TO_SHOW ):
        $area_name = $value['name'];
        if($value['employment'] != 0 && $value['employment'] != DB_DASH){
          array_push($employment, $value['employment']);
        }
        if($value['annual_mean'] != 0 && $value['annual_mean'] != DB_DASH) {
          array_push($salary, (double)$value['annual_mean']);
        }
        $employment_value = zero_to_na($value['employment'], "");
        $annual_mean = zero_to_na((double)$value['annual_mean'], "salary");
        $output['graph_table']['content'] .= "<tr><td>{$i}. {$area_name}</td><td>{$annual_mean}</td><td>{$employment_value}</td><td>{$value['area_code']}</td></tr>";
      endif;
      $i++;

    }
    $output['graph_table']['content'] .= "</table>";
    if(count($employment) > 0 ) $output['graph_table']['jobs']['min'] = zero_to_na(min($employment), "");
    if(count($employment) > 0 ) $output['graph_table']['jobs']['max'] = zero_to_na(max($employment), "");
    if(count($salary) > 0 ) $output['graph_table']['salary']['min'] = zero_to_na(min($salary), "salary");
    if(count($salary) > 0 ) $output['graph_table']['salary']['max'] = zero_to_na(max($salary), "salary");
  }

  // top ten occupations within the occupation group
  $occupation_group_code = $occupation_group;
  if($primary_sort_by == "average") {
    $sql = "select industry_name, occupation_name, annual_mean, employment from employment_data, occupation, industry where employment_data.industry_code = industry.industry_code and employment_data.occupation_code = occupation.occupation_code and occugroup_code = ".$occupation_group." and employment_data.industry_code = 00000 and areatype_code = 'N' order by annual_mean DESC";
  }
  if($primary_sort_by == "jobs") {
    $sql = "select industry_name, occupation_name, annual_mean, employment from industry, employment_data, occupation where employment_data.industry_code = industry.industry_code and employment_data.occupation_code = occupation.occupation_code and occugroup_code = ".$occupation_group." and employment_data.industry_code = 00000 and areatype_code = 'N' order by employment DESC";
  }

  // Call function that runs the sql query and outputs the data in a table. for the primary data.
  $temp_output = get_primary_data($sql);
  $output['primary']['content'] = $temp_output['primary']['content'];
  $output['primary']['heading'] .= "Top occupations within ".$GLOBALS['og_name'];

  // Get top industries for the occupation group
  if($industry_sort_by == "average") {
    $sql = "select distinct industry_name, annual_mean, employment from employment_data, occupation, industry where employment_data.industry_code = industry.industry_code and employment_data.occupation_code = occupation.occupation_code and occugroup_code = ".$occupation_group." and areatype_code = 'N' group by employment_data.industry_code order by annual_mean DESC ";
  }
  if($industry_sort_by == "jobs") {
    $sql = "select distinct industry_name, annual_mean, employment from employment_data, occupation, industry where employment_data.industry_code = industry.industry_code and employment_data.occupation_code = occupation.occupation_code and occugroup_code = ".$occupation_group." and areatype_code = 'N' group by employment_data.industry_code order by employment DESC ";
  }

  // Call function that runs the sql query and outputs the data in a table.
  $temp_output = get_secondary_data($sql);
  $output['secondary']['content'] = $temp_output['secondary']['content'];
  $output['secondary']['heading'] = "Top industries for ".$GLOBALS['og_name'];

  $db->close();
  $map_output = map_svggraph_output("national", "");
  $output['map'] = $map_output['map'];
  $output['svgGraph'] = $map_output['svgGraph'];

  return $output;

}

function only_occupation($occupation_group, $occupation, $sort_by, $industry_sort_by, $primary_sort_by){

  $db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
  $db->connect();

  $occupation = $db->escape($occupation);
  if($sort_by == "average") {
    $sql = "select area_name, employment_data.area_code, annual_mean, employment from employment_data, area where employment_data.area_code = lpad(area.area_code, 7, '0') and employment_data.areatype_code = 'S' and industry_code = '000000' and occupation_code = ".$occupation."   and employment_data.area_code != '6600000' and employment_data.area_code != '7800000' and employment_data.area_code !=  '7200000' order by annual_mean DESC";
  }
  if($sort_by == "jobs") {
    $sql = "select area_name, employment_data.area_code, annual_mean, employment from employment_data, area where employment_data.area_code = lpad(area.area_code, 7, '0') and employment_data.areatype_code = 'S' and industry_code = '000000' and occupation_code = ".$occupation."   and employment_data.area_code != '6600000' and employment_data.area_code != '7800000' and employment_data.area_code !=  '7200000' order by employment DESC";
  }
  $rows = $db->query($sql);
  $state_data = array();
  $i = 0;
  while($record = $db->fetch_array($rows)){
    $state_data[$i]['name'] = $record['area_name'];
    $state_data[$i]['area_code'] = $record['area_code'];
    $state_data[$i]['annual_mean'] = $record['annual_mean'];
    $state_data[$i]['employment'] = $record['employment'];
    $i++;
  }
  $i = 1;
  $occupation_code = $occupation;
  $employment = array();
  $salary = array();
  if(count($state_data) == 0){
    $output['graph_table']['heading'] = "Top states for ".$GLOBALS['occupation_name'];
    $output['graph_table']['content'] = ERROR_MESSAGE;
  } else {
    $output['graph_table']['heading'] = "Top states for ".$GLOBALS['occupation_name'];
    $output['graph_table']['content'] .= "<table id='graphData' border='1'><tr><th>State</th><th>State average</th><th>Number of jobs</th><th>State code</th></tr>";
    foreach($state_data as $value ) {
      if($i < MAX_TO_SHOW ):
        $state_name = $value['name'];
        if($value['employment'] != '0' && $value['employment'] != DB_DASH) {
          array_push($employment, $value['employment']);
        }
        if($value['annual_mean'] != '0' && $value['annual_mean'] != DB_DASH) {
          array_push($salary, (double)$value['annual_mean']);
        }
        $employment_value = zero_to_na($value['employment'], "");
        $annual_mean = zero_to_na((double)$value['annual_mean'], "salary");
        $output['graph_table']['content'] .= "<tr><td>{$i}. {$state_name}</td><td>{$annual_mean}</td><td>{$employment_value}</td><td>{$value['area_code']}</td></tr>";
      endif;
      $i++;
    }
    $output['graph_table']['content'] .= "</table>";
    if(count($employment) > 0 ) $output['graph_table']['jobs']['min'] = zero_to_na(min($employment), "");
    if(count($employment) > 0 ) $output['graph_table']['jobs']['max'] = zero_to_na(max($employment), "");
    if(count($salary) > 0 ) $output['graph_table']['salary']['min'] = zero_to_na(min($salary), "salary");
    if(count($salary) > 0 ) $output['graph_table']['salary']['max'] = zero_to_na(max($salary), "salary");
  }

  // top occupations within the occupation group
  $occupation_group_code = $occupation_group;
  if($primary_sort_by == "average") {
    $sql = "select industry_name, occupation_name, annual_mean, employment from employment_data, occupation, industry where employment_data.industry_code = industry.industry_code and employment_data.occupation_code = occupation.occupation_code and occugroup_code = ".$occupation_group." and employment_data.industry_code = 00000 and areatype_code = 'N' and employment_data.occupation_code != ".$occupation_group." order by annual_mean DESC";
  }
  if($primary_sort_by == "jobs") {
    $sql = "select industry_name, occupation_name, annual_mean, employment from industry, employment_data, occupation where employment_data.industry_code = industry.industry_code and employment_data.occupation_code = occupation.occupation_code and occugroup_code = ".$occupation_group." and employment_data.industry_code = 00000 and areatype_code = 'N' and employment_data.occupation_code != ".$occupation_group." order by employment DESC";
  }

  // Call function that runs the sql query and outputs the data in a table. for the primary data.
  $temp_output = get_primary_data($sql);
  $output['primary']['content'] = $temp_output['primary']['content'];
  $output['primary']['heading'] = "Top occupations within ".$GLOBALS['og_name'];

  // Get top ranked industries
  if($industry_sort_by == "average") {
    $sql = "select distinct industry_name, annual_mean, employment from employment_data, industry where employment_data.industry_code = industry.industry_code and occupation_code = ".$occupation_code." and employment_data.industry_code != '000000' order by annual_mean DESC";
  }
  if($industry_sort_by == "jobs") {
    $sql = "select distinct industry_name, annual_mean, employment from employment_data, industry where employment_data.industry_code = industry.industry_code and occupation_code = ".$occupation_code." and employment_data.industry_code != '000000' order by employment DESC";
  }

  // Call function that runs the sql query and outputs the data in a table.
  $temp_output = get_secondary_data($sql);
  $output['secondary']['content'] = $temp_output['secondary']['content'];
  $output['secondary']['heading'] = "Industries that employ ".$GLOBALS['occupation_name'];

  $db->close();

  $map_output = map_svggraph_output("national", "");
  $output['map'] = $map_output['map'];
  $output['svgGraph'] = $map_output['svgGraph'];

  return $output;
}

function only_state($state, $sort_by, $industry_sort_by, $primary_sort_by){

  $db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
  $db->connect();

  $state = $db->escape($state);

  if(isset($state) && !empty($state)){
    if($state != "00000"){
      $state_code = str_pad($state, 7, "0", STR_PAD_LEFT);
      $state_code = substr($state_code, 0, 2);
    }
  }

  // Get occupation data for the state
  if($sort_by == "average") {
    $sql = "select occugroup.occugroup_name as occupation_name, avg(annual_mean) as annual_mean, avg(employment) as employment from employment_data, occugroup, statemsa where employment_data.area_code = statemsa.msa_code and statemsa.state_code = '06' and employment_data.occugroup_code = occugroup.occugroup_code and areatype_code = 'M' and industry_code = '000000' group by employment_data.occugroup_code order by avg(annual_mean) DESC";
  }
  if($sort_by == "jobs") {
    $sql = "select occugroup.occugroup_name as occupation_name, avg(annual_mean) as annual_mean, avg(employment) as employment from employment_data, occugroup, statemsa where employment_data.area_code = statemsa.msa_code and statemsa.state_code = '06' and employment_data.occugroup_code = occugroup.occugroup_code and areatype_code = 'M' and industry_code = '000000' group by employment_data.occugroup_code order by avg(employment) DESC";
  }
  $rows = $db->query($sql);
  $occupation_data = array();
  $i = 0;
  while($record = $db->fetch_array($rows)){
    $occupation_data[$i]['name'] = $record['occupation_name'];
    $occupation_data[$i]['annual_mean'] = round($record['annual_mean']);
    $occupation_data[$i]['employment'] = round($record['employment']);
    $i++;
  }
  $i = 1;
  $employment = array();
  $salary = array();
  if(count($occupation_data) == 0){
    $output['graph_table']['content'] = "<div>Sorry, there is no occupation data available for this combinations. Please change the selection.</div>";
  } else {
    $output['graph_table']['heading'] = "Top occupation groups within ".$GLOBALS['state_name'];
    $output['graph_table']['content'] .= "<table id='graphData' border='1'><tr><th>Occupation Groups</th><th>Occupation average</th><th>Number of jobs</th></tr>";
    foreach($occupation_data as $value ) {
      if($i < MAX_TO_SHOW ):
        $occupation_name = $value['name'];
        if($value['employment'] != 0 && $value['employment'] != DB_DASH) {
          array_push($employment, $value['employment']);
        }
        if($value['annual_mean'] != 0 && $value['annual_mean'] != DB_DASH) {
          array_push($salary, (double)$value['annual_mean']);
        }
        $employment_value = zero_to_na($value['employment'], "");
        $annual_mean = zero_to_na((double)$value['annual_mean'], "salary");
        $output['graph_table']['content'] .= "<tr><td>{$i}. {$occupation_name}</td><td>{$annual_mean}</td><td>{$employment_value}</td></tr>";
      endif;
      $i++;
    }
    $output['graph_table']['content'] .= "</table>";
    if(count($employment) > 0 ) $output['graph_table']['jobs']['min'] = zero_to_na(min($employment), "");
    if(count($employment) > 0 ) $output['graph_table']['jobs']['max'] = zero_to_na(max($employment), "");
    if(count($salary) > 0 ) $output['graph_table']['salary']['min'] = zero_to_na(min($salary), "salary");
    if(count($salary) > 0 ) $output['graph_table']['salary']['max'] = zero_to_na(max($salary), "salary");
  }

  // Get top ranked industries for national statistics
  if($industry_sort_by == "average") {
    $sql = "select industry_name, avg(annual_mean) as annual_mean, avg(employment) as employment from employment_data, industry where employment_data.industry_code = industry.industry_code and areatype_code = 'N' and employment_data.industry_code != 00000 group by employment_data.industry_code order by avg(annual_mean) DESC";
  }
  if($industry_sort_by == "jobs") {
    $sql = "select industry_name, avg(annual_mean) as annual_mean, avg(employment) as employment from employment_data, industry where employment_data.industry_code = industry.industry_code and areatype_code = 'N' and employment_data.industry_code != 00000 group by employment_data.industry_code order by avg(employment) DESC";
  }

  // Call function that runs the sql query and outputs the data in a table.
  $temp_output = get_secondary_data($sql);
  $output['secondary']['content'] = $temp_output['secondary']['content'];
  $output['secondary']['heading'] = "Top industries nationwide";

  $db->close();

  $map_output = map_svggraph_output("state",$GLOBALS['state_name']);
  $output['map'] = $map_output['map'];
  $output['svgGraph'] = $map_output['svgGraph'];

   return $output;

}

function only_metro($state, $metro, $sort_by, $industry_sort_by, $primary_sort_by){

  $db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
  $db->connect();

  $metro = $db->escape($metro);

  if($sort_by == "average") {
    $sql = "select occupation_name, annual_mean, employment from employment_data, occupation where employment_data.occupation_code = occupation.occupation_code and areatype_code = 'M' and area_code = ".$metro." order by annual_mean DESC";
  }
  if($sort_by == "jobs") {
    $sql = "select occupation_name, annual_mean, employment from employment_data, occupation where employment_data.occupation_code = occupation.occupation_code and areatype_code = 'M' and area_code = ".$metro." order by employment DESC";
  }
  $rows = $db->query($sql);
  $occupation_data = array();
  $i = 0;
  while($record = $db->fetch_array($rows)){
    $occupation_data[$i]['name'] = $record['occupation_name'];
    $occupation_data[$i]['annual_mean'] = $record['annual_mean'];
    $occupation_data[$i]['employment'] = $record['employment'];
    $i++;
  }
  $i = 1;
  $metro_code = $metro;
  $employment = array();
  $salary = array();
  if(count($occupation_data) == 0){
    $output['graph_table']['heading'] = "Top occupations within ".$GLOBALS['metro_name'];
    $output['graph_table']['content'] .= ERROR_MESSAGE;
  } else {
    $output['graph_table']['heading'] = "Top occupations within ".$GLOBALS['metro_name'];
    $output['graph_table']['content'] .= "<table id='graphData' border='1'><tr><th>Occupation</th><th>Occupation average</th><th>Number of jobs</th></tr>";
    foreach($occupation_data as $value ) {
      if($i < MAX_TO_SHOW ):
        $occupation_name = $value['name'];
        if($value['employment'] != 0 && $value['employment'] != DB_DASH) {
          array_push($employment, $value['employment']);
        }
        if($value['annual_mean'] != 0 && $value['annual_mean'] != DB_DASH) {
          array_push($salary, (double)$value['annual_mean']);
        }
        $employment_value = zero_to_na($value['employment'], "");
        $annual_mean = zero_to_na((double)$value['annual_mean'], "salary");
        $output['graph_table']['content'] .= "<tr><td>{$i}. {$occupation_name}</td><td>{$annual_mean}</td><td>{$employment_value}</td></tr>";
      endif;
      $i++;
    }
    $output['graph_table']['content'] .= "</table>";
    if(count($employment) > 0 ) $output['graph_table']['jobs']['min'] = zero_to_na(min($employment), "");
    if(count($employment) > 0 ) $output['graph_table']['jobs']['max'] = zero_to_na(max($employment), "");
    if(count($salary) > 0 ) $output['graph_table']['salary']['min'] = zero_to_na(min($salary), "salary");
    if(count($salary) > 0 ) $output['graph_table']['salary']['max'] = zero_to_na(max($salary), "salary");
  }

  /* Get nationwide stats on industries ranked by avg income */
  if($industry_sort_by == "average") {
    $sql = "select industry_name, avg(annual_mean) as annual_mean, avg(employment) as employment from employment_data, industry where employment_data.industry_code = industry.industry_code and areatype_code = 'N' and employment_data.industry_code != 00000 group by employment_data.industry_code order by avg(annual_mean) DESC";
  }
  if($industry_sort_by == "jobs") {
    $sql = "select industry_name, avg(annual_mean) as annual_mean, avg(employment) as employment from employment_data, industry where employment_data.industry_code = industry.industry_code and areatype_code = 'N' and employment_data.industry_code != 00000 group by employment_data.industry_code order by avg(employment) DESC";
  }

  // Call function that runs the sql query and outputs the data in a table.
  $temp_output = get_secondary_data($sql);
  $output['secondary']['content'] = $temp_output['secondary']['content'];
  $output['secondary']['heading'] = "Top industries nationwide ";

  $db->close();

  $map_output = map_svggraph_output("state", $GLOBALS['state_name']);
  $output['map'] = $map_output['map'];
  $output['svgGraph'] = $map_output['svgGraph'];

  return $output;
}

function og_state($occupation_group, $state, $sort_by, $industry_sort_by, $primary_sort_by){

  $db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
  $db->connect();

  $occupation_group = $db->escape($occupation_group);
  $state = $db->escape($state);

  if(isset($state) && !empty($state)){
    if($state != "00000"){
      $state_code = str_pad($state, 7, "0", STR_PAD_LEFT);
      $state_code = substr($state_code, 0, 2);
    }
  }

  // top ten occupations within the occupation group for the state
  $occupation_group_code = $occupation_group;

  if($primary_sort_by == "average") {
    $sql = "SELECT DISTINCT occupation_name, annual_mean, employment FROM statemsa, employment_data, occupation WHERE employment_data.occupation_code = occupation.occupation_code AND occugroup_code = ".$occupation_group." AND areatype_code = 'S' AND employment_data.area_code = ".$state." and employment_data.occupation_code != ".$occupation_group." ORDER BY annual_mean DESC";
  }
  if($primary_sort_by == "jobs") {
    $sql = "SELECT DISTINCT occupation_name, annual_mean, employment FROM statemsa, employment_data, occupation WHERE employment_data.occupation_code = occupation.occupation_code AND occugroup_code = ".$occupation_group." AND areatype_code = 'S' AND employment_data.area_code = ".$state." and employment_data.occupation_code != ".$occupation_group." ORDER BY employment DESC";
  }

  // Call function that runs the sql query and outputs the data in a table. for the primary data.
  $temp_output = get_primary_data($sql);
  $output['primary']['content'] = $temp_output['primary']['content'];
  $output['primary']['heading'] .= "Top occupations within ".$GLOBALS['og_name'];

  // Get top ranked regions for the occupation group for the state
  if($sort_by == "average") {
    $sql = "select msa_name, concat( state_code, substring( msa_code, 3 ) ) AS msa_code, avg(annual_mean) as annual_mean, avg(employment) as employment from employment_data, statemsa where employment_data.area_code = statemsa.msa_code and employment_data.areatype_code = 'M' and occugroup_code = ".$occupation_group." and statemsa.state_code = ".$state_code." group by employment_data.area_code order by avg(annual_mean) DESC";
  }
  if($sort_by == "jobs") {
    $sql = "select msa_name, concat( state_code, substring( msa_code, 3 ) ) AS msa_code, avg(annual_mean) as annual_mean, avg(employment) as employment from employment_data, statemsa where employment_data.area_code = statemsa.msa_code and employment_data.areatype_code = 'M' and occugroup_code = ".$occupation_group." and statemsa.state_code = ".$state_code." group by employment_data.area_code order by avg(employment) DESC";
  }
  $rows = $db->query($sql);
  $state_data = array();
  $i = 0;
  while($record = $db->fetch_array($rows)){
    $state_data[$i]['name'] = $record['msa_name'];
    $state_data[$i]['msa_code'] = $record['msa_code'];
    $state_data[$i]['annual_mean'] = round($record['annual_mean']);
    $state_data[$i]['employment'] = round($record['employment']);
    $i++;
  }
  $i = 1;
  $employment = array();
  $salary = array();
  if(count($state_data) == 0){
    $output['graph_table']['heading'] = "Top regions for ".$GLOBALS['og_name'];
    $output['graph_table']['content'] = ERROR_MESSAGE;
  } else {
    $output['graph_table']['heading'] = "Top regions for ".$GLOBALS['og_name'];
    $output['graph_table']['content'] .= "<table id='graphData' border='1'><tr><th>Region</th><th>Region average</th><th>Number of jobs</th><th>MSA code</th></tr>";
    foreach($state_data as $value ) {
      if($i < MAX_TO_SHOW ):
        $area_name = $value['name'];
        if($value['employment'] != 0 && $value['employment'] != DB_DASH) {
          array_push($employment, $value['employment']);
        }
        if($value['annual_mean'] != 0 && $value['annual_mean'] != DB_DASH) {
          array_push($salary, (double)$value['annual_mean']);
        }
        $employment_value = zero_to_na($value['employment'], "");
        $annual_mean = zero_to_na((double)$value['annual_mean'], "salary");
        $output['graph_table']['content'] .= "<tr><td>{$i}. {$area_name}</td><td>{$annual_mean}</td><td>{$employment_value}</td><td>{$value['msa_code']}</td></tr>";
      endif;
      $i++;

    }
    $output['graph_table']['content'] .= "</table>";
    if(count($employment) > 0 ) $output['graph_table']['jobs']['min'] = zero_to_na(min($employment), "");
    if(count($employment) > 0 ) $output['graph_table']['jobs']['max'] = zero_to_na(max($employment), "");
    if(count($salary) > 0 ) $output['graph_table']['salary']['min'] = zero_to_na(min($salary), "salary");
    if(count($salary) > 0 ) $output['graph_table']['salary']['max'] = zero_to_na(max($salary), "salary");
  }
  // Get top industries for the occupation group
  if($industry_sort_by == "average") {
    $sql = "select distinct industry_name, annual_mean, employment from employment_data, occupation, industry where employment_data.industry_code = industry.industry_code and employment_data.occupation_code = occupation.occupation_code and occugroup_code = ".$occupation_group." and areatype_code = 'N' group by employment_data.industry_code order by annual_mean DESC ";
  }
  if($industry_sort_by == "jobs") {
    $sql = "select distinct industry_name, annual_mean, employment from employment_data, occupation, industry where employment_data.industry_code = industry.industry_code and employment_data.occupation_code = occupation.occupation_code and occugroup_code = ".$occupation_group." and areatype_code = 'N' group by employment_data.industry_code order by employment DESC ";
  }

  // Call function that runs the sql query and outputs the data in a table.
  $temp_output = get_secondary_data($sql);
  $output['secondary']['content'] = $temp_output['secondary']['content'];
  $output['secondary']['heading'] = "Industries that employ in ".$GLOBALS['og_name'];
  $db->close();

  $map_output = map_svggraph_output("state",$GLOBALS['state_name']);
  $output['map'] = $map_output['map'];
  $output['svgGraph'] = $map_output['svgGraph'];

  return $output;

}

function og_metro($occupation_group, $state, $metro, $sort_by, $industry_sort_by, $primary_sort_by){

  $db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
  $db->connect();

  $occupation_group = $db->escape($occupation_group);
  $state = $db->escape($state);

  if($sort_by == "average") {
    $sql = "select occupation_name, annual_mean, employment from employment_data, occupation where employment_data.occupation_code = occupation.occupation_code and areatype_code = 'M' and occugroup_code = ".$occupation_group." and area_code = ".$metro." order by annual_mean DESC";
  }
  if($sort_by == "jobs") {
    $sql = "select occupation_name, annual_mean, employment from employment_data, occupation where employment_data.occupation_code = occupation.occupation_code and areatype_code = 'M' and occugroup_code = ".$occupation_group." and area_code = ".$metro." order by employment DESC";
  }
  $rows = $db->query($sql);
  $occupation_data = array();
  $i = 0;
  while($record = $db->fetch_array($rows)){
    $occupation_data[$i]['name'] = $record['occupation_name'];
    $occupation_data[$i]['annual_mean'] = $record['annual_mean'];
    $occupation_data[$i]['employment'] = $record['employment'];
    $i++;
  }
  $i = 1;
  $metro_code = $metro;
  $employment = array();
  $salary = array();
  if(count($occupation_data) == 0){
    $output['graph_table']['heading'] = "Top occupations within ".$GLOBALS['og_name'];
    $output['graph_table']['content'] = ERROR_MESSAGE;
  } else {
    $output['graph_table']['heading'] = "Top occupations within ".$GLOBALS['og_name'];
    $output['graph_table']['content'] .= "<table id='graphData' border='1'><tr><th>Occupation</th><th>Occupation average</th><th>Number of jobs</th></tr>";
    foreach($occupation_data as $value ) {
      if($i < MAX_TO_SHOW ):
        $occupation_name = $value['name'];
        if($value['employment'] != 0 && $value['employment'] != DB_DASH){
          array_push($employment, $value['employment']);
        }
        if($value['annual_mean'] != 0 && $value['annual_mean'] != DB_DASH) {
          array_push($salary, (double)$value['annual_mean']);
        }
        $employment_value = zero_to_na($value['employment'], "");
        $annual_mean = zero_to_na((double)$value['annual_mean'], "salary");
        $output['graph_table']['content'] .= "<tr><td>{$i}. {$occupation_name}</td><td>{$annual_mean}</td><td>{$employment_value}</td></tr>";
      endif;
      $i++;
    }
    $output['graph_table']['content'] .= "</table>";
    if(count($employment) > 0 ) $output['graph_table']['jobs']['min'] = zero_to_na(min($employment), "");
    if(count($employment) > 0 ) $output['graph_table']['jobs']['max'] = zero_to_na(max($employment), "");
    if(count($salary) > 0 ) $output['graph_table']['salary']['min'] = zero_to_na(min($salary), "salary");
    if(count($salary) > 0 ) $output['graph_table']['salary']['max'] = zero_to_na(max($salary), "salary");
  }

  // top ten occupations within the occupation group for the state
  $occupation_group_code = $occupation_group;

  if($primary_sort_by == "average") {
    $sql = "SELECT DISTINCT occupation_name, annual_mean, employment FROM statemsa, employment_data, occupation WHERE employment_data.occupation_code = occupation.occupation_code AND occugroup_code = ".$occupation_group." AND areatype_code = 'S' AND employment_data.area_code = ".$state." and employment_data.occupation_code != ".$occupation_group." ORDER BY annual_mean DESC";
  }
  if($primary_sort_by == "jobs") {
    $sql = "SELECT DISTINCT occupation_name, annual_mean, employment FROM statemsa, employment_data, occupation WHERE employment_data.occupation_code = occupation.occupation_code AND occugroup_code = ".$occupation_group." AND areatype_code = 'S' AND employment_data.area_code = ".$state." and employment_data.occupation_code != ".$occupation_group." ORDER BY employment DESC";
  }

  // Call function that runs the sql query and outputs the data in a table. for the primary data.
  $temp_output = get_primary_data($sql);
  $output['primary']['content'] = $temp_output['primary']['content'];
  $output['primary']['heading'] .= "Top occupations within ".$GLOBALS['og_name'];

// Get top industries for the occupation group
  if($industry_sort_by == "average") {
    $sql = "select distinct industry_name, annual_mean, employment from employment_data, occupation, industry where employment_data.industry_code = industry.industry_code and employment_data.occupation_code = occupation.occupation_code and occugroup_code = ".$occupation_group." and areatype_code = 'N' group by employment_data.industry_code order by annual_mean DESC ";
  }
  if($industry_sort_by == "jobs") {
    $sql = "select distinct industry_name, annual_mean, employment from employment_data, occupation, industry where employment_data.industry_code = industry.industry_code and employment_data.occupation_code = occupation.occupation_code and occugroup_code = ".$occupation_group." and areatype_code = 'N' group by employment_data.industry_code order by employment DESC ";
  }

  // Call function that runs the sql query and outputs the data in a table.
  $temp_output = get_secondary_data($sql);
  $output['secondary']['content'] = $temp_output['secondary']['content'];
  $output['secondary']['heading'] = "Industries that employ in ".$GLOBALS['og_name'];

  $db->close();

  $map_output = map_svggraph_output("state",$GLOBALS['state_name']);
  $output['map'] = $map_output['map'];
  $output['svgGraph'] = $map_output['svgGraph'];

  return $output;

}

function occupation_state($occupation_group, $occupation, $state, $sort_by, $industry_sort_by, $primary_sort_by){

  $db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
  $db->connect();

  $occupation_group = $db->escape($occupation_group);
  $state = $db->escape($state);

  if(isset($state) && !empty($state)){
    if($state != "00000"){
      $state_code = str_pad($state, 7, "0", STR_PAD_LEFT);
      $state_code = substr($state_code, 0, 2);
    }
  }

  // State level statistic for the occupation
  $occupation_group_code = $occupation_group;
  if($sort_by == "average") {
    $sql = "select occupation_name, annual_mean, employment from employment_data, occupation where employment_data.occupation_code = occupation.occupation_code and areatype_code = 'S' and employment_data.area_code = ".$state." and employment_data.occupation_code = ".$occupation." order by annual_mean DESC";
  }
  if($sort_by == "jobs") {
    $sql = "select occupation_name, annual_mean, employment, industry_name from industry, statemsa, employment_data, occupation where employment_data.industry_code = industry.industry_code and employment_data.occupation_code = occupation.occupation_code and areatype_code = 'M' and statemsa.msa_code = employment_data.area_code and statemsa.state_code = ".$state_code." and employment_data.occupation_code = ".$occupation." order by avg(employment) DESC";
    $sql = "select occupation_name, annual_mean, employment from employment_data, occupation where employment_data.occupation_code = occupation.occupation_code and areatype_code = 'S' and employment_data.area_code = ".$state." and employment_data.occupation_code = ".$occupation." order by employment DESC";
  }
  $rows = $db->query($sql);
  $occupation_data = array();
  $i = 0;
  while($record = $db->fetch_array($rows)){
    $occupation_data[$i]['name'] = $record['occupation_name'];
    $occupation_data[$i]['industry_name'] = $record['industry_name'];
    $occupation_data[$i]['annual_mean'] = $record['annual_mean'];
    $occupation_data[$i]['employment'] = round($record['employment']);
    $i++;
  }
  $i = 1;

  if(count($occupation_data) == 0){
    $output['primary']['state']['heading'] .= "<h3>".$GLOBALS['occupation_name']." Statistics </h3>";
    //$output['primary']['state']['content'] = ERROR_MESSAGE;
  } else {
    $output['primary']['state']['heading'] .= "<h3>".$GLOBALS['occupation_name']." Statistics </h3>";
    foreach($occupation_data as $value ) {
      if($i < MAX_TO_SHOW ):
        $occupation_name = $value['name'];
        $employment_value = number_format($value['employment']);
        $annual_mean = '$'.number_format((double)$value['annual_mean']);
        $output['primary']['state']['content'] .= "<span class='label'>Statewide average salary: </span>{$annual_mean}, <span class='label'>Number of jobs: </span>{$employment_value}";
      endif;
      $i++;

    }
  }

  // top ten occupations within the occupation group for the state
  $occupation_group_code = $occupation_group;

  if($primary_sort_by == "average") {
    $sql = "SELECT DISTINCT occupation_name, annual_mean, employment FROM statemsa, employment_data, occupation WHERE employment_data.occupation_code = occupation.occupation_code AND occugroup_code = ".$occupation_group." AND areatype_code = 'S' AND employment_data.area_code = ".$state." and employment_data.occupation_code != ".$occupation_group." ORDER BY annual_mean DESC";
  }
  if($primary_sort_by == "jobs") {
    $sql = "SELECT DISTINCT occupation_name, annual_mean, employment FROM statemsa, employment_data, occupation WHERE employment_data.occupation_code = occupation.occupation_code AND occugroup_code = ".$occupation_group." AND areatype_code = 'S' AND employment_data.area_code = ".$state." and employment_data.occupation_code != ".$occupation_group." ORDER BY employment DESC";
  }

  // Call function that runs the sql query and outputs the data in a table. for the primary data.
  $temp_output = get_primary_data($sql);
  $output['primary']['content'] = $temp_output['primary']['content'];
  $output['primary']['heading'] .= "Top occupations within ".$GLOBALS['og_name'];
  // Get top ranked metros for the occupation
  if($sort_by == "average") {
    $sql = "select msa_name, concat( state_code, substring( msa_code, 3 ) ) AS msa_code, annual_mean, employment from employment_data, statemsa where employment_data.area_code = statemsa.msa_code and employment_data.areatype_code = 'M' and statemsa.state_code = ".$state_code." and employment_data.occupation_code = ".$occupation."  order by annual_mean DESC";
  }
  if($sort_by == "jobs") {
    $sql = "select msa_name, concat( state_code, substring( msa_code, 3 ) ) AS msa_code, annual_mean, employment from employment_data, statemsa where employment_data.area_code = statemsa.msa_code and employment_data.areatype_code = 'M' and statemsa.state_code = ".$state_code." and employment_data.occupation_code = ".$occupation."  order by employment DESC";
  }
  $rows = $db->query($sql);
  $state_data = array();
  $i = 0;
  while($record = $db->fetch_array($rows)){
    $state_data[$i]['name'] = $record['msa_name'];
    $state_data[$i]['msa_code'] = $record['msa_code'];
    $state_data[$i]['annual_mean'] = round($record['annual_mean']);
    $state_data[$i]['employment'] = round($record['employment']);
    $i++;
  }
  $i = 1;
  $employment = array();
  $salary = array();
  if(count($state_data) == 0){
    $output['graph_table']['heading'] = "Top regions in ".$GLOBALS['state_name']." for ".$GLOBALS['occupation_name'];
    $output['graph_table']['content'] = ERROR_MESSAGE;
  } else {
    $output['graph_table']['heading'] = "Top regions in ".$GLOBALS['state_name']." for ".$GLOBALS['occupation_name'];
    $output['graph_table']['content'] .= "<table id='graphData' border='1'><tr><th>Region</th><th>Region average</th><th>Number of jobs</th><th>MSA code</th></tr>";
    foreach($state_data as $value ) {
      if($i < MAX_TO_SHOW ):
        $area_name = $value['name'];
        if($value['employment'] != 0 && $value['employment'] != DB_DASH) {
          array_push($employment, $value['employment']);
        }
        if($value['annual_mean'] != 0 && $value['annual_mean'] != DB_DASH) {
          array_push($salary, (double)$value['annual_mean']);
        }
        $employment_value = zero_to_na($value['employment'], "");
        $annual_mean = zero_to_na((double)$value['annual_mean'], "salary");
        $output['graph_table']['content'] .= "<tr><td>{$i}. {$area_name}</td><td>{$annual_mean}</td><td>{$employment_value}</td><td>{$value['msa_code']}</td></tr>";
      endif;
      $i++;
    }
    $output['graph_table']['content'] .= "</table>";
    if(count($employment) > 0 ) $output['graph_table']['jobs']['min'] = zero_to_na(min($employment), "");
    if(count($employment) > 0 ) $output['graph_table']['jobs']['max'] = zero_to_na(max($employment), "");
    if(count($salary) > 0 ) $output['graph_table']['salary']['min'] = zero_to_na(min($salary), "salary");
    if(count($salary) > 0 ) $output['graph_table']['salary']['max'] = zero_to_na(max($salary), "salary");
  }

  // Get top ranked industries
  if($industry_sort_by == "average") {
    $sql = "select distinct industry_name, annual_mean, employment from employment_data, industry where employment_data.industry_code = industry.industry_code and occupation_code = ".$occupation." and employment_data.industry_code != '000000' order by annual_mean DESC";
  }
  if($industry_sort_by == "jobs") {
    $sql = "select distinct industry_name, annual_mean, employment from employment_data, industry where employment_data.industry_code = industry.industry_code and occupation_code = ".$occupation." and employment_data.industry_code != '000000' order by employment DESC";
  }

  // Call function that runs the sql query and outputs the data in a table.
  $temp_output = get_secondary_data($sql);
  $output['secondary']['content'] = $temp_output['secondary']['content'];
  $output['secondary']['heading'] = "Industries that employ ".$GLOBALS['occupation_name'];

  $db->close();

  $map_output = map_svggraph_output("state",$GLOBALS['state_name']);
  $output['map'] = $map_output['map'];
  $output['svgGraph'] = $map_output['svgGraph'];

  return $output;

}

function occupation_metro($occupation_group, $occupation, $state, $metro, $sort_by, $industry_sort_by, $primary_sort_by){

  $db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
  $db->connect();

  $occupation_group = $db->escape($occupation_group);
  $occupation = $db->escape($occupation);
  $state = $db->escape($state);
  $metro = $db->escape($metro);

  $state_code = "";
  if(isset($state) && !empty($state)){
    if($state != "00000"){
      $state_code = str_pad($state, 7, "0", STR_PAD_LEFT);
      $state_code = substr($state_code, 0, 2);
    }
  }

  // Occupation statistics for the occupation and metro.
  $occupation_group_code = $occupation_group;
  $sql = "select distinct occupation_name, annual_mean, employment, industry_name , annual_ten, annual_twentyfive, annual_median, annual_seventyfive, annual_ninety from industry, statemsa, employment_data, occupation where employment_data.industry_code = industry.industry_code and employment_data.occupation_code = occupation.occupation_code and areatype_code = 'M' and statemsa.msa_code = employment_data.area_code and employment_data.area_code = ".$metro." and employment_data.occupation_code = ".$occupation;
  $rows = $db->query($sql);
  $national_data = array();
  $i = 0;
  while($record = $db->fetch_array($rows)){
    $occupation_data[$i]['name'] = $record['occupation_name'];
    $occupation_data[$i]['industry_name'] = $record['industry_name'];
    $occupation_data[$i]['annual_mean'] = $record['annual_mean'];
    $occupation_data[$i]['annual_ten'] = $record['annual_ten'];
    $occupation_data[$i]['annual_twentyfive'] = $record['annual_twentyfive'];
    $occupation_data[$i]['annual_median'] = $record['annual_median'];
    $occupation_data[$i]['annual_seventyfive'] = $record['annual_seventyfive'];
    $occupation_data[$i]['annual_ninety'] = $record['annual_ninety'];
    $occupation_data[$i]['employment'] = round($record['employment']);
    $i++;
  }
  $i = 1;

  if(count($occupation_data) == 0){
    $output['graph_table']['heading'] = $GLOBALS['occupation_name']." Statistics";
    $output['graph_table']['content'] .= ERROR_MESSAGE;
  } else {
    $output['graph_table']['heading'] = $GLOBALS['occupation_name']." Statistics";
    foreach($occupation_data as $value ) {
      if($i < MAX_TO_SHOW ):
        $occupation_name = $value['name'];
        $employment_value = zero_to_na($value['employment'], "");
        $annual_mean = zero_to_na((double)$value['annual_mean'], "salary");
        $annual_ten = zero_to_na((double)$value['annual_ten'], "salary");
        $annual_twentyfive = zero_to_na((double)$value['annual_twentyfive'], "salary");
        $annual_median = zero_to_na((double)$value['annual_median'], "salary");
        $annual_seventyfive = zero_to_na((double)$value['annual_seventyfive'], "salary");
        $annual_ninety = zero_to_na((double)$value['annual_ninety'], "salary");
        $output['graph_table']['content'] .= "<div id=\"regional_statistics\"><div class=\"regional_salary\"><span class='label'>Average salary: </span>{$annual_mean}</div><div class=\"regional_jobs\"><span class='label'>Number of jobs: </span>{$employment_value}</div></div>";
        $output['graph_table']['content'] .= "<div id=\"regional_data_container\"><table class=\"dataTables\" id=\"regional_data\"><thead><tr><th></th><th>Annual 10th percentile</th><th>Annual 25th percentile</th><th>Annual 50th percentile</th><th>Annual 75th percentile</th><th>Annual 90th percentile</th></tr></thead>";
        $output['graph_table']['content'] .= "<tr><td>{$GLOBALS['metro_name']} Statistics</td><td>{$annual_ten}</td><td>{$annual_twentyfive}</td><td>{$annual_median}</td><td>{$annual_seventyfive}</td><td>{$annual_ninety}</td></tr>";
      endif;
      $i++;

    }
    //$output['graph_table']['content'] .= "</table>";
  }
  //National Occupation statistics for the occupation
  $occupation_group_code = $occupation_group;
  $sql = "select distinct occupation_name, annual_mean, employment, industry_name , annual_ten, annual_twentyfive, annual_median, annual_seventyfive, annual_ninety from industry, employment_data, occupation where employment_data.industry_code = industry.industry_code and employment_data.occupation_code = occupation.occupation_code and areatype_code = 'N' and employment_data.occupation_code = ".$occupation." and employment_data.industry_code ='000000'";
  $rows = $db->query($sql);
  $national_data = array();
  $i = 0;
  while($record = $db->fetch_array($rows)){
    $national_data[$i]['name'] = $record['occupation_name'];
    $national_data[$i]['industry_name'] = $record['industry_name'];
    $national_data[$i]['annual_mean'] = $record['annual_mean'];
    $national_data[$i]['annual_ten'] = $record['annual_ten'];
    $national_data[$i]['annual_twentyfive'] = $record['annual_twentyfive'];
    $national_data[$i]['annual_median'] = $record['annual_median'];
    $national_data[$i]['annual_seventyfive'] = $record['annual_seventyfive'];
    $national_data[$i]['annual_ninety'] = $record['annual_ninety'];
    $national_data[$i]['employment'] = round($record['employment']);
    $i++;
  }
  $i = 1;

  if(count($occupation_data) > 0){
    //$output['graph_table']['heading'] = "<h3>".$GLOBALS['occupation_name']." National Statistics </h3>";
    //$output['graph_table']['content'] .= "<table border='1'><tr><th>Occupation</th><th>Occupation average</th><th>Number of jobs</th><th>Annual 10th percentile</th><th>Annual 25th percentile</th><th>Annual 50th percentile</th><th>Annual 75th percentile</th><th>Annual 90th percentile</th></tr>";
    foreach($national_data as $value ) {
      if($i < MAX_TO_SHOW ):
        $occupation_name = $value['name'];
        $employment_value = zero_to_na($value['employment'], "");
        $annual_mean = zero_to_na((double)$value['annual_mean'], "salary");
        $annual_ten = zero_to_na((double)$value['annual_ten'], "salary");
        $annual_twentyfive = zero_to_na((double)$value['annual_twentyfive'], "salary");
        $annual_median = zero_to_na((double)$value['annual_median'], "salary");
        $annual_seventyfive = zero_to_na((double)$value['annual_seventyfive'], "salary");
        $annual_ninety = zero_to_na((double)$value['annual_ninety'], "salary");
        $output['graph_table']['content'] .= "<tr><td>National Statistics</td><td>{$annual_ten}</td><td>{$annual_twentyfive}</td><td>{$annual_median}</td><td>{$annual_seventyfive}</td><td>{$annual_ninety}</td></tr>";
      endif;
      $i++;

    }
  }
  $output['graph_table']['content'] .= "</table></div>";
  $msa_code = $metro;
  if(strlen($metro) == 5) {
    $msa_code = $state_code.$metro;
  }
  $output['graph_table']['content'] .= "<div id=\"granular_msa_code\">{$msa_code}</div>";

  // top ten occupations within the occupation group for the state
  $occupation_group_code = $occupation_group;

  if($primary_sort_by == "average") {
    $sql = "SELECT DISTINCT occupation_name, annual_mean, employment FROM statemsa, employment_data, occupation WHERE employment_data.occupation_code = occupation.occupation_code AND occugroup_code = ".$occupation_group." AND areatype_code = 'S' AND employment_data.area_code = ".$state." and employment_data.occupation_code != ".$occupation_group." ORDER BY annual_mean DESC";
  }
  if($primary_sort_by == "jobs") {
    $sql = "SELECT DISTINCT occupation_name, annual_mean, employment FROM statemsa, employment_data, occupation WHERE employment_data.occupation_code = occupation.occupation_code AND occugroup_code = ".$occupation_group." AND areatype_code = 'S' AND employment_data.area_code = ".$state." and employment_data.occupation_code != ".$occupation_group." ORDER BY employment DESC";
  }

  // Call function that runs the sql query and outputs the data in a table. for the primary data.
  $temp_output = get_primary_data($sql);
  $output['primary']['content'] = $temp_output['primary']['content'];
  $output['primary']['heading'] .= "Top occupations within ".$GLOBALS['og_name'];

  // Get top ranked industries
  if($industry_sort_by == "average") {
    $sql = "select distinct industry_name, annual_mean, employment from employment_data, industry where employment_data.industry_code = industry.industry_code and occupation_code = ".$occupation." and employment_data.industry_code != '000000' order by annual_mean DESC";
  }
  if($industry_sort_by == "jobs") {
    $sql = "select distinct industry_name, annual_mean, employment from employment_data, industry where employment_data.industry_code = industry.industry_code and occupation_code = ".$occupation." and employment_data.industry_code != '000000' order by employment DESC";
  }

  // Call function that runs the sql query and outputs the data in a table.
  $temp_output = get_secondary_data($sql);
  $output['secondary']['content'] = $temp_output['secondary']['content'];
  $output['secondary']['heading'] = "Industries that employ ".$GLOBALS['occupation_name'];

  $db->close();


  $map_output = map_svggraph_output("state",$GLOBALS['state_name']);
  $output['map'] = $map_output['map'];

  return $output;

}

?>
