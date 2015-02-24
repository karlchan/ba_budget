<script type="text/javascript" src="./includes/js/html2canvas.js"></script>
<!--[if IE]><script language="javascript" type="text/javascript" src="js/excanvas.js"></script><![endif]-->
<script type="text/javascript" src="./includes/js/jit-yc.js"></script>
<link href="./includes/treemap.css" rel="stylesheet" type="text/css" />

<script type="text/javascript">
	$(document).ready(
		function () {
			var fiscalYear = <?php echo substr($years[0], 0, 4); ?>;
			if (false) { //&& fiscalYear < 2015
				var data = [
					<?php
						$currentYear = $years[0];//$years[count($years) - 1]
						$programTotal = getBudgetRollup_FY_Program(0, mysql_real_escape_string($currentYear),true);
						$offices = getProgramTypes();
						$officeList = array();
						$colorList = array('#B3FAFF', '#95FF7A', '#FFA3CE', '#F1A3FF');
						
						// to iterate in order
						while($row = mysql_fetch_array($offices)) {
							$officeList[] = $row;
						}
						
						// main offices
						for ($i = mysql_num_rows($offices) - 1; $i >= 0; $i--) {
							mysql_data_seek($offices, $i);
							$row = mysql_fetch_assoc($offices);
							$officeRowContent = "{ label: '" . $row['program_name'] . "', value: null, color: '" . $colorList[$i] . "' }, \n";
							echo $officeRowContent;
						}
						
						// programs
						$programBudget = 0;
						$programs = returnProgramListing($tmpParentIDList[0],0);
						while ($row = mysql_fetch_array($programs)) {
							$programBudget = getBudgetRollup_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($currentYear));
							if ($programBudget > 0 && !empty($row['programTypeID']) ) {
								$programRowContent = "{ label: '" . $row['program_name'] . " " . number_format(($programBudget / $programTotal) * 100, 2) . "% (" . number_format($programBudget, 0) . ")', value: ". $programBudget .", parent: '" . $officeList[intval($row['programTypeID']) - 1]['program_name'] . "', ";
								$programRowContent .= " data: { description: \"" . number_format(($programBudget / $programTotal) * 100, 2) . "% (" . number_format($programBudget, 0) . ")\", title: \"" . $row['program_name'] . "\" } }, \n";
								echo $programRowContent;
							}
						}
						echo "{ label: 'NA', value: 0, parent: 'Renewable', data: { description: 'NA', title: 'NA'} }, ";
					?>
				];

				var theme = getDemoTheme();
				$('#treemap').jqxTreeMap({
					width: 1050,//950,
					height: 1000,//900,
					source: data,
					theme: theme,
					colorRange: 60,
					renderCallbacks: { 
						'*': function (element, value) {
							if (value.data) {
								element.jqxTooltip({
									content: '<div><span style="font-weight: bold; max-width: 350px; font-family: verdana; font-size: 14px;">' + value.data.title + ': </span><span style="width: 350px; font-family: verdana; font-size: 12px;">' + value.data.description + '</span></div>',
									position: 'mouse',
									autoHideDelay: 6000,
									theme: theme
								});
							} else if (value.data === undefined) {
								element.css({
									backgroundColor: '#fff',
									border: '1px solid #555'
								});
							}
						}
					}
				});
			
			}
			else
			{
				var labelType, useGradients, nativeTextSupport, animate;

				(function() {
				  var ua = navigator.userAgent,
					  iStuff = ua.match(/iPhone/i) || ua.match(/iPad/i),
					  typeOfCanvas = typeof HTMLCanvasElement,
					  nativeCanvasSupport = (typeOfCanvas == 'object' || typeOfCanvas == 'function'),
					  textSupport = nativeCanvasSupport 
						&& (typeof document.createElement('canvas').getContext('2d').fillText == 'function');
				  //I'm setting this based on the fact that ExCanvas provides text support for IE
				  //and that as of today iPhone/iPad current text support is lame
				  labelType = (!nativeCanvasSupport || (textSupport && !iStuff))? 'Native' : 'HTML';
				  nativeTextSupport = labelType == 'Native';
				  useGradients = nativeCanvasSupport;
				  animate = !(iStuff || !nativeCanvasSupport);
				})();

				
				var json = <?php 
					//$fiscalYear = substr($years[0], 0, 4);
					$currentYear = $years[0];
					$programTotal = getBudgetRollup_FY_Program(0, mysql_real_escape_string($currentYear),true);
					
					function lineargradient($ra,$ga,$ba,$rz,$gz,$bz,$iterationnr) {
					  $colorindex = array();
					  for($iterationc=1; $iterationc<=$iterationnr; $iterationc++) {
						 $iterationdiff = $iterationnr-$iterationc;
						 $colorindex[] = '#'.
							dechex(intval((($ra*$iterationc)+($rz*$iterationdiff))/$iterationnr)).
							dechex(intval((($ga*$iterationc)+($gz*$iterationdiff))/$iterationnr)).
							dechex(intval((($ba*$iterationc)+($bz*$iterationdiff))/$iterationnr));
					  }
					  return $colorindex;
					}


					function scale($valueIn, $baseMin, $baseMax, $limitMin, $limitMax) {
						return (($limitMax - $limitMin) * ($valueIn - $baseMin) / ($baseMax - $baseMin)) + $limitMin;
					}
					function getColorValue($num, $totalLimit) {
						return (scale(abs($num), 0, $totalLimit, 20, 200) - 1);
					}
					
					function getPalette ($programTypeID, $budget, $subTotal) {
						$colorindex1 = lineargradient(
						  30, 73, 204,    // rgb of the start color
						  200, 214, 255,  // rgb of the end color
						  200             // number of colors in your linear gradient
						);
						$colorindex2 = lineargradient(
						  204, 112, 29,    // rgb of the start color
						  255, 226, 200,  // rgb of the end color
						  200             // number of colors in your linear gradient
						);
						$colorindex3 = lineargradient(
						  92, 204, 29,    // rgb of the start color
						  219, 255, 199,  // rgb of the end color
						  200             // number of colors in your linear gradient
						);
						$colorindex4 = lineargradient(
						  29, 202, 204,    // rgb of the start color
						  199, 254, 255,  // rgb of the end color
						  200             // number of colors in your linear gradient
						);
						$colorindex5 = lineargradient(
						  204, 197, 29, 
						  255, 253, 199,
						  200             // number of colors in your linear gradient
						);
						//echo "This is " . $budget . " and " . $subTotal ;
						switch ($programTypeID) {
							case 1:
								$color = $colorindex1[getColorValue($budget, $subTotal)];
								break;
							case 2:
								$color = $colorindex2[getColorValue($budget, $subTotal)];
								break;
							case 3:
								$color = $colorindex3[getColorValue($budget, $subTotal)];
								break;
							case 4:
								$color = $colorindex4[getColorValue($budget, $subTotal)];
								break;
							default:
								$color = $colorindex1[getColorValue($budget, $subTotal)];

						}
						return $color;
					}
					
					function formatBudgetNumber($officeBudget) {
						return number_format(round($officeBudget / 1000, 0, PHP_ROUND_HALF_UP));
					}
					
					// build JSON data 
					$offices = getProgramTypes();
					$data = "";
					$data = $data . " { 'children': [  \n";
					for ($i = mysql_num_rows($offices) - 1; $i >= 0; $i--) {
						mysql_data_seek($offices, $i);
						$row = mysql_fetch_assoc($offices);
						$officeBudget = getBudgetRollup_FY_Program(0, mysql_real_escape_string($currentYear), true, mysql_real_escape_string($row['programID']));
						if ($officeBudget <= 0) { continue; }

						$data = $data . " { 'children': [  \n";
						$programs = returnProgramListing($tmpParentIDList[0],$row['programID']);
						while ($prow = mysql_fetch_array($programs)) {
							$programBudget = getBudgetRollup_FY_Program(mysql_real_escape_string($prow['programID']), mysql_real_escape_string($currentYear));
							if ($programBudget <= 0) { continue; }
							
							$data = $data . " { 'children': [  \n";
							$subPrograms = returnProgramListing($prow['programID'], 0);
							while ($sprow = mysql_fetch_array($subPrograms)) {
								$subProgramBudget = getBudget_FY_Program(mysql_real_escape_string($sprow['programID']), mysql_real_escape_string($currentYear));
								if ( ($subProgramBudget <= 0) || strrpos($sprow['program_name'], 'Rollup') ) { continue; }

								$color = getPalette($row['programID'], $subProgramBudget, $programBudget);
								$data = $data . " { 'children': [], \n";
								$data = $data . " 'data': { '\$area': " . $subProgramBudget . ", 'amount': " . $subProgramBudget . ", '\$color': '" . $color . "' }, \n";
								$data = $data . " 'id': '" . $row['programID'] . "-" . $prow['programID'] . "-" . $sprow['programID'] . "', 'name': '" . $sprow['program_name'] . "' }, \n";
							}
							$color = getPalette($row['programID'], $programBudget, $officeBudget);
							$data = $data . "] , 'data': { '\$area': " . $programBudget . ", 'amount': " . $programBudget . ", '\$color': '" . $color . "' }, \n";
							$data = $data . " 'id': '" . $row['programID'] . "-" . $prow['programID'] . "', 'name': '" . $prow['program_name'] . " ($" . formatBudgetNumber($programBudget) . ")" . "' }, \n";
						}
						$color = getPalette($row['programID'], $officeBudget, $officeBudget+1);
						$data = $data . "], 'data': { '\$area': " . $officeBudget . ", 'amount': " . $officeBudget . ", '\$color': '" . $color . "' }, \n";
						$data = $data . " 'id': '" . $row['programID'] . "', 'name': '" . $row['program_name'] . " ($" . formatBudgetNumber($officeBudget) . ")". "' }, \n";
					}
					$data = $data . "], 'data': { }, \n";
					$data = $data . " 'id': 'root', 'name': 'EERE Budget for FY Budget ". fy_forDisplay($currentYear). " ($" . formatBudgetNumber($programTotal) . " - $ in millions)" ." ' } \n";
					echo $data;
					
				?>

				
				  var formatter = new Intl.NumberFormat('en-US', {
					  style: 'currency',
					  currency: 'USD',
					  minimumFractionDigits: 0,
					});
				  //init TreeMap
				  var tm = new $jit.TM.Squarified({
					//where to inject the visualization
					injectInto: 'treemap',
					//parent box title heights
					titleHeight: 22,
				   //enable animations  
					animate: animate, 
					//box offsets
					offset: 1,
					Color: {
						//Allow coloring
						enable: true,
						//Set min value and max value constraints
						//for the *$color* property value.
						//Default's to -100 and 100.
						minValue: 0,
						maxValue: 6,
						//Set color range. Default's to reddish and greenish.
						//It takes an array of three
						//integers as R, G and B values.
						minColorValue: [78, 73, 255],
						maxColorValue: [85, 255, 37]
					},
				  //Attach left and right click events  
				  Events: {  
					enable: true,  
					onClick: function(node) {  
					  if(node) tm.enter(node);  
					},  
					onRightClick: function() {  
					  tm.out();  
					}  
				  },  
					//Attach click events
				   // Events: {
				     // enable: true,
				     // onclick: function(node) {
				       // if (links[node.id]){
				           // window.open(links[node.id]);
				       // }
				     // }
				   // },
					duration: 1000,
					//Enable tips
					Tips: {
					  enable: true,
					  //add positioning offsets
					  offsetX: 20,
					  offsetY: 20,
					  //implement the onShow method to
					  //add content to the tooltip when a node
					  //is hovered
					  onShow: function(tip, node, isLeaf, domElement) {
						var html = "<div class=\"tip-title\">" + node.name 
						  + "</div><div class=\"tip-text\">";
						var data = node.data;
						if(data.amount) {
						  html += "Amount: " + formatter.format(data.amount) + "<br/>% Total Budget: " + Math.round((data.$area/<?php echo $programTotal?>)*10000)/100 + "%";
						}
						tip.innerHTML =  html; 
					  }  
					},
					//Add the name of the node in the correponding label
					//This method is called once, on label creation.
					onCreateLabel: function(domElement, node){
						domElement.innerHTML = node.name;
						var style = domElement.style;
						style.display = '';
						style.border = '1px solid transparent';
						domElement.onmouseover = function() {
						  style.border = '1px solid #9FD4FF';
						};
						domElement.onmouseout = function() {
						  style.border = '1px solid transparent';
						};
					}
				  });
				  tm.loadJSON(json);
				  tm.refresh();
			}
			
			$("#image_export").on("click", function() {
				html2canvas($("#divTreeContainer"), {
					onrendered: function(canvas) {
						$("#divTreeContainer").append(canvas);
						canvas.style.display="none";

						//console.log(<?php echo "'" . fy_forDisplay($currentYear) . "'" ?> + "." + $("#sel_export option:selected").text().toLowerCase());
						//console.log($("#sel_export option:selected").val());
						
						var fileext = "png" ; // $("#sel_export option:selected").text().toLowerCase();
						var filetype = "image/png"; // $("#sel_export option:selected").val();
						var filename = "EERE Budget for FY " + <?php echo "'" . fy_forDisplay($currentYear) . "'" ?> + "." + fileext;
						var data = canvas.toDataURL(filetype);

						// Redirect option
						//data = data.replace($("#sel_export option:selected").val(), "image/octet-stream");
						//window.location.href = data;						

						//var is_chrome = /chrom(e|ium)/.test(navigator.userAgent.toLowerCase());
						// Chrome version
						// var nameInput = document.createElement("a");
						// nameInput.setAttribute("href", data);
						// nameInput.setAttribute("download", filename);
						// nameInput.setAttribute("name", "nameInput."+ fileext);
						// nameInput.click();

						data = data.substr(data.indexOf(',') + 1).toString();
						$("#imgdata").val(data);
						$("#filename").val(filename);
						$("#filetype").val(filetype);
						
						$("#frmExport").submit(); 
					
					}
				});
			});
        });
</script>
<div id="divExport">
<form action="includes/tree_image.php" method="post" id="frmExport">
	<label class="budget_control" for="sel_export" style="display:none;">Export To</label>
	<a id="lnkDownload">
	<input id="imgdata" name="imgdata" type="hidden">
	<input id="filename" name="filename" type="hidden">
	<input id="filetype" name="filetype" type="hidden">
	<select id="sel_export" size="1" style="visibility: hidden;">
		<option value="image/png">PNG</option>
		<option value="image/jpeg">JPG</option>
	</select>
	<input value="Export" type="button" id="image_export" name="image_export" class="controlbutton" style="">
</form>
</div>
<div style="clear:both;padding: 10px 5px;"></div>
<div id="divInstructions" style="clear:both;padding: 10px 10px; font-size:14px; font-family:Arial;" class="well">
<b>Tree Map Usage Instructions:</b>
<br/><br/>
<span style="color:red;"><i>To zoom in</i></span> on a sub-program, click on the an individual square area or header to expand the box. 
Individual offices and/or programs may be selected to show more granular detail. <br/><br/>
<span style="color:red;"><i>To zoom out</i></span> right click the mouse button to show an overview at a higher level.
</div>
<div id="divTreeContainer">
	<div style="text-align:center;clear:both;">
		<span style="font-weight:bold; font-size:20px; font-family:'Times New Roman';">EERE Budget for FY <?php echo fy_forDisplay($currentYear) ?></span><br/><br/>
		<span style="font-size:14px; font-family:Serif;">EERE Total: <?php echo number_format($programTotal) ?> (Dollars in Thousands) </span>
	</div>
	<div style="padding-top:15px;">&nbsp;</div>
	<div id="container" style="clear:both;">
		<div id="treemap" style="clear:both; top:1px; left:1px;"></div>
	</div>
	<div style="padding-bottom:15px;">&nbsp;</div>
</div>