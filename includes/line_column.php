<script type="text/javascript" src="includes/highcharts/modules/exporting.js"></script>


		<!-- 2. Add the JavaScript to initialize the chart on document ready -->
		<script type="text/javascript">

			var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
						renderTo: 'container',
						defaultSeriesType: '<?php echo $tmpChartType;?>',
						marginRight:  25
					},
					title: {
						text: '<?php
                      if (count($tmpParentIDList) == 1) {
                        if ($tmpParentIDList[0] == 0) $tmpParentName = "EERE Budget";
                        else $tmpParentName = getProgramName($tmpParentIDList[0]);
                      }
                      //get the parent program, just send the first programID

                    else {
                      $tmpParentName = getParentProgramName($tmpParentIDList[0]);
                      if ($tmpParentName == "") $tmpParentName = "EERE Budget";
                    }
                    echo $tmpParentName;?>',
						x: -20 //center
					},
          subtitle: {
						text: 'Dollars in Thousands',
						x: -20
					},
					xAxis: {
            <?php
              //following code helps set up display for FY
              $tmpYears = array();
              foreach($years as $tmpYear){
                array_push($tmpYears, fy_forDisplay($tmpYear));
              }
            ?>
						categories: ['<?php echo implode("', '", $tmpYears);?>']
					},
					yAxis: {
						title: {
							text: 'Dollars in Thousands'
						},
						plotLines: [{
							value: 0,
							width: 1,
							color: '#808080'
						}]
					},
          credits: {
            enabled:false
          },
					tooltip: {
						formatter: function() {
				                return '<strong>'+ this.series.name +'</strong><br/>'+
								this.x +': '+ formatNumber(this.y,0) +' ';
						}
					},
					legend: {
						layout: 'vertical',
						align: 'center',
						verticalAlign: 'bottom',
						borderWidth: 0
					},
					series: [

            <?php
              //for multiple selections
              if (count($tmpParentIDList) > 1) {
                foreach ($tmpParentIDList as $tmpParentID) {
                  $tmpDisplayRow = "";
                  $tmpBudgetTotal = 0;
                  $result = returnProgramListing($tmpParentID, 0);
                  $tmpDisplayRow .= "{" . "\n";
                  $tmpDisplayRow .= "name:'" . getProgramName($tmpParentID) . "'," . "\ndata:[";
                  foreach($years as $year){
                    if (getParentProgramID($tmpParentID) == 0) {
                    //still on a program that can 'drill down'
                   
                      $tmpBudget = getBudgetRollup_FY_Program(mysql_real_escape_string($tmpParentID), mysql_real_escape_string($year));
                      $tmpBudgetTotal += $tmpBudget;
                      $tmpDisplayRow .= $tmpBudget . ", ";
                    }
                    else { //drilled down as far as possible.
                      $tmpBudget = getBudget_FY_Program(mysql_real_escape_string($tmpParentID), mysql_real_escape_string($year));
                      $tmpBudgetTotal += $tmpBudget;
                      $tmpDisplayRow .= $tmpBudget . ", ";
                    }
                  }
                  $tmpDisplayRow .= "]\n} ," . "\n";
                  //only display if totals are not zero
                  if ($tmpBudgetTotal != 0) {
                    echo $tmpDisplayRow;
                  }
                }
              }
              else {
                //drill down, only one program selected

                  //get subprograms for the selected program.
                  //use programType only if at top level EERE
                if ($tmpParentIDList[0] == 0){
                  if ($tmpProgramType != 4) {
                    //get only programs for a specific program type
                    $result = returnProgramListing($tmpParentIDList[0], $tmpProgramType);
                  }
                  else $result = getProgramTypes(); //Get only program types.

                }
                else $result = returnProgramListing($tmpParentIDList[0],0);
                 while($row = mysql_fetch_array($result)) {
                   $tmpDisplayRow = "";
                   $tmpBudgetTotal = 0;

                   $tmpDisplayRow .= "{" . "\n";
                   $tmpDisplayRow .= "name:'" . $row['program_name'] . "'," . "\ndata:[";
                   foreach($years as $year) {
                     //KC: removing subprogram navigation: if ($tmpParentIDList[0] == 0) {
                       //get rollup for parent level
                       if ($tmpProgramType != 4)
                        $tmpBudget = getBudgetRollup_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($year));
                       else
                        $tmpBudget = getBudgetRollup_FY_Program(0, mysql_real_escape_string($year), true, mysql_real_escape_string($row['programID']));
                       $tmpBudgetTotal += $tmpBudget;
                       $tmpDisplayRow .= $tmpBudget . ", ";
                     //KC: removing subprogram navigation: }
                     //KC: removing subprogram navigation: else {
                       //get budget number for subprogram
                     //KC: removing subprogram navigation:   $tmpBudget = getBudget_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($year));
                     //KC: removing subprogram navigation:   $tmpBudgetTotal += $tmpBudget;
                     //KC: removing subprogram navigation:   $tmpDisplayRow .= $tmpBudget . ", ";
                     //KC: removing subprogram navigation: }
                   }
                   $tmpDisplayRow .= "]\n} ," . "\n";
                   //only display if totals are not zero
                   if ($tmpBudgetTotal != 0 ) {
                     echo $tmpDisplayRow;
                   }
                 }

                }
          ?>]


				});


			});

		</script>

<?php
if ((count($tmpParentIDList) == 1) && ($tmpParentIDList[0] != 0)){
echo "<h3 style='text-align:center; color:red; clear:both'>Please select more than one program</h3>";
}
?>
  <div id="container" style="width: 100%; height: 700px; clear:both;"></div>
