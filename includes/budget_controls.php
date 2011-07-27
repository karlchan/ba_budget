<form id="frm_budget" name="frm_budget" action="program_budget_formulation.php" method="post">
  <?php
    printf ("<input type=\"hidden\" value=\"%s\" name=\"hdn_ParentIDs\"/>", htmlspecialchars(implode(",",$tmpParentIDList)));
    printf ("<input type=\"hidden\" value=\"%s\" name=\"hdn_Years\"/>", htmlspecialchars(implode(",",$years)));
    printf ("<input type=\"hidden\" value=\"%s\" name=\"hdn_programType\"/>", htmlspecialchars($tmpProgramType));
    printf ("<input type=\"hidden\" value=\"%s\" name=\"hdn_chartType\"/>", htmlspecialchars($tmpChartTypeID));
  //only present the form if only one program has been selected
  if (count($tmpParentIDList) == 1){
  ?>
    <div id="program_select">
<?php

    //create program select control
    $result = returnProgramListing($tmpParentIDList[0], $tmpProgramType);
    printf("<div>%s</div>",createSelect("programID",
                                        "program_name",
                                        "sel_programs[]",
                                        "Programs",
                                        $result,
                                        "multiple",
                                        5 ,
                                        "All EERE Programs",0)
    );
?>
    </div>
    <?php } ?>
    <div id="year_select">
      <label for="sel_years[]">Fiscal Year(s)</label><br/>
      <select name="sel_years[]" id="sel_years[]" <?php if ($tmpChartTypeID <> 1) echo "size=\"5\" multiple";?>>
        <?php if ($tmpChartTypeID <> 1) { ?>
        <option value="14">10 Year Period</option>
        <option value="9">5 Year Period</option>
        <option value="7">3 Year Period</option>
  <?php
      }
      foreach(getFiscalYears(0) as $possibleYears){
        echo "<option ";
        if (in_array($possibleYears, $years)) echo "selected ";
        echo "value=\"" . $possibleYears . "\">" . $possibleYears . "</option>\n";
      }
      ?>
      </select>
    </div>
  <?php
    //only show program type at top level
    if (count($tmpParentIDList) == 1 and $tmpParentIDList[0] == 0){
    ?>
    <div id="program_type_select">
      <label for="sel_programType">EERE Program Type</label><br/>
      <select name="sel_programType" id="sel_programType">
        <option <?php if ($tmpProgramType == 0) echo " selected ";?>value="0">All EERE Program Types</option>
        <option <?php if ($tmpProgramType == 1) echo " selected ";?>value="1">Renewable</option>
        <option <?php if ($tmpProgramType == 2) echo " selected ";?>value="2">Efficiency</option>
        <option <?php if ($tmpProgramType == 3) echo " selected ";?>value="3">Corporate</option>
      </select>
    </div>
    <?php } ?>
    <div id="chart_select">
      <label for="sel_chart">Display Type</label><br/>
      <select name="sel_chart" id="sel_chart">
        <option <?php if ($tmpChartTypeID == 0) echo " selected ";?>value="0">Tabular</option>
        <option <?php if ($tmpChartTypeID == 1) echo " selected ";?>value="1">Pie</option>
        <option <?php if ($tmpChartTypeID == 2) echo " selected ";?>value="2">Line</option>
        <option <?php if ($tmpChartTypeID == 3) echo " selected ";?>value="3">Column</option>
      </select>
    </div>
    <input type="submit" value="Submit"/>
    <input type="button" value="Reset" onclick="javascript:document.location.href='program_budget_formulation.php';"/>
  </form>