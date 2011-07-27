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
						categories: ['<?php echo implode("', '", $years);?>']
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
					tooltip: {
						formatter: function() {
				                return '<b>'+ this.series.name +'</b><br/>'+
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
                $result = returnProgramListing($tmpParentID,0);
                echo "{" . "\n";
                    echo "name:'" . getProgramName($tmpParentID) . "'," . "\ndata:[";
                    foreach($years as $year){
                      if (mysql_num_rows($result) > 1) //still on a program that can 'drill down'
                        echo getBudgetRollup_FY_Program(mysql_real_escape_string($tmpParentID), mysql_real_escape_string($year)) . ", ";
                      else //drilled down as far as possible.
                        echo getBudget_FY_Program(mysql_real_escape_string($tmpParentID), mysql_real_escape_string($year)) . ", ";

                    }
                  echo "]\n} ," . "\n";
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
                    echo "{" . "\n";
                    echo "name:'" . $row['program_name'] . "'," . "\ndata:[";
                    foreach($years as $year){
                      if ($tmpParentIDList[0] == 0){
                        //get rollup for parent level
                        echo getBudgetRollup_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($year)) . ", ";
                      }
                      else {
                        //get budget number for subprogram
                        echo getBudget_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($year)) . ", ";
                      }
                    }
                    echo "]\n} ," . "\n";
                }

              }
          ?>]


				});


			});

		</script>


  <div id="container" style="width: 100%; height: 700px; clear:both;"></div>