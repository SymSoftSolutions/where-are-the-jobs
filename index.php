<?php 
  session_start();
  $_SESSION['disclaimer_viewed'] = "yes"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Cache-control" content="private" />
<title>Where are the jobs?</title>
<link type="text/css" href="css/style.css" rel="stylesheet" />
<style type="text/css">
body {
  background: #000 url('img/DOLdataviz-0start.png') ;
  background-repeat:no-repeat;
  background-position:center top;
}
</style>
</head>
<body>

<?php
  include_once("includes/csv_file_parser.php");
  /* Get the facts that have to be displayed from the csv file into an array */
  $random_facts = array();
  $random_facts = get_facts_from_csv();
?>

<div class="disclaimer_box">
<div class="disclaimer_box_inner">
<form id="app_form" action="app.php" method="post">
<div id="fact_statement">
  <h2>
  <?php
    $i = rand(0, (count($random_facts)-1));
    echo $random_facts[$i]['text'];
    if(isset($random_facts[$i]['occupation']) && !empty($random_facts[$i]['occupation'])) {
      echo '<input type="hidden" name="occupation" id="occupation" value="'.$random_facts[$i]['occupation'].'"/>';
    }
    if(isset($random_facts[$i]['occupation_group']) && !empty($random_facts[$i]['occupation_group'])) {
      echo '<input type="hidden" name="occupation_group" id="occupation_group" value="'.$random_facts[$i]['occupation_group'].'"/>';
    }
    if(isset($random_facts[$i]['state']) && !empty($random_facts[$i]['state'])) {
      echo '<input type="hidden" name="state" id="state" value="'.$random_facts[$i]['state'].'"/>';
    }
    if(isset($random_facts[$i]['metro']) && !empty($random_facts[$i]['metro'])) {
      echo '<input type="hidden" name="metro" id="metro" value="'.$random_facts[$i]['metro'].'"/>';
    }
    if(isset($random_facts[$i]['sort_by']) && !empty($random_facts[$i]['sort_by'])) {
      echo '<input type="hidden" name="sort" id="sort" value="'.$random_facts[$i]['sort_by'].'"/>';
    }
  ?>
  </h2>
</div>
<div id="disclaimer_text">
To know more such interesting details please click below after reading the disclaimer.<br/><br/>
THE MATERIAL EMBODIED IN THIS SOFTWARE IS PROVIDED TO YOU "AS-IS" AND WITHOUT WARRANTY OF ANY KIND, EXPRESS, IMPLIED, OR OTHERWISE, INCLUDING WITHOUT LIMITATION, ANY WARRANTY OF FITNESS FOR A PARTICULAR PURPOSE. IN NO EVENT SHALL THE UNITED STATES DEPARTMENT OF LABOR (DOL) OR THE UNITED STATES GOVERNMENT BE LIABLE TO YOU OR ANYONE ELSE FOR ANY DIRECT, SPECIAL, INCIDENTAL, INDIRECT, OR CONSEQUENTIAL DAMAGES OF ANY KIND, OR ANY DAMAGES WHATSOEVER, INCLUDING WITHOUT LIMITATION, LOSS OF PROFIT, LOSS OF USE, SAVINGS OR REVENUE, OR THE CLAIMS OF THIRD PARTIES, WHETHER OR NOT DOL OR THE U.S. GOVERNMENT HAS BEEN ADVISED OF THE POSSIBILITY OF SUCH LOSS, HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, ARISING OUT OF OR IN CONNECTION WITH THE POSSESSION, USE, OR PERFORMANCE OF THIS SOFTWARE.
</div>
<div id="disclaimer_accept_button">
   <input type="submit" value="Accept" />
</div>
</form>
</div>
</div>
</body>
</html>
