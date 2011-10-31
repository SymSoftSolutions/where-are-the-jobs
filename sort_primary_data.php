<?php
  include_once "processing.php";
  require_once("php_mysql_class/config.inc.php");
  require_once("php_mysql_class/Database.class.php");

  $db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
  $db->connect();

  if(parameter_validator($_GET['og'])) {
    $sql = "select occupation_name from occupation where occupation_code = ".$db->escape($_GET['og']);
    $rows = $db->query($sql);
    if($record = $db->fetch_array($rows)){
      $GLOBALS['og_name'] = $record['occupation_name'];
    }
  }

  if(parameter_validator($_GET['occupation'])) {
    $sql = "select occupation_name from occupation where occupation_code = ".$db->escape($_GET['occupation']);
    $rows = $db->query($sql);
    if($record = $db->fetch_array($rows)){
      $GLOBALS['occupation_name'] = $record['occupation_name'];
    }
  }

  $sort_by = "average";
  $industry_sort_by = "average";

  $output = get_data($_GET['og'], $_GET['occupation'], $_GET['state'], $_GET['metro'], $sort_by, $industry_sort_by, $_GET['primary_sort']);
  echo "<h3>".$output['primary']['heading']."</h3>";
  if($_GET['primary_sort'] == "average"):
    print '<button type="button" id="primary_sort" class="average" >[ Sort by jobs ]</button>';
  endif;
  if($_GET['primary_sort'] == "jobs"):
    print '<button type="button" id="primary_sort" class="jobs" >[ Sort by salary ]</button>';
  endif;
  echo '<table border="0" cellpadding="0" cellspacing="0" width="100%" class="scrollTable"><thead class="fixedHeader" id="fixedHeader"><tr><th class="first">Occupation</th><th class="second">Occupation average</th><th class="third">Number<br /> of jobs</th></tr></thead><tbody id="primary_tbody" class="scrollContent">';
  echo $output['primary']['content'];
  echo '</tbody></table>';
?>
