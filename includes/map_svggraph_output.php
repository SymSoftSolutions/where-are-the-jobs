<?php

function map_svggraph_output($type, $state_name) {
  $safari_fix = "" ;
  $agent = strtolower($_SERVER['HTTP_USER_AGENT']);
  preg_match("#(safari)[/ ]?([0-9.]*)#", $agent, $match);
  if (stripos($match[1], "safari") !== FALSE && stripos($agent, "chrome") === FALSE) {
    preg_match("#version/([0-9.]*)#", $agent, $vmatch);
    if (floatval($vmatch[1]) < 5.1) {
      $safari_fix = "safari/" ;
    }
  }
  
  if($type == "national") {
    $output['map'] = '<!--[if !IE]>-->
                        <object data="svg/' . $safari_fix . 'states.svg" type="image/svg+xml"
                                width="500" height="320" id="svgMap"> <!--<![endif]-->
                    <!--[if lt IE 9]>
                        <object src="svg/states.svg" classid="image/svg+xml"
                                width="500" height="320" id="svgMap"> <![endif]-->
                    <!--[if gte IE 9]>
                        <object data="svg/states.svg" type="image/svg+xml"
                                width="500" height="320" id="svgMap"> <![endif]-->
                    </object>';
    $output['svgGraph'] = '<!--[if !IE]>-->
                      <object data="svg/' . $safari_fix . 'graph-10.svg" type="image/svg+xml"
                              width="800" height="600" id="svgGraph"> <!--<![endif]-->
                  <!--[if lt IE 9]>
                      <object src="svg/graph-10.svg" classid="image/svg+xml"
                              width="800" height="600" id="svgGraph"> <![endif]-->
                  <!--[if gte IE 9]>
                      <object data="svg/graph-10.svg" type="image/svg+xml"
                              width="800" height="600" id="svgGraph"> <![endif]-->
                  </object>';
  }

  if($type == "state") {
    $svg_state_name = str_replace(" ", "_", strtolower($state_name));
    $output['map'] = '<!--[if !IE]>-->
                        <object data="svg/' . $safari_fix . 'state_'.$svg_state_name.'.svg" type="image/svg+xml"
                                width="500" height="400" id="svgMap"> <!--<![endif]-->
                    <!--[if lt IE 9]>
                        <object src="svg/state_'.$svg_state_name.'.svg" classid="image/svg+xml"
                                width="500" height="400" id="svgMap"> <![endif]-->
                    <!--[if gte IE 9]>
                        <object data="svg/state_'.$svg_state_name.'.svg" type="image/svg+xml"
                                width="500" height="400" id="svgMap"> <![endif]-->
                    </object>';
    $output['svgGraph'] = '<!--[if !IE]>-->
                      <object data="svg/' . $safari_fix . 'graph-10.svg" type="image/svg+xml"
                              width="800" height="600" id="svgGraph"> <!--<![endif]-->
                  <!--[if lt IE 9]>
                      <object src="svg/graph-10.svg" classid="image/svg+xml"
                              width="800" height="600" id="svgGraph"> <![endif]-->
                  <!--[if gte IE 9]>
                      <object data="svg/graph-10.svg" type="image/svg+xml"
                              width="800" height="600" id="svgGraph"> <![endif]-->
                  </object>';
  }
  return $output;
}
