<script type="text/javascript">




  //---------------EERE Budget chart-----------------------
  var fy_total = <?php
            if (count($tmpParentIDList) == 1) {
                if ($tmpParentIDList[0] == 0){
                  //at EERE root, get totals for all EERE
                  $tmpTotal = getBudgetRollup_FY_Program(0, mysql_real_escape_string($years[0]),true);
                  $tmpTotalText = "EERE Total: ";
                }
                else {
                    $tmpTotalText = "Program Total: ";
                    if (getParentProgramName($tmpParentIDList[0]) == "") {
                      //There is no parent, must be a Program Level, get program level totals
                      $tmpTotal = getBudgetRollup_FY_Program($tmpParentIDList[0], mysql_real_escape_string($years[0]));
                    }
                    else {
                      //At the SubProgram detail level, get parent totals
                      $tmpTotal = getBudgetRollup_FY_Program(getParentProgramID($tmpParentIDList[0]), mysql_real_escape_string($years[0]));
                    }
                }
            }
            else {
              //more than one program/subprogram has been chosen
              if (getParentProgramName($tmpParentIDList[0]) == "") {
                //no parent, at Program Level, get all EERE totals
                $tmpTotal = getBudgetRollup_FY_Program(0, mysql_real_escape_string($years[0]),true);
                $tmpTotalText = "EERE Total: ";
              }
              else {
                //no parent, must be at subprogram level, get totals for parentID
                $tmpTotal = getBudgetRollup_FY_Program(getParentProgramID($tmpParentIDList[0]), mysql_real_escape_string($years[0]));
                $tmpTotalText = "Program Total: ";
              }
            }
      echo $tmpTotal;?>;
  var chart;
			$(document).ready(function() {
				chart = new Highcharts.Chart({
					chart: {
            marginLeft: 100,
						renderTo: 'pie_eere_budget',
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false
					},
					title: {
						text: '<?php
                      if (count($tmpParentIDList) == 1) {
                        if ($tmpParentIDList[0] == 0) $tmpParentName = "EERE Budget for FY " . fy_forDisplay($years[0]);
                        else $tmpParentName = sprintf("%s Budget for FY %s", getProgramName($tmpParentIDList[0]), fy_forDisplay($years[0]));
                      }
                      //get the parent program, just send the first programID

                    else {
                      $tmpParentName = sprintf("%s Budget for FY %s", getParentProgramName($tmpParentIDList[0]), fy_forDisplay($years[0]));
                      if ($tmpParentName == "") $tmpParentName = "EERE Budget for FY " . fy_forDisplay($years[0]);
                    }
                    echo $tmpParentName;?>'
					},
          subtitle: {
						text: '<?php echo $tmpTotalText . number_format($tmpTotal);?> (Dollars in Thousands)',
						x: -20
					},
          credits: {
            enabled:false
          },
					tooltip: {
						formatter: function() {
							return '<strong>'+ this.point.name +'</strong>: ' + formatNumber((this.y/fy_total)*100, 2) + '% ('+ formatNumber(this.y,0) + ')';
						}
					},
					plotOptions: {
						pie: {
							allowPointSelect: true,
							cursor: 'pointer',
							dataLabels: {
								enabled: true,
								color: '#000000',
								style: {
                  width: 170,
                  fontWeight: 'bold'
                },
                connectorColor: '#000000',
								formatter: function() {
									return '<strong>'+ this.point.name +'</strong>';
								}
							}
						}
					},
				    series: [{
						type: 'pie',
						name: 'EERE Budget',
						data: [
            <?php


                //for multiple selections
              if (count($tmpParentIDList) > 1) {
                foreach ($tmpParentIDList as $tmpParentID) {
                  $result = returnProgramListing($tmpParentID, 0);
                  $tmpDisplayRow = "";
                  $tmpBudgetTotal = 0;
                  $tmpDisplayRow .= "[";
                      $tmpDisplayRow .= " '" . getProgramName($tmpParentID) . "'," . " ";

                        if (getParentProgramID($tmpParentID) == 0) {
                        //if (mysql_num_rows($result) > 1) { //still on a program that can 'drill down'
                          $tmpBudget = getBudgetRollup_FY_Program(mysql_real_escape_string($tmpParentID), mysql_real_escape_string($years[0]));
                          $tmpBudgetTotal += $tmpBudget;
                          $tmpDisplayRow .= $tmpBudgetTotal . ", ";

                        }
                        else { //drilled down as far as possible.
                          $tmpBudget = getBudget_FY_Program(mysql_real_escape_string($tmpParentID), mysql_real_escape_string($years[0]));
                          $tmpBudgetTotal += $tmpBudget;
                          $tmpDisplayRow .= $tmpBudgetTotal . ", ";

                        }
                    $tmpDisplayRow .= "],\n";
                    if ($tmpBudgetTotal > 0) {
                      echo $tmpDisplayRow;
                    }
                  }
              }
              else {
                //drill down, only one program selected

                  //get subprograms for the selected program.
                  //use programType only if at top level EERE
                if ($tmpParentIDList[0] == 0) {
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

                   $tmpDisplayRow .= "['" . $row['program_name'] . "', ";
                    //KC: removing subprogram navigation: if ($tmpParentIDList[0] == 0){
                      //get rollup for parent level
                      if ($tmpProgramType != 4)
                        $tmpBudget = getBudgetRollup_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($years[0]));
                      else
                        $tmpBudget = getBudgetRollup_FY_Program(0, mysql_real_escape_string($years[0]), true, mysql_real_escape_string($row['programID']));
                      $tmpBudgetTotal += $tmpBudget;
                      $tmpDisplayRow .= $tmpBudgetTotal . ", ";
                    //KC: removing subprogram navigation: }
                    //KC: removing subprogram navigation: else {
                      //get budget number for subprogram
                    //KC: removing subprogram navigation:   $tmpBudget = getBudget_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($years[0]));
                    //KC: removing subprogram navigation:  $tmpBudgetTotal += $tmpBudget;
                    //KC: removing subprogram navigation:  $tmpDisplayRow .= $tmpBudgetTotal . ", ";

                    //KC: removing subprogram navigation: }

                    $tmpDisplayRow .= "], \n";
                    if ($tmpBudgetTotal > 0) {
                     echo $tmpDisplayRow;
                    }

                }

              }
            ?>
						]
					}]
				});
			});

</script>
<?php
if ((count($tmpParentIDList) == 1) && ($tmpParentIDList[0] != 0)){
echo "<h3 style='text-align:center; color:red; clear:both'>Please select more than one program</h3>";
}
?>
  <div id="pie_eere_budget" style="width: 100%; height: 700px; clear:both;"></div>
