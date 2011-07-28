<?php
  //Loop through each program that has been selected
  foreach ($tmpParentIDList as $tmpParentID) {

  //get parent name for subprograms
  $tmpParentName = getProgramName($tmpParentID);
  $result = returnProgramListing($tmpParentID, $tmpProgramType);
?>

<?php

//Display table
    //if (mysql_num_rows($result) > 0) {
      //reset result set pointer to beginning
      //mysql_data_seek($result, 0);
    //}
  echo "<br/><p style=\"clear:both;\">* dollars in thousands</p> \n
      <table class=\"data\" width='100%' summary='This table contains historical EERE fiscal year budget data for EERE program areas'>";
  if ($tmpParentName <> ""){
    printf("<caption>%s</caption>", $tmpParentName);
  }
  echo "<tr>
    <th scope='col'>EERE Program Area</th>";
  foreach ($years as $year) {
    printf("<th scope='col'>FY %s</th>", $year);
  }
  echo "</tr>";

  while($row = mysql_fetch_array($result)) {
      echo "<tr>" . "\n";
      echo "<td>" . htmlspecialchars($row['program_name']) . "</td>" . "\n";
      foreach($years as $year){
        if ($tmpParentID == 0){
          //get rollup for parent level
          echo "<td style=\"text-align:right; vertical-align:bottom;\">" . number_format(getBudgetRollup_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($year))) . "</td>" . "\n";
        }
        else {
          //get budget number for subprogram
          echo "<td style=\"text-align:right; vertical-align:bottom;\">" . number_format(getBudget_FY_Program(mysql_real_escape_string($row['programID']), mysql_real_escape_string($year))) . "</td>" . "\n";
        }
      }
      echo "</tr>" . "\n";
  }
  //if there are multiple results, show the totals row
  if (mysql_num_rows($result) > 1) {
    echo "<tr><td style=\"text-align:right; vertical-align:bottom;\"><strong>EERE Totals:</strong></td>\n";
      foreach($years as $year){
        if ($tmpParentID == 0){
          //Totals for ALL EERE
          echo "<td style=\"text-align:right; vertical-align:bottom;\">" . number_format(getBudgetRollup_FY_Program(0, mysql_real_escape_string($year),true)) . "</td>" . "\n";
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