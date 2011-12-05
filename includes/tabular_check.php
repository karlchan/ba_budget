<?php
  //Loop through each program that has been selected
  foreach ($tmpParentIDList as $tmpParentID) {

  //get parent name for subprograms
  $tmpParentName = getProgramName($tmpParentID);
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
    <th scope='col'>EERE Program Area</th>";
  foreach ($years as $year) {
    printf("<th scope='col'>FY %s</th>", fy_forDisplay($year));
  }
  echo "</tr>";

  while($row = mysql_fetch_array($result)) {

      $tmpDisplayRow = "";
      $tmpBudgetTotal = 0;
      $tmpDisplayRow .= "<tr>" . "\n";
      $tmpDisplayRow .= "<td>" . htmlspecialchars($row['program_name']) . "</td>" . "\n";
      foreach($years as $year){
        if ($tmpParentID == 0){
          //get rollup for parent level
          if ($tmpProgramType != 4)
            $tmpBudget = getBudgetRollup_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($year));
          else
            $tmpBudget = getBudgetRollup_FY_Program(0, mysql_real_escape_string($year), true, mysql_real_escape_string($row['programID']));
          $tmpBudgetTotal += $tmpBudget;
          $tmpDisplayRow .= "<td style=\"text-align:right; vertical-align:bottom;\">" . number_format($tmpBudget) . "</td>" . "\n";
        }
        else {
          //get budget number for subprogram
          $tmpBudget = getBudget_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($year));
          $tmpBudgetTotal += $tmpBudget;
          $tmpDisplayRow .= "<td style=\"text-align:right; vertical-align:bottom;\">" . number_format($tmpBudget) . "</td>" . "\n";
        }
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
echo "</table>\n";
} //end foreach loop
?>