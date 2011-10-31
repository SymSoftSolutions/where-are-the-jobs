function getUrl() {
  var appUrl = document.location.href;
  var partUrl = appUrl.replace("/app.php", "");
  return partUrl;
}

function getOccupations(og, occupation, occupation_string) {
  if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else {// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      document.getElementById("occupation_list").innerHTML=xmlhttp.responseText;
    }
  }
  var url = getUrl() + "/includes/getoccupation.php?occupation_partial_string=" + occupation_string + "&og=" + og + "&occupation=" + occupation;
  xmlhttp.open("GET", url, true);
  xmlhttp.send();
  return false;
}

function getMetros(state, metro, metro_partial_string) {
  if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();
  } else {// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  }
  
  xmlhttp.onreadystatechange=function() {
    if (xmlhttp.readyState==4 && xmlhttp.status==200) {
      document.getElementById("metros_list").innerHTML=xmlhttp.responseText;
    }
  }
  
  var url = getUrl() + "/includes/getmetros.php?metro_partial_string=" + metro_partial_string + "&state=" + state + "&metro=" + metro;
  xmlhttp.open("GET", url, true);
  xmlhttp.send();
}


function submit() {
  document.forms["app_form"].submit();
}



$(window).bind("load", function() {
	//$('body').append('<div id="postConsole"></div>');
	
	//sort button functionality
	function sort_button_functionality(){
	  var og = $('#occupation_group').val();
	  var occupation = $('#occupation').val();
	  var state = $('#state').val();
	  var metro = $('#metro').val();
	  $('#primary_sort').live('click', function () {
		var class_name = $(this).attr('class');
		var primary_sort = $(this).attr('value');
		if(class_name == "average") {
		  primary_sort = "jobs";
		  $(this).attr('class', 'jobs');  
		}
		if(class_name == "jobs") {
		  primary_sort = "average";
		  $(this).attr('class', 'average');  
		}
		get_primary_data(og, occupation, state, metro,primary_sort);
	  });
	  $('#secondary_sort').live('click', function () {
		var class_name = $(this).attr('class');
		var industry_sort = $(this).attr('value');
		if(class_name == "average") {
		  industry_sort = "jobs";
		  $(this).attr('class', 'jobs');
		}
		if(class_name == "jobs") {
		  industry_sort = "average";
		  $(this).attr('class', 'average');
		}
		get_secondary_data(og, occupation, state, metro, industry_sort);
	  });
	  $('#graph_sort').live('click', function () {
		if($(this).attr('value') == "average") {
		  $('#sort').val("jobs");
		}
		if($(this).attr('value') == "jobs") {
		  $('#sort').val("average");
		}
		submit();
	  });
	}
	
	// custom css expression for a case-insensitive contains()
	jQuery.expr[':'].Contains = function(a,i,m){
		return (a.textContent || a.innerText || "").toUpperCase().indexOf(m[3].toUpperCase())>=0;
	 };
	 
	var delay = (function(){
		var timer = 0;
		return function(callback, ms){
		 	clearTimeout (timer);
		 	timer = setTimeout(callback, ms);
		};
	})();
	 
	//input to filter select lists
	function listFilter(input, list) { // header is any element, list is an unordered list  
		var matches = ""; 
		$(input)
		  .change( function () {
			var filter = $(this).val();
			//if(filter && filter.length >= 3) {
			if(filter) {
			  // this finds all links in a list that contain the input,
			  // and hide the ones not containing the input while showing the ones that do
			  $(list).find("li:not(:Contains(" + filter + "))").slideUp('fast');
			  $(list).find("li:Contains(" + filter + ")").slideDown('fast');
			} else {
			  $(list).find("li").slideDown();
			}
			return false;
		  })
		.keyup( function () {
			// fire the above change event after every letter
		//delay(function(){ $(this).change(); }, 500);
			$(this).change();
		});
	}						
	
	//table sor ajax functions
	function get_primary_data(og, occupation, state, metro, primary_sort){
	  if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	  } else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	  xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
		  document.getElementById("primary_data").innerHTML=xmlhttp.responseText;
		  enableTableScroll('#primary_data.tableContainer');
		}
	  }
	  var url = "http://" + document.domain + "/sort_primary_data.php?og=" + og + "&occupation=" + occupation + "&state=" + state + "&metro=" + metro + "&primary_sort=" + primary_sort;
	  xmlhttp.open("GET", url, true);
	  xmlhttp.send();
	}
	
	function get_secondary_data(og, occupation, state, metro, industry_sort){
	  if (window.XMLHttpRequest){// code for IE7+, Firefox, Chrome, Opera, Safari
		xmlhttp=new XMLHttpRequest();
	  } else {// code for IE6, IE5
		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	  }
	  xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
		  document.getElementById("secondary_data").innerHTML=xmlhttp.responseText;
		  enableTableScroll('#secondary_data.tableContainer');
		}
	  }
	  var url = "http://" + document.domain + "/sort_secondary_data.php?og=" + og + "&occupation=" + occupation + "&state=" + state + "&metro=" + metro + "&industry_sort=" + industry_sort;
	  xmlhttp.open("GET", url, true);
	  xmlhttp.send();
	  return false;
	}
								
	//"Sort By" button functionality - local to all the tables
	sort_button_functionality();
						   
	//Select list submit functions			   
	$('#occupation_group_list li:not(.selected)').click(function () {
			$('#occupation_group').val($(this).attr('value'));
			$('#occupation').val("");
			submit();
	});
	$('#occupation_list li:not(.selected)').live('click', function () {
			$('#occupation').val($(this).attr('value'));
			submit();
	});
	$('#states_list li:not(.selected)').click(function () {
			$('#state').val($(this).attr('value'));
			$('#metro').val("");
			submit();
	});
	$('#metros_list li:not(.selected)').live('click', function () {
			$('#metro').val($(this).attr('value'));
			submit();
	});
	
	//highlight nav items
	$('#nav form > ul').hover(function () {$(this).addClass('highlight')},function () {$(this).removeClass('highlight')});
	$('#nav form > ul li input').focus(function(){$(this).addClass('highlight')});
	$('#nav form > ul li input').blur(function(){$(this).removeClass('highlight')});
	
	$('#occupation_group_list .close').click(function () {
			$('#occupation_group').val("");
			$('#occupation').val("");
			submit();
	});
	$('#occupation_list .close').live('click', function () {
			$('#occupation').val("");
			submit();
	});
	$('#states_list .close').click(function () {
			$('#state').val("");
			$('#metro').val("");
			submit();
	});
	$('#metros_list .close').live('click', function () {
			$('#metro').val("");
			submit();
	});
	//$('#nav form > ul li.selected .close').click(function() {alert('close');});
	
	
	//Select list scroll to active element
	if($("#occupation_group_list li.selected").length)
	{
		$('#occupation_group_list').scrollTop($('#occupation_group_list li.selected:eq(0)').offset().top - $('#occupation_group_list').offset().top);
	}
	if($("#occupation_list li.selected").length)
	{
		$('#occupation_list').scrollTop($('#occupation_list li.selected:eq(0)').offset().top - $('#occupation_list').offset().top);
	}
	
	if($("#states_list li.selected").length)
	{
		$('#states_list').scrollTop($('#states_list li.selected:eq(0)').offset().top - $('#states_list').offset().top);
	}
	
	if($("#metros_list li.selected").length)
	{
		$('#metros_list').scrollTop($('#metros_list li.selected:eq(0)').offset().top - $('#metros_list').offset().top);
	}
	
	//add placeholder text to nav input boxes
	$('#nav form > ul li input').each(function() {
		$(this).val($(this).attr('placeholder'));
		$(this).focus(function(){
			if($(this).val() == $(this).attr('placeholder'))
			{
				$(this).val('');
			}
		});
	});
	
	//input box to filter select lists
	listFilter($("#og_text"), $("#occupation_group_list"));
	//listFilter($("#occupation_text"), $("#occupation_list"));
	listFilter($("#state_text"), $("#states_list"));
	//listFilter($("#region_text"), $("#metros_list"));
						   
	var gOriginX = 80; 	
	var gOriginY = 60; 		//starting position of the graph from bottom left of svg
	var gSpacing = 25; 		//spacing bewteen nodes in graph
	//var aStep = 4; 		//how far objects move with each animation cycle
	//var aSpeed = 40; 		//animation speed;
	var numVisible = 10; 	//number of nodes to show at one time
	var canvasHeight = 600;	//height of svg graph
	var barHeight = 288;	//max height of bar in graph
	var highlightColor = '#8cc63f';
	var hoverColor = '#3fc66f';
	
	var svgDoc; 			//var for svg object
	var points;				//array of graph bars 

	var connectMap = false;	//links graph to map
	if($('#svgMap').length && $('#graphData tr:eq(0) th:eq(3)').html() != null){connectMap = true;}
	
	var selectedNode = 0; 	//keep track of the selected node
	
	var nodeCount = $('#graphData tr:not(:eq(0))').size();
	if(numVisible > nodeCount) {numVisible = nodeCount} //limit data points to amount of data returned
	
	//Find max and min of salary and jobs
	var calcSalary = [];
	var calcJobs = [];
	
	var sortedBy = $('#sort').val();
	
	$('#graphData tr:not(:eq(0))').each(function () {
		var i = $(this).index()-1;
		var curSalary = dollarsToInt($(this).find('td:eq(1)').html());
		if(!isNaN(curSalary)) {
			calcSalary.push(curSalary)
		}
		var curJob = parseInt(cleanNumber($(this).find('td:eq(2)').html()));
		if(!isNaN(curJob)) {
			calcJobs.push(curJob);
		}
	});
	
	$('#graphData').hide();
	
	salaryMax = Math.max.apply(Math, calcSalary);
	salaryMin = Math.min.apply(Math, calcSalary);
	jobsMax = Math.max.apply(Math, calcJobs);
	jobsMin = Math.min.apply(Math, calcJobs);
	salaryMax2 = $('#max_salary').html();
	salaryMin2 = $('#min_salary').html();
	jobsMax2 = $('#max_jobs').html();
	jobsMin2 = $('#min_jobs').html();
	console('salaryMax:' + salaryMax + ' | <br />salaryMin:' + salaryMin + ' | <br />jobxMax:' + jobsMax + ' | <br />jobsMin:' + jobsMin + ' <br /> ' + nodeCount + 'salaryMax2:' + salaryMax2 + ' | <br />salaryMin2:' + salaryMin2 + ' | <br />jobxMax2:' + jobsMax2 + ' | <br />jobsMin2:' + jobsMin2);
	
	//populate chart key
	
	if(!$('#no_data').length && $('#svgGraph').length) {
		$('#graph_sort').show();
		$('#key').show();
		if(sortedBy == "average") {
			$('#key .max').html(salaryMax2);
			$('#key .min').html(salaryMin2);
		} else {
			$('#key .max').html(jobsMax2 + " Jobs");
			$('#key .min').html(jobsMin2 + " Jobs");
		}
		$('#graphContainer').prepend('<h3 id="salary_label">Average Salary</h3><div id="salary_label_value"></div><h3 id="jobs_label">Number of Jobs</h3><div id="jobs_label_value"></div>');
		$('#salary_label_value').html(salaryMax2);
		$('#jobs_label_value').html(jobsMax2);
	}
	
	if($('#occupation_definition_icon')) {
		
	}
	$('#occupation_definition_icon').hover(
		function(){ 
			var defLeft = $(this).position().left-$('#occupation_definition').width()/2;
			if(defLeft<0) {defLeft = 0};
			$('#occupation_definition').show().css({left: defLeft, top: $(this).position().top+30});
		},
		function(){
			$('#occupation_definition').hide();
		}
	);

	//Feed table data into array
	function Node() {
		this.title = "";
		this.salary = 10 + Math.random()*barHeight;
		this.jobs = 10 + Math.random()*barHeight;
	}
	
	var nodes = [];
	
	for(i=0;i<nodeCount;i++) {
		nodes[i] = new Node();
	}
	
	//$('#page_footer').after('<div id="postConsole"></div>');
	
	$('#graphData tr:not(:eq(0))').each(function () {
		var i = $(this).index()-1;
		nodes[i].salaryRaw =  $(this).find('td:eq(1)').html();
		nodes[i].jobsRaw = $(this).find('td:eq(2)').html();
		nodes[i].title = $(this).find('td:eq(0)').html();
		var curSalary = barHeight*dollarsToInt(nodes[i].salaryRaw)/salaryMax;
		if(!isNaN(curSalary)) {
			nodes[i].salary = curSalary;
		} else {
			nodes[i].salary = "N/A";
		}
		var curJobs = barHeight*parseInt(cleanNumber(nodes[i].jobsRaw))/jobsMax;
		if(!isNaN(curJobs)) {
			nodes[i].jobs = curJobs;
		} else {
			nodes[i].jobs = "N/A";
		}
		nodes[i].colorValueSalary = colorValue((dollarsToInt(nodes[i].salaryRaw)-salaryMin)/(salaryMax-salaryMin));
		nodes[i].colorValueJobs = colorValue((cleanNumber(nodes[i].jobsRaw)-jobsMin)/(jobsMax-jobsMin));
		if(sortedBy == "average") {
			nodes[i].colorValue = nodes[i].colorValueSalary;
		} else {
			nodes[i].colorValue = nodes[i].colorValueJobs;
		}
		nodes[i].selected = false;
		if(connectMap==true) {
			nodes[i].locationCode = $(this).find('td:eq(3)').html();
		}
	});
	
	function svgLoaded() {							
		// ready to work with SVG now
		// SVG on the page should only be manipulated after the page is 
		// finished loading
		var stepLevel = 0;
		
		if(connectMap==true){
			svgMapDoc = document.getElementById('svgMap').contentDocument;
		}
		
		//build graph type 1
		svgDoc = document.getElementById('svgGraph').contentDocument;
		pC = svgDoc.getElementById('pointsContainer');
		points = pC.getElementsByTagNameNS(svgns,'g');
		lC = svgDoc.getElementById('lines');
		lines = lC.getElementsByTagNameNS(svgns,'g');
		grid = svgDoc.getElementById('gridLines');
		
		if(nodeCount > 10) {
			$('#graphContainer').append('<div id="scrollIn"></div><div id="scrollOut">');
			$('#scrollIn').hover(function(){walkIn()},function(){clearWalks()});
			$('#scrollOut').hover(function(){walkOut()},function(){clearWalks()});
			$('#scrollIn').css({left: gOriginX+gSpacing*numVisible, top: canvasHeight-(gOriginY+gSpacing*numVisible+20)});
			$('#scrollOut').css({left: gOriginX-gSpacing*2, top: canvasHeight-(gOriginY-gSpacing)});
		}// end if(nodeCount > 10)
		
		for(i=0;i<10;i++)
		{
			points[i].setAttribute('opacity',0);
		}
		
		
		for(i=0;i<4;i++) {
			lines[i].setAttribute('transform','translate('+(gOriginX+gSpacing*(i*2))+','+ (canvasHeight-(gOriginY+gSpacing*(i*2))) +')');
			lines[i].getElementsByTagNameNS(svgns,'line')[0].setAttribute('x2',barHeight-4);
			lines[i].getElementsByTagNameNS(svgns,'line')[1].setAttribute('y2',(0-barHeight));
		}
		
		lines[0].getElementsByTagNameNS(svgns,'line')[0].style.stroke="#333";
		lines[1].getElementsByTagNameNS(svgns,'line')[0].style.stroke="#282828";
		lines[2].getElementsByTagNameNS(svgns,'line')[0].style.stroke="#222";
		lines[3].getElementsByTagNameNS(svgns,'line')[0].style.stroke="#181818";
		lines[0].getElementsByTagNameNS(svgns,'line')[1].style.stroke="#333";
		lines[1].getElementsByTagNameNS(svgns,'line')[1].style.stroke="#282828";
		lines[2].getElementsByTagNameNS(svgns,'line')[1].style.stroke="#222";
		lines[3].getElementsByTagNameNS(svgns,'line')[1].style.stroke="#181818";
		
		grid.setAttribute('transform','translate('+(gOriginX)+','+ (canvasHeight-(gOriginY)) +')');
		
		//display node data in graph type 1
		for(i=0;i<numVisible;i++) {
			points[i].setAttribute('transform','translate('+(gOriginX+gSpacing*i)+','+ (canvasHeight-(gOriginY+gSpacing*i)) +')');
			points[i].getElementsByTagNameNS(svgns,'line')[2].style.strokeWidth=gSpacing;
			points[i].getElementsByTagNameNS(svgns,'line')[3].style.strokeWidth=gSpacing;
			updatePoint(i);
			points[i].setAttribute('opacity',1);
			
			//assign event functions to points
			(function(i) {
					  
				points[i].addEventListener('mouseover', function(evt) {
					 actionOverPoint(i);
				}, false);
				
				/*points[i].addEventListener('mouseout', function(evt) {
					 actionOutNode(i);
				}, false);*/
			
			}) (i);
		}// end for(i=0;i<numVisible;i++)
		
		function nodeColorValue(nodeIndex) {
			var svgMapDocLoc = svgMapDoc.getElementById(nodes[nodeIndex].locationCode);
			if(svgMapDocLoc != null) {
				svgMapDoc.getElementById(nodes[nodeIndex].locationCode).setAttribute('fill',nodes[nodeIndex].colorValue);
			}
		}
		
		function nodeColorHighlight(nodeIndex) {
			var svgMapDocLoc = svgMapDoc.getElementById(nodes[nodeIndex].locationCode);
			if(svgMapDocLoc != null) {
				svgMapDoc.getElementById(nodes[nodeIndex].locationCode).setAttribute('fill',highlightColor);
			}
		}
		
		//set color values on map
		if(connectMap == true) {
			for(i=0;i<nodeCount;i++) {
				nodeColorValue(i);
				//assign event functions to map elements
				var svgMapDocLoc = svgMapDoc.getElementById(nodes[i].locationCode);
				if(svgMapDocLoc != null) {
					(function(i) {
						svgMapDoc.getElementById(nodes[i].locationCode).addEventListener('mousedown', function(evt) {
							actionOverNode(i);
						}, false);
						$(svgMapDoc.getElementById(nodes[i].locationCode)).css('cursor','pointer');
						
						//assign tooltip functions separately for ie and other browsers
						if ( $.browser.msie ) {
							svgMapDoc.getElementById(nodes[i].locationCode).addEventListener('mousemove', function(evt) {
								var tooltipX = mX-$('#content').offset().left-10;
								var tooltipY = mY-$('#content').offset().top-$('#tooltip').height()-40;
								$('#tooltip').css({left:tooltipX,top:tooltipY});
							}, false);
						} else {
							svgMapDoc.getElementById(nodes[i].locationCode).addEventListener('mousemove', function(evt) {
								var tooltipX = evt.pageX+$('#svgMap').offset().left-$('#content').offset().left-10;
								var tooltipY = evt.pageY+$('#svgMap').offset().top-$('#content').offset().top-$('#tooltip').height()-40;
								$('#tooltip').css({left:tooltipX,top:tooltipY});
							}, false);
						}
						
						svgMapDoc.getElementById(nodes[i].locationCode).addEventListener('mouseover', function(evt) {
							tooltip(nodes[i].title,nodes[i].salaryRaw,nodes[i].jobsRaw); 
							svgMapDoc.getElementById(nodes[i].locationCode).setAttribute('fill',hoverColor);
							$('#tooltip').show();
						}, false);
						
						svgMapDoc.getElementById(nodes[i].locationCode).addEventListener('mouseout', function(evt) {
							if(nodes[i].selected == true){
								svgMapDoc.getElementById(nodes[i].locationCode).setAttribute('fill',highlightColor);
							} else {
								svgMapDoc.getElementById(nodes[i].locationCode).setAttribute('fill',nodes[i].colorValue);
							}
							$('#tooltip').hide();
						}, false);
						
					}) (i);
				}
			}
		}
		
		//populate info with top result
		if(!$('#no_data').length){
		info('<h3>' + nodes[0].title + '</h3><div id="info_salary"><span class="label">Average Salary: </span>' + nodes[0].salaryRaw + '</div><div id="info_jobs"><span class="label">Number of Jobs: </span>' + nodes[0].jobsRaw + '</div>');
		}
		
		//define event functions
		function actionOverPoint(i) {
			if(nodes[i+stepLevel].selected == false)
			{
				unHighlightNode(); //updatePoints() will only update the numVisible points. This removes the highlight from the state that updatePoints will not remove;
				nodes[selectedNode].selected = false; //global selectedNode is still set to a number
				nodes[i+stepLevel].selected = true;
				selectedNode = i+stepLevel;
				if(selectedNode >= stepLevel && selectedNode < stepLevel+numVisible) {
					updatePoints();
				}
			}
		}
		
		function actionOverNode(i) {
			if(nodes[i].selected == false)
			{
				unHighlightNode(); //updatePoints() will only update the numVisible points. This removes the highlight from the state that updatePoints will not remove;
				nodes[selectedNode].selected = false; //global selectedNode is still set to a number
				nodes[i].selected = true;
				selectedNode = i;
				if(selectedNode >= stepLevel && selectedNode < stepLevel+numVisible) {
					updatePoints();
				} else {
					stepLevel = selectedNode;
					if(stepLevel > (nodes.length-numVisible)) {
						stepLevel = nodes.length-numVisible;
					}
					updatePoints();
				}
				/*if(connectMap == true) {
					svgMapDoc.getElementById(nodes[selectedNode].locationCode).setAttribute('fill',highlightColor);
				}*/
			}
		}
		
		function unHighlightNode() {
				if(connectMap == true) {
					nodeColorValue(selectedNode);
				}
		}
		
		function actionOutNode(i) {
		}
		
		//var stepLevel = 0;
		
		function stepIn() {
			if(stepLevel >= (nodes.length-numVisible)) {
				clearWalks();
				return;
			}
			stepLevel++;
			updatePoints();
		}
		
		function stepOut() {
			if(stepLevel <= 0) {
				clearWalks();
				return;
			}
			stepLevel--;
			updatePoints();
		}		
		
		function updatePoints() {
			for(i=0;i<numVisible;i++) {
				updatePoint(i);
			}	
		}
		
		function highlightPoint(i) {
			points[i].setAttribute('fill',highlightColor);
			points[i].getElementsByTagNameNS(svgns,'line')[0].style.stroke=highlightColor;
			points[i].getElementsByTagNameNS(svgns,'circle')[0].setAttribute('fill',highlightColor);
			points[i].getElementsByTagNameNS(svgns,'line')[1].style.stroke=highlightColor;
			points[i].getElementsByTagNameNS(svgns,'circle')[1].setAttribute('fill',highlightColor);
			info('<h3>' + nodes[i+stepLevel].title + '</h3><div id="info_salary"><span class="label">Average Salary: </span>' + nodes[i+stepLevel].salaryRaw + '</div><div id="info_jobs"><span class="label">Number of Jobs: </span>' + nodes[i+stepLevel].jobsRaw + '</div>');
			if(connectMap == true) {
				nodeColorHighlight(i+stepLevel);
			}
		}
		
		function updatePoint(i) {
				var c = i + stepLevel;
				
				postConsole(nodes[c].title + ' | ' + nodes[c].salary);
				if(nodes[c].jobs == "N/A") {
					points[i].getElementsByTagNameNS(svgns,'line')[0].setAttribute('x2',barHeight);
					points[i].getElementsByTagNameNS(svgns,'circle')[0].setAttribute('cx',-10000);
				} else {
					points[i].getElementsByTagNameNS(svgns,'line')[0].setAttribute('x2',nodes[c].jobs);
					points[i].getElementsByTagNameNS(svgns,'circle')[0].setAttribute('cx',nodes[c].jobs);
				}
				//points[i].getElementsByTagNameNS(svgns,'line')[1].setAttribute('y2',(0-nodes[c].salary));
				if(nodes[c].salary == "N/A") {
					points[i].getElementsByTagNameNS(svgns,'line')[1].setAttribute('y2',(0-barHeight));
					points[i].getElementsByTagNameNS(svgns,'circle')[1].setAttribute('cy',-10000);
				} else {
					points[i].getElementsByTagNameNS(svgns,'line')[1].setAttribute('y2',(0-nodes[c].salary));
					points[i].getElementsByTagNameNS(svgns,'circle')[1].setAttribute('cy',1-nodes[c].salary);
				}
				points[i].getElementsByTagNameNS(svgns,'line')[2].setAttribute('x2',nodes[c].jobs);
				points[i].getElementsByTagNameNS(svgns,'line')[3].setAttribute('y2',(0-nodes[c].salary));
				
				points[i].getElementsByTagNameNS(svgns,'text')[0].childNodes[0].nodeValue = nodes[c].title;
				if(nodes[c].selected == true) {
					highlightPoint(i);
					return;
				}
				points[i].setAttribute('fill','#fff');
				if(nodes[c].jobs == "N/A") {
					points[i].getElementsByTagNameNS(svgns,'line')[0].style.stroke="#4f003d";
				} else {
					points[i].getElementsByTagNameNS(svgns,'line')[0].style.stroke="#fff";
				}
				if(nodes[c].salary == "N/A") {
					points[i].getElementsByTagNameNS(svgns,'line')[1].style.stroke="#4f003d";
				} else {
					points[i].getElementsByTagNameNS(svgns,'line')[1].style.stroke="#fff";
				}
				points[i].getElementsByTagNameNS(svgns,'circle')[0].setAttribute('fill',nodes[c].colorValueJobs);
				points[i].getElementsByTagNameNS(svgns,'circle')[1].setAttribute('fill',nodes[c].colorValueSalary);
				if(connectMap == true) {
					nodeColorValue(c);
				}
		}
		
		function updateNode(i) {
				var c = i;
				if(connectMap == true) {
					nodeColorValue(c);
				}
		}
		
		var moveWalkIn; //set for step and walk functions
		var moveWalkOut; // ""
		
		function walkIn() {
			clearWalks();
			moveWalkIn = setInterval( function () {stepIn()},80);
		}
		
		function walkOut() {
			clearWalks();
			moveWalkIn = setInterval( function () {stepOut()},80);
		}
		
		function clearWalks()
		{
			if(moveWalkIn) {
				clearInterval(moveWalkIn);
			}
			if(moveWalkOut) {
				clearInterval(moveWalkOut);
			}
		} 
	}

	
	function randomColor()
	{
		var randomColor ="#";
		for(var i=0; i<=5; i++)
		{
			randomColor += Math.floor(Math.random()*16).toString(16).toUpperCase();
		}
		return randomColor;
	}
		
	function padHex(str)
	{
		if(str.length < 2) {
			str = "0"+str;
		}
		return str;
	}
	
	function colorValue(val) {
		decRed = padHex(Math.round((255-48)*val+48).toString(16));
		decGreen = padHex(Math.round((170-48)*val+48).toString(16));
		decBlue = padHex(Math.round((0-48)*val+48).toString(16));
		return "#" + decRed + decGreen + decBlue; 
	}
	
	function dollarsToInt(string) {
		string = string.replace("$","")
		return string.replace(/,/g,"");
		return string;
	}
	
	function cleanNumber(string)
	{
		return string.replace(/,/g,"");
	}
	
	function trim(string)
	{
		return string.replace(/ /g,"");
	}
	
	function console(content) {
		$('#console').html(content);
	}
	
	function postConsole(content) {
		$('#postConsole').append(content + '<br />');
	}
	
	function clearConsole() {
		$('#console').html('');
	}
	
	function info(content) {
		$('#info').html(content);
	}
	function tooltip(str1,str2,str3) {
		$('#tooltip_title').html(str1);
		$('#tooltip_salary').html(str2);
		$('#tooltip_jobs').html(str3);
	}
	
	function clearInfo() {
		$('#info').html('');
	}
	
	function enableTableScroll(selector) {
		$(selector).wrapInner('<div class="tableContainerInner" style="padding-right: 24px; padding-bottom: .5em;"	></div>');
		$(selector + ' .scrollTable').each(function() {
			var tbodyHeight = 0;
			var trLength = $(this).find('tbody tr').length;
			trLength = (trLength > 10) ? 10 : trLength; 
			for(i=0;i<trLength;i++)
			{
				tbodyHeight += $(this).find('tbody tr:eq('+i+')').height();
			}
			$(this).tableScroll({height: tbodyHeight});
		});
	}
	
	enableTableScroll('.tableContainer');
	
	if($('#svgGraph').length) {
		 if ( $.browser.msie ) {
			window.addEventListener('SVGLoad', function() {
	
				svgLoaded();
			}, false);			
		 } else {
			svgLoaded();
		 }
	 }
	 
	 function svgGranular()
	 {
		 svgMapDoc = document.getElementById('svgMap').contentDocument;
		 var locationCode = trim($('#granular_msa_code').html());
		 svgMapDoc.getElementById(locationCode).setAttribute('fill',highlightColor);
	 }
	 
	 if($('#svgMap').length && $('#granular_msa_code').length) {
		 if ( $.browser.msie ) {
			window.addEventListener('SVGLoad', function() {
	
				svgGranular();
			}, false);			
		 } else {
			svgGranular();
		 }
	 }
	 
	 //get mouse position for ie tooltip
	if( $.browser.msie ) {
		var mX = 0;
		var mY = 0;
		$(document).mousemove(function(e){
			mX = e.pageX;
			mY = e.pageY;
		});
	}
});	  



$(document).ready(function() {
	$("#occupation_text").keyup(function() {
	  getOccupations($("#occupation_group").val(), $("#occupation").val(), $(this).val());
	});
	$("#region_text").keyup(function() {
	  getMetros($("#state").val(), $("#metro").val(), $(this).val());
	});
});
