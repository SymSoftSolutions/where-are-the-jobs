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
  $primary_sort_by = "average";
  $output = get_data($_GET['og'], $_GET['occupation'], $_GET['state'], $_GET['metro'], $sort_by, $_GET['industry_sort'], $primary_sort_by);
  echo "<h3>".$output['secondary']['heading']."</h3>";
  if($_GET['industry_sort'] == "average"):
    print '<button type="button" id="secondary_sort" class="average" >[ Sort by jobs ]</button>';
  endif;
  if($_GET['industry_sort'] == "jobs"):
    print '<button type="button" id="secondary_sort" class="jobs" >[ Sort by salary ]</button>';
  endif;
  echo '<table border="0" cellpadding="0" cellspacing="0" width="100%" class="scrollTable"><thead class="fixedHeader" id="fixedHeader"><tr><th class="first">Industry</th><th class="second">Industry average</th><th class="third">Number<br /> of jobs</th></tr></thead><tbody id="secondary_tbody" class="scrollContent">';
  echo $output['secondary']['content'];
  echo '</tbody></table>';
?>
