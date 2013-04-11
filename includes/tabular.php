<?php
  
  //Loop through each program that has been selected
  foreach ($tmpParentIDList as $tmpParentID) {

  $wip_shown = false;
  //get parent name for subprograms
  $tmpParentName = getProgramName($tmpParentID);
  if($tmpParentName == "Weatherization and Intergovernmental Activities")
    $wip_shown = true;
  if ($tmpProgramType != 4)
    $result = returnProgramListing($tmpParentID, $tmpProgramType);
  else $result = getProgramTypes(); //Get only program types.
?>

<?php

  echo "<br/><p style=\"clear:both;\">* dollars in thousands</p> \n
      <table class=\"data\" width='100%' summary='This table contains historical EERE fiscal year budget data for EERE program areas'>";
  if ($tmpParentName <> ""){
    printf("<caption>%s</caption>", $tmpParentName);
  }
  echo "<tr>
    <th scope='col'>EERE Area</th>";
  $col_count = 1;
  foreach ($years as $year) {
    printf("<th scope='col'>FY %s</th>", fy_forDisplay($year));
    $col_count++;
  }
  echo "</tr>";
  
  
  while($row = mysql_fetch_array($result)) {

      $tmpDisplayRow = "";
      $tmpBudgetTotal = 0;
      $tmpDisplayRow .= "<tr>" . "\n";
      $programNameDisplay = htmlspecialchars($row['program_name']);
      if ($programNameDisplay == "Weatherization and Intergovernmental Activities") {
        $programNameDisplay = $programNameDisplay . " *";
        $wip_shown = true;
      }
      $tmpDisplayRow .= "<td>" . $programNameDisplay . "</td>" . "\n";
      foreach($years as $year){
        //KC: removing subprogram navigation: if ($tmpParentID == 0){
          //get rollup for parent level
          if ($tmpProgramType != 4)
            $tmpBudget = getBudgetRollup_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($year));
          else
            $tmpBudget = getBudgetRollup_FY_Program(0, mysql_real_escape_string($year), true, mysql_real_escape_string($row['programID']));
          $tmpBudgetTotal += $tmpBudget;
          $tmpDisplayRow .= "<td style=\"text-align:right; vertical-align:bottom;\">" . number_format($tmpBudget) . "</td>" . "\n";
        //KC: removing subprogram navigation: }
        //KC: removing subprogram navigation: else {
          //get budget number for subprogram
          //KC: removing subprogram navigation: $tmpBudget = getBudget_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($year));
          //KC: removing subprogram navigation: $tmpBudgetTotal += $tmpBudget;
          //KC: removing subprogram navigation: $tmpDisplayRow .= "<td style=\"text-align:right; vertical-align:bottom;\">" . number_format($tmpBudget) . "</td>" . "\n";
        //KC: removing subprogram navigation: }
      }
      $tmpDisplayRow .= "</tr>" . "\n";
      if ($tmpBudgetTotal != 0) {
        echo $tmpDisplayRow;
      }
  }
  //if there are multiple results, show the totals row
  if (mysql_num_rows($result) > 1) {
    echo "<tr><td style=\"text-align:right; vertical-align:bottom;\"><strong>Totals:</strong></td>\n";
      foreach($years as $year){
        if ($tmpParentID == 0){
          //Totals for ALL EERE
          if ($tmpProgramType == 0 or $tmpProgramType == 4)
            echo "<td style=\"text-align:right; vertical-align:bottom;\">" . number_format(getBudgetRollup_FY_Program(0, mysql_real_escape_string($year),true)) . "</td>" . "\n";
          else
            echo "<td style=\"text-align:right; vertical-align:bottom;\">" . number_format(getBudgetRollup_FY_Program(0, mysql_real_escape_string($year),true, mysql_real_escape_string($tmpProgramType))) . "</td>" . "\n";
        }
        else {
          //Totals for Subprogram
          echo "<td style=\"text-align:right; vertical-align:bottom;\">" . number_format(getBudgetRollup_FY_Program(mysql_real_escape_string($tmpParentID), mysql_real_escape_string($year))) . "</td>" . "\n";
        }
      }
    echo "</tr>\n";
  }
  
  
  //special row for WIP Control Points
  if ($wip_shown){
  echo "<tr class=\"wip_control_points\">
          <td colspan=\"".$col_count."\" style=\"font-size:85%;\">* WIP is comprised of 3 Programs, with statutory controls and funds appropriated under these areas. In FY 12, amounts were $68 million for the Weatherization Assistance Program (WAP), $50 million for the State Energy Program (SEP), and $10 million for the Tribal Energy Program (TEP). In FY 13, amounts were $68.4 million for WAP, $50.3 million for SEP, and $10 million for TEP. In FY 14, our budget request includes for WAP, $184 million, for SEP $57 million, and $7 million for TEP.</td></tr>";
  }
echo "</table>\n";
} //end foreach loop
?>