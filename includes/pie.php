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
						renderTo: 'pie_eere_budget',
						plotBackgroundColor: null,
						plotBorderWidth: null,
						plotShadow: false
					},
					title: {
						text: '<?php
                      if (count($tmpParentIDList) == 1) {
                        if ($tmpParentIDList[0] == 0) $tmpParentName = "EERE Budget for FY " . $years[0];
                        else $tmpParentName = sprintf("%s Budget for FY %s", getProgramName($tmpParentIDList[0]), $years[0]);
                      }
                      //get the parent program, just send the first programID

                    else {
                      $tmpParentName = sprintf("%s Budget for FY %s", getParentProgramName($tmpParentIDList[0]), $years[0]);
                      if ($tmpParentName == "") $tmpParentName = "EERE Budget for FY " . $years[0];
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
                echo "[";
                    echo " '" . getProgramName($tmpParentID) . "'," . " ";

                      if (mysql_num_rows($result) > 1) //still on a program that can 'drill down'
                        echo getBudgetRollup_FY_Program(mysql_real_escape_string($tmpParentID), mysql_real_escape_string($years[0])) . ", ";
                      else //drilled down as far as possible.
                        echo getBudget_FY_Program(mysql_real_escape_string($tmpParentID), mysql_real_escape_string($years[0])) . ", ";


                  echo "],\n";
                }
              }
              else {
                //drill down, only one program selected

                  //get subprograms for the selected program.
                  //use programType only if at top level EERE
                if ($tmpParentIDList[0] == 0){
                  $result = returnProgramListing($tmpParentIDList[0], $tmpProgramType);
                }
                else $result = returnProgramListing($tmpParentIDList[0],0);
                   while($row = mysql_fetch_array($result)) {
                    echo "['" . $row['program_name'] . "', ";
                      if ($tmpParentIDList[0] == 0){
                        //get rollup for parent level
                        echo getBudgetRollup_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($years[0])) . ", ";
                      }
                      else {
                        //get budget number for subprogram
                        echo getBudget_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($years[0])) . ", ";
                      }

                    echo "], \n";
                }

              }
            ?>
						]
					}]
				});
			});

</script>

  <div id="pie_eere_budget" style="width: 100%; height: 700px; clear:both;"></div>
