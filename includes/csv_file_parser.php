<?php
/* Function to get the contents from the interesting_facts.csv file.
 * Parse the contents and construct it into an associative array.
 */ 
function get_facts_from_csv(){
  $row = 0;
  $data = array();;
  $facts = array();
  if (($handle = fopen("interesting_facts/interesting_facts.csv", "r")) !== FALSE) {
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
      $num = count($data);
      for ($c=0; $c < $num; $c++) {
        $facts[$row][$c] = $data[$c];
      }
      $row++;
    }
    fclose($handle);
  }

  $new_facts = array();
  
  $j = 0;
  $i = 0;
  foreach($facts as $value) {
    if($j < count($facts) && $j > 0) {
      $i = 0;
      foreach($value as $sub_value){
        if($i < count($facts[0]) ){
          $column_name = $facts[0][$i];
          $new_facts[$j-1][$column_name] = $sub_value;
	  $i++;
        }
      }
    }
    $j++;
  }
  return $new_facts;
}
?>
