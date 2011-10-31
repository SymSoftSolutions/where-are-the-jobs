<?php
include_once("includes/get_data.php");
include_once("includes/parameter_validator.php");

function get_data($occupation_group, $occupation, $state, $metro, $sort_by, $industry_sort_by, $primary_sort_by){
  if(empty($occupation_group) && empty($occupation) && empty($state) && empty($metro)) {
    if(parameter_validator($occupation_group) && parameter_validator($occupation) && parameter_validator($state) && parameter_validator($metro)) {
      return default_case($sort_by, $industry_sort_by, $primary_sort_by);
    }
  }

  if(!empty($occupation_group) && empty($occupation) && empty($state) && empty($metro)) {
    if(parameter_validator($occupation_group) && parameter_validator($occupation) && parameter_validator($state) && parameter_validator($metro)) {
      return only_occupation_group($occupation_group, $sort_by, $industry_sort_by, $primary_sort_by);
    }
  }

  if(isset($occupation_group) && !empty($occupation) && empty($state) && empty($metro)) {
    if(parameter_validator($occupation_group) && parameter_validator($occupation) && parameter_validator($state) && parameter_validator($metro)) {
      return only_occupation($occupation_group, $occupation, $sort_by, $industry_sort_by, $primary_sort_by);
    }
  }

  if(empty($occupation_group) && empty($occupation) && !empty($state) && empty($metro)) {
    if(parameter_validator($occupation_group) && parameter_validator($occupation) && parameter_validator($state) && parameter_validator($metro)) {
      return only_state($state, $sort_by, $industry_sort_by, $primary_sort_by);
    }
  }

  if(empty($occupation_group) && empty($occupation) && isset($state) && !empty($metro)) {
    if(parameter_validator($occupation_group) && parameter_validator($occupation) && parameter_validator($state) && parameter_validator($metro)) {
      return only_metro($state, $metro, $sort_by, $industry_sort_by, $primary_sort_by);
    }
  }

  if(!empty($occupation_group) && !empty($occupation) && empty($state) && empty($metro)) {
    if(parameter_validator($occupation_group) && parameter_validator($occupation) && parameter_validator($state) && parameter_validator($metro)) {
      return only_occupation($occupation_group, $occupation, $sort_by, $industry_sort_by, $primary_sort_by);
    }
  }

  if(!empty($occupation_group) && empty($occupation) && !empty($state) && empty($metro)) {
    if(parameter_validator($occupation_group) && parameter_validator($occupation) && parameter_validator($state) && parameter_validator($metro)) {
      return og_state($occupation_group, $state, $sort_by, $industry_sort_by, $primary_sort_by);
    }
  }

  if(isset($occupation_group) && !empty($occupation) && !empty($state) && empty($metro)) {
    if(parameter_validator($occupation_group) && parameter_validator($occupation) && parameter_validator($state) && parameter_validator($metro)) {
      return occupation_state($occupation_group, $occupation, $state, $sort_by, $industry_sort_by, $primary_sort_by);
    }
  }

  if(!empty($occupation_group) && empty($occupation) && !empty($state) && !empty($metro)) {
    if(parameter_validator($occupation_group) && parameter_validator($occupation) && parameter_validator($state) && parameter_validator($metro)) {
      return og_metro($occupation_group, $state, $metro, $sort_by, $industry_sort_by, $primary_sort_by);
    }
  }
  
  if(isset($occupation_group) && !empty($occupation) && isset($state) && !empty($metro)) {
    if(parameter_validator($occupation_group) && parameter_validator($occupation) && parameter_validator($state) && parameter_validator($metro)) {
      return occupation_metro($occupation_group, $occupation, $state, $metro, $sort_by, $industry_sort_by, $primary_sort_by);
    }
  }
}
?>
