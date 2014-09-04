<script type="text/javascript" src="./includes/js/html2canvas.js"></script>
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
			
			$("#image_export").on("click", function() {
				html2canvas($("#divTreeContainer"), {
					onrendered: function(canvas) {
						$("#divTreeContainer").append(canvas);
						canvas.style.display="none";

						//console.log(<?php echo "'" . fy_forDisplay($currentYear) . "'" ?> + "." + $("#sel_export option:selected").text().toLowerCase());
						//console.log($("#sel_export option:selected").val());
						
						var is_chrome = /chrom(e|ium)/.test(navigator.userAgent.toLowerCase());
						var fileext = "png" ; // $("#sel_export option:selected").text().toLowerCase();
						var filetype = "image/png"; // $("#sel_export option:selected").val();
						var filename = "EERE Budget for FY " + <?php echo "'" . fy_forDisplay($currentYear) . "'" ?> + "." + fileext;
						var data = canvas.toDataURL(filetype);
						
						// New tab option
						//var tab = window.open();
						//tab.document.write("<img src='" + canvas.toDataURL($("#sel_export option:selected").val()) + "' />");

						// Redirect option
						//data = data.replace($("#sel_export option:selected").val(), "image/octet-stream");
						//window.location.href = data;						

						// Firefox version
						// $("#lnkdownload").attr('href', data);
						// $("#lnkdownload").attr('download', filename);
						// $("#lnkdownload").on('click', 'a', function(e) { e.preventdefault(); });

						if (is_chrome) {
						// Chrome version
							var nameInput = document.createElement("a");
							nameInput.setAttribute("href", data);
							nameInput.setAttribute("download", filename);
							nameInput.setAttribute("name", "nameInput."+ fileext);
							nameInput.click();
						} else {
						// IE and Firefox version
							data = data.substr(data.indexOf(',') + 1).toString();
							$("#imgdata").val(data);
							$("#filename").val(filename);
							$("#filetype").val(filetype);
							
							$("#frmExport").submit();
						}
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
	<input value="Export" type="button" id="image_export" name="image_export" class="controlbutton" style="display: none;">
</form>
</div>
<div id="divTreeContainer" style="">
	<div style="text-align:center;clear:both;">
		<span style="font-weight:bold; font-size:20px; font-family:'Times New Roman';">EERE Budget for FY <?php echo fy_forDisplay($currentYear) ?></span><br/><br/>
		<span style="font-size:14px; font-family:Serif;">EERE Total: <?php echo number_format($programTotal) ?> (Dollars in Thousands) </span>
	</div>
	<div style="padding-top:15px;"></div>
	<div id="treemap" style="clear:both;"></div>
	<div style="padding-top:15px;">&nbsp;</div>
</div>