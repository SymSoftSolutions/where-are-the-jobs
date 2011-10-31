<?php
require_once("php_mysql_class/config.inc.php");
require_once("php_mysql_class/Database.class.php");

$chosen_occupation_group_code = "";
$chosen_occupation_code = "";
$chosen_state_code = "";
$chosen_metro_code = "";

if(isset($_POST['occupation_group']) && !empty($_POST['occupation_group'])){
  $chosen_occupation_group_code = $_POST['occupation_group'];
}
if(isset($_POST['occupation']) && !empty($_POST['occupation'])){
  $chosen_occupation_code = $_POST['occupation'];
}
if(isset($_POST['state']) && !empty($_POST['state'])){
  $chosen_state_code = $_POST['state'];
}
if(isset($_POST['metro']) && !empty($_POST['metro'])){
  $chosen_metro_code = $_POST['metro'];
}
$db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->connect();

/* set the occupation group if the occupation is selected first */
if(empty($chosen_occupation_group_code) && (isset($chosen_occupation_code) && !empty($chosen_occupation_code))){
  $sql = "select distinct occugroup_code, occupation_code from employment_data where occupation_code = ".$chosen_occupation_code;
  $rows = $db->query($sql);
  if($record = $db->fetch_array($rows)) {
    $chosen_occupation_group_code = $record['occugroup_code'];
    $_POST['occupation_group'] = $chosen_occupation_group_code;
  }
}
/* End of OG selection */

/* set the state if the metro/non metro region is selected */
if(empty($chosen_state_code) && (isset($chosen_metro_code) && !empty($chosen_metro_code))){
  $sql = "SELECT area_code, area_name FROM statemsa, area WHERE area_code = concat( trim(LEADING '0'FROM state_code ) , '00000' ) AND msa_code = ".$chosen_metro_code;
  $rows = $db->query($sql);
  if($record = $db->fetch_array($rows)) {
    $chosen_state_code = $record['area_code'];
    $_POST['state'] = $chosen_state_code;
  }
}
/* End of state selection */

/* Get the list of occupation groups from DB */
$sql = "select occugroup_code, occugroup_name from occugroup order by occugroup_name";
$occupation_groups = array();
$occupation_group_list = "";
$rows = $db->query($sql);
while ($record = $db->fetch_array($rows)) {
  if(isset($chosen_occupation_group_code) && !empty($chosen_occupation_group_code)){
    if($record['occugroup_code'] == $chosen_occupation_group_code) {
      $occupation_group_list .= "<li value='".$record['occugroup_code']."' class='selected' ><span class='close'>[x]</span>".$record['occugroup_name']."</li>";
      $GLOBALS['og_name'] = $record['occugroup_name'];
    } else {
      $occupation_group_list .= "<li value='".$record['occugroup_code']."' >".$record['occugroup_name']."</li>";
    }
  } else {
    $occupation_group_list .= "<li value='".$record['occugroup_code']."' >".$record['occugroup_name']."</li>";
  }
  $occupation_group_code = $record['occugroup_code'];
  $occupation_groups[$occupation_group_code] = $record['occugroup_name'];
}

/* End of occupation groups from DB */

/* Get the occupation list from the DB */
if(isset($chosen_occupation_group_code) && !empty($chosen_occupation_group_code)) {
  $occugroup_code_start = substr($chosen_occupation_group_code,0, 2);
  $sql = "select occupation_code, occupation_name from occugroup, occupation where left(occugroup_code, 2) = left(occupation_code, 2) and left(occupation_code, 2) = ".$occugroup_code_start." order by occupation_name";
} else {
  $sql = "select occupation_code, occupation_name from occupation where occupation_code != '000000' order by occupation_name";
}
$occupations = ""; 
$occupations_list = array();
$rows = $db->query($sql);
while ($record = $db->fetch_array($rows)) {
  if(isset($chosen_occupation_code) && !empty($chosen_occupation_code)){
    if($record['occupation_code'] == $_POST['occupation']) {
      $occupations .= "<li value='".$record['occupation_code']."' class= 'selected' ><span class=\"close\">[x]</span>".$record['occupation_name']."</li>";
      $GLOBALS['occupation_name'] = $record['occupation_name'];
    } else {
      $occupations .= "<li value='".$record['occupation_code']."' >".$record['occupation_name']."</li>";
    }
  } else {
    $occupations .= "<li value='".$record['occupation_code']."' >".$record['occupation_name']."</li>";
  }
  $occupation_code = $record['occupation_code'];
  $occupations_list[$occupation_code] = $record['occupation_name'];
}

/* Get the occupation title if it is avaible in the table */
$occ_definition = "";
$occ_code = substr($chosen_occupation_code, 0,2)."-".substr($chosen_occupation_code, -4);
$sql = "select def from occupation_definitions where occ_code = \"".$occ_code."\"";
$row = $db->query($sql);
if($record = $db->fetch_array($row)) {
  $occ_definition = $record['def'];
}
/* end of occupation definition */

/* End of occupation list */

/* Get the states from the Database */
$sql = "select area_code, area_name from area where areatype_code = 'S' and area_code != '6600000' and area_code != '7200000' and area_code != '7800000'";
$states_array = array();
$rows = $db->query($sql);
while ($record = $db->fetch_array($rows)) {
  if(isset($chosen_state_code) && !empty($chosen_state_code)){
    if($record['area_code'] == $chosen_state_code) {
      $states .= "<li value='".$record['area_code']."' class='selected' ><span class='close'>[x]</span>".$record['area_name']."</li>";
      $GLOBALS['state_name'] = $record['area_name'];
    } else {
      $states .= "<li value='".$record['area_code']."' >".$record['area_name']."</li>";
    }
  } else {
    $states .= "<li value='".$record['area_code']."' >".$record['area_name']."</li>";
  }
  $state_code = $record['area_code'];
  $states_array[$state_code] = $record['area_name'];
}
/* End of states list */

/* Get the metro+non metro areas from the DB based on the state */
/* Get two digit state code from the area code */

if(isset($chosen_state_code) && !empty($chosen_state_code)){
  if($chosen_state_code != "00000"){
    $state_code = str_pad($chosen_state_code, 7, "0", STR_PAD_LEFT);
    $state_code = substr($state_code, 0, 2);
  }
 }

$metro_areas = "";
$metros = array();

if(isset($state_code)){
  $sql = "select msa_code, msa_name from statemsa where state_code = ".$state_code;
}
if(empty($chosen_state_code)){
  $sql = "select msa_code, msa_name from statemsa where state_code != '66' and state_code != '72' and state_code != '78'";
}
$rows = $db->query($sql);
while ($record = $db->fetch_array($rows)) {
  if(isset($chosen_metro_code) && !empty($chosen_metro_code)){
    if($record['msa_code'] == $chosen_metro_code) {
      $metro_areas .= "<li value='".$record['msa_code']."' class='selected' ><span class='close'>[x]</span>".$record['msa_name']."</li>";
      $GLOBALS['metro_name'] = $record['msa_name'];
    } else {
      $metro_areas .= "<li value='".$record['msa_code']."' >".$record['msa_name']."</li>";
    }
  } else {
    $metro_areas .= "<li value='".$record['msa_code']."' >".$record['msa_name']."</li>";
  }
  $metro_code = $record['msa_code'];
  $metros[$metro_code] = $record['msa_name'];
}
$metro_area_list .= "</ul>";
/* End of metro + non metro list */
$db->close();

?>

      <div id="header">
	<h1>WHERE ARE THE JOBS?</h1>
        <h2 class="tagline">A Graphic Representation of occupation employment statistics</h2>
        <div id="subheader"><h2><a href="http://www.symsoftsolutions.com"><span class="tag_line" style="color:#fa0; text-decoration:none;">developed by</span></a><span class="credit">Data Provided <br />by <span style="color:#fa0">US Department <br />of Labor</span></span></h2></div>
      </div>
      <div id="nav">
      <form id="app_form" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
	 <ul class="occupationGroup">
	   <li class="title">Occupation Group</li>
           <li>
             <input id = "og_text" type="text" name="" placeholder="Enter the occupation group" />
           </li>
	   <li class="listContainer">
	     <ul id="occupation_group_list" class="list">
	       <?php print $occupation_group_list; ?>
	     </ul>
	   </li>
	</ul>
	<ul class="occupation">
           <li class="title">Occupation</li>
           <li>
             <input id = "occupation_text" type="text" name="" placeholder="Enter the occupation" />
           </li>
	   <li class="listContainer">
	     <ul id="occupation_list" class="list">
	       <?php print $occupations; ?>
	     </ul>
	   </li>
	</ul>
	<ul class="state">
           <li class="title">State</li>
           <li>
             <input id = "state_text" type="text" name="" placeholder="Enter the state" />
           </li>
	   <li class="listContainer">
	     <ul id="states_list" class="list">
	       <?php print $states; ?>
	     </ul>
	   </li>
	</ul>
	<ul class="region">
           <li class="title">Region</li>
           <li>
             <input id = "region_text" type="text" name="" placeholder="Enter the region" />
           </li>
	   <li class="listContainer">
	     <ul id="metros_list" class="list">
	       <?php print $metro_areas; ?>
	     </ul>
	   </li>
	 </ul>
	 <?php 
	   if(isset($GLOBALS['sort_by']) && !empty($GLOBALS['sort_by'])){
	     echo '<input type="hidden" name="sort" id="sort" value="'.$GLOBALS['sort_by'].'"  />';
	   } else {
	     echo '<input type="hidden" name="sort" id="sort" value=""  />';
	   }
	   if(isset($GLOBALS['industry_sort_by']) && !empty($GLOBALS['industry_sort_by'])){
	     echo '<input type="hidden" name="industry_sort" id="industry_sort" value="'.$GLOBALS['industry_sort_by'].'"  />';
	   } else {
	     echo '<input type="hidden" name="industry_sort" id="industry_sort" value=""  />';
	   }
	   if(isset($GLOBALS['primary_sort_by']) && !empty($GLOBALS['primary_sort_by'])){
	     echo '<input type="hidden" name="primary_sort" id="primary_sort" value="'.$GLOBALS['primary_sort_by'].'"  />';
	   } else {
	     echo '<input type="hidden" name="primary_sort" id="primary_sort" value=""  />';
	   }
	 ?>
	 <?php 
	   if(isset($chosen_occupation_group_code) && !empty($chosen_occupation_group_code)){
	     echo '<input type="hidden" name="occupation_group" id="occupation_group" value="'.$chosen_occupation_group_code.'" />';
	   } else {
	     echo '<input type="hidden" name="occupation_group" id="occupation_group" value="0" />';
	   }
	   if(isset($chosen_occupation_code) && !empty($chosen_occupation_code)){
             echo '<input type="hidden" name="occupation" id="occupation" value="'.$chosen_occupation_code.'"/>';
	   } else {
	     echo '<input type="hidden" name="occupation" id="occupation" value="0"/>';
	   }
	   if(isset($chosen_state_code) && !empty($chosen_state_code)){
	     echo '<input type="hidden" name="state" id="state" value="'.$chosen_state_code.'"/>';
	   } else {
	     echo '<input type="hidden" name="state" id="state" value="0"/>';
	   }
	   if(isset($chosen_metro_code) && !empty($chosen_metro_code)){
             echo '<input type="hidden" name="metro" id="metro" value="'.$chosen_metro_code.'"/>';
	   } else {
	     echo '<input type="hidden" name="metro" id="metro" value="0" />';
	   }
	 ?>
     </form>
     </div>
     <div style="clear: both;" ></div>
