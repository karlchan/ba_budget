<?php
/**
 * Various helper functions for BA Budget Web Application
 * Including, database connection information.
 * Please include at the top of every page that needs the DB
 */

$con = mysql_connect("localhost","budgetuser","password");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }
mysql_select_db("ba_budget", $con);

//require_once('debug/FirePHPCore/fb.php');

//set timezone for all php date functions
date_default_timezone_set("America/New_York");

//set array of all fiscal years in DB

function getFiscalYears($numColumns){
  //the most current FY has 4 columns need to include that in the calculation
  //for all the return years.

  //Get all possible FYs, they should've been imported into the DB in the correct order
  $tmpArray = array();
  $sSQL = sprintf("select distinct fiscal_year from fy_budget");
  $result = mysql_query($sSQL);
  while ($row = mysql_fetch_array($result, MYSQL_NUM)){
    array_push($tmpArray, $row[0]);
  }

  //calculate the offset array, $numColumns == 0 means get all years.
  if ($numColumns > 0){
    $tmpFrontOffset = count($tmpArray) - $numColumns;
    $tmpRearOffset = count($tmpArray) - FY_OFFSET;
    $tmpCount = 0;
    $tmpReturnArray = array();
    foreach($tmpArray as $years){
      //account for the extra 2009 ARRA column
      if ($years == "2009 ARRA") $tmpCount--;
      if ($tmpCount >= $tmpFrontOffset and $tmpCount < $tmpRearOffset ){
        array_push($tmpReturnArray, $years);
      }
      $tmpCount++;
    }
  }
  else $tmpReturnArray = $tmpArray;
  return $tmpReturnArray;
}

//get program name
function getProgramName($programID){
  $sSQL = sprintf("SELECT program_name FROM program WHERE programID = %d", $programID);
  $result = mysql_query($sSQL);
  if (mysql_num_rows($result) == 0){
    return "";
  }
  else {
    return mysql_result($result, 0);
  }
}

//get program name
function getParentProgramName($programID){
  $sSQL = sprintf("SELECT parentID FROM program WHERE programID = %d", $programID);
  $result = mysql_query($sSQL);
  if (mysql_num_rows($result) == 0){
    return "";
  }
  else {
    $tmpParentID = mysql_result($result, 0);
    $sSQL = sprintf("SELECT program_name FROM program WHERE programID = %d", $tmpParentID);
    $result = mysql_query($sSQL);
    if (mysql_num_rows($result) == 0) return "";
    else return mysql_result($result, 0);
  }
}

//Return an object of program names and IDs for either parent programs or subprograms of a parent
function returnProgramListing($programID){
  if ($programID == 0) {
    //0 means bring back parent level programs
    $sSQL = sprintf("SELECT * FROM program WHERE parentID is NULL ORDER BY programID");
  }
  else {
    //get subprograms of a parent
    $sSQL = sprintf("SELECT * FROM program WHERE parentID = %d ORDER BY programID", $programID);
  }

  $result = mysql_query($sSQL);

  //Can't find any sub-level programs, must need ONLY one program.
  if (mysql_num_rows($result) == 0 ){
    $sSQL = sprintf("SELECT * FROM program WHERE programID = %d", $programID);
    $result = mysql_query($sSQL);
  }


  return $result;
}

//get TOTAL budget for programID and FY
function getBudgetRollup_FY_Program($programID, $fiscal_year, $blnAllEERE = false){

  if ($blnAllEERE) {
    $sSQL = sprintf("select sum(fy_budget.budget) from fy_budget
                   inner join program on fy_budget.programID = program.programID
                   where fy_budget.fiscal_year = '%s'", $fiscal_year);
  }
  else {
    $sSQL = sprintf("select sum(fy_budget.budget) from fy_budget
                   inner join program on fy_budget.programID = program.programID
                   where program.parentID = %d and fy_budget.fiscal_year = '%s'", $programID, $fiscal_year);
  }
  $result = mysql_query($sSQL);
  if (mysql_num_rows($result) == 0) return 0;
  else return mysql_result($result, 0);
}

//get budget for a Specific subprogram using ParentID and FY
function getBudget_FY_Program($programID, $fiscal_year){

  $sSQL = sprintf("SELECT budget FROM fy_budget WHERE fiscal_year = '%s' AND programID = %d", $fiscal_year, $programID);
  $result = mysql_query($sSQL);
  if (!mysql_num_rows($result)) return 0;
  else return mysql_result($result, 0);
}


function createSelect($tbl_value, $tbl_label, $select_name, $label, $result, $multiple='', $size=5, $all_label, $all_value) {

  //setup optionals
  $tmpOptions = "";
  if ($multiple == "multiple"){
    $tmpOptions .= " multiple";
    $tmpOptions .= " size=\"" . $size . "\"";
  }

  $menu = "<label for=\"".$select_name."\">".$label."</label><br/>\n";
  $menu .= "<select name=\"".$select_name."\"". $tmpOptions .">\n";
  $menu .= "  <option value=\"" . $all_value . "\"";
  //$menu .= (!isset ($_REQUEST[$select_name])) ? " selected" : "";
  $menu .= ">" . $all_label . "\n";
  while ($obj = mysql_fetch_object($result)) {
      $menu .= "  <option value=\"".$obj->$tbl_value."\"";
      //$menu .= (isset($_REQUEST[$select_name]) && $obj->$tbl_value == $_REQUEST[$select_name]) ? " selected" : "";
      $menu .= ">".$obj->$tbl_label."\n";
  }
  $menu .= "</select>\n";

  return $menu;
}
?>