<?php
  session_start();
  if($_SESSION['disclaimer_viewed'] != "yes")  header("Location:index.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <!--[if IE]>
  <meta http-equiv="X-UA-Compatible" content="IE=8" />
  <![endif]-->
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="Cache-control" content="private" />
  
  <title>Where are the jobs?</title>
  
  <link rel="stylesheet" type="text/css" href="css/style.css">
 
  <!--[if lt IE 9]>
    <link rel="stylesheet" type="text/css" href="css/ie-fix.css" />
  <![endif]-->

  <script src="svg/svg.js" type="text/javascript"></script>
 
  <!--[if !IE]>-->
    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/jquery.tablescroll.js" type="text/javascript"></script>
    <script src="js/scripts.js" type="text/javascript"></script> 
  <!--<![endif]-->

  <!--[if gte IE 9]>
    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/jquery.tablescroll.js" type="text/javascript"></script>
    <script src="js/scripts.js" type="text/javascript"></script>
  <![endif]-->
</head>
<body>

<?php
  $sort_by = "average";
  if(isset($_POST['sort']) && !empty($_POST['sort'])) $sort_by = $_POST['sort'];
  if(isset($GLOBALS['sort_by']) && !empty($GLOBALS['sort_by'])) $sort_by = $GLOBALS['sort_by'];
  
  $industry_sort_by = "average";
  if(isset($_POST['industry_sort'])  && !empty($_POST['industry_sort'])) $industry_sort_by = $_POST['industry_sort'];
  if(isset($GLOBALS['industry_sort_by']) && !empty($GLOBALS['industry_sort_by'])) $industry_sort_by = $GLOBALS['industry_sort_by'];

  $primary_sort_by = "average";
  if(isset($_POST['primary_sort'])  && !empty($_POST['primary_sort'])) $primary_sort_by = $_POST['primary_sort'];
  if(isset($GLOBALS['primary_sort_by']) && !empty($GLOBALS['primary_sort_by'])) $primary_sort_by = $GLOBALS['primary_sort_by'];

  $GLOBALS['industry_sort_by'] = $industry_sort_by;
  $GLOBALS['primary_sort_by'] = $primary_sort_by;
  $GLOBALS['sort_by'] = $sort_by;
?>

<?php include_once "includes/header.inc.php"; ?>

<?php
/* Get data according to the parameters selected - */ 
  include_once "processing.php";
  
  $occupation_group = $_POST['occupation_group'];
  $occupation = $_POST['occupation'];
  $state = $_POST['state'];
  $metro = $_POST['metro'];
  $GLOBALS['occupation_group'] = $_POST['occupation_group'];
  $GLOBALS['occupation'] = $_POST['occupation'];
  $GLOBALS['state'] = $_POST['state'];
  $GOBALS['metro'] = $_POST['metro'];
  $output = "";
  
  // Processing function to get the data from the database
  $output = get_data($occupation_group, $occupation, $state, $metro, $sort_by, $industry_sort_by, $primary_sort_by);

/* End of getting data */
?>

<div id="main">
  <div id="breadcrumb">
    <?php if(isset($chosen_occupation_group_code) && !empty($chosen_occupation_group_code)) echo "<span>".$occupation_groups[$chosen_occupation_group_code]."</span>"; ?>
    <?php if(isset($chosen_occupation_code) && !empty($chosen_occupation_code)) echo "<span>".$occupations_list[$chosen_occupation_code]."</span>"; ?>
    <?php if(isset($chosen_state_code) && !empty($chosen_state_code)) echo "<span>".$states_array[$chosen_state_code]."</span>"; ?>
    <?php
      if(isset($chosen_metro_code) && !empty($chosen_metro_code)) {
        $chosen_metro_code = str_pad($chosen_metro_code, 7, "0", STR_PAD_LEFT);
        echo "<span>".$metros[$chosen_metro_code]."</span>";
      }
    ?>
  </div>
    
    
  <div id="console"></div>
        
  <?php 
    if(isset($output['graph_table']['heading']) && !empty($output['graph_table']['heading'])) {
      print "<h2>".$output['graph_table']['heading'];
      if(isset($occ_definition) && !empty($occ_definition)) {
        print "<span id=\"occupation_definition_icon\"></span>";
      }
      print "</h2>"; 
    }
  ?>
  <?php
    if(isset($occ_definition) && !empty($occ_definition)) {
      echo "<div id=\"occupation_definition\">";
      echo "<h3>".$GLOBALS['occupation_name']."</h3>";
      echo $occ_definition;
      echo "</div>";
    }
  ?>

  <div id="max_min_numbers">
    Max and min statistics for jobs and salary:
    <?php if(isset($output['graph_table']['jobs']) && !empty($output['graph_table']['jobs'])) print '<div id="min_jobs">'.$output['graph_table']['jobs']['min'].'</div><div id="max_jobs">'.$output['graph_table']['jobs']['max'].'</div>'; ?>
    <?php if(isset($output['graph_table']['salary']) && !empty($output['graph_table']['salary'])) print '<div id="min_salary">'.$output['graph_table']['salary']['min'].'</div><div id="max_salary">'.$output['graph_table']['salary']['max'].'</div>'; ?>
  </div>
  <div id="content">
    <div id="data">
       <?php 
         if(isset($output['primary']['state']['content']) && !empty($output['primary']['state']['content'])) {
           print '<div id="state_data">';
           print $output['primary']['state']['content'];
           print '</div>'; 
           print '<div style=\'clear: both;\'></div>'; 
         }
       ?>
       <div id="graphContainer" <?php if(!empty($occupation) && !empty($metro)) echo 'class="granular"'; ?> >
         
	 <!-- Embed the map, svGraph and the data table to be used by the svgGraph here. -->              
         <?php if(isset($output['map']) && !empty($output['map'])) print $output['map']; ?>
         <?php if(isset($output['svgGraph']) && !empty($output['svgGraph'])) print $output['svgGraph']; ?>
         <?php if(isset($output['graph_table']['content']) && !empty($output['graph_table']['content'])) print $output['graph_table']['content']; ?>
            
         <div id="info"></div>
           <div id="key">
             <div class="inner">
               <div class="max"></div>
               <div class="min"></div>
             </div>
         </div>
         
         <div id="tooltip">
         	<div id="tooltip_inner"> 
                <img class="notch" src="img/notch.png" alt="" />
                <h2 id="tooltip_title"></h2>
                <h3><span>Salary: </span><span id="tooltip_salary"></span></h3>
                <h3><span>Number of Jobs: </span><span id="tooltip_jobs"></span></h3>
            </div>
		</div>

         <?php if($sort_by == "average"): ?> 
           <button type="button" id="graph_sort" class="average" value="average">[ Sort by jobs ]</button> 
         <?php endif; ?>
         <?php if($sort_by == "jobs"): ?> 
           <button type="button" id="graph_sort" class="jobs" value="jobs">[ Sort by salary ]</button> 
         <?php endif; ?>
      </div>

      <?php
        if(!isset($output['primary']['content']) || empty($output['primary']['content'])) {
          echo '<div id="data_sub_container" class="single">';
        } else {
          echo '<div id="data_sub_container">';
        }
      ?>
  
        <?php
/* 
          if(isset($output['primary']['content']) && !empty($output['primary']['content'])) {
            print '<div id="primary_data" class="dataTables">';
	    print $output['primary']['heading'];
	    print '<button type="button" id="primary_sort" class="average" value="average">[ Sort by jobs ]</button>';
            echo '<table border="1"><thead><tr><th class="first">Occupation</th><th class="second">Occupation average</th><th class="third">Number<br /> of jobs</th></tr></thead>';
	    print $output['primary']['content'];
	    print '</div>'; 
          }*/
        ?>
        <?php if(isset($output['primary']['content']) && !empty($output['primary']['content'])): ?>
          <div id="primary_data" class="tableContainer">
            <?php if(isset($output['primary']['heading']) && !empty($output['primary']['heading'])) print '<h3>'.$output['primary']['heading'].'</h3>'; ?>
            <button type="button" class="average" id="primary_sort" value="jobs">[ Sort by jobs ]</button>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="scrollTable">
              <thead class="fixedHeader" id="fixedHeader"><tr><th class="first">Occupation</th><th class="second">Occupation average</th><th class="third">Number<br /> of jobs</th></tr></thead>
              <tbody id="primary_tbody" class="scrollContent">
                <?php if(isset($output['primary']['content']) && !empty($output['primary']['content'])) print $output['primary']['content']; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>

	<?php if(isset($output['secondary']['content']) && !empty($output['secondary']['content'])): ?>
	  <div id="secondary_data" class="tableContainer"> 
	    <?php if(isset($output['secondary']['heading']) && !empty($output['secondary']['heading'])) print '<h3>'.$output['secondary']['heading'].'</h3>'; ?>
	    <button type="button" class="average" id="secondary_sort" value="jobs">[ Sort by jobs ]</button>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="scrollTable">
	      <thead class="fixedHeader" id="fixedHeader"><tr><th class="first">Industry</th><th class="second">Industry average</th><th class="third">Number<br /> of jobs</th></tr></thead>
	      <tbody id="secondary_tbody" class="scrollContent"> 
		<?php if(isset($output['secondary']['content']) && !empty($output['secondary']['content'])) print $output['secondary']['content']; ?>
	      </tbody>
	    </table>
          </div>
	<?php endif; ?>
	
	<div style="clear:both;"></div>
	
        </div> <!-- End of enclosing div for primary and secondary data -->

      </div>

    </div>

</div>

<div id="page_footer">
<span class="disclaimer">The application is based on data published by US Department of Labor in May 2010. 
<div id="copyright">
&copy; <?php echo date('Y') ; ?> <a href="http://www.symsoftsolutions.com">SymSoft Solutions, LLC</a> 
</div>
<div class="pre_browser">Best viewed using <span class="orange">Firebox, Chrome or Safari 5.1+ </span></div></span>
</div>

<div style="clear: both; padding-bottom: 2em;">&nbsp;</div>

<!--[if lt IE 9]>
    <script src="js/jquery.js" type="text/javascript"></script>
    <script src="js/jquery.tablescroll.js" type="text/javascript"></script>
    <script src="js/scripts.js" type="text/javascript"></script>
<![endif]-->
</body>
</html>
