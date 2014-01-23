<script type="text/javascript">
$(document).ready(
	$(function () {
		var data = [
			<?php 				
				$programTotal = getBudgetRollup_FY_Program(0, mysql_real_escape_string($years[0]),true);
				$offices = getProgramTypes();
				$officeList = [];
				$colorList = ['#B3FAFF', '#95FF7A', '#FFA3CE', '#F1A3FF'];
				
				$parentRowContent = "";
				$i = 0;
				while($row = mysql_fetch_array($offices)) {
					$officeRowContent = "{ label: '" . $row['program_name'] . "', value: null, color: '" . $colorList[$i++] . "' }, \n";
					$officeList[] = $row;
					//echo $officeRowContent;
				}
				for ($i = mysql_num_rows($offices) - 1; $i >= 0; $i--) {
					mysql_data_seek($offices, $i);
					$row = mysql_fetch_assoc($offices);
					$officeRowContent = "{ label: '" . $row['program_name'] . "', value: null, color: '" . $colorList[$i] . "' }, \n";
					echo $officeRowContent;
				}
				
				$programs = returnProgramListing($tmpParentIDList[0],0);
				
				$programBudget = 0;
				while ($row = mysql_fetch_array($programs)) {
					$programBudget = getBudgetRollup_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($years[0]));
					if ($programBudget > 0 && !empty($row['programTypeID']) ) {
						$programRowContent = "{ label: '" . $row['program_name'] . "', value: ". $programBudget .", parent: '" . $officeList[intval($row['programTypeID']) - 1]['program_name'] . "', ";
						$programRowContent .= " data: { description: \"" . number_format(($programBudget / $programTotal) * 100, 2) . "% (" . number_format($programBudget, 0) . ")\", title: \"" . $row['program_name'] . "\" } }, \n";
						echo $programRowContent;
					}
				}

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
							content: '<div><span style="font-weight: bold; max-width: 350px; font-family: verdana; font-size: 11px;">' + value.data.title + ': </span><span style="width: 350px; font-family: verdana; font-size: 10px;">' + value.data.description + '</span></div>',
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
	})
);
</script>
<br/><br/>
  <div id="treemap" style="clear:both;top:25px"></div>
  
<?php
if ((count($tmpParentIDList) == 1) && ($tmpParentIDList[0] != 0)){
echo "<h3 style='text-align:center; color:red; clear:both'>Please select more than one program</h3>";
}
?>