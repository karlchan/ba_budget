<script type="text/javascript">
	$(document).ready(
		function () {
            var data = [
				<?php
					$currentYear = $years[0];//$years[count($years) - 1]
					$programTotal = getBudgetRollup_FY_Program(0, mysql_real_escape_string($currentYear),true);
					$offices = getProgramTypes();
					$officeList = array();
					$colorList = array('#B3FAFF', '#95FF7A', '#FFA3CE', '#F1A3FF');
					
					//$i = 0;
					while($row = mysql_fetch_array($offices)) {
						//$officeRowContent = "{ label: '" . $row['program_name'] . "', value: null, color: '" . $colorList[$i++] . "' }, \n";
						$officeList[] = $row;
						//echo $officeRowContent;
					}
					for ($i = mysql_num_rows($offices) - 1; $i >= 0; $i--) {
						mysql_data_seek($offices, $i);
						$row = mysql_fetch_assoc($offices);
						$officeRowContent = "{ label: '" . $row['program_name'] . "', value: null, color: '" . $colorList[$i] . "' }, \n";
						echo $officeRowContent;
					}
					
					$programBudget = 0;
					$programs = returnProgramListing($tmpParentIDList[0],0);
					while ($row = mysql_fetch_array($programs)) {
						$programBudget = getBudgetRollup_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($currentYear));
						if ($programBudget > 0 && !empty($row['programTypeID']) ) {
							$programRowContent = "{ label: '" . $row['program_name'] . "', value: ". $programBudget .", parent: '" . $officeList[intval($row['programTypeID']) - 1]['program_name'] . "', ";
							$programRowContent .= " data: { description: \"" . number_format(($programBudget / $programTotal) * 100, 2) . "% (" . number_format($programBudget, 0) . ")\", title: \"" . $row['program_name'] . "\" } }, \n";
							echo $programRowContent;
						}
					}
					echo "{ label: 'NA', value: 0, parent: 'Renewable', data: { description: 'NA', title: 'NA'} }, ";
				?>
            ];

			var theme = getDemoTheme();
			$('#treemap').jqxTreeMap({
                width: 800,
                height: 800,
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
        });
</script>
<br/><br/>
	<div style="text-align:center;clear:both;top:10px;">
		<span style="font-weight:bold; font-size:20px; font-family:'Times New Roman';">EERE Budget for FY <?php echo fy_forDisplay($currentYear) ?></span><br/><br/>
		<span style="font-size:14px; font-family:Serif;">EERE Total: <?php echo number_format($programTotal) ?> (Dollars in Thousands) </span>
	</div>
	<div id="treemap" style="clear:both;top:25px"></div>