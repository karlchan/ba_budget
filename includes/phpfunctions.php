<?php
/**
 * Various helper functions for BA Budget Web Application
 * Including, database connection information.
 * Please include at the top of every page that needs the DB
 */

require("db.php");
//require_once('debug/FirePHPCore/fb.php');

//set timezone for all php date functions
date_default_timezone_set("America/New_York");


//get year description for table headers
//get parent program name
function getYearDesc($year){
  $sSQL = sprintf("SELECT description FROM fy_budget WHERE fiscal_year = '%s' LIMIT 1", $year);
  $result = mysql_query($sSQL);
  if (mysql_num_rows($result) == 0){
    return "";
  }
  else {
    return mysql_result($result, 0);
  }
}

//set array of all fiscal years in DB
function getFiscalYears($numColumns){
  //the most current FY has 4 columns need to include that in the calculation
  //for all the return years.

  //Get all possible FYs, they should've been imported into the DB in the correct order
  $tmpArray = array();
  $sSQL = sprintf("select distinct fiscal_year from fy_budget");
  $result = mysql_query($sSQL);
  while ($row = mysql_fetch_array($result, MYSQL_NUM)){
    // only add the current FY data, ignore old 'request', 'house', and 'senate' data
    if ((strpos($row[0], (string)CURRENT_FY) !== false) or (is_numeric($row[0])) or (strpos($row[0], "ARRA"))) {
      array_push($tmpArray, $row[0]);
    }
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


//Alter fiscal year for display. If it is just a year (or numeric), it means that it is that fiscal year's final
//appropriation. Add the text to the year only. Otherwise, just send back the FY text untouched.
function fy_forDisplay($fy){
  if (is_numeric($fy)){
    //custom naming before Appropriation is 'Enacted', this will probably be changed on a year to year basis
    switch ($fy){
      case 2012:
        $fy = $fy . " Current";
        break;
      case 2013:
        $fy = $fy . " Annualized CR";
        break;
      default:
        $fy = $fy . " Appropriation";
    }
    
    return $fy;
  }
  else {
    return $fy;
  }
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

//get parent program name
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

//get parent programID
function getParentProgramID($programID){
  $sSQL = sprintf("SELECT parentID FROM program WHERE programID = %d", $programID);
  $result = mysql_query($sSQL);
  if (mysql_num_rows($result) == 0){
    return 0;
  }
  else {
    return mysql_result($result, 0);
  }
}

//Return an object of program names and IDs for either parent programs or subprograms of a parent
function returnProgramListing($programID, $programType){
  if ($programID == 0) {
    //0 means bring back parent level programs
    if ($programType > 0) $tmpProgramFilter = " AND programTypeID = " . $programType;
    else $tmpProgramFilter = "";
    $sSQL = sprintf("SELECT * FROM program WHERE parentID is NULL %s ORDER BY programID", $tmpProgramFilter);
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

//KC: removing subprogram navigation: this function just returns the one program.
function returnProgram($programID) {
  $sSQL = sprintf("SELECT * FROM program WHERE programID = %d", $programID);
  $result = mysql_query($sSQL);
  return $result;
}


//get TOTAL budget for programID and FY
function getBudgetRollup_FY_Program($programID, $fiscal_year, $blnAllEERE = false, $programType = 0){

  if ($blnAllEERE) {
    if ($programType == 0 ) {
      $sSQL = sprintf("select sum(fy_budget.budget) from fy_budget
                   inner join program on fy_budget.programID = program.programID
                   where fy_budget.fiscal_year = '%s'", $fiscal_year);
    }
    else {
      //need to get totals for a certain program type
      $sSQL = sprintf("select programID from program where programTypeID = %d", $programType);
      $result = mysql_query($sSQL);
      $tmpTotal = 0;
      while ($obj = mysql_fetch_array($result)) {
        $tmpTotal += getBudgetRollup_FY_Program($obj['programID'], $fiscal_year);
      }
      return $tmpTotal;
    }
  }
  else {
    $sSQL = sprintf("select sum(fy_budget.budget) from fy_budget
                   inner join program on fy_budget.programID = program.programID
                   where program.parentID = %d and fy_budget.fiscal_year = '%s'", $programID, $fiscal_year);
  }
  $result = mysql_query($sSQL);
  if (mysql_num_rows($result) == 0) return 0;
  else {
    if (is_null(mysql_result($result, 0))) return 0;
    else return mysql_result($result, 0);
  }
}

//get budget for a Specific subprogram using ParentID and FY
function getBudget_FY_Program($programID, $fiscal_year){

  $sSQL = sprintf("SELECT budget FROM fy_budget WHERE fiscal_year = '%s' AND programID = %d", $fiscal_year, $programID);
  $result = mysql_query($sSQL);
  if (!mysql_num_rows($result)) return 0;
  else return mysql_result($result, 0);
}


function createProgramSelect($tbl_value, $tbl_label, $select_name, $label, $result, $multiple='', $size=5, $all_label, $all_value) {

  //setup optionals
  $tmpOptions = "";
  if ($multiple == "multiple"){
    $tmpOptions .= " multiple='multiple'";
    $tmpOptions .= " size=\"" . $size . "\"";
  }

  $menu = "<label class=\"budget_control\" for=\"". str_replace("[]", "", $select_name) . "\">".$label."</label>\n";
  $menu .= "<select name=\"". $select_name . "\"". $tmpOptions . " id=\"" . str_replace("[]", "", $select_name) . "\">\n";
  $menu .= "  <option value=\"" . $all_value . "\"";
  //$menu .= (!isset ($_REQUEST[$select_name])) ? " selected" : "";
  $menu .= ">" . $all_label . "</option>\n";
  while ($obj = mysql_fetch_object($result)) {
      $menu .= "  <option value=\"".$obj->$tbl_value."\"";
      //$menu .= (isset($_REQUEST[$select_name]) && $obj->$tbl_value == $_REQUEST[$select_name]) ? " selected" : "";
      $menu .= ">" . htmlspecialchars($obj->$tbl_label) . "</option>\n";
  }
  $menu .= "</select>\n";

  return $menu;
}

function getProgramTypes(){
  $sSQL = sprintf("SELECT id as programID, type as program_name FROM program_type");
  $result = mysql_query($sSQL);
  if (!mysql_num_rows($result)) return 0;
  else return $result;
}
?>